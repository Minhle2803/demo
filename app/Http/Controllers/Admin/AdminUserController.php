<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\ClientUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function edit(int $id)
    {
        $user = ClientUser::findOrFail($id);

        return view('pages.admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $user = ClientUser::findOrFail($id);

        DB::transaction(function () use ($user, $request) {
            $data = $request->validated();

            if ($data['is_verified'] ?? false) {
                $data['verified_at'] = $user->verified_at ?? now();
            } else {
                $data['verified_at'] = null;
            }

            unset($data['is_verified']);

            $user->update($data);
        });

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', __('admin.user_updated'));
    }

    public function approveKyc(int $id): RedirectResponse
    {
        $user = ClientUser::findOrFail($id);

        $user->forceFill(['kyc_verified_at' => now()])->save();

        return redirect()->back()
            ->with('success', __('admin.kyc_approved'));
    }
}
