<?php

use App\Http\Controllers\Admin\AcademicSessionController;
use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\ElectionAspirantController;
use App\Http\Controllers\Admin\ElectionPositionController;
use App\Http\Controllers\Admin\ElectionVoteAdjustmentController;
use App\Http\Controllers\Admin\ElectionVoteController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Admin\PriceSettingController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\MemberDirectoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptVerificationController;
use App\Http\Controllers\ResourceController;
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

Route::get('/our-members', [MemberDirectoryController::class, 'index'])->middleware('throttle:60,1')->name('members.index');
Route::get('/our-members/photo/{directory}/{filename}', [MemberDirectoryController::class, 'photo'])
    ->whereIn('directory', ['profile_photos', 'passport_photographs'])
    ->where('filename', '[A-Za-z0-9._-]+')
    ->name('members.photo');

Route::get('/verify-receipt', ReceiptVerificationController::class)
    ->middleware('throttle:30,1')
    ->name('receipts.verify');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:5,1')->name('password.update');
});

Route::middleware(['auth', 'payment.verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/dashboard/transactions', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/dashboard/payments/pay', [PaymentController::class, 'create'])->name('payments.create');
    Route::get('/dashboard/payments/{payment}', [PaymentController::class, 'show'])->whereNumber('payment')->name('payments.show');
    Route::get('/dashboard/resources', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('/dashboard/resources/{resource}', [ResourceController::class, 'show'])->whereNumber('resource')->name('resources.show');
    Route::post('/dashboard/resources/{resource}/purchase', [ResourceController::class, 'purchase'])
        ->whereNumber('resource')
        ->middleware('throttle:10,1')
        ->name('resources.purchase');
    Route::get('/dashboard/resources/{resource}/download', [ResourceController::class, 'download'])
        ->whereNumber('resource')
        ->middleware('throttle:30,1')
        ->name('resources.download');
    Route::post('/dashboard/payments/{payment}/submit', [PaymentController::class, 'submit'])
        ->middleware('throttle:10,1')
        ->name('payments.submit');
    Route::get('/dashboard/payments/{payment}/evidence', [PaymentController::class, 'evidence'])
        ->middleware('throttle:60,1')
        ->name('payments.evidence');
    Route::get('/dashboard/payments/{payment}/receipt', [PaymentController::class, 'receipt'])
        ->middleware('throttle:20,1')
        ->name('payments.receipt');
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
        Route::patch('/academic-sessions/{academicSession}/semester', [AcademicSessionController::class, 'setSemester'])
            ->name('academic-sessions.set-semester');
        Route::resource('academic-sessions', AcademicSessionController::class)
            ->parameters(['academic-sessions' => 'academicSession'])
            ->except('show');

        Route::prefix('election')->name('election.')->group(function () {
            Route::get('/votes', [ElectionVoteController::class, 'index'])->name('votes.index');
            Route::get('/votes/live', [ElectionVoteController::class, 'live'])->name('votes.live');
            Route::get('/votes/pdf', [ElectionVoteController::class, 'downloadPdf'])->middleware('throttle:10,1')->name('votes.pdf');
            Route::get('/vote-adjustments', [ElectionVoteAdjustmentController::class, 'create'])->name('vote-adjustments.create');
            Route::post('/vote-adjustments', [ElectionVoteAdjustmentController::class, 'store'])
                ->middleware('throttle:10,1')
                ->name('vote-adjustments.store');
            Route::resource('positions', ElectionPositionController::class)
                ->parameters(['positions' => 'position'])
                ->except('show');
            Route::resource('aspirants', ElectionAspirantController::class)
                ->parameters(['aspirants' => 'aspirant'])
                ->except('show');
        });

        Route::resource('price-settings', PriceSettingController::class)
            ->parameters(['price-settings' => 'priceSetting'])
            ->except('show');
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/export', [AdminPaymentController::class, 'export'])->middleware('throttle:10,1')->name('payments.export');
        Route::get('/payments/record', [AdminPaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments/record', [AdminPaymentController::class, 'store'])->middleware('throttle:20,1')->name('payments.store');
        Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
        Route::patch('/payments/{payment}/approve', [AdminPaymentController::class, 'approve'])->middleware('throttle:60,1')->name('payments.approve');
        Route::patch('/payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->middleware('throttle:60,1')->name('payments.reject');
        Route::resource('resources', AdminResourceController::class)
            ->parameters(['resources' => 'resource'])
            ->except('show')
            ->middlewareFor(['store', 'update'], 'throttle:20,1');
        Route::resource('bank-accounts', BankAccountController::class)
            ->parameters(['bank-accounts' => 'bankAccount'])
            ->except('show');

        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->middleware('throttle:10,1')->name('settings.update');
        Route::resource('members', MemberController::class)
            ->parameters(['members' => 'member'])
            ->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::resource('admins', AdministratorController::class)
            ->parameters(['admins' => 'admin'])
            ->except('show');
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
