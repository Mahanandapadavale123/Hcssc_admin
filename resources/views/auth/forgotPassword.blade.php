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

                            <form method="POST" action="{{ route('forgotPassword') }}" class="vh-100">
                                @csrf
                                <div class="vh-100 d-flex flex-column justify-content-between p-4 pb-0">
                                    <div class=" mx-auto mb-5 text-center">
                                        <img src="{{ asset('hcssc.png') }}" class="img-fluid" alt="Logo">
                                    </div>
                                    <div class="">
                                        <div class="text-center mb-3">
                                            <h2 class="mb-2">Forgot Password?</h2>
                                            <p class="mb-0">If you forgot your password, well, then we'll email you
                                                instructions to reset your password.</p>
                                        </div>

                                        @if (session('success'))
                                            <div class="alert alert-success">{{ session('success') }}</div>
                                        @endif

                                        @if (session('error'))
                                            <div class="alert alert-danger">{{ session('error') }}</div>
                                        @endif


                                        <div class="mb-3">
                                            <label class="form-label">Email Address</label>
                                            <div class="input-group">
                                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror border-end-0"
                                                    value="{{ old('email') }}" required autocomplete="email" autofocus>
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
