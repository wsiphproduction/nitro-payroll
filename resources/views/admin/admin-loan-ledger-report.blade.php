@extends('layout.adminweb')
@section('content')
<style>
    .btn.btn-icon{
       padding: 10px 50px;
           height: 45px;
           margin: 20px 0px; 
    }
</style>
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
                                    <li class="breadcrumb-item active">Employee Loan Ledger Report
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
                                    <h4 class="card-title">Employee Loan Ledger Report </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="col-md-4">
                                                            <fieldset class="form-group">
                                                                <label for="PayrollPeriodName">Payroll Period: <span class="required_field">* </span></label><span class="search-txt">(Type &amp; search from the list)</span>
                                                               
                                                                        <input id="PayrollPeriodID" type="hidden" value="0">
                                                                        <input id="PayrollPeriodYear" type="hidden" value="">
                                                                        <input id="PayrollPeriodCode" type="hidden" value="">
                                                                       <input id="PayrollPeriodName" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-complete-type="payroll-period" placeholder="Payroll Period" style="border: 1px solid rgb(204, 204, 204);">
                                                                  
                                                              </fieldset>
                                                            </div>                                        
                                                       
                                                       <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border">
                                                            Search <i class="bx bx-search"></i>
                                                        </button>

                                                      <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="Print()">
                                                            <i class="bx bx-printer"></i> Print All
                                                        </button>
                                    
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                         <div id="style-2" class="table-responsive col-md-12 table_default_height">
                                            <table id="tblList" class="table zero-configuration complex-headers border">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th></th>
                                                        <th>Transaction Date</th>
                                                        <th>Employee No</th>
                                                        <th>Employee Name</th>
                                                        <th>Payroll Period</th>
                                                        <th>Year</th>
                                                        <th>Deduction Amount</th>
                                                        <th>Net Amount</th>
                                                        <th>Status</th>
                                                    </tr>
                                                   </thead>
                                                  <tbody>
                                                </tbody>
                                            </table>
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

    <!-- MODAL -->
    <div id="record-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title white-color">SSS Table Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="SSS_ID" value="0" readonly>
                        <div class="row">
                            <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="Status">Year: <span class="required_field">*</span></label>
                                <div class="form-group">
                                    <select id="Year" class="form-control select2">
                                       <option value="">Please Select</option>
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                         <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="Status">Status: <span class="required_field">*</span></label>
                                <div class="form-group">
                                    <select id="Status" class="form-control select2">
                                        <option value="">Please Select</option>
                                        <option value="{{ config('app.STATUS_ACTIVE') }}">{{ config('app.STATUS_ACTIVE') }}</option>
                                        <option value="{{ config('app.STATUS_INACTIVE') }}">{{ config('app.STATUS_INACTIVE') }}</option>
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                    </div>
               
                    <div class="row">
                         <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="SalaryFrom">Salary From: <span class="required_field">*</span></label>
                                <input id="SalaryFrom" type="text" class="form-control DecimalOnly text-align-right" placeholder="Salary From" autocomplete="off">
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="SalaryTo">Salary To: <span class="required_field">*</span></label>
                                <input id="SalaryTo" type="text" class="form-control DecimalOnly text-align-right" placeholder="Salary To" autocomplete="off">
                            </fieldset>
                        </div>
                       
                    </div>
                     <div class="row">
                         <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="RegularSSEC">Regular SS/EC: <span class="required_field">*</span></label>
                                <input id="RegularSSEC" type="text" class="form-control DecimalOnly text-align-right" placeholder="Regular SS/EC" autocomplete="off">
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="RegularER">Regular ER: <span class="required_field">*</span></label>
                                <input id="RegularER" type="text" class="form-control DecimalOnly text-align-right" placeholder="Regular ER" autocomplete="off">
                            </fieldset>
                        </div>
                       
                    </div>
                    <div class="row">
                         <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="RegularEE">Regular EE: <span class="required_field">*</span></label>
                                <input id="RegularEE" type="text" class="form-control DecimalOnly text-align-right" placeholder="Regular EE" autocomplete="off">
                            </fieldset>
                        </div>
                         <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="ECC">ECC: <span class="required_field">*</span></label>
                                <input id="ECC" type="text" class="form-control DecimalOnly text-align-right" placeholder="EEC" autocomplete="off">
                            </fieldset>
                        </div>
    
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button id="btnSaveRecord" type="button" class="btn btn-primary ml-1" onclick="SaveRecord()">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Save</span>
                    </button>
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

     <!-- MODAL -->
    <div id="upload-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title white-color">SSS Excel Uploader</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                          <h5>Browse & select SSS table excel file:</h5>
                         <fieldset class="form-group">
                                <label for="myfile">Select files:</label>
                                 <input type="file" id="myfile" name="myfile" accept=".xlsx, .xls"/>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnSaveRecord" type="button" class="btn btn-primary ml-1" onclick="UploadExcelRecord()">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Upload</span>
                    </button>
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

</section>

