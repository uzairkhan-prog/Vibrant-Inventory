<?php

namespace App\Http\Controllers;

use App\Models\Agent;

class AgentLedgerController extends Controller
{
    public function show(Agent $agent)
    {
        $balance = 0;

        $ledger = $agent->sales()
            ->with('customer')
            ->orderBy('date')
            ->get()
            ->map(function ($sale) use (&$balance) {

                $customer = $sale->customer;

                $balance += $sale->total_amount;

                return [
                    'date' => $sale->date,
                    'invoice_no' => 'Sale - Invoice no: ' . $sale->id,
                    'customer' => $customer
                        ? ($customer->company_name ?? '-')
                        . ($customer->name ? " ({$customer->name})" : '')
                        : '-',
                    'amount' => $sale->total_amount,
                    'balance' => $balance,
                ];
            });

        $totalSales = $ledger->sum('amount');

        return view('agents.ledger', compact('agent', 'ledger', 'totalSales'));
    }
}
