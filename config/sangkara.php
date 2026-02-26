<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sangkara Officials (Signatures for generated documents)
    |--------------------------------------------------------------------------
    |
    | Data pejabat yang akan digunakan pada template dokumen.
    | Bisa diatur via .env untuk fleksibilitas.
    |
    */

    'ketua_nama' => env('SANGKARA_KETUA_NAMA', 'Nama Ketua'),
    'ketua_nim' => env('SANGKARA_KETUA_NIM', '000000000'),
    'sekre_nama' => env('SANGKARA_SEKRE_NAMA', 'Nama Sekretaris'),
    'sekre_nim' => env('SANGKARA_SEKRE_NIM', '000000000'),
];
