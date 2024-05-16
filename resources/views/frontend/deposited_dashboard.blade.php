@extends('layouts.app')

@section('title', 'Deposited Dashboard')

@section('content')
    <div class="row mt-5">
        <div class="col-md-6 offset-md-3">
            @if(session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Deposited Dashboard</h2>
                </div>
                <div class="card-body">
                    <h3>Current Balance: ${{ Auth::user()->balance }}</h3>
                    <hr>
                    <h3>Deposited Transactions</h3>
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
                            @foreach($depositedTransactions as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                <td>{{ ucfirst($transaction->transaction_type) }}</td>
                                <td>{{ $transaction->amount }}</td>
                                <td>{{ $transaction->date }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#depositModal">
                        Make Deposit
                    </button>

                    <!-- Deposit Modal -->
                    <div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="depositModalLabel">Make Deposit</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('deposit') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Amount to Deposit:</label>
                                            <input type="number" id="amount" name="amount" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Deposit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
