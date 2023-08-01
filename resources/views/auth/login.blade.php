<x-guest-layout>
    <div class="login-box">
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <x-auth-validation-errors class="mb-4" :errors="$errors" />
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <span  class="h2"><b>Carob</b>Mailer</span>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <form action="{{ route('login') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="email" autocomplete="username" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Password" autocomplete="current-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>
                @if (Route::has('password.request'))
                    <p class="mt-1 mb-1">
                        <a href="{{ route('password.request') }}">I forgot my password</a>
                    </p>
                @endif
                @if (Route::has('register'))
                <p class="mt-1 mb-0">
                    <a href="{{ route('register') }}" class="text-center">Register a new membership</a>
                </p>
                @endif
            </div>
        </div>
    </div>
</x-guest-layout>
