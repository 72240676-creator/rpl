<?php

namespace Tests\Unit;

use App\Helpers\MateriHelper;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MateriModelTest extends TestCase
{
    /**
     * Menguji bahwa format kode pertanyaan mengembalikan format yang benar.
     */
    public function test_format_kode_pertanyaan_mengembalikan_format_yang_benar(): void
    {
        $result = MateriHelper::formatKodePertanyaan(1, 2);
        $this->assertSame('UNIT1_2', $result);
    }

    /**
     * Menguji bahwa format kode pertanyaan menghapus spasi berlebih pada input.
     */
    public function test_format_kode_pertanyaan_menghapus_spasi_berlebih(): void
    {
        $result = MateriHelper::formatKodePertanyaan('  3  ', '  5  ');
        $this->assertSame('UNIT3_5', $result);
    }

    /**
     * Menguji bahwa format kode pertanyaan melempar exception jika parameter kosong.
     */
    public function test_format_kode_pertanyaan_melempar_exception_jika_kosong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        MateriHelper::formatKodePertanyaan('', '1');
    }

    /**
     * Menguji bahwa format nama PDF exercise sesuai pola yang diharapkan.
     */
    public function test_format_nama_pdf_exercise_sesuai_pola(): void
    {
        $result = MateriHelper::formatNamaPdfExercise(2, 4);
        $this->assertSame('Course - UNIT 2 (View Exercise-4).pdf', $result);
    }
}
