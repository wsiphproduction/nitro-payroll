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
  .table.dataTable thead .sorting:before, .table.dataTable thead .sorting:after, .table.dataTable thead .sorting_asc:before, .table.dataTable thead .sorting_asc:after, .table.dataTable thead .sorting_desc:before, .table.dataTable thead .sorting_desc:after{
          top: -6px !important;
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
option:disabled {
  background: #b3d9ff;
  color: #fff !important;
  font-weight: 400;
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
                                    <li class="breadcrumb-item active">Employee Income & Deduction Transaction List
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
                                    <h4 class="card-title">Employee Income & Deduction</h4>
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
                                                          <option disabled="disabled">[ By Type ]</option>
                                                          <option value="Earning">Type: Earning</option>
                                                          <option value="Deduction">Type: Deduction</option>
                                                          <option disabled="disabled">[ By Status ]</option>
                                                          <option value="Pending">Status: Pending</option>
                                                          <option value="Approved">Status: Approved</option>
                                                          <option value="Cancelled">Status: Cancelled</option>
                                                          <option value="OnHold">Status: On Hold</option>
                                                        </select>
                                                  
                                                        <input type="text" class="form-control searchtext" placeholder="Search Here.." style="width: 34%;margin-left: 6px;">                                                        
                                                        <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border" tooltip="Search Here.." tooltip-position="top">
                                                            <i class="bx bx-search"></i>
                                                        </button>
                                                        
                                                      @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload==1)
                                                            <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="NewRecord()" tooltip="Create New" tooltip-position="top">
                                                                <i class="bx bx-plus"></i> New
                                                            </button>
                                                      @endif    

                                                     @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload==1)
                                                         <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="UploadExcelRecord()" tooltip="Upload Income & Deduction Excel" tooltip-position="top">
                                                           <i class="bx bx-upload"></i> Upload Income & Deduction Excel
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
                                                        <th>FREQUENCY</th>                            
                                                        <th>EMPLOYEE ID</th>
                                                        <th>EMPLOYEE NAME</th>                                  
                                                        <th>TYPE</th>
                                                        <th>CODE</th>
                                                        <th>DESCRIPTION</th>                                       
                                                        <th>TOTAL AMOUNT</th>                                                           
                                                        <th>PAYMENT MADE</th>  
                                                        <th>REMAINING BALANCE</th>  
                                                        <th>STATUS</th>
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

    <!-- MODAL -->
    <div id="record-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Employee Income & Deduction Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">

                <input type="hidden" id="EmployeeIncomeDeductionTransID" value="0" readonly>
                <input type="hidden" id="IncomeDeductionTable" value="0" readonly>

                    <div class="row">
                        
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Transaction Date: <span class="required_field">*</span></label>
                             <div class="div-percent">
                                  <input id="TransDate" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" disabled style="border: 1px solid rgb(204, 204, 204);"><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;"></i> </span>
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
                            <label for="Status">Reference No: </label>
                             <input id="ReferenceNo" type="text" class="form-control" placeholder="Reference No" autocomplete="off">
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
                            <label for="Status">Employee Name: <span class="required_field">* </span></label><span class="search-txt">(Type & search from the list)</span>
                             <div class="div-percent">
                                   <input id="EmployeeID" type="hidden" value="0">                                    
                                   <input id="EmployeeName" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-complete-type="employee" placeholder="Employee Name"><span class='percent-sign' onclick="ClearEmployee()"> <i class="bx bx-trash" style="line-height: 21px;cursor:pointer;" ></i> </span>
                                </div> 
                          </fieldset>
                        </div>
                   </div>
                    <div class="row">

                           <div class="col-md-2" style="padding: 0px;">
                               <fieldset class="form-group">
                                <label for="Status">Income Deduction Code: </label>                                                
                                  <input id="IncomeDeductionTypeCode" type="text" class="form-control" placeholder="Code" disabled style="border-top-right-radius: 0px;border-bottom-right-radius: 0px;">                             
                            </fieldset>
                           </div>  

                           <div class="col-md-2" style="padding: 0px;">
                               <fieldset class="form-group">
                                <label for="Status">Income Deduction Type: </label>                                                
                                  <input id="IncomeDeductionType" type="text" class="form-control" placeholder="Type" disabled style="border-top-left-radius: 0px;border-bottom-left-radius: 0px;">                                           
                            </fieldset>
                           </div> 
                        
                        <div class="col-md-8">
                               <fieldset class="form-group">
                               <label for="Status">Income Deduction Name: <span class="required_field">*</span></label><span class="search-txt">(Type & search from the list)</span>
                                <div class="div-percent">
                                   <input id="IncomeDeductionTypeID" type="hidden" value="0">            
                                   <input id="IncomeDeductionTypeName" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-complete-type="income-deduction-type" placeholder="Income & Deduction Name"><span class='percent-sign' onclick="ClearIncomeDeductionType()"> <i class="bx bx-trash" style="line-height: 21px;cursor:pointer;" ></i> </span>
                                </div>
                            </fieldset>
                           </div>   
                    
                   </div>
                    <div class="row">
                        <div class="col-md-4">
                              <fieldset class="form-group">
                                <label class="spnReleaseType" for="Status">Release Schedule: </label> <span class="required_field" style="font-weight: 600;">*</span>
                                <div class="form-group">
                                    <select id="ReleaseType" class="form-control">
                                       <option value="">Please Select</option>
                                        <option value="{{ config('app.RELEASE_1ST_HALF_ID') }}">1ST HALF</option>
                                        <option value="{{ config('app.RELEASE_2ND_HALF_ID') }}">2ND HALF</option>
                                        <option value="{{ config('app.RELEASE_EVERY_CUTOFF_ID') }}">EVERY CUTOFF</option>
                                        <option value="{{ config('app.RELEASE_ONE_TIME_ID') }}">ONE TIME</option>
                                    </select>
                                </div>
                            </fieldset>
                        </div>

                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Status">Date Issued : </label>
                             <div class="div-percent">
                                   <input id="DateIssued" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" ><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;" onclick="LoadCalendar('DateIssued')"></i> </span>
                                </div>
                          </fieldset>                          
                        </div>

                      <div class="col-md-4">
                         <fieldset class="form-group">
                            <label class="dtReleaseType" for="Status">Date Start Payment : <span class="required_field">*</span></label>
                             <div class="div-percent">
                                   <input id="DateStartPayment" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" ><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;" onclick="LoadCalendar('DateStartPayment')"></i> </span>
                                </div>
                          </fieldset>  
                      </div>
                     </div>

                    <div id="divFirstSecondHalf" style="display:none;"> 
                        <div class="row">
                          <div class="col-md-4">
                              <fieldset class="form-group">
                              <label id="lblDeductionAmount" for="DeductionAmnt"> Deduction Amount: <span class="required_field">*</span></label>
                               <input id="DeductionAmnt" type="text" class="form-control DecimalOnly compute_total_incomededuction text-align-right" placeholder="Amount" autocomplete="off">
                            </fieldset>
                          </div> 
                          <div class="col-md-4">
                             <fieldset class="form-group">
                              <label for="Status">Interest Amount: </label>
                               <input id="InterestAmnt" type="text" class="form-control DecimalOnly compute_total_incomededuction text-align-right" placeholder="Interest Amount" autocomplete="off">
                            </fieldset>
                          </div>                                        
                          <div class="col-md-4">
                              <fieldset class="form-group">
                              <label  id="lblTotalDeductionAmount" for="Status">Total Deduction Amount: <span class="required_field">*</span></label>
                               <input id="TotalIncomeDeductionAmnt" type="text" class="form-control DecimalOnly text-align-right" placeholder="Total Amount" autocomplete="off" style="border:#ccc 1px solid;" disabled>
                            </fieldset>
                          </div>                
                      </div>

                       <div class="row">
                        <div class="col-xs-4 col-md-8"> </div>                        
                            <div class="col-md-4">
                              <fieldset class="form-group">
                              <label for="Status"> Amortization Amount: <span class="required_field">*</span></label>
                               <input id="AmortizationAmnt" type="text" class="form-control DecimalOnly  compute_total_get_months text-align-right" placeholder="Amortization Amount" autocomplete="off">
                            </fieldset>
                          </div> 
                       </div>

                        <!-- <div id="NoCutOff" class="row" style="display:none;">
                        <div class="col-xs-4 col-md-8"> </div> 
                            <div class="col-md-4">
                              <fieldset class="form-group">
                              <label for="Status"> No. of Period Cut-Off To Pay: </label>
                               <input id="MonthsToPay" type="text" class="form-control DecimalOnly compute_total_get_amortization text-align-right" placeholder="No. of Period Cut-Off To Pay" autocomplete="off">
                            </fieldset>
                          </div>                           
                       </div> -->

                  </div>
                
                   <div id="divEveryOneTime" style="display:none;"> 
                    <div class="row">
                        <div class="col-xs-4 col-md-8"> </div>    
                          <div class="col-md-4">
                            <fieldset class="form-group">
                            <label id="lblEveryOneTime" for="Status">Income Amount: <span class="required_field">*</span></label>
                             <input id="IncomeAmnt" type="text" class="form-control DecimalOnly text-align-right" placeholder="Amount" autocomplete="off">
                          </fieldset>
                        </div>
                      </div> 
                   </div>    
                
                    <div id="divTotalPayment" class="row" style="display:none;">
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
                                                                                                                                
                    <div class="row">
                        <div class="form-group" style="width:100%;padding: 5px;">                             
                            <label for="Remarks">Remarks: <span style="font-size: 11px;color: #b62020;  text-transform: lowercase;font-style: italic; font-weight: normal;"> ( characters: &nbsp; </span> <span class="remaining_chars"> 250</span></label><span style="font-size: 11px;color: #b62020;  text-transform: lowercase;font-style: italic; font-weight: normal;">&nbsp;)</span>
                            <textarea id="Remarks" class="form-control" rows="4"></textarea>                           
                       </div>
                   </div> 
                    <hr>
                    <div class="row" style="float:right;">
                        <button id="btnSaveRecord" type="button" class="btn btn-primary ml-1" onclick="SaveRecord()">
                          <i class="bx bx-check d-block d-sm-none"></i>
                          <span class="d-none d-sm-block"> <i class='bx bx-save mr-1' style="font-size: 21px;"></i>Save</span>
                       </button>
                       <button id="btnCancelRecord" type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-left:10px;">
                          <i class="bx bx-x d-block d-sm-none"></i>
                           <span class="d-none d-sm-block">Cancel</span>
                       </button>
                     </div>
                </div>
        
            </div>
        </div>
    </div>
    <!-- END MODAL -->

   <!-- UPLOAD MODAL -->
    <div id="upload-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title white-color">Income/Deduction Excel Uploader </h5>  
                       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>                
                </div>
                <div class="modal-body">
                    <div class="row">
                         <h5 style="padding-top:10px;padding-bottom: 10px;">Browse Income/Deduction Summary csv file:</h5>
                         <fieldset class="form-group">
                                <label for="myfile">Select files:</label>
                                 <input type="file" id="ExcelFile" name="ExcelFile" accept=".csv"/>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer">

                    <a href="{{URL::asset('public/web/excel template/Income-Deduction-Summary-Template.csv')}}" id="btnDownloadTemplate" class="btn btn-light-secondary">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Download Template Format</span>
                    </a>

                    <button id="btnUploadTSSCSV" type="button" class="btn btn-primary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Upload CSV</span>
                    </button>
                 
                </div>
            </div>
        </div>
    </div>

     <!-- APPROVED MODAL -->
    <div id="approve-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background:green;">
                     <h5 class="modal-title white-color">Set approve employee income & deduction </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                         <fieldset class="form-group">
                                 <input type="hidden" class="IncomeDeductionIDStatus" value="0" readonly>
                                <label style="text-transform: unset;">Do you want to approve this record?</label>
                        </fieldset>
                    </div>
                </div>

                <div class="modal-footer" style="padding-right: 25px;">
                   <button id="btnApproveIncomeDeduction" type="button" class="btn btn-primary ml-1" style="background:green !important;" onclick="SetIncomeDeductionStatus('Approved')">
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
    <div id="cancel-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header" style="background:red;">
                    <h5 class="modal-title white-color">Set cancel employee income & deduction </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                         <fieldset class="form-group">
                                <input type="hidden" class="IncomeDeductionIDStatus" value="0" readonly>
                                <label style="text-transform: unset;">Do you want to cancel this record?</label>
                        </fieldset>
                    </div>
                </div>

                <div class="modal-footer" style="padding-right: 25px;">                
                     <button id="btnCancelIncomeDeduction" type="button" class="btn btn-primary ml-1" style="background:red !important;" onclick="SetIncomeDeductionStatus('Cancelled')">
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

    <!--ONHOLD MODAL -->
    <div id="onhold-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header" style="background:blue;">
                    <h5 class="modal-title white-color">On Hold Payment Income & Deduction </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                         <fieldset class="form-group">
                                <input type="hidden" class="IncomeDeductionIDStatus" value="0" readonly>
                                <label style="text-transform: unset;">Do you want to on hold the payment & deduction of this record?</label>
                        </fieldset>
                    </div>
                </div>

                <div class="modal-footer" style="padding-right: 25px;">                
                     <button id="btnCancelIncomeDeduction" type="button" class="btn btn-primary ml-1" style="background:blue !important;" onclick="SetIncomeDeductionStatus('OnHold')">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Set On Hold</span>
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

    <!--RESUME MODAL -->
    <div id="resume-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header" style="background:blue;">
                    <h5 class="modal-title white-color">Set resume payment income & deduction </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                         <fieldset class="form-group">
                                <input type="hidden" class="IncomeDeductionIDStatus" value="0" readonly>
                                <label style="text-transform: unset;">Do you want to resume the payment & deduction of this record?</label>
                        </fieldset>
                    </div>
                </div>

                <div class="modal-footer" style="padding-right: 25px;">                
                     <button id="btnCancelIncomeDeduction" type="button" class="btn btn-primary ml-1" style="background:blue !important;" onclick="SetIncomeDeductionStatus('Approved')">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Set Resume</span>
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
  <div id="excel-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="left:10px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">

                <div class="modal-header">
                    <h5 class="modal-title white-color"> Review Excel Data: <span id="spnUploadedRecord">0</span> has uploaded from excel. <span id="spnUploadedErrorRecord"></span></h5> 
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
                                    <th style="color: white;">EMPLOYEE ID</th>
                                    <th style="color: white;">EMPLOYEE NAME</th>
                                    <th style="color: white;">FREQUENCY</th>
                                    <th style="color: white;">CODE</th>
                                    <th style="color: white;">TYPE</th>
                                    <th style="color: white;">NAME</th>       
                                    <th style="color: white;">AMOUNT</th>                
                                    <th style="color: white;">STATUS</th>
                                    <th style="color: white;">UPLOAD STATUS</th>
                               </tr>
                            </thead> 
                    </table>            
                </div>

              <div id="divTempPaging" class="col-md-11" style="display: none;">   
                <div style="width:110%;font-size: 11px;">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      <ul class="pagination ul-paging-income scrollbar" style="overflow-x: auto;"></ul>
                     </div>
                    </div>
              </div>

                <div class="modal-footer" style="margin-top: -6px;"> 
                    <div style="float:left;width: 70%;text-align: left;">
                   
                    <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 16px;">
                         * Records highlighted in red are missing in employee references based on the employee code in the Excel file.
                    </p>                  
                    <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 16px;">
                        * Records highlighted in red can be also missing in income & deduction reference based on income & deduction in the Excel file.
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
                                <th style="color:#fff;">PAYMENT REFERENCE</th>
                                <th style="color:#fff;">INCOME DEDUCTION CODE</th>
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
                            <label for="ledgerTotalPaymentMade">Total Deduction Amount:</label>
                             <input id="ledgerTotalLoanAmount" type="text" class="form-control" placeholder="Total Incomde Deduction Amount" style="text-align:right;" readonly>                             
                          </fieldset>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-md-9">
                        </div>
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="ledgerTotalPaymentMade">Total Payment Made:</label>
                             <input id="ledgerTotalPaymentMade" type="text" class="form-control" placeholder="Total Payment Made" style="text-align:right;" readonly>                             
                          </fieldset>
                        </div>
                    </div> 
                     <div class="row">
                        <div class="col-md-9">
                        </div>
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="ledgerTotalPaymentMade">Remaining Balance:</label>
                             <input id="ledgerTotalRemainingBalance" type="text" class="form-control" placeholder="Remaining Balance" style="text-align:right;color:red;" readonly>                             
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
            "order": [[11, "desc" ]]
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
                    UploadErrorMsg=aData[10];
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
            "order": [[10, "desc" ]]
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
        getRecordList(intCurrentPage, '');        
        isPageFirstLoad = false;

        //EXCEL REVIEW FULL HIGHLIGHT
        var tblExcelList = $('#tblList-Excel').DataTable();
        $('#tblList-Excel tbody').on('click', 'tr', function() {            
            tblExcelList.$('tr.highlighted').removeClass('highlighted');        
            $(this).addClass('highlighted');
        });

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

    $(document).on('change keyup blur','.compute_total_incomededuction',function(){
        RecomputeTotal();
    });

    function RecomputeTotal(){

        vTotalIncomeDeductionAmnt=0;

        var vIncomeDeductionAmnt = 0;
        if($('#DeductionAmnt').length){
            if($("#DeductionAmnt").val() != ""){
                var strValue = $("#DeductionAmnt").val();
                vIncomeDeductionAmnt = parseFloat(strValue.replace(",",""));
            }
        } 

        var vIntrstLoanAmnt = 0;
        if($('#InterestAmnt').length){
            if($("#InterestAmnt").val() != ""){
                var strValue = $("#InterestAmnt").val();
                vIntrstLoanAmnt = parseFloat(strValue.replace(",",""));
            }
        } 

        vTotalIncomeDeductionAmnt = vIncomeDeductionAmnt + vIntrstLoanAmnt;
        $("#TotalIncomeDeductionAmnt").val(FormatDecimal(vTotalIncomeDeductionAmnt,2));
                            
    }

    $(document).on('change keyup blur','.compute_total_get_amortization',function(){
        RecomputGetAmortizationTotal();
    });

     function RecomputGetAmortizationTotal(){

        vAmortizationAmnt=0;
        var vTotalIncomeDeductionAmnt = 0;
        if($('#TotalIncomeDeductionAmnt').length){
            if($("#TotalIncomeDeductionAmnt").val() != ""){
                var strValue = $("#TotalIncomeDeductionAmnt").val();
                vTotalIncomeDeductionAmnt = parseFloat(strValue.replace(",",""));
            }
        } 

        var vMonthsToPay = 0;
        if($('#MonthsToPay').length){
            if($("#MonthsToPay").val() != "" && $("#MonthsToPay").val() > 0){
                var strValue = $("#MonthsToPay").val();
                vMonthsToPay = parseFloat(strValue.replace(",",""));
            }
        } 

        vAmortizationAmnt = vTotalIncomeDeductionAmnt / vMonthsToPay;

        if(vAmortizationAmnt === Infinity){
            vAmortizationAmnt=0;
        }else{
            vAmortizationAmnt = vTotalIncomeDeductionAmnt / vMonthsToPay;
        }

        $("#AmortizationAmnt").val(FormatDecimal(vAmortizationAmnt,2));
                            
    }

     $(document).on('change keyup blur','.compute_total_get_months',function(){
        RecomputGetMonthsToPay();
    });

     function RecomputGetMonthsToPay(){

        vMonthsToPay=0;

        var vTotalIncomeDeductionAmnt = 0;
        if($('#TotalIncomeDeductionAmnt').length){
            if($("#TotalIncomeDeductionAmnt").val() != "" ){
                var strValue = $("#TotalIncomeDeductionAmnt").val();
                vTotalIncomeDeductionAmnt = parseFloat(strValue.replace(",",""));
            }
        } 

        var vAmortizationAmnt = 0;
        if($('#AmortizationAmnt').length){
            if($("#AmortizationAmnt").val() != "" && $("#AmortizationAmnt").val() > 0){
                var strValue = $("#AmortizationAmnt").val();
                vAmortizationAmnt = parseFloat(strValue.replace(",",""));
            }
        } 

         vMonthsToPay = vTotalIncomeDeductionAmnt / vAmortizationAmnt;
         vMonthsToPay=Math.ceil(vMonthsToPay);

        if(vMonthsToPay === Infinity){
            vMonthsToPay=0;
        }else{
             vMonthsToPay = vTotalIncomeDeductionAmnt / vAmortizationAmnt;
             vMonthsToPay=Math.ceil(vMonthsToPay);
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
            url: "{{ route('get-employee-income-deduction-transaction-list')}}",
            dataType: "json",
            success: function(data){
                total_rec=data.TotalRecord;
                LoadRecordList(data.EmployeeIncomeDeductionList);
                 if(total_rec>0){
                     CreateEmployeeIncomeDeductionPaging(total_rec,vLimit);  
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

     function CreateEmployeeIncomeDeductionPaging(vTotalRecord,vLimit){

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

        Balance= vData.TotalIncomeDeductionAmount - vData.TotalPayment;

        tdAction="";

      if(IsAdmin==1 || IsAllowView==1){

        tdAction = "<div class='dropdown'>";

                        if(vData.Status=='Pending'){
                             tdAction = tdAction + "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:red;'></span> ";  
                             tdAction = tdAction +  "<div class='dropdown-menu dropdown-menu-right'>"; 
                         }else if(vData.Status=='Approved'){
                            tdAction = tdAction + "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:green;'></span> ";
                            tdAction = tdAction +  "<div class='dropdown-menu dropdown-menu-right'>"; 
                        }else if(vData.Status=='OnHold'){
                            tdAction = tdAction + "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:blue;'></span> ";
                            tdAction = tdAction +  "<div class='dropdown-menu dropdown-menu-right'>";                              
                        }else if(vData.Status=='Cancelled'){
                            tdAction = tdAction + "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:#f68c1f;'></span> ";
                            tdAction = tdAction +  "<div class='dropdown-menu dropdown-menu-right'>"; 
                         }
                      
                        
                        if( vData.Status=='Pending' && (IsAdmin==1 || IsAllowEdit==1)){

                              tdAction = tdAction + 

                                   "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",1,false)' style='border-bottom: 1px solid lightgray;'color: black;>"+
                                      "<i class='bx bx-edit-alt mr-1'></i> " +
                                      "Edit Employee Income & Deduction" +
                                  "</a>";

                        }else if( vData.Status=='Approved' && IsAdmin==1){

                          tdAction = tdAction + 

                               "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",1,false)' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                  "<i class='bx bx-search-alt mr-1'></i> " +
                                  "Edit Employee Income & Deduction" +
                              "</a>";

                        }else{

                          tdAction = tdAction + 

                               "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",1,true)' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                  "<i class='bx bx-search-alt mr-1'></i> " +
                                  "View Employee Income & Deduction" +
                              "</a>";

                        }

                        if( vData.Status=='Pending' && (IsAdmin==1 || IsAllowApprove==1)){

                         tdAction = tdAction + 

                             "<a class='dropdown-item' href='javascript:void(0);' onclick='ApproveRecord(" + vData.ID + ")' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-check-circle mr-1'></i> " +
                                "Approve Employee Income & Deduction" +
                            "</a>"+

                             "<a class='dropdown-item' href='javascript:void(0);' onclick='CancelRecord(" + vData.ID + ")' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-x-circle mr-1'></i> " +
                                "Cancel Employee Income & Deduction" +
                            "</a>";
                        }


                        if(vData.Status=='Approved' && vData.TotalPayment>0 ){

                             tdAction = tdAction +
                               "<a class='dropdown-item' href='javascript:void(0);' onclick='ViewPaymentLedgerHistory(" + vData.ID + ")' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-wallet mr-1'></i> " +
                                "View Payment History" +
                            "</a>";

                        }

                 
                       if(vData.Status=='Approved' && (IsAdmin==1 || IsAllowPrint==1)){

                            tdAction = tdAction +
                              "<a class='dropdown-item' href='javascript:void(0);' onclick='OnHold(" + vData.ID + ",1,false)' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                  "<i class='bx bx-x-circle mr-1'></i> " +
                                  "On Hold Payment Deduction" +
                              "</a>"+

                            "<a class='dropdown-item' href='javascript:void(0);' onclick='PrintIncomeDeduction(" + vData.ID + ")' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-printer mr-1'></i> " +
                                "Print Income & Deduction " +
                            "</a>";
                         
                        }

                       if( vData.Status=='OnHold' && (IsAdmin==1 || IsAllowCancel==1)){

                        tdAction = tdAction +                               
                              "<a class='dropdown-item' href='javascript:void(0);' onclick='Resume(" + vData.ID + ",1,false)' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                  "<i class='bx bx-x-circle mr-1'></i> " +
                                  "Set Resume Payment Deduction" +
                              "</a>"+


                              "<a class='dropdown-item' href='javascript:void(0);' onclick='PrintIncomeDeduction(" + vData.ID + ")' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-printer mr-1'></i> " +
                                "Print Income & Deduction " +
                            "</a>";                   
                        }
            
                                         
                        tdAction = tdAction +  "</div>"+
                      
                    "</div>";

        }

        
        tdCategory = "";
        if(vData.Category == 'EARNING'){
            tdCategory += "<span style='color:green;display:flex;'>EARNING</span>";
        }else{
            tdCategory += "<span style='color:red;display:flex;'>DEDUCTION</span>";
        }

        tdEmpNo = "<span>" + vData.EmployeeNumber + "</span>";
        tdEmpName = "<span>" + vData.FullName + "</span>";

         tdReleaseType = "";
        if(vData.ReleaseTypeID == '1'){
            tdReleaseType += "<span> 1ST HALF </span>";
        }else if(vData.ReleaseTypeID == '2'){
            tdReleaseType += "<span> 2ND HALF </span>";
        }else if(vData.ReleaseTypeID == '3'){
            tdReleaseType += "<span> EVERY CUTOFF </span>";
         }else if(vData.ReleaseTypeID == '4'){
            tdReleaseType += "<span> EVERY MONTH </span>";
         }else if(vData.ReleaseTypeID == '5'){
            tdReleaseType += "<span> EVERY TWO MONTHS </span>";
         }else{
          tdReleaseType += "<span> ONE TIME </span>";
         }

        tdIncomdeDeductionCode = "<span>" + vData.IncomeDeductionTypeCode + "</span>";
        tdIncomdeDeductionName = "<span>" + vData.IncomeDeductionTypeName + "</span>";

       if(vData.Category=='DEDUCTION'){
          tdInterestAmount = "<span>" + FormatDecimal(vData.InterestAmount,2) + "</span>";
       }else{
         tdInterestAmount = "<span></span>";
       }

        
      if(vData.Category=='DEDUCTION'){
           tdAmortizationAmount = "<span>" + FormatDecimal(vData.AmortizationAmount,2) + "</span>";
       }else{
         tdAmortizationAmount = "<span></span>";
       }

        tdIncomeDeductionAmount = "<span>" + FormatDecimal(vData.IncomeDeductionAmount,2) + "</span>";      
        tdTotalIncomeDeductionAmount = "<span>" + FormatDecimal(vData.TotalIncomeDeductionAmount,2) + "</span>";


        tdTotalPaymentMade = "<span>" + FormatDecimal(vData.TotalPayment,2) + "</span>";

        if(Balance <=0 ){
          tdRemainingBalance = "<span> 0.00 </span>";
        }else{
          tdRemainingBalance = "<span>" + FormatDecimal(vData.TotalIncomeDeductionAmount - vData.TotalPayment,2) + "</span>";
        }

         tdStatus = "";
        if(vData.Status == 'Approved'){
            tdStatus += "<span style='color:green;display:flex;'> <i class='bx bx-check-circle'></i> Approved </span>";
        }
        if(vData.Status == 'Pending'){
            tdStatus += "<span style='color:red;display:flex;'> <i class='bx bx-x-circle'></i> Pending </span>";
        }
        if(vData.Status == 'OnHold'){
            tdStatus += "<span style='color:blue;display:flex;'> <i class='bx bx-x-circle'></i> On Hold </span>";
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
                curData[2] = tdReleaseType;                                             
                curData[3] = tdEmpNo;
                curData[4] = tdEmpName;
                curData[5] = tdCategory;                
                curData[6] = tdIncomdeDeductionCode;
                curData[7] = tdIncomdeDeductionName;                                
                curData[8] = tdTotalIncomeDeductionAmount;
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
                    tdReleaseType,                                                       
                    tdEmpNo,
                    tdEmpName,
                    tdCategory,
                    tdIncomdeDeductionCode,
                    tdIncomdeDeductionName,                                      
                    tdTotalIncomeDeductionAmount,
                    tdTotalPaymentMade,        
                    tdRemainingBalance,            
                    tdStatus
                ]).draw();          
        }
    }

    function Clearfields(){

        vCurrentDate=new Date();
        $("#TransDate").val(getFormattedDate(vCurrentDate));

        $("#EmployeeIncomeDeductionTransID").val(0);      
        
        $("#EmployeeID").val(0);
        $("#EmployeeNo").val('');
        $("#EmployeeName").val('');

        $("#ReleaseType").val('').change();        
        $("#DateStartPayment").val('');        
 
        $("#IncomeDeductionTypeName").val('');
        $("#IncomeDeductionTypeID").val(0);
        $("#IncomeDeductionTypeCode").val('');
        $("#IncomeDeductionType").val('');

        $(".spnType").text('');
        $(".spnReleaseType").text('Frequency Schedule:');
        $(".dtReleaseType").text('Date Start Payment:');

        $("#lblDeductionAmount").text('Deduction Amount:');
        $("#lblTotalDeductionAmount").text('Total Deduction Amount:');
        
        $("#DeductionAmnt").val('');        
        $("#IncomeAmnt").val('');        
        
        $("#AmortizationAmnt").val('');
        $("#InterestAmnt").val('');
        $("#IncomeDeductionAmnt").val('');
        $("#MonthsToPay").val('');        
        $("#TotalIncomeDeductionAmnt").val('');

        $("#TotalPayment").val(FormatDecimal(0,2));
        $("#TotalBalance").val(FormatDecimal(0,2));
                      
        $("#ReferenceNo").val('');
        $("#DateIssued").val('');        

        $("#Remarks").val('');
        $(".remaining_chars").text('250');
            
        $("#Status").val('').change();
        $("#btnSaveRecord").show();

        $("#divTotalPayment").hide();
        $("#divTotalBalance").hide();

        resetTextBorderToNormal();
    }

    function EnabledDisbledText(vEnabled){

    
        $("#Remarks").attr('disabled', vEnabled);

        $("#DateStartPayment").attr('disabled', vEnabled);
        $("#ReleaseType").attr('disabled', vEnabled);

        $("#EmployeeName").attr('disabled', vEnabled);
        
        $("#IncomeDeductionTypeName").attr('disabled', vEnabled);
        
        $("#ReferenceNo").attr('disabled', vEnabled);
        $("#DateIssued").attr('disabled', vEnabled);
        $("#DateStartPayment").attr('disabled', vEnabled);

        $("#AmortizationAmnt").attr('disabled', vEnabled);
        $("#InterestAmnt").attr('disabled', vEnabled);

        $("#DeductionAmnt").attr('disabled', vEnabled);
        $("#IncomeAmnt").attr('disabled', vEnabled);            

    }

    function resetTextBorderToNormal(){
                
        $("#EmployeeName").css({"border":"#ccc 1px solid"});  
        
        $("#DateStartPayment").css({"border":"#ccc 1px solid"});
        $("#ReleaseType").css({"border":"#ccc 1px solid"});
        
        $("#IncomeDeductionTypeName").css({"border":"#ccc 1px solid"});
        
        $("#ReferenceNo").css({"border":"#ccc 1px solid"}); 
        $("#DateIssued").css({"border":"#ccc 1px solid"});
        $("#DateStartPayment").css({"border":"#ccc 1px solid"});

        $("#AmortizationAmnt").css({"border":"#ccc 1px solid"});
        $("#InterestAmnt").css({"border":"#ccc 1px solid"});

        $("#IncomeAmnt").css({"border":"#ccc 1px solid"});    
        $("#DeductionAmnt").css({"border":"#ccc 1px solid"});

        $("#Status").css({"border":"#ccc 1px solid"});

    }

    function NewRecord(){

        Clearfields();
        EnabledDisbledText(false);

        $("#IncomeDeductionTable").val(1);

        $("#PayrollPeriodID").val('{{Session::get('ADMIN_PAYROLL_PERIOD_SCHED_ID')}}');
        $("#PayrollPeriodYear").val('{{Session::get('ADMIN_PAYROLL_PERIOD_SCHED_YEAR')}}');
        $("#PayrollPeriodCode").val('{{Session::get('ADMIN_PAYROLL_PERIOD_SCHED_CODE')}}');
        $("#PayrollPeriodName").val('{{Session::get('ADMIN_PAYROLL_PERIOD_SCHED_CODE').': '.Session::get('ADMIN_PAYROLL_PERIOD_SCHED_START').' - '.Session::get('ADMIN_PAYROLL_PERIOD_SCHED_END')}}');

        $("#Status").val('Pending');        
        $("#Status").attr("style", "color: red !important; font-weight: bold; border: 1px solid rgb(204, 204, 204)");
        $("#EmployeeNo").attr("style", "border: 1px solid rgb(204, 204, 204)");

        $("#IncomeDeductionTypeCode").attr("style", "border: 1px solid rgb(204, 204, 204)");
        $("#IncomeDeductionType").attr("style", "border: 1px solid rgb(204, 204, 204)");

        $("#btnSaveRecord").show();
        $("#btnCancelRecord").text('Cancel');

        $("#divTotalPayment").hide();
        $("#divTotalBalance").hide();

        $("#bntViewPaymentHistory").hide();
        $("#record-modal").modal();

    }

    function CancelRecord(vRecordID){
        $(".IncomeDeductionIDStatus").val(vRecordID);
        $("#cancel-modal").modal();
    }

    function ApproveRecord(vRecordID){
        $(".IncomeDeductionIDStatus").val(vRecordID);
        $("#approve-modal").modal();
    }

     function OnHold(vRecordID){
       $(".IncomeDeductionIDStatus").val(vRecordID);
        $("#onhold-modal").modal();
    }

    function Resume(vRecordID){
       $(".IncomeDeductionIDStatus").val(vRecordID);
        $("#resume-modal").modal();
    }

    function UploadExcelRecord(){

        Clearfields();
        $("#IncomeDeductionTable").val(0);
        $("#spnExcelRecord").val(0);
        $("#ExcelFile").val('');
        $("#upload-modal").modal();
       
    }

  function SetIncomeDeductionStatus(vStatus){

    vRecordID= $(".IncomeDeductionIDStatus").val();
      if(vRecordID>0){
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                IncomeDeductionID: vRecordID,
                NewStatus: vStatus
            },
            url: "{{ route('do-set-income-deduction-transaction-status') }}",
            dataType: "json",
            success: function(data){
              if(data.Response =='Success'){
                 showHasSuccessMessage(data.ResponseMessage);
                 LoadRecordRow(data.EmployeeIncomeDeductionTransactionInfo);

                  $("#approve-modal").modal('hide');
                  $("#cancel-modal").modal('hide');

                  $("#onhold-modal").modal('hide');
                  $("#resume-modal").modal('hide');

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
                IncomeDeductionTransID: vRecID               
            },
            url: "{{ route('get-employee-income-deduction-ledger-payment-list') }}",
            dataType: "json",
            success: function(data){                
                
                LoadEmployeeIncomeDeductionLedgerPaymentHistoryList(data.EmployeeIncomeDeductionHistory);
                $("#ledger-modal").modal('show');                   
            },
            error: function(data){
                console.log(data.responseText);
            },
            beforeSend:function(vData){              
            }
        });
       
    }

     function LoadEmployeeIncomeDeductionLedgerPaymentHistoryList(vList){ 
        
        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadEmployeeIncomeDeductionLedgerPaymentHistoryRowList(vList[x]);                
            }
        }         
    }

    function LoadEmployeeIncomeDeductionLedgerPaymentHistoryRowList(vData){
        
        var tblList = $("#tblEmployee-Ledger-Payment-List").DataTable();
       
        var vLedgerTotalPaymentMade=0;        
        var vLedgerRemainingBalance=0;
        var vTotalIncomeDeductionAmount=0;

        vLedgerTotalPaymentMade=vData.TotalPayment;
        vTotalIncomeDeductionAmount=vData.TotalIncomeDeductionAmount;

        vLedgerRemainingBalance=parseFloat(vTotalIncomeDeductionAmount) - parseFloat(vLedgerTotalPaymentMade);

        if(vLedgerRemainingBalance<=0){
           vLedgerRemainingBalance=0;
        }

        $("#ledgerTotalLoanAmount").val(FormatDecimal(vTotalIncomeDeductionAmount,2));
        $("#ledgerTotalPaymentMade").val(FormatDecimal(vLedgerTotalPaymentMade,2));
        $("#ledgerTotalRemainingBalance").val(FormatDecimal(vLedgerRemainingBalance,2));

        tdID = vData.ID;              
        tdPaymentDate = "<span>" + vData.PaymentDateFormat + "</span>";

        if(vData.PaymentModuleType=='Manual'){
           tdPaymentFrom = "<span> Manual Payment </span>";
        }else{
           tdPaymentFrom = "<span> Payroll Deduction </span>";
        }

        tdDeductionCode = "<span>" +vData.DeductionCode + "</span>";
        tdDeductionName = "<span>" + vData.DeductionName + "</span>";
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
                curData[3] = tdDeductionCode;
                curData[4] = tdDeductionName;
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
                 tdDeductionCode,    
                 tdDeductionName,
                 tdAmountPayment                              
                ]).draw();          
        }          
    }

    function EditRecord(vRecordID,vTable,vAllowEdit){

        if(vTable==0){
           var postURL="{{ URL::route('get-employee-income-deduction-transaction-temp-info')}}";
           $("#IncomeDeductionTable").val(0); // Temp Table
           $("#bntViewPaymentHistory").hide();
        }else{
          var postURL="{{ URL::route('get-employee-income-deduction-transaction-info')}}";
          $("#IncomeDeductionTable").val(1); // Final Table
          $("#bntViewPaymentHistory").show();
        }

        if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    EmployeeIncomeDeductionTransID: vRecordID,                    
                },
                url: postURL,
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.EmployeeIncomeDeductionTransactionInfo != undefined){

                       Clearfields();

                        $("#EmployeeIncomeDeductionTransID").val(data.EmployeeIncomeDeductionTransactionInfo.ID);

                        $("#EmployeeID").val(data.EmployeeIncomeDeductionTransactionInfo.EmployeeID);
                        $("#EmployeeNo").val(data.EmployeeIncomeDeductionTransactionInfo.EmployeeNumber);
                        $("#EmployeeNo").attr("style", "border: 1px solid rgb(204, 204, 204) !important");

                        if(data.EmployeeIncomeDeductionTransactionInfo.EmployeeID<=0){
                             $("#EmployeeName").val('');
                        }else{
                            $("#EmployeeName").val(data.EmployeeIncomeDeductionTransactionInfo.FullName); 
                        }
                                                            
                         $("#TransDate").val(data.EmployeeIncomeDeductionTransactionInfo.TransactionDateFormat);
                                             
                         $("#IncomeDeductionTypeID").val(data.EmployeeIncomeDeductionTransactionInfo.IncomeDeductionTypeID);
                         $("#IncomeDeductionTypeCode").val(data.EmployeeIncomeDeductionTransactionInfo.IncomeDeductionTypeCode);
                
                        if(data.EmployeeIncomeDeductionTransactionInfo.IncomeDeductionTypeID<=0){
                             $("#IncomeDeductionTypeName").val('');
                        }else{
                            $("#IncomeDeductionTypeName").val(data.EmployeeIncomeDeductionTransactionInfo.IncomeDeductionTypeCode+' - '+data.EmployeeIncomeDeductionTransactionInfo.IncomeDeductionTypeName);                             
                            $("#IncomeDeductionType").val(data.EmployeeIncomeDeductionTransactionInfo.Category);
                        }

                        if(data.EmployeeIncomeDeductionTransactionInfo.Category=='DEDUCTION'){  

                          $("#bntViewPaymentHistory").show(); 

                          $(".spnReleaseType").text('Deduction Schedule:');
                          $(".dtReleaseType").text('Date Start Payment:');

                          $("#lblDeductionAmount").text('Deduction Amount:');
                          $("#lblTotalDeductionAmount").text('Total Deduction Amount:');

                                                     
                        }else{
                   
                          $("#AmortizationAmnt").val('');                        
                          $("#InterestAmnt").val('');                        
                          $("#MonthsToPay").val('');  
                          $("#bntViewPaymentHistory").hide();                                                                          

                          $(".spnReleaseType").text('Release Schedule:');
                          $(".dtReleaseType").text('Date Start Release:');

                          $("#lblDeductionAmount").text('Income Amount:');
                          $("#lblTotalDeductionAmount").text('Total Income Amount:');

                        }  
                        
                        $("#ReferenceNo").val(data.EmployeeIncomeDeductionTransactionInfo.VoucherNo);

                        $("#DateIssued").val(data.EmployeeIncomeDeductionTransactionInfo.DateIssueFormat);          
                        $("#DateStartPayment").val(data.EmployeeIncomeDeductionTransactionInfo.DateStartPaymentFormat);  

                        $("#ReleaseType").val(data.EmployeeIncomeDeductionTransactionInfo.ReleaseTypeID).change();        
                        $("#InterestAmnt").val(FormatDecimal(data.EmployeeIncomeDeductionTransactionInfo.InterestAmount,2));              
                        $("#AmortizationAmnt").val(FormatDecimal(data.EmployeeIncomeDeductionTransactionInfo.AmortizationAmount,2));   

                        if(data.EmployeeIncomeDeductionTransactionInfo.Category=='DEDUCTION'){                               
                            $(".dtReleaseType").text('Date Start Payment:');
                            $("#DeductionAmnt").val(FormatDecimal(data.EmployeeIncomeDeductionTransactionInfo.IncomeDeductionAmount,2));
                            $("#IncomeAmnt").val(FormatDecimal(data.EmployeeIncomeDeductionTransactionInfo.IncomeDeductionAmount,2));                           
                         }else{                           
                           $(".dtReleaseType").text('Date Start Release:');
                           $("#DeductionAmnt").val(FormatDecimal(data.EmployeeIncomeDeductionTransactionInfo.IncomeDeductionAmount,2));
                           $("#IncomeAmnt").val(FormatDecimal(data.EmployeeIncomeDeductionTransactionInfo.IncomeDeductionAmount,2));                           
                         } 

                        $("#IncomeDeductionTypeCode").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#IncomeDeductionType").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                                                                          
                        $("#MonthsToPay").val(parseInt(data.EmployeeIncomeDeductionTransactionInfo.MonthsToPay));              
                        $("#TotalIncomeDeductionAmnt").val(FormatDecimal(data.EmployeeIncomeDeductionTransactionInfo.TotalIncomeDeductionAmount,2));
                                              
                        $("#Remarks").val(data.EmployeeIncomeDeductionTransactionInfo.Remarks);
                        $(".remaining_chars").text(250-data.EmployeeIncomeDeductionTransactionInfo.Remarks.length);                        
                    
                       if(data.EmployeeIncomeDeductionTransactionInfo.Status=='Pending'){
                            $("#Status").val('Pending');
                            $("#Status").attr("style", "color: red !important; font-weight: bold;border: 1px solid rgb(204, 204, 204) !important");
                        }

                        if(data.EmployeeIncomeDeductionTransactionInfo.Status=='Approved'){
                            $("#Status").val('Approved');
                            $("#Status").attr("style", "color: green !important; font-weight: bold;border: 1px solid rgb(204, 204, 204) !important");
                        }

                        if(data.EmployeeIncomeDeductionTransactionInfo.Status=='Cancelled'){
                            $("#Status").val('Cancelled');
                            $("#Status").attr("style", "color: #f68c1f !important; font-weight: bold;border: 1px solid rgb(204, 204, 204) !important");                             
                        }

                        if(data.EmployeeIncomeDeductionTransactionInfo.TotalPayment>0 && data.EmployeeIncomeDeductionTransactionInfo.Status=='Approved'){

                         vTotalBalance= parseFloat(data.EmployeeIncomeDeductionTransactionInfo.TotalIncomeDeductionAmount) - parseFloat(data.EmployeeIncomeDeductionTransactionInfo.TotalPayment);
                          
                          if(vTotalBalance<=0){
                             $("#TotalBalance").val('0.00');                                                        
                          }else{
                             $("#TotalBalance").val(FormatDecimal(vTotalBalance,2));                                                         
                          }

                          alert("naka  bayd na");

                          $("#divTotalPayment").show();

                          if(data.EmployeeIncomeDeductionTransactionInfo.ReleaseTypeID==3 || data.EmployeeIncomeDeductionTransactionInfo.ReleaseTypeID==6){
                            $("#divTotalBalance").hide();
                          }else{
                            $("#divTotalBalance").show();
                          }
                                                                                                 
                         $("#TotalPayment").val(FormatDecimal(data.EmployeeIncomeDeductionTransactionInfo.TotalPayment,2))                                                  
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

    function SaveRecord(){

        vIncomeDeductionTable=$("#IncomeDeductionTable").val();
        
        if(vIncomeDeductionTable==0){ //Temp Table
           var postURL="{{ URL::route('do-save-employee-income-deduction-temp-transaction')}}";      
        }else{ //Final Table
          var postURL="{{ URL::route('do-save-employee-income-deduction-transaction')}}";
        }

        vEmployeeIncomeDeductionTransID= $("#EmployeeIncomeDeductionTransID").val();
        vTransDate= $("#TransDate").val();

        vEmpID=$("#EmployeeID").val();
        vEmpNo=$("#EmployeeNo").val();
                
        vReleaseTypeID=$("#ReleaseType").val();
        vIncomeDeductionType=$("#IncomeDeductionType").val();
        vIncomeDeductionTypeID=$("#IncomeDeductionTypeID").val();
        vIncomeDeductionTypeCode=$("#IncomeDeductionTypeCode").val();
        
        vReferenceNo=$("#ReferenceNo").val();
        vDateIssued=$("#DateIssued").val();

        vDateStartPayment=$("#DateStartPayment").val();
        vInterestAmnt=$("#InterestAmnt").val();   
        
        vTotalMonthsToPay=$("#MonthsToPay").val(); 
                      
        vRemarks=$("#Remarks").val();
        vStatus=$("#Status").val();
        IsUploaded=1;

        if(vReleaseTypeID=='3' || vReleaseTypeID=='6'){             
            vIncomeDeductionAmnt=$("#IncomeAmnt").val();          
            vAmortizationAmnt=$("#IncomeAmnt").val();              
            vTotalIncomeDeductionAmnt=$("#IncomeAmnt").val();          
        }else{
            vIncomeDeductionAmnt=$("#DeductionAmnt").val();          
            vAmortizationAmnt=$("#AmortizationAmnt").val();              
            vTotalIncomeDeductionAmnt=$("#TotalIncomeDeductionAmnt").val();          
        }  

        var checkInput =true;
        checkInput=doCheckIncomeDeductionInput();

        if(!checkInput){
            return;
        }

       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                EmployeeIncomeDeductionTransID: vEmployeeIncomeDeductionTransID,
                TransDate: vTransDate,
                EmpID: vEmpID,
                EmpNo: vEmpNo,                                
                ReleaseTypeID: vReleaseTypeID,                
                IncomeDeductionTypeID: vIncomeDeductionTypeID,
                IncomeDeductionType: vIncomeDeductionType,
                IncomeDeductionTypeCode: vIncomeDeductionTypeCode,
                ReferenceNo: vReferenceNo,
                DateIssued: vDateIssued,                                              
                DateStartPayment: vDateStartPayment,                                              
                InterestAmnt: vInterestAmnt,   
                AmortizationAmnt: vAmortizationAmnt,                 
                IncomeDeductionAmnt: vIncomeDeductionAmnt,                
                TotalMonthsToPay: vTotalMonthsToPay,
                TotalIncomeDeductionAmnt: vTotalIncomeDeductionAmnt,                                                
                Remarks: vRemarks,
                Status: vStatus,
                IsUploaded:1
            },
            url: postURL, 
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

                   if(data.Response =='Success'){
                     showHasSuccessMessage(data.ResponseMessage);
                    if(vIncomeDeductionTable==0){ // Temp Table
                      //LoadTempRecordRow(data.DTRTempInfo);
                      $("#tblList-Excel").DataTable().clear().draw();
                      getIncomeDeductionTempRecordList(1);
                    }else{ // Final Table
                       LoadRecordRow(data.EmployeeIncomeDeductionTransactionInfo);
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
        url: "{{ route('do-upload-save-income-deduction-transaction') }}",
        dataType: "json",
        success: function(data){
              if(data.Response =='Success'){
                 
                if(vTotalError>0){
                   setTimeout(function () {
                      showHasSuccessMessage(data.ResponseMessage);
                      getRecordList(1, '');
                      $("#divLoader").hide(); 
                      $("#excel-modal").modal('hide');                                    
                   }, 3000);                
                }else{                     
                     showHasSuccessMessage(data.ResponseMessage);
                     getRecordList(1, '');
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
            showHasErrorMessage('', 'There are ' + vTotalError + ' income & deduction records will not be save due to data issues.');
            $("#divLoader").show(); 
        }
    });
 }

  function PrintIncomeDeduction(vTransID){
    
    window.open('{{config('app.url')}}admin-employee-income-deduction-print-report?IncomeDeductionTransactionID=' +vTransID, '_blank');
  
 }

  $(document).on('focus','.autocomplete_txt',function(){
      
       isEmployee=false;       
       isIncomdeDeductionType=false;
       var valAttrib  = $(this).attr('data-complete-type');
       
       if(valAttrib=='employee'){
            isEmployee=true;
             searchlen=2;
            var postURL="{{ URL::route('get-employee-search-list')}}";
        }
    
       if(valAttrib=='income-deduction-type'){
            isIncomdeDeductionType=true;
            searchlen=2;
            var postURL="{{ URL::route('get-earning-deduction-type-search-list')}}";
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

                                   if (isIncomdeDeductionType){
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

                     if(isIncomdeDeductionType){

                      $("#IncomeDeductionTypeID").val(seldata[0]);
                      $("#IncomeDeductionTypeCode").val(seldata[1].trim());
                      $("#IncomeDeductionTypeName").val(seldata[2].trim());
                      $("#IncomeDeductionType").val(seldata[3].trim());                      

                      if(seldata[3]=='DEDUCTION'){
                          
                          if($("#ReleaseType").val()=='3' || $("#ReleaseType").val()=='6'){                            
                            $("#divEveryOneTime").show();                                  
                            $("#divFirstSecondHalf").hide();  
                          }else{                            
                            $("#divEveryOneTime").hide();  
                            $("#divFirstSecondHalf").show();                                                                  
                          }
                          
                          $(".spnReleaseType").text('Deduction Schedule:'); 
                          $(".dtReleaseType").text('Date Start Payment:');

                          $("#lblDeductionAmount").text('Deduction Amount:');
                          $("#lblTotalDeductionAmount").text('Total Deduction Amount:');

                          $("#lblEveryOneTime").text('Deduction Amount:');
                                                                     
                      }else if(seldata[3]=='EARNING'){

                         if($("#ReleaseType").val()=='3' || $("#ReleaseType").val()=='6'){
                            $("#divEveryOneTime").show();                                  
                            $("#divFirstSecondHalf").hide();                                
                          }else{
                            $("#divEveryOneTime").hide();  
                            $("#divFirstSecondHalf").show();                                      
                          }

                          $(".spnReleaseType").text('Release Schedule:');    
                          $(".dtReleaseType").text('Date Start Release:');   

                          $("#lblDeductionAmount").text('Income Amount:');
                          $("#lblTotalDeductionAmount").text('Total Income Amount:');  

                          $("#lblEveryOneTime").text('Income Amount:');
                      
                      }else{
                          
                          $("#divEveryOneTime").hide();  

                          $("#AmortizationAmnt").val('');                        
                          $("#InterestAmnt").val('');                        
                          $("#MonthsToPay").val('');                         
                                                                                  
                        }                    
                    }                
              }
        });
    });

  $("#ReleaseType").change(function() { 

      vReleaseType=$(this).val();          
      vIncomeDeductionType=$("#IncomeDeductionType").val();  

      if(vIncomeDeductionType=='EARNING'){

        if($("#ReleaseType").val()=='3' || $("#ReleaseType").val()=='6'){
            $("#divEveryOneTime").show();                                  
            $("#divFirstSecondHalf").hide();  

            $("#DeductionAmnt").val(''); 
            $("#AmortizationAmnt").val('');                                                                           
        }else{
            $("#divEveryOneTime").hide();  
            $("#divFirstSecondHalf").show();  
            $("#IncomeAmnt").val('');                                    
        }

        $(".spnReleaseType").text('Release Schedule:');    
        $(".dtReleaseType").text('Date Start Release:');   

        $("#lblDeductionAmount").text('Income Amount:');
        $("#lblTotalDeductionAmount").text('Total Income Amount:');  

        $("#lblEveryOneTime").text('Income Amount:');
                      
      }else if(vIncomeDeductionType=='DEDUCTION'){

         if($("#ReleaseType").val()=='3' || $("#ReleaseType").val()=='6'){                            
            $("#divEveryOneTime").show();                                  
            $("#divFirstSecondHalf").hide();  
            $("#DeductionAmnt").val('');                                    
            $("#AmortizationAmnt").val(''); 
         }else{                            
            $("#divEveryOneTime").hide();  
            $("#divFirstSecondHalf").show();                
            $("#IncomeAmnt").val('');                                                                        
        }
                          
        $(".spnReleaseType").text('Deduction Schedule:'); 
        $(".dtReleaseType").text('Date Start Payment:');

        $("#lblDeductionAmount").text('Deduction Amount:');
        $("#lblTotalDeductionAmount").text('Total Deduction Amount:');

        $("#lblEveryOneTime").text('Deduction Amount:');

      }else{
         $("#divEveryOneTime").hide();

         $("#lblEveryOneTime").text('');
      }

 });

  function getIncomeDeductionTempRecordList(vPageNo){

      $("#tblList-Excel").DataTable().clear().draw();
      $(".ul-paging-income >.paginate_button").remove();

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
            url: "{{ route('get-employee-income-deduction-transaction-temp-list') }}",
            dataType: "json",
            success: function(data){

                total_rec=data.TotalRecord;
                LoadTempRecordList(data.EmployeeIncomeDeductionTempList);

                  if(total_rec>0){
                     CreateEmployeeIncomeDeductionTempPaging(total_rec,vLimit);  
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

    function CreateEmployeeIncomeDeductionTempPaging(vTotalRecord,vLimit){

       var i;
       paging_button="";
    
        limit=vLimit; //get limit
        totalcount=vTotalRecord; //get total count
        pages=Math.ceil(totalcount/limit); //get pages
      
       if (pages!=1) {     

          paging_button="<li class='paginate_button page-item previous' id='example2_previous'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getIncomeDeductionTempRecordList(1)'>First</a></li>"
          $(".ul-paging-income").append(paging_button);
       }
       
        if (pages>1) {        
          for (i = 1; i <= pages; i++) {            
            paging_button="<li class='paginate_button page-item'><a href='javascript:void(0)' id='paging_button_id"+i+"' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getIncomeDeductionTempRecordList("+i+")'>"+i+"</a></li>"
             $(".ul-paging-income").append(paging_button);
          }
        }
          
       if (pages!=1) {            
        paging_button="<li class='paginate_button page-item next' id='example2_next'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getIncomeDeductionTempRecordList("+pages+",)'>Last</a></li>"
        $(".ul-paging-income").append(paging_button);
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
                                "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",0,true)'>"+
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
                                "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",0,true)'>"+
                                    "<i class='bx bx-edit-alt mr-1'></i> " +
                                    "View Information" +
                                "</a>";
                            }    
                        }

                         tdAction = tdAction +  "</div>"+
                      
                    "</div>";
                
      
        tdEmpNo = "<span>" + vData.EmployeeNumber + "</span>";
        tdEmpName = "<span>" + vData.FullName + "</span>";
        tdIncomdeDeductionCode = "<span>" + vData.IncomeDeductionTypeCode + "</span>";
        tdIncomdeDeductionCategory = "<span>" + vData.Category + "</span>";
        tdIncomdeDeductionName = "<span>" + vData.IncomeDeductionTypeName + "</span>";

        tdReleaseType = "";
        if(vData.ReleaseTypeID == '1'){
            tdReleaseType += "<span> 1ST HALF </span>";
        }else if(vData.ReleaseTypeID == '2'){
            tdReleaseType += "<span> 2ND HALF </span>";
        }else if(vData.ReleaseTypeID == '3'){
            tdReleaseType += "<span> EVERY CUTOFF </span>";
         }else if(vData.ReleaseTypeID == '4'){
            tdReleaseType += "<span> EVERY MONTH </span>";
         }else if(vData.ReleaseTypeID == '5'){
            tdReleaseType += "<span> EVERY TWO MONTHS </span>";
         }else{
          tdReleaseType += "<span> ONE TIME </span>";
         }


        tdInterestAmount = "<span>" + FormatDecimal(vData.InterestAmount,2) + "</span>";
        tdTotalIncomeDeductionAmount = "<span>" + FormatDecimal(vData.TotalIncomeDeductionAmount,2) + "</span>";
        
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
                curData[2] = tdEmpNo;
                curData[3] = tdEmpName;
                curData[4] = tdReleaseType;
                curData[5] = tdIncomdeDeductionCode;
                curData[6] = tdIncomdeDeductionCategory;
                curData[7] = tdIncomdeDeductionName;                
                curData[8] = tdTotalIncomeDeductionAmount;
                curData[9] = tdStatus;
                curData[10] = tdIsUploadError;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,
                    tdAction,                                                        
                    tdEmpNo,
                    tdEmpName,
                    tdReleaseType,
                    tdIncomdeDeductionCode,
                    tdIncomdeDeductionCategory,
                    tdIncomdeDeductionName,                 
                    tdTotalIncomeDeductionAmount,
                    tdStatus,
                    tdIsUploadError
                ]).draw();          
        }
    }

 function clearIncomeDeductionTempTransaction(){

        $("#tblList-Excel").DataTable().clear().draw();

        $.ajax({
            type: "post",
            data: {
             _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}"
            },
            url: "{{ route('do-clear-income-deduction-temp-transaction') }}",
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
        url: "{{ route('do-remove-duplicate-income-deduction-transaction') }}",
        dataType: "json",
        success: function(data){
          if(data.Response =='Success'){
            showHasSuccessMessage(data.ResponseMessage);
            // DeleteTableRow(vRecID);
            $("#tblList-Excel").DataTable().clear().draw();
            getIncomeDeductionTempRecordList(intCurrentPage);
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

  function getIncomeDeductionTempUploadedCount(vExcelRecord){

      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}'
        },
        url: "{{ route('get-income-deduction-temp-transaction-upload-count') }}",
        dataType: "json",
        success: function(data){

          $("#spnUploadedRecord").text(parseInt(data.MaxCount));
          $("#spnExcelRecord").text(parseInt(vExcelRecord-1));

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

    function doCheckIncomeDeductionInput(){

        var IsNotComplete=true;
        var vEmployeeIncomeDeductionTransID = $("#EmployeeIncomeDeductionTransID").val();        
        
        var vTransDate = $("#TransDate").val();
        var vDateStartPayment = $("#DateStartPayment").val();
        var vEmployeeID = $("#EmployeeID").val();
        var vEmployeeNo = $("#EmployeeNo").val();
        var vEmployeeName = $("#EmployeeName").val();
        
        var vIncomeDeductionTypeID = $("#IncomeDeductionTypeID").val();
        var vIncomeDeductionTypeName = $("#IncomeDeductionTypeName").val();
        var vIncomeDeductionType = $("#IncomeDeductionType").val();

        var ReferenceNo = $("#ReferenceNo").val();
        var vDateIssued = $("#DateIssued").val();  
        
        var vDeductionAmnt = $("#DeductionAmnt").val();
        var vIncomeAmnt = $("#IncomeAmnt").val();

        var vAmortizationAmnt = $("#AmortizationAmnt").val();
        var vRemarks = $("#Remarks").val();
        
        var vReleaseType = $("#ReleaseType").val();
        var vStatus = $("#Status").val();

        resetTextBorderToNormal();

       if(vEmployeeID<=0) {
         showHasErrorMessage('EmployeeName','Search and select employee from the list.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vIncomeDeductionTypeID<=0) {
         showHasErrorMessage('IncomeDeductionTypeName','Search and select income & deduction type from the list.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vReleaseType=="") {
          if(vIncomeDeductionType=='EARNING'){
            showHasErrorMessage('ReleaseType','Enter frequency release schedule.');
          }else{
             showHasErrorMessage('ReleaseType','Enter frequency deduction schedule.');   
          }         
         IsNotComplete=false;
         return IsNotComplete;
       }

    if(vIncomeDeductionType=='EARNING'){
        if(vDateStartPayment=="") {
         showHasErrorMessage('DateStartPayment','Enter start release date.');
         IsNotComplete=false;
         return IsNotComplete;
      }    
    }else{
     if(vDateStartPayment=="") {
         showHasErrorMessage('DateStartPayment','Enter start payment date.');
         IsNotComplete=false;
         return IsNotComplete;
      }
    }
      
      
      if(vIncomeDeductionType=='DEDUCTION'){
          
         if($("#ReleaseType").val()=='3' || $("#ReleaseType").val()=='6'){
             if(vIncomeAmnt.trim()=="" || vIncomeAmnt<=0) {        
               showHasErrorMessage('IncomeAmnt','Enter deduction amount.');                 
               IsNotComplete=false;
               return IsNotComplete;
             }else{
                $("#DeductionAmnt").val(vIncomeAmnt);  
                $("#TotalIncomeDeductionAmnt").val(vIncomeAmnt);          
                IsNotComplete=true;
                return IsNotComplete;
             } 
         }else if($("#ReleaseType").val()!='3' || $("#ReleaseType").val()!='6'){     
             if(vDeductionAmnt.trim()=="" || vDeductionAmnt<=0) {        
               showHasErrorMessage('DeductionAmnt','Enter deduction amount.');                 
               IsNotComplete=false;
               return IsNotComplete;
             }else{

                if(vAmortizationAmnt.trim()=="" || vAmortizationAmnt<=0) {
                 showHasErrorMessage('AmortizationAmnt','Enter amortization amount');
                 IsNotComplete=false;
                 return IsNotComplete;
               }else{
                  $("#DeductionAmnt").val(vDeductionAmnt);  
                  $("#TotalIncomeDeductionAmnt").val(vDeductionAmnt);  
                  IsNotComplete=true;
                  return IsNotComplete;
                }                
             } 

          }else{
            if(vDeductionAmnt.trim()=="" || vDeductionAmnt<=0) {        
               showHasErrorMessage('DeductionAmnt','Enter deduction amount.');                 
               IsNotComplete=false;
               return IsNotComplete;
             }else{
                 $("#DeductionAmnt").val(vDeductionAmnt);  
             }  
        }  
         
      }else if(vIncomeDeductionType=='EARNING'){  

         if($("#ReleaseType").val()=='3' || $("#ReleaseType").val()=='6'){
            if(vIncomeAmnt.trim()=="" || vIncomeAmnt<=0) {        
               showHasErrorMessage('IncomeAmnt','Enter income amount.');                 
               IsNotComplete=false;
               return IsNotComplete;
             }else{
                $("#DeductionAmnt").val(vIncomeAmnt);  
                $("#TotalIncomeDeductionAmnt").val(vIncomeAmnt);  
                IsNotComplete=true;
               return IsNotComplete;                
             } 
         }else if($("#ReleaseType").val()!='3' || $("#ReleaseType").val()!='6'){     
             if(vDeductionAmnt.trim()=="" || vDeductionAmnt<=0) {        
               showHasErrorMessage('DeductionAmnt','Enter income amount.');                 
               IsNotComplete=false;
               return IsNotComplete;
             }else{

                if(vAmortizationAmnt.trim()=="" || vAmortizationAmnt<=0) {
                 showHasErrorMessage('AmortizationAmnt','Enter amortization amount');
                 IsNotComplete=false;
                 return IsNotComplete;
               }else{
                  $("#DeductionAmnt").val(vDeductionAmnt);  
                  $("#TotalIncomeDeductionAmnt").val(vDeductionAmnt);  
                  IsNotComplete=true;
                  return IsNotComplete;
                }                
             } 

         }else{

            if(vDeductionAmnt.trim()=="" || vDeductionAmnt<=0) {        
               showHasErrorMessage('DeductionAmnt','Enter deduction amount.');                 
               IsNotComplete=false;
               return IsNotComplete;
             }else{
                 $("#DeductionAmnt").val(vDeductionAmnt); 
                 IsNotComplete=true;
               return IsNotComplete; 
             }  
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


 $("#IncomeDeductionTypeName").blur(function() {
     vIncomeDeductionTypeName=$(this).val();
       if(vIncomeDeductionTypeName.length<=5 || vIncomeDeductionTypeName==''){
         $(".spnType").text('');
         $(".spnReleaseType").text('Frequency Schedule:');
         $(".dtReleaseType").text('Date Start Payment: ');

         $("#lblDeductionAmount").text('Deduction Amount:');
         $("#lblTotalDeductionAmount").text('Total Deduction Amount:');

         $("#IncomeDeductionTypeID").val(0);
         $("#IncomeDeductionTypeCode").val('');
         $("#IncomeDeductionType").val('');        
      }

  });
  
  $("#IncomeDeductionTypeName").keyup(function() { 
    vIncomeDeductionTypeName=$(this).val();
       if(vIncomeDeductionTypeName.length<=5 || vIncomeDeductionTypeName==''){
         $(".spnType").text('');
         $(".spnReleaseType").text('Frequency Schedule:');
         $(".dtReleaseType").text('Date Start Payment: ');

         $("#lblDeductionAmount").text('Deduction Amount:');
         $("#lblTotalDeductionAmount").text('Total Deduction Amount:');

         $("#IncomeDeductionTypeID").val(0);
         $("#IncomeDeductionTypeCode").val('');
         $("#IncomeDeductionType").val('');       
      }
    });

    $("#IncomeDeductionTypeName").change(function() { 
      vIncomeDeductionTypeName=$(this).val();
      vReleaseType=$("ReleaseType").val();

       if(vIncomeDeductionTypeName=='EARNING'){
         $(".spnType").text('');
         $(".spnReleaseType").text('Release Schedule:');
          $(".dtReleaseType").text('Date Start Release: ');

          $("#lblDeductionAmount").text('Income Amount:');
          $("#lblTotalDeductionAmount").text('Total Income Amount:');
                 
      }

      if(vIncomeDeductionTypeName=='DEDUCTION'){

        $(".spnType").text('');
        $(".spnReleaseType").text('Deduction Schedule:');
        $(".dtReleaseType").text('Date Start Payment: ');

        $("#lblDeductionAmount").text('Deduction Amount:');
        $("#lblTotalDeductionAmount").text('Total Deduction Amount:');

        if(vReleaseType=="6"){
          $("#dvDeduction").hide();
        }else{
          $("#dvDeduction").show();
        }                    
      }
    });

$(function() {    
  $("#DateIssued").datepicker();
  $("#DateStartPayment").datepicker();
});

function ClearEmployee(){

   $("#EmployeeID").val('0'); 
   $("#EmployeeNo").val('');
   $("#EmployeeName").val('');

    resetTextBorderToNormal();
 }

 function ClearIncomeDeductionType(){

   $("#IncomeDeductionTypeID").val('0'); 
   $("#IncomeDeductionType").val('');
   $("#IncomeDeductionTypeName").val('');

    resetTextBorderToNormal();
 }

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

   <!-- //NEW STYLE CVS GET DATA AND CHUNK -->
<script>
    document.getElementById('btnUploadTSSCSV').addEventListener('click', function() {

        let  i = 1;
        let  x = 0;
        var recPerBatch=100;
       
        const fileInput = document.getElementById('ExcelFile');

        if (!fileInput.files.length) {
            showHasErrorMessage('','Browse and upload Employee Income & Deduction transaction csv file.');
            return;
        }
        
        const file = fileInput.files[0];
        
        if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
             showHasErrorMessage('','Please upload a valid Employee Income & Deduction csv file.');
            return;
        }
        
        // Read the file
        const reader = new FileReader();
        
        reader.onload = function(e) {

           clearMessageNotification();
           clearIncomeDeductionTempTransaction();

            const contents = e.target.result;
            const employee_incomde_deduction_temp_record = [];
             
            var NoNEmptyRowCount=1; 
            const vDataLen=contents.length-1;
            const lines = contents.split('\n');
            
            //SKIP HEADER COLUMN
            for (let i = 1; i < lines.length; i++) {
                const line = lines[i].trim();

                if (line) {

                    NoNEmptyRowCount++;
                    const vData = line.split(',');

                    vIncomeDeductionTransID = 0;
                    vTransDate=new Date();

                    vReleaseType=(vData[0]!=undefined ? vData[0] : '');

                    if(vReleaseType=='END'){
                       break;
                    }

                    vDateStartPayment=(vData[1]!=undefined ? vData[1] :  '');  
                    vEmployeeNo=(vData[2]!=undefined ? vData[2] : '');
                    vIncomeDeductionTypeCode=(vData[3]!=undefined ? vData[3] : '');

                    // vAmortizationAmnt=0;              
                    vInterestAmnt=0;

                    vIncomeDeductionAmnt=(vData[4]!=undefined ? parseFloat(vData[4],2) : 0);
                    vAmortizationAmnt=(vData[4]!=undefined ? parseFloat(vData[4],2) : 0);                      

                    vRemarks=(vData[5]!=undefined ? vData[5] : '');
                    vTotalIncomeDeductionAmnt = parseFloat(vIncomeDeductionAmnt) + parseFloat(vInterestAmnt);

                    vStatus='Pending';
                    vIsUploaded=1;

                    // Collect employee temp loan data
                    employee_incomde_deduction_temp_record.push({
                        IncomeDeductionTransID: vIncomeDeductionTransID,                
                        ReleaseType: vReleaseType,                
                        TransDate: vTransDate,
                        EmpNo: vEmployeeNo,
                        DateStartPayment: vDateStartPayment,
                        InterestAmnt: vInterestAmnt,
                        AmortizationAmnt: vAmortizationAmnt,
                        IncomeDeductionTypeCode: vIncomeDeductionTypeCode,
                        TotalIncomeDeductionAmnt: vTotalIncomeDeductionAmnt,
                        Remarks: vRemarks,
                        IsUploaded:vIsUploaded,
                        Status: vStatus              
                    });
                }
            }
            
   
           // Process in batches of 10
            const temp_batches = [];
            for (let x = 0; x< employee_incomde_deduction_temp_record.length; x += recPerBatch) {
                temp_batches.push(employee_incomde_deduction_temp_record.slice(x, x + recPerBatch));
            }
          
          
            saveEmployeeTempIncomeDeductionByBatchRecord(0);
            
            // Function to save each batch
            function saveEmployeeTempIncomeDeductionByBatchRecord(batchIndex) {

                  $("#spnTotalData").text(batchIndex * recPerBatch +'/'+ parseInt(NoNEmptyRowCount-2));

                if (batchIndex >= temp_batches.length) {
                     
                    getIncomeDeductionTempUploadedCount(NoNEmptyRowCount-2);
                    getIncomeDeductionTempRecordList(1)

                    $("#upload-modal").modal('hide');
                    $("#excel-modal").modal('show');
                    return;
                }
                
                const currentTempLoanBatch = temp_batches[batchIndex];

                 //SAVE Batch of data
                $.ajax({
                    type: "post",
                    url: "{{ route('do-save-income-deduction-temp-transaction-batch') }}",
                    data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    IncomeDeductionTempDataItems: employee_incomde_deduction_temp_record
                    },
                    dataType: "json",
                    success: function(data){

                        buttonOneClick("btnUploadTSSCSV", "Upload CSV", false);

                        if(data.Response =='Success'){
                          
                          $("#spnTotalData").hide();
                          $("#divLoader").hide();
                             saveEmployeeTempIncomeDeductionByBatchRecord(batchIndex + 1);  // Proc

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



