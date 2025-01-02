### Basic Banking System using ***Laravel 11***
    Individual Account:
      •	Designed for personal use.
      •	Offers a certain amount of free withdrawals each month.
      •	Charges a 1.5% fee on withdrawals exceeding the free limit.
      •	Special benefit: Free withdrawals on Fridays.
    Business Account:
      •	Tailored for business transactions.
      •	Allows higher withdrawal limits.
      •	Charges a 1.5% fee on withdrawals after reaching a total of 50,000.
      •	Provides a more flexible structure for managing larger sums of money.

### Requirements:
    "php": "^8.2"

### For Installation:      ***Delete the composer.lock file first (recommended)***
    composer Install
    cp .env.example .env
    php artisan migrate
    php artisan key:generate
    php artisan serve
    


TransactionController.php
-------------------------
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
