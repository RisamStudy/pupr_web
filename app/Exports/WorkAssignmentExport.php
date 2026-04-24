<?php

namespace App\Exports;

use App\Models\WorkAssignment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Http\Request;

class WorkAssignmentExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    use Exportable;

    protected $request;
    protected $rowCount = 0;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = WorkAssignment::query()->with(['heavyEquipment', 'assignmentUsers.user', 'city', 'district', 'village']);

        if ($this->request->has('status') && $this->request->status != '') {
            $query->where('status', $this->request->status);
        }

        if ($this->request->has('start_date') && $this->request->has('end_date')) {
            $query->whereBetween('start_date', [$this->request->start_date, $this->request->end_date]);
        }

        $this->rowCount = $query->count();
        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pekerjaan',
            'No Lambung',
            'Alat yang Digunakan',
            'Waktu Mulai',
            'Waktu Selesai',
            'Latitude',
            'Longitude',
            'Tipe Pekerjaan',
            'Permasalahan',
            'Jumlah Hari',
            'Kabupaten',
            'Kecamatan',
            'Desa',
            'Status',
        ];
    }

    public function map($workAssignment): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $workAssignment->project_name,
            $workAssignment->heavyEquipment->nomor_lambung,
            $workAssignment->heavyEquipment->name,
            $workAssignment->start_date->format('d/m/Y'),
            $workAssignment->end_date ? $workAssignment->end_date->format('d/m/Y') : 'Belum selesai',
            $workAssignment->latitude,
            $workAssignment->longitude,
            $workAssignment->tipe_pekerjaan,
            $workAssignment->permasalahan ?: '-',
            $workAssignment->expected_duration,
            $workAssignment->city->name,
            $workAssignment->district->name,
            $workAssignment->village->name,
            $this->formatStatus($workAssignment->status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->rowCount + 1;

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE]
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
            ],
            'A1:O' . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => Color::COLOR_BLACK]
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ],
            'A2:A' . $lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ],
            'G2:H' . $lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ],
                'numberFormat' => [
                    'formatCode' => '0.000000'
                ]
            ],
            'K2:K' . $lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ],
            'O2:O' . $lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,     // No
            'B' => 30,    // Nama Pekerjaan
            'C' => 15,    // No Lambung
            'D' => 20,    // Alat yang Digunakan
            'E' => 12,    // Waktu Mulai
            'F' => 12,    // Waktu Selesai
            'G' => 12,    // Latitude
            'H' => 12,    // Longitude
            'I' => 20,    // Tipe Pekerjaan
            'J' => 30,    // Permasalahan
            'K' => 10,    // Jumlah Hari
            'L' => 20,    // Kabupaten
            'M' => 20,    // Kecamatan
            'N' => 20,    // Desa
            'O' => 15,    // Status
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Set row height for header
                $sheet->getDelegate()->getRowDimension(1)->setRowHeight(20);

                // Freeze first row
                $sheet->getDelegate()->freezePane('A2');

                // Auto-filter
                $lastColumn = 'O';
                $lastRow = $this->rowCount + 1;
                $sheet->getDelegate()->setAutoFilter("A1:{$lastColumn}{$lastRow}");

                // Add zebra striping
                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getDelegate()
                            ->getStyle("A{$row}:O{$row}")
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('FFF2F2F2');
                    }
                }
            }
        ];
    }

    private function formatStatus($status): string
    {
        $statusMap = [
            'Belum Dimulai' => 'Belum Dimulai',
            'Sedang Berlangsung' => 'Sedang Berlangsung',
            'Selesai' => 'Selesai'
        ];

        return $statusMap[$status] ?? $status;
    }
}
