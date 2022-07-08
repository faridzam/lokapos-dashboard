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

class SalesExportMobile implements FromCollection,WithHeadings,WithStyles,WithColumnWidths,WithEvents
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
            $datas = DB::table('pos_activity_and')->select('id', 'no_invoice', 'id_kasir', 'id_jenis_pembayaran', 'total_pembelian')
            ->whereBetween('created_at', [$con['from'], $con['to']])
            ->get();
        } else {
            $datas = DB::table('pos_activity_and')->select('id', 'no_invoice', 'id_kasir', 'id_jenis_pembayaran', 'total_pembelian')
            ->where('id_store', $con['store'])
            ->whereBetween('created_at', [$con['from'], $con['to']])
            ->get();
        }

        foreach ($datas as $data) {
            $methodName = DB::table('pos_jenis_pembayaran')->where('id_jenis_pembayaran', $data->id_jenis_pembayaran)
            ->value('jenis_pembayaran');
            $itemCollections = DB::table('pos_activity_item_and')
            ->where('no_invoice', $data->no_invoice)
            ->select('nama_item', 'qty')
            ->get();

            $stringItemSales = [];

            foreach ($itemCollections as $item) {
                array_push($stringItemSales, "- ".$item->nama_item."(x".$item->qty.")");
            }
            $split_strings = implode(", ", $stringItemSales);

            $data->items = $split_strings;
            $data->id_jenis_pembayaran = $methodName;
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

        return [["Nama Store:", $storeName],
        ["Total Omset:", $con['omzet']],
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
