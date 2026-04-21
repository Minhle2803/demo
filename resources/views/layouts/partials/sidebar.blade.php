<div class="app-menu navbar-menu">
<!-- LOGO -->
<div class="navbar-brand-box">
<!-- Dark Logo-->
<a class="logo logo-dark" href="{{ route('dashboard') }}">
<span class="logo-sm">
<img alt="" height="22" src="{{ asset('assets/images/logo-sm.png') }}"/>
</span>
<span class="logo-lg">
<img alt="" height="17" src="{{ asset('assets/images/logo-dark.png') }}"/>
</span>
</a>
<!-- Light Logo-->
<a class="logo logo-light" href="{{ route('dashboard') }}">
<span class="logo-sm">
<img alt="" height="22" src="{{ asset('assets/images/logo-sm.png') }}"/>
</span>
<span class="logo-lg">
<img alt="" height="17" src="{{ asset('assets/images/logo-light.png') }}"/>
</span>
</a>
<button class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover" type="button">
<i class="ri-record-circle-line"></i>
</button>
</div>
<div class="dropdown sidebar-user m-1 rounded">
<button aria-expanded="false" aria-haspopup="true" class="btn material-shadow-none" data-bs-toggle="dropdown" id="page-header-user-dropdown" type="button">
<span class="d-flex align-items-center gap-2">
<img alt="Header Avatar" class="rounded header-profile-user" src="{{ asset('assets/images/users/avatar-1.jpg') }}"/>
<span class="text-start">
<span class="d-block fw-medium sidebar-user-name-text">Anna Adame</span>
<span class="d-block fs-14 sidebar-user-name-sub-text"><i class="ri ri-circle-fill fs-10 text-success align-baseline"></i> <span class="align-middle">Online</span></span>
</span>
</span>
</button>
<div class="dropdown-menu dropdown-menu-end">
<!-- item-->
<h6 class="dropdown-header">Welcome Anna!</h6>
<a class="dropdown-item" href="pages-profile.html"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span></a>
<a class="dropdown-item" href="apps-chat.html"><i class="mdi mdi-message-text-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Messages</span></a>
<a class="dropdown-item" href="apps-tasks-kanban.html"><i class="mdi mdi-calendar-check-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Taskboard</span></a>
<a class="dropdown-item" href="pages-faqs.html"><i class="mdi mdi-lifebuoy text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Help</span></a>
<div class="dropdown-divider"></div>
<a class="dropdown-item" href="pages-profile.html"><i class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Balance : <b>$5971.67</b></span></a>
<a class="dropdown-item" href="pages-profile-settings.html"><span class="badge bg-success-subtle text-success mt-1 float-end">New</span><i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Settings</span></a>
<a class="dropdown-item" href="auth-lockscreen-basic.html"><i class="mdi mdi-lock text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Lock screen</span></a>
<a class="dropdown-item" href="auth-logout-basic.html"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
</div>
</div>
<div id="scrollbar">
<div class="container-fluid">
<div id="two-column-menu">
</div>
<ul class="navbar-nav" id="navbar-nav">
<li class="menu-title"><span data-key="t-menu">Menu</span></li>
<li class="nav-item">
<a aria-controls="sidebarDashboards" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarDashboards" role="button">
<i class="ri-dashboard-2-line"></i> <span data-key="t-dashboards">Dashboards</span>
</a>
<div class="collapse menu-dropdown" id="sidebarDashboards">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-analytics" href="dashboard-analytics.html"> Analytics </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-crm" href="dashboard-crm.html"> CRM </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-ecommerce" href="{{ route('dashboard') }}"> Ecommerce </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-crypto" href="dashboard-crypto.html"> Crypto </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-projects" href="dashboard-projects.html"> Projects </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-nft" href="dashboard-nft.html"> NFT</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-job" href="dashboard-job.html">Job</a>
</li>
</ul>
</div>
</li> <!-- end Dashboard Menu -->
<li class="nav-item">
<a aria-controls="sidebarApps" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarApps" role="button">
<i class="ri-apps-2-line"></i> <span data-key="t-apps">Apps</span>
</a>
<div class="collapse menu-dropdown" id="sidebarApps">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a aria-controls="sidebarCalendar" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-calender" href="#sidebarCalendar" role="button">
                                            Calendar
                                        </a>
