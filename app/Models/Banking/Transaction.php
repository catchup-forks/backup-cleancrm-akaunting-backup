<?php
namespace App\Models\Banking;

use App\Models\Expense\Bill;
use App\Models\Expense\Payment;
use App\Models\Income\Invoice;
use App\Models\Income\Revenue;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public static function getUserTransactions($staff_id, $type)
    {
        $transactions = array();
        switch ($type) {
            case 'payments':
                $bills = Bill::where('vendor_id', $staff_id)->get();
                foreach ($bills as $bill) {
                    $bill_payments = $bill->payments;
                    if ($bill_payments) {
                        foreach ($bill_payments as $bill_payment) {
                            $transactions[] = (object)[
                              'date' => $bill_payment->paid_at,
                              'bankaccount' => $bill_payment->bankaccount->name,
                              'type' => trans('invoices.status.partial'),
                              'category' => trans_choice('general.invoices', 1),
                              'description' => $bill_payment->description,
                              'amount' => $bill_payment->amount,
                              'currency_code' => $bill_payment->currency_code,
                            ];
                        }
                    }
                }
                $payments = Payment::where('vendor_id', $staff_id)->get();
                foreach ($payments as $payment) {
                    $transactions[] = (object)[
                      'date' => $payment->paid_at,
                      'bankaccount' => $payment->bankaccount->name,
                      'type' => 'Expense',
                      'category' => $payment->category->name,
                      'description' => $payment->description,
                      'amount' => $payment->amount,
                      'currency_code' => $payment->currency_code,
                    ];
                }
                break;
            case 'revenues':
                $invoices = Invoice::where('customer_id', $staff_id)->get();
                foreach ($invoices as $invoice) {
                    $invoice_payments = $invoice->payments;
                    if ($invoice_payments) {
                        foreach ($invoice_payments as $invoice_payment) {
                            $transactions[] = (object)[
                              'date' => $invoice_payment->paid_at,
                              'bankaccount' => $invoice_payment->bankaccount->name,
                              'type' => trans('invoices.status.partial'),
                              'category' => trans_choice('general.invoices', 1),
                              'description' => $invoice_payment->description,
                              'amount' => $invoice_payment->amount,
                              'currency_code' => $invoice_payment->currency_code,
                            ];
                        }
                    }
                }
                $revenues = Revenue::where('customer_id', $staff_id)->get();
                foreach ($revenues as $revenue) {
                    $transactions[] = (object)[
                      'date' => $revenue->paid_at,
                      'bankaccount' => $revenue->bankaccount->name,
                      'type' => trans_choice('general.payments', 1),
                      'category' => $revenue->category->name,
                      'description' => $revenue->description,
                      'amount' => $revenue->amount,
                      'currency_code' => $revenue->currency_code,
                    ];
                }
                break;
        }
        return $transactions;
    }
}
