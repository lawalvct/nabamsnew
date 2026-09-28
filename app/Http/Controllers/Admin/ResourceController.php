<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResourceController as MemberResourceController;
use App\Models\Level;
use App\Models\Payment;
use App\Models\Resource;
use App\Support\SecureUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public const MAX_UPLOAD_KB = 51200;

    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'access' => ['nullable', Rule::in(['free', 'paid'])],
        ]);

        $resources = Resource::query()
            ->with('level')
            ->withCount(['payments as purchases_count' => fn ($query) => $query->where('status', Payment::STATUS_APPROVED)])
            ->withCount(['payments as pending_count' => fn ($query) => $query->where('status', Payment::STATUS_PENDING)])
            ->withSum(['payments as revenue' => fn ($query) => $query->where('status', Payment::STATUS_APPROVED)], 'amount_paid')
            ->when($filters['q'] ?? null, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->when($filters['access'] ?? null, fn ($query, $access) => $query->where('access', $access))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.resources.index', [
            'user' => $request->user(),
            'resources' => $resources,
            'filters' => $filters,
            'totals' => [
                'resources' => Resource::count(),
                'views' => (int) Resource::sum('view_count'),
                'downloads' => (int) Resource::sum('download_count'),
                'revenue' => (int) Payment::approved()->where('type', Payment::TYPE_RESOURCE)->sum('amount_paid'),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.resources.create', $this->formData($request, new Resource(['access' => 'free', 'is_published' => 'Yes'])));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request, fileRequired: true);
        $stored = SecureUpload::store($request->file('file'), 'file', 'resources', MemberResourceController::DISK, SecureUpload::RESOURCE_EXTENSIONS);

        Resource::create([
            ...$data,
            'file_path' => $stored['path'],
            'file_extension' => $stored['extension'],
            'file_mime' => $stored['mime'],
            'file_size' => $stored['size'],
            'uploaded_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.resources.index')->with('success', 'Resource uploaded successfully.');
    }

    public function edit(Request $request, Resource $resource): View
    {
        $this->authorizeAdmin($request);

        return view('admin.resources.edit', $this->formData($request, $resource));
    }

    public function update(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $data = $this->validatedData($request, fileRequired: false);
        $oldPath = null;

        if ($request->hasFile('file')) {
            $stored = SecureUpload::store($request->file('file'), 'file', 'resources', MemberResourceController::DISK, SecureUpload::RESOURCE_EXTENSIONS);
            $oldPath = $resource->file_path;
            $data += [
                'file_path' => $stored['path'],
                'file_extension' => $stored['extension'],
                'file_mime' => $stored['mime'],
                'file_size' => $stored['size'],
            ];
        }

        $resource->update($data);

        if ($oldPath) {
            Storage::disk(MemberResourceController::DISK)->delete($oldPath);
        }

        return redirect()->route('admin.resources.index')->with('success', 'Resource updated successfully.');
    }

    public function destroy(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorizeAdmin($request);

        if ($resource->payments()->where('status', Payment::STATUS_APPROVED)->exists()) {
            return back()->with('error', 'Members have already bought this resource. Unpublish it instead so buyers keep access.');
        }

        Storage::disk(MemberResourceController::DISK)->delete($resource->file_path);
        $resource->delete();

        return back()->with('success', 'Resource deleted successfully.');
    }

    private function formData(Request $request, Resource $resource): array
    {
        return [
            'user' => $request->user(),
            'resource' => $resource,
            'levels' => Level::query()->orderBy('sort_order')->get(),
            'categories' => Resource::CATEGORIES,
            'maxUploadMb' => (int) (self::MAX_UPLOAD_KB / 1024),
        ];
    }

    private function validatedData(Request $request, bool $fileRequired): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['nullable', Rule::in(Resource::CATEGORIES)],
            'level_id' => ['nullable', 'integer', Rule::exists('levels', 'id')],
            'access' => ['required', Rule::in(['free', 'paid'])],
            'price' => ['nullable', 'required_if:access,paid', 'integer', 'min:0', 'max:10000000'],
            'is_published' => ['required', Rule::in(['Yes', 'No'])],
            'file' => [
                $fileRequired ? 'required' : 'nullable',
                'file',
                'max:'.self::MAX_UPLOAD_KB,
                'extensions:'.implode(',', SecureUpload::RESOURCE_EXTENSIONS),
            ],
        ], [
            'price.required_if' => 'Set a price for a paid resource.',
            'file.extensions' => 'Allowed file types: '.strtoupper(implode(', ', SecureUpload::RESOURCE_EXTENSIONS)).'.',
            'file.max' => 'The file must not be larger than '.(int) (self::MAX_UPLOAD_KB / 1024).'MB.',
        ]);

        if ($data['access'] === 'paid' && (int) ($data['price'] ?? 0) < 1) {
            throw \Illuminate\Validation\ValidationException::withMessages(['price' => 'A paid resource must cost at least ₦1.']);
        }

        unset($data['file']);
        $data['price'] = $data['access'] === 'paid' ? (int) $data['price'] : 0;

        return $data;
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(strtolower((string) $request->user()?->role) === 'admin', 403);
    }
}