<div class="collapse menu-dropdown" id="sidebarCalendar">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-main-calender" href="apps-calendar.html"> Main Calender </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-month-grid" href="apps-calendar-month-grid.html"> Month Grid </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-chat" href="apps-chat.html"> Chat </a>
</li>
<li class="nav-item">
<a aria-controls="sidebarEmail" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-email" href="#sidebarEmail" role="button">
                                            Email
                                        </a>
<div class="collapse menu-dropdown" id="sidebarEmail">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-mailbox" href="apps-mailbox.html"> Mailbox </a>
</li>
<li class="nav-item">
<a aria-controls="sidebaremailTemplates" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-email-templates" href="#sidebaremailTemplates" role="button">
                                                        Email Templates
                                                    </a>
<div class="collapse menu-dropdown" id="sidebaremailTemplates">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-basic-action" href="apps-email-basic.html"> Basic Action </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-ecommerce-action" href="apps-email-ecommerce.html"> Ecommerce Action </a>
</li>
</ul>
</div>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarEcommerce" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-ecommerce" href="#sidebarEcommerce" role="button">
                                            Ecommerce
                                        </a>
<div class="collapse menu-dropdown" id="sidebarEcommerce">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-products" href="apps-ecommerce-products.html"> Products </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-product-Details" href="apps-ecommerce-product-details.html"> Product Details </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-create-product" href="apps-ecommerce-add-product.html"> Create Product </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-orders" href="apps-ecommerce-orders.html">
                                                        Orders </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-order-details" href="apps-ecommerce-order-details.html"> Order Details </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-customers" href="apps-ecommerce-customers.html"> Customers </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-shopping-cart" href="apps-ecommerce-cart.html"> Shopping Cart </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-checkout" href="apps-ecommerce-checkout.html"> Checkout </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-sellers" href="apps-ecommerce-sellers.html">
                                                        Sellers </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-sellers-details" href="apps-ecommerce-seller-details.html"> Seller Details </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarProjects" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-projects" href="#sidebarProjects" role="button">
                                            Projects
                                        </a>
<div class="collapse menu-dropdown" id="sidebarProjects">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-list" href="apps-projects-list.html"> List
                                                    </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-overview" href="apps-projects-overview.html"> Overview </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-create-project" href="apps-projects-create.html"> Create Project </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarTasks" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-tasks" href="#sidebarTasks" role="button"> Tasks
                                        </a>
<div class="collapse menu-dropdown" id="sidebarTasks">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-kanbanboard" href="apps-tasks-kanban.html">
                                                        Kanban Board </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-list-view" href="apps-tasks-list-view.html">
                                                        List View </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-task-details" href="apps-tasks-details.html"> Task Details </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarCRM" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-crm" href="#sidebarCRM" role="button"> CRM
                                        </a>
<div class="collapse menu-dropdown" id="sidebarCRM">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-contacts" href="apps-crm-contacts.html">
                                                        Contacts </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-companies" href="apps-crm-companies.html">
                                                        Companies </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-deals" href="apps-crm-deals.html"> Deals
                                                    </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-leads" href="apps-crm-leads.html"> Leads
                                                    </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarCrypto" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-crypto" href="#sidebarCrypto" role="button"> Crypto
                                        </a>