<script type="text/javascript">

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
            "order": [[ 3, "asc" ]]
        });

        //Load Initial Data
        $("#tblList").DataTable().clear().draw();
        getRecordList(intCurrentPage, '');

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

    function getRecordList(vPageNo, vSearchText){

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SearchText: vSearchText,
                Status: $("#selSearchStatus").val(),
                PageNo: vPageNo
            },
            url: "{{ route('admin-get-report-list') }}",
            dataType: "json",
            success: function(data){
                LoadRecordList(data.ReportList);
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
        tdAction = "<div class='dropdown'>" + 
                        "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:#475F7B;'></span> " +
                        "<div class='dropdown-menu dropdown-menu-right'>" +
                            "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ")'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Information" +
                            "</a>"+
                          "</div>"+
                    "</div>";
      
        tdSalaryFrom = "<span>" + FormatDecimal(vData.RangeFrom,2) + "</span>";
        tdSalaryTo = "<span>" + FormatDecimal(vData.RangeTo,2) + "</span>";
        tdRegularSSEC = "<span>" + FormatDecimal(vData.RegularSSEC,2) + "</span>";
        tdRegularER = "<span>" + FormatDecimal(vData.RegularER,2) + "</span>";
        tdRegularEE = "<span>" + FormatDecimal(vData.RegularEE,2) + "</span>";
        tdECC = "<span>" + FormatDecimal(vData.ECC,2) + "</span>";
        tdYear = "<span>" + vData.Year + "</span>";

        tdStatus = "";

        if(vData.Status == 'Active'){
            tdStatus += "<span style='color:green;'> <i class='bx bx-check-circle'></i> Active </span>";
        }else{
            tdStatus += "<span style='color:red;'> <i class='bx bx-x-circle'></i> Inactive </span>";
        }

       
        //Check if record already listed
        var IsRecordExist = false;
        tblList.rows().every(function ( rowIdx, tableLoop, rowLoop ) {
            var rowData = this.data();
            if(rowData[0] == vData.ID){

                IsRecordExist = true;
                //Edit Row
                curData = tblList.row(rowIdx).data();
                curData[0] = tdID;
                curData[1] = tdAction;
                curData[2] = tdSalaryFrom;
                curData[3] = tdSalaryTo;
                curData[4] = tdRegularSSEC;
                curData[5] = tdRegularER;
                curData[6] = tdRegularEE;
                curData[7] = tdYear;
                curData[8] = tdStatus;

                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,
                    tdAction,
                    tdSalaryFrom,
                    tdSalaryTo,
                    tdRegularSSEC,
                    tdRegularER,
                    tdRegularEE,
                    tdYear,
                    tdStatus
                ]).draw();          
        }
    }

    function Print(){

      vDateFrom= $("#DateFrom").val();
      vDateTo= $("#DateTo").val();

       window.open('http://localhost/philsagapayroll/admin-employee-loan-ledger-print-report?DateFrom=' +vDateFrom +'&DateTo=' +vDateTo, '_blank');
    }


  $(document).on('focus','.autocomplete_txt',function(){
      
       isEmployee=false;
       isLoanType=false;
       isPayrollPeriod=false;
       var valAttrib  = $(this).attr('data-complete-type');
       
       if(valAttrib=='employee'){
            isEmployee=true;
            var postURL="{{ URL::route('get-employee-search-list')}}";
        }

        if(valAttrib=='payroll-period'){
            isPayrollPeriod=true;
            var postURL="{{ URL::route('get-payroll-period-search-list')}}";
        }

        if(valAttrib=='loantype'){
            isLoanType=true;
            var postURL="{{ URL::route('get-loan-type-search-list')}}";
        }
        
     $(this).autocomplete({
            source: function( request, response ) {
               if(request.term.length >= 2){
                        $.ajax({
                            method: 'post',
                            url: postURL,
                            dataType: "json",
                            data: {
                                _token: '{{ csrf_token() }}',
                                SearchText: request.term,
                               PageNo : 1,
                               Status : ""
                            },
                            success: function( data ) {
                                response( $.map( data, function( item ) {
                                  var code = item.split("|");
                                     
                                  if (isEmployee){
                                     return {
                                     label: code[1] +' - '+ code[5],
                                     value: code[1] +' - '+ code[5],
                                     data : item
                                    }
                                 }
                                   
                                  if (isLoanType){
                                     return {
                                     label: code[1] +' - '+ code[2],
                                     value: code[1] +' - '+ code[2],
                                     data : item
                                   }
                                }

                                if(isPayrollPeriod){
                                  return {
                                     label: code[1] +': '+ code[3] +' - '+ code[4],
                                     value: code[1] +': '+ code[3] +' - '+ code[4],
                                     data : item
                                    }  
                                 }
                                  
                              }));
                            },
                            error: function(data){
                                @if(config("app.DebugMode") == 0)
                                  console.log(data.responseText);
                                @endif
                            }
                        });
                     }
                },
                autoFocus: true,
                minLength: 0,
                appendTo: "#modal-fullscreen",
                select: function( event, ui ) {
                    
                    var seldata = ui.item.data.split("|");

                    if(isEmployee){
                      $("#EmployeeID").val(seldata[0]);
                      $("#EmployeeNo").val(seldata[1].trim());
                      $("#EmployeeName").val(seldata[4].trim());
                    }

                     if(isLoanType){
                      $("#LoanTypeID").val(seldata[0]);
                      $("#LoanTypeCode").val(seldata[1].trim());
                      $("#LoanTypeName").val(seldata[2].trim());
                    }

                    if(isPayrollPeriod){
                      $("#PayrollPeriodID").val(seldata[0]);
                      $("#PayrollPeriodCode").val(seldata[1].trim());
                      $("#PayrollPeriodYear").val(seldata[2]);
                      $("#PayrollPeriodName").val(seldata[2] + ' - ' +seldata[3]);
                    }

              }
        });
    });


   // Load & Append data on scroll down
    // $(window).scroll(function() {
    //     if(!isPageFirstLoad){
    //         if($(window).scrollTop() + $(window).height() >= ($(document).height() - 10) ){
    //             intCurrentPage = intCurrentPage + 1;
    //             getRecordList(intCurrentPage, $('.searchtext').val());
    //         }
    //     }
    // });

 $( function() {
    $("#DateFrom").datepicker();
    $("#DateTo").datepicker();
  });

</script>

@endsection



