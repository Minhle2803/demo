<?php

namespace App\Services\Admin;

use App\Models\CryptoAsset;

class CryptoAssetService
{
    public function store(array $data): CryptoAsset
    {
        return CryptoAsset::create($data);
    }

    public function update(CryptoAsset $asset, array $data): CryptoAsset
    {
        $asset->update($data);

        return $asset->fresh();
    }

    public function destroy(CryptoAsset $asset): void
    {
        $asset->delete();
    }
}
