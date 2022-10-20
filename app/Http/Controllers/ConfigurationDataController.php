<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreMember;
use App\Models\InvtItem;
use App\Models\InvtItemBarcode;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackge;
use App\Models\InvtItemRack;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use App\Models\PreferenceVoucher;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConfigurationDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('content.ConfigurationData.ConfigurationData');
    }

    public function checkDataConfiguration()
    {
        $item_stock = curl_init();
        curl_setopt($item_stock, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-item-stock');
        curl_setopt($item_stock, CURLOPT_RETURNTRANSFER, true);
        $response_item_stock = curl_exec($item_stock);
        $result_item_stock = json_decode($response_item_stock,TRUE);
        curl_close($item_stock);
        
        foreach ($result_item_stock as $key => $val) {
            $data_stock[$key] = InvtItemStock::where('company_id', Auth::user()->company_id)
            ->where('item_id', $val['item_id'])
            ->where('item_unit_id', $val['item_unit_id'])
            ->where('item_category_id', $val['item_category_id'])
            ->where('last_balance','!=',$val['last_balance'])
            ->first();
        }

        $data = array_slice($data_stock, 0, 1);
        return json_encode($data, true);

    }

    public function dwonloadConfigurationData() 
    {
        $item_category = curl_init();
        curl_setopt($item_category, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-item-category');
        curl_setopt($item_category, CURLOPT_RETURNTRANSFER, true);
        $response_item_category = curl_exec($item_category);
        $result_item_category = json_decode($response_item_category,TRUE);
        curl_close($item_category);
        
        InvtItemCategory::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_category_id')->delete();
        foreach ($result_item_category as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                InvtItemCategory::create($val);
            }
        }
        
        $item_unit = curl_init();
        curl_setopt($item_unit, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-item-unit');
        curl_setopt($item_unit, CURLOPT_RETURNTRANSFER, true);
        $response_item_unit = curl_exec($item_unit);
        $result_item_unit = json_decode($response_item_unit,TRUE);
        curl_close($item_unit);
        
        InvtItemUnit::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_unit_id')->delete();
        foreach ($result_item_unit as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                InvtItemUnit::create($val);
            }
        }

        $item_barcode = curl_init();
        curl_setopt($item_barcode, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-item-barcode');
        curl_setopt($item_barcode, CURLOPT_RETURNTRANSFER, true);
        $response_item_barcode = curl_exec($item_barcode);
        $result_item_barcode = json_decode($response_item_barcode,TRUE);
        curl_close($item_barcode);
        
        InvtItemBarcode::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_barcode_id')->delete();
        foreach ($result_item_barcode as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                InvtItemBarcode::create($val);
            }
        }

        $item_packge = curl_init();
        curl_setopt($item_packge, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-item-packge');
        curl_setopt($item_packge, CURLOPT_RETURNTRANSFER, true);
        $response_item_packge = curl_exec($item_packge);
        $result_item_packge = json_decode($response_item_packge,TRUE);
        curl_close($item_packge);
        
        InvtItemPackge::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_packge_id')->delete();
        foreach ($result_item_packge as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                InvtItemPackge::create($val);
            }
        }
        
        $item_warehouse = curl_init();
        curl_setopt($item_warehouse, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-item-warehouse');
        curl_setopt($item_warehouse, CURLOPT_RETURNTRANSFER, true);
        $response_item_warehouse = curl_exec($item_warehouse);
        $result_item_warehouse = json_decode($response_item_warehouse,TRUE);
        curl_close($item_warehouse);

        InvtWarehouse::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('warehouse_id')->delete();
        foreach ($result_item_warehouse as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                InvtWarehouse::create($val);
            }
        }

        $item = curl_init();
        curl_setopt($item, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-item');
        curl_setopt($item, CURLOPT_RETURNTRANSFER, true);
        $response_item = curl_exec($item);
        $result_item = json_decode($response_item,TRUE);
        curl_close($item);

        InvtItem::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_id')->delete();
        foreach ($result_item as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                InvtItem::create($val);
            }
        }

        $item_stock = curl_init();
        curl_setopt($item_stock, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-item-stock');
        curl_setopt($item_stock, CURLOPT_RETURNTRANSFER, true);
        $response_item_stock = curl_exec($item_stock);
        $result_item_stock = json_decode($response_item_stock,TRUE);
        curl_close($item_stock);
        
        InvtItemStock::whereNotNull('item_stock_id')->delete();
        foreach ($result_item_stock as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                InvtItemStock::create($val);
            }
        }

        $item_rack = curl_init();
        curl_setopt($item_rack, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-item-rack');
        curl_setopt($item_rack, CURLOPT_RETURNTRANSFER, true);
        $response_item_rack = curl_exec($item_rack);
        $result_item_rack = json_decode($response_item_rack,TRUE);
        curl_close($item_rack);
        
        InvtItemRack::whereNotNull('item_rack_id')->delete();
        foreach ($result_item_rack as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                InvtItemRack::create($val);
            }
        }

        $core_member = curl_init();
        curl_setopt($core_member, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-core-member');
        curl_setopt($core_member, CURLOPT_RETURNTRANSFER, true);
        $response_core_member = curl_exec($core_member);
        $result_core_member = json_decode($response_core_member,TRUE);
        curl_close($core_member);
        
        CoreMember::whereNotNull('member_id')->delete();
        foreach ($result_core_member as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                CoreMember::create($val);
            }
        }

        $preference_voucher = curl_init();
        curl_setopt($preference_voucher, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-preference-voucher');
        curl_setopt($preference_voucher, CURLOPT_RETURNTRANSFER, true);
        $response_preference_voucher = curl_exec($preference_voucher);
        $result_preference_voucher = json_decode($response_preference_voucher,TRUE);
        curl_close($preference_voucher);
        
        PreferenceVoucher::whereNotNull('voucher_id')->delete();
        foreach ($result_preference_voucher as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                PreferenceVoucher::create($val);
            }
        }

        return redirect()->back()->with('msg', 'Data berhasil diunduh');
    }   

    public function uploadConfigurationData()
    {
        $data_sales_invoice = SalesInvoice::where('status_upload',0)
        ->where('company_id',Auth::user()->company_id)
        ->get();
        $data_sales_invoice_item = SalesInvoiceItem::where('status_upload',0)
        ->where('company_id',Auth::user()->company_id)
        ->get();
        $data_core_member = CoreMember::where('company_id',Auth::user()->company_id)
        ->get();

        $data_sales_invoice = json_decode($data_sales_invoice,TRUE);
        for ($i=0; $i < count($data_sales_invoice); $i++) { 
            $sales_invoice = curl_init();
            curl_setopt($sales_invoice, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/post-data-sales-invoice');
            curl_setopt($sales_invoice, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($sales_invoice, CURLOPT_POSTFIELDS, $data_sales_invoice[$i]);
            $response_sales_invoice = curl_exec($sales_invoice);
            $result_sales_invoice = json_decode($response_sales_invoice,TRUE);
            curl_close($sales_invoice);
        }

        $data_sales_invoice_item = json_decode($data_sales_invoice_item,TRUE);
        for ($i=0; $i < count($data_sales_invoice_item); $i++) { 
            $sales_invoice_item = curl_init();
            curl_setopt($sales_invoice_item, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/post-data-sales-invoice-item');
            curl_setopt($sales_invoice_item, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($sales_invoice_item, CURLOPT_POSTFIELDS, $data_sales_invoice_item[$i]);
            $response_sales_invoice_item = curl_exec($sales_invoice_item);
            $result_sales_invoice_item = json_decode($response_sales_invoice_item,TRUE);
            curl_close($sales_invoice_item);    
        }
        // dd($response_sales_invoice_item);

        $data_core_member = json_decode($data_core_member,TRUE);
        for ($i=0; $i < count($data_core_member); $i++) { 
            $core_member = curl_init();
            curl_setopt($core_member, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/post-data-core-member');
            curl_setopt($core_member, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($core_member, CURLOPT_POSTFIELDS, $data_core_member[$i]);
            $response_core_member = curl_exec($core_member);
            $result_core_member = json_decode($response_core_member,TRUE);
            curl_close($core_member);    
        }
        for ($i=0; $i < count($data_core_member); $i++) { 
            $core_member_kopkar = curl_init();
            curl_setopt($core_member_kopkar, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/post-data-core-member-kopkar');
            curl_setopt($core_member_kopkar, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($core_member_kopkar, CURLOPT_POSTFIELDS, $data_core_member[$i]);
            $response_core_member_kopkar = curl_exec($core_member_kopkar);
            $result_core_member_kopkar = json_decode($response_core_member_kopkar,TRUE);
            curl_close($core_member_kopkar);    
        }


        CoreMember::where('company_id',Auth::user()->company_id)
        ->update(['member_account_receivable_amount_temp' => 0]);
        SalesInvoice::where('status_upload',0)
        ->where('company_id',Auth::user()->company_id)
        ->update(['status_upload' => 1]);
        SalesInvoiceItem::where('status_upload',0)
        ->where('company_id',Auth::user()->company_id)
        ->update(['status_upload' => 1]);

        return redirect()->back()->with('msg', 'Data berhasil di unggah');
    }
}
