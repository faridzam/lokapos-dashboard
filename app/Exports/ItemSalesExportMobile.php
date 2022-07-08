<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use App\Models\pos_cashier;
use App\Models\pos_store;

class ItemSalesExportMobile implements FromCollection,WithHeadings,WithStyles,WithColumnWidths,WithEvents
{
    protected $conditions = array();

    public function __construct(array $conditions)
    {
        $this->conditions = $conditions;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $con = $this->conditions;

        if ($con['store']  === '08993011870') {
            $orders = DB::table('pos_activity_and')->whereBetween('created_at', [$con['from'], $con['to']])
            ->select('no_invoice', 'created_at')
            ->pluck('no_invoice');
        } else {
            $orders = DB::table('pos_activity_and')->where('id_store', $con['store'])
            ->whereBetween('created_at', [$con['from'], $con['to']])
            ->select('no_invoice', 'created_at')
            ->pluck('no_invoice');
        }

        $products = DB::table('pos_item')->get();

        $result = collect();

        foreach ($products as $product) {

            $qty = DB::table('pos_activity_item_and')->whereIn('no_invoice', $orders)
            ->where('id_item', $product->id_item)
            ->select('qty')
            ->sum('qty');
            $subtotal = DB::table('pos_activity_item_and')->whereIn('no_invoice', $orders)
            ->where('id_item', $product->id_item)
            ->select('total')
            ->sum('total');

            if ($qty !== 0) {

                $result->push([
                    'product_code'=> $product->id_item, 
                    'name'=> $product->nama_item, 
                    'cost'=> $product->hpp, 
                    'price'=> $product->harga, 
                    'qty' => $qty, 
                    'subtotal' => $subtotal, 
                ]);
            }

        }

        return $result;
    }

    public function headings(): array
    {
        $con = $this->conditions;

        if ($con['store'] === '08993011870') {
            $storeName = 'All';
        } else {
            $storeName = pos_store::where('id', $con['store'])->value('name');
        }

        return [["Store Name:", $storeName],
        ["Omzet Total:", $con['omzet']],
        [""],
        [""],
        [""],
        ["Product Code", "Product Name", "Cost(Rp)", "Price(Rp)", "Quantity", "Subtotal(Rp)"]];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            6    => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {

                $event->sheet->getDelegate()->getStyle('A1:Z1048576')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            },
        ];
    }
}
