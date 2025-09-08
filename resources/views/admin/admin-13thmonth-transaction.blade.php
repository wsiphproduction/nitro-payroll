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
                                    <li class="breadcrumb-item active">13th Month Transaction List
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
                                    <h4 class="card-title">13th Month Transaction List</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <fieldset>
                                                    <div class="input-group">
                                                        <input id="SearchPayrollTransactionID" type="hidden" value="0">
                                                        <input id="SearchPayrollTransactionStatus" type="hidden" value="">
                                                        <input id="SearchPayrollTransaction" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-type="13th-month-transaction" placeholder="Search Here..">
                                                        &nbsp;&nbsp;

                                                       @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload==1)

                                                            <button type="button" class="btn btn-icon btn-outline-primary mr-1" style="margin:2px !important;" onclick="GeneratePayrollRecord(0)">
                                                                <i class="bx bx-plus"></i> New
                                                            </button>
                                                        @endif     
                                                     
                                                        @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload==1)    
                                                            <button id="btnRegenerate" type="button" class="btn btn-icon btn-outline-primary mr-1" onclick="GeneratePayrollRecord(1)" style="margin:2px !important; display: none;">
                                                                Regenerate
                                                            </button>

                                                        @endif
                                                         
                                                         @if(Session::get('IS_SUPER_ADMIN') || $Allow_Post_UnPost_Approve_UnApprove==1)  
                                                              
                                                            <button id="btnApprove" type="button" class="btn btn-icon btn-outline-primary mr-1" onclick="ApprovePayrollTransaction()" style="margin:2px !important; display: none;">
                                                                Approve
                                                            </button>
                                                        @endif
                                                            
                                                         @if(Session::get('IS_SUPER_ADMIN') || $Allow_Delete_Cancel==1)        
                                                            <button id="btnCancel" type="button" class="btn btn-icon btn-outline-primary mr-1" onclick="CancelPayrollTransaction()" style="margin:2px !important; display: none;">
                                                                Cancel
                                                            </button>
                                                       @endif  

                                                    </div>
                                                </fieldset>
                                                <fieldset>
                                                    <div id="divSearchInfo" style="margin-top:5px; display:none;">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <fieldset class="form-group">
                                                                    <label id="SearchTransNo">Transaction No.: </label>                     
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <fieldset class="form-group">
                                                                    <label id="SearchTransDate">Transaction Date/Time: </label>                     
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <fieldset class="form-group">
                                                                    <label id="SearchStatus">Status: </label>                     
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <fieldset class="form-group">
                                                                    <label id="SearchPayrollPeriod">Period : </label>                     
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                                <fieldset>
                                                    <div id="divSearchFilter" class="input-group" style="margin-top:5px;  display:none;">
                                                        <input id="SearchEmployee" type="text" class="form-control searchtext" placeholder="Search Employee Here.."  disabled>
                                                        <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border" disabled>
                                                            <i class="bx bx-search"></i>
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
                                                        <th>Employee No.</th>
                                                        <th>Employee Name</th>
                                                        <th>Location</th>
                                                        <th>Division</th>
                                                        <th>Department</th>
                                                        <th>Section</th>
                                                        <th>Job Title</th>
                                                        <th><span  class="float_right">Total Basic Salary</span></th>
                                                        <th><span  class="float_right">Total Paid Leaves</span></th>
                                                        <th><span  class="float_right">Total Late</span></th>
                                                        <th><span  class="float_right">Total Undertime</span></th>
                                                        <th><span  class="float_right">Balance</span></th>
                                                        <th><span  class="float_right">Net Pay</span></th>
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
    <div id="generate-payroll-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Generate 13th Month</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">

                    <input type="hidden" id="GeneratePayrollTransactionID" value="0" readonly>

                    <div class="row">
                        <div class="col-md-4">
                            <fieldset class="form-group">
                                <label for="GeneratePayrollPeriodCode">Payroll Period: <span class="required_field">* </span></label><span class="search-txt">(Type & search from the list)</span>
                                <div class="div-percent">
                                   <input id="GeneratePayrollPeriodID" type="hidden" value="0">
                                   <input id="GeneratePayrollPeriodCode" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-type="payroll-period" placeholder="Payroll Period"><span class='percent-sign'> <i class="bx bx-search" style="line-height: 21px;"></i> </span>
                                </div> 
                            </fieldset>
                        </div>
                        <div class="col-md-4">
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

                    <div class="row" style="width:100%;">
                        <div class="col-md-4">
                            <fieldset class="form-group">
                                <label for="GenerateFilter">Filter: </label>
                                    <select id="GenerateFilter" class="form-control select2">
                                        <option value="Location">Location</option>
                                        <option value="Division">Division</option>
                                        <option value="Department">Department</option>
                                        <option value="Section">Section</option>
                                        <option value="Job Type">Job Type</option>
                                    </select>
                            </fieldset>
                        </div>
                        <div class="col-md-8">
                            <fieldset class="form-group">
                                <label id="GeneratePayrollFilterLabel">Location: <span class="required_field">* </span></label><span class="search-txt">(Type & search from the list)</span>
                                <div id="divLocation" class="div-percent">
                                   <input id="GeneratePayrollBranchID" type="hidden" value="0">
                                   <input id="GeneratePayrollBranch" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-type="generate-branch" placeholder="Location"><span class='percent-sign'> <i class="bx bx-search" style="line-height: 21px;"></i> </span>
                                </div> 
                                <div id="divDivision" class="div-percent" style="display:none;">
                                   <input id="GeneratePayrollDivisionID" type="hidden" value="0">
                                   <input id="GeneratePayrollDivision" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-type="generate-division" placeholder="Division"><span class='percent-sign'> <i class="bx bx-search" style="line-height: 21px;"></i> </span>
                                </div> 
                                <div id="divDepartment" class="div-percent" style="display:none;">
                                   <input id="GeneratePayrollDepartmentID" type="hidden" value="0">
                                   <input id="GeneratePayrollDepartment" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-type="generate-department" placeholder="Department"><span class='percent-sign'> <i class="bx bx-search" style="line-height: 21px;"></i> </span>
                                </div> 
                                <div id="divSection" class="div-percent" style="display:none;">
                                   <input id="GeneratePayrollSectionID" type="hidden" value="0">
                                   <input id="GeneratePayrollSection" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-type="generate-section" placeholder="Section"><span class='percent-sign'> <i class="bx bx-search" style="line-height: 21px;"></i> </span>
                                </div> 
                                <div id="divJobType" class="div-percent" style="display:none;">
                                   <input id="GeneratePayrollJobTypeID" type="hidden" value="0">
                                   <input id="GeneratePayrollJobType" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-type="generate-jobtype" placeholder="JobType"><span class='percent-sign'> <i class="bx bx-search" style="line-height: 21px;"></i> </span>
                                </div> 
                            </fieldset>
                        </div>
                    </div>
                    <div id="divGenerateNotes" class="row" style="display:none;">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <span style="color:red;">Note : Generated 13th month previously will be deleted.</span>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnSaveRecord" type="button" class="btn btn-primary ml-1" onclick="doGeneratePayroll()">
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
                    <h5 class="modal-title white-color">Employee 13th Month Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">

                    <input type="hidden" id="EmpPayrollTransID" value="0" readonly>
                    <input type="hidden" id="EmpPayrollTransPayrollPeriodID" value="0" readonly>
                    <input type="hidden" id="EmpPayrollTransDetailsID" value="0" readonly>

                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label style="font-size: 20px;">Employee Information</label>                     
                            </fieldset>
                        </div>
                    </div>

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

                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label style="font-size: 20px;">Designation</label>                     
                            </fieldset>
                        </div>
                    </div>

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
                    </div>

                    <div class="row search-menu">
                        <div class="col-12">
                            <!-- search menu tab -->
                            <ul class="nav nav-tabs py-1" role="tablist">
                                <li class="nav-item" style="width:20%;">
                                    <a class="nav-link active" data-toggle="tab" href="#all" role="tab" aria-selected="true" onclick="SetActiveTab('13th Month Summary')">
                                        <span class="d-none d-sm-block">13th Month Summary</span>
                                    </a>
                                </li>
                                <li class="nav-item" style="width:20%;">
                                    <a class="nav-link" id="images-tab" data-toggle="tab" href="javascript:void(0);" role="tab" aria-selected="false" onclick="SetActiveTab('Basic Salary')">
                                        <span class="d-none d-sm-block">Basic Salary</span>
                                    </a>
                                </li>
                                <li class="nav-item" style="width:20%;">
                                    <a class="nav-link" id="images-tab" data-toggle="tab" href="javascript:void(0);" role="tab" aria-selected="false" onclick="SetActiveTab('Leave')">
                                        <span class="d-none d-sm-block">Leave</span>
                                    </a>
                                </li>
                                <li class="nav-item" style="width:20%;">
                                    <a class="nav-link" id="images-tab" data-toggle="tab" href="javascript:void(0);" role="tab" aria-selected="false" onclick="SetActiveTab('Late')">
                                        <span class="d-none d-sm-block">Late</span>
                                    </a>
                                </li>
                                <li class="nav-item" style="width:20%;">
                                    <a class="nav-link" id="images-tab" data-toggle="tab" href="javascript:void(0);" role="tab" aria-selected="false" onclick="SetActiveTab('Undertime')">
                                        <span class="d-none d-sm-block">Undertime</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div id="div13thMonthSummary" class="row">
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label style="font-size: 20px;">Earnings</label>                     
                            </fieldset>

                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                <fieldset>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Basic Salary</span>
                                        </div>
                                        <input id="Emp13thMonthSummaryTotalBasicSalary" type="text" class="form-control DecimalOnly align-right" placeholder="Basic Salary" aria-describedby="basic-addon1" readonly>
                                    </div>
                                </fieldset>
                            </div>

                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                <fieldset>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Leave</span>
                                        </div>
                                        <input id="Emp13thMonthSummaryTotalLeave" type="text" class="form-control DecimalOnly align-right" placeholder="Leave" aria-describedby="basic-addon1" readonly>
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
                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Late</span>
                                        </div>
                                        <input id="Emp13thMonthSummaryTotalLate" type="text" class="form-control DecimalOnly align-right" placeholder="Late" aria-describedby="basic-addon1" readonly>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                                <fieldset>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Undertime</span>
                                        </div>
                                        <input id="Emp13thMonthSummaryTotalUndertime" type="text" class="form-control DecimalOnly align-right" placeholder="Late" aria-describedby="basic-addon1" readonly>
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
                                        <input id="Emp13thMonthSummaryNetPay" type="text" class="form-control DecimalOnly align-right" placeholder="Net Pay" aria-describedby="basic-addon1" readonly>
                                    </div>
                                </fieldset>
                            </div>  

                        </div>
                    </div>                    
                    <div id="divBasicSalary" class="row" style="display:none;">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label style="font-size: 20px;">Basic Salary</label>                     
                            </fieldset>
                        </div>
                        <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                            <table id="tblBasicSalaryList" class="table zero-configuration complex-headers border">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Period</th>
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
                                        <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Basic Salary</span>
                                    </div>
                                    <input id="BasicSalaryTotalBasicSalary" type="text" class="form-control DecimalOnly align-right" placeholder="Total Basic Salary" aria-describedby="basic-addon1" readonly>
                                </div>
                            </fieldset>
                        </div>  
                    </div>    
                    <div id="divLeave" class="row" style="display:none;">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label style="font-size: 20px;">Leave</label>                     
                            </fieldset>
                        </div>
                        <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                            <table id="tblLeaveList" class="table zero-configuration complex-headers border">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Period</th>
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
                                    <input id="LeaveTotalLeave" type="text" class="form-control DecimalOnly align-right" placeholder="Total Leave" aria-describedby="basic-addon1" readonly>
                                </div>
                            </fieldset>
                        </div>  
                    </div>                    
                    <div id="divLate" class="row" style="display:none;">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label style="font-size: 20px;">Late</label>                     
                            </fieldset>
                        </div>
                        <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                            <table id="tblLateList" class="table zero-configuration complex-headers border">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Period</th>
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
                                        <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Late</span>
                                    </div>
                                    <input id="LateTotalLate" type="text" class="form-control DecimalOnly align-right" placeholder="Total Late" aria-describedby="basic-addon1" readonly>
                                </div>
                            </fieldset>
                        </div>  
                    </div>                    
                    <div id="divUndertime" class="row" style="display:none;">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <label style="font-size: 20px;">Undertime</label>                     
                            </fieldset>
                        </div>
                        <div class="col-md-12 remove_md_padding" style="margin-top:5px;">
                            <table id="tblUndertimeList" class="table zero-configuration complex-headers border">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Period</th>
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
                                        <span class="input-group-text summary_totals_width mx3-disabled-background-color">Total Undertime</span>
                                    </div>
                                    <input id="UndertimeTotalUndertime" type="text" class="form-control DecimalOnly align-right" placeholder="Total Late" aria-describedby="basic-addon1" readonly>
                                </div>
                            </fieldset>
                        </div>  
                    </div>                    

                </div>
                <div class="modal-footer">
                    <button id="btnRegeneratePayroll" type="button" class="btn btn-primary ml-1" style="display:none;" onclick="RegenerateEmployeePayroll()">
                      <i class="bx bx-check d-block d-sm-none"></i>
                      <span class="d-none d-sm-block">Recompute</span>
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
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Approve Payroll</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <span>This will approve the generated 13th month and make this as final. Would you like to proceed?</span>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnApprovePayrollProceed" type="button" class="btn btn-primary ml-1" onclick="doApprovePayrollTransaction()">
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
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Cancel Generated 13th Month</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">

                    <div class="row">
                        <div class="col-md-12">
                            <fieldset class="form-group">
                                <span>Are you sure you want to cancel this generated 13th month?</span>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnCancelPayrollProceed" type="button" class="btn btn-primary ml-1" onclick="doCancelPayrollTransaction()">
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
                "order": [[ 1, "asc" ]]
            });

            $('#tblBasicSalaryList').DataTable( {
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

            $('#tblLeaveList').DataTable( {
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

            $('#tblLateList').DataTable( {
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

            $('#tblUndertimeList').DataTable( {
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

            $("#record-modal").modal({
                show: false,
                backdrop: 'static'
            });

        });

        $("#btnSearch").click(function(){
            $("#tblList").DataTable().clear().draw();
            intCurrentPage = 1;
            getRecordList(intCurrentPage, $('.searchtext').val());
        });

        $('.searchtext').on('keypress', function (e) {
            if(e.which === 13){
                $("#tblList").DataTable().clear().draw();
                intCurrentPage = 1;
                getRecordList(intCurrentPage, $('.searchtext').val());
            }
        });

        function getRecordList(vPageNo, vSearchText){

            if($("#SearchPayrollTransactionID").val() == "" || $("#SearchPayrollTransactionID").val() == "0"){
                showHasErrorMessage('13th Month Transaction','Unable to identify 13th month transaction.');
            }else{
                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollTransactionID: $("#SearchPayrollTransactionID").val(),
                        SearchText: $("#SearchEmployee").val(),
                        Status: $("#SearchPayrollTransactionStatus").val(),
                        PageNo: vPageNo
                    },
                    url: "{{ route('get-13th-month-transaction-employee-list') }}",
                    dataType: "json",
                    success: function(data){
                        LoadRecordList(data.ThirteenMonthTransactionEmployeeList);
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

            tdAction="";

          if(IsAdmin==1 || IsAllowView==1){

            tdAction = "<div class='dropdown'>" + 
                        "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:#475F7B;'></span> " +
                            "<div class='dropdown-menu dropdown-menu-right'>";

                                tdAction += "<a class='dropdown-item' href='javascript:void(0);' onclick='ViewPayrollDetails(" + vData.PayrollTransactionID + "," + vData.EmployeeID + ")'>"+
                                    "View Details" +
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

            tdTotalBasicSalary = "<span class='font-normal float_right'>" + FormatDecimal(vData.TotalBasicSalary,2) + "</span>";
            tdTotalLeaves = "<span class='font-normal float_right'>" + FormatDecimal(vData.TotalLeaves,2) + "</span>";

            tdTotalLate = "<span class='font-normal float_right'>" + FormatDecimal(vData.TotalLate,2) + "</span>";
            tdTotalUndertime = "<span class='font-normal float_right'>" + FormatDecimal(vData.TotalUndertime,2) + "</span>";
            tdBalance = "<span class='font-normal float_right'>" + FormatDecimal(vData.Balance,2) + "</span>";
            tdNetPay = "<span class='font-normal float_right'>" + FormatDecimal(vData.NetPay,2) + "</span>";
            
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
                    curData[2] = tdEmployeeNo;
                    curData[3] = tdEmployeeName;
                    curData[4] = tdBranchName;
                    curData[5] = tdDivision;
                    curData[6] = tdDepartment;
                    curData[7] = tdSection;
                    curData[8] = tdJobTitle;
                    curData[9] = tdTotalBasicSalary;
                    curData[10] = tdTotalLeaves;
                    curData[11] = tdTotalLate;
                    curData[12] = tdTotalUndertime;
                    curData[13] = tdBalance;
                    curData[14] = tdNetPay;

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
                        tdTotalBasicSalary,
                        tdTotalLeaves,
                        tdTotalLate,
                        tdTotalUndertime,
                        tdBalance,
                        tdNetPay
                    ]).draw();          
            }

        }

        function GeneratePayrollRecord(vIsNew){

            $("#divGenerateNotes").hide();

            if(vIsNew == 0){
                $("#GeneratePayrollTransactionID").val(0);
            }else{
                $("#divGenerateNotes").show();
                $("#GeneratePayrollTransactionID").val($("#SearchPayrollTransactionID").val());
            }

            $("#GeneratePayrollBranchID").val(0);
            $("#GeneratePayrollBranch").val('');

            $("#GeneratePayrollDivisionID").val(0);
            $("#GeneratePayrollDivision").val('');

            $("#GeneratePayrollDepartmentID").val(0);
            $("#GeneratePayrollDepartment").val('');

            $("#GeneratePayrollSectionID").val(0);
            $("#GeneratePayrollSection").val('');

            $("#GeneratePayrollJobTypeID").val(0);
            $("#GeneratePayrollJobType").val('');

            $("#GeneratePayrollPeriodID").val(0);
            $("#GeneratePayrollPeriodCode").val('');
            $("#GeneratePeriodDateStart").val('');
            $("#GeneratePeriodDateEnd").val('');

            if($("#GeneratePayrollTransactionID").val() != "" && $("#GeneratePayrollTransactionID").val() != "0"){
                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollTransactionID: $("#GeneratePayrollTransactionID").val(),
                        EmployeeID: 0,
                        Status: ""
                    },
                    url: "{{ route('get-13th-month-transaction-info') }}",
                    dataType: "json",
                    success: function(data){
                        if(data.Response =='Success' && data.ThirteenMonthTransactionInfo != undefined){

                            $("#GeneratePayrollPeriodID").val(data.ThirteenMonthTransactionInfo.PayrollPeriodID);
                            $("#GeneratePayrollPeriodCode").val(data.ThirteenMonthTransactionInfo.PayrollPeriodCode);
                            $("#GeneratePeriodDateStart").val(data.ThirteenMonthTransactionInfo.PayrollPeriodStartDate);
                            $("#GeneratePeriodDateEnd").val(data.ThirteenMonthTransactionInfo.PayrollPeriodEndDate);

                            $("#GeneratePayrollBranchID").val(data.ThirteenMonthTransactionInfo.BranchID);
                            $("#GeneratePayrollBranch").val(data.ThirteenMonthTransactionInfo.BranchName);

                            $("#GeneratePayrollDivisionID").val(data.ThirteenMonthTransactionInfo.DivisionID);
                            $("#GeneratePayrollDivision").val(data.ThirteenMonthTransactionInfo.Division);

                            $("#GeneratePayrollDepartmentID").val(data.ThirteenMonthTransactionInfo.DepartmentID);
                            $("#GeneratePayrollDepartment").val(data.ThirteenMonthTransactionInfo.Department);

                            $("#GeneratePayrollSectionID").val(data.ThirteenMonthTransactionInfo.SectionID);
                            $("#GeneratePayrollSection").val(data.ThirteenMonthTransactionInfo.Section);

                            $("#GeneratePayrollJobTypeID").val(data.ThirteenMonthTransactionInfo.JobTypeID);
                            $("#GeneratePayrollJobType").val(data.ThirteenMonthTransactionInfo.JobTitle);

                            $("#divLoader").hide();
                            $("#generate-payroll-modal").modal();
                        }else{
                            $("#divLoader").hide();
                            showModalMessage("DANGER","Generate 13th Month",data.ResponseMessage,"OK");
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
            }else{
                $("#generate-payroll-modal").modal();
            }

        }

        $("#GenerateFilter").change(function(){

            $("#divLocation").hide();
            $("#divDivision").hide();
            $("#divDepartment").hide();
            $("#divSection").hide();
            $("#divJobType").hide();

            if($("#GenerateFilter").val() == "Location"){
                $("#GeneratePayrollFilterLabel").text("Location");
                $("#divLocation").show();
            }else if($("#GenerateFilter").val() == "Division"){
                $("#GeneratePayrollFilterLabel").text("Division");
                $("#divDivision").show();
            }else if($("#GenerateFilter").val() == "Department"){
                $("#GeneratePayrollFilterLabel").text("Department");
                $("#divDepartment").show();
            }else if($("#GenerateFilter").val() == "Section"){
                $("#GeneratePayrollFilterLabel").text("Section");
                $("#divSection").show();
            }else if($("#GenerateFilter").val() == "Job Type"){
                $("#GeneratePayrollFilterLabel").text("Job Type");
                $("#divJobType").show();
            }

        });

        function doGeneratePayroll(){

            if($("#GeneratePayrollPeriodID").val() == "0"){
                showHasErrorMessage('Period','Please select payroll period.');
            }else if($("#GeneratePayrollPeriodID").val() == "0"){
                showHasErrorMessage('Period','Please select payroll period.');
            }else if($("#GenerateFilter").val() == "Location" && ($("#GeneratePayrollBranchID").val() == "" || $("#GeneratePayrollBranchID").val() == "0")){ 
                showHasErrorMessage('Location','Please select location.');
            }else if($("#GenerateFilter").val() == "Division" && ($("#GeneratePayrollDivisionID").val() == "" || $("#GeneratePayrollDivisionID").val() == "0")){ 
                showHasErrorMessage('Division','Please select division.');
            }else if($("#GenerateFilter").val() == "Department" && ($("#GeneratePayrollDepartmentID").val() == "" || $("#GeneratePayrollDepartmentID").val() == "0")){ 
                showHasErrorMessage('Department','Please select department.');
            }else if($("#GenerateFilter").val() == "Section" && ($("#GeneratePayrollSectionID").val() == "" || $("#GeneratePayrollSectionID").val() == "0")){ 
                showHasErrorMessage('Section','Please select section.');
            }else if($("#GenerateFilter").val() == "Job Type" && ($("#GeneratePayrollJobTypeID").val() == "" || $("#GeneratePayrollJobTypeID").val() == "0")){ 
                showHasErrorMessage('Job Type','Please select job type.');

            }else{
                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollTransactionID: $("#GeneratePayrollTransactionID").val(),
                        PayrollPeriodID: $("#GeneratePayrollPeriodID").val(),
                        PayrollType: "{{ config("app.GENERATE_13THMONTH_BATCH") }}",
                        FilterType: $("#GenerateFilter").val(),
                        BranchID: $("#GeneratePayrollBranchID").val(),
                        DivisionID: $("#GeneratePayrollDivisionID").val(),
                        DepartmentID: $("#GeneratePayrollDepartmentID").val(),
                        SectionID: $("#GeneratePayrollSectionID").val(),
                        JobTypeID: $("#GeneratePayrollJobTypeID").val(),
                        Status: '{{ config('app.STATUS_PENDING') }}'
                    },
                    url: "{{ route('do-generate-13th-month') }}",
                    dataType: "json",
                    success: function(data){
                        if(data.Response =='Success' && data.ThirteenMonthTransactionInfo != undefined){
                            
                            $("#SearchPayrollTransactionID").val(data.ThirteenMonthTransactionInfo.ID);
                            $("#SearchPayrollTransaction").val("Trans No. " + data.ThirteenMonthTransactionInfo.TransNo + "; Period : " + data.ThirteenMonthTransactionInfo.PayrollPeriodCode + " " + data.ThirteenMonthTransactionInfo.PayrollPeriodStartDate + " - " + data.ThirteenMonthTransactionInfo.PayrollPeriodEndDate);
                            $("#SearchPayrollTransactionStatus").val(data.ThirteenMonthTransactionInfo.Status);

                            $("#SearchTransNo").text("Transaction No. : " + data.ThirteenMonthTransactionInfo.TransNo);
                            $("#SearchTransDate").text("Transaction Date/Time : " + data.ThirteenMonthTransactionInfo.TransDateTime);
                            $("#SearchStatus").text("Status : " + data.ThirteenMonthTransactionInfo.Status);
                            if(data.ThirteenMonthTransactionInfo.Status == "{{ config('app.STATUS_PENDING') }}"){
                                $("#SearchStatus").css("color", "orange");
                            }else if(data.ThirteenMonthTransactionInfo.Status == "{{ config('app.STATUS_CANCELLED') }}"){
                                $("#SearchStatus").css("color", "red");
                            }else if(data.ThirteenMonthTransactionInfo.Status == "{{ config('app.STATUS_APPROVED') }}"){
                                $("#SearchStatus").css("color", "green");
                            }

                            $("#SearchPayrollPeriod").text("Period : " + data.ThirteenMonthTransactionInfo.PayrollPeriodCode + " " + data.ThirteenMonthTransactionInfo.PayrollPeriodStartDate + " - " + data.ThirteenMonthTransactionInfo.PayrollPeriodEndDate);

                            $("#SearchEmployee").val('');
                            $("#SearchEmployee").prop('disabled', false);
                            $("#btnSearch").prop('disabled', false);
                            
                            if($("#SearchPayrollTransactionStatus").val() == '{{ config('app.STATUS_PENDING') }}'){
                                $("#btnRegenerate").show();
                                $("#btnApprove").show();
                                $("#btnCancel").show();
                            }else if($("#SearchPayrollTransactionStatus").val() == '{{ config('app.STATUS_APPROVED') }}'){
                                $("#btnRegenerate").hide();
                                $("#btnApprove").hide();
                                $("#btnCancel").show();
                            }else{
                                $("#btnRegenerate").hide();
                                $("#btnApprove").hide();
                                $("#btnCancel").hide();
                            }

                            $("#divSearchInfo").show();                            
                            $("#divSearchFilter").show();

                            $("#tblList").DataTable().clear().draw();
                            intCurrentPage = 1;
                            getRecordList(intCurrentPage, '');

                            $("#divLoader").hide();

                            showHasSuccessMessage("13th month has been generated successfully.");

                            $("#generate-payroll-modal").modal('hide');

                        }else{
                            $("#divLoader").hide();
                            showHasErrorMessage('Generate Payroll',data.ResponseMessage);
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

        function ViewPayrollDetails(vID, vEmployeeID){

            if(vID > 0 && vEmployeeID > 0){
                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollTransactionID: vID,
                        EmployeeID: vEmployeeID,
                        Status: $("#SearchPayrollTransactionStatus").val()
                    },
                    url: "{{ route('get-13th-month-transaction-info') }}",
                    dataType: "json",
                    success: function(data){
                        if(data.Response =='Success' && data.ThirteenMonthTransactionEmployeeInfo != undefined){
                            
                            $("#EmpPayrollTransID").val(data.ThirteenMonthTransactionEmployeeInfo.PayrollTransactionID);
                            $("#EmpPayrollTransPayrollPeriodID").val(data.ThirteenMonthTransactionEmployeeInfo.PayrollPeriodID);
                            $("#EmpPayrollTransDetailsID").val(data.ThirteenMonthTransactionEmployeeInfo.ID);

                            $("#EmpPayrollTransEmployeeID").val(data.ThirteenMonthTransactionEmployeeInfo.EmployeeID);
                            $("#EmpPayrollTransFirstName").val(data.ThirteenMonthTransactionEmployeeInfo.FirstName);
                            $("#EmpPayrollTransMiddleName").val(data.ThirteenMonthTransactionEmployeeInfo.MiddleName);
                            $("#EmpPayrollTransLastName").val(data.ThirteenMonthTransactionEmployeeInfo.LastName);

                            $("#EmpPayrollTransEmployeeNo").val(data.ThirteenMonthTransactionEmployeeInfo.EmployeeNo);
                            $("#EmpPayrollTransStatus").val(data.ThirteenMonthTransactionEmployeeInfo.EmployeeStatus);
                            
                            $("#EmpPayrollTransContactNumber").val(data.ThirteenMonthTransactionEmployeeInfo.ContactNumber);
                            $("#EmpPayrollTransEmailAddress").val(data.ThirteenMonthTransactionEmployeeInfo.EmailAddress);

                            $("#EmpPayrollTransDivision").val(data.ThirteenMonthTransactionEmployeeInfo.Division);
                            $("#EmpPayrollTransDepartment").val(data.ThirteenMonthTransactionEmployeeInfo.Department);
                            $("#EmpPayrollTransSection").val(data.ThirteenMonthTransactionEmployeeInfo.Section);
                            $("#EmpPayrollTransJobTitle").val(data.ThirteenMonthTransactionEmployeeInfo.JobTitle);
                            
                            $("#Emp13thMonthSummaryTotalBasicSalary").val(FormatDecimal(data.ThirteenMonthTransactionEmployeeInfo.TotalBasicSalary,2));
                            $("#Emp13thMonthSummaryTotalLeave").val(FormatDecimal(data.ThirteenMonthTransactionEmployeeInfo.TotalLeaves,2));
                            $("#Emp13thMonthSummaryTotalLate").val(FormatDecimal(data.ThirteenMonthTransactionEmployeeInfo.TotalLate,2));
                            $("#Emp13thMonthSummaryTotalUndertime").val(FormatDecimal(data.ThirteenMonthTransactionEmployeeInfo.TotalUndertime,2));
                            $("#Emp13thMonthSummaryNetPay").val(FormatDecimal(data.ThirteenMonthTransactionEmployeeInfo.NetPay,2));


                            $("#tblBasicSalaryList").DataTable().clear().draw();
                            var tblBasicSalaryList = $("#tblBasicSalaryList").DataTable();

                            $("#tblLeaveList").DataTable().clear().draw();
                            var tblLeaveList = $("#tblLeaveList").DataTable();

                            $("#tblLateList").DataTable().clear().draw();
                            var tblLateList = $("#tblLateList").DataTable();

                            $("#tblUndertimeList").DataTable().clear().draw();
                            var tblUndertimeList = $("#tblUndertimeList").DataTable();

                            var dblTotalBasicSalary = 0;
                            if(data.ThirteenMonthTransactionEmployeeBasicSalaryList != undefined){
                                for(var x=0; x < data.ThirteenMonthTransactionEmployeeBasicSalaryList.length; x++){

                                        //List on Table
                                        tdID = data.ThirteenMonthTransactionEmployeeBasicSalaryList[x].ID;
                                        tdPeriod = "<span class='font-normal'>" + data.ThirteenMonthTransactionEmployeeBasicSalaryList[x].PayrollPeriodCode + " : " + data.ThirteenMonthTransactionEmployeeBasicSalaryList[x].PayrollPeriodStartDate + " - " + data.ThirteenMonthTransactionEmployeeBasicSalaryList[x].PayrollPeriodEndDate + "</span>";
                                        tdAmount = "<span class='font-normal float_right'>" + FormatDecimal(data.ThirteenMonthTransactionEmployeeBasicSalaryList[x].Total,2) + "</span>";

                                        tblBasicSalaryList.row.add([
                                                                tdID,
                                                                tdPeriod,
                                                                tdAmount
                                                            ]).draw();

                                        dblTotalBasicSalary = dblTotalBasicSalary + parseFloat(data.ThirteenMonthTransactionEmployeeBasicSalaryList[x].Total);
                                }
                            }
                            $("#BasicSalaryTotalBasicSalary").val(FormatDecimal(dblTotalBasicSalary,2));

                            var dblTotalLeave = 0;
                            if(data.ThirteenMonthTransactionEmployeeLeaveList != undefined){
                                for(var x=0; x < data.ThirteenMonthTransactionEmployeeLeaveList.length; x++){

                                        //List on Table
                                        tdID = data.ThirteenMonthTransactionEmployeeLeaveList[x].ID;
                                        tdPeriod = "<span class='font-normal'>" + data.ThirteenMonthTransactionEmployeeLeaveList[x].PayrollPeriodCode + " : " + data.ThirteenMonthTransactionEmployeeLeaveList[x].PayrollPeriodStartDate + " - " + data.ThirteenMonthTransactionEmployeeLeaveList[x].PayrollPeriodEndDate + "</span>";
                                        tdAmount = "<span class='font-normal float_right'>" + FormatDecimal(data.ThirteenMonthTransactionEmployeeLeaveList[x].Total,2) + "</span>";

                                        tblLeaveList.row.add([
                                                                tdID,
                                                                tdPeriod,
                                                                tdAmount
                                                            ]).draw();

                                        dblTotalLeave = dblTotalLeave + parseFloat(data.ThirteenMonthTransactionEmployeeLeaveList[x].Total);
                                }
                            }
                            $("#LeaveTotalLeave").val(FormatDecimal(dblTotalLeave,2));

                            var dblTotalLate = 0;
                            if(data.ThirteenMonthTransactionEmployeeLateList != undefined){
                                for(var x=0; x < data.ThirteenMonthTransactionEmployeeLateList.length; x++){

                                        //List on Table
                                        tdID = data.ThirteenMonthTransactionEmployeeLateList[x].ID;
                                        tdPeriod = "<span class='font-normal'>" + data.ThirteenMonthTransactionEmployeeLateList[x].PayrollPeriodCode + " : " + data.ThirteenMonthTransactionEmployeeLateList[x].PayrollPeriodStartDate + " - " + data.ThirteenMonthTransactionEmployeeLateList[x].PayrollPeriodEndDate + "</span>";
                                        tdAmount = "<span class='font-normal float_right'>" + FormatDecimal(data.ThirteenMonthTransactionEmployeeLateList[x].Total,2) + "</span>";

                                        tblLateList.row.add([
                                                                tdID,
                                                                tdPeriod,
                                                                tdAmount
                                                            ]).draw();

                                        dblTotalLate = dblTotalLate + parseFloat(data.ThirteenMonthTransactionEmployeeLateList[x].Total);
                                }
                            }
                            $("#LateTotalLate").val(FormatDecimal(dblTotalLate,2));

                            var dblTotalUndertime = 0;
                            if(data.ThirteenMonthTransactionEmployeeUnderTimeList != undefined){
                                for(var x=0; x < data.ThirteenMonthTransactionEmployeeUnderTimeList.length; x++){

                                        //List on Table
                                        tdID = data.ThirteenMonthTransactionEmployeeUnderTimeList[x].ID;
                                        tdPeriod = "<span class='font-normal'>" + data.ThirteenMonthTransactionEmployeeUnderTimeList[x].PayrollPeriodCode + " : " + data.ThirteenMonthTransactionEmployeeUnderTimeList[x].PayrollPeriodStartDate + " - " + data.ThirteenMonthTransactionEmployeeUnderTimeList[x].PayrollPeriodEndDate + "</span>";
                                        tdAmount = "<span class='font-normal float_right'>" + FormatDecimal(data.ThirteenMonthTransactionEmployeeUnderTimeList[x].Total,2) + "</span>";

                                        tblUndertimeList.row.add([
                                                                tdID,
                                                                tdPeriod,
                                                                tdAmount
                                                            ]).draw();

                                        dblTotalUndertime = dblTotalUndertime + parseFloat(data.ThirteenMonthTransactionEmployeeUnderTimeList[x].Total);
                                }
                            }
                            $("#UndertimeTotalUndertime").val(FormatDecimal(dblTotalUndertime,2));

                            $("#btnRegeneratePayroll").hide();
                            if($("#SearchPayrollTransactionStatus").val() == "{{ config('app.STATUS_PENDING') }}"){
                                $("#btnRegeneratePayroll").show();
                            }
                            
                            $("#divLoader").hide();

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

            $("#div13thMonthSummary").hide();
            $("#divBasicSalary").hide();
            $("#divLeave").hide();
            $("#divLate").hide();
            $("#divUndertime").hide();

            if(vTab == "13th Month Summary"){
                $("#div13thMonthSummary").show();
            }else if(vTab == "Basic Salary"){
                $("#divBasicSalary").show();
            }else if(vTab == "Leave"){
                $("#divLeave").show();
            }else if(vTab == "Late"){
                $("#divLate").show();
            }else if(vTab == "Undertime"){
                $("#divUndertime").show();
            }
        }

        function RegenerateEmployeePayroll(){
            if($("#SearchPayrollTransactionStatus").val() == "{{ config('app.STATUS_PENDING') }}"){

                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollTransactionID: $("#EmpPayrollTransID").val(),
                        PayrollType: "{{ config("app.GENERATE_13THMONTH_EMPLOYEE") }}",
                        PayrollPeriodID: $("#EmpPayrollTransPayrollPeriodID").val(),
                        FilterType: $("#GenerateFilter").val(),
                        BranchID: 0,
                        DivisionID: 0,
                        DepartmentID: 0,
                        SectionID: 0,
                        JobTypeID: 0,
                        EmployeeID: $("#EmpPayrollTransEmployeeID").val(),
                        Status: $("#SearchPayrollTransactionStatus").val()
                    },
                    url: "{{ route('do-generate-13th-month') }}",
                    dataType: "json",
                    success: function(data){
                        if(data.Response =='Success' && data.ThirteenMonthTransactionInfo != undefined){
                            
                            LoadRecordRow(data.ThirteenMonthTransactionEmployeeInfo);
                            ViewPayrollDetails($("#EmpPayrollTransID").val(), $("#EmpPayrollTransEmployeeID").val());
                            showHasSuccessMessage("Employee 13th month has been regenerated successfully.");
                            $("#divLoader").hide();

                        }else{
                            $("#divLoader").hide();
                            showHasErrorMessage('Generate Payroll',data.ResponseMessage);
                        }
                      $("#spnLoadingLabel").text('Loading...');    
                    },
                    error: function(data){
                        $("#divLoader").hide();
                        console.log(data.responseText);
                    },
                    beforeSend:function(vData){
                        $("#divLoader").show();
                        $("#spnLoadingLabel").text('Generating...');
                    }

                });

            }
        }

        function ApprovePayrollTransaction(vIsNew){
            $("#approve-payroll-modal").modal();
        }

        function doApprovePayrollTransaction(){

            if($("#SearchPayrollTransactionID").val() == "" || $("#SearchPayrollTransactionID").val() == "0"){
                showHasErrorMessage('13th Month Transaction','Unable to identify 13th month transaction.');
            }else{
                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollTransactionID: $("#SearchPayrollTransactionID").val()
                    },
                    url: "{{ route('do-approve-generated-13th-month') }}",
                    dataType: "json",
                    success: function(data){
                        if(data.Response =='Success' && data.ThirteenMonthTransactionInfo != undefined){
                            
                            $("#SearchPayrollTransactionID").val(data.ThirteenMonthTransactionInfo.ID);
                            $("#SearchPayrollTransaction").val("Trans No. " + data.ThirteenMonthTransactionInfo.TransNo + "; Period : " + data.ThirteenMonthTransactionInfo.PayrollPeriodCode + " " + data.ThirteenMonthTransactionInfo.PayrollPeriodStartDate + " - " + data.ThirteenMonthTransactionInfo.PayrollPeriodEndDate);
                            $("#SearchPayrollTransactionStatus").val(data.ThirteenMonthTransactionInfo.Status);

                            $("#SearchTransNo").text("Transaction No. : " + data.ThirteenMonthTransactionInfo.TransNo);
                            $("#SearchTransDate").text("Transaction Date/Time : " + data.ThirteenMonthTransactionInfo.TransDateTime);
                            $("#SearchStatus").text("Status : " + data.ThirteenMonthTransactionInfo.Status);
                            if(data.ThirteenMonthTransactionInfo.Status == "{{ config('app.STATUS_PENDING') }}"){
                                $("#SearchStatus").css("color", "orange");
                            }else if(data.ThirteenMonthTransactionInfo.Status == "{{ config('app.STATUS_CANCELLED') }}"){
                                $("#SearchStatus").css("color", "red");
                            }else if(data.ThirteenMonthTransactionInfo.Status == "{{ config('app.STATUS_APPROVED') }}"){
                                $("#SearchStatus").css("color", "green");
                            }

                            $("#SearchPayrollPeriod").text("Period : " + data.ThirteenMonthTransactionInfo.PayrollPeriodCode + " " + data.ThirteenMonthTransactionInfo.PayrollPeriodStartDate + " - " + data.ThirteenMonthTransactionInfo.PayrollPeriodEndDate);

                            $("#SearchEmployee").val('');
                            $("#SearchEmployee").prop('disabled', false);
                            $("#btnSearch").prop('disabled', false);
                            
                            if($("#SearchPayrollTransactionStatus").val() == '{{ config('app.STATUS_PENDING') }}'){
                                $("#btnRegenerate").show();
                                $("#btnApprove").show();
                                $("#btnCancel").show();
                            }else if($("#SearchPayrollTransactionStatus").val() == '{{ config('app.STATUS_APPROVED') }}'){
                                $("#btnRegenerate").hide();
                                $("#btnApprove").hide();
                                $("#btnCancel").show();
                            }else{
                                $("#btnRegenerate").hide();
                                $("#btnApprove").hide();
                                $("#btnCancel").hide();
                            }

                            $("#divSearchInfo").show();                            
                            $("#divSearchFilter").show();

                            $("#tblList").DataTable().clear().draw();
                            intCurrentPage = 1;
                            getRecordList(intCurrentPage, '');

                            $("#divLoader").hide();

                            showHasSuccessMessage("Generated 13th month has been approved successfully.");

                            $("#approve-payroll-modal").modal('hide');

                        }else{
                            $("#divLoader").hide();
                            showHasErrorMessage('13th Month Transaction',data.ResponseMessage);
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

        function CancelPayrollTransaction(vIsNew){
            $("#cancel-payroll-modal").modal();
        }

        function doCancelPayrollTransaction(){

            if($("#SearchPayrollTransactionID").val() == "" || $("#SearchPayrollTransactionID").val() == "0"){
                showHasErrorMessage('Payroll Transaction','Unable to identify payroll transaction.');
            }else{
                $.ajax({
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        PayrollTransactionID: $("#SearchPayrollTransactionID").val()
                    },
                    url: "{{ route('do-cancel-generated-13th-month') }}",
                    dataType: "json",
                    success: function(data){
                        if(data.Response =='Success' && data.ThirteenMonthTransactionInfo != undefined){
                            
                            $("#SearchPayrollTransactionID").val(data.ThirteenMonthTransactionInfo.ID);
                            $("#SearchPayrollTransaction").val("Trans No. " + data.ThirteenMonthTransactionInfo.TransNo + "; Period : " + data.ThirteenMonthTransactionInfo.PayrollPeriodCode + " " + data.ThirteenMonthTransactionInfo.PayrollPeriodStartDate + " - " + data.ThirteenMonthTransactionInfo.PayrollPeriodEndDate);
                            $("#SearchPayrollTransactionStatus").val(data.ThirteenMonthTransactionInfo.Status);

                            $("#SearchTransNo").text("Transaction No. : " + data.ThirteenMonthTransactionInfo.TransNo);
                            $("#SearchTransDate").text("Transaction Date/Time : " + data.ThirteenMonthTransactionInfo.TransDateTime);
                            $("#SearchStatus").text("Status : " + data.ThirteenMonthTransactionInfo.Status);
                            if(data.ThirteenMonthTransactionInfo.Status == "{{ config('app.STATUS_PENDING') }}"){
                                $("#SearchStatus").css("color", "orange");
                            }else if(data.ThirteenMonthTransactionInfo.Status == "{{ config('app.STATUS_CANCELLED') }}"){
                                $("#SearchStatus").css("color", "red");
                            }else if(data.ThirteenMonthTransactionInfo.Status == "{{ config('app.STATUS_APPROVED') }}"){
                                $("#SearchStatus").css("color", "green");
                            }

                            $("#SearchPayrollPeriod").text("Payroll Period : " + data.ThirteenMonthTransactionInfo.PayrollPeriodCode + " " + data.ThirteenMonthTransactionInfo.PayrollPeriodStartDate + " - " + data.ThirteenMonthTransactionInfo.PayrollPeriodEndDate);

                            $("#SearchEmployee").val('');
                            $("#SearchEmployee").prop('disabled', false);
                            $("#btnSearch").prop('disabled', false);
                            
                            if($("#SearchPayrollTransactionStatus").val() == '{{ config('app.STATUS_PENDING') }}'){
                                $("#btnRegenerate").show();
                                $("#btnApprove").show();
                                $("#btnCancel").show();
                            }else if($("#SearchPayrollTransactionStatus").val() == '{{ config('app.STATUS_APPROVED') }}'){
                                $("#btnRegenerate").hide();
                                $("#btnApprove").hide();
                                $("#btnCancel").show();
                            }else{
                                $("#btnRegenerate").hide();
                                $("#btnApprove").hide();
                                $("#btnCancel").hide();
                            }

                            $("#divSearchInfo").show();                            
                            $("#divSearchFilter").show();

                            $("#tblList").DataTable().clear().draw();
                            intCurrentPage = 1;
                            getRecordList(intCurrentPage, '');

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

            if($(this).data('type') == "13th-month-transaction"){
          
                $(this).autocomplete({
                    source: function( request, response ) {
                       if(request.term.length >= 2){
                                $.ajax({
                                    method: 'post',
                                    url: 'get-13th-month-transaction-search-list',
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
                                                label: "Trans No. " + code[1] + "; Period : " + code[6] + " " + code[7] + " - " + code[8],
                                                value: "Trans No. " + code[1] + "; Period : " + code[6] + " " + code[7] + " - " + code[8],
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

                            $("#SearchPayrollTransactionID").val(seldata[0]);
                            $("#SearchPayrollTransactionStatus").val(seldata[10]);

                            $("#SearchTransNo").text("Transaction No. : " + seldata[1]);
                            $("#SearchTransDate").text("Transaction Date/Time : " + seldata[2]);
                            $("#SearchStatus").text("Status : " + seldata[10]);
                            if(seldata[10] == "{{ config('app.STATUS_PENDING') }}"){
                                $("#SearchStatus").css("color", "orange");
                            }else if(seldata[10] == "{{ config('app.STATUS_CANCELLED') }}"){
                                $("#SearchStatus").css("color", "red");
                            }else if(seldata[10] == "{{ config('app.STATUS_APPROVED') }}"){
                                $("#SearchStatus").css("color", "green");
                            }

                            $("#SearchPayrollPeriod").text("Payroll Period : " + seldata[6] + " " + seldata[7] + " - " + seldata[8]);
                                                        
                            $("#SearchEmployee").val('');
                            $("#SearchEmployee").prop('disabled', false);
                            $("#btnSearch").prop('disabled', false);

                            if($("#SearchPayrollTransactionStatus").val() == '{{ config('app.STATUS_PENDING') }}'){
                                $("#btnRegenerate").show();
                                $("#btnApprove").show();
                                $("#btnCancel").show();
                            }else if($("#SearchPayrollTransactionStatus").val() == '{{ config('app.STATUS_APPROVED') }}'){
                                $("#btnRegenerate").hide();
                                $("#btnApprove").hide();
                                $("#btnCancel").show();
                            }else{
                                $("#btnRegenerate").hide();
                                $("#btnApprove").hide();
                                $("#btnCancel").hide();
                            }

                            $("#divSearchInfo").show();                            
                            $("#divSearchFilter").show();

                            $("#tblList").DataTable().clear().draw();
                            intCurrentPage = 1;
                            getRecordList(intCurrentPage, '');
                        }
                });
            
            }else if($(this).data('type') == "generate-branch"){
          
                $(this).autocomplete({
                    source: function( request, response ) {
                       if(request.term.length >= 2){
                                $.ajax({
                                    method: 'post',
                                    url: 'get-branch-search-list',
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
                                                label: code[1],
                                                value: code[1],
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

                            $("#GeneratePayrollBranchID").val(seldata[0]);
                            $("#GeneratePayrollBranch").val(seldata[1].trim());
                        }
                });

            }else if($(this).data('type') == "generate-division"){
          
                $(this).autocomplete({
                    source: function( request, response ) {
                       if(request.term.length >= 2){
                                $.ajax({
                                    method: 'post',
                                    url: 'get-division-search-list',
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
                                                label: code[1],
                                                value: code[1],
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

                            $("#GeneratePayrollDivisionID").val(seldata[0]);
                            $("#GeneratePayrollDivision").val(seldata[1].trim());
                        }
                });

            }else if($(this).data('type') == "generate-department"){
          
                $(this).autocomplete({
                    source: function( request, response ) {
                       if(request.term.length >= 2){
                                $.ajax({
                                    method: 'post',
                                    url: 'get-department-search-list',
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
                                                label: code[1],
                                                value: code[1],
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

                            $("#GeneratePayrollDepartmentID").val(seldata[0]);
                            $("#GeneratePayrollDepartment").val(seldata[1].trim());
                            $("#GeneratePayrollDivisionID").val(seldata[2]);
                            $("#GeneratePayrollDivision").val(seldata[3].trim());

                        }
                });

            }else if($(this).data('type') == "generate-section"){
          
                $(this).autocomplete({
                    source: function( request, response ) {
                       if(request.term.length >= 2){
                                $.ajax({
                                    method: 'post',
                                    url: 'get-section-search-list',
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
                                                label: code[1],
                                                value: code[1],
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

                            $("#GeneratePayrollSectionID").val(seldata[0]);
                            $("#GeneratePayrollSection").val(seldata[1].trim());
                            $("#GeneratePayrollDepartmentID").val(seldata[2]);
                            $("#GeneratePayrollDepartment").val(seldata[3].trim());
                            $("#GeneratePayrollDivisionID").val(seldata[4]);
                            $("#GeneratePayrollDivision").val(seldata[5].trim());

                        }
                });

            }else if($(this).data('type') == "generate-jobtype"){
          
                $(this).autocomplete({
                    source: function( request, response ) {
                       if(request.term.length >= 2){
                                $.ajax({
                                    method: 'post',
                                    url: 'get-jobtype-search-list',
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
                                                label: code[1],
                                                value: code[1],
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

                            $("#GeneratePayrollJobTypeID").val(seldata[0]);
                            $("#GeneratePayrollJobType").val(seldata[1].trim());

                        }
                });

            }else if($(this).data('type') == "payroll-period"){
          
                $(this).autocomplete({
                    source: function( request, response ) {
                       if(request.term.length >= 2){
                                $.ajax({
                                    method: 'post',
                                    url: 'get-payroll-period-search-list',
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
                                                label: code[1] + " : " + code[3] + " - " + code[4],
                                                value: code[1],
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

                            $("#GeneratePayrollPeriodID").val(seldata[0]);
                            $("#GeneratePayrollPeriodCode").val(seldata[1].trim());
                            $("#GeneratePeriodDateStart").val(seldata[3].trim());
                            $("#GeneratePeriodDateEnd").val(seldata[4].trim());
                        }
                });
            }


        });

        $(window).scroll(function() {

            if(!isPageFirstLoad){
                if($(window).scrollTop() + $(window).height() >= ($(document).height() - 10) ){
                    intCurrentPage = intCurrentPage + 1;
                    getRecordList(intCurrentPage, $('.searchtext').val());
                }
            }
        });

</script>

@endsection



