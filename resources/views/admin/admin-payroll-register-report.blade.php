@extends('layout.adminweb')
@section('content')

<style>
#style-2::-webkit-scrollbar-track
{
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    background-color: #F5F5F5;
}

#style-2::-webkit-scrollbar
{
    width: 10px;
    background-color: #F5F5F5;
}

#style-2::-webkit-scrollbar-thumb
{
    background-color: #f68c1f;
}

.select2-container{
    z-index: 99999999999;
}
.Error-Level {
    background: #ffcccc;
}
.Dupli-Level {
    background: #ccffcc;
}

.Normal-Level {
   background: #fff;
}

nav > .nav.nav-tabs{

  border: none;
    color:#fff;    
    border-radius:0;
    margin-bottom: initial ;

}
nav > div a.nav-item.nav-link,
nav > div a.nav-item.nav-link.active
{
  border: none;
    color:#fff;
    background:#475F7B;
    border-radius:0;
}
.nav.nav-tabs .nav-item, .nav.nav-pills .nav-item{
  margin-right: initial;
  padding-bottom:0px;
  margin-bottom: 0px;
}
nav > div a.nav-item.nav-link.active:after
 {
  content: "";
  position: relative;
  bottom: -38px;
  left: -10%;
  border-top-color: #f68c1f ;
}
.tab-content{
line-height: 25px;
border: 0px solid #ddd;
border-top:5px solid #f68c1f;
border-bottom:1px solid #eee;
border-left:1px solid #eee;
border-right:1px solid #eee;
}

nav > div a.nav-item.nav-link:hover,
nav > div a.nav-item.nav-link:focus
{
  border: none;
    background: #f68c1f;
    color:#fff;
    border-radius:0;
    transition:background 0.20s linear;
}

.highlighted {
    background: #ffffcc !important;
}

</style>

<style type="text/css">
        

      table.alt-background th {
            background-color: #475F7B;
            position: sticky !important;
            top: 0;
            color: white;
        }
        
     
      table.alt-background th:nth-child(1),  table.alt-background td:nth-child(1) {
            width: 125px;
            position: sticky;
            left: -7px;
            z-index: 4;
            border-left: 1px solid #ddd ;
        }
        

       table.alt-background th:nth-child(2),  table.alt-background td:nth-child(2) {
           width: 160px;
            position: sticky;
            left: 110px; 
            z-index: 1;
            border-right: 1px solid #ddd ;
        }

     
      table.alt-background  th:nth-child(1) {
            z-index: 2;
        }
        
      table.alt-background th:nth-child(1), table.alt-background  th:nth-child(2) {
            z-index: 2;
        }
        
        table.alt-background tr:nth-child(odd) td {
            background-color: #f5f5f5;
            border: 1px solid #ddd !important;
        }
        
        table.alt-background tr:nth-child(even) td {
            background-color: white;
            border: 1px solid #ddd !important;
        }
        
        table.alt-background tr.selected td {
            background-color: #ffffcc !important; 
            color: black !important;
        }
        
    </style>

