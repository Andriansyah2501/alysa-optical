<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PromoEvent;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PromoCalendarController extends Controller
{
    // 1. Tampilan Kalender Promo Publik & Toko
    public function publicIndex(Request $request)
    {
        $year = (int) $request->query('year', Carbon::now()->year);
        $month = (int) $request->query('month', Carbon::now()->month);

        $currentDate = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $currentDate->daysInMonth;
        $startDayOfWeek = $currentDate->copy()->startOfMonth()->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.

        // Ambil event promo aktif di bulan tersebut
        $events = PromoEvent::where('is_active', true)
            ->where(function ($q) use ($currentDate) {
                $start = $currentDate->copy()->startOfMonth();
                $end = $currentDate->copy()->endOfMonth();
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end])
                  ->orWhere(function ($sub) use ($start, $end) {
                      $sub->where('start_date', '<=', $start)
                          ->where('end_date', '>=', $end);
                  });
            })
            ->get();

        $allUpcomingEvents = PromoEvent::where('is_active', true)
            ->where('end_date', '>=', Carbon::today())
            ->orderBy('start_date')
            ->get();

        return view('shop.calendar', compact('currentDate', 'daysInMonth', 'startDayOfWeek', 'events', 'allUpcomingEvents', 'year', 'month'));
    }

    // 2. Dasbor Kalender Admin & Manajemen Event
    public function adminIndex()
    {
        $events = PromoEvent::orderBy('start_date', 'desc')->get();
        $vouchers = Voucher::where('is_active', true)->get();

        return view('admin.calendar.index', compact('events', 'vouchers'));
    }

    // 3. Store Promo Event Baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'badge' => 'required|string|max:50',
            'description' => 'nullable|string',
            'discount_info' => 'nullable|string|max:100',
            'voucher_code' => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'color_theme' => 'required|in:rose,amber,emerald,sky,purple',
            'target_audience' => 'nullable|string|max:100',
        ]);

        PromoEvent::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(4),
            'badge' => strtoupper($validated['badge']),
            'description' => $validated['description'],
            'discount_info' => $validated['discount_info'],
            'voucher_code' => $validated['voucher_code'] ? strtoupper($validated['voucher_code']) : null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'color_theme' => $validated['color_theme'],
            'target_audience' => ($validated['target_audience'] ?? 'Semua Pelanggan'),
            'is_active' => true,
        ]);

        return back()->with('success', "Event promo '{$validated['title']}' berhasil ditambahkan ke kalender.");
    }

    // 4. Update Promo Event
    public function update(Request $request, PromoEvent $promo)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'badge' => 'required|string|max:50',
            'description' => 'nullable|string',
            'discount_info' => 'nullable|string|max:100',
            'voucher_code' => 'nullable|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'color_theme' => 'required|in:rose,amber,emerald,sky,purple',
            'target_audience' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $promo->update([
            'title' => $validated['title'],
            'badge' => strtoupper($validated['badge']),
            'description' => $validated['description'],
            'discount_info' => $validated['discount_info'],
            'voucher_code' => $validated['voucher_code'] ? strtoupper($validated['voucher_code']) : null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'color_theme' => $validated['color_theme'],
            'target_audience' => ($validated['target_audience'] ?? 'Semua Pelanggan'),
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', "Event promo '{$promo->title}' berhasil diperbarui.");
    }

    // 5. Delete Promo Event
    public function destroy(PromoEvent $promo)
    {
        $title = $promo->title;
        $promo->delete();

        return back()->with('success', "Event promo '{$title}' telah dihapus dari kalender.");
    }
}
