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
                                                                        <option value="Section">Section</option>
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

                                                      @endif  
                                    
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                        <div id="style-2" class="table-responsive col-md-12 table_default_height">
                                            <table id="tblList" class="table zero-configuration complex-headers border alt-background">
                                                <thead>
                                                     <th></th>                                 
                                                        <th style="min-width: 110px;">EMPLOYEE ID</th>                                              
                                                        <th style="min-width: 120px;">EMPLOYEE NAME</th>                                              
                                                        <th style="min-width: 110px;">BASIC PAY</th>
                                                        <th style="min-width: 110px;">E-COLA</th>
                                                        <th style="min-width: 60px;">LATE</th>
                                                        <th style="min-width: 110px;">UNDER TIME</th>
                                                        <th style="min-width: 110px;">ABSENT</th>
                                                        <th style="min-width: 60px;">SL </th>
                                                        <th style="min-width: 60px;">VL</th>
                                                        <th style="min-width: 60px;">OL</th>
                                                        <th style="min-width: 80px;">NIGHT DIFF</th>
                                                        <th style="min-width: 110px;">OVERTIME PAY</th>
                                                        <th style="min-width: 110px;">ND OT</th>
                                                        <th style="min-width: 130px;">OTHER TAXABLE EARNING </th> 
                                                        <th style="min-width: 130px;">OTHER NON TAXABLE EARNING </th> 
                                                        <th style="min-width: 110px;">GROSS PAY</th>
                                                        <th style="min-width: 60px;">SSS</th>
                                                        <th style="min-width: 80px;">PHIL HEALTH</th>
                                                        <th style="min-width: 80px;">PAG IBIG</th>
                                                        <th style="min-width: 80px;">PAG IBIG MP2</th>                                                        
                                                        <th style="min-width: 110px;">TAXABLE INCOME</th>
                                                        <th style="min-width: 60px;">WTAX</th>
                                                        <th style="min-width: 110px;">LOAN DEDUCTION</th>
                                                        <th style="min-width: 110px;">OTHER DEDUCTION</th>
                                                        <th style="min-width: 110px;">TOTAL DEDUCTION</th>
                                                        <th style="min-width: 80px;">TOTAL NETPAY</th>
                                                        <th style="min-width: 60px;">STATUS</th>
                                                   </thead>
                                                  <tbody>
                                                </tbody>
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
                        LoadRecordList(data.PayrollRegisterReport);
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

        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadRecordRow(vList[x]);
            }
        }
    }

   function LoadRecordRow(vData){

            var tblList = $("#tblList").DataTable();
                                                        
            tdID = vData.EmployeeID;
   
            tdEmployeeNo = "<span class='font-normal'>" + vData.EmployeeNo + "</span>";
            tdEmployeeName = "<span class='font-normal'>" + vData.EmployeeName + "</span>";

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

            tdOvertimeND = "<span class='font-normal'>" + FormatDecimal(vData.OTND,2) + "</span>"; 

            tdOtherTaxableEarnings = "<span class='font-normal'>" + FormatDecimal(vData.OtherTaxableEarnings,2) + "</span>"; 
            tdOtherNonTaxableEarnings = "<span class='font-normal'>" + FormatDecimal(vData.OtherNonTaxableEarnings,2) + "</span>";  
            tdTaxableIncome = "<span class='font-normal'>" + FormatDecimal(vData.TaxableIncome,2) + "</span>";  

            tdGrossPay = "<span class='font-normal'>" + FormatDecimal(vData.GrossPay,2) + "</span>";
            tdSSS = "<span class='font-normal'>" + FormatDecimal(vData.SSS,2) + "</span>";
            tdPHIC = "<span class='font-normal'>" + FormatDecimal(vData.PHIC,2) + "</span>";
            tdHDMF = "<span class='font-normal'>" + FormatDecimal(vData.HDMF,2) + "</span>";
            tdHDMFMP2 = "<span class='font-normal'>" + FormatDecimal(vData.HDMFMP2,2) + "</span>";

            tdWTax = "<span class='font-normal'>" + FormatDecimal(vData.WTax,2) + "</span>";

            tdLoanDeduction = "<span class='font-normal'>" + FormatDecimal(vData.LoanDeduction,2) + "</span>";
            tOtherDeduction = "<span class='font-normal'>" + FormatDecimal(vData.OtherDeduction,2) + "</span>";

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
                    curData[3] = tdBasicPay;
                    curData[4] = tdECOLA;
                    curData[5] = tdLate;                      
                    curData[6] = tdUnderTime;                      
                    curData[7] = tdAbsent;                      
                    curData[8] = tdSL;
                    curData[9] = tdVL;
                    curData[10] = tdOL;
                    curData[11] = tdNightDiff;
                    curData[12] = tdOvertimePay;    
                    curData[13] = tdOvertimeND;    
                    curData[14] = tdOtherTaxableEarnings;
                    curData[15] = tdOtherNonTaxableEarnings;
                    curData[16] = tdGrossPay;
                    curData[17] = tdSSS;
                    curData[18] = tdPHIC;
                    curData[19] = tdHDMF;   
                    curData[20] = tdHDMFMP2;
                    curData[21] = tdTaxableIncome;                    
                    curData[22] = tdWTax;
                    curData[23] = tdLoanDeduction;
                    curData[24] = tOtherDeduction;
                    curData[25] = tdTotalDeduction;
                    curData[26] = tdNetPay;
                    curData[27] = tdStatus;
                    
                    this.data(curData).invalidate().draw();
                }
            });

            if(!IsRecordExist){
                //New Row
                tblList.row.add([
                tdID,
                tdEmployeeNo,
                tdEmployeeName,
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
                tdOvertimeND,
                tdOtherTaxableEarnings,
                tdOtherNonTaxableEarnings,
                tdGrossPay,
                tdSSS,
                tdPHIC,
                tdHDMF,     
                tdHDMFMP2,           
                tdTaxableIncome,
                tdWTax,
                tdLoanDeduction,
                tOtherDeduction,
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
                            "{{ strtoupper('Emlpoyee No.') }}",
                            "{{ strtoupper('Emlpoyee Name') }}",
                            "{{ strtoupper('Basic Pay') }}", 
                            "{{ strtoupper('ECOLA') }}",
                            "{{ strtoupper('Late') }}", 
                            "{{ strtoupper('UnderTime') }}", 
                            "{{ strtoupper('Absent') }}",
                            "{{ strtoupper('SL') }}", 
                            "{{ strtoupper('VL') }}",
                            "{{ strtoupper('OL') }}", 
                            "{{ strtoupper('Night Differential') }}",
                            "{{ strtoupper('Overtime Pay') }}",
                            "{{ strtoupper('ND OT') }}",
                            "{{ strtoupper('Other Taxable Earnings') }}",
                            "{{ strtoupper('Other Non Taxable Earnings') }}",
                            "{{ strtoupper('Gross Pay') }}", 
                            "{{ strtoupper('SSS Contribution') }}",
                            "{{ strtoupper('PHIC Contribution') }}", 
                            "{{ strtoupper('HDMF Contribution') }}", 
                            "{{ strtoupper('HDMF MP2') }}", 
                            "{{ strtoupper('Taxable Income') }}", 
                            "{{ strtoupper('Withholding Tax') }}",
                            "{{ strtoupper('Loan Deductions') }}",
                            "{{ strtoupper('Other Deductions') }}", 
                            "{{ strtoupper('Total Deductions') }}", 
                            "{{ strtoupper('Net Pay') }}", 
                            "{{ strtoupper('Status') }}"                         
                          ];

          
            xlsRows=resultquery;
          
            createXLSLFormatObj.push(xlsHeader);

            var intRowCnt = 5;
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
            var dblLoanDeduction = 0;
            var dblOtherDeduction = 0;
            var dblTotalDeduction = 0;
            var dblNetPay = 0;

              $.each(xlsRows, function(index, value) {
                  var innerRowData = [];   
                  $.each(value, function(ind, val) {
                                        
                    if(ind == "BasicPay" ||
                        ind == "ECOLA" ||
                        ind == "LateAmount" ||
                        ind == "UndertimeAmount" ||
                        ind == "AbsentAmount" ||
                        ind == "SL" ||
                        ind == "VL" ||
                        ind == "OL" ||
                        ind == "NightDiff" ||
                        ind == "OTPay" ||
                        ind == "OTND" ||
                        ind == "OtherTaxableEarnings" ||
                        ind == "OtherNonTaxableEarnings" ||
                        ind == "GrossPay" ||
                        ind == "SSS" ||
                        ind == "PHIC" ||
                        ind == "HDMF" ||
                        ind == "HDMFMP2" ||
                        ind == "TaxableIncome" ||
                        ind == "WTax" ||
                        ind == "LoanDeduction" ||
                        ind == "OtherDeduction" ||
                        ind == "TotalDeduction" ||
                        ind == "NetPay"){

                        innerRowData.push(val);

                        if(ind == "BasicPay"){
                            dblBasicPay = parseFloat(dblBasicPay) + parseFloat(val);
                        }else if(ind == "ECOLA"){
                            dblECOLA = parseFloat(dblECOLA) + parseFloat(val);
                        }else if(ind == "LateAmount"){
                            dblLateAmount = parseFloat(dblLateAmount) + parseFloat(val);
                        }else if(ind == "UndertimeAmount"){
                            dblUndertimeAmount = parseFloat(dblUndertimeAmount) + parseFloat(val);
                        }else if(ind == "AbsentAmount"){
                            dblAbsentAmount = parseFloat(dblAbsentAmount) + parseFloat(val);
                        }else if(ind == "SL"){
                            dblSL = parseFloat(dblSL) + parseFloat(val);
                        }else if(ind == "VL"){
                            dblVL = parseFloat(dblVL) + parseFloat(val);
                        }else if(ind == "OL"){
                            dblOL = parseFloat(dblOL) + parseFloat(val);
                        }else if(ind == "NightDiff"){
                            dblNightDiff = parseFloat(dblNightDiff) + parseFloat(val);
                        }else if(ind == "OTPay"){
                            dblOTPay = parseFloat(dblOTPay) + parseFloat(val);
                        }else if(ind == "OTND"){
                            dblOTND = parseFloat(dblOTND) + parseFloat(val);
                        }else if(ind == "OtherTaxableEarnings"){
                            dblOtherTaxableEarnings = parseFloat(dblOtherTaxableEarnings) + parseFloat(val);
                        }else if(ind == "OtherNonTaxableEarnings"){
                            dblOtherNonTaxableEarnings = parseFloat(dblOtherNonTaxableEarnings) + parseFloat(val);
                        }else if(ind == "GrossPay"){
                            dblGrossPay = parseFloat(dblGrossPay) + parseFloat(val);
                        }else if(ind == "SSS"){
                            dblSSS = parseFloat(dblSSS) + parseFloat(val);
                        }else if(ind == "PHIC"){
                            dblPHIC = parseFloat(dblPHIC) + parseFloat(val);
                        }else if(ind == "HDMF"){
                            dblHDMF = parseFloat(dblHDMF) + parseFloat(val);
                        }else if(ind == "HDMFMP2"){
                            dblHDMFMP2 = parseFloat(dblHDMFMP2) + parseFloat(val);
                        }else if(ind == "TaxableIncome"){
                            dblTaxableIncome = parseFloat(dblTaxableIncome) + parseFloat(val);
                        }else if(ind == "WTax"){
                            dblWTax = parseFloat(dblWTax) + parseFloat(val);
                        }else if(ind == "LoanDeduction"){
                            dblLoanDeduction = parseFloat(dblLoanDeduction) + parseFloat(val);
                        }else if(ind == "OtherDeduction"){
                            dblOtherDeduction = parseFloat(dblOtherDeduction) + parseFloat(val);
                        }else if(ind == "TotalDeduction"){
                            dblTotalDeduction = parseFloat(dblTotalDeduction) + parseFloat(val);
                        }else if(ind == "NetPay"){
                            dblNetPay = parseFloat(dblNetPay) + parseFloat(val);
                        }
                    }else{
                        innerRowData.push(val);
                    }

                  });

                  createXLSLFormatObj.push(innerRowData);
                  intRowCnt = intRowCnt + 1;

              });
            
            //Total
            var innerRowData = [];   
            innerRowData.push("Total");
            innerRowData.push("");
            innerRowData.push(dblBasicPay);
            innerRowData.push(dblECOLA);
            innerRowData.push(dblLateAmount);
            innerRowData.push(dblUndertimeAmount);
            innerRowData.push(dblAbsentAmount);
            innerRowData.push(dblSL);
            innerRowData.push(dblVL);
            innerRowData.push(dblOL);
            innerRowData.push(dblNightDiff);
            innerRowData.push(dblOTPay);
            innerRowData.push(dblOTND);
            innerRowData.push(dblOtherTaxableEarnings);
            innerRowData.push(dblOtherNonTaxableEarnings);
            innerRowData.push(dblGrossPay);
            innerRowData.push(dblSSS);
            innerRowData.push(dblPHIC);
            innerRowData.push(dblHDMF);
            innerRowData.push(dblHDMFMP2);
            innerRowData.push(dblTaxableIncome);
            innerRowData.push(dblWTax);
            innerRowData.push(dblLoanDeduction);
            innerRowData.push(dblOtherDeduction);
            innerRowData.push(dblTotalDeduction);
            innerRowData.push(dblNetPay);
            createXLSLFormatObj.push(innerRowData);
            intRowCnt = intRowCnt + 1;

            var ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);
            for (var i = 6; i < intRowCnt; i++) {
                for (var c = 2; c < xlsHeader.length; c++){
                    var ExcelCol = ExcelColumn(i, c);
                    ws[ExcelCol].z = '#,##0.00_);\\(#,##0.00\\)';
                    ws[ExcelCol].t = 'n';
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
        $("#GeneratePayrollFilterLabel").text("Section");
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



