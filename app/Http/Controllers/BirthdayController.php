<?php

namespace App\Http\Controllers;

use App\Models\BirthdayGreeting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BirthdayController extends Controller
{
    /**
     * List alumni yang berulang tahun hari ini & bulan ini.
     */
    public function index()
    {
        $today = now();
        $month = $today->month;
        $day   = $today->day;

        // Alumni yang ulang tahun HARI INI
        $todayBirthdays = User::where('role', 'alumni')
            ->where('status', 'active')
            ->where('birthday_public', true)
            ->whereNotNull('birthday')
            ->whereMonth('birthday', $month)
            ->whereDay('birthday', $day)
            ->select('id', 'name', 'profile_picture', 'major', 'graduation_year', 'birthday', 'city')
            ->get()
            ->map(function ($u) {
                $u->age = Carbon::parse($u->birthday)->age;
                return $u;
            });

        // Alumni yang ulang tahun BULAN INI (selain hari ini)
        $monthBirthdays = User::where('role', 'alumni')
            ->where('status', 'active')
            ->where('birthday_public', true)
            ->whereNotNull('birthday')
            ->whereMonth('birthday', $month)
            ->whereDay('birthday', '!=', $day)
            ->orderByRaw('DAY(birthday) ASC')
            ->select('id', 'name', 'profile_picture', 'major', 'graduation_year', 'birthday', 'city')
            ->get()
            ->map(function ($u) use ($day) {
                $u->days_until = Carbon::parse($u->birthday)->setYear(now()->year)->dayOfYear - now()->dayOfYear;
                if ($u->days_until < 0) $u->days_until += 365;
                return $u;
            });

        // Greetings yang sudah saya kirim hari ini
        $sentToday = auth()->check()
            ? BirthdayGreeting::where('from_user_id', auth()->id())
                ->whereDate('created_at', today())
                ->pluck('to_user_id')
            : collect();

        return view('birthday.index', compact('todayBirthdays', 'monthBirthdays', 'sentToday', 'month'));
    }

    /**
     * Kirim ucapan ulang tahun.
     */
    public function greet(Request $request, User $user)
    {
        $request->validate([
            'message' => 'nullable|string|max:300',
            'emoji'   => 'nullable|string|max:10',
        ]);

        // Check if already greeted today
        $alreadyGreeted = BirthdayGreeting::where('from_user_id', auth()->id())
            ->where('to_user_id', $user->id)
            ->whereDate('created_at', today())
            ->exists();

        if ($alreadyGreeted) {
            return response()->json(['error' => 'Kamu sudah mengucapkan selamat hari ini!'], 422);
        }

        // Check recipient really has birthday today
        $isBirthdayToday = $user->birthday
            && Carbon::parse($user->birthday)->month === now()->month
            && Carbon::parse($user->birthday)->day   === now()->day;

        abort_if(!$isBirthdayToday, 422, 'Hari ini bukan ulang tahun alumni tersebut.');

        BirthdayGreeting::create([
            'from_user_id' => auth()->id(),
            'to_user_id'   => $user->id,
            'message'      => $request->message ?? '🎉 Selamat Ulang Tahun! Semoga sukses selalu, Alumni Steman!',
            'emoji'        => $request->emoji ?? '🎂',
        ]);

        // Award XP for spreading positivity
        auth()->user()->increment('points', 1);

        $totalGreetings = BirthdayGreeting::where('to_user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        return response()->json([
            'success'        => true,
            'total_greetings'=> $totalGreetings,
            'message'        => 'Ucapan terkirim! 🎉',
        ]);
    }

    /**
     * My greetings inbox — ucapan yang saya terima.
     */
    public function myGreetings()
    {
        $greetings = BirthdayGreeting::where('to_user_id', auth()->id())
            ->with('sender:id,name,profile_picture,major')
            ->latest()
            ->paginate(20);

        return view('birthday.my_greetings', compact('greetings'));
    }

    /**
     * API: berapa hari lagi ulang tahun saya?
     */
    public function countdown()
    {
        $user = auth()->user();
        if (!$user->birthday) {
            return response()->json(['has_birthday' => false]);
        }

        $birthday = Carbon::parse($user->birthday);
        $next     = $birthday->setYear(now()->year);
        if ($next->isPast()) $next->addYear();

        $daysUntil = now()->diffInDays($next);

        return response()->json([
            'has_birthday'   => true,
            'days_until'     => $daysUntil,
            'is_today'       => $daysUntil === 0,
            'birthday_date'  => $next->format('d M'),
        ]);
    }
}
