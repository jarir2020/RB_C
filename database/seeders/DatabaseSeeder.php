<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Cheque;
use App\Models\CostCenter;
use App\Models\OnboardingLead;
use App\Models\ProductionOrder;
use App\Models\ProductVariant;
use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Contact;
use App\Models\Employee;
use App\Models\LedgerAccount;
use App\Models\LedgerEntry;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create(['name' => 'Admin Workspace', 'email' => 'admin@redbook.test', 'role' => 'Administrator']);

        $branches = collect([
            ['name' => 'Dhanmondi flagship', 'code' => 'DHK-01', 'city' => 'Dhaka', 'sales_target' => 1900000],
            ['name' => 'Uttara outlet', 'code' => 'DHK-02', 'city' => 'Dhaka', 'sales_target' => 1500000],
            ['name' => 'Chattogram hub', 'code' => 'CTG-01', 'city' => 'Chattogram', 'sales_target' => 1200000],
            ['name' => 'Sylhet city', 'code' => 'SYL-01', 'city' => 'Sylhet', 'sales_target' => 900000],
            ['name' => 'Rajshahi central', 'code' => 'RAJ-01', 'city' => 'Rajshahi', 'sales_target' => 760000],
            ['name' => 'Khulna riverside', 'code' => 'KHL-01', 'city' => 'Khulna', 'sales_target' => 720000],
            ['name' => 'Mymensingh north', 'code' => 'MYM-01', 'city' => 'Mymensingh', 'sales_target' => 620000],
            ['name' => 'Cumilla road', 'code' => 'CML-01', 'city' => 'Cumilla', 'sales_target' => 580000],
        ])->mapWithKeys(fn (array $branch) => [$branch['code'] => Branch::create($branch)]);

        foreach ([
            ['name' => 'Nusrat Jahan', 'role' => 'Branch manager', 'email' => 'nusrat@redbook.test', 'phone' => '01700000101', 'branch' => 'DHK-01', 'joined_on' => '2024-03-12'],
            ['name' => 'Mahin Rahman', 'role' => 'Inventory lead', 'email' => 'mahin@redbook.test', 'phone' => '01700000102', 'branch' => 'DHK-02', 'joined_on' => '2024-08-22'],
            ['name' => 'Sadia Ahmed', 'role' => 'Sales associate', 'email' => 'sadia@redbook.test', 'phone' => '01700000103', 'branch' => 'CTG-01', 'joined_on' => '2025-01-09'],
            ['name' => 'Rafiul Karim', 'role' => 'Warehouse coordinator', 'email' => 'rafiul@redbook.test', 'phone' => '01800000104', 'branch' => 'DHK-01', 'joined_on' => '2025-05-18'],
            ['name' => 'Tania Sultana', 'role' => 'Finance officer', 'email' => 'tania@redbook.test', 'phone' => '01700000105', 'branch' => 'DHK-02', 'joined_on' => '2025-07-02'],
            ['name' => 'Imran Chowdhury', 'role' => 'Sales associate', 'email' => 'imran@redbook.test', 'phone' => '01800000106', 'branch' => 'CTG-01', 'joined_on' => '2026-02-15'],
        ] as $employee) {
            Employee::create(['name' => $employee['name'], 'role' => $employee['role'], 'email' => $employee['email'], 'phone' => $employee['phone'], 'joined_on' => $employee['joined_on'], 'branch_id' => $branches[$employee['branch']]->id]);
        }

        foreach ([
            ['name' => 'Dhanmondi central store', 'code' => 'WH-DHK-01', 'branch' => 'DHK-01', 'capacity' => 1200, 'utilization' => 78],
            ['name' => 'Uttara fast-move hub', 'code' => 'WH-DHK-02', 'branch' => 'DHK-02', 'capacity' => 900, 'utilization' => 64],
            ['name' => 'Chattogram receiving', 'code' => 'WH-CTG-01', 'branch' => 'CTG-01', 'capacity' => 760, 'utilization' => 52],
            ['name' => 'Sylhet overflow store', 'code' => 'WH-SYL-01', 'branch' => 'SYL-01', 'capacity' => 440, 'utilization' => 31],
        ] as $warehouse) {
            Warehouse::create(['name' => $warehouse['name'], 'code' => $warehouse['code'], 'capacity' => $warehouse['capacity'], 'utilization' => $warehouse['utilization'], 'branch_id' => $branches[$warehouse['branch']]->id]);
        }

        $contacts = collect([
            ['name' => 'California Fried Chicken', 'kind' => 'Customer', 'email' => 'accounts@california.example', 'phone' => '01700000001', 'balance' => 0],
            ['name' => 'Akhter Enterprise', 'kind' => 'Customer', 'email' => 'finance@akhter.example', 'phone' => '01700000002', 'balance' => 32840],
            ['name' => 'U Turn Fashion Express', 'kind' => 'Customer', 'email' => 'hello@uturn.example', 'phone' => '01700000003', 'balance' => 0],
            ['name' => 'Popular Medical Centre', 'kind' => 'Customer', 'email' => 'accounts@popular.example', 'phone' => '01700000004', 'balance' => 18750],
            ['name' => 'Nahar Traders', 'kind' => 'Vendor', 'email' => 'sales@nahar.example', 'phone' => '01800000001', 'balance' => 126400],
            ['name' => 'Bismillah Electronics', 'kind' => 'Vendor', 'email' => 'trade@bismillah.example', 'phone' => '01800000002', 'balance' => 284900],
            ['name' => 'Apex Furniture', 'kind' => 'Vendor', 'email' => 'orders@apex.example', 'phone' => '01800000003', 'balance' => 98200],
            ['name' => 'Fresh Mart Distribution', 'kind' => 'Vendor', 'email' => 'orders@freshmart.example', 'phone' => '01800000004', 'balance' => 0],
        ])->mapWithKeys(fn (array $contact) => [$contact['name'] => Contact::create($contact)]);

        $products = [
            ['name' => 'Premium Basmati Rice 25kg', 'sku' => 'GRC-0251', 'category' => 'Grocery', 'price' => 3450, 'stock' => 42, 'reorder_level' => 18],
            ['name' => 'Aarong Cotton Panjabi', 'sku' => 'FSH-1180', 'category' => 'Fashion', 'price' => 2850, 'stock' => 86, 'reorder_level' => 20],
            ['name' => 'Walton Smart Refrigerator', 'sku' => 'ELC-4412', 'category' => 'Electronics', 'price' => 68900, 'stock' => 7, 'reorder_level' => 10],
            ['name' => 'Fresh Soybean Oil 5L', 'sku' => 'GRC-3022', 'category' => 'Grocery', 'price' => 890, 'stock' => 128, 'reorder_level' => 30],
            ['name' => 'Orchid Office Chair', 'sku' => 'FUR-0718', 'category' => 'Furniture', 'price' => 12400, 'stock' => 18, 'reorder_level' => 8],
            ['name' => 'Samsung Galaxy A55', 'sku' => 'ELC-5601', 'category' => 'Electronics', 'price' => 48999, 'stock' => 4, 'reorder_level' => 8],
        ];
        foreach ($products as $product) Product::create($product);

        $stockAllocations = [
            'GRC-0251' => ['DHK-01' => 18, 'DHK-02' => 10, 'CTG-01' => 8, 'SYL-01' => 6],
            'FSH-1180' => ['DHK-01' => 30, 'DHK-02' => 22, 'CTG-01' => 18, 'SYL-01' => 16],
            'ELC-4412' => ['DHK-01' => 2, 'DHK-02' => 2, 'CTG-01' => 2, 'SYL-01' => 1],
            'GRC-3022' => ['DHK-01' => 50, 'DHK-02' => 35, 'CTG-01' => 25, 'SYL-01' => 18],
            'FUR-0718' => ['DHK-01' => 7, 'DHK-02' => 4, 'CTG-01' => 4, 'SYL-01' => 3],
            'ELC-5601' => ['DHK-01' => 1, 'DHK-02' => 1, 'CTG-01' => 1, 'SYL-01' => 1],
        ];
        foreach (Product::all() as $productRecord) {
            foreach ($branches as $branchCode => $branch) {
                BranchStock::create(['branch_id' => $branch->id, 'product_id' => $productRecord->id, 'quantity' => $stockAllocations[$productRecord->sku][$branchCode] ?? 0]);
            }
        }

        Product::where('sku', 'GRC-0251')->update(['brand' => 'Premium Harvest', 'unit' => 'bag', 'barcode' => '890025100251', 'cost_price' => 2950, 'wholesale_price' => 3200, 'price_label' => 'Retail']);
        Product::where('sku', 'FSH-1180')->update(['brand' => 'Aarong', 'unit' => 'piece', 'barcode' => '890118011180', 'cost_price' => 2100, 'wholesale_price' => 2550, 'price_label' => 'MRP']);
        Product::where('sku', 'GRC-3022')->update(['brand' => 'Fresh', 'unit' => 'bottle', 'barcode' => '89030223022', 'cost_price' => 760, 'wholesale_price' => 835, 'price_label' => 'Retail']);
        ProductVariant::create(['product_id' => Product::where('sku', 'FSH-1180')->value('id'), 'name' => 'Panjabi · Navy · L', 'sku' => 'FSH-1180-NV-L', 'barcode' => '890118011181', 'attributes' => ['colour' => 'Navy', 'size' => 'L'], 'price' => 2950, 'stock' => 12, 'reorder_level' => 4, 'status' => 'Active']);
        ProductVariant::create(['product_id' => Product::where('sku', 'FSH-1180')->value('id'), 'name' => 'Panjabi · White · XL', 'sku' => 'FSH-1180-WH-XL', 'barcode' => '890118011182', 'attributes' => ['colour' => 'White', 'size' => 'XL'], 'price' => 2950, 'stock' => 8, 'reorder_level' => 4, 'status' => 'Active']);

        $date = '2026-09-28';
        Sale::create(['invoice_no' => 'INV-2034', 'contact_id' => $contacts['California Fried Chicken']->id, 'branch_id' => $branches['DHK-01']->id, 'sold_at' => $date, 'total' => 48500, 'status' => 'Paid', 'channel' => 'Wholesale']);
        Sale::create(['invoice_no' => 'INV-2033', 'contact_id' => $contacts['Akhter Enterprise']->id, 'branch_id' => $branches['DHK-02']->id, 'sold_at' => $date, 'total' => 32840, 'status' => 'Pending', 'channel' => 'Retail']);
        Sale::create(['invoice_no' => 'INV-2032', 'contact_id' => $contacts['U Turn Fashion Express']->id, 'branch_id' => $branches['CTG-01']->id, 'sold_at' => '2026-09-27', 'total' => 126200, 'status' => 'Paid', 'channel' => 'Wholesale']);
        Sale::create(['invoice_no' => 'INV-2031', 'contact_id' => $contacts['Popular Medical Centre']->id, 'branch_id' => $branches['DHK-01']->id, 'sold_at' => '2026-09-27', 'total' => 18750, 'status' => 'Overdue', 'channel' => 'Institutional']);

        $productsBySku = Product::all()->keyBy('sku');
        foreach ([
            ['invoice' => 'INV-2034', 'sku' => 'GRC-0251', 'quantity' => 10],
            ['invoice' => 'INV-2034', 'sku' => 'GRC-3022', 'quantity' => 20],
            ['invoice' => 'INV-2033', 'sku' => 'FSH-1180', 'quantity' => 8],
            ['invoice' => 'INV-2032', 'sku' => 'ELC-4412', 'quantity' => 1],
            ['invoice' => 'INV-2031', 'sku' => 'ELC-5601', 'quantity' => 1],
        ] as $item) {
            $sale = Sale::where('invoice_no', $item['invoice'])->firstOrFail();
            $product = $productsBySku[$item['sku']];
            SaleItem::create(['sale_id' => $sale->id, 'product_id' => $product->id, 'quantity' => $item['quantity'], 'unit_price' => $product->price, 'line_total' => $product->price * $item['quantity']]);
        }

        Purchase::create(['order_no' => 'PO-884', 'contact_id' => $contacts['Nahar Traders']->id, 'branch_id' => $branches['DHK-01']->id, 'ordered_at' => $date, 'total' => 126400, 'status' => 'Received', 'items_count' => 18]);
        Purchase::create(['order_no' => 'PO-883', 'contact_id' => $contacts['Bismillah Electronics']->id, 'branch_id' => $branches['DHK-02']->id, 'ordered_at' => '2026-09-27', 'total' => 284900, 'status' => 'In transit', 'items_count' => 7]);
        Purchase::create(['order_no' => 'PO-882', 'contact_id' => $contacts['Apex Furniture']->id, 'branch_id' => $branches['CTG-01']->id, 'ordered_at' => '2026-09-26', 'total' => 98200, 'status' => 'Draft', 'items_count' => 12]);
        Purchase::create(['order_no' => 'PO-881', 'contact_id' => $contacts['Fresh Mart Distribution']->id, 'branch_id' => $branches['DHK-01']->id, 'ordered_at' => '2026-09-25', 'total' => 64600, 'status' => 'Received', 'items_count' => 34]);

        $returnSale = Sale::where('invoice_no', 'INV-2034')->firstOrFail();
        $returnProduct = $productsBySku['GRC-0251'];
        SaleReturn::create(['return_no' => 'RET-3001', 'sale_id' => $returnSale->id, 'contact_id' => $returnSale->contact_id, 'branch_id' => $returnSale->branch_id, 'product_id' => $returnProduct->id, 'returned_on' => $date, 'quantity' => 1, 'refund_amount' => $returnProduct->price, 'reason' => 'Customer changed quantity', 'status' => 'Completed']);

        StockTransfer::create(['transfer_no' => 'TRF-4001', 'product_id' => $productsBySku['GRC-3022']->id, 'from_branch_id' => $branches['DHK-01']->id, 'to_branch_id' => $branches['CTG-01']->id, 'transferred_on' => $date, 'quantity' => 24, 'status' => 'In transit', 'note' => 'Fast-move replenishment']);
        StockTransfer::create(['transfer_no' => 'TRF-4002', 'product_id' => $productsBySku['FSH-1180']->id, 'from_branch_id' => $branches['DHK-02']->id, 'to_branch_id' => $branches['SYL-01']->id, 'transferred_on' => '2026-09-27', 'quantity' => 12, 'status' => 'Received', 'note' => 'Seasonal allocation']);
        BranchStock::where('branch_id', $branches['DHK-01']->id)->where('product_id', $productsBySku['GRC-3022']->id)->decrement('quantity', 24);
        BranchStock::where('branch_id', $branches['DHK-02']->id)->where('product_id', $productsBySku['FSH-1180']->id)->decrement('quantity', 12);
        BranchStock::where('branch_id', $branches['SYL-01']->id)->where('product_id', $productsBySku['FSH-1180']->id)->increment('quantity', 12);

        $salesByInvoice = Sale::all()->keyBy('invoice_no');
        foreach ([
            ['number' => 'PAY-1001', 'invoice' => 'INV-2034', 'amount' => 48500, 'method' => 'Bank transfer', 'reference' => 'TRX-2034'],
            ['number' => 'PAY-1002', 'invoice' => 'INV-2032', 'amount' => 126200, 'method' => 'Cash', 'reference' => 'CASH-2032'],
            ['number' => 'PAY-1003', 'invoice' => 'INV-2033', 'amount' => 12000, 'method' => 'Mobile banking', 'reference' => 'MBL-2033'],
        ] as $payment) {
            $sale = $salesByInvoice[$payment['invoice']];
            Payment::create(['payment_no' => $payment['number'], 'sale_id' => $sale->id, 'contact_id' => $sale->contact_id, 'branch_id' => $sale->branch_id, 'paid_on' => $date, 'amount' => $payment['amount'], 'method' => $payment['method'], 'reference' => $payment['reference']]);
        }

        foreach ([
            ['code' => '1001', 'name' => 'Cash & cash equivalents', 'type' => 'Asset', 'balance' => 842400, 'trend' => '+12.4%'],
            ['code' => '1104', 'name' => 'Trade receivables', 'type' => 'Asset', 'balance' => 682000, 'trend' => '-4.2%'],
            ['code' => '2001', 'name' => 'Trade payables', 'type' => 'Liability', 'balance' => 428700, 'trend' => '+2.8%'],
            ['code' => '4001', 'name' => 'Sales revenue', 'type' => 'Income', 'balance' => 4820000, 'trend' => '+18.6%'],
        ] as $account) LedgerAccount::create($account);

        $ledgerAccounts = LedgerAccount::all()->keyBy('code');
        foreach ([
            ['account' => '1001', 'branch' => 'DHK-01', 'reference' => 'OPEN-2026', 'description' => 'Opening cash position', 'debit' => 842400, 'credit' => 0],
            ['account' => '1104', 'branch' => 'DHK-02', 'reference' => 'OPEN-2026', 'description' => 'Opening trade receivables', 'debit' => 682000, 'credit' => 0],
            ['account' => '2001', 'branch' => 'DHK-01', 'reference' => 'OPEN-2026', 'description' => 'Opening trade payables', 'debit' => 0, 'credit' => 428700],
            ['account' => '4001', 'branch' => 'DHK-01', 'reference' => 'INV-2034', 'description' => 'Revenue posted for wholesale invoice', 'debit' => 0, 'credit' => 48500],
            ['account' => '1001', 'branch' => 'DHK-01', 'reference' => 'PAY-1001', 'description' => 'Bank transfer received from customer', 'debit' => 48500, 'credit' => 0],
            ['account' => '4001', 'branch' => 'CTG-01', 'reference' => 'INV-2032', 'description' => 'Revenue posted for wholesale invoice', 'debit' => 0, 'credit' => 126200],
            ['account' => '1001', 'branch' => 'CTG-01', 'reference' => 'PAY-1002', 'description' => 'Cash received from customer', 'debit' => 126200, 'credit' => 0],
            ['account' => '2001', 'branch' => 'DHK-01', 'reference' => 'PO-884', 'description' => 'Received purchase posted to payables', 'debit' => 0, 'credit' => 126400],
        ] as $entry) {
            LedgerEntry::create([
                'ledger_account_id' => $ledgerAccounts[$entry['account']]->id,
                'branch_id' => $branches[$entry['branch']]->id,
                'entry_date' => $date,
                'reference' => $entry['reference'],
                'description' => $entry['description'],
                'debit' => $entry['debit'],
                'credit' => $entry['credit'],
                'status' => 'Posted',
            ]);
        }

        $bankAccounts = collect([
            ['name' => 'Redbook operating account', 'bank' => 'BRAC Bank', 'account_no' => 'BRAC-001-8842', 'type' => 'Current', 'opening_balance' => 620000, 'balance' => 620000, 'status' => 'Active'],
            ['name' => 'Branch mobile wallet', 'bank' => 'bKash Business', 'account_no' => 'BKASH-RED-01', 'type' => 'Mobile wallet', 'opening_balance' => 185000, 'balance' => 185000, 'status' => 'Active'],
        ])->mapWithKeys(fn (array $account) => [$account['account_no'] => BankAccount::create($account)]);
        Cheque::create(['bank_account_id' => $bankAccounts['BRAC-001-8842']->id, 'contact_id' => $contacts['Akhter Enterprise']->id, 'cheque_no' => 'CHQ-2401', 'cheque_date' => $date, 'amount' => 32840, 'direction' => 'In', 'status' => 'Pending', 'notes' => 'Against INV-2033']);
        Cheque::create(['bank_account_id' => $bankAccounts['BRAC-001-8842']->id, 'contact_id' => $contacts['Nahar Traders']->id, 'cheque_no' => 'CHQ-2402', 'cheque_date' => '2026-09-27', 'amount' => 25000, 'direction' => 'Out', 'status' => 'Cleared', 'notes' => 'Supplier settlement']);
        $bankAccounts['BRAC-001-8842']->decrement('balance', 25000);
        CostCenter::create(['name' => 'Dhanmondi retail floor', 'code' => 'CC-DHK-01', 'budget' => 320000, 'spent' => 182500, 'status' => 'Active']);
        CostCenter::create(['name' => 'Branch marketing', 'code' => 'CC-MKT-01', 'budget' => 180000, 'spent' => 94400, 'status' => 'Active']);
        $bom = Bom::create(['product_id' => Product::where('sku', 'FSH-1180')->value('id'), 'name' => 'Panjabi finishing recipe', 'output_quantity' => 1, 'status' => 'Active', 'notes' => 'Demo assembly recipe']);
        BomItem::create(['bom_id' => $bom->id, 'product_id' => Product::where('sku', 'GRC-3022')->value('id'), 'quantity' => 1]);
        ProductionOrder::create(['bom_id' => $bom->id, 'branch_id' => $branches['DHK-01']->id, 'order_no' => 'PRO-0501', 'planned_on' => $date, 'output_quantity' => 5, 'status' => 'Planned', 'notes' => 'Demo finishing run']);
        OnboardingLead::create(['name' => 'Demo founder', 'email' => 'founder@example.test', 'phone' => '01700000999', 'business_name' => 'Demo Retail Co.', 'business_type' => 'Retail', 'branch_count' => 2, 'status' => 'New']);
    }
}
