<?php

namespace App\Http\Controllers\Backend\Accounting\Sale;

use App\Http\Controllers\Backend\Accounting\BaseCrudController;
use App\Models\SysSaleInvoice;
use App\Models\SysSaleInvoiceItem;
use App\Models\SysTax;
use App\Models\User;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SaleInvoiceController extends BaseCrudController
{
    protected string $modelClass = SysSaleInvoice::class;
    protected string $viewPath = 'backend.accounting.sale.invoices';
    protected string $routeName = 'backend.accounting.sale.invoices';
    protected string $title = 'Sale Invoices';
    protected array $defaults = ['status' => 'draft', 'invoice_date' => null];
    protected array $with = ['items'];
    protected array $columns = [
        ['key' => 'id', 'label' => '#'],
        ['key' => 'invoice_no', 'label' => 'Invoice No'],
        ['key' => 'user_id', 'label' => 'Customer'],
        ['key' => 'invoice_date', 'label' => 'Invoice Date', 'type' => 'date'],
        ['key' => 'due_date', 'label' => 'Due Date', 'type' => 'date'],
        ['key' => 'total_amount', 'label' => 'Total', 'type' => 'money'],
        ['key' => 'balance_amount', 'label' => 'Balance', 'type' => 'money'],
        ['key' => 'status', 'label' => 'Status'],
    ];

    public function create()
    {
        $defaults = array_merge($this->defaults, [
            'invoice_no' => $this->nextInvoiceNo(),
        ]);

        return view($this->viewPath . '.create', [
            'title' => $this->title,
            'routeName' => $this->routeName,
            'fields' => $this->fields(),
            'selectOptions' => $this->options(),
            'defaults' => $defaults,
        ]);
    }

    protected function fields(): array
    {
        return [
            ['name' => 'user_id', 'label' => 'Customer', 'type' => 'select', 'required' => true],
            ['name' => 'invoice_no', 'label' => 'Invoice No', 'type' => 'text', 'required' => true],
            ['name' => 'invoice_date', 'label' => 'Invoice Date', 'type' => 'date', 'required' => true],
            ['name' => 'due_date', 'label' => 'Due Date', 'type' => 'date'],
            ['name' => 'total_amount', 'label' => 'Total Amount', 'type' => 'number', 'step' => '0.01', 'min' => '0', 'required' => true],
            ['name' => 'balance_amount', 'label' => 'Balance Amount', 'type' => 'number', 'step' => '0.01', 'min' => '0', 'required' => true],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'issued' => 'Issued', 'paid' => 'Paid', 'partial' => 'Partial', 'cancelled' => 'Cancelled']],
            ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea'],
        ];
    }

    protected function rules(?int $id = null): array
    {
        $uniqueInvoiceNo = Rule::unique('sys_sale_invoices', 'invoice_no');
        if ($id) {
            $uniqueInvoiceNo = $uniqueInvoiceNo->ignore($id);
        }

        return [
            'user_id' => ['required', 'exists:users,id'],
            'invoice_no' => ['required', 'string', 'max:50', $uniqueInvoiceNo],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'balance_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['draft', 'issued', 'paid', 'partial', 'cancelled'])],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:0'],
            'items.*.rate' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_id' => ['nullable', 'exists:sys_taxes,id'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.line_total' => ['nullable', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
        ];
    }

    protected function options(): array
    {
        $taxes = SysTax::orderBy('name')->get(['id', 'name', 'rate']);
        return [
            'user_id' => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
            'tax_id' => $taxes->pluck('name', 'id')->toArray(),
            'tax_rates' => $taxes->pluck('rate', 'id')->toArray(),
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data = $this->preparePayload($request, $data);
        if (empty($data['invoice_no'])) {
            $data['invoice_no'] = $this->nextInvoiceNo();
        }
        $this->persistItems($data, null);

        return redirect()->route($this->routeName . '.index')
            ->with('success', $this->title . ' created successfully.');
    }

    public function update(Request $request, int $id)
    {
        $invoice = SysSaleInvoice::findOrFail($id);
        $data = $request->validate($this->rules($id));
        $data = $this->preparePayload($request, $data);
        if (empty($data['invoice_no'])) {
            $data['invoice_no'] = $invoice->invoice_no ?? $this->nextInvoiceNo();
        }
        $this->persistItems($data, $invoice);

        return redirect()->route($this->routeName . '.index')
            ->with('success', $this->title . ' updated successfully.');
    }

    private function persistItems(array &$data, ?SysSaleInvoice $invoice): void
    {
        $items = $data['items'];
        unset($data['items']);

        DB::transaction(function () use (&$data, $items, $invoice) {
            $subtotal = 0;
            $taxTotal = 0;
            $linePayloads = [];

            foreach ($items as $row) {
                $qty = (float)($row['quantity'] ?? 0);
                $rate = (float)($row['rate'] ?? 0);
                $discount = (float)($row['discount'] ?? 0);
                $taxRate = (float)($row['tax_rate'] ?? 0);

                $lineBase = max(0, ($qty * $rate) - $discount);
                $taxAmount = $taxRate > 0 ? ($lineBase * $taxRate / 100) : 0;
                $lineTotal = $lineBase + $taxAmount;

                $subtotal += $lineBase;
                $taxTotal += $taxAmount;

                $linePayloads[] = [
                    'item_name' => $row['item_name'],
                    'description' => $row['description'] ?? null,
                    'quantity' => $qty,
                    'rate' => $rate,
                    'discount' => $discount,
                    'tax_id' => $row['tax_id'] ?? null,
                    'tax_rate' => $taxRate ?: null,
                    'tax_amount' => $taxAmount,
                    'line_total' => $lineTotal,
                    'notes' => $row['notes'] ?? null,
                ];
            }

            $total = $subtotal + $taxTotal;
            $data['total_amount'] = $total;
            $data['balance_amount'] = $data['balance_amount'] ?? $total;

            if ($invoice) {
                $invoice->update($data);
                $invoice->items()->delete();
                $invoiceId = $invoice->id;
            } else {
                $invoice = SysSaleInvoice::create($data);
                $invoiceId = $invoice->id;
            }

            foreach ($linePayloads as $payload) {
                $payload['sale_invoice_id'] = $invoiceId;
                SysSaleInvoiceItem::create($payload);
            }
        });
    }

    private function nextInvoiceNo(): string
    {
        $prefix = BusinessSetting::where('type', 'sale_invoice_prefix')->value('value') ?? 'INV';
        $next = (SysSaleInvoice::max('id') ?? 0) + 1;
        return $prefix . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    public function show(int $id)
    {
        $invoice = SysSaleInvoice::with([
            'items',
            'payments.bankAccount',
            'payments.paymentMethod',
            'receipts'
        ])->findOrFail($id);
        $customer = User::find($invoice->user_id);

        $subtotal = 0;
        $taxTotal = 0;
        foreach ($invoice->items as $row) {
            $qty = (float)($row->quantity ?? 0);
            $rate = (float)($row->rate ?? 0);
            $discount = (float)($row->discount ?? 0);
            $lineBase = max(0, ($qty * $rate) - $discount);
            $tax = (float)($row->tax_amount ?? 0);
            $subtotal += $lineBase;
            $taxTotal += $tax;
        }
        $total = $subtotal + $taxTotal;
        $paid = $invoice->payments->sum('amount');
        $balance = $invoice->balance_amount ?? max(0, $total - $paid);

        return view($this->viewPath . '.show', [
            'title' => 'Invoice ' . $invoice->invoice_no,
            'invoice' => $invoice,
            'customer' => $customer,
            'subtotal' => $subtotal,
            'taxTotal' => $taxTotal,
            'total' => $total,
            'paid' => $paid,
            'balance' => $balance,
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Initiate Stripe Checkout (test/sandbox) for the invoice balance.
     */
    public function pay(Request $request, int $id)
    {
        $invoice = SysSaleInvoice::findOrFail($id);

        if (($invoice->balance_amount ?? 0) <= 0) {
            return back()->with('error', 'Invoice is already fully paid.');
        }

        $secret = config('services.stripe.test_secret') ?: env('STRIPE_TEST_SECRET');
        $publishable = config('services.stripe.test_key') ?: env('STRIPE_PUBLISHABLE_TEST');

        if (!$secret || !$publishable) {
            return back()->with('error', 'Stripe test keys are missing. Set STRIPE_TEST_SECRET and STRIPE_PUBLISHABLE_TEST in .env.');
        }

        try {
            $sessionUrl = $this->createStripeCheckoutUrl(
                $secret,
                $invoice,
                $request->input('currency', 'usd'),
                $publishable
            );
        } catch (\Throwable $e) {
            return back()->with('error', 'Could not create Stripe session: ' . $e->getMessage());
        }

        return redirect()->away($sessionUrl);
    }

    /**
     * Minimal Stripe Checkout session creation using cURL (no SDK).
     */
    private function createStripeCheckoutUrl(string $secret, SysSaleInvoice $invoice, string $currency, string $publishable): string
    {
        $amountCents = (int) round(max(0.5, $invoice->balance_amount) * 100); // Stripe requires >= 50 cents equivalent

        $data = [
            'mode' => 'payment',
            'payment_method_types[]' => 'card',
            'success_url' => URL::signedRoute('backend.accounting.sale.invoices.paid', $invoice->id),
            'cancel_url' => route($this->routeName . '.edit', $invoice->id),
            'customer_email' => optional($invoice->user)->email,
            'client_reference_id' => 'sale-invoice-' . $invoice->id,
            'line_items[0][price_data][currency]' => $currency,
            'line_items[0][price_data][product_data][name]' => 'Invoice ' . $invoice->invoice_no,
            'line_items[0][price_data][product_data][description]' => 'Payment for invoice #' . $invoice->invoice_no,
            'line_items[0][price_data][unit_amount]' => $amountCents,
            'line_items[0][quantity]' => 1,
        ];

        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        curl_setopt($ch, CURLOPT_USERPWD, $secret . ':');

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \RuntimeException('Stripe API error: ' . curl_error($ch));
        }
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = json_decode($response, true);
        if ($status >= 400 || empty($json['url'])) {
            $message = $json['error']['message'] ?? 'Unknown error';
            throw new \RuntimeException('Stripe responded with ' . $status . ': ' . $message);
        }

        return $json['url'];
    }

    /**
     * Mark invoice paid after Stripe success (test mode).
     */
    public function markPaid(Request $request, int $id)
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        $invoice = SysSaleInvoice::with('payments')->findOrFail($id);

        DB::transaction(function () use ($invoice) {
            $amount = $invoice->balance_amount ?? 0;
            if ($amount > 0) {
                $bankId = \App\Models\SysBankAccount::query()->value('id');
                $methodId = \App\Models\PaymentMethod::query()->value('id');

                if ($bankId && $methodId) {
                    \App\Models\SysPayment::create([
                        'user_id' => $invoice->user_id,
                        'sys_bank_account_id' => $bankId,
                        'payment_method_id' => $methodId,
                        'payment_type' => 'income',
                        'reference_type' => 'sale_invoice',
                        'reference_id' => $invoice->id,
                        'payment_date' => now()->toDateString(),
                        'amount' => $amount,
                        'notes' => 'Stripe test payment auto-recorded',
                    ]);
                }
            }

            $invoice->update([
                'status' => 'paid',
                'balance_amount' => 0,
            ]);
        });

        return redirect()->route($this->routeName . '.index')
            ->with('success', "Invoice {$invoice->invoice_no} marked as paid (Stripe test).");
    }
}
