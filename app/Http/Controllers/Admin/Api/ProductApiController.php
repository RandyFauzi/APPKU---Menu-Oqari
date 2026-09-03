<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $shopId = Auth::user()->shop_id;
        if (!$shopId) {
            return response()->json([]);
        }

        $perPage = $request->query('per_page', 25);

        $products = Product::where('shop_id', $shopId)
            ->with(['category', 'variants', 'modifierGroups.modifiers'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json($products);
    }
}
