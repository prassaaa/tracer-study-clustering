<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SurveyExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        // Hapus header row dari data (karena sudah kita handle di headings())
        return array_slice($this->data, 1);
    }

    public function headings(): array
    {
        // Ambil header row (row pertama dari data)
        return $this->data[0] ?? [];
    }
}