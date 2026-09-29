<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Menampilkan daftar riwayat notifikasi user yang login
    public function index()
    {
        $user = Auth::user();
        
        // Mengambil notifikasi milik user yang sedang login
        $notifications = $user ? $user->notifications : collect();
        $unreadCount = $user ? $user->unreadNotifications->count() : 0;

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    // Menandai satu notifikasi sebagai sudah dibaca
    public function markAsRead($id)
    {
        $user = Auth::user();
        if ($user) {
            $notification = $user->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        }

        return back()->with('success', 'Notifikasi berhasil ditandai sudah dibaca.');
    }

    // Menandai semua notifikasi sebagai sudah dibaca
    public function markAllAsRead()
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}