@extends('layouts.auth')

@section('content')
        <div class="container-fuild">
        <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
            <div class="row">
                <div class="col-lg-6">
                    <div class="d-lg-flex align-items-center justify-content-center d-none flex-wrap vh-100 bg-primary-transparent">
                        <div>
                            <img src="{{ asset('admin/img/bg/authentication-bg-03.svg') }}" alt="Img">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
                        <div class="col-md-7 mx-auto vh-100">

                            <form method="POST" action="{{ route('login') }}" class="vh-100">
                                @csrf

                                <div class="vh-100 d-flex flex-column justify-content-between p-4 pb-0">
                                    <div class=" mx-auto mb-5 text-center">
                                        <img src="{{ asset('hcssc.png') }}" class="img-fluid" alt="Logo">
                                    </div>

                                    <div class="">
                                        <div class="text-center mb-3">
                                            <h2 class="mb-2">Sign In</h2>
                                            <p class="mb-0">Please enter your details to sign in</p>
                                        </div>

                                         @if(session('success'))
                                            <div class="alert alert-success">{{ session('success') }}</div>
                                        @endif

                                        @if(session('error'))
                                            <div class="alert alert-danger">{{ session('error') }}</div>
                                        @endif
                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <div class="input-group">
                                                <input type="email" class="form-control border-end-0" @error('email') is-invalid @enderror" name="email"
                                                value="{{ old('email', 'admin@gmail.com') }}" required autocomplete="email" autofocus>
                                                <span class="input-group-text border-start-0">
                                                    <i class="ti ti-mail"></i>
                                                </span>
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror

                                            </div>

                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="pass-group">
                                                <input id="password" type="password" class="pass-input form-control @error('password') is-invalid @enderror"
                                                name="password" required autocomplete="current-password" value="password">
                                                <span class="ti toggle-password ti-eye-off"></span>
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror

                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-check-md mb-0">
                                                    <input class="form-check-input" id="remember_me" type="checkbox">
                                                    <label for="remember_me" class="form-check-label mt-0">Remember
                                                        Me</label>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                @if (Route::has('password.request'))
                                                    <a href="{{  url('/forgotPassword') }}" class="link-danger">{{ __('Forgot Password?') }}</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                                        </div>
                                        <div class="text-center">
                                            <h6 class="fw-normal text-dark mb-0">Don’t have an account?
                                                <a href="javascript:void(0)" class="hover-a"> Contact Admin</a>
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="mt-5 pb-4 text-center">
                                        <p class="mb-0 text-gray-9">Copyright &copy; {{ date('Y') }} - {{ env('APP_NAME') }}</p>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
