<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->string('search')->toString());
        $query = Product::query()->orderBy('name');
        if ($search !== '') {
            $query->where(fn ($builder) => $builder
                ->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%"));
        }
        $products = $query->get();
        $stockValue = Product::all()->sum(fn (Product $product) => $product->price * $product->stock);

        return Inertia::render('Inventory/Index', [
            'products' => $products->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'brand' => $product->brand,
                'category' => $product->category,
                'unit' => $product->unit,
                'price' => '৳ '.number_format((float) $product->price),
                'stock' => $product->stock,
                'reorder' => $product->reorder_level,
                'variants' => $product->variants()->count(),
                'status' => $product->stock_status,
            ])->values(),
            'filters' => ['search' => $search],
            'metrics' => [
                'total' => Product::count(),
                'lowStock' => Product::whereColumn('stock', '<=', 'reorder_level')->count(),
                'stockValue' => '৳ '.number_format($stockValue / 1000000, 2).'M',
                'categories' => Product::query()->distinct('category')->count('category'),
                'variants' => ProductVariant::count(),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Inventory/Create', ['units' => ['piece', 'kg', 'litre', 'box', 'pack', 'set']]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'sku' => ['required', 'string', 'max:40', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:80', 'unique:products,barcode'],
            'category' => ['required', 'string', 'max:80'],
            'brand' => ['nullable', 'string', 'max:80'],
            'unit' => ['nullable', 'string', 'max:30'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'price_label' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:1000'],
            'stock' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
        ]);

        $product = Product::create([
            ...$data,
            'sku' => strtoupper($data['sku']),
            'reorder_level' => $data['reorder_level'] ?? 10,
            'cost_price' => $data['cost_price'] ?? 0,
            'wholesale_price' => $data['wholesale_price'] ?? $data['price'],
            'unit' => $data['unit'] ?? 'piece',
            'status' => 'Active',
        ]);
        Branch::pluck('id')->each(fn (int $branchId) => BranchStock::create(['branch_id' => $branchId, 'product_id' => $product->id, 'quantity' => 0]));

        return to_route('inventory.index')->with('success', 'Product added to the inventory workspace.');
    }

    public function show(Product $product)
    {
        $product->load(['variants', 'branchStocks.branch']);
        return Inertia::render('Inventory/Show', ['product' => ['id' => $product->id, 'name' => $product->name, 'sku' => $product->sku, 'barcode' => $product->barcode, 'brand' => $product->brand, 'category' => $product->category, 'unit' => $product->unit, 'price' => (float) $product->price, 'costPrice' => (float) $product->cost_price, 'wholesalePrice' => (float) $product->wholesale_price, 'priceLabel' => $product->price_label, 'description' => $product->description, 'stock' => $product->stock, 'reorderLevel' => $product->reorder_level, 'status' => $product->status], 'variants' => $product->variants->map(fn (ProductVariant $variant) => ['name' => $variant->name, 'sku' => $variant->sku, 'barcode' => $variant->barcode, 'price' => '৳ '.number_format($variant->price), 'stock' => $variant->stock]), 'branchStocks' => $product->branchStocks->map(fn (BranchStock $stock) => ['branch' => $stock->branch->name, 'quantity' => $stock->quantity])]);
    }

    public function edit(Product $product)
    {
        return Inertia::render('Inventory/Edit', ['product' => ['id' => $product->id, 'name' => $product->name, 'sku' => $product->sku, 'barcode' => $product->barcode, 'brand' => $product->brand, 'category' => $product->category, 'unit' => $product->unit, 'price' => (float) $product->price, 'cost_price' => (float) $product->cost_price, 'wholesale_price' => (float) $product->wholesale_price, 'price_label' => $product->price_label, 'description' => $product->description, 'stock' => $product->stock, 'reorder_level' => $product->reorder_level], 'units' => ['piece', 'kg', 'litre', 'box', 'pack', 'set']]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'sku' => ['required', 'string', 'max:40', 'unique:products,sku,'.$product->id], 'barcode' => ['nullable', 'string', 'max:80', 'unique:products,barcode,'.$product->id], 'category' => ['required', 'string', 'max:80'], 'brand' => ['nullable', 'string', 'max:80'], 'unit' => ['required', 'string', 'max:30'], 'price' => ['required', 'numeric', 'min:0'], 'cost_price' => ['nullable', 'numeric', 'min:0'], 'wholesale_price' => ['nullable', 'numeric', 'min:0'], 'price_label' => ['nullable', 'string', 'max:30'], 'description' => ['nullable', 'string', 'max:1000'], 'stock' => ['required', 'integer', 'min:0'], 'reorder_level' => ['required', 'integer', 'min:0']]);
        $product->update([...$data, 'sku' => strtoupper($data['sku']), 'cost_price' => $data['cost_price'] ?? 0, 'wholesale_price' => $data['wholesale_price'] ?? $data['price']]);
        return to_route('inventory.show', $product)->with('success', 'Product details updated.');
    }

    public function destroy(Product $product)
    {
        if ($product->saleItems()->exists() || $product->returns()->exists() || $product->transfers()->exists() || $product->variants()->exists()) return to_route('inventory.show', $product)->with('success', 'This product has transaction history, so it was kept and marked archived.');
        $product->update(['status' => 'Archived']);
        return to_route('inventory.index')->with('success', 'Product archived from active inventory.');
    }

    public function variants(Request $request)
    {
        $search = trim($request->string('search')->toString());
        $query = ProductVariant::with('product')->latest('id');
        if ($search !== '') $query->where(fn ($builder) => $builder->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%")->orWhere('barcode', 'like', "%{$search}%"));
        return Inertia::render('Inventory/Variants', [
            'variants' => $query->get()->map(fn (ProductVariant $variant) => ['id' => $variant->id, 'product' => $variant->product->name, 'name' => $variant->name, 'sku' => $variant->sku, 'barcode' => $variant->barcode, 'price' => '৳ '.number_format($variant->price), 'stock' => $variant->stock, 'status' => $variant->stock <= $variant->reorder_level ? 'Low stock' : $variant->status]),
            'products' => Product::orderBy('name')->get(['id', 'name']),
            'filters' => ['search' => $search],
        ]);
    }

    public function createVariant()
    {
        return Inertia::render('Inventory/VariantCreate', ['products' => Product::orderBy('name')->get(['id', 'name'])]);
    }

    public function storeVariant(Request $request)
    {
        $data = $request->validate(['product_id' => ['required', 'exists:products,id'], 'name' => ['required', 'string', 'max:120'], 'sku' => ['required', 'string', 'max:40', 'unique:product_variants,sku'], 'barcode' => ['nullable', 'string', 'max:80', 'unique:product_variants,barcode'], 'price' => ['required', 'numeric', 'min:0'], 'stock' => ['required', 'integer', 'min:0'], 'reorder_level' => ['required', 'integer', 'min:0'], 'attributes' => ['nullable', 'string', 'max:500']]);
        $attributes = collect(explode(',', (string) ($data['attributes'] ?? '')))->mapWithKeys(function (string $pair) {
            [$key, $value] = array_pad(explode(':', trim($pair), 2), 2, '');
            return $key !== '' ? [$key => trim($value)] : [];
        })->all();
        ProductVariant::create([...$data, 'sku' => strtoupper($data['sku']), 'attributes' => $attributes]);
        return to_route('inventory.variants')->with('success', 'Product variant added to the catalogue.');
    }

    public function labels()
    {
        return Inertia::render('Inventory/Labels', ['products' => Product::whereNotNull('barcode')->orWhereNotNull('price_label')->orderBy('name')->get()->map(fn (Product $product) => ['name' => $product->name, 'sku' => $product->sku, 'barcode' => $product->barcode ?: $product->sku, 'price' => '৳ '.number_format($product->price), 'label' => $product->price_label ?: 'Retail'])]);
    }

    public function bulkUpdate(Request $request)
    {
        $data = $request->validate(['product_ids' => ['required', 'array', 'min:1'], 'product_ids.*' => ['integer', 'exists:products,id'], 'action' => ['required', 'in:activate,archive,reorder'], 'reorder_level' => ['nullable', 'integer', 'min:0']]);
        $updates = ['status' => $data['action'] === 'archive' ? 'Archived' : 'Active'];
        if ($data['action'] === 'reorder') $updates = ['reorder_level' => $data['reorder_level']];
        Product::whereIn('id', $data['product_ids'])->update($updates);
        return to_route('inventory.index')->with('success', 'Bulk inventory update applied.');
    }
}
