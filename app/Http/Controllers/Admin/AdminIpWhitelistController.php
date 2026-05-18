<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IpCountryWhitelist;
use Illuminate\Http\Request;

class AdminIpWhitelistController extends Controller
{
    public function index()
    {
        $countries = IpCountryWhitelist::latest()->get();
        $allCountries = config('countries');
        $whitelistedCodes = $countries->pluck('country_code')->all();
        $availableCountries = array_diff_key($allCountries, array_flip($whitelistedCodes));

        return view('pages.admin.ip-whitelist.index', compact(
            'countries',
            'allCountries',
            'availableCountries'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_code' => ['required', 'string', 'size:2', 'unique:ip_country_whitelist,country_code', 'in:'.implode(',', array_keys(config('countries')))],
        ]);

        $countryName = config("countries.{$validated['country_code']}");

        IpCountryWhitelist::create([
            'country_code' => $validated['country_code'],
            'country_name' => $countryName,
        ]);

        return redirect()->route('admin.ip-whitelist.index')
            ->with('success', __('admin.ip_whitelist_created'));
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'country_code' => ['required', 'string', 'size:2', 'unique:ip_country_whitelist,country_code,'.$id, 'in:'.implode(',', array_keys(config('countries')))],
        ]);

        $countryName = config("countries.{$validated['country_code']}");

        $country = IpCountryWhitelist::findOrFail($id);
        $country->update([
            'country_code' => $validated['country_code'],
            'country_name' => $countryName,
        ]);

        return redirect()->route('admin.ip-whitelist.index')
            ->with('success', __('admin.ip_whitelist_updated'));
    }

    public function destroy(int $id)
    {
        $country = IpCountryWhitelist::findOrFail($id);
        $country->delete();

        return redirect()->route('admin.ip-whitelist.index')
            ->with('success', __('admin.ip_whitelist_deleted'));
    }
}
