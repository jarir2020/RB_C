<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Contact;
use App\Models\Employee;
use App\Models\LedgerAccount;
use App\Models\LedgerEntry;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class WorkspaceController extends Controller
{
    public function pos()
    {
        return Inertia::render('Pos/Index', [
            'customers' => Contact::where('kind', 'Customer')->orderBy('name')->get(['id', 'name']),
            'branches' => Branch::orderBy('name')->get(['id', 'name']),
            'products' => Product::with('branchStocks')->where('stock', '>', 0)->orderBy('name')->get()->map(fn (Product $product) => ['id' => $product->id, 'name' => $product->name, 'sku' => $product->sku, 'price' => (float) $product->price, 'stock' => $product->stock, 'stocks' => $product->branchStocks->mapWithKeys(fn (BranchStock $stock) => [(string) $stock->branch_id => $stock->quantity])]),
            'paymentMethods' => ['Cash', 'Mobile banking', 'Card', 'On account'],
        ]);
    }

    public function storePos(Request $request)
    {
        $data = $request->validate([
            'contact_id' => ['required', 'exists:contacts,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'payment_method' => ['required', 'in:Cash,Mobile banking,Card,On account'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $sale = DB::transaction(function () use ($data) {
            $productIds = collect($data['items'])->pluck('product_id')->map(fn ($id) => (int) $id)->unique();
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');
            $branchStocks = BranchStock::where('branch_id', $data['branch_id'])->whereIn('product_id', $productIds)->lockForUpdate()->get()->keyBy('product_id');
            $requested = collect($data['items'])->groupBy('product_id')->map(fn ($lines) => $lines->sum('quantity'));
            foreach ($requested as $productId => $quantity) {
                $product = $products->get((int) $productId);
                $branchStock = $branchStocks->get((int) $productId);
                if (! $product || ! $branchStock || $branchStock->quantity < $quantity) {
                    throw ValidationException::withMessages(["items.0.quantity" => "{$product?->name} has only {$branchStock?->quantity} unit(s) at the selected branch."]);
                }
            }

            $lineItems = [];
            $total = 0;
            foreach ($data['items'] as $item) {
                $product = $products->get((int) $item['product_id']);
                $lineTotal = (float) $product->price * (int) $item['quantity'];
                $total += $lineTotal;
                $lineItems[] = ['product' => $product, 'quantity' => (int) $item['quantity'], 'line_total' => $lineTotal];
            }

            $number = 'INV-'.str_pad((string) (Sale::count() + 2031), 4, '0', STR_PAD_LEFT);
            $status = $data['payment_method'] === 'On account' ? 'Pending' : 'Paid';
            $sale = Sale::create(['invoice_no' => $number, 'contact_id' => $data['contact_id'], 'branch_id' => $data['branch_id'], 'sold_at' => now()->toDateString(), 'total' => $total, 'status' => $status, 'channel' => 'POS']);

            foreach ($lineItems as $line) {
                SaleItem::create(['sale_id' => $sale->id, 'product_id' => $line['product']->id, 'quantity' => $line['quantity'], 'unit_price' => $line['product']->price, 'line_total' => $line['line_total']]);
                $line['product']->decrement('stock', $line['quantity']);
                $branchStocks[$line['product']->id]->decrement('quantity', $line['quantity']);
            }

            if ($status === 'Paid') {
                Payment::create(['payment_no' => 'PAY-'.str_pad((string) (Payment::count() + 1001), 4, '0', STR_PAD_LEFT), 'sale_id' => $sale->id, 'contact_id' => $sale->contact_id, 'branch_id' => $sale->branch_id, 'paid_on' => now()->toDateString(), 'amount' => $total, 'method' => $data['payment_method'], 'reference' => 'POS-'.$number, 'status' => 'Completed']);
            }

            return $sale;
        });

        return to_route('pos.index')->with('success', "POS checkout {$sale->invoice_no} completed successfully.");
    }

    public function sales(Request $request)
    {
        $status = $request->string('status')->toString();
        $query = Sale::with('contact')->latest('sold_at');
        if (in_array($status, ['Paid', 'Pending', 'Overdue'], true)) $query->where('status', $status);
        $orders = $query->get();
        $total = (float) Sale::sum('total');
        $due = (float) Sale::where('status', '!=', 'Paid')->sum('total');
        return Inertia::render('Sales/Index', [
            'summary' => ['gross' => '৳ '.number_format($total / 1000000, 2).'M', 'orders' => number_format(Sale::count()), 'average' => '৳ '.number_format(Sale::avg('total') ?: 0), 'due' => '৳ '.number_format($due / 1000).'K'],
            'orders' => $orders->map(fn (Sale $sale) => ['number' => $sale->invoice_no, 'customer' => $sale->contact->name, 'date' => $sale->sold_at->format('d M Y'), 'total' => '৳ '.number_format($sale->total), 'status' => $sale->status, 'channel' => $sale->channel]),
            'filters' => ['status' => $status],
        ]);
    }

    public function createSale()
    {
        return Inertia::render('Sales/Create', ['customers' => Contact::where('kind', 'Customer')->orderBy('name')->get(['id', 'name']), 'branches' => Branch::orderBy('name')->get(['id', 'name'])]);
    }

    public function storeSale(Request $request)
    {
        $data = $request->validate(['contact_id' => ['required', 'exists:contacts,id'], 'branch_id' => ['required', 'exists:branches,id'], 'total' => ['required', 'numeric', 'min:1'], 'channel' => ['required', 'string', 'max:40']]);
        $number = 'INV-'.str_pad((string) (Sale::count() + 2031), 4, '0', STR_PAD_LEFT);
        Sale::create([...$data, 'invoice_no' => $number, 'sold_at' => now()->toDateString(), 'status' => 'Pending']);
        return to_route('sales.index')->with('success', "Sale {$number} was created and is waiting for approval.");
    }

    public function createPurchase()
    {
        return Inertia::render('Purchases/Create', ['vendors' => Contact::where('kind', 'Vendor')->orderBy('name')->get(['id', 'name']), 'branches' => Branch::orderBy('name')->get(['id', 'name'])]);
    }

    public function storePurchase(Request $request)
    {
        $data = $request->validate(['contact_id' => ['required', 'exists:contacts,id'], 'branch_id' => ['required', 'exists:branches,id'], 'total' => ['required', 'numeric', 'min:1'], 'items_count' => ['required', 'integer', 'min:1']]);
        $number = 'PO-'.str_pad((string) (Purchase::count() + 881), 3, '0', STR_PAD_LEFT);
        Purchase::create([...$data, 'order_no' => $number, 'ordered_at' => now()->toDateString(), 'status' => 'Draft']);
        return to_route('purchases.index')->with('success', "Purchase order {$number} was saved as a draft.");
    }

    public function createContact()
    {
        return Inertia::render('Contacts/Create');
    }

    public function storeContact(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'kind' => ['required', 'in:Customer,Vendor'], 'email' => ['nullable', 'email', 'max:120'], 'phone' => ['nullable', 'string', 'max:30']]);
        Contact::create($data);
        return to_route('contacts.index')->with('success', 'Contact added to the relationship centre.');
    }

    public function purchases(Request $request)
    {
        $status = $request->string('status')->toString();
        $query = Purchase::with('contact')->latest('ordered_at');
        if (in_array($status, ['Received', 'In transit', 'Draft'], true)) $query->where('status', $status);
        $orders = $query->get();
        $total = (float) Purchase::sum('total');
        $pending = (float) Purchase::whereIn('status', ['In transit', 'Draft'])->sum('total');
        return Inertia::render('Purchases/Index', [
            'summary' => ['purchases' => '৳ '.number_format($total / 1000000, 2).'M', 'received' => '৳ '.number_format((float) Purchase::where('status', 'Received')->sum('total') / 1000).'K', 'pending' => '৳ '.number_format($pending / 1000).'K', 'vendors' => number_format(Contact::where('kind', 'Vendor')->count())],
            'orders' => $orders->map(fn (Purchase $purchase) => ['number' => $purchase->order_no, 'vendor' => $purchase->contact->name, 'date' => $purchase->ordered_at->format('d M Y'), 'total' => '৳ '.number_format($purchase->total), 'status' => $purchase->status, 'items' => $purchase->items_count]),
            'filters' => ['status' => $status],
        ]);
    }

    public function invoices(Request $request)
    {
        $status = $request->string('status')->toString();
        $query = Sale::with(['contact', 'branch'])->withSum('payments', 'amount')->latest('sold_at');
        if (in_array($status, ['Paid', 'Pending', 'Overdue'], true)) $query->where('status', $status);
        $invoices = $query->get();
        $total = (float) Sale::sum('total');
        $collected = (float) Payment::sum('amount');

        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices->map(fn (Sale $sale) => ['number' => $sale->invoice_no, 'customer' => $sale->contact->name, 'branch' => $sale->branch->name, 'date' => $sale->sold_at->format('d M Y'), 'total' => '৳ '.number_format($sale->total), 'collected' => '৳ '.number_format($sale->payments_sum_amount ?? 0), 'status' => $sale->status]),
            'filters' => ['status' => $status],
            'summary' => ['total' => number_format(Sale::count()), 'paid' => number_format(Sale::where('status', 'Paid')->count()), 'open' => number_format(Sale::where('status', '!=', 'Paid')->count()), 'due' => '৳ '.number_format(max(0, $total - $collected))],
        ]);
    }

    public function payments()
    {
        $payments = Payment::with(['sale', 'contact', 'branch'])->latest('paid_on')->latest('id')->get();
        return Inertia::render('Payments/Index', [
            'payments' => $payments->map(fn (Payment $payment) => ['number' => $payment->payment_no, 'invoice' => $payment->sale?->invoice_no ?? 'Account payment', 'customer' => $payment->contact->name, 'branch' => $payment->branch->name, 'date' => $payment->paid_on->format('d M Y'), 'amount' => '৳ '.number_format($payment->amount), 'method' => $payment->method, 'status' => $payment->status]),
            'summary' => ['collected' => '৳ '.number_format((float) Payment::sum('amount')), 'count' => number_format(Payment::count()), 'today' => '৳ '.number_format((float) Payment::whereDate('paid_on', now()->toDateString())->sum('amount')), 'methods' => number_format(Payment::distinct('method')->count('method'))],
        ]);
    }

    public function createPayment()
    {
        $sales = Sale::with('contact')->withSum('payments', 'amount')->where('status', '!=', 'Paid')->orderBy('invoice_no')->get();
        return Inertia::render('Payments/Create', ['sales' => $sales->map(fn (Sale $sale) => ['id' => $sale->id, 'invoice' => $sale->invoice_no, 'customer' => $sale->contact->name, 'total' => (float) $sale->total, 'outstanding' => max(0, (float) $sale->total - (float) ($sale->payments_sum_amount ?? 0))]), 'methods' => ['Bank transfer', 'Cash', 'Mobile banking', 'Cheque']]);
    }

    public function storePayment(Request $request)
    {
        $data = $request->validate(['sale_id' => ['required', 'exists:sales,id'], 'amount' => ['required', 'numeric', 'min:1'], 'method' => ['required', 'in:Bank transfer,Cash,Mobile banking,Cheque'], 'paid_on' => ['required', 'date'], 'reference' => ['nullable', 'string', 'max:80']]);
        $sale = Sale::findOrFail($data['sale_id']);
        $number = 'PAY-'.str_pad((string) (Payment::count() + 1001), 4, '0', STR_PAD_LEFT);
        Payment::create([...$data, 'payment_no' => $number, 'contact_id' => $sale->contact_id, 'branch_id' => $sale->branch_id, 'status' => 'Completed']);
        $collected = (float) Payment::where('sale_id', $sale->id)->sum('amount');
        if ($collected >= (float) $sale->total) $sale->update(['status' => 'Paid']);
        return to_route('payments.index')->with('success', "Payment {$number} was recorded successfully.");
    }

    public function returns()
    {
        $returns = SaleReturn::with(['sale', 'contact', 'branch', 'product'])->latest('returned_on')->latest('id')->get();
        return Inertia::render('Returns/Index', [
            'returns' => $returns->map(fn (SaleReturn $return) => ['number' => $return->return_no, 'invoice' => $return->sale->invoice_no, 'customer' => $return->contact->name, 'product' => $return->product->name, 'quantity' => $return->quantity, 'date' => $return->returned_on->format('d M Y'), 'refund' => '৳ '.number_format($return->refund_amount), 'reason' => $return->reason, 'status' => $return->status]),
            'summary' => ['count' => number_format(SaleReturn::count()), 'units' => number_format((int) SaleReturn::sum('quantity')), 'refunds' => '৳ '.number_format((float) SaleReturn::sum('refund_amount')), 'today' => number_format(SaleReturn::whereDate('returned_on', now()->toDateString())->count())],
        ]);
    }

    public function createReturn()
    {
        $sales = Sale::with(['contact', 'items.product'])->whereHas('items')->orderByDesc('sold_at')->get();
        return Inertia::render('Returns/Create', ['sales' => $sales->map(fn (Sale $sale) => ['id' => $sale->id, 'invoice' => $sale->invoice_no, 'customer' => $sale->contact->name, 'items' => $sale->items->map(fn (SaleItem $item) => ['id' => $item->product_id, 'name' => $item->product->name, 'sku' => $item->product->sku, 'quantity' => $item->quantity, 'unit_price' => (float) $item->unit_price])->values()]), 'reasons' => ['Customer changed quantity', 'Damaged item', 'Wrong item supplied', 'Quality concern']]);
    }

    public function storeReturn(Request $request)
    {
        $data = $request->validate(['sale_id' => ['required', 'exists:sales,id'], 'product_id' => ['required', 'exists:products,id'], 'quantity' => ['required', 'integer', 'min:1'], 'returned_on' => ['required', 'date'], 'reason' => ['required', 'string', 'max:120']]);
        $sale = Sale::with('items')->findOrFail($data['sale_id']);
        $item = $sale->items->firstWhere('product_id', (int) $data['product_id']);
        if (! $item) throw ValidationException::withMessages(['product_id' => 'That product was not sold on the selected invoice.']);
        $alreadyReturned = (int) SaleReturn::where('sale_id', $sale->id)->where('product_id', $item->product_id)->sum('quantity');
        $available = $item->quantity - $alreadyReturned;
        if ($data['quantity'] > $available) throw ValidationException::withMessages(['quantity' => "Only {$available} unit(s) remain available for return."]);
        $number = 'RET-'.str_pad((string) (SaleReturn::count() + 3001), 4, '0', STR_PAD_LEFT);
        SaleReturn::create([...$data, 'return_no' => $number, 'contact_id' => $sale->contact_id, 'branch_id' => $sale->branch_id, 'refund_amount' => (float) $item->unit_price * $data['quantity'], 'status' => 'Completed']);
        Product::whereKey($item->product_id)->increment('stock', $data['quantity']);
        BranchStock::firstOrCreate(['branch_id' => $sale->branch_id, 'product_id' => $item->product_id], ['quantity' => 0]);
        BranchStock::where('branch_id', $sale->branch_id)->where('product_id', $item->product_id)->increment('quantity', $data['quantity']);
        return to_route('returns.index')->with('success', "Return {$number} was recorded and stock was restocked.");
    }

    public function transfers()
    {
        $transfers = StockTransfer::with(['product', 'fromBranch', 'toBranch'])->latest('transferred_on')->latest('id')->get();
        return Inertia::render('Transfers/Index', [
            'transfers' => $transfers->map(fn (StockTransfer $transfer) => ['id' => $transfer->id, 'number' => $transfer->transfer_no, 'product' => $transfer->product->name, 'sku' => $transfer->product->sku, 'from' => $transfer->fromBranch->name, 'to' => $transfer->toBranch->name, 'quantity' => $transfer->quantity, 'date' => $transfer->transferred_on->format('d M Y'), 'status' => $transfer->status, 'note' => $transfer->note]),
            'summary' => ['count' => number_format(StockTransfer::count()), 'units' => number_format((int) StockTransfer::sum('quantity')), 'inTransit' => number_format(StockTransfer::where('status', 'In transit')->count()), 'received' => number_format(StockTransfer::where('status', 'Received')->count())],
        ]);
    }

    public function createTransfer()
    {
        return Inertia::render('Transfers/Create', ['products' => Product::with('branchStocks')->where('stock', '>', 0)->orderBy('name')->get()->map(fn (Product $product) => ['id' => $product->id, 'name' => $product->name, 'sku' => $product->sku, 'stock' => $product->stock, 'stocks' => $product->branchStocks->mapWithKeys(fn (BranchStock $stock) => [(string) $stock->branch_id => $stock->quantity])]), 'branches' => Branch::orderBy('name')->get(['id', 'name'])]);
    }

    public function storeTransfer(Request $request)
    {
        $data = $request->validate(['product_id' => ['required', 'exists:products,id'], 'from_branch_id' => ['required', 'exists:branches,id', 'different:to_branch_id'], 'to_branch_id' => ['required', 'exists:branches,id'], 'quantity' => ['required', 'integer', 'min:1'], 'transferred_on' => ['required', 'date'], 'note' => ['nullable', 'string', 'max:160']]);
        $transfer = DB::transaction(function () use ($data) {
            $stock = BranchStock::where('branch_id', $data['from_branch_id'])->where('product_id', $data['product_id'])->lockForUpdate()->first();
            if (! $stock || $stock->quantity < $data['quantity']) {
                throw ValidationException::withMessages(['quantity' => 'The source branch does not have enough allocated stock for this movement.']);
            }
            $number = 'TRF-'.str_pad((string) (StockTransfer::count() + 4001), 4, '0', STR_PAD_LEFT);
            $stock->decrement('quantity', $data['quantity']);
            return StockTransfer::create([...$data, 'transfer_no' => $number, 'status' => 'In transit']);
        });
        return to_route('transfers.index')->with('success', "Transfer {$transfer->transfer_no} was added to the movement log.");
    }

    public function receiveTransfer(StockTransfer $transfer)
    {
        if ($transfer->status !== 'In transit') return to_route('transfers.index');
        DB::transaction(function () use ($transfer) {
            $destination = BranchStock::firstOrCreate(['branch_id' => $transfer->to_branch_id, 'product_id' => $transfer->product_id], ['quantity' => 0]);
            $destination->increment('quantity', $transfer->quantity);
            $transfer->update(['status' => 'Received']);
        });
        return to_route('transfers.index')->with('success', "Transfer {$transfer->transfer_no} marked as received.");
    }

    public function ledger(Request $request)
    {
        $search = trim($request->string('search')->toString());
        $accountId = (int) $request->integer('account_id');
        $query = LedgerEntry::with(['account', 'branch'])->latest('entry_date')->latest('id');
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($accountId > 0) $query->where('ledger_account_id', $accountId);
        $entries = $query->get();

        return Inertia::render('Accounting/Ledger', [
            'entries' => $entries->map(fn (LedgerEntry $entry) => [
                'date' => $entry->entry_date->format('d M Y'),
                'reference' => $entry->reference,
                'description' => $entry->description,
                'account' => $entry->account->code.' · '.$entry->account->name,
                'branch' => $entry->branch?->name ?? 'All branches',
                'debit' => $entry->debit > 0 ? '৳ '.number_format($entry->debit) : '—',
                'credit' => $entry->credit > 0 ? '৳ '.number_format($entry->credit) : '—',
            ]),
            'accounts' => LedgerAccount::orderBy('code')->get(['id', 'code', 'name', 'type']),
            'filters' => ['search' => $search, 'account_id' => $accountId ?: null],
            'summary' => [
                'entries' => number_format($entries->count()),
                'debit' => '৳ '.number_format((float) $entries->sum(fn (LedgerEntry $entry) => (float) $entry->debit)),
                'credit' => '৳ '.number_format((float) $entries->sum(fn (LedgerEntry $entry) => (float) $entry->credit)),
                'accounts' => number_format($entries->pluck('ledger_account_id')->unique()->count()),
            ],
        ]);
    }

    public function createLedger()
    {
        return Inertia::render('Accounting/LedgerCreate', [
            'accounts' => LedgerAccount::orderBy('code')->get(['id', 'code', 'name', 'type']),
            'branches' => Branch::where('status', 'Active')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function storeLedger(Request $request)
    {
        $data = $request->validate([
            'ledger_account_id' => ['required', 'exists:ledger_accounts,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'entry_date' => ['required', 'date'],
            'reference' => ['required', 'string', 'max:40'],
            'description' => ['required', 'string', 'max:160'],
            'debit' => ['nullable', 'numeric', 'min:0'],
            'credit' => ['nullable', 'numeric', 'min:0'],
        ]);
        $debit = round((float) ($data['debit'] ?? 0), 2);
        $credit = round((float) ($data['credit'] ?? 0), 2);
        if (($debit > 0) === ($credit > 0)) {
            throw ValidationException::withMessages([
                'debit' => 'Enter exactly one debit or credit amount.',
                'credit' => 'Enter exactly one debit or credit amount.',
            ]);
        }

        $entry = DB::transaction(function () use ($data, $debit, $credit) {
            $account = LedgerAccount::lockForUpdate()->findOrFail($data['ledger_account_id']);
            $entry = LedgerEntry::create([
                'ledger_account_id' => $account->id,
                'branch_id' => $data['branch_id'] ?? null,
                'entry_date' => $data['entry_date'],
                'reference' => $data['reference'],
                'description' => $data['description'],
                'debit' => $debit,
                'credit' => $credit,
                'status' => 'Posted',
            ]);
            $delta = in_array($account->type, ['Asset', 'Expense'], true) ? $debit - $credit : $credit - $debit;
            $account->increment('balance', $delta);
            return $entry;
        });

        return to_route('ledger.index')->with('success', "Voucher {$entry->reference} was posted to the ledger.");
    }

    public function accounting()
    {
        $accountRecords = LedgerAccount::withCount('entries')->orderBy('code')->get();
        $balanceFor = fn (string $code): float => (float) ($accountRecords->firstWhere('code', $code)?->balance ?? 0);
        $cash = $balanceFor('1001');
        $receivables = $balanceFor('1104');
        $payables = $balanceFor('2001');

        return Inertia::render('Accounting/Index', [
            'accounts' => $accountRecords->map(fn (LedgerAccount $account) => ['code' => $account->code, 'name' => $account->name, 'type' => $account->type, 'balance' => '৳ '.number_format($account->balance), 'trend' => $account->trend, 'entries' => $account->entries_count]),
            'metrics' => [
                ['label' => 'Cash & bank', 'value' => $this->compactMoney($cash), 'note' => 'Posted ledger balance'],
                ['label' => 'Receivables', 'value' => $this->compactMoney($receivables), 'note' => 'Collection watch'],
                ['label' => 'Payables', 'value' => $this->compactMoney($payables), 'note' => 'Within payment plan'],
                ['label' => 'Net position', 'value' => $this->compactMoney($cash + $receivables - $payables), 'note' => 'Assets less payables'],
            ],
            'recentEntries' => LedgerEntry::with(['account', 'branch'])->latest('entry_date')->latest('id')->limit(5)->get()->map(fn (LedgerEntry $entry) => ['reference' => $entry->reference, 'description' => $entry->description, 'account' => $entry->account->code, 'date' => $entry->entry_date->format('d M Y'), 'amount' => $entry->debit > 0 ? 'Dr ৳ '.number_format($entry->debit) : 'Cr ৳ '.number_format($entry->credit)]),
            'reports' => ['Trial balance', 'Profit & loss', 'Balance sheet', 'Cash flow'],
        ]);
    }

    public function branches()
    {
        return Inertia::render('Branches/Index', ['branches' => Branch::withSum('sales', 'total')->orderBy('name')->get()->map(function (Branch $branch) { $sales = (float) ($branch->sales_sum_total ?? 0); return ['name' => $branch->name, 'code' => $branch->code, 'city' => $branch->city, 'sales' => '৳ '.number_format($sales), 'target' => '৳ '.number_format($branch->sales_target), 'score' => $branch->sales_target ? min(100, round($sales / $branch->sales_target * 100)) : 0, 'status' => $branch->status]; })]);
    }

    public function createBranch()
    {
        return Inertia::render('Branches/Create');
    }

    public function storeBranch(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'code' => ['required', 'string', 'max:20', 'unique:branches,code'], 'city' => ['required', 'string', 'max:80'], 'sales_target' => ['required', 'numeric', 'min:0']]);
        Branch::create($data);
        return to_route('branches.index')->with('success', 'Branch added to the network view.');
    }

    public function employees(Request $request)
    {
        $search = trim($request->string('search')->toString());
        $query = Employee::with('branch')->orderBy('name');
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")->orWhere('role', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Employees/Index', [
            'employees' => $query->get()->map(fn (Employee $employee) => [
                'name' => $employee->name,
                'role' => $employee->role,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'branch' => $employee->branch->name,
                'joined' => $employee->joined_on?->format('d M Y'),
                'status' => $employee->status,
            ]),
            'filters' => ['search' => $search],
            'summary' => ['total' => Employee::count(), 'active' => Employee::where('status', 'Active')->count(), 'branches' => Employee::distinct('branch_id')->count('branch_id'), 'roles' => Employee::distinct('role')->count('role')],
        ]);
    }

    public function createEmployee()
    {
        return Inertia::render('Employees/Create', ['branches' => Branch::orderBy('name')->get(['id', 'name'])]);
    }

    public function storeEmployee(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'role' => ['required', 'string', 'max:80'], 'email' => ['nullable', 'email', 'max:120'], 'phone' => ['nullable', 'string', 'max:30'], 'branch_id' => ['required', 'exists:branches,id'], 'joined_on' => ['nullable', 'date']]);
        Employee::create([...$data, 'status' => 'Active']);
        return to_route('employees.index')->with('success', 'Employee added to the people directory.');
    }

    public function warehouses()
    {
        $warehouses = Warehouse::with('branch')->orderBy('name')->get();
        $capacity = (int) $warehouses->sum('capacity');
        $used = (int) $warehouses->sum(fn (Warehouse $warehouse) => round($warehouse->capacity * $warehouse->utilization / 100));

        return Inertia::render('Warehouses/Index', [
            'warehouses' => $warehouses->map(fn (Warehouse $warehouse) => ['name' => $warehouse->name, 'code' => $warehouse->code, 'branch' => $warehouse->branch->name, 'capacity' => number_format($warehouse->capacity).' pallets', 'utilization' => $warehouse->utilization, 'status' => $warehouse->status]),
            'summary' => ['total' => $warehouses->count(), 'capacity' => number_format($capacity).' pallets', 'used' => number_format($used).' pallets', 'available' => number_format(max(0, $capacity - $used)).' pallets'],
        ]);
    }

    public function createWarehouse()
    {
        return Inertia::render('Warehouses/Create', ['branches' => Branch::orderBy('name')->get(['id', 'name'])]);
    }

    public function storeWarehouse(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'code' => ['required', 'string', 'max:20', 'unique:warehouses,code'], 'branch_id' => ['required', 'exists:branches,id'], 'capacity' => ['required', 'integer', 'min:1']]);
        Warehouse::create([...$data, 'utilization' => 0, 'status' => 'Active']);
        return to_route('warehouses.index')->with('success', 'Warehouse added to the stock network.');
    }

    public function contacts(Request $request)
    {
        $kind = $request->string('kind')->toString();
        $search = trim($request->string('search')->toString());
        $query = Contact::query()->orderBy('name');
        if (in_array($kind, ['Customer', 'Vendor'], true)) $query->where('kind', $kind);
        if ($search !== '') $query->where('name', 'like', "%{$search}%");
        return Inertia::render('Contacts/Index', ['contacts' => $query->get()->map(fn (Contact $contact) => ['name' => $contact->name, 'kind' => $contact->kind, 'email' => $contact->email, 'phone' => $contact->phone, 'balance' => '৳ '.number_format($contact->balance)]), 'filters' => ['kind' => $kind, 'search' => $search], 'counts' => ['customers' => Contact::where('kind', 'Customer')->count(), 'vendors' => Contact::where('kind', 'Vendor')->count()]]);
    }

    public function reports()
    {
        $revenue = (float) Sale::sum('total');
        $purchases = (float) Purchase::sum('total');
        $returns = (float) SaleReturn::sum('refund_amount');
        $inventory = (float) Product::get()->sum(fn (Product $product) => (float) $product->price * $product->stock);
        $receivables = (float) Sale::withSum('payments', 'amount')->get()->sum(fn (Sale $sale) => max(0, (float) $sale->total - (float) ($sale->payments_sum_amount ?? 0)));
        $overdue = Sale::where('status', 'Overdue')->count();
        $activeBranches = Branch::where('status', 'Active')->count();

        return Inertia::render('Reports/Index', ['cards' => [
            ['title' => 'Profit & loss', 'description' => 'Revenue less purchases and completed customer refunds.', 'value' => $this->compactMoney($revenue - $purchases - $returns), 'change' => number_format($revenue).' revenue tracked', 'tone' => 'lime'],
            ['title' => 'Stock valuation', 'description' => 'Current sell-price value across every allocated product unit.', 'value' => $this->compactMoney($inventory), 'change' => number_format(Product::count()).' SKUs', 'tone' => 'blue'],
            ['title' => 'Receivables ageing', 'description' => 'Open invoice balances that still need collection attention.', 'value' => $this->compactMoney($receivables), 'change' => number_format($overdue).' overdue', 'tone' => 'amber'],
            ['title' => 'Branch comparison', 'description' => 'Active locations contributing to the operating network.', 'value' => number_format($activeBranches).' active', 'change' => number_format(Branch::count()).' total locations', 'tone' => 'violet'],
        ]]);
    }

    private function compactMoney(float $amount): string
    {
        $amount = round($amount);
        if (abs($amount) >= 1000000) return '৳ '.number_format($amount / 1000000, 2).'M';
        if (abs($amount) >= 1000) return '৳ '.number_format($amount / 1000, 0).'K';
        return '৳ '.number_format($amount);
    }

    public function settings()
    {
        return Inertia::render('Settings/Index', ['company' => ['name' => 'Redbook Demo Holdings', 'currency' => 'Bangladeshi Taka (BDT)', 'timezone' => 'Asia/Dhaka', 'branches' => Branch::count()], 'preferences' => ['Low-stock alerts' => true, 'Negative stock' => false, 'Compact tables' => false, 'Auto-post invoices' => true]]);
    }
}
