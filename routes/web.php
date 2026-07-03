<?php

use App\Http\Controllers\Admin\AcademicSessionController;
use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\ElectionAspirantController;
use App\Http\Controllers\Admin\ElectionPositionController;
use App\Http\Controllers\Admin\ElectionVoteController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/excos', function () {
    return view('excos');
})->name('excos');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/dashboard/election', [ElectionController::class, 'index'])->name('election.index');
    Route::post('/dashboard/election/vote', [ElectionController::class, 'vote'])
        ->middleware('throttle:10,1')
        ->name('election.vote');
    Route::get('/dashboard/profile/photo/{directory}/{filename}', [ProfileController::class, 'photo'])
        ->whereIn('directory', ['profile_photos', 'passport_photographs'])
        ->where('filename', '[A-Za-z0-9._-]+')
        ->name('profile.photo');
    Route::get('/dashboard/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/dashboard/profile', [ProfileController::class, 'update'])->middleware('throttle:10,1')->name('profile.update');
    Route::put('/dashboard/profile/password', [ProfileController::class, 'updatePassword'])->middleware('throttle:5,1')->name('profile.password.update');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::patch('/academic-sessions/{academicSession}/make-current', [AcademicSessionController::class, 'makeCurrent'])
            ->name('academic-sessions.make-current');
        Route::resource('academic-sessions', AcademicSessionController::class)
            ->parameters(['academic-sessions' => 'academicSession'])
            ->except('show');

        Route::prefix('election')->name('election.')->group(function () {
            Route::get('/votes', [ElectionVoteController::class, 'index'])->name('votes.index');
            Route::resource('positions', ElectionPositionController::class)
                ->parameters(['positions' => 'position'])
                ->except('show');
            Route::resource('aspirants', ElectionAspirantController::class)
                ->parameters(['aspirants' => 'aspirant'])
                ->except('show');
        });

        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->middleware('throttle:10,1')->name('settings.update');
        Route::resource('members', MemberController::class)
            ->parameters(['members' => 'member'])
            ->only(['index', 'show', 'edit', 'update']);
        Route::resource('admins', AdministratorController::class)
            ->parameters(['admins' => 'admin'])
            ->except('show');
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
