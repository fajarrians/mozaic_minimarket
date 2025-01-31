<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ConsolidatedReceiptsReportController extends Controller
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

        $sales_invoice_mi = curl_init();
        curl_setopt($sales_invoice_mi, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-sales-invoice');
        curl_setopt($sales_invoice_mi, CURLOPT_RETURNTRANSFER, true);
        $response_sales_invoice_mi = curl_exec($sales_invoice_mi);
        $result_sales_invoice_mi = json_decode($response_sales_invoice_mi,TRUE);
        curl_close($sales_invoice_mi);

        $data_sales_invoice = [];
        if (!empty($result_sales_invoice_mi)) {
            for ($i=0; $i < count($result_sales_invoice_mi) ; $i++) { 
                if (($result_sales_invoice_mi[$i]['sales_invoice_date'] >= $start_date) && ($result_sales_invoice_mi[$i]['sales_invoice_date'] <= $end_date)) {
                    $result_sales_invoice_mi[$i]['updated_at'] = "Minimarket";
                    array_push($data_sales_invoice, $result_sales_invoice_mi[$i]);
                }
            }
        }

        $sales_invoice_mo = curl_init();
        curl_setopt($sales_invoice_mo, CURLOPT_URL,'https://ciptapro.com/kasihibu_mozaic/api/get-data-sales-invoice');
        curl_setopt($sales_invoice_mo, CURLOPT_RETURNTRANSFER, true);
        $response_sales_invoice_mo = curl_exec($sales_invoice_mo);
        $result_sales_invoice_mo = json_decode($response_sales_invoice_mo,TRUE);
        curl_close($sales_invoice_mo);

        if (!empty($result_sales_invoice_mo)){
            for ($i=0; $i < count($result_sales_invoice_mo) ; $i++) { 
                if (($result_sales_invoice_mo[$i]['sales_invoice_date'] >= $start_date) && ($result_sales_invoice_mo[$i]['sales_invoice_date'] <= $end_date)) {
                    $result_sales_invoice_mo[$i]['updated_at'] = "Mozaic";
                    array_push($data_sales_invoice, $result_sales_invoice_mo[$i]);
                } 
            }
        }

        // $data_sales_invoice = array_merge($result_sales_invoice_mi, $result_sales_invoice_mo);
        // dd($data_sales_invoice);
        

        return view('content.ConsolidatedReceiptsReport.ListConsolidatedReceiptsReport',compact('data_sales_invoice','start_date','end_date'));
    }

    public function filterConsolidatedReceiptsReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/consolidated-receipts-report');
    }

    public function resetFilterConsolidatedReceiptsReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/consolidated-receipts-report');
    }

    public function printConsolidatedReceiptsReport()
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

        $sales_invoice_mi = curl_init();
        curl_setopt($sales_invoice_mi, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-sales-invoice');
        curl_setopt($sales_invoice_mi, CURLOPT_RETURNTRANSFER, true);
        $response_sales_invoice_mi = curl_exec($sales_invoice_mi);
        $result_sales_invoice_mi = json_decode($response_sales_invoice_mi,TRUE);
        curl_close($sales_invoice_mi);

        $data_sales_invoice = [];
        if (!empty($result_sales_invoice_mi)) {
            for ($i=0; $i < count($result_sales_invoice_mi) ; $i++) { 
                if (($result_sales_invoice_mi[$i]['sales_invoice_date'] >= $start_date) && ($result_sales_invoice_mi[$i]['sales_invoice_date'] <= $end_date)) {
                    $result_sales_invoice_mi[$i]['updated_at'] = "Minimarket";
                    array_push($data_sales_invoice, $result_sales_invoice_mi[$i]);
                }
            }
        }

        $sales_invoice_mo = curl_init();
        curl_setopt($sales_invoice_mo, CURLOPT_URL,'https://ciptapro.com/kasihibu_mozaic/api/get-data-sales-invoice');
        curl_setopt($sales_invoice_mo, CURLOPT_RETURNTRANSFER, true);
        $response_sales_invoice_mo = curl_exec($sales_invoice_mo);
        $result_sales_invoice_mo = json_decode($response_sales_invoice_mo,TRUE);
        curl_close($sales_invoice_mo);

        if (!empty($result_sales_invoice_mo)) {
            for ($i=0; $i < count($result_sales_invoice_mo) ; $i++) { 
                if (($result_sales_invoice_mo[$i]['sales_invoice_date'] >= $start_date) && ($result_sales_invoice_mo[$i]['sales_invoice_date'] <= $end_date)) {
                    $result_sales_invoice_mo[$i]['updated_at'] = "Mozaic";
                    array_push($data_sales_invoice, $result_sales_invoice_mo[$i]);
                } 
            }
        }

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN KONSOLIDASI PENERIMAAN KAS</div></td>
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
                <td width=\"5%\" ><div style=\"text-align: center;  font-weight: bold\">No</div></td>
                <td width=\"23%\" ><div style=\"text-align: center;  font-weight: bold\">Sumber</div></td>
                <td width=\"26%\" ><div style=\"text-align: center;  font-weight: bold\">Keterangan</div></td>
                <td width=\"23%\" ><div style=\"text-align: center;  font-weight: bold\">Tanggal</div></td>
                <td width=\"23%\" ><div style=\"text-align: center;  font-weight: bold\">Nominal</div></td>
            </tr>
        
             ";

        $no = 1;
        $total_amount = 0;
        $tblStock2 =" ";
        foreach ($data_sales_invoice as $key => $val) {
            $tblStock2 .="
                <tr>			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">".$val['updated_at']."</td>
                    <td style=\"text-align:left\">Penjualan Produk</td>
                    <td style=\"text-align:left\">".date('d-m-Y', strtotime($val['sales_invoice_date']))."</td>
                    <td style=\"text-align:right\">".number_format($val['total_amount'],2,'.',',')."</td>
                </tr>
                
            ";
            $total_amount += $val['total_amount'];
            $no++;
        }
        $tblStock3 = " 
        <tr>
            <td colspan=\"4\"><div style=\"text-align: center;  font-weight: bold\">TOTAL</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($total_amount,2,'.',',') ."</div></td>
        </tr>
        </table>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td style=\"text-align:right\">".Auth::user()->name.", ".date('d-m-Y H:i')."</td>
            </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');


        $filename = 'Laporan_Konsolidasi_Penerimaan_kas_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportConsolidatedReceiptsReport()
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

        $sales_invoice_mi = curl_init();
        curl_setopt($sales_invoice_mi, CURLOPT_URL,'https://ciptapro.com/kasihibu_minimarket/api/get-data-sales-invoice');
        curl_setopt($sales_invoice_mi, CURLOPT_RETURNTRANSFER, true);
        $response_sales_invoice_mi = curl_exec($sales_invoice_mi);
        $result_sales_invoice_mi = json_decode($response_sales_invoice_mi,TRUE);
        curl_close($sales_invoice_mi);

        $data_sales_invoice = [];
        if (!empty($result_sales_invoice_mi)) {
            for ($i=0; $i < count($result_sales_invoice_mi) ; $i++) { 
                if (($result_sales_invoice_mi[$i]['sales_invoice_date'] >= $start_date) && ($result_sales_invoice_mi[$i]['sales_invoice_date'] <= $end_date)) {
                    $result_sales_invoice_mi[$i]['updated_at'] = "Minimarket";
                    array_push($data_sales_invoice, $result_sales_invoice_mi[$i]);
                }
            }
        }

        $sales_invoice_mo = curl_init();
        curl_setopt($sales_invoice_mo, CURLOPT_URL,'https://ciptapro.com/kasihibu_mozaic/api/get-data-sales-invoice');
        curl_setopt($sales_invoice_mo, CURLOPT_RETURNTRANSFER, true);
        $response_sales_invoice_mo = curl_exec($sales_invoice_mo);
        $result_sales_invoice_mo = json_decode($response_sales_invoice_mo,TRUE);
        curl_close($sales_invoice_mo);

        if (!empty($result_sales_invoice_mo)) {
            for ($i=0; $i < count($result_sales_invoice_mo) ; $i++) { 
                if (($result_sales_invoice_mo[$i]['sales_invoice_date'] >= $start_date) && ($result_sales_invoice_mo[$i]['sales_invoice_date'] <= $end_date)) {
                    $result_sales_invoice_mo[$i]['updated_at'] = "Mozaic";
                    array_push($data_sales_invoice, $result_sales_invoice_mo[$i]);
                } 
            }
        }

        $spreadsheet = new Spreadsheet();

        if(count($data_sales_invoice)>=0){
            $spreadsheet->getProperties()->setCreator("MOZAIC")
                                        ->setLastModifiedBy("MOZAIC")
                                        ->setTitle("Cash Konsolidasi Receipts Report")
                                        ->setSubject("")
                                        ->setDescription("Cash Konsolidasi Receipts Report")
                                        ->setKeywords("Cash, Konsolidasi, Receipts, Report")
                                        ->setCategory("Cash Konsolidasi Receipts Report");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(25);
    
            $spreadsheet->getActiveSheet()->mergeCells("B1:F1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Konsolidasi Penerimaan Kas Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Sumber");
            $sheet->setCellValue('D3',"Keterangan");
            $sheet->setCellValue('E3',"Tanggal");
            $sheet->setCellValue('F3',"Nominal"); 
            
            $j=4;
            $no=0;
            $total_amount = 0;
            
            foreach($data_sales_invoice as $key=>$val){

                $sheet = $spreadsheet->getActiveSheet(0);
                $spreadsheet->getActiveSheet()->setTitle("Laporan Penerimaan Kas");
                $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
        
                $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);



                $no++;
                $sheet->setCellValue('B'.$j, $no);
                $sheet->setCellValue('C'.$j, $val['updated_at']);
                $sheet->setCellValue('D'.$j, 'Penjualan Produk');
                $sheet->setCellValue('E'.$j, date('d-m-Y', strtotime($val['sales_invoice_date'])));
                $sheet->setCellValue('F'.$j, $val['total_amount']);

                $j++;
                $total_amount += $val['total_amount'];
        
            }
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':E'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, 'TOTAL');
            $sheet->setCellValue('F'.$j, $total_amount);

            $j++;
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':F'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
            
            $filename='Laporan_Konsolidasi_Penerimaan_Kas_'.$start_date.'_s.d._'.$end_date.'.xls';
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
