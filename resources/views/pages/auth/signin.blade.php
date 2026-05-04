@extends('layouts.auth')

@section('title', 'Signin')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card overflow-hidden card-bg-fill galaxy-border-none">
            <div class="row g-0">
                <div class="col-lg-6">
                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                        <div class="bg-overlay"></div>
                        <div class="position-relative h-100 d-flex flex-column">
                            <div class="mb-4">
                                <a href="{{ route('landing2') }}" class="d-block">
                                    <img src="{{ asset('assets/images/logo/logo.png') }}" alt="" height="64">
                                </a>
                            </div>
                            <div class="mt-auto">
                                <div class="mb-3">
                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                </div>

                                <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-indicators">
                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                    </div>
                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                        <div class="carousel-item active">
                                            <p class="fs-15 fst-italic">" Institutional-grade spot trading. Real-time order matching, KYC-secured accounts, and non-custodial wallet protection "</p>
                                        </div>
                                        <div class="carousel-item">
                                            <p class="fs-15 fst-italic">" Row-level DB locking prevents race conditions. Negative balances are blocked at the database level."</p>
                                        </div>
                                        <div class="carousel-item">
                                            <p class="fs-15 fst-italic">" All trades auditable. Admin manual matching recorded with full timestamp and actor ID. "</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- end carousel -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col -->

                <div class="col-lg-6">
                    <div class="p-lg-5 p-4">
                        <div>
                            <h5 class="text-primary">{{ __('messages.auth.welcome_back') }}</h5>
                            <p class="text-muted">{{ __('messages.auth.sign_in_continue') }}</p>
                        </div>

                        <div class="mt-4">
                            <form  method="POST" id="loginForm" action="{{ route('client.login') }}">
                                @csrf
                                @if (session('error'))
                                <div class="alert alert-danger material-shadow" role="alert">
                                    {{ session('error') }}
                                </div>
                                @endif
                                <div class="mb-3">
                                    <label for="login" class="form-label">{{ __('messages.auth.email_or_phone') }}</label>
                                    <input type="text" class="form-control" id="login" value="{{ old('login') }}" name="login" placeholder="{{ __('messages.auth.enter_email_or_phone') }}">
                                </div>

                                <div class="mb-3">
                                    <div class="float-end">
                                        <a href="auth-pass-reset-cover.html" class="text-muted">{{ __('messages.auth.forgot_password') }}</a>
                                    </div>
                                    <label class="form-label" for="password-input">{{ __('messages.auth.password') }}</label>
                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                        <input type="password" class="form-control pe-5 password-input" name="password" placeholder="{{ __('messages.auth.enter_password') }}" id="password-input">
                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" name="remember" type="checkbox" value="" id="auth-remember-check">
                                    <label class="form-check-label" for="auth-remember-check">{{ __('messages.auth.remember_me') }}</label>
                                </div>

                                <div class="mt-4">
                                    <button class="btn btn-success w-100" type="submit">{{ __('messages.auth.sign_in') }}</button>
                                </div>
                            </form>
                        </div>

                        <div class="mt-5 text-center">
                            <p class="mb-0">{{ __('messages.auth.dont_have_account') }} <a href="{{ route('signup') }}" class="fw-semibold text-primary text-decoration-underline"> {{ __('messages.auth.signup') }}</a> </p>
                        </div>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->

</div>
<!-- end row -->
@endsection

@push('scripts')
    @vite(['resources/js/auth/signin.js'])
@endpush
