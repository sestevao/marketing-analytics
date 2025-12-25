<?php

namespace App\Services;

use App\Models\AdAccount;
use Carbon\Carbon;

class MarketingAnalyticsService
{
    public function fetchAnalytics(AdAccount $account, $startDate, $endDate)
    {
        // In a real application, you would switch based on platform
        return match ($account->platform) {
            'google' => $this->fetchGoogleAds($account, $startDate, $endDate),
            'facebook' => $this->fetchFacebookAds($account, $startDate, $endDate),
            'linkedin' => $this->fetchLinkedInAds($account, $startDate, $endDate),
            default => [
                'impressions' => 0,
                'clicks' => 0,
                'conversions' => 0,
                'leads' => 0,
                'spend' => 0,
                'daily_data' => [],
                'leads_list' => [],
            ],
        };
    }

    protected function generateMockDailyData($days = 30)
    {
        $data = [];
        $date = Carbon::now()->subDays($days);

        for ($i = 0; $i <= $days; $i++) {
            $data[] = [
                'date' => $date->copy()->addDays($i)->format('Y-m-d'),
                'impressions' => rand(100, 1000),
                'clicks' => rand(10, 100),
                'conversions' => rand(5, 20),
                'leads' => rand(0, 5), // Subset of conversions typically
                'spend' => rand(10, 50),
            ];
        }

        return $data;
    }

    protected function generateMockLeads($count = 10)
    {
        $leads = [];
        $firstNames = ['John', 'Jane', 'Michael', 'Emily', 'Chris', 'Sarah', 'David', 'Laura'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis'];
        $domains = ['gmail.com', 'yahoo.com', 'outlook.com', 'company.com'];

        for ($i = 0; $i < $count; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            
            $leads[] = [
                'id' => uniqid(),
                'name' => "$firstName $lastName",
                'email' => strtolower($firstName . '.' . $lastName . rand(1, 99) . '@' . $domains[array_rand($domains)]),
                'phone' => '+1 ' . rand(200, 999) . '-' . rand(200, 999) . '-' . rand(1000, 9999),
                'status' => ['New', 'Contacted', 'Qualified', 'Converted'][rand(0, 3)],
                'created_at' => Carbon::now()->subDays(rand(0, 30))->format('Y-m-d H:i:s'),
                'campaign' => 'Campaign #' . rand(100, 999),
            ];
        }

        // Sort by date desc
        usort($leads, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $leads;
    }

    protected function fetchGoogleAds(AdAccount $account, $startDate, $endDate)
    {
        $dailyData = $this->generateMockDailyData();
        $leadsList = $this->generateMockLeads(rand(5, 15));

        return [
            'platform' => 'Google Ads',
            'impressions' => array_sum(array_column($dailyData, 'impressions')),
            'clicks' => array_sum(array_column($dailyData, 'clicks')),
            'conversions' => array_sum(array_column($dailyData, 'conversions')),
            'leads' => array_sum(array_column($dailyData, 'leads')),
            'spend' => array_sum(array_column($dailyData, 'spend')),
            'daily_data' => $dailyData,
            'leads_list' => $leadsList,
        ];
    }

    protected function fetchFacebookAds(AdAccount $account, $startDate, $endDate)
    {
        $dailyData = $this->generateMockDailyData();
        $leadsList = $this->generateMockLeads(rand(5, 15));

        return [
            'platform' => 'Facebook Ads',
            'impressions' => array_sum(array_column($dailyData, 'impressions')),
            'clicks' => array_sum(array_column($dailyData, 'clicks')),
            'conversions' => array_sum(array_column($dailyData, 'conversions')),
            'leads' => array_sum(array_column($dailyData, 'leads')),
            'spend' => array_sum(array_column($dailyData, 'spend')),
            'daily_data' => $dailyData,
            'leads_list' => $leadsList,
        ];
    }
    
    protected function fetchLinkedInAds(AdAccount $account, $startDate, $endDate)
    {
        $dailyData = $this->generateMockDailyData();
        $leadsList = $this->generateMockLeads(rand(5, 15));

        return [
            'platform' => 'LinkedIn Ads',
            'impressions' => array_sum(array_column($dailyData, 'impressions')),
            'clicks' => array_sum(array_column($dailyData, 'clicks')),
            'conversions' => array_sum(array_column($dailyData, 'conversions')),
            'leads' => array_sum(array_column($dailyData, 'leads')),
            'spend' => array_sum(array_column($dailyData, 'spend')),
            'daily_data' => $dailyData,
            'leads_list' => $leadsList,
        ];
    }
}
