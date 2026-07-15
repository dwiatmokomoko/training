<?php

namespace App\Support;

use Illuminate\Support\Collection;

class TrainingCatalog
{
    public static function grouped(): array
    {
        return [
            'Hakim' => [
                'Sertifikasi Hakim Tipikor',
                'Sertifikasi Hakim Lingkungan Hidup',
                'Sertifikasi Hakim Anak',
                'Sertifikasi Hakim Niaga',
                'Sertifikasi Hakim Mediator',
                'Bimtek Penyusunan Putusan',
            ],
            'Panitera/Panitera Pengganti' => [
                'Pelatihan Teknis Yustisial',
                'Administrasi Perkara Perdata dan Pidana',
                'SIPP',
                'e-Court dan e-Litigation',
                'Sertifikasi Panitera/Panitera Pengganti',
                'Minutasi dan Arsip Perkara',
            ],
            'Jurusita' => [
                'Teknis Pemanggilan dan Pemberitahuan',
                'Eksekusi Putusan Pengadilan',
                'e-Court dan e-Summons',
                'Sertifikasi Jurusita',
                'Komunikasi Efektif dan Pelayanan Publik',
            ],
            'Kesekretariatan' => [
                'PKA/PKN',
                'SAKTI dan DIPA',
                'PBJ Sertifikasi LKPP',
                'BMN',
                'SAKIP',
                'Kearsipan dan Tata Naskah Dinas',
                'IKPA',
            ],
        ];
    }

    public static function names(): array
    {
        return Collection::make(self::grouped())->flatten()->values()->all();
    }

    public static function categoryFor(string $trainingName): ?string
    {
        foreach (self::grouped() as $category => $items) {
            if (in_array($trainingName, $items, true)) {
                return $category;
            }
        }

        return null;
    }
}
