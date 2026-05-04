<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCryptoAssetRequest;
use App\Http\Requests\Admin\UpdateCryptoAssetRequest;
use App\Models\CryptoAsset;
use App\Services\Admin\CryptoAssetService;

class AdminCryptoAssetController extends Controller
{
    public function index()
    {
        $assets = CryptoAsset::latest()->get();

        return view('pages.admin.settings.crypto-assets.index', compact('assets'));
    }

    public function store(StoreCryptoAssetRequest $request, CryptoAssetService $service)
    {
        $service->store($request->validated());

        return redirect()->route('admin.crypto-assets.index')
            ->with('success', __('admin.crypto_asset_created'));
    }

    public function update(UpdateCryptoAssetRequest $request, int $id, CryptoAssetService $service)
    {
        $asset = CryptoAsset::findOrFail($id);
        $service->update($asset, $request->validated());

        return redirect()->route('admin.crypto-assets.index')
            ->with('success', __('admin.crypto_asset_updated'));
    }

    public function destroy(int $id, CryptoAssetService $service)
    {
        $asset = CryptoAsset::findOrFail($id);
        $service->destroy($asset);

        return redirect()->route('admin.crypto-assets.index')
            ->with('success', __('admin.crypto_asset_deleted'));
    }
}
