<?php

namespace App\Http\Controllers;

use App\Models\Charger;
use App\Models\ChargingSession;
use App\Models\Location;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OperatorDashboardController extends Controller
{
    public function index(): View
    {
        $chargers = Charger::with(['location', 'inspectionReporter'])
            ->orderBy('id_location')
            ->orderBy('device_number')
            ->get();

        $recentSessions = ChargingSession::with(['user', 'charger.location'])
            ->latest('start_time')
            ->limit(6)
            ->get();

        $stats = $this->stats();

        return view('operator.dashboard', compact('chargers', 'recentSessions', 'stats'));
    }

    public function live(): JsonResponse
    {
        return response()->json([
            'stats' => $this->stats(),
            'inspection_reports' => Charger::with('inspectionReporter')
                ->get(['id_charger', 'needs_inspection', 'inspection_note', 'inspection_reported_at', 'inspection_reported_by'])
                ->map(fn (Charger $charger) => [
                    'id' => $charger->id_charger,
                    'needs_inspection' => $charger->needs_inspection,
                    'note' => $charger->inspection_note,
                    'reported_at' => $charger->inspection_reported_at?->timezone('Asia/Jakarta')->diffForHumans(),
                    'reporter' => $charger->inspectionReporter?->nama,
                ]),
            'updated_at' => now()->toIso8601String(),
        ]);
    }

    public function updateInspection(Request $request, int $id_charger)
    {
        $validated = $request->validate([
            'needs_inspection' => ['required', 'boolean'],
            'inspection_note' => ['nullable', 'required_if:needs_inspection,1', 'string', 'max:1000'],
        ], [
            'inspection_note.required_if' => 'Catatan pemeriksaan wajib diisi saat charger ditandai perlu diperiksa.',
        ]);

        $charger = Charger::findOrFail($id_charger);
        $needsInspection = (bool) $validated['needs_inspection'];

        $charger->update([
            'needs_inspection' => $needsInspection,
            'inspection_note' => $needsInspection ? $validated['inspection_note'] : null,
            'inspection_reported_at' => $needsInspection ? now() : null,
            'inspection_reported_by' => $needsInspection ? $request->user()->id_user : null,
        ]);

        return back()->with(
            'success',
            $needsInspection
                ? "Charger {$charger->device_number} ditandai perlu pemeriksaan."
                : "Pemeriksaan charger {$charger->device_number} telah diselesaikan."
        );
    }

    private function stats(): array
    {
        return [
            'locations' => Location::count(),
            'active_locations' => Location::where('status', 'aktif')->count(),
            'chargers' => Charger::count(),
            'available_chargers' => Charger::where('status', 'tersedia')
                ->where('is_online', true)
                ->count(),
            'ongoing_sessions' => ChargingSession::where('status', 'ongoing')->count(),
            'attention_chargers' => Charger::where('needs_inspection', true)->count(),
            'sessions_today' => ChargingSession::whereDate('start_time', today())->count(),
            'energy_today' => (float) ChargingSession::whereDate('start_time', today())
                ->sum('energy_consumed_kwh'),
        ];
    }
}
