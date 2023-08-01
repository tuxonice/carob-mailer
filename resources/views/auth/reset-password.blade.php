<x-guest-layout>
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <span class="h2"><b>Carob</b>Mailer</span>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Set new password</p>
                <form action="{{ route('password.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="email" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="password"
                               required autocomplete="new-password" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Retype password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-guest-layout>
