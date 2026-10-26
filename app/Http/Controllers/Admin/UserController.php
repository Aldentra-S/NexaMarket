<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of registered marketplace users.
     */
    public function index(Request $request): View
    {
        $roleFilter = $request->query('role');
        $search = $request->query('search');

        $query = User::with('shop')->latest();

        if ($roleFilter && in_array($roleFilter, UserRole::values(), true)) {
            $query->where('role', $roleFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'currentRole' => $roleFilter,
            'search' => $search,
            'totalCount' => User::count(),
            'buyerCount' => User::where('role', UserRole::BUYER->value)->count(),
            'sellerCount' => User::where('role', UserRole::SELLER->value)->count(),
            'adminCount' => User::where('role', UserRole::ADMIN->value)->count(),
        ]);
    }
}