<div class="collapse menu-dropdown" id="sidebarCrypto">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-transactions" href="apps-crypto-transactions.html"> Transactions </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-buy-sell" href="apps-crypto-buy-sell.html">
                                                        Buy &amp; Sell </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-orders" href="apps-crypto-orders.html">
                                                        Orders </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-my-wallet" href="apps-crypto-wallet.html">
                                                        My Wallet </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-ico-list" href="apps-crypto-ico.html"> ICO
                                                        List </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-kyc-application" href="apps-crypto-kyc.html"> KYC Application </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarInvoices" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-invoices" href="#sidebarInvoices" role="button">
                                            Invoices
                                        </a>
<div class="collapse menu-dropdown" id="sidebarInvoices">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-list-view" href="apps-invoices-list.html">
                                                        List View </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-details" href="apps-invoices-details.html">
                                                        Details </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-create-invoice" href="apps-invoices-create.html"> Create Invoice </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarTickets" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-supprt-tickets" href="#sidebarTickets" role="button">
                                            Support Tickets
                                        </a>
<div class="collapse menu-dropdown" id="sidebarTickets">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-list-view" href="apps-tickets-list.html">
                                                        List View </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-ticket-details" href="apps-tickets-details.html"> Ticket Details </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarnft" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-nft-marketplace" href="#sidebarnft" role="button">
                                            NFT Marketplace
                                        </a>
<div class="collapse menu-dropdown" id="sidebarnft">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-marketplace" href="apps-nft-marketplace.html"> Marketplace </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-explore-now" href="apps-nft-explore.html"> Explore Now </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-live-auction" href="apps-nft-auction.html"> Live Auction </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-item-details" href="apps-nft-item-details.html"> Item Details </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-collections" href="apps-nft-collections.html"> Collections </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-creators" href="apps-nft-creators.html"> Creators </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-ranking" href="apps-nft-ranking.html"> Ranking </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-wallet-connect" href="apps-nft-wallet.html"> Wallet Connect </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-create-nft" href="apps-nft-create.html"> Create NFT </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a class="nav-link" href="apps-file-manager.html"> <span data-key="t-file-manager">File Manager</span></a>
</li>
<li class="nav-item">
<a class="nav-link" href="apps-todo.html"> <span data-key="t-to-do">To Do</span></a>
</li>
<li class="nav-item">
<a aria-controls="sidebarjobs" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-jobs" href="#sidebarjobs" role="button"> Jobs</a>
<div class="collapse menu-dropdown" id="sidebarjobs">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-statistics" href="apps-job-statistics.html"> Statistics </a>
</li>
<li class="nav-item">
<a aria-controls="sidebarJoblists" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-job-lists" href="#sidebarJoblists" role="button">
                                                        Job Lists
                                                    </a>
<div class="collapse menu-dropdown" id="sidebarJoblists">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-list" href="apps-job-lists.html"> List
                                                                </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-grid" href="apps-job-grid-lists.html"> Grid </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-overview" href="apps-job-details.html"> Overview</a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarCandidatelists" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-candidate-lists" href="#sidebarCandidatelists" role="button">
                                                        Candidate Lists
                                                    </a>
<div class="collapse menu-dropdown" id="sidebarCandidatelists">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-list-view" href="apps-job-candidate-lists.html"> List View
                                                                </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-grid-view" href="apps-job-candidate-grid.html"> Grid View</a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-application" href="apps-job-application.html"> Application </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-new-job" href="apps-job-new.html"> New Job </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-companies-list" href="apps-job-companies-lists.html"> Companies List </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-job-categories" href="apps-job-categories.html"> Job Categories</a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-api-key" href="apps-api-key.html">API Key</a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarLayouts" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarLayouts" role="button">
