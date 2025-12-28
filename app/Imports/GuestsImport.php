<?php

// app/Imports/GuestsImport.php
namespace App\Imports;

use App\Models\Guest;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Kalau excel ada header-nya

class GuestsImport implements ToModel {
    public function model(array $row) {
        return new Guest([
            'name' => $row[1], // col2: Nama
            'city' => $row[2], // col3: Kota
        ]);
    }
}
