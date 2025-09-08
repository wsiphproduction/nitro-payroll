@extends('layout.adminweb')
@section('content')

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
                                    <li class="breadcrumb-item active"> Generate Employee Payslip
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
                                    <h4 class="card-title"> Generate Employee Payslip</h4>
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
                                                                        <select id="GeneratePayrollBranch" class="form-control">
                                                                            <option value="">Please Select</option>
                                                                            @foreach($BranchList as $brow)
                                                                            <option value="{{ $brow->ID }}">{{ $brow->BranchName }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                    <div id="divSite" class="div-percent">
                                                                        <select id="GeneratePayrollSite" class="form-control">
                                                                            <option value="">Please Select</option>
                                                                            @foreach($BranchSite as $siterow)
                                                                            <option value="{{ $siterow->ID }}">{{ $siterow->SiteName }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                    <div id="divDivision" class="div-percent" style="display:none;">
                                                                        <select id="GeneratePayrollDivision" class="form-control">
                                                                            <option value="">Please Select</option>
                                                                            @foreach($DivisionList as $divrow)
                                                                            <option value="{{ $divrow->ID }}">{{ $divrow->Division }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                    <div id="divDepartment" class="div-percent" style="display:none;">
                                                                        <select id="GeneratePayrollDepartment" class="form-control">
                                                                            <option value="">Please Select</option>
                                                                            @foreach($DepartmentList as $deptrow)
                                                                            <option value="{{ $deptrow->ID }}">{{ $deptrow->Department }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                    <div id="divSection" class="div-percent" style="display:none;">
                                                                        <select id="GeneratePayrollSection" class="form-control">
                                                                            <option value="">Please Select</option>
                                                                            @foreach($SectionList as $secrow)
                                                                            <option value="{{ $secrow->ID }}">{{ $secrow->Section }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                    <div id="divJobType" class="div-percent" style="display:none;">
                                                                        <select id="GeneratePayrollJobType" class="form-control">
                                                                            <option value="">Please Select</option>
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
               
        
                                                       <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border" tooltip="Search Here" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;margin-top: 23px;margin-left: -5px;">
                                                           <i class="bx bx-search"></i>
                                                        </button>

                                                     @if(Session::get('IS_SUPER_ADMIN') || $Allow_View_Print_Export==1)        
                                                      <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="Print()" tooltip="Print Payslip" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;margin-top: 23px;margin-left: -13px;">
                                                         <i class="bx bx-printer"></i> 
                                                      </button>

                                                      <!--   <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="GenerateExcel()">
                                                           <i class="bx bx-file"></i> Export To Excel
                                                        </button> -->

                                                      @endif  
                                    
                                                    </div>
                                                </fieldset>                                                
                                            </div>
                                        </div>

                                        <div id="style-2" class="table-responsive col-md-12 table_default_height">
                                            <table id="tblList" class="table zero-configuration complex-headers border">
                                                <thead>
                                                    <th></th>
                                                        <th style="min-width: 110px;">EMPLOYEE ID</th>                                              
                                                        <th style="min-width: 130px">EMPLOYEE NAME</th>                                              
                                                        <th style="min-width: 60px">PERIOD</th>                                              
                                                        <th style="min-width: 80px">BASIC PAY</th>                                              
                                                        <th style="min-width: 60px">ECOLA</th>                                              
                                                        <th style="min-width: 60px">LATE</th>
                                                        <th style="min-width: 80px">UNDER TIME</th>
                                                        <th style="min-width: 80px;">ABSENT</th>
                                                        <th style="min-width: 60px;">VL</th>                                              
                                                        <th style="min-width: 60px;">SL</th>                                                       
                                                        <th style="min-width: 60px;">OL</th>                                                       
                                                        <th style="min-width: 90px;">NIGHT DIFF</th>                                  
                                                        <th style="min-width: 90px;">OVERTIME PAY</th>
                                                        <th style="min-width: 90px;">OT ND</th>
                                                        <th style="min-width: 130px;">OTHER TAXABLE EARNING </th> 
                                                        <th style="min-width: 130px;">OTHER NON TAXABLE EARNING </th> 
                                                        <th style="min-width: 80px;">GROSS PAY</th> 
                                                        <th style="min-width: 60px;">SSS</th>
                                                        <th style="min-width: 80px;">PHIL HEALTH</th>
                                                        <th style="min-width: 80px;">PAG IBIG</th>                                                        
                                                        <th style="min-width: 80px;">TOTAL TAXABLE </th> 
                                                        <th style="min-width: 60px;">WTAX</th> 
                                                        <th style="min-width: 80px;">LOAN DEDUCTION</th> 
                                                        <th style="min-width: 80px;">OTHER DEDUCTION</th> 
                                                        <th style="min-width: 80px;">TOTAL DEDUCTION</th> 
                                                        <th style="min-width: 80px;">TOTAL NET PAY</th> 
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
    
    var pages=0;
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
            "order": [[ 1, "asc" ]]
        });

        //Load Initial Data
        $("#tblList").DataTable().clear().draw();
        // getRecordList(intCurrentPage);

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

        vLimit=100;  

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                PayrollPeriodID: $("#SearchPayrollPeriodCode").val(),
                FilterType: $("#GenerateFilter").val(),
                BranchID: $("#GeneratePayrollBranch").val(),
                BranchSiteID: $("#GeneratePayrollSite").val(),
                DivisionID: $("#GeneratePayrollDivision").val(),
                DepartmentID: $("#GeneratePayrollDepartment").val(),
                SectionID: $("#GeneratePayrollSection").val(),
                JobTypeID: $("#GeneratePayrollJobType").val(),
                EmployeeID: $("#GeneratePayrollEmployee").val(),
                Status: "Approved",
                Limit: vLimit,
                PageNo: vPageNo
            },
            url: "{{ route('get-admin-employee-payslip-list') }}",
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
                        LoadRecordList(data.EmployeePaySlipReport);
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
                                                        
            tdID = vData.ID;
            
            tdEmployeeNo = "<span class='font-normal'>" + vData.EmployeeNo + "</span>";   
            tdEmployeeName = "<span class='font-normal'>" + vData.EmployeeName + "</span>";
            tdPayrollPeriodCode = "<span class='font-normal'>" + vData.PayrollPeriodCode + "</span>";

            tdBasicPay = "<span class='font-normal'>" + FormatDecimal(vData.BasicPay,2) + "</span>";
            tdECOLA = "<span class='font-normal'>" + FormatDecimal(vData.ECOLA,2) + "</span>";

            tdLate = "<span class='font-normal'>" + FormatDecimal(vData.LateAmount,2) + "</span>";
            tdUnderTime = "<span class='font-normal'>" + FormatDecimal(vData.UndertimeAmount,2) + "</span>";
            tdAbsent = "<span class='font-normal'>" + FormatDecimal(vData.AbsentAmount ,2) + "</span>";

            tdVL = "<span class='font-normal'>" + FormatDecimal(vData.VL,2) + "</span>";
            tdSL = "<span class='font-normal'>" + FormatDecimal(vData.SL,2) + "</span>";            
            tdOL = "<span class='font-normal'>" + FormatDecimal(vData.OL,2) + "</span>";

            tdNightDiff = "<span class='font-normal'>" + FormatDecimal(vData.NightDifferential,2) + "</span>"; 
            tdOvertimePay = "<span class='font-normal'>" + FormatDecimal(vData.OTPay,2) + "</span>"; 
            tdOvertimeND = "<span class='font-normal'>" + FormatDecimal(vData.OTND,2) + "</span>"; 

            tdOtherTaxableEarnings = "<span class='font-normal'>" + FormatDecimal(vData.OtherTaxableEarnings,2) + "</span>"; 
            tdOtherNonTaxableEarnings = "<span class='font-normal'>" + FormatDecimal(vData.OtherNonTaxableEarnings,2) + "</span>";  

            tdGrossPay = "<span class='font-normal'>" + FormatDecimal(vData.GrossPay,2) + "</span>";
            tdSSS = "<span class='font-normal'>" + FormatDecimal(vData.SSS,2) + "</span>";
            tdPHIC = "<span class='font-normal'>" + FormatDecimal(vData.PHIC,2) + "</span>";
            tdHDMF = "<span class='font-normal'>" + FormatDecimal(vData.HDMF,2) + "</span>";

            tdTaxableIncome = "<span class='font-normal'>" + FormatDecimal(vData.TaxableIncome,2) + "</span>";
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
                if(rowData[0] == vData.tdID){

                    IsRecordExist = true;
                    //Edit Row
                    curData = tblList.row(rowIdx).data();
                    curData[0] = tdID;                    
                    curData[1] = tdEmployeeNo;
                    curData[2] = tdEmployeeName;
                    curData[3] = tdPayrollPeriodCode;                    
                    curData[4] = tdBasicPay;                  
                    curData[5] = tdECOLA;                  
                    curData[6] = tdLate;                      
                    curData[7] = tdUnderTime;                      
                    curData[8] = tdAbsent;                                       
                    curData[9] = tdVL;                  
                    curData[10] = tdSL;                  
                    curData[11] = tdOL;                  
                    curData[12] = tdNightDiff;                  
                    curData[13] = tdOvertimePay;
                    curData[14] = tdOvertimeND;
                    curData[15] = tdOtherTaxableEarnings;
                    curData[16] = tdOtherNonTaxableEarnings;
                    curData[17] = tdGrossPay;
                    curData[18] = tdSSS;
                    curData[19] = tdPHIC;
                    curData[20] = tdHDMF;
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
                tdPayrollPeriodCode,                      
                tdBasicPay,
                tdECOLA,
                tdLate,
                tdUnderTime,
                tdAbsent,
                tdVL,
                tdSL,
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

  function Print(){

      vPageBatchNO=$("#PageBatchNo").val();

      vFilterType= $("#GenerateFilter").val();
      vFilterType = vFilterType =='' ? 'All' : vFilterType;

      vPayrollPeriodID= $("#SearchPayrollPeriodCode").val();
      vBranchID= $("#GeneratePayrollBranch").val();
      vBranchSiteID = $("#GeneratePayrollSite").val(),
      vDivisionID= $("#GeneratePayrollDivision").val();
      vDepartmentID= $("#GeneratePayrollDepartment").val();
      vSectionID= $("#GeneratePayrollSection").val();
      vJobTypeID= $("#GeneratePayrollJobType").val();
      vEmployeeID= $("#GeneratePayrollEmployee").val();

      
      if(total_rec<=0){
        showHasErrorMessage('','No record(s) found base on search criteria.');
         return;   
     }

    showHasSuccessMessage('Printing  Employee Payslip  Batch No: ' + vPageBatchNO + ' / ' + pages);  
    
    setTimeout(function (){     
       
       if(vFilterType=='All'){
        window.open('{{config('app.url')}}admin-employee-payslip-print-report?PayrollPeriodID=' +vPayrollPeriodID +'&FilterType='+ vFilterType +'&PrintingBatchNo=' +vPageBatchNO, '_blank');
       }

        if(vFilterType=='Location'){
        window.open('{{config('app.url')}}admin-employee-payslip-print-report?PayrollPeriodID=' +vPayrollPeriodID +'&FilterType='+ vFilterType +'&BranchID='+ vBranchID +'&PrintingBatchNo=' +vPageBatchNO, '_blank');
       }

        if(vFilterType=='Site'){
        window.open('{{config('app.url')}}admin-employee-payslip-print-report?PayrollPeriodID=' +vPayrollPeriodID +'&FilterType='+ vFilterType +'&BranchSiteID='+ vBranchSiteID +'&PrintingBatchNo=' +vPageBatchNO, '_blank');
       }

        if(vFilterType=='Division'){
         window.open('{{config('app.url')}}admin-employee-payslip-print-report?PayrollPeriodID=' +vPayrollPeriodID +'&FilterType='+ vFilterType +'&DivisionID='+ vDivisionID +'&PrintingBatchNo=' +vPageBatchNO, '_blank');
       }

        if(vFilterType=='Department'){
         window.open('{{config('app.url')}}admin-employee-payslip-print-report?PayrollPeriodID=' +vPayrollPeriodID +'&FilterType='+ vFilterType +'&DepartmentID='+ vDepartmentID +'&PrintingBatchNo=' +vPageBatchNO, '_blank');
       }

        if(vFilterType=='Section'){
       window.open('{{config('app.url')}}admin-employee-payslip-print-report?PayrollPeriodID=' +vPayrollPeriodID +'&FilterType='+ vFilterType +'&SectionID='+ vSectionID +'&PrintingBatchNo=' +vPageBatchNO, '_blank');
       }

        if(vFilterType=='Job Type'){
       window.open('{{config('app.url')}}admin-employee-payslip-print-report?PayrollPeriodID=' +vPayrollPeriodID +'&FilterType='+ vFilterType +'&JobTypeID='+ vJobTypeID +'&PrintingBatchNo=' +vPageBatchNO, '_blank');
       }

        if(vFilterType=='Employee'){
       window.open('{{config('app.url')}}admin-employee-payslip-print-report?PayrollPeriodID=' +vPayrollPeriodID +'&FilterType='+ vFilterType +'&EmployeeID='+ vEmployeeID +'&PrintingBatchNo=' +vPageBatchNO, '_blank');
       }
           
        let toastMain = document.getElementsByClassName('toast-success')[0];
               toastMain.classList.remove("toast-show");
        }, 2500);

 }
   function GenerateExcel(){

        if(total_rec<=0){
        showHasErrorMessage('','No record(s) found base on search criteria.');
         return;   
      }
      
      showHasSuccessMessage('Please wait while generating payroll journal excel file.');  


        $.ajax({
              type: "post",
              data: {
                  _token: '{{ csrf_token() }}',
                  Platform : "{{ config('app.PLATFORM_ADMIN') }}",          
                  PayrollPeriodID: $("#SearchPayrollPeriodCode").val(),                                      
                  FilterType: $("#GenerateFilter").val(),
                  BranchID: $("#GeneratePayrollBranch").val(),
                  BranchSiteID: $("#GeneratePayrollSite").val(),
                  DivisionID: $("#GeneratePayrollDivision").val(),
                  DepartmentID: $("#GeneratePayrollDepartment").val(),
                  SectionID: $("#GeneratePayrollSection").val(),
                  JobTypeID: $("#GeneratePayrollJobType").val(),
                  EmployeeID: $("#GeneratePayrollEmployee").val(),
                  Status: $("#Status").val(),          
              },
              url: "{{ route('get-excel-payroll-journal-list') }}",
              dataType: "json",
              success: function(data){

                  if(data.Response=="Success"){
                       resultquery=data.PayrollJournalExcelList;
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
                 
         // Excel Headers
          var xlsHeader = [
                            " Emlpoyee No "," Emlpoyee Name "," Period Month Code "," Basic Pay ", " ECOLA "," Late ", " Under Time ", " Absent ",
                            " VL ", " SL "," OL ", " Night Differential "," Overtime Pay "," ND Overtime  "," Other Taxable Earning ", 
                            " Other Non Taxable Earning "," Taxable Income "," Gross Pay ",  " SSS "," Phil Health ", " PagIbig ",
                            " Withholding Tax "," Loan Deduction "," Other Deduction ",  " Total Deduction ", " Net Pay " 
                          ];

          
            xlsRows=resultquery;
          
            createXLSLFormatObj.push(xlsHeader);

              $.each(xlsRows, function(index, value) {
                  var innerRowData = [];   
                  $.each(value, function(ind, val) {
                                      
                    innerRowData.push(val);

                  });

                  createXLSLFormatObj.push(innerRowData);
        
              });

            var ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);
            const countheader = Object.keys(createXLSLFormatObj); // columns name header to

             var wscols = [];
              for (var i = 0; i < countheader.length; i++) {  // columns length added
                 wscols.push({ wch: Math.max(countheader[i].toString().length + 27) })
              }
              
            ws['!cols'] = wscols;
                       
            /* File Name */
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws,"Payroll Journal Sheet");

            /* generate file and download */
            const wbout = XLSX.write(wb, { type: "array", bookType: "xlsx" });
            saveAs(new Blob([wbout], { type: "application/octet-stream" }), "Payroll-Journal-Report.xlsx");
                          
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

@endsection



