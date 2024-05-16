<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function showDepositedTransactions()
    {
        $depositedTransactions = Auth::user()->transactions()->where('transaction_type', 'deposit')->orderBy('date', 'desc')->get();
        return view('frontend.deposited_dashboard', ['depositedTransactions' => $depositedTransactions]);
    }

    public function withdrawalTransactions()
    {
        $withdrawalTransactions = Auth::user()->transactions()->where('transaction_type', 'withdrawal')->orderBy('date', 'desc')->get();
        return view('frontend.withdrawal_dashboard', ['withdrawalTransactions' => $withdrawalTransactions]);
    }

    public function depositMoney(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);
        $userId = Auth::id();
        $user = Auth::user();
        $user->balance += $request->amount;
        $user->save();

        Transactions::create([
            'user_id' => $userId,
            'transaction_type' => 'deposit',
            'amount' => $request->amount,
            'fee' => 0,
            'date' => now(),
        ]);

        return redirect()->route('deposited_transactions')->with('success', 'Deposit successful.');
    }

    public function withdrawMoney(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $withdrawAmount = $request->amount;
        $user = Auth::user();
        $accountType = $user->account_type;

        $isFriday = Carbon::now()->dayOfWeek === Carbon::FRIDAY;

        // current month total withdraw
        $totalWithdrawals = Transactions::where('user_id', $user->id)->where('transaction_type', 'withdrawal')->sum('amount');
        // monthly total withdraw
        $monthlyTotalWithdraw = Transactions::where('user_id', $user->id)->where('transaction_type', 'withdrawal')->whereMonth('date', now()->month)->sum('amount');

        if ($accountType === 'individual'){
            if($isFriday){
                $freeAmount = $withdrawAmount;
            }
            elseif($totalWithdrawals < 1000){
                $freeAmount =  1000 - $totalWithdrawals;
            }
            elseif($monthlyTotalWithdraw < 5000){
                $freeAmount = 5000 - $monthlyTotalWithdraw;
            }
            else{
                $freeAmount = 0;
            }

            if($freeAmount > 0){
                $withCharge = $withdrawAmount - $freeAmount;
                $withCharge += $withCharge * 0.015;
                $withCharge += $freeAmount;
            }
            else{
                $withdrawAmount += $withdrawAmount * 0.015;
                $withCharge = $withdrawAmount;
            }
        }
        elseif ($accountType === 'business'){
            if($totalWithdrawals >= 50000){
                $withdrawAmount += $withdrawAmount * 0.015;
            }
            else{
                $extra = 50000 - $totalWithdrawals;
                $extraAmount = $extra + ($extra * 0.025);
                $withdrawAmount = $extraAmount + $extra;
            }
        }
        if($withdrawAmount > $user->balance){
            return redirect()->back()->with('error', 'Insufficient balance.');
        }

        $user->balance -= $withdrawAmount;
        $user->save();

        Transactions::create([
            'user_id' => $user->id,
            'transaction_type' => 'withdrawal',
            'amount' => $withdrawAmount,
            'fee' => $withdrawAmount,
            'date' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Withdrawal successful.');
        
    }
}
