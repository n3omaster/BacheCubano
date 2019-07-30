<!-- Content section Start -->
<section class="login section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-12 col-xs-12">
                <div class="login-form login-area">

                    <h3>Entrar en su cuenta de Bachecubano</h3>

                    <form role="form" class="login-form" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <div class="input-icon">
                                <i class="lni-user"></i>
                                <input type="text" id="sender-email" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="correo@correo.com" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-icon">
                                <i class="lni-lock"></i>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="***********" name="password" required autocomplete="current-password">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="checkbox">
                                <input type="checkbox" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label>Mantenerme Logueado</label>
                            </div>
                            @if (Route::has('password.request'))
                            <a class="forgetpassword" href="{{ route('password.request') }}">Olvidaste tu contraseña?</a>
                            @endif
                        </div>
                        <div class="text-center">
                            <button class="btn btn-common log-btn" type="submit">Acceder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Content section End -->