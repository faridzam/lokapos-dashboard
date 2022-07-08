<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\pos_store;
use App\Models\pos_product;
use App\Models\pos_cashier;
use App\Models\pos_payment;
use App\Models\pos_order_desktop;
use App\Models\pos_order_detail_desktop;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use App\Exports\SalesExportMobile;
use App\Exports\ItemSalesExport;
use App\Exports\ItemSalesExportMobile;

class Reports extends Controller
{
    //
    public function getStores(){
        //
        $result = collect();
        $stores = pos_store::select('id', 'name', 'isActive')   
        ->where('platform', 'desktop')
        ->where('isActive', 1)
        ->get();

        $result->push(['id'=>'08993011870', 'name'=>'ALL', 'isActive' => '1']);
        foreach ($stores as $store) {
            $result->push($store);
        }
  
        $result->all();

        return response()->json([
            'stores' => $result,
        ], 200);
    }

    public function getStoresMobile(){
        //
        $result = collect();
        $stores = pos_store::select('id', 'name', 'isActive')
        ->where('platform', 'mobile')
        ->where('isActive', 1)
        ->get();

        $result->push(['id'=>'08993011870', 'name'=>'ALL', 'isActive' => '1']);
        foreach ($stores as $store) {
            $result->push($store);
        }
  
        $result->all();

        return response()->json([
            'stores' => $result,
        ], 200);
    }

    public function getSalesOrder($store, $dateStart, $dateEnd){
        $from = $dateStart." 00:00:00";
        $to = $dateEnd." 23:59:59";

        if ($store === '08993011870') {
            $orders = pos_order_desktop::where('isActive', 1)
            ->whereBetween('created_at', [$from, $to])
            ->select('id', 'no_invoice', 'cashier_id', 'payment_id', 'bill_amount', 'created_at')
            ->get();
            $omset = pos_order_desktop::where('isActive', 1)
            ->whereBetween('created_at', [$from, $to])
            ->sum('bill_amount');
        } else {
            $orders = pos_order_desktop::where('store_id', $store)
            ->where('isActive', 1)
            ->whereBetween('created_at', [$from, $to])
            ->select('id', 'no_invoice', 'cashier_id', 'payment_id', 'bill_amount', 'created_at')
            ->get();
            $omset = pos_order_desktop::where('store_id', $store)
            ->where('isActive', 1)
            ->whereBetween('created_at', [$from, $to])
            ->sum('bill_amount');
        }

        $result = collect();

        foreach ($orders as $order) {
            
            $itemCollections = pos_order_detail_desktop::where('order_id', $order->id)
            ->where('isActive', 1)
            ->select('order_id', 'product_id', 'qty')
            ->get();
            $paymentMethod = pos_payment::where('id', $order->payment_id)
            ->select('id', 'name')
            ->value('name');
            $cashierName = pos_cashier::where('id', $order->cashier_id)
            ->select('id', 'name')
            ->value('name');

            $stringItemSales = [];

            foreach ($itemCollections as $item) {
                $product = pos_product::where('id', $item->product_id)
                ->first();

                array_push($stringItemSales, "- ".$product->name."(x".$item->qty.")");
            }
            $split_strings = implode(",", $stringItemSales);
            
            $order['items'] = $split_strings;
            $order['payment_id'] = $paymentMethod;
            $order['cashier_id'] = $cashierName;

            $result->push($order);
        }

        return response()->json([
            'order' => $result,
            'omset' => $omset
        ]);
    }

    public function getSalesOrderMobile($store, $dateStart, $dateEnd){
        $from = $dateStart." 00:00:00";
        $to = $dateEnd." 23:59:59";

        if ($store === '08993011870') {
            $mobileOrders = DB::table('pos_activity_and')
            ->whereBetween('created_at', [$from, $to])
            ->select('id', 'no_invoice', 'id_kasir', 'id_jenis_pembayaran', 'total_pembelian', 'created_at')
            ->get();
            $mobileOmset = DB::table('pos_activity_and')
            ->whereBetween('created_at', [$from, $to])
            ->sum('total_pembelian');
        } else {
            $mobileOrders = DB::table('pos_activity_and')
            ->where('id_store', $store)
            ->whereBetween('created_at', [$from, $to])
            ->select('id', 'no_invoice', 'id_kasir', 'id_jenis_pembayaran', 'total_pembelian', 'created_at')
            ->get();
            $mobileOmset = DB::table('pos_activity_and')
            ->where('id_store', $store)
            ->whereBetween('created_at', [$from, $to])
            ->sum('total_pembelian');
        }

        $result = collect();

        foreach ($mobileOrders as $mobileOrder) {
            $mobileItemCollections = DB::table('pos_activity_item_and')
            ->where('no_invoice', $mobileOrder->no_invoice)
            ->select('no_invoice', 'nama_item', 'qty')
            ->get();

            $stringMobileItemSales = [];

            foreach ($mobileItemCollections as $mobileItemCollection) {
                array_push($stringMobileItemSales, "- ".$mobileItemCollection->nama_item."(x".$mobileItemCollection->qty.")");
            }
            $mobile_split_strings = implode(",", $stringMobileItemSales);

            $mobileOrder->items = $mobile_split_strings;
            $mobileOrder->cashier_id = $mobileOrder->id_kasir;
            $mobileOrder->payment_id = "mobile";
            $mobileOrder->bill_amount = $mobileOrder->total_pembelian;

            $result->push($mobileOrder);
        }

        return response()->json([
            'order' => $result,
            'omset' => $mobileOmset
        ]);
    }

