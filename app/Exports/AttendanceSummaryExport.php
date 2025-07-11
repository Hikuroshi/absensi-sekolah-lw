<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceSummaryExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $dateFrom, $dateTo, $classId, $userId, $searchStudent;

    public function __construct($dateFrom, $dateTo, $classId = null, $userId = null, $searchStudent = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->classId = $classId;
        $this->userId = $userId;
        $this->searchStudent = $searchStudent;
    }

    public function collection()
    {
        $query = \App\Models\Attendance::query()
            ->select([
                'user_id',
                'class_id',
                'subject_id',
                \DB::raw('MAX(checked_at) as checked_at'),
                \DB::raw('SUM(status = "hadir" AND session_type = "masuk") as hadir_masuk'),
                \DB::raw('SUM(status = "hadir" AND session_type = "keluar") as hadir_keluar'),
                \DB::raw('SUM(status = "izin" AND session_type = "masuk") as izin_masuk'),
                \DB::raw('SUM(status = "izin" AND session_type = "keluar") as izin_keluar'),
                \DB::raw('SUM(status = "sakit" AND session_type = "masuk") as sakit_masuk'),
                \DB::raw('SUM(status = "sakit" AND session_type = "keluar") as sakit_keluar'),
                \DB::raw('SUM(status = "alpa" AND session_type = "masuk") as alpa_masuk'),
                \DB::raw('SUM(status = "alpa" AND session_type = "keluar") as alpa_keluar'),
            ])
            ->whereBetween('checked_at', [
                $this->dateFrom . ' 00:00:00',
                $this->dateTo . ' 23:59:59',
            ]);
        if ($this->classId) {
            $query->where('class_id', $this->classId);
        }
        if ($this->userId) {
            $query->where('user_id', $this->userId);
        }
        if (!empty($this->searchStudent)) {
            $search = $this->searchStudent;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        $data = $query->groupBy('user_id', 'class_id', 'subject_id')
            ->with(['user', 'class'])
            ->orderBy('user_id')
            ->get();
        return $data->map(function ($item) {
            return [
                'Nama' => $item->user->name ?? '-',
                'Kelas' => $item->class->name ?? '-',
                'Hadir Masuk' => $item->hadir_masuk,
                'Hadir Keluar' => $item->hadir_keluar,
                'Izin Masuk' => $item->izin_masuk,
                'Izin Keluar' => $item->izin_keluar,
                'Sakit Masuk' => $item->sakit_masuk,
                'Sakit Keluar' => $item->sakit_keluar,
                'Alpa Masuk' => $item->alpa_masuk,
                'Alpa Keluar' => $item->alpa_keluar,
                'Tanggal Terakhir' => $item->checked_at ? \Carbon\Carbon::parse($item->checked_at)->format('d-m-Y H:i:s') : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama', 
            'Kelas', 
            'Hadir Masuk', 
            'Hadir Keluar', 
            'Izin Masuk', 
            'Izin Keluar', 
            'Sakit Masuk', 
            'Sakit Keluar', 
            'Alpa Masuk', 
            'Alpa Keluar', 
            'Tanggal Terakhir'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => '4F81BD'] // Soft blue
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D3D3D3']
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);

        // Data rows style
        $sheet->getStyle('A2:K'.$sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D3D3D3']
                ]
            ]
        ]);

        // Alternate row coloring
        foreach (range(2, $sheet->getHighestRow()) as $row) {
            if ($row % 2 == 0) {
                $sheet->getStyle('A'.$row.':K'.$row)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('F2F2F2'); // Very light gray
            }
        }

        // Center align numeric columns
        $sheet->getStyle('C2:K'.$sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            ]
        ]);

        // Freeze header row
        $sheet->freezePane('A2');

        return [];
    }
}