<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MemberDirectoryController extends Controller
{
    public function index(): View
    {
        $members = User::query()
            ->where('role', 'Member')
            ->where('is_active', 'Yes')
            ->where('is_ban', 'No')
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->inRandomOrder()
            ->limit(96)
            ->get();

        return view('members', [
            'members' => $members,
        ]);
    }

    public function photo(string $directory, string $filename)
    {
        abort_unless(in_array($directory, ['profile_photos', 'passport_photographs'], true), 404);

        $path = $directory.'/'.basename($filename);

        abort_unless(
            User::query()
                ->where('role', 'Member')
                ->where('is_active', 'Yes')
                ->where('is_ban', 'No')
                ->where('image', $path)
                ->exists(),
            404
        );

        abort_unless(Storage::disk('public')->exists($path), 404);

        return response()->file(Storage::disk('public')->path($path), [
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
