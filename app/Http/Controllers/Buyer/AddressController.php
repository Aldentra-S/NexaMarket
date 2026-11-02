<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Buyer\StoreAddressRequest;
use App\Http\Requests\Buyer\UpdateAddressRequest;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AddressController extends Controller
{
    /**
     * Display a listing of the authenticated buyer's saved addresses.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $addresses = $user->addresses()
            ->orderByDesc('is_default')
            ->latest('id')
            ->get();

        return view('buyer.addresses.index', [
            'addresses' => $addresses,
        ]);
    }

    /**
     * Show the form for creating a new address.
     */
    public function create(): View
    {
        return view('buyer.addresses.create');
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $hasAddresses = $user->addresses()->exists();
        $isDefault = ! empty($data['is_default']) || ! $hasAddresses;

        $address = DB::transaction(function () use ($user, $data, $isDefault) {
            $data['user_id'] = $user->id;
            $data['is_default'] = false; // set by makeDefault if needed

            $newAddress = Address::create($data);

            if ($isDefault) {
                $newAddress->makeDefault();
            }

            return $newAddress;
        });

        // If returned from checkout, redirect back to checkout
        if ($request->has('redirect_to_checkout')) {
            return redirect()->route('checkout.create', ['address_id' => $address->id])
                ->with('success', 'Alamat pengiriman baru berhasil disimpan dan dipilih.');
        }

        return redirect()->route('buyer.addresses.index')
            ->with('success', 'Alamat pengiriman baru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified address.
     */
    public function edit(Address $address): View
    {
        Gate::authorize('update', $address);

        return view('buyer.addresses.edit', [
            'address' => $address,
        ]);
    }

    /**
     * Update the specified address in storage.
     */
    public function update(UpdateAddressRequest $request, Address $address): RedirectResponse
    {
        Gate::authorize('update', $address);

        $data = $request->validated();
        $isDefault = ! empty($data['is_default']);

        DB::transaction(function () use ($address, $data, $isDefault) {
            $address->update($data);

            if ($isDefault) {
                $address->makeDefault();
            }
        });

        return redirect()->route('buyer.addresses.index')
            ->with('success', 'Alamat pengiriman berhasil diperbarui.');
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Address $address): RedirectResponse
    {
        Gate::authorize('delete', $address);

        $user = $address->user;
        $wasDefault = $address->is_default;

        DB::transaction(function () use ($address, $user, $wasDefault) {
            $address->delete();

            // If the deleted address was the default, elect the next available address as default
            if ($wasDefault) {
                $nextAddress = $user->addresses()->first();
                if ($nextAddress) {
                    $nextAddress->makeDefault();
                }
            }
        });

        return redirect()->route('buyer.addresses.index')
            ->with('success', 'Alamat pengiriman berhasil dihapus.');
    }

    /**
     * Set the specified address as the buyer's default shipping destination.
     */
    public function setDefault(Address $address): RedirectResponse
    {
        Gate::authorize('update', $address);

        $address->makeDefault();

        return back(fallback: route('buyer.addresses.index'))
            ->with('success', "Alamat '{$address->label}' telah dijadikan sebagai alamat utama.");
    }
}
