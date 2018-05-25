<?php

namespace LifeOnScreen\LaravelQuickBooks;

use QuickBooksOnline\API\Facades as QuickBooksFacades;

/**
 * Class QuickBooksResources
 * @package LifeOnScreen\LaravelQuickBooks
 */
class QuickBooksResources
{
    const ACCOUNT = [
        'name'   => 'Account',
        'facade' => QuickBooksFacades\Account::class
    ];

    const BILL = [
        'name'   => 'Bill',
        'facade' => QuickBooksFacades\Bill::class
    ];

    const BILL_PAYMENT = [
        'name'   => 'BillPayment',
        'facade' => QuickBooksFacades\BillPayment::class
    ];

    const COMPANY_CURRENCY = [
        'name'   => 'CompanyCurrency',
        'facade' => QuickBooksFacades\CompanyCurrency::class
    ];

    const CREDIT_MEMO = [
        'name'   => 'CreditMemo',
        'facade' => QuickBooksFacades\CreditMemo::class
    ];

    const CUSTOMER = [
        'name'   => 'Customer',
        'facade' => QuickBooksFacades\Customer::class
    ];

    const DEPARTMENT = [
        'name'   => 'Department',
        'facade' => QuickBooksFacades\Department::class
    ];

    const EMPLOYEE = [
        'name'   => 'Employee',
        'facade' => QuickBooksFacades\Employee::class
    ];

    const ESTIMATE = [
        'name'   => 'Estimate',
        'facade' => QuickBooksFacades\Estimate::class
    ];

    const INVOICE = [
        'name'   => 'Invoice',
        'facade' => QuickBooksFacades\Invoice::class
    ];

    const ITEM = [
        'name'   => 'Item',
        'facade' => QuickBooksFacades\Item::class
    ];

    const JOURNAL_ENTRY = [
        'name'   => 'JournalEntry',
        'facade' => QuickBooksFacades\JournalEntry::class
    ];

    const LINE = [
        'name'   => 'Line',
        'facade' => QuickBooksFacades\Line::class
    ];

    const PAYMENT = [
        'name'   => 'Payment',
        'facade' => QuickBooksFacades\Payment::class
    ];

    const PURCHASE = [
        'name'   => 'Purchase',
        'facade' => QuickBooksFacades\Purchase::class
    ];

    const PURCHASE_ORDER = [
        'name'   => 'PurchaseOrder',
        'facade' => QuickBooksFacades\PurchaseOrder::class
    ];

    const REFUND_RECEIPT = [
        'name'   => 'RefundReceipt',
        'facade' => QuickBooksFacades\RefundReceipt::class
    ];

    const SALES_RECEIPT = [
        'name'   => 'SalesReceipt',
        'facade' => QuickBooksFacades\SalesReceipt::class
    ];

    const TAX_RATE = [
        'name'   => 'TaxRateDetails',
        'facade' => QuickBooksFacades\TaxRate::class
    ];

    const TAX_SERVICE = [
        'name'   => 'TaxService',
        'facade' => QuickBooksFacades\TaxService::class
    ];

    const TIME_ACTIVITY = [
        'name'   => 'TimeActivity',
        'facade' => QuickBooksFacades\TimeActivity::class
    ];

    const TRANSFER = [
        'name'   => 'Transfer',
        'facade' => QuickBooksFacades\Transfer::class
    ];

    const VENDOR = [
        'name'   => 'Vendor',
        'facade' => QuickBooksFacades\Vendor::class
    ];

    const VENDOR_CREDIT = [
        'name'   => 'VendorCredit',
        'facade' => QuickBooksFacades\VendorCredit::class
    ];
}