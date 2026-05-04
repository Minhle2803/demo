<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a class="logo logo-dark" href="{{ route('dashboard') }}">
            <span class="logo-sm">
                <img alt="" height="22" src="{{ asset('assets/images/logo-sm.png') }}" />
            </span>
            <span class="logo-lg">
                <img alt="" height="17" src="{{ asset('assets/images/logo-dark.png') }}" />
            </span>
        </a>
        <!-- Light Logo-->
        <a class="logo logo-light" href="{{ route('dashboard') }}">
            <span class="logo-sm">
                <img alt="" height="22" src="{{ asset('assets/images/logo-sm.png') }}" />
            </span>
            <span class="logo-lg">
                <img alt="" height="17" src="{{ asset('assets/images/logo-light.png') }}" />
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
                    <span class="d-block fw-medium sidebar-user-name-text">Anna Adame</span>
                    <span class="d-block fs-14 sidebar-user-name-sub-text"><i class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span class="align-middle">{{ __('messages.common.online') }}</span></span>
                </span>
            </span>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            <!-- item-->
            <h6 class="dropdown-header">{{ __('messages.auth.welcome_user', ['name' => 'Anna']) }}</h6>
            <a class="dropdown-item" href="pages-profile.html"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.profile') }}</span></a>
            <a class="dropdown-item" href="apps-chat.html"><i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.messages') }}</span></a>
            <a class="dropdown-item" href="apps-tasks-kanban.html"><i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Taskboard</span></a>
            <a class="dropdown-item" href="pages-faqs.html"><i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.help') }}</span></a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="pages-profile.html"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.balance') }} : <b>$5971.67</b></span></a>
            <a class="dropdown-item" href="pages-profile-settings.html"><span class="badge bg-success-subtle text-success mt-1 float-end">New</span><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.settings') }}</span></a>
            <a class="dropdown-item" href="auth-lockscreen-basic.html"><i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.auth.lock_screen') }}</span></a>
            <a class="dropdown-item" href="auth-logout-basic.html"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">{{ __('messages.common.logout') }}</span></a>
        </div>
    </div>
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">{{ __('messages.menu.menu') }}</span></li>
                <li class="nav-item">
                    <a  class="nav-link"  href="{{ route('dashboard') }}" role="button">
                        <i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">{{ __('messages.menu.home') }}</span>
                    </a>
                </li> <!-- end Dashboard Menu -->
                <li class="nav-item">
                    <a aria-controls="sidebarApps" aria-expanded="false" class="nav-link" href="{{ route('spot.trading') }}" role="button">
                        <i class="ri-apps-2-line"></i> <span data-key="t-apps">{{ __('messages.menu.spot_trading') }}</span>
                    </a>
                </li>
                
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>