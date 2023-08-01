<x-guest-layout>
    <div class="login-box">
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <span class="h2"><b>Carob</b>Mailer</span>
            </div>
            <div class="card-body">
                <p class="login-box-msg">{{ __('Forgot your password? Just let us know your email address and we will email you a password reset link.') }}</p>
                <form action="{{ route('password.email') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </div>
                </form>
                <p class="mt-1 mb-0">
                    <a href="{{ route('login') }}" class="text-center">I already have a membership</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
