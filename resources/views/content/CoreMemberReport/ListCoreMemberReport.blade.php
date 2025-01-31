@inject('CMRC','App\Http\Controllers\CoreMemberReportController' )

@extends('adminlte::page')

@section('title', 'MOZAIC Minimarket')
@section('js')
<script>
    function reset_add(){
		$.ajax({
				type: "GET",
				url : "{{route('reset-filter-core-member-report')}}",
				success: function(msg){
                    location.reload();
			}

		});
	}
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
      <li class="breadcrumb-item active" aria-current="page">Laporan Piutang</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Laporan Piutang</b>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{ route('filter-core-member-report') }}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Filter
            </h5>
        </div>
    
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class = "row">
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Mulai
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" value="{{ $start_date }}" style="width: 15rem;"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" value="{{ $end_date }}" style="width: 15rem;"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onclick="reset_add();"><i class="fa fa-times"></i> Batal</button>
                    <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                </div>
            </div>
        </div>
        </div>
    </form>
</div>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div class="card border border-dark">
  <div class="card-header bg-dark clearfix">
    <h5 class="mb-0 float-left">
        Daftar
    </h5>
  </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th style='text-align:center; width: 5%'>No</th>
                        <th style='text-align:center;'>Nama Anggota</th>
                        <th style='text-align:center;'>Total Transaksi</th>
                        <th style='text-align:center;'>Total Barang</th>
                        <th style='text-align:center;'>Total Pembelian</th>
                        <th style='text-align:center;'>Total Piutang</th>
                        <th style='text-align:center;'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                  <?php $no=1; ?>
                  @foreach ($data_member as $row)
                  <tr>
                    <td class="text-center">{{ $no++ }}.</td>
                    <td>{{ $row['member_name'] }} - {{ $row['division_name'] }}</td>
                    <td style="text-align: right">{{ $CMRC->getTotalTransaction($row['member_id']) }}</td>
                    <td style="text-align: right">{{ $CMRC->getTotalItem($row['member_id']) }}</td>
                    <td style="text-align: right">{{ number_format($CMRC->getTotalAmount($row['member_id']),2,'.',',') }}</td>
                    <td style="text-align: right">{{ number_format($CMRC->getTotalCredit($row['member_id']),2,'.',',') }}</td>
                    <td class="text-center">
                        <a class="btn btn-secondary btn-sm" href="{{ url('core-member-report/print-card/'.$row['member_id']) }}"><i class="fa fa-file-pdf"></i> Kartu Piutang</a>
                    </td>
                  </tr> 
                  @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a class="btn btn-secondary" href="{{ url('core-member-report/print') }}"><i class="fa fa-file-pdf"></i> Pdf</a>
            <a class="btn btn-dark" href="{{ url('core-member-report/export') }}"><i class="fa fa-download"></i> Export Data</a>
            {{-- <p class="mt-2">
                <a class="btn btn-secondary" href="{{ url('sales-invoice-report/print-detail') }}"><i class="fa fa-file-pdf"></i> Pdf Detail</a>
            </p> --}}
        </div>
    </div>
  </div>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop

@section('js')
    
@stop   