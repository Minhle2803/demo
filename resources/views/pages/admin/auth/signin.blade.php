@extends('layouts.auth')

@section('title', __('admin.admin_signin'))

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
                                    <i class="ri-shield-check-line display-4 text-success"></i>
                                </div>
                                <p class="fs-15 fst-italic text-white-50">
                                    {{ __('admin.admin_portal_desc') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="p-lg-5 p-4">
                        <div>
                            <h5 class="text-primary">{{ __('admin.admin_signin') }}</h5>
                            <p class="text-muted">{{ __('admin.admin_signin_desc') }}</p>
                        </div>

                        <div class="mt-4">
                            <form method="POST" action="{{ route('admin.login') }}">
                                @csrf
                                @if (session('error'))
                                <div class="alert alert-danger material-shadow" role="alert">
                                    {{ session('error') }}
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label for="email" class="form-label">{{ __('admin.username') }}</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}" placeholder="admin" required autofocus>
                                </div>

                                <div class="mb-3">
                                    <div class="float-end">
                                        <a href="javascript:void(0);" class="text-muted">{{ __('admin.forgot_password') }}</a>
                                    </div>
                                    <label class="form-label" for="password-input">{{ __('admin.password') }}</label>
                                    <div class="position-relative  mb-3">
                                        <input type="password" class="form-control pe-5 password-input" name="password"
                                            placeholder="{{ __('admin.enter_password') }}" id="password-input" required>
                                        <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon material-shadow-none"
                                            type="button" id="password-addon">
                                            <i class="ri-eye-fill align-middle"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" name="remember" type="checkbox" id="auth-remember-check">
                                    <label class="form-check-label" for="auth-remember-check">{{ __('admin.remember_me') }}</label>
                                </div>

                                <div class="mt-4">
                                    <button class="btn btn-success w-100" type="submit">{{ __('admin.sign_in') }}</button>
                                </div>
                            </form>
                        </div>

                        <div class="mt-5 text-center">
                            <p class="mb-0 text-muted">
                                <i class="ri-shield-check-line align-middle me-1"></i>
                                {{ __('admin.restricted_area') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
