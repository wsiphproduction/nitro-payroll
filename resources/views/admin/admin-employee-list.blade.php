@extends('layout.adminweb')
@section('content')

<!--excel--->
<script src="{{ URL::to('public/admin/excel/xlsx.full.min.js') }}"></script>
<script src="{{ URL::to('public/admin/excel/FileSaver.js') }}"></script>

<style>

#tblAllowanceList .dataTables_empty{
    display: none;
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
                                    <li class="breadcrumb-item active">Employee List
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
                                    <h4 class="card-title">Employee List</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <fieldset>

                                                    <div class="input-group">
                                                         <select id="selSearchStatus" class="form-control" style="width:12%">
                                                          <option value="">All Record</option>

                                                         <option disabled="disabled">[ By Location Option ]</option>
                                                          @foreach($BranchList as $brnrow)
                                                          <option value="Location|{{ $brnrow->ID }}">Location : {{ $brnrow->BranchName }}</option>
                                                          @endforeach

                                                          <option disabled="disabled">[ By Site ]</option>
                                                          @foreach($BranchSite as $siterow)
                                                          <option value="Site|{{ $siterow->ID }}">Site : {{ $siterow->SiteName }}</option>
                                                          @endforeach

                                                          <option disabled="disabled">[ Salary Option ]</option>
                                                          <option value="Daily">Salary: Daily</option>
                                                          <option value="Monthly">Salary: Monthly</option>
                                                          <option disabled="disabled">[ Status Option ]</option>
                                                          <option value="Active">Status: Active</option>
                                                          <option value="Inactive">Status: Inactive</option>
                                                        </select>
                                                      
                                                        <input type="text" class="form-control searchtext" placeholder="Search Here.." style="width: 36%;margin-left:6px;">

                                                        <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border" tooltip="Search Here.." tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;">
                                                          <i class="bx bx-search"></i>
                                                       </button>

                                                       @if(Session::get('IS_SUPER_ADMIN') || $Allow_View_Print_Export==1)
                                                        <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="GenerateExcel()" tooltip="Export To Excel" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;margin-left: -11px;">
                                                           <i class="bx bx-file"></i> 
                                                        </button>  
                                                       @endif  

                                                         @if(Session::get('IS_SUPER_ADMIN') || $Allow_View_Print_Export==1)
                                                             <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="UploadRateExcelRecord()" tooltip="Upload Rate Excel" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;">
                                                               <i class="bx bx-upload"></i>  Upload Rates
                                                             </button> 

                                                              <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="UploadMP2ExcelRecord()" tooltip="Upload MP2 Excel" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;">
                                                               <i class="bx bx-upload"></i>  Upload MP2
                                                              </button> 

                                                        @endif 
                                                        @if (Session::get('IS_SUPER_ADMIN'))
                                                            <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="SyncEmployee()" tooltip="Sync Employee" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;">
                                                            <i class="bx bx-sync"></i>  Sync Employee
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
                                                        <th style="width:5%;">EMPLOYEE ID</th>
                                                        <th style="width:11%;">EMPLOYEE NAME</th>
                                                        <th style="width:5%;">SALARY TYPE</th>
                                                        <th style="width:5%;">TIN NO</th>
                                                        <th style="width:5%;">SSS NO</th>
                                                        <th style="width:5%;">PAGIBIG NO</th>
                                                        <th style="width:5%;">PHIC NO</th>
                                                        <th style="width:10%;">DIVISION</th>
                                                        <th style="width:15%;">DEPARTMENT</th>
                                                        <th style="width:15%;">SECTION</th>
                                                        <th style="width:6%;">LOCATION</th>
                                                        <th style="width:5%;">MONTHLY RATE</th>
                                                        <th style="width:5%;">DAILY RATE</th>
                                                        <th style="width:5%;">HOURLY RATE</th>
                                                        <th style="width:8%;">STATUS</th>
                                                    </tr>
                                                   </thead>
                                                  <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="divEmployeePaging" class="col-md-11" style="display: none;">   
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
    <div id="upload-rates-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title white-color">Employee Rate Excel Uploader </h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>                
                </div>
                <div class="modal-body">
                    <div class="row">                                        
                          <h5 style="padding-top:10px;padding-bottom: 10px;">Browse Employee Rate Summary csv file:</h5>
                         <fieldset class="form-group">
                                <label for="myfile">Select files:</label>
                                 <input type="file" id="RateExcelFile" name="RateExcelFile" accept=".csv"/>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer" style="margin-right:10px;">
                
                     <a href="{{URL::asset('public/web/excel template/Employee-New-Rate-Template.csv')}}" id="btnDownloadTemplate" class="btn btn-light-secondary">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Download Template Format</span>
                    </a>

                     <button id="btnUploadRateCSV" type="button" class="btn btn-primary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Upload CSV</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

    <!-- MODAL -->
    <div id="upload-mp2-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title white-color">Employee MP2 Excel Uploader </h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>                
                </div>
                <div class="modal-body">
                    <div class="row">                          
                          <h5 style="padding-top:10px;padding-bottom: 10px;">Browse Employee MP2 Summary csv file:</h5>
                         <fieldset class="form-group">
                                <label for="myfile">Select files:</label>
                                 <input type="file" id="MP2ExcelFile" name="MP2ExcelFile" accept=".csv"/>
                        </fieldset>
                    </div>
                </div>
              <div class="modal-footer" style="margin-right:10px;">
                
                     <a href="{{URL::asset('public/web/excel template/Employee-MP2-Template.csv')}}" id="btnDownloadTemplate" class="btn btn-light-secondary">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Download Template Format</span>
                    </a>

                     <button id="btnUploadMP2CSV" type="button" class="btn btn-primary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Upload CSV</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

    <!-- MODAL -->
    <div id="sync-employee-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title white-color">Sync Employees </h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>                
                </div>
                <div class="modal-body">
                    <div class="row">                          
                          <h5 style="padding-top:10px;padding-bottom: 10px;">Click start to sync employees</h5>
                    </div>
                </div>
              <div class="modal-footer" style="margin-right:10px;">
                     <button id="sync-employee-btn" type="button" class="btn btn-primary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Start Sync</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

     <!-- MODAL -->
    <div id="view-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">View Employee Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">
                 <input type="hidden" class="EmployeeID" value="0" readonly> 
                  
       
                   <div class="col-md-12">
                           <fieldset class="fieldset-border">
                             <legend class="legend-text">| Employee Information |</legend>
                    <div class="row">
                         <div class="col-md-2">
                        <fieldset class="form-group">
                            <label for="PayrollPeriodName">Employee No:</label></span>   
                             <input type="text" class="form-control EmployeeNo" placeholder="Employee No" readonly>                             
                          </fieldset>
                        </div>
                      
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="EmployeeName">First Name: </label>
                                   <input type="text" class="form-control FirstName" placeholder="Employee Name" readonly>
                          </fieldset>
                        </div>
                       
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="LastName">Last Name: </label>
                                   <input type="text" class="form-control LastName" placeholder="Employee Name" readonly>
                          </fieldset>
                        </div>

                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="EmployeeName">Middle Name: </label>
                                   <input type="text" class="form-control MiddleName" placeholder="Employee Name" readonly>
                          </fieldset>
                        </div>  

                        <div class="col-md-1">
                          <fieldset class="form-group">
                            <label for="EmployeeName">Status: </label>
                                <div id="dvActive" style="display:block;">
                                    <span style="color:green;display:flex;margin-top: 6px;"> <i class="bx bx-check-circle"></i> Active </span>
                                </div>
                               <div id="dvInactive" style="display:block;">
                                    <span style="color:red;display:flex;margin-top: 6px;"> <i class="bx bx-x-circle"></i> Inactive </span>
                                </div>      
                        </div>               
                   </div>   
                  </fieldset>              
               </div>    
                   <hr>

                <div class="col-md-12">
                           <fieldset class="fieldset-border">
                             <legend class="legend-text">| Other Information |</legend>
                   <div class="row">
                        <div class="col-md-6">
                          <fieldset class="form-group">
                            <label for="Location">Location: </label>
                                   <input id="Location" type="text" class="form-control" placeholder="Employee Location" readonly>
                          </fieldset>
                        </div>
                        <div class="col-md-6">
                          <fieldset class="form-group">
                            <label for="Site">Site: </label>
                                   <input id="Site" type="text" class="form-control" placeholder="Employee Designation Site" readonly>
                          </fieldset>
                        </div>
                    </div>
                    <div class="row">
                         <div class="col-md-4">
                        <fieldset class="form-group">
                            <label for="PayrollPeriodName">Division:</label></span>   
                             <input id="Division" type="text" class="form-control" placeholder="Employee Division" readonly>                             
                          </fieldset>
                        </div>
                      
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="EmployeeName">Department: </label>
                                   <input id="Department" type="text" class="form-control" placeholder="Employee Department" readonly>
                          </fieldset>
                        </div>
                       
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="Section">Section: </label>
                                   <input id="Section" type="text" class="form-control" placeholder="Employee Section" readonly>
                          </fieldset>
                        </div>
                   </div>
                    <div class="row">
                          <div class="col-md-8">
                          <fieldset class="form-group">
                            <label for="LastName">Job Title: </label>
                                   <input id="Position" type="text" class="form-control" placeholder="Employee Job Title" readonly>
                          </fieldset>
                        </div>
                         <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="LastName">Salary Type: </label>
                                   <input id="SalaryType" type="text" class="form-control" placeholder="Salary Type" readonly>
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
                                      <a class="nav-item nav-link" id="nav-contribution-tab" data-toggle="tab" href="#nav-contribution" role="tab" aria-controls="nav-contribution" aria-selected="true" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"><i class='bx bx-wallet mr-1'></i> Gov. Premiums Information</a>
                                      <a class="nav-item nav-link" id="nav-rates-tab" data-toggle="tab" href="#nav-rates" role="tab" aria-controls="nav-rates" aria-selected="false" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"> <i class='bx bx-calculator mr-1'></i>Rate History</a>
                                      <a class="nav-item nav-link" id="nav-loan-tab" data-toggle="tab" href="#nav-loan" role="tab" aria-controls="nav-loan" aria-selected="false" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"> <i class='bx bx-money mr-1'></i> Loan History</a>
                                      <a class="nav-item nav-link" id="nav-allowances-tab" data-toggle="tab" href="#nav-allowances" role="tab" aria-controls="nav-allowances" aria-selected="false" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"><i class='bx bx-detail mr-1'></i> Allowance Information</a>
                                </div>
                          </nav>
                          <div class="tab-content" id="nav-tabContent">
                         
                            <div class="tab-pane fade show" id="nav-contribution" role="tabpanel" aria-labelledby="nav-contribution-tab">
                              <!-- Contribution Information -->
                                   <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31);padding-bottom: 15px;padding-top: 15px;">
                                        <div id="tblList_wrapper" class="dataTables_wrapper no-footer">   

                                         <div class="col-md-12">
                                             <fieldset class="fieldset-border">
                                                <legend class="legend-text">| TIN/SSS/PHIC Information |</legend>
                                                   <div class="row">
                                                     <div class="col-md-4">
                                                    <fieldset class="form-group">
                                                        <label for="PayrollPeriodName">TIN No:</label></span>   
                                                         <input id="TINNo" type="text" class="form-control" placeholder="TIN No" readonly>                             
                                                      </fieldset>
                                                    </div>
                                                  
                                                    <div class="col-md-4">
                                                      <fieldset class="form-group">
                                                        <label for="EmployeeName">SSS No: </label>
                                                               <input id="SSSNo" type="text" class="form-control" placeholder="SSS No" readonly>
                                                      </fieldset>
                                                    </div>
                                                                                                      
                                                    <div class="col-md-4">
                                                      <fieldset class="form-group">
                                                        <label for="EmployeeName">PHIC No: </label>
                                                               <input id="PHICNo" type="text" class="form-control" placeholder="PHIC No" readonly>
                                                      </fieldset>
                                                    </div>
                                                   </div>       
                                              </fieldset>
                                            </div>  

                                         <div class="col-md-12">
                                             <fieldset class="fieldset-border">
                                                <legend class="legend-text">| PAG-IBIG Information |</legend>
                                                   <div class="row">
                                                     <div class="col-md-3">
                                                    <fieldset class="form-group">
                                                        <label for="PayrollPeriodName">PAGIBIG No:</label></span>   
                                                         <input id="PAGIBIGNo" type="text" class="form-control" placeholder="PAGIBIG No" readonly>                             
                                                      </fieldset>
                                                    </div>
                                                  
                                                    <div class="col-md-3">
                                                      <fieldset class="form-group">
                                                        <label for="PAGIBIGER">PAG-IBIG ER Current Contribution: </label>
                                                               <input id="PAGIBIGER" type="text" class="form-control" placeholder="PAG-IBIG ER Contribution" readonly>
                                                      </fieldset>
                                                    </div>
                                                   
                                                    <div class="col-md-3">
                                                      <fieldset class="form-group">
                                                        <label for="PAGIBIGEE">PAGIBIG EE Current Contribution: </label>
                                                               <input id="PAGIBIGEE" type="text" class="form-control" placeholder="PAG-IBIG EE Contribution" readonly>
                                                      </fieldset>
                                                    </div>

                                                    <div class="col-md-3">
                                                        @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload || $Allow_Edit_Update)
                                                          <fieldset class="form-group">                                                        
                                                             <button id="btnSaveNewHDF" type="button" class="btn btn-primary ml-1" onclick="SetLoadHDMF()" style="margin-top: 28px;width: 90%;font-size: 13px;">
                                                                <i class="bx bx-check d-block d-sm-none"></i>
                                                                <span class="d-none d-sm-block"> <i class="bx bx-plus"></i> Set New Contribution</span>
                                                              </button> 
                                                          </fieldset>
                                                         @endif  
                                                    </div>                                                                                                                                                         
                                                   </div>                                                 
                                              </fieldset>
                                            </div>  
                                       
                                           <div class="col-md-12">
                                                 <fieldset class="fieldset-border">
                                                    <legend class="legend-text">| PAG-IBIG MP2 Information |</legend>
                                                       <div class="row">
                                                         <div class="col-md-3">
                                                        <fieldset class="form-group">
                                                            <label for="MP2No">MP2 No:</label></span>   
                                                             <input id="MP2No" type="text" class="form-control" placeholder="MP2 No" readonly>                             
                                                          </fieldset>
                                                        </div>
                                                      
                                                        <div class="col-md-3">
                                                          <fieldset class="form-group">
                                                            <label for="MP2DedcutionAmount">Current MP2 Deduction Amount </label>
                                                                   <input id="MP2DedcutionAmount" type="text" class="form-control" placeholder="MP2 Deduction Amount" readonly>
                                                          </fieldset>
                                                        </div>

                                                      <div class="col-md-3">
                                                          <fieldset class="form-group">
                                                            <label for="MP2FrequencySchedule">Frequency Schedule: </label>
                                                                   <input id="MP2FrequencySchedule" type="text" class="form-control" placeholder="MP2 Frequency Schedule" readonly>
                                                          </fieldset>
                                                        </div>
                                                        
                                                        <div class="col-md-3">
                                                            @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload || $Allow_Edit_Update)
                                                            <label for="btnSaveNewMP2">&nbsp;</label>
                                                            <button id="btnSaveNewMP2" type="button" class="btn btn-primary ml-1" onclick="SetLoadMP2()" style="margin-top: 28px;width: 90%;font-size: 13px">
                                                               <i class="bx bx-check d-block d-sm-none"></i>
                                                               <span class="d-none d-sm-block"> <i class="bx bx-plus"></i> Set New Contribution</span>
                                                            </button>  
                                                            @endif
                                                        </div>
                                                                                                                                                           
                                                       </div>       
                                                  </fieldset>
                                              </div>                                                                                                                                                                                                  
                                          </div>
                                     </div>                                                                
                                  <!--End Contribution Information -->       
                            </div>

                            <div class="tab-pane fade" id="nav-rates" role="tabpanel" aria-labelledby="nav-rates-tab">                      
                              <!-- Rates Information -->
                                  <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31);padding-bottom: 15px;padding-top: 15px;">
                                        <div id="tblList_wrapper" class="dataTables_wrapper no-footer">                                              
                                                    <div class="row">                                                       
                                                            <div class="col-md-12 mb-1">
                                                                <fieldset>
                                                                    <div class="input-group">                      
                                                                        <input type="text" class="form-control ratesearchtext" placeholder="Search Here..">
                                                                        <button id="btnSearchRate" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border">
                                                                            <i class="bx bx-search"></i>
                                                                        </button>

                                                                      @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload || $Allow_Edit_Update)                                                               
                                                                          <button id="btnSetNewRate" type="button" class="btn btn-primary ml-1" onclick="ViewRate()">
                                                                             <i class="bx bx-check d-block d-sm-none"></i>
                                                                             <span class="d-none d-sm-block">Set New Rate</span>
                                                                        </button> 

                                                                      @endif   

                                                                    </div>
                                                                </fieldset>
                                                             </div>             
                                                             <div id="style-2" class="table-responsive col-md-12 table_default_half_height">
                                                              <table id="tblEmployee-Rate-List" class="table zero-configuration complex-headers border" style="font-size: 12px;">
                                                                 <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th style="color: white;">EFFECTIVITY DATE</th>
                                                                        <th style="color: white;">MONTHLY RATE</th>
                                                                        <th style="color: white;">DAILY RATE</th>
                                                                        <th style="color: white;">HOURLY RATE</th>                                                         
                                                                    </tr>
                                                                </thead> 
                                                              </table>
                                                      </div>                                                                                                             
                                                </div>
                                            </div>
                                    </div>
                               
                               <!--End Rates Information --> 
                            </div>

                             <div class="tab-pane fade" id="nav-loan" role="tabpanel" aria-labelledby="nav-loan-tab">                    
                                <!-- Loan Information -->
                                  <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31);;padding-bottom: 15px;padding-top: 15px;">
                                        <div id="tblList_wrapper" class="dataTables_wrapper no-footer">
                                              <div class="col-md-12">                                                                                                        
                                                      <div class="col-md-12 mb-1">
                                                        <fieldset>
                                                            <div class="input-group">                      
                                                                <input type="text" class="form-control loansearchtext" placeholder="Search Here..">
                                                                <button id="btnSearchLoan" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border">
                                                                    <i class="bx bx-search"></i>
                                                                </button>
                                                                                                
                                                            </div>
                                                        </fieldset>
                                                     </div>
                                             
                                                   <div id="style-2" class="table-responsive col-md-12 table_default_half_height">
                                                      <table id="tblEmployee-Loan-List" class="table zero-configuration complex-headers border" style="font-size: 12px;">
                                                         <thead>
                                                            <tr>
                                                                <th></th>                                                                                                    
                                                                <th style="color: white;">EMP. ID</th>
                                                                <th style="color: white;">EMPLOYEE NAME</th>
                                                                <th style="color: white;">CODE</th>
                                                                <th style="color: white;">DESCRIPTION</th>
                                                                <th style="color: white;">INTEREST AMOUNT</th>
                                                                <th style="color: white;">LOAN AMOUNT</th>
                                                                <th style="color: white;">TOTAL LOAN</th>
                                                                <th style="color: white;">AMORT. AMOUNT</th>
                                                                <th style="color: white;">STATUS</th>
                                                            </tr>
                                                        </thead> 
                                                      </table>
                                                     </div>                                                        
                                                </div>                        
                                          </div>
                                    </div>                               
                               <!--End Loan Information --> 
                            </div>

                            <div class="tab-pane fade" id="nav-allowances" role="tabpanel" aria-labelledby="nav-allowances-tab">                    
                                <!-- Allowances Information -->
                                <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31);;padding-bottom: 15px;padding-top: 15px;">
                                        <div id="tblList_wrapper" class="dataTables_wrapper no-footer">
                                                  <div class="col-md-12">
                                                                   
                                                    <div id="style-2" class="table-responsive col-md-12 table_default_half_height">
                                                      <table id="tblEmployee-Allowance-List" class="table zero-configuration complex-headers border" style="font-size: 12px;">
                                                         <thead>
                                                            <tr>
                                                                <th></th>                                                                                                                                                                    
                                                                <th style="color: white;">CODE</th>
                                                                <th style="color: white;">DESCRIPTION</th>                                                                                                                                
                                                                <th style="color: white;">AMOUNT </th>    
                                                                <th style="color: white;">FREQUENCY SCHEDULE</th>                                                                                                                        
                                                                <th></th> 
                                                            </tr>
                                                        </thead> 
                                                         <tbody id="tblAllowanceList" style="font-size: 12px;">
                                                         </tbody>
                                                      </table>
                                                     </div> 
                
                                                  </div>                        
                                              </div>
                                 </div>                               
                                 
                                 <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-2"></div>

                                    <div class="col-md-2">
                                        @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload || $Allow_Edit_Update)
                                        <label for="btnSaveEmployeeAllowance">&nbsp;</label>
                                       <button id="btnSaveEmployeeAllowance" type="button" class="btn btn-primary ml-1" onclick="SetAllowance()" style="margin-top: 15px;margin-bottom: 15px;width: 90%;">
                                         <i class="bx bx-check d-block d-sm-none"></i>
                                          <span class="d-none d-sm-block"> <span class="d-none d-sm-block"> <i class="bx bx-save mr-1" style="font-size: 21px;"></i> Save</span> 
                                        </button>  
                                        @endif
                                    </div>
                                 </div>
                                  
                               <!--End Allowances Information --> 
                              </div>
                          </div>
                        </div>
                    </div>
                    <!-- End Tab  -->  
                                      
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

     <!-- MODAL -->
    <div id="salary-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Employee Salary Rate & History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">                 
                 <input type="hidden" class="EmployeeID" value="0" readonly> 
                  
                 <fieldset class="fieldset-border ">
                     <legend class="legend-text"> | Employee Details |</legend>
                    <div class="row">
                         <div class="col-md-3">
                        <fieldset class="form-group">
                            <label for="PayrollPeriodName">Employee No:</label></span>   
                             <input type="text" class="form-control EmployeeNo" placeholder="Employee No" readonly>                             
                          </fieldset>
                        </div>
                      
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="EmployeeName">First Name: </label>
                                   <input type="text" class="form-control FirstName" placeholder="Employee Name" readonly>
                          </fieldset>
                        </div>
                       
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="LastName">Last Name: </label>
                                   <input type="text" class="form-control LastName" placeholder="Employee Name" readonly>
                          </fieldset>
                        </div>

                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="EmployeeName">Middle Name: </label>
                                   <input type="text" class="form-control MiddleName" placeholder="Employee Name" readonly>
                          </fieldset>
                        </div>                
                   </div>
                </fieldset>

               <hr>
              <div class="col-md-12">
                 <div class="card">   
                        <div class="card-header" style="padding: 13px 32px 8px 6px;">
                           <h4 class="card-title">Employee Rate List</h4>
                        </div>                             
                   </div>  
                </div>                         
                   <br>
                   <div class="modal-footer">
                           
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>              
                  </div>            
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

    <!-- MODAL -->
    <div id="new-rate-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Set New Employee Rate</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                 <input type="hidden" id="EmployeeRateID" value="0" readonly> 
                 <!-- <input type="hidden" class="EmployeeID" value="0" readonly>  -->
                 
                   <div class="row">
                       <div class="col-md-6">
                          <fieldset class="form-group">
                            <label for="Status">Effectivity Date: <span class="required_field">*</span></label>
                             <div class="div-percent">
                                   <input id="EffectivityDate" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" ><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;" onclick="LoadCalendar('EffectivityDate')"></i> </span>
                                </div>
                          </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="form-group">
                            <label for="EmployeeName">New Monthly Rate: <span class="required_field">*</span></label>
                                   <input id="MonthlyRate" type="text" class="form-control DecimalOnly" autocomplete="off" placeholder="Employee New Monthly Rate">
                             </fieldset>
                           </div>                 
                    </div>
                    
                    <div class="row">  
                        <div class="col-md-6">
                          <fieldset class="form-group">
                            <label for="EmployeeName">New Daily Rate: <span class="required_field">*</span></label>
                                   <input id="DailyRate" type="text" class="form-control DecimalOnly" autocomplete="off" placeholder="Employee New Daily Rate">
                          </fieldset>
                        </div>
                       
                        <div class="col-md-6">
                          <fieldset class="form-group">
                            <label for="LastName">New Hourly Rate: <span class="required_field">*</span></label>
                                   <input id="HourlyRate" type="text" class="form-control DecimalOnly" autocomplete="off" placeholder="Employee New Hourly Rate">
                          </fieldset>
                        </div>
                       </div>

                     <div class="row">
                        <div class="form-group" style="width:100%">                             
                            <label for="RateRemarks">Remarks:</label>
                            <textarea id="RateRemarks" class="form-control" rows="4"></textarea>                           
                       </div>
                    </div>  

                  </div>
                           
                  <div class="modal-footer" style="margin-right:10px;">

                        <button id="btnSaveNewRate" type="button" class="btn btn-primary ml-1" onclick="SaveNewRateRecord()">
                          <i class="bx bx-check d-block d-sm-none"></i>
                          <span class="d-none d-sm-block"> <i class='bx bx-save mr-1' style="font-size: 21px;"></i> Save</span>
                       </button>
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>              
                  </div> 

                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

    <!-- MODAL -->
    <div id="new-hdmf-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Set New PAG-IBIG Contribution</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                 <input type="hidden" class="EmployeeID" value="0" readonly>                                                            
                    <div class="row">  
                        <div class="col-md-6">
                          <fieldset class="form-group">
                            <label for="HDMFER">New PAG-IBIG ER Contribution: <span class="required_field">*</span></label>
                                <input id="HDMFER" type="text" class="form-control DecimalOnly" autocomplete="off" placeholder="HDMF ER Contribution">
                          </fieldset>
                        </div>
                       
                        <div class="col-md-6">
                          <fieldset class="form-group">
                            <label for="HDMFEE">New PAG-IBIG EE Contribution: <span class="required_field">*</span></label>
                               <input id="HDMFEE" type="text" class="form-control DecimalOnly" autocomplete="off" placeholder="HDMF EE Contribution">
                          </fieldset>
                        </div>
                       </div>

                  </div>
                           
                  <div class="modal-footer" style="margin-right:10px;">

                        <button id="btnSaveHDMF" type="button" class="btn btn-primary ml-1" onclick="SaveNewHDMFRecord()">
                          <i class="bx bx-check d-block d-sm-none"></i>
                          <span class="d-none d-sm-block"> <i class='bx bx-save mr-1' style="font-size: 21px;"></i> Save</span>
                       </button>
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>              
                  </div> 

                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

    <!-- MODAL -->
    <div id="new-mp2-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title white-color"> Set New PAG-IBIG MP2 Contribution</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                 <input type="hidden" class="EmployeeID" value="0" readonly>                                                            


                   <div class="row">  
                        <div class="col-md-12">
                          <fieldset class="form-group">
                            <label for="MP2AccountNo">MP2 Account No: <span class="required_field">*</span></label>
                                <input id="MP2AccountNo" type="text" class="form-control" autocomplete="off" placeholder="MP2 Account No">
                          </fieldset>
                        </div>
                   </div>
                        
                    <div class="row">  
                        <div class="col-md-6">
                          <fieldset class="form-group">
                            <label for="MP2ContributionAmount">New MP2 Contribution: <span class="required_field">*</span></label>
                                <input id="MP2ContributionAmount" type="text" class="form-control DecimalOnly" autocomplete="off" placeholder="MP2 Contribution Amount">
                          </fieldset>
                        </div>
                       
                        <div class="col-md-6">
                              <fieldset class="form-group">
                                <label class="spnReleaseType" for="Status">Frequency: </label> <span class="required_field" style="font-weight: 600;">*</span>
                                <div class="form-group">
                                    <select id="MP2Frequency" class="form-control">
                                       <option value="">Please Select</option>
                                        <option value="{{ config('app.RELEASE_1ST_HALF_ID') }}">1ST HALF</option>
                                        <option value="{{ config('app.RELEASE_2ND_HALF_ID') }}">2ND HALF</option>
                                        <option value="{{ config('app.RELEASE_EVERY_CUTOFF_ID') }}">EVERY CUTOFF</option>                                        
                                    </select>
                                </div>
                            </fieldset>                        
                        </div>
                       </div>
                  </div>
                           
                  <div class="modal-footer" style="margin-right:10px;">
                        <button id="btnSaveMP2" type="button" class="btn btn-primary ml-1" onclick="SaveNewMP2Record()">
                          <i class="bx bx-check d-block d-sm-none"></i>
                          <span class="d-none d-sm-block"> <i class='bx bx-save mr-1' style="font-size: 21px;"></i> Save</span>
                       </button>
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>              
                  </div> 

                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

   <!-- MODAL -->
    <div id="update-mp2-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Update MP2 Employee Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">

                 <input type="hidden" id="MP2TempID" value="0" readonly> 
                 <input type="hidden" class="EmployeeID" value="0" readonly> 
                  
                  <fieldset class="fieldset-border ">
                     <legend class="legend-text"> | Employee Details |</legend>
                    <div class="row">
                         <div class="col-md-3">
                        <fieldset class="form-group">
                            <label for="PayrollPeriodName">Employee No:</label></span>   
                             <input type="text" class="form-control UpdateEmployeeNo" placeholder="Employee No" readonly>                             
                          </fieldset>
                        </div>
                      
                        <div class="col-md-9">
                          <fieldset class="form-group">
                           <label for="EmployeeName">Employee Name: <span class="required_field">* </span></label><span class="search-txt">(Type & search from the list)</span>
                             <div class="div-percent">                                   
                                   <input type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input UpdateEmployeeName" autocomplete="off" data-complete-type="employee" placeholder="Employee Name"><span class='percent-sign'> <i class="bx bx-trash" style="line-height: 21px;cursor:pointer;" onclick="ClearEmployeeRateModal()"></i> </span>
                                </div> 
                          </fieldset>
                        </div>                                                   
                   </div>
                </fieldset>

                <fieldset class="fieldset-border ">
                    <legend class="legend-text">| Uploaded MP2 Information |</legend>
                       <div class="row">
                       
                         <div class="col-md-4">
                         <fieldset class="form-group">
                            <label for="MP2AccountNo"> MP2 Account  No:</label></span>   
                             <input id="UpdateMP2AccountNo" type="text" class="form-control" autocomplete="off" placeholder="MP2 Account No">                            
                          </fieldset>
                        </div>
                      
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="UpdateMP2Amount"> MP2 Deduction Amount: </label>
                                   <input id="UpdateMP2Amount" type="text" class="form-control DecimalOnly" autocomplete="off" placeholder="MP2 Deduction Amount">
                          </fieldset>
                        </div>
                       
                        <div class="col-md-4">
                          <fieldset class="form-group">
                           <label class="spnReleaseType" for="Status">Frequency Schedule: </label> <span class="required_field" style="font-weight: 600;">*</span>
                                <div class="form-group">
                                    <select id="UpdateMP2Frequency" class="form-control">
                                       <option value="">Please Select</option>
                                        <option value="{{ config('app.RELEASE_1ST_HALF_ID') }}">1ST HALF</option>
                                        <option value="{{ config('app.RELEASE_2ND_HALF_ID') }}">2ND HALF</option>
                                        <option value="{{ config('app.RELEASE_EVERY_CUTOFF_ID') }}">EVERY CUTOFF</option>                                        
                                    </select>
                                </div>
                          </fieldset>
                        </div>
                   </div>

                </fieldset> 

                <br>
                 <div class="modal-footer" style="margin-right:10px;">
                    <button id="btnUpdateMP2" type="button" class="btn btn-primary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block" onclick="UpdateUploadedMP2()"> <i class='bx bx-save mr-1' style="font-size: 21px;"></i> Save</span>
                    </button>
                       <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                     </button>             
                   </div>        
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

     <!-- MODAL -->
    <div id="history-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Employee Loan History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">                 
                 <input type="hidden" class="EmployeeID" value="0" readonly> 
                  
                 <fieldset class="fieldset-border ">
                     <legend class="legend-text"> | Employee Details |</legend>
                    <div class="row">
                         <div class="col-md-3">
                        <fieldset class="form-group">
                            <label for="PayrollPeriodName">Employee No:</label></span>   
                             <input type="text" class="form-control EmployeeNo" placeholder="Employee No" readonly>                             
                          </fieldset>
                        </div>
                      
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="EmployeeName">First Name: </label>
                                   <input type="text" class="form-control FirstName" placeholder="Employee Name" readonly>
                          </fieldset>
                        </div>
                       
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="LastName">Last Name: </label>
                                   <input type="text" class="form-control LastName" placeholder="Employee Name" readonly>
                          </fieldset>
                        </div>

                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="EmployeeName">Middle Name: </label>
                                   <input type="text" class="form-control MiddleName" placeholder="Employee Name" readonly>
                          </fieldset>
                        </div>                
                   </div>
                </fieldset>

               <hr>

                   <br>
                  <div class="modal-footer" style="margin-right:10px;">                           
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>              
                  </div>            
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

     <!-- MODAL -->
    <div id="update-rate-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Update Rate Employee Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">

                 <input type="hidden" id="RateTempID" value="0" readonly> 
                 <input type="hidden" class="EmployeeID" value="0" readonly> 
                  
                  <fieldset class="fieldset-border ">
                     <legend class="legend-text"> | Employee Details |</legend>
                    <div class="row">
                         <div class="col-md-3">
                        <fieldset class="form-group">
                            <label for="PayrollPeriodName">Employee No:</label></span>   
                             <input type="text" id="" class="form-control UpdateEmployeeNo" placeholder="Employee No" readonly>                             
                          </fieldset>
                        </div>
                      
                        <div class="col-md-9">
                          <fieldset class="form-group">
                           <label for="EmployeeName">Employee Name: <span class="required_field">* </span></label><span class="search-txt">(Type & search from the list)</span>
                             <div class="div-percent">                                   
                                   <input type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input UpdateEmployeeName" autocomplete="off" data-complete-type="employee" placeholder="Employee Name"><span class='percent-sign'> <i class="bx bx-trash" style="line-height: 21px;cursor:pointer;" onclick="ClearEmployeeRateModal()"></i> </span>
                                </div> 
                          </fieldset>
                        </div>                                                   
                   </div>
                </fieldset>

                <fieldset class="fieldset-border ">
                    <legend class="legend-text">| Uploaded Rates Information |</legend>
                       <div class="row">
                         <div class="col-md-3">
                             <fieldset class="form-group">
                            <label for="Status">Effectivity Date: <span class="required_field">*</span></label>
                             <div class="div-percent">
                                   <input id="UpdateEffectivityDate" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off" ><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;"></i> </span>
                                </div>
                          </fieldset>
                        </div>
                         <div class="col-md-3">
                         <fieldset class="form-group">
                            <label for="PayrollPeriodName"> Monthly Rate:</label></span>   
                             <input id="UpdateMonthlyRate" type="text" class="form-control DecimalOnly" autocomplete="off" placeholder="Employee Monthly Rate">                            
                          </fieldset>
                        </div>
                      
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="EmployeeName"> Daily Rate: </label>
                                   <input id="UpdateDailyRate" type="text" class="form-control DecimalOnly" autocomplete="off" placeholder="Employee Daily Rate">
                          </fieldset>
                        </div>
                       
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="LastName"> Hourly Rate: </label>
                                   <input id="UpdateHourlyRate" type="text" class="form-control DecimalOnly" autocomplete="off"placeholder="Employee Hourly Rate">
                          </fieldset>
                        </div>
                   </div>

                    <div class="row">
                        <div class="form-group" style="width:100%">                             
                            <label for="UpdateRemarks">Remarks:</label>
                            <textarea id="UpdateRemarks" class="form-control" rows="4"></textarea>                           
                       </div>
                    </div>  

                </fieldset> 

                <br>
                  <div class="modal-footer" style="margin-right:10px;">
                    <button id="btnUpdateRates" type="button" class="btn btn-primary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block" onclick="UpdateUploadedRates()"> <i class='bx bx-save mr-1' style="font-size: 21px;"></i> Save</span>
                    </button>
                       <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                     </button>             
                   </div>        
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

     
     <!-- MODAL -->
    <div id="earning-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color">Employee Loan History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">                 
                 <input type="hidden" class="EmployeeID" value="0" readonly> 
                  
                 <fieldset class="fieldset-border ">
                     <legend class="legend-text"> | Employee Details |</legend>
                    <div class="row">
                         <div class="col-md-3">
                        <fieldset class="form-group">
                            <label for="PayrollPeriodName">Employee No:</label></span>   
                             <input type="text" class="form-control EmployeeNo" placeholder="Employee No" readonly>                             
                          </fieldset>
                        </div>
                      
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="EmployeeName">First Name: </label>
                                   <input type="text" class="form-control FirstName" placeholder="Employee Name" readonly>
                          </fieldset>
                        </div>
                       
                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="LastName">Last Name: </label>
                                   <input type="text" class="form-control LastName" placeholder="Employee Name" readonly>
                          </fieldset>
                        </div>

                        <div class="col-md-3">
                          <fieldset class="form-group">
                            <label for="EmployeeName">Middle Name: </label>
                                   <input type="text" class="form-control MiddleName" placeholder="Employee Name" readonly>
                          </fieldset>
                        </div>                
                   </div>
                </fieldset>

               <hr>
              <div class="col-md-12">
                 <div class="card">   
                    <div class="card-header" style="padding: 13px 32px 8px 6px;">
                       <h4 class="card-title">Employee Salary Earning History </h4>
                    </div>             
   
                      <div class="col-md-12 mb-1">
                        <fieldset>
                            <div class="input-group">                      
                                <input type="text" class="form-control earningsearchtext" placeholder="Search Here..">
                                <button id="btnSearchEarning" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border">
                                    <i class="bx bx-search"></i>
                                </button>
                                                                
                            </div>
                        </fieldset>
                     </div>
             
                   <div id="style-2" class="table-responsive col-md-12 table_default_half_height">
                      <table id="tblEmployee-Earning-List" class="table zero-configuration complex-headers border">
                         <thead>
                            <tr>
                                <th></th>                                
                                <th style="width:8% !important;">FREQUENCY</th>
                                <th style="width:8% !important;">EMPLOYEE ID</th>
                                <th style="width:20% !important;">EMPLOYEE NAME</th>
                                <th style="width:8% !important;">LOAN CODE</th>
                                <th style="width:30% !important;">LOAN NAME</th>
                                <th style="width:6% !important;">INTRST. AMNT.</th>
                                <th style="width:6% !important;">LOAN AMNT.</th>
                                <th style="width:6% !important;">TOTAL LOAN</th>
                                <th style="width:6% !important;">AMRT. AMNT.</th>
                                <th style="width:10% !important;">STATUS</th>
                            </tr>
                        </thead> 
                      </table>
                     </div> 
                   </div>  
                </div>                         
                   <br>
                  <div class="modal-footer" style="margin-right:10px;">                           
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal" style="margin-right: -10px;">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>              
                  </div>            
                </div>
            </div>
        </div>
    </div>  
    <!-- END MODAL -->

  <!--RATE EXCEL REVIEW MODAL -->
  <div id="rate-excel-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="left:10px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color"> Review Excel Rate Data: <span id="spnUploadedRateRecord">0</span> / <span id="spnExcelRateRecord">0</span> has uploaded from excel. </h5> 
                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>

                </div>
                <div class="modal-body">
                    <div class="table-responsive col-md-12 table_default_height">
                          <table id="tblList-Rate-Excel" class="table zero-configuration complex-headers border">
                             <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th style="color: white;">EMP. ID</th>
                                    <th style="color: white;">EMPLOYEE NAME</th>
                                    <th style="color: white;">EFFECTIVITY DATE</th>
                                    <th style="color: white;">NEW MONTHLY RATE</th>
                                    <th style="color: white;">NEW DAILY RATE</th>
                                    <th style="color: white;">NEW HOURLY RATE</th>                                    
                                    <th style="color: white;">UPLOAD STATUS</th>
                                </tr>
                            </thead> 
                    </table>            
                </div>

              <div id="divRatePaging" class="col-md-11" style="display: none;">   
               <hr style="margin-top:0px;margin-bottom:0px;">   
                <div style="width:110%;font-size: 11px;">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      <ul class="pagination ul-paging-rate scrollbar" style="overflow-x: auto;"></ul>
                     </div>
                    </div>
              </div>

                <div class="modal-footer" style="margin-top: -6px;"> 

                    <div style="float:left;width: 70%;text-align: left;">
                    <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 16px;">
                        -Highlight in Green color are duplicate Employee Rates record(s) in excel.
                    </p>
                    <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 16px;">
                       -Highlight in Red color are data with no Employee Record(s) base on Employee Code in excel.  
                    </p>
                    </div>

                    <div style="float:right;width: 30%;text-align: right;">

                    <button id="btnUploadFinalRateRecord" type="button" class="btn btn-primary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block" onclick="SaveFinalRateRecord()"><i class='bx bx-save mr-1' style="font-size: 21px;"></i> Save Final Record </span>
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


    <!--MP2 EXCEL REVIEW MODAL -->
   <div id="mp2-excel-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="left:10px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">
                <div class="modal-header">
                    <h5 class="modal-title white-color"> Review Excel MP2 Data: <span id="spnUploadedMP2Record">0</span> / <span id="spnExcelMP2Record">0</span> has uploaded from excel. </h5> 
                   <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive col-md-12 table_default_height">
                          <table id="tblList-MP2-Excel" class="table zero-configuration complex-headers border">
                             <thead>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th style="color: white;">EMP. ID</th>
                                    <th style="color: white;">EMPLOYEE NAME</th>
                                    <th style="color: white;">MP2 ACCOUNT NO</th>
                                    <th style="color: white;">MP2 AMOUNT DEDUCTION</th>
                                    <th style="color: white;">FREQUENCY SCHEDULE</th>                                    
                                    <th style="color: white;">UPLOAD STATUS</th>
                                </tr>
                            </thead> 
                    </table>            
                </div>

              <div id="divMP2Paging" class="col-md-11" style="display: none;">   
               <hr style="margin-top:0px;margin-bottom:0px;">   
                <div style="width:110%;font-size: 11px;">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      <ul class="pagination ul-paging-mp2 scrollbar" style="overflow-x: auto;"></ul>
                     </div>
                    </div>
              </div>

                <div class="modal-footer" style="margin-top: -6px;"> 

                    <div style="float:left;width: 70%;text-align: left;">
                    <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 16px;">
                        -Highlight in Green color are duplicate Employee MP2 record(s) in excel.
                    </p>  
                     <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 16px;">
                       -Highlight in Red color are data with no Employee Record(s) base on Employee Code in excel.  
                    </p>                  
                    </div>

                    <div style="float:right;width: 30%;text-align: right;">

                    <button id="btnUploadFinalMP2Record" type="button" class="btn btn-primary ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block" onclick="SaveFinalMP2Record()"><i class='bx bx-save mr-1' style="font-size: 21px;"></i> Save Final Record </span>
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
</section>

