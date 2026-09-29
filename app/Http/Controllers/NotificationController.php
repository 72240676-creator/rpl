<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    // Menampilkan daftar notifikasi user yang login
    public function index()
    {
        $userId = Auth::id();

        $notifications = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    // Menandai satu notifikasi sebagai sudah dibaca
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->update([
            'is_read' => true
        ]);

        return redirect()->back()
            ->with('success', 'Pemberitahuan ditandai telah dibaca.');
    }

    // Menandai semua notifikasi sebagai sudah dibaca
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true
            ]);

        return redirect()->back()
            ->with('success', 'Semua pemberitahuan ditandai telah dibaca.');
    }
}