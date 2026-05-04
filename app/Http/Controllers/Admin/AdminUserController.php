<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientUser;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientUser::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nickname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kyc')) {
            if ($request->input('kyc') === 'verified') {
                $query->whereNotNull('kyc_front_url')
                    ->whereNotNull('kyc_back_url')
                    ->where('kyc_front_url', '!=', '')
                    ->where('kyc_back_url', '!=', '');
            } elseif ($request->input('kyc') === 'unverified') {
                $query->where(function ($q) {
                    $q->whereNull('kyc_front_url')
                        ->orWhereNull('kyc_back_url')
                        ->orWhere('kyc_front_url', '')
                        ->orWhere('kyc_back_url', '');
                });
            }
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('pages.admin.users.index', compact('users'));
    }

    public function show(int $id)
    {
        $user = ClientUser::findOrFail($id);

        return view('pages.admin.users.show', compact('user'));
    }
}