<i class="ri-layout-3-line"></i> <span data-key="t-layouts">Layouts</span> <span class="badge badge-pill bg-danger" data-key="t-hot">Hot</span>
</a>
<div class="collapse menu-dropdown" id="sidebarLayouts">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-horizontal" href="layouts-horizontal.html" target="_blank">Horizontal</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-detached" href="layouts-detached.html" target="_blank">Detached</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-two-column" href="layouts-two-column.html" target="_blank">Two Column</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-hovered" href="layouts-vertical-hovered.html" target="_blank">Hovered</a>
</li>
</ul>
</div>
</li> <!-- end Dashboard Menu -->
<li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-pages">Pages</span></li>
<li class="nav-item">
<a aria-controls="sidebarAuth" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarAuth" role="button">
<i class="ri-account-circle-line"></i> <span data-key="t-authentication">Authentication</span>
</a>
<div class="collapse menu-dropdown" id="sidebarAuth">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a aria-controls="sidebarSignIn" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-signin" href="#sidebarSignIn" role="button"> Sign In
                                        </a>
<div class="collapse menu-dropdown" id="sidebarSignIn">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-basic" href="auth-signin-basic.html"> Basic
                                                    </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-cover" href="auth-signin-cover.html"> Cover
                                                    </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarSignUp" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-signup" href="#sidebarSignUp" role="button"> Sign Up
                                        </a>
<div class="collapse menu-dropdown" id="sidebarSignUp">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-basic" href="auth-signup-basic.html"> Basic
                                                    </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-cover" href="auth-signup-cover.html"> Cover
                                                    </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarResetPass" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-password-reset" href="#sidebarResetPass" role="button">
                                            Password Reset
                                        </a>
<div class="collapse menu-dropdown" id="sidebarResetPass">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-basic" href="auth-pass-reset-basic.html">
                                                        Basic </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-cover" href="auth-pass-reset-cover.html">
                                                        Cover </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarchangePass" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-password-create" href="#sidebarchangePass" role="button">
                                            Password Create
                                        </a>
<div class="collapse menu-dropdown" id="sidebarchangePass">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-basic" href="auth-pass-change-basic.html">
                                                        Basic </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-cover" href="auth-pass-change-cover.html">
                                                        Cover </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarLockScreen" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-lock-screen" href="#sidebarLockScreen" role="button">
                                            Lock Screen
                                        </a>
<div class="collapse menu-dropdown" id="sidebarLockScreen">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-basic" href="auth-lockscreen-basic.html">
                                                        Basic </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-cover" href="auth-lockscreen-cover.html">
                                                        Cover </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarLogout" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-logout" href="#sidebarLogout" role="button"> Logout
                                        </a>
<div class="collapse menu-dropdown" id="sidebarLogout">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-basic" href="auth-logout-basic.html"> Basic
                                                    </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-cover" href="auth-logout-cover.html"> Cover
                                                    </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarSuccessMsg" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-success-message" href="#sidebarSuccessMsg" role="button"> Success Message
                                        </a>
<div class="collapse menu-dropdown" id="sidebarSuccessMsg">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-basic" href="auth-success-msg-basic.html">
                                                        Basic </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-cover" href="auth-success-msg-cover.html">
                                                        Cover </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarTwoStep" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-two-step-verification" href="#sidebarTwoStep" role="button"> Two Step Verification
                                        </a>
<div class="collapse menu-dropdown" id="sidebarTwoStep">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-basic" href="auth-twostep-basic.html"> Basic
                                                    </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-cover" href="auth-twostep-cover.html"> Cover
                                                    </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarErrors" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-errors" href="#sidebarErrors" role="button"> Errors
                                        </a>
<div class="collapse menu-dropdown" id="sidebarErrors">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-404-basic" href="auth-404-basic.html"> 404
                                                        Basic </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-404-cover" href="auth-404-cover.html"> 404
                                                        Cover </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-404-alt" href="auth-404-alt.html"> 404 Alt
                                                    </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-500" href="auth-500.html"> 500 </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-offline-page" href="auth-offline.html"> Offline Page </a>
</li>
</ul>
</div>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarPages" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarPages" role="button">
<i class="ri-pages-line"></i> <span data-key="t-pages">Pages</span>
</a>
<div class="collapse menu-dropdown" id="sidebarPages">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-starter" href="pages-starter.html"> Starter </a>
</li>
<li class="nav-item">
<a aria-controls="sidebarProfile" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-profile" href="#sidebarProfile" role="button"> Profile
                                        </a>
<div class="collapse menu-dropdown" id="sidebarProfile">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-simple-page" href="pages-profile.html">
                                                        Simple Page </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-settings" href="pages-profile-settings.html"> Settings </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-team" href="pages-team.html"> Team </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-timeline" href="pages-timeline.html"> Timeline </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-faqs" href="pages-faqs.html"> FAQs </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-pricing" href="pages-pricing.html"> Pricing </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-gallery" href="pages-gallery.html"> Gallery </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-maintenance" href="pages-maintenance.html"> Maintenance
                                        </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-coming-soon" href="pages-coming-soon.html"> Coming Soon
                                        </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-sitemap" href="pages-sitemap.html"> Sitemap </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-search-results" href="pages-search-results.html"> Search Results </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-privacy-policy" href="pages-privacy-policy.html">Privacy Policy</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-term-conditions" href="pages-term-conditions.html">Term &amp; Conditions</a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarLanding" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarLanding" role="button">
<i class="ri-rocket-line"></i> <span data-key="t-landing">Landing</span>
</a>
<div class="collapse menu-dropdown" id="sidebarLanding">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-one-page" href="landing.html"> One Page </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-nft-landing" href="nft-landing.html"> NFT Landing </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-job" href="job-landing.html">Job</a>
</li>
</ul>
</div>
</li>
<li class="menu-title"><i class="ri-more-fill"></i> <span data-key="t-components">Components</span></li>
<li class="nav-item">
<a aria-controls="sidebarUI" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarUI" role="button">
<i class="ri-pencil-ruler-2-line"></i> <span data-key="t-base-ui">Base UI</span>
</a>
<div class="collapse menu-dropdown mega-dropdown-menu" id="sidebarUI">
<div class="row">
<div class="col-lg-4">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-alerts" href="ui-alerts.html">Alerts</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-badges" href="ui-badges.html">Badges</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-buttons" href="ui-buttons.html">Buttons</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-colors" href="ui-colors.html">Colors</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-cards" href="ui-cards.html">Cards</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-carousel" href="ui-carousel.html">Carousel</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-dropdowns" href="ui-dropdowns.html">Dropdowns</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-grid" href="ui-grid.html">Grid</a>
</li>
</ul>
</div>
<div class="col-lg-4">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-images" href="ui-images.html">Images</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-tabs" href="ui-tabs.html">Tabs</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-accordion-collapse" href="ui-accordions.html">Accordion &amp; Collapse</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-modals" href="ui-modals.html">Modals</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-offcanvas" href="ui-offcanvas.html">Offcanvas</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-placeholders" href="ui-placeholders.html">Placeholders</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-progress" href="ui-progress.html">Progress</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-notifications" href="ui-notifications.html">Notifications</a>
</li>
</ul>
</div>
<div class="col-lg-4">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-media-object" href="ui-media.html">Media
                                                    object</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-embed-video" href="ui-embed-video.html">Embed
                                                    Video</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-typography" href="ui-typography.html">Typography</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-lists" href="ui-lists.html">Lists</a>
