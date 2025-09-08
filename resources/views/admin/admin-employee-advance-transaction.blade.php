@extends('layout.adminweb')
@section('content')

<style>
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
    background:#475F7B;
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
                                    <li class="breadcrumb-item active">Employee Advance Transaction List
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
                                    <h4 class="card-title">Employee Advance</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <fieldset>
                                                    <div class="input-group">

                                                        <select id="selSearchStatus" class="form-control" style="background-image:none;">
                                                          <option value="">All Record</option>
                                                          <option disabled="disabled">[ Location Option ]</option>
                                                          <option value="PMC Davao">Location: PMC Davao</option>
                                                          <option value="PMC Agusan">Location: PMC Agusan</option>
                                                          <option disabled="disabled">[ Status Option ]</option>
                                                          <option value="Active">Status: Pending</option>
                                                          <option value="Inactive">Status: Approved</option>
                                                          <option value="Inactive">Status: Cancelled</option>
                                                        </select>

                                                  
                                                        <input type="text" class="form-control searchtext" placeholder="Search Here.." style="width: 39%;margin-left: 6px;">

                                                        <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border">
                                                            <i class="bx bx-search"></i>
                                                        </button>
                                                        
                                                   @if(Session::get('IS_SUPER_ADMIN') || Session::get('ALLOW_CREATE'))
                                                        <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="NewRecord()">
                                                            <i class="bx bx-plus"></i> New
                                                        </button>
                                                       @endif    

 
                                                     @if(Session::get('IS_SUPER_ADMIN') || Session::get('ALLOW_UPLOAD'))
                                                         <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="UploadExcelRecord()">
                                                           <i class="bx bx-upload"></i> Upload Advance Excel
                                                        </button>
                                                          @endif 

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
                                                        <th style="width:5% !important;">Payroll Period</th>
                                                        <th style="width:2% !important;">Year</th>
                                                        <th style="width:12% !important;">Cut Off</th>
                                                        <th>Employee No</th>
                                                        <th>Employee Name</th>
                                                        <th style="width:5% !important;">Intrst Amnt</th>
                                                        <th style="width:5% !important;">Advance Amnt</th>
                                                        <th style="width:5% !important;">Total Advance Amnt</th>
                                                        <th style="width:5% !important;">Amort Amnt</th>
                                                        <th style="width:8% !important;">Status</th>
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
    <div id="record-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Employee Advance Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">
                <input type="hidden" id="EmployeeAdvanceTransID" value="0" readonly>
                <input type="hidden" id="AdvanceTable" value="0" readonly>
                    <div class="row">                 
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Transaction Date: <span class="required_field">*</span></label>
                                <div class="div-percent">
                                  <input id="TransDate" type="text" class="form-control" value="{{date('m/d/Y',strtotime(date('Y-m-d')))}}" placeholder="mm/dd/yyyy" autocomplete="off" readonly><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;"></i> </span>
                                </div>
                          </fieldset>
                        </div>
                          <div class="col-md-4">
                            <fieldset class="form-group">
                              <label for="Status">Status: <span class="required_field">*</span></label>
                              <input id="Status" type="text" class="form-control text-readonly-color" placeholder="Status" readonly style="font-weight: bold;">
                            </fieldset>
                        </div>
                        <div class="col-md-4">
                        <fieldset class="form-group">
                            <label for="PayrollPeriodName">Payroll Period: <span class="required_field">* </span></label><span class="search-txt">(Type & search from the list)</span>
                             <div class="div-percent">
                                    <input id="PayrollPeriodID" type="hidden" value="0">
                                    <input id="PayrollPeriodYear" type="hidden" value="">
                                    <input id="PayrollPeriodCode" type="hidden" value="">
                                   <input id="PayrollPeriodName" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-complete-type="payroll-period" placeholder="Payroll Period"><span class='percent-sign'> <i class="bx bx-search" style="line-height: 21px;"></i> </span>
                                </div> 
                          </fieldset>
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Employee No: </label>
                             <div class="div-percent">                                   
                                   <input id="EmployeeNo" type="text" class="form-control" placeholder="Employee No" readonly>
                                </div> 
                          </fieldset>
                        </div>
                        <div class="col-md-8">
                          <fieldset class="form-group">
                            <label for="Status">Employee Name: <span class="required_field">* </span></label><span class="search-txt">(Type & search from the list)</span>
                             <div class="div-percent">
                                   <input id="EmployeeID" type="hidden" value="0">                                    
                                   <input id="EmployeeName" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-complete-type="employee" placeholder="Employee Name"><span class='percent-sign'> <i class="bx bx-search" style="line-height: 21px;"></i> </span>
                                </div> 
                          </fieldset>
                        </div>
                   </div>
                    <div class="row">
                        <div class="col-md-3">
                              <fieldset class="form-group">
                                <label for="Status">Cut Off: <span class="required_field">*</span></label>
                                <div class="form-group">
                                    <select id="CutOff" class="form-control select2">
                                       <option value="">Please Select</option>
                                        <option value="{{ config('app.PERIOD_1ST_HALF_ID') }}">1ST HALF</option>
                                        <option value="{{ config('app.PERIOD_2ND_HALF_ID') }}">2ND HALF</option>
                                        <option value="{{ config('app.PERIOD_EVERY_CUTOFF_ID') }}">EVERY CUTOFF</option>
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="Status">Reference No: </label>
                             <input id="ReferenceNo" type="text" class="form-control" placeholder="Reference No" autocomplete="off">
                          </fieldset>
                        </div>
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="Status">Date Issued : <span class="required_field">*</span></label>
                             <div class="div-percent">
                                   <input id="DateIssued" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" ><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;"></i> </span>
                                </div>
                          </fieldset>
                        </div>
                          <div class="col-md-3">
                            <fieldset class="form-group">
                            <label for="Status">Date Start Payment: <span class="required_field">*</span></label>
                            <div class="div-percent">
                                  <input id="DateStartPayment" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" ><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;"></i> </span>
                                </div>
                          </fieldset>
                        </div>
                    </div>
                    <div class="row">
                       <div class="col-md-3">
                           <fieldset class="form-group">
                            <label for="Status">Amortization Amount: <span class="required_field">*</span></label>
                             <input id="AmortizationAmnt" type="text" class="form-control DecimalOnly text-align-right" placeholder="Amortization Amount" autocomplete="off">
                          </fieldset>
                        </div>                   
                         <div class="col-md-3">
                            <fieldset class="form-group">
                            <label for="Status">Advance Amount: <span class="required_field">*</span></label>
                             <input id="AdvanceAmnt" type="text" class="form-control DecimalOnly compute_total_advance text-align-right" placeholder="Advance Amount" autocomplete="off">
                          </fieldset>
                        </div>
                        <div class="col-md-3">
                           <fieldset class="form-group">
                            <label for="Status">Interest Amount:</label>
                             <input id="InterestAmnt" type="text" class="form-control DecimalOnly compute_total_advance text-align-right" placeholder="Interest Amount" autocomplete="off">
                          </fieldset>
                        </div>
                       <div class="col-md-3">
                            <fieldset class="form-group">
                            <label for="Status">Total Advance Amount: <span class="required_field">*</span></label>
                             <input id="TotalAdvanceAmnt" type="text" class="form-control DecimalOnly text-align-right" placeholder="Total Advance Amount" autocomplete="off" style="border:#ccc 1px solid;" disabled>
                          </fieldset>
                        </div>
                     </div>
                    <div class="row">
                        <div class="col-xs-3 col-md-9"> </div> 
                         <div class="col-md-3">
                            <fieldset class="form-group">
                            <label for="Status">Total Payment Made: <span class="required_field">*</span></label>
                             <input id="TotalPayment" type="text" class="form-control DecimalOnly text-align-right" placeholder="Total Payment Amount" autocomplete="off" style="border:#ccc 1px solid;" disabled>
                          </fieldset>
                        </div>
                         
                      </div>  
                    <div class="row">
                        <div class="col-xs-3 col-md-9"> </div>    
                          <div class="col-md-3">
                            <fieldset class="form-group">
                            <label for="Status">Remaining Balance: <span class="required_field">*</span></label>
                             <input id="TotalBalance" type="text" class="form-control DecimalOnly text-align-right" placeholder="Balance Amount" autocomplete="off" style="border:#ccc 1px solid;" disabled>
                          </fieldset>
                        </div>
                      </div> 
                     <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                            <label for="Status">Remarks: </label>
                             <input id="Remarks" type="text" class="form-control" placeholder="Remarks" autocomplete="off" >
                          </fieldset>
                        </div>
                     </div>
                     <hr>
                      <div class="row" style="float:left;">
                       <button id="bntViewPaymentHistory" type="button" class="btn btn-primary ml-1" onclick="ViewPaymentHistory()">
                          <i class="bx bx-check d-block d-sm-none"></i>
                          <span class="d-none d-sm-block">View Payment History</span>
                       </button>
                   
                     </div>
                    <div class="row" style="float:right;">
                       <button id="btnSaveRecord" type="button" class="btn btn-primary ml-1" onclick="SaveRecord()">
                          <i class="bx bx-check d-block d-sm-none"></i>
                          <span class="d-none d-sm-block">Save</span>
                       </button>
                       <button class="btn btn-light-secondary" data-dismiss="modal" style="margin-left:10px;">
                          <i class="bx bx-x d-block d-sm-none"></i>
                           <span class="d-none d-sm-block">Cancel</span>
                       </button>
                     </div>
                </div>
          <!--       <div class="modal-footer">
               
                </div> -->
            </div>
        </div>
    </div>
    <!-- END MODAL -->

   <!-- UPLOAD MODAL -->
    <div id="upload-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title white-color">Advance Excel Uploader </h5>
                  
                </div>
                <div class="modal-body">
                    <div class="row">
                          <h5>Browse Advance Summary csv file:</h5>
                         <fieldset class="form-group">
                                <label for="myfile">Select files:</label>
                                 <input type="file" id="ExcelFile" name="ExcelFile" accept=".csv"/>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer">
                      <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>
                    <button  id="btnUploadTSSCSV" type="button" class="btn btn-primary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Upload CSV</span>
                    </button>              
                </div>
            </div>
        </div>
    </div>

  <!--EXCEL REVIEW MODAL --> 
 <div id="excel-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="left:10px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">

                <div class="modal-header">
                    <h5 class="modal-title white-color"> Review Excel Data: <span id="spnUploadedRecord">0</span> / <span id="spnExcelRecord">0</span> has uploaded from excel. </h5> 

                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>

                </div>
                <div class="modal-body">
                    <div class="table-responsive col-md-12 table_default_height">
                          <table id="tblList-Excel" class="table zero-configuration complex-headers border">
                             <thead>
                                <tr>
                                   <th></th>
                                    <th></th>
                                    <th style="width:5% !important;">Payroll Period</th>
                                    <th style="width:2% !important;">Year</th>
                                    <th style="width:12% !important;">Cut Off</th>
                                    <th>Employee No</th>
                                    <th>Employee Name</th>
                                    <th style="width:5% !important;">Intrst Amnt</th>
                                    <th style="width:5% !important;">Advance Amnt</th>
                                    <th style="width:5% !important;">Total Advance Amnt</th>
                                    <th style="width:5% !important;">Amort Amnt</th>
                                    <th style="width:5% !important;">Upload Status</th>
                                </tr>
                            </thead> 
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

                <div class="modal-footer" style="margin-top: -6px;"> 

                    <div style="float:left;width: 70%;text-align: left;">
                    <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 15px;">
                        -highlight in green are duplicate employee advance in excel data.
                    </p>
                    <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 15px;">
                       -highlight in red are data with issues either that employee code or payroll code does not match.  
                    </p>
                    </div>

                    <div style="float:right;width: 30%;text-align: right;">

                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>

                    <button id="btnUploadFinalRecord" type="button" class="btn btn-primary ml-1" >
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block" onclick="SaveFinalRecord()">Save As Final Record </span>
                    </button>
                    </div>   

                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->
