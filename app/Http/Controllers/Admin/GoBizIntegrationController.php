<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Services\GoBizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoBizIntegrationController extends Controller
{
    protected $goBizService;

    public function __construct(GoBizService $goBizService)
    {
        $this->goBizService = $goBizService;
    }

    public function connect(Request $request)
    {
        $shop = Shop::where('id', Auth::user()->shop_id)->firstOrFail();

        // Generate a random state for security
        $state = bin2hex(random_bytes(16));
        $request->session()->put('gobiz_oauth_state', $state);

        $url = $this->goBizService->getAuthorizationUrl($state);

        return redirect()->away($url);
    }

    public function callback(Request $request)
    {
        $shop = Shop::where('id', Auth::user()->shop_id)->firstOrFail();

        $state = $request->session()->pull('gobiz_oauth_state');

        if (! $state || $state !== $request->query('state')) {
            return redirect()->route('admin.dashboard')->with('error', 'Invalid OAuth state.');
        }

        if ($request->has('error')) {
            return redirect()->route('admin.dashboard')->with('error', 'GoBiz Authorization failed: '.$request->query('error_description'));
        }

        $code = $request->query('code');

        if ($this->goBizService->exchangeToken($shop, $code)) {
            // Need to set outlet ID ideally. In a real world scenario,
            // the user might have to select the outlet or we get it from GoBiz API.
            // For now, we assume success.
            return redirect()->route('admin.dashboard')->with('success', 'Berhasil terhubung dengan GoFood!');
        }

        return redirect()->route('admin.dashboard')->with('error', 'Gagal menukarkan token dengan GoBiz.');
    }

    public function syncCatalog(Request $request)
    {
        $shop = Shop::where('id', Auth::user()->shop_id)->firstOrFail();

        $request->validate([
            'outlet_id' => 'required|string',
        ]);

        if (! $shop->gobiz_outlet_id) {
            $shop->update(['gobiz_outlet_id' => $request->outlet_id]);
        }

        if ($this->goBizService->syncMenu($shop)) {
            return back()->with('success', 'Menu berhasil disinkronisasi ke GoFood!');
        }

        return back()->with('error', 'Gagal melakukan sinkronisasi menu ke GoFood.');
    }
}
