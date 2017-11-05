<?php
namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Expense\Bill;
use App\Models\Expense\BillPayment;
use App\Models\Expense\Payment;
use App\Models\Setting\Category;
use Date;

class ExpenseSummary extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $dates = $totals = $expenses = $expenses_graph = $categories = [];
        $status = request('status');
        //if ($filter != 'upcoming') {
        $categories = Category::enabled()->type('expense')->pluck('name', 'id')->toArray();
        //}
        // Add Bill in Categories
        $categories[0] = trans_choice('general.bills', 2);
        // Get year
        $year = request('year');
        if (empty($year)) {
            $year = Date::now()->year;
        }
        // Dates
        for ($j = 1; $j <= 12; $j++) {
            $dates[$j] = Date::parse($year . '-' . $j)->format('F');
            $expenses_graph[Date::parse($year . '-' . $j)->format('F-Y')] = 0;
            // Totals
            $totals[$dates[$j]] = array(
              'amount' => 0,
              'currency_code' => setting('general.default_currency'),
              'currency_rate' => 1
            );
            // Bill
            $expenses[0][$dates[$j]] = array(
              'category_id' => 0,
              'name' => trans_choice('general.bills', 1),
              'amount' => 0,
              'currency_code' => setting('general.default_currency'),
              'currency_rate' => 1
            );
            foreach ($categories as $category_id => $category_name) {
                $expenses[$category_id][$dates[$j]] = array(
                  'category_id' => $category_id,
                  'name' => $category_name,
                  'amount' => 0,
                  'currency_code' => setting('general.default_currency'),
                  'currency_rate' => 1
                );
            }
        }
        // Bills
        switch ($status) {
            case 'all':
                $bills = Bill::getMonthsOfYear('billed_at');
                $this->setAmount($expenses_graph, $totals, $expenses, $bills, 'bill', 'billed_at');
                break;
            case 'upcoming':
                $bills = Bill::getMonthsOfYear('due_at');
                $this->setAmount($expenses_graph, $totals, $expenses, $bills, 'bill', 'due_at');
                break;
            default:
                $bills = BillPayment::getMonthsOfYear('paid_at');
                $this->setAmount($expenses_graph, $totals, $expenses, $bills, 'bill', 'paid_at');
                break;
        }
        // Payments
        if ($status != 'upcoming') {
            $payments = Payment::getMonthsOfYear('paid_at');
            $this->setAmount($expenses_graph, $totals, $expenses, $payments, 'payment', 'paid_at');
        }
        // Expenses Graph
        $expenses_graph = json_encode($expenses_graph);
        return view('reports.expense_summary.index',
          compact('dates', 'categories', 'expenses', 'expenses_graph', 'totals'));
    }

    private function setAmount(&$graph, &$totals, &$expenses, $items, $type, $date_field)
    {
        foreach ($items as $item) {
            $date = Date::parse($item->$date_field)->format('F');
            if ($type == 'bill') {
                $category_id = 0;
            } else {
                $category_id = $item->category_id;
            }
            if (!isset($expenses[$category_id])) {
                continue;
            }
            $amount = $item->getConvertedAmount();
            // Forecasting
            if (($type == 'bill') && ($date_field == 'due_at')) {
                foreach ($item->payments as $payment) {
                    $amount -= $payment->getConvertedAmount();
                }
            }
            $expenses[$category_id][$date]['amount'] += $amount;
            $expenses[$category_id][$date]['currency_code'] = $item->currency_code;
            $expenses[$category_id][$date]['currency_rate'] = $item->currency_rate;
            $graph[Date::parse($item->$date_field)->format('F-Y')] += $amount;
            $totals[$date]['amount'] += $amount;
        }
    }
}