</section>

<script type="text/javascript">

    var IsAdmin="{{Session::get('IS_SUPER_ADMIN')}}";
    var IsAllowEdit="{{Session::get('ALLOW_EDIT')}}";
    var IsAllowPrint="{{Session::get('ALLOW_PRINT')}}";

    //get Data via CSV
    const btnUploadSCV = document.getElementById('btnUploadTSSCSV').addEventListener('click',()=> {

     if($('#ExcelFile').get(0).files.length === 0) {
           showHasErrorMessage('','Browse and upload employee advance transaction csv file.');
           return;
       }
       else{
           clearMessageNotification();
           clearAdvanceTempTransaction();

           var reader = new FileReader();
           reader.readAsText($('#ExcelFile').get(0).files[0]);
           reader.onload = loadHandler;
       }
    })

 function loadHandler(event) {
    var csv = event.target.result;

    var vResult = csv.split("\n");

     getChunkCSVData(0,vResult);    
    //getCSVDataConvertToJSON(csv);
 }

//  function getCSVDataConvertToJSON(csv) {
//      // Newline split
//      var lines = csv.split("\n");
//      var headers = lines[0].split(",");
//      for (var i = 1; i < lines.length - 1; i++) {
//         var obj = {};
//         //Comma split
//         var currentline = lines[i].split(",");
//             for (var j = 0; j < headers.length; j++) {
//                obj[headers[j]] = currentline[j];
//             }
//         result.push(obj);
//     }
//     // OUTPUT JSON
//     console.log(result);
// }

