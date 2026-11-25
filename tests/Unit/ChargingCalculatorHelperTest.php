<?php

namespace Tests\Unit;

use App\Helpers\ChargingCalculatorHelper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ChargingCalculatorHelperTest extends TestCase
{
    /**
     * Menguji perhitungan biaya energi listrik secara akurat.
     */
    public function test_hitung_biaya_menghasilkan_nilai_yang_benar(): void
    {
        // 10.5 kWh dengan tarif Rp 2.500 / kWh = Rp 26.250
        $biaya = ChargingCalculatorHelper::hitungBiaya(10.5, 2500);
        $this->assertEquals(26250.0, $biaya);
    }

    /**
     * Menguji bahwa biaya bernilai 0 jika energi 0 kWh.
     */
    public function test_hitung_biaya_nol_jika_energi_nol(): void
    {
        $biaya = ChargingCalculatorHelper::hitungBiaya(0, 2500);
        $this->assertEquals(0.0, $biaya);
    }

    /**
     * Menguji bahwa input negatif melempar InvalidArgumentException.
     */
    public function test_hitung_biaya_melempar_exception_jika_nilai_negatif(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChargingCalculatorHelper::hitungBiaya(-5.0, 2500);
    }

    /**
     * Menguji estimasi durasi pengisian daya dalam menit.
     */
    public function test_estimasi_durasi_menit_menghitung_dengan_benar(): void
    {
        // Kebutuhan 25 kWh di charger 50 kW (DC Fast) -> 0.5 jam = 30 menit
        $durasi = ChargingCalculatorHelper::estimasiDurasiMenit(25.0, 50.0);
        $this->assertSame(30, $durasi);

        // Kebutuhan 11 kWh di charger 22 kW (AC Type 2) -> 0.5 jam = 30 menit
        $durasiAc = ChargingCalculatorHelper::estimasiDurasiMenit(11.0, 22.0);
        $this->assertSame(30, $durasiAc);
    }

    /**
     * Menguji bahwa daya charger nol atau negatif melempar exception.
     */
    public function test_estimasi_durasi_melempar_exception_jika_daya_charger_nol(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChargingCalculatorHelper::estimasiDurasiMenit(20.0, 0);
    }

    /**
     * Menguji pembuatan format kode unik sesi charging.
     */
    public function test_format_kode_sesi_mengembalikan_format_yang_benar(): void
    {
        $kode = ChargingCalculatorHelper::formatKodeSesi(1, 105);
        $this->assertSame('CHG-STA1-S105', $kode);
    }

    /**
     * Menguji format kode sesi melempar exception jika parameter kosong.
     */
    public function test_format_kode_sesi_melempar_exception_jika_kosong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ChargingCalculatorHelper::formatKodeSesi('', '105');
    }
}
