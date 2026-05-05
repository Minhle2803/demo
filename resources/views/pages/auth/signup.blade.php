@extends('layouts.auth')

@section('title', 'Signup')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card overflow-hidden m-0 card-bg-fill galaxy-border-none">
            <div class="row justify-content-center g-0">
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
                <div class="col-lg-6">
                    <div class="p-lg-5 p-4">
                        <div>
                            <h5 class="text-primary">{{ __('messages.auth.register_account') }}</h5>
                            <p class="text-muted">{{ __('messages.auth.get_free_account') }}</p>
                        </div>

                        <div class="mt-4">
                            <form class="needs-validation" novalidate action="{{ route('client.register') }}" method="POST" id="form-signup">
                                @csrf
                                @if (session('error'))
                                <div class="alert alert-danger material-shadow" role="alert">
                                    {{ session('error') }}
                                </div>
                                @endif
                                <div class="mb-3">
                                    <label for="nickname" class="form-label">{{ __('messages.auth.nickname') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="nickname" class="form-control" id="nickname" placeholder="{{ __('messages.auth.enter_nickname') }}" required>
                                    <div class="invalid-feedback">
                                        {{ __('messages.auth.validate_nickname') }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">{{ __('messages.auth.phone_number') }} <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone_number" placeholder="{{ __('messages.auth.enter_phone') }}" required>
                                    <div class="invalid-feedback">
                                        {{ __('messages.auth.validate_phone') }}
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="password-input">{{ __('messages.auth.password') }}</label>
                                    <div class="position-relative auth-pass-inputgroup">
                                        <input type="password" name="password" class="form-control pe-5 password-input" onpaste="return false" placeholder="{{ __('messages.auth.enter_password') }}" id="password-input" aria-describedby="passwordInput"  required>
                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                        <div class="invalid-feedback">
                                            {{ __('messages.auth.validate_password') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="referral_code" class="form-label">{{ __('messages.auth.referral_code') }}</label>
                                    <input type="text" class="form-control" id="referral_code" name="referral_code" placeholder="{{ __('messages.auth.enter_referral') }}">
                                </div>
                                <div class="mb-4">
                                    <p class="mb-0 fs-12 text-muted fst-italic">{{ __('messages.auth.terms_agree') }} <a href="#" class="text-primary text-decoration-underline fst-normal fw-medium">{{ __('messages.auth.terms_of_use') }}</a></p>
                                </div>

                                <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                    <h5 class="fs-13">{{ __('messages.auth.password_requirements') }}</h5>
                                    <p id="pass-length" class="invalid fs-12 mb-2">{!! __('messages.auth.min_chars') !!}</p>
                                    <p id="pass-lower" class="invalid fs-12 mb-2">{!! __('messages.auth.lowercase') !!}</p>
                                    <p id="pass-upper" class="invalid fs-12 mb-2">{!! __('messages.auth.uppercase') !!}</p>
                                    <p id="pass-number" class="invalid fs-12 mb-0">{!! __('messages.auth.number') !!}</p>
                                </div>

                                <div class="mt-4">
                                    <button class="btn btn-success w-100" type="submit">{{ __('messages.auth.signup') }}</button>
                                </div>
                            </form>
                        </div>

                        <div class="mt-5 text-center">
                            <p class="mb-0">{{ __('messages.auth.already_have_account') }} <a href="{{ route('signin') }}" class="fw-semibold text-primary text-decoration-underline"> {{ __('messages.auth.sign_in') }}</a> </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->

</div>
<!-- end row -->
@endsection