<script type="text/javascript">
   
    var itemCount=0;
    var IsAdmin="{{Session::get('IS_SUPER_ADMIN')}}";

    var IsAllowPrint="{{$Allow_View_Print_Export}}";
    var IsAllowView="{{$Allow_View_Print_Export}}";

    var IsAllowCreate="{{$Allow_Add_Create_Import_Upload}}";
    var IsAllowEdit="{{$Allow_Edit_Update}}";
    var IsAllowCancel="{{$Allow_Delete_Cancel}}";
    var IsAllowApprove="{{$Allow_Post_UnPost_Approve_UnApprove}}";

    //GET DATA RATE IN CSV UPLOADER
     const btnUploadRateSCV = document.getElementById('btnUploadRateCSV').addEventListener('click',()=> {
     if($('#RateExcelFile').get(0).files.length === 0) {
           showHasErrorMessage('','Browse and upload Employee New Rate Summary CSV File.');
           return;
       }
       else{

           clearMessageNotification();
           ClearRateTempUpload();

           var reader = new FileReader();
           reader.readAsText($('#RateExcelFile').get(0).files[0]);
           reader.onload = loadRateHandler;
       }
    })

    // Sync Employee Record
    const syncBtn = document.getElementById('sync-employee-btn');

    syncBtn.addEventListener('click', () => {

        syncBtn.disabled = true;
        syncBtn.innerHTML = "Syncing...";

        fetch('/sync-employees', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            $('#sync-employee-modal').modal('hide');

            syncBtn.disabled = false;
            syncBtn.innerHTML = "Start Sync";
        })
        .catch(err => {
            alert("Something went wrong.");
            syncBtn.disabled = false;
            syncBtn.innerHTML = "Start Sync";
            console.log(err);
        });
    });

    //GET DATA MP2 IN CSV UPLOADER
    const btnUploadMP2SCV = document.getElementById('btnUploadMP2CSV').addEventListener('click',()=> {
     if($('#MP2ExcelFile').get(0).files.length === 0) {
           showHasErrorMessage('','Browse and upload Employee MP2 Deduction Summary CSV File.');
           return;
       }
       else{

           clearMessageNotification();
           ClearMP2TempUpload();

           var reader = new FileReader();
           reader.readAsText($('#MP2ExcelFile').get(0).files[0]);
           reader.onload = loadMP2Handler;
       }
    })

 function loadRateHandler(event) {
    var csv = event.target.result;

    var vResult = csv.split("\n");
     getChunkRateCSVData(1,vResult);    

 }

  function loadMP2Handler(event) {
    var csv = event.target.result;

    var vResult = csv.split("\n");
     getChunkMP2CSVData(1,vResult);    

 }

