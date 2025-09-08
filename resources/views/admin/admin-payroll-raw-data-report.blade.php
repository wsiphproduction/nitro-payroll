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
                                    <li class="breadcrumb-item active"> Payroll Raw Data Report
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
                                    <h4 class="card-title"> Payroll Raw Data Report </h4>
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
<!-- 
                                                        <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="Print()">
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
                                        <div id="style-2" class="table-responsive col-md-12 table_default_height">
                                            <table id="tblList" class="table zero-configuration complex-headers border alt-background">
                                                <thead>
                                                         <th></th>                                 
                                                         <th style="min-width: 110px;">EMPLOYEE ID</th>  
                                                        <th style="min-width: 130px;">EMPLOYEE NAME</th>                                                                                                                
                                                         <th style="min-width: 110px;">DEPARTMENT</th>
                                                         <th style="min-width: 80px;">DIVISION</th>
                                                         <th style="min-width: 80px;">LOCATION</th>
                                                                                            
                                                         <th style="min-width: 80px;">SECTION</th> 
                                                         <th style="min-width: 80px;">POSITION</th> 
                                                         <th style="min-width: 100px;">P.R STRUCTURE</th> 
                                                         <th style="min-width: 80px;">PAYMENT TYPE</th> 
                                                         <th style="min-width: 60px;">P.R CODE</th> 
                                                         <th style="min-width: 60px;">P.R YEAR</th> 
                                                         <th style="min-width: 100px;">MONTHLY RATE</th> 
                                                         <th style="min-width: 100px;">DAILY RATE</th> 
                                                         <th style="min-width: 80px;">HOURLY RATE</th> 
                                                         <th style="min-width: 80px;">EMP. STATUS</th>                                                          
                                                         
                                                         <th style="min-width: 60px;">REG. HRS.</th> 
                                                         <th style="min-width: 60px;">REG. PAY</th> 
                                                         <th style="min-width: 60px;">LATE HRS.</th> 
                                                         <th style="min-width: 60px;">LATE PAY</th> 
                                                         <th style="min-width: 60px;">UT HRS.</th> 
                                                         <th style="min-width: 60px;">UT PAY.</th> 
                                                         <th style="min-width: 80px;">ABSENT HRS.</th> 
                                                         <th style="min-width: 80px;">ABSENT PAY</th> 
                                                         <th style="min-width: 80px;">TOTAL LEAVE HRS.</th> 
                                                         <th style="min-width: 80px;">TOTAL LEAVE PAY</th> 
                                                         <th style="min-width: 80px;">TOTAL O.T HRS.</th> 
                                                         <th style="min-width: 80px;">TOTAL O.T PAY</th> 

                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(1, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(1, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(2, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(2, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(3, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(3, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(4, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(4, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(5, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(5, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(6, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(6, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(7, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(7, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(8, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(8, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(9, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(9, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(10, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(10, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(11, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(11, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(12, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(12, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(13, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(13, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(14, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(14, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(15, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(15, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(16, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(16, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(17, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(17, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(18, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(18, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(19, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(19, $LeaveTypeList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(20, $LeaveTypeList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $LeaveTypeModel->getLeaveTypeByID(20, $LeaveTypeList) }} PAY</th> 

                                                         <th style="min-width: 90px;">ND HRS.</th> 
                                                         <th style="min-width: 90px;">ND PAY</th> 

                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(1, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(1, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(2, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(2, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(3, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(3, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(4, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(4, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(5, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(5, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(6, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(6, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(7, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(7, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(8, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(8, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(9, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(9, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(10, $OTRateList) }}  HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(10, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(11, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(11, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(12, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(12, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(13, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(13, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(14, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(14, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(15, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(15, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(16, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(16, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(17, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(17, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(18, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(18, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(19, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(19, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(20, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(20, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(21, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(21, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(22, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(22, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(23, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(23, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(24, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(24, $OTRateList) }} PAY</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(25, $OTRateList) }} HRS</th> 
                                                         <th style="min-width: 90px;">{{ $OTRateModel->getOTByID(25, $OTRateList) }} PAY</th> 

                                                         <th style="min-width: 100px;">OTHER EARNINGS</th> 
                                                         <th style="min-width: 120px;">TAXABLE OTHER EARNINS</th> 
                                                         <th style="min-width: 80px;">OTHER DEDUCTION</th> 
                                                         <th style="min-width: 120px;">TAXABLE OTHER DEDUCTION</th> 
                                                         <th style="min-width: 80px;">LOAN DEDUCTION</th>  
                                                         
                                                         <th style="min-width: 80px;">TAXABLE INCOME</th> 
                                                         <th style="min-width: 60px;">WTAX</th> 

                                                         <th style="min-width: 80px;">SSS EE</th> 
                                                         <th style="min-width: 80px;">SSS WISP</th> 
                                                         <th style="min-width: 80px;">SSS ER</th>    
                                                         <th style="min-width: 80px;">SSS ECER</th> 
                                                         
                                                         <th style="min-width: 60px;">PHIC EE</th>    
                                                         <th style="min-width: 60px;">PHIC ER</th>  

                                                         <th style="min-width: 80px;">PAGIBIG EE</th>    
                                                         <th style="min-width: 80px;">PAGIBIG MP2</th>    
                                                         <th style="min-width: 80px;">PAGIBIG ER</th>    

                                                         <th style="min-width: 100px;">GROSS PAY</th>
                                                         <th style="min-width: 100px;">TOTAL DEDUCTION</th>
                                                         <th style="min-width: 80px;">TOTAL PAY</th>
                                                         <th style="min-width: 80px;">NET PAY</th>
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
            "order": [[ 1, "asc" ]]
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
            url: "{{ route('get-payroll-raw-data-report-list') }}",
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
            tdEmployeeNo = "<span class='font-normal'>" + vData.EmpNo + "</span>";
            tdFullName = "<span class='font-normal'>" + vData.EmployeeName + "</span>";

            tdDepartment = "<span class='font-normal'>" + vData.Department + "</span>";
            tdDivision = "<span class='font-normal'>" + vData.Division + "</span>";
            tdLocation = "<span class='font-normal'>" + vData.Location + "</span>";
          
            tdSection = "<span class='font-normal'>" + vData.Section + "</span>";
            tdJobPosition = "<span class='font-normal'>" + vData.JobPosition + "</span>";
            tdPRSttructure = "<span class='font-normal'>" + vData.PRSttructure + "</span>";
            tdPaymentType = "<span class='font-normal'>" + vData.PaymentType + "</span>";
            tdPeriodCode = "<span class='font-normal'>" + vData.PeriodCode + "</span>";
            tdYear = "<span class='font-normal'>" + vData.PeriodYear + "</span>";
            tdMonthlyRate = "<span class='font-normal'>" + FormatDecimal(vData.MonthlyRate,2) + "</span>";
            tdDailyRate = "<span class='font-normal'>" + FormatDecimal(vData.DailyRate,2) + "</span>";
            tdHourlyRate = "<span class='font-normal'>" + FormatDecimal(vData.HourlyRate,2)+ "</span>";
            tdEmpStatus = "<span class='font-normal'>" + vData.EmpStatus + "</span>";            

            tdRegHrs = "<span class='font-normal'>" + FormatDecimal(vData.RegHrs,2) + "</span>";
            tdRegPay = "<span class='font-normal'>" + FormatDecimal(vData.RegPay,2) + "</span>";
            tdLateHours = "<span class='font-normal'>" + FormatDecimal(vData.LateHours,2)+ "</span>";
            tdLatePay = "<span class='font-normal'>" + FormatDecimal(vData.LatePay,2) + "</span>";
            tdUndertimeHours = "<span class='font-normal'>" + FormatDecimal(vData.UndertimeHours,2) + "</span>";
            tdUndertimePay = "<span class='font-normal'>" + FormatDecimal(vData.UndertimePay,2) + "</span>";
            tdAbsentHrs = "<span class='font-normal'>" + FormatDecimal(vData.AbsentHrs,2) + "</span>";
            tdAbsentPay = "<span class='font-normal'>" + FormatDecimal(vData.AbsentPay,2)+ "</span>";
            tdTotalLeaveHrs = "<span class='font-normal'>" + FormatDecimal(vData.TotalLeaveHrs,2) + "</span>";
            tdTotalLeavePay = "<span class='font-normal'>" + FormatDecimal(vData.TotalLeavePay,2) + "</span>";
            tdTotalOvertimeHrs = "<span class='font-normal'>" + FormatDecimal(vData.TotalOvertimeHrs,2) + "</span>";
            tdTotalOvertimePay = "<span class='font-normal'>" + FormatDecimal(vData.TotalOvertimePay,2) + "</span>";

            tdLeave1Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave1Hrs,2)+ "</span>";
            tdLeave1Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave1Pay,2) + "</span>";
            tdLeave2Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave2Hrs,2) + "</span>";
            tdLeave2Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave2Pay,2) + "</span>";
            tdLeave3Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave3Hrs,2) + "</span>";
            tdLeave3Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave3Pay,2)+ "</span>";
            tdLeave4Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave4Hrs,2) + "</span>";
            tdLeave4Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave4Pay,2) + "</span>";
            tdLeave5Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave5Hrs,2) + "</span>";
            tdLeave5Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave5Pay,2) + "</span>";
            tdLeave6Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave6Hrs,2) + "</span>";
            tdLeave6Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave6Pay,2)+ "</span>";
            tdLeave7Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave7Hrs,2) + "</span>";
            tdLeave7Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave7Pay,2) + "</span>";
            tdLeave8Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave8Hrs,2) + "</span>";
            tdLeave8Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave8Pay,2) + "</span>";
            tdLeave9Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave9Hrs,2) + "</span>";
            tdLeave9Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave9Pay,2)+ "</span>";
            tdLeave10Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave10Hrs,2) + "</span>";
            tdLeave10Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave10Pay,2) + "</span>";
            tdLeave11Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave11Hrs,2) + "</span>";
            tdLeave11Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave11Pay,2) + "</span>";
            tdLeave12Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave12Hrs,2) + "</span>";
            tdLeave12Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave12Pay,2)+ "</span>";
            tdLeave13Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave13Hrs,2) + "</span>";
            tdLeave13Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave13Pay,2) + "</span>";
            tdLeave14Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave14Hrs,2) + "</span>";
            tdLeave14Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave14Pay,2) + "</span>";
            tdLeave15Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave15Hrs,2) + "</span>";
            tdLeave15Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave15Pay,2)+ "</span>";
            tdLeave16Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave16Hrs,2) + "</span>";
            tdLeave16Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave16Pay,2) + "</span>";
            tdLeave17Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave17Hrs,2) + "</span>";
            tdLeave17Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave17Pay,2) + "</span>";
            tdLeave18Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave18Hrs,2) + "</span>";
            tdLeave18Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave18Pay,2)+ "</span>";
            tdLeave19Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave19Hrs,2) + "</span>";
            tdLeave19Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave19Pay,2) + "</span>";
            tdLeave20Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Leave20Hrs,2) + "</span>";
            tdLeave20Pay = "<span class='font-normal'>" + FormatDecimal(vData.Leave20Pay,2) + "</span>";

            tdNDHrs = "<span class='font-normal'>" + FormatDecimal(vData.NightDifferentialHrs,2) + "</span>";
            tdNDPay = "<span class='font-normal'>" + FormatDecimal(vData.NightDifferentialPay,2)+ "</span>";

            tdOT1Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime1Hrs,2) + "</span>";
            tdOT1Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime1Pay,2) + "</span>";
            tdOT2Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime2Hrs,2) + "</span>";
            tdOT2Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime2Pay,2) + "</span>";
            tdOT3Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime3Hrs,2) + "</span>";
            tdOT3Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime3Pay,2) + "</span>";
            tdOT4Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime4Hrs,2) + "</span>";
            tdOT4Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime4Pay,2) + "</span>";
            tdOT5Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime5Hrs,2) + "</span>";
            tdOT5Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime5Pay,2) + "</span>";
            tdOT6Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime6Hrs,2) + "</span>";
            tdOT6Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime6Pay,2) + "</span>";
            tdOT7Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime7Hrs,2) + "</span>";
            tdOT7Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime7Pay,2) + "</span>";
            tdOT8Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime8Hrs,2) + "</span>";
            tdOT8Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime8Pay,2) + "</span>";
            tdOT9Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime9Hrs,2) + "</span>";
            tdOT9Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime9Pay,2) + "</span>";
            tdOT10Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime10Hrs,2) + "</span>";
            tdOT10Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime10Pay,2) + "</span>";
            tdOT11Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime11Hrs,2) + "</span>";
            tdOT11Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime11Pay,2) + "</span>";
            tdOT12Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime12Hrs,2) + "</span>";
            tdOT12Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime12Pay,2) + "</span>";
            tdOT13Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime13Hrs,2) + "</span>";
            tdOT13Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime13Pay,2) + "</span>";
            tdOT14Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime14Hrs,2) + "</span>";
            tdOT14Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime14Pay,2) + "</span>";
            tdOT15Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime15Hrs,2) + "</span>";
            tdOT15Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime15Pay,2) + "</span>";
            tdOT16Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime16Hrs,2) + "</span>";
            tdOT16Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime16Pay,2) + "</span>";
            tdOT17Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime17Pay,2) + "</span>";
            tdOT17Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime17Hrs,2) + "</span>";
            tdOT18Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime18Pay,2) + "</span>";
            tdOT18Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime18Hrs,2) + "</span>";
            tdOT19Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime19Pay,2) + "</span>";
            tdOT19Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime19Hrs,2) + "</span>";
            tdOT20Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime20Pay,2) + "</span>";
            tdOT20Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime20Hrs,2) + "</span>";
            tdOT21Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime21Pay,2) + "</span>";
            tdOT21Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime21Hrs,2) + "</span>";
            tdOT22Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime22Pay,2) + "</span>";
            tdOT22Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime22Hrs,2) + "</span>";
            tdOT23Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime23Pay,2) + "</span>";
            tdOT23Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime23Hrs,2) + "</span>";
            tdOT24Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime24Pay,2) + "</span>";
            tdOT24Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime24Hrs,2) + "</span>";
            tdOT25Pay = "<span class='font-normal'>" + FormatDecimal(vData.Overtime25Pay,2) + "</span>";
            tdOT25Hrs = "<span class='font-normal'>" + FormatDecimal(vData.Overtime25Hrs,2) + "</span>";

            tdOtherNonTaxableEarnings = "<span class='font-normal'>" + FormatDecimal(vData.OtherNonTaxableEarnings,2) + "</span>";
            tdOtherTaxableEarnings = "<span class='font-normal'>" + FormatDecimal(vData.OtherTaxableEarnings,2) + "</span>";
            tdOtherDeduction = "<span class='font-normal'>" + FormatDecimal(vData.OtherDeduction,2) + "</span>";
            tdOtherTaxableDeduction = "<span class='font-normal'>" + FormatDecimal(vData.TaxableOtherDeduction,2) + "</span>";
            
            tdLoanDeduction = "<span class='font-normal'>" + FormatDecimal(vData.LoanDeduction,2) + "</span>";            
            
            tdTaxableIncome = "<span class='font-normal'>" + FormatDecimal(vData.TaxableIncome,2) + "</span>";
            tdWTax = "<span class='font-normal'>" + FormatDecimal(vData.WTax,2) + "</span>";
            tdSSSEE = "<span class='font-normal'>" + FormatDecimal(vData.SSSEE,2) + "</span>";
            tdSSSWISP = "<span class='font-normal'>" + FormatDecimal(vData.SSSWISPEE,2) + "</span>";
            tdSSSER = "<span class='font-normal'>" + FormatDecimal(vData.SSSER,2) + "</span>";
            tdSSSECER = "<span class='font-normal'>" + FormatDecimal(vData.SSSECER,2) + "</span>";
            
            tdPHICEE = "<span class='font-normal'>" + FormatDecimal(vData.PHICEE,2) + "</span>";
            tdPHICER = "<span class='font-normal'>" + FormatDecimal(vData.PHICER,2) + "</span>";
            tdHDMFEE = "<span class='font-normal'>" + FormatDecimal(vData.HDMFEE,2) + "</span>";
            tdHDMFER = "<span class='font-normal'>" + FormatDecimal(vData.HDMFER,2) + "</span>";
            tdHDMFMP2 = "<span class='font-normal'>" + FormatDecimal(vData.HDMFMP2,2) + "</span>";
            tdGrossPay = "<span class='font-normal'>" + FormatDecimal(vData.GrossPay,2) + "</span>";
            tdTotalDeduction = "<span class='font-normal'>" + FormatDecimal(vData.TotalDeduction,2) + "</span>";
            tdTotalPay = "<span class='font-normal'>" + FormatDecimal(vData.NetPay,2) + "</span>";
            tdNetPay = "<span class='font-normal'>" + FormatDecimal(vData.NetPay,2) + "</span>";

            if(vData.Status=='Un-Posted'){
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
                    curData[2] = tdFullName;

                    curData[3] = tdDepartment;
                    curData[4] = tdDivision;
                    curData[5] = tdLocation;
                    
                    curData[6] = tdSection;
                    curData[7] = tdJobPosition;
                    curData[8] = tdPRSttructure;
                    curData[9] = tdPaymentType;
                    curData[10] = tdPeriodCode;

                    curData[11] = tdYear;
                    curData[12] = tdMonthlyRate;
                    curData[13] = tdDailyRate;
                    curData[14] = tdHourlyRate;
                    curData[15] = tdEmpStatus;
                    
                    curData[16] = tdRegHrs;
                    curData[17] = tdRegPay;
                    curData[18] = tdLateHours;
                    curData[19] = tdLatePay;

                    curData[20] = tdUndertimeHours;
                    curData[21] = tdUndertimePay;
                    curData[22] = tdAbsentHrs;
                    curData[23] = tdAbsentPay;
                    curData[24] = tdTotalLeaveHrs;
                    curData[25] = tdTotalLeavePay;
                    curData[26] = tdTotalOvertimeHrs;
                    curData[27] = tdTotalOvertimePay;

                    curData[28] = tdLeave1Hrs;
                    curData[29] = tdLeave1Pay;
                    curData[30] = tdLeave2Hrs;
                    curData[31] = tdLeave2Pay;
                    curData[32] = tdLeave3Hrs;
                    curData[33] = tdLeave3Pay;
                    curData[34] = tdLeave4Hrs;
                    curData[35] = tdLeave4Pay;
                    curData[36] = tdLeave5Hrs;
                    curData[37] = tdLeave5Pay;
                    curData[38] = tdLeave6Hrs;
                    curData[39] = tdLeave6Pay;
                    curData[40] = tdLeave7Hrs;
                    curData[41] = tdLeave7Pay;
                    curData[42] = tdLeave8Hrs;
                    curData[43] = tdLeave8Pay;
                    curData[44] = tdLeave9Hrs;
                    curData[45] = tdLeave9Pay;
                    curData[46] = tdLeave10Hrs;
                    curData[47] = tdLeave10Pay;
                    curData[48] = tdLeave11Hrs;
                    curData[49] = tdLeave11Pay;
                    curData[50] = tdLeave12Hrs;
                    curData[51] = tdLeave12Pay;
                    curData[52] = tdLeave13Hrs;
                    curData[53] = tdLeave13Pay;
                    curData[54] = tdLeave14Hrs;
                    curData[55] = tdLeave14Pay;
                    curData[56] = tdLeave15Hrs;
                    curData[57] = tdLeave15Pay;
                    curData[58] = tdLeave16Hrs;
                    curData[59] = tdLeave16Pay;
                    curData[60] = tdLeave17Hrs;
                    curData[61] = tdLeave17Pay;
                    curData[62] = tdLeave18Hrs;
                    curData[63] = tdLeave18Pay;
                    curData[64] = tdLeave19Hrs;
                    curData[65] = tdLeave19Pay;
                    curData[66] = tdLeave20Hrs;
                    curData[67] = tdLeave20Pay;

                    curData[68] = tdNDHrs;
                    curData[69] = tdNDPay;

                    curData[70] = tdOT1Hrs;
                    curData[71] = tdOT1Pay;                    
                    curData[72] = tdOT2Hrs;
                    curData[73] = tdOT2Pay;
                    curData[74] = tdOT3Hrs;
                    curData[75] = tdOT3Pay;
                    curData[76] = tdOT4Hrs;
                    curData[77] = tdOT4Pay;
                    curData[78] = tdOT5Hrs;
                    curData[79] = tdOT5Pay;
                    curData[80] = tdOT6Hrs;
                    curData[81] = tdOT6Pay;
                    curData[82] = tdOT7Hrs;
                    curData[83] = tdOT7Pay;
                    curData[84] = tdOT8Hrs;
                    curData[85] = tdOT8Pay;
                    curData[86] = tdOT9Hrs;
                    curData[87] = tdOT9Pay;
                    curData[88] = tdOT10Hrs;
                    curData[89] = tdOT10Pay;
                    curData[90] = tdOT11Hrs;
                    curData[91] = tdOT11Pay;
                    curData[92] = tdOT12Hrs;
                    curData[93] = tdOT12Pay;
                    curData[94] = tdOT13Hrs;
                    curData[95] = tdOT13Pay;
                    curData[96] = tdOT14Hrs;
                    curData[97] = tdOT14Pay;
                    curData[98] = tdOT15Hrs;
                    curData[99] = tdOT15Pay;
                    curData[100] = tdOT16Hrs;
                    curData[101] = tdOT16Pay;
                    curData[102] = tdOT17Hrs;
                    curData[103] = tdOT17Pay;
                    curData[104] = tdOT18Hrs;
                    curData[105] = tdOT18Pay;
                    curData[106] = tdOT19Hrs;
                    curData[107] = tdOT19Pay;
                    curData[108] = tdOT20Hrs;
                    curData[109] = tdOT20Pay;
                    curData[110] = tdOT21Hrs;
                    curData[111] = tdOT21Pay;
                    curData[112] = tdOT22Hrs;
                    curData[113] = tdOT22Pay;
                    curData[114] = tdOT23Hrs;
                    curData[115] = tdOT23Pay;
                    curData[116] = tdOT24Hrs;
                    curData[117] = tdOT24Pay;
                    curData[118] = tdOT25Hrs;
                    curData[119] = tdOT25Pay;

                    curData[120] = tdOtherNonTaxableEarnings;
                    curData[121] = tdOtherTaxableEarnings;
                    curData[122] = tdOtherDeduction;
                    curData[123] = tdOtherTaxableDeduction;
                    curData[124] = tdLoanDeduction;

                    curData[125] = tdTaxableIncome;
                    curData[126] = tdWTax;
                    curData[127] = tdSSSEE;
                    curData[128] = tdSSSWISP;
                    curData[129] = tdSSSER;
                    curData[130] = tdSSSECER;
                    
                    
                    curData[131] = tdPHICEE;
                    curData[132] = tdPHICER;
                    curData[133] = tdHDMFEE;
                    curData[134] = tdHDMFMP2;
                    curData[135] = tdHDMFER;
                    curData[136] = tdGrossPay;
                    curData[137] = tdTotalDeduction;
                    curData[138] = tdTotalPay;                    
                    curData[139] = tdNetPay;
                    curData[140] = tdStatus;
                    

                    this.data(curData).invalidate().draw();
                }
            });

            if(!IsRecordExist){
                //New Row
                tblList.row.add([
                tdID,
                tdEmployeeNo,
                tdFullName,
                tdDepartment,
                tdDivision,
                tdLocation,
                tdSection,
                tdJobPosition,
                tdPRSttructure,
                tdPaymentType,
                tdPeriodCode,
                tdYear,
                tdMonthlyRate,
                tdDailyRate,
                tdHourlyRate,
                tdEmpStatus,                

                tdRegHrs,
                tdRegPay,
                tdLateHours,
                tdLatePay,
                tdUndertimeHours,
                tdUndertimePay,
                tdAbsentHrs,
                tdAbsentPay,
                tdTotalLeaveHrs,
                tdTotalLeavePay,
                tdTotalOvertimeHrs,
                tdTotalOvertimePay,  

                tdLeave1Hrs,
                tdLeave1Pay,
                tdLeave2Hrs,
                tdLeave2Pay,
                tdLeave3Hrs,
                tdLeave3Pay,
                tdLeave4Hrs,
                tdLeave4Pay,
                tdLeave5Hrs,
                tdLeave5Pay,
                tdLeave6Hrs,
                tdLeave6Pay,
                tdLeave7Hrs,
                tdLeave7Pay,
                tdLeave8Hrs,
                tdLeave8Pay,
                tdLeave9Hrs,
                tdLeave9Pay,
                tdLeave10Hrs,
                tdLeave10Pay,
                tdLeave11Hrs,
                tdLeave11Pay,
                tdLeave12Hrs,
                tdLeave12Pay,
                tdLeave13Hrs,
                tdLeave13Pay,
                tdLeave14Hrs,
                tdLeave14Pay,
                tdLeave15Hrs,
                tdLeave15Pay,
                tdLeave16Hrs,
                tdLeave16Pay,
                tdLeave17Hrs,
                tdLeave17Pay,
                tdLeave18Hrs,
                tdLeave18Pay,
                tdLeave19Hrs,
                tdLeave19Pay,
                tdLeave20Hrs,
                tdLeave20Pay,

                tdNDHrs,
                tdNDPay,

                tdOT1Hrs,
                tdOT1Pay,
                tdOT2Hrs,
                tdOT2Pay,
                tdOT3Hrs,
                tdOT3Pay,
                tdOT4Hrs,
                tdOT4Pay,
                tdOT5Hrs,
                tdOT5Pay,
                tdOT6Hrs,
                tdOT6Pay,
                tdOT7Hrs,
                tdOT7Pay,
                tdOT8Hrs,
                tdOT8Pay,
                tdOT9Hrs,
                tdOT9Pay,
                tdOT10Hrs,
                tdOT10Pay,
                tdOT11Hrs,
                tdOT11Pay,
                tdOT12Hrs,
                tdOT12Pay,
                tdOT13Hrs,
                tdOT13Pay,
                tdOT14Hrs,
                tdOT14Pay,
                tdOT15Hrs,
                tdOT15Pay,
                tdOT16Hrs,
                tdOT16Pay,
                tdOT17Hrs,
                tdOT17Pay,
                tdOT18Hrs,
                tdOT18Pay,
                tdOT19Hrs,
                tdOT19Pay,
                tdOT20Hrs,
                tdOT20Pay,
                tdOT21Hrs,
                tdOT21Pay,
                tdOT22Hrs,
                tdOT22Pay,
                tdOT23Hrs,
                tdOT23Pay,
                tdOT24Hrs,
                tdOT24Pay,
                tdOT25Hrs,
                tdOT25Pay,

                tdOtherNonTaxableEarnings,
                tdOtherTaxableEarnings,
                tdOtherDeduction,
                tdOtherTaxableDeduction,
                tdLoanDeduction,
            
                tdTaxableIncome,
                tdWTax,
                tdSSSEE,
                tdSSSWISP,
                tdSSSER,
                tdSSSECER,
            
                tdPHICEE,
                tdPHICER,

                tdHDMFEE,
                tdHDMFMP2,
                tdHDMFER,

                tdGrossPay,
                tdTotalDeduction,

                tdTotalPay,
                tdNetPay,
                tdStatus,
            
              ]).draw();          
            }
        }

    function GenerateExcel(){

      if(total_rec<=0){
        showHasErrorMessage('','No record(s) found base on search criteria.');
         return;   
      }
      
      showHasSuccessMessage('Please wait while generating Payroll Raw Data List excel file. This may take some few minutes.');                     
         
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
              url: "{{ route('get-excel-payroll-raw-data-list') }}",
              dataType: "json",
              success: function(data){

                  if(data.Response=="Success"){
                       resultquery=data.PayrollRawDataExcelList;

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
                            "Payroll Raw Data Report",
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
                            "{{ strtoupper('Department') }}",  
                            "{{ strtoupper('Division') }}", 
                            "{{ strtoupper('Location') }}", 
                            "{{ strtoupper('Employee Name') }}", 
                            "{{ strtoupper('Section') }}", 
                            "{{ strtoupper('Position') }}", 
                            "{{ strtoupper('PR Structure') }}", 
                            "{{ strtoupper('Payment Type') }}", 
                            "{{ strtoupper('Period Code') }}",  
                            "{{ strtoupper('Year') }}", 
                            "{{ strtoupper('Monthly Rate') }}", 
                            "{{ strtoupper('Daily Rate') }}",  
                            "{{ strtoupper('Hourly Rate') }}", 
                            "{{ strtoupper('Employee Status') }}",
                            "{{ strtoupper('Reg Hrs.') }}",  
                            "{{ strtoupper('Reg Pay') }}", 
                            "{{ strtoupper('Late Hrs.') }}", 
                            "{{ strtoupper('Late Pay') }}", 
                            "{{ strtoupper('UT Hrs.') }}" ,
                            "{{ strtoupper('UT Pay') }}",  
                            "{{ strtoupper('Absent Hrs') }}",  
                            "{{ strtoupper('Absent Pay') }}", 
                            "{{ strtoupper('Total Leave Hrs') }}", 
                            "{{ strtoupper('Total Leave Pay') }}",  
                            "{{ strtoupper('Total OT Hrs') }}", 
                            "{{ strtoupper('Total OT Pay') }}", 

                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(1, $LeaveTypeList).'  HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(1, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(2, $LeaveTypeList). ' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(2, $LeaveTypeList).' PAY') }}",
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(3, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(3, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(4, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(4, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(5, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(5, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(6, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(6, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(7, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(7, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(8, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(8, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(9, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(9, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(10, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(10, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(11, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(11, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(12, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(12, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(13, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(13, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(14, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(14, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(15, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(15, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(16, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(16, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(17, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(17, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(18, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(18, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(19, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(19, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(20, $LeaveTypeList).' HRS') }}", 
                            "{{ strtoupper($LeaveTypeModel->getLeaveTypeByID(20, $LeaveTypeList).' PAY') }}", 
                            "{{ strtoupper('ND Hrs') }}", 
                            "{{ strtoupper('ND Pay') }}", 

                            "{{ strtoupper($OTRateModel->getOTByID(1, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(1, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(2, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(2, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(3, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(3, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(4, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(4, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(5, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(5, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(6, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(6, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(7, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(7, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(8, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(8, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(9, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(9, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(10, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(10, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(11, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(11, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(12, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(12, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(13, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(13, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(14, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(14, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(15, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(15, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(16, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(16, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(17, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(17, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(18, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(18, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(19, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(19, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(20, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(20, $OTRateList).' PAY') }}",                                
                            "{{ strtoupper($OTRateModel->getOTByID(21, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(21, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(22, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(22, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(23, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(23, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(24, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(24, $OTRateList).' PAY') }}",
                            "{{ strtoupper($OTRateModel->getOTByID(25, $OTRateList).' HRS') }}", 
                            "{{ strtoupper($OTRateModel->getOTByID(25, $OTRateList).' PAY') }}",
                            "{{ strtoupper('Other Earnings') }}", 
                            "{{ strtoupper('Taxable Other Earnings') }}",
                            "{{ strtoupper('Other Deduction') }}",
                            "{{ strtoupper('Taxable Other Deduction') }}", 
                            "{{ strtoupper('Loan Deduction') }}", 
                            "{{ strtoupper('Taxable Income') }}", 
                            "{{ strtoupper('W/ Tax') }}", 
                            "{{ strtoupper('SSS EE') }}", 
                            "{{ strtoupper('SSS WISP') }}", 
                            "{{ strtoupper('SSS ER') }}", 
                            "{{ strtoupper('SSS EC ER') }}", 
                            "{{ strtoupper('PHIC EE') }}" , 
                            "{{ strtoupper('PHIC ER') }}" , 
                            "{{ strtoupper('HDMF EE') }}" , 
                            "{{ strtoupper('HDMF MP2') }}" , 
                            "{{ strtoupper('HDMF ER') }}" , 
                            "{{ strtoupper('Gross Pay') }}", 
                            "{{ strtoupper('Total Deduction') }}", 
                            "{{ strtoupper('Total Pay') }}", 
                            "{{ strtoupper('NetPay') }}", 
                            "{{ strtoupper('Status') }}"       
                          ];

            xlsRows = resultquery;
      
            createXLSLFormatObj.push(xlsHeader);

            var intRowCnt = 5;

              $.each(xlsRows, function(index, value) {
                  var innerRowData = [];   
                  $.each(value, function(ind, val) {
                                        
                        if(ind == "MonthlyRate" ||
                            ind == "DailyRate" ||
                            ind == "HourlyRate" ||
                            ind == "RegHrs" ||
                            ind == "RegPay" ||
                            ind == "LateHours" ||
                            ind == "LatePay" ||
                            ind == "UndertimeHours" ||
                            ind == "UndertimePay" ||
                            ind == "AbsentHrs" ||
                            ind == "AbsentPay" ||
                            ind == "TotalLeaveHrs" ||
                            ind == "TotalLeavePay" ||
                            ind == "TotalOvertimeHrs" ||
                            ind == "TotalOvertimePay" ||

                            ind == "Leave1Hrs" ||
                            ind == "Leave1Pay" ||
                            ind == "Leave2Hrs" ||
                            ind == "Leave2Pay" ||
                            ind == "Leave3Hrs" ||
                            ind == "Leave3Pay" ||
                            ind == "Leave4Hrs" ||
                            ind == "Leave4Pay" ||
                            ind == "Leave5Hrs" ||
                            ind == "Leave5Pay" ||
                            ind == "Leave6Hrs" ||
                            ind == "Leave6Pay" ||
                            ind == "Leave7Hrs" ||
                            ind == "Leave7Pay" ||
                            ind == "Leave8Hrs" ||
                            ind == "Leave8Pay" ||
                            ind == "Leave9Hrs" ||
                            ind == "Leave9Pay" ||
                            ind == "Leave10Hrs" ||
                            ind == "Leave10Pay" ||
                            ind == "Leave11Hrs" ||
                            ind == "Leave11Pay" ||
                            ind == "Leave12Hrs" ||
                            ind == "Leave12Pay" ||
                            ind == "Leave13Hrs" ||
                            ind == "Leave13Pay" ||
                            ind == "Leave14Hrs" ||
                            ind == "Leave14Pay" ||
                            ind == "Leave15Hrs" ||
                            ind == "Leave15Pay" ||
                            ind == "Leave16Hrs" ||
                            ind == "Leave16Pay" ||
                            ind == "Leave17Hrs" ||
                            ind == "Leave17Pay" ||
                            ind == "Leave18Hrs" ||
                            ind == "Leave18Pay" ||
                            ind == "Leave19Hrs" ||
                            ind == "Leave19Pay" ||
                            ind == "Leave20Hrs" ||
                            ind == "Leave20Pay" ||

                            ind == "NightDifferentialHrs" ||
                            ind == "NightDifferentialPay" ||

                            ind == "Overtime1Hrs" ||
                            ind == "Overtime1Pay" ||
                            ind == "Overtime2Hrs" ||
                            ind == "Overtime2Pay" ||
                            ind == "Overtime3Hrs" ||
                            ind == "Overtime3Pay" ||
                            ind == "Overtime4Hrs" ||
                            ind == "Overtime4Pay" ||
                            ind == "Overtime5Hrs" ||
                            ind == "Overtime5Pay" ||
                            ind == "Overtime6Hrs" ||
                            ind == "Overtime6Pay" ||
                            ind == "Overtime7Hrs" ||
                            ind == "Overtime7Pay" ||
                            ind == "Overtime8Hrs" ||
                            ind == "Overtime8Pay" ||
                            ind == "Overtime9Hrs" ||
                            ind == "Overtime9Pay" ||
                            ind == "Overtime10Hrs" ||
                            ind == "Overtime10Pay" ||
                            ind == "Overtime11Hrs" ||
                            ind == "Overtime11Pay" ||
                            ind == "Overtime12Hrs" ||
                            ind == "Overtime12Pay" ||
                            ind == "Overtime13Hrs" ||
                            ind == "Overtime13Pay" ||
                            ind == "Overtime14Hrs" ||
                            ind == "Overtime14Pay" ||
                            ind == "Overtime15Hrs" ||
                            ind == "Overtime15Pay" ||
                            ind == "Overtime16Hrs" ||
                            ind == "Overtime16Pay" ||
                            ind == "Overtime17Hrs" ||
                            ind == "Overtime17Pay" ||
                            ind == "Overtime18Hrs" ||
                            ind == "Overtime18Pay" ||
                            ind == "Overtime19Hrs" ||
                            ind == "Overtime19Pay" ||
                            ind == "Overtime20Hrs" ||
                            ind == "Overtime20Pay" ||
                            ind == "Overtime21Hrs" ||
                            ind == "Overtime21Pay" ||
                            ind == "Overtime22Hrs" ||
                            ind == "Overtime22Pay" ||
                            ind == "Overtime23Hrs" ||
                            ind == "Overtime23Hrs" ||
                            ind == "Overtime24Hrs" ||
                            ind == "Overtime24Pay" ||
                            ind == "Overtime25Hrs" ||
                            ind == "Overtime25Pay" ||

                            ind == "OtherNonTaxableEarnings" ||
                            ind == "OtherTaxableEarnings" ||
                            ind == "OtherDeduction" ||
                            ind == "TaxableOtherDeduction" ||
                            ind == "LoanDeduction" ||
                            ind == "TaxableIncome" ||

                            ind == "WTax" ||

                            ind == "SSSEE" ||
                            ind == "SSSWISPEE" ||
                            ind == "SSSER" ||
                            ind == "SSSECER" ||

                            ind == "PHICEE" ||
                            ind == "PHICER" ||

                            ind == "HDMFEE" ||
                            ind == "HDMFMP2" ||
                            ind == "HDMFER" ||

                            ind == "GrossPay" ||
                            ind == "TotalDeduction" ||
                            ind == "NetPay"
                            ){
                            innerRowData.push(val);
                        }else{
                            innerRowData.push(val);
                        }

                  });

                  createXLSLFormatObj.push(innerRowData);
                  intRowCnt = intRowCnt + 1;
              });

            var ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);
            for (var i = 6; i <= intRowCnt; i++) {
                for (var c = 2; c < xlsHeader.length; c++){
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
                XLSX.utils.book_append_sheet(wb, ws,"Posted Payroll Raw Data");  
            }else{
                XLSX.utils.book_append_sheet(wb, ws,"Un-Posted Payroll Raw Data");    
            }
            
            /* generate file and download */
            const wbout = XLSX.write(wb, { type: "array", bookType: "xlsx" });
            saveAs(new Blob([wbout], { type: "application/octet-stream" }), "Payroll-Raw-Data-Report.xlsx");

            showHasSuccessMessage('Excel file has successfully created & downloaded at Download Folder');     
                          
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



