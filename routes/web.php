<?php

use App\Http\Controllers\OAuthClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // OAuth Application Management (Create, List, Update, Delete)
    Route::resource('oauth/clients', OAuthClientController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->names([
            'index' => 'clients.index',
            'store' => 'clients.store',
            'update' => 'clients.update',
            'destroy' => 'clients.destroy',
        ]);
});

// Single Sign-Out (SLO) - called by tenant apps to destroy central session
Route::get('/sso/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    $redirectUri = $request->query('redirect_uri');

    if ($redirectUri && filter_var($redirectUri, FILTER_VALIDATE_URL)) {
        return redirect()->away($redirectUri);
    }

    return redirect()->route('login');
})->name('sso.logout');

require __DIR__.'/settings.php';