function getChunkRateCSVData(vIndex,vResult){

    var pData = [];
    var intCntr = 0;
    var recPerBatch=100;

    var vDataLen = vResult.length;
    var vLimit = (vDataLen < (vIndex + recPerBatch) ? vDataLen : (vIndex + recPerBatch));

    intCntr = 0;
    for (x=vIndex; x < vLimit; x++){

        var vData = vResult[x].split(',');

        vEmployeeRateID = 0;

        vEffectivityDate=(vData[0]!=undefined ? vData[0] : '');
        if(vEffectivityDate=='END' || vEffectivityDate=='End' || vEffectivityDate=='end'){
            break;            
        } 

        vEmployeeNo=(vData[1]!=undefined ? vData[1] : '');
        vNewMonthlyRate=(vData[2]!=undefined ? parseFloat(vData[2],2) : 0);
        vNewDailyRate=(vData[3]!=undefined ? parseFloat(vData[3],2) : 0);
        vNewHourlyRate=(vData[4]!=undefined ? parseFloat(vData[4],2) : 0 );
        vRemarks=(vData[5]!=undefined ? vData[5] : '');

        vIsUploaded=1;
        vStatus='Inactive';

        pData[intCntr] = {
                EmployeeRateID: vEmployeeRateID,
                EffectivityDate: vEffectivityDate,
                EmpNo: vEmployeeNo,
                NewMonthlyRate: vNewMonthlyRate,
                NewDailyRate: vNewDailyRate,
                NewHourlyRate: vNewHourlyRate,
                Remarks: vRemarks,
                IsUploaded:vIsUploaded,
                Status: vStatus            
                
            };

        intCntr = intCntr + 1;
    }

      $("#spnTotalData").text(parseInt(vLimit-3) +'/'+ parseInt(vDataLen-2));

    if(pData.length > 0){

        //SAVE Batch of data
        $.ajax({
            type: "post",
            url: "{{ route('do-save-employee-temp-rate-batch') }}",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                TempRateDataItems: pData
            },
            dataType: "json",
            success: function(data){

                buttonOneClick("btnUploadRateCSV", "Upload CSV", false);

                if(data.Response =='Success'){
                  
                  $("#spnTotalData").hide();
                  $("#divLoader").hide();

                      $("#spnTotalData").text(parseInt(vLimit-1) +'/'+ parseInt(vDataLen-2));
                      getChunkRateCSVData(vIndex + pData.length,vResult);

                }else{
                      showHasErrorMessage('', data.ResponseMessage);
                    return; 
                }
            },
            error: function(data){

                $("#divLoader").hide();
                $("#divLoader1").hide();
                $("#spnTotalData").hide();

                buttonOneClick("btnUploadRateCSV", "Uploading...", false);
                console.log(data.responseText);
               
            },
            beforeSend:function(vData){

                $("#divLoader").show(); 
                $("#divLoader1").show(); 
                $("#spnTotalData").show();

                $("#spnLoadingLabel").text('Uploading...');                
                buttonOneClick("btnUploadRateCSV", "", true);
            }
        });

    }else{

        $("#divLoader").hide();
        $("#divLoader1").hide();
        $("#spnTotalData").hide();

        $("#upload-rates-modal").modal('hide');
        $(".spnLoadingLabel").text('Loading...');

         getTempRateUploadedCount(vResult.length-2);         
         getRateTempRecordList(1)
         $("#rate-excel-modal").modal();
         
    }
}

