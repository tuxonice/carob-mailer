<x-guest-layout>
    <div class="login-box">
        <x-auth-session-verification-link-sent class="mb-4" :status="session('status')" />
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <span  class="h2"><b>Carob</b>Mailer</span>
            </div>
            <div class="card-body">
                <p class="login-box-msg">{{ __('Before getting started, please verify your email address by clicking on the link we just emailed to you.') }}</p>
                <form method="post" action="{{ route('verification.send') }}">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Resend Verification Email') }}</button>
                        </div>
                    </div>
                </form>
                <br/>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Log Out') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
