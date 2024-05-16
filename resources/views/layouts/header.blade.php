<header class="bg-dark text-light p-3">
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="m-0">My Banking System</h1>
        <nav>
            @auth
                <ul class="nav">
                    {{-- <li class="nav-item">
                        <a class="nav-link text-light" href="{{ route('dashboard') }}">Dashboard</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link text-light" href="{{ route('dashboard') }}">All Transactions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="{{ route('deposited_transactions') }}">Deposited Transactions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="{{ route('withdrawal_transactions') }}">Withdrawal Transactions</a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-link text-light">Logout</button>
                        </form>
                    </li>
                </ul>
            @else
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link text-light" href="{{ route('loginForm') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="{{ route('register') }}">Register</a>
                    </li>
                </ul>
            @endauth
        </nav>
    </div>
</header>
