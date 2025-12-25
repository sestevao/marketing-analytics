<?php

use App\Http\Controllers\AdAccountController;
use App\Http\Controllers\ProfileController;
use App\Services\MarketingAnalyticsService;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function (Illuminate\Http\Request $request, MarketingAnalyticsService $service) {
    $user = $request->user();
    $accounts = $user->adAccounts()->latest()->get();
    $allLeads = [];

    foreach ($accounts as $account) {
        // Fetch last 30 days of data to get leads
        $analytics = $service->fetchAnalytics($account, now()->subDays(30), now());
        
        if (isset($analytics['leads_list'])) {
            foreach ($analytics['leads_list'] as $lead) {
                // Add account context to the lead
                $lead['account_name'] = $account->name;
                $lead['platform'] = $account->platform;
                $lead['account_id'] = $account->id;
                $allLeads[] = $lead;
            }
        }
    }

    // Sort by newest first
    usort($allLeads, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    // Pagination
    $page = $request->input('page', 1);
    $perPage = 15;
    $offset = ($page - 1) * $perPage;
    $items = array_slice($allLeads, $offset, $perPage);

    $recentLeads = new LengthAwarePaginator(
        $items,
        count($allLeads),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    return Inertia::render('Dashboard', [
        'accounts' => $accounts,
        'recentLeads' => $recentLeads,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/ad-accounts', [AdAccountController::class, 'store'])->name('ad-accounts.store');
    Route::delete('/ad-accounts/{adAccount}', [AdAccountController::class, 'destroy'])->name('ad-accounts.destroy');
    Route::get('/ad-accounts/{adAccount}/analytics', [AdAccountController::class, 'analytics'])->name('ad-accounts.analytics');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
