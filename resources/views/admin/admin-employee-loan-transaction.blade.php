@extends('layout.adminweb')
@section('content')

<style>

.remaining_chars {
  font-size: 11px;
  color: #b62020;
  margin-top: 3px;
  float: right;
  font-weight: normal;
  text-transform: lowercase;
}
.custom-select:disabled{
    background-color: #F2F4F4 !important;
}
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

.highlighted {
    background: #ffffcc !important;
}

table.alt-background tr.selected td {
    background-color: #ffffcc !important; 
    color: black !important;
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
                                    <li class="breadcrumb-item active">Employee Loan Transaction List
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
                                    <h4 class="card-title">Employee Loan</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <fieldset>
                                                    <div class="input-group">
                                                       <select id="selSearchStatus" class="form-control" style="width:15%">
                                                          <option value="">All Record</option>
                                                          <option disabled="disabled">[ By Location ]</option>
                                                          @foreach($BranchList as $brnrow)
                                                          <option value="Location|{{ $brnrow->ID }}">Location : {{ $brnrow->BranchName }}</option>
                                                          @endforeach
                                                          <option disabled="disabled">[ By Site ]</option>
                                                          @foreach($BranchSite as $siterow)
                                                          <option value="Site|{{ $siterow->ID }}">Site : {{ $siterow->SiteName }}</option>
                                                          @endforeach
                                                          <option disabled="disabled">[ By Status ]</option>
                                                          <option value="Pending">Status: Pending</option>
                                                          <option value="Approved">Status: Approved</option>
                                                          <option value="Cancelled">Status: Cancelled</option>
                                                          <option disabled="disabled">[ By Payment ]</option>
                                                          <option value="Paid">Payment: Paid</option>
                                                          <option value="Balance">Payment: With Balance</option>
                                                        </select>
                                                  
                                                        <input type="text" class="form-control searchtext" placeholder="Search Here.." style="width: 39%;margin-left: 6px;">

                                                        <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border" tooltip="Search Here.." tooltip-position="top">
                                                            <i class="bx bx-search"></i>
                                                        </button>
                                                        
                                                      @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload==1)
                                                        <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="NewRecord()" tooltip="Create New" tooltip-position="top">
                                                            <i class="bx bx-plus"></i> New
                                                        </button>
                                                       @endif    

                                                       @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload==1)
                                                         <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="UploadExcelRecord()" tooltip="Upload Loan Excel" tooltip-position="top">
                                                           <i class="bx bx-upload"></i> Upload Loan Excel
                                                        </button>

                                                     @endif 

                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                         <div id="style-2" class="table-responsive col-md-12 table_default_height">
                                              <table id="tblList" class="table zero-configuration complex-headers border alt-background">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th style="width:1%;"></th>
                                                        <th style="width:8% !important;">FREQUENCY</th>
                                                        <th style="width:8% !important;">EMPLOYEE ID</th>
                                                        <th style="width:20% !important;">EMPLOYEE NAME</th>
                                                        <th style="width:8% !important;">CODE</th>
                                                        <th style="width:30% !important;">DESCRIPTION</th>
                                                        <th style="width:6% !important;">AMORT. AMOUNT</th>
                                                        <th style="width:6% !important;">TOTAL LOAN AMOUNT</th>
                                                        <th style="width:6% !important;">PAYMENT MADE</th>
                                                        <th style="width:6% !important;">REMAINING BALANCE</th>
                                                        <th style="width:10% !important;">STATUS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="divPaging" class="col-md-11" style="display: none;">   
                                           <hr style="margin-top:0px;margin-bottom:0px;">   
                                            <div style="width:110%;font-size: 11px;">
                                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                                    <ul class="pagination ul-paging scrollbar" style="overflow-x: auto;">
                                                        
                                                    </ul>
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

    <!--EMPLOYEE LOAN INFO MODAL -->
    <div id="record-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Employee Loan Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">                  
                <input type="hidden" id="EmployeeLoanTransID" value="0" readonly>
                <input type="hidden" id="LoanTable" value="0" readonly>

                    <div class="row">                 
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Transaction Date: <span class="required_field">*</span></label>
                                <div class="div-percent">
                                  <input id="TransDate" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" disabled><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;"></i> </span>
                                </div>
                          </fieldset>
                        </div>
                       
                          <div class="col-md-4">
                            <fieldset class="form-group">
                              <label for="Status">Status: <span class="required_field">*</span></label>
                              <input id="Status" type="text" class="form-control" placeholder="Status" disabled style="font-weight: bold;">
                            </fieldset>
                        </div>

                        <div class="col-md-4">
                             <fieldset class="form-group">
                                <label for="Status">Frequency: <span class="required_field">*</span></label>
                                <div class="form-group">
                                    <select id="CutOff" class="form-control">
                                       <option value="">Please Select</option>
                                        <option value="{{ config('app.PERIOD_1ST_HALF_ID') }}">1ST HALF</option>
                                        <option value="{{ config('app.PERIOD_2ND_HALF_ID') }}">2ND HALF</option>
                                        <option value="{{ config('app.PERIOD_EVERY_CUTOFF_ID') }}">EVERY CUTOFF</option>
                                    </select>
                                </div>
                            </fieldset>
                        </div>    
                    </div>
                    <div class="row">
                            <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Employee No: </label>                                                                                                  
                                <input id="EmployeeNo" type="text" class="form-control" placeholder="Employee No" disabled>                                
                          </fieldset>
                        </div>
                        <div class="col-md-8">
                          <fieldset class="form-group">
                            <label for="Status">Employee Name: <span class="required_field">* </span> </label> <span class="search-txt">(Type & search from the list)</span>
                             <div class="div-percent">
                                   <input id="EmployeeID" type="hidden" value="0">                                    
                                   <input id="EmployeeName" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-complete-type="employee" placeholder="Employee Name"><span class='percent-sign' onclick="ClearEmployee()"> <i class="bx bx-trash" style="line-height: 21px;cursor:pointer;" ></i> </span>
                                </div> 
                          </fieldset>
                        </div>
                   </div>

                    <div class="row">
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Loan Code: </label>                                                                                                  
                                <input id="LoanTypeCode" type="text" class="form-control" placeholder="Loan Code" disabled>                                
                          </fieldset>
                          </div>
                       <div class="col-md-8">
                          <fieldset class="form-group">
                               <label for="Status">Loan Type: <span class="required_field">*</span></label><span class="search-txt txt-capitalize">(Type & search from the list)</span>
                                <div class="div-percent">
                                   <input id="LoanTypeID" type="hidden" value="0">
                                   <input id="LoanTypeName" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-complete-type="loantype" placeholder="Loan Type Name"><span class='percent-sign' onclick="ClearLoanType()"> <i class="bx bx-trash" style="line-height: 21px;cursor:pointer;" ></i> </span>
                                </div>
                            </fieldset>
                        </div>
                   </div>
                    <div class="row">
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Voucher No: </label>
                             <input id="VoucherNo" type="text" class="form-control" placeholder="Voucher No" autocomplete="off">
                          </fieldset>
                        </div>
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Date Issued : <span class="required_field">*</span></label>
                             <div class="div-percent">
                                   <input id="DateIssued" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" ><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;" onclick="LoadCalendar('DateIssued')"></i> </span>
                                </div>
                          </fieldset>
                        </div>

                          <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status">Date Start Payment: <span class="required_field">*</span></label>
                            <div class="div-percent">
                                  <input id="DateStartPayment" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" ><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;" onclick="LoadCalendar('DateStartPayment')"></i> </span>
                                </div>
                          </fieldset>
                        </div>
                    </div>
                    <div class="row">
                         <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status">Loan Amount: <span class="required_field">*</span></label>
                             <input id="LoanAmnt" type="text" class="form-control DecimalOnly compute_total_loan text-align-right" placeholder="Loan Amount" autocomplete="off">
                          </fieldset>
                        </div>
                         <div class="col-md-4">
                           <fieldset class="form-group">
                            <label for="Status">Interest Amount: </label>
                             <input id="InterestAmnt" type="text" class="form-control DecimalOnly compute_total_loan text-align-right" placeholder="Interest Amount" autocomplete="off">
                          </fieldset>
                        </div>
                        <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status">Total Loan Amount: <span class="required_field">*</span></label>
                             <input id="TotalLoanAmnt" type="text" class="form-control DecimalOnly text-align-right" placeholder="Total Payment Amount" autocomplete="off" style="border:#ccc 1px solid;" disabled>
                          </fieldset>
                        </div>
                     </div>

                    <div class="row">
                        <div class="col-xs-4 col-md-8"> 
                            <fieldset id="SetOptions" class="fieldset-border" style="padding-bottom: 15px;border: 1px solid #DFE3E7; display: none;">
                              <legend class="legend-text">| Select From Options:  |</legend>
                               <div class="row">
                                 <div class="col-md-12">
                                    <fieldset class="form-group">
                                        <label for="DelayedOptions">Delayed Payment : <span class="required_field">*</span></label>
                                        <div class="form-group">
                                            <select id="DelayedOptions" class="form-control">
                                                <option value="">Please Select</option>
                                                <option value="1">Leave As Is & No Changes</option>
                                                <option value="2">Full Payment on Next Payroll</option>
                                                <option value="3">Add On To The Remaining Cut Off</option>                                            
                                            </select>
                                        </div>
                                     </fieldset>
                                </div>  
                              </div>                                                                                                                                                                      
                          </fieldset>                            
                        </div>       
                        
                        <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status">No. of Period Cut-Off To Pay : <span class="required_field">*</span></label>
                                  <input id="MonthsToPay" type="text" class="form-control text-align-right DecimalOnly compute_total_get_amortization" placeholder="No. of Periodof Cut-Off to Pay" autocomplete="off" >                            
                          </fieldset>
                        </div>                                 
                    </div>
                    <div class="row">
                         <div class="col-xs-4 col-md-8"> </div>                
                        <div id="MaringOption" class="col-md-4" style="margin-top: -82px;">
                           <fieldset class="form-group">
                            <label for="Status">Amortization Amount: <span class="required_field">*</span></label>
                             <input id="AmortizationAmnt" type="text" class="form-control DecimalOnly compute_total_get_months text-align-right" placeholder="Amortization Amount" autocomplete="off">
                          </fieldset>
                        </div>            
                    </div>   

                    <div id="divTotalPayment" class="row" style="margin-top: -11px;display:none;">
                        <div class="col-xs-4 col-md-8"> </div> 
                         <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status">Total Payment Made: <span class="required_field">*</span></label>
                             <input id="TotalPayment" type="text" class="form-control DecimalOnly text-align-right" placeholder="Total Payment Amount" autocomplete="off" style="border:#ccc 1px solid;" disabled>
                          </fieldset>
                        </div>                         
                      </div>  

                    <div id="divTotalBalance" class="row" style="display:none;">
                        <div class="col-xs-4 col-md-8"> </div>    
                          <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status">Remaining Balance: <span class="required_field">*</span></label>
                             <input id="TotalBalance" type="text" class="form-control DecimalOnly text-align-right" placeholder="Balance Amount" autocomplete="off" style="border:#ccc 1px solid;color: red !important;" disabled>
                          </fieldset>
                        </div>
                    </div> 

                    <div id="divRemarks" class="row" style="margin-top: -24px;">
                          <div class="form-group" style="width:100%;padding: 5px;">                             
                              <label for="Remarks">Remarks:    <span style="font-size: 11px;color: #b62020;  text-transform: lowercase;font-style: italic; font-weight: normal;"> ( characters: &nbsp; </span> <span class="remaining_chars"> 250</span></label><span style="font-size: 11px;color: #b62020;  text-transform: lowercase;font-style: italic; font-weight: normal;">&nbsp;)</span>
                            <textarea id="Remarks" class="form-control" rows="4"></textarea>                          
                       </div>
                   </div> 
                    <hr>
                    <div class="row" style="float:left;">
                       <button id="bntViewPaymentHistory" type="button" class="btn btn-primary ml-1" onclick="ViewPaymentLedgerHistory($('#EmployeeLoanTransID').val())">
                          <i class="bx bx-check d-block d-sm-none"></i>
                          <span class="d-none d-sm-block">View Payment History</span>
                       </button>
                   
                     </div>
                    <div class="row" style="float:right;">
                       <button id="btnSaveRecord" type="button" class="btn btn-primary ml-1" onclick="SaveRecord()">
                          <i class="bx bx-check d-block d-sm-none"></i>
                          <span class="d-none d-sm-block"> <i class='bx bx-save mr-1' style="font-size: 21px;"></i> Save</span>
                       </button>
                       <button id="btnCancelRecord" type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-left:10px;">
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


   <!-- LOAN UPLOAD -->
    <div id="upload-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title white-color">Loan Excel Uploader </h5> 
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>                
                </div>
                <div class="modal-body">
                    <div class="row">
                         <h5 style="padding-top:10px;padding-bottom: 10px;">Browse Loan Summary csv file:</h5>
                         <fieldset class="form-group">
                                <label for="myfile">Select files:</label>
                                  <input type="file" id="ExcelFile" accept=".csv" />
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer">

                    <a href="{{URL::asset('public/web/excel template/Employee-Loan-Summary-Template.csv')}}" id="btnDownloadTemplate" class="btn btn-light-secondary">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Download Template Format</span>
                    </a>
                    
                    <button id="btnUploadTSSCSV" type="button" class="btn btn-primary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Upload CSV </span>
                    </button>
               
                </div>
            </div>
        </div>
    </div>

     <!-- APPROVED MODAL -->
    <div id="approve-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background:green;">
                     <h5 class="modal-title white-color">Set Approve Employee Loan </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                         <fieldset class="form-group">
                                 <input type="hidden" class="LoanIDStatus" value="0" readonly>
                                <label style="text-transform: unset;">Do you want to approve this record?</label>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer" style="padding-right: 25px;">

                     <button id="btnApproveLoan" type="button" class="btn btn-primary ml-1" style="background:green !important;" onclick="SetLoanStatus('Approved')">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Set Approve</span>
                    </button>

                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>

                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

     <!--CANCEL MODAL -->
    <div id="cancel-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header" style="background:red;">
                    <h5 class="modal-title white-color">Set Cancel Employee Loan </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                         <fieldset class="form-group">
                                <input type="hidden" class="LoanIDStatus" value="0" readonly>
                               <label style="text-transform: unset;">Do you want to cancel this record?</label>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer" style="padding-right: 25px;">
                     <button id="btnCancelLoan" type="button" class="btn btn-primary ml-1" style="background:red !important;" onclick="SetLoanStatus('Cancelled')">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Set Cancel</span>
                    </button>

                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>

                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

    <!--SET MANUAL PAYMENT INFO MODAL -->
    <div id="payment-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel5" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Loan Manual Payment </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">                 
                 <div class="row">                 
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Transaction Date: <span class="required_field">*</span></label>
                                <div class="div-percent">
                                  <input id="viewTransDate" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" disabled><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;border: 1px solid rgb(204, 204, 204)"></i> </span>
                                </div>
                          </fieldset>
                        </div>
                       
                          <div class="col-md-4">
                            <fieldset class="form-group">
                              <label for="Status">Status: <span class="required_field">*</span></label>
                              <input id="viewStatus" type="text" class="form-control" placeholder="Status" disabled style="font-weight: bold;border: 1px solid rgb(204, 204, 204)">
                            </fieldset>
                        </div>

                        <div class="col-md-4">
                             <fieldset class="form-group">
                                <label for="Status">Frequency: <span class="required_field">*</span></label>
                                <div class="form-group">
                                    <input id="viewCutOff" type="text" class="form-control" placeholder="Frequency" disabled style="border: 1px solid rgb(204, 204, 204)">                                   
                                </div>
                            </fieldset>
                        </div>    
                    </div>
                    <div class="row">
                            <div class="col-md-4">
                          <fieldset class="form-group">
                              <label for="Status">Employee No: </label>                                               
                                <input id="viewEmployeeNo" type="text" class="form-control" placeholder="Employee No" disabled style="border: 1px solid rgb(204, 204, 204)">                                
                          </fieldset>
                        </div>
                        <div class="col-md-8">
                          <fieldset class="form-group">
                            <label for="Status">Employee Name: <span class="required_field">* </span> </label> 
                                   <input id="viewEmployeeName" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" placeholder="Employee Name" disabled style="border: 1px solid rgb(204, 204, 204)">
                             
                          </fieldset>
                        </div>
                   </div>

                    <div class="row">
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Loan Code: </label>
                                <input id="viewLoanTypeCode" type="text" class="form-control" placeholder="Loan Code" disabled style="border: 1px solid rgb(204, 204, 204)">                                
                          </fieldset>
                          </div>
                       <div class="col-md-8">
                          <fieldset class="form-group">
                               <label for="Status">Loan Type: <span class="required_field">*</span></label>                             
                                   <input id="viewLoanTypeID" type="hidden" value="0">
                                   <input id="viewLoanTypeName" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" placeholder="Loan Type Name" disabled style="border: 1px solid rgb(204, 204, 204)"> 
                                
                            </fieldset>
                        </div>
                   </div>
                    <div class="row">
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Voucher No: <span class="required_field">*</span></label>
                             <input id="viewVoucherNo" type="text" class="form-control" placeholder="Voucher No" autocomplete="off" disabled style="border: 1px solid rgb(204, 204, 204)">
                          </fieldset>
                        </div>
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Date Issued : <span class="required_field">*</span></label>
                             <div class="div-percent">
                                   <input id="viewDateIssued" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" disabled style="border: 1px solid rgb(204, 204, 204)"><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;"></i> </span>
                                </div>
                          </fieldset>
                        </div>
                          <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status">Date Start Payment: <span class="required_field">*</span></label>
                            <div class="div-percent">
                                  <input id="viewDateStartPayment" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" disabled style="border: 1px solid rgb(204, 204, 204)"><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;"></i> </span>
                                </div>
                          </fieldset>
                        </div>
                    </div>
                    <div class="row">                                    
                         <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status">Loan Amount: <span class="required_field">*</span></label>
                             <input id="viewLoanAmnt" type="text" class="form-control DecimalOnly text-align-right" placeholder="Loan Amount" autocomplete="off" disabled style="border: 1px solid rgb(204, 204, 204)">
                          </fieldset>
                        </div>
                         <div class="col-md-4">
                           <fieldset class="form-group">
                            <label for="Status">Interest Amount: </label>
                             <input id="viewInterestAmnt" type="text" class="form-control DecimalOnly text-align-right" placeholder="Interest Amount" autocomplete="off" disabled style="border: 1px solid rgb(204, 204, 204)">
                          </fieldset>
                        </div>
                        <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status">Total Loan Amount: <span class="required_field">*</span></label>
                             <input id="viewTotalLoanAmnt" type="text" class="form-control DecimalOnly text-align-right" placeholder="Total Loan Amount" autocomplete="off" style="border:#ccc 1px solid;border: 1px solid rgb(204, 204, 204)" disabled>
                          </fieldset>
                        </div>
                     </div>

                   <div class="row">
                        <div class="col-md-4">
                           <fieldset class="form-group">
                            <label for="Status">Amortization Amount: <span class="required_field">*</span></label>
                             <input id="viewAmortizationAmnt" type="text" class="form-control DecimalOnly text-align-right" placeholder="Amortization Amount" autocomplete="off" disabled style="border: 1px solid rgb(204, 204, 204);">
                          </fieldset>
                        </div>
                        <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status">No of Cut-Off Period To Pay: <span class="required_field">*</span></label>                            
                                  <input id="viewNoMontsToPay" type="text" class="form-control" placeholder="No of Cut-Off Period to Pay" autocomplete="off" disabled style="border: 1px solid rgb(204, 204, 204)">
                            
                          </fieldset>
                        </div>
                        <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status">Delayed Payment Options: <span class="required_field">*</span> </label>
                            <input id="viewDelayedOptions" type="text" class="form-control" placeholder="Delayed Payment Options" disabled style="border: 1px solid rgb(204, 204, 204)">                                   
                          </fieldset>
                        </div>
                    </div>

                    <div id="dvPaymentMade" class="row" style="display:none;">
                        <div class="col-xs-4 col-md-8"> </div> 
                        <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status">Total Payment Made: <span class="required_field">*</span></label>
                             <input id="viewTotalPayment" type="text" class="form-control DecimalOnly text-align-right" placeholder="Payment Made" autocomplete="off" disabled style="border: 1px solid rgb(204, 204, 204)">
                          </fieldset>
                        </div> 
                    </div>

                    <div id="dvRemainingBalance" class="row" style="display:none;">
                        <div class="col-xs-4 col-md-8"> </div> 
                        <div class="col-md-4">
                            <fieldset class="form-group">
                            <label for="Status"> Remaining Balance: <span class="required_field">*</span></label>
                             <input id="viewTotalBalance" type="text" class="form-control DecimalOnly text-align-right" placeholder="Remaining Balance" autocomplete="off" disabled style="color: red !important;border: 1px solid rgb(204, 204, 204);">
                          </fieldset>
                        </div> 
                    </div>

                   <div class="row">
                          <div class="form-group" style="width:100%;padding: 5px;">                             
                              <label for="viewRemarks">Remarks:</label>
                              <textarea id="viewRemarks" class="form-control" rows="4" disabled></textarea>                           
                       </div>
                   </div>
                                    
               <hr>
              <div class="col-md-12">                          
                      <div class="row" style="float:left;padding-left: 10px;padding-top: 10px;">
                         <h5>Payment History</h5>
                    </div>
 

                  @if($Allow_Edit_Update || Session::get('IS_SUPER_ADMIN'))
                     <div class="row" style="float:right;padding-right: 10px;padding-bottom: 6px;">
                        <button type="button" class="btn btn-primary ml-1" onclick="NewManualPaymentRecord()">
                          <i class="bx bx-check d-block d-sm-none"></i>
                          <span class="d-none d-sm-block"> <i class="bx bx-plus"></i> New Payment </span>
                        </button>
                    </div> 
                  @endif           
                          
                   <div id="style-2" class="table-responsive col-md-12 table_default_half_height">
                      <table id="tblEmployee-Manual-Payment-List" class="table zero-configuration complex-headers border">
                         <thead>
                            <tr>
                                <th></th>                                
                                <th style='color:#fff;'>DATE OF PAYMENT</th>
                                <th style='color:#fff;'>LOAN CODE</th>
                                <th style='color:#fff;'>DESCRIPTION</th>
                                <th style='color:#fff;'>AMOUNT PAID</th>                                
                            </tr>
                        </thead> 
                      </table>
                     </div> 

             
                </div>                         
                   <br>
                   <div class="modal-footer">
                           
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Close</span>
                    </button>              
                  </div>            
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

  <!--SAVE MANUAL PAYMENT MODAL -->
    <div id="new-payment-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel6" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Manual Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                 <input type="hidden" id="LoanPaymentTransactionID" value="0" readonly> 
                 <input type="hidden" class="EmployeeLoanTransactionID" value="0" readonly> 
                 <input type="hidden" class="EmployeeID" value="0" readonly> 
                                  
                   <div class="row">
                       <div class="col-md-6">
                          <fieldset class="form-group">
                            <label for="Status">Payment Date: <span class="required_field">*</span></label>
                             <div class="div-percent">                                   
                                   <input id="PaymentDate" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" ><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;" onclick="LoadCalendar('PaymentDate')"></i> </span>
                                </div>
                          </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="form-group">
                            <label for="EmployeeName">Payment Amount : <span class="required_field">*</span></label>
                                   <input id="PaymentLoanAmount" type="text" class="form-control DecimalOnly" autocomplete="off" placeholder="Payment Loan Amount">
                             </fieldset>
                           </div>                 
                    </div>
      
                      <div class="row">
                           <div class="col-md-12">
                            <fieldset class="form-group">
                            <label for="EmployeeName">Remarks: </label>                                   
                                   <textarea id="PaymentRemarks" class="form-control" rows="4"></textarea>
                             </fieldset>
                           </div>                 
                    </div>
                  </div>
                           
                   <div class="modal-footer" style="padding-right: 20px;">
                        <button id="btnSaveNewRate" type="button" class="btn btn-primary ml-1" onclick="SaveNewManualPaymentRecord()">
                          <i class="bx bx-check d-block d-sm-none"></i>
                          <span class="d-none d-sm-block"><i class='bx bx-save mr-1' style="font-size: 21px;"></i> Save</span>
                       </button>
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>              
                  </div> 

            </div>
      </div>      
  </div>    
    <!-- END MODAL -->

  <!--EXCEL REVIEW MODAL --> 
  <div id="excel-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel7" aria-hidden="true" style="left:10px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">

                <div class="modal-header">
                    <h5 class="modal-title white-color"> Review Excel: <span id="spnUploadedRecord">0</span> has uploaded from excel. <span id="spnUploadedErrorRecord"></span></h5> 
                    <input type="hidden" id="ErrorRecords" value="0" readonly>
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
                                    <th style="color: white;">FREQUENCY</th>
                                    <th style="color: white;">EMPLOYEE ID</th>
                                    <th style="color: white;">EMPLOYEE NAME</th>
                                    <th style="color: white;">CODE</th>
                                    <th style="color: white;">DESCRIPTION</th>
                                    <th style="color: white;">VOUCHER CODE</th>
                                    <th style="color: white;">INTEREST AMOUNT</th>
                                    <th style="color: white;">LOAN AMOUNT</th>
                                    <th style="color: white;">TOTAL LOAN</th>
                                    <th style="color: white;">AMORTIZATION AMOUNT</th>
                                    <th style="color: white;">STATUS</th>
                                    <th style="color: white;">UPLOAD STATUS</th>
                                </tr>
                            </thead> 
                    </table>            
                </div>

              <div id="divTempPaging" class="col-md-11" style="display: none;">   
               <hr style="margin-top:0px;margin-bottom:0px;">   
                <div style="width:110%;font-size: 11px;">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      <ul class="pagination ul-paging scrollbar" style="overflow-x: auto;"></ul>
                     </div>
                    </div>
              </div>

                <div class="modal-footer" style="margin-top: -6px;"> 

                    <div style="float:left;width: 70%;text-align: left;">
                     <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 16px;">
                        * Records highlighted in green are duplicate employee loan entries in the Excel file.
                    </p>
                    <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 16px;">
                       * Records highlighted in red are missing in employee references based on the employee code in the Excel file.
                    </p>
                     <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 16px;">
                      * Records highlighted in red can be also missing in loan references based on the loan code in the Excel file.
                    </p>
                    </div>

                    <div style="float:right;width: 30%;text-align: right;">
                        <button id="btnUploadFinalRecord" type="button" class="btn btn-primary ml-1" >
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block" onclick="SaveFinalRecord()"> <i class='bx bx-save mr-1' style="font-size: 21px;"></i> Save Final Record </span>
                        </button>

                        <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Cancel</span>
                        </button>
                    </div>   

                </div>
            </div>
        </div>
    </div>
  </div>  
    <!-- END MODAL -->

   <!--LEDGER LOAN INFO MODAL -->
    <div id="ledger-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel8" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Payment History </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">                  
                  <div id="style-2" class="table-responsive col-md-12 table_default_half_height">
                      <table id="tblEmployee-Ledger-Payment-List" class="table zero-configuration complex-headers border">
                         <thead>
                            <tr>
                                <th></th>                                
                                <th style="color:#fff;">DATE OF PAYMENT</th>
                                <th style="color:#fff;">PAYMENT REF.</th>
                                <th style="color:#fff;">LOAN CODE</th>
                                <th style="color:#fff;">DESCRIPTION</th>
                                <th style="color:#fff; text-align: right;">AMOUNT PAID</th>                                
                            </tr>
                        </thead> 
                      </table>
                     </div> 
                    <hr>
                     <div class="row">
                        <div class="col-md-9">
                        </div>
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="ledgerTotalPaymentMade">Total Loan  Amount:</label>
                             <input id="ledgerTotalLoanAmount" type="text" class="form-control" placeholder="Total Loan Amount" style="text-align:right;border: 1px solid rgb(204, 204, 204)" disabled>                             
                          </fieldset>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-md-9">
                        </div>
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="ledgerTotalPaymentMade">Total Payment Made:</label>
                             <input id="ledgerTotalPaymentMade" type="text" class="form-control" placeholder="Total Payment Made" style="text-align:right;border: 1px solid rgb(204, 204, 204)" disabled>                             
                          </fieldset>
                        </div>
                    </div> 
                     <div class="row">
                        <div class="col-md-9">
                        </div>
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="ledgerTotalPaymentMade">Remaining Balance:</label>
                             <input id="ledgerTotalRemainingBalance" type="text" class="form-control" placeholder="Remaining Balance" style="text-align:right;color:red !important;border: 1px solid rgb(204, 204, 204)" disabled>                             
                          </fieldset>
                        </div>
                    </div> 
                    <hr>
                
                    <div class="row" style="float:right;">
                       <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-left:10px;">
                          <i class="bx bx-x d-block d-sm-none"></i>
                           <span class="d-none d-sm-block">Close</span>
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

    var IsAllowPrint="{{$Allow_View_Print_Export}}";
    var IsAllowView="{{$Allow_View_Print_Export}}";

    var IsAllowEdit="{{$Allow_Edit_Update}}";
    var IsAllowCancel="{{$Allow_Delete_Cancel}}";
    var IsAllowApprove="{{$Allow_Post_UnPost_Approve_UnApprove}}";
  
    var ErrorStatusCounter=0;
    var intCurrentPage = 1;
    var isPageFirstLoad = true;
    var dblTotalLoanPayment = 0;

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
            "order": [[11, "desc" ]]
        });

          $('#tblList-Payment-List').DataTable( {
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
            "order": [[10, "asc" ]]
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
                    UploadErrorMsg=aData[13];
                    if(UploadErrorMsg=='No Record'){
                      $(nRow).addClass('Error-Level');  
                    }else if(UploadErrorMsg=='Duplicate'){
                      $(nRow).addClass('Dupli-Level');
                    }else{
                      $(nRow).addClass('Normal-Level');
                    }
                    
                },

            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : false,
            'info'        : false,
            'autoWidth'   : false,
            "order": [[12, "desc" ]]
        });

        $('#tblEmployee-Manual-Payment-List').DataTable( {
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
            'ordering'    : false,
            'info'        : false,
            'autoWidth'   : false
        });

          $('#tblEmployee-Ledger-Payment-List').DataTable( {
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
            'ordering'    : false,
            'info'        : false,
            'autoWidth'   : false
        });

        //Load Initial Data
        $("#tblList").DataTable().clear().draw();
        getRecordList(intCurrentPage);

        isPageFirstLoad = false;
        
        //EXCEL REVIEW FULL HIGHLIGHT
        var tblExcelList = $('#tblList-Excel').DataTable();
        $('#tblList-Excel tbody').on('click', 'tr', function() {            
            tblExcelList.$('tr.highlighted').removeClass('highlighted');        
            $(this).addClass('highlighted');
        });

    });

    //LOAN TRANSACTION
    $("#selSearchStatus").change(function(){
        $("#tblList").DataTable().clear().draw();
        intCurrentPage = 1;
        getRecordList(1);
    });

    $("#btnSearch").click(function(){
        $("#tblList").DataTable().clear().draw();
        intCurrentPage = 1;
        getRecordList(1);
    });

    $('.searchtext').on('keypress', function (e) {
        if(e.which === 13){
            $("#tblList").DataTable().clear().draw();
            intCurrentPage = 1;
            getRecordList(1);
        }
    });

    //PAYMENT SEARCH
    $("#btnSearchPayment").click(function(){
        $("#tblEmployee-Manual-Payment-List").DataTable().clear().draw();
        intCurrentPage = 1;
        getEmployeeLoanManualPaymentHistory(1,$('.EmployeeID').val(), $('.paymentsearchtext').val());
    });
    $('.paymentsearchtext').on('keypress', function (e) {
        if(e.which === 13){
            $("#tblEmployee-Manual-Payment-List").DataTable().clear().draw();
            intCurrentPage = 1;
            getEmployeeLoanManualPaymentHistory(1,$('.EmployeeID').val(), $('.paymentsearchtext').val());
        }
    });

    $(document).on('change keyup blur','.compute_total_loan',function(){
        RecomputeTotal();
    });

    function RecomputeTotal(){

        vTotalLoanAmnt=0;

        var vLoanAmnt = 0;
        if($('#LoanAmnt').length){
            if($("#LoanAmnt").val() != ""){
                var strValue = $("#LoanAmnt").val();
                vLoanAmnt = parseFloat(strValue.replace(",",""));
            }
        } 

        var vIntrstLoanAmnt = 0;
        if($('#InterestAmnt').length){
            if($("#InterestAmnt").val() != ""){
                var strValue = $("#InterestAmnt").val();
                vIntrstLoanAmnt = parseFloat(strValue.replace(",",""));
            }
        } 

        vTotalLoanAmnt = vLoanAmnt + vIntrstLoanAmnt;

        $("#TotalLoanAmnt").val(FormatDecimal(vTotalLoanAmnt,2));
       
    };

      $(document).on('change keyup blur','.compute_total_get_amortization',function(){
        RecomputGetAmortizationTotal();
    });

      function RecomputGetAmortizationTotal(){

        vAmortizationAmnt=0;

        var vTotalLoanAmnt = 0;
        if($('#TotalLoanAmnt').length){
            if($("#TotalLoanAmnt").val() != ""){
                var strValue = $("#TotalLoanAmnt").val();
                vTotalLoanAmnt = parseFloat(strValue.replace(",",""));
            }
        } 

        var vMonthsToPay = 0;
        if($('#MonthsToPay').length){
            if($("#MonthsToPay").val() != "" && $("#MonthsToPay").val() > 0){
                var strValue = $("#MonthsToPay").val();
                vMonthsToPay = parseFloat(strValue.replace(",",""));
            }
        } 

        vAmortizationAmnt = vTotalLoanAmnt / vMonthsToPay;

        if(vAmortizationAmnt === Infinity){
            vAmortizationAmnt=0;
        }

        if (isNaN(vAmortizationAmnt)) {
          vAmortizationAmnt=0;
         }

        $("#AmortizationAmnt").val(FormatDecimal(vAmortizationAmnt,2));
                            
    }


     $(document).on('change keyup blur','.compute_total_get_months',function(){
        RecomputGetMonthsToPay();
    });

   function RecomputGetMonthsToPay(){

       vMonthsToPay=0;

        var vTotalLoanAmnt = 0;
        if($('#TotalLoanAmnt').length){
            if($("#TotalLoanAmnt").val() != "" ){
                var strValue = $("#TotalLoanAmnt").val();
                vTotalLoanAmnt = parseFloat(strValue.replace(",",""));
            }
        } 

        var vAmortizationAmnt = 0;
        if($('#AmortizationAmnt').length){
            if($("#AmortizationAmnt").val() != "" && $("#AmortizationAmnt").val() > 0){
                var strValue = $("#AmortizationAmnt").val();
                vAmortizationAmnt = parseFloat(strValue.replace(",",""));
            }
        } 

         vMonthsToPay = vTotalLoanAmnt / vAmortizationAmnt;         
         vMonthsToPay=Math.ceil(vMonthsToPay);
        
        if(vMonthsToPay === Infinity){
            vMonthsToPay=0;
        }

        if (isNaN(vMonthsToPay)) {
          vMonthsToPay=0;
        }
        
        $("#MonthsToPay").val(vMonthsToPay);   
        
    }

    function getRecordList(vPageNo){

      $("#tblList").DataTable().clear().draw();
      $(".paginate_button").remove(); 
      vLimit=100;

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SearchText: $('.searchtext').val(),
                Status: $("#selSearchStatus").val(),
                Limit: vLimit,
                PageNo: vPageNo
            },
            url: "{{ route('get-employee-loan-transaction-list')}}",
            dataType: "json",
            success: function(data){
                total_rec=data.TotalRecord;   
                LoadRecordList(data.EmployeeLoanList);
                if(total_rec>0){
                     CreateEmployeeLoanPaging(total_rec,vLimit);  
                     if(total_rec>vLimit){
                        $("#divPaging").show(); 
                        $("#total-record").text(total_rec);
                        $("#paging_button_id"+vPageNo).css("background", "#0069d9");
                        $("#paging_button_id"+vPageNo).css("color", "#fff");
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

     function CreateEmployeeLoanPaging(vTotalRecord,vLimit){

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
        var Balance =0;

        tdID = vData.ID;

        Balance= vData.TotalLoanAmount - vData.TotalPayment;

        tdAction="";

        if(IsAdmin==1 || IsAllowView==1){

        tdAction = "<div class='dropdown'>";

                         if(vData.Status=='Pending'){
                             tdAction = tdAction + "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:red;'></span> ";  
                             tdAction = tdAction +  "<div class='dropdown-menu dropdown-menu-right'>"; 
                         }else if(vData.Status=='Approved'){
                            tdAction = tdAction + "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:green;'></span> ";
                            tdAction = tdAction +  "<div class='dropdown-menu dropdown-menu-right'>"; 
                          }else if(vData.Status=='Cancelled'){
                            tdAction = tdAction + "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:#f68c1f;'></span> ";
                            tdAction = tdAction +  "<div class='dropdown-menu dropdown-menu-right'>"; 
                         }
                        
                         if( vData.Status=='Pending' && (IsAdmin==1 || IsAllowEdit==1)){

                            tdAction = tdAction + 

                            "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",1,false)' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Employee Loan" +
                            "</a>";

                      } else if(vData.Status=='Approved' && IsAdmin==1){

                        tdAction = tdAction + 

                            "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",1,false)' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Employee Loan" +
                            "</a>";

                        }else{

                           tdAction = tdAction +

                          "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",1,true)' style='border-bottom: 1px solid lightgray;color: black;'>"+
                              "<i class='bx bx-search-alt mr-1'></i> " +
                              "View Employee Loan" +
                          "</a>";
                        }

                      if( vData.Status=='Pending' && (IsAdmin==1 || IsAllowApprove==1)){

                        tdAction = tdAction + 

                             "<a class='dropdown-item' href='javascript:void(0);' onclick='ApproveRecord(" + vData.ID + ")' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-check-circle mr-1'></i> " +
                                "Approve Employee Loan" +
                            "</a>";

                        }

                        if( vData.Status=='Pending' && (IsAdmin==1 || IsAllowCancel==1)){

                        tdAction = tdAction +  

                             "<a class='dropdown-item' href='javascript:void(0);' onclick='CancelRecord(" + vData.ID + ")' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-x-circle mr-1'></i> " +
                                "Cancel Employee Loan" +
                            "</a>";
                        
                        }

                    
                        if(vData.Status=='Approved' && vData.TotalPayment>0 ){

                             tdAction = tdAction +
                               "<a class='dropdown-item' href='javascript:void(0);' onclick='ViewPaymentLedgerHistory(" + vData.ID + ")' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-wallet mr-1'></i> " +
                                "View Payment History" +
                            "</a>";

                        }

                        if(vData.Status=='Approved' && (IsAdmin==1 || IsAllowEdit==1)){

                            tdAction = tdAction +

                              "<a class='dropdown-item' href='javascript:void(0);' onclick='SetManualPayment(" + vData.ID + ",1,true)' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-money mr-1'></i> " +
                                "Manual Payment " +
                            "</a>";
                        }


                      if(vData.Status=='Approved' && (IsAdmin==1 || IsAllowPrint==1)){

                        tdAction = tdAction +

                         "<a class='dropdown-item' href='javascript:void(0);' onclick='PrintLoan(" + vData.ID + ")' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-printer mr-1'></i> " +
                                "Print Loan" +
                            "</a>";
               
                         }
                 

                         tdAction = tdAction +  "</div>"+
                      
                    "</div>";

           }         

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

        tdLoanTypeCode = "<span>" + vData.LoanTypeCode + "</span>";
        tdLoanTypeName = "<span>" + vData.LoanTypeName + "</span>";

        tdIntrstAmount = "<span>" + FormatDecimal(vData.InterestAmount,2) + "</span>";
        tdLoanAmount = "<span>" + FormatDecimal(vData.LoanAmount,2) + "</span>";

        tdTotalLoanAmount = "<span>" + FormatDecimal(vData.TotalLoanAmount,2) + "</span>";
        tdAmortizationAmount = "<span>" + FormatDecimal(vData.AmortizationAmount,2) + "</span>";

        tdTotalPaymentMade = "<span>" + FormatDecimal(vData.TotalPayment,2) + "</span>";

        if(Balance <=0 ){
          tdRemainingBalance = "<span> 0.00 </span>";
        }else{
          tdRemainingBalance = "<span>" + FormatDecimal(vData.TotalLoanAmount - vData.TotalPayment,2) + "</span>";
        }

        tdStatus = "";
        if(vData.Status == 'Approved'){
            tdStatus += "<span style='color:green;display:flex;'> <i class='bx bx-check-circle'></i> Approved </span>";
        }
        if(vData.Status == 'Pending'){
            tdStatus += "<span style='color:red;display:flex;'> <i class='bx bx-x-circle'></i> Pending </span>";
        }
        if(vData.Status == 'Cancelled'){
            tdStatus += "<span style='color:#f68c1f;display:flex;'> <i class='bx bx-x-circle'></i> Cancelled </span>";
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
                curData[2] = tdCutOff;
                curData[3] = tdEmpNo;
                curData[4] = tdEmpName;
                curData[5] = tdLoanTypeCode;
                curData[6] = tdLoanTypeName;   
                curData[7] = tdAmortizationAmount;             
                curData[8] = tdTotalLoanAmount;
                curData[9] = tdTotalPaymentMade;
                curData[10] = tdRemainingBalance;
                curData[11] = tdStatus;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,
                    tdAction,
                    tdCutOff,
                    tdEmpNo,
                    tdEmpName,
                    tdLoanTypeCode,
                    tdLoanTypeName,  
                    tdAmortizationAmount,                 
                    tdTotalLoanAmount,
                    tdTotalPaymentMade,
                    tdRemainingBalance,
                    tdStatus
                ]).draw();          
        }
    }

    function Clearfields(){

        vCurrentDate=new Date();
        $("#TransDate").val(getFormattedDate(vCurrentDate));

        $(".EmployeeLoanTransID").val(0);
        $("#EmployeeLoanTransID").val(0);                

        $(".EmployeeID").val(0);
        $("#EmployeeID").val(0);
        $("#EmployeeNo").val('');
        $("#EmployeeName").val('');
      
        $("#LoanTypeCode").val('');
        $("#LoanTypeID").val(0);
        $("#LoanTypeName").val('');

        $("#VoucherNo").val('');
        $("#DateIssued").val('');
        $("#DateStartPayment").val('');
        $("#AmortizationAmnt").val('');
        $("#InterestAmnt").val('');
        $("#LoanAmnt").val('');
        $("#MonthsToPay").val('');
        $("#TotalLoanAmnt").val(''); 

        $("#Remarks").val('');
        $(".remaining_chars").text('250');

        $("#TotalPayment").val(FormatDecimal(0,2));
        $("#TotalBalance").val(FormatDecimal(0,2));

        $("#viewTotalPayment").val(FormatDecimal(0,2));
        $("#viewTotalBalance").val(FormatDecimal(0,2));
        
        $("#CutOff").val('').change();
        $("#Status").val('').change();
        $("#DelayedOptions").val('').change();
        
        $("#btnSaveRecord").show();

        resetTextBorderToNormal();

    }

    function resetTextBorderToNormal(){

        //LOAN TRANSACTION                
        $("#EmployeeName").css({"border":"#ccc 1px solid"}); 
        $("#IncomeDeductionTypeName").css({"border":"#ccc 1px solid"});
        $("#CutOff").css({"border":"#ccc 1px solid"}); 
        $("#LoanTypeName").css({"border":"#ccc 1px solid"});  
        $("#VoucherNo").css({"border":"#ccc 1px solid"}); 
        $("#DateIssued").css({"border":"#ccc 1px solid"});
        $("#DateStartPayment").css({"border":"#ccc 1px solid"});
        $("#AmortizationAmnt").css({"border":"#ccc 1px solid"});
        $("#InterestAmnt").css({"border":"#ccc 1px solid"});
        $("#LoanAmnt").css({"border":"#ccc 1px solid"});
        $("#Status").css({"border":"#ccc 1px solid"});
    
        //LOAN PAYMENT        
        $("#PaymentDate").css({"border":"#ccc 1px solid"}); 
        $("#PaymentLoanAmount").css({"border":"#ccc 1px solid"}); 
                
    }

   function EnabledDisbledText(vEnabled){

        $("#EmployeeName").attr('disabled', vEnabled);      
        $("#CutOff").attr('disabled', vEnabled);  
        
        $("#LoanTypeName").attr('disabled', vEnabled);
        $("#VoucherNo").attr('disabled', vEnabled);
        $("#DateIssued").attr('disabled', vEnabled);
        $("#DateStartPayment").attr('disabled', vEnabled);
        $("#AmortizationAmnt").attr('disabled', vEnabled);
        $("#InterestAmnt").attr('disabled', vEnabled);
        $("#LoanAmnt").attr('disabled', vEnabled);
        $("#MonthsToPay").attr('disabled', vEnabled);
        $("#TotalLoanAmnt").attr('disabled', vEnabled);        
        $("#Remarks").attr('disabled', vEnabled);
    }

    function NewRecord(){

        Clearfields();
        EnabledDisbledText(false);

        $("#LoanTable").val(1);

        $("#Status").val('Pending');        
        $("#Status").attr("style", "color: red !important; font-weight: bold; border: 1px solid rgb(204, 204, 204)");
        $("#EmployeeNo").attr("style", "border: 1px solid rgb(204, 204, 204)");

        $("#LoanTypeCode").attr("style", "border: 1px solid rgb(204, 204, 204)");
        
        $("#SetOptions").show();
        $("#MaringOption").css("margin-top", "-82px");
        
        $("#btnSaveRecord").show();
        $("#btnCancelRecord").text('Cancel');        
              
        $("#divTotalPayment").hide();
        $("#divTotalBalance").hide();
        $("#bntViewPaymentHistory").hide();
          
        $("#record-modal").modal('show');

    }

    function CancelRecord(vRecordID){

        $(".LoanIDStatus").val(vRecordID);
          
        $("#cancel-modal").modal('show');
    }

    function ApproveRecord(vRecordID){

        $(".LoanIDStatus").val(vRecordID);
          
        $("#approve-modal").modal('show');
    }

    function UploadExcelRecord(){

        Clearfields();
        $("#LoanTable").val(0);
        $("#spnExcelRecord").val(0);
        $("#ExcelFile").val('');

        $("#upload-modal").modal('show');
       
    }

  function SetLoanStatus(vStatus){

    vRecordID= $(".LoanIDStatus").val();
      if(vRecordID>0){
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                LoanID: vRecordID,
                NewStatus: vStatus
            },
            url: "{{ route('do-set-loan-transaction-status') }}",
            dataType: "json",
            success: function(data){
              if(data.Response =='Success'){
                 showHasSuccessMessage(data.ResponseMessage);
                 LoadRecordRow(data.EmployeeLoanTransactionInfo);

                 $("#approve-modal").modal('hide');
                 $("#cancel-modal").modal('hide');

                }else{
                  showHasErrorMessage('', data.ResponseMessage);
                }
            },
            error: function(data){
                console.log(data.responseText);
            },
            beforeSend:function(vData){
            }
        });
      }
    }

   function ViewPaymentLedgerHistory(vRecID){
       
      $("#tblEmployee-Ledger-Payment-List").DataTable().clear().draw();
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SearchText: '',
                LoanTransID: vRecID               
            },
            url: "{{ route('get-employee-loan-ledger-payment-list') }}",
            dataType: "json",
            success: function(data){
                LoadEmployeeLoanLedgerPaymentHistoryList(data.EmployeeLoanLegderPaymentList);
                $("#ledger-modal").modal('show');   
                
            },
            error: function(data){
                console.log(data.responseText);
            },
            beforeSend:function(vData){
              
            }
        });    
    }

    function LoadEmployeeLoanLedgerPaymentHistoryList(vList){ 
        
        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadEmployeeLoanLedgerPaymentHistoryRow(vList[x]);                
            }
        }         
    }

    function LoadEmployeeLoanLedgerPaymentHistoryRow(vData){
        
        var tblList = $("#tblEmployee-Ledger-Payment-List").DataTable();
       
        var vLedgerTotalPaymentMade=0;
        var vLedgerTotalLoanAmount=0;
        var vLedgerRemainingBalance=0;

        vLedgerTotalPaymentMade=vData.TotalPayment;
        vLedgerTotalLoanAmount=vData.TotalLoanAmount;

        vLedgerRemainingBalance=parseFloat(vLedgerTotalLoanAmount) - parseFloat(vLedgerTotalPaymentMade);

        if(vLedgerRemainingBalance<=0){
           vLedgerRemainingBalance=0;
        }

        $("#ledgerTotalLoanAmount").val(FormatDecimal(vLedgerTotalLoanAmount,2));
        $("#ledgerTotalPaymentMade").val(FormatDecimal(vLedgerTotalPaymentMade,2));
        $("#ledgerTotalRemainingBalance").val(FormatDecimal(vLedgerRemainingBalance,2));

        tdID = vData.ID;              
        tdPaymentDate = "<span>" + vData.PaymentDateFormat + "</span>";

        if(vData.PaymentModuleType=='Manual'){
           tdPaymentFrom = "<span> Manual Payment </span>";
        }else{
           tdPaymentFrom = "<span> Payroll Deduction </span>";
        }

        tdLoanTypeCode = "<span>" +vData.LoanTypeCode + "</span>";
        tdLoanTypeName = "<span>" + vData.LoanTypeName + "</span>";
        tdAmountPayment = "<span style='float:right;'>" + FormatDecimal(vData.AmountPayment,2) + "</span>";
          
        //Check if record already listed
        var IsRecordExist = false;
        tblList.rows().every(function ( rowIdx, tableLoop, rowLoop ) {
            var rowData = this.data();
            if(rowData[0] == vData.ID){

                IsRecordExist = true;
                //Edit Row
                curData = tblList.row(rowIdx).data();
                curData[0] = tdID;                
                curData[1] = tdPaymentDate;
                curData[2] = tdPaymentFrom;
                curData[3] = tdLoanTypeCode;
                curData[4] = tdLoanTypeName;
                curData[5] = tdAmountPayment;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                 tdID,                 
                 tdPaymentDate,  
                 tdPaymentFrom,  
                 tdLoanTypeCode,    
                 tdLoanTypeName,
                 tdAmountPayment                              
                ]).draw();          
        }          
    }

   function EditRecord(vRecordID,vTable,vAllowEdit){

        if(vTable==0){
           var postURL="{{ URL::route('get-employee-loan-transaction-temp-info')}}";
           $("#LoanTable").val(0); // Temp Table
           $("#SetOptions").hide();
           $("#MaringOption").css("margin-top", "0px");
           $("#bntViewPaymentHistory").hide();
           
        }else{
          var postURL="{{ URL::route('get-employee-loan-transaction-info')}}";
          $("#LoanTable").val(1); // Final Table
          $("#SetOptions").show();
          $("#MaringOption").css("margin-top", "-82px");
          $("#bntViewPaymentHistory").hide();
        }

        if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    EmployeeLoanTransID: vRecordID
                },
                url:postURL,
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.EmployeeLoanTransactionInfo != undefined){

                      Clearfields();

                        $("#EmployeeLoanTransID").val(data.EmployeeLoanTransactionInfo.ID);
                        
                        $("#EmployeeID").val(data.EmployeeLoanTransactionInfo.EmployeeID);
                        $("#EmployeeNo").val(data.EmployeeLoanTransactionInfo.EmployeeNumber);
                        $("#EmployeeNo").attr("style", "border: 1px solid rgb(204, 204, 204) !important");

                        if(data.EmployeeLoanTransactionInfo.EmployeeID<=0){
                             $("#EmployeeName").val('');
                        }else{
                            $("#EmployeeName").val(data.EmployeeLoanTransactionInfo.FullName); 
                        }

                        $("#TransDate").val(data.EmployeeLoanTransactionInfo.TransactionDateFormat);
                        $("#CutOff").val(data.EmployeeLoanTransactionInfo.CutOff).change();

                        $("#LoanTypeID").val(data.EmployeeLoanTransactionInfo.LoanTypeID);
                        $("#LoanTypeCode").val(data.EmployeeLoanTransactionInfo.LoanTypeCode);
                        $("#LoanTypeName").val(data.EmployeeLoanTransactionInfo.LoanTypeName);
                        $("#LoanTypeCode").attr("style", "border: 1px solid rgb(204, 204, 204) !important");

                        $("#DelayedOptions").val(data.EmployeeLoanTransactionInfo.DelayedPaymentOption).change();
                        
                        $("#VoucherNo").val(data.EmployeeLoanTransactionInfo.VoucherNo);

                        $("#DateIssued").val(data.EmployeeLoanTransactionInfo.DateIssueFormat);
                        $("#DateStartPayment").val(data.EmployeeLoanTransactionInfo.PaymentStartDateFormat);

                        $("#AmortizationAmnt").val(FormatDecimal(data.EmployeeLoanTransactionInfo.AmortizationAmount,2));
                        $("#InterestAmnt").val(FormatDecimal(data.EmployeeLoanTransactionInfo.InterestAmount,2));

                        $("#LoanAmnt").val(FormatDecimal(data.EmployeeLoanTransactionInfo.LoanAmount,2));
                        $("#MonthsToPay").val(parseInt(data.EmployeeLoanTransactionInfo.MonthsToPay));
                        $("#TotalLoanAmnt").val(FormatDecimal(data.EmployeeLoanTransactionInfo.TotalLoanAmount,2));
          
                        $("#Remarks").val(data.EmployeeLoanTransactionInfo.Remarks);
                        $(".remaining_chars").text(250-data.EmployeeLoanTransactionInfo.Remarks.length);                        

                        if(data.EmployeeLoanTransactionInfo.Status=='Pending'){
                             $("#Status").val('Pending');
                             $("#Status").attr("style", "color: red !important; font-weight: bold;border: 1px solid rgb(204, 204, 204) !important");
                        }

                        if(data.EmployeeLoanTransactionInfo.Status=='Approved'){
                             $("#Status").val('Approved');
                             $("#Status").attr("style", "color: green !important; font-weight: bold;border: 1px solid rgb(204, 204, 204) !important");
                        }

                        if(data.EmployeeLoanTransactionInfo.Status=='Cancelled'){
                             $("#Status").val('Cancelled');
                             $("#Status").attr("style", "color: #f68c1f !important; font-weight: bold;border: 1px solid rgb(204, 204, 204) !important");  
                        }

                       if(data.EmployeeLoanTransactionInfo.TotalPayment>0 && data.EmployeeLoanTransactionInfo.Status=='Approved'){

                          vTotalBalance= parseFloat(data.EmployeeLoanTransactionInfo.TotalLoanAmount) - parseFloat(data.EmployeeLoanTransactionInfo.TotalPayment);
                          
                          if(vTotalBalance<=0){
                             $("#TotalBalance").val('0.00');
                          }else{
                            $("#TotalBalance").val(FormatDecimal(vTotalBalance,2));
                          }
                         
                         
                         $("#TotalPayment").val(FormatDecimal(data.EmployeeLoanTransactionInfo.TotalPayment,2))

                         $("#divTotalPayment").show();
                         $("#divTotalBalance").show();

                         $("#bntViewPaymentHistory").show();
                         
                       }

                        buttonOneClick("btnSaveRecord", "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);

                        if(vAllowEdit){
                            $("#btnSaveRecord").hide();
                            $("#btnCancelRecord").text('Close');
                        }else{
                            $("#btnSaveRecord").show();          
                            $("#btnCancelRecord").text('Cancel');                  
                           
                        }
                        EnabledDisbledText(vAllowEdit);                        
                        $("#record-modal").modal('show');

                    }else{                       
                         showHasErrorMessage('',data.ResponseMessage);
                         return;
                    }

                       $("#divLoader").hide();
                },
                error: function(data){
                    $("#divLoader").hide();
                    buttonOneClick("btnSaveRecord", "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);                
                    console.log(data.responseText);                  
                   
                },
                beforeSend:function(vData){
                    $("#divLoader").show();
                    buttonOneClick("btnSaveRecord", "", false);
                }
            });
        }
    }

    function SetManualPayment(vRecordID){

      $("#tblEmployee-Manual-Payment-List").DataTable().clear().draw();

       if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    EmployeeLoanTransID: vRecordID
                },
                url: "{{ route('get-employee-loan-transaction-info') }}",
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.EmployeeLoanTransactionInfo != undefined){

                        Clearfields();

                        $(".EmployeeID").val(data.EmployeeLoanTransactionInfo.EmployeeID);
                        $(".EmployeeLoanTransactionID").val(data.EmployeeLoanTransactionInfo.ID);

                        //View Laon Data
                        $("#viewEmployeeLoanTransID").val(data.EmployeeLoanTransactionInfo.ID);
                        $("#viewEmployeeID").val(data.EmployeeLoanTransactionInfo.EmployeeID);
                        $("#viewEmployeeNo").val(data.EmployeeLoanTransactionInfo.EmployeeNumber);

                        if(data.EmployeeLoanTransactionInfo.EmployeeID<=0){
                             $("#viewEmployeeName").val('');
                        }else{
                            $("#viewEmployeeName").val(data.EmployeeLoanTransactionInfo.FullName); 
                        }

                        $("#viewTransDate").val(data.EmployeeLoanTransactionInfo.TransactionDateFormat);

                        if(data.EmployeeLoanTransactionInfo.CutOff==1){
                          $("#viewCutOff").val('1ST HALF');
                        }else if(data.EmployeeLoanTransactionInfo.CutOff==2){
                          $("#viewCutOff").val('2ND HALF');
                        }else{
                          $("#viewCutOff").val('EVERY CUTOFF');
                        }
                                                
                        $("#viewLoanTypeID").val(data.EmployeeLoanTransactionInfo.LoanTypeID);
                        $("#viewLoanTypeCode").val(data.EmployeeLoanTransactionInfo.LoanTypeCode);
                        $("#viewLoanTypeName").val(data.EmployeeLoanTransactionInfo.LoanTypeName);
                        
                        $("#viewVoucherNo").val(data.EmployeeLoanTransactionInfo.VoucherNo);

                        $("#viewDateIssued").val(data.EmployeeLoanTransactionInfo.DateIssueFormat);
                        $("#viewDateStartPayment").val(data.EmployeeLoanTransactionInfo.PaymentStartDateFormat);

                        $("#viewAmortizationAmnt").val(FormatDecimal(data.EmployeeLoanTransactionInfo.AmortizationAmount,2));
                        $("#viewInterestAmnt").val(FormatDecimal(data.EmployeeLoanTransactionInfo.InterestAmount,2));
                        $("#viewLoanAmnt").val(FormatDecimal(data.EmployeeLoanTransactionInfo.LoanAmount,2));
                        $("#viewNoMontsToPay").val(data.EmployeeLoanTransactionInfo.MonthsToPay);
                        $("#viewTotalLoanAmnt").val(FormatDecimal(data.EmployeeLoanTransactionInfo.TotalLoanAmount,2));

                        if(data.EmployeeLoanTransactionInfo.DelayedPaymentOption==1){
                          $("#viewDelayedOptions").val('Full Payment on Next Payroll');
                        }else if(data.EmployeeLoanTransactionInfo.DelayedPaymentOption==2){
                          $("#viewDelayedOptions").val('Add On to Remaining Months');                        
                        }
          
                        $("#viewRemarks").val(data.EmployeeLoanTransactionInfo.Remarks);

                        if(data.EmployeeLoanTransactionInfo.Status=='Pending'){
                             $("#viewStatus").val('Pending');
                             $("#viewStatus").css("color", "red");
                        }

                        if(data.EmployeeLoanTransactionInfo.Status=='Approved'){
                             $("#viewStatus").val('Approved');
                             $("#viewStatus").css("color", "green");
                        }

                        if(data.EmployeeLoanTransactionInfo.Status=='Cancelled'){
                             $("#viewStatus").val('Cancelled');
                             $("#viewStatus").css("color", "#f68c1f");
                        }

                         $("#viewTotalPayment").val('0');
                         $("#viewTotalBalance").val('0')                         


                        $("#dvRemainingBalance").hide();
                        $("#dvPaymentMade").hide();

                       if(data.EmployeeLoanTransactionInfo.TotalPayment>0 && data.EmployeeLoanTransactionInfo.Status=='Approved'){

                        $("#dvRemainingBalance").show();
                        $("#dvPaymentMade").show();
                        
                          vTotalBalance = parseFloat(data.EmployeeLoanTransactionInfo.TotalLoanAmount) - parseFloat(data.EmployeeLoanTransactionInfo.TotalPayment);
                         
                         if(vTotalBalance<=0){
                           $("#viewTotalBalance").val('0.00');   
                         }else{
                           $("#viewTotalBalance").val(FormatDecimal(vTotalBalance,2))   
                         }

                         $("#viewTotalPayment").val(FormatDecimal(data.EmployeeLoanTransactionInfo.TotalPayment,2))                                              
                         getEmployeeLoanManualPaymentHistory(1,data.EmployeeLoanTransactionInfo.ID,'');
                         
                       }

                        buttonOneClick("btnSaveRecord", "Save", false);                                               
                        $("#payment-modal").modal('show');

                    }else{
            
                         showHasErrorMessage('',data.ResponseMessage);
                         return;
                    }

                     $("#divLoader").hide();
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

  function getEmployeeLoanManualPaymentHistory(vPageNo,vLoanTransID,vSearchText){

      $("#tblEmployee-Manual-Payment-List").DataTable().clear().draw();
      $(".paginate_button").remove(); 

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SearchText: vSearchText,
                LoanTransID: vLoanTransID,
                Status: 'Active',
                Limit:10,
                PageNo: vPageNo
            },
            url: "{{ route('get-employee-loan-manual-payment-list') }}",
            dataType: "json",
            success: function(data){

                 LoadEmployeeLoanManualPaymentHistoryList(data.EmployeeLoanManualPaymentList);

            $("#divLoader").hide(); 

            },
            error: function(data){
                console.log(data.responseText);
            },
            beforeSend:function(vData){
                $("#divLoader").show(); 
            }
        });
    }

    function LoadEmployeeLoanManualPaymentHistoryList(vList){      
        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadEmployeeLoanManualPaymentHistoryRow(vList[x]);
            }
        }
    }

    function LoadEmployeeLoanManualPaymentHistoryRow(vData){
        
        var tblList = $("#tblEmployee-Manual-Payment-List").DataTable();
        var LoanTypeName=$("#viewLoanTypeName").val();

        tdID = vData.ID;              
        tdPaymentDate = "<span>" + vData.PaymentDateFormat + "</span>";
        tdLoanTypeCode = "<span>" +vData.LoanTypeCode + "</span>";
        tdLoanTypeName = "<span>" + LoanTypeName + "</span>";
        tdAmount = "<span>" + FormatDecimal(vData.Amount,2) + "</span>";
                
        tdStatus = "";        
        if(vData.RateStatus == 'Active'){
            tdStatus += "<span style='color:green;'> <i class='bx bx-check-circle'></i> Active </span>";
        }else{
            tdStatus += "<span style='color:red;'> <i class='bx bx-x-circle'></i> Inactive </span>";
        }
                  
        //Check if record already listed
        var IsRecordExist = false;
        tblList.rows().every(function ( rowIdx, tableLoop, rowLoop ) {
            var rowData = this.data();
            if(rowData[0] == vData.EmployeeRateID){

                IsRecordExist = true;
                //Edit Row
                curData = tblList.row(rowIdx).data();
                curData[0] = tdID;                
                curData[1] = tdPaymentDate;
                curData[2] = tdLoanTypeCode;
                curData[3] = tdLoanTypeName;
                curData[4] = tdAmount;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                 tdID,                 
                 tdPaymentDate,    
                 tdLoanTypeCode,    
                 tdLoanTypeName,
                 tdAmount                              
                ]).draw();          
        }  
    }
    
    function NewManualPaymentRecord(){

      var vviewTotalPayment = $("#viewTotalPayment").val();
      var vTotalBalance = $("#viewTotalBalance").val();

      if(parseFloat(vviewTotalPayment)>0 && parseFloat(vTotalBalance)<=0){
          showHasErrorMessage('', 'Employee loan is already paid and completed.');
          return;
      }
       
       $("#EmployeeLoanTransactionID").val('0');
       $("#PaymentLoanAmount").val(''); 
       $("#PaymentRemarks").val('');  
       $("#PaymentDate").val(getFormattedDate(vCurrentDate));
       
        $("#divLoader").hide();
        $("#new-payment-modal").modal('show');
    }

    function SaveNewManualPaymentRecord(){

        var vLoanPaymentTransactionID = $("#LoanPaymentTransactionID").val();

        var vEmployeeLoanTransID= $(".EmployeeLoanTransactionID").val();
        var vEmployeeID = $(".EmployeeID").val();

        var vPaymentDate = $("#PaymentDate").val();
        var vPaymentLoanAmount = $("#PaymentLoanAmount").val();
        
        resetTextBorderToNormal();

       if(vPaymentLoanAmount=="" || vPaymentLoanAmount<=0) {
          showHasErrorMessage('PaymentLoanAmount','Enter loan amount payment.');
         return;  
       }
      
        // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                LoanPaymentTransID: $("#LoanPaymentTransID").val(),
                EmployeeLoanTransID: $(".EmployeeLoanTransactionID").val(),
                EmployeeID: $(".EmployeeID").val(),
                PaymentDate: $("#PaymentDate").val(),                
                PaymentLoanAmount: $("#PaymentLoanAmount").val(),         
                PaymentRemarks: $("#PaymentRemarks").val()                                  
            },
            url: "{{ route('do-save-employee-manual-loan-payment') }}",
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

                if(data.Response =='Success'){

                    toast('toast-success', data.ResponseMessage);  
                     ReloadEmployeeLoanInformation(vEmployeeLoanTransID);
                     LoadRecordRow(data.EmployeeLoanTransactionInfo);
                     $("#new-payment-modal").modal('hide');

                }else{
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
                buttonOneClick("btnSaveRecord", "", true);
            }
        });

    }

    function ReloadEmployeeLoanInformation(vRecordID){

         if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    EmployeeLoanTransID: vRecordID
                },
                url: "{{ route('get-employee-loan-transaction-info') }}",
                dataType: "json",
                success: function(data){

                
                    if(data.Response =='Success' && data.EmployeeLoanTransactionInfo != undefined){

                        Clearfields();
                        $(".EmployeeID").val(data.EmployeeLoanTransactionInfo.EmployeeID);
                        $(".EmployeeLoanTransactionID").val(data.EmployeeLoanTransactionInfo.ID);

                        //View Laon Data
                        $("#viewEmployeeLoanTransID").val(data.EmployeeLoanTransactionInfo.ID);
                        $("#viewEmployeeID").val(data.EmployeeLoanTransactionInfo.EmployeeID);
                        $("#viewEmployeeNo").val(data.EmployeeLoanTransactionInfo.EmployeeNumber);

                        if(data.EmployeeLoanTransactionInfo.EmployeeID<=0){
                             $("#viewEmployeeName").val('');
                        }else{
                            $("#viewEmployeeName").val(data.EmployeeLoanTransactionInfo.FullName); 
                        }

                        $("#viewTransDate").val(data.EmployeeLoanTransactionInfo.TransactionDateFormat);

                        if(data.EmployeeLoanTransactionInfo.CutOff==1){
                          $("#viewCutOff").val('1ST HALF');
                        }else if(data.EmployeeLoanTransactionInfo.CutOff==2){
                          $("#viewCutOff").val('2ND HALF');
                        }else{
                          $("#viewCutOff").val('EVERY CUTOFF');
                        }
                        
                        $("#viewLoanTypeID").val(data.EmployeeLoanTransactionInfo.LoanTypeID);
                        $("#viewLoanTypeCode").val(data.EmployeeLoanTransactionInfo.LoanTypeCode);
                        $("#viewLoanTypeName").val(data.EmployeeLoanTransactionInfo.LoanTypeName);
                        
                        $("#viewVoucherNo").val(data.EmployeeLoanTransactionInfo.VoucherNo);

                        $("#viewDateIssued").val(data.EmployeeLoanTransactionInfo.DateIssueFormat);
                        $("#viewDateStartPayment").val(data.EmployeeLoanTransactionInfo.PaymentStartDateFormat);

                        $("#viewAmortizationAmnt").val(FormatDecimal(data.EmployeeLoanTransactionInfo.AmortizationAmount,2));
                        $("#viewInterestAmnt").val(FormatDecimal(data.EmployeeLoanTransactionInfo.InterestAmount,2));
                        $("#viewLoanAmnt").val(FormatDecimal(data.EmployeeLoanTransactionInfo.LoanAmount,2));
                        $("#viewNoMontsToPay").val(data.EmployeeLoanTransactionInfo.MonthsToPay);
                        $("#viewTotalLoanAmnt").val(FormatDecimal(data.EmployeeLoanTransactionInfo.TotalLoanAmount,2));
          
                        $("#viewRemarks").val(data.EmployeeLoanTransactionInfo.Remarks);

                        if(data.EmployeeLoanTransactionInfo.Status=='Pending'){
                             $("#viewStatus").val('Pending');
                             $("#viewStatus").css("color", "red");
                        }

                        if(data.EmployeeLoanTransactionInfo.Status=='Approved'){
                             $("#viewStatus").val('Approved');
                             $("#viewStatus").css("color", "green");
                        }

                        if(data.EmployeeLoanTransactionInfo.Status=='Cancelled'){
                             $("#viewStatus").val('Cancelled');
                             $("#viewStatus").css("color", "#f68c1f");
                        }

                        $("#dvRemainingBalance").hide();
                        $("#dvPaymentMade").hide();

                       if(data.EmployeeLoanTransactionInfo.TotalPayment>0 && data.EmployeeLoanTransactionInfo.Status=='Approved'){

                        $("#dvRemainingBalance").show();
                        $("#dvPaymentMade").show();
                        
                        vTotalBalance= parseFloat(data.EmployeeLoanTransactionInfo.TotalLoanAmount) - parseFloat(data.EmployeeLoanTransactionInfo.TotalPayment);
                        if(vTotalBalance<=0){
                          $("#viewTotalBalance").val('0.00')     
                        }else{
                         $("#viewTotalBalance").val(FormatDecimal(vTotalBalance,2))        
                        }
                                                  
                         $("#viewTotalPayment").val(FormatDecimal(data.EmployeeLoanTransactionInfo.TotalPayment,2))

                        getEmployeeLoanManualPaymentHistory(1,data.EmployeeLoanTransactionInfo.ID,'');                      
                         
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
    }

    function SaveRecord(){

        vLoanTable=$("#LoanTable").val();

        if(vLoanTable==0){ //Temp Table                        
           var postURL="{{ URL::route('do-save-employee-loan-temp-transaction')}}"; 
            vIsUploaded=1;          
        }else{ //Final Table            
          var postURL="{{ URL::route('do-save-employee-loan-transaction')}}";
           vIsUploaded=1;
        }

        vEmployeeLoanTransID= $("#EmployeeLoanTransID").val();
        vTransDate= $("#TransDate").val();

        vEmpID= $("#EmployeeID").val();
        vEmpNo= $("#EmployeeNo").val();

        vLoanTypeID= $("#LoanTypeID").val();
        vLoanTypeCode= $("#LoanTypeCode").val();
        vDelayedPaymentOption =  $("#DelayedPaymentOption").val();

        vCutOff= $("#CutOff").val();
        vDelayedOptions= $("#DelayedOptions").val();

        vVoucherNo= $("#VoucherNo").val();
        vDateIssued= $("#DateIssued").val();
        vDateStartPayment= $("#DateStartPayment").val();

        vAmortizationAmnt= $("#AmortizationAmnt").val();
        vInterestAmnt= $("#InterestAmnt").val();
        vLoanAmnt= $("#LoanAmnt").val();
        vMonthsToPay = $("#MonthsToPay").val();
        vTotalLoanAmnt= $("#TotalLoanAmnt").val();

        vTotalPayment= $("#TotalPayment").val();
        vTotalBalance= $("#TotalBalance").val();

        vRemarks= $("#Remarks").val();
        vStatus =$("#Status").val();
        vIsUploaded=1;

        var checkInput =true;
        checkInput=doCheckLoanInput(vLoanTable);

        if(checkInput==false){
            return;
        }
        
       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                EmployeeLoanTransID: vEmployeeLoanTransID,
                TransDate: vTransDate,
                EmpID: vEmpID,
                EmpNo: vEmpNo,
                CutOff: vCutOff,
                LoanTypeID: vLoanTypeID,
                LoanTypeCode: vLoanTypeCode,
                VoucherNo: vVoucherNo,
                DateIssued: vDateIssued,
                DelayedOptions: vDelayedOptions,
                DateStartPayment: vDateStartPayment,
                AmortizationAmnt: vAmortizationAmnt,
                InterestAmnt: vInterestAmnt,
                LoanAmnt: vLoanAmnt,
                TotalLoanAmnt: vTotalLoanAmnt,
                TotalPayment: vTotalPayment,
                MonthsToPay: vMonthsToPay,
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
                    if(vLoanTable==0){ // Temp Table
                      //LoadTempRecordRow(data.DTRTempInfo);
                      $("#tblList-Excel").DataTable().clear().draw();
                      getLoanTempRecordList(1);
                    }else{ // Final Table
                       LoadRecordRow(data.EmployeeLoanTransactionInfo);
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

 function SaveFinalRecord(){

    vTotalError=$("#ErrorRecords").val(); 
      
     $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}'
        },
        url: "{{ route('do-upload-save-loan-transaction') }}",
        dataType: "json",
        success: function(data){
              if(data.Response =='Success'){
                                
                if(vTotalError>0){
                   setTimeout(function () {                      
                      showHasSuccessMessage(data.ResponseMessage);
                      getRecordList(1);                
                      $("#divLoader").hide(); 
                      $("#excel-modal").modal('hide');
                   }, 3000);                
                }else{                      
                      showHasSuccessMessage(data.ResponseMessage);
                      getRecordList(1);                
                      $("#divLoader").hide(); 
                      $("#excel-modal").modal('hide');
                }    
                
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
           showHasErrorMessage('', 'There are ' + vTotalError + ' loan records will not be save due to data issues.');
           $("#divLoader").show();           
        }
    });
 }

 function PrintLoan(vTransID){

    window.open('{{config('app.url')}}admin-employee-loan-print-report?LoanTransactionID=' +vTransID, '_blank');
 }

  $(document).on('focus','.autocomplete_txt',function(){
      
       isEmployee=false;
       isLoanType=false;
       var valAttrib  = $(this).attr('data-complete-type');
       
       if(valAttrib=='employee'){
            isEmployee=true;
            searchlen=2;
            var postURL="{{ URL::route('get-employee-search-list')}}";
        }

        if(valAttrib=='loantype'){
            isLoanType=true;
            searchlen=2;
            var postURL="{{ URL::route('get-loan-type-search-list')}}";
        }
        
     $(this).autocomplete({
            source: function( request, response ) {
               if(request.term.length >= searchlen){
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
                                     value: code[5],
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

              }
        });
    });

  function getLoanTempRecordList(vPageNo){

      $("#tblList-Excel").DataTable().clear().draw();
      $(".paginate_button").remove(); 

      $("#ErrorRecords").val(0); 
      $("#spnUploadedErrorRecord").text('');

      vLimit=15;

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                Status: 'Pending',
                Limit:vLimit,
                PageNo: vPageNo
            },
            url: "{{ route('get-employee-loan-transaction-temp-list') }}",
            dataType: "json",
            success: function(data){

                total_rec=data.TotalRecord;                                       
                LoadTempRecordList(data.EmployeeLoanTempList);
                  if(total_rec>0){
                     CreateEmployeeLoanTempPaging(total_rec,vLimit);  
                     if(total_rec>vLimit){
                        $("#divTempPaging").show(); 
                        $("#total-record").text(total_rec);
                        $("#paging_button_id"+vPageNo).css("background", "#0069d9");
                        $("#paging_button_id"+vPageNo).css("color", "#fff");
                     }

                    if(data.TotalError>0){
                       $("#ErrorRecords").val(data.TotalError); 
                       $("#spnUploadedErrorRecord").text(' And ' + data.TotalError + ' records has data issues.');            
                     }else{
                       $("#ErrorRecords").val(0); 
                       $("#spnUploadedErrorRecord").text('');            
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

  function CreateEmployeeLoanTempPaging(vTotalRecord,vLimit){

       var i;
       paging_button="";
    
        limit=vLimit; //get limit
        totalcount=vTotalRecord; //get total count
        pages=Math.ceil(totalcount/limit); //get pages
      
       if (pages!=1) {     

          paging_button="<li class='paginate_button page-item previous' id='example2_previous'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getLoanTempRecordList(1)'>First</a></li>"
          $(".ul-paging").append(paging_button);
       }
       
        if (pages>1) {        
          for (i = 1; i <= pages; i++) {            
            paging_button="<li class='paginate_button page-item'><a href='javascript:void(0)' id='paging_button_id"+i+"' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getLoanTempRecordList("+i+")'>"+i+"</a></li>"
             $(".ul-paging").append(paging_button);
          }
        }
          
       if (pages!=1) {            
        paging_button="<li class='paginate_button page-item next' id='example2_next'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getLoanTempRecordList("+pages+",)'>Last</a></li>"
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
                              tdAction += "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",0,false)'>"+
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
                              tdAction += "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",0,false)'>"+
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
        tdLoanTypeCode = "<span>" + vData.LoanTypeCode + "</span>";
        tdLoanTypeName = "<span>" + vData.LoanTypeName + "</span>";

        tdVoucherNo = "<span>" + vData.VoucherNo + "</span>";

        tdIntrstAmount = "<span>" + FormatDecimal(vData.InterestAmount,2) + "</span>";
        tdLoanAmount = "<span>" + FormatDecimal(vData.LoanAmount,2) + "</span>";
        tdTotalLoanAmount = "<span>" + FormatDecimal(vData.TotalLoanAmount,2) + "</span>";
        tdAmortizationAmount = "<span>" + FormatDecimal(vData.AmortizationAmount,2) + "</span>";

        tdStatus = "";
        if(vData.Status == 'Pending'){
            tdStatus += "<span style='color:red;display:flex;'> <i class='bx bx-x-circle'></i> Pending </span>";
        }

         tdIsUploadError='';
        if(vData.IsUploadError==1){
           tdIsUploadError = "No Record";
        }else if(vData.IsUploadError==2){
           tdIsUploadError = "Duplicate";        
        }else{
            tdIsUploadError = "";
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
                curData[2] = tdCutOff;
                curData[3] = tdEmpNo;
                curData[4] = tdEmpName;
                curData[5] = tdLoanTypeCode;
                curData[6] = tdLoanTypeName;
                curData[7] = tdVoucherNo;                
                curData[8] = tdIntrstAmount;
                curData[9] = tdLoanAmount;
                curData[10] = tdTotalLoanAmount;
                curData[11] = tdAmortizationAmount;
                curData[12] = tdStatus;
                curData[13] = tdIsUploadError;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,
                    tdAction,
                    tdCutOff,
                    tdEmpNo,
                    tdEmpName,
                    tdLoanTypeCode,
                    tdLoanTypeName,
                    tdVoucherNo,
                    tdIntrstAmount,
                    tdLoanAmount,
                    tdTotalLoanAmount,
                    tdAmortizationAmount,
                    tdStatus,
                    tdIsUploadError
                ]).draw();          
        }
    }

 function clearLoanTempTransaction(){

        $("#tblList-Excel").DataTable().clear().draw();

        $.ajax({
            type: "post",
            data: {
             _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}"
            },
            url: "{{ route('do-clear-loan-temp-transaction') }}",
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
        url: "{{ route('do-remove-duplicate-loan-transaction') }}",
        dataType: "json",
        success: function(data){
              if(data.Response =='Success'){
                showHasSuccessMessage(data.ResponseMessage);
                // DeleteTableRow(vRecID);
                $("#tblList-Excel").DataTable().clear().draw();
                getLoanTempRecordList(intCurrentPage);
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

  function getLoanTempUploadedCount(vExcelRecord){

      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}'
        },
        url: "{{ route('get-loan-temp-transaction-upload-count') }}",
        dataType: "json",
        success: function(data){

          $("#spnUploadedRecord").text(data.MaxCount);
          $("#spnExcelRecord").text(vExcelRecord);

          if(parseInt(data.MaxCount) == parseInt(vExcelRecord)){                
            showHasSuccessMessage('Excel data has successfully uploaded. Please review and address any data issues highlighted in red or green.');
          }else{
            showHasErrorMessage('','The uploaded Excel data does not match the Excel record data. Please re-upload the file.');                
          }

          $("#btnUploadFinalRecord").show();            
             
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

    function doCheckLoanInput(vLoanTable){
       
        var IsNotComplete=true;
        var vEmployeeLoanTransID = $("#EmployeeLoanTransID").val();        

        var vTransDate = $("#TransDate").val();
        var vEmployeeID = $("#EmployeeID").val();
        var vEmployeeNo = $("#EmployeeNo").val();
        var vEmployeeName = $("#EmployeeName").val();

        var vCutOff = $("#CutOff").val();
        var vLoanTypeID = $("#LoanTypeID").val();
        var vLoanTypeName = $("#LoanTypeName").val();

        var vVoucherNo = $("#VoucherNo").val();
        var vDelayedOptions = $("#DelayedOptions").val();

        var vDateIssued = $("#DateIssued").val();
        var vDateStartPayment = $("#DateStartPayment").val();
        var vAmortizationAmnt = $("#AmortizationAmnt").val();
        var vInterestAmnt = $("#InterestAmnt").val();
        var vLoanAmnt = $("#LoanAmnt").val();

        var vRemarks = $("#Remarks").val();        
        var vStatus = $("#Status").val();

        resetTextBorderToNormal();

       if(vCutOff.trim()=="") {
         showHasErrorMessage('CutOff','Select frequency period from the list.');
          IsNotComplete=false;
          return IsNotComplete;
       }

       if(vEmployeeID<=0) {
         showHasErrorMessage('EmployeeName','Search and select employee from the list.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLoanTypeID<=0) {
         showHasErrorMessage('LoanTypeName','Search and select loan type from the list.');
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

      if(vLoanAmnt.trim()=="" || vLoanAmnt<=0) {
         showHasErrorMessage('LoanAmnt','Enter loanable amount.');
          IsNotComplete=false;
           return IsNotComplete;
       }   
     
      if(vAmortizationAmnt.trim()=="" || vAmortizationAmnt<=0 ) {
         showHasErrorMessage('AmortizationAmnt','Enter amortization amount.');
           IsNotComplete=false;
           return IsNotComplete;
       } 
      
      if(vLoanTable==1){        
          if(vDelayedOptions=="") {
             showHasErrorMessage('DelayedOptions','Select if delayed payment option from the list.');
              IsNotComplete=false;
              return IsNotComplete;
          }
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

 $("#LoanTypeName").blur(function() {
     vLoanType=$(this).val();
       if(vLoanType.length<=5 || vLoanType==''){
         $("#LoanTypeID").val(0);
         $("#LoanTypeCode").val('');
      }
  });
  
  $("#LoanTypeName").keyup(function() { 
    vLoanType=$(this).val();
       if(vLoanType.length<=5 || vLoanType==''){
         $("#LoanTypeID").val(0);
         $("#LoanTypeCode").val('');
      }
    });

 $(function() {    
    $("#DateIssued").datepicker();
    $("#DateStartPayment").datepicker();
    $("#PaymentDate").datepicker();    
  });

function ClearEmployee(){
   $("#EmployeeID").val('0'); 
   $("#EmployeeNo").val('');
   $("#EmployeeName").val('');

    resetTextBorderToNormal();
 }


 function ClearLoanType(){
   $("#LoanTypeID").val('0'); 
   $("#LoanTypeCode").val('');
   $("#LoanTypeName").val('');

    resetTextBorderToNormal();
 }

$(document ).ready(function() {

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

 function LoadCalendar(vElem){
    $("#"+vElem).focus();
  }

 var max_length = 250;
  $("#Remarks").on('change keyup keydown', function() {
     var len = max_length - $(this).val().length;             
        $('.remaining_chars').text(len);
         
        if (event.keyCode == 8 || event.keyCode == 46) {
            return true;            
        }else{            
            if(len <= 0){                
                 return false;
            }else{
                 return true;
            }    
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

  <!--      //NEW STYLE CVS GET DATA AND CHUNK -->
  <script>

    document.getElementById('btnUploadTSSCSV').addEventListener('click', function() {

        let  i = 1;
        let  x = 0;
        var recPerBatch=100;
       
        const fileInput = document.getElementById('ExcelFile');

        if (!fileInput.files.length) {
            showHasErrorMessage('','Browse and upload Employee Loan transaction csv file.');
            return;
        }
        
        const file = fileInput.files[0];
        
        if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
             showHasErrorMessage('','Please upload a valid Employee Loan csv file.');
            return;
        }
        
        // Read the file
        const reader = new FileReader();
        
        reader.onload = function(e) {

           clearMessageNotification();
           clearLoanTempTransaction();

            const contents = e.target.result;
            const employee_loan_temp_record = [];
             
            var NoNEmptyRowCount=1; 
            const vDataLen=contents.length-1;
            const lines = contents.split('\n');
            
            //SKIP HEADER COLUMN
            for (let i = 1; i < lines.length; i++) {
                const line = lines[i].trim();

                if (line) {

                    NoNEmptyRowCount++;
                    const vData = line.split(',');

                    vEmployeeLoanTransID=0;
                    vTotalLoanAmnt=0;
                    
                    vTransDate=new Date();
                    vEmployeeNo=(vData[0]!=undefined ? vData[0] : '');

                     if(vEmployeeNo=='END'){
                        break;
                      }
         
                    vLoanTypeCode=(vData[1]!=undefined ? vData[1] : '');
                    vCutOff=(vData[2]!=undefined ? vData[2] : 0);
                        
                    vVoucherNo=(vData[3]!=undefined ? vData[3] : '');
                    vDateIssued=(vData[4]!=undefined ? vData[4] : '');
                   
                    vLoanAmnt=(vData[5]!=undefined ? parseFloat(vData[5],2) : 0);
                    vInterestAmnt=(vData[6]!=undefined ? parseFloat(vData[6],2) : 0);
                    vAmortizationAmnt=(vData[7]!=undefined ? parseFloat(vData[7],2) : 0);

                    vDateStartPayment=(vData[8]!=undefined ? vData[8] : '');    
                    vRemarks=(vData[9]!=undefined ? vData[9] : '');

                    if (isNaN(vLoanAmnt)) {
                      vLoanAmnt=0;
                    } else if (isNaN(vInterestAmnt)) {
                      vInterestAmnt=0;
                    } else if (isNaN(vAmortizationAmnt)) {
                      vAmortizationAmnt=0;
                   }

                   vTotalLoanAmnt = parseFloat(vLoanAmnt) + parseFloat(vInterestAmnt);
                   vMonthsToPay = Math.ceil(parseFloat(vLoanAmnt) / parseFloat(vAmortizationAmnt));

                    if(vMonthsToPay === Infinity){
                        vMonthsToPay=0;
                    }else{
                         vMonthsToPay = vTotalLoanAmnt / vAmortizationAmnt;
                         vMonthsToPay=Math.ceil(vMonthsToPay);
                    }

                    vIsUploaded=1;
                    
                    // Collect employee temp loan data
                    employee_loan_temp_record.push({
                        LoanTransID: vEmployeeLoanTransID,
                        TransDate: vTransDate,
                        EmpNo: vEmployeeNo,
                        LoanTypeCode: vLoanTypeCode,
                        CutOff: vCutOff,
                        VoucherNo: vVoucherNo,
                        DateIssued: vDateIssued,
                        LoanAmnt: vLoanAmnt,
                        InterestAmnt: vInterestAmnt,
                        TotalLoanAmnt: vTotalLoanAmnt,
                        MonthsToPay: vMonthsToPay,
                        AmortizationAmnt: vAmortizationAmnt,
                        DateStartPayment: vDateStartPayment,
                        Remarks: vRemarks,
                        IsUploaded:vIsUploaded      
                    });
                }
            }
            
   
           // Process in batches of 10
            const temp_batches = [];
            for (let x = 0; x< employee_loan_temp_record.length; x += recPerBatch) {
                temp_batches.push(employee_loan_temp_record.slice(x, x + recPerBatch));
            }
          
          
            saveEmployeeTempLoanByBatchRecord(0);
            
            // Function to save each batch
            function saveEmployeeTempLoanByBatchRecord(batchIndex) {

                  $("#spnTotalData").text(batchIndex * recPerBatch +'/'+ parseInt(NoNEmptyRowCount-2));

                if (batchIndex >= temp_batches.length) {
                  
                      getLoanTempUploadedCount(NoNEmptyRowCount-2);
                      getLoanTempRecordList(1);   

                      $("#upload-modal").modal('hide');               
                      $("#excel-modal").modal('show');
                    return;
                }
                
                const currentTempLoanBatch = temp_batches[batchIndex];


                 //SAVE Batch of data
                $.ajax({
                    type: "post",
                    url: "{{ route('do-save-loan-temp-transaction-batch') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                        LoanTempDataItems: currentTempLoanBatch
                    },
                    dataType: "json",
                    success: function(data){

                        buttonOneClick("btnUploadTSSCSV", "Upload CSV", false);

                        if(data.Response =='Success'){
                          
                          $("#spnTotalData").hide();
                          $("#divLoader").hide();
                             saveEmployeeTempLoanByBatchRecord(batchIndex + 1);  // Proc


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
            }
            
        };
        
        reader.readAsText(file);
        //Clear File Input
        fileInput.value = '';
    });
</script>

@endsection



