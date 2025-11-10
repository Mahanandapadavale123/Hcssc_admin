<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HCSSC - Handicrafts and Carpet Sector Skill Council</title>
    <link rel="stylesheet" href="{{ asset('enduser/style.css') }}" >
    <link rel="stylesheet" href="{{ asset('enduser/sign.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/9e52eae9bf.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{  asset('hcssc1.png') }}">
</head>

<body>
   <div class="bgMain">
        <div class="container">

            @if (session('message'))
                <div class="alert alert-warning">
                    {{ session('message') }}
                </div>
            @endif
            @if ($errors->has('captcha'))
                <div class="alert alert-danger">
                    {{ $errors->first('captcha') }}
                </div>
            @endif


            <div class="row">
                <div class="col-sm-12 col-md-5 d-none d-md-block">
                    <div class="loginPageText">
                        <p class="loginPageHeading">Welcome To <br><span>HCSSC<span></p>
                        <p class="loginPageSubHeading">Handicrafts and Carpet <br>Sector Skill Council</p>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="rounded-3 my-md-5 p-4 border border-1 shadow bg-light" style="box-sizing: content-box;">
                        <div class="text-center mb-2">
                            <img src="{{  asset('hcssc1.png') }}" alt="" width="70px">
                        </div>
                        <h2 class="card-header text-center mb-2 fw-bold mt-2">{{ __('Sign Up') }}</h2>
                        <p class="card-header text-center mb-4 fw-normal">{{ __('Welcome to HCSSC') }}</p>

                        <form method="POST" action="/register" name="hcsscRegForm" id="hcsscRegForm">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="user_type"
                                            class="col-form-label fw-semibold">{{ __('Choose User Type') }}</label>
                                        <div>
                                            <select id="user_type" class="form-select @error('user_type') is-invalid @enderror"  name="user_type" required>
                                                <option value="TP User" {{ old('user_type', $user_type ?? '') == 'TP User' ? 'selected' : '' }}>Training Partner</option>
                                                <option value="Industry" {{ old('user_type', $user_type ?? '') == 'Industry' ? 'selected' : '' }}>Industry</option>
                                                <option value="CoE" {{ old('user_type', $user_type ?? '') == 'CoE' ? 'selected' : '' }}>Center Of Excellence</option>
                                            </select>
                                            @error('user_type')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="spoc_name" class=" col-form-label fw-semibold">{{ __('Spoc name') }}</label>
                                            @include('enduser.components.infoicon', [
                                                'name' => 'spoc_name',
                                                'define' => 'Single point of contact',
                                            ])
                                        <div>
                                            <input id="spoc_name" type="text"
                                                class="form-control @error('spoc_name') is-invalid @enderror"
                                                name="spoc_name" value="{{ old('spoc_name') }}" placeholder="" >
                                            @error('spoc_name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tp_name"
                                    class="col-form-label fw-semibold">{{ __('Organisation Name') }}</label>
                                <div>
                                    <input id="tp_name" type="text"
                                        class="form-control @error('tp_name') is-invalid @enderror" name="tp_name"
                                        value="{{ old('tp_name') }}" placeholder="" >
                                    @error('tp_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="phone"
                                            class=" col-form-label fw-semibold">{{ __('Mobile No.') }}</label>
                                        <div>
                                            <input id="phone" type="tel"
                                                class="form-control @error('phone') is-invalid @enderror" name="phone"
                                                value="{{ old('phone') }}" placeholder="" >
                                            @error('phone')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="email"
                                            class="col-form-label fw-semibold">{{ __('Email Address') }}</label>
                                        <div>
                                            <input id="email" type="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                name="email" value="{{ old('email') }}"
                                                autocomplete="email" placeholder="">
                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="password"
                                            class="col-form-label fw-semibold">{{ __('Password*') }}</label>
                                        <div>
                                            <input id="password" type="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                name="password"  autocomplete="new-password" placeholder="">

                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-3">
                                        <label for="password-confirm"
                                            class="fw-semibold col-form-label ">{{ __('Confirm Password*') }}</label>
                                        <div>
                                            <input id="password-confirm" type="password" class="form-control"
                                                name="password_confirmation"  autocomplete="new-password"
                                                placeholder="">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-0">
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value=""
                                            id="flexCheckDefault" >
                                        <label class="form-check-label" for="flexCheckDefault">
                                            <a class="link fw-normal" href="/TermsAndConditions"
                                                target="_block">{{ __('Agree to Terms and Conditions') }}</a>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="mb-3">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="g-recaptcha" data-sitekey="{{env('RECAPTCHA_SITE_KEY')}}"></div>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="mb-0">
                                <div>
                                    <button style="background-color: #0DAB4E" type="submit"
                                        class="btn w-100 text-light fw-bold">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>
                            <p class="text-center mt-2">
                                <a class="link fw-normal " href="/login"
                                    onclick="location.href='/login'">{{ __('Already have an account?') }}</a>
                            </p>
                        </form>
                    </div>
                </div>
                <div class="col-sm-12 col-md-1"></div>
            </div>
        </div>
    </div>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script>
        window.addEventListener('load', () => {
            const $recaptcha = document.querySelector('#g-recaptcha-response');
            if ($recaptcha) {
                $recaptcha.setAttribute('required', 'required');

                const $form = $recaptcha.closest('form');
                $form.addEventListener('submit', (event) => {
                    if (!$recaptcha.value) {
                        event.preventDefault();
                        const $error = document.querySelector('.recaptcha-error');
                        if ($error) {
                            $error.remove();
                        }
                        const $errorMessage = document.createElement('span');
                        $errorMessage.classList.add('recaptcha-error');
                        $errorMessage.innerText = 'Please complete the reCAPTCHA field.';
                        $recaptcha.after($errorMessage);
                    }
                });
            }
        });
    </script>
    <script>
        // $(document).ready(function () {
        //     var form = $('#hcsscRegForm');
        //     var rules = {
        //         user_type: 'required',
        //         spoc_name: {
        //             required: true,
        //             digits: false,
        //         },
        //         // }'required',
        //         tp_name: 'required',
        //         phone: {
        //             required: true,
        //             digits: true,
        //             minlength: 10,
        //             maxlength: 10
        //         },
        //         email: {
        //             required: true,
        //             email: true
        //         },
        //         password: {
        //             required: true,
        //             minlength: 8
        //         },
        //         password_confirmation: {
        //             required: true,
        //             equalTo: '#password'
        //         },
        //         captcha: 'required'
        //     };

        //     var messages = {
        //         user_type: 'Please select a user type',
        //         spoc_name: 'Please enter your full name',
        //         tp_name: 'Please enter the training partner name',
        //         phone: {
        //             required: 'Please enter your mobile number',
        //             digits: 'Please enter only digits',
        //             minlength: 'Please enter a valid 10-digit mobile number',
        //             maxlength: 'Please enter a valid 10-digit mobile number'
        //         },
        //         email: {
        //             required: 'Please enter your email address',
        //             email: 'Please enter a valid email address'
        //         },
        //         password: {
        //             required: 'Please enter a password',
        //             minlength: 'Please enter a password with at least 8 characters'
        //         },
        //         password_confirmation: {
        //             required: 'Please confirm your password',
        //             equalTo: 'Passwords do not match'
        //         },
        //         captcha: 'Please enter the captcha'
        //     };

        //     // Set up form validation using jQuery Validate plugin
        //     form.validate({
        //         rules: rules,
        //         messages: messages,
        //         errorElement: 'span',
        //         errorPlacement: function (error, element) {
        //             error.addClass('invalid-feedback');
        //             element.closest('.mb-3').append(error);
        //         },
        //         highlight: function (element, errorClass, validClass) {
        //             $(element).addClass('is-invalid').removeClass('is-valid');
        //         },
        //         unhighlight: function (element, errorClass, validClass) {
        //             $(element).removeClass('is-invalid').addClass('is-valid');
        //         }
        //     });
        // });

        // $('#hcsscRegForm').submit(function (e) {
        //     e.preventDefault();
        //     var formValid = true;
        //     $(this).find(':required').each(function () {
        //         if (!$(this).val()) {
        //             $(this).addClass('is-invalid');
        //             formValid = false;
        //         } else {
        //             $(this).removeClass('is-invalid');
        //         }
        //     });
        //     if (formValid) { this.submit(); }
        // });

        // validate fields on change
        // $('#hcsscRegForm :input').change(function () {
        //     if ($(this).val()) { $(this).removeClass('is-invalid'); }
        // });
    </script>
</body>

</html>
