<?php

namespace App\Http\Controllers;

use App\Models\Charger;
use App\Models\ChargingSession;
use App\Models\Location;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalChargers = Charger::count();
        $onlineChargers = Charger::where('is_online', true)
            ->where('status', '!=', 'rusak')
            ->count();
        $offlineChargers = Charger::where('is_online', false)
            ->where('status', '!=', 'rusak')
            ->count();
        $maintenanceChargers = Charger::where('status', 'rusak')->count();
        $attentionChargers = Charger::where(function ($query) {
            $query->where('needs_inspection', true)
                ->orWhere('status', 'rusak')
                ->orWhere('is_online', false);
        })->count();

        $chargerStatuses = [
            [
                'label' => 'Online',
                'count' => $onlineChargers,
                'color' => 'green',
            ],
            [
                'label' => 'Offline',
                'count' => $offlineChargers,
                'color' => 'gray',
            ],
            [
                'label' => 'Perlu perbaikan',
                'count' => $maintenanceChargers,
                'color' => 'orange',
            ],
        ];

        foreach ($chargerStatuses as &$status) {
            $status['percentage'] = $totalChargers > 0
                ? (int) round(($status['count'] / $totalChargers) * 100)
                : 0;
        }
        unset($status);

        $stats = [
            'locations' => Location::count(),
            'chargers' => $totalChargers,
            'online_chargers' => $onlineChargers,
            'attention_chargers' => $attentionChargers,
        ];

        $inspectionReports = Charger::with(['location', 'inspectionReporter'])
            ->where('needs_inspection', true)
            ->latest('inspection_reported_at')
            ->get();

        $recentSessions = ChargingSession::with(['user', 'charger.location'])
            ->latest('start_time')
            ->limit(3)
            ->get();

        return view('admin.stations.dashboard', compact(
            'stats',
            'chargerStatuses',
            'recentSessions',
            'inspectionReports',
        ));
    }

    public function updateInspection(Request $request, int $id_charger)
    {
        $validated = $request->validate([
            'needs_inspection' => ['required', 'boolean'],
            'inspection_note' => ['nullable', 'required_if:needs_inspection,1', 'string', 'max:1000'],
        ], [
            'inspection_note.required_if' => 'Catatan pemeriksaan wajib diisi saat laporan masih perlu ditindaklanjuti.',
        ]);

        $charger = Charger::findOrFail($id_charger);
        $needsInspection = (bool) $validated['needs_inspection'];

        $charger->update([
            'needs_inspection' => $needsInspection,
            'inspection_note' => $needsInspection ? $validated['inspection_note'] : null,
            'inspection_reported_at' => $needsInspection ? now() : null,
            'inspection_reported_by' => $needsInspection
                ? ($charger->inspection_reported_by ?? $request->user()->id_user)
                : null,
        ]);

        return back()->with(
            'success',
            $needsInspection
                ? "Laporan {$charger->device_number} berhasil diperbarui."
                : "Laporan {$charger->device_number} diselesaikan."
        );
    }
}
