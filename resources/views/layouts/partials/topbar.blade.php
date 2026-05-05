<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box horizontal-logo">
                    <a class="logo logo-dark" href="{{ route('landing2') }}">
                        <span class="logo-sm">
                            <img alt="" height="22" src="{{ asset('assets/images/logo/logo.png') }}" />
                        </span>
                        <span class="logo-lg">
                            <img alt="" height="64" src="{{ asset('assets/images/logo/logo.png') }}" />
                        </span>
                    </a>
                    <a class="logo logo-light" href="{{ route('landing2') }}">
                        <span class="logo-sm">
                            <img alt="" height="64" src="{{ asset('assets/images/logo/logo.png') }}" />
                        </span>
                        <span class="logo-lg">
                            <img alt="" height="64" src="{{ asset('assets/images/logo/logo.png') }}" />
                        </span>
                    </a>
                </div>
                <button class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none" id="topnav-hamburger-icon" type="button">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                <!-- App Search-->
                <form class="app-search d-none d-md-block">
                    <div class="position-relative">
                        <input autocomplete="off" class="form-control" id="search-options" placeholder="{{ __('messages.common.search') }}" type="text" value="" />
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                        <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none" id="search-close-options"></span>
                    </div>
                    <div class="dropdown-menu dropdown-menu-lg" id="search-dropdown">
                        <div data-simplebar="" style="max-height: 320px;">
                            <!-- item-->
                            <div class="dropdown-header">
                                <h6 class="text-overflow text-muted mb-0 text-uppercase">Recent Searches</h6>
                            </div>
                            <div class="dropdown-item bg-transparent text-wrap">
                                <a class="btn btn-soft-secondary btn-sm rounded-pill" href="{{ route('landing2') }}">how to setup <i class="mdi mdi-magnify ms-1"></i></a>
                                <a class="btn btn-soft-secondary btn-sm rounded-pill" href="{{ route('landing2') }}">buttons <i class="mdi mdi-magnify ms-1"></i></a>
                            </div>
                            <!-- item-->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-1 text-uppercase">Pages</h6>
                            </div>
                            <!-- item-->
                            <a class="dropdown-item notify-item" href="javascript:void(0);">
                                <i class="ri-bubble-chart-line align-middle fs-18 text-muted me-2"></i>
                                <span>Analytics Dashboard</span>
                            </a>
                            <!-- item-->
                            <a class="dropdown-item notify-item" href="javascript:void(0);">
                                <i class="ri-lifebuoy-line align-middle fs-18 text-muted me-2"></i>
                                <span>Help Center</span>
                            </a>
                            <!-- item-->
                            <a class="dropdown-item notify-item" href="javascript:void(0);">
                                <i class="ri-user-settings-line align-middle fs-18 text-muted me-2"></i>
                                <span>My account settings</span>
                            </a>
                            <!-- item-->
                            <div class="dropdown-header mt-2">
                                <h6 class="text-overflow text-muted mb-2 text-uppercase">Members</h6>
                            </div>
                            <div class="notification-list">
                                <!-- item -->
                                <a class="dropdown-item notify-item py-2" href="javascript:void(0);">
                                    <div class="d-flex">
                                        <img alt="user-pic" class="me-3 rounded-circle avatar-xs" src="{{ asset('assets/images/users/avatar-2.jpg') }}" />
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">Angela Bernier</h6>
                                            <span class="fs-11 mb-0 text-muted">Manager</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a class="dropdown-item notify-item py-2" href="javascript:void(0);">
                                    <div class="d-flex">
                                        <img alt="user-pic" class="me-3 rounded-circle avatar-xs" src="{{ asset('assets/images/users/avatar-3.jpg') }}" />
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">David Grasso</h6>
                                            <span class="fs-11 mb-0 text-muted">Web Designer</span>
                                        </div>
                                    </div>
                                </a>
                                <!-- item -->
                                <a class="dropdown-item notify-item py-2" href="javascript:void(0);">
                                    <div class="d-flex">
                                        <img alt="user-pic" class="me-3 rounded-circle avatar-xs" src="{{ asset('assets/images/users/avatar-5.jpg') }}" />
                                        <div class="flex-grow-1">
                                            <h6 class="m-0">Mike Bunch</h6>
                                            <span class="fs-11 mb-0 text-muted">React Developer</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="text-center pt-3 pb-1">
                            <a class="btn btn-primary btn-sm" href="pages-search-results.html">View All Results <i class="ri-arrow-right-line ms-1"></i></a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown d-md-none topbar-head-dropdown header-item">
                    <button aria-expanded="false" aria-haspopup="true" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" id="page-header-search-dropdown" type="button">
                        <i class="bx bx-search fs-22"></i>
                    </button>
                    <div aria-labelledby="page-header-search-dropdown" class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">
                        <form class="p-3">
                            <div class="form-group m-0">
                                <div class="input-group">
                                    <input aria-label=') }}"Recipient' s username" class="form-control" placeholder="Search ..." type="text" />
                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="dropdown ms-1 topbar-head-dropdown header-item">
                    <button aria-expanded="false" aria-haspopup="true" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown" type="button">
                        <img alt="Header Language" class="rounded" height="20" id="header-lang-img" src="{{ asset('assets/images/flags/' . (app()->getLocale() === 'vi' ? 'vn' : 'us') . '.svg') }}" />
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item notify-item language py-2 {{ app()->getLocale() === 'en' ? 'active' : '' }}" data-lang="en" href="{{ route('lang.switch', 'en') }}" title="English">
                            <img alt="EN" class="me-2 rounded" height="18" src="{{ asset('assets/images/flags/us.svg') }}" />
                            <span class="align-middle">English</span>
                        </a>
                        <a class="dropdown-item notify-item language py-2 {{ app()->getLocale() === 'vi' ? 'active' : '' }}" data-lang="vi" href="{{ route('lang.switch', 'vi') }}" title="Vietnamese">
                            <img alt="VI" class="me-2 rounded" height="18" src="{{ asset('assets/images/flags/vn.svg') }}" />
                            <span class="align-middle">Tiếng Việt</span>
                        </a>
                    </div>
                </div>
                @auth('client')
                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <button aria-expanded="false" aria-haspopup="true" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" data-bs-auto-close="outside" data-bs-toggle="dropdown" id="page-header-notifications-dropdown" type="button">
                        <i class="bx bx-bell fs-22"></i>
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">3<span class="visually-hidden">unread messages</span></span>
                    </button>
                    <div aria-labelledby="page-header-notifications-dropdown" class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">
                        <div class="dropdown-head bg-primary bg-pattern rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold text-white"> {{ __('messages.common.notifications') }} </h6>
                                    </div>
                                    <div class="col-auto dropdown-tabs">
                                        <span class="badge bg-light text-body fs-13"> 4 New</span>
                                    </div>
                                </div>
                            </div>
                            <div class="px-2 pt-2">
                                <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" data-dropdown-tabs="true" id="notificationItemsTab" role="tablist">
                                    <li class="nav-item waves-effect waves-light">
                                        <a aria-selected="true" class="nav-link active" data-bs-toggle="tab" href="#all-noti-tab" role="tab">
                                            All (4)
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light">
                                        <a aria-selected="false" class="nav-link" data-bs-toggle="tab" href="#messages-tab" role="tab">
                                            Messages
                                        </a>
                                    </li>
                                    <li class="nav-item waves-effect waves-light">
                                        <a aria-selected="false" class="nav-link" data-bs-toggle="tab" href="#alerts-tab" role="tab">
                                            Alerts
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content position-relative" id="notificationItemsTabContent">
                            <div class="tab-pane fade show active py-2 ps-2" id="all-noti-tab" role="tabpanel">
                                <div class="pe-2" data-simplebar="" style="max-height: 300px;">
                                    <div class="text-reset notification-item d-block dropdown-item position-relative">
                                        <div class="d-flex">
                                            <div class="avatar-xs me-3 flex-shrink-0">
                                                <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16">
                                                    <i class="bx bx-badge-check"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <a class="stretched-link" href="#!">
                                                    <h6 class="mt-0 mb-2 lh-base">Your <b>Elite</b> author Graphic
                                                        Optimization <span class="text-secondary">reward</span> is
                                                        ready!
                                                    </h6>
                                                </a>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> Just 30 sec ago</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" id="all-notification-check01" type="checkbox" value="" />
                                                    <label class="form-check-label" for="all-notification-check01"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-reset notification-item d-block dropdown-item position-relative">
                                        <div class="d-flex">
                                            <img alt="user-pic" class="me-3 rounded-circle avatar-xs flex-shrink-0" src="{{ asset('assets/images/users/avatar-2.jpg') }}" />
                                            <div class="flex-grow-1">
                                                <a class="stretched-link" href="#!">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold">Angela Bernier</h6>
                                                </a>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">Answered to your comment on the cash flow forecast's
                                                        graph 🔔.</p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> 48 min ago</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" id="all-notification-check02" type="checkbox" value="" />
                                                    <label class="form-check-label" for="all-notification-check02"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-reset notification-item d-block dropdown-item position-relative">
                                        <div class="d-flex">
                                            <div class="avatar-xs me-3 flex-shrink-0">
                                                <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-16">
                                                    <i class="bx bx-message-square-dots"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1">
                                                <a class="stretched-link" href="#!">
                                                    <h6 class="mt-0 mb-2 fs-13 lh-base">You have received <b class="text-success">20</b> new messages in the conversation
                                                    </h6>
                                                </a>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> 2 hrs ago</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" id="all-notification-check03" type="checkbox" value="" />
                                                    <label class="form-check-label" for="all-notification-check03"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-reset notification-item d-block dropdown-item position-relative">
                                        <div class="d-flex">
                                            <img alt="user-pic" class="me-3 rounded-circle avatar-xs flex-shrink-0" src="{{ asset('assets/images/users/avatar-8.jpg') }}" />
                                            <div class="flex-grow-1">
                                                <a class="stretched-link" href="#!">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold">Maureen Gibson</h6>
                                                </a>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">We talked about a project on linkedin.</p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> 4 hrs ago</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" id="all-notification-check04" type="checkbox" value="" />
                                                    <label class="form-check-label" for="all-notification-check04"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-3 text-center view-all">
                                        <button class="btn btn-soft-success waves-effect waves-light" type="button">View
                                            All Notifications <i class="ri-arrow-right-line align-middle"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div aria-labelledby="messages-tab" class="tab-pane fade py-2 ps-2" id="messages-tab" role="tabpanel">
                                <div class="pe-2" data-simplebar="" style="max-height: 300px;">
                                    <div class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <img alt="user-pic" class="me-3 rounded-circle avatar-xs" src="{{ asset('assets/images/users/avatar-3.jpg') }}" />
                                            <div class="flex-grow-1">
                                                <a class="stretched-link" href="#!">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold">James Lemire</h6>
                                                </a>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">We talked about a project on linkedin.</p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> 30 min ago</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" id="messages-notification-check01" type="checkbox" value="" />
                                                    <label class="form-check-label" for="messages-notification-check01"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <img alt="user-pic" class="me-3 rounded-circle avatar-xs" src="{{ asset('assets/images/users/avatar-2.jpg') }}" />
                                            <div class="flex-grow-1">
                                                <a class="stretched-link" href="#!">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold">Angela Bernier</h6>
                                                </a>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">Answered to your comment on the cash flow forecast's
                                                        graph 🔔.</p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> 2 hrs ago</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" id="messages-notification-check02" type="checkbox" value="" />
                                                    <label class="form-check-label" for="messages-notification-check02"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <img alt="user-pic" class="me-3 rounded-circle avatar-xs" src="{{ asset('assets/images/users/avatar-6.jpg') }}" />
                                            <div class="flex-grow-1">
                                                <a class="stretched-link" href="#!">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold">Kenneth Brown</h6>
                                                </a>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">Mentionned you in his comment on 📃 invoice #12501.
                                                    </p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> 10 hrs ago</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" id="messages-notification-check03" type="checkbox" value="" />
                                                    <label class="form-check-label" for="messages-notification-check03"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <img alt="user-pic" class="me-3 rounded-circle avatar-xs" src="{{ asset('assets/images/users/avatar-8.jpg') }}" />
                                            <div class="flex-grow-1">
                                                <a class="stretched-link" href="#!">
                                                    <h6 class="mt-0 mb-1 fs-13 fw-semibold">Maureen Gibson</h6>
                                                </a>
                                                <div class="fs-13 text-muted">
                                                    <p class="mb-1">We talked about a project on linkedin.</p>
                                                </div>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> 3 days ago</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-15">
                                                <div class="form-check notification-check">
                                                    <input class="form-check-input" id="messages-notification-check04" type="checkbox" value="" />
                                                    <label class="form-check-label" for="messages-notification-check04"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="my-3 text-center view-all">
                                        <button class="btn btn-soft-success waves-effect waves-light" type="button">View
                                            All Messages <i class="ri-arrow-right-line align-middle"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div aria-labelledby="alerts-tab" class="tab-pane fade p-4" id="alerts-tab" role="tabpanel"></div>
                            <div class="notification-actions" id="notification-actions">
                                <div class="d-flex text-muted justify-content-center">
                                    Select <div class="text-body fw-semibold px-1" id="select-content">0</div> Result <button class="btn btn-link link-danger p-0 ms-3" data-bs-target="#removeNotificationModal" data-bs-toggle="modal" type="button">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button aria-expanded="false" aria-haspopup="true" class="btn material-shadow-none" data-bs-toggle="dropdown" id="page-header-user-dropdown" type="button">
                        <span class="d-flex align-items-center">
                            <img alt="Header Avatar" class="rounded-circle header-profile-user" src="{{ asset('assets/images/users/avatar-1.jpg') }}" />
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::guard('client')->user()->nickname }}</span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <h6 class="dropdown-header">{{ __('messages.auth.welcome_user', ['name' => Auth::guard('client')->user()->nickname]) }}</h6>
                        <a class="dropdown-item" href="{{ route('client.profile.show') }}"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.profile') }}</span></a>
                        <a class="dropdown-item" href="apps-chat.html"><i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.messages') }}</span></a>
                        <a class="dropdown-item" href="pages-faqs.html"><i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.help') }}</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('client.profile.show') }}"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.balance') }} : <b>{{ Auth::guard('client')->user()->balance }}</b></span></a>
                        <a class="dropdown-item" href="auth-lockscreen-basic.html"><i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.auth.lock_screen') }}</span></a>
                        <a class="dropdown-item" href="auth-logout-basic.html"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle">{{ __('messages.common.logout') }}</span></a>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </div>
</header>