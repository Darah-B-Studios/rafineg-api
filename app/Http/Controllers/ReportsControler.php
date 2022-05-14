<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportsControler extends Controller
{
    public function transaction_report(Request $request)
    {
        $data = $request->validate([
            "startDate" => "date",
            "endDate" => "date"
        ]);

        $transactions = Transaction::where('createdAt', '>=', $data['startDate'])
            ->where('createdAt', '<=', $data['endDate'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            "success" => true,
            "data" => $transactions,
            "message" => "transaction reports from {$data['startDate']} to {$data['endDate']}"
        ]);
    }
}