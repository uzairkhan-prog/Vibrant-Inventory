<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseName;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 20);

        $fromDate  = $request->input('from_date');
        $toDate    = $request->input('to_date');
        $monthYear = $request->input('month_year');

        $query = Expense::with('paymentType', 'expenseName')
            ->orderBy('created_at', 'desc');

        /*
        |--------------------------------------------------------------------------
        | FILTER PRIORITY SYSTEM
        |--------------------------------------------------------------------------
        | Priority:
        | 1) From-To Date
        | 2) Month
        | 3) Default Current Month
        */

        // -------------------------
        // 1️⃣ DATE RANGE FILTER (HIGHEST PRIORITY)
        // -------------------------
        if (!empty($fromDate) || !empty($toDate)) {
            if (!empty($fromDate) && !empty($toDate)) {
                $query->whereBetween('created_at', [
                    Carbon::parse($fromDate)->startOfDay(),
                    Carbon::parse($toDate)->endOfDay()
                ]);
            } elseif (!empty($fromDate)) {
                $query->where('created_at', '>=', Carbon::parse($fromDate)->startOfDay());
            } elseif (!empty($toDate)) {
                $query->where('created_at', '<=', Carbon::parse($toDate)->endOfDay());
            }
        }
        // -------------------------
        // 2️⃣ MONTH FILTER
        // -------------------------
        elseif ($request->has('month_year')) {
            // If user selected "All Records" → no filter
            if ($monthYear && $monthYear !== 'all') {
                $startDate = Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth();
                $endDate   = Carbon::createFromFormat('Y-m', $monthYear)->endOfMonth();

                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }
        // -------------------------
        // 3️⃣ DEFAULT CURRENT MONTH
        // -------------------------
        else {
            $startDate = now()->startOfMonth();
            $endDate   = now()->endOfMonth();

            $query->whereBetween('created_at', [$startDate, $endDate]);
            $monthYear = now()->format('Y-m'); // auto select current month in dropdown
        }

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */
        $expenses = $query->paginate($perPage)->appends($request->all());

        /*
        |--------------------------------------------------------------------------
        | GRAND TOTAL (NOT PAGINATION)
        |--------------------------------------------------------------------------
        */
        // $totalQuery = Expense::query();

        // // Apply same filter to total

        // if (!empty($fromDate) || !empty($toDate)) {
        //     if (!empty($fromDate) && !empty($toDate)) {
        //         $totalQuery->whereBetween('created_at', [
        //             Carbon::parse($fromDate)->startOfDay(),
        //             Carbon::parse($toDate)->endOfDay()
        //         ]);
        //     } elseif (!empty($fromDate)) {
        //         $totalQuery->where('created_at', '>=', Carbon::parse($fromDate)->startOfDay());
        //     } elseif (!empty($toDate)) {
        //         $totalQuery->where('created_at', '<=', Carbon::parse($toDate)->endOfDay());
        //     }
        // } elseif ($request->has('month_year') && $monthYear !== 'all' && !empty($monthYear)) {
        //     $startDate = Carbon::createFromFormat('Y-m', $monthYear)->startOfMonth();
        //     $endDate   = Carbon::createFromFormat('Y-m', $monthYear)->endOfMonth();

        //     $totalQuery->whereBetween('created_at', [$startDate, $endDate]);
        // }
        // // else "all records" → no where condition

        // $grandTotal = $totalQuery->sum('amount');

        $grandTotal = (clone $query)->sum('amount');

        /*
        |--------------------------------------------------------------------------
        | MONTH DROPDOWN DATA
        |--------------------------------------------------------------------------
        */
        $months = Expense::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        return view('expenses.index', compact(
            'expenses',
            'grandTotal',
            'months',
            'monthYear',
            'fromDate',
            'toDate'
        ));
    }

    public function create()
    {
        $expenseNames = ExpenseName::all();
        $paymentTypes = PaymentType::all();
        return view('expenses.create', compact('paymentTypes', 'expenseNames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_name_id'  => 'required|exists:expense_names,id',
            'payment_type_id'  => 'required|exists:payment_types,id',
            'expense_date'     => 'required|date',
            'amount'           => 'required|numeric|min:0',
            'description'      => 'nullable|string',
        ]);

        Expense::create([
            'expense_name_id'  => $request->expense_name_id,
            'payment_type_id'  => $request->payment_type_id,
            'amount'           => $request->amount,
            'description'      => $request->description,
            'created_at'       => Carbon::parse($request->expense_date)->startOfDay(),
            'updated_at'       => now(),
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    public function edit(Expense $expense)
    {
        $expenseNames = ExpenseName::all();
        $paymentTypes = PaymentType::all();
        return view('expenses.edit', compact('expense', 'paymentTypes', 'expenseNames'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'expense_name_id' => 'required|exists:expense_names,id',
            'payment_type_id' => 'required|exists:payment_types,id',
            'expense_date'    => 'required|date',
            'amount'          => 'required|numeric|min:0',
            'description'     => 'nullable|string',
        ]);

        $expense->update([
            'expense_name_id' => $request->expense_name_id,
            'payment_type_id' => $request->payment_type_id,
            'amount'          => $request->amount,
            'description'     => $request->description,
            'created_at'      => Carbon::parse($request->expense_date)->startOfDay(),
            'updated_at'      => now(),
        ]);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