function getChunkCSVData(vIndex,vResult){

    var pData = [];
    var intCntr = 1;
    var recPerBatch=100;

    var vCurrentDate=new Date();

    var vDataLen = vResult.length;
    var vLimit = (vDataLen < (vIndex + recPerBatch) ? vDataLen : (vIndex + recPerBatch));

    intCntr = 0;
    for (x=vIndex; x < vLimit; x++){

        var vData = vResult[x].split(',');

        vEmployeeAdvanceTransID = 0;
        vTransDate=new Date();

        vPayrollPeriodCode=(vData[0]!=undefined ? vData[0] : '');

        if(vPayrollPeriodCode=='END'){
            break;
        }

        vYear=(vData[1]!=undefined ? vData[1] :  vCurrentDate.getFullYear());  
        vEmployeeNo=(vData[2]!=undefined ? vData[2] : '');
                                  
        vCutOff=(vData[3]!=undefined ? vData[3] : '');
        vReferenceNo=(vData[4]!=undefined ? vData[4] : '');
        vDateIssued=(vData[5]!=undefined ? vData[5] : '');        

        vAdvanceAmnt=(vData[6]!=undefined ? parseFloat(vData[6],2) : 0);
        vInterestAmnt=(vData[7]!=undefined ? parseFloat(vData[7],2) : 0);
        vAmortizationAmnt=(vData[8]!=undefined ? parseFloat(vData[8],2) : 0);

        vDateStartPayment=(vData[9]!=undefined ? vData[9] : '');
        vRemarks=(vData[10]!=undefined ? vData[10] : '');

        vTotalAdvanceAmnt = parseFloat(vAdvanceAmnt) + parseFloat(vInterestAmnt);

        vStatus='Pending';
        vIsUploaded=1;

        pData[intCntr] = {
                AdvanceTransID: vEmployeeAdvanceTransID,
                Year: vYear,
                PayrollCode: vPayrollPeriodCode,
                TransDate: vTransDate,
                EmpNo: vEmployeeNo,
                CutOff: vCutOff,
                ReferenceNo: vReferenceNo,
                DateIssued: vDateIssued,
                AdvanceAmnt: vAdvanceAmnt,
                InterestAmnt: vInterestAmnt,
                TotalAdvanceAmnt: vTotalAdvanceAmnt,
                AmortizationAmnt: vAmortizationAmnt,
                DateStartPayment: vDateStartPayment,
                Remarks: vRemarks,
                IsUploaded:vIsUploaded,
                Status: vStatus            
                
            };

        intCntr = intCntr + 1;
    }

    $("#spnTotalData").text(vLimit +'/'+ parseInt(vDataLen-2));

    if(pData.length > 0){

        //SAVE Batch of data
        $.ajax({
            type: "post",
            url: "{{ route('do-save-advance-temp-transaction-batch') }}",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                AdvanceTempDataItems: pData
            },
            dataType: "json",
            success: function(data){

                buttonOneClick("btnUploadTSSCSV", "Upload CSV", false);

                if(data.Response =='Success'){
                  
                  $("#spnTotalData").hide();
                  $("#divLoader").hide();

                    $("#spnTotalData").text(vLimit +'/'+ parseInt(vDataLen-2));
                    getChunkCSVData(vIndex + pData.length,vResult);


                }else{
                      showHasErrorMessage('', data.ResponseMessage);
                    return; 
                }
            },
            error: function(data){

                $("#divLoader").hide();
                $("#divLoader1").hide();
                $("#spnTotalData").hide();

                buttonOneClick("btnUploadTSSCSV", "Uploading...", false);
                console.log(data.responseText);
               
            },
            beforeSend:function(vData){

                $("#divLoader").show(); 
                $("#divLoader1").show(); 
                $("#spnTotalData").show();

                $("#spnLoadingLabel").text('Uploading...');
                
                buttonOneClick("btnUploadTSSCSV", "", true);
            }
        });

    }else{
        showHasSuccessMessage('Employee Income/Deduction Transactiondata has successfully uploaded. Kindly check for any data  issues before final');

        $("#divLoader").hide();
        $("#divLoader1").hide();
        $("#spnTotalData").hide();

        $("#upload-modal").modal('hide');
        $("#spnLoadingLabel").text('Loading...');

        getAdvanceTempUploadedCount(vResult.length-2);
        getAdvanceTempRecordList(1)
        $("#excel-modal").modal();
    }
}
</script>

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

        "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                hasUploadError=parseInt(aData[10]);
                if(hasUploadError==1){
                     $(nRow).addClass('Error-Level');  
                }else if(hasUploadError==2){
                      $(nRow).addClass('Dupli-Level');
                }else{
                     $(nRow).addClass('Normal-Level');
                }
                
            },

            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false,
            "order": [[3, "asc" ]]
        });

        $('#tblList-Excel').DataTable( {
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ],

            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    hasUploadError=parseInt(aData[12]);
                    if(hasUploadError==1){
                      $(nRow).addClass('Error-Level');  
                    }else if(hasUploadError==2){
                      $(nRow).addClass('Dupli-Level');
                    }else{
                      $(nRow).addClass('Normal-Level');
                    }
                    
                },

            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false,
            "order": [[12, "desc" ]]
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

     $('.compute_total_advance').on('keyup', function (e) {

        vTotalAdvanceAmnt=0;
        vIntrstLoanAmnt=$("#InterestAmnt").val();
        vAdvanceAmnt=$("#AdvanceAmnt").val();

        vTotalAdvanceAmnt=parseFloat(vIntrstLoanAmnt=='' ? 0 : vIntrstLoanAmnt) + parseFloat(vAdvanceAmnt=='' ? 0 : vAdvanceAmnt);

        if(isNaN(vTotalAdvanceAmnt) || vTotalAdvanceAmnt=='undefined'){
            vTotalAdvanceAmnt=0;        
        }

        $("#TotalAdvanceAmnt").val(FormatDecimal(vTotalAdvanceAmnt,2));
       
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
            url: "{{ route('get-employee-advance-transaction-list')}}",
            dataType: "json",
            success: function(data){
                LoadRecordList(data.EmployeeAdvanceList);
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
                        "<div class='dropdown-menu dropdown-menu-right'>";

                    if(IsAdmin==1 || IsAllowEdit==1){
                          tdAction = tdAction + 
                            "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",1,true)'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Information" +
                            "</a>";
                        
                        }else{
                              tdAction = tdAction +
                            "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",1,false)'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "View Information" +
                            "</a>";
                         
                        }

                        if(IsAdmin==1 || IsAllowPrint==1 && vData.Status=='Approved'){

                            tdAction = tdAction +
                            "<a class='dropdown-item' href='javascript:void(0);' onclick='PrintAdvance(" + vData.ID + ")'>"+
                                "<i class='bx bx-printer mr-1'></i> " +
                                "Print Advance Information" +
                            "</a>";
                         
                          
                        }

                         tdAction = tdAction +  "</div>"+
                      
                    "</div>";
      
        tdPayrollPeriod = "<span>" + vData.PayrollPeriodCode + "</span>";
        tdYear = "<span>" + vData.Year + "</span>";
        tdCutOff = "<span>" + vData.CutOff   + "</span>";  

        tdCutOff = "";
        if(vData.CutOff == 1){
            tdCutOff += "<span>1ST HALF</span>";
        }else if(vData.CutOff == 2){
            tdCutOff += "<span>2ND HALF</span>";
        }else{
             tdCutOff += "<span>EVERY CUTOFF</span>";   
        }

        tdEmpNo = "<span>" + vData.EmployeeNumber + "</span>";
        tdEmpName = "<span>" + vData.FullName + "</span>";

        tdInterestAmount = "<span>" + FormatDecimal(vData.InterestAmount,2) + "</span>";
        tdAdvanceAmount = "<span>" + FormatDecimal(vData.AdvanceAmount,2) + "</span>";
        tdTotalAdvanceAmount = "<span>" + FormatDecimal(vData.TotalAdvanceAmount,2) + "</span>";
        tdAmortizationAmount = "<span>" + FormatDecimal(vData.AmortizationAmount,2) + "</span>";

        tdStatus = "";
        if(vData.Status == 'Approved'){
            tdStatus += "<span style='color:green;'> <i class='bx bx-check-circle'></i> Approved </span>";
        }else{
            tdStatus += "<span style='color:red;'> <i class='bx bx-x-circle'></i> Pending </span>";
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
                curData[2] = tdPayrollPeriod;
                curData[3] = tdYear;
                curData[4] = tdCutOff;
                curData[5] = tdEmpNo;
                curData[6] = tdEmpName;                
                curData[7] = tdInterestAmount;
                curData[8] = tdAdvanceAmount;
                curData[9] = tdTotalAdvanceAmount;                
                curData[10] = tdAmortizationAmount;
                curData[11] = tdStatus;

                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,
                    tdAction,
                    tdPayrollPeriod,
                    tdYear,
                    tdCutOff,
                    tdEmpNo,
                    tdEmpName,
                    tdInterestAmount,
                    tdAdvanceAmount,
                    tdTotalAdvanceAmount,
                    tdAmortizationAmount,
                    tdStatus
                ]).draw();          
        }
    }

      function Clearfields(){

        $("#EmployeeAdvanceTransID").val(0);
        $("#TransNo").val('');
        // $("#TransDate").val('');

        $("#EmployeeID").val(0);
        $("#EmployeeNo").val('');
        $("#EmployeeName").val('');

        $("#PayrollPeriodID").val(0);
        $("#PayrollPeriodYear").val('');
        $("#PayrollPeriodCode").val('');
        $("#PayrollPeriodName").val('');

        $("#ReferenceNo").val('');
        $("#DateIssued").val('');
        $("#DateStartPayment").val('');
        $("#AmortizationAmnt").val('');
        $("#InterestAmnt").val('');
        $("#AdvanceAmnt").val('');
        $("#TotalAdvanceAmnt").val('');
        $("#Remarks").val('');

        $("#TotalPayment").val(FormatDecimal(0,2));
        $("#TotalBalance").val(FormatDecimal(0,2));

        $("#CutOff").val('').change();
        $("#Status").val('').change();
        $("#btnSaveRecord").show();

       resetTextBorderToNormal();

    }


    function resetTextBorderToNormal(){

        $("#TransNo").css({"border":"#ccc 1px solid"});        
        $("#EmployeeName").css({"border":"#ccc 1px solid"});  
        
        $("#PayrollPeriod").css({"border":"#ccc 1px solid"});
        $("#PayrollPeriodName").css({"border":"#ccc 1px solid"});
        
        $("#ReferenceNo").css({"border":"#ccc 1px solid"}); 
        $("#DateIssued").css({"border":"#ccc 1px solid"});
        $("#DateStartPayment").css({"border":"#ccc 1px solid"});

        $("#AmortizationAmnt").css({"border":"#ccc 1px solid"});
        $("#InterestAmnt").css({"border":"#ccc 1px solid"});
        $("#AdvanceAmnt").css({"border":"#ccc 1px solid"});

        $("#Status").css({"border":"#ccc 1px solid"});

    }

    function NewRecord(){

        Clearfields();
        $("#AdvanceTable").val(1);
        $("#bntViewPaymentHistory").hide();
        $("#record-modal").modal();
    }

     function UploadExcelRecord(){

        Clearfields();
        $("#AdvanceTable").val(0);
        $("#spnExcelRecord").val(0);
        $("#ExcelFile").val('');
        $("#upload-modal").modal();
       
    }

    function ViewPaymentHistory(){

         showHasErrorMessage('','No payment made for this transaction yet.');
    }

    function EditRecord(vRecordID,vTable,vAllowEdit){

        if(vTable==0){
           var postURL="{{ URL::route('get-employee-advance-transaction-temp-info')}}";
           $("#AdvanceTable").val(0); // Temp Table
           $("#bntViewPaymentHistory").hide();
           
        }else{
          var postURL="{{ URL::route('get-employee-advance-transaction-info')}}";
          $("#AdvanceTable").val(1); // Final Table
          $("#bntViewPaymentHistory").show();
        }

        if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    EmployeeAdvanceTransID: vRecordID
                },
                url: postURL,
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.EmployeeAdvanceTransactionInfo != undefined){

                      Clearfields();

                        $("#EmployeeAdvanceTransID").val(data.EmployeeAdvanceTransactionInfo.ID);

                        $("#EmployeeID").val(data.EmployeeAdvanceTransactionInfo.EmployeeID);
                        $("#EmployeeNo").val(data.EmployeeAdvanceTransactionInfo.EmployeeNumber);

                        if(data.EmployeeAdvanceTransactionInfo.EmployeeID<=0){
                             $("#EmployeeName").val('');
                        }else{
                            $("#EmployeeName").val(data.EmployeeAdvanceTransactionInfo.EmployeeNumber+ ' - ' +data.EmployeeAdvanceTransactionInfo.FullName); 
                        }

                        if(data.EmployeeAdvanceTransactionInfo.PayrollPeriodID<=0){
                            $("#PayrollPeriodID").val(0);
                            $("#PayrollPeriodYear").val('');
                            $("#PayrollPeriodCode").val('');
                            $("#PayrollPeriodName").val('');
                        }else{
                            $("#PayrollPeriodID").val(data.EmployeeAdvanceTransactionInfo.PayrollPeriodID);
                            $("#PayrollPeriodYear").val(data.EmployeeAdvanceTransactionInfo.Year);
                            $("#PayrollPeriodCode").val(data.EmployeeAdvanceTransactionInfo.PayrollPeriodCode);
                            $("#PayrollPeriodName").val(data.EmployeeAdvanceTransactionInfo.PayrollPeriodCode+': '+ data.EmployeeAdvanceTransactionInfo.StartDateFormat + ' - '  + data.EmployeeAdvanceTransactionInfo.EndDateFormat);
                        }

                        $("#TransNo").val(data.EmployeeAdvanceTransactionInfo.TransactionNo);
                        $("#TransDate").val(data.EmployeeAdvanceTransactionInfo.TransactionDateFormat);
                        
                        $("#CutOff").val(data.EmployeeAdvanceTransactionInfo.CutOff).change();
                        $("#ReferenceNo").val(data.EmployeeAdvanceTransactionInfo.ReferenceNo);
                        $("#DateIssued").val(data.EmployeeAdvanceTransactionInfo.DateIssueFormat);
                        $("#DateStartPayment").val(data.EmployeeAdvanceTransactionInfo.PaymentStartDateFormat);
                        $("#AmortizationAmnt").val(FormatDecimal(data.EmployeeAdvanceTransactionInfo.AmortizationAmount,2));
                        $("#InterestAmnt").val(FormatDecimal(data.EmployeeAdvanceTransactionInfo.InterestAmount,2));
                        $("#AdvanceAmnt").val(FormatDecimal(data.EmployeeAdvanceTransactionInfo.AdvanceAmount,2));
                        $("#TotalAdvanceAmnt").val(FormatDecimal(data.EmployeeAdvanceTransactionInfo.TotalAdvanceAmount,2));

                        $("#TotalPayment").val(FormatDecimal(0,2));
                        $("#TotalBalance").val(FormatDecimal(0,2));
                       
                        $("#Remarks").val(data.EmployeeAdvanceTransactionInfo.Remarks);
                        $("#Status").val(data.EmployeeAdvanceTransactionInfo.Status).change();

                        buttonOneClick("btnSaveRecord", "Save", false);

                        if(vAllowEdit){
                            $("#btnSaveRecord").show();
                        }else{
                             $("#btnSaveRecord").hide();
                        }

                        $("#divLoader").hide();
                        $("#record-modal").modal();

                    }else{
                        $("#divLoader").hide();
                         toast('toast-error', data.ResponseMessage);
                         return;
                    }
                },
                error: function(data){
                    $("#divLoader").hide();
                    buttonOneClick("btnSaveRecord", "Save", false);

                 
                       console.log(data.responseText);
                  
                   
                },
                beforeSend:function(vData){
                    $("#divLoader").show();
                    buttonOneClick("btnSaveRecord", "", false);
                }
            });
        }
    }

    function SaveRecord(){

        vAdvanceTable=$("#AdvanceTable").val();

        if(vAdvanceTable==0){ //Temp Table
           var postURL="{{ URL::route('do-save-advance-temp-transaction')}}"; 
            vIsUploaded=1;          
        }else{ //Final Table
          var postURL="{{ URL::route('do-save-employee-advance-transaction')}}";
           vIsUploaded=1;
        }

        vEmployeeAdvanceTransID = $("#EmployeeAdvanceTransID").val();
        vTransDate= $("#TransDate").val();

        vEmpID= $("#EmployeeID").val();
        vEmpNo= $("#EmployeeNo").val();

        vCutOff= $("#CutOff").val();
        vYear= $("#PayrollPeriodYear").val();
        vPayrollPeriodID= $("#PayrollPeriodID").val();
        vPayrollPeriodCode= $("#PayrollPeriodCode").val();
                
        vReferenceNo= $("#ReferenceNo").val();
        vDateIssued= $("#DateIssued").val();
        vDateStartPayment= $("#DateStartPayment").val();
        vAmortizationAmnt= $("#AmortizationAmnt").val();
        
        vInterestAmnt= $("#InterestAmnt").val();
        vAdvanceAmnt= $("#AdvanceAmnt").val();
        vTotalAdvanceAmnt= $("#TotalAdvanceAmnt").val();
        
        vTotalPayment= $("#TotalPayment").val();
        vTotalBalance= $("#TotalBalance").val();
        
        vRemarks= $("#Remarks").val();
        vStatus= $("#Status").val();
        IsUploaded=vIsUploaded;

        var checkInput =true;
        checkInput=doCheckAdvanceInput();

        if(checkInput==false){
            return;
        }
    
       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                EmployeeAdvanceTransID: vEmployeeAdvanceTransID,
                TransDate: vTransDate,
                EmpID: vEmpID,
                EmpNo: vEmpNo,
                CutOff: vCutOff,
                Year: vYear,
                PayrollID: vPayrollPeriodID,
                PayrollCode: vPayrollPeriodCode,
                ReferenceNo: vReferenceNo,
                DateIssued: vDateIssued,
                DateStartPayment: vDateStartPayment,
                AmortizationAmnt: vAmortizationAmnt,
                InterestAmnt: vInterestAmnt,
                AdvanceAmnt: vAdvanceAmnt,
                TotalAdvanceAmnt: vTotalAdvanceAmnt,
                TotalPayment: vTotalPayment,
                TotalBalance: vTotalBalance,
                Remarks: vRemarks,
                Status: vStatus,
                IsUploaded:vIsUploaded
            },
            url: postURL,
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

                   if(data.Response =='Success'){
                     showHasSuccessMessage(data.ResponseMessage);
                    if(vAdvanceTable==0){ // Temp Table
                      //LoadTempRecordRow(data.EmployeeAdvanceTempInfo);
                      $("#tblList-Excel").DataTable().clear().draw();
                      getAdvanceTempRecordList(1);
                    }else{ // Final Table
                       LoadRecordRow(data.EmployeeAdvanceTransactionInfo);
                    }

                    $("#record-modal").modal('hide');
                    $("#divLoader").hide();
                    return; 
                     
                }else{
                    showHasErrorMessage('', data.ResponseMessage);
                     $("#divLoader").hide();
                    return; 
                }
            },
            error: function(data){
                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

               
                console.log(data.responseText);
               
            },
            beforeSend:function(vData){
                $("#divLoader").show();
                buttonOneClick("btnSaveRecord", "", true);
            }
        });

    }

   // Load & Append data on scroll down
    $(window).scroll(function() {
        if(!isPageFirstLoad){
            if($(window).scrollTop() + $(window).height() >= ($(document).height() - 10) ){
                intCurrentPage = intCurrentPage + 1;
                getRecordList(intCurrentPage, $('.searchtext').val());
            }
        }
    });

