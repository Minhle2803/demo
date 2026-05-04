@extends('layouts.admin')

@section('title', __('admin.crypto_asset_management'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">{{ __('admin.crypto_asset_management') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.admin') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">{{ __('admin.settings') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('admin.crypto_asset_management') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm-auto ms-auto">
                        <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" data-bs-target="#addAssetModal">
                            <i class="ri-add-line align-bottom me-1"></i> {{ __('admin.add_crypto_asset') }}
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
                                <th>{{ __('admin.symbol') }}</th>
                                <th>{{ __('admin.name') }}</th>
                                <th>{{ __('admin.base_asset') }}</th>
                                <th>{{ __('admin.quote_asset') }}</th>
                                <th>{{ __('admin.price') }}</th>
                                <th>{{ __('admin.status') }}</th>
                                <th>{{ __('admin.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($assets as $asset)
                                <tr>
                                    <td>{{ $asset->id }}</td>
                                    <td><strong>{{ $asset->symbol }}</strong></td>
                                    <td>{{ $asset->name }}</td>
                                    <td>{{ $asset->base_asset }}</td>
                                    <td>{{ $asset->quote_asset }}</td>
                                    <td>{{ $asset->price ? number_format((float) $asset->price, (int) $asset->price_precision) : '-' }}</td>
                                    <td>
                                        @if ($asset->isActive())
                                            <span class="badge bg-success-subtle text-success">{{ __('admin.active') }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">{{ __('admin.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-soft-info"
                                                data-bs-toggle="modal" data-bs-target="#editAssetModal{{ $asset->id }}">
                                                <i class="ri-pencil-line align-bottom"></i>
                                            </button>
                                            <form action="{{ route('admin.crypto-assets.destroy', $asset->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-soft-danger"
                                                    onclick="return confirm('{{ __('admin.confirm_delete_asset') }}')">
                                                    <i class="ri-delete-bin-line align-bottom"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted py-3">{{ __('admin.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Asset Modal --}}
<div class="modal fade" id="addAssetModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.crypto-assets.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('admin.add_crypto_asset') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('admin.symbol') }}</label>
                            <input type="text" class="form-control" name="symbol" required maxlength="20">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('admin.name') }}</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('admin.base_asset') }}</label>
                            <input type="text" class="form-control" name="base_asset" required maxlength="10">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('admin.quote_asset') }}</label>
                            <input type="text" class="form-control" name="quote_asset" required maxlength="10">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('admin.price') }}</label>
                            <input type="number" class="form-control" name="price" step="0.00000001">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('admin.price_precision') }}</label>
                            <input type="number" class="form-control" name="price_precision" value="2" min="0" max="18">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ __('admin.quantity_precision') }}</label>
                            <input type="number" class="form-control" name="quantity_precision" value="8" min="0" max="18">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('admin.min_quantity') }}</label>
                            <input type="number" class="form-control" name="min_quantity" step="0.00000001" value="0.00000001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('admin.min_notional') }}</label>
                            <input type="number" class="form-control" name="min_notional" step="0.01" value="10.00">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">{{ __('admin.status') }}</label>
                            <select class="form-select" name="status">
                                <option value="active">{{ __('admin.active') }}</option>
                                <option value="inactive">{{ __('admin.inactive') }}</option>
                            </select>
                        </div>
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

{{-- Edit Asset Modals --}}
@foreach ($assets as $asset)
    <div class="modal fade" id="editAssetModal{{ $asset->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.crypto-assets.update', $asset->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('admin.edit_crypto_asset') }}: {{ $asset->symbol }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('admin.symbol') }}</label>
                                <input type="text" class="form-control" name="symbol" value="{{ $asset->symbol }}" required maxlength="20">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('admin.name') }}</label>
                                <input type="text" class="form-control" name="name" value="{{ $asset->name }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('admin.base_asset') }}</label>
                                <input type="text" class="form-control" name="base_asset" value="{{ $asset->base_asset }}" required maxlength="10">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('admin.quote_asset') }}</label>
                                <input type="text" class="form-control" name="quote_asset" value="{{ $asset->quote_asset }}" required maxlength="10">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('admin.price') }}</label>
                                <input type="number" class="form-control" name="price" step="0.00000001" value="{{ $asset->price }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('admin.price_precision') }}</label>
                                <input type="number" class="form-control" name="price_precision" value="{{ $asset->price_precision }}" min="0" max="18">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('admin.quantity_precision') }}</label>
                                <input type="number" class="form-control" name="quantity_precision" value="{{ $asset->quantity_precision }}" min="0" max="18">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('admin.min_quantity') }}</label>
                                <input type="number" class="form-control" name="min_quantity" step="0.00000001" value="{{ $asset->min_quantity }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('admin.min_notional') }}</label>
                                <input type="number" class="form-control" name="min_notional" step="0.01" value="{{ $asset->min_notional }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('admin.status') }}</label>
                                <select class="form-select" name="status">
                                    <option value="active" {{ $asset->status === 'active' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
                                    <option value="inactive" {{ $asset->status === 'inactive' ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
                                </select>
                            </div>
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
