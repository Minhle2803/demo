@extends('layouts.admin')

@section('title', __('admin.ip_whitelist'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ __('admin.ip_whitelist') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('admin.ip_whitelist') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0">{{ __('admin.ip_whitelist_countries') }}</h5>
                    </div>
                    <div class="col-sm-auto ms-auto">
                        <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                            <i class="ri-add-line align-bottom me-1"></i> {{ __('admin.ip_whitelist_add') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive table-card">
                    <table class="table align-middle table-nowrap">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>#</th>
                                <th>{{ __('admin.ip_whitelist_country_code') }}</th>
                                <th>{{ __('admin.ip_whitelist_country_name') }}</th>
                                <th>{{ __('admin.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($countries as $country)
                                <tr>
                                    <td>{{ $country->id }}</td>
                                    <td><span class="badge bg-primary-subtle text-primary">{{ $country->country_code }}</span></td>
                                    <td>{{ $country->country_name }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-soft-info"
                                                data-bs-toggle="modal" data-bs-target="#editCountryModal{{ $country->id }}">
                                                <i class="ri-pencil-line align-bottom"></i>
                                            </button>
                                            <form action="{{ route('admin.ip-whitelist.destroy', $country->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-soft-danger"
                                                    onclick="return confirm('{{ __('admin.ip_whitelist_confirm_delete') }}')">
                                                    <i class="ri-delete-bin-line align-bottom"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('admin.ip_whitelist_info') }}</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">{{ __('admin.ip_whitelist_info_desc') }}</p>
                <ul class="list-unstyled text-muted mb-0">
                    <li class="mb-2"><i class="ri-checkbox-circle-line text-success me-1"></i> {{ __('admin.ip_whitelist_info_format') }}</li>
                    <li class="mb-2"><i class="ri-checkbox-circle-line text-success me-1"></i> {{ __('admin.ip_whitelist_info_admin') }}</li>
                    <li><i class="ri-checkbox-circle-line text-success me-1"></i> {{ __('admin.ip_whitelist_info_redirect') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Add Country Modal --}}
<div class="modal fade" id="addCountryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.ip-whitelist.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.ip_whitelist_add') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.ip_whitelist_select_country') }}</label>
                        <select class="form-select" name="country_code" required>
                            <option value="">{{ __('admin.ip_whitelist_select_country_placeholder') }}</option>
                            @foreach ($availableCountries as $code => $name)
                                <option value="{{ $code }}">{{ $name }} ({{ $code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Country Modals --}}
@foreach ($countries as $country)
    <div class="modal fade" id="editCountryModal{{ $country->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.ip-whitelist.update', $country->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('admin.ip_whitelist_edit') }}: {{ $country->country_code }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.ip_whitelist_select_country') }}</label>
                            <select class="form-select" name="country_code" required>
                                <option value="">{{ __('admin.ip_whitelist_select_country_placeholder') }}</option>
                                @foreach ($allCountries as $code => $name)
                                    <option value="{{ $code }}" {{ $country->country_code === $code ? 'selected' : '' }}>{{ $name }} ({{ $code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection
