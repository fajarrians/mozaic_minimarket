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
        $response = Http::get('https://ciptapro.com/kasihibu_minimarket/api/get-data');

        DB::beginTransaction();
        try {

            CoreMember::select(DB::statement('SET FOREIGN_KEY_CHECKS = 0'))->truncate();
            foreach ($response['member'] as $key => $val) {
                if ($val['company_id'] == Auth::user()->company_id) {
                    CoreMember::create($val);
                }
            }

            InvtItemCategory::select(DB::statement('SET FOREIGN_KEY_CHECKS = 0'))->truncate();
            foreach ($response['category'] as $key => $val) {
                if ($val['company_id'] == Auth::user()->company_id) {
                    InvtItemCategory::create($val);
                }
            }

            InvtItemUnit::select(DB::statement('SET FOREIGN_KEY_CHECKS = 0'))->truncate();
            foreach ($response['unit'] as $key => $val) {
                if ($val['company_id'] == Auth::user()->company_id) {
                    InvtItemUnit::create($val);
                }
            }

            InvtItemBarcode::select(DB::statement('SET FOREIGN_KEY_CHECKS = 0'))->truncate();
            foreach ($response['barcode'] as $key => $val) {
                if ($val['company_id'] == Auth::user()->company_id) {
                    InvtItemBarcode::create($val);
                }
            }

            InvtItemPackge::select(DB::statement('SET FOREIGN_KEY_CHECKS = 0'))->truncate();
            foreach ($response['packge'] as $key => $val) {
                if ($val['company_id'] == Auth::user()->company_id) {
                    InvtItemPackge::create($val);
                }
            }

            InvtWarehouse::select(DB::statement('SET FOREIGN_KEY_CHECKS = 0'))->truncate();
            foreach ($response['warehouse'] as $key => $val) {
                if ($val['company_id'] == Auth::user()->company_id) {
                    InvtWarehouse::create($val);
                }
            }

            InvtItem::select(DB::statement('SET FOREIGN_KEY_CHECKS = 0'))->truncate();
            foreach ($response['item'] as $key => $val) {
                if ($val['company_id'] == Auth::user()->company_id) {
                    InvtItem::create($val);
                }
            }

            InvtItemStock::select(DB::statement('SET FOREIGN_KEY_CHECKS = 0'))->truncate();
            foreach ($response['stock'] as $key => $val) {
                if ($val['company_id'] == Auth::user()->company_id) {
                    InvtItemStock::create($val);
                }
            }

            PreferenceVoucher::select(DB::statement('SET FOREIGN_KEY_CHECKS = 0'))->truncate();
            foreach ($response['voucher'] as $key => $val) {
                if ($val['company_id'] == Auth::user()->company_id) {
                    PreferenceVoucher::create($val);
                }
            }

            InvtItemRack::select(DB::statement('SET FOREIGN_KEY_CHECKS = 0'))->truncate();
            foreach ($response['rack'] as $key => $val) {
                if ($val['company_id'] == Auth::user()->company_id) {
                    InvtItemRack::create($val);
                }
            }

            DB::commit();
            session()->flash('msg', "Data berhasil didownload");
            return redirect('configuration-data');

        } catch (\Throwable $th) {

            DB::rollback();
            session()->flash('msg', "Data gagal didownload");
            return redirect('configuration-data');

        }
    }

    public function uploadConfigurationData()
    {
        $sales = SalesInvoice::where('status_upload',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $salesItem = SalesInvoiceItem::where('status_upload',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $member = CoreMember::where('member_account_receivable_amount_temp', '!=', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $closeCashier = CloseCashierLog::where('status_upload', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $loginLog = SystemLoginLog::where('status_upload', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $salesRemove = SIIRemoveLog::where('status_upload', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $response = Http::post('https://ciptapro.com/kasihibu_minimarket/api/post-data', [
            'sales'         => json_decode($sales, true),
            'salesItem'     => json_decode($salesItem, true),
            'member'        => json_decode($member, true),
            'closeCashier'  => json_decode($closeCashier, true),
            'loginLog'      => json_decode($loginLog, true),
            'salesRemove'   => json_decode($salesRemove, true),
        ]);

        if ($response->body() == 'true') {
            DB::beginTransaction();
            try {

                SalesInvoice::where('status_upload',0)
                ->where('company_id', Auth::user()->company_id)
                ->update([
                    'status_upload' => 1,
                    'updated_id' => Auth::id()
                ]);

                SalesInvoiceItem::where('status_upload',0)
                ->where('company_id', Auth::user()->company_id)
                ->update([
                    'status_upload' => 1,
                    'updated_id' => Auth::id()
                ]);

                CoreMember::where('member_account_receivable_amount_temp', '!=', 0)
                ->where('company_id', Auth::user()->company_id)
                ->update([
                    'member_account_receivable_amount_temp' => 0,
                ]);

                CloseCashierLog::where('status_upload', 0)
                ->where('company_id', Auth::user()->company_id)
                ->update([
                    'status_upload' => 1,
                    'updated_id' => Auth::id()
                ]);

                SystemLoginLog::where('status_upload', 0)
                ->where('company_id', Auth::user()->company_id)
                ->update([
                    'status_upload' => 1,
                ]);

                SIIRemoveLog::where('status_upload', 0)
                ->where('company_id', Auth::user()->company_id)
                ->update([
                    'status_upload' => 1,
                    'updated_id' => Auth::id()
                ]);

                DB::commit();
                $msg = "Data Berhasil diupload";
                return redirect('configuration-data')->with('msg', $msg);

            } catch (\Throwable $th) {

                DB::rollback();
                $msg = "Data Gagal diupload";
                return redirect('configuration-data')->with('msg', $msg);

            }
        } else {
            $msg = "Data Gagal diupload";
            return redirect('configuration-data')->with('msg', $msg);
        }
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
