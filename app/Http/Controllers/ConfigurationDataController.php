<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CloseCashierLog;
use App\Models\CoreMember;
use App\Models\InvtItem;
use App\Models\InvtItemBarcode;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackge;
use App\Models\InvtItemRack;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use App\Models\PreferenceCompany;
use App\Models\PreferenceVoucher;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SIIRemoveLog;
use App\Models\SystemLoginLog;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
        $response = Http::get('https://ciptapro.com/kasihibu_minimarket/api/get-data-item-stock');
        $result_item_stock = json_decode($response,TRUE);
        
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
        $get_1 = Http::get('https://ciptapro.com/kasihibu_minimarket/api/get-data-item-category');
        $response_1 = json_decode($get_1->body(), true);

        InvtItemCategory::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_category_id')->delete();
        foreach ($response_1 as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data1 = InvtItemCategory::create($val);
            }
        }

        $get_2 = Http::get('https://ciptapro.com/kasihibu_minimarket/api/get-data-item-unit');
        $response_2 = json_decode($get_2->body(), true);

        InvtItemUnit::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_unit_id')->delete();
        foreach ($response_2 as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data2 = InvtItemUnit::create($val);
            }
        }

        $get_3 = Http::get('https://ciptapro.com/kasihibu_minimarket/api/get-data-item-barcode');
        $response_3 = json_decode($get_3->body(), true);

        InvtItemBarcode::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_barcode_id')->delete();
        foreach ($response_3 as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data3 = InvtItemBarcode::create($val);
            }
        }

        $get_4 = Http::get('https://ciptapro.com/kasihibu_minimarket/api/get-data-item-packge');
        $response_4 = json_decode($get_4->body(), true);

        InvtItemPackge::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_packge_id')->delete();
        foreach ($response_4 as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data4 = InvtItemPackge::create($val);
            }
        }

        $get_5 = Http::get('https://ciptapro.com/kasihibu_minimarket/api/get-data-item-warehouse');
        $response_5 = json_decode($get_5->body(), true);

        InvtWarehouse::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('warehouse_id')->delete();
        foreach ($response_5 as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data5 = InvtWarehouse::create($val);
            }
        }

        $get_6 = Http::get('https://ciptapro.com/kasihibu_minimarket/api/get-data-item');
        $response_6 = json_decode($get_6->body(), true);

        InvtItem::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_id')->delete();
        foreach ($response_6 as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data6 = InvtItem::create($val);
            }
        }

        $get_7 = Http::get('https://ciptapro.com/kasihibu_minimarket/api/get-data-item-stock');
        $response_7 = json_decode($get_7->body(), true);

        InvtItemStock::whereNotNull('item_stock_id')->delete();
        foreach ($response_7 as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data7 = InvtItemStock::create($val);
            }
        }

        $get_8 = Http::get('https://ciptapro.com/kasihibu_minimarket/api/get-data-item-rack');
        $response_8 = json_decode($get_8->body(), true);

        InvtItemRack::whereNotNull('item_rack_id')->delete();
        foreach ($response_8 as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data8 = InvtItemRack::create($val);
            }
        }

        $get_9 = Http::get('https://ciptapro.com/kasihibu_minimarket/api/get-data-core-member');
        $response_9 = json_decode($get_9->body(), true);

        CoreMember::whereNotNull('member_id')->delete();
        foreach ($response_9 as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data9 = CoreMember::create($val);
            }
        }

        $get_10 = Http::get('https://ciptapro.com/kasihibu_minimarket/api/get-data-preference-voucher');
        $response_10 = json_decode($get_10->body(), true);

        PreferenceVoucher::whereNotNull('voucher_id')->delete();
        foreach ($response_10 as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data10 = PreferenceVoucher::create($val);
            }
        }

        if (($data1 == true) && ($data2 == true) && ($data3 == true) && ($data4 == true) && ($data5 == true) && ($data6 == true) && ($data7 == true) && ($data8 == true) && ($data9 == true) && ($data10 == true)) {
            session()->flash('msg',"Data Berhasil didownload");
            return redirect('configuration-data');
        } else {
            session()->flash('msg',"Data Gagal didownload");
            return redirect('configuration-data');
        }
    }   

    public function uploadConfigurationData()
    {
        $data_sales_invoice = SalesInvoice::where('status_upload',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $data_sales_invoice_item = SalesInvoiceItem::where('status_upload',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $data_core_member = CoreMember::where('member_account_receivable_amount_temp', '!=', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $data_close_cashier = CloseCashierLog::where('status_upload', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $data_login_log = SystemLoginLog::where('status_upload', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $data_sii_remove = SIIRemoveLog::where('status_upload', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        if (count($data_sales_invoice) != 0) {
            foreach ($data_sales_invoice as $key => $val) {
                $response = Http::post('https://ciptapro.com/kasihibu_minimarket/api/post-data-sales-invoice', [
                    'sales_invoice_id'          => $val['sales_invoice_id'],
                    'company_id'                => $val['company_id'],
                    'customer_id'               => $val['customer_id'],
                    'voucher_id'                => $val['voucher_id'],
                    'voucher_no'                => $val['voucher_no'],
                    'sales_invoice_no'          => $val['sales_invoice_no'],
                    'sales_invoice_date'        => $val['sales_invoice_date'],
                    'sales_payment_method'      => $val['sales_payment_method'],
                    'subtotal_item'             => $val['subtotal_item'],
                    'subtotal_amount'           => $val['subtotal_amount'],
                    'voucher_amount'            => $val['voucher_amount'],
                    'discount_percentage_total' => $val['discount_percentage_total'],
                    'discount_amount_total'     => $val['discount_amount_total'],
                    'total_amount'              => $val['total_amount'],
                    'paid_amount'               => $val['paid_amount'],
                    'change_amount'             => $val['change_amount'],
                    'from_store'                => $val['from_store'],
                    'data_state'                => $val['data_state'],
                    'created_id'                => $val['created_id'],
                    'updated_id'                => $val['updated_id'],
                    'created_at'                => $val['created_at'],
                    'updated_at'                => $val['updated_at'],
                ]);
    
                if ($response->successful() == true) {
                    SalesInvoice::where('sales_invoice_id', $val['sales_invoice_id'])
                    ->update([
                        'status_upload' => 1,
                        'updated_id'    => Auth::id()
                    ]);
                }
            }
        }

        if (count($data_sales_invoice_item) != 0) {
            foreach ($data_sales_invoice_item as $key => $val) {
                $response = Http::post('https://ciptapro.com/kasihibu_minimarket/api/post-data-sales-invoice-item', [
                    'sales_invoice_item_id'             => $val['sales_invoice_item_id'],
                    'company_id'                        => $val['company_id'],
                    'sales_invoice_id'                  => $val['sales_invoice_id'],
                    'item_category_id'                  => $val['item_category_id'],
                    'item_unit_id'                      => $val['item_unit_id'],
                    'item_id'                           => $val['item_id'],
                    'quantity'                          => $val['quantity'],
                    'item_unit_price'                   => $val['item_unit_price'],
                    'subtotal_amount'                   => $val['subtotal_amount'],
                    'discount_percentage'               => $val['discount_percentage'],
                    'discount_amount'                   => $val['discount_amount'],
                    'subtotal_amount_after_discount'    => $val['subtotal_amount_after_discount'],
                    'data_state'                        => $val['data_state'],
                    'created_id'                        => $val['created_id'],
                    'updated_id'                        => $val['updated_id'],
                    'created_at'                        => $val['created_at'],
                    'updated_at'                        => $val['updated_at'],
                ]);
    
                if ($response->successful() == true) {
                    SalesInvoiceItem::where('sales_invoice_item_id', $val['sales_invoice_item_id'])
                    ->update([
                        'status_upload' => 1,
                        'updated_id'    => Auth::id()
                    ]);
                }
            }
        }

        if (count($data_core_member) != 0) {
            foreach ($data_core_member as $key => $val) {
                $response = Http::post('https://ciptapro.com/kasihibu_minimarket/api/post-data-core-member', [
                    'member_no'                                 => $val['member_no'],
                    'member_account_receivable_amount_temp'     => $val['member_account_receivable_amount_temp'],
                ]);

                Http::post('https://ciptapro.com/kasihibu_minimarket/api/post-data-core-member-kopkar', [
                    'member_no'                                 => $val['member_no'],
                    'member_account_receivable_amount_temp'     => $val['member_account_receivable_amount_temp'],
                ]);
    
                if ($response->successful() == true) {
                    CoreMember::where('member_id', $val['member_id'])
                    ->update([
                        'member_account_receivable_amount_temp' => 0,
                    ]);
                }
            }
        }

        if (count($data_close_cashier) != 0) {
            foreach ($data_core_member as $key => $val) {
                $response = Http::post('https://ciptapro.com/kasihibu_minimarket/api/post-data-close-cashier', [
                    'cashier_log_id'                => $val['cashier_log_id'],
                    'company_id'                    => $val['company_id'],
                    'cashier_log_date'              => $val['cashier_log_date'],
                    'shift_cashier'                 => $val['shift_cashier'],
                    'total_cash_transaction'        => $val['total_cash_transaction'],
                    'amount_cash_transaction'       => $val['amount_cash_transaction'],
                    'total_receivable_transaction'  => $val['total_receivable_transaction'],
                    'amount_receivable_transaction' => $val['amount_receivable_transaction'],
                    'total_cashless_transaction'    => $val['total_cashless_transaction'],
                    'amount_cashless_transaction'   => $val['amount_cashless_transaction'],
                    'total_transaction'             => $val['total_transaction'],
                    'total_amount'                  => $val['total_amount'],
                    'data_state'                    => $val['data_state'],
                    'created_id'                    => $val['created_id'],
                    'updated_id'                    => $val['updated_id'],
                    'created_at'                    => $val['created_at'],
                    'updated_at'                    => $val['updated_at'],
                ]);
    
                if ($response->successful() == true) {
                    CloseCashierLog::where('cashier_log_id', $val['cashier_log_id'])
                    ->update([
                        'status_upload' => 1,
                        'updated_id'    => Auth::id()
                    ]);
                }
            }
        }

        if (count($data_login_log) != 0) {
            foreach ($data_login_log as $key => $val) {
                $response = Http::post('https://ciptapro.com/kasihibu_minimarket/api/post-data-login-log', [
                    'login_log_id'  => $val['login_log_id'],
                    'user_id'       => $val['user_id'],
                    'company_id'    => $val['company_id'],
                    'log_time'      => $val['log_time'],
                    'log_status'    => $val['log_status'],
                    'status_upload' => $val['status_upload'],
                    'created_at'    => $val['created_at'],
                    'updated_at'    => $val['updated_at'],
                ]);
    
                if ($response->successful() == true) {
                    SystemLoginLog::where('login_log_id', $val['login_log_id'])
                    ->update([
                        'status_upload' => 1,
                    ]);
                }
            }
        }

        if (count($data_sii_remove) != 0) {
            foreach ($data_sii_remove as $key => $val) {
                $response = Http::post('https://ciptapro.com/kasihibu_minimarket/api/post-data-sii-remove-log', [
                    'sii_remove_log_id'         => $val['sii_remove_log_id'],
                    'company_id'                => $val['company_id'],
                    'sales_invoice_id'          => $val['sales_invoice_id'],
                    'sales_invoice_item_id'     => $val['sales_invoice_item_id'],
                    'sales_invoice_no'          => $val['sales_invoice_no'],
                    'sii_amount'                => $val['sii_amount'],
                    'data_state'                => $val['data_state'],
                    'created_id'                => $val['created_id'],
                    'updated_id'                => $val['updated_id'],
                    'created_at'                => $val['created_at'],
                    'updated_at'                => $val['updated_at'],
                ]);
    
                if ($response->successful() == true) {
                    SIIRemoveLog::where('sii_remove_log_id', $val['sii_remove_log_id'])
                    ->update([
                        'status_upload' => 1,
                        'updated_id'    => Auth::id()
                    ]);
                }
            }
        }
        
        $msg = "Data Berhasil diupload";
        return redirect('configuration-data')->with('msg', $msg);
    }

    public function checkCloseCashierConfiguration()
    {
        $data = CloseCashierLog::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->whereDate('cashier_log_date', date('Y-m-d'))
        ->get();

        return count($data);
    }

    public function closeCashierConfiguration()
    {
        $sales_invoice = SalesInvoice::where('data_state',0)
        ->whereDate('sales_invoice_date', date('Y-m-d'))
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $close_cashier = CloseCashierLog::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->whereDate('cashier_log_date', date('Y-m-d'))
        ->get();
        $first_cashier = CloseCashierLog::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->whereDate('cashier_log_date', date('Y-m-d'))
        ->first();

        $total_cash_transaction         = 0;
        $amount_cash_transaction        = 0;
        $total_receivable_transaction   = 0;
        $amount_receivable_transaction  = 0;
        $total_cashless_transaction     = 0;
        $amount_cashless_transaction    = 0;
        $total_transaction              = 0;
        $total_amount                   = 0;

        foreach ($sales_invoice as $key => $val) {
            if ($val['sales_payment_method'] == 1) {
                $total_cash_transaction += 1;
                $amount_cash_transaction += $val['total_amount'];
            } else if ($val['sales_payment_method'] == 2) {
                $total_receivable_transaction += 1;
                $amount_receivable_transaction += $val['total_amount'];
            } else {
                $total_cashless_transaction += 1;
                $amount_cashless_transaction += $val['total_amount'];
            }

            $total_transaction += 1;
            $total_amount +=  $val['total_amount'];
        }

        if (count($close_cashier) == 1) {
            $data_close_cashier = array(
                'company_id' => Auth::user()->company_id,
                'cashier_log_date' => date('Y-m-d'),
                'shift_cashier' => 2,
                'total_cash_transaction' => $total_cash_transaction - $first_cashier['total_cash_transaction'],
                'amount_cash_transaction' =>  $amount_cash_transaction - $first_cashier['amount_cash_transaction'],
                'total_receivable_transaction' => $total_receivable_transaction - $first_cashier['total_receivable_transaction'],
                'amount_receivable_transaction' => $amount_receivable_transaction - $first_cashier['amount_receivable_transaction'],
                'total_cashless_transaction' => $total_cashless_transaction - $first_cashier['total_cashless_transaction'],
                'amount_cashless_transaction' => $amount_cashless_transaction - $first_cashier['amount_cashless_transaction'],
                'total_transaction' => $total_transaction - ($first_cashier['total_cash_transaction'] + $first_cashier['total_receivable_transaction'] + $first_cashier['total_cashless_transaction']),
                'total_amount' => $total_amount - ($first_cashier['amount_cash_transaction'] + $first_cashier['amount_receivable_transaction'] + $first_cashier['amount_cashless_transaction']),
                'created_id' => Auth::id(),
                'updated_id' => Auth::id()
            );
        } else if (count($close_cashier) == 0) {
            $data_close_cashier = array(
                'company_id' => Auth::user()->company_id,
                'cashier_log_date' => date('Y-m-d'),
                'shift_cashier' => 1,
                'total_cash_transaction' => $total_cash_transaction,
                'amount_cash_transaction' => $amount_cash_transaction,
                'total_receivable_transaction' => $total_receivable_transaction,
                'amount_receivable_transaction' => $amount_receivable_transaction,
                'total_cashless_transaction' => $total_cashless_transaction,
                'amount_cashless_transaction' => $amount_cashless_transaction,
                'total_transaction' => $total_transaction,
                'total_amount' => $total_amount,
                'created_id' => Auth::id(),
                'updated_id' => Auth::id()
            );
        }

        if (CloseCashierLog::create($data_close_cashier)) {
            $msg = "Tutup Kasir Berhasil";
            return redirect('/configuration-data')->with('msg',$msg);
        } else {
            $msg = "Tutup Kasir Gagal";
            return redirect('/configuration-data')->with('msg',$msg);
        }
    }

    public function printCloseCashierConfiguration1()
    {
        $data = CloseCashierLog::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->orderBy('cashier_log_id', 'DESC')
        ->first();

        $data_company = PreferenceCompany::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->first();

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(1, 1, 1, 1); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::AddPage('P', array(48, 3276));

        $pdf::SetFont('helvetica', '', 10);

        $tbl = " 
        <table style=\" font-size:9px; \" >
            <tr>
                <td style=\"text-align: center; font-size:12px; font-weight: bold\">".$data_company['company_name']."</td>
            </tr>
            <tr>
                <td style=\"text-align: center; font-size:9px;\">".$data_company['company_address']."</td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
            
        $tblStock1 = "
        <div>---------------------------------------</div>
        <table style=\" font-size:9px; \">
            <tr>
                <td width=\"25%\">TGL.</td>
                <td width=\"10%\" style=\"text-align: center;\">:</td>
                <td width=\"60%\">".date('d-m-Y')."  ".date('H:i')."</td>
            </tr>
            <tr>
                <td width=\"25%\">SHIFT</td>
                <td width=\"10%\" style=\"text-align: center;\">:</td>
                <td>".$data['shift_cashier']."</td>
            </tr>
            <tr>
                <td width=\"25%\">KASIR</td>
                <td width=\"10%\" style=\"text-align: center;\">:</td>
                <td width=\"60%\">".Auth::user()->name."</td>
            </tr>
        </table>
        <div>---------------------------------------</div>
        ";

        $tblStock2 = "
        <table style=\" font-size:9px; \" width=\" 100% \">
            <tr>
                <td width=\" 45% \" style=\"text-align: left;\">SALDO AWAL</td>
                <td width=\" 15% \" style=\"text-align: right;\"></td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">400.000</td>
            </tr>
            <tr>
                <td width=\" 35% \" style=\"text-align: left;\">TOTAL</td>
                <td width=\" 25% \" style=\"text-align: right;\">(".$data['total_transaction'].")</td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['total_amount'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 35% \" style=\"text-align: left;\">PIUTANG</td>
                <td width=\" 25% \" style=\"text-align: right;\">(".$data['total_receivable_transaction'].")</td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['amount_receivable_transaction'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 35% \" style=\"text-align: left;\">E-WALLET</td>
                <td width=\" 25% \" style=\"text-align: right;\">(".$data['total_cashless_transaction'].")</td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['amount_cashless_transaction'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 35% \" style=\"text-align: left;\">TUNAI</td>
                <td width=\" 25% \" style=\"text-align: right;\">(".$data['total_cash_transaction'].")</td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['amount_cash_transaction'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 45% \" style=\"text-align: left;\">DISETOR</td>
                <td width=\" 15% \" style=\"text-align: right;\"></td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['amount_cash_transaction'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 45% \" style=\"text-align: left;\">SALDO AKHIR</td>
                <td width=\" 15% \" style=\"text-align: right;\"></td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">400.000</td>
            </tr>
        </table>
        <div>---------------------------------------</div>
        
        ";

        $pdf::writeHTML($tblStock1.$tblStock2, true, false, false, false, '');


        $filename = 'Tutup_Kasir.pdf';
        $pdf::Output($filename, 'I');
    }

    public function printCloseCashierConfiguration()
    {
        $data = CloseCashierLog::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->orderBy('cashier_log_id', 'DESC')
        ->first();

        $data_company = PreferenceCompany::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->first();

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(5, 1, 5, 1); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::AddPage('P', array(75, 3276));

        $pdf::SetFont('helvetica', '', 10);

        $tbl = " 
        <table style=\" font-size:9px; \" >
            <tr>
                <td style=\"text-align: center; font-size:12px; font-weight: bold\">".$data_company['company_name']."</td>
            </tr>
            <tr>
                <td style=\"text-align: center; font-size:9px;\">".$data_company['company_address']."</td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
            
        $tblStock1 = "
        <div>-------------------------------------------------------</div>
        <table style=\" font-size:9px; \">
            <tr>
                <td width=\"25%\">TGL.</td>
                <td width=\"10%\" style=\"text-align: center;\">:</td>
                <td width=\"60%\">".date('d-m-Y')."  ".date('H:i')."</td>
            </tr>
            <tr>
                <td width=\"25%\">SHIFT</td>
                <td width=\"10%\" style=\"text-align: center;\">:</td>
                <td>".$data['shift_cashier']."</td>
            </tr>
            <tr>
                <td width=\"25%\">KASIR</td>
                <td width=\"10%\" style=\"text-align: center;\">:</td>
                <td width=\"60%\">".ucfirst(Auth::user()->name)."</td>
            </tr>
        </table>
        <div>-------------------------------------------------------</div>
        ";

        $tblStock2 = "
        <table style=\" font-size:9px; \" width=\" 100% \">
            <tr>
                <td width=\" 45% \" style=\"text-align: left;\">SALDO AWAL</td>
                <td width=\" 15% \" style=\"text-align: right;\"></td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">400.000</td>
            </tr>
            <tr>
                <td width=\" 35% \" style=\"text-align: left;\">TOTAL</td>
                <td width=\" 25% \" style=\"text-align: right;\">(".$data['total_transaction'].")</td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['total_amount'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 35% \" style=\"text-align: left;\">PIUTANG</td>
                <td width=\" 25% \" style=\"text-align: right;\">(".$data['total_receivable_transaction'].")</td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['amount_receivable_transaction'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 35% \" style=\"text-align: left;\">E-WALLET</td>
                <td width=\" 25% \" style=\"text-align: right;\">(".$data['total_cashless_transaction'].")</td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['amount_cashless_transaction'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 35% \" style=\"text-align: left;\">TUNAI</td>
                <td width=\" 25% \" style=\"text-align: right;\">(".$data['total_cash_transaction'].")</td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['amount_cash_transaction'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 45% \" style=\"text-align: left;\">DISETOR</td>
                <td width=\" 15% \" style=\"text-align: right;\"></td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['amount_cash_transaction'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 45% \" style=\"text-align: left;\">SALDO AKHIR</td>
                <td width=\" 15% \" style=\"text-align: right;\"></td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">400.000</td>
            </tr>
        </table>
        <div>-------------------------------------------------------</div>
        
        ";

        $pdf::writeHTML($tblStock1.$tblStock2, true, false, false, false, '');


        $filename = 'Tutup_Kasir.pdf';
        $pdf::Output($filename, 'I');
    }

    public function backupDataConfiguration()
    {
        exec('start /B C:\xampp\htdocs\kasihibu_minimarket\backup_data.bat');

        $msg = "Data Berhasil dicadangkan";
        return redirect('/configuration-data')->with('msg', $msg);
    }
}