<!--excel--->
<script src="{{ URL::to('public/admin/excel/xlsx.full.min.js') }}"></script>
<script src="{{ URL::to('public/admin/excel/FileSaver.js') }}"></script>


    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2 mt-1">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb p-0 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="javascript:void(0);">
                                            <i class="bx bx-home-alt"></i>
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active"> Payroll Register Report
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <section id="basic-input">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title"> Payroll Register Report </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">

                                         <div class="row">
                                            <div class="col-md-12">
                                                <fieldset>
                                                    <div class="input-group">

                                                       <input id="PageBatchNo" type="hidden" value="1">
                                                       <div class="col-md-2">
                                                        <label for="SearchPayrollPeriodCode">Payroll Period: </label>
                                                             <select id="SearchPayrollPeriodCode" class="form-control">
                                                              <!--   <option value="">Please Select</option> -->
                                                                @foreach($PayrollPeriodList as $prdrow)
                                                                <option value="{{ $prdrow->ID }}" {{ ($prdrow->ID == Session('ADMIN_PAYROLL_PERIOD_SCHED_ID') ? 'selected' : '' ) }}>{{ $prdrow->Code.' : '.$prdrow->StartDateFormat.' - '.$prdrow->EndDateFormat }}</option>
                                                                @endforeach
                                                            </select>        
                                                      </div>         
   
                                                        <div class="col-md-2">
                                                            <fieldset class="form-group">
                                                                <label for="GenerateFilter">Filter By: </label>
                                                                    <select id="GenerateFilter" class="form-control">
                                                                        <option value="">All</option>
                                                                        <option value="Location">Location</option>
                                                                        <option value="Site">Site</option>
                                                                        <option value="Division">Division</option>
                                                                        <option value="Department">Department</option>
                                                                        <option value="Section">Team Leader</option>
                                                                        <option value="Job Type">Job Type</option>
                                                                        <option value="Employee">Employee</option>
                                                                    </select>
                                                            </fieldset>
                                                        </div>

                                                            <div id="divFilters" class="col-md-4" style="display: none;">
                                                                <fieldset class="form-group">
                                                                    <label id="GeneratePayrollFilterLabel">Location: <span class="required_field">* </span></label>
                                                                    <span id='spnTypeSearch' class="search-txt">(Type & search from the list)</span>
                                                                    <div id="divLocation" class="div-percent">
                                                                        <select id="GeneratePayrollBranch" class="form-control select2" multiple="multiple">
                                                                            @foreach($BranchList as $brow)
                                                                            <option value="{{ $brow->ID }}">{{ $brow->BranchName }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                    <div id="divSite" class="div-percent" style="display:none;">
                                                                        <select id="GeneratePayrollSite" class="form-control select2" multiple="multiple">
                                                                            @foreach($BranchSite as $siterow)
                                                                            <option value="{{ $siterow->ID }}">{{ $siterow->SiteName }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                    <div id="divDivision" class="div-percent" style="display:none;">
                                                                        <select id="GeneratePayrollDivision" class="form-control select2" multiple="multiple">
                                                                            @foreach($DivisionList as $divrow)
                                                                            <option value="{{ $divrow->ID }}">{{ $divrow->Division }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                    <div id="divDepartment" class="div-percent" style="display:none;">
                                                                        <select id="GeneratePayrollDepartment" class="form-control select2" multiple="multiple">
                                                                            @foreach($DepartmentList as $deptrow)
                                                                            <option value="{{ $deptrow->ID }}">{{ $deptrow->Department }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                    <div id="divSection" class="div-percent" style="display:none;">
                                                                        <select id="GeneratePayrollSection" class="form-control select2" multiple="multiple">
                                                                            @foreach($SectionList as $secrow)
                                                                            <option value="{{ $secrow->ID }}">{{ $secrow->Section }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                    <div id="divJobType" class="div-percent" style="display:none;">
                                                                        <select id="GeneratePayrollJobType" class="form-control select2" multiple="multiple">
                                                                            @foreach($JobTypeList as $jobrow)
                                                                            <option value="{{ $jobrow->ID }}">{{ $jobrow->JobTitle }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                    <div id="divEmployee" class="div-percent" style="display:none;">
                                                                        <select id="GeneratePayrollEmployee" class="form-control select2">
                                                                            <option value="">Please Select</option>
                                                                            @foreach($EmployeeList as $emp)
                                                                            <option value="{{ $emp->employee_id }}">{{ $emp->employee_number.' - '.$emp->FullName }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                </fieldset>
                                                            </div>


                                                         <div class="col-md-2">
                                                            <fieldset class="form-group">
                                                                <label for="Status">Filter Status: </label>
                                                                    <select id="Status" class="form-control">                                                                        
                                                                        <option value="Approved">Posted</option>
                                                                        <option value="Pending">Un-Posted</option>                                                                                                                                           
                                                                    </select>
                                                            </fieldset>
                                                        </div>
               
        
                                                       <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border" tooltip="Search Here" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;margin-top: 23px;margin-left: -5px;">
                                                            <i class="bx bx-search"></i>
                                                        </button>

                                                     @if(Session::get('IS_SUPER_ADMIN') || $Allow_View_Print_Export==1)   

                                                     <!--    <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="Print()">
                                                            <i class="bx bx-printer"></i> Print
                                                        </button> -->

                                                          <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="GenerateExcel()" tooltip="Excel Report" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;margin-top: 23px;margin-left: -13px;">
                                                           <i class="bx bx-file"></i>
                                                        </button>

                                                        <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="Print()" tooltip="Print Report" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;margin-top: 23px;margin-left: -13px;">
                                                           <i class="bx bx-printer"></i>
                                                        </button>

                                                      @endif  
                                    
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                        <div id="style-2" class="table-responsive col-md-12 table_default_height">
                                            <table id="tblList" class="table zero-configuration complex-headers border alt-background">
                                                <thead>
                                                     <th></th>                                 
                                                        <th style="min-width: 110px;" class="fixed-column">EMPLOYEE ID</th>                                              
                                                        <th style="min-width: 120px;" class="fixed-column">EMPLOYEE NAME</th>                                              
                                                        <th style="min-width: 120px;" class="fixed-column">TEAM LEADER</th>                                              
                                                        <th style="min-width: 120px;" class="fixed-column">POSITION</th>                                                
                                                        <th style="min-width: 120px;" class="fixed-column">RATE</th>   
                                                        <th style="min-width: 60px;" class="fixed-column">NO OF DAYS</th>                                        
                                                        <th style="min-width: 110px;" class="fixed-column">BASIC PAY</th>
                                                        <th style="min-width: 110px;">E-COLA</th>
                                                        <th style="min-width: 60px;" class="fixed-column">LATE</th>
                                                        <th style="min-width: 110px;" class="fixed-column">UNDER TIME</th>
                                                        <th style="min-width: 110px;" class="fixed-column">ABSENT</th>
                                                        <th style="min-width: 60px;" class="fixed-column">SL </th>
                                                        <th style="min-width: 60px;" class="fixed-column">VL</th>
                                                        <th style="min-width: 60px;">OL</th>
                                                        <th style="min-width: 80px;" class="fixed-column">NIGHT DIFF</th>
                                                        <th style="min-width: 110px;" class="fixed-column">OVERTIME PAY</th>
                                                        <th style="min-width: 110px;">LEGAL HOLIDAY</th>
                                                        <th style="min-width: 110px;">SPECIAL HOLIDAY</th>
                                                        <th style="min-width: 110px;">RDD PAY</th>
                                                        <th style="min-width: 110px;">ND OT</th>
                                                        {{-- <th style="min-width: 130px;">OTHER TAXABLE EARNING </th>  --}}
                                                        <th id="otherEarningsMarker" style="min-width: 130px;">OTHER NON TAXABLE EARNING </th> 
                                                        <th style="min-width: 110px;" class="fixed-column">GROSS PAY</th>
                                                        <th style="min-width: 60px;">SSS</th>
                                                        <th style="min-width: 80px;">PHIL HEALTH</th>
                                                        <th style="min-width: 80px;">PAG IBIG</th>
                                                        <th style="min-width: 80px;">PAG IBIG MP2</th>                                                        
                                                        {{-- <th style="min-width: 110px;" class="fixed-column">TAXABLE INCOME</th> --}}
                                                        <th style="min-width: 60px;" class="fixed-column">WTAX</th>
                                                        <th style="min-width: 110px;">SSS SALARY LOAN</th>
                                                        <th style="min-width: 110px;">SSS CALAMITY LOAN</th>
                                                        <th style="min-width: 110px;">HDMF LOAN</th>
                                                        <th style="min-width: 110px;">HDMF CALAMITY LOAN</th>
                                                        <th style="min-width: 110px;">OTHER LOAN</th>
                                                        <th style="min-width: 110px;">OTHER DEDUCTION</th>
                                                        <th style="min-width: 110px;">TOTAL DEDUCTION</th>
                                                        <th style="min-width: 80px;" class="fixed-column">TOTAL NETPAY</th>
                                                        <th style="min-width: 60px;" class="fixed-column">STATUS</th>
                                                   </thead>
                                                  <tbody>
                                                </tbody>
                                                <tfoot>
                                                        <tr>
                                                            <th></th>
                                                            <th colspan="5" class="fixed-column">TOTAL</th>
                                                            <th id="ftDays" title="Days" class="fixed-column"></th>
                                                            <th id="ftBasicPay" title="Basic Pay" class="fixed-column"></th>
                                                            <th id="ftECOLA" title="E-Cola"></th>
                                                            <th id="ftLate" title="Late" class="fixed-column"></th>
                                                            <th id="ftUndertime" title="Undertime" class="fixed-column"></th>
                                                            <th id="ftAbsent" title="Absent" class="fixed-column"></th>
                                                            <th id="ftSL" title="SL" class="fixed-column"></th>
                                                            <th id="ftVL" title="VL" class="fixed-column"></th>
                                                            <th id="ftOL" title="OL"></th>
                                                            <th id="ftNightDiff" title="Night Diff" class="fixed-column"></th>
                                                            <th id="ftOTPay" title="Overtime Pay" class="fixed-column"></th>
                                                            <th id="ftLH" title="Legal Holiday"></th>
                                                            <th id="ftSH" title="Special Holiday"></th>
                                                            <th id="ftRDDPay" title="RDD Pay"></th>
                                                            <th id="ftOTND" title="ND OT"></th>
                                                            {{-- <th id="ftOtherTaxable" title="Other Taxable"></th> --}}
                                                            <th id="ftOtherNonTaxable" title="Other Non Taxable"></th>
                                                            <th id="ftGrossPay" title="Gross Pay" class="fixed-column"></th>
                                                            <th id="ftSSS" title="SSS"></th>
                                                            <th id="ftPHIC" title="Philhealth"></th>
                                                            <th id="ftHDMF" title="HDMF"></th>
                                                            <th id="ftHDMFMP2" title="HDMF MP2"></th>
                                                            {{-- <th id="ftTaxableIncome" title="Taxable Income" class="fixed-column"></th> --}}
                                                            <th id="ftWTax" title="WTax" class="fixed-column"></th>
                                                            <th id="ftSSSSalaryLoan" title="SSS Salary Loan"></th>
                                                            <th id="ftSSSCalamityLoan" title="SSS Calamity Loan"></th>
                                                            <th id="ftHDMFLoan" title="HDMF Loan"></th>
                                                            <th id="ftHDMFCalamityLoan" title="HDMF Calamity Loan"></th>
                                                            <th id="ftOtherLoan" title="Other Loan"></th>
                                                            <th id="ftOtherDeduction" title="Other Deduction"></th>
                                                            <th id="ftTotalDeduction" title="Total Deduction"></th>
                                                            <th id="ftNetPay" title="Net Pay" class="fixed-column"></th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                            </table>
                                        </div>

                                           <div id="divPaging" class="col-md-11" style="display: none;">   
                                         <hr style="margin-top:0px;margin-bottom:0px;">   
                                <div style="width:110%;font-size: 11px;">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                      <ul class="pagination ul-paging scrollbar" style="overflow-x: auto;"></ul>
                                     </div>
                                    </div>
                              </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <!-- END: Content-->


</section>

<script type="text/javascript">

    var IsAdmin="{{Session::get('IS_SUPER_ADMIN')}}";

    var IsAllowPrint="{{$Allow_View_Print_Export}}";
    var IsAllowView="{{$Allow_View_Print_Export}}";

    var IsAllowEdit="{{$Allow_Edit_Update}}";
    var IsAllowCancel="{{$Allow_Delete_Cancel}}";
    var IsAllowApprove="{{$Allow_Post_UnPost_Approve_UnApprove}}";

    var total_rec=0;
    var intCurrentPage = 1;
    var isPageFirstLoad = true;

    $(document).ready(function() {

        $('#tblList').DataTable( {
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ],
            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false,
            "order": [[ 2, "asc" ]]
        });

        //Load Initial Data
        $("#tblList").DataTable().clear().draw();
        //getRecordList(intCurrentPage);
        isPageFirstLoad = false;

    });

    $("#selSearchStatus").change(function(){
        $("#tblList").DataTable().clear().draw();
        intCurrentPage = 1;
        getRecordList(1, $('.searchtext').val());
    });

    $("#btnSearch").click(function(){
        $("#tblList").DataTable().clear().draw();
        intCurrentPage = 1;
        getRecordList(1, $('.searchtext').val());
    });

    $('.searchtext').on('keypress', function (e) {
        if(e.which === 13){
            $("#tblList").DataTable().clear().draw();
            intCurrentPage = 1;
            getRecordList(1, $('.searchtext').val());
        }
    });

    function Print(){

        var form = $('<form>', {
            method: 'POST',
            action: "{{ route('payroll-register-print') }}",
            target: '_blank'
        });

        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'PayrollPeriodID',
            value: $("#SearchPayrollPeriodCode").val()
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'FilterType',
            value: $("#GenerateFilter").val()
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'BranchID',
            value: ($("#GeneratePayrollBranch").val() || []).join(',')
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'SiteID',
            value: ($("#GeneratePayrollSite").val() || []).join(',')
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'DivisionID',
            value: ($("#GeneratePayrollDivision").val() || []).join(',')
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'DepartmentID',
            value: ($("#GeneratePayrollDepartment").val() || []).join(',')
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'SectionID',
            value: ($("#GeneratePayrollSection").val() || []).join(',')
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'JobTypeID',
            value: ($("#GeneratePayrollJobType").val() || []).join(',')
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'EmployeeID',
            value: $("#GeneratePayrollEmployee").val()
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'Status',
            value: $("#Status").val()
        }));

        $('body').append(form);
        form.submit();
        form.remove();
    }

    var OtherEarningsTypes = [];
    var footerTotals = {};
    var visibleColumns = [];

    function LoadDynamicHeaders(){

        $(".dynamicOEHeader").remove();
        $(".dynamicOEFooter").remove();

        var headerHtml = '';
        var footerHtml = '';

        $.each(OtherEarningsTypes,function(i,row){

            headerHtml +=
                '<th class="dynamicOEHeader" style="min-width:120px;">'
                + row.Name.toUpperCase()
                + '</th>';

            footerHtml +=
                '<th title="'+row.Name.toUpperCase()+'" class="dynamicOEFooter" id="ftOE_'
                + row.IncomeDeductionTypeID
                + '"></th>';

        });

        // HEADER
        $(headerHtml).insertAfter("#otherEarningsMarker");

        // FOOTER
        $(footerHtml).insertAfter("#ftOtherNonTaxable");
    }


    function getRecordList(vPageNo){

        $("#tblList").DataTable().clear().draw();
        $(".paginate_button").remove(); 

        $("#PageBatchNo").val(vPageNo);   
        vLimit=100;  

     
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                PayrollPeriodID: $("#SearchPayrollPeriodCode").val(),
                FilterType: $("#GenerateFilter").val(),
                BranchID: $("#GeneratePayrollBranch").val(),
                SiteID: $("#GeneratePayrollSite").val(),
                DivisionID: $("#GeneratePayrollDivision").val(),
                DepartmentID: $("#GeneratePayrollDepartment").val(),
                SectionID: $("#GeneratePayrollSection").val(),
                JobTypeID: $("#GeneratePayrollJobType").val(),
                EmployeeID: $("#GeneratePayrollEmployee").val(),
                Status: $("#Status").val(),
                Limit: vLimit,
                PageNo: vPageNo
            },
            url: "{{ route('get-payroll-transaction-report-list') }}",
            dataType: "json",
            success: function(data){

                if(data.Response=='Success'){
                    total_rec=data.TotalRecord;                    
                    if(total_rec>0){

                    CreatePaging(total_rec,vLimit); 

                    if(total_rec>vLimit){
                        $("#divPaging").show(); 
                        $("#total-record").text(total_rec);
                        $("#paging_button_id"+vPageNo).css("background", "#0069d9");
                        $("#paging_button_id"+vPageNo).css("color", "#fff");
                     }   

                        OtherEarningsTypes = data.OtherEarningsTypes || [];

                        footerTotals = data.Totals;

                        // destroy old datatable
                        if ($.fn.DataTable.isDataTable('#tblList')) {
                            $('#tblList').DataTable().destroy();
                        }

                        $("#tblList tbody").empty();

                        LoadDynamicHeaders();

                        // rebuild datatable
                        $('#tblList').DataTable({
                            columnDefs: [{
                                targets: [0],
                                visible: false,
                                searchable: false
                            }],
                            paging: false,
                            lengthChange: false,
                            searching: false,
                            ordering: true,
                            info: false,
                            autoWidth: false,
                            order: [[2, "asc"]]
                        });

                        LoadRecordList(data.PayrollRegisterReport);

                        $("#ftBasicPay").html(
                            FormatDecimal(data.Totals.BasicPay,2)
                        );

                        $("#ftECOLA").html(
                            FormatDecimal(data.Totals.ECOLA,2)
                        );

                        $("#ftLate").html(
                            FormatDecimal(data.Totals.LateAmount,2)
                        );

                        $("#ftUndertime").html(
                            FormatDecimal(data.Totals.UndertimeAmount,2)
                        );

                        $("#ftAbsent").html(
                            FormatDecimal(data.Totals.AbsentAmount,2)
                        );

                        $("#ftSL").html(
                            FormatDecimal(data.Totals.SL,2)
                        );

                        $("#ftVL").html(
                            FormatDecimal(data.Totals.VL,2)
                        );

                        $("#ftOL").html(
                            FormatDecimal(data.Totals.OL,2)
                        );

                        $("#ftNightDiff").html(
                            FormatDecimal(data.Totals.NightDiff,2)
                        );

                        $("#ftOTPay").html(
                            FormatDecimal(data.Totals.OTPay,2)
                        );

                        $("#ftLH").html(
                            FormatDecimal(Math.round(data.Totals.LH * 10) / 10, 2)
                        );

                        $("#ftSH").html(
                            FormatDecimal(Math.round(data.Totals.SH * 10) / 10, 2)
                        );

                        $("#ftRDDPay").html(
                            FormatDecimal(data.Totals.RDDPay,2)
                        );

                        $("#ftOTND").html(
                            FormatDecimal(data.Totals.OTND,2)
                        );

                        // $("#ftOtherTaxable").html(
                        //     FormatDecimal(data.Totals.OtherTaxableEarnings,2)
                        // );

                        $("#ftOtherNonTaxable").html(
                            FormatDecimal(data.Totals.OtherNonTaxableEarnings,2)
                        );

                        $.each(OtherEarningsTypes,function(i,row){

                            var field = row.Name.replace(/[^A-Za-z0-9_]/g,'_');

                            $("#ftOE_" + row.IncomeDeductionTypeID).html(
                                FormatDecimal(data.Totals[field] || 0,2)
                            );

                        });

                        $("#ftGrossPay").html(
                            FormatDecimal(data.Totals.GrossPay,2)
                        );

                        $("#ftSSS").html(
                            FormatDecimal(data.Totals.SSS,2)
                        );

                        $("#ftPHIC").html(
                            FormatDecimal(data.Totals.PHIC,2)
                        );

                        $("#ftHDMF").html(
                            FormatDecimal(data.Totals.HDMF,2)
                        );

                        $("#ftHDMFMP2").html(
                            FormatDecimal(data.Totals.HDMFMP2,2)
                        );

                        // $("#ftTaxableIncome").html(
                        //     FormatDecimal(data.Totals.TaxableIncome,2)
                        // );

                        $("#ftWTax").html(
                            FormatDecimal(data.Totals.WTax,2)
                        );

                        $("#ftSSSSalaryLoan").html(
                            FormatDecimal(data.Totals.SSSSalaryLoan,2)
                        );

                        $("#ftSSSCalamityLoan").html(
                            FormatDecimal(data.Totals.SSSCalamityLoan,2)
                        );

                        $("#ftHDMFLoan").html(
                            FormatDecimal(data.Totals.HDMFLoan,2)
                        );

                        $("#ftHDMFCalamityLoan").html(
                            FormatDecimal(data.Totals.HDMFCalamityLoan,2)
                        );

                        $("#ftOtherLoan").html(
                            FormatDecimal(data.Totals.OtherLoanDeductions,2)
                        );

                        $("#ftOtherDeduction").html(
                            FormatDecimal(data.Totals.OtherDeduction,2)
                        );

                        $("#ftTotalDeduction").html(
                            FormatDecimal(data.Totals.TotalDeduction,2)
                        );

                        $("#ftNetPay").html(
                            FormatDecimal(data.Totals.NetPay,2)
                        );

                        var table = $('#tblList').DataTable();

                        var visibleIndexes = [];

                        table.columns().every(function(index){

                            if(this.visible()){
                                visibleIndexes.push(index);
                            }

                        });

                        table.columns().visible(true);


                        HideZeroColumns();
                    }else{
                        showHasErrorMessage('','No record(s) found base on search criteria.');
                    }                    
                }
                
                $("#divLoader").hide();
            },
            error: function(data){
                $("#divLoader").hide();
                console.log(data.responseText);
            },
            beforeSend:function(vData){
                $("#divLoader").show();
            }

        });

    };


    function CreatePaging(vTotalRecord,vLimit){

       var i;
       paging_button="";
    
        limit=vLimit; //get limit
        totalcount=vTotalRecord; //get total count
        pages=Math.ceil(totalcount/limit); //get pages
      
       if (pages!=1) {     

          paging_button="<li class='paginate_button page-item previous' id='example2_previous'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getRecordList(1)'>First</a></li>"
          $(".ul-paging").append(paging_button);
       }
       
        if (pages>1) {        
          for (i = 1; i <= pages; i++) {            
            paging_button="<li class='paginate_button page-item'><a href='javascript:void(0)' id='paging_button_id"+i+"' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getRecordList("+i+")'>"+i+"</a></li>"
             $(".ul-paging").append(paging_button);
          }
        }
          
       if (pages!=1) {            
        paging_button="<li class='paginate_button page-item next' id='example2_next'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getRecordList("+pages+",)'>Last</a></li>"
        $(".ul-paging").append(paging_button);
        }    
   }

    function LoadRecordList(vList){

        if(vList.length == 0){
            showHasErrorMessage('','No record(s) found base on search criteria.');
        }

        $.each(vList,function(i,v){
            LoadRecordRow(v);
        });
    }

   function LoadRecordRow(vData){

            var tblList = $("#tblList").DataTable();
                       
            var dynamicOEColumns = [];

            $.each(OtherEarningsTypes,function(i,row){

                var field = row.Name.replace(/[^A-Za-z0-9_]/g,'_');

                dynamicOEColumns.push(
                    "<span class='font-normal'>" +
                    FormatDecimal(vData[field] || 0,2) +
                    "</span>"
                );

            });
            
            tdID = vData.EmployeeID;
   
            tdEmployeeNo = "<span class='font-normal'>" + vData.EmployeeNo + "</span>";
            tdEmployeeName = "<span class='font-normal'>" + vData.EmployeeName + "</span>";

            tdTeamLeader = "<span class='font-normal'>" + (vData.TeamLeader ?? 'NO TEAM LEADER') + "</span>";

            tdPosition = "<span class='font-normal'>" + (vData.Position ?? 'NO POSITION') + "</span>";

            tdRate = "<span class='font-normal'>" + FormatDecimal(vData.RateType == 1 ? vData.DailyRate : vData.MonthlyRate,2) + "</span>";

            tdDays = "<span class='font-normal'>" + FormatDecimal(vData.Days,2) + "</span>";

            tdBasicPay = "<span class='font-normal'>" + FormatDecimal(vData.BasicPay,2) + "</span>";
            tdECOLA = "<span class='font-normal'>" + FormatDecimal(vData.ECOLA,2) + "</span>";

            tdLate = "<span class='font-normal'>" + FormatDecimal(vData.LateAmount,2) + "</span>";
            tdUnderTime = "<span class='font-normal'>" + FormatDecimal(vData.UndertimeAmount,2) + "</span>";
            tdAbsent = "<span class='font-normal'>" + FormatDecimal(vData.AbsentAmount ,2) + "</span>";

            tdSL = "<span class='font-normal'>" + FormatDecimal(vData.SL,2) + "</span>";
            tdVL = "<span class='font-normal'>" + FormatDecimal(vData.VL,2) + "</span>";
            tdOL = "<span class='font-normal'>" + FormatDecimal(vData.OL,2) + "</span>";

            tdNightDiff = "<span class='font-normal'>" + FormatDecimal(vData.NightDiff,2) + "</span>"; 
            tdOvertimePay = "<span class='font-normal'>" + FormatDecimal(vData.OTPay,2) + "</span>"; 


            tdLH = "<span class='font-normal'>" + FormatDecimal(Math.round(vData.LH * 10) / 10, 2) + "</span>"; 
            tdSH = "<span class='font-normal'>" + FormatDecimal(Math.round(vData.SH * 10) / 10, 2) + "</span>"; 

            tdRDDPay = "<span class='font-normal'>" + FormatDecimal(vData.RDDPay,2) + "</span>"; 

            tdOvertimeND = "<span class='font-normal'>" + FormatDecimal(vData.OTND,2) + "</span>"; 

            // tdOtherTaxableEarnings = "<span class='font-normal'>" + FormatDecimal(vData.OtherTaxableEarnings,2) + "</span>"; 
            tdOtherNonTaxableEarnings = "<span class='font-normal'>" + FormatDecimal(vData.OtherNonTaxableEarnings,2) + "</span>";  
            // tdTaxableIncome = "<span class='font-normal'>" + FormatDecimal(vData.TaxableIncome,2) + "</span>";  

            tdGrossPay = "<span class='font-normal'>" + FormatDecimal(vData.GrossPay,2) + "</span>";
            tdSSS = "<span class='font-normal'>" + FormatDecimal(vData.SSS,2) + "</span>";
            tdPHIC = "<span class='font-normal'>" + FormatDecimal(vData.PHIC,2) + "</span>";
            tdHDMF = "<span class='font-normal'>" + FormatDecimal(vData.HDMF,2) + "</span>";
            tdHDMFMP2 = "<span class='font-normal'>" + FormatDecimal(vData.HDMFMP2,2) + "</span>";

            tdWTax = "<span class='font-normal'>" + FormatDecimal(vData.WTax,2) + "</span>";


            tdSSSSalaryLoan = "<span class='font-normal'>" + FormatDecimal(vData.SSSSalaryLoan,2) + "</span>";
            tdSSSCalamityLoan = "<span class='font-normal'>" + FormatDecimal(vData.SSSCalamityLoan,2) + "</span>";
            tdHDMFLoan = "<span class='font-normal'>" + FormatDecimal(vData.HDMFLoan,2) + "</span>";
            tdHDMFCalamityLoan = "<span class='font-normal'>" + FormatDecimal(vData.HDMFCalamityLoan,2) + "</span>";

            tdOtherLoan = "<span class='font-normal'>" + FormatDecimal(vData.OtherLoanDeductions,2) + "</span>";

            tdOtherDeduction = "<span class='font-normal'>" + FormatDecimal(vData.OtherDeduction,2) + "</span>";

            tdTotalDeduction = "<span class='font-normal'>" + FormatDecimal(vData.TotalDeduction,2) + "</span>";

            tdNetPay = "<span class='font-normal'>" + FormatDecimal(vData.NetPay,2) + "</span>";
            
            if(vData.Status=='Pending'){
              tdStatus = "<span style='color:red;display:flex;'> Un-Posted </span>";
            }else{
              tdStatus = "<span style='color:green;display:flex;'> <i class='bx bx-check-circle'></i> Posted </span>";
            }
            
           
            //Check if record already listed
            var IsRecordExist = false;
            tblList.rows().every(function ( rowIdx, tableLoop, rowLoop ) {
                var rowData = this.data();
                if(rowData[0] == vData.EmployeeID){

                    IsRecordExist = true;
                    //Edit Row
                    curData = tblList.row(rowIdx).data();
                    curData[0] = tdID;
                    curData[1] = tdEmployeeNo;
                    curData[2] = tdEmployeeName;
                    curData[3] = tdTeamLeader;
                    curData[4] = tdPosition;
                    curData[5] = tdRate;
                    curData[6] = tdDays;
                    curData[7] = tdBasicPay;
                    curData[8] = tdECOLA;
                    curData[9] = tdLate;                      
                    curData[10] = tdUnderTime;                      
                    curData[11] = tdAbsent;                      
                    curData[12] = tdSL;
                    curData[13] = tdVL;
                    curData[14] = tdOL;
                    curData[15] = tdNightDiff;
                    curData[16] = tdOvertimePay; 
                    curData[17] = tdLH;  
                    curData[18] = tdSH;  
                    curData[19] = tdRDDPay;   
                    curData[20] = tdOvertimeND;    
                    // curData[21] = tdOtherTaxableEarnings;
                    curData[22] = tdOtherNonTaxableEarnings;
                    curData[23] = tdGrossPay;
                    curData[24] = tdSSS;
                    curData[25] = tdPHIC;
                    curData[26] = tdHDMF;   
                    curData[27] = tdHDMFMP2;
                    // curData[28] = tdTaxableIncome;                    
                    curData[29] = tdWTax;
                    curData[30] = tdSSSSalaryLoan;
                    curData[31] = tdSSSCalamityLoan;
                    curData[32] = tdHDMFLoan;
                    curData[33] = tdHDMFCalamityLoan;
                    curData[34] = tdOtherLoan;
                    curData[35] = tdOtherDeduction;
                    curData[36] = tdTotalDeduction;
                    curData[37] = tdNetPay;
                    curData[38] = tdStatus;
                    
                    this.data(curData).invalidate().draw();
                }
            });

            if(!IsRecordExist){
                //New Row
                tblList.row.add([
                tdID,
                tdEmployeeNo,
                tdEmployeeName,
                tdTeamLeader,
                tdPosition,
                tdRate,
                tdDays,
                tdBasicPay,
                tdECOLA,
                tdLate,
                tdUnderTime,
                tdAbsent,
                tdSL,
                tdVL,
                tdOL,
                tdNightDiff,
                tdOvertimePay,
                tdLH,
                tdSH,
                tdRDDPay,
                tdOvertimeND,
                // tdOtherTaxableEarnings,
                tdOtherNonTaxableEarnings,
                ...dynamicOEColumns,
                tdGrossPay,
                tdSSS,
                tdPHIC,
                tdHDMF,     
                tdHDMFMP2,           
                // tdTaxableIncome,
                tdWTax,
                tdSSSSalaryLoan,
                tdSSSCalamityLoan,
                tdHDMFLoan,
                tdHDMFCalamityLoan,
                tdOtherLoan,
                tdOtherDeduction,
                tdTotalDeduction,
                tdNetPay,
                tdStatus

            ]).draw();          
            }
        }

    function GenerateExcel(){

      if(total_rec<=0){
        showHasErrorMessage('','No record(s) found base on search criteria.');
         return;   
      }
      
      showHasSuccessMessage('Please wait while generating Payroll Register excel file.');                     
         
        $.ajax({
              type: "post",
              data: {
                  _token: '{{ csrf_token() }}',
                  Platform : "{{ config('app.PLATFORM_ADMIN') }}",          
                  PayrollPeriodID: $("#SearchPayrollPeriodCode").val(),                                      
                  FilterType: $("#GenerateFilter").val(),
                  BranchID: $("#GeneratePayrollBranch").val(),
                  SiteID: $("#GeneratePayrollSite").val(),
                  DivisionID: $("#GeneratePayrollDivision").val(),
                  DepartmentID: $("#GeneratePayrollDepartment").val(),
                  SectionID: $("#GeneratePayrollSection").val(),
                  JobTypeID: $("#GeneratePayrollJobType").val(),
                  EmployeeID: $("#GeneratePayrollEmployee").val(),
                  Status: $("#Status").val(),
              },
              url: "{{ route('get-excel-payroll-register-list') }}",
              dataType: "json",
              success: function(data){

                  if(data.Response=="Success"){
                       resultquery=data.PayrollRegisterExcelList;
                       if (total_rec>0){     
                          ShowGeneratedExcel();
                       }else{
                        showHasErrorMessage('','No record(s) found base on search criteria.');
                       }  
                  }
                 
              },
              error: function(data){
                  console.log(data.responseText);
              },
              beforeSend:function(vData){
        
              }
      });
       
     }

    function HideZeroColumns() {

        var table = $('#tblList').DataTable();

        table.columns().every(function(index){

            var header = $(table.column(index).header());

            // always keep fixed columns
            if(header.hasClass('fixed-column')){
                return;
            }

            var footer = $(table.column(index).footer());

            if(!footer.length){
                return;
            }

            var total = parseFloat(
                footer.text().replace(/,/g,'') || 0
            );

            if(isNaN(total) || total === 0){
                table.column(index).visible(false);
            }

        });

    }


    function ShowGeneratedExcel(){
        
          var xlsRows = [];
          var createXLSLFormatObj = [];

           var Status =$("#Status").val();

          var xlsReportHeader = [
                            "NITRO PACIFIC",
                            "",
                            ""
                          ];
          createXLSLFormatObj.push(xlsReportHeader);
          xlsReportHeader = [
                            "Payroll Register Report",
                            "",
                            ""
                          ];
          createXLSLFormatObj.push(xlsReportHeader);
          xlsReportHeader = [
                            "Date/Time: {{ date("m/d/Y H:i:s") }}",
                            "",
                            ""
                          ];
          createXLSLFormatObj.push(xlsReportHeader);
          xlsReportHeader = [
                            "",
                            "",
                            ""
                          ];
          createXLSLFormatObj.push(xlsReportHeader);
                
         // Excel Headers
        var xlsHeader = [
            "EMPLOYEE NO.",
            "EMPLOYEE NAME",
            "TEAM LEADER",
            "POSITION",
            "RATE",
            "NO. OF DAYS",
            "BASIC PAY",
            "ECOLA",
            "LATE",
            "UNDERTIME",
            "ABSENT",
            "SL",
            "VL",
            "OL",
            "NIGHT DIFFERENTIAL",
            "OVERTIME PAY",
            "LEGAL HOLIDAY",
            "SPECIAL HOLIDAY",
            "RDD PAY",
            "ND OT",
            // "OTHER TAXABLE EARNINGS",
            "OTHER NON TAXABLE EARNINGS"
        ];

        $.each(OtherEarningsTypes,function(i,row){

            xlsHeader.push(
                row.Name.toUpperCase()
            );

        });

        xlsHeader.push(
            "GROSS PAY",
            "SSS CONTRIBUTION",
            "PHIC CONTRIBUTION",
            "HDMF CONTRIBUTION",
            "HDMF MP2",
            // "TAXABLE INCOME",
            "WITHHOLDING TAX",
            "SSS SALARY LOAN",
            "SSS CALAMITY LOAN",
            "HDMF LOAN",
            "HDMF CALAMITY LOAN",
            "OTHER LOAN",
            "OTHER DEDUCTIONS",
            "TOTAL DEDUCTIONS",
            "NET PAY",
            "STATUS"
        );
          
        visibleColumns = [
            { title: "EMPLOYEE NO.", field: "EmployeeNo", fixed: true },
            { title: "EMPLOYEE NAME", field: "EmployeeName", fixed: true },
            { title: "TEAM LEADER", field: "TeamLeader", fixed: true },
            { title: "POSITION", field: "Position", fixed: true },
            { title: "RATE", field: "Rate", fixed: true },
            { title: "NO. OF DAYS", field: "Days", fixed: true },
            { title: "BASIC PAY", field: "BasicPay", fixed: true },
            { title: "ECOLA", field: "ECOLA", fixed: false },
            { title: "LATE", field: "LateAmount", fixed: true },
            { title: "UNDERTIME", field: "UndertimeAmount", fixed: true },
            { title: "ABSENT", field: "AbsentAmount", fixed: true },
            { title: "SL", field: "SL", fixed: true },
            { title: "VL", field: "VL", fixed: true },
            { title: "OL", field: "OL", fixed: false },
            { title: "NIGHT DIFFERENTIAL", field: "NightDiff", fixed: true },
            { title: "OVERTIME PAY", field: "OTPay", fixed: true },
            { title: "LEGAL HOLIDAY", field: "LH", fixed: false },
            { title: "SPECIAL HOLIDAY", field: "SH", fixed: false },
            { title: "RDD PAY", field: "RDDPay", fixed: false },
            { title: "ND OT", field: "OTND", fixed: false },
            // { title: "OTHER TAXABLE EARNINGS", field: "OtherTaxableEarnings", fixed: false },
            { title: "OTHER NON TAXABLE EARNINGS", field: "OtherNonTaxableEarnings", fixed: false }
        ];

        $.each(OtherEarningsTypes,function(i,row){

            var field = row.Name.replace(/[^A-Za-z0-9_]/g,'_');

            visibleColumns.push({
                title: row.Name.toUpperCase(),
                field: field,
                fixed: false
            });

        });

        visibleColumns.push(
            { title:"GROSS PAY", field:"GrossPay", fixed:true },
            { title:"SSS CONTRIBUTION", field:"SSS", fixed:false },
            { title:"PHIC CONTRIBUTION", field:"PHIC", fixed:false },
            { title:"HDMF CONTRIBUTION", field:"HDMF", fixed:false },
            { title:"HDMF MP2", field:"HDMFMP2", fixed:false },
            // { title:"TAXABLE INCOME", field:"TaxableIncome", fixed:true },
            { title:"WITHHOLDING TAX", field:"WTax", fixed:true },
            { title:"SSS SALARY LOAN", field:"SSSSalaryLoan", fixed:false },
            { title:"SSS CALAMITY LOAN", field:"SSSCalamityLoan", fixed:false },
            { title:"HDMF LOAN", field:"HDMFLoan", fixed:false },
            { title:"HDMF CALAMITY LOAN", field:"HDMFCalamityLoan", fixed:false },
            { title:"OTHER LOAN", field:"OtherLoanDeductions", fixed:false },
            { title:"OTHER DEDUCTIONS", field:"OtherDeduction", fixed:false },
            { title:"TOTAL DEDUCTIONS", field:"TotalDeduction", fixed:false },
            { title:"NET PAY", field:"NetPay", fixed:true },
            { title:"STATUS", field:"Status", fixed:true }
        );

        xlsHeader = [];

        $.each(visibleColumns,function(i,col){

            if(col.fixed){
                xlsHeader.push(col.title);
                return;
            }

            var total = parseFloat(
                footerTotals[col.field] || 0
            );

            if(total !== 0){
                xlsHeader.push(col.title);
            }

        });

        visibleColumns = visibleColumns.filter(function(col){

            if(col.fixed){
                return true;
            }

            return parseFloat(
                footerTotals[col.field] || 0
            ) !== 0;

        });


            xlsRows=resultquery;
          
            // createXLSLFormatObj.push(xlsHeader);

            var intRowCnt = 5;
            var dblDays = 0;
            var dblBasicPay = 0;
            var dblECOLA = 0;
            var dblLateAmount = 0;
            var dblUndertimeAmount = 0;
            var dblAbsentAmount = 0;
            var dblSL = 0;
            var dblVL = 0;
            var dblOL = 0;
            var dblNightDiff = 0;
            var dblOTPay = 0;
            var dbLH = 0;
            var dbSH = 0;
            var dblRDDPay = 0;
            var dblOTND = 0;
            var dblOtherTaxableEarnings = 0;
            var dblOtherNonTaxableEarnings = 0;
            var dblGrossPay = 0;
            var dblSSS = 0;
            var dblPHIC = 0;
            var dblHDMF = 0;
            var dblHDMFMP2 = 0;
            var dblTaxableIncome = 0;
            var dblWTax = 0;
            var dblSSSSalaryLoan = 0;
            var dblSSSCalamityLoan = 0;
            var dblHDMFLoan = 0;
            var dblHDMFCalamityLoan = 0;
            var dblOtherLoanDeductions = 0;
            var dblOtherDeduction = 0;
            var dblTotalDeduction = 0;
            var dblNetPay = 0;

        // grand totals
        var gDays = 0, gBasicPay = 0, gECOLA = 0, gLate = 0, gUndertime = 0, gAbsent = 0,
            gSL = 0, gVL = 0, gOL = 0, gNightDiff = 0, gOTPay = 0, gLH = 0, gSH = 0, gRDDPay = 0, gOTND = 0,
            gOtherTaxable = 0, gOtherNonTaxable = 0, gGrossPay = 0,
            gSSS = 0, gPHIC = 0, gHDMF = 0, gHDMFMP2 = 0,
            gTaxable = 0, gWTax = 0, gSSSSalaryLoan = 0, gSSSCalamityLoan = 0, gHDMFLoan = 0, gHDMFCalamityLoan = 0, gOtherDed = 0, gOtherLoan = 0,
            gTotalDed = 0, gNetPay = 0;

        // team leader subtotals
        var tDays = 0, tBasicPay = 0, tECOLA = 0, tLate = 0, tUndertime = 0, tAbsent = 0,
            tSL = 0, tVL = 0, tOL = 0, tNightDiff = 0, tOTPay = 0, tLH = 0, tSH = 0, tRDDPay = 0, tOTND = 0,
            tOtherTaxable = 0, tOtherNonTaxable = 0, tGrossPay = 0,
            tSSS = 0, tPHIC = 0, tHDMF = 0, tHDMFMP2 = 0,
            tTaxable = 0, tWTax = 0, tSSSSalaryLoan = 0, tSSSCalamityLoan = 0, tHDMFLoan = 0, tHDMFCalamityLoan = 0, tOtherDed = 0, tOtherLoan = 0,
            tTotalDed = 0, tNetPay = 0;

        var currentTeamLeader = null;

        var dynamicGrandTotals = {};
        var dynamicTeamTotals = {};

        $.each(OtherEarningsTypes,function(i,row){

            var field = row.Name.replace(/[^A-Za-z0-9_]/g,'_');

            dynamicGrandTotals[field] = 0;
            dynamicTeamTotals[field] = 0;

        });

        $.each(xlsRows, function(index, v) {

            // TEAM LEADER
            if (currentTeamLeader !== v.TeamLeader) {

                // sho previous subtotal
                if (currentTeamLeader !== null) {
                    createXLSLFormatObj.push(
                        pushSubtotalRow("SUBTOTAL", {
                            Days: tDays,
                            BasicPay: tBasicPay, ECOLA: tECOLA, Late: tLate,
                            Undertime: tUndertime, Absent: tAbsent,
                            SL: tSL, VL: tVL, OL: tOL,
                            NightDiff: tNightDiff, OTPay: tOTPay, LH: tLH, SH: tSH, RDDPay: tRDDPay, OTND: tOTND,
                            // OtherTaxable: tOtherTaxable,
                            OtherNonTaxable: tOtherNonTaxable,
                            GrossPay: tGrossPay,
                            SSS: tSSS, PHIC: tPHIC, HDMF: tHDMF, HDMFMP2: tHDMFMP2,
                            // Taxable: tTaxable, 
                            WTax: tWTax,
                            SSSSalaryLoan: tSSSSalaryLoan, SSSCalamityLoan: tSSSCalamityLoan, HDMFLoan: tHDMFLoan, HDMFCalamityLoan: tHDMFCalamityLoan, OtherLoan: tOtherLoan, OtherDed: tOtherDed,
                            TotalDed: tTotalDed, NetPay: tNetPay, ...dynamicTeamTotals
                        })
                    );
                    createXLSLFormatObj.push([]);
                }

                // reset TL totals
                tDays = tBasicPay = tECOLA = tLate = tUndertime = tAbsent =
                tSL = tVL = tOL = tNightDiff = tOTPay = tLH = tSH = tRDDPay = tOTND =
                // tOtherTaxable = 
                tOtherNonTaxable = tGrossPay =
                tSSS = tPHIC = tHDMF = tHDMFMP2 =
                // tTaxable = 
                tWTax = tSSSSalaryLoan = tSSSCalamityLoan = tHDMFLoan = tHDMFCalamityLoan = tOtherDed = tOtherLoan =
                tTotalDed = tNetPay = 0;

                // header block
                createXLSLFormatObj.push(["TEAM LEADER: " + v.TeamLeader]);
                createXLSLFormatObj.push([]);
                createXLSLFormatObj.push(xlsHeader);
                // createXLSLFormatObj.push(new Array(xlsHeader.length).fill("—"));

                currentTeamLeader = v.TeamLeader;

                $.each(OtherEarningsTypes,function(i,row){

                    var field = row.Name.replace(/[^A-Za-z0-9_]/g,'_');

                    dynamicTeamTotals[field] = 0;

                });
            }

            // EMPLOYEE ROW
            var rowData = {
                EmployeeNo: v.EmployeeNo,
                EmployeeName: v.EmployeeName,
                TeamLeader: v.TeamLeader,
                Position: v.Position,
                Rate: v.RateType == 1 ? v.DailyRate : v.MonthlyRate,
                Days: v.Days,
                BasicPay: v.BasicPay,
                ECOLA: v.ECOLA,
                LateAmount: v.LateAmount,
                UndertimeAmount: v.UndertimeAmount,
                AbsentAmount: v.AbsentAmount,
                SL: v.SL,
                VL: v.VL,
                OL: v.OL,
                NightDiff: v.NightDiff,
                OTPay: v.OTPay,
                LH: FormatDecimal(Math.round(v.LH * 10) / 10, 2),
                SH: FormatDecimal(Math.round(v.SH * 10) / 10, 2),
                RDDPay: v.RDDPay,
                OTND: v.OTND,
                // OtherTaxableEarnings: v.OtherTaxableEarnings,
                OtherNonTaxableEarnings: v.OtherNonTaxableEarnings,
                GrossPay: v.GrossPay,
                SSS: v.SSS,
                PHIC: v.PHIC,
                HDMF: v.HDMF,
                HDMFMP2: v.HDMFMP2,
                // TaxableIncome: v.TaxableIncome,
                WTax: v.WTax,
                SSSSalaryLoan: v.SSSSalaryLoan,
                SSSCalamityLoan: v.SSSCalamityLoan,
                HDMFLoan: v.HDMFLoan,
                HDMFCalamityLoan: v.HDMFCalamityLoan,
                OtherLoanDeductions: v.OtherLoanDeductions,
                OtherDeduction: v.OtherDeduction,
                TotalDeduction: v.TotalDeduction,
                NetPay: v.NetPay,
                Status: v.Status
            };

            $.each(OtherEarningsTypes,function(i,row){

                var field = row.Name.replace(/[^A-Za-z0-9_]/g,'_');

                rowData[field] = v[field] || 0;

            });

            var excelRow = [];

            $.each(visibleColumns,function(i,col){

                if(col.fixed){
                    excelRow.push(rowData[col.field] ?? '');
                    return;
                }

                var total = parseFloat(
                    footerTotals[col.field] || 0
                );

                if(total !== 0){
                    excelRow.push(rowData[col.field] ?? '');
                }

            });

            createXLSLFormatObj.push(excelRow);

            // ACCUMULATE TOTALS
            tDays += parseFloat(v.Days, 2);
            tBasicPay += parseFloat(v.BasicPay, 2);       gBasicPay += parseFloat(v.BasicPay, 2);
            tECOLA += parseFloat(v.ECOLA, 2);             gECOLA += parseFloat(v.ECOLA, 2);
            tLate += parseFloat(v.LateAmount, 2);         gLate += parseFloat(v.LateAmount, 2);
            tUndertime += parseFloat(v.UndertimeAmount, 2); gUndertime += parseFloat(v.UndertimeAmount, 2);
            tAbsent += parseFloat(v.AbsentAmount, 2);     gAbsent += parseFloat(v.AbsentAmount, 2);
            tSL += parseFloat(v.SL, 2);                   gSL += parseFloat(v.SL, 2);
            tVL += parseFloat(v.VL, 2);                   gVL += parseFloat(v.VL, 2);
            tOL += parseFloat(v.OL, 2);                   gOL += parseFloat(v.OL, 2);
            tNightDiff += parseFloat(v.NightDiff, 2);     gNightDiff += parseFloat(v.NightDiff, 2);
            tOTPay += parseFloat(v.OTPay, 2);             gOTPay += parseFloat(v.OTPay, 2);
            tOTND += parseFloat(v.OTND, 2);               gOTND += parseFloat(v.OTND, 2);
            // tOtherTaxable += parseFloat(v.OtherTaxableEarnings, 2); gOtherTaxable += parseFloat(v.OtherTaxableEarnings, 2);
            tOtherNonTaxable += parseFloat(v.OtherNonTaxableEarnings, 2); gOtherNonTaxable += parseFloat(v.OtherNonTaxableEarnings, 2);
            tGrossPay += parseFloat(v.GrossPay, 2);       gGrossPay += parseFloat(v.GrossPay, 2);
            tSSS += parseFloat(v.SSS, 2);                 gSSS += parseFloat(v.SSS, 2);
            tPHIC += parseFloat(v.PHIC, 2);               gPHIC += parseFloat(v.PHIC, 2);
            tHDMF += parseFloat(v.HDMF, 2);               gHDMF += parseFloat(v.HDMF, 2);
            tHDMFMP2 += parseFloat(v.HDMFMP2, 2);         gHDMFMP2 += parseFloat(v.HDMFMP2, 2);
            // tTaxable += parseFloat(v.TaxableIncome, 2);   gTaxable += parseFloat(v.TaxableIncome, 2);
            tWTax += parseFloat(v.WTax, 2);               gWTax += parseFloat(v.WTax, 2);
            tOtherLoan += parseFloat(v.OtherLoanDeductions, 2); gOtherLoan += parseFloat(v.OtherLoanDeductions, 2);
            tOtherDed += parseFloat(v.OtherDeduction, 2); gOtherDed += parseFloat(v.OtherDeduction, 2);
            tTotalDed += parseFloat(v.TotalDeduction, 2); gTotalDed += parseFloat(v.TotalDeduction, 2);
            tNetPay += parseFloat(v.NetPay, 2);           gNetPay += parseFloat(v.NetPay, 2);
            tRDDPay += parseFloat(v.RDDPay, 2);           gRDDPay += parseFloat(v.RDDPay, 2);
            tLH += parseFloat(FormatDecimal(Math.round(v.LH * 10) / 10, 2), 2);                   gLH += parseFloat(FormatDecimal(Math.round(v.LH * 10) / 10, 2), 2);
            tSH += parseFloat(FormatDecimal(Math.round(v.SH * 10) / 10, 2), 2);                   gSH += parseFloat(FormatDecimal(Math.round(v.SH * 10) / 10, 2), 2);
            tSSSSalaryLoan += parseFloat(v.SSSSalaryLoan, 2); gSSSSalaryLoan += parseFloat(v.SSSSalaryLoan, 2);
            tSSSCalamityLoan += parseFloat(v.SSSCalamityLoan, 2); gSSSCalamityLoan += parseFloat(v.SSSCalamityLoan, 2);
            tHDMFLoan += parseFloat(v.HDMFLoan, 2); gHDMFLoan += parseFloat(v.HDMFLoan, 2);
            tHDMFCalamityLoan += parseFloat(v.HDMFCalamityLoan, 2); gHDMFCalamityLoan += parseFloat(v.HDMFCalamityLoan, 2);

            $.each(OtherEarningsTypes,function(i,row){

                var field = row.Name.replace(/[^A-Za-z0-9_]/g,'_');

                var value = parseFloat(v[field] || 0);

                dynamicTeamTotals[field] += value;
                dynamicGrandTotals[field] += value;

            });

        });


        // last team leader subtotal
        createXLSLFormatObj.push(
            pushSubtotalRow("SUBTOTAL", {
                Days: tDays,
                BasicPay: tBasicPay, ECOLA: tECOLA, Late: tLate,
                Undertime: tUndertime, Absent: tAbsent,
                SL: tSL, VL: tVL, OL: tOL,
                NightDiff: tNightDiff, OTPay: tOTPay, LH: tLH, SH: tSH, RDDPay: tRDDPay, OTND: tOTND,
                // OtherTaxable: tOtherTaxable,
                OtherNonTaxable: tOtherNonTaxable,
                GrossPay: tGrossPay,
                SSS: tSSS, PHIC: tPHIC, HDMF: tHDMF, HDMFMP2: tHDMFMP2,
                // Taxable: tTaxable, 
                WTax: tWTax,
                SSSSalaryLoan: tSSSSalaryLoan, SSSCalamityLoan: tSSSCalamityLoan, HDMFLoan: tHDMFLoan, HDMFCalamityLoan: tHDMFCalamityLoan, OtherLoan: tOtherLoan, OtherDed: tOtherDed,
                TotalDed: tTotalDed, NetPay: tNetPay, 
                ...dynamicTeamTotals
            })
        );

        // space
        createXLSLFormatObj.push([]);

        // GRAND TOTAL
        createXLSLFormatObj.push(
            pushSubtotalRow("GRAND TOTAL", {
                Days: gDays,
                BasicPay: gBasicPay, ECOLA: gECOLA, Late: gLate,
                Undertime: gUndertime, Absent: gAbsent,
                SL: gSL, VL: gVL, OL: gOL,
                NightDiff: gNightDiff, OTPay: gOTPay, LH: gLH, SH: gSH, RDDPay: gRDDPay, OTND: gOTND,
                // OtherTaxable: gOtherTaxable,
                OtherNonTaxable: gOtherNonTaxable,
                GrossPay: gGrossPay,
                SSS: gSSS, PHIC: gPHIC, HDMF: gHDMF, HDMFMP2: gHDMFMP2,
                // Taxable: gTaxable, 
                WTax: gWTax,
                SSSSalaryLoan: gSSSSalaryLoan, SSSCalamityLoan: gSSSCalamityLoan, HDMFLoan: gHDMFLoan, HDMFCalamityLoan: gHDMFCalamityLoan, OtherLoan: gOtherLoan, OtherDed: gOtherDed,
                TotalDed: gTotalDed, NetPay: gNetPay,
                ...dynamicGrandTotals
            })
        );
            
        intRowCnt = intRowCnt + 1;

        var ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);
        for (var i = 6; i < intRowCnt; i++) {
            for (var c = 2; c < xlsHeader.length; c++){
                var ExcelCol = ExcelColumn(i, c);
                if (ws[ExcelCol]) {
                    ws[ExcelCol].z = '#,##0.00_);\\(#,##0.00\\)';
                    ws[ExcelCol].t = 'n';
                }
            }
        }

        const countheader = Object.keys(createXLSLFormatObj); // columns name header to

            var wscols = [];
            for (var i = 0; i < countheader.length; i++) {  // columns length added
                wscols.push({ wch: Math.max(countheader[i].toString().length + 27) })
            }
            
        ws['!cols'] = wscols;
                    
        /* File Name */
        var wb = XLSX.utils.book_new();

        if(Status=='Approved'){
            XLSX.utils.book_append_sheet(wb, ws,"Posted Payroll Register");  
        }else{
            XLSX.utils.book_append_sheet(wb, ws,"Un-Posted Payroll Register");    
        }
        

        /* generate file and download */
        const wbout = XLSX.write(wb, { type: "array", bookType: "xlsx" });
        saveAs(new Blob([wbout], { type: "application/octet-stream" }), "Payroll-Register-Report.xlsx");

        showHasSuccessMessage('Excel file has successfully created & downloaded at Download Folder');  
                          
    }

    function pushSubtotalRow(label, totals) {

        var rowData = {
            EmployeeNo: label,
            EmployeeName: "",
            TeamLeader: "",
            Position: "",
            Rate: "",
            Days: totals.Days,
            BasicPay: totals.BasicPay,
            ECOLA: totals.ECOLA,
            LateAmount: totals.Late,
            UndertimeAmount: totals.Undertime,
            AbsentAmount: totals.Absent,
            SL: totals.SL,
            VL: totals.VL,
            OL: totals.OL,
            NightDiff: totals.NightDiff,
            OTPay: totals.OTPay,
            LH: FormatDecimal(Math.round(totals.LH * 10) / 10, 2),
            SH: FormatDecimal(Math.round(totals.SH * 10) / 10, 2),
            RDDPay: totals.RDDPay,
            OTND: totals.OTND,
            // OtherTaxableEarnings: totals.OtherTaxable,
            OtherNonTaxableEarnings: totals.OtherNonTaxable,
            GrossPay: totals.GrossPay,
            SSS: totals.SSS,
            PHIC: totals.PHIC,
            HDMF: totals.HDMF,
            HDMFMP2: totals.HDMFMP2,
            // TaxableIncome: totals.Taxable,
            WTax: totals.WTax,
            SSSSalaryLoan: totals.SSSSalaryLoan,
            SSSCalamityLoan: totals.SSSCalamityLoan,
            HDMFLoan: totals.HDMFLoan,
            HDMFCalamityLoan: totals.HDMFCalamityLoan,
            OtherLoanDeductions: totals.OtherLoan,
            OtherDeduction: totals.OtherDed,
            TotalDeduction: totals.TotalDed,
            NetPay: totals.NetPay,
            Status: ""
        };

        $.each(OtherEarningsTypes,function(i,item){

            var field = item.Name.replace(/[^A-Za-z0-9_]/g,'_');

            rowData[field] = totals[field] || 0;

        });

        var row = [];

        $.each(visibleColumns,function(i,col){

            if(col.fixed){
                row.push(
                    rowData[col.field] !== undefined
                        ? rowData[col.field]
                        : ''
                );
                return;
            }

            var total = parseFloat(
                footerTotals[col.field] || 0
            );

            if(total !== 0){
                row.push(
                    rowData[col.field] !== undefined
                        ? rowData[col.field]
                        : ''
                );
            }

        });

        return row;
    }

    $("#GenerateFilter").change(function(){

    $("#divFilters").hide();
    $("#divLocation").hide();
    $("#divSite").hide();
    $("#divDivision").hide();
    $("#divDepartment").hide();
    $("#divSection").hide();
    $("#divJobType").hide();
    $("#divEmployee").hide();
    $("#spnTypeSearch").hide();

    if($("#GenerateFilter").val() == "Location"){
        $("#divFilters").show();
        $("#GeneratePayrollFilterLabel").text("Location");
        $("#spnTypeSearch").hide();
        $("#divLocation").show();
    }else if($("#GenerateFilter").val() == "Site"){
        $("#divFilters").show();
        $("#GeneratePayrollFilterLabel").text("Site");
        $("#spnTypeSearch").hide();
        $("#divSite").show();
    }else if($("#GenerateFilter").val() == "Division"){
        $("#divFilters").show();
        $("#GeneratePayrollFilterLabel").text("Division");
        $("#spnTypeSearch").hide();
        $("#divDivision").show();
    }else if($("#GenerateFilter").val() == "Department"){
        $("#divFilters").show();
        $("#GeneratePayrollFilterLabel").text("Department");
        $("#spnTypeSearch").hide();
        $("#divDepartment").show();
    }else if($("#GenerateFilter").val() == "Section"){
        $("#divFilters").show();
        $("#GeneratePayrollFilterLabel").text("Team Leader");
        $("#spnTypeSearch").hide();
        $("#divSection").show();
    }else if($("#GenerateFilter").val() == "Job Type"){
        $("#divFilters").show();
        $("#GeneratePayrollFilterLabel").text("Job Type");
        $("#spnTypeSearch").hide();
        $("#divJobType").show();
    }else if($("#GenerateFilter").val() == "Employee"){
        $("#divFilters").show();
        $("#GeneratePayrollFilterLabel").text("Employee");
        $("#spnTypeSearch").show();
        $("#divEmployee").show();
    }

});

</script>

<!-- SCROLL DRAG LEFT & RIGHT  -->
<script>
    $(document).ready(function() {
      // Initialize the DataTable
      $('#tblList').DataTable();

      // Enable horizontal scroll
      $('#style-2').on('mousedown', function(e) {
        var startX = e.pageX;
        var scrollLeft = $(this).scrollLeft();
        $(this).on('mousemove', function(e) {
          var distance = e.pageX - startX;
          $(this).scrollLeft(scrollLeft - distance);
        });
        $(this).on('mouseup', function() {
          $(this).off('mousemove');
        });
      });
    });
  </script>

  <!-- HIGHLIGHT LIGHT YELLOW -->
 <script>
    $(document).ready(function() {
        $('#tblList tbody').on('click', 'tr', function() {
            // Remove 'selected' class from all rows
            $('#tblList tbody tr').removeClass('selected');
            
            // Add 'selected' class to the clicked row
            $(this).addClass('selected');
        });
    });
</script>

@endsection