function getChunkMP2CSVData(vIndex,vResult){

    var pData = [];
    var intCntr = 0;
    var recPerBatch=100;

    var vDataLen = vResult.length;
    var vLimit = (vDataLen < (vIndex + recPerBatch) ? vDataLen : (vIndex + recPerBatch));

    intCntr = 0;
    for (x=vIndex; x < vLimit; x++){

        var vData = vResult[x].split(',');

        vEmployeeMP2ID = 0;

        vEmployeeNo=(vData[0]!=undefined ? vData[0] : '');

        if(vEmployeeNo=='END' || vEmployeeNo=='End' || vEmployeeNo=='end' ){
            break;            
        } 

        vMP2AccountNo=(vData[1]!=undefined ? vData[1] : '');
        vMP2ADeductionAmount=(vData[2]!=undefined ? parseFloat(vData[2],2) : 0);
        vMP2Frequency=(vData[3]!=undefined ? vData[3] : '');
        
        vIsUploaded=1;
        vStatus='Inactive';

          pData[intCntr] = {
                EmployeeMP2ID: vEmployeeMP2ID,                
                EmpNo: vEmployeeNo,
                MP2AccountNo: vMP2AccountNo,
                MP2ADeductionAmount: vMP2ADeductionAmount,
                MP2Frequency: vMP2Frequency,                
                IsUploaded:vIsUploaded,
                Status: vStatus                        
            };        
        intCntr = intCntr + 1;
    }

   $("#spnTotalData").text(parseInt(vLimit-1) +'/'+ parseInt(vDataLen-2));

    if(pData.length > 0){

        //SAVE Batch of data
        $.ajax({
            type: "post",
            url: "{{ route('do-save-employee-temp-mp2-batch') }}",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                TempMP2DataItems: pData
            },
            dataType: "json",
            success: function(data){

                buttonOneClick("btnUploadMP2CSV", "Upload CSV", false);

                if(data.Response =='Success'){
                  
                  $("#spnTotalData").hide();
                  $("#divLoader").hide();

                    $("#spnTotalData").text(parseInt(vLimit-1) +'/'+ parseInt(vDataLen-2));
                     getChunkMP2CSVData(vIndex + pData.length,vResult);

                }else{
                      showHasErrorMessage('', data.ResponseMessage);
                    return; 
                }
            },
            error: function(data){

                $("#divLoader").hide();
                $("#divLoader1").hide();
                $("#spnTotalData").hide();

                buttonOneClick("btnUploadMP2CSV", "Uploading...", false);
                console.log(data.responseText);
               
            },
            beforeSend:function(vData){

                $("#divLoader").show(); 
                $("#divLoader1").show(); 
                $("#spnTotalData").show();

                $("#spnLoadingLabel").text('Uploading...');
                
                buttonOneClick("btnUploadMP2CSV", "", true);
            }
        });

    }else{
        
        $("#divLoader").hide();
        $("#divLoader1").hide();
        $("#spnTotalData").hide();

        $("#upload-mp2-modal").modal('hide');
        $("#spnLoadingLabel").text('Loading...');

         getTempMP2UploadedCount(vResult.length-2);
         getMP2TempRecordList(1)
         $("#mp2-excel-modal").modal();
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
            'paging'      : false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false,
            "order": [[ 3, "asc" ]]
        });

       $('#tblEmployee-Rate-List').DataTable( {
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ],

            'responsive': true,
            'autoWidth': false,
            'paging': false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : false,
            'info'        : false,
            'autoWidth'   : false,
            "order": [[5, "asc" ]]
        });

       $('#tblEmployee-Loan-List').DataTable( {
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ],

            'responsive': true,
            'autoWidth': false,
            'paging': false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : false,
            'info'        : false,
            'autoWidth'   : false,
            "order": [[5, "asc" ]]
        });


         $('#tblEmployee-Allowance-List').DataTable( {
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ],

            'responsive': true,
            'autoWidth': false,
            'paging': false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : false,
            'info'        : false,
            'autoWidth'   : false,
            "order": [[5, "asc" ]]
        });


       // $('#tblEmployee-Earning-List').DataTable( {
       //      "columnDefs": [
       //          {
       //              "targets": [ 0 ],
       //              "visible": false,
       //              "searchable": false
       //          }
       //      ],

       //      'responsive': true,
       //      'autoWidth': false,
       //      'paging': false,
       //      'lengthChange': false,
       //      'searching'   : false,
       //      'ordering'    : true,
       //      'info'        : false,
       //      'autoWidth'   : false,
       //      "order": [[5, "asc" ]]
       //  });

        $('#tblList-Rate-Excel').DataTable( {
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ],

            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                   UploadErrorMsg=aData[8];                    
                   if(UploadErrorMsg=="No Record"){
                      $(nRow).addClass('Error-Level');  
                    }else if(UploadErrorMsg=="Duplicate"){
                      $(nRow).addClass('Dupli-Level');
                    }else{
                      $(nRow).addClass('Normal-Level');
                    }                    
                },

            'responsive': true,
            'autoWidth': false,
            'paging': false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : false,
            'info'        : false,
            'autoWidth'   : false,
            "order": [[8, "desc" ]]
        });

        $('#tblList-MP2-Excel').DataTable( {
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ],

            "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                    UploadErrorMsg=aData[7];                    
                    if(UploadErrorMsg=="No Record"){
                      $(nRow).addClass('Error-Level');  
                    }else if(UploadErrorMsg=="Duplicate"){
                      $(nRow).addClass('Dupli-Level');
                    }else{
                      $(nRow).addClass('Normal-Level');
                    }
                    
                },

            'responsive': true,
            'autoWidth': false,
            'paging': false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : false,
            'info'        : false,
            'autoWidth'   : false,
            "order": [[7, "desc" ]]
        });

        //Load Initial Data
        $("#tblList").DataTable().clear().draw();
        getRecordList(intCurrentPage, '');

        isPageFirstLoad = false;

        //SET FULL ROW HIGHLIGHT        
        var tblList = $('#tblList').DataTable();        
        $('#tblList tbody').on('click', 'tr', function() {            
            tblList.$('tr.highlighted').removeClass('highlighted');        
            $(this).addClass('highlighted');
        });

        var tblRate = $('#tblList-Rate-Excel').DataTable();
        // Handle row click event
        $('#tblList-Rate-Excel tbody').on('click', 'tr', function() {            
            tblRate.$('tr.highlighted').removeClass('highlighted');        
            $(this).addClass('highlighted');
        });

         var tblMP2 = $('#tblList-MP2-Excel').DataTable();
        $('#tblList-MP2-Excel tbody').on('click', 'tr', function() {            
            tblMP2.$('tr.highlighted').removeClass('highlighted');        
            $(this).addClass('highlighted');
        });
        
    });
   
   // EMLOYEE
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
    
    //RATE SEARCH
    $("#btnSearchRate").click(function(){
        $("#tblEmployee-Rate-List").DataTable().clear().draw();
        intCurrentPage = 1;
        getEmployeeRateHistory($('.EmployeeID').val());
    });
    $('.ratesearchtext').on('keypress', function (e) {
        if(e.which === 13){
            $("#tblEmployee-Rate-List").DataTable().clear().draw();
            intCurrentPage = 1;
            getEmployeeRateHistory($('.EmployeeID').val());
        }
    });
    
    // LOAN SEARCH
    $("#btnSearchLoan").click(function(){
        $("#tblEmployee-Loan-List").DataTable().clear().draw();
        intCurrentPage = 1;
        getEmployeeLoanHistory(1, $('.EmployeeID').val(), $('.searchtext').val());
    });
      $('.loansearchtext').on('keypress', function (e) {
        if(e.which === 13){
            $("#tblEmployee-Loan-List").DataTable().clear().draw();
            intCurrentPage = 1;
            getEmployeeLoanHistory(1,$('.EmployeeID').val(), $('.ratesearchtext').val());
        }
    });

    //EARNING SEARCH
    $("#btnSearchEarning").click(function(){
        $("#tblEmployee-Earning-List").DataTable().clear().draw();
        intCurrentPage = 1;
        getEmployeeEarningHistory(1, $('.EmployeeID').val(), $('.searchtext').val());
    });
      $('.loansearchtext').on('keypress', function (e) {
        if(e.which === 13){
            $("#tblEmployee-Earning-List").DataTable().clear().draw();
            intCurrentPage = 1;
            getEmployeeEarningHistory(1,$('.EmployeeID').val(), $('.ratesearchtext').val());
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
                SearchText: $('.searchtext').val(),
                Status: $("#selSearchStatus").val(),
                Limit: vLimit,
                PageNo: vPageNo
            },
            url: "{{ route('get-employee-list') }}",
            dataType: "json",
            success: function(data){
                total_rec=data.TotalRecord;       
                LoadRecordList(data.EmployeeList);
                if(total_rec>0){
                     CreateEmployeePaging(total_rec,vLimit);  
                     if(total_rec>vLimit){
                        $("#divEmployeePaging").show(); 
                        $("#total-record").text(total_rec);
                        $("#paging_button_id"+vPageNo).css("background", "#0069d9");
                        $("#paging_button_id"+vPageNo).css("color", "#fff");
                     }else{
                        $("#divEmployeePaging").hide(); 
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

     function CreateEmployeePaging(vTotalRecord,vLimit){

       var i;
       paging_button="";
    
        limit=vLimit; //get limit
        totalcount=vTotalRecord; //get total count
        pages=Math.ceil(totalcount/limit); //get pages
      
            
          paging_button="<li class='paginate_button page-item previous' id='example2_previous'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getRecordList(1)'>First</a></li>"
          $(".ul-paging").append(paging_button);
       
       
        if (pages>1) {        
          for (i = 1; i <= pages; i++) {            
            paging_button="<li class='paginate_button page-item'><a href='javascript:void(0)' id='paging_button_id"+i+"' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getRecordList("+i+")'>"+i+"</a></li>"
             $(".ul-paging").append(paging_button);
          }
        }
          
       
        paging_button="<li class='paginate_button page-item next' id='example2_next'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getRecordList("+pages+",)'>Last</a></li>"
        $(".ul-paging").append(paging_button);
       
      
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
      tdID = vData.employee_id;
      tdAction="";

      if(IsAdmin==1 || IsAllowView==1){

        tdAction = "<div class='dropdown'>";


                       if(vData.Status==1){
                             tdAction = tdAction + "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:green;'></span> ";  
                             tdAction = tdAction +  "<div class='dropdown-menu dropdown-menu-right'>"; 
                         }else {
                            tdAction = tdAction + "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:red;'></span> ";
                            tdAction = tdAction +  "<div class='dropdown-menu dropdown-menu-right'>";                          
                         }

                                      
                     tdAction = tdAction +
                            "<a class='dropdown-item' href='javascript:void(0);' onclick='ViewRecord(" + vData.employee_id + ",false)'>"+
                                "<i class='bx bx-search-alt mr-1'></i> " +
                                "View Employee Information" +
                            "</a>" +
                    "</div>";   

        }
        
        tdEmployeeNo = "<span>" + vData.employee_number + "</span>";
        tdEmployeeName = "<span>" + vData.FullName + "</span>";

        tdSalaryType = "";
        if(vData.salary_type == 1){
            tdSalaryType += "<span> DAILY </span>";
        }else{
            tdSalaryType += "<span> MONHTLY </span>";
        }

        tdTINNo = "<span>" + vData.tin_number + "</span>";
        tdSSSNo = "<span>" + vData.sss_number + "</span>";
        tdPAGIBIGNo = "<span>" + vData.pagibig_number + "</span>";
        tdPHICNo = "<span>" + vData.philhealth_number + "</span>";

        tdDivision = "<span>" + vData.Division + "</span>";
        tdDepartment = "<span>" + vData.Department + "</span>";
        tdSection = "<span>" + vData.Section + "</span>";
        tdLocation = "<span>" + vData.BranchName + "</span>";

        tdMonthlyRates = "<span>" + FormatDecimal(vData.MonthlyRate,2) + "</span>";
        tdDailyRates = "<span>" + FormatDecimal(vData.DailyRate,2) + "</span>";
        tdHourlyRates = "<span>" + FormatDecimal(vData.HourlyRate,2) + "</span>";

        tdStatus = "";

        if(vData.Status == 1){
            tdStatus += "<span style='color:green;display:flex;'> <i class='bx bx-check-circle'></i> Active </span>";
        }else{
            tdStatus += "<span style='color:red;display:flex;'> <i class='bx bx-x-circle'></i> Inactive </span>";
        }

        //Check if record already listed
        var IsRecordExist = false;
        tblList.rows().every(function ( rowIdx, tableLoop, rowLoop ) {
            var rowData = this.data();
            if(rowData[0] == vData.employee_id){

                IsRecordExist = true;
                //Edit Row
                curData = tblList.row(rowIdx).data();
                curData[0] = tdID;
                curData[1] = tdAction;              
                curData[2] = tdEmployeeNo;
                curData[3] = tdEmployeeName;
                curData[4] = tdSalaryType;
                curData[5] = tdTINNo;
                curData[6] = tdSSSNo;
                curData[7] = tdPAGIBIGNo;
                curData[8] = tdPHICNo;    
                curData[9] = tdDivision; 
                curData[10] = tdDepartment; 
                curData[11] = tdSection; 
                curData[12] = tdLocation;
                curData[13] = tdMonthlyRates;     
                curData[14] = tdDailyRates;     
                curData[15] = tdHourlyRates;                                 
                curData[16] = tdStatus;

                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,
                    tdAction,
                    tdEmployeeNo,
                    tdEmployeeName,
                    tdSalaryType,
                    tdTINNo,
                    tdSSSNo,
                    tdPAGIBIGNo,
                    tdPHICNo,
                    tdDivision,
                    tdDepartment,
                    tdSection,   
                    tdLocation,
                    tdMonthlyRates,
                    tdDailyRates,
                    tdHourlyRates,
                    tdStatus
                ]).draw();          
        }
    }

    function SetLoadHDMF(){

        //SET NEW HDMF
        $("#HDMFER").css({"border":"#ccc 1px solid"});
        $("#HDMFEE").css({"border":"#ccc 1px solid"});

        $("#HDMFER").val('');
        $("#HDMFEE").val('');

        $("#new-hdmf-modal").modal();
    }

    function SetLoadMP2(){
               
        //SET NEW MP2
        $("#MP2AccountNo").css({"border":"#ccc 1px solid"});
        $("#MP2ContributionAmount").css({"border":"#ccc 1px solid"});
        $("#MP2Frequency").css({"border":"#ccc 1px solid"});
        
        $("#MP2ContributionAmount").val('');
        $("#MP2Frequency").val('').change();;

        $("#new-mp2-modal").modal();
    }

    function Clearfields(){

        $(".EmployeeID").val('0'); 
        $(".EmployeeNo").val(''); 
        
        // Employee
        $(".FirstName").val(''); 
        $(".LastName").val(''); 
        $(".MiddleName").val(''); 
       
       //Gov IDs
        $("#TINNo").val('');
        $("#SSSNo").val('');
        $("#PAGIBIGNo").val('');
        $("#PHICNo").val('');
        
        //Division etc
        $("#Location").val('');
        $("#Site").val('');

        $("#Division").val('');
        $("#Department").val('');
        $("#Section").val('');
    
        $("#btnSaveRecord").show();

    }

   function ClearRatefields(){

        //Rate
        $("#EmployeeRateID").val('0');
        $("#EffectivityDate").val('');
        $("#ViewEffectivityDate").val('');
        $("#MonthlyRate").val('');
        $("#ViewMonthlyRate").val('');
        $("#DailyRate").val('');
        $("#ViewDailyRate").val('');
        $("#HourlyRate").val('');
        $("#ViewHourlyRate").val('');
        $("#RateRemarks").val('');
        
        //Update Rate
        $("#RateTempID").val('0');
        $(".UpdateEmployeeName").val();
        $("#UpdateEffectivityDate").val('');
        $("#UpdateMonthlyRate").val(''); 
        $("#UpdateDailyRate").val(''); 
        $("#UpdateHourlyRate").val(''); 
        $("#UpdateRemarks").val(''); 
    
        $("#btnSaveRecord").show();
    }

    function ClearNewHDMFfields(){

        //New HDMF Contribution
        $("#HDMFER").val('');
        $("#HDMFEE").val('');

        resetNewHDMFTextBorderToNormal();

        $("#btnSaveHDMF").show();
    }

    function ClearNewMP2fields(){

        //New MP2 Contribution
        $("#MP2AccountNo").val('');
        $("#MP2ContributionAmount").val('');
        $("#MP2Frequency").val('').change();

        resetNewMP2TextBorderToNormal();

        $("#btnSaveMP2").show();
    }

    function EnabledDisbledText(vEnabled){


    $(".EmployeeNo").attr('disabled', vEnabled); 
    $(".EmployeeNo").attr('disabled', vEnabled); 
    $(".FirstName").attr('disabled', vEnabled); 
    $(".LastName").attr('disabled', vEnabled); 
    $(".MiddleName").attr('disabled', vEnabled); 

    $("#TINNo").attr('disabled', vEnabled); 
    $("#SSSNo").attr('disabled', vEnabled); 
    $("#PAGIBIGNo").attr('disabled', vEnabled); 
    $("#PHICNo").attr('disabled', vEnabled); 

    $("#Location").attr('disabled', vEnabled); 
    $("#Site").attr('disabled', vEnabled); 
    $("#Division").attr('disabled', vEnabled);
    $("#Department").attr('disabled', vEnabled);     
    $("#Section").attr('disabled', vEnabled); 

    $("#PAGIBIGER").attr('disabled', vEnabled); 
    $("#PAGIBIGEE").attr('disabled', vEnabled); 

    $("#MP2No").attr('disabled', vEnabled); 
    $("#MP2DedcutionAmount").attr('disabled', vEnabled); 
    $("#MP2FrequencySchedule").attr('disabled', vEnabled); 

    $("#Position").attr('disabled', vEnabled); 
    
   }


    function ViewRate(){
       
       $("#EmployeeRateID").val('0'); 
       $("#EffectivityDate").val(''); 
       $("#MonthlyRate").val(''); 
       $("#DailyRate").val(''); 
       $("#HourlyRate").val(''); 
       $("#RateRemarks").val(''); 

        $("#new-rate-modal").modal();
    }

    function UploadRecord(){
        $("#upload-modal").modal();
    }

    function ViewRecord(vRecordID){


      if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    EmployeeID: vRecordID
                },
                url: "{{ route('get-employee-info') }}",
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.EmployeeInfo != undefined){

                         Clearfields();
                         EnabledDisbledText(true);

                        $(".EmployeeID").val(data.EmployeeInfo.employee_id);
                        $(".EmployeeNo").val(data.EmployeeInfo.employee_number);

                        $(".FirstName").val(data.EmployeeInfo.first_name);
                        $(".LastName").val(data.EmployeeInfo.last_name);
                        $(".MiddleName").val(data.EmployeeInfo.middle_name);

                         if(data.EmployeeInfo.Status==1){
                           $("#dvInactive").hide();
                           $("#dvActive").show();
                         }else{
                            $("#dvActive").hide();
                            $("#dvInactive").show();
                         }                         
                         
                        $("#TINNo").val(data.EmployeeInfo.tin_number);
                        $("#SSSNo").val(data.EmployeeInfo.sss_number);
                        $("#PAGIBIGNo").val(data.EmployeeInfo.pagibig_number);
                        $("#PHICNo").val(data.EmployeeInfo.philhealth_number);

                        $("#Location").val(data.EmployeeInfo.BranchName);
                        $("#Site").val(data.EmployeeInfo.SiteName);
                        $("#Division").val(data.EmployeeInfo.Division);
                        $("#Department").val(data.EmployeeInfo.Department);
                        $("#Section").val(data.EmployeeInfo.Section);

                        $("#Position").val(data.EmployeeInfo.Position);

                        if(data.EmployeeInfo.salary_type==1){
                          $("#SalaryType").val('DAILY');
                        }else{
                          $("#SalaryType").val('MONTHLY'); 
                        }

                        $("#ViewEffectivityDate").val(data.EmployeeInfo.EffectivityDateFormat);
                        $("#ViewMonthlyRate").val(FormatDecimal(data.EmployeeInfo.MonthlyRate,2));
                        $("#ViewDailyRate").val(FormatDecimal(data.EmployeeInfo.DailyRate,2));
                        $("#ViewHourlyRate").val(FormatDecimal(data.EmployeeInfo.HourlyRate,2));

                        resetNewHDMFTextBorderToNormal();
                        $("#PAGIBIGER").val(data.EmployeeInfo.HDMFER);
                        $("#PAGIBIGEE").val(data.EmployeeInfo.HDMFEE);

                        resetNewMP2TextBorderToNormal();
                        $("#MP2No").val(data.EmployeeInfo.MP2No);
                        $("#MP2AccountNo").val(data.EmployeeInfo.MP2No);

                        if(data.EmployeeInfo.MP2Amount==0){
                          $("#MP2DedcutionAmount").val('');    
                        }else{
                          $("#MP2DedcutionAmount").val(data.EmployeeInfo.MP2Amount);    
                        }
                        
                        if(data.EmployeeInfo.MP2FrequencyID==0){
                           $("#MP2FrequencySchedule").val(''); 
                        }else if(data.EmployeeInfo.MP2FrequencyID==1){
                           $("#MP2FrequencySchedule").val('1ST HALF');
                        }else if(data.EmployeeInfo.MP2FrequencyID==2){
                           $("#MP2FrequencySchedule").val('2ND HALF');
                        }else if(data.EmployeeInfo.MP2FrequencyID==3){
                          $("#MP2FrequencySchedule").val('EVERY CUTOFF');                        
                        }
                        
                        $(".EmployeeNo").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $(".FirstName").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $(".LastName").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $(".MiddleName").attr("style", "border: 1px solid rgb(204, 204, 204) !important");

                        $("#TINNo").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#SSSNo").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#PAGIBIGNo").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#PHICNo").attr("style", "border: 1px solid rgb(204, 204, 204) !important");

                        $("#Location").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#Site").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#Division").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#Section").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#Department").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        
                        $("#PAGIBIGER").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#PAGIBIGEE").attr("style", "border: 1px solid rgb(204, 204, 204) !important");

                        $("#MP2No").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#MP2DedcutionAmount").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#MP2FrequencySchedule").attr("style", "border: 1px solid rgb(204, 204, 204) !important");

                        $("#Position").attr("style", "border: 1px solid rgb(204, 204, 204) !important");

                        ViewSalaryHistory(vRecordID);
                        ViewLoanHistory(vRecordID);
                        getEmployeeAllowanceList();
                        
                        $("#divLoader").hide();
                        $("#view-modal").modal();

                        $("#nav-contribution-tab").click();

                    }else{
                        $("#divLoader").hide();
                        showHasErrorMessage('',data.ResponseMessage);
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

    function ViewSalaryHistory(vRecordID){

        if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    EmployeeID: vRecordID
                },
                url: "{{ route('get-employee-info') }}",
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.EmployeeInfo != undefined){

                        ClearRatefields();

                        $(".EmployeeID").val(data.EmployeeInfo.employee_id);
                        $(".EmployeeNo").val(data.EmployeeInfo.employee_number);

                        $(".FirstName").val(data.EmployeeInfo.first_name);
                        $(".LastName").val(data.EmployeeInfo.last_name);
                        $(".MiddleName").val(data.EmployeeInfo.middle_name);

                        $("#MonthlyRate").val(FormatDecimal(data.EmployeeInfo.MonthlyRate,2));
                        $("#DailyRate").val(FormatDecimal(data.EmployeeInfo.DailyRate,2));
                        $("#HourlyRate").val(FormatDecimal(data.EmployeeInfo.HourlyRate,2));

                        getEmployeeRateHistory(data.EmployeeInfo.employee_id);                    
                        $("#divLoader").hide();                        

                    }else{
                        $("#divLoader").hide();
                        showHasErrorMessage('',data.ResponseMessage);
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

   function EditRate(vRecordID){

    if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    EmployeeRateID: vRecordID
                },
                url: "{{ route('get-employee-rate-info') }}",
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.EmployeeRateInfo != undefined){

                        Clearfields();

                        $("#EmployeeRateID").val(data.EmployeeRateInfo.EmployeeRateID);    
                        $(".EmployeeID").val(data.EmployeeRateInfo.employee_id);    

                        $("#EffectivityDate").val(data.EmployeeRateInfo.EffectivityDateFormat); 

                        $("#MonthlyRate").val(FormatDecimal(data.EmployeeRateInfo.MonthlyRate,2));
                        $("#DailyRate").val(FormatDecimal(data.EmployeeRateInfo.DailyRate,2));
                        $("#HourlyRate").val(FormatDecimal(data.EmployeeRateInfo.HourlyRate,2));

                        $("#RateRemarks").val(data.EmployeeRateInfo.Remarks);
                        
                        $("#divLoader").hide();
                        $("#new-rate-modal").modal();

                    }else{
                        $("#divLoader").hide();
                        showHasErrorMessage('',data.ResponseMessage);
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

  function getEmployeeRateHistory(vEmployeeID){

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SearchText: $(".ratesearchtext").val(),
                EmployeeID: vEmployeeID,
                Status: '',
                Limit: 0,
                PageNo: 0
            },
            url: "{{ route('get-employee-rate-list') }}",
            dataType: "json",
            success: function(data){
                 LoadEmployeeRateHistoryList(data.EmployeeRateList);
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

    function LoadEmployeeRateHistoryList(vList){

        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadEmployeeRateHistoryRow(vList[x]);
            }
        }
    }

    function LoadEmployeeRateHistoryRow(vData){

        var tblList = $("#tblEmployee-Rate-List").DataTable();
        tdID = vData.EmployeeRateID;

        tdAction="";

         if(IsAdmin==1 || IsAllowEdit==1){

          tdAction = "<div class='dropdown'>" + 
                        "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:#475F7B;'></span> " +
                        "<div class='dropdown-menu dropdown-menu-right'>" +

                           "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRate(" + vData.EmployeeRateID + ")'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Employee Rate" +
                            "</a>"+

                        "</div>"+
                    "</div>";

         }

        tdEffectivityDate = "<span>" + vData.EffectivityDateFormat + "</span>";
        tdMonthlyRate = "<span>" + FormatDecimal(vData.MonthlyRate,2) + "</span>";
        tdDailyRate = "<span>" + FormatDecimal(vData.DailyRate,2) + "</span>";
        tdHourlyRate = "<span>" + FormatDecimal(vData.HourlyRate,2) + "</span>";
                
      
        //Check if record already listed
        var IsRecordExist = false;
        tblList.rows().every(function ( rowIdx, tableLoop, rowLoop ) {
            var rowData = this.data();
            if(rowData[0] == vData.EmployeeRateID){

                IsRecordExist = true;
                //Edit Row
                curData = tblList.row(rowIdx).data();
                curData[0] = tdID;
                curData[1] = tdAction;
                curData[2] = tdEffectivityDate;
                curData[3] = tdMonthlyRate;
                curData[4] = tdDailyRate;
                curData[5] = tdHourlyRate;                                
                            
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                 tdID,
                 tdAction,
                 tdEffectivityDate,    
                 tdMonthlyRate,    
                 tdDailyRate,    
                 tdHourlyRate
                               
                ]).draw();          
        }
    }

    function resetTextBorderToNormal(){
        
        //SET NEW RATE
        $("#EffectivityDate").css({"border":"#ccc 1px solid"});
        $("#MonthlyRate").css({"border":"#ccc 1px solid"}); 
        $("#DailyRate").css({"border":"#ccc 1px solid"}); 
        $("#HourlyRate").css({"border":"#ccc 1px solid"}); 

        //SET NEW HDMF
        $("#HDMFER").css({"border":"#ccc 1px solid"});
        $("#HDMFEE").css({"border":"#ccc 1px solid"});
                
        //EDIT/SAVE RATE UPLOAD        
        $(".UpdateEmployeeName").css({"border":"#ccc 1px solid"});
        $("#UpdateEffectivityDate").css({"border":"#ccc 1px solid"});
        $("#UpdateMonthlyRate").css({"border":"#ccc 1px solid"}); 
        $("#UpdateDailyRate").css({"border":"#ccc 1px solid"}); 
        $("#UpdateHourlyRate").css({"border":"#ccc 1px solid"}); 
        
        //EDIT/SAVE MP2 UPLOAD     
        $("#UpdateMP2AccountNo").css({"border":"#ccc 1px solid"});
        $("#UpdateMP2Amount").css({"border":"#ccc 1px solid"}); 
        $("#UpdateMP2Frequency").css({"border":"#ccc 1px solid"});         
            
    }

    function resetNewHDMFTextBorderToNormal(){

        $("#HDMFER").css({"border":"#ccc 1px solid"}); 
        $("#HDMFEE").css({"border":"#ccc 1px solid"}); 
            
    }

   function resetNewMP2TextBorderToNormal(){

        $("#MP2AccountNo").css({"border":"#ccc 1px solid"}); 
        $("#MP2ContributionAmount").css({"border":"#ccc 1px solid"}); 
        $("#MP2Frequency").css({"border":"#ccc 1px solid"}); 
            
    }

    function SaveNewRateRecord(){

        var vEmployeeRateID = $("#EmployeeRateID").val();
        var vEmployeeID = $(".EmployeeID").val();

        var vEffectivityDate = $("#EffectivityDate").val();
        var vMonthlyRate = $("#MonthlyRate").val();
        var vDailyRate = $("#DailyRate").val();
        var vHourlyRate = $("#HourlyRate").val();

        resetTextBorderToNormal();

        if(vEffectivityDate=="") {
         showHasErrorMessage('EffectivityDate','Enter salary effectivity date.');
         return;  
       }

        if(vMonthlyRate=="" || vMonthlyRate<=0) {
         showHasErrorMessage('MonthlyRate','Enter employee new monthly rate.');
         return;  
       }

       if(vDailyRate=="" || vDailyRate<=0) {
         showHasErrorMessage('DailyRate','Enter employee new daily rate.');
         return;  
       }

       if(vHourlyRate=="" || vHourlyRate<=0 ) {
         showHasErrorMessage('HourlyRate','Enter employee new hourly rate.');
         return;  
       }
       
       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                EmployeeRateID: $("#EmployeeRateID").val(),
                EmployeeID: $(".EmployeeID").val(),
                EffectivityDate: $("#EffectivityDate").val(),
                MonthlyRate: $("#MonthlyRate").val(),
                DailyRate: $("#DailyRate").val(),
                HourlyRate: $("#HourlyRate").val(),
                RateRemarks: $("#RateRemarks").val()                                  
            },
            url: "{{ route('do-save-employee-rate') }}",
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveNewRate",  "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);            

                if(data.Response =='Success'){
                    $("#new-rate-modal").modal('hide');
                    toast('toast-success', data.ResponseMessage);
                                        
                     getEmployeeRateHistory(vEmployeeID);
                     LoadRecordRow(data.EmployeeInfo);
                }else{
                     toast('toast-error', data.ResponseMessage);
                     return;
                }
            },
            error: function(data){
                $("#divLoader").hide();
                buttonOneClick("btnSaveNewRate",  "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);                    
                console.log(data.responseText);               
            },
            beforeSend:function(vData){
                $("#divLoader").show();
                buttonOneClick("btnSaveNewRate", "", true);
            }
        });
    }

    function SaveNewHDMFRecord(){
        
        var vEmployeeID = $(".EmployeeID").val();

        var vHDMF_New_ER = $("#HDMFER").val();
        var vHDMF_New_EE = $("#HDMFEE").val();

        resetNewHDMFTextBorderToNormal();
      
        if(vHDMF_New_ER=="" || vHDMF_New_ER<=0) {
         showHasErrorMessage('HDMFER','Enter employee new HDMF ER contribution.');
         return;  
       }  

        if(vHDMF_New_EE=="" || vHDMF_New_EE<=0) {
         showHasErrorMessage('HDMFEE','Enter employer new HDMF ER contribution.');
         return;  
       }

       // UPDATE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",                
                EmployeeID: $(".EmployeeID").val(),                
                HDMF_New_EE: vHDMF_New_EE,
                HDMF_New_ER: vHDMF_New_ER                         
            },
            url: "{{ route('do-update-employee-new-hmdf-contribution') }}",
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveNewRate",  "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);            

                if(data.Response =='Success'){                    
                    $("#new-hdmf-modal").modal('hide');
                    toast('toast-success', data.ResponseMessage);                                        
                     ViewRecord(vEmployeeID);
                     ClearNewHDMFfields();
                     

                }else{
                     toast('toast-error', data.ResponseMessage);
                     return;
                }
            },
            error: function(data){
                $("#divLoader").hide();
                buttonOneClick("btnSaveNewRate",  "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);            
                console.log(data.responseText);               
            },
            beforeSend:function(vData){
                $("#divLoader").show();
                buttonOneClick("btnSaveNewRate", "", true);
            }
        }); 
    }

    function SaveNewMP2Record(){
                    
        var vEmployeeID = $(".EmployeeID").val();
        var vMP2AccountNo = $("#MP2AccountNo").val();
        var vMP2ContributionAmount = $("#MP2ContributionAmount").val();
        var vMP2Frequency = $("#MP2Frequency").val();
        
        resetNewMP2TextBorderToNormal();

         if(vMP2AccountNo=="") {
         showHasErrorMessage('MP2AccountNo','Enter employee MP2 Account No.');
         return;  
       }  
      
        if(vMP2ContributionAmount=="" || vMP2ContributionAmount<=0) {
         showHasErrorMessage('MP2ContributionAmount','Enter employee MP2 contribution amount.');
         return;  
       }  

        if(vMP2Frequency=="" ) {
         showHasErrorMessage('MP2Frequency','Select MP2 frequency from the list.');
         return;  
       }

       // UPDATE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",                
                EmployeeID: $(".EmployeeID").val(),                                
                MP2AccountNo: vMP2AccountNo,
                MP2ContributionAmount: vMP2ContributionAmount,                      
                MP2Frequency: vMP2Frequency,                      
            },
            url: "{{ route('do-save-employee-mp2-contribution-set-up') }}",
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveMP2",  "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);            

                if(data.Response =='Success'){                   
                     $("#new-mp2-modal").modal('hide');
                     toast('toast-success', data.ResponseMessage);                                        
                     ViewRecord(vEmployeeID);
                     ClearNewMP2fields();                    
                }else{
                     toast('toast-error', data.ResponseMessage);
                     return;
                }
            },
            error: function(data){
                $("#divLoader").hide();
                buttonOneClick("btnSaveMP2",  "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);            
                console.log(data.responseText);               
            },
            beforeSend:function(vData){
                $("#divLoader").show();
                buttonOneClick("btnSaveMP2", "", true);
            }
        }); 
    }

    function getMP2TempRecordList(vPageNo){

      $("#tblList-MP2-Excel").DataTable().clear().draw();
      $(".ul-paging-mp2 >.paginate_button").remove();       
      vLimit=20;

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                Status: 'Pending',
                Limit: vLimit,
                PageNo: vPageNo
            },
            url: "{{ route('get-employee-temp-mp2-list') }}",
            dataType: "json",
            success: function(data){
                
                 total_rec=data.TotalRecord;
                 LoadTempMP2RecordList(data.MP2TempList);
                 if(total_rec>0){
                     CreateTempEmployeeMP2Paging(total_rec,vLimit);  
                     if(total_rec>vLimit){
                        $("#divMP2Paging").show(); 
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

    function CreateTempEmployeeMP2Paging(vTotalRecord,vLimit){

       var i;
       paging_button="";
    
        limit=vLimit; //get limit
        totalcount=vTotalRecord; //get total count
        pages=Math.ceil(totalcount/limit); //get pages
      
       if (pages!=1) {     

          paging_button="<li class='paginate_button page-item previous' id='example2_previous'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getMP2TempRecordList(1)'>First</a></li>"
          $(".ul-paging-mp2").append(paging_button);
       }
       
        if (pages>1) {        
          for (i = 1; i <= pages; i++) {            
            paging_button="<li class='paginate_button page-item'><a href='javascript:void(0)' id='paging_button_id"+i+"' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getMP2TempRecordList("+i+")'>"+i+"</a></li>"
             $(".ul-paging-mp2").append(paging_button);
          }
        }
          
       if (pages!=1) {            
        paging_button="<li class='paginate_button page-item next' id='example2_next'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getMP2TempRecordList("+pages+",)'>Last</a></li>"
        $(".ul-paging-mp2").append(paging_button);
        }  
   }

   function LoadTempMP2RecordList(vList){

        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadTempMP2RecordRow(vList[x]);
            }
        }
    }

    function LoadTempMP2RecordRow(vData){

        var tblList = $("#tblList-MP2-Excel").DataTable();

        tdID = vData.EmployeeMP2ID;
        tdAction = "<div class='dropdown'>" + 
                        "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:#475F7B;'></span> " +
                        "<div class='dropdown-menu dropdown-menu-right'>";

                        if(vData.IsUploadError==2){
                               tdAction += "<a class='dropdown-item' href='javascript:void(0);' onclick='RemovedMP2Duplicate(" + vData.EmployeeMP2ID + ")'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Removed Duplicate" +
                            "</a>";
                        }

                        if(vData.IsUploadError==1){
                            if(IsAdmin==1 || IsAllowEdit==1){
                              tdAction += "<a class='dropdown-item' href='javascript:void(0);' onclick='UpdateMP2Information(" + vData.EmployeeMP2ID + ",true)'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Information" +
                              "</a>";
                            }else{
                                  tdAction = tdAction +
                                "<a class='dropdown-item' href='javascript:void(0);' onclick='UpdateMP2Information(" + vData.EmployeeMP2ID + ",false)'>"+
                                    "<i class='bx bx-edit-alt mr-1'></i> " +
                                    "View Information" +
                                "</a>";
                            }
                      }

                      if(vData.IsUploadError==0){
                         if(IsAdmin==1 || IsAllowEdit==1){
                              tdAction += "<a class='dropdown-item' href='javascript:void(0);' onclick='UpdateMP2Information(" + vData.EmployeeMP2ID + ",true)'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Information" +
                              "</a>";
                            }else{
                                  tdAction = tdAction +
                                "<a class='dropdown-item' href='javascript:void(0);' onclick='UpdateMP2Information(" + vData.EmployeeMP2ID + ",false)'>"+
                                    "<i class='bx bx-edit-alt mr-1'></i> " +
                                    "View Information" +
                                "</a>";
                            }
                        }  

                           tdAction += "</div>"+
                    "</div>";
        
        tdEmployeeNo = "<span>" + vData.employee_number + "</span>";
        tdEmployeeName = "<span>" + vData.FullName + "</span>";

        tdMP2AccountNo = "<span>" + vData.MP2AccountNo + "</span>";
        tdMP2Amount = "<span>" + FormatDecimal(vData.MP2Amount,2) + "</span>";

        tdFrequency='';
        if(vData.FrequencyID==1){
           tdFrequency = "<span> 1ST HALF </span>";
        }else if(vData.FrequencyID==2){
           tdFrequency = "<span> 2ND HALF </span>";
        }else if(vData.FrequencyID==3){
           tdFrequency = "<span> EVERY CUTOFF </span>";       
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
                curData[2] = tdEmployeeNo;
                curData[3] = tdEmployeeName;
                curData[4] = tdMP2AccountNo;
                curData[5] = tdMP2Amount;
                curData[6] = tdFrequency;                        
                curData[7] = tdIsUploadError;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                tdID,
                tdAction,
                tdEmployeeNo,
                tdEmployeeName,
                tdMP2AccountNo,
                tdMP2Amount,
                tdFrequency,                            
                tdIsUploadError
            ]).draw();          
        }
    }

      function SaveFinalMP2Record(){


      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}'

        },
        url: "{{ route('do-upload-save-employee-mp2') }}",
        dataType: "json",
        success: function(data){
              if(data.Response =='Success'){
                  showHasSuccessMessage(data.ResponseMessage);
                  getRecordList(1, $('.searchtext').val());
                  $("#divLoader").hide(); 
                  $("#mp2-excel-modal").modal('hide');
                return;
              }else{
                showHasErrorMessage('', data.ResponseMessage);
                $("#divLoader").hide(); 
                return; 
              }

              $("#spnLoadingLabel").text('Loading...');
        },
        error: function(data){ 
            console.log(data.responseText);
        },
        beforeSend:function(vData){
            $("#divLoader").show(); 
            $("#spnLoadingLabel").text('Saving...');
        }

    });
 }

  function getRateTempRecordList(vPageNo){

      $("#tblList-Rate-Excel").DataTable().clear().draw();
      $(".ul-paging-rate >.paginate_button").remove(); 
      
      vLimit=20;

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                Status: 'Pending',
                Limit: vLimit,
                PageNo: vPageNo
            },
            url: "{{ route('get-employee-temp-rate-list') }}",
            dataType: "json",
            success: function(data){

                 total_rec=data.TotalRecord;
                 LoadTempRateRecordList(data.RateTempList);
                 if(total_rec>0){
                     CreateTempEmployeeRatePaging(total_rec,vLimit);  
                     if(total_rec>vLimit){
                        $("#divRatePaging").show(); 
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

   function CreateTempEmployeeRatePaging(vTotalRecord,vLimit){

       var i;
       paging_button="";
    
        limit=vLimit; //get limit
        totalcount=vTotalRecord; //get total count
        pages=Math.ceil(totalcount/limit); //get pages
      
       if (pages!=1) {     

          paging_button="<li class='paginate_button page-item previous' id='example2_previous'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getRateTempRecordList(1)'>First</a></li>"
          $(".ul-paging-rate").append(paging_button);
       }
       
        if (pages>1) {        
          for (i = 1; i <= pages; i++) {            
            paging_button="<li class='paginate_button page-item'><a href='javascript:void(0)' id='paging_button_id"+i+"' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getRateTempRecordList("+i+")'>"+i+"</a></li>"
             $(".ul-paging-rate").append(paging_button);
          }
        }
          
       if (pages!=1) {            
        paging_button="<li class='paginate_button page-item next' id='example2_next'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getRateTempRecordList("+pages+",)'>Last</a></li>"
        $(".ul-paging-rate").append(paging_button);
        }
      
   }
  
    function LoadTempRateRecordList(vList){

        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadTempRateRecordRow(vList[x]);
            }
        }
    }

    function LoadTempRateRecordRow(vData){

        var tblList = $("#tblList-Rate-Excel").DataTable();

        tdID = vData.EmployeeRateID;
        tdAction = "<div class='dropdown'>"+

                        "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:#475F7B;'></span> " +
                        "<div class='dropdown-menu dropdown-menu-right'>";

                        if(vData.IsUploadError==2){
                            tdAction = tdAction +
                               "<a class='dropdown-item' href='javascript:void(0);' onclick='RemovedRateDuplicate(" + vData.EmployeeRateID + ")'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Removed Duplicate" +
                            "</a>";
                        }

                        if(vData.IsUploadError==1){
                            if(IsAdmin==1 || IsAllowEdit==1){
                            tdAction = tdAction +
                              "<a class='dropdown-item' href='javascript:void(0);' onclick='UpdateRateInformation(" + vData.EmployeeRateID + ",true)'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Information" +
                              "</a>";
                            }else{
                                  tdAction = tdAction +
                                "<a class='dropdown-item' href='javascript:void(0);' onclick='UpdateRateInformation(" + vData.EmployeeRateID + ",false)'>"+
                                    "<i class='bx bx-edit-alt mr-1'></i> " +
                                    "View Information" +
                                "</a>";
                            }
                      }

                      if(vData.IsUploadError==0){
                         if(IsAdmin==1 || IsAllowEdit==1){
                             tdAction = tdAction +
                             "<a class='dropdown-item' href='javascript:void(0);' onclick='UpdateRateInformation(" + vData.EmployeeRateID + ",true)'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Information" +
                              "</a>";
                            }else{
                                  tdAction = tdAction +
                                "<a class='dropdown-item' href='javascript:void(0);' onclick='UpdateRateInformation(" + vData.EmployeeRateID + ",false)'>"+
                                    "<i class='bx bx-edit-alt mr-1'></i> " +
                                    "View Information" +
                                "</a>";
                            }
                        }  

                           tdAction += "</div>"+
                    "</div>";
        
        tdEmployeeNo = "<span>" + vData.employee_number + "</span>";
        tdEmployeeName = "<span>" + vData.FullName + "</span>";

        tdEffectivityDate = "<span>" + vData.EffectivityDateFormat + "</span>";

        tdMonthlyRate = "<span>" + FormatDecimal(vData.MonthlyRate,2) + "</span>";
        tdDailyRate = "<span>" + FormatDecimal(vData.DailyRate,2) + "</span>";
        tdHourlyRate = "<span>" + FormatDecimal(vData.HourlyRate,2) + "</span>";
         
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
                curData[2] = tdEmployeeNo;
                curData[3] = tdEmployeeName;
                curData[4] = tdEffectivityDate;
                curData[5] = tdMonthlyRate;
                curData[6] = tdDailyRate;
                curData[7] = tdHourlyRate;                
                curData[8] = tdIsUploadError;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                tdID,
                tdAction,
                tdEmployeeNo,
                tdEmployeeName,
                tdEffectivityDate,
                tdMonthlyRate,
                tdDailyRate,
                tdHourlyRate,                
                tdIsUploadError
            ]).draw();          
        }
    }

    function ViewLoanHistory(vRecordID){

        if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    EmployeeID: vRecordID
                },
                url: "{{ route('get-employee-info') }}",
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.EmployeeInfo != undefined){
                        
                        $(".EmployeeID").val(data.EmployeeInfo.employee_id);
                        $(".EmployeeNo").val(data.EmployeeInfo.employee_number);

                        $(".FirstName").val(data.EmployeeInfo.first_name);
                        $(".LastName").val(data.EmployeeInfo.last_name);
                        $(".MiddleName").val(data.EmployeeInfo.middle_name);
  
                        getEmployeeLoanHistory(1,data.EmployeeInfo.employee_id,'');                    
                        $("#divLoader").hide();                        

                    }else{
                        $("#divLoader").hide();
                        showHasErrorMessage('',data.ResponseMessage);
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

  function getEmployeeLoanHistory(vPageNo,vEmployeeID){

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SearchText: $(".loansearchtext").val(),
                EmployeeID: vEmployeeID,
                Status: '',
                Limit: 0,
                PageNo: 0
            },
            url: "{{ route('get-employee-loan-history') }}",
            dataType: "json",
            success: function(data){
                 LoadEmployeeLoanHistoryList(data.EmployeeLoanHistory);
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

     function LoadEmployeeLoanHistoryList(vList){

        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadEmployeeLoanHistoryRow(vList[x]);
            }
        }
    }

    function LoadEmployeeLoanHistoryRow(vData){

        var tblList = $("#tblEmployee-Loan-List").DataTable();

        tdID = vData.ID;
        
        tdEmpNo = "<span>" + vData.EmployeeNumber + "</span>";
        tdEmpName = "<span>" + vData.FullName + "</span>";

        tdLoanTypeCode = "<span>" + vData.LoanTypeCode + "</span>";
        tdLoanTypeName = "<span>" + vData.LoanTypeName + "</span>";

        tdIntrstAmount = "<span>" + FormatDecimal(vData.InterestAmount,2) + "</span>";
        tdLoanAmount = "<span>" + FormatDecimal(vData.LoanAmount,2) + "</span>";
        tdTotalLoanAmount = "<span>" + FormatDecimal(vData.TotalLoanAmount,2) + "</span>";
        tdAmortizationAmount = "<span>" + FormatDecimal(vData.AmortizationAmount,2) + "</span>";

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
                curData[1] = tdEmpNo;
                curData[2] = tdEmpName;
                curData[3] = tdLoanTypeCode;
                curData[4] = tdLoanTypeName;
                curData[5] = tdIntrstAmount;
                curData[6] = tdLoanAmount;
                curData[7] = tdTotalLoanAmount;
                curData[8] = tdAmortizationAmount;
                curData[9] = tdStatus;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,                                        
                    tdEmpNo,
                    tdEmpName,
                    tdLoanTypeCode,
                    tdLoanTypeName,
                    tdIntrstAmount,
                    tdLoanAmount,
                    tdTotalLoanAmount,
                    tdAmortizationAmount,
                    tdStatus
                ]).draw();          
        }
    }
 
 // function ViewAccumulatedEarning(vRecordID){

 //   if(vRecordID > 0){
 //            $.ajax({
 //                type: "post",
 //                data: {
 //                    _token: '{{ csrf_token() }}',
 //                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
 //                    EmployeeID: vRecordID
 //                },
 //                url: "{{ route('get-employee-info') }}",
 //                dataType: "json",
 //                success: function(data){

 //                    if(data.Response =='Success' && data.EmployeeInfo != undefined){

 //                        Clearfields();

 //                        $(".EmployeeID").val(data.EmployeeInfo.employee_id);
 //                        $(".EmployeeNo").val(data.EmployeeInfo.employee_number);

 //                        $(".FirstName").val(data.EmployeeInfo.first_name);
 //                        $(".LastName").val(data.EmployeeInfo.last_name);
 //                        $(".MiddleName").val(data.EmployeeInfo.middle_name);
  
 //                        getEmployeeEarningHistory(1,data.EmployeeInfo.employee_id,'');                        
 //                        $("#divLoader").hide();
 //                        $("#earning-modal").modal();

 //                    }else{
 //                        $("#divLoader").hide();
 //                        showHasErrorMessage('',data.ResponseMessage);
 //                        return; 
 //                    }
 //                },
 //                error: function(data){
 //                    $("#divLoader").hide();
 //                    buttonOneClick("btnSaveRecord", "Save", false);                
 //                       console.log(data.responseText);                                   
 //                },
 //                beforeSend:function(vData){
 //                    $("#divLoader").show();
 //                    buttonOneClick("btnSaveRecord", "", false);
 //                }
 //          });
 //      }     
 // }

