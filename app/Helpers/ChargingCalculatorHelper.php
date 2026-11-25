<?php

namespace App\Helpers;

use InvalidArgumentException;

class ChargingCalculatorHelper
{
    /**
     * Menghitung total biaya pengisian daya listrik berdasarkan kWh dan tarif per kWh.
     *
     * @param float $energyKwh Jumlah energi listrik yang dikonsumsi (kWh)
     * @param float $pricePerKwh Tarif per kWh (Rp)
     * @return float Total biaya dalam Rupiah
     * @throws InvalidArgumentException
     */
    public static function hitungBiaya(float $energyKwh, float $pricePerKwh): float
    {
        if ($energyKwh < 0 || $pricePerKwh < 0) {
            throw new InvalidArgumentException('Energi dan tarif per kWh tidak boleh bernilai negatif.');
        }

        return round($energyKwh * $pricePerKwh, 2);
    }

    /**
     * Menghitung estimasi durasi pengisian daya dalam satuan menit.
     *
     * @param float $energyNeededKwh Energi yang dibutuhkan untuk mengisi baterai (kWh)
     * @param float $chargerPowerKw Daya output unit charger (kW)
     * @return int Estimasi durasi dalam menit
     * @throws InvalidArgumentException
     */
    public static function estimasiDurasiMenit(float $energyNeededKwh, float $chargerPowerKw): int
    {
        if ($energyNeededKwh < 0) {
            throw new InvalidArgumentException('Kebutuhan energi tidak boleh negatif.');
        }

        if ($chargerPowerKw <= 0) {
            throw new InvalidArgumentException('Daya charger harus lebih besar dari 0 kW.');
        }

        // Durasi = (Energi / Daya) * 60 menit
        return (int) ceil(($energyNeededKwh / $chargerPowerKw) * 60);
    }

    /**
     * Format kode sesi charging unik: SESI-{ID_STASIUN}-{TIMESTAMP}.
     */
    public static function formatKodeSesi(int|string $stationId, int|string $sessionId): string
    {
        $station = trim((string) $stationId);
        $session = trim((string) $sessionId);

        if ($station === '' || $session === '') {
            throw new InvalidArgumentException('ID Stasiun dan ID Sesi tidak boleh kosong.');
        }

        return "CHG-STA{$station}-S{$session}";
    }
}
