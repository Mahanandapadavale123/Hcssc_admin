@extends('layouts.auth')
@section('content')

    <div class="container-fuild">
        <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
            <div class="row">
                <div class="col-lg-6">
                    <div
                        class="d-lg-flex align-items-center justify-content-center d-none flex-wrap vh-100 bg-primary-transparent">
                        <div>
                            <img src="{{ asset('admin/img/bg/authentication-bg-04.svg') }}" alt="Img">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
                        <div class="col-md-7 mx-auto vh-100">

                            <form method="POST" action="{{ route('newPassword') }}" class="vh-100">

                                @csrf
                                <div class="vh-100 d-flex flex-column justify-content-between p-4 pb-0">
                                    <div class=" mx-auto mb-5 text-center">
                                        <img src="{{ asset('hcssc.png') }}" class="img-fluid" alt="Logo">
                                    </div>
                                    <div class="">
                                        <div class="text-center mb-3">
                                            <h2 class="mb-2">{{ __('Enter OTP') }}</h2>
                                            <p class="mb-0">
                                                {{ __('Please enter the OTP you received in your email below to reset your password.') }}
                                            </p>
                                        </div>

                                        <div class="mb-3">

                                            <input type="hidden" name="email" id="email"
                                                value="@if (!empty($email)) {{ $email }} @endif">

                                            <label class="form-label">{{ __('OTP ( One Time Password )') }}</label>
                                            <div class="input-group">
                                                <input type="number" name="otp"
                                                    class="form-control @error('otp') is-invalid @enderror border-end-0"
                                                    value="{{ old('otp') }}" required maxlength="6" minlength="6">

                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror

                                            </div>
                                        </div>

                                        @if (session('error'))
                                            <div class="alert alert-danger">{{ session('error') }}</div>
                                        @endif

                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary w-100">Submit</button>
                                        </div>

                                        <div class="text-center">
                                            <h6 class="fw-normal text-dark mb-0">Return to
                                                <a href="{{ url('/login') }}" class="hover-a">Sign In</a>
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="mt-5 pb-4 text-center">
                                        <p class="mb-0 text-gray-9">Copyright &copy; {{ date('Y') }} -
                                            {{ env('APP_NAME') }}</p>
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
