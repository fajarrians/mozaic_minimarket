<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesInvoice;

class SalesInvoiceRecapController extends Controller
{
    public function index()
    {
        if(!Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $sales_payment_method_list = [
            1 => 'Tunai',
            2 => 'Piutang',
            3 => 'Gopay',
            4 => 'Ovo',
            5 => 'Shopeepay'
        ];
        return view('content.SalesInvoiceRecap.ListSalesInvoiceRecap',compact('start_date', 'end_date','sales_payment_method_list'));
    }

    public function filterSalesRecap(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date',$start_date);
        Session::put('end_date', $end_date);

        return redirect('/sales-invoice-recap');
    }

    public function resetFilterSalesRecap(Request $request)
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/sales-invoice-recap');
    }

    public function getAmount($key)
    {   
        if(!Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data = SalesInvoice::where('data_state',0)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->where('company_id', Auth::user()->company_id)
        ->where('sales_payment_method', $key)
        ->get();

        $amount = 0;
        foreach ($data as $key => $val) {
            $amount += $val['total_amount'];
        }

        return $amount;
    }

    public function printSalesRecap()
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
        $data = [
            1 => 'Tunai',
            2 => 'Piutang',
            3 => 'Gopay',
            4 => 'Ovo',
            5 => 'Shopeepay'
        ];

        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(10, 10, 10, 10); // put space of 10 on top

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">RECAP PENJUALAN</div></td>
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
                <td width=\"10%\"><div style=\"text-align: center; font-weight: bold\">No</div></td>
                <td width=\"45%\"><div style=\"text-align: center; font-weight: bold\">Metode Pembayaran</div></td>
                <td width=\"45%\"><div style=\"text-align: center; font-weight: bold\">Total</div></td>

            </tr>
        
             ";

        $no = 1;
        $tblStock2 =" ";
        foreach ($data as $key => $val) {

            $tblStock2 .="
                <tr>			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">".$val."</td>
                    <td style=\"text-align:right\">".number_format($this->getAmount($key),2,'.',',')."</td>
                </tr>
                
            ";
            $no++;
        }
        $tblStock3 = " 
        </table>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td style=\"text-align:right\">".Auth::user()->name.", ".date('d-m-Y H:i')."</td>
            </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        $filename = 'Rekap_Penjualan_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportSalesRecap()
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

        $data = [
            1 => 'Tunai',
            2 => 'Piutang',
            3 => 'Gopay',
            4 => 'Ovo',
            5 => 'Shopeepay'
        ];

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("CST MOZAIQ POS")
                                        ->setLastModifiedBy("CST MOZAIQ POS")
                                        ->setTitle("Recap Penjualan")
                                        ->setSubject("")
                                        ->setDescription("Recap Penjualan")
                                        ->setKeywords("Recap, Penjualan")
                                        ->setCategory("Recap Penjualan");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(40);

    
            $spreadsheet->getActiveSheet()->mergeCells("B1:D1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B3:D3')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('B3:D3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:D3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Recap Penjualan Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Metode Pembayaran");
            $sheet->setCellValue('D3',"Total");
            
            $j=4;
            $no=0;
            $totalitem = 0;
            $totalamount = 0;
            
            foreach($data as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Recap Penjualan");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':D'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    $no++;
                    $sheet->setCellValue('B'.$j, $no);
                    $sheet->setCellValue('C'.$j, $val);
                    $sheet->setCellValue('D'.$j, $this->getAmount($key));

                }else{
                    continue;
                }
                $j++;
            }

            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':D'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
            
            $filename='Recap_Penjualan_'.$start_date.'_s.d._'.$end_date.'.xls';
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
