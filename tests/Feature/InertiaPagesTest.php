<?php

namespace Tests\Feature;

use App\Models\LedgerAccount;
use App\Models\ProductionOrder;
use App\Models\ProductVariant;
use App\Models\BankAccount;
use App\Models\LedgerEntry;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InertiaPagesTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::where('email', 'admin@redbook.test')->firstOrFail());
    }
    public function test_dashboard_renders_the_inertia_root(): void
    {
        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard\\/Index', false);
    }

    public function test_sales_form_creates_a_persistent_pending_invoice(): void
    {
        $response = $this->post('/sales', [
            'contact_id' => 1,
            'branch_id' => 1,
            'total' => 22500,
            'channel' => 'Retail',
        ]);

        $response->assertRedirect('/sales');
        $this->assertDatabaseHas('sales', ['total' => 22500, 'status' => 'Pending']);
    }

    public function test_inventory_form_validates_and_redirects_after_a_product_is_added(): void
    {
        $response = $this->post('/inventory', [
            'name' => 'Demo Product',
            'sku' => 'demo-1',
            'category' => 'Demo',
            'price' => 1200,
            'stock' => 12,
        ]);

        $response->assertRedirect('/inventory');
        $response->assertSessionHas('success', 'Product added to the inventory workspace.');
        $this->get('/inventory')->assertSee('Demo Product');
    }

    public function test_purchase_form_creates_a_persistent_draft_order(): void
    {
        $response = $this->post('/purchase', [
            'contact_id' => 5,
            'branch_id' => 1,
            'total' => 48500,
            'items_count' => 3,
        ]);

        $response->assertRedirect('/purchase');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('purchases', ['total' => 48500, 'status' => 'Draft', 'items_count' => 3]);
    }

    public function test_contact_form_creates_a_persistent_relationship(): void
    {
        $response = $this->post('/contacts', [
            'name' => 'Demo Retail Partner',
            'kind' => 'Customer',
            'email' => 'partner@example.test',
            'phone' => '+880 1700 000 000',
        ]);

        $response->assertRedirect('/contacts');
        $response->assertSessionHas('success', 'Contact added to the relationship centre.');
        $this->assertDatabaseHas('contacts', ['name' => 'Demo Retail Partner', 'kind' => 'Customer']);
    }


    public function test_branch_form_creates_a_persistent_location(): void
    {
        $response = $this->post('/branches', [
            'name' => 'Demo south outlet',
            'code' => 'DHK-99',
            'city' => 'Dhaka',
            'sales_target' => 450000,
        ]);

        $response->assertRedirect('/branches');
        $response->assertSessionHas('success', 'Branch added to the network view.');
        $this->assertDatabaseHas('branches', ['name' => 'Demo south outlet', 'code' => 'DHK-99']);
    }


    public function test_sales_status_filter_returns_only_matching_orders(): void
    {
        $response = $this->get('/sales?status=Pending');

        $response->assertOk();
        $response->assertSee('INV-2033');
        $response->assertDontSee('INV-2034');
    }


    public function test_employee_form_creates_a_persistent_team_member(): void
    {
        $response = $this->post('/employees', [
            'name' => 'Demo Team Member',
            'role' => 'Operations associate',
            'email' => 'team.member@redbook.test',
            'phone' => '01700000999',
            'branch_id' => 1,
            'joined_on' => '2026-09-28',
        ]);

        $response->assertRedirect('/employees');
        $response->assertSessionHas('success', 'Employee added to the people directory.');
        $this->assertDatabaseHas('employees', ['name' => 'Demo Team Member', 'role' => 'Operations associate']);
    }

    public function test_employee_search_matches_name_or_role(): void
    {
        $response = $this->get('/employees?search=warehouse');

        $response->assertOk();
        $response->assertSee('Rafiul Karim');
        $response->assertDontSee('Nusrat Jahan');
    }

    public function test_warehouse_form_creates_a_persistent_storage_point(): void
    {
        $response = $this->post('/warehouses', [
            'name' => 'Demo overflow storage',
            'code' => 'WH-DHK-99',
            'branch_id' => 1,
            'capacity' => 500,
        ]);

        $response->assertRedirect('/warehouses');
        $response->assertSessionHas('success', 'Warehouse added to the stock network.');
        $this->assertDatabaseHas('warehouses', ['name' => 'Demo overflow storage', 'code' => 'WH-DHK-99']);
    }


    public function test_invoice_register_filters_by_status(): void
    {
        $response = $this->get('/invoices?status=Paid');

        $response->assertOk();
        $response->assertSee('INV-2034');
        $response->assertDontSee('INV-2033');
    }

    public function test_full_payment_is_persisted_and_closes_an_invoice(): void
    {
        $response = $this->post('/payments', [
            'sale_id' => 2,
            'amount' => 32840,
            'method' => 'Cash',
            'paid_on' => '2026-09-28',
            'reference' => 'CASH-FINAL-2033',
        ]);

        $response->assertRedirect('/payments');
        $this->assertDatabaseHas('payments', ['sale_id' => 2, 'amount' => 32840, 'method' => 'Cash']);
        $this->assertDatabaseHas('sales', ['id' => 2, 'status' => 'Paid']);
    }


    public function test_pos_checkout_creates_items_decrements_stock_and_records_payment(): void
    {
        $stockBefore = Product::findOrFail(1)->stock;

        $response = $this->post('/pos', [
            'contact_id' => 1,
            'branch_id' => 1,
            'payment_method' => 'Cash',
            'items' => [
                ['product_id' => 1, 'quantity' => 2],
                ['product_id' => 2, 'quantity' => 1],
            ],
        ]);

        $response->assertRedirect('/pos');
        $sale = Sale::latest('id')->firstOrFail();
        $this->assertSame('Paid', $sale->status);
        $this->assertSame(9750.0, (float) $sale->total);
        $this->assertDatabaseHas('sale_items', ['sale_id' => $sale->id, 'product_id' => 1, 'quantity' => 2]);
        $this->assertDatabaseHas('payments', ['sale_id' => $sale->id, 'amount' => 9750, 'method' => 'Cash']);
        $this->assertSame($stockBefore - 2, Product::findOrFail(1)->stock);
    }

    public function test_pos_checkout_rejects_more_stock_than_available(): void
    {
        $response = $this->from('/pos')->post('/pos', [
            'contact_id' => 1,
            'branch_id' => 1,
            'payment_method' => 'On account',
            'items' => [['product_id' => 1, 'quantity' => 9999]],
        ]);

        $response->assertRedirect('/pos');
        $response->assertSessionHasErrors('items.0.quantity');
    }


    public function test_return_restock_is_persisted_and_increases_product_stock(): void
    {
        $stockBefore = Product::findOrFail(1)->stock;

        $response = $this->post('/returns', [
            'sale_id' => 1,
            'product_id' => 1,
            'quantity' => 2,
            'returned_on' => '2026-09-28',
            'reason' => 'Damaged item',
        ]);

        $response->assertRedirect('/returns');
        $this->assertDatabaseHas('sales_returns', ['sale_id' => 1, 'product_id' => 1, 'quantity' => 2, 'refund_amount' => 6900]);
        $this->assertSame($stockBefore + 2, Product::findOrFail(1)->stock);
    }

    public function test_stock_transfer_is_persisted_with_branch_route(): void
    {
        $response = $this->post('/transfers', [
            'product_id' => 1,
            'from_branch_id' => 1,
            'to_branch_id' => 2,
            'quantity' => 4,
            'transferred_on' => '2026-09-28',
            'note' => 'Demo replenishment',
        ]);

        $response->assertRedirect('/transfers');
        $this->assertDatabaseHas('stock_transfers', ['product_id' => 1, 'from_branch_id' => 1, 'to_branch_id' => 2, 'quantity' => 4, 'status' => 'In transit']);
    }


    public function test_pos_uses_and_decrements_selected_branch_stock(): void
    {
        $before = \App\Models\BranchStock::where('branch_id', 1)->where('product_id', 1)->firstOrFail()->quantity;

        $this->post('/pos', [
            'contact_id' => 1,
            'branch_id' => 1,
            'payment_method' => 'On account',
            'items' => [['product_id' => 1, 'quantity' => 2]],
        ])->assertRedirect('/pos');

        $this->assertSame($before - 2, \App\Models\BranchStock::where('branch_id', 1)->where('product_id', 1)->firstOrFail()->quantity);
    }

    public function test_transfer_receiving_moves_stock_to_destination_branch(): void
    {
        $sourceBefore = \App\Models\BranchStock::where('branch_id', 1)->where('product_id', 1)->firstOrFail()->quantity;
        $destinationBefore = \App\Models\BranchStock::where('branch_id', 2)->where('product_id', 1)->firstOrFail()->quantity;

        $this->post('/transfers', ['product_id' => 1, 'from_branch_id' => 1, 'to_branch_id' => 2, 'quantity' => 3, 'transferred_on' => '2026-09-28', 'note' => 'Branch replenishment'])->assertRedirect('/transfers');
        $transfer = \App\Models\StockTransfer::latest('id')->firstOrFail();
        $this->assertSame($sourceBefore - 3, \App\Models\BranchStock::where('branch_id', 1)->where('product_id', 1)->firstOrFail()->quantity);
        $this->post('/transfers/'.$transfer->id.'/receive')->assertRedirect('/transfers');
        $this->assertSame($destinationBefore + 3, \App\Models\BranchStock::where('branch_id', 2)->where('product_id', 1)->firstOrFail()->quantity);
        $this->assertDatabaseHas('stock_transfers', ['id' => $transfer->id, 'status' => 'Received']);
    }


    public function test_ledger_voucher_is_persisted_and_updates_account_balance(): void
    {
        $before = (float) LedgerAccount::findOrFail(1)->balance;

        $response = $this->post('/account/ledger', [
            'ledger_account_id' => 1,
            'branch_id' => 1,
            'entry_date' => '2026-09-28',
            'reference' => 'EXP-2026-001',
            'description' => 'Demo branch expense',
            'debit' => 1250,
            'credit' => 0,
        ]);

        $response->assertRedirect('/account/ledger');
        $response->assertSessionHas('success', 'Voucher EXP-2026-001 was posted to the ledger.');
        $this->assertDatabaseHas('ledger_entries', ['reference' => 'EXP-2026-001', 'debit' => 1250, 'credit' => 0]);
        $this->assertSame($before + 1250, (float) LedgerAccount::findOrFail(1)->balance);
    }

    public function test_ledger_rejects_a_voucher_with_both_sides_empty_or_filled(): void
    {
        $response = $this->from('/account/ledger/create')->post('/account/ledger', [
            'ledger_account_id' => 1,
            'branch_id' => 1,
            'entry_date' => '2026-09-28',
            'reference' => 'BAD-001',
            'description' => 'Invalid voucher',
            'debit' => 100,
            'credit' => 50,
        ]);

        $response->assertRedirect('/account/ledger/create');
        $response->assertSessionHasErrors(['debit', 'credit']);
        $this->assertDatabaseMissing('ledger_entries', ['reference' => 'BAD-001']);
    }

    public function test_ledger_search_returns_matching_journal_entries(): void
    {
        $response = $this->get('/account/ledger?search=PAY-1001');

        $response->assertOk();
        $response->assertSee('PAY-1001');
        $response->assertSee('Bank transfer received from customer');
        $response->assertDontSee('Revenue posted for wholesale invoice');
    }


    public function test_product_metadata_and_variant_are_persisted(): void
    {
        $this->post('/inventory', [
            'name' => 'Demo labelled product', 'sku' => 'DEM-9001', 'barcode' => '89090010001', 'category' => 'Demo', 'brand' => 'Demo Brand', 'unit' => 'box', 'price' => 1200, 'cost_price' => 800, 'wholesale_price' => 1050, 'price_label' => 'MRP', 'stock' => 12, 'reorder_level' => 4,
        ])->assertRedirect('/inventory');
        $product = Product::where('sku', 'DEM-9001')->firstOrFail();
        $this->post('/inventory/variants', ['product_id' => $product->id, 'name' => 'Demo small', 'sku' => 'DEM-9001-S', 'barcode' => '89090010002', 'attributes' => 'size:S, colour:Blue', 'price' => 1250, 'stock' => 3, 'reorder_level' => 1])->assertRedirect('/inventory/variants');
        $this->assertDatabaseHas('product_variants', ['sku' => 'DEM-9001-S', 'product_id' => $product->id]);
        $this->assertSame(1, ProductVariant::where('product_id', $product->id)->count());
    }

    public function test_banking_cheque_and_cost_centre_records_are_persisted(): void
    {
        $this->post('/banking', ['name' => 'Demo savings', 'bank' => 'Demo Bank', 'account_no' => 'DEMO-001', 'type' => 'Savings', 'opening_balance' => 50000])->assertRedirect('/banking');
        $account = BankAccount::where('account_no', 'DEMO-001')->firstOrFail();
        $this->post('/cheques', ['bank_account_id' => $account->id, 'contact_id' => 1, 'cheque_no' => 'DEMO-CHQ-1', 'cheque_date' => '2026-09-28', 'amount' => 2500, 'direction' => 'In', 'status' => 'Cleared', 'notes' => 'Demo'])->assertRedirect('/cheques');
        $this->post('/cost-centres', ['name' => 'Demo costs', 'code' => 'CC-DEMO', 'budget' => 10000])->assertRedirect('/cost-centres');
        $this->assertDatabaseHas('cheques', ['cheque_no' => 'DEMO-CHQ-1', 'status' => 'Cleared']);
        $this->assertDatabaseHas('cost_centers', ['code' => 'CC-DEMO']);
        $this->assertSame(52500.0, (float) BankAccount::findOrFail($account->id)->balance);
    }

    public function test_production_order_consumes_components_and_adds_finished_stock(): void
    {
        $componentBefore = (int) Product::where('sku', 'GRC-3022')->value('stock');
        $finishedBefore = (int) Product::where('sku', 'FSH-1180')->value('stock');
        $this->post('/production', ['product_id' => 2, 'component_product_id' => 4, 'component_quantity' => 1, 'branch_id' => 1, 'planned_on' => '2026-09-28', 'output_quantity' => 3, 'notes' => 'Test run'])->assertRedirect('/production');
        $order = ProductionOrder::latest('id')->firstOrFail();
        $this->post('/production/'.$order->id.'/complete')->assertRedirect('/production');
        $this->assertDatabaseHas('production_orders', ['id' => $order->id, 'status' => 'Completed']);
        $this->assertSame($componentBefore - 3, (int) Product::where('sku', 'GRC-3022')->value('stock'));
        $this->assertSame($finishedBefore + 3, (int) Product::where('sku', 'FSH-1180')->value('stock'));
    }

    public function test_public_registration_creates_an_onboarding_lead(): void
    {
        $this->post('/register', ['name' => 'New founder', 'email' => 'new.founder@example.test', 'phone' => '01700000000', 'business_name' => 'New business', 'business_type' => 'Wholesale', 'branch_count' => 3])->assertRedirect('/register/complete');
        $this->assertDatabaseHas('onboarding_leads', ['email' => 'new.founder@example.test', 'business_name' => 'New business', 'branch_count' => 3]);
    }

}
