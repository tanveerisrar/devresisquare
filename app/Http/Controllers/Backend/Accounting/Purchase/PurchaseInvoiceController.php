<?php

namespace App\Http\Controllers\Backend\Accounting\Purchase;

use App\Http\Controllers\Backend\Accounting\BaseCrudController;
use App\Models\SysPurchaseInvoice;
use App\Models\User;
use Illuminate\Validation\Rule;

class PurchaseInvoiceController extends BaseCrudController
{
    protected string $modelClass = SysPurchaseInvoice::class;
    protected string $viewPath = 'backend.accounting.purchase.invoices';
    protected string $routeName = 'backend.accounting.purchase.invoices';
    protected string $title = 'Purchase Invoices';
    protected array $defaults = ['status' => 'draft', 'invoice_date' => null];
    protected array $columns = [
        ['key' => 'id', 'label' => '#'],
        ['key' => 'invoice_no', 'label' => 'Invoice No'],
        ['key' => 'user_id', 'label' => 'Vendor'],
        ['key' => 'invoice_date', 'label' => 'Invoice Date', 'type' => 'date'],
        ['key' => 'due_date', 'label' => 'Due Date', 'type' => 'date'],
        ['key' => 'total_amount', 'label' => 'Total', 'type' => 'money'],
        ['key' => 'balance_amount', 'label' => 'Balance', 'type' => 'money'],
        ['key' => 'status', 'label' => 'Status'],
    ];

    protected function fields(): array
    {
        return [
            ['name' => 'user_id', 'label' => 'Vendor', 'type' => 'select', 'required' => true],
            ['name' => 'invoice_no', 'label' => 'Invoice No', 'type' => 'text', 'required' => true],
            ['name' => 'invoice_date', 'label' => 'Invoice Date', 'type' => 'date', 'required' => true],
            ['name' => 'due_date', 'label' => 'Due Date', 'type' => 'date'],
            ['name' => 'total_amount', 'label' => 'Total Amount', 'type' => 'number', 'step' => '0.01', 'min' => '0', 'required' => true],
            ['name' => 'balance_amount', 'label' => 'Balance Amount', 'type' => 'number', 'step' => '0.01', 'min' => '0', 'required' => true],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['draft' => 'Draft', 'received' => 'Received', 'paid' => 'Paid', 'partial' => 'Partial', 'cancelled' => 'Cancelled']],
            ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea'],
        ];
    }

    protected function options(): array
    {
        return [
            'user_id' => User::query()->orderBy('name')->pluck('name', 'id')->toArray(),
        ];
    }

    protected function rules(?int $id = null): array
    {
        $uniqueInvoiceNo = Rule::unique('sys_purchase_invoices', 'invoice_no');
        if ($id) {
            $uniqueInvoiceNo = $uniqueInvoiceNo->ignore($id);
        }

        return [
            'user_id' => ['required', 'exists:users,id'],
            'invoice_no' => ['required', 'string', 'max:50', $uniqueInvoiceNo],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'balance_amount' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['draft', 'received', 'paid', 'partial', 'cancelled'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