    public function getItemSalesOrder($store, $dateStart, $dateEnd){
        $from = $dateStart." 00:00:00";
        $to = $dateEnd." 23:59:59";

        if ($store === '08993011870') {
            $orders = pos_order_desktop::where('isActive', 1)
            ->whereBetween('created_at', [$from, $to])
            ->pluck('id');
        } else {
            $orders = pos_order_desktop::where('store_id', $store)
            ->where('isActive', 1)
            ->whereBetween('created_at', [$from, $to])
            ->pluck('id');
        }

        $products = pos_product::all();

        $result = collect();

        foreach ($products as $product) {

            $qty = pos_order_detail_desktop::whereIn('order_id', $orders)
            ->where('product_id', $product->id)
            ->sum('qty');
            $subtotal = pos_order_detail_desktop::whereIn('order_id', $orders)
            ->where('product_id', $product->id)
            ->sum('subtotal');
            
            if ($qty !== 0) {
                $result->push([
                    'product_code'=> $product->product_code, 
                    'name'=> $product->name, 
                    'price'=> $product->price, 
                    'qty' => $qty, 
                    'subtotal' => $subtotal, 
                ]);
            }

        }

        return response()->json([
            'order' => $result,
        ]);
    }

    public function getItemSalesOrderMobile($store, $dateStart, $dateEnd){
        $from = $dateStart." 00:00:00";
        $to = $dateEnd." 23:59:59";

        if ($store === '08993011870') {
            $orders = DB::table('pos_activity_item_and')
            ->whereBetween('created_at', [$from, $to])
            ->pluck('id');
        } else {
            $orders = DB::table('pos_activity_item_and')
            ->where('id_store', $store)
            ->whereBetween('created_at', [$from, $to])
            ->pluck('id');
        }

        $products = DB::table('pos_item')->get();

        $result = collect();

        foreach ($products as $product) {

            $qty = DB::table('pos_activity_item_and')->whereIn('id', $orders)
            ->where('id_item', $product->id_item)
            ->sum('qty');
            $subtotal = DB::table('pos_activity_item_and')->whereIn('id', $orders)
            ->where('id_item', $product->id_item)
            ->sum('total');
            
            if ($qty !== 0) {
                $result->push([
                    'product_code'=> $product->id_item, 
                    'name'=> $product->nama_item, 
                    'price'=> $product->harga, 
                    'qty' => $qty, 
                    'subtotal' => $subtotal, 
                ]);
            }

        }

        return response()->json([
            'order' => $result,
        ]);
    }

    public function getExistOrderId(){
        //
        $existOrderId = pos_order_desktop::whereDate('created_at', '>=', Carbon::today()->subDays(7))
        ->pluck('id');
        return response()->json([
            'existOrderId' => $existOrderId
        ]);
    }
    
    public function fetchOrder(Request $request){
        //
        if (count(json_decode(request('order'), TRUE)) > 0) {
            $orderObj = json_decode(request('order'), TRUE);
            $orderId = [];

            foreach ($orderObj as $key => $value) {

                pos_order_desktop::create([
                    'id' => $value['id'],
                    'no_invoice' => $value['no_invoice'],
                    'pc_id' => $value['pc_id'],
                    'store_id' => $value['store_id'],
                    'cashier_id' => $value['cashier_id'],
                    'payment_id' => $value['payment_id'],
                    'bill_amount' => $value['bill_amount'],
                    'tax' => $value['tax'],
                    'discount' => $value['discount'],
                    'isActive' => $value['isActive'],
                    'created_at' => $value['created_at'],
                    'updated_at' => $value['updated_at'],
                ]);
                
                array_push($orderId, $value['id']);
            }

            // URL
            $apiURL = 'http://127.0.0.1:8099/api/sync-order-detail';

            // Headers
            $headers = [
                //...
            ];

            // POST Data
            $postInput = [
                'arr' => $orderId
            ];

            $response = Http::withHeaders($headers)->post($apiURL, $postInput);
            $statusCode = $response->status();
            $responseBody = json_decode($response->getBody(), true);

            // $client = new Client();
            // $body = $client->get('http://172.26.160.242:8099/api/sync-order-detail')->getBody();

            foreach ($responseBody as $key => $value) {

                pos_order_detail_desktop::create([

                    'id' => $value['id'],
                    'no_invoice' => $value['no_invoice'],
                    'order_id' => $value['order_id'],
                    'product_id' => $value['product_id'],
                    'qty' => $value['qty'],
                    'subtotal' => $value['subtotal'],
                    'discount' => $value['discount'],
                    'specialPrice' => $value['specialPrice'],
                    'note' => $value['note'],
                    'isActive' => $value['isActive'],
                    'created_at' => $value['created_at'],
                    'updated_at' => $value['updated_at'],
                    
                ]);

            }

            return response()->json([
                'orderDetail' => $responseBody,
                'message' => 'sales data synchronized'
            ]);
        } else{
            return response()->json([
                'message' => 'no update available'
            ]);
        }
    }

