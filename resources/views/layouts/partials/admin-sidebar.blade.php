<div class="app-menu navbar-menu">
    <div class="navbar-brand-box">
        <a class="logo logo-dark" href="{{ route('landing2') }}">
            <span class="logo-sm">
                <img alt="" height="64" src="{{ asset('assets/images/logo/logo.png') }}" />
            </span>
            <span class="logo-lg">
                <img alt="" height="64" src="{{ asset('assets/images/logo/logo.png') }}" />
            </span>
        </a>
        <!-- Light Logo-->
        <a class="logo logo-light" href="{{ route('landing2') }}">
            <span class="logo-sm">
                <img alt="" height="64" src="{{ asset('assets/images/logo/logo.png') }}" />
            </span>
            <span class="logo-lg">
                <img alt="" height="64" src="{{ asset('assets/images/logo/logo.png') }}" />
            </span>
        </a>
        <button class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover" type="button">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div class="dropdown sidebar-user m-1 rounded">
        <button aria-expanded="false" aria-haspopup="true" class="btn material-shadow-none" data-bs-toggle="dropdown" id="page-header-user-dropdown" type="button">
            <span class="d-flex align-items-center gap-2">
                <img alt="Header Avatar" class="rounded header-profile-user" src="{{ asset('assets/images/users/avatar-1.jpg') }}" />
                <span class="text-start">
                    <span class="d-block fw-medium sidebar-user-name-text">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <span class="d-block fs-14 sidebar-user-name-sub-text"><i class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span class="align-middle">Admin</span></span>
                </span>
            </span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <h6 class="dropdown-header">{{ __('admin.welcome_admin', ['name' => Auth::user()->name ?? 'Admin']) }}</h6>
            <a class="dropdown-item" href="{{ route('admin.settings.index') }}"><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('admin.settings') }}</span></a>
            <div class="dropdown-divider"></div>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.logout') }}</span></button>
            </form>
        </div>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">{{ __('admin.admin') }}</span></li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="ri-dashboard-2-line"></i> <span>{{ __('admin.dashboard') }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.show') || request()->routeIs('admin.users.edit') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                        <i class="ri-user-settings-line"></i> <span>{{ __('admin.user_management') }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.kyc') ? 'active' : '' }}" href="{{ route('admin.users.kyc') }}">
                        <i class="ri-shield-check-line"></i> <span>{{ __('admin.kyc_verification') }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.deposits.*') ? 'active' : '' }}" href="{{ route('admin.deposits.index') }}">
                        <i class="ri-upload-cloud-2-line"></i> <span>{{ __('admin.deposit_management') }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.withdraws.*') ? 'active' : '' }}" href="{{ route('admin.withdraws.index') }}">
                        <i class="ri-download-cloud-2-line"></i> <span>{{ __('admin.withdraw_management') }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.referrals.*') ? 'active' : '' }}" href="{{ route('admin.referrals.index') }}">
                        <i class="ri-links-line"></i> <span>Referrals</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}" href="{{ route('admin.sessions.index') }}">
                        <i class="ri-time-line"></i> <span>Sessions</span>
                    </a>
                </li>

                <li class="menu-title"><span>{{ __('admin.settings') }}</span></li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                        <i class="ri-settings-2-line"></i> <span>{{ __('admin.bank_settings') }}</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.crypto-assets.*') ? 'active' : '' }}" href="{{ route('admin.crypto-assets.index') }}">
                        <i class="ri-coins-line"></i> <span>{{ __('admin.crypto_asset_management') }}</span>
                    </a>
                </li>

                <li class="menu-title"><span>{{ __('messages.menu.link') }}</span></li>
            </ul>
        </div>
    </div>
    <div class="sidebar-background"></div>
</div>
