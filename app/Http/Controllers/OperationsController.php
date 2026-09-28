<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Cheque;
use App\Models\Contact;
use App\Models\CostCenter;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class OperationsController extends Controller
{
    public function banking()
    {
        $accounts = BankAccount::withCount('cheques')->orderBy('name')->get();
        return Inertia::render('Finance/Banking', [
            'accounts' => $accounts->map(fn (BankAccount $account) => ['id' => $account->id, 'name' => $account->name, 'bank' => $account->bank, 'accountNo' => $account->account_no, 'type' => $account->type, 'balance' => '৳ '.number_format($account->balance), 'cheques' => $account->cheques_count, 'status' => $account->status]),
            'summary' => ['balance' => '৳ '.number_format($accounts->sum(fn (BankAccount $account) => (float) $account->balance)), 'accounts' => $accounts->count(), 'active' => $accounts->where('status', 'Active')->count(), 'cheques' => Cheque::where('status', 'Pending')->count()],
        ]);
    }

    public function storeBankAccount(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'bank' => ['required', 'string', 'max:100'], 'account_no' => ['required', 'string', 'max:50', 'unique:bank_accounts,account_no'], 'type' => ['required', 'in:Current,Savings,Mobile wallet'], 'opening_balance' => ['required', 'numeric', 'min:0']]);
        BankAccount::create([...$data, 'balance' => $data['opening_balance'], 'status' => 'Active']);
        return to_route('banking.index')->with('success', 'Bank account added to the treasury view.');
    }

    public function cheques()
    {
        return Inertia::render('Finance/Cheques', [
            'cheques' => Cheque::with(['bankAccount', 'contact'])->latest('cheque_date')->latest('id')->get()->map(fn (Cheque $cheque) => ['number' => $cheque->cheque_no, 'bank' => $cheque->bankAccount->name, 'contact' => $cheque->contact?->name ?? 'Walk-in / internal', 'date' => $cheque->cheque_date->format('d M Y'), 'amount' => '৳ '.number_format($cheque->amount), 'direction' => $cheque->direction, 'status' => $cheque->status]),
            'accounts' => BankAccount::where('status', 'Active')->orderBy('name')->get(['id', 'name']),
            'contacts' => Contact::orderBy('name')->get(['id', 'name']),
            'summary' => ['pending' => Cheque::where('status', 'Pending')->count(), 'in' => '৳ '.number_format(Cheque::where('direction', 'In')->sum('amount')), 'out' => '৳ '.number_format(Cheque::where('direction', 'Out')->sum('amount'))],
        ]);
    }

    public function storeCheque(Request $request)
    {
        $data = $request->validate(['bank_account_id' => ['required', 'exists:bank_accounts,id'], 'contact_id' => ['nullable', 'exists:contacts,id'], 'cheque_no' => ['required', 'string', 'max:50'], 'cheque_date' => ['required', 'date'], 'amount' => ['required', 'numeric', 'min:1'], 'direction' => ['required', 'in:In,Out'], 'status' => ['required', 'in:Pending,Cleared,Bounced'], 'notes' => ['nullable', 'string', 'max:160']]);
        DB::transaction(function () use ($data) {
            Cheque::create($data);
            if ($data['status'] === 'Cleared') {
                $account = BankAccount::lockForUpdate()->findOrFail($data['bank_account_id']);
                $data['direction'] === 'In' ? $account->increment('balance', $data['amount']) : $account->decrement('balance', $data['amount']);
            }
        });
        return to_route('cheques.index')->with('success', "Cheque {$data['cheque_no']} was recorded.");
    }

    public function costCenters()
    {
        $centers = CostCenter::orderBy('name')->get();
        return Inertia::render('Finance/CostCenters', ['centers' => $centers->map(fn (CostCenter $center) => ['name' => $center->name, 'code' => $center->code, 'budget' => '৳ '.number_format($center->budget), 'spent' => '৳ '.number_format($center->spent), 'remaining' => '৳ '.number_format(max(0, $center->budget - $center->spent)), 'status' => $center->status]), 'summary' => ['centers' => $centers->count(), 'budget' => '৳ '.number_format($centers->sum('budget')), 'spent' => '৳ '.number_format($centers->sum('spent'))]]);
    }

    public function storeCostCenter(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'code' => ['required', 'string', 'max:30', 'unique:cost_centers,code'], 'budget' => ['required', 'numeric', 'min:0']]);
        CostCenter::create([...$data, 'spent' => 0, 'status' => 'Active']);
        return to_route('cost-centers.index')->with('success', 'Cost centre added to the planning view.');
    }

    public function production()
    {
        return Inertia::render('Production/Index', [
            'orders' => ProductionOrder::with(['bom.product', 'branch'])->latest('planned_on')->latest('id')->get()->map(fn (ProductionOrder $order) => ['id' => $order->id, 'number' => $order->order_no, 'bom' => $order->bom->name, 'product' => $order->bom->product->name, 'branch' => $order->branch->name, 'date' => $order->planned_on->format('d M Y'), 'quantity' => $order->output_quantity, 'status' => $order->status]),
            'boms' => Bom::with(['product', 'items.product'])->orderBy('name')->get()->map(fn (Bom $bom) => ['name' => $bom->name, 'product' => $bom->product->name, 'component' => $bom->items->map(fn (BomItem $item) => $item->product->name.' × '.$item->quantity)->join(', '), 'output' => $bom->output_quantity, 'status' => $bom->status]),
            'summary' => ['orders' => ProductionOrder::count(), 'planned' => ProductionOrder::where('status', 'Planned')->count(), 'completed' => ProductionOrder::where('status', 'Completed')->count(), 'recipes' => Bom::count()],
        ]);
    }

    public function createProduction()
    {
        return Inertia::render('Production/Create', ['products' => Product::orderBy('name')->get(['id', 'name', 'sku']), 'branches' => Branch::where('status', 'Active')->orderBy('name')->get(['id', 'name'])]);
    }

    public function storeProduction(Request $request)
    {
        $data = $request->validate(['product_id' => ['required', 'exists:products,id'], 'component_product_id' => ['required', 'exists:products,id', 'different:product_id'], 'component_quantity' => ['required', 'numeric', 'min:0.001'], 'branch_id' => ['required', 'exists:branches,id'], 'planned_on' => ['required', 'date'], 'output_quantity' => ['required', 'integer', 'min:1'], 'notes' => ['nullable', 'string', 'max:160']]);
        $order = DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);
            $bom = Bom::create(['product_id' => $product->id, 'name' => $product->name.' assembly recipe', 'output_quantity' => 1, 'status' => 'Active', 'notes' => $data['notes'] ?? null]);
            BomItem::create(['bom_id' => $bom->id, 'product_id' => $data['component_product_id'], 'quantity' => $data['component_quantity']]);
            $number = 'PRO-'.str_pad((string) (ProductionOrder::count() + 501), 4, '0', STR_PAD_LEFT);
            return ProductionOrder::create(['bom_id' => $bom->id, 'branch_id' => $data['branch_id'], 'order_no' => $number, 'planned_on' => $data['planned_on'], 'output_quantity' => $data['output_quantity'], 'status' => 'Planned', 'notes' => $data['notes'] ?? null]);
        });
        return to_route('production.index')->with('success', "Production order {$order->order_no} was planned.");
    }

    public function completeProduction(ProductionOrder $order)
    {
        if ($order->status === 'Completed') return to_route('production.index');
        DB::transaction(function () use ($order) {
            $order->load('bom.items');
            foreach ($order->bom->items as $item) {
                $required = (float) $item->quantity * $order->output_quantity;
                $product = Product::lockForUpdate()->findOrFail($item->product_id);
                $branchStock = BranchStock::where('branch_id', $order->branch_id)->where('product_id', $product->id)->lockForUpdate()->first();
                if (! $branchStock || $branchStock->quantity < $required || $product->stock < $required) throw ValidationException::withMessages(['production' => "{$product->name} does not have enough stock for this production run."]);
                $product->decrement('stock', $required);
                $branchStock->decrement('quantity', $required);
            }
            $finished = Product::lockForUpdate()->findOrFail($order->bom->product_id);
            $finished->increment('stock', $order->output_quantity);
            $destination = BranchStock::firstOrCreate(['branch_id' => $order->branch_id, 'product_id' => $finished->id], ['quantity' => 0]);
            $destination->increment('quantity', $order->output_quantity);
            $order->update(['status' => 'Completed']);
        });
        return to_route('production.index')->with('success', "Production order {$order->order_no} completed and consumed its components.");
    }

    public function access()
    {
        return Inertia::render('Settings/Access', ['users' => User::orderBy('name')->get(['id', 'name', 'email', 'role', 'created_at'])->map(fn (User $user) => ['name' => $user->name, 'email' => $user->email, 'role' => $user->role, 'joined' => $user->created_at?->format('d M Y')]), 'roles' => ['Administrator', 'Finance manager', 'Inventory manager', 'Operator', 'Viewer'], 'permissions' => ['Administrator' => ['Everything', 'User access', 'Financial posting'], 'Finance manager' => ['Accounting', 'Payments', 'Reports'], 'Inventory manager' => ['Inventory', 'Transfers', 'Production'], 'Operator' => ['Sales', 'POS checkout', 'Contacts'], 'Viewer' => ['Dashboard', 'Reports']]]);
    }

    public function storeAccess(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:120', 'unique:users,email'], 'role' => ['required', 'in:Administrator,Finance manager,Inventory manager,Operator,Viewer'], 'password' => ['required', 'string', 'min:8']]);
        User::create([...$data, 'password' => Hash::make($data['password'])]);
        return to_route('access.index')->with('success', 'Workspace user and role access created.');
    }
}
