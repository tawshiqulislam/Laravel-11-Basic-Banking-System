@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="row mt-5">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Dashboard</h2>
                </div>
                <div class="card-body">
                    <h3>Current Balance: ${{ Auth::user()->balance }}</h3>
                    <hr>
                    <h3>All Transactions</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                <td>{{ ucfirst($transaction->transaction_type) }}</td>
                                <td>{{ $transaction->amount }}</td>
                                <td>{{ $transaction->date }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
