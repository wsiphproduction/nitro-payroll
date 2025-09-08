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
                                    <li class="breadcrumb-item active"> Payroll Journal Report
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
                                    <h4 class="card-title"> Payroll Journal Report </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                    <div class="row">
                                            <div class="col-md-12">
                                                <fieldset>
                                                    <div class="input-group">
                                                     <input id="PageBatchNo" type="hidden" value="1">

                                                         <div class="col-md-2">
                                                            <label for="SearchYear">Year: </label>
                                                                 <select id="SearchYear" class="form-control">
                                                                    @php($CurYear = date("Y"))
                                                                    @for($x = $CurYear; $x >= 2023; $x--)
                                                                        <option value="{{ $x }}" {{ ($x == $CurYear ? "selected" : "") }}>{{ $x }}</option>
                                                                    @endfor
                                                                </select>        
                                                          </div>         
   
                                                        <div class="col-md-2">
                                                            <fieldset class="form-group">
                                                                <label for="GenerateFilter">Filter : </label>
                                                                    <select id="GenerateFilter" class="form-control">
                                                                        <option value="All" selected>All</option>
                                                                        <option value="Location">Location</option>
                                                                        <option value="Site">Site</option>
                                                                        <option value="Division">Division</option>
                                                                        <option value="Department">Department</option>
                                                                        <option value="Section">Section</option>
                                                                        <option value="Job Type">Job Type</option>
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
                                                                        @foreach($BranchSiteList as $siterow)
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
                                                            </fieldset>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <fieldset class="form-group">
                                                                <label for="Status">Status: </label>
                                                                    <select id="Status" class="form-control">
                                                                        <option value="Approved">Posted</option>
                                                                        <option value="Pending">Un-Posted</option>
                                                                    </select>
                                                            </fieldset>
                                                        </div>
               
        
                                                       <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border" tooltip="Search Here" tooltip-position="top"  style="padding: 0.4rem 0.6rem;height: 40px;margin-top: 23px;margin-left: -5px;">
                                                          <i class="bx bx-search"></i>
                                                        </button>

                                                     @if(Session::get('IS_SUPER_ADMIN') || $Allow_View_Print_Export==1)        
                                                      <!-- <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="Print()">
                                                            <i class="bx bx-printer"></i> Print
                                                        </button>      
                                                       -->


                                                        <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="GenerateExcel()" tooltip="Excel Report" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;margin-top: 23px;margin-left: -13px;">
                                                           <i class="bx bx-file"></i> 
                                                        </button>

                                                      @endif  
                                    
                                                    </div>
                                                </fieldset>                                                
                                            </div>
                                        </div>

                                        <div id="divList"> 
                                        </div>

                                        <div id="divModel" style="display: none;"> 
                                            <div id="divSet" style="margin-top: 10px; margin-bottom: 10px;">

                                                <div class="row">
                                                     <div class="col-md-12">
                                                        <fieldset class="form-group">
                                                            <label id="lblEmployeeID">Employee ID : </label>
                                                            <br>
                                                            <label id="lblEmployeeName">Employee Name : </label>
                                                        </fieldset>
                                                    </div>
                                                </div>

                                                <div id="style-2" class="table-responsive col-md-12">
                                                    <table id="tblList" class="table zero-configuration complex-headers border">
                                                        <thead>
                                                                <th style="min-width: 80px;">PERIOD</th>                                              
                                                                <th style="min-width: 80px; text-align: right;">BASIC PAY</th>                                              
                                                                <th style="min-width: 80px; text-align: right;">ECOLA</th>                                              
                                                                <th style="min-width: 60px; text-align: right;">LATE</th>
                                                                <th style="min-width: 80px; text-align: right;">UNDER TIME</th>
                                                                <th style="min-width: 60px; text-align: right;">ABSENT</th>
                                                                <th style="min-width: 60px; text-align: right;">VL</th>                                              
                                                                <th style="min-width: 60px; text-align: right;">SL</th>                                                       
                                                                <th style="min-width: 60px; text-align: right;">OL</th>                                                       
                                                                <th style="min-width: 80px; text-align: right;">NIGHT DIFF</th>                                  
                                                                <th style="min-width: 80px; text-align: right;">OVERTIME PAY</th>
                                                                <th style="min-width: 80px; text-align: right;">ND OVERTIME</th>
                                                                <th style="min-width: 120px; text-align: right;">OTHER TAXABLE EARNING </th> 
                                                                <th style="min-width: 120px; text-align: right;">OTHER NON TAXABLE EARNING </th> 
                                                                <th style="min-width: 80px; text-align: right;">GROSS PAY</th> 
                                                                <th style="min-width: 60px; text-align: right;">SSS</th> 
                                                                <th style="min-width: 60px; text-align: right;">SSS WISP</th> 
                                                                <th style="min-width: 80px; text-align: right;">PHIL HEALTH</th> 
                                                                <th style="min-width: 80px; text-align: right;">PAG IBIG</th> 
                                                                <th style="min-width: 80px; text-align: right;">PAG IBIG MP2</th> 
                                                                <th style="min-width: 110px; text-align: right;">TOTAL TAXABLE </th> 
                                                                <th style="min-width: 110px; text-align: right;">WTAX</th> 
                                                                <th style="min-width: 110px; text-align: right;">LOAN DEDUCTION</th> 
                                                                <th style="min-width: 110px; text-align: right;">OTHER DEDUCTION</th> 
                                                                <th style="min-width: 110px; text-align: right;">TOTAL DEDUCTION</th> 
                                                                <th style="min-width: 80px; text-align: right;">TOTAL NETPAY</th> 
                                                                <th style="min-width: 60px;">STATUS</th> 
                                                           </thead>
                                                          <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
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
            'language' : {
                "zeroRecords": " "             
            },
            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false,
            "order": [[ 0, "asc" ]]
        });

        //Load Initial Data
        $("#tblList").DataTable().clear().draw();
        // getRecordList(intCurrentPage);
        isPageFirstLoad = false;

    });

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
        }

    });

    $("#selSearchStatus").change(function(){
        intCurrentPage = 1;
        getRecordList(1, $('.searchtext').val());
    });

    $("#btnSearch").click(function(){
        intCurrentPage = 1;
        getRecordList(1, $('.searchtext').val());
    });

    $('.searchtext').on('keypress', function (e) {
        if(e.which === 13){
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
                PeriodYear: $("#SearchYear").val(),
                FilterType: $("#GenerateFilter").val(),
                BranchID: $("#GeneratePayrollBranch").val(),
                SiteID: $("#GeneratePayrollSite").val(),
                DivisionID: $("#GeneratePayrollDivision").val(),
                DepartmentID: $("#GeneratePayrollDepartment").val(),
                SectionID: $("#GeneratePayrollSection").val(),
                JobTypeID: $("#GeneratePayrollJobType").val(),
                Status: $("#Status").val(),
                Limit: vLimit,
                PageNo: vPageNo
            },
            url: "{{ route('get-payroll-journal-report-list') }}",
            dataType: "json",
            success: function(data){

                 if($('#divList').length){
                    $("#divList").empty();
                 }                     

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

                     LoadRecordList(data.PayrollJournalReport);
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

        var EmployeeID = 0;
        var newTableID = "";

        var prevTableID = "";
        var dblTotalBasicSalary = 0;
        var dblTotalECOLA = 0;
        var dblTotalLateAmount = 0;
        var dblTotalUndertimeAmount = 0;
        var dblTotalAbsentAmount = 0;
        var dblTotalVL = 0;
        var dblTotalSL = 0;
        var dblTotalOL = 0;
        var dblTotalND = 0;
        var dblTotalOT = 0;
        var dblTotalOTND = 0;
        var dblTotalOtherTaxableEarnings = 0;
        var dblTotalOtherNonTaxableEarnings = 0;
        var dblTotalGrossPay = 0;
        var dblTotalSSS = 0;
        var dblTotalSSSWISP = 0;
        var dblTotalPHIC = 0;
        var dblTotalHDMF = 0;
        var dblTotalHDMFMP2 = 0;
        var dblTotalTaxableIncome = 0;
        var dblTotalWTax = 0;
        var dblTotalLoanDeduction = 0;
        var dblTotalOtherDeduction = 0;
        var dblTotalDeduction = 0;
        var dblTotalNetPay = 0;
        
        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){

                if(EmployeeID != vList[x].EmployeeID){

                    if(x > 0){
                        prevTableID = newTableID;
                    }

                    EmployeeID = vList[x].EmployeeID;

                    var divModel = $("#divModel").clone();

                    divModel.find('div').each(function() {

                        var $element = $(this);
                        var newID = $element.attr('id') + "-" + vList[x].EmployeeID;

                        if($element.attr('id') == "divSet"){
                            $element.attr('id', newID);
                        }
                    });                        

                    divModel.find('label').each(function() {

                        var $element = $(this);
                        var newID = $element.attr('id') + "-" + vList[x].EmployeeID;

                        if($element.attr('id') == "lblEmployeeID"){
                            $element.text("Employee ID : " + vList[x].EmployeeNo);
                        }else if($element.attr('id') == "lblEmployeeName"){
                            $element.text("Employee Name : " + vList[x].EmployeeName);
                        }

                        $element.attr('id', newID);
                        $element.attr('name', newID);

                    });

                    var vTable = $(divModel).find('table');
                    newTableID = vTable.attr('id') + "-" + vList[x].EmployeeID;
                    vTable.attr('id', newTableID);

                    $("#divList").append(divModel.html());                    
                }

                //Footer
                if(prevTableID != ""){
                    var newRow = "<tr>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold;'>Total</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalBasicSalary,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalECOLA,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalLateAmount,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalUndertimeAmount,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalAbsentAmount,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalVL,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalSL,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalOL,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalND,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalOT,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalOTND,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalOtherTaxableEarnings,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalOtherNonTaxableEarnings,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalGrossPay,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalSSS,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalSSSWISP,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalPHIC,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalHDMF,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalHDMFMP2,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalTaxableIncome,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalWTax,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalLoanDeduction,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalOtherDeduction,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalDeduction,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalNetPay,2) + "</span></td>";

                    newRow += "<td></td>";
                    newRow += "</tr>";

                    $("#" + prevTableID + " tbody").append(newRow);

                    //Reset
                    prevTableID = "";

                    dblTotalBasicSalary = 0;
                    dblTotalECOLA = 0;
                    dblTotalLateAmount = 0;
                    dblTotalUndertimeAmount = 0;
                    dblTotalAbsentAmount = 0;
                    dblTotalVL = 0;
                    dblTotalSL = 0;
                    dblTotalOL = 0;
                    dblTotalND = 0;
                    dblTotalOT = 0;
                    dblTotalOTND = 0;
                    dblTotalOtherTaxableEarnings = 0;
                    dblTotalOtherNonTaxableEarnings = 0;
                    dblTotalGrossPay = 0;
                    dblTotalSSS = 0;
                    dblTotalSSSWISP = 0;
                    dblTotalPHIC = 0;
                    dblTotalHDMF = 0;
                    dblTotalHDMFMP2 = 0;
                    dblTotalTaxableIncome = 0;
                    dblTotalWTax = 0;
                    dblTotalLoanDeduction = 0;
                    dblTotalOtherDeduction = 0;
                    dblTotalDeduction = 0;
                    dblTotalNetPay = 0;
                }

                if(newTableID != ""){
                    var newRow = "<tr>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + vList[x].PayrollPeriodCode + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalBasicSalary,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalECOLA,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalLateAmount,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalUndertimeAmount,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalAbsentAmount,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalVL,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalSL,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalOL,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalND,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalOT,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalOTND,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalOtherTaxableEarnings,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalOtherNonTaxableEarnings,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalGrossPay,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalSSS,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalSSSWISP,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalPHIC,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalHDMF,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalHDMFMP2,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalTaxableIncome,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalWTax,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalLoanDeduction,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalOtherDeduction,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalDeduction,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span class='font-normal; float_right;'>" + FormatDecimal(vList[x].TotalNetPay,2) + "</span></td>";

                    if(vList[x].Status=='Un-Posted'){
                        newRow += "<td><span style='color:red;display:flex;'> Un-Posted </span></td>";
                    }else{
                        newRow += "<td><span style='color:green;display:flex;'> <i class='bx bx-check-circle'></i> Posted </span></td>";
                    }
                    newRow += "</tr>";

                    $("#" + newTableID + " tbody").append(newRow);

                    dblTotalBasicSalary = parseFloat(dblTotalBasicSalary) + parseFloat(vList[x].TotalBasicSalary);
                    dblTotalECOLA = parseFloat(dblTotalECOLA) + parseFloat(vList[x].TotalECOLA);
                    dblTotalLateAmount = parseFloat(dblTotalLateAmount) + parseFloat(vList[x].TotalLateAmount);
                    dblTotalUndertimeAmount = parseFloat(dblTotalUndertimeAmount) + parseFloat(vList[x].TotalUndertimeAmount);
                    dblTotalAbsentAmount = parseFloat(dblTotalAbsentAmount) + parseFloat(vList[x].TotalAbsentAmount);
                    dblTotalVL = parseFloat(dblTotalVL) + parseFloat(vList[x].TotalVL);
                    dblTotalSL = parseFloat(dblTotalSL) + parseFloat(vList[x].TotalSL);
                    dblTotalOL = parseFloat(dblTotalOL) + parseFloat(vList[x].TotalOL);
                    dblTotalND = parseFloat(dblTotalND) + parseFloat(vList[x].TotalND);
                    dblTotalOT = parseFloat(dblTotalOT) + parseFloat(vList[x].TotalOT);
                    dblTotalOTND = parseFloat(dblTotalOTND) + parseFloat(vList[x].TotalOTND);
                    dblTotalOtherTaxableEarnings = parseFloat(dblTotalOtherTaxableEarnings) + parseFloat(vList[x].TotalOtherTaxableEarnings);
                    dblTotalOtherNonTaxableEarnings = parseFloat(dblTotalOtherNonTaxableEarnings) + parseFloat(vList[x].TotalOtherNonTaxableEarnings);
                    dblTotalGrossPay = parseFloat(dblTotalGrossPay) + parseFloat(vList[x].TotalGrossPay);
                    dblTotalSSS = parseFloat(dblTotalSSS) + parseFloat(vList[x].TotalSSS);
                    dblTotalSSSWISP = parseFloat(dblTotalSSSWISP) + parseFloat(vList[x].TotalSSSWISP);
                    dblTotalPHIC = parseFloat(dblTotalPHIC) + parseFloat(vList[x].TotalPHIC);
                    dblTotalHDMF = parseFloat(dblTotalHDMF) + parseFloat(vList[x].TotalHDMF);
                    dblTotalHDMFMP2 = parseFloat(dblTotalHDMFMP2) + parseFloat(vList[x].TotalHDMFMP2);
                    dblTotalTaxableIncome = parseFloat(dblTotalTaxableIncome) + parseFloat(vList[x].TotalTaxableIncome);
                    dblTotalWTax = parseFloat(dblTotalWTax) + parseFloat(vList[x].TotalWTax);
                    dblTotalLoanDeduction = parseFloat(dblTotalLoanDeduction) + parseFloat(vList[x].TotalLoanDeduction);
                    dblTotalOtherDeduction = parseFloat(dblTotalOtherDeduction) + parseFloat(vList[x].TotalOtherDeduction);
                    dblTotalDeduction = parseFloat(dblTotalDeduction) + parseFloat(vList[x].TotalDeduction);
                    dblTotalNetPay = parseFloat(dblTotalNetPay) + parseFloat(vList[x].TotalNetPay);
                }

                if((x + 1) == vList.length){
                    var newRow = "<tr>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>Total</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalBasicSalary,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalECOLA,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalLateAmount,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalUndertimeAmount,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalAbsentAmount,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalVL,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalSL,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalOL,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalND,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalOT,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalOTND,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalOtherTaxableEarnings,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalOtherNonTaxableEarnings,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalGrossPay,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalSSS,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalSSSWISP,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalPHIC,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalHDMF,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalHDMFMP2,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalTaxableIncome,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalWTax,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalLoanDeduction,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalOtherDeduction,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalDeduction,2) + "</span></td>";
                    newRow += "<td style='text-align: right;'><span style='font-weight:bold; float_right;'>" + FormatDecimal(dblTotalNetPay,2) + "</span></td>";

                    newRow += "<td></td>";
                    newRow += "</tr>";

                    $("#" + newTableID + " tbody").append(newRow);

                }


            }
        }
    }

   function Print(){
      if(total_rec<=0){
        showHasErrorMessage('','No record(s) found base on search criteria.');
         return;   
      }

      vPayrollPeriodYear= $("#SearchPayrollPeriodYear").val();
      window.open('{{config('app.url')}}admin-payroll-journal-print-report?YearCover=' +vPayrollPeriodYear, '_blank');
    }

   function GenerateExcel(){

        if(total_rec<=0){
        showHasErrorMessage('','No record(s) found base on search criteria.');
         return;   
      }
      
      showHasSuccessMessage('Please wait while generating Payroll Journal excel file.');  


        $.ajax({
              type: "post",
              data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    PeriodYear: $("#SearchYear").val(),
                    FilterType: $("#GenerateFilter").val(),
                    BranchID: $("#GeneratePayrollBranch").val(),
                    SiteID: $("#GeneratePayrollSite").val(),
                    DivisionID: $("#GeneratePayrollDivision").val(),
                    DepartmentID: $("#GeneratePayrollDepartment").val(),
                    SectionID: $("#GeneratePayrollSection").val(),
                    JobTypeID: $("#GeneratePayrollJobType").val(),
                    Status: $("#Status").val(),
                    Limit: 0,
                    PageNo: 0
              },
              url: "{{ route('get-payroll-journal-report-list') }}",
              dataType: "json",
              success: function(data){

                  if(data.Response=="Success"){
                       resultquery=data.PayrollJournalReport;
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

            var TotalBasicPay = 0;
            var TotalECOLA = 0;
            var TotalLate = 0;
            var TotalUnderTime = 0;
            var TotalAbsent = 0;
            var TotalVL = 0;
            var TotalSL = 0;
            var TotalOL = 0;
            var TotalND = 0;
            var TotalOT = 0;
            var TotalOTND = 0;
            var TotalTaxableEarning = 0;
            var TotalNonTaxableEarning = 0;
            var TotalGrossPay = 0;
            var TotalSSS = 0;
            var TotalSSSWISP = 0;
            var TotalPHIC = 0;
            var TotalHDMF = 0;
            var TotalHDMFMP2 = 0;
            var TotalTotalTaxable = 0;
            var TotalWTax = 0;
            var TotalLoanDeduction = 0;
            var TotalOtherDeduction = 0;
            var TotalDeduction = 0;
            var TotalNetPay = 0;

            var intRowCnt = 5;

            xlsRows=resultquery;

            var EmployeeID = 0;
            var IsNewSet = false; 
            var intCntr = 0;


              var xlsReportHeader = [
                                "NITRO PACIFIC",
                                "",
                                ""
                              ];
              createXLSLFormatObj.push(xlsReportHeader);
              xlsReportHeader = [
                                "Payroll Journal Report",
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
                "{{ strtoupper('Payroll Period') }}", 
                "{{ strtoupper('Basic Pay') }}", 
                "{{ strtoupper('ECOLA') }}",
                "{{ strtoupper('Late') }}", 
                "{{ strtoupper('Under Time') }}", 
                "{{ strtoupper('Absent') }}",
                "{{ strtoupper('VL') }}", 
                "{{ strtoupper('SL') }}",
                "{{ strtoupper('OL') }}", 
                "{{ strtoupper('Night Differential') }}",
                "{{ strtoupper('Overtime Pay') }}", 
                "{{ strtoupper('ND OT') }}", 
                "{{ strtoupper('Other Taxable Earning') }}", 
                "{{ strtoupper('Other Non Taxable Earning') }}",
                "{{ strtoupper('Gross Pay') }}",  
                "{{ strtoupper('SSS') }}", 
                "{{ strtoupper('SSS WISP') }}", 
                "{{ strtoupper('PHIC') }}", 
                "{{ strtoupper('HDMF') }}", 
                "{{ strtoupper('HDMF MP2') }}", 
                "{{ strtoupper('Total Taxable') }}",
                "{{ strtoupper('WTax') }}",
                "{{ strtoupper('Loan Deduction') }}",
                "{{ strtoupper('Other Deduction') }}", 
                "{{ strtoupper('Total Deduction') }}", 
                "{{ strtoupper('Net Pay') }}", 
                "{{ strtoupper('Status') }}"];
            
            $.each(xlsRows, function(index, value) {
                intCntr = intCntr + 1;
                var innerRowData = [];
                $.each(value, function(ind, val) {

                    var SpaceData = [];   
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");
                    SpaceData.push("");

                    if(ind == "EmployeeID"){
                        if(EmployeeID != val){

                            if(intCntr > 1){
                                //Set Totals

                                var TotalRowData = [];   
                                TotalRowData.push("Total");
                                TotalRowData.push(TotalBasicPay);
                                TotalRowData.push(TotalECOLA);
                                TotalRowData.push(TotalLate);
                                TotalRowData.push(TotalUnderTime);
                                TotalRowData.push(TotalAbsent);
                                TotalRowData.push(TotalVL);
                                TotalRowData.push(TotalSL);
                                TotalRowData.push(TotalOL);
                                TotalRowData.push(TotalND);
                                TotalRowData.push(TotalOT);
                                TotalRowData.push(TotalOTND);
                                TotalRowData.push(TotalTaxableEarning);
                                TotalRowData.push(TotalNonTaxableEarning);
                                TotalRowData.push(TotalGrossPay);
                                TotalRowData.push(TotalSSS);
                                TotalRowData.push(TotalSSSWISP);
                                TotalRowData.push(TotalPHIC);
                                TotalRowData.push(TotalHDMF);
                                TotalRowData.push(TotalHDMFMP2);
                                TotalRowData.push(TotalTotalTaxable);
                                TotalRowData.push(TotalWTax);
                                TotalRowData.push(TotalLoanDeduction);
                                TotalRowData.push(TotalOtherDeduction);
                                TotalRowData.push(TotalDeduction);
                                TotalRowData.push(TotalNetPay);
                                TotalRowData.push("");

                                createXLSLFormatObj.push(TotalRowData);
                                intRowCnt = intRowCnt + 1;

                                //Space
                                createXLSLFormatObj.push(SpaceData);
                                intRowCnt = intRowCnt + 1;

                                createXLSLFormatObj.push(SpaceData);
                                intRowCnt = intRowCnt + 1;

                            }

                            //New Set
                            EmployeeID = val;
                            IsNewSet = true;

                            TotalBasicPay = 0;
                            TotalECOLA = 0;
                            TotalLate = 0;
                            TotalUnderTime = 0;
                            TotalAbsent = 0;
                            TotalVL = 0;
                            TotalSL = 0;
                            TotalOL = 0;
                            TotalND = 0;
                            TotalOT = 0;
                            TotalOTND = 0;
                            TotalTaxableEarning = 0;
                            TotalNonTaxableEarning = 0;
                            TotalGrossPay = 0;
                            TotalSSS = 0;
                            TotalSSSWISP = 0;
                            TotalPHIC = 0;
                            TotalHDMF = 0;
                            TotalHDMFMP2 = 0;
                            TotalTotalTaxable = 0;
                            TotalWTax = 0;
                            TotalLoanDeduction = 0;
                            TotalOtherDeduction = 0;
                            TotalDeduction = 0;
                            TotalNetPay = 0;

                        }
                    }


                    if(ind == "EmployeeNo"){
                        if(IsNewSet){
                            var EmployeeInformation = [];   
                            EmployeeInformation.push("{{ strtoupper('Employee ID : ') }}" + val);
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            createXLSLFormatObj.push(EmployeeInformation);

                            intRowCnt = intRowCnt + 1;
                        }
                    }
                    if(ind == "EmployeeName"){
                        if(IsNewSet){
                            var EmployeeInformation = [];   
                            EmployeeInformation.push("{{ strtoupper('Employee Name : ') }}" + val);
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");
                            EmployeeInformation.push("");

                            createXLSLFormatObj.push(EmployeeInformation);
                            intRowCnt = intRowCnt + 1;

                            //Space
                            createXLSLFormatObj.push(SpaceData);
                            intRowCnt = intRowCnt + 1;

                            createXLSLFormatObj.push(xlsHeader);
                            intRowCnt = intRowCnt + 1;

                            IsNewSet = false;
                        }
                    }

                    if(ind == "PayrollPeriodCode" ||
                        ind == "TotalBasicSalary" ||
                        ind == "TotalECOLA" ||
                        ind == "TotalLateAmount" ||
                        ind == "TotalUndertimeAmount" ||
                        ind == "TotalAbsentAmount" ||
                        ind == "TotalSL" ||
                        ind == "TotalVL" ||
                        ind == "TotalOL" ||
                        ind == "TotalND" ||
                        ind == "TotalOT" ||
                        ind == "TotalOTND" ||
                        ind == "TotalOtherTaxableEarnings" ||
                        ind == "TotalOtherNonTaxableEarnings" ||
                        ind == "TotalGrossPay" ||
                        ind == "TotalSSS" ||
                        ind == "TotalSSSWISP" ||
                        ind == "TotalPHIC" ||
                        ind == "TotalHDMF" ||
                        ind == "TotalHDMFMP2" ||
                        ind == "TotalTaxableIncome" ||
                        ind == "TotalWTax" ||
                        ind == "TotalLoanDeduction" ||
                        ind == "TotalOtherDeduction" ||
                        ind == "TotalDeduction" ||
                        ind == "TotalNetPay" ||
                        ind == "Status"){

                        if(ind == "PayrollPeriodCode" || ind == "Status"){
                            innerRowData.push(val);
                        }else{

                            innerRowData.push(parseFloat(val));

                            if(ind == "TotalBasicSalary"){
                              TotalBasicPay = TotalBasicPay + parseFloat(val);
                            }else if(ind == "TotalECOLA"){
                              TotalECOLA = TotalECOLA + parseFloat(val);
                            }else if(ind == "TotalLateAmount"){
                              TotalLate = TotalLate + parseFloat(val);
                            }else if(ind == "TotalUndertimeAmount"){
                              TotalUnderTime = TotalUnderTime + parseFloat(val);
                            }else if(ind == "TotalAbsentAmount"){
                              TotalAbsent = TotalAbsent + parseFloat(val);
                            }else if(ind == "TotalVL"){
                              TotalVL = TotalVL + parseFloat(val);
                            }else if(ind == "TotalSL"){
                              TotalSL = TotalSL + parseFloat(val);
                            }else if(ind == "TotalOL"){
                              TotalOL = TotalOL + parseFloat(val);
                            }else if(ind == "TotalND"){
                              TotalND = TotalND + parseFloat(val);
                            }else if(ind == "TotalOT"){
                              TotalOT = TotalOT + parseFloat(val);
                            }else if(ind == "TotalOTND"){
                              TotalOTND = TotalOTND + parseFloat(val);
                            }else if(ind == "TotalOtherTaxableEarnings"){
                              TotalTaxableEarning = TotalTaxableEarning + parseFloat(val);
                            }else if(ind == "TotalOtherNonTaxableEarnings"){
                              TotalNonTaxableEarning = TotalNonTaxableEarning + parseFloat(val);
                            }else if(ind == "TotalGrossPay"){
                              TotalGrossPay = TotalGrossPay + parseFloat(val);
                            }else if(ind == "TotalSSS"){
                              TotalSSS = TotalSSS + parseFloat(val);
                            }else if(ind == "TotalSSSWISP"){
                              TotalSSSWISP = TotalSSSWISP + parseFloat(val);
                            }else if(ind == "TotalPHIC"){
                              TotalPHIC = TotalPHIC + parseFloat(val);
                            }else if(ind == "TotalHDMF"){
                              TotalHDMF = TotalHDMF + parseFloat(val);
                            }else if(ind == "TotalHDMFMP2"){
                              TotalHDMFMP2 = TotalHDMFMP2 + parseFloat(val);
                            }else if(ind == "TotalTaxableIncome"){
                              TotalTotalTaxable = TotalTotalTaxable + parseFloat(val);
                            }else if(ind == "TotalWTax"){
                              TotalWTax = TotalWTax + parseFloat(val);
                            }else if(ind == "TotalLoanDeduction"){
                              TotalLoanDeduction = TotalLoanDeduction + parseFloat(val);
                            }else if(ind == "TotalOtherDeduction"){
                              TotalOtherDeduction = TotalOtherDeduction + parseFloat(val);
                            }else if(ind == "TotalDeduction"){
                              TotalDeduction = TotalDeduction + parseFloat(val);
                            }else if(ind == "TotalNetPay"){
                              TotalNetPay = TotalNetPay + parseFloat(val);
                            }

                        }

                    }

            
                });

                createXLSLFormatObj.push(innerRowData);
                intRowCnt = intRowCnt + 1;

            });

            var ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);
            for (var i = 6; i < intRowCnt; i++) {

                for (var c = 0; c < xlsHeader.length; c++){
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
                XLSX.utils.book_append_sheet(wb, ws,"Posted Payroll Journal Sheet");  
            }else{
                XLSX.utils.book_append_sheet(wb, ws,"Un-Posted Payroll Journal Sheet");    
            }
        
            /* generate file and download */
            const wbout = XLSX.write(wb, { type: "array", bookType: "xlsx" });
            saveAs(new Blob([wbout], { type: "application/octet-stream" }), "Payroll-Journal-Report.xlsx");

            showHasSuccessMessage('Excel file has successfully created & downloaded at Download Folder');     
                          
     }


</script>

@endsection



