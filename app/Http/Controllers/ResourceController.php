<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Level;
use App\Models\Payment;
use App\Models\Resource;
use App\Support\SecureUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class ResourceController extends Controller
{
    public const DISK = 'local';

    public function index(Request $request): View
    {
        $user = $request->user();

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', Rule::in(Resource::CATEGORIES)],
            'level_id' => ['nullable', 'integer'],
            'access' => ['nullable', Rule::in(['free', 'paid', 'owned'])],
        ]);

        $ownedIds = Payment::approved()->where('user_id', $user->id)->whereNotNull('resource_id')->pluck('resource_id');

        $resources = Resource::published()
            ->with('level')
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where(fn ($query) => $query
                ->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('category', $category))
            ->when($filters['level_id'] ?? null, fn ($query, $id) => $query->where(fn ($query) => $query->where('level_id', $id)->orWhereNull('level_id')))
            ->when(($filters['access'] ?? null) === 'free', fn ($query) => $query->where('access', 'free'))
            ->when(($filters['access'] ?? null) === 'paid', fn ($query) => $query->where('access', 'paid'))
            ->when(($filters['access'] ?? null) === 'owned', fn ($query) => $query->whereIn('id', $ownedIds))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('resources.index', [
            'user' => $user,
            'resources' => $resources,
            'filters' => $filters,
            'ownedIds' => $ownedIds,
            'levels' => Level::active()->orderBy('sort_order')->get(),
            'categories' => Resource::CATEGORIES,
        ]);
    }

    public function show(Request $request, Resource $resource): View
    {
        $user = $request->user();

        abort_unless($resource->is_published === 'Yes' || $this->isAdmin($request), 404);

        // Count one view per resource per login session so refreshes don't inflate the number.
        $viewed = $request->session()->get('viewed_resources', []);

        if (! in_array($resource->id, $viewed, true)) {
            $resource->increment('view_count');
            $request->session()->put('viewed_resources', [...$viewed, $resource->id]);
        }

        $payment = $resource->payments()
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();

        return view('resources.show', [
            'user' => $user,
            'resource' => $resource->load('level'),
            'payment' => $payment,
            'canDownload' => $resource->canBeDownloadedBy($user),
        ]);
    }

    public function purchase(Request $request, Resource $resource): RedirectResponse
    {
        $user = $request->user();

        abort_unless($resource->is_published === 'Yes', 404);

        if ($resource->canBeDownloadedBy($user)) {
            return redirect()->route('resources.show', $resource)->with('success', 'You already have access to this resource.');
        }

        $payment = $resource->payments()
            ->where('user_id', $user->id)
            ->where('status', '!=', Payment::STATUS_APPROVED)
            ->latest('id')
            ->first();

        $attributes = [
            'items' => [['name' => $resource->title, 'semester' => null, 'amount' => $resource->price]],
            'amount_due' => $resource->price,
            'level_id' => $user->level_id,
            'level_name' => Level::query()->whereKey($user->level_id)->value('name') ?? $user->academic_level,
        ];

        if (! $payment) {
            $payment = Payment::create([
                ...$attributes,
                'type' => Payment::TYPE_RESOURCE,
                'resource_id' => $resource->id,
                'user_id' => $user->id,
                'academic_session_id' => AcademicSession::current()->value('id'),
                'status' => Payment::STATUS_AWAITING,
            ]);
        } elseif ($payment->status === Payment::STATUS_AWAITING) {
            // Keep the price in sync until the member actually pays.
            $payment->update($attributes);
        }

        return redirect()->route('payments.show', $payment);
    }

    public function download(Request $request, Resource $resource): Response
    {
        abort_unless($resource->canBeDownloadedBy($request->user()), 403);
        abort_unless(Storage::disk(self::DISK)->exists($resource->file_path), 404);

        $resource->increment('download_count');

        return Storage::disk(self::DISK)->download(
            $resource->file_path,
            SecureUpload::safeDownloadName($resource->title, $resource->file_extension),
            SecureUpload::responseHeaders($resource->file_extension, inline: false),
        );
    }

    private function isAdmin(Request $request): bool
    {
        return strtolower((string) $request->user()?->role) === 'admin';
    }
}
