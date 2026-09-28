<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $revenue = (float) Sale::sum('total');
        $inventoryValue = Product::all()->sum(fn (Product $product) => $product->price * $product->stock);
        $branches = Branch::withSum('sales', 'total')->get()->map(function (Branch $branch) {
            $sales = (float) ($branch->sales_sum_total ?? 0);
            $score = $branch->sales_target > 0 ? min(100, round(($sales / $branch->sales_target) * 100)) : 0;
            return ['name' => $branch->name, 'code' => $branch->code, 'sales' => '৳ '.number_format($sales / 1000000, 2).'M', 'status' => $score >= 85 ? 'Ahead' : 'On track', 'score' => $score];
        });
        $topProducts = Product::query()->orderByDesc('stock')->limit(3)->get()->map(fn (Product $product) => ['name' => $product->name, 'category' => $product->category, 'sold' => number_format(max(120, $product->stock * 16)).' units', 'progress' => min(92, max(35, $product->stock)), 'color' => $product->category === 'Electronics' ? 'amber' : ($product->category === 'Fashion' ? 'blue' : 'lime')]);

        return Inertia::render('Dashboard/Index', [
            'kpis' => [
                ['label' => 'Net revenue', 'value' => '৳ '.number_format($revenue / 1000000, 2).'M', 'change' => '+18.6%', 'tone' => 'lime', 'icon' => 'TrendingUp'],
                ['label' => 'Inventory value', 'value' => '৳ '.number_format($inventoryValue / 1000000, 2).'M', 'change' => '+7.4%', 'tone' => 'blue', 'icon' => 'Package'],
                ['label' => 'Outstanding', 'value' => '৳ 682K', 'change' => '-4.2%', 'tone' => 'amber', 'icon' => 'WalletCards'],
                ['label' => 'Active branches', 'value' => str_pad((string) Branch::count(), 2, '0', STR_PAD_LEFT), 'change' => 'All healthy', 'tone' => 'violet', 'icon' => 'GitBranch'],
            ],
            'salesTrend' => ['labels' => ['01 Sep', '05 Sep', '10 Sep', '15 Sep', '20 Sep', '25 Sep', '30 Sep'], 'values' => [42, 55, 48, 72, 64, 84, 92]],
            'recentActivity' => [
                ['title' => 'Wholesale invoice #INV-2034 paid', 'meta' => 'California Fried Chicken · 12 min ago', 'amount' => '+ ৳ 48,500', 'type' => 'positive'],
                ['title' => 'Purchase order #PO-884 received', 'meta' => 'Nahar Traders · 46 min ago', 'amount' => '৳ 126,400', 'type' => 'neutral'],
                ['title' => 'Low stock threshold reached', 'meta' => 'Premium Basmati Rice 25kg · 1 hr ago', 'amount' => 'Review', 'type' => 'warning'],
                ['title' => 'Branch transfer completed', 'meta' => 'Dhanmondi → Uttara · 2 hrs ago', 'amount' => '৳ 84,000', 'type' => 'neutral'],
            ],
            'topProducts' => $topProducts,
            'branches' => $branches,
        ]);
    }
}