function SaveFinalRecord(){

      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}'
        },
        url: "{{ route('do-upload-save-advance-transaction') }}",
        dataType: "json",
        success: function(data){
              if(data.Response =='Success'){
                  showHasSuccessMessage(data.ResponseMessage);
                  getRecordList(1, '');
                  $("#divLoader").hide(); 
                  $("#excel-modal").modal('hide');
                return;
              }else{
                showHasErrorMessage('', data.ResponseMessage);
                $("#divLoader").hide(); 
                return; 
              }
        },
        error: function(data){ 
            console.log(data.responseText);
        },
        beforeSend:function(vData){
            $("#divLoader").show(); 
        }
    });
 }

 function PrintAdvance(vTransID){

    window.open('http://localhost/philsagapayroll/admin-employee-advance-print-report?ReferenceID=' +vTransID, '_blank');
  
 }
 
  $(document).on('focus','.autocomplete_txt',function(){
      
       isEmployee=false;
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

                     if(isPayrollPeriod){
                      $("#PayrollPeriodID").val(seldata[0]);
                      $("#PayrollPeriodCode").val(seldata[1].trim());
                      $("#PayrollPeriodYear").val(seldata[2]);
                      $("#PayrollPeriodName").val(seldata[2] + ' - ' +seldata[3]);
                    }
              }
        });
    });

  function getAdvanceTempRecordList(vPageNo){

      $("#tblList-Excel").DataTable().clear().draw();
      $(".paginate_button").remove(); 

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                Status: 'Pending',
                Limit:10,
                PageNo: vPageNo
            },
            url: "{{ route('get-employee-advance-transaction-temp-list') }}",
            dataType: "json",
            success: function(data){

                total_rec=data.TotalRecord;
                LoadTempRecordList(data.EmployeeAdvanceTempList);

                  if(total_rec>0){
                     CreatePaging(total_rec);  
                     if(total_rec>10){
                        $("#divPaging").show(); 
                        $("#total-record").text(total_rec);
                        $("#paging_button_id"+vPageNo).css("background", "#0069d9");
                        $("#paging_button_id"+vPageNo).css("color", "#fff");
                     }
                  }
                $("#divLoader").hide();  
            },
            error: function(data){
                    console.log(data.responseText);
            },
            beforeSend:function(vData){
               $("#divLoader").show(); 
            }
        });
    };

    function CreatePaging(vTotalRecord){

       var i;
       paging_button="";
    
        limit=10; //get limit
        totalcount=vTotalRecord; //get total count
        pages=Math.ceil(totalcount/limit); //get pages
      
       if (pages!=1) {     

          paging_button="<li class='paginate_button page-item previous' id='example2_previous'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getAdvanceTempRecordList(1)'>First</a></li>"
          $(".ul-paging").append(paging_button);
       }
       
        if (pages>1) {        
          for (i = 1; i <= pages; i++) {            
            paging_button="<li class='paginate_button page-item'><a href='javascript:void(0)' id='paging_button_id"+i+"' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getAdvanceTempRecordList("+i+")'>"+i+"</a></li>"
             $(".ul-paging").append(paging_button);
          }
        }
          
       if (pages!=1) {            
        paging_button="<li class='paginate_button page-item next' id='example2_next'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getAdvanceTempRecordList("+pages+",)'>Last</a></li>"
        $(".ul-paging").append(paging_button);
        }    
   }

    function LoadTempRecordList(vList){

        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadTempRecordRow(vList[x]);
            }
        }
    }

    function LoadTempRecordRow(vData){

        var tblList = $("#tblList-Excel").DataTable();

        tdID = vData.ID;
        tdAction = "<div class='dropdown'>" + 
                        "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:#475F7B;'></span> " +
                        "<div class='dropdown-menu dropdown-menu-right'>";

                           if(vData.IsUploadError==2){
                               tdAction += "<a class='dropdown-item' href='javascript:void(0);' onclick='RemovedDuplicate(" + vData.ID + ")'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Removed Duplicate" +
                            "</a>";
                        }

                        if(vData.IsUploadError==1){
                            if(IsAdmin==1 || IsAllowEdit==1){
                              tdAction += "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",0,true)'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Information" +
                              "</a>";
                            }else{
                                  tdAction = tdAction +
                                "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",0,false)'>"+
                                    "<i class='bx bx-edit-alt mr-1'></i> " +
                                    "View Information" +
                                "</a>";
                            }
                        }

                        if(vData.IsUploadError==0){
                           if(IsAdmin==1 || IsAllowEdit==1){
                              tdAction += "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",0,true)'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Information" +
                              "</a>";
                            }else{
                                  tdAction = tdAction +
                                "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",0,false)'>"+
                                    "<i class='bx bx-edit-alt mr-1'></i> " +
                                    "View Information" +
                                "</a>";
                            }    
                        }

                         tdAction = tdAction +  "</div>"+
                      
                    "</div>";

        tdPayrollPeriodCode = "<span>" + vData.PayrollPeriodCode + "</span>";
        tdYear = "<span>" + vData.Year + "</span>";
        
        tdCutOff = "";
        if(vData.CutOff == 1){
            tdCutOff += "<span>1ST HALF</span>";
        }else if(vData.CutOff == 2){
           tdCutOff += "<span>2ND HALF</span>";
        }else{
            tdCutOff += "<span>EVERY CUTOFF</span>";
        }

        tdEmpNo = "<span>" + vData.EmployeeNumber + "</span>";
        tdEmpName = "<span>" + vData.FullName + "</span>";

        tdInterestAmount = "<span>" + FormatDecimal(vData.InterestAmount,2) + "</span>";
        tdAdvanceAmount = "<span>" + FormatDecimal(vData.AdvanceAmount,2) + "</span>";
        tdTotalAdvanceAmount = "<span>" + FormatDecimal(vData.TotalAdvanceAmount,2) + "</span>";
        tdAmortizationAmount = "<span>" + FormatDecimal(vData.AmortizationAmount,2) + "</span>";

        tdStatus = "";
        if(vData.Status == 'Approved'){
            tdStatus += "<span style='color:green;'> <i class='bx bx-check-circle'></i> Approved </span>";
        }else{
            tdStatus += "<span style='color:red;'> <i class='bx bx-x-circle'></i> Pending </span>";
        }

         tdIsUploadError = vData.IsUploadError;
       
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
                curData[2] = tdPayrollPeriodCode;
                curData[3] = tdYear;
                curData[4] = tdCutOff;
                curData[5] = tdEmpNo;
                curData[6] = tdEmpName;
                curData[7] = tdInterestAmount;                
                curData[8] = tdAdvanceAmount;                
                curData[9] = tdTotalAdvanceAmount;
                curData[10] = tdAmortizationAmount;
                curData[11] = tdStatus;
                curData[12] = tdIsUploadError;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,
                    tdAction,
                    tdPayrollPeriodCode,
                    tdYear,
                    tdCutOff,
                    tdEmpNo,
                    tdEmpName,
                    tdInterestAmount,
                    tdAdvanceAmount,
                    tdTotalAdvanceAmount,
                    tdAmortizationAmount,
                    tdStatus,
                    tdIsUploadError
                ]).draw();          
        }
    }

 function clearAdvanceTempTransaction(){

        $("#tblList-Excel").DataTable().clear().draw();

        $.ajax({
            type: "post",
            data: {
             _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}"
            },
            url: "{{ route('do-clear-advance-temp-transaction') }}",
            dataType: "json",
            success: function(data){
                
            },
            error: function(data){ 
                console.log(data.responseText);
            },
            beforeSend:function(vData){
                  $("#divLoader").show(); 
            }

        });
    } 

    function RemovedDuplicate(vRecID){

     intCurrentPage=1;

      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}',
            tempID: vRecID
        },
        url: "{{ route('do-remove-duplicate-advance-transaction') }}",
        dataType: "json",
        success: function(data){
              if(data.Response =='Success'){
                showHasSuccessMessage(data.ResponseMessage);
                // DeleteTableRow(vRecID);
                $("#tblList-Excel").DataTable().clear().draw();
                getAdvanceTempRecordList(1);
                $("#divLoader").hide(); 
                return;
              }
             
        },
        error: function(data){ 
            console.log(data.responseText);
        },  
        beforeSend:function(vData){
              $("#divLoader").show(); 
        }

    });
 }

 function getAdvanceTempUploadedCount(vExceRecord){

      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}'
        },
        url: "{{ route('get-advance-temp-transaction-upload-count') }}",
        dataType: "json",
        success: function(data){

          $("#spnUploadedRecord").text(data.MaxCount);
          $("#spnExcelRecord").text(vExceRecord);
             
        },
        error: function(data){ 
            console.log(data.responseText);
        },  
        beforeSend:function(vData){
 
        }

    });
 }

 function DeleteTableRow(vID){

        //Remove Row
        var vIsDeleted = false;
        var tblItemList = $("#tblList-Excel").DataTable();

        tblItemList.rows().every(function ( rowIdx, tableLoop, rowLoop ) {
            var rowData = this.data();
            if(!vIsDeleted){
                if(rowData[0] == vID){
                    if(rowData[1] > 0){
                        pDeletedItem[intDeletedItem] = rowData[1]; 
                        intDeletedItem = intDeletedItem + 1;
                    }

                    this.remove().draw();
                    vIsDeleted = true;
                }
            }
        });
    }

    function doCheckAdvanceInput(){

        var IsNotComplete=true;

        var vEmployeeAdvanceTransID = $("#EmployeeAdvanceTransID").val();
        var vTransNo = $("#TransNo").val();

        var vPayrollPeriodID = $("#PayrollPeriodID").val();
        var vPayrollPeriod = $("#PayrollPeriod").val();

        var vTransDate = $("#TransDate").val();
        var vEmployeeID = $("#EmployeeID").val();
        var vEmployeeNo = $("#EmployeeNo").val();
        var vEmployeeName = $("#EmployeeName").val();
        var vCutOff = $("#CutOff").val();
        var vReferenceNo = $("#ReferenceNo").val();
        var vDateIssued = $("#DateIssued").val();
        var vDateStartPayment = $("#DateStartPayment").val();
        var vAmortizationAmnt = $("#AmortizationAmnt").val();
        var vInterestAmnt = $("#InterestAmnt").val();
        var vAdvanceAmnt = $("#AdvanceAmnt").val();
        var vRemarks = $("#Remarks").val();
        
        var vStatus = $("#Status").val();

        resetTextBorderToNormal();

        if(vTransDate=="") {
         showHasErrorMessage('TransDate','Enter transaction date.');
         IsNotComplete=false;
         return IsNotComplete;
       }

       if(vStatus=="") {
         showHasErrorMessage('Status','Select status from the list.');
          IsNotComplete=false;
          return IsNotComplete;
       }

       if(vPayrollPeriod=="" || vPayrollPeriodID==0) {
         showHasErrorMessage('PayrollPeriodName','Select payroll period schedule from the list.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vEmployeeID<=0) {
         showHasErrorMessage('EmployeeName','Search and select employee from the list.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vCutOff.trim()=="") {
         showHasErrorMessage('CutOff','Select cutoff schedule from the list.');
          IsNotComplete=false;
          return IsNotComplete;
       }

       if(vDateIssued.trim()=="") {
         showHasErrorMessage('DateIssued','Enter date issued.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vDateStartPayment.trim()=="") {
         showHasErrorMessage('DateStartPayment','Enter date issued.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vAmortizationAmnt.trim()=="" || vAmortizationAmnt<=0 ) {
         showHasErrorMessage('AmortizationAmnt','Enter amortization amount.');
          IsNotComplete=false;
           return IsNotComplete;
       }

      if(vAdvanceAmnt.trim()=="" || vAdvanceAmnt<=0) {
         showHasErrorMessage('AdvanceAmnt','Enter advance amount.');
          IsNotComplete=false;
           return IsNotComplete;
       }      
 }
   
    
 $("#EmployeeName").blur(function() {
     vEmployee=$(this).val();
       if(vEmployee.length<=5 || vEmployee==''){
         $("#EmployeeID").val(0);
         $("#EmployeeNo").val('');
      }
  });
  
  $("#EmployeeName").keyup(function() { 
    vEmployee=$(this).val();
       if(vEmployee.length<=5 || vEmployee==''){
         $("#EmployeeID").val(0);
         $("#EmployeeNo").val('');
      }
    });

  $("#PayrollPeriodName").blur(function() {
     vPayrollPeriod=$(this).val();
       if(vPayrollPeriod.length<=5 || vPayrollPeriod==''){
        $("#PayrollPeriodID").val(0);
         $("#PayrollPeriodYear").val('');
         $("#PayrollPeriodCode").val('');
      }
  });
  
  $("#PayrollPeriodName").keyup(function() { 
    vPayrollPeriod=$(this).val();
       if(vPayrollPeriod.length<=5 || vPayrollPeriod==''){
         $("#PayrollPeriodID").val(0);
         $("#PayrollPeriodYear").val('');
         $("#PayrollPeriodCode").val('');
         
      }
    });

$( function() {    
    $( "#DateIssued").datepicker();
    $( "#DateStartPayment").datepicker();

  } );

$( document ).ready(function() {

      $('#record-modal').modal({
        backdrop: 'static',
        keyboard: true,
        show: false 
    });

    $('#excel-modal').modal({
        backdrop: 'static',
        keyboard: true,
        show: false 
    });

    $('#upload-modal').modal({
        backdrop: 'static',
        keyboard: true,
        show: false 
    });


});

function clearMessageNotification(){

      let toastMain1 = document.getElementsByClassName('toast-success')[0];
      toastMain1.classList.remove("toast-show");

       let toastMain2 = document.getElementsByClassName('toast-error')[0];
        toastMain2.classList.remove("toast-show");

      }

</script>

@endsection