function getEmployeeEarningHistory(vPageNo,vEmployeeID,vSearchText){

      $("#tblEmployee-Earning-List").DataTable().clear().draw();
      $(".paginate_button").remove(); 
       vLimit=100;

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SearchText: vSearchText,
                EmployeeID: vEmployeeID,
                Status: '',
                Limit: vLimit,
                PageNo: vPageNo
            },
            url: "{{ route('get-employee-payroll-earning-history') }}",
            dataType: "json",
            success: function(data){

                 total_rec=data.TotalRecord;
                 LoadEmployeePayrollEarningHistoryList(data.EmployeeLoanHistory);

                 if(total_rec>0){
                     CreatePaging(total_rec,vLimit);  
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
                console.log(data.responseText);
            },
            beforeSend:function(vData){
                $("#divLoader").show(); 
            }
        });
    };

     function LoadEmployeePayrollEarningHistoryList(vList){

        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadEmployeeEarningHistoryRow(vList[x]);
            }
        }
    }

    function LoadEmployeeEarningHistoryRow(vData){

        var tblList = $("#tblEmployee-Earning-List").DataTable();

        tdID = vData.ID;
        
        tdCutOff = "";
        if(vData.CutOff == 1){
            tdCutOff += "<span>1ST HALF</span>";
        }else if(vData.CutOff == 2){
            tdCutOff += "<span>2ND HALF</spa-1n>";
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
                curData[1] = tdCutOff;
                curData[2] = tdEmpNo;
                curData[3] = tdEmpName;
                curData[4] = tdLoanTypeCode;
                curData[5] = tdLoanTypeName;
                curData[6] = tdIntrstAmount;
                curData[7] = tdLoanAmount;
                curData[8] = tdTotalLoanAmount;
                curData[9] = tdAmortizationAmount;
                curData[10] = tdStatus;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,                    
                    tdCutOff,
                    tdEmpNo,
                    tdEmpName,
                    tdLoanTypeCode,
                    tdLoanTypeName,
                    tdIntrstAmount,
                    tdLoanAmount,
                    tdTotalLoanAmount,
                    tdAmortizationAmount,
                    tdStatus
                ]).draw();          
        }
    }
 
