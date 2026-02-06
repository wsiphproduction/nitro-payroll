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
                                                        <th style="min-width: 120px;">TEAM LEADER</th>                                             
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
                                                        <th style="min-width: 110px;">RDD PAY</th>
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

            tdTeamLeader = "<span class='font-normal'>" + (vData.TeamLeader ?? 'NO TEAM LEADER') + "</span>";

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
            tdRDDPay = "<span class='font-normal'>" + FormatDecimal(vData.RDDPay,2) + "</span>"; 

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
                    curData[3] = tdTeamLeader;
                    curData[4] = tdBasicPay;
                    curData[5] = tdECOLA;
                    curData[6] = tdLate;                      
                    curData[7] = tdUnderTime;                      
                    curData[8] = tdAbsent;                      
                    curData[9] = tdSL;
                    curData[10] = tdVL;
                    curData[11] = tdOL;
                    curData[12] = tdNightDiff;
                    curData[13] = tdOvertimePay;  
                    curData[14] = tdRDDPay;   
                    curData[15] = tdOvertimeND;    
                    curData[16] = tdOtherTaxableEarnings;
                    curData[17] = tdOtherNonTaxableEarnings;
                    curData[18] = tdGrossPay;
                    curData[19] = tdSSS;
                    curData[20] = tdPHIC;
                    curData[21] = tdHDMF;   
                    curData[22] = tdHDMFMP2;
                    curData[23] = tdTaxableIncome;                    
                    curData[24] = tdWTax;
                    curData[25] = tdLoanDeduction;
                    curData[26] = tOtherDeduction;
                    curData[27] = tdTotalDeduction;
                    curData[28] = tdNetPay;
                    curData[29] = tdStatus;
                    
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
                tdRDDPay,
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
                "{{ strtoupper('Employee No.') }}",
                "{{ strtoupper('Employee Name') }}",
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
                "{{ strtoupper('RDD Pay') }}",
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
          
            // createXLSLFormatObj.push(xlsHeader);

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
            var dblLoanDeduction = 0;
            var dblOtherDeduction = 0;
            var dblTotalDeduction = 0;
            var dblNetPay = 0;

        // grand totals
        var gBasicPay = 0, gECOLA = 0, gLate = 0, gUndertime = 0, gAbsent = 0,
            gSL = 0, gVL = 0, gOL = 0, gNightDiff = 0, gOTPay = 0, gRDDPay = 0, gOTND = 0,
            gOtherTaxable = 0, gOtherNonTaxable = 0, gGrossPay = 0,
            gSSS = 0, gPHIC = 0, gHDMF = 0, gHDMFMP2 = 0,
            gTaxable = 0, gWTax = 0, gLoan = 0, gOtherDed = 0,
            gTotalDed = 0, gNetPay = 0;

        // team leader subtotals
        var tBasicPay = 0, tECOLA = 0, tLate = 0, tUndertime = 0, tAbsent = 0,
            tSL = 0, tVL = 0, tOL = 0, tNightDiff = 0, tOTPay = 0, tRDDPay = 0, tOTND = 0,
            tOtherTaxable = 0, tOtherNonTaxable = 0, tGrossPay = 0,
            tSSS = 0, tPHIC = 0, tHDMF = 0, tHDMFMP2 = 0,
            tTaxable = 0, tWTax = 0, tLoan = 0, tOtherDed = 0,
            tTotalDed = 0, tNetPay = 0;

        var currentTeamLeader = null;


        var currentTeamLeader = null;

        $.each(xlsRows, function(index, v) {

            // TEAM LEADER
            if (currentTeamLeader !== v.TeamLeader) {

                // sho previous subtotal
                if (currentTeamLeader !== null) {
                    createXLSLFormatObj.push(
                        pushSubtotalRow("SUBTOTAL", {
                            BasicPay: tBasicPay, ECOLA: tECOLA, Late: tLate,
                            Undertime: tUndertime, Absent: tAbsent,
                            SL: tSL, VL: tVL, OL: tOL,
                            NightDiff: tNightDiff, OTPay: tOTPay, RDDPay: tRDDPay, OTND: tOTND,
                            OtherTaxable: tOtherTaxable,
                            OtherNonTaxable: tOtherNonTaxable,
                            GrossPay: tGrossPay,
                            SSS: tSSS, PHIC: tPHIC, HDMF: tHDMF, HDMFMP2: tHDMFMP2,
                            Taxable: tTaxable, WTax: tWTax,
                            Loan: tLoan, OtherDed: tOtherDed,
                            TotalDed: tTotalDed, NetPay: tNetPay
                        })
                    );
                    createXLSLFormatObj.push([]);
                }

                // reset TL totals
                tBasicPay = tECOLA = tLate = tUndertime = tAbsent =
                tSL = tVL = tOL = tNightDiff = tOTPay = tRDDPay = tOTND =
                tOtherTaxable = tOtherNonTaxable = tGrossPay =
                tSSS = tPHIC = tHDMF = tHDMFMP2 =
                tTaxable = tWTax = tLoan = tOtherDed =
                tTotalDed = tNetPay = 0;

                // header block
                createXLSLFormatObj.push(["TEAM LEADER: " + v.TeamLeader]);
                createXLSLFormatObj.push([]);
                createXLSLFormatObj.push(xlsHeader);
                // createXLSLFormatObj.push(new Array(xlsHeader.length).fill("—"));

                currentTeamLeader = v.TeamLeader;
            }

            // EMPLOYEE ROW
            createXLSLFormatObj.push([
                v.EmployeeNo, v.EmployeeName, v.BasicPay, v.ECOLA, v.LateAmount,
                v.UndertimeAmount, v.AbsentAmount, v.SL, v.VL, v.OL,
                v.NightDiff, v.OTPay, v.RDDPay, v.OTND,
                v.OtherTaxableEarnings, v.OtherNonTaxableEarnings,
                v.GrossPay, v.SSS, v.PHIC, v.HDMF, v.HDMFMP2,
                v.TaxableIncome, v.WTax, v.LoanDeduction,
                v.OtherDeduction, v.TotalDeduction, v.NetPay, v.Status
            ]);

            // ACCUMULATE TOTALS
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
            tOtherTaxable += parseFloat(v.OtherTaxableEarnings, 2); gOtherTaxable += parseFloat(v.OtherTaxableEarnings, 2);
            tOtherNonTaxable += parseFloat(v.OtherNonTaxableEarnings, 2); gOtherNonTaxable += parseFloat(v.OtherNonTaxableEarnings, 2);
            tGrossPay += parseFloat(v.GrossPay, 2);       gGrossPay += parseFloat(v.GrossPay, 2);
            tSSS += parseFloat(v.SSS, 2);                 gSSS += parseFloat(v.SSS, 2);
            tPHIC += parseFloat(v.PHIC, 2);               gPHIC += parseFloat(v.PHIC, 2);
            tHDMF += parseFloat(v.HDMF, 2);               gHDMF += parseFloat(v.HDMF, 2);
            tHDMFMP2 += parseFloat(v.HDMFMP2, 2);         gHDMFMP2 += parseFloat(v.HDMFMP2, 2);
            tTaxable += parseFloat(v.TaxableIncome, 2);   gTaxable += parseFloat(v.TaxableIncome, 2);
            tWTax += parseFloat(v.WTax, 2);               gWTax += parseFloat(v.WTax, 2);
            tLoan += parseFloat(v.LoanDeduction, 2);      gLoan += parseFloat(v.LoanDeduction, 2);
            tOtherDed += parseFloat(v.OtherDeduction, 2); gOtherDed += parseFloat(v.OtherDeduction, 2);
            tTotalDed += parseFloat(v.TotalDeduction, 2); gTotalDed += parseFloat(v.TotalDeduction, 2);
            tNetPay += parseFloat(v.NetPay, 2);           gNetPay += parseFloat(v.NetPay, 2);
            tRDDPay += parseFloat(v.RDDPay, 2);           gRDDPay += parseFloat(v.RDDPay, 2);
        });


        // last team leader subtotal
        createXLSLFormatObj.push(
            pushSubtotalRow("SUBTOTAL", {
                BasicPay: tBasicPay, ECOLA: tECOLA, Late: tLate,
                Undertime: tUndertime, Absent: tAbsent,
                SL: tSL, VL: tVL, OL: tOL,
                NightDiff: tNightDiff, OTPay: tOTPay, RDDPay: tRDDPay, OTND: tOTND,
                OtherTaxable: tOtherTaxable,
                OtherNonTaxable: tOtherNonTaxable,
                GrossPay: tGrossPay,
                SSS: tSSS, PHIC: tPHIC, HDMF: tHDMF, HDMFMP2: tHDMFMP2,
                Taxable: tTaxable, WTax: tWTax,
                Loan: tLoan, OtherDed: tOtherDed,
                TotalDed: tTotalDed, NetPay: tNetPay
            })
        );

        // space
        createXLSLFormatObj.push([]);

        // GRAND TOTAL
        createXLSLFormatObj.push(
            pushSubtotalRow("GRAND TOTAL", {
                BasicPay: gBasicPay, ECOLA: gECOLA, Late: gLate,
                Undertime: gUndertime, Absent: gAbsent,
                SL: gSL, VL: gVL, OL: gOL,
                NightDiff: gNightDiff, OTPay: gOTPay, RDDPay: gRDDPay, OTND: gOTND,
                OtherTaxable: gOtherTaxable,
                OtherNonTaxable: gOtherNonTaxable,
                GrossPay: gGrossPay,
                SSS: gSSS, PHIC: gPHIC, HDMF: gHDMF, HDMFMP2: gHDMFMP2,
                Taxable: gTaxable, WTax: gWTax,
                Loan: gLoan, OtherDed: gOtherDed,
                TotalDed: gTotalDed, NetPay: gNetPay
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
        return [
            label, "",
            totals.BasicPay,
            totals.ECOLA,
            totals.Late,
            totals.Undertime,
            totals.Absent,
            totals.SL,
            totals.VL,
            totals.OL,
            totals.NightDiff,
            totals.OTPay,
            totals.RDDPay,
            totals.OTND,
            totals.OtherTaxable,
            totals.OtherNonTaxable,
            totals.GrossPay,
            totals.SSS,
            totals.PHIC,
            totals.HDMF,
            totals.HDMFMP2,
            totals.Taxable,
            totals.WTax,
            totals.Loan,
            totals.OtherDed,
            totals.TotalDed,
            totals.NetPay,
            ""
        ];
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



