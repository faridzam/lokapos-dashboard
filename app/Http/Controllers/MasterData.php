<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\pos_store;
use App\Models\pos_category;
use App\Models\pos_product;

class MasterData extends Controller
{
    public function getDataStores(){
        //
        $stores = pos_store::orderBy('name', 'asc')->get();

        foreach ($stores as $store) {
            if ($store->isActive === 1) {
                $store['isActive'] = 'ACTIVE';
            } else {
                $store['isActive'] = 'NON-ACTIVE';
            }
        }

        return response()->json([
            'stores' => $stores,
        ], 200);
    }

    public function getStoreDesktopID(){
        $stores = pos_store::where('platform', 'desktop')
        ->pluck('store_id');
        
        return response()->json([
            'storeId' => $stores
        ]);
    }

    public function fetchStoreDesktop(){

        if (count(json_decode(request('stores'), TRUE)) > 0) {
            $storeObj = json_decode(request('stores'), TRUE);

            foreach ($storeObj as $key => $value) {

                pos_store::create([
                    'store_id' => $value['id'],
                    'name' => $value['name'],
                    'ip_address_mobile' => null,
                    'type' => $value['type'],
                    'area' => $value['area'],
                    'platform' => "desktop",
                    'isActive' => $value['isActive'],
                    'created_at' => $value['created_at'],
                    'updated_at' => $value['updated_at'],
                ]);
                
            }

            return response()->json([
                'message' => 'store data synchronized'
            ]);
        } else{
            return response()->json([
                'message' => 'no update available'
            ]);
        }
    }

    public function addStoreDesktop(Request $request){
        //
        pos_store::create([
            'store_id' => request('store_id'),
            'name' => request('name'),
            'type' => request('type'),
            'area' => request('area'),
            'platform' => request('platform'),
        ]);
    }

    public function updateStore(Request $request){
        //
        $store = pos_store::find(request('id'));
        $store->platform = request('platform');
        $store->type = request('type');
        $store->area = request('area');
        $store->name = request('name');
        $store->ip_address_mobile = request('ip_address_mobile');
        $store->updated_at = Carbon::now();
        $store->update();
    }

    public function getDataCategories(){
        //
        $categories = pos_category::orderBy('name', 'asc')->get();

        foreach ($categories as $category) {
            if ($category->isActive === 1) {
                $category['isActive'] = 'ACTIVE';
            } else {
                $category['isActive'] = 'NON-ACTIVE';
            }
        }

        return response()->json([
            'categories' => $categories,
        ], 200);
    }

    public function addCategoryDesktop(Request $request){
        //
        pos_category::create([
            'category_id' => request('category_id'),
            'name' => request('name'),
            'type' => request('type'),
            'platform' => 'desktop',
        ]);
    }

    public function updateCategory(Request $request){
        //

        if (request('platform') === 'desktop') {
            $category = pos_category::find(request('id'));
            $category->type = request('type');
            $category->name = request('name');
            $category->updated_at = Carbon::now();
            $category->update();
        } else {
            $category = pos_category::find(request('id'));
            $category->name = request('name');
            $category->updated_at = Carbon::now();
            $category->update();
        }
    }

    public function getDataProducts(){
        //
        $products = pos_product::orderBy('name', 'asc')->get();

        foreach ($products as $product) {
            if ($product->isActive === 1) {
                $product['isActive'] = 'ACTIVE';
            } else {
                $product['isActive'] = 'NON-ACTIVE';
            }
        }

        return response()->json([
            'products' => $products,
        ], 200);
    }
}
