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
                                    <li class="breadcrumb-item active">Payroll Transaction List
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
                                    <h4 class="card-title">Payroll Transaction List</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <fieldset>
                                                    <div class="input-group">
                                                     @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload==1 || $Allow_Delete_Cancel==1)
                                                        <div class="col-md-8 remove-left-padding" style="margin-top: 2px;z-index: 99;">
                                                     @else
                                                        <div class="col-md-12 remove-left-padding" style="margin-top: 2px;z-index: 99;">
                                                     @endif       
                                                            <select id="SearchPayrollPeriodCode" class="form-control">
                                                             <!--    <option value="">Please Select</option> -->
                                                                @foreach($PayrollPeriodList as $prdrow)
                                                                <option value="{{ $prdrow->ID }}" {{ ($prdrow->ID == Session('ADMIN_PAYROLL_PERIOD_SCHED_ID') ? 'selected' : '' ) }}>{{ $prdrow->Code.' : '.$prdrow->StartDateFormat.' - '.$prdrow->EndDateFormat }}</option>
                                                                @endforeach
                                                            </select>                                
                                                        </div>

                                                       @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload==1)
                                                            <button type="button" class="btn btn-icon btn-outline-primary mr-1" style="margin:2px !important;width: 17%;" onclick="GeneratePayrollRecord()">
                                                                <i class="bx bx-plus"></i> Generate Payroll
                                                            </button>
                                                       @endif
                                                       
                                                       @if(Session::get('IS_SUPER_ADMIN') || $Allow_Post_UnPost_Approve_UnApprove==1)        
                                                            <button id="btnApprove" type="button" class="btn btn-icon btn-outline-primary mr-1" onclick="ApprovePayrollTransaction()" style="margin:2px !important; display: none;">Approve  </button>
                                                       @endif
                                                             
                                                       @if(Session::get('IS_SUPER_ADMIN') || $Allow_Delete_Cancel==1)        
                                                            <button id="btnCancel" type="button" class="btn btn-icon btn-outline-primary mr-1" onclick="CancelPayrollTransaction()" style="margin:2px !important; display: none;">Cancel 
                                                            </button>
                                                       @endif    
                                                    </div>
                                                </fieldset>
                                                <fieldset>
                                                    <div id="divSearchFilter" class="input-group" style="margin-top:5px;">
                                                        <input id="SearchEmployee" type="text" class="form-control searchtext" placeholder="Search Employee Here.."  >
                                                        <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border">
                                                            <i class="bx bx-search"></i>
                                                        </button>
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
                                                        <th>EMPLOYEE ID</th>
                                                        <th>EMPLOYEE NAME</th>
                                                        <th>LOCATION</th>
                                                        <th>DIVISION</th>
                                                        <th>DEPARTMENT</th>
                                                        <th>SECTION</th>
                                                        <th>JOB TITLE</th>
                                                        <th><span  class="float_right">MIN. PAY</span></th>
                                                        <th>SALARY TYPE</th>
                                                        <th><span  class="float_right">MONTHLY RATE</span></th>
                                                        <th><span  class="float_right">HOURLY RATE</span></th>
                                                        <th><span  class="float_right">BASIC SALARY</span></th>
                                                        <th><span  class="float_right">OTHER TAXABLE INCOME</span></th>
                                                        <th><span  class="float_right">NON TAXABLE INCOME</span></th>
                                                        <th><span  class="float_right">INSURANCE PREMIUMS</span></th>
                                                        <th><span  class="float_right">OTHER DEDUCTION</span></th>
                                                        <th><span  class="float_right">NET PAY</span></th>
                                                        <th><span  class="float_right">STATUS</span></th>
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
    <div id="generate-payroll-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Generate Payroll</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label for="GeneratePayrollType">Type: </label>
                                    <select id="GeneratePayrollType" class="form-control">
                                        <option value="{{ config('app.GENERATE_PAYROLL_BATCH') }}" selected>{{ config('app.GENERATE_PAYROLL_BATCH') }}</option>
                                        {{-- <option value="{{ config('app.GENERATE_PAYROLL_FINAL') }}" selected>{{ config('app.GENERATE_PAYROLL_FINAL') }}</option> --}}
                                    </select>
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <fieldset class="form-group">
                                <label for="GeneratePayrollPeriodCode">Payroll Period: <span class="required_field">* </span></label><span class="search-txt">(Select from the list)</span>
                                <div class="div-percent">
                                    <select id="GeneratePayrollPeriodCode" class="form-control">
                                        <option value="">Please Select</option>
                                        @foreach($PayrollPeriodList as $prdrow)
                                        <option value="{{ $prdrow->ID }}" data-datefrom="{{ $prdrow->StartDateFormat }}" data-dateto="{{ $prdrow->EndDateFormat }}">{{ $prdrow->Code.' : '.$prdrow->StartDateFormat.' - '.$prdrow->EndDateFormat }}</option>
                                        @endforeach
                                    </select>
                                </div> 
                            </fieldset>
                        </div>
                        <div class="col-md-3">
                            <fieldset class="form-group">
                                <label for="GeneratePeriodDateStart">Date Start: </label>                     
                                <input id="GeneratePeriodDateStart" type="text" class="form-control" placeholder="Date Start" readonly>
                            </fieldset>
                        </div>
                        <div class="col-md-4">
                            <fieldset class="form-group">
                                <label for="GeneratePeriodDateEnd">Date End: </label>                                            
                                <input id="GeneratePeriodDateEnd" type="text" class="form-control" placeholder="Date End" readonly>
                            </fieldset>
                        </div>
                    </div>

                    <div id="divBatchPayroll">
                        <div class="row" style="width:100%;">
                            <div class="col-md-5">
                                <fieldset class="form-group">
                                    <label for="GenerateFilter">Filter: </label>
                                        <select id="GenerateFilter" class="form-control">
                                            <option value="">All</option>
                                            <option value="Location">Location</option>
                                            <option value="Division">Division</option>
                                            <option value="Department">Department</option>
                                            <option value="Section">Section</option>
                                            <option value="Job Type">Job Type</option>
                                            <option value="Employee">Employee</option>
                                        </select>
                                </fieldset>
                            </div>
                            <div id="divFilters" class="col-md-7" style="display: none;">
                                <fieldset class="form-group">
                                    <label id="GeneratePayrollFilterLabel">Location: <span class="required_field">* </span></label><span class="search-txt">(Type & search from the list)</span>
                                    <div id="divLocation" class="div-percent">
                                        <select id="GeneratePayrollBranch" class="form-control">
                                            <option value="">Please Select</option>
                                            @foreach($BranchList as $brow)
                                            <option value="{{ $brow->ID }}">{{ $brow->BranchName }}</option>
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
                        </div>
                    </div>
                    <div id="divFinalPayroll" style="display:none;">
                        <div class="row" style="width:100%;">
                            <div class="col-md-12">
                                <fieldset class="form-group">
                                    <label for="GeneratePayrollEmployee">Employee : <span class="required_field">* </span></label><span class="search-txt">(Type & search from the list)</span>
                                    <div class="div-percent">
                                       <input id="GeneratePayrollFinalEmployeeID" type="hidden" value="0">
                                       <input id="GeneratePayrollFinalEmployee" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-type="generate-final-pay" placeholder="Employee"><span class='percent-sign'> <i class="bx bx-search" style="line-height: 21px;"></i> </span>
                                    </div> 
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div id="divGenerateNotes" class="row" style="display:none;">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <span style="color:red;">Note : Generated payroll previously will be deleted.</span>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnSaveRecord" type="button" class="btn btn-primary ml-1" onclick="doGeneratePayroll(1)">
                      <i class="bx bx-check d-block d-sm-none"></i>
                      <span class="d-none d-sm-block">Generate</span>
                   </button>
                   <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-left:10px;">
                      <i class="bx bx-x d-block d-sm-none"></i>
                       <span class="d-none d-sm-block">Cancel</span>
                   </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

    <!-- MODAL -->
    <div id="view-payroll-transaction-employee-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Employee Payroll Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">

                    <input type="hidden" id="EmpPayrollTransactionRowID" value="0" readonly>
                    <input type="hidden" id="EmpPayrollTransactionID" value="0" readonly>
                    <input type="hidden" id="EmpPayrollTransPayrollPeriodID" value="0" readonly>
                    <input type="hidden" id="EmpPayrollTransDetailsID" value="0" readonly>
               
                    <div class="col-md-12">
                        <fieldset class="fieldset-border">
                             <legend class="legend-text">| Employee Information |</legend>
                     
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label for="EmpPayrollTransEmployeeNo">Employee No.: </label>                     
                                        <input id="EmpPayrollTransEmployeeNo" type="text" class="form-control" placeholder="Employee No." readonly>
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label for="EmpPayrollTransStatus">Status: </label>                     
                                        <input id="EmpPayrollTransStatus" type="text" class="form-control" placeholder="Status" readonly>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <fieldset class="form-group">
                                        <label for="EmpPayrollTransFirstName">First Name: </label>                     
                                        <input type="hidden" id="EmpPayrollTransEmployeeID" value="0" readonly>
                                        <input id="EmpPayrollTransFirstName" type="text" class="form-control" placeholder="First Name" readonly>
                                    </fieldset>
                                </div>
                                <div class="col-md-4">
                                    <fieldset class="form-group">
                                        <label for="EmpPayrollTransMiddleName">Middle Name: </label>                     
                                        <input id="EmpPayrollTransMiddleName" type="text" class="form-control" placeholder="Middle Name" readonly>
                                    </fieldset>
                                </div>
                                <div class="col-md-4">
                                    <fieldset class="form-group">
                                        <label for="EmpPayrollTransLastName">Last Name: </label>                     
                                        <input id="EmpPayrollTransLastName" type="text" class="form-control" placeholder="Last Name" readonly>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label for="EmpPayrollTransContactNumber">Contact No.: </label>                     
                                        <input id="EmpPayrollTransContactNumber" type="text" class="form-control" placeholder="Contact No." readonly>
                                    </fieldset>
                                </div>
                                <div class="col-md-6">
                                    <fieldset class="form-group">
                                        <label for="EmpPayrollTransEmailAddress">Email Address: </label>                     
                                        <input id="EmpPayrollTransEmailAddress" type="text" class="form-control" placeholder="Email Address" readonly>
                                    </fieldset>
                                </div>
                            </div>

                        </fieldset>
                    </div>
                 
                <div class="col-md-12">
                        <fieldset class="fieldset-border">
                             <legend class="legend-text">| Other Information |</legend>
                                <div class="row">
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="EmpPayrollTransDivision">Division: </label>                     
                                            <input id="EmpPayrollTransDivision" type="text" class="form-control" placeholder="Division" readonly>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="EmpPayrollTransDepartment">Department: </label>                     
                                            <input id="EmpPayrollTransDepartment" type="text" class="form-control" placeholder="Department" readonly>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="EmpPayrollTransSection">Section: </label>                     
                                            <input id="EmpPayrollTransSection" type="text" class="form-control" placeholder="Section" readonly>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12">
                                        <fieldset class="form-group">
                                            <label for="EmpPayrollTransJobTitle">Job Title: </label>                     
                                            <input id="EmpPayrollTransJobTitle" type="text" class="form-control" placeholder="Job Title" readonly>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="EmpPayrollTransSalaryType">Salary Type: </label>                     
                                            <input id="EmpPayrollTransSalaryType" type="text" class="form-control" placeholder="Salary Type" readonly>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="EmpPayrollTransMonthlyRate">Monthly Rate: </label>                     
                                            <input id="EmpPayrollTransMonthlyRate" type="text" class="form-control" placeholder="Monthly Rate" readonly>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="EmpPayrollTransHourlyRate">Hourly Rate: </label>                     
                                            <input id="EmpPayrollTransHourlyRate" type="text" class="form-control" placeholder="Hourly Rate" readonly>
                                        </fieldset>
                                    </div>
                                </div>

                        </fieldset>
                    </div>
                 
                 <hr>

                <!--TAB  -->
                 <div class="row">
                        <div class="col-md-12">
                          <nav>
                                 <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist" style="border-top-left-radius: 32px; border-top-right-radius: 32px;">
                                      <a class="nav-item nav-link" id="nav-summary-tab" data-toggle="tab" href="#nav-summary" role="tab" aria-controls="nav-summary" aria-selected="true" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"><i class='bx bx-detail mr-1'></i> Summary</a>
                                      <a class="nav-item nav-link" id="nav-income-tab" data-toggle="tab" href="#nav-income" role="tab" aria-controls="nav-income" aria-selected="false" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"> <i class='bx bx-money mr-1'></i>Income</a>
                                      <a class="nav-item nav-link" id="nav-ot-tab" data-toggle="tab" href="#nav-ot" role="tab" aria-controls="nav-ot" aria-selected="false" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"> <i class='bx bx-time mr-1'></i> Over Time</a>
                                      <a class="nav-item nav-link" id="nav-leave-tab" data-toggle="tab" href="#nav-leave" role="tab" aria-controls="nav-leave" aria-selected="false" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"><i class='bx bx-car mr-1'></i> Leave </a>
                                      <a class="nav-item nav-link" id="nav-deduction-tab" data-toggle="tab" href="#nav-deduction" role="tab" aria-controls="nav-deduction" aria-selected="false" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"><i class='bx bx-calculator mr-1'></i> Deduction </a>
                                      <a class="nav-item nav-link" id="nav-loan-tab" data-toggle="tab" href="#nav-loan" role="tab" aria-controls="nav-loan" aria-selected="false" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"><i class='bx bx-edit mr-1'></i> Loan </a>
                                </div>
                          </nav>
                          <div class="tab-content" id="nav-tabContent">
                         
                            <div class="tab-pane fade show" id="nav-summary" role="tabpanel" aria-labelledby="nav-summary-tab">
                                 <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31);padding-bottom: 15px;padding-top: 15px;">
                                        <div id="tblList_wrapper" class="dataTables_wrapper no-footer">   
                                 <!-- SUMMARY --> 
                                    <div id="divPayrollSummary" class="row">
                                        <div class="col-md-6">
                                            <fieldset class="form-group">
                                                <label style="font-size: 20px;">Earnings</label>                     
                                            </fieldset>

                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals1_width mx3-disabled-background-color">Basic Salary</span>
                                                        </div>
                                                        <input id="EmpPayrollTransBasicSalaryQty" type="text" class="form-control align-right" placeholder="HRS" aria-describedby="basic-addon1" readonly>
                                                        <input id="EmpPayrollTransBasicSalary" type="text" class="form-control align-right" placeholder="Basic Salary" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals1_width mx3-disabled-background-color">Total Night Differential</span>
                                                        </div>
                                                        <input id="EmpPayrollTransNDQty" type="text" class="form-control align-right" placeholder="HRS" aria-describedby="basic-addon1" readonly>
                                                        <input id="EmpPayrollTransND" type="text" class="form-control align-right" placeholder="Night Differential" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  

                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals1_width mx3-disabled-background-color">Total Overtime</span>
                                                        </div>
                                                        <input id="EmpPayrollTransOvertime" type="text" class="form-control align-right" placeholder="Overtime" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  
                                            
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals1_width mx3-disabled-background-color">Total Leave</span>
                                                        </div>
                                                        <input id="EmpPayrollTransLeave" type="text" class="form-control align-right" placeholder="Leave" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>   

                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals1_width mx3-disabled-background-color">Other Taxable Earning</span>
                                                        </div>
                                                        <input id="EmpPayrollTransOtherTaxableEarning" type="text" class="form-control align-right" placeholder="Other Taxable Earning" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  

                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals1_width mx3-disabled-background-color">Non Taxable Earning</span>
                                                        </div>
                                                        <input id="EmpPayrollTransNonTaxableEarning" type="text" class="form-control align-right" placeholder="Non Taxable Earning" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  
                                        </div>

                                        <div class="col-md-6">
                                            <fieldset class="form-group">
                                                <label style="font-size: 20px;">Deductions</label>                     
                                            </fieldset>

                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Absent</span>
                                                        </div>
                                                        <input id="EmpPayrollTransAbsentQty" type="text" class="form-control align-right" placeholder="HRS" aria-describedby="basic-addon1" readonly>
                                                        <input id="EmpPayrollTransAbsent" type="text" class="form-control align-right" placeholder="Absent" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Late</span>
                                                        </div>
                                                        <input id="EmpPayrollTransLateQty" type="text" class="form-control align-right" placeholder="HRS" aria-describedby="basic-addon1" readonly>
                                                        <input id="EmpPayrollTransLate" type="text" class="form-control align-right" placeholder="Late" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Undertime</span>
                                                        </div>
                                                        <input id="EmpPayrollTransUndertimeQty" type="text" class="form-control align-right" placeholder="HRS" aria-describedby="basic-addon1" readonly>
                                                        <input id="EmpPayrollTransUndertime" type="text" class="form-control align-right" placeholder="Undertime" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>

                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">SSS Contribution</span>
                                                        </div>
                                                        <input id="EmpPayrollTransSSSContributionER" type="text" class="form-control align-right" placeholder="Employer" aria-describedby="basic-addon1" readonly>
                                                        <input id="EmpPayrollTransSSSContributionECER" type="text" class="form-control align-right" placeholder="EC ER" aria-describedby="basic-addon1" readonly>
                                                        <input id="EmpPayrollTransSSSContributionEE" type="text" class="form-control align-right" placeholder="Employee" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">PHIC Contribution</span>
                                                        </div>
                                                        <input id="EmpPayrollTransPHICContributionER" type="text" class="form-control align-right" placeholder="Employer" aria-describedby="basic-addon1" readonly>
                                                        <input id="EmpPayrollTransPHICContributionEE" type="text" class="form-control align-right" placeholder="Employee" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">HDMF Contribution</span>
                                                        </div>
                                                        <input id="EmpPayrollTransHDMFContributionER" type="text" class="form-control align-right" placeholder="Employer" aria-describedby="basic-addon1" readonly>
                                                        <input id="EmpPayrollTransHDMFMP2" type="text" class="form-control align-right" placeholder="Employee MP2" aria-describedby="basic-addon1" readonly>
                                                        <input id="EmpPayrollTransHDMFContributionEE" type="text" class="form-control align-right" placeholder="Employee" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Withholding Tax</span>
                                                        </div>
                                                        <input id="EmpPayrollTransWTax" type="text" class="form-control align-right" placeholder="Withholding Tax" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Loan</span>
                                                        </div>
                                                        <input id="EmpPayrollTransLoan" type="text" class="form-control align-right" placeholder="Loan" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Other Deduction</span>
                                                        </div>
                                                        <input id="EmpPayrollTransOtherDeduction" type="text" class="form-control align-right" placeholder="Other Deduction" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  

                                        </div>
                                        <div class="col-md-12">
                                            <fieldset class="form-group">
                                                <label style="font-size: 20px; margin-top:5px; margin-bottom:5px;">Net Pay</label>                     
                                            </fieldset>

                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Net Pay</span>
                                                        </div>
                                                        <input id="EmpPayrollTransNetPay" type="text" class="form-control align-right" placeholder="Net Pay" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  

                                          </div>
                                       </div>  
                                    </div>
                                </div>                                 
                            </div>

                            <div class="tab-pane fade" id="nav-income" role="tabpanel" aria-labelledby="nav-income-tab">     
                                  <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31);padding-bottom: 15px;padding-top: 15px;">
                                        <div id="tblList_wrapper" class="dataTables_wrapper no-footer">   
                                      <!-- INCOME --> 
                                        <div id="divIncome" class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label style="font-size: 20px;">Income</label>                     
                                                </fieldset>
                                            </div>
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <table id="tblEmpPayrollTransIncomeList" class="table zero-configuration complex-headers border">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>Reference</th>
                                                            <th>Taxable</th>
                                                            <th><span  class="float_right">Amount</span></th>
                                                       </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>                            
                                            </div>  
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Taxable</span>
                                                        </div>
                                                        <input id="EmpPayrollTransTotalIncomeTaxable" type="text" class="form-control align-right" placeholder="Total Income" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Non Taxable</span>
                                                        </div>
                                                        <input id="EmpPayrollTransTotalIncomeNonTaxable" type="text" class="form-control align-right" placeholder="Total Income" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  
                                        </div>   
                                          <!-- END INCOME -->                  
                                   </div>
                               </div>
                            </div>

                             <div class="tab-pane fade" id="nav-ot" role="tabpanel" aria-labelledby="nav-ot-tab">      
                                   <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31);padding-bottom: 15px;padding-top: 15px;">
                                        <div id="tblList_wrapper" class="dataTables_wrapper no-footer">   
                                       <!-- OVER TIME--> 
                                        <div id="divOvertime" class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label style="font-size: 20px;">Overtime</label>                     
                                                </fieldset>
                                            </div>
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <table id="tblEmpPayrollTransOvertimeList" class="table zero-configuration complex-headers border">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>Reference</th>
                                                            <th>Taxable</th>
                                                            <th><span  class="float_right">Amount</span></th>
                                                       </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>                            
                                            </div>  
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Overtime</span>
                                                        </div>
                                                        <input id="EmpPayrollTransTotalOvertime" type="text" class="form-control align-right" placeholder="Total Overtime" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  
                                        </div> 
                                        <!-- END OVER TIME-->               
                                   </div>                                    
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-leave" role="tabpanel" aria-labelledby="nav-leave-tab">   
                               <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31);padding-bottom: 15px;padding-top: 15px;">
                                  <div id="tblList_wrapper" class="dataTables_wrapper no-footer">   
                                 <!-- LEAVE --> 
                                    <div id="divLeave" class="row">
                                        <div class="col-md-12">
                                            <fieldset class="form-group">
                                                <label style="font-size: 20px;">Leave</label>                     
                                            </fieldset>
                                        </div>
                                        <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                            <table id="tblEmpPayrollTransLeaveList" class="table zero-configuration complex-headers border">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Reference</th>
                                                        <th>Hours</th>
                                                        <th><span  class="float_right">Amount</span></th>
                                                   </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>                            
                                        </div>  
                                        <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                            <fieldset>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Leave</span>
                                                    </div>
                                                    <input id="EmpPayrollTransTotalLeave" type="text" class="form-control align-right" placeholder="Total Leave" aria-describedby="basic-addon1" readonly>
                                                </div>
                                            </fieldset>
                                        </div>  
                                    </div>  
                                    <!-- END LEAVE --> 
                                  </div>
                               </div>    
                            </div>

                            <div class="tab-pane fade" id="nav-deduction" role="tabpanel" aria-labelledby="nav-deduction-tab">    
                              <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31);padding-bottom: 15px;padding-top: 15px;">
                                  <div id="tblList_wrapper" class="dataTables_wrapper no-footer">         
                                  <!-- DEDUCTION --> 
                                   <div id="divDeduction" class="row">
                                            <div class="col-md-12">
                                                <fieldset class="form-group">
                                                    <label style="font-size: 20px;">Deduction</label>                     
                                                </fieldset>
                                            </div>
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <table id="tblEmpPayrollTransDeductionList" class="table zero-configuration complex-headers border">
                                                    <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>Reference</th>
                                                            <th><span  class="float_right">Amount</span></th>
                                                       </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>                            
                                            </div>  
                                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Deduction</span>
                                                        </div>
                                                        <input id="EmpPayrollTransTotalDeduction" type="text" class="form-control align-right" placeholder="Total Deduction" aria-describedby="basic-addon1" readonly>
                                                    </div>
                                                </fieldset>
                                            </div>  
                                        </div>                     
                                    <!-- END -->                                               
                                  </div>
                                </div>    
                            </div>

                           <div class="tab-pane fade" id="nav-loan" role="tabpanel" aria-labelledby="nav-loan-tab"> 
                               <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31);padding-bottom: 15px;padding-top: 15px;">
                                  <div id="tblList_wrapper" class="dataTables_wrapper no-footer">   
                                 <!-- LOAN --> 
                                <div id="divLoan" class="row">
                                    <div class="col-md-12">
                                        <fieldset class="form-group">
                                            <label style="font-size: 20px;">Loan</label>                     
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                        <table id="tblEmpPayrollTransLoanList" class="table zero-configuration complex-headers border">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Reference</th>
                                                    <th><span  class="float_right">Amount</span></th>
                                               </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>                            
                                    </div>  
                                    <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                        <fieldset>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Loan</span>
                                                </div>
                                                <input id="EmpPayrollTransTotalLoan" type="text" class="form-control align-right" placeholder="Total Loan" aria-describedby="basic-addon1" readonly>
                                            </div>
                                        </fieldset>
                                    </div>  
                                </div> 
                                 <!-- END LOAN --> 
                              </div>
                             </div> 
                          </div>   

                          </div>
                        </div>
                    </div>
                    <!-- End Tab  -->  
                                      
                </div>
                <div class="modal-footer">
                    <button id="btnRegeneratePayroll" type="button" class="btn btn-primary ml-1" style="display:none;" onclick="RegenerateEmployeePayroll('Pending',1)">
                      <i class="bx bx-check d-block d-sm-none"></i>
                      <span class="d-none d-sm-block">Recompute Payroll</span>
                   </button>
                   <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-left:10px;">
                      <i class="bx bx-x d-block d-sm-none"></i>
                       <span class="d-none d-sm-block">Close</span>
                   </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

    <!-- MODAL -->
    <div id="approve-payroll-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: green;">
                    <h5 class="modal-title white-color">Approve Generated Payroll</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="background:#fff !important;color: green;">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <span>This will approve the generated payroll and make this as final. Would you like to proceed?</span>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnApprovePayrollProceed" type="button" class="btn btn-primary ml-1" onclick="doApprovePayrollTransaction()" style="background: green !important;">
                      <i class="bx bx-check d-block d-sm-none"></i>
                      <span class="d-none d-sm-block">Proceed</span>
                   </button>
                   <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-left:10px;">
                      <i class="bx bx-x d-block d-sm-none"></i>
                       <span class="d-none d-sm-block">Cancel</span>
                   </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

    <!-- MODAL -->
    <div id="cancel-payroll-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: red;">
                    <h5 class="modal-title white-color">Cancel Generated Payroll</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="background:#fff !important;color: red;">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <span>Are you sure you want to cancel this generated payroll?</span>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnCancelPayrollProceed" type="button" class="btn btn-primary ml-1" onclick="doCancelPayrollTransaction()" style="background: red !important;">
                      <i class="bx bx-check d-block d-sm-none"></i>
                      <span class="d-none d-sm-block">Proceed</span>
                   </button>
                   <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-left:10px;">
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
                "order": [[ 2, "asc" ]]
            });

            $('#tblEmpPayrollTransDeductionList').DataTable( {
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

            $('#tblEmpPayrollTransIncomeList').DataTable( {
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

            $('#tblEmpPayrollTransLeaveList').DataTable( {
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

            $('#tblEmpPayrollTransLoanList').DataTable( {
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
            
            $('#tblEmpPayrollTransOvertimeList').DataTable( {
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

            isPageFirstLoad = false;

            $("#tblList").DataTable().clear().draw();
            getRecordList(intCurrentPage);

             //SET FULL ROW HIGHLIGHT        
            var tblList = $('#tblList').DataTable();        
            $('#tblList tbody').on('click', 'tr', function() {            
                tblList.$('tr.highlighted').removeClass('highlighted');        
                $(this).addClass('highlighted');
            });

            $("#record-modal").modal({
                show: false,
                backdrop: 'static'
            });

            $("#nav-summary-tab").click();

        });

        $("#SearchPayrollPeriodCode").change(function(){

            $("#tblList").DataTable().clear().draw();
            intCurrentPage = 1;
            getRecordList(intCurrentPage);

        });

        $("#btnSearch").click(function(){
            $("#tblList").DataTable().clear().draw();
            intCurrentPage = 1;
            getRecordList(intCurrentPage);
        });

        $('.searchtext').on('keypress', function (e) {
            if(e.which === 13){
                $("#tblList").DataTable().clear().draw();
                intCurrentPage = 1;
                getRecordList(intCurrentPage);
            }
        });

        function getRecordList(vPageNo){

            if($("#SearchPayrollPeriodCode").val() == "" || $("#SearchPayrollPeriodCode").val() == "0"){
                showHasErrorMessage('Payroll Period','Unable to identify payroll period.');
            }else{

              vLimit=50;  

              $("#tblList").DataTable().clear().draw();
              $(".paginate_button").remove(); 

                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollPeriodID: $("#SearchPayrollPeriodCode").val(),
                        SearchText: $("#SearchEmployee").val(),
                        Status: "",
                        Limit: vLimit,
                        PageNo: vPageNo
                    },
                    url: "{{ route('get-payroll-transaction-employee-list-by-period') }}",
                    dataType: "json",
                    success: function(data){

                        total_rec=data.TotalRecord;
                        
                        LoadRecordList(data.PayrollTransactionEmployeeList);

                        if(total_rec>0){
                             CreatePaging(total_rec,vLimit);  

                             if(total_rec>vLimit){
                                
                                $("#divPaging").show(); 
                                $("#total-record").text(total_rec);
                                $("#paging_button_id"+vPageNo).css("background", "#0069d9");
                                $("#paging_button_id"+vPageNo).css("color", "#fff");
                             }
                          }

                        $("#btnApprove").hide();
                        if(data.IsHasPendingTransactions > 0){
                            $("#btnApprove").show();
                        }

                        $("#btnCancel").hide();
                        if(data.PayrollTransactionEmployeeList.length > 0){
                            $("#btnCancel").show();
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
            }
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
        paging_button="<li class='paginate_button page-item next' id='example2_next'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getRecordList("+pages+")'>Last</a></li>"
        $(".ul-paging").append(paging_button);
        }
      
   }
  

        function LoadRecordList(vList){

            if(vList.length > 0){
                for(var x=0; x < vList.length; x++){
                    LoadRecordRow(0, vList[x]);
                }
            }

        }

        function LoadRecordRow(vRow, vData){

            var tblList = $("#tblList").DataTable();
                                                        
            tdID = vData.ID;

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
               

                        tdAction += "<a class='dropdown-item' href='javascript:void(0);' onclick='ViewPayrollDetails(" + vData.ID + "," + vData.PayrollTransactionID + "," + vData.EmployeeID + ",\"" + vData.Status + "\")'>"+
                                    " <i class='bx bx-search-alt mr-1'></i> View Payroll Details" +
                                "</a>" +
                            "</div> " +
                        "</div>";
             }
             
            tdEmployeeNo = "<span class='font-normal'>" + vData.EmployeeNo + "</span>";
            tdEmployeeName = "<span class='font-normal'>" + vData.FullName + "</span>";

            tdBranchName = "<span class='font-normal'>" + vData.BranchName + "</span>";
            tdDivision = "<span class='font-normal'>" + vData.Division + "</span>";
            tdDepartment = "<span class='font-normal'>" + vData.Department + "</span>";
            tdSection = "<span class='font-normal'>" + vData.Section + "</span>";
            tdJobTitle = "<span class='font-normal'>" + vData.JobTitle + "</span>";

            tdMinTakeHomePay = "<span class='font-normal'>" + FormatDecimal(vData.MinTakeHomePay,2) + "</span>";

            tdSalaryType = "<span class='font-normal'>" + (vData.SalaryType == 1 ? "Daily" : "Monthly") + "</span>";
            tdMonthlyRate = "<span class='font-normal float_right'>" + FormatDecimal(vData.MonthlyRate,2) + "</span>";
            tdHourlyRate = "<span class='font-normal float_right'>" + FormatDecimal(vData.HourlyRate,2) + "</span>";

            tdBasicSalary = "<span class='font-normal float_right'>" + FormatDecimal(vData.BasicSalary,2) + "</span>";
            tdTotalOtherTaxableIncome = "<span class='font-normal float_right'>" + FormatDecimal(vData.TotalOtherTaxableIncome,2) + "</span>";
            tdTotalNonTaxableIncome = "<span class='font-normal float_right'>" + FormatDecimal(vData.TotalNonTaxableIncome,2) + "</span>";
            tdTotalEEInsurancePremiums = "<span class='font-normal float_right'>" + FormatDecimal(vData.TotalEEInsurancePremiums,2) + "</span>";
            tdTotalOtherDeductions = "<span class='font-normal float_right'>" + FormatDecimal(vData.TotalOtherDeductions,2) + "</span>";
            tdNetPay = "<span class='font-normal float_right'>" + FormatDecimal(vData.NetPay,2) + "</span>";

            tdStatus = "";
            if(vData.Status == "Approved"){
                tdStatus = "<span style='color:green;display:flex;'> <i class='bx bx-check-circle'></i> Posted </span>";
            }else if(vData.Status == "Pending"){
                tdStatus = "<span style='color:red;display:flex;'> Un-Posted </span>";
            }else if(vData.Status == "Cancelled"){
                tdStatus = "<span style='color:#f68c1f;display:flex;'>Cancelled </span>";
            }     
            
            //Check if record already listed
            var IsRecordExist = false;
            tblList.rows().every(function ( rowIdx, tableLoop, rowLoop ) {
                var rowData = this.data();
                if(rowData[0] == (vRow != "0" ? vRow : vData.ID)){

                    IsRecordExist = true;
                    //Edit Row
                    curData = tblList.row(rowIdx).data();
                    curData[0] = tdID;
                    curData[1] = tdAction;
                    curData[2] = tdEmployeeNo;
                    curData[3] = tdEmployeeName;
                    curData[4] = tdBranchName;
                    curData[5] = tdDivision;
                    curData[6] = tdDepartment;
                    curData[7] = tdSection;
                    curData[8] = tdJobTitle;
                    curData[9] = tdMinTakeHomePay;
                    curData[10] = tdSalaryType;
                    curData[11] = tdMonthlyRate;
                    curData[12] = tdHourlyRate;
                    curData[13] = tdBasicSalary;
                    curData[14] = tdTotalOtherTaxableIncome;
                    curData[15] = tdTotalNonTaxableIncome;
                    curData[16] = tdTotalEEInsurancePremiums;
                    curData[17] = tdTotalOtherDeductions;
                    curData[18] = tdNetPay;
                    curData[19] = tdStatus;

                    this.data(curData).invalidate().draw();
                }
            });

            if(!IsRecordExist){

                //New Row
                tblList.row.add([
                        tdID,
                        tdAction,
                        tdEmployeeNo,
                        tdEmployeeName,
                        tdBranchName,
                        tdDivision,
                        tdDepartment,
                        tdSection,
                        tdJobTitle,
                        tdMinTakeHomePay,
                        tdSalaryType,
                        tdMonthlyRate,
                        tdHourlyRate,
                        tdBasicSalary,
                        tdTotalOtherTaxableIncome,
                        tdTotalNonTaxableIncome,
                        tdTotalEEInsurancePremiums,
                        tdTotalOtherDeductions,
                        tdNetPay,
                        tdStatus
                    ]).draw();          
            }

        }

        function GeneratePayrollRecord(){

            $("#divGenerateNotes").hide();

            $("#GeneratePayrollType").val('{{ config('app.GENERATE_PAYROLL_BATCH') }}').change();
            $("#divBatchPayroll").show();

            $("#GeneratePayrollBranch").val('').change();
            $("#GeneratePayrollDivision").val('').change();
            $("#GeneratePayrollDepartment").val('').change();
            $("#GeneratePayrollSection").val('').change();
            $("#GeneratePayrollJobType").val('').change();
            $("#GeneratePayrollEmployee").val('').change();

            $("#GeneratePeriodDateStart").val("");
            $("#GeneratePeriodDateEnd").val("");
            $("#GeneratePayrollPeriodCode").val($("#SearchPayrollPeriodCode").val()).change();

            $("#divFinalPayroll").hide();
            $("#GeneratePayrollFinalEmployeeID").val(0);
            $("#GeneratePayrollFinalEmployee").val('');

            $("#generate-payroll-modal").modal();

        }

        $("#GeneratePayrollType").change(function(){

            $("#divBatchPayroll").hide();
            $("#divFinalPayroll").hide();
            if($("#GeneratePayrollType").val() == "{{ config('app.GENERATE_PAYROLL_BATCH') }}"){
                $("#divBatchPayroll").show();
            }else{
                $("#divFinalPayroll").show();
            }

        });

        $("#GenerateFilter").change(function(){

            $("#divFilters").hide();
            $("#divLocation").hide();
            $("#divDivision").hide();
            $("#divDepartment").hide();
            $("#divSection").hide();
            $("#divJobType").hide();
            $("#divEmployee").hide();

            if($("#GenerateFilter").val() == "Location"){
                $("#divFilters").show();
                $("#GeneratePayrollFilterLabel").text("Location");
                $("#divLocation").show();
            }else if($("#GenerateFilter").val() == "Division"){
                $("#divFilters").show();
                $("#GeneratePayrollFilterLabel").text("Division");
                $("#divDivision").show();
            }else if($("#GenerateFilter").val() == "Department"){
                $("#divFilters").show();
                $("#GeneratePayrollFilterLabel").text("Department");
                $("#divDepartment").show();
            }else if($("#GenerateFilter").val() == "Section"){
                $("#divFilters").show();
                $("#GeneratePayrollFilterLabel").text("Section");
                $("#divSection").show();
            }else if($("#GenerateFilter").val() == "Job Type"){
                $("#divFilters").show();
                $("#GeneratePayrollFilterLabel").text("Job Type");
                $("#divJobType").show();
            }else if($("#GenerateFilter").val() == "Employee"){
                $("#divFilters").show();
                $("#GeneratePayrollFilterLabel").text("Employee");
                $("#divEmployee").show();
            }

        });

        $("#GeneratePayrollPeriodCode").change(function(){

            if($('#GeneratePayrollPeriodCode').length){
                if($("#GeneratePayrollPeriodCode").val() != ""){
                    if($("#GeneratePayrollPeriodCode").find('option:selected').data('datefrom') != undefined){
                        $("#GeneratePeriodDateStart").val($("#GeneratePayrollPeriodCode").find('option:selected').data('datefrom'));
                    }
                    if($("#GeneratePayrollPeriodCode").find('option:selected').data('dateto') != undefined){
                        $("#GeneratePeriodDateEnd").val($("#GeneratePayrollPeriodCode").find('option:selected').data('dateto'));
                    }
                }
            }

        });

        function doGeneratePayroll(vProcessNo){

            if($("#GeneratePayrollType").val() == ""){ 
                showHasErrorMessage('Payroll Type','Please select payroll type.');
            }else if($("#GeneratePayrollPeriodCode").val() == "" || $("#GeneratePayrollPeriodCode").val() == "0"){
                showHasErrorMessage('Period','Please select payroll period.');
            }else if($("#GeneratePayrollType").val() == "{{ config('app.GENERATE_PAYROLL_BATCH') }}" && $("#GenerateFilter").val() == "Location" && ($("#GeneratePayrollBranch").val() == "" || $("#GeneratePayrollBranch").val() == "0")){ 
                showHasErrorMessage('Location','Please select location.');
            }else if($("#GeneratePayrollType").val() == "{{ config('app.GENERATE_PAYROLL_BATCH') }}" && $("#GenerateFilter").val() == "Division" && ($("#GeneratePayrollDivision").val() == "" || $("#GeneratePayrollDivision").val() == "0")){ 
                showHasErrorMessage('Division','Please select division.');
            }else if($("#GeneratePayrollType").val() == "{{ config('app.GENERATE_PAYROLL_BATCH') }}" && $("#GenerateFilter").val() == "Department" && ($("#GeneratePayrollDepartment").val() == "" || $("#GeneratePayrollDepartment").val() == "0")){ 
                showHasErrorMessage('Department','Please select department.');
            }else if($("#GeneratePayrollType").val() == "{{ config('app.GENERATE_PAYROLL_BATCH') }}" && $("#GenerateFilter").val() == "Section" && ($("#GeneratePayrollSection").val() == "" || $("#GeneratePayrollSection").val() == "0")){ 
                showHasErrorMessage('Section','Please select section.');
            }else if($("#GeneratePayrollType").val() == "{{ config('app.GENERATE_PAYROLL_BATCH') }}" && $("#GenerateFilter").val() == "Job Type" && ($("#GeneratePayrollJobType").val() == "" || $("#GeneratePayrollJobType").val() == "0")){ 
                showHasErrorMessage('Job Type','Please select job type.');
            }else if($("#GeneratePayrollType").val() == "{{ config('app.GENERATE_PAYROLL_BATCH') }}" && $("#GenerateFilter").val() == "Employee" && ($("#GeneratePayrollEmployee").val() == "" || $("#GeneratePayrollEmployee").val() == "0")){ 
                showHasErrorMessage('Employee','Please select employee.');

            }else if($("#GeneratePayrollType").val() == "Final Payroll" && ($("#GeneratePayrollFinalEmployeeID").val() == "" || $("#GeneratePayrollFinalEmployeeID").val() == "0")){ 
                showHasErrorMessage('Employee','Please select employee.');
            
            }else{

                if(vProcessNo == 1){
                    $("#spnLoadingLabel").text('Processing Employee DTR (1/17) ...');   
                }else if(vProcessNo == 2){
                    $("#spnLoadingLabel").text('Processing Basic Salary (2/17) ...');   
                }else if(vProcessNo == 3){
                    $("#spnLoadingLabel").text('Processing Absences (3/17) ...');   
                }else if(vProcessNo == 4){
                    $("#spnLoadingLabel").text('Processing Late Hours (4/17) ...');   
                }else if(vProcessNo == 5){
                    $("#spnLoadingLabel").text('Processing Undertime Hours (5/17) ...');   
                }else if(vProcessNo == 6){
                    $("#spnLoadingLabel").text('Processing Night Differential (6/17) ...');   
                }else if(vProcessNo == 7){
                    $("#spnLoadingLabel").text('Processing Overtime Pay (7/17) ...');   
                }else if(vProcessNo == 8){
                    $("#spnLoadingLabel").text('Processing Leaves (8/17) ...');   
                }else if(vProcessNo == 9){
                    $("#spnLoadingLabel").text('Processing Other Income (9/17) ...');   
                }else if(vProcessNo == 10){
                    $("#spnLoadingLabel").text('Processing Allowances (10/17) ...');   
                }else if(vProcessNo == 11){
                    $("#spnLoadingLabel").text('Processing SSS Contributions (11/17) ...');   
                }else if(vProcessNo == 12){
                    $("#spnLoadingLabel").text('Processing PHIC Contributions (12/17) ...');   
                }else if(vProcessNo == 13){
                    $("#spnLoadingLabel").text('Processing HDMF Contributions (13/17) ...');   
                }else if(vProcessNo == 14){
                    $("#spnLoadingLabel").text('Processing HDMF MP2 (14/17) ...');   
                }else if(vProcessNo == 15){
                    $("#spnLoadingLabel").text('Processing Withholding Tax (15/17) ...');   
                }else if(vProcessNo == 16){
                    $("#spnLoadingLabel").text('Processing Loan Deductions (16/17) ...');   
                }else if(vProcessNo == 17){
                    $("#spnLoadingLabel").text('Processing Other Deductions (17/17) ...');   
                }

                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollType: $("#GeneratePayrollType").val(),
                        PayrollPeriodID: $("#GeneratePayrollPeriodCode").val(),
                        FilterType: $("#GenerateFilter").val(),
                        BranchID: $("#GeneratePayrollBranch").val(),
                        DivisionID: $("#GeneratePayrollDivision").val(),
                        DepartmentID: $("#GeneratePayrollDepartment").val(),
                        SectionID: $("#GeneratePayrollSection").val(),
                        JobTypeID: $("#GeneratePayrollJobType").val(),
                        EmployeeID: $("#GeneratePayrollEmployee").val(),
                        Status: '{{ config('app.STATUS_PENDING') }}',
                        ProcessNo: vProcessNo
                    },
                    url: "{{ route('do-generate-payroll') }}",
                    dataType: "json",
                    success: function(data){

                        if(data.Response =='Success'){
                            if(vProcessNo < 17){
                                doGeneratePayroll(vProcessNo + 1);
                            }else{
                                $("#divLoader").hide();
                                $("#generate-payroll-modal").modal('hide');

                                $("#tblList").DataTable().clear().draw();
                                intCurrentPage = 1;
                                getRecordList(intCurrentPage);
                            }
                        }else{
                            $("#divLoader").hide();
                            showHasErrorMessage('Generate Payroll',data.ResponseMessage);
                        }
                    },
                    error: function(data){
                        $("#divLoader").hide();
                        $("#divLoader1").hide(); 
                        $("#spnLoader1Text").hide(); 
                        console.log(data.responseText);

                    },
                    beforeSend:function(vData){
                        $("#divLoader").show();

                        $("#divLoader1").show(); 
                        $("#spnLoader1Text").show(); 
                        $("#spnLoader1Text").text("This might take a few minutes, so please don't interrupt..."); 
                        $("#spnTotalData").hide(); 
                        $("#spnTotalData").text("Payroll generation has been initiated..."); 
                    }

                });
            }
        }

        function ViewPayrollDetails(vRow, vID, vEmployeeID, vStatus){

            if(vEmployeeID > 0){
                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollTransactionID: vID,
                        EmployeeID: vEmployeeID,
                        Status: vStatus
                    },
                    url: "{{ route('get-payroll-transaction-details') }}",
                    dataType: "json",
                    success: function(data){
                        if(data.Response =='Success' && data.PayrollTransactionEmployeeInfo != undefined){
                            
                            $("#EmpPayrollTransactionRowID").val(vRow);

                            $("#EmpPayrollTransactionID").val(vID);

                            $("#EmpPayrollTransPayrollPeriodID").val(data.PayrollTransactionEmployeeInfo.PayrollPeriodID);

                            $("#EmpPayrollTransDetailsID").val(data.PayrollTransactionEmployeeInfo.ID);


                            $("#EmpPayrollTransEmployeeID").val(data.PayrollTransactionEmployeeInfo.EmployeeID);
                            $("#EmpPayrollTransFirstName").val(data.PayrollTransactionEmployeeInfo.FirstName);
                            $("#EmpPayrollTransMiddleName").val(data.PayrollTransactionEmployeeInfo.MiddleName);
                            $("#EmpPayrollTransLastName").val(data.PayrollTransactionEmployeeInfo.LastName);

                            $("#EmpPayrollTransEmployeeNo").val(data.PayrollTransactionEmployeeInfo.EmployeeNo);
                            $("#EmpPayrollTransStatus").val(data.PayrollTransactionEmployeeInfo.EmployeeStatus);
                            
                            $("#EmpPayrollTransContactNumber").val(data.PayrollTransactionEmployeeInfo.ContactNumber);
                            $("#EmpPayrollTransEmailAddress").val(data.PayrollTransactionEmployeeInfo.EmailAddress);

                            $("#EmpPayrollTransDivision").val(data.PayrollTransactionEmployeeInfo.Division);
                            $("#EmpPayrollTransDepartment").val(data.PayrollTransactionEmployeeInfo.Department);
                            $("#EmpPayrollTransSection").val(data.PayrollTransactionEmployeeInfo.Section);
                            $("#EmpPayrollTransJobTitle").val(data.PayrollTransactionEmployeeInfo.JobTitle);
                                
                            $("#EmpPayrollTransSalaryType").val((data.PayrollTransactionEmployeeInfo.SalaryType == 1 ? "Daily" : "Monthly"));
                            $("#EmpPayrollTransMonthlyRate").val(FormatDecimal(data.PayrollTransactionEmployeeInfo.MonthlyRate,2));

                            $("#EmpPayrollTransHourlyRate").val(FormatDecimal(data.PayrollTransactionEmployeeInfo.HourlyRate,2));
                            
                            var dblTotalBasicSalaryQty = 0;
                            var dblTotalBasicSalary = 0;

                            var dblTotalNDQty = 0;
                            var dblTotalND = 0;

                            var dblTotalLeave = 0;
                            var dblTotalOvertime = 0;

                            var dblTotalAbsentHoursQty = 0;
                            var dblTotalAbsentHours = 0;

                            var dblTotalLateHoursQty = 0;
                            var dblTotalLateHours = 0;

                            var dblTotalOtherTaxableIncome = 0;
                            var dblTotalOtherNonTaxableIncome = 0;

                            var dblTotalUndertimeQty = 0;
                            var dblTotalUndertime = 0;

                            var dblTotalSSSEEContribution = 0;
                            var dblTotalSSSERContribution = 0;
                            var dblTotalSSSECER = 0;

                            var dblTotalPHICEEContribution = 0;
                            var dblTotalPHICERContribution = 0;

                            var dblTotalHDMFEEContribution = 0;
                            var dblTotalHDMFMP2 = 0;
                            var dblTotalHDMFERContribution = 0;

                            var dblTotalWTax = 0;
                            var dblTotalLoan = 0;
                            var dblTotalOtherDeduction = 0;

                            $("#tblEmpPayrollTransDeductionList").DataTable().clear().draw();
                            var tblEmpPayrollTransDeductionList = $("#tblEmpPayrollTransDeductionList").DataTable();

                            $("#tblEmpPayrollTransIncomeList").DataTable().clear().draw();
                            var tblEmpPayrollTransIncomeList = $("#tblEmpPayrollTransIncomeList").DataTable();

                            $("#tblEmpPayrollTransLeaveList").DataTable().clear().draw();
                            var tblEmpPayrollTransLeaveList = $("#tblEmpPayrollTransLeaveList").DataTable();

                            $("#tblEmpPayrollTransLoanList").DataTable().clear().draw();
                            var tblEmpPayrollTransLoanList = $("#tblEmpPayrollTransLoanList").DataTable();

                            $("#tblEmpPayrollTransOvertimeList").DataTable().clear().draw();
                            var tblEmpPayrollTransOvertimeList = $("#tblEmpPayrollTransOvertimeList").DataTable();

                            if(data.PayrollTransactionDetails.length > 0){
                                for(var x=0; x < data.PayrollTransactionDetails.length; x++){

                                    if(data.PayrollTransactionDetails[x].ReferenceType == "Basic Salary"){
                                        dblTotalBasicSalaryQty = dblTotalBasicSalaryQty + parseFloat(data.PayrollTransactionDetails[x].Qty);
                                        dblTotalBasicSalary = dblTotalBasicSalary + parseFloat(data.PayrollTransactionDetails[x].Total);
                                        
                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "Night Differential"){
                                        dblTotalNDQty = dblTotalNDQty + parseFloat(data.PayrollTransactionDetails[x].Qty);
                                        dblTotalND = dblTotalND + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "Leave"){
                                        dblTotalLeave = dblTotalLeave + parseFloat(data.PayrollTransactionDetails[x].Total);

                                        //List on Table
                                        if(data.PayrollTransactionDetails[x].Total > 0){
                                            tdID = data.PayrollTransactionDetails[x].ID;
                                            tdReference = "<span class='font-normal'>" + data.PayrollTransactionDetails[x].Reference + "</span>";
                                            tdHours = "<span class='font-normal float_right'>" + FormatDecimal(data.PayrollTransactionDetails[x].Qty,2) + " H" + "</span>";
                                            tdAmount = "<span class='font-normal float_right'>" + FormatDecimal(data.PayrollTransactionDetails[x].Total,2) + "</span>";

                                            tblEmpPayrollTransLeaveList.row.add([
                                                                    tdID,
                                                                    tdReference,
                                                                    tdHours,
                                                                    tdAmount
                                                                ]).draw();
                                        }

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "Overtime"){
                                        dblTotalOvertime = dblTotalOvertime + parseFloat(data.PayrollTransactionDetails[x].Total);

                                        //List on Table
                                        if(data.PayrollTransactionDetails[x].Total > 0){
                                            tdID = data.PayrollTransactionDetails[x].ID;
                                            tdReference = "<span class='font-normal'>" + data.PayrollTransactionDetails[x].Reference, + "</span>";
                                            tdHours = "<span class='font-normal float_right'>" + FormatDecimal(data.PayrollTransactionDetails[x].Qty,2) + " H" + "</span>";
                                            tdAmount = "<span class='font-normal float_right'>" + FormatDecimal(data.PayrollTransactionDetails[x].Total,2) + "</span>";

                                            tblEmpPayrollTransOvertimeList.row.add([
                                                                    tdID,
                                                                    tdReference,
                                                                    tdHours,
                                                                    tdAmount
                                                                ]).draw();
                                        }

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "Allowance" || data.PayrollTransactionDetails[x].ReferenceType == "Income"){

                                        if(data.PayrollTransactionDetails[x].IsTaxable == 1){
                                            dblTotalOtherTaxableIncome = dblTotalOtherTaxableIncome + parseFloat(data.PayrollTransactionDetails[x].Total);
                                        }else{
                                            dblTotalOtherNonTaxableIncome = dblTotalOtherNonTaxableIncome + parseFloat(data.PayrollTransactionDetails[x].Total);
                                        }

                                        //List on Table
                                        if(data.PayrollTransactionDetails[x].Total > 0){
                                            tdID = data.PayrollTransactionDetails[x].ID;
                                            tdReference = "<span class='font-normal'>" + data.PayrollTransactionDetails[x].Reference + "</span>";
                                            tdTaxable = "<span class='font-normal'>" + (data.PayrollTransactionDetails[x].IsTaxable == 1 ? "Yes":"No") + "</span>";
                                            tdAmount = "<span class='font-normal float_right'>" + FormatDecimal(data.PayrollTransactionDetails[x].Total,2) + "</span>";

                                            tblEmpPayrollTransIncomeList.row.add([
                                                                    tdID,
                                                                    tdReference,
                                                                    tdTaxable,
                                                                    tdAmount
                                                                ]).draw();
                                        }

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "Absent"){
                                        dblTotalAbsentHoursQty = dblTotalAbsentHoursQty + parseFloat(data.PayrollTransactionDetails[x].Qty);
                                        dblTotalAbsentHours = dblTotalAbsentHours + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "Late Hours"){
                                        dblTotalLateHoursQty = dblTotalLateHoursQty + parseFloat(data.PayrollTransactionDetails[x].Qty);
                                        dblTotalLateHours = dblTotalLateHours + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "Undertime Hours"){
                                        dblTotalUndertimeQty = dblTotalUndertimeQty + parseFloat(data.PayrollTransactionDetails[x].Qty);
                                        dblTotalUndertime = dblTotalUndertime + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "SSS EE Contribution" || data.PayrollTransactionDetails[x].ReferenceType == "SSS WISP EE"){
                                        dblTotalSSSEEContribution = dblTotalSSSEEContribution + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "SSS ER Contribution" || data.PayrollTransactionDetails[x].ReferenceType == "SSS WISP ER"){
                                        dblTotalSSSERContribution = dblTotalSSSERContribution + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "SSS EC ER"){
                                        dblTotalSSSECER = dblTotalSSSECER + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "PHIC EE Contribution"){
                                        dblTotalPHICEEContribution = dblTotalPHICEEContribution + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "PHIC ER Contribution"){
                                        dblTotalPHICERContribution = dblTotalPHICERContribution + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "HDMF EE Contribution"){
                                        dblTotalHDMFEEContribution = dblTotalHDMFEEContribution + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "HDMF MP2"){
                                        dblTotalHDMFMP2 = dblTotalHDMFMP2 + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "HDMF ER Contribution"){
                                        dblTotalHDMFERContribution = dblTotalHDMFERContribution + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "Withholding Tax"){
                                        dblTotalWTax = dblTotalWTax + parseFloat(data.PayrollTransactionDetails[x].Total);

                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "Loan"){
                                        dblTotalLoan = dblTotalLoan + parseFloat(data.PayrollTransactionDetails[x].Total);

                                        //List on Table
                                        if(data.PayrollTransactionDetails[x].Total > 0){
                                            tdID = data.PayrollTransactionDetails[x].ID;
                                            tdReference = "<span class='font-normal'>" + data.PayrollTransactionDetails[x].Reference + "</span>";
                                            tdAmount = "<span class='font-normal float_right'>" + FormatDecimal(data.PayrollTransactionDetails[x].Total,2) + "</span>";

                                            tblEmpPayrollTransLoanList.row.add([
                                                                    tdID,
                                                                    tdReference,
                                                                    tdAmount
                                                                ]).draw();
                                        }
                                    }else if(data.PayrollTransactionDetails[x].ReferenceType == "Deduction"){
                                        dblTotalOtherDeduction = dblTotalOtherDeduction + parseFloat(data.PayrollTransactionDetails[x].Total);

                                        //List on Table
                                        if(data.PayrollTransactionDetails[x].Total > 0){
                                            tdID = data.PayrollTransactionDetails[x].ID;
                                            tdReference = "<span class='font-normal'>" + data.PayrollTransactionDetails[x].Reference + "</span>";
                                            tdAmount = "<span class='font-normal float_right'>" + FormatDecimal(data.PayrollTransactionDetails[x].Total,2) + "</span>";

                                            tblEmpPayrollTransDeductionList.row.add([
                                                                    tdID,
                                                                    tdReference,
                                                                    tdAmount
                                                                ]).draw();
                                        }

                                    }

                                }
                            }

                            $("#EmpPayrollTransBasicSalaryQty").val(FormatDecimal(dblTotalBasicSalaryQty,2) + " H");
                            $("#EmpPayrollTransBasicSalary").val(FormatDecimal(dblTotalBasicSalary,2));

                            $("#EmpPayrollTransNDQty").val(FormatDecimal(dblTotalNDQty,2) + " H");
                            $("#EmpPayrollTransND").val(FormatDecimal(dblTotalND,2));

                            $("#EmpPayrollTransLeave").val(FormatDecimal(dblTotalLeave,2));
                            $("#EmpPayrollTransTotalLeave").val(FormatDecimal(dblTotalLeave,2));

                            $("#EmpPayrollTransOvertime").val(FormatDecimal(dblTotalOvertime,2));
                            $("#EmpPayrollTransTotalOvertime").val(FormatDecimal(dblTotalOvertime,2));

                            $("#EmpPayrollTransOtherTaxableEarning").val(FormatDecimal(dblTotalOtherTaxableIncome,2));
                            $("#EmpPayrollTransTotalIncomeTaxable").val(FormatDecimal(dblTotalOtherTaxableIncome,2));

                            $("#EmpPayrollTransNonTaxableEarning").val(FormatDecimal(dblTotalOtherNonTaxableIncome,2));
                            $("#EmpPayrollTransTotalIncomeNonTaxable").val(FormatDecimal(dblTotalOtherNonTaxableIncome,2));

                            $("#EmpPayrollTransAbsentQty").val(FormatDecimal(dblTotalAbsentHoursQty,2) + " H");
                            $("#EmpPayrollTransAbsent").val(FormatDecimal(dblTotalAbsentHours,2));

                            $("#EmpPayrollTransLateQty").val(FormatDecimal(dblTotalLateHoursQty,2) + " H");
                            $("#EmpPayrollTransLate").val(FormatDecimal(dblTotalLateHours,2));

                            $("#EmpPayrollTransUndertimeQty").val(FormatDecimal(dblTotalUndertimeQty,2) + " H");
                            $("#EmpPayrollTransUndertime").val(FormatDecimal(dblTotalUndertime,2));

                            $("#EmpPayrollTransSSSContributionEE").val("EE : " + FormatDecimal(dblTotalSSSEEContribution,2));
                            $("#EmpPayrollTransSSSContributionER").val("ER : " + FormatDecimal(dblTotalSSSERContribution,2));
                            $("#EmpPayrollTransSSSContributionECER").val("EC ER : " + FormatDecimal(dblTotalSSSECER,2));

                            $("#EmpPayrollTransPHICContributionEE").val("EE : " + FormatDecimal(dblTotalPHICEEContribution,2));
                            $("#EmpPayrollTransPHICContributionER").val("ER : " + FormatDecimal(dblTotalPHICERContribution,2));

                            $("#EmpPayrollTransHDMFContributionEE").val("EE : " + FormatDecimal(dblTotalHDMFEEContribution,2));
                            $("#EmpPayrollTransHDMFMP2").val("MP2 : " + FormatDecimal(dblTotalHDMFMP2,2));
                            $("#EmpPayrollTransHDMFContributionER").val("ER : " +FormatDecimal(dblTotalHDMFERContribution,2));

                            $("#EmpPayrollTransWTax").val(FormatDecimal(dblTotalWTax,2));
                            
                            $("#EmpPayrollTransLoan").val(FormatDecimal(dblTotalLoan,2));
                            $("#EmpPayrollTransTotalLoan").val(FormatDecimal(dblTotalLoan,2));

                            $("#EmpPayrollTransOtherDeduction").val(FormatDecimal(dblTotalOtherDeduction,2));
                            $("#EmpPayrollTransTotalDeduction").val(FormatDecimal(dblTotalOtherDeduction,2));

                            $("#EmpPayrollTransNetPay").val(FormatDecimal(data.PayrollTransactionEmployeeInfo.NetPay,2));

                            $("#divLoader").hide();

                            $("#btnRegeneratePayroll").hide();
                            if(vStatus == "{{ config('app.STATUS_PENDING') }}"){
                                $("#btnRegeneratePayroll").show();
                            }

                            $("#view-payroll-transaction-employee-modal").modal();

                        }else{
                            $("#divLoader").hide();
                            showHasErrorMessage('Payroll Details',data.ResponseMessage);
                        }

                    },
                    error: function(data){
                        $("#divLoader").hide();
                        console.log(data.responseText);
                    },
                    beforeSend:function(vData){
                        $("#divLoader").show();
                    }

                });

            }

        }

        function SetActiveTab(vTab){

            $("#divPayrollSummary").hide();
            $("#divDeduction").hide();
            $("#divIncome").hide();
            $("#divLeave").hide();
            $("#divLoan").hide();
            $("#divOvertime").hide();

            if(vTab == "Payroll Summary"){
                $("#divPayrollSummary").show();
            }else if(vTab == "Deduction"){
                $("#divDeduction").show();
            }else if(vTab == "Income"){
                $("#divIncome").show();
            }else if(vTab == "Leave"){
                $("#divLeave").show();
            }else if(vTab == "Loan"){
                $("#divLoan").show();
            }else if(vTab == "Overtime"){
                $("#divOvertime").show();
            }
        }

        function RegenerateEmployeePayroll(vStatus, vProcessNo){
            if(vStatus == "{{ config('app.STATUS_PENDING') }}"){

                if(vProcessNo == 1){
                    $("#spnLoadingLabel").text('Processing Employee DTR ...');   
                }else if(vProcessNo == 2){
                    $("#spnLoadingLabel").text('Processing Basic Salary ...');   
                }else if(vProcessNo == 3){
                    $("#spnLoadingLabel").text('Processing Absences ...');   
                }else if(vProcessNo == 4){
                    $("#spnLoadingLabel").text('Processing Late Hours ...');   
                }else if(vProcessNo == 5){
                    $("#spnLoadingLabel").text('Processing Undertime Hours ...');   
                }else if(vProcessNo == 6){
                    $("#spnLoadingLabel").text('Processing Night Differential ...');   
                }else if(vProcessNo == 7){
                    $("#spnLoadingLabel").text('Processing Overtime Pay ...');   
                }else if(vProcessNo == 8){
                    $("#spnLoadingLabel").text('Processing Leaves ...');   
                }else if(vProcessNo == 9){
                    $("#spnLoadingLabel").text('Processing Other Income ...');   
                }else if(vProcessNo == 10){
                    $("#spnLoadingLabel").text('Processing Allowances ...');   
                }else if(vProcessNo == 11){
                    $("#spnLoadingLabel").text('Processing SSS Contributions ...');   
                }else if(vProcessNo == 12){
                    $("#spnLoadingLabel").text('Processing PHIC Contributions ...');   
                }else if(vProcessNo == 13){
                    $("#spnLoadingLabel").text('Processing HDMF Contributions ...');   
                }else if(vProcessNo == 14){
                    $("#spnLoadingLabel").text('Processing HDMF MP2 ...');   
                }else if(vProcessNo == 15){
                    $("#spnLoadingLabel").text('Processing WTax ...');   
                }else if(vProcessNo == 16){
                    $("#spnLoadingLabel").text('Processing Loan Deductions ...');   
                }else if(vProcessNo == 17){
                    $("#spnLoadingLabel").text('Processing Other Deductions ...');   
                }

                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollType: "{{ config('app.GENERATE_PAYROLL_BATCH') }}",
                        PayrollTransactionID: $("#EmpPayrollTransactionID").val(),
                        PayrollPeriodID: $("#EmpPayrollTransPayrollPeriodID").val(),
                        FilterType: 'Employee',
                        BranchID: 0,
                        DivisionID: 0,
                        DepartmentID: 0,
                        SectionID: 0,
                        JobTypeID: 0,
                        EmployeeID: $("#EmpPayrollTransEmployeeID").val(),
                        Status: vStatus,

                        ProcessNo: vProcessNo
                    },
                    url: "{{ route('do-generate-payroll') }}",
                    dataType: "json",
                    success: function(data){
                        if(data.Response =='Success' && data.PayrollTransactionInfo != undefined){


                            if(vProcessNo < 17){
                                RegenerateEmployeePayroll(vStatus,vProcessNo + 1);
                            }else{
                                LoadRecordRow($("#EmpPayrollTransactionRowID").val(),data.PayrollTransactionEmployeeInfo);

                                ViewPayrollDetails($("#EmpPayrollTransactionRowID").val(), $("#EmpPayrollTransactionID").val(), $("#EmpPayrollTransEmployeeID").val(), vStatus);

                                showHasSuccessMessage("Employee payroll has been regenerated successfully.");
                                $("#divLoader").hide();
                            }

                        }else{
                            $("#divLoader").hide();
                            showHasErrorMessage('Regenerate Payroll',data.ResponseMessage);
                        }

                    },
                    error: function(data){
                        $("#divLoader").hide();
                        $("#divLoader1").hide(); 
                        $("#spnLoader1Text").hide(); 
                        console.log(data.responseText);

                    },
                    beforeSend:function(vData){
                        $("#divLoader").show();

                        $("#divLoader1").show(); 
                        $("#spnLoader1Text").show(); 
                        $("#spnLoader1Text").text("This might take a few minutes, so please don't interrupt..."); 
                        $("#spnTotalData").hide(); 
                        $("#spnTotalData").text("Payroll regeneration has been initiated..."); 
                    }
                });

            }
        }

        function ApprovePayrollTransaction(vIsNew){
            $("#approve-payroll-modal").modal();
        }

        function doApprovePayrollTransaction(){

            if($("#SearchPayrollPeriodCode").val() == "" || $("#SearchPayrollPeriodCode").val() == "0"){
                 showHasErrorMessage('Payroll Transaction','Unable to identify payroll period.');
            }else{
                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollPeriodID: $("#SearchPayrollPeriodCode").val()
                    },
                    url: "{{ route('do-approve-generated-payroll') }}",
                    dataType: "json",
                    success: function(data){
                        if(data.Response =='Success' && data.PayrollTransactionInfo != undefined){
                            
                            $("#SearchEmployee").val('');
                            $("#SearchEmployee").prop('disabled', false);
                            $("#btnSearch").prop('disabled', false);
                            
                            $("#tblList").DataTable().clear().draw();
                            intCurrentPage = 1;
                            getRecordList(intCurrentPage);

                            $("#divLoader").hide();

                            showHasSuccessMessage("Generated payroll has been approved successfully.");

                            $("#approve-payroll-modal").modal('hide');

                        }else{
                            $("#divLoader").hide();
                            showHasErrorMessage('Payroll Transaction',data.ResponseMessage);
                        }

                        $("#spnLoadingLabel").text('Loading...');

                        $("#divLoader1").hide(); 
                        $("#spnLoader1Text").hide(); 

                    },
                    error: function(data){
                        $("#divLoader").hide();
                        $("#divLoader1").hide(); 
                        $("#spnLoader1Text").hide(); 
                        console.log(data.responseText);

                    },
                    beforeSend:function(vData){
                        $("#divLoader").show();
                        $("#spnLoadingLabel").text('Processing...');

                        $("#divLoader1").show(); 

                        $("#spnLoader1Text").show(); 
                        $("#spnLoader1Text").text('Do not interrupt while processing approve payroll data.'); 
                    }

                });

            }

        }

        function CancelPayrollTransaction(vIsNew){
            $("#cancel-payroll-modal").modal();
        }

        function doCancelPayrollTransaction(){

            if($("#SearchPayrollPeriodCode").val() == "" || $("#SearchPayrollPeriodCode").val() == "0"){
                showHasErrorMessage('Payroll Transaction','Unable to identify payroll transaction.');
            }else{
                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollPeriodID: $("#SearchPayrollPeriodCode").val()
                    },
                    url: "{{ route('do-cancel-generated-payroll') }}",
                    dataType: "json",
                    success: function(data){
                        if(data.Response =='Success'){
                            
                            $("#SearchEmployee").val('');
                            $("#SearchEmployee").prop('disabled', false);
                            $("#btnSearch").prop('disabled', false);
                            
                            $("#tblList").DataTable().clear().draw();
                            intCurrentPage = 1;
                            getRecordList(intCurrentPage);

                            $("#divLoader").hide();

                            showHasSuccessMessage("Generated payroll has been cancelled successfully.");

                            $("#cancel-payroll-modal").modal('hide');

                        }else{
                            $("#divLoader").hide();
                            showHasErrorMessage('Payroll Transaction',data.ResponseMessage);
                        }

                        $("#spnLoadingLabel").text('Loading...');

                    },
                    error: function(data){
                        $("#divLoader").hide();
                        console.log(data.responseText);
                    },
                    beforeSend:function(vData){
                        $("#divLoader").show();
                        $("#spnLoadingLabel").text('Processing...');
                    }

                });

            }

        }

        $(document).on('focus','.autocomplete_txt',function(){

            if($(this).data('type') == "generate-final-pay"){
          
                $(this).autocomplete({
                    source: function( request, response ) {
                       if(request.term.length >= 2){
                                $.ajax({
                                    method: 'post',
                                    url: 'get-employee-search-list',
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
                                             
                                            return {
                                                label: code[1] +' - '+ code[5],
                                                value: code[1] +' - '+ code[5],
                                                data : item
                                            }

                                      }));
                                    },
                                    error: function(data){
                                      console.log(data.responseText);
                                    }
                                });
                             }
                        },
                        autoFocus: true,
                        minLength: 0,
                        appendTo: "#modal-fullscreen",
                        select: function( event, ui ) {
                            
                            var seldata = ui.item.data.split("|");
                            $("#GeneratePayrollFinalEmployeeID").val(seldata[0]);
                            $("#GeneratePayrollFinalEmployee").val(seldata[1].trim() + " - " + seldata[4].trim());
                        }
                });

            }

        });


    $("#nav-summary-tab").click(function(){
        $("#nav-summary-tab").css({"background":"#f68c1f"}); 
        $("#nav-income-tab").css({"background":"#475F7B"}); 
        $("#nav-ot-tab").css({"background":"#475F7B"}); 
        $("#nav-leave-tab").css({"background":"#475F7B"});
        $("#nav-deduction-tab").css({"background":"#475F7B"});
        $("#nav-loan-tab").css({"background":"#475F7B"});
    });

    $("#nav-income-tab").click(function(){
        $("#nav-income-tab").css({"background":"#f68c1f"}); 
        $("#nav-summary-tab").css({"background":"#475F7B"}); 
        $("#nav-ot-tab").css({"background":"#475F7B"}); 
        $("#nav-leave-tab").css({"background":"#475F7B"});
        $("#nav-deduction-tab").css({"background":"#475F7B"});
        $("#nav-loan-tab").css({"background":"#475F7B"});
    });

     $("#nav-ot-tab").click(function(){
        $("#nav-ot-tab").css({"background":"#f68c1f"}); 
        $("#nav-summary-tab").css({"background":"#475F7B"}); 
        $("#nav-income-tab").css({"background":"#475F7B"}); 
        $("#nav-leave-tab").css({"background":"#475F7B"});
        $("#nav-deduction-tab").css({"background":"#475F7B"});
        $("#nav-loan-tab").css({"background":"#475F7B"});
    });

     $("#nav-leave-tab").click(function(){
        $("#nav-leave-tab").css({"background":"#f68c1f"}); 
        $("#nav-summary-tab").css({"background":"#475F7B"}); 
        $("#nav-income-tab").css({"background":"#475F7B"}); 
        $("#nav-ot-tab").css({"background":"#475F7B"});
        $("#nav-deduction-tab").css({"background":"#475F7B"});
        $("#nav-loan-tab").css({"background":"#475F7B"});
    });

     $("#nav-deduction-tab").click(function(){
        $("#nav-deduction-tab").css({"background":"#f68c1f"});
        $("#nav-summary-tab").css({"background":"#475F7B"}); 
        $("#nav-income-tab").css({"background":"#475F7B"}); 
        $("#nav-ot-tab").css({"background":"#475F7B"});
        $("#nav-leave-tab").css({"background":"#475F7B"});
        $("#nav-loan-tab").css({"background":"#475F7B"});
    });

      $("#nav-loan-tab").click(function(){
        $("#nav-loan-tab").css({"background":"#f68c1f"}); 
        $("#nav-summary-tab").css({"background":"#475F7B"}); 
        $("#nav-income-tab").css({"background":"#475F7B"}); 
        $("#nav-ot-tab").css({"background":"#475F7B"});
        $("#nav-leave-tab").css({"background":"#475F7B"});
        $("#nav-deduction-tab").css({"background":"#475F7B"});
    });

</script>


<!-- Scrolling left & right by dragging table  -->
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


@endsection



