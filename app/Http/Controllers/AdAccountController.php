<?php

namespace App\Http\Controllers;

use App\Models\AdAccount;
use App\Services\MarketingAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AdAccountController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'platform' => 'required|string|in:google,facebook,linkedin,other',
            'account_id' => 'nullable|string|max:255',
            'access_token' => 'nullable|string',
        ]);

        $request->user()->adAccounts()->create($validated);

        return Redirect::back();
    }

    public function destroy(Request $request, AdAccount $adAccount)
    {
        if ($request->user()->id !== $adAccount->user_id) {
            abort(403);
        }

        $adAccount->delete();

        return Redirect::back();
    }

    public function analytics(Request $request, AdAccount $adAccount, MarketingAnalyticsService $service)
    {
        if ($request->user()->id !== $adAccount->user_id) {
            abort(403);
        }

        $data = $service->fetchAnalytics($adAccount, now()->subDays(30), now());

        return response()->json($data);
    }
}
