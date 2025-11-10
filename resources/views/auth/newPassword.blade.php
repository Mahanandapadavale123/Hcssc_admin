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

                            <form method="POST" action="{{ route('setNewPassword') }}">
                                @csrf
                                <div class="vh-100 d-flex flex-column justify-content-between p-4 pb-0">

                                    <div class="mx-auto mb-5 text-center">
                                        <img src="{{ asset('hcssc.png') }}" class="img-fluid" alt="Logo">
                                    </div>

                                    <div class="">
                                        <div class="text-center mb-3">
                                            <h2 class="mb-2">Reset Password</h2>
                                            <p class="mb-0">Your new password must be different from previous used
                                                passwords.
                                            </p>
                                        </div>

                                        @if (session('error'))
                                            <div class="alert alert-danger">{{ session('error') }}</div>
                                        @endif

                                        @if (session('success'))
                                            <div class="alert alert-success">{{ session('success') }}</div>
                                        @endif

                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                @foreach ($errors->all() as $error)
                                                    <div>{{ $error }}</div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div>
                                            <div class="input-block mb-3">

                                                <input type="hidden" name="email" id="email"
                                                    value="@if (!empty($email)) {{ $email }} @endif">

                                                <div class="mb-3">
                                                    <label class="form-label">New Password</label>
                                                    <div class="pass-group" id="passwordInput">
                                                        <input type="password" id="password" name="password"
                                                            class="form-control pass-input @error('password') is-invalid @enderror"
                                                            value="{{ old('password') }}" required
                                                            autocomplete="new-password">
                                                        <span class="ti toggle-password ti-eye-off"></span>
                                                    </div>
                                                    @error('password')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong></span>
                                                    @enderror

                                                </div>
                                                <div class="password-strength d-flex" id="passwordStrength">
                                                    <span id="poor"></span>
                                                    <span id="weak"></span>
                                                    <span id="strong"></span>
                                                    <span id="heavy"></span>
                                                </div>
                                                <div id="passwordInfo" class="mb-2"></div>
                                                <p class="fs-12">Use 8 or more characters with a mix of letters, numbers &
                                                    symbols.</p>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Confirm Password</label>
                                                <div class="pass-group">
                                                    <input type="password" name="password_confirmation" required
                                                        autocomplete="new-password"
                                                        class="pass-inputs form-control @error('password') is-invalid @enderror">
                                                    <span class="ti toggle-passwords ti-eye-off"></span>
                                                </div>
                                                @error('password_confirmation')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <button type="submit" class="btn btn-primary w-100">Submit</button>
                                            </div>
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
