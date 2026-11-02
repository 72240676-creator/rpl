<?php

namespace App\Http\Controllers;

use App\Models\Charger;
use App\Models\ChargingSession;
use App\Models\Location;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    private const LOW_BALANCE_THRESHOLD = 25000;

    public function index()
    {
        $user = Auth::user();

        if ($user->peran === 'admin') {
            $notifications = collect();

            $sessions = ChargingSession::with(['charger.location', 'user'])
                ->latest('updated_at')
                ->limit(10)
                ->get();

            foreach ($sessions as $session) {
                $status = match ($session->status) {
                    'ongoing' => 'sedang berlangsung',
                    'completed' => 'selesai dan menunggu pembayaran',
                    'paid' => 'selesai dan sudah dibayar',
                    default => Str::lower($session->status),
                };
                $chargerName = $session->charger?->device_number ?? 'Charger tidak diketahui';
                $locationName = $session->charger?->location?->nama_lokasi ?? 'Lokasi tidak diketahui';
                $userName = $session->user?->nama ?? 'Pengguna';

                $notifications->push($this->makeAdminNotification(
                    'Sesi charging #' . $session->id . ' ' . $status,
                    "{$userName} · {$chargerName} · {$locationName}",
                    $session->updated_at ?? $session->start_time,
                ));
            }

            Charger::with('location')
                ->where(function ($query) {
                    $query->where('is_online', false)
                        ->orWhere('status', 'rusak');
                })
                ->latest('updated_at')
                ->get()
                ->each(function (Charger $charger) use ($notifications) {
                    $status = $charger->status === 'rusak'
                        ? 'rusak'
                        : 'offline';
                    $locationName = $charger->location?->nama_lokasi ?? 'Lokasi tidak diketahui';

                    $notifications->push($this->makeAdminNotification(
                        "Charger {$charger->device_number} perlu diperiksa",
                        "Status {$status} di {$locationName}. Silakan koordinasikan pemeriksaan dengan operator.",
                        $charger->updated_at,
                    ));
                });

            Location::where('status', '!=', 'aktif')
                ->latest('updated_at')
                ->get()
                ->each(function (Location $location) use ($notifications) {
                    $notifications->push($this->makeAdminNotification(
                        "Status lokasi {$location->nama_lokasi} berubah",
                        "Status operasional saat ini: " . Str::lower(str_replace('_', ' ', $location->status)) . '.',
                        $location->updated_at,
                    ));
                });

            $notifications = $notifications
                ->sortByDesc(fn ($notification) => $notification->created_at?->timestamp ?? 0)
                ->values();
            $unreadCount = 0;
            $isAdmin = true;
        } else {
            $notifications = Notification::where('user_id', $user->id_user)
                ->latest()
                ->get()
                ->map(fn (Notification $notification) => (object) [
                    'id' => $notification->id,
                    'data' => [
                        'title' => $notification->title,
                        'message' => $notification->message,
                    ],
                    'read_at' => $notification->is_read ? $notification->updated_at : null,
                    'created_at' => $notification->created_at,
                ]);

            if ($user->peran === 'pengemudi') {
                $hasOngoingSession = ChargingSession::where('user_id', $user->id_user)
                    ->where('status', 'ongoing')
                    ->exists();
                $hasChargingHistory = ChargingSession::where('user_id', $user->id_user)
                    ->exists();
                $balance = (float) ($user->saldo ?? 0);

                if (!$hasOngoingSession && $balance < self::LOW_BALANCE_THRESHOLD) {
                    $balanceText = 'Rp ' . number_format($balance, 0, ',', '.');
                    $message = $balance <= 0
                        ? 'Saldo Anda saat ini Rp 0. Isi saldo sebelum memulai sesi charging agar transaksi berjalan lancar.'
                        : "Saldo Anda saat ini {$balanceText}. Isi saldo sebelum charging berikutnya agar pembayaran berjalan lancar.";

                    $notifications->push($this->makeUserRecommendation(
                        $balance <= 0 ? 'Isi saldo sebelum mulai charging' : 'Saldo Anda mulai menipis',
                        $message,
                        'low_balance',
                        route('topup.index'),
                        'Isi saldo sekarang',
                    ));
                }

                if (!$hasChargingHistory && !$hasOngoingSession && $balance >= 25000) {
                    $notifications->push($this->makeUserRecommendation(
                        'Temukan SPKLU di sekitar Anda',
                        'Jelajahi lokasi dan ketersediaan stasiun charging sebelum memulai perjalanan.',
                        'stations',
                        route('locations.index'),
                        'Lihat lokasi SPKLU',
                    ));
                }

                if ((int) ($user->points ?? 0) > 0) {
                    $notifications->push($this->makeUserRecommendation(
                        'Poin Anda bisa ditukar',
                        'Anda memiliki ' . number_format((int) $user->points, 0, ',', '.') . ' poin. Tukarkan poin untuk mendapatkan saldo tambahan.',
                        'points',
                        route('points.index'),
                        'Tukar poin',
                    ));
                }

                $notifications = $notifications
                    ->sortByDesc(fn ($notification) => $notification->created_at?->timestamp ?? 0)
                    ->values();
            }

            $unreadCount = Notification::where('user_id', $user->id_user)
                ->where('is_read', false)
                ->count();
            $isAdmin = false;
        }

        return view('notifications.index', compact('notifications', 'unreadCount', 'isAdmin'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::user()->id_user)->findOrFail($id);
        $notification->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Pemberitahuan ditandai telah dibaca.');
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::user()->id_user)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Semua pemberitahuan ditandai telah dibaca.');
    }

    private function makeAdminNotification(string $title, string $message, $createdAt): object
    {
        return (object) [
            'id' => null,
            'data' => [
                'title' => $title,
                'message' => $message,
            ],
            'read_at' => $createdAt,
            'created_at' => $createdAt,
        ];
    }

    private function makeUserRecommendation(
        string $title,
        string $message,
        string $type,
        string $url,
        string $actionLabel,
    ): object {
        $createdAt = now();

        return (object) [
            'id' => null,
            'data' => [
                'title' => $title,
                'message' => $message,
                'recommendation_type' => $type,
                'action_url' => $url,
                'action_label' => $actionLabel,
            ],
            'read_at' => $createdAt,
            'created_at' => $createdAt,
        ];
    }
}