<?php

namespace App\Http\Controllers\Admin;

use App\Events\BalanceUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\ClientUser;
use App\Models\Trade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
        $userBankName = $this->resolveBankName($user->bank_account);
        $trades = [];
        $userId = $user->id;

        $trades = Trade::query()
            ->join('trading_sessions', 'trades.session_id', '=', 'trading_sessions.id')
            ->select([
                'trades.id',
                'trades.user_id',
                'trades.session_id',
                'trades.type',
                'trades.amount',
                'trades.status',
                'trades.payout',
                'trades.created_at',
                'trading_sessions.symbol as session_symbol',
                'trading_sessions.open_price as session_open_price',
                'trading_sessions.close_price as session_close_price',
            ])
            ->where('trades.user_id', $userId)
            ->orderByDesc('trades.session_id')
            ->orderByDesc('trades.id')
            ->paginate(20);

        return view('pages.admin.users.show', compact('user', 'userBankName', 'trades'));
    }

    public function edit(int $id)
    {
        $user = ClientUser::findOrFail($id);

        return view('pages.admin.users.edit', compact('user'))
            ->with('bank_list', config('bank'));
    }

    public function updatePass(Request $request, int $id)
    {
        $user = ClientUser::findOrFail($id);
        // Validate password ngoài transaction
        $request->validate([
            'password' => ['required', Password::min(8), 'confirmed'],
        ]);
        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', __('admin.user_updated'));
    }

    public function blockUser(Request $request, int $id)
    {
        $user = ClientUser::findOrFail($id);
        // Validate password ngoài transaction
        DB::transaction(function () use ($user, $request) {
            $user->update([
                'is_blocked' => true,
            ]);
        });
        return redirect()->route('admin.users.show', $user->id)
            ->with('success', __('admin.user_blocked'));
    }

    public function unBlockUser(Request $request, int $id)
    {
        $user = ClientUser::findOrFail($id);
        // Validate password ngoài transaction
        DB::transaction(function () use ($user, $request) {
            $user->update([
                'is_blocked' => false,
            ]);
        });

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', __('admin.user_unblocked'));
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $user = ClientUser::findOrFail($id);
        $originalBalance = (float) $user->balance;

        DB::transaction(function () use ($user, $request) {
            $data = $request->validated();

            if ($data['is_verified'] ?? false) {
                $data['verified_at'] = $user->verified_at ?? now();
            } else {
                $data['verified_at'] = null;
            }

            unset($data['is_verified']);

            if ($request->hasFile('kyc_front')) {
                $data['kyc_front_url'] = $this->storeKycFile($user, $request->file('kyc_front'), 'front');
            }
            if ($request->hasFile('kyc_back')) {
                $data['kyc_back_url'] = $this->storeKycFile($user, $request->file('kyc_back'), 'back');
            }

            unset($data['kyc_front'], $data['kyc_back']);

            $user->update($data);
        });

        $freshUser = $user->fresh();
        $newBalance = (float) $freshUser->balance;

        if (abs($newBalance - $originalBalance) > 0.001) {
            BalanceUpdated::dispatch(
                $freshUser->id,
                $newBalance,
                'admin_update',
                $newBalance - $originalBalance,
            );
        }

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', __('admin.user_updated'));
    }

    private function storeKycFile(ClientUser $user, UploadedFile $file, string $type): string
    {
        $filename = $user->user_id.'_'.$type.'_'.time().'.'.$file->getClientOriginalExtension();

        return Storage::disk('public')->putFileAs('kyc', $file, $filename);
    }

    public function kyc(Request $request)
    {
        $query = ClientUser::whereNotNull('kyc_front_url')
            ->whereNotNull('kyc_back_url')
            ->where('kyc_front_url', '!=', '')
            ->where('kyc_back_url', '!=', '')
            ->whereNull('kyc_verified_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nickname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('pages.admin.users.kyc', compact('users'));
    }

    public function approveKyc(int $id): RedirectResponse
    {
        $user = ClientUser::findOrFail($id);

        $user->forceFill(['kyc_verified_at' => now(), 'is_verified' => true])->save();

        return redirect()->back()
            ->with('success', __('admin.kyc_approved'));
    }

    private function findBankByCode(?string $code): ?array
    {
        if (! $code) {
            return null;
        }

        foreach (config('bank') as $bank) {
            if (($bank['code'] ?? '') === $code) {
                return $bank;
            }
        }

        return null;
    }

    private function resolveBankName(?string $code): ?string
    {
        $bank = $this->findBankByCode($code);

        return $bank['name'] ?? $code;
    }
}