function clearMessageNotification(){

  let toastMain1 = document.getElementsByClassName('toast-success')[0];
  toastMain1.classList.remove("toast-show");

   let toastMain2 = document.getElementsByClassName('toast-error')[0];
    toastMain2.classList.remove("toast-show");

  }

 function ClearRateTempUpload(){

        $("#tblList-Rate-Excel").DataTable().clear().draw();

        $.ajax({
            type: "post",
            data: {
             _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}"
            },
            url: "{{ route('do-clear-rate-temp-upload') }}",
            dataType: "json",
            success: function(data){
                  $("#spnUploadedRateRecord").text('0');
                  $("#spnExcelRateRecord").text('0');                  
            },
            error: function(data){ 
                console.log(data.responseText);
            },
            beforeSend:function(vData){
                  $("#divLoader").show(); 
            }

        });
 } 

  function ClearMP2TempUpload(){

        $("#tblList-MP2-Excel").DataTable().clear().draw();

        $.ajax({
            type: "post",
            data: {
             _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}"
            },
            url: "{{ route('do-clear-mp2-temp-upload') }}",
            dataType: "json",
            success: function(data){
                $("#spnUploadedMP2Record").text('0');
                $("#spnExcelMP2Record").text('0');                
            },
            error: function(data){ 
                console.log(data.responseText);
            },
            beforeSend:function(vData){
                  $("#divLoader").show(); 
            }

        });
 }

 function RemovedMP2Duplicate(vRecID){

     intCurrentPage=1;

      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}',
            tempID: vRecID
        },
        url: "{{ route('do-remove-duplicate-temp-employee-mp2') }}",
        dataType: "json",
        success: function(data){
              if(data.Response =='Success'){
                showHasSuccessMessage(data.ResponseMessage);
                getMP2TempRecordList(1);
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

 function RemovedRateDuplicate(vRecID){

     intCurrentPage=1;

      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}',
            tempID: vRecID
        },
        url: "{{ route('do-remove-duplicate-temp-employee-rate') }}",
        dataType: "json",
        success: function(data){
              if(data.Response =='Success'){
                showHasSuccessMessage(data.ResponseMessage);
                getRateTempRecordList(1);
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
 
 function getTempRateUploadedCount(vExceRecord){

      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}'
        },
        url: "{{ route('get-rate-temp-upload-count') }}",
        dataType: "json",
        success: function(data){
         
          $("#spnUploadedRateRecord").text(parseInt(data.MaxCount));
          $("#spnExcelRateRecord").text(parseInt(vExceRecord)-1);

          if(parseInt(data.MaxCount) == parseInt(vExceRecord)-1){                
            showHasSuccessMessage('Employee Rate Excel Data has successfully uploaded. Kindly check for any data  issues before saving.');
            $("#btnUploadFinalRateRecord").show();
            
          }else{
            showHasErrorMessage('','Uploaded Excel Data is not equal to Excel Record Data. Please re-upload again.');                   
            $("#btnUploadFinalRateRecord").hide();            
          }
                
        },
        error: function(data){ 
            console.log(data.responseText);
        },  
        beforeSend:function(vData){
 
        }
     });
    
   }

 function getTempMP2UploadedCount(vExceRecord){

      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}'
        },
        url: "{{ route('get-mp2-temp-upload-count') }}",
        dataType: "json",
        success: function(data){
            
          $("#spnUploadedMP2Record").text(parseInt(data.MaxCount));
          $("#spnExcelMP2Record").text(parseInt(vExceRecord)-1);

          if(parseInt(data.MaxCount) == parseInt(vExceRecord)-1){                
            showHasSuccessMessage('Employee MP2 Excel Data has successfully uploaded. Kindly check for any data  issues before saving.');
            $("#btnUploadFinalMP2Record").show();
            
          }else{
            showHasErrorMessage('','Uploaded Excel Data is not equal to Excel Record Data. Please re-upload again.');                       
            $("#btnUploadFinalMP2Record").hide();            
          }
             
        },
        error: function(data){ 
            console.log(data.responseText);
        },  
        beforeSend:function(vData){
 
        }
    });
  }

 function UpdateMP2Information(vRecordID, IsAllowUpdate){
     
    $("#btnUpdateRates").hide(); 
    resetTextBorderToNormal();

    if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    EmployeeMP2ID: vRecordID
                },
                url: "{{ route('get-employee-temp-mp2-info') }}",
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.EmployeeTempMP2Info != undefined){

                        Clearfields();

                        $("#MP2TempID").val(data.EmployeeTempMP2Info.EmployeeMP2ID);                         
                        $(".EmployeeID").val(data.EmployeeTempMP2Info.employee_id);  
                        $(".UpdateEmployeeNo").val(data.EmployeeTempMP2Info.employee_number);  
                        
                        if(data.EmployeeTempMP2Info.employee_id<=0){
                             $(".UpdateEmployeeName").val('');
                        }else{
                            $(".UpdateEmployeeName").val(data.EmployeeTempMP2Info.FullName); 
                        }

                        $("#UpdateMP2AccountNo").val(data.EmployeeTempMP2Info.MP2AccountNo); 
                        $("#UpdateMP2Amount").val(FormatDecimal(data.EmployeeTempMP2Info.MP2Amount,2));   
                        $("#UpdateMP2Frequency").val(data.EmployeeTempMP2Info.FrequencyID).change();                                       

                        if(IsAllowUpdate==true){
                            $("#btnUploadFinalMP2Record").show();
                        }
                        
                        $("#divLoader").hide();
                        $("#update-mp2-modal").modal();

                    }else{
                        $("#divLoader").hide();
                        showHasErrorMessage('',data.ResponseMessage);
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


function SaveNewMP2Record(){
                    
        var vEmployeeID = $(".EmployeeID").val();

        var vMP2AccountNo = $("#MP2AccountNo").val();
        var vMP2ContributionAmount = $("#MP2ContributionAmount").val();
        var vMP2Frequency = $("#MP2Frequency").val();
        
        resetNewMP2TextBorderToNormal();

         if(vMP2AccountNo=="") {
         showHasErrorMessage('MP2AccountNo','Enter employee MP2 Account No.');
         return;  
       }  
      
        if(vMP2ContributionAmount=="" || vMP2ContributionAmount<=0) {
         showHasErrorMessage('MP2ContributionAmount','Enter employee MP2 contribution amount.');
         return;  
       }  

        if(vMP2Frequency=="" ) {
         showHasErrorMessage('MP2Frequency','Select MP2 frequency from the list.');
         return;  
       }

       // UPDATE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",                
                EmployeeID: $(".EmployeeID").val(),                                
                MP2AccountNo: vMP2AccountNo,
                MP2ContributionAmount: vMP2ContributionAmount,                      
                MP2Frequency: vMP2Frequency,                      
            },
            url: "{{ route('do-save-employee-mp2-contribution-set-up') }}",
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveMP2",  "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);            

                if(data.Response =='Success'){
                    $("#new-mp2-modal").modal('hide');
                    toast('toast-success', data.ResponseMessage);                                        
                    ViewRecord(vEmployeeID);
                    ClearNewMP2fields();                    
                }else{
                     toast('toast-error', data.ResponseMessage);
                     return;
                }
            },
            error: function(data){
                $("#divLoader").hide();
                buttonOneClick("btnSaveMP2",  "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);            
                console.log(data.responseText);               
            },
            beforeSend:function(vData){
                $("#divLoader").show();
                buttonOneClick("btnSaveMP2", "", true);
            }
        }); 
    }

 function UpdateRateInformation(vRecordID, IsAllowUpdate){
     
    $("#btnUpdateRates").hide(); 
    resetTextBorderToNormal();

    if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    EmployeeRateID: vRecordID
                },
                url: "{{ route('get-employee-temp-rate-info') }}",
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.EmployeeTempRateInfo != undefined){

                        Clearfields();

                        $("#RateTempID").val(data.EmployeeTempRateInfo.EmployeeRateID); 
                        $(".UpdateEmployeeNo").val(data.EmployeeTempRateInfo.employee_number); 

                        $(".EmployeeID").val(data.EmployeeTempRateInfo.employee_id);   

                        if(data.EmployeeTempRateInfo.employee_id<=0){
                             $(".UpdateEmployeeName").val('');
                        }else{
                            $(".UpdateEmployeeName").val(data.EmployeeTempRateInfo.FullName); 
                        }

                        $("#UpdateEffectivityDate").val(data.EmployeeTempRateInfo.EffectivityDateFormat); 

                        $("#UpdateMonthlyRate").val(FormatDecimal(data.EmployeeTempRateInfo.MonthlyRate,2));
                        $("#UpdateDailyRate").val(FormatDecimal(data.EmployeeTempRateInfo.DailyRate,2));
                        $("#UpdateHourlyRate").val(FormatDecimal(data.EmployeeTempRateInfo.HourlyRate,2));

                        $("#UpdateRemarks").val(data.EmployeeTempRateInfo.Remarks);

                        if(IsAllowUpdate==true){
                            $("#btnUpdateRates").show();
                        }
                        
                        $("#divLoader").hide();
                        $("#update-rate-modal").modal();

                    }else{
                        $("#divLoader").hide();
                        showHasErrorMessage('',data.ResponseMessage);
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

  function UpdateUploadedRates(){

        var vRateTempID = $("#RateTempID").val();
        var vEmployeeID = $(".EmployeeID").val();

        var vEffectivityDate = $("#UpdateEffectivityDate").val();
        var vMonthlyRate = $("#UpdateMonthlyRate").val();
        var vDailyRate = $("#UpdateDailyRate").val();
        var vHourlyRate = $("#UpdateHourlyRate").val();
        var vRemarks = $("#UpdateRemarks").val();

        resetTextBorderToNormal();

        if(vEmployeeID=="" || vEmployeeID<=0) {
         showHasErrorMessage('UpdateRateEmployeeName','Search and select employee from the list.');
         return;  
       }

        if(vEffectivityDate=="") {
         showHasErrorMessage('UpdateEffectivityDate','Enter salary effectivity date.');
         return;  
       }

        if(vMonthlyRate=="" || vMonthlyRate<=0) {
         showHasErrorMessage('UpdateMonthlyRate','Enter employee uploaded monthly rate.');
         return;  
       }

       if(vDailyRate=="" || vDailyRate<=0) {
         showHasErrorMessage('UpdateDailyRate','Enter employee uploaded daily rate.');
         return;  
       }

       if(vHourlyRate=="" || vHourlyRate<=0 ) {
         showHasErrorMessage('UpdateHourlyRate','Enter employee uploaded hourly rate.');
         return;  
       }
       
       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                EmployeeTempRateID: $("#RateTempID").val(),
                EmployeeID: $(".EmployeeID").val(),
                EffectivityDate: $("#UpdateEffectivityDate").val(),
                MonthlyRate: $("#UpdateMonthlyRate").val(),
                DailyRate: $("#UpdateDailyRate").val(),
                HourlyRate: $("#UpdateHourlyRate").val(),
                RateRemarks: $("#UpdateRemarks").val()                                  
            },
            url: "{{ route('do-save-employee-temp-rate') }}",
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

                if(data.Response =='Success'){                    
                    toast('toast-success', data.ResponseMessage);
                    getRateTempRecordList(1);
                    $("#update-rate-modal").modal('hide');
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

  function UpdateUploadedMP2(){

        var vMP2TempID = $("#MP2TempID").val();
        var vEmployeeID = $(".EmployeeID").val();

        var vUpdateMP2AccountNo = $("#UpdateMP2AccountNo").val();
        var vUpdateMP2Amount = $("#UpdateMP2Amount").val();
        var vUpdateMP2Frequency = $("#UpdateMP2Frequency").val();        

        resetTextBorderToNormal();

        if(vEmployeeID=="" || vEmployeeID<=0) {
         showHasErrorMessage('UpdateRateEmployeeName','Search and select employee from the list.');
         return;  
       }

        if(vUpdateMP2AccountNo=="") {
         showHasErrorMessage('UpdateMP2AccountNo','Enter MP2 Account No.');
         return;  
       }

        if(vUpdateMP2Amount=="" || vUpdateMP2Amount<=0) {
         showHasErrorMessage('UpdateMP2Amount','Enter employee MP2 deduction amount.');
         return;  
       }

       if(vUpdateMP2Frequency=="") {
         showHasErrorMessage('UpdateMP2Frequency','Select frequency  from the list.');
         return;  
       }
       
       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                EmployeeTempMP2ID: $("#MP2TempID").val(),
                EmployeeID: $(".EmployeeID").val(),
                MP2AccountNo: $("#UpdateMP2AccountNo").val(),
                MP2DeductionAmount: $("#UpdateMP2Amount").val(),                
                MP2Frequency: $("#UpdateMP2Frequency").val(),                                                                
            },
            url: "{{ route('do-save-employee-temp-mp2') }}",
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

                if(data.Response =='Success'){                    
                    toast('toast-success', data.ResponseMessage);
                    getMP2TempRecordList(1);
                    $("#update-mp2-modal").modal('hide');
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

function DeleteTableRow(vID){

        //Remove Row
        var vIsDeleted = false;
        var tblItemList = $("#tblList-Rate-Excel").DataTable();

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

$( function() {    
       $("#EffectivityDate").datepicker(); 
       $("#UpdateEffectivityDate").datepicker();       
   } );

    $( document ).ready(function() {

      $('#view-modal').modal({
        backdrop: 'static',
        keyboard: true,
        show: false 
    });

    $('#salary-modal').modal({
        backdrop: 'static',
        keyboard: true,
        show: false 
    });

    $('#upload-rates-modal').modal({
        backdrop: 'static',
        keyboard: true,
        show: false 
    });

     $('#rate-excel-modal').modal({
        backdrop: 'static',
        keyboard: true,
        show: false 
    });

     $('#upload-mp2-modal').modal({
        backdrop: 'static',
        keyboard: true,
        show: false 
    });

    $('#mp2-excel-modal').modal({
        backdrop: 'static',
        keyboard: true,
        show: false 
    });


      $("#nav-basic-tab").click();
});

 function UploadRateExcelRecord(){

    //Clearfields();
    $("#spnExcelRecord").val(0);
    $("#RateExcelFile").val('');
    $("#upload-rates-modal").modal();
       
  }

   function UploadMP2ExcelRecord(){

    $("#spnExcelRecord").val(0);
    $("#MP2ExcelFile").val('');
    $("#upload-mp2-modal").modal();
       
  }

   function SyncEmployee(){
    $("#sync-employee-modal").modal();
  }

  $(document).on('focus','.autocomplete_txt',function(){

       vElemID=0;
       isEmployee=false;
       isAllowance=false;
       var valAttrib  = $(this).attr('data-complete-type');

       if(valAttrib=='employee'){
            searchlen=3;
            isEmployee=true;
            var postURL="{{ URL::route('get-employee-search-list')}}";
        }

        if(valAttrib=='allowance'){
            searchlen=2;
            isAllowance=true;            

            var thisID = $(this).attr('id');
            var splitID=thisID.split("_");

            var vElemID=splitID[1];
            var postURL="{{ URL::route('get-allowance-type-search-list')}}";
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


                                if (isAllowance){
                                     return {
                                     label: code[1] +' - '+ code[2],
                                     value: code[2],
                                     data : item
                                   }
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

                    if(isEmployee){
                      $(".EmployeeID").val(seldata[0]);
                      $(".UpdateEmployeeNo").val(seldata[1].trim());
                      $(".UpdateEmployeeName").val(seldata[4].trim());
                    }

                    if(isAllowance){
                      $("#allowance-id_"+vElemID).val(seldata[0]);
                      $("#allowance-code_"+vElemID).val(seldata[1]);
                      $("#allowance-name_"+vElemID).val(seldata[2]);
                    }
              }
        });
    });

  function SaveFinalRateRecord(){

      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}'

        },
        url: "{{ route('do-upload-save-employee-rate') }}",
        dataType: "json",
        success: function(data){
              if(data.Response =='Success'){
                  showHasSuccessMessage(data.ResponseMessage);
                  getRecordList(1, $('.searchtext').val());
                  $("#divLoader").hide(); 
                  $("#rate-excel-modal").modal('hide');
                return;
              }else{
                showHasErrorMessage('', data.ResponseMessage);
                $("#divLoader").hide(); 
                return; 
              }

              $("#spnLoadingLabel").text('Loading...');
        },
        error: function(data){ 
            console.log(data.responseText);
        },
        beforeSend:function(vData){
            $("#divLoader").show(); 
            $("#spnLoadingLabel").text('Saving...');
        }

    });
 }

 function ClearEmployeeRateModal(){

   $(".EmployeeID").val('0');
   $(".UpdateEmployeeNo").val('');
   $(".UpdateEmployeeName").val('');

    resetTextBorderToNormal();
 }

  function GenerateExcel(){
        $.ajax({
              type: "post",
              data: {
                  _token: '{{ csrf_token() }}',
                  Platform : "{{ config('app.PLATFORM_ADMIN') }}"          
              },
              url: "{{ route('get-excel-employee-list') }}",
              dataType: "json",
              success: function(data){

                  if(data.Response=="Success"){
                       resultquery=data.AllEmployeeExcelList;
                       ShowGeneratedExcel();
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
                            " EMPLOYEE NO ",
                            " EMPLOYEE NAME ",
                            " MOBILE NO ", 
                            " EMAIL ADDRESS ",
                            " TIN NO ",
                            " SSS NO ", 
                            " PAG-IBIG NO ",
                            " PHIC NO ", 
                            " POSITION ",
                            " DIVISION ",
                            " DEPARTMENT ", 
                            " SECTION ",
                            " LOCATION ",
                            " MONHTLY RATE ",  
                            " DAILY RATE ",
                            " HOURLY RATE ", 
                            " STATUS "                                                                                        
                          ];

          
            xlsRows=resultquery;
          
            createXLSLFormatObj.push(xlsHeader);

            var intRowCnt = 1;
              $.each(xlsRows, function(index, value) {
                  var innerRowData = [];   
                  $.each(value, function(ind, val) {
                    
                     if(ind == "employee_number" ||
                        ind == "FullName" ||
 
                        ind == "MobileNo" ||
                        ind == "EmailAddress" ||

                        ind == "tin_number" ||
                        ind == "sss_number" ||
                        ind == "pagibig_number" ||
                        ind == "philhealth_number" ||

                        ind == "Position" ||
                        ind == "Division" ||
                        ind == "Department" ||
                        ind == "Section" ||
                        ind == "BranchName" ||

                        ind == "MonthlyRate" ||
                        ind == "DailyRate" ||
                        ind == "HourlyRate" ||

                        ind == "emp_status"){

                            innerRowData.push(val);
                        }

                  });

                  createXLSLFormatObj.push(innerRowData);
                   intRowCnt = intRowCnt + 1;
        
              });

         
           var ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);
            for (var i = 1; i <= intRowCnt; i++) {
                ws["N" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["O" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["P" + i].z = '#,##0.00_);\\(#,##0.00\\)';
       

                ws["N" + i].t = 'n';
                ws["O" + i].t = 'n';
                ws["P" + i].t = 'n';
                     
            }

            const countheader = Object.keys(createXLSLFormatObj); // columns name header to

             var wscols = [];
              for (var i = 0; i < countheader.length; i++) {  // columns length added
                 wscols.push({ wch: Math.max(countheader[i].toString().length + 25) })
              }
              
            ws['!cols'] = wscols;
                       
            /* File Name */
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws,"Employee List Sheet");

            /* generate file and download */
            const wbout = XLSX.write(wb, { type: "array", bookType: "xlsx" });
            saveAs(new Blob([wbout], { type: "application/octet-stream" }), "All-Employee-Report.xlsx");

            showHasSuccessMessage('Excel file has successfully created & downloaded at Download Folder');     
                          
     }

  $("#UpdateEmployeeName").blur(function() {
     vEmployee=$(this).val();
       if(vEmployee.length<=5 || vEmployee==''){
         $(".EmployeeID").val(0);
         $(".UpdateEmployeeNo").val('');
      }
  });
  
  $("#UpdateEmployeeName").keyup(function() { 
    vEmployee=$(this).val();
       if(vEmployee.length<=5 || vEmployee==''){
         $(".EmployeeID").val(0);
         $(".UpdateEmployeeNo").val('');
      }
    });

 $("#nav-contribution-tab").click(function(){

    resetNewHDMFTextBorderToNormal();
    resetNewMP2TextBorderToNormal();

    $("#nav-contribution-tab").css({"background":"#f68c1f"}); 
    $("#nav-rates-tab").css({"background":"#475F7B"}); 
    $("#nav-loan-tab").css({"background":"#475F7B"});
    $("#nav-allowances-tab").css({"background":"#475F7B"});
});

  $("#nav-rates-tab").click(function(){

    resetNewHDMFTextBorderToNormal();
    resetNewMP2TextBorderToNormal();

    $("#nav-rates-tab").css({"background":"#f68c1f"}); 
    $("#nav-contribution-tab").css({"background":"#475F7B"}); 
    $("#nav-loan-tab").css({"background":"#475F7B"});
    $("#nav-allowances-tab").css({"background":"#475F7B"}); 
});

 $("#nav-loan-tab").click(function(){

    resetNewHDMFTextBorderToNormal();
    resetNewMP2TextBorderToNormal();

    $("#nav-loan-tab").css({"background":"#f68c1f"}); 
    $("#nav-contribution-tab").css({"background":"#475F7B"}); 
    $("#nav-rates-tab").css({"background":"#475F7B"});
    $("#nav-allowances-tab").css({"background":"#475F7B"});
});

 $("#nav-allowances-tab").click(function(){

    resetNewHDMFTextBorderToNormal();
    resetNewMP2TextBorderToNormal();

    $("#nav-allowances-tab").css({"background":"#f68c1f"}); 
    $("#nav-contribution-tab").css({"background":"#475F7B"}); 
    $("#nav-rates-tab").css({"background":"#475F7B"});
    $("#nav-loan-tab").css({"background":"#475F7B"});
});

function LoadCalendar(vElem){
    $("#"+vElem).focus();
}

 //=================================================================
  function AddNewAllowanceRow(){
    
    tableBody = $("#tblEmployee-Allowance-List");
    vCounter=$('#tblAllowanceList').find('tr').length;
    
     itemCount=itemCount+1;
    
      item = "<tr id='tr_"+vCounter+"'>"+               
              "<td>"+         
                    "<input id='employee-allowance-id_"+vCounter+"' type='hidden' class='form-control' style='width:100%; font-weight:normal;' value='0' readonly>"+  
                    "<input id='allowance-id_"+vCounter+"' type='hidden' class='form-control' style='width:100%; font-weight:normal;' value='0' readonly>"+           
                    "<input id='allowance-code_"+vCounter+"' type='text' class='form-control' style='width:100%; font-weight:normal;' value='' autocomplete='off' readonly>"+                    
              "</td>"+
              "<td><input id='allowance-name_"+vCounter+"' type='text' class='form-control custom-select autocomplete_txt ui-autocomplete-input' autocomplete='off' data-complete-type='allowance' style='width:100%; font-weight:normal;'></td>"+            
              "<td><input id='allowance-amount_"+vCounter+"' type='text' class='form-control DecimalOnly' style='width:100%; font-weight:normal;text-align: center;' autocomplete='off'></td>"+              
              "<td>"+                  
                      "<select id='allowance-frequency_"+vCounter+"' class='form-control' style='height: calc(1.4em + 0.94rem + 3.7px);padding: 0.47rem 0.8rem;font-size: 1rem;font-weight: 400;border-radius: 0.267rem;color: #475F7B;background-color: #FFFFFF;background-clip: padding-box;border: 1px solid rgb(204, 204, 204);padding-right: 1.5rem;background-image: url(../philsagapayroll/public/img/combo-arrow.png);background-size: 12px 12px, 10px 10px;background-repeat: no-repeat;-webkit-appearance: none;width:100%;background-position: calc(100% - 12px) 13px, calc(100% - 20px) 13px, 100% 0;'>"+
                         "<option value=''>Please Select</option>"+
                         "<option value='1'>1ST HALF</option>" +
                         "<option value='2'>2ND HALF</option>" +
                         "<option value='3'>EVERY CUTOFF</option> " +                         
                      "</select>"+
              "</td>"+              
              "<td>"+
                     "<i id='new_"+vCounter+"'class='bx bx-plus add-item' onclick='AddNewAllowanceRow()' style='color:white;background:red;margin-right:2px;padding:2px;'></i>"+
                     "<i id='del_"+vCounter+"' class='bx bx-trash remove-item' onclick='RemoveAllowanceRow("+vCounter+")' style='color:white;background:rgb(246, 140, 31);margin-left:2px;padding:2px;'></i>"+
              "</td>"+
            "</tr>";   
                
    tableBody.append(item);
  }

  function RemoveAllowanceRow(vElemID){

     $('#tr_'+vElemID).remove();    
      itemCount=$('#tblAllowanceList').find('tr').length-1;

      if(itemCount==0){         
          AddNewAllowanceRow();     
     }
  }

  function LoadRecordAllowanceList(vItemList){

    itemCount=0;
    if(vItemList.length > 0){        
      for(var x=0; x < vItemList.length; x++){
        LoadAllowanceListDataRow(vItemList[x],x+1);
      }
     }
     else{        
         AddNewAllowanceRow();
       }
  }

 function LoadAllowanceListDataRow(vData,vCounter){

   itemCount=itemCount+1;    
   tableBody = $("#tblAllowanceList");
  
   item = "<tr id='tr_"+vCounter+"'>"+
               "<td>"+                
                 "<input id='employee-allowance-id_"+vCounter+"' type='hidden' class='form-control' style='width:100%; font-weight:normal;' value="+ vData.EmployeeAllowanceID +" readonly>"+           
                 "<input id='allowance-id_"+vCounter+"' type='hidden' class='form-control' style='width:100%; font-weight:normal;' value="+ vData.AllowanceID +" readonly>"+           
                 "<input id='allowance-code_"+vCounter+"' type='text' class='form-control' style='width:100%; font-weight:normal;' autocomplete='off' value='"+ vData.AllowanceCode +"' readonly>"+                                                     
              "</td>"+
              "<td><input id='allowance-name_"+vCounter+"' type='text' class='form-control custom-select autocomplete_txt ui-autocomplete-input' value='"+ vData.AllowanceName +"'  autocomplete='off' data-complete-type='allowance' style='width:100%; font-weight:normal;'></td>"+                    
              "<td><input id='allowance-amount_"+vCounter+"' type='text' class='form-control DecimalOnly' style='width:100%; font-weight:normal;text-align: center;' value="+ vData.AllowanceAmount +" autocomplete='off'></td>"+     
              "<td>";
             
             if(vData.FrequencyID==1){
                 
               item= item + "<select id='allowance-frequency_"+vCounter+"' class='form-control' style='height: calc(1.4em + 0.94rem + 3.7px);padding: 0.47rem 0.8rem;font-size: 1rem;font-weight: 400;border-radius: 0.267rem;color: #475F7B;background-color: #FFFFFF;background-clip: padding-box;border: 1px solid rgb(204, 204, 204);padding-right: 1.5rem;background-image: url(../philsagapayroll/public/img/combo-arrow.png);background-size: 12px 12px, 10px 10px;background-repeat: no-repeat;-webkit-appearance: none;width:100%;background-position: calc(100% - 12px) 13px, calc(100% - 20px) 13px, 100% 0;'>"+
                         "<option value=''>Please Select</option>"+
                         "<option value='1' selected>1ST HALF</option>" +
                         "<option value='2'>2ND HALF</option>" +
                         "<option value='3'>EVERY CUTOFF</option> " +                         
                      "</select>";

             } else if(vData.FrequencyID==2){
                 
                    
               item= item + "<select id='allowance-frequency_"+vCounter+"' class='form-control' style='height: calc(1.4em + 0.94rem + 3.7px);padding: 0.47rem 0.8rem;font-size: 1rem;font-weight: 400;border-radius: 0.267rem;color: #475F7B;background-color: #FFFFFF;background-clip: padding-box;border: 1px solid rgb(204, 204, 204);padding-right: 1.5rem;background-image: url(../philsagapayroll/public/img/combo-arrow.png);background-size: 12px 12px, 10px 10px;background-repeat: no-repeat;-webkit-appearance: none;width:100%;background-position: calc(100% - 12px) 13px, calc(100% - 20px) 13px, 100% 0;'>"+
                         "<option value=''>Please Select</option>"+
                         "<option value='1'>1ST HALF</option>" +
                         "<option value='2' selected>2ND HALF</option>" +
                         "<option value='3'>EVERY CUTOFF</option> " +                         
                      "</select>";

             } else if(vData.FrequencyID==3){
                 
                    
               item= item + "<select id='allowance-frequency_"+vCounter+"' class='form-control' style='height: calc(1.4em + 0.94rem + 3.7px);padding: 0.47rem 0.8rem;font-size: 1rem;font-weight: 400;border-radius: 0.267rem;color: #475F7B;background-color: #FFFFFF;background-clip: padding-box;border: 1px solid rgb(204, 204, 204);padding-right: 1.5rem;background-image: url(../philsagapayroll/public/img/combo-arrow.png);background-size: 12px 12px, 10px 10px;background-repeat: no-repeat;-webkit-appearance: none;width:100%;background-position: calc(100% - 12px) 13px, calc(100% - 20px) 13px, 100% 0;'>"+
                         "<option value=''>Please Select</option>"+
                         "<option value='1'>1ST HALF</option>" +
                         "<option value='2'>2ND HALF</option>" +
                         "<option value='3' selected>EVERY CUTOFF</option> " +                         
                      "</select>";
             }
             
              item= item + "</td>"+
                                
              "<td>"+
                     "<i id='new_"+vCounter+"'class='bx bx-plus add-item' onclick='AddNewAllowanceRow()' style='color:white;background:red;margin-right:2px;padding:2px;'></i>"+
                     "<i id='del_"+vCounter+"' class='bx bx-trash remove-item' onclick='RemoveAllowanceRow("+vCounter+")' style='color:white;background:rgb(246, 140, 31);margin-left:2px;padding:2px;'></i>"+
              "</td>"+
            "</tr>";   
                
    tableBody.append(item);
}

function SetAllowance(){

 //CHECKING ALLOWNCES INPUT
  if(itemCount>0) {

      hasAllowance=true;
      hasAmount=true;
      hasFrequency=true;
       
      for (var i = 1; i <= itemCount; i++) {
       
        $("#allowance-code"  + i).css({"border":"#ccc 1px solid"});
        $("#allowance-name_"  + i).css({"border":"#ccc 1px solid"});
        $("#allowance-amount_"  + i).css({"border":"#ccc 1px solid"});
        $("#allowance-frequency_"  + i).css({"border":"#ccc 1px solid"});

        if($('#allowance-id_' + i).length){
          if($("#allowance-id_" + i).val() == 0){            
            hasAllowance=false;
          }
        }
                
        if(!hasAllowance) {
         showHasErrorMessage('allowance-name_' +i ,'Type and select allowance from the list.');
         return;  
         }
        
        if($('#allowance-amount_' + i).length){
          if($("#allowance-amount_" + i).val() == "" || $("#allowance-amount_" + i).val() <= 0){          
          hasAmount=false;
          }
        }

         if(!hasAmount) {
         showHasErrorMessage('allowance-amount_' +i ,'Enter allowance amount.');
         return;  
         }
        
      if($('#allowance-frequency_' + i).length){
         if($("#allowance-frequency_" + i).val() == ""){
          hasFrequency=false;
         }
      }
            
      if(!hasFrequency) {
         showHasErrorMessage('allowance-frequency_' +i ,'Select frequency from the list.');
         return;  
       }
    }
  }


    //FINAL SAVING TO JSON ARRAY
    var pItemData = [];
    var intCntr = 1;
    var blnIsIncomplete = false;

    for (var i = 1; i <= itemCount; i++) {

    var EmployeeAllowanceItemID = 0;
    var AllowanceID = 0;
    var AllowanceAmount = 0;    
    var FrequencyID = 0; 
    
    if($('#allowance-id_' + i).length){
        if($("#allowance-id_" + i).val() != "" || $("#allowance-id_" + i).val()>0 ){
            AllowanceID = $("#allowance-id_" + i).val();
        }else{
            blnIsIncomplete = true;
        }
    }

    if($('#allowance-amount_' + i).length){
        if($("#allowance-amount_" + i).val()!= ""){
            AllowanceAmount = $("#allowance-amount_" + i).val();
        }else{
            blnIsIncomplete = true;
        }
    }

     if($('#allowance-frequency_' + i).length){
        if($("#allowance-frequency_" + i).val()!= ""){
            FrequencyID = $("#allowance-frequency_" + i).val();
        }else{
            blnIsIncomplete = true;
        }
    }
    
    if(!blnIsIncomplete){
        pItemData[intCntr] = {
            EmployeeAllowanceItemID:EmployeeAllowanceItemID,
            AllowanceID:AllowanceID,
            AllowanceAmount:AllowanceAmount,            
            FrequencyID:FrequencyID            
        };
         intCntr = intCntr + 1;
    }else{
        break;
    }

  }

  //Save Transaction
 SaveAllowanceTransaction(pItemData,itemCount);

}

function SaveAllowanceTransaction(pDetails,vItemCount){

    $.ajax({
      type: "post",
      data: {
        _token: '{{ csrf_token() }}',
        EmployeeID: $(".EmployeeID").val(),
        EmployeeAllowanceItems: pDetails,
        ItemCount: vItemCount
      },
      url: "{{ route('do-save-employee-allowance-set-up') }}",
      dataType: "json",
      success: function(data){
          
        if(data.Response == "Success"){                             
            getEmployeeAllowanceList();
            toast('toast-success', data.ResponseMessage);            
            return ;
         }
         
        if(data.Response == "Failed"){
              toast('toast-error', data.ResponseMessage);
              buttonOneClick("btnSaveEmployeeAllowance", "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);            
         }
      },
      error: function(data){
        buttonOneClick("btnSaveEmployeeAllowance", "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);            
         console.log(data.responseText);
      },
      beforeSend:function(vData){
        buttonOneClick("btnCreate","spnCreateOrder", "Saving..", true);
      }
    });

  }

function getEmployeeAllowanceList(){
    
    $.ajax({
      type: "post",
      data: {
        _token: '{{ csrf_token() }}',
        EmployeeID: $(".EmployeeID").val()        
      },
      url: "{{ route('get-employee-allowance-set-up-list') }}",
      dataType: "json",
      success: function(data){
          
        if(data.Response == "Success"){    
           $("#tblEmployee-Allowance-List").DataTable().clear().draw();                      
            LoadRecordAllowanceList(data.EmployeeAllowanceInfo);                                             
         }
         
        if(data.Response == "Failed"){                        
              toast('toast-error', data.ResponseMessage);
              buttonOneClick("btnSaveEmployeeAllowance", "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);            
         }
      },
      error: function(data){
        buttonOneClick("btnSaveEmployeeAllowance", "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);            
         console.log(data.responseText);
      },
      beforeSend:function(vData){
        buttonOneClick("btnCreate","spnCreateOrder", "Saving..", true);
      }
    });

  }


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