    public function exportSales($store, $dateFrom, $dateTo){
        //
        if ($store === '08993011870') {
            $omzet = pos_order_desktop::whereBetween('created_at', [$dateFrom." 00:00:00", $dateTo." 23:59:59"])
            ->where('isActive', 1)
            ->sum('bill_amount');
            $storeName = 'All';
        } else {
            $omzet = pos_order_desktop::where('store_id', $store)
            ->whereBetween('created_at', [$dateFrom." 00:00:00", $dateTo." 23:59:59"])
            ->where('isActive', 1)
            ->sum('bill_amount');
            $storeName = pos_store::where('id', $store)
            ->value('name');
        }

        $conditions = array(
            'store' => $store,
            'from' => $dateFrom." 00:00:00",
            'to' => $dateTo." 23:59:59",
            'omzet' => $omzet,
        );

        $export = new SalesExport($conditions);
        return Excel::download($export, 'sales-'.$storeName."-".$dateFrom."-".$dateTo.'.xlsx');
    }
    public function exportSalesMobile($store, $dateFrom, $dateTo){
        //
        if ($store === '08993011870') {
            $omzet = DB::table('pos_activity_and')->whereBetween('created_at', [$dateFrom." 00:00:00", $dateTo." 23:59:59"])
            ->sum('total_pembelian');
            $storeName = 'All';
        } else {
            $omzet = DB::table('pos_activity_and')->where('id_store', $store)
            ->whereBetween('created_at', [$dateFrom." 00:00:00", $dateTo." 23:59:59"])
            ->sum('total_pembelian');
            $storeName = pos_store::where('id', $store)
            ->value('name');
        }

        $conditions = array(
            'store' => $store,
            'from' => $dateFrom." 00:00:00",
            'to' => $dateTo." 23:59:59",
            'omzet' => $omzet,
        );

        $export = new SalesExportMobile($conditions);
        return Excel::download($export, 'sales-'.$storeName."-".$dateFrom."-".$dateTo.'.xlsx');
    }

    public function exportItemSales($store, $dateFrom, $dateTo){
        if ($store === '08993011870') {
            $omzet = pos_order_desktop::whereBetween('created_at', [$dateFrom." 00:00:00", $dateTo." 23:59:59"])
            ->where('isActive', 1)
            ->sum('bill_amount');
            $storeName = 'All';
        } else {
            $omzet = pos_order_desktop::where('store_id', $store)
            ->whereBetween('created_at', [$dateFrom." 00:00:00", $dateTo." 23:59:59"])
            ->where('isActive', 1)
            ->sum('bill_amount');
            $storeName = pos_store::where('id', $store)
            ->value('name');
        }

        $conditions = array(
            'from' => $dateFrom." 00:00:00",
            'to' => $dateTo." 23:59:59",
            'store' => $store,
            'omzet' => $omzet,
        );

        $export = new ItemSalesExport($conditions);
        return Excel::download($export, 'item-sales-'.$storeName."-".$dateFrom."-".$dateTo.'.xlsx');
    }
    public function exportItemSalesMobile($store, $dateFrom, $dateTo){
        if ($store === '08993011870') {
            $omzet = DB::table('pos_activity_and')->whereBetween('created_at', [$dateFrom." 00:00:00", $dateTo." 23:59:59"])
            ->select('total_pembelian')
            ->sum('total_pembelian');
            $storeName = 'All';
        } else {
            $omzet = DB::table('pos_activity_and')->whereBetween('created_at', [$dateFrom." 00:00:00", $dateTo." 23:59:59"])
            ->where('id_store', $store)
            ->select('total_pembelian')
            ->sum('total_pembelian');
            $storeName = pos_store::where('id', $store)
            ->value('name');
        }

        $conditions = array(
            'from' => $dateFrom." 00:00:00",
            'to' => $dateTo." 23:59:59",
            'store' => $store,
            'omzet' => $omzet,
        );

        $export = new ItemSalesExportMobile($conditions);
        return Excel::download($export, 'item-sales-'.$storeName."-".$dateFrom."-".$dateTo.'.xlsx');
    }
}
