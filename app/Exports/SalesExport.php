<?php

namespace App\Exports;

use Illuminate\Support\Collection;

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

class SalesExport implements FromCollection,WithHeadings,WithStyles,WithColumnWidths,WithEvents
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
        //
        $con = $this->conditions;

        if ($con['store']  === '08993011870') {
            $datas = pos_order_desktop::whereBetween('created_at', [$con['from'], $con['to']])
            ->where('isActive', 1)
            ->select('id', 'no_invoice', 'cashier_id', 'payment_id', 'bill_amount')
            ->get();
        } else {
            $datas = pos_order_desktop::where('store_id', $con['store'])
            ->whereBetween('created_at', [$con['from'], $con['to']])
            ->where('isActive', 1)
            ->select('id', 'no_invoice', 'cashier_id', 'payment_id', 'bill_amount')
            ->get();
        }

        foreach ($datas as $data) {
            $methodName = pos_payment::where('id', $data->payment_id)
            ->value('name');
            $cashierName = pos_cashier::where('id', $data->cashier_id)
            ->value('username');
            $itemCollections = pos_order_detail_desktop::where('order_id', $data->id)
            ->where('isActive', 1)
            ->select('order_id', 'product_id', 'qty')
            ->get();

            $stringItemSales = [];
            
            foreach($itemCollections as $item) {
                $product = pos_product::where('id', $item->product_id)
                ->first();

                array_push($stringItemSales, $product->name."(x".$item->qty.")");
            }
            $split_strings = implode(", ", $stringItemSales);

            $data['payment_id'] = $methodName;
            $data['cashier_id'] = $cashierName;
            $data['items'] = $split_strings;
        }

        return $datas;
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
        ["ID", "Nomor Invoice", "Cashier", "Payment Method", "Bill Amount(Rp)", "Items"]];
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
            'A' => 10,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 80,
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
