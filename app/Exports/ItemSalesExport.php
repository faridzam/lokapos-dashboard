<?php

namespace App\Exports;

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

use App\Models\User;
use App\Models\pos_cashier;
use App\Models\pos_store;
use App\Models\pos_payment;
use App\Models\pos_product;
use App\Models\pos_order_desktop;
use App\Models\pos_order_detail_desktop;

class ItemSalesExport implements FromCollection,WithHeadings,WithStyles,WithColumnWidths,WithEvents
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
            $orders = pos_order_desktop::whereBetween('created_at', [$con['from'], $con['to']])
            ->where('isActive', 1)
            ->pluck('id');
        } else {
            $orders = pos_order_desktop::where('store_id', $con['store'])
            ->whereBetween('created_at', [$con['from'], $con['to']])
            ->where('isActive', 1)
            ->pluck('id');
        }

        $products = pos_product::all();

        $result = collect();

        foreach ($products as $product) {

            $qty = pos_order_detail_desktop::whereIn('order_id', $orders)
            ->where('isActive', 1)
            ->where('product_id', $product->id)
            ->sum('qty');
            $subtotal = pos_order_detail_desktop::whereIn('order_id', $orders)
            ->where('isActive', 1)
            ->where('product_id', $product->id)
            ->sum('subtotal');

            if ($qty !== 0) {

                $result->push([
                    'product_code'=> $product->product_code, 
                    'name'=> $product->name, 
                    'cost'=> $product->cost, 
                    'price'=> $product->price, 
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
