<?php

namespace App\Http\Controllers;

use App\Models\Merchandise;
use App\Models\MerchandiseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MerchandiseController extends Controller
{
    public function index(Request $request)
    {
        $query = Merchandise::where('is_active', true)->orderBy('sort_order')->orderBy('name');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('type')) {
            if ($request->type === 'pre_order') {
                $query->where('is_pre_order', true);
            } elseif ($request->type === 'ready') {
                $query->where('is_pre_order', false);
            }
        }

        $merchandise = $query->get();
        $categories  = Merchandise::getCategories();

        return view('merchandise.index', compact('merchandise', 'categories'));
    }

    public function show(string $slug)
    {
        $item       = Merchandise::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $categories = Merchandise::getCategories();
        return view('merchandise.show', compact('item', 'categories'));
    }

    public function order(Request $request, string $slug)
    {
        $item = Merchandise::where('slug', $slug)->where('is_active', true)->firstOrFail();

        if (! $item->isOrderable()) {
            return back()->with('error', 'Produk ini sedang tidak tersedia untuk dipesan.');
        }

        $validated = $request->validate([
            'buyer_name'    => 'required|string|max:255',
            'buyer_phone'   => 'required|string|max:20',
            'buyer_email'   => 'nullable|email|max:255',
            'buyer_address' => 'required|string',
            'size'          => 'nullable|string|max:20',
            'color'         => 'nullable|string|max:50',
            'quantity'      => 'required|integer|min:' . $item->min_order,
            'custom_note'   => 'nullable|string|max:500',
        ]);

        // Member price only for verified alumni (approved + role alumni)
        $isVerifiedAlumni = Auth::check()
            && Auth::user()->status === 'approved'
            && Auth::user()->role === 'alumni';
        $unitPrice  = ($isVerifiedAlumni && $item->price_member) ? $item->price_member : $item->price;
        $totalPrice = $unitPrice * $validated['quantity'];

        $order = MerchandiseOrder::create([
            ...$validated,
            'merchandise_id' => $item->id,
            'user_id'        => Auth::id(),
            'order_code'     => MerchandiseOrder::generateOrderCode(),
            'unit_price'     => $unitPrice,
            'total_price'    => $totalPrice,
            'status'         => 'pending',
        ]);

        // Store order code in session so only the submitter can view the success page
        $request->session()->put('merch_order_' . $order->order_code, true);

        return redirect()->route('merchandise.order.success', $order->order_code)
            ->with('success', 'Pre-order berhasil dikirim! Kode pesanan Anda: ' . $order->order_code);
    }

    public function orderSuccess(Request $request, string $code)
    {
        // Only the user who placed the order (or an admin) can view success page
        if (! $request->session()->has('merch_order_' . $code)) {
            return redirect()->route('merchandise.index')
                ->with('info', 'Halaman konfirmasi hanya tersedia segera setelah pemesanan.');
        }

        $order = MerchandiseOrder::where('order_code', $code)
            ->with('merchandise')
            ->firstOrFail();

        return view('merchandise.order-success', compact('order'));
    }
}
