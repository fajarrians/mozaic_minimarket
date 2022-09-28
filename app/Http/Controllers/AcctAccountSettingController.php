<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctAccount;
use App\Models\AcctAccountSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcctAccountSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
    }

    public function index()
    {

        $accountlist = AcctAccount::select(DB::raw("CONCAT(account_code,' - ',account_name) AS full_account"),'account_id')
        ->where('data_state',0)
        ->where('company_id',Auth::user()->company_id)
        ->get()
        ->pluck('full_account','account_id');
        $status = array(
            '0' => 'Debit',
            '1' => 'Kredit'
        );
        return view('content.AcctAccountSetting.AcctAccountSetting',compact('accountlist','status'));
    }

    public function processAddAcctAccountSetting(Request $request)
    {
        // dd($request->all());
        
        $data = array(
            '1_account_id'               => $request->input('purchase_cash_account_id'),
            '1_account_setting_status'   => $request->input('purchase_cash_account_status'),
            '1_account_setting_name'     => 'purchase_account',
            // '1_account_default_status'     => $this->getAccountDefault($request->input('purchase_cash_account_id')),

            '2_account_id'               => $request->input('account_cash_purchase_id'),
            '2_account_setting_status'   => $request->input('account_cash_purchase_status'),
            '2_account_setting_name'     => 'purchase_cash_account',
            // '2_account_default_status'     => $this->getAccountDefault($request->input('account_cash_purchase_id')),

            '3_account_id'               => $request->input('purchase_payment_account_id'),
            '3_account_setting_status'   => $request->input('purchase_payment_account_status'),
            '3_account_setting_name'     => 'purchase_payment_account',
            
            '4_account_id'               => $request->input('cash_purchase_payment_account_id'),
            '4_account_setting_status'   => $request->input('cash_purchase_payment_account_status'),
            '4_account_setting_name'     => 'purchase_cash_payment_account',

            '5_account_id'               => $request->input('account_payable_account_id'),
            '5_account_setting_status'   => $request->input('account_payable_account_status'),
            '5_account_setting_name'     => 'purchase_payable_account',

            '6_account_id'               => $request->input('account_payable_cash_account_id'),
            '6_account_setting_status'   => $request->input('account_payable_cash_account_status'),
            '6_account_setting_name'     => 'purchase_cash_payable_account',


            // '7_account_id'               => $request->input('account_payable_bank_account_id'),
            // '7_account_setting_status'   => $request->input('account_payable_bank_account_status'),
            // '7_account_setting_name'     => 'account_payable_bank',

            // '8_account_id'               => $request->input('bank_purchase_payment_account_id'),
            // '8_account_setting_status'   => $request->input('bank_purchase_payment_account_status'),
            // '8_account_setting_name'     => 'bank_purchase_payment',

            // '9_account_id'               => $request->input('giro_purchase_payment_account_id'),
            // '9_account_setting_status'   => $request->input('giro_purchase_payment_account_status'),
            // '9_account_setting_name'     => 'giro_purchase_payment',

            // '10_account_id'               => $request->input('account_payable_giro_account_id'),
            // '10_account_setting_status'   => $request->input('account_payable_giro_account_status'),
            // '10_account_setting_name'     => 'account_payable_giro',

            // '11_account_id'               => $request->input('giro_purchase_liquefaction_id'),
            // '11_account_setting_status'   => $request->input('giro_purchase_liquefaction_account_status'),
            // '11_account_setting_name'     => 'giro_purchase_liquefaction',

            // '12_account_id'               => $request->input('bank_purchase_liquefaction_account_id'),
            // '12_account_setting_status'   => $request->input('bank_purchase_liquefaction_account_status'),
            // '12_account_setting_name'     => 'bank_purchase_liquefaction',
            '7_account_id'               => $request->input('purchase_return_account_id'),
            '7_account_setting_status'   => $request->input('purchase_return_account_status'),
            '7_account_setting_name'     => 'purchase_return_account',
            // '3_account_default_status'     => $this->getAccountDefault($request->input('purchase_return_account_id')),

            '8_account_id'               => $request->input('account_payable_return_account_id'),
            '8_account_setting_status'   => $request->input('account_payable_return_account_status'),
            '8_account_setting_name'     => 'purchase_return_cash_account',
            // '4_account_default_status'     => $this->getAccountDefault($request->input('account_payable_return_account_id')),

            '9_account_id'               => $request->input('sales_account_id'),
            '9_account_setting_status'   => $request->input('sales_account_status'),
            '9_account_setting_name'     => 'sales_account',
            // '5_account_default_status'     => $this->getAccountDefault($request->input('sales_account_id')),

            '10_account_id'               => $request->input('cash_sales_account_id'),
            '10_account_setting_status'   => $request->input('cash_sales_account_status'),
            '10_account_setting_name'     => 'sales_cash_account',
            // '6_account_default_status'     => $this->getAccountDefault($request->input('account_receivable_account_id')),

            '11_account_id'               => $request->input('receivable_account_id'),
            '11_account_setting_status'   => $request->input('receivable_account_status'),
            '11_account_setting_name'     => 'sales_receivable_account',

            '12_account_id'               => $request->input('sales_account_receivable_id'),
            '12_account_setting_status'   => $request->input('sales_account_receivable_status'),
            '12_account_setting_name'     => 'sales_cash_receivable_account',

            '13_account_id'               => $request->input('expenditure_account_id'),
            '13_account_setting_status'   => $request->input('expenditure_account_status'),
            '13_account_setting_name'     => 'expenditure_account',
            // '7_account_default_status'     => $this->getAccountDefault($request->input('expenditure_account_id')),

            '14_account_id'               => $request->input('expenditure_cash_account_id'),
            '14_account_setting_status'   => $request->input('expenditure_cash_account_status'),
            '14_account_setting_name'     => 'expenditure_cash_account',
            // '8_account_default_status'     => $this->getAccountDefault($request->input('expenditure_cash_account_id')),


            // '19_account_id'               => $request->input('sales_discount_account_id'),
            // '19_account_setting_status'   => $request->input('sales_discount_account_status'),
            // '19_account_setting_name'     => 'account_diskon',

            // '20_account_id'               => $request->input('account_receivable_discount_account_id'),
            // '20_account_setting_status'   => $request->input('account_receivable_discount_account_status'),
            // '20_account_setting_name'     => 'account_receivable_discount',

            // '21_account_id'               => $request->input('cash_sales_collection_account_id'),
            // '21_account_setting_status'   => $request->input('cash_sales_collection_account_status'),
            // '21_account_setting_name'     => 'cash_sales_collection',

            // '22_account_id'               => $request->input('account_receivable_cash_account_id'),
            // '22_account_setting_status'   => $request->input('account_receivable_cash_account_status'),
            // '22_account_setting_name'     => 'account_receivable_cash',

            // '23_account_id'               => $request->input('bank_sales_collection_account_id'),
            // '23_account_setting_status'   => $request->input('bank_sales_collection_account_status'),
            // '23_account_setting_name'     => 'bank_sales_collection',

            // '24_account_id'               => $request->input('account_receivable_bank_account_id'),
            // '24_account_setting_status'   => $request->input('account_receivable_bank_account_status'),
            // '24_account_setting_name'     => 'account_receivable_bank',

            // '25_account_id'               => $request->input('giro_sales_collection_account_id'),
            // '25_account_setting_status'   => $request->input('giro_sales_collection_account_status'),
            // '25_account_setting_name'     => 'giro_sales_collection',

            // '26_account_id'               => $request->input('account_receivable_giro_account_id'),
            // '26_account_setting_status'   => $request->input('account_receivable_giro_account_status'),
            // '26_account_setting_name'     => 'account_receivable_giro',

            // '27_account_id'               => $request->input('bank_sales_liquefaction_account_id'),
            // '27_account_setting_status'   => $request->input('bank_sales_liquefaction_account_status'),
            // '27_account_setting_name'     => 'bank_sales_liquefaction',

            // '28_account_id'               => $request->input('giro_sales_liquefaction_account_id'),
            // '28_account_setting_status'   => $request->input('giro_sales_liquefaction_account_status'),
            // '28_account_setting_name'     => 'giro_sales_liquefaction',

            // '29_account_id'               => $request->input('sales_return_account_id'),
            // '29_account_setting_status'   => $request->input('sales_return_account_status'),
            // '29_account_setting_name'     => 'sales_return',

            // '30_account_id'               => $request->input('account_receivable_return_account_id'),
            // '30_account_setting_status'   => $request->input('account_receivable_return_account_status'),
            // '30_account_setting_name'     => 'account_receivable_return',

            // '7_account_id'               => $request->input('service_cash_account_id'),
            // '7_account_setting_status'   => $request->input('service_cash_account_status'),
            // '7_account_setting_name'     => 'account_service_cash',
            // '7_account_default_status'     => $this->getAccountDefault($request->input('service_cash_account_id')),

            // '8_account_id'               => $request->input('service_account_id'),
            // '8_account_setting_status'   => $request->input('service_account_status'),
            // '8_account_setting_name'     => 'service_cash_account',
            // '8_account_default_status'     => $this->getAccountDefault($request->input('service_account_id')),
            
        );

        $company_id = AcctAccountSetting::where('company_id', Auth::user()->company_id)->first();
        if(!empty($company_id)){
            for($key = 1; $key<=14;$key++){
                $data_item = array(
                    'account_id' 				=> $data[$key."_account_id"],
                    'account_setting_status'	=> $data[$key."_account_setting_status"],
                    'account_setting_name' 		=> $data[$key."_account_setting_name"],
                    // 'account_default_status'    => $data[$key."_account_default_status"],
                    'company_id'                => Auth::user()->company_id
                );
                AcctAccountSetting::where('account_setting_name',$data_item['account_setting_name'])
                ->where('company_id', Auth::user()->company_id)
                ->update($data_item);
            }
        } else {
            for($key = 1; $key<=14;$key++){
                $data_item = array(
                    'account_id' 				=> $data[$key."_account_id"],
                    'account_setting_status'	=> $data[$key."_account_setting_status"],
                    'account_setting_name' 		=> $data[$key."_account_setting_name"],
                    // 'account_default_status'    => $data[$key."_account_default_status"],
                    'company_id'                => Auth::user()->company_id
                );
                AcctAccountSetting::create($data_item);    
            }
        }
        $msg = 'Setting Jurnal Berhasil';
        return redirect('/acct-account-setting')->with('msg',$msg);
        
    }

    public function getAccountDefault($account_id)
    {
        $data = AcctAccount::where('account_id', $account_id)->first();

        return $data['account_default_status'];
    }

    public function getAccountId($account_setting_name)
    {
        $data = AcctAccountSetting::where('company_id', Auth::user()->company_id)->where('account_setting_name', $account_setting_name)->first();

        if(empty($data)){
            return ' ';
        } else{
            return $data['account_id'];
        }
    }

    public function getAccountSettingStatus($account_setting_name)
    {
        $data = AcctAccountSetting::where('company_id', Auth::user()->company_id)->where('account_setting_name', $account_setting_name)->first();

        return $data['account_setting_status'];
    }
}