</li>
<li class="nav-item">
<a class="nav-link" href="ui-links.html"><span data-key="t-links">Links</span> <span class="badge badge-pill bg-success" data-key="t-new">New</span></a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-general" href="ui-general.html">General</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-ribbons" href="ui-ribbons.html">Ribbons</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-utilities" href="ui-utilities.html">Utilities</a>
</li>
</ul>
</div>
</div>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarAdvanceUI" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarAdvanceUI" role="button">
<i class="ri-stack-line"></i> <span data-key="t-advance-ui">Advance UI</span>
</a>
<div class="collapse menu-dropdown" id="sidebarAdvanceUI">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-sweet-alerts" href="advance-ui-sweetalerts.html">Sweet
                                            Alerts</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-nestable-list" href="advance-ui-nestable.html">Nestable
                                            List</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-scrollbar" href="advance-ui-scrollbar.html">Scrollbar</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-animation" href="advance-ui-animation.html">Animation</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-tour" href="advance-ui-tour.html">Tour</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-swiper-slider" href="advance-ui-swiper.html">Swiper
                                            Slider</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-ratings" href="advance-ui-ratings.html">Ratings</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-highlight" href="advance-ui-highlight.html">Highlight</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-scrollSpy" href="advance-ui-scrollspy.html">ScrollSpy</a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a class="nav-link menu-link" href="widgets.html">
<i class="ri-honour-line"></i> <span data-key="t-widgets">Widgets</span>
</a>
</li>
<li class="nav-item">
<a aria-controls="sidebarForms" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarForms" role="button">
<i class="ri-file-list-3-line"></i> <span data-key="t-forms">Forms</span>
</a>
<div class="collapse menu-dropdown" id="sidebarForms">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-basic-elements" href="forms-elements.html">Basic
                                            Elements</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-form-select" href="forms-select.html"> Form Select </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-checkboxs-radios" href="forms-checkboxs-radios.html">Checkboxs &amp; Radios</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-pickers" href="forms-pickers.html"> Pickers </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-input-masks" href="forms-masks.html">Input Masks</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-advanced" href="forms-advanced.html">Advanced</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-range-slider" href="forms-range-sliders.html"> Range
                                            Slider </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-validation" href="forms-validation.html">Validation</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-wizard" href="forms-wizard.html">Wizard</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-editors" href="forms-editors.html">Editors</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-file-uploads" href="forms-file-uploads.html">File
                                            Uploads</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-form-layouts" href="forms-layouts.html">Form Layouts</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-select2" href="forms-select2.html">Select2</a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarTables" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarTables" role="button">
<i class="ri-layout-grid-line"></i> <span data-key="t-tables">Tables</span>
</a>
<div class="collapse menu-dropdown" id="sidebarTables">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-basic-tables" href="tables-basic.html">Basic Tables</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-grid-js" href="tables-gridjs.html">Grid Js</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-list-js" href="tables-listjs.html">List Js</a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-datatables" href="tables-datatables.html">Datatables</a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarCharts" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarCharts" role="button">
<i class="ri-pie-chart-line"></i> <span data-key="t-charts">Charts</span>
</a>
<div class="collapse menu-dropdown" id="sidebarCharts">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a aria-controls="sidebarApexcharts" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-apexcharts" href="#sidebarApexcharts" role="button">
                                            Apexcharts
                                        </a>
<div class="collapse menu-dropdown" id="sidebarApexcharts">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-line" href="charts-apex-line.html"> Line
                                                    </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-area" href="charts-apex-area.html"> Area
                                                    </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-column" href="charts-apex-column.html">
                                                        Column </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-bar" href="charts-apex-bar.html"> Bar </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-mixed" href="charts-apex-mixed.html"> Mixed
                                                    </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-timeline" href="charts-apex-timeline.html">
                                                        Timeline </a>
