<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreMember;
use App\Models\SalesInvoice;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class CoreMemberReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data_member = CoreMember::select('member_name','member_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        return view('content.CoreMemberReport.ListCoreMemberReport', compact('data_member','start_date','end_date'));
    }

    public function filterCoreMemberReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date',$start_date);
        Session::put('end_date', $end_date);

        return redirect('/core-member-report');
    }

    public function resetFilterCoreMemberReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/core-member-report');
    }

    public function getTotalTransaction($member_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data_sales = SalesInvoice::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('customer_id', $member_id)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->get();

        return count($data_sales);
    }

    public function getTotalItem($member_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data_sales = SalesInvoice::select('subtotal_item')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('customer_id', $member_id)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->get();

        $total_item = 0;
        foreach($data_sales as $key=>$val) {
            $total_item += $val['subtotal_item'];
        }

        return $total_item;
    }

    public function getTotalAmount($member_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data_sales = SalesInvoice::select('total_amount')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('customer_id', $member_id)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->get();;

        $total_amount = 0;
        foreach($data_sales as $key=>$val) {
            $total_amount += $val['total_amount'];
        }

        return $total_amount;
    }

    public function printCoreMemberReport()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data_member = CoreMember::select('member_name','member_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf::setHeaderCallback(function($pdf){
            $pdf->SetFont('helvetica', '', 8);
            $header = "
            <div></div>
                <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                    <tr>
                        <td rowspan=\"3\" width=\"76%\"><img src=\"".asset('resources/assets/img/logo_kopkar.png')."\" width=\"120\"></td>
                        <td width=\"10%\"><div style=\"text-align: left;\">Halaman</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".$pdf->getAliasNumPage()." / ".$pdf->getAliasNbPages()."</div></td>
                    </tr>  
                    <tr>
                        <td width=\"10%\"><div style=\"text-align: left;\">Dicetak</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".ucfirst(Auth::user()->name)."</div></td>
                    </tr>
                    <tr>
                        <td width=\"10%\"><div style=\"text-align: left;\">Tgl. Cetak</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".date('d-m-Y H:i')."</div></td>
                    </tr>
                </table>
                <hr>
            ";

            $pdf->writeHTML($header, true, false, false, false, '');
        });
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(10, 20, 10, 10); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 8);

        $tbl = "
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PEMBELIAN ANGGOTA</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $no = 1;
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\"><div style=\"text-align: center; font-weight: bold\">No</div></td>
                <td width=\"24%\"><div style=\"text-align: center; font-weight: bold\">Nama Anggota</div></td>
                <td width=\"23%\"><div style=\"text-align: center; font-weight: bold\">Total Transaksi</div></td>
                <td width=\"24%\"><div style=\"text-align: center; font-weight: bold\">Total Barang</div></td>
                <td width=\"24%\"><div style=\"text-align: center; font-weight: bold\">Total Pembelian</div></td>

            </tr>
        
             ";

        $no = 1;
        $TotalTransaction = 0;
        $TotalItem = 0;
        $TotalAmount = 0;
        $tblStock2 =" ";
        foreach ($data_member as $key => $val) {

            $tblStock2 .="
                <tr nobr=\"true\">			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">".$val['member_name']."</td>
                    <td style=\"text-align:right\">".$this->getTotalTransaction($val['member_id'])."</td>
                    <td style=\"text-align:right\">".$this->getTotalItem($val['member_id'])."</td>
                    <td style=\"text-align:right\">".number_format($this->getTotalAmount($val['member_id']),2,'.',',')."</td>
                </tr>
                
            ";
            $no++;
            $TotalTransaction += $this->getTotalTransaction($val['member_id']);
            $TotalItem += $this->getTotalItem($val['member_id']);
            $TotalAmount += $this->getTotalAmount($val['member_id']);
        }
        $tblStock3 = " 
        <tr nobr=\"true\">
            <td colspan=\"2\"><div style=\"text-align: center;  font-weight: bold\">TOTAL</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". $TotalTransaction ."</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". $TotalItem ."</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($TotalAmount,2,'.',',') ."</div></td>
        </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        $filename = 'Laporan_Pembelian_Aanggota_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportCoreMemberReport()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data_member = CoreMember::select('member_name','member_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $spreadsheet = new Spreadsheet();

        if(count($data_member)>=0){
            $spreadsheet->getProperties()->setCreator("CST MOZAIQ POS")
                                        ->setLastModifiedBy("CST MOZAIQ POS")
                                        ->setTitle("Laporan Pembelian Anggota")
                                        ->setSubject("")
                                        ->setDescription("Laporan Pembelian Anggota")
                                        ->setKeywords("Laporan, Pembelian, Anggota")
                                        ->setCategory("Laporan Pembelian Anggota");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);

    
            $spreadsheet->getActiveSheet()->mergeCells("B1:F1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Pembelian Anggota Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Nama Anggota");
            $sheet->setCellValue('D3',"Total Transaksi");
            $sheet->setCellValue('E3',"Total Barang");
            $sheet->setCellValue('F3',"Total Pembelian");
            
            $j=4;
            $no=0;
            $TotalTransaction = 0;
            $TotalItem = 0;
            $TotalAmount = 0;
            foreach($data_member as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Pembelian Anggota");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $val['member_name']);
                        $sheet->setCellValue('D'.$j, $this->getTotalTransaction($val['member_id']));
                        $sheet->setCellValue('E'.$j, $this->getTotalItem($val['member_id']));
                        $sheet->setCellValue('F'.$j, $this->getTotalAmount($val['member_id']));

                }else{
                    continue;
                }
                $j++;
                $TotalTransaction += $this->getTotalTransaction($val['member_id']);
                $TotalItem += $this->getTotalItem($val['member_id']);
                $TotalAmount += $this->getTotalAmount($val['member_id']);
        
            }
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':C'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, 'TOTAL');
            $sheet->setCellValue('D'.$j, $TotalTransaction);
            $sheet->setCellValue('E'.$j, $TotalItem);
            $sheet->setCellValue('F'.$j, $TotalAmount);

            $j++;
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':F'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
            
            $filename='Laporan_Pembelian_Anggota_'.$start_date.'_s.d._'.$end_date.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        }else{
            echo "Maaf data yang di eksport tidak ada !";
        }
    }
}
