<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Events\AfterSheet;

class LeadsExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithColumnWidths
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        $count = 1;

        return Lead::where('search_id', $this->id)->get()->map(function ($lead) use (&$count) {

            // 🔥 Clean phone (remove 0 + format)
            $phone = ltrim($lead->phone, '0');
            $phone = '+91-' . $phone;

            return [
                'S.No' => $count++, // ✅ Serial number
                'Name' => $lead->name,
                'Phone' => $phone,
                'Email' => $lead->email,              // ✅ ADDED
                'Website' => $lead->website,
                'Address' => $lead->address,
                'Main Area' => $lead->main_area,      // ✅ ADDED
                'Pincode' => $lead->pincode,          // ✅ ADDED
                'Maps' => $lead->maps_url,
                'Rating' => $lead->rating,
            ];
        });
    }

    public function headings(): array
    {
        return ['S.No', 'Name', 'Phone', 'Email', 'Website', 'Address', 'Main Area', 'Pincode', 'Maps', 'Rating'];
    }

    // 🔥 HEADER STYLE
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }

    // 🔥 COLUMN WIDTHS (compact)
    public function columnWidths(): array
    {
        return [
            'A' => 8,   // S.No
            'B' => 30,  // Name
            'C' => 18,  // Phone
            'D' => 30,  // Email ✅
            'E' => 35,  // Website
            'F' => 50,  // Address
            'G' => 25,  // Main Area ✅
            'H' => 15,  // Pincode ✅
            'I' => 35,  // Maps
            'J' => 10,  // Rating
        ];
    }

    // 🔥 EVENTS (ALIGN + FILTER + WRAP)
   public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {

            // Wrap Address
            $event->sheet->getStyle('F:F')->getAlignment()->setWrapText(true);

            // Align Phone LEFT
            $event->sheet->getStyle('C:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Center Pincode ✅
            $event->sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Center Rating
            $event->sheet->getStyle('J:J')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // 🔥 ADD FILTERS
            $event->sheet->setAutoFilter('A1:J1');

            // Compact rows
            $event->sheet->getDefaultRowDimension()->setRowHeight(-1);
        },
    ];
}
}