<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Menampilkan daftar riwayat notifikasi user
    public function index()
    {
        $userId = Auth::user()->id_user ?? 1;
        $notifications = Notification::where('user_id', $userId)->latest()->get();
        $unreadCount = $notifications->where('is_read', false)->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    // Menandai satu notifikasi sebagai sudah dibaca
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notifikasi berhasil ditandai sudah dibaca.');
    }

    // Menandai semua notifikasi sebagai sudah dibaca
    public function markAllAsRead()
    {
        $userId = Auth::id() ?? 1;
        Notification::where('user_id', $userId)->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi berhasil ditandai sudah dibaca.');
    }

    // Fungsi pembantu (helper) statis agar modul lain bisa memicu notifikasi otomatis
    public static function send($userId, $title, $message)
    {
        return Notification::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }
}
