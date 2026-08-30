<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Shop;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoBizService
{
    protected $clientId;

    protected $clientSecret;

    protected $partnerId;

    protected $oauthBaseUrl;

    protected $apiBaseUrl;

    public function __construct()
    {
        $this->clientId = config('gobiz.client_id');
        $this->clientSecret = config('gobiz.client_secret');
        $this->partnerId = config('gobiz.partner_id');

        $env = config('gobiz.env');
        $this->oauthBaseUrl = config("gobiz.urls.{$env}.oauth");
        $this->apiBaseUrl = config("gobiz.urls.{$env}.api");
    }

    /**
     * Get the OAuth authorization URL
     */
    public function getAuthorizationUrl(string $state): string
    {
        $query = http_build_query([
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'scope' => 'offline',
            'redirect_uri' => route('admin.integrations.gobiz.callback'),
            'state' => $state,
        ]);

        return "{$this->oauthBaseUrl}/oauth/v2/auth?{$query}";
    }

    /**
     * Exchange auth code for access token
     */
    public function exchangeToken(Shop $shop, string $authCode): bool
    {
        $response = Http::asForm()->post("{$this->oauthBaseUrl}/oauth/v2/token", [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'redirect_uri' => route('admin.integrations.gobiz.callback'),
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $shop->update([
                'gobiz_access_token' => $data['access_token'],
                'gobiz_refresh_token' => $data['refresh_token'],
                'gobiz_token_expires_at' => now()->addSeconds($data['expires_in']),
            ]);

            return true;
        }

        Log::error('GoBiz Token Exchange Failed', ['response' => $response->body()]);

        return false;
    }

    /**
     * Refresh access token
     */
    public function refreshToken(Shop $shop): bool
    {
        if (! $shop->gobiz_refresh_token) {
            return false;
        }

        $response = Http::asForm()->post("{$this->oauthBaseUrl}/oauth/v2/token", [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $shop->gobiz_refresh_token,
        ]);

        if ($response->successful()) {
            $data = $response->json();

            $shop->update([
                'gobiz_access_token' => $data['access_token'],
                'gobiz_refresh_token' => $data['refresh_token'],
                'gobiz_token_expires_at' => now()->addSeconds($data['expires_in']),
            ]);

            return true;
        }

        Log::error('GoBiz Token Refresh Failed', ['shop_id' => $shop->id, 'response' => $response->body()]);

        return false;
    }

    /**
     * Sync menu catalog to GoBiz
     */
    public function syncMenu(Shop $shop): bool
    {
        if (! $shop->gobiz_access_token || ! $shop->gobiz_outlet_id) {
            return false;
        }

        if ($shop->gobiz_token_expires_at && $shop->gobiz_token_expires_at->isPast()) {
            $this->refreshToken($shop);
        }

        // Format products to GoBiz Catalog schema
        $products = Product::where('shop_id', $shop->id)->get();

        $categories = $products->groupBy('category_name')->map(function ($items, $categoryName) {
            return [
                'name' => $categoryName,
                'items' => $items->map(function ($item) {
                    return [
                        'partner_item_id' => (string) $item->id,
                        'name' => $item->name,
                        'price' => (float) $item->price,
                        'status' => $item->is_sold_out ? 'UNAVAILABLE' : 'AVAILABLE',
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        $payload = [
            'partner_id' => $this->partnerId,
            'categories' => $categories,
        ];

        $response = Http::withToken($shop->gobiz_access_token)
            ->put("{$this->apiBaseUrl}/integrations/gofood/outlets/{$shop->gobiz_outlet_id}/v1/catalog", $payload);

        if ($response->successful()) {
            return true;
        }

        Log::error('GoBiz Menu Sync Failed', ['shop_id' => $shop->id, 'response' => $response->body()]);

        return false;
    }
}