</li>
<li class="nav-item">
<a class="nav-link" href="charts-apex-range-area.html"><span data-key="t-range-area">Range Area</span> <span class="badge badge-pill bg-success" data-key="t-new">New</span></a>
</li>
<li class="nav-item">
<a class="nav-link" href="charts-apex-funnel.html"><span data-key="t-funnel">Funnel</span> <span class="badge badge-pill bg-success" data-key="t-new">New</span></a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-candlstick" href="charts-apex-candlestick.html"> Candlstick </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-boxplot" href="charts-apex-boxplot.html">
                                                        Boxplot </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-bubble" href="charts-apex-bubble.html">
                                                        Bubble </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-scatter" href="charts-apex-scatter.html">
                                                        Scatter </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-heatmap" href="charts-apex-heatmap.html">
                                                        Heatmap </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-treemap" href="charts-apex-treemap.html">
                                                        Treemap </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-pie" href="charts-apex-pie.html"> Pie </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-radialbar" href="charts-apex-radialbar.html"> Radialbar </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-radar" href="charts-apex-radar.html"> Radar
                                                    </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-polar-area" href="charts-apex-polar.html">
                                                        Polar Area </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-chartjs" href="charts-chartjs.html"> Chartjs </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-echarts" href="charts-echarts.html"> Echarts </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarIcons" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarIcons" role="button">
<i class="ri-compasses-2-line"></i> <span data-key="t-icons">Icons</span>
</a>
<div class="collapse menu-dropdown" id="sidebarIcons">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" href="icons-remix.html"><span data-key="t-remix">Remix</span> <span class="badge badge-pill bg-info">v3.6</span></a>
</li>
<li class="nav-item">
<a class="nav-link" href="icons-boxicons.html"><span data-key="t-boxicons">Boxicons</span> <span class="badge badge-pill bg-info">v2.1.4</span></a>
</li>
<li class="nav-item">
<a class="nav-link" href="icons-materialdesign.html"><span data-key="t-material-design">Material Design</span> <span class="badge badge-pill bg-info">v7.2.96</span></a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-line-awesome" href="icons-lineawesome.html">Line Awesome</a>
</li>
<li class="nav-item">
<a class="nav-link" href="icons-feather.html"><span data-key="t-feather">Feather</span> <span class="badge badge-pill bg-info">v4.29.1</span></a>
</li>
<li class="nav-item">
<a class="nav-link" href="icons-crypto.html"> <span data-key="t-crypto-svg">Crypto SVG</span></a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarMaps" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarMaps" role="button">
<i class="ri-map-pin-line"></i> <span data-key="t-maps">Maps</span>
</a>
<div class="collapse menu-dropdown" id="sidebarMaps">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-google" href="maps-google.html">
                                            Google
                                        </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-vector" href="maps-vector.html">
                                            Vector
                                        </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-leaflet" href="maps-leaflet.html">
                                            Leaflet
                                        </a>
</li>
</ul>
</div>
</li>
<li class="nav-item">
<a aria-controls="sidebarMultilevel" aria-expanded="false" class="nav-link menu-link" data-bs-toggle="collapse" href="#sidebarMultilevel" role="button">
<i class="ri-share-line"></i> <span data-key="t-multi-level">Multi Level</span>
</a>
<div class="collapse menu-dropdown" id="sidebarMultilevel">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-level-1.1" href="#"> Level 1.1 </a>
</li>
<li class="nav-item">
<a aria-controls="sidebarAccount" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-level-1.2" href="#sidebarAccount" role="button"> Level
                                            1.2
                                        </a>
<div class="collapse menu-dropdown" id="sidebarAccount">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-level-2.1" href="#"> Level 2.1 </a>
</li>
<li class="nav-item">
<a aria-controls="sidebarCrm" aria-expanded="false" class="nav-link" data-bs-toggle="collapse" data-key="t-level-2.2" href="#sidebarCrm" role="button"> Level 2.2
                                                    </a>
<div class="collapse menu-dropdown" id="sidebarCrm">
<ul class="nav nav-sm flex-column">
<li class="nav-item">
<a class="nav-link" data-key="t-level-3.1" href="#"> Level 3.1
                                                                </a>
</li>
<li class="nav-item">
<a class="nav-link" data-key="t-level-3.2" href="#"> Level 3.2
                                                                </a>
</li>
</ul>
</div>
</li>
</ul>
</div>
</li>
</ul>
</div>
</li>
</ul>
</div>
<!-- Sidebar -->
</div>
<div class="sidebar-background') }}"></div>
</div>