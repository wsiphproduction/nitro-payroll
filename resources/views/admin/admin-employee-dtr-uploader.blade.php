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

.pagination .page-item .page-link{
    border: 1px solid #DFE3E7 !important;
    color:blue;

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

</style>

<style type="text/css">
        

      table.alt-background th {
            background-color: #475F7B;
            position: sticky !important;
            top: 0;
            color: white;
        }
        
     
        /*  table.alt-background th:nth-child(1),  table.alt-background td:nth-child(1) {
            width: 25px;
            position: sticky;
            left: -7px;
            z-index: 4;
            border-left: 1px solid #ddd ;
        }*/
        

      /* table.alt-background th:nth-child(2),  table.alt-background td:nth-child(2) {
           width: 120px;
            position: sticky;
            left: -7px; 
            z-index: 1;
            border-right: 1px solid #ddd ;
        }

         table.alt-background th:nth-child(3),  table.alt-background td:nth-child(3) {
           width: 150px;
            position: sticky;
            left: 90px; 
            z-index: 1;
            border-right: 1px solid #ddd ;
        }
        
       table.alt-background  th:nth-child(2) {
            z-index: 2;
        }
        
        table.alt-background th:nth-child(2), table.alt-background  th:nth-child(3) {
            z-index: 2;
        }
        

        table.alt-background tr:nth-child(odd) td {
            background-color: #f5f5f5;
            border: 1px solid #ddd !important;
        }
        
        table.alt-background tr:nth-child(even) td {
            background-color: white;
            border: 1px solid #ddd !important;
        }*/
        
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
                                    <li class="breadcrumb-item active">Employee DTR Transaction List
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
                                    <h4 class="card-title">Employee DTR Summary</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <fieldset>
                                                    <div class="input-group">
                                                          <select id="selSearchStatus" class="form-control" style="width:15%">
                                                          <option value="">All Record</option>
                                                          <option disabled="disabled">[ By Location Option ]</option>
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
                                                        </select>

                                                        <input type="text" class="form-control searchtext" placeholder="Search Here.." style="width: 39%;margin-left: 6px;">

                                                        <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border" tooltip="Search Here.." tooltip-position="top" > 
                                                            <i class="bx bx-search"></i>
                                                        </button>
                                                        
                                                      @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload==1)
                                                        <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="NewRecord()" tooltip="Create New" tooltip-position="top">
                                                            <i class="bx bx-plus"></i> New
                                                        </button>
                                                       @endif    

                                                      @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload==1)
                                                         <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="UploadExcelRecord()" tooltip="Upload DTR/TSS Excel" tooltip-position="top">
                                                           <i class="bx bx-upload"></i> Upload TSS/DTR Excel
                                                        </button>
                                                      @endif 

                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>

                                        <div id="style-2" class="table-responsive col-md-12 table_default_height table-wrapper">
                                              <table id="tblList" class="table zero-configuration complex-headers border alt-background">
                                                 <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th style="width:1%;">
                                                        <th style="width: 10%;color: white;">EMPLOYEE ID </th>
                                                        <th style="width: 12%;color: white;">EMPLOYEE NAME </th>
                                                        <th style="width: 3%;color: white;">CODE</th>
                                                        <th style="width: 6%;color: white;">YEAR</th>                                                        
                                                        <th style="width: 4%;color: white;">HOUR RATE </th>
                                                        <th style="width: 4%;color: white;">REG. HRS </th>
                                                        <th style="width: 4%;color: white;">LATE HRS </th>
                                                        <th style="width: 4%;color: white;">UD HRS </th>
                                                        <th style="width: 4%;color: white;">ND HRS </th>
                                                        <th style="width: 4%;color: white;">TOTAL ABSENT </th>

                                                        <th style="width: 4%;color: white;">SL</th>
                                                        <th style="width: 4%;color: white;">VL</th>
                                                        <th style="width: 4%;color: white;">EL</th>
                                                        <th style="width: 4%;color: white;">ML</th>
                                                        <th style="width: 4%;color: white;">PL</th>
                                                        <th style="width: 4%;color: white;">SIL</th>
                                                        <th style="width: 4%;color: white;">ADO</th>
                                                        <th style="width: 4%;color: white;">SPL</th>
                                                        <th style="width: 6%;color: white;">LEAVE09</th>
                                                        <th style="width: 4%;color: white;">SWL</th>

                                                        <th style="width: 6%;color: white;">LEAVE11</th>
                                                        <th style="width: 6%;color: white;">LEAVE12</th>
                                                        <th style="width: 6%;color: white;">LEAVE13</th>
                                                        <th style="width: 6%;color: white;">LEAVE14</th>
                                                        <th style="width: 6%;color: white;">LEAVE15</th>
                                                        <th style="width: 6%;color: white;">LEAVE16</th>
                                                        <th style="width: 6%;color: white;">LEAVE17</th>
                                                        <th style="width: 6%;color: white;">LEAVE18</th>
                                                        <th style="width: 6%;color: white;">LEAVE19</th>
                                                        <th style="width: 6%;color: white;">LEAVE20</th>

                                                        <th style="width: 4%;color: white;">ROT</th>
                                                        <th style="width: 4%;color: white;">NPROT</th>
                                                        <th style="width: 4%;color: white;">DO</th>
                                                        <th style="width: 4%;color: white;">SH</th>
                                                        <th style="width: 4%;color: white;">LH</th>
                                                        <th style="width: 4%;color: white;">SHDO</th>
                                                        <th style="width: 4%;color: white;">LHDO</th>
                                                        <th style="width: 4%;color: white;">OTDO</th>
                                                        <th style="width: 4%;color: white;">OTSH</th>
                                                        <th style="width: 4%;color: white;">OTLH</th>
                                                        <th style="width: 4%;color: white;">OTSHDO</th>
                                                        <th style="width: 4%;color: white;">OTLHDO</th>
                                                        <th style="width: 4%;color: white;">NNDO</th>
                                                        <th style="width: 4%;color: white;">NDSH</th>
                                                        <th style="width: 4%;color: white;">NDLH</th>
                                                        <th style="width: 4%;color: white;">NDSHDO</th>
                                                        <th style="width: 4%;color: white;">NDLHDO</th>
                                                        <th style="width: 4%;color: white;">NPDO</th>
                                                        <th style="width: 4%;color: white;">NPSH</th>
                                                        <th style="width: 4%;color: white;">NPLH</th>
                                                        <th style="width: 4%;color: white;">NPSHDO</th>
                                                        <th style="width: 4%;color: white;">NPLHDO</th>

                                                        <th style="width: 4%;color: white;">OT HRS 23</th>
                                                        <th style="width: 4%;color: white;">OT HRS 24</th>
                                                        <th style="width: 4%;color: white;">OT HRS 25</th>
                                                                                                                                                                                                                                                                                                                                                                                                                                    
                                                        <th style="color: white;">STATUS</th>
                                                        
                                                    </tr>
                                                </thead> 
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
                    <h5 class="modal-title white-color">Employee DTR Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div id="style-2" class="modal-body">

               <input type="hidden" id="EmployeeDTRID" value="0" readonly>
               <input type="hidden" id="DTRTable" value="0" readonly>

                <div class="row"> 
                        <div class="col-md-4">
                          <fieldset class="form-group">
                            <label for="TransDate">Transaction Date: <span class="required_field">*</span></label>
                             <div class="div-percent">
                                  <input id="TransDate" type="text" class="form-control " placeholder="mm/dd/yyyy" autocomplete="off" disabled style="border: 1px solid rgb(204, 204, 204);"><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 21px;"></i> </span>
                                </div>
                          </fieldset>
                        </div>
                         <div class="col-md-4">
                            <fieldset class="form-group">
                              <label for="Status">Status: <span class="required_field">*</span></label>
                              <input id="Status" type="text" class="form-control " placeholder="Status" disabled style="font-weight: bold; border: 1px solid rgb(204, 204, 204);">
                            </fieldset>
                        </div>

                        <div class="col-md-4">
                         <fieldset class="form-group">
                            <label for="PayrollPeriodName">Payroll Period: <span class="required_field">* </span></label><span class="search-txt">(Type & search from the list)</span>
                             <div class="div-percent">
                                    <input id="PayrollPeriodID" type="hidden" value="0">
                                    <input id="PayrollPeriodYear" type="hidden" value="">
                                    <input id="PayrollPeriodCode" type="hidden" value="">
                                   <input id="PayrollPeriodName" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-complete-type="payroll-period" placeholder="Payroll Period"><span class='percent-sign'  onclick="ClearPayrollPeriod()"> <i class="bx bx-trash" style="line-height: 21px;cursor:pointer;"></i> </span>
                                </div> 
                          </fieldset>
                        </div>
                    </div>

                    <div class="row">
                         <div class="col-md-4">
                        <fieldset class="form-group">
                            <label for="PayrollPeriodName">Employee No:</label>                                     
                             <input id="EmployeeNo" type="text" class="form-control " placeholder="Employee No" disabled>                             
                          </fieldset>
                        </div>
                      
                        <div class="col-md-8">
                          <fieldset class="form-group">
                            <label for="EmployeeName">Employee Name: <span class="required_field">* </span></label><span class="search-txt">(Type & search from the list)</span>
                             <div class="div-percent">
                                   <input id="EmployeeID" type="hidden" value="0">                                    
                                   <input id="EmployeeName" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-complete-type="employee" placeholder="Employee Name"><span class='percent-sign' onclick="ClearEmployee()"> <i class="bx bx-trash" style="line-height: 21px;cursor:pointer;"></i> </span>
                                </div> 
                          </fieldset>
                        </div>
                   </div>
                      
                   <div class="row">
                        <div class="form-group" style="width:100%;padding: 5px;">                             
                            <label for="Remarks">Remarks:    <span style="font-size: 11px;color: #b62020;  text-transform: lowercase;font-style: italic; font-weight: normal;"> ( characters: &nbsp; </span> <span class="remaining_chars"> 250</span></label><span style="font-size: 11px;color: #b62020;  text-transform: lowercase;font-style: italic; font-weight: normal;">&nbsp;)</span>
                            <textarea id="Remarks" class="form-control" rows="4"></textarea>                           
                       </div>
                  </div>  
                     
                   <hr>
                     
                     <!--TAB  -->
                     <div class="row">
                        <div class="col-md-12">
                          <nav>
                                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist" style="border-top-left-radius: 32px; border-top-right-radius: 32px;">
                                   <a class="nav-item nav-link" id="nav-basic-tab" data-toggle="tab" href="#nav-basic" role="tab" aria-controls="nav-basic" aria-selected="true" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"><i class='bx bx-clipboard mr-1'></i> Basic Information</a>
                                   <a class="nav-item nav-link" id="nav-leaves-tab" data-toggle="tab" href="#nav-leaves" role="tab" aria-controls="nav-leaves" aria-selected="false" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"> <i class='bx bx-notepad mr-1'></i> Leave Information</a>
                                   <a class="nav-item nav-link" id="nav-othours-tab" data-toggle="tab" href="#nav-othours" role="tab" aria-controls="nav-othours" aria-selected="false" style="width: 100px;border-right: 1px solid white;padding-bottom: 6px;border-top-right-radius: 15px 20px;"> <i class='bx bx-time mr-1'></i> O.T Hours Information</a>
                                </div>
                          </nav>
                          <div class="tab-content" id="nav-tabContent">
                         
                            <div class="tab-pane fade show" id="nav-basic" role="tabpanel" aria-labelledby="nav-basic-tab">
                              <!-- Basic Information -->
                                   <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31)">
                                        <div id="tblList_wrapper" class="dataTables_wrapper no-footer">
                                            <table id="tblMenuList" class="table zero-configuration complex-headers dataTable no-footer" role="grid">
                                                <tbody>
                                              
                                                <tr role="row" class="odd">
                                                    <td style="width: 15%;padding: 2px 1px !important;"> 
                                                        <span style="margin-left: 20px;line-height: 40px;">  <label for="EmployeeRate">Hourly Rate: <span class="required_field">* </span></label></span>
                                                    </td>
                                                   <td style="width: 20%;padding: 2px 1px !important;border-right: 1px solid #DFE3E7;padding-right: 10px !important;">
                                                      <input type="hidden" id="EmployeeRateID" value="0" readonly>
                                                        <input type="text" id="EmployeeRate" class="form-control  DecimalOnly text-align-right" placeholder="Employee Hour Rate" autocomplete="off" disabled>
                                                    </td>
                                                     <td style="width: 15%;"> 
                                                        <span style="margin-left: 20px;line-height: 40px;"> <label for="RegularHours">Regular Hour: <span class="required_field">* </span></label></span>
                                                    </td>
                                                    <td style="width: 20%;"> 
                                                        <input type="text" id="RegularHours" class="form-control DecimalOnly text-align-right" placeholder="Regular Hours" autocomplete="off">
                                                    </td>
                                                 
                                                </tr>
                                                  <tr role="row" class="odd">
                                                    <td style="width: 15%;padding: 2px 1px !important;"> 
                                                        <span style="margin-left: 20px;line-height: 40px;">  <label for="LateHours">Late Hours: <span class="required_field">* </span></label></span>
                                                    </td>
                                                    <td style="width: 20%;padding: 2px 1px !important;border-right: 1px solid #DFE3E7;padding-right: 10px !important;">
                                                        <input type="text" id="LateHours" class="form-control DecimalOnly text-align-right"  placeholder="Late Hours" autocomplete="off"> 
                                                    </td>
                                                      <td style="width: 20%;padding: 2px 1px !important;"> 
                                                        <span style="margin-left: 20px;line-height: 40px;">  <label for="UndertimeHours">Undertime Hours: <span class="required_field">* </span></label></span>
                                                    </td>
                                                    <td style="width: 20%;padding: 2px 1px !important;"> 
                                                        <input type="text" id="UndertimeHours" class="form-control DecimalOnly text-align-right" placeholder="Undertime Hours" autocomplete="off">
                                                    </td>
                                                 
                                                </tr>
                                                  <tr role="row" class="odd">
                                                      <td style="width: 20%;padding: 2px 1px !important;"> 
                                                        <span style="margin-left: 20px;line-height: 40px;"> <label for="NDHours">ND Hours: <span class="required_field">* </span></label></span>
                                                    </td>
                                                    <td style="width: 20%;padding: 2px 1px !important;border-right: 1px solid #DFE3E7;padding-right: 10px !important;"> 
                                                        <input type="text" id="NDHours" class="form-control DecimalOnly text-align-right" placeholder="ND Hours" autocomplete="off">
                                                    </td>
                                                       <td style="width: 20%;padding: 2px 1px !important;"> 
                                                        <span style="margin-left: 20px;line-height: 40px;">  <label for="Absent">Total Absent Hours: <span class="required_field">* </span></label></span>
                                                    </td>
                                                      <td style="width: 20%;padding: 2px 1px !important;"> 
                                                        <input type="text" id="Absent" class="form-control DecimalOnly text-align-right" placeholder="Total Absent  Hours" autocomplete="off">
                                                    </td>
                                                 
                                                </tr>
                                                </tbody>
                                            </table></div>
                                        </div>
                             
                                    <br> <br> <br>
                                  <!--End Basic Information -->       
                            </div>
                            <div class="tab-pane fade" id="nav-leaves" role="tabpanel" aria-labelledby="nav-leaves-tab">
                      
                              <!-- Leave Information -->
                                  <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31)">
                                        <div id="tblList_wrapper" class="dataTables_wrapper no-footer">
                                            <table id="tblMenuList" class="table zero-configuration complex-headers dataTable no-footer" role="grid">
                                                <tbody>
                                              
                                                <tr role="row" class="odd">
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave01">Sick Leave: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(Leave 01)</span></label>
                                                         <input id="Leave01" type="text" class="form-control DecimalOnly text-align-right" placeholder="Sick Leave:" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave02">Vacation Leave: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(Leave 02)</span></label>
                                                         <input id="Leave02" type="text" class="form-control DecimalOnly text-align-right" placeholder="Vacation Leave" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave11">Leave 11: <span class="required_field">* </span></label>
                                                         <input id="Leave11" type="text" class="form-control DecimalOnly text-align-right" placeholder="Leave 11" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave12">Leave 12: <span class="required_field">* </span></label>
                                                         <input id="Leave12" type="text" class="form-control DecimalOnly text-align-right" placeholder="Leave 12" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                               </tr>
                                                <tr role="row" class="odd">
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave03">Emergency Leave:  <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(Leave 03)</span></label>
                                                         <input id="Leave03" type="text" class="form-control DecimalOnly text-align-right" placeholder="Emergency Leave" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave04">Maternity Leave: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(Leave 04)</span></label>
                                                         <input id="Leave04" type="text" class="form-control DecimalOnly text-align-right" placeholder="Maternity Leave" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave13">Leave 13: <span class="required_field">* </span></label>
                                                         <input id="Leave13" type="text" class="form-control DecimalOnly text-align-right" placeholder="Leave 13" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave14">Leave 14: <span class="required_field">* </span></label>
                                                         <input id="Leave14" type="text" class="form-control DecimalOnly text-align-right" placeholder="Leave 14" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                               </tr>
                                               <tr role="row" class="odd">
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave05">Paternity Leave:  <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(Leave 05)</span></label>
                                                         <input id="Leave05" type="text" class="form-control DecimalOnly text-align-right" placeholder="Paternity Leave" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave06">Service Incentive Leave: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(Leave 06)</span></label>
                                                         <input id="Leave06" type="text" class="form-control DecimalOnly text-align-right" placeholder="Service Incentive Leave" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave15">Leave 15: <span class="required_field">* </span></label>
                                                         <input id="Leave15" type="text" class="form-control DecimalOnly text-align-right" placeholder="Leave 15" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave16">Leave 16: <span class="required_field">* </span></label>
                                                         <input id="Leave16" type="text" class="form-control DecimalOnly text-align-right" placeholder="Leave 16" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                               </tr>
                                               <tr role="row" class="odd">
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave07">ADO: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(Leave 07)</span></label>
                                                         <input id="Leave07" type="text" class="form-control DecimalOnly text-align-right" placeholder="ADO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave08">SPL: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(Leave 08)</span></label>
                                                         <input id="Leave08" type="text" class="form-control DecimalOnly text-align-right" placeholder="SPL" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave17">Leave 17: <span class="required_field">* </span></label>
                                                         <input id="Leave17" type="text" class="form-control DecimalOnly text-align-right" placeholder="Leave 17" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave18">Leave 18: <span class="required_field">* </span></label>
                                                         <input id="Leave18" type="text" class="form-control DecimalOnly text-align-right" placeholder="Leave 18" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                               </tr>
                                                <tr role="row" class="odd">
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave09">Leave 09: <span class="required_field">* </span></label>
                                                         <input id="Leave09" type="text" class="form-control DecimalOnly text-align-right" placeholder="" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave10">SWL: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(Leave 10)</span></label>
                                                         <input id="Leave10" type="text" class="form-control DecimalOnly text-align-right" placeholder="SWL" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Status">Leave 19: <span class="required_field">* </span></label>
                                                         <input id="Leave19" type="text" class="form-control DecimalOnly text-align-right" placeholder="Leave 19" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;"> 
                                                       <fieldset class="form-group">
                                                         <label for="Leave20">Leave 20: <span class="required_field">* </span></label>
                                                         <input id="Leave20" type="text" class="form-control DecimalOnly text-align-right" placeholder="Leave 20" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                               </tr>
                                        
                                                </tbody>
                                            </table></div>
                                        </div>
                               
                               <!--End Leave Information --> 
                            </div>

                             <div class="tab-pane fade" id="nav-othours" role="tabpanel" aria-labelledby="nav-othours-tab">                    
                                <!-- OT Hours Information -->
                                      <div class="table-responsive col-md-12" style="border: 2px solid rgb(246, 140, 31)">
                                        <div id="tblList_wrapper" class="dataTables_wrapper no-footer">
                                            <table id="tblMenuList" class="table zero-configuration complex-headers dataTable no-footer" role="grid">
                                                <tbody>
                                              
                                                <tr role="row" class="odd">
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours01">ROT: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 01)</span></label>
                                                         <input id="OTHours01" type="text" class="form-control DecimalOnly text-align-right" placeholder="ROT" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours02">NPROT: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 02)</span></label>
                                                         <input id="OTHours02" type="text" class="form-control DecimalOnly text-align-right" placeholder="NPROT" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours13">NNDO: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 13)</span></label>
                                                         <input id="OTHours13" type="text" class="form-control DecimalOnly text-align-right" placeholder="NNDO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;"> 
                                                        <fieldset class="form-group">
                                                         <label for="OTHours14">NDSH: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 14)</span></label>
                                                         <input id="OTHours14" type="text" class="form-control DecimalOnly text-align-right" placeholder="NDSH" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                               </tr>

                                               <tr role="row" class="odd">
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours03">DO: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 03)</span></label>
                                                         <input id="OTHours03" type="text" class="form-control DecimalOnly text-align-right" placeholder="DO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours04">SH: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 04)</span></label>
                                                         <input id="OTHours04" type="text" class="form-control DecimalOnly text-align-right" placeholder="SH" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours15">NDLH: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 15)</span></label>
                                                         <input id="OTHours15" type="text" class="form-control DecimalOnly text-align-right" placeholder="NDLH" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;"> 
                                                        <fieldset class="form-group">
                                                         <label for="OTHours16">NDSHDO: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 16)</span></label>
                                                         <input id="OTHours16" type="text" class="form-control DecimalOnly text-align-right" placeholder="NDSHDO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                               </tr>

                                                <tr role="row" class="odd">
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours05">LH: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 05)</span></label>
                                                         <input id="OTHours05" type="text" class="form-control DecimalOnly text-align-right" placeholder="LH" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours06">SHDO: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 06)</span></label>
                                                         <input id="OTHours06" type="text" class="form-control DecimalOnly text-align-right" placeholder="SHDO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours17">NDLHDO: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 17)</span></label>
                                                         <input id="OTHours17" type="text" class="form-control DecimalOnly text-align-right" placeholder="NDLHDO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;"> 
                                                        <fieldset class="form-group">
                                                         <label for="OTHours18">NPDO: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 18)</span></label>
                                                         <input id="OTHours18" type="text" class="form-control DecimalOnly text-align-right" placeholder="NPDO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                               </tr>

                                                <tr role="row" class="odd">
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours07">LHDO: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 07)</span></label>
                                                         <input id="OTHours07" type="text" class="form-control DecimalOnly text-align-right" placeholder="LHDO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours08">OTDO: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 08)</span></label>
                                                         <input id="OTHours08" type="text" class="form-control DecimalOnly text-align-right" placeholder="OTDO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours19">NPSH: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 19)</span></label>
                                                         <input id="OTHours19" type="text" class="form-control DecimalOnly text-align-right" placeholder="NPSH" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;"> 
                                                        <fieldset class="form-group">
                                                         <label for="OTHours20">NPLH: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 20)</span></label>
                                                         <input id="OTHours20" type="text" class="form-control DecimalOnly text-align-right" placeholder="NPLH" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                               </tr>

                                                 <tr role="row" class="odd">
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours09">OTSH: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 09)</span></label>
                                                         <input id="OTHours09" type="text" class="form-control DecimalOnly text-align-right" placeholder="OTSH" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours10">OTLH: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 10)</span></label>
                                                         <input id="OTHours10" type="text" class="form-control DecimalOnly text-align-right" placeholder="OTLH" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours21">NPSHDO: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 21)</span></label>
                                                         <input id="OTHours21" type="text" class="form-control DecimalOnly text-align-right" placeholder="NPSHDO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;"> 
                                                        <fieldset class="form-group">
                                                         <label for="OTHours22">NPLHDO: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 22)</span></label>
                                                         <input id="OTHours22" type="text" class="form-control DecimalOnly text-align-right" placeholder="NPLHDO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                               </tr>

                                               <tr role="row" class="odd">
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="autocomplete">OTSHDO: <span class="required_field">* </span><span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 11)</span></label>
                                                         <input id="OTHours11" type="text" class="form-control DecimalOnly text-align-right" placeholder="OTSHDO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="autocomplete">OTLHDO: <span style="font-size:11px;color:red;font-weight: normal;">(OT Hours 12)</span></label>
                                                         <input id="OTHours12" type="text" class="form-control DecimalOnly text-align-right" placeholder="OTLHDO" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;border-right: 1px solid #DFE3E7;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours23">OT Hours 23: <span class="required_field">* </span></label>
                                                         <input id="OTHours23" type="text" class="form-control DecimalOnly text-align-right" placeholder="OT Hours 23" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                                    <td style="width: 20%;"> 
                                                        <fieldset class="form-group">
                                                         <label for="OTHours24">OT Hours 24: <span class="required_field">* </span></label>
                                                         <input id="OTHours24" type="text" class="form-control DecimalOnly text-align-right" placeholder="OT Hours 24" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                               </tr>

                                                <tr role="row" class="odd">
                                                    <td style="width: 20%;"></td>
                                                    <td style="width: 20%;"></td>
                                                    <td style="width: 20%;"></td>
                                                    </td>
                                                        <td style="width: 20%;"> 
                                                       <fieldset class="form-group">
                                                         <label for="OTHours25">OT Hours 25: <span class="required_field">* </span></label>
                                                         <input id="OTHours25" type="text" class="form-control DecimalOnly text-align-right" placeholder="OT Hours 25" autocomplete="off">
                                                      </fieldset>
                                                    </td>
                                               </tr>
                                                </tbody>
                                            </table></div>
                                        </div>
                               
                               <!--End OT Hours Information --> 
                            </div>
                          </div>
                        </div>
                    </div>


                    <!-- End Tab  -->
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

     <!-- MODAL -->
    <div id="upload-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title white-color">DTR/TSS Excel Uploader </h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>                

                </div>
                <div class="modal-body">
                    <div class="row">
                         <h5 style="padding-top:10px;padding-bottom: 10px;">Browse TSS/DTR Summary csv file:</h5>
                         <fieldset class="form-group">
                                <label for="myfile">Select files:</label>
                                 <input type="file" id="ExcelFile" name="ExcelFile" accept=".csv"/>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer">

                     <a href="{{URL::asset('public/web/excel template/Employee DTR-TSS-Summary-Template.csv')}}" id="btnDownloadTemplate" class="btn btn-light-secondary">
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
    <!-- END MODAL -->

     <!-- APPROVED MODAL -->
    <div id="approve-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background:green;">
                     <h5 class="modal-title white-color">Set approve employee DTR </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                         <fieldset class="form-group">
                            <input type="hidden" class="DTRIDStatus" value="0" readonly>
                            <label style="text-transform: unset;">Do you want to approve this record?</label>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer" style="padding-right: 25px;">
                     <button id="btnApproveDTR" type="button" class="btn btn-primary ml-1" style="background:green !important;" onclick="SetDTRStatus('Approved')">
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

     <!-- APPROVED MODAL -->
    <div id="over-write-approve-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background:blue;">
                     <h5 class="modal-title white-color">Over write approve employee DTR </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                         <fieldset class="form-group">
                            <input type="hidden" class="DTRIDStatus" value="0" readonly>
                            <label style="text-transform: unset;">This employee record has an existing approved DTR. <br>Do you want to over write previous approved DTR record?</label>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer" style="padding-right: 25px;">
                     <button id="btnApproveDTR" type="button" class="btn btn-primary ml-1" style="background:blue !important;" onclick="SetDTRStatus('Overwrite')">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Set To Over Write</span>
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
                    <h5 class="modal-title white-color">Set cancel employee DTR </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                         <fieldset class="form-group">
                            <input type="hidden" class="DTRIDStatus" value="0" readonly>
                            <label style="text-transform: unset;">Do you want to cancel this record?</label>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer" style="padding-right: 25px;">
                     <button id="btnCancelDTR" type="button" class="btn btn-primary ml-1" style="background:red !important;" onclick="SetDTRStatus('Cancelled')">
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

  <!--EXCEL REVIEW MODAL -->
 <div id="excel-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="left:10px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content modal-xl">

                <div class="modal-header">
                    <h5 class="modal-title white-color"> Review Excel Data: <span id="spnUploadedRecord">0</span> has uploaded from excel.  <span id="spnUploadedErrorRecord"></span></h5> 
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
                                    <th style="width: 3%;color: white;">PERIOD</th>
                                    <th style="width: 4%;color: white;">YEAR</th>
                                    <th style="width: 8%;color: white;">EMPLOYEE ID</th>
                                    <th style="width: 12%;color: white;">EMPLOYEE NAME</th>
                                    <th style="width: 4%;color: white;">HRS. RATE</th>
                                    <th style="width: 4%;color: white;">REG. HRS.</th>
                                    <th style="width: 4%;color: white;">LATE HRS.</th>
                                    <th style="width: 4%;color: white;">UD HRS.</th>
                                    <th style="width: 4%;color: white;">ND HRS.</th>
                                    <th style="width: 4%;color: white;">ABSENT</th>

                                    <th style="width: 2%;color: white;">SL</th>
                                    <th style="width: 2%;color: white;">VL</th>
                                    <th style="width: 2%;color: white;">EL</th>
                                    <th style="width: 2%;color: white;">ML</th>
                                    <th style="width: 2%;color: white;">PL</th>
                                    <th style="width: 2%;color: white;">SIL</th>
                                    <th style="width: 2%;color: white;">ADO</th>
                                    <th style="width: 2%;color: white;">SPL</th>
                                    <th style="width: 4%;color: white;">LEAVE09</th>
                                    <th style="width: 2%;color: white;">SWL</th>
                                    <th style="width: 4%;color: white;">LEAVE11</th>
                                    <th style="width: 4%;color: white;">LEAVE12</th>
                                    <th style="width: 4%;color: white;">LEAVE13</th>
                                    <th style="width: 4%;color: white;">LEAVE14</th>
                                    <th style="width: 4%;color: white;">LEAVE15</th>
                                    <th style="width: 4%;color: white;">LEAVE16</th>
                                    <th style="width: 4%;color: white;">LEAVE17</th>
                                    <th style="width: 4%;color: white;">LEAVE18</th>
                                    <th style="width: 4%;color: white;">LEAVE19</th>
                                    <th style="width: 4%;color: white;">LEAVE20</th>

                                    <th style="width: 4%;color: white;">ROT</th>
                                    <th style="width: 4%;color: white;">NPROT</th>
                                    <th style="width: 4%;color: white;">DO</th>
                                    <th style="width: 4%;color: white;">SH</th>
                                    <th style="width: 4%;color: white;">LH</th>
                                    <th style="width: 4%;color: white;">SHDO</th>
                                    <th style="width: 4%;color: white;">LHDO</th>
                                    <th style="width: 4%;color: white;">OTDO</th>
                                    <th style="width: 4%;color: white;">OTSH</th>
                                    <th style="width: 4%;color: white;">OTLH</th>
                                    <th style="width: 4%;color: white;">OTSHDO</th>
                                    <th style="width: 4%;color: white;">OTLHDO</th>
                                    <th style="width: 4%;color: white;">NNDO</th>
                                    <th style="width: 4%;color: white;">NDSH</th>
                                    <th style="width: 4%;color: white;">NDLH</th>
                                    <th style="width: 4%;color: white;">NDSHDO</th>
                                    <th style="width: 4%;color: white;">NDLHDO</th>
                                    <th style="width: 4%;color: white;">NPDO</th>
                                    <th style="width: 4%;color: white;">NPSH</th>
                                    <th style="width: 4%;color: white;">NPLH</th>
                                    <th style="width: 4%;color: white;">NPSHDO</th>
                                    <th style="width: 4%;color: white;">NPLHDO</th>

                                    <th style="width: 4%;color: white;">OT HRS. 23</th>
                                    <th style="width: 4%;color: white;">OT HRS. 24</th>
                                    <th style="width: 4%;color: white;">OT HRS. 25</th>
                                                                                                                                                                                                                                                                                                                                                                                                                    
                                    <th style="color: white;">STAUS</th>
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
                        * Records highlighted in green are duplicate employee dtr entries in the Excel file.
                    </p>
                    <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 16px;">
                      * Records highlighted in red are missing in employee references based on the employee code in the Excel file.
                    </p>
                    <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 16px;">
                        * Records highlighted in red can be also missing in payroll period reference based on the payroll period code in the Excel file.
                    </p>
                    <p style="color:red;font-size: 12px;margin-bottom: 0px;line-height: 15px;">
                        * Records highlighted in red can be also missing inrate set-up reference based on the employee code in the Excel file.
                    </p>
                    </div>

                    <div style="float:right;width: 30%;text-align: right;">

                    <button id="btnUploadFinalRecord" type="button" class="btn btn-primary ml-1" >
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block" onclick="SaveFinalRecord()">Save Final Record </span>
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
    <!-- END MODAL -->
</section>


<script type="text/javascript">

    var IsAdmin="{{Session::get('IS_SUPER_ADMIN')}}";

    var IsAllowView="{{$Allow_View_Print_Export}}";
    var IsAllowPrint="{{$Allow_View_Print_Export}}";

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

            'responsive': true,
            'autoWidth': false,
            'paging': false,
            'lengthChange': false,
            'searching'   : false,
            'ordering'    : true,
            'info'        : false,
            'autoWidth'   : false,
            "order": [[57, "desc" ]]
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
                    UploadErrorMsg=aData[58];
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
            "order": [[58, "desc" ]]
        });

        //Load Initial Data
        $("#tblList").DataTable().clear().draw();
        getRecordList(1);

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
            url: "{{ route('get-employee-dtr-list') }}",
            dataType: "json",
            success: function(data){
               total_rec=data.TotalRecord;
                LoadRecordList(data.EmployeeDTRList);
                 if(total_rec>0){
                     CreateEmployeeDTRPaging(total_rec,vLimit);  
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

    function CreateEmployeeDTRPaging(vTotalRecord,vLimit){

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

                            "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",1,true)' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "    Edit Employee DTR" +
                            "</a>";

                       }
                        
                      if( vData.Status=='Pending' && (IsAdmin==1 || IsAllowApprove==1)){

                        tdAction = tdAction + 

                             "<a class='dropdown-item' href='javascript:void(0);' onclick='ApproveRecord(" + vData.ID + ")' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-check-circle mr-1'></i> " +
                                "    Approve Employee DTR" +
                            "</a>";

                      }  

                      if( vData.Status=='Pending' && (IsAdmin==1 || IsAllowCancel==1)){

                        tdAction = tdAction +  

                             "<a class='dropdown-item' href='javascript:void(0);' onclick='CancelRecord(" + vData.ID + ")' style='border-bottom: 1px solid lightgray;color: black;'>"+
                                "<i class='bx bx-x-circle mr-1'></i> " +
                                "    Cancel Employee DTR" +
                            "</a>";
                          }
                        
                     
                        tdAction = tdAction +  

                        "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",1,false)'  style='color: black;'>"+
                            "<i class='bx bx-search-alt mr-1'></i> " +
                            "    View Employee DTR" +
                        "</a>";
                         
                 
                         tdAction = tdAction +  "</div>"+
                      
                    "</div>";

          }
                    
        tdYear = "<span>" + vData.Year + "</span>";
        tdPayrollPeriodCode = "<span>" + vData.PayrollPeriodCode + "</span>";
        tdEmployeeCode = "<span>" + vData.EmployeeNumber + "</span>";
        tdEmployeeName = "<span>" + vData.FullName + "</span>";

        tdEmployeeRate = "<span>" + FormatDecimal(vData.EmployeeRate,2) + "</span>";
        tdRegularHours = "<span>" + FormatDecimal(vData.RegularHours,2) + "</span>";
        tdLateHours = "<span>" + FormatDecimal(vData.LateHours,2) + "</span>";
        tdUndertimeHours = "<span>" + FormatDecimal(vData.UndertimeHours,2) + "</span>";
        tdNDHours = "<span>" + FormatDecimal(vData.NDHours,2) + "</span>";
        tdAbsent = "<span>" + FormatDecimal(vData.Absent,2) + "</span>";

        tdLeave01 = "<span>" + FormatDecimal(vData.Leave01,2) + "</span>";
        tdLeave02 = "<span>" + FormatDecimal(vData.Leave02,2) + "</span>";
        tdLeave03 = "<span>" + FormatDecimal(vData.Leave03,2) + "</span>";
        tdLeave04 = "<span>" + FormatDecimal(vData.Leave04,2) + "</span>";
        tdLeave05 = "<span>" + FormatDecimal(vData.Leave05,2) + "</span>";
        tdLeave06 = "<span>" + FormatDecimal(vData.Leave06,2) + "</span>"; 
        tdLeave07 = "<span>" + FormatDecimal(vData.Leave07,2) + "</span>";
        tdLeave08 = "<span>" + FormatDecimal(vData.Leave08,2) + "</span>";
        tdLeave09 = "<span>" + FormatDecimal(vData.Leave09,2) + "</span>";
        tdLeave10 = "<span>" + FormatDecimal(vData.Leave10,2) + "</span>";
        tdLeave11 = "<span>" + FormatDecimal(vData.Leave11,2) + "</span>";
        tdLeave12 = "<span>" + FormatDecimal(vData.Leave12,2) + "</span>"; 
        tdLeave13 = "<span>" + FormatDecimal(vData.Leave13,2) + "</span>";
        tdLeave14 = "<span>" + FormatDecimal(vData.Leave14,2) + "</span>";
        tdLeave15 = "<span>" + FormatDecimal(vData.Leave15,2) + "</span>";
        tdLeave16 = "<span>" + FormatDecimal(vData.Leave16,2) + "</span>";
        tdLeave17 = "<span>" + FormatDecimal(vData.Leave17,2) + "</span>";
        tdLeave18 = "<span>" + FormatDecimal(vData.Leave18,2) + "</span>"; 
        tdLeave19 = "<span>" + FormatDecimal(vData.Leave19,2) + "</span>";
        tdLeave20 = "<span>" + FormatDecimal(vData.Leave20,2) + "</span>";

        tdOTHours01 = "<span>" + FormatDecimal(vData.OTHours01,2) + "</span>";
        tdOTHours02 = "<span>" + FormatDecimal(vData.OTHours02,2) + "</span>";
        tdOTHours03 = "<span>" + FormatDecimal(vData.OTHours03,2) + "</span>";
        tdOTHours04 = "<span>" + FormatDecimal(vData.OTHours04,2) + "</span>"; 
        tdOTHours05 = "<span>" + FormatDecimal(vData.OTHours05,2) + "</span>"; 
        tdOTHours06 = "<span>" + FormatDecimal(vData.OTHours06,2) + "</span>";
        tdOTHours07 = "<span>" + FormatDecimal(vData.OTHours07,2) + "</span>";
        tdOTHours08 = "<span>" + FormatDecimal(vData.OTHours08,2) + "</span>";
        tdOTHours09 = "<span>" + FormatDecimal(vData.OTHours09,2) + "</span>"; 
        tdOTHours10 = "<span>" + FormatDecimal(vData.OTHours10,2) + "</span>"; 
        tdOTHours11 = "<span>" + FormatDecimal(vData.OTHours11,2) + "</span>";
        tdOTHours12= "<span>" + FormatDecimal(vData.OTHours12,2) + "</span>";
        tdOTHours13 = "<span>" + FormatDecimal(vData.OTHours13,2) + "</span>";
        tdOTHours14 = "<span>" + FormatDecimal(vData.OTHours14,2) + "</span>"; 
        tdOTHours15 = "<span>" + FormatDecimal(vData.OTHours15,2) + "</span>"; 
        tdOTHours16 = "<span>" + FormatDecimal(vData.OTHours16,2) + "</span>";
        tdOTHours17 = "<span>" + FormatDecimal(vData.OTHours17,2) + "</span>";
        tdOTHours18 = "<span>" + FormatDecimal(vData.OTHours18,2) + "</span>";
        tdOTHours19 = "<span>" + FormatDecimal(vData.OTHours19,2) + "</span>"; 
        tdOTHours20 = "<span>" + FormatDecimal(vData.OTHours20,2) + "</span>"; 
        tdOTHours21 = "<span>" + FormatDecimal(vData.OTHours21,2) + "</span>";
        tdOTHours22 = "<span>" + FormatDecimal(vData.OTHours22,2) + "</span>";
        tdOTHours23 = "<span>" + FormatDecimal(vData.OTHours23,2) + "</span>";
        tdOTHours24 = "<span>" + FormatDecimal(vData.OTHours24,2) + "</span>"; 
        tdOTHours25 = "<span>" + FormatDecimal(vData.OTHours25,2) + "</span>"; 
        
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
                 curData[2] = tdEmployeeCode;
                curData[3] = tdEmployeeName;        
                curData[4] = tdPayrollPeriodCode;
                curData[5] = tdYear;
                curData[6] = tdEmployeeRate;
                curData[7] = tdRegularHours;
                curData[8] = tdLateHours;
                curData[9] = tdUndertimeHours;
                curData[10] = tdNDHours;
                curData[11] = tdAbsent;
                curData[12] = tdLeave01;
                curData[13] = tdLeave02;
                curData[14] = tdLeave03;
                curData[15] = tdLeave04;
                curData[16] = tdLeave05;
                curData[17] = tdLeave06;
                curData[18] = tdLeave07;
                curData[19] = tdLeave08;
                curData[20] = tdLeave09;
                curData[21] = tdLeave10;
                curData[22] = tdLeave11;
                curData[23] = tdLeave12;
                curData[24] = tdLeave13;
                curData[25] = tdLeave14;
                curData[26] = tdLeave15;
                curData[27] = tdLeave16;
                curData[28] = tdLeave17;
                curData[29] = tdLeave18;
                curData[30] = tdLeave19;
                curData[31] = tdLeave20;
                curData[32] = tdOTHours01;
                curData[33] = tdOTHours02;
                curData[34] = tdOTHours03;
                curData[35] = tdOTHours04;
                curData[36] = tdOTHours05;
                curData[37] = tdOTHours06;
                curData[38] = tdOTHours07;
                curData[39] = tdOTHours08;
                curData[40] = tdOTHours09;
                curData[41] = tdOTHours10;
                curData[42] = tdOTHours11;
                curData[43] = tdOTHours12;
                curData[44] = tdOTHours13;
                curData[45] = tdOTHours14;
                curData[46] = tdOTHours15;
                curData[47] = tdOTHours16;
                curData[48] = tdOTHours17;
                curData[49] = tdOTHours18;
                curData[50] = tdOTHours19;
                curData[51] = tdOTHours20;
                curData[52] = tdOTHours21;
                curData[53] = tdOTHours22;
                curData[54] = tdOTHours23;
                curData[55] = tdOTHours24;
                curData[56] = tdOTHours25;
                curData[57] = tdStatus;                
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                tdID,
                tdAction,
                 tdEmployeeCode,
                tdEmployeeName,
                tdPayrollPeriodCode,
                tdYear,
                tdEmployeeRate,
                tdRegularHours,
                tdLateHours,
                tdUndertimeHours,
                tdNDHours,
                tdAbsent,
                tdLeave01,
                tdLeave02,
                 tdLeave03,
                 tdLeave04,
                 tdLeave05,
                 tdLeave06,
                 tdLeave07,
                 tdLeave08,
                 tdLeave09,
                 tdLeave10,
                 tdLeave11,
                 tdLeave12,
                 tdLeave13,
                 tdLeave14,
                 tdLeave15,
                 tdLeave16,
                 tdLeave17,
                 tdLeave18,
                 tdLeave19,
                 tdLeave20,
                 tdOTHours01,
                 tdOTHours02,
                 tdOTHours03,
                 tdOTHours04,
                 tdOTHours05,
                 tdOTHours06,
                 tdOTHours07,
                 tdOTHours08,
                 tdOTHours09,
                 tdOTHours10,
                 tdOTHours11,
                 tdOTHours12,
                 tdOTHours13,
                 tdOTHours14,
                 tdOTHours15,
                 tdOTHours16,
                 tdOTHours17,
                 tdOTHours18,
                 tdOTHours19,
                 tdOTHours20,
                 tdOTHours21,
                 tdOTHours22,
                 tdOTHours23,
                 tdOTHours24,
                 tdOTHours25,
                 tdStatus                 

                ]).draw();          
        }
    }

    function Clearfields(vEnabled){

    EnabledDisbledText(false);

    vCurrentDate=new Date();
    $("#TransDate").val(getFormattedDate(vCurrentDate));

    $("#EmployeeDTRID").val(0);
    $("#EmployeeID").val(0);
    $("#EmployeeNo").val(''); 
    $("#EmployeeName").val('');

    $("#PayrollPeriodID").val(0);
    $("#PayrollPeriodYear").val('');
    $("#PayrollPeriodCode").val('');
    $("#PayrollPeriodName").val('');        

    //Basic Info 
    $("#EmployeeRateID").val('0');
    $("#EmployeeRate").val('');
    $("#RegularHours").val('');
    $("#UndertimeHours").val('');
    $("#LateHours").val('');
    $("#NDHours").val('');
    $("#Absent").val('');    
    //Leaves Info 
    $("#Leave01").val('');
    $("#Leave02").val('');
    $("#Leave03").val('');
    $("#Leave04").val('');
    $("#Leave05").val('');
    $("#Leave06").val('');
    $("#Leave07").val('');
    $("#Leave08").val('');
    $("#Leave09").val('');
    $("#Leave10").val('');
    $("#Leave11").val('');
    $("#Leave12").val('');
    $("#Leave13").val('');
    $("#Leave14").val('');
    $("#Leave15").val('');  
    $("#Leave16").val('');
    $("#Leave17").val('');
    $("#Leave18").val('');
    $("#Leave19").val('');
    $("#Leave20").val('');  
    //OT Hours Info 
    $("#OTHours01").val('');
    $("#OTHours02").val('');
    $("#OTHours03").val('');
    $("#OTHours04").val('');
    $("#OTHours05").val('');
    $("#OTHours06").val('');
    $("#OTHours07").val('');
    $("#OTHours08").val('');
    $("#OTHours09").val('');
    $("#OTHours10").val('');
    $("#OTHours11").val('');
    $("#OTHours12").val('');
    $("#OTHours13").val('');
    $("#OTHours14").val('');
    $("#OTHours15").val('');  
    $("#OTHours16").val('');
    $("#OTHours17").val('');
    $("#OTHours18").val('');
    $("#OTHours19").val('');
    $("#OTHours20").val(''); 
    $("#OTHours21").val(''); 
    $("#OTHours22").val(''); 
    $("#OTHours23").val(''); 
    $("#OTHours24").val(''); 
    $("#OTHours25").val(''); 

    $("#Remarks").val('');
    $(".remaining_chars").text('250');

    resetTextBorderToNormal();

    }

  function EnabledDisbledText(vEnabled){
    
    $("#EmployeeName").attr('disabled', vEnabled);
    
    $("#PayrollPeriodYear").attr('disabled', vEnabled);
    $("#PayrollPeriodCode").attr('disabled', vEnabled);
    $("#PayrollPeriodName").attr('disabled', vEnabled);        

    //Basic Info     
    $("#RegularHours").attr('disabled', vEnabled);
    $("#UndertimeHours").attr('disabled', vEnabled);
    $("#LateHours").attr('disabled', vEnabled);
    $("#NDHours").attr('disabled', vEnabled);
    $("#Absent").attr('disabled', vEnabled);    
    //Leaves Info 
    $("#Leave01").attr('disabled', vEnabled);
    $("#Leave02").attr('disabled', vEnabled);
    $("#Leave03").attr('disabled', vEnabled);
    $("#Leave04").attr('disabled', vEnabled);
    $("#Leave05").attr('disabled', vEnabled);
    $("#Leave06").attr('disabled', vEnabled);
    $("#Leave07").attr('disabled', vEnabled);
    $("#Leave08").attr('disabled', vEnabled);
    $("#Leave09").attr('disabled', vEnabled);
    $("#Leave10").attr('disabled', vEnabled);
    $("#Leave11").attr('disabled', vEnabled);
    $("#Leave12").attr('disabled', vEnabled);
    $("#Leave13").attr('disabled', vEnabled);
    $("#Leave14").attr('disabled', vEnabled);
    $("#Leave15").attr('disabled', vEnabled);  
    $("#Leave16").attr('disabled', vEnabled);
    $("#Leave17").attr('disabled', vEnabled);
    $("#Leave18").attr('disabled', vEnabled);
    $("#Leave19").attr('disabled', vEnabled);
    $("#Leave20").attr('disabled', vEnabled);  
    //OT Hours Info 
    $("#OTHours01").attr('disabled', vEnabled);
    $("#OTHours02").attr('disabled', vEnabled);
    $("#OTHours03").attr('disabled', vEnabled);
    $("#OTHours04").attr('disabled', vEnabled);
    $("#OTHours05").attr('disabled', vEnabled);
    $("#OTHours06").attr('disabled', vEnabled);
    $("#OTHours07").attr('disabled', vEnabled);
    $("#OTHours08").attr('disabled', vEnabled);
    $("#OTHours09").attr('disabled', vEnabled);
    $("#OTHours10").attr('disabled', vEnabled);
    $("#OTHours11").attr('disabled', vEnabled);
    $("#OTHours12").attr('disabled', vEnabled);
    $("#OTHours13").attr('disabled', vEnabled);
    $("#OTHours14").attr('disabled', vEnabled);
    $("#OTHours15").attr('disabled', vEnabled);  
    $("#OTHours16").attr('disabled', vEnabled);
    $("#OTHours17").attr('disabled', vEnabled);
    $("#OTHours18").attr('disabled', vEnabled);
    $("#OTHours19").attr('disabled', vEnabled);
    $("#OTHours20").attr('disabled', vEnabled); 
    $("#OTHours21").attr('disabled', vEnabled); 
    $("#OTHours22").attr('disabled', vEnabled); 
    $("#OTHours23").attr('disabled', vEnabled); 
    $("#OTHours24").attr('disabled', vEnabled); 
    $("#OTHours25").attr('disabled', vEnabled); 
    $("#Remarks").attr('disabled', vEnabled); 

    }

    function resetTextBorderToNormal(){

    $("#EmployeeName").css({"border":"#ccc 1px solid"});
    $("#PayrollPeriod").css({"border":"#ccc 1px solid"});
    $("#PayrollPeriodName").css({"border":"#ccc 1px solid"});
    
    $("#EmployeeRate").css({"border":"#ccc 1px solid"}); 
    $("#RegularHours").css({"border":"#ccc 1px solid"});
    $("#LateHours").css({"border":"#ccc 1px solid"});
    $("#UndertimeHours").css({"border":"#ccc 1px solid"});
    $("#NDHours").css({"border":"#ccc 1px solid"});
    $("#Absent").css({"border":"#ccc 1px solid"});

    $("#Leave01").css({"border":"#ccc 1px solid"}); 
    $("#Leave02").css({"border":"#ccc 1px solid"});
    $("#Leave03").css({"border":"#ccc 1px solid"});
    $("#Leave04").css({"border":"#ccc 1px solid"});
    $("#Leave05").css({"border":"#ccc 1px solid"});
    $("#Leave06").css({"border":"#ccc 1px solid"});
    $("#Leave07").css({"border":"#ccc 1px solid"}); 
    $("#Leave08").css({"border":"#ccc 1px solid"});
    $("#Leave09").css({"border":"#ccc 1px solid"});
    $("#Leave10").css({"border":"#ccc 1px solid"});
    $("#Leave11").css({"border":"#ccc 1px solid"});
    $("#Leave12").css({"border":"#ccc 1px solid"});
    $("#Leave13").css({"border":"#ccc 1px solid"}); 
    $("#Leave14").css({"border":"#ccc 1px solid"});
    $("#Leave15").css({"border":"#ccc 1px solid"});
    $("#Leave16").css({"border":"#ccc 1px solid"});
    $("#Leave17").css({"border":"#ccc 1px solid"});
    $("#Leave18").css({"border":"#ccc 1px solid"});
    $("#Leave19").css({"border":"#ccc 1px solid"});
    $("#Leave20").css({"border":"#ccc 1px solid"});

    $("#OTHours01").css({"border":"#ccc 1px solid"}); 
    $("#OTHours02").css({"border":"#ccc 1px solid"});
    $("#OTHours03").css({"border":"#ccc 1px solid"});
    $("#OTHours04").css({"border":"#ccc 1px solid"});
    $("#OTHours05").css({"border":"#ccc 1px solid"});
    $("#OTHours06").css({"border":"#ccc 1px solid"});
    $("#OTHours07").css({"border":"#ccc 1px solid"}); 
    $("#OTHours08").css({"border":"#ccc 1px solid"});
    $("#OTHours09").css({"border":"#ccc 1px solid"});
    $("#OTHours10").css({"border":"#ccc 1px solid"});
    $("#OTHours11").css({"border":"#ccc 1px solid"});
    $("#OTHours12").css({"border":"#ccc 1px solid"});
    $("#OTHours13").css({"border":"#ccc 1px solid"}); 
    $("#OTHours14").css({"border":"#ccc 1px solid"});
    $("#OTHours15").css({"border":"#ccc 1px solid"});
    $("#OTHours16").css({"border":"#ccc 1px solid"});
    $("#OTHours17").css({"border":"#ccc 1px solid"});
    $("#OTHours18").css({"border":"#ccc 1px solid"});
    $("#OTHours19").css({"border":"#ccc 1px solid"});
    $("#OTHours20").css({"border":"#ccc 1px solid"});
    $("#OTHours21").css({"border":"#ccc 1px solid"});
    $("#OTHours22").css({"border":"#ccc 1px solid"});
    $("#OTHours23").css({"border":"#ccc 1px solid"});
    $("#OTHours24").css({"border":"#ccc 1px solid"});
    $("#OTHours25").css({"border":"#ccc 1px solid"});

    $("#Status").css({"border":"#ccc 1px solid"}); 
    }

    function NewRecord(){

        Clearfields();
        $("#DTRTable").val(1);

        $("#PayrollPeriodID").val('{{Session::get('ADMIN_PAYROLL_PERIOD_SCHED_ID')}}');
        $("#PayrollPeriodYear").val('{{Session::get('ADMIN_PAYROLL_PERIOD_SCHED_YEAR')}}');
        $("#PayrollPeriodCode").val('{{Session::get('ADMIN_PAYROLL_PERIOD_SCHED_CODE')}}');
        $("#PayrollPeriodName").val('{{Session::get('ADMIN_PAYROLL_PERIOD_SCHED_CODE').': '.Session::get('ADMIN_PAYROLL_PERIOD_SCHED_START').' - '.Session::get('ADMIN_PAYROLL_PERIOD_SCHED_END')}}');

        $("#Status").val('Pending');        
        $("#Status").attr("style", "color: red !important; font-weight: bold; border: 1px solid rgb(204, 204, 204)");
        $("#EmployeeNo").attr("style", "border: 1px solid rgb(204, 204, 204)");

        $("#btnSaveRecord").show();
        $("#btnCancelRecord").text('Cancel');

        $("#nav-basic-tab").click();
        $("#record-modal").modal();
    }

    function CancelRecord(vRecordID){
        $(".DTRIDStatus").val(vRecordID);
        $("#cancel-modal").modal();
    }

    function ApproveRecord(vRecordID){
        $(".DTRIDStatus").val(vRecordID);
        $("#approve-modal").modal();
    }

    function UploadExcelRecord(){

        Clearfields();
        $("#DTRTable").val(0);
        $("#spnExcelRecord").val(0);
        $("#ExcelFile").val('');
        $("#upload-modal").modal();
       
    }

  function SetDTRStatus(vStatus){

    vRecordID= $(".DTRIDStatus").val();
    
      if(vRecordID>0){
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                DTR_ID: vRecordID,
                NewStatus: vStatus
            },
            url: "{{ route('do-set-dtr-transaction-status') }}",
            dataType: "json",
            success: function(data){
              if(data.Response =='Success'){
                 showHasSuccessMessage(data.ResponseMessage);
                 LoadRecordRow(data.EmployeeDTRInfo);
                  $("#approve-modal").modal('hide');
                  $("#cancel-modal").modal('hide');
                }else{
                  showHasErrorMessage('', data.ResponseMessage);
                  if (data.DuplicateDTR) {
                    $("#approve-modal").modal('hide');
                    $("#over-write-approve-modal").modal();  
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

    function EditRecord(vRecordID,vTable,vAllowEdit){

        if(vTable==0){
           var postURL="{{ URL::route('get-dtr-temp-info')}}";
           $("#DTRTable").val(0); // Temp Table
           
        }else{
          var postURL="{{ URL::route('get-employee-dtr-info')}}";
          $("#DTRTable").val(1); // Final Table
        }

        if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    DTR_ID: vRecordID
                },
                url: postURL,
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.EmployeeDTRInfo != undefined){

                        Clearfields();

                        $("#EmployeeDTRID").val(data.EmployeeDTRInfo.ID);
                        $("#EmployeeID").val(data.EmployeeDTRInfo.EmployeeID);
                        $("#EmployeeNo").val(data.EmployeeDTRInfo.EmployeeNumber);
                        $("#EmployeeNo").attr("style", "border: 1px solid rgb(204, 204, 204) !important");

                        if(data.EmployeeDTRInfo.EmployeeID<=0){
                             $("#EmployeeName").val('');
                        }else{
                            $("#EmployeeName").val(data.EmployeeDTRInfo.FullName); 
                        }
                                               
                        $("#TransDate").val(data.EmployeeDTRInfo.TransactionDateFormat);

                        if(data.EmployeeDTRInfo.PayrollPeriodID<=0){
                            $("#PayrollPeriodID").val(0);
                            $("#PayrollPeriodYear").val('');
                            $("#PayrollPeriodCode").val('');
                            $("#PayrollPeriodName").val('');
                        }else{
                            $("#PayrollPeriodID").val(data.EmployeeDTRInfo.PayrollPeriodID);
                            $("#PayrollPeriodYear").val(data.EmployeeDTRInfo.Year);
                            $("#PayrollPeriodCode").val(data.EmployeeDTRInfo.PayrollPeriodCode);
                            $("#PayrollPeriodName").val(data.EmployeeDTRInfo.PayrollPeriodCode+': '+ data.EmployeeDTRInfo.StartDateFormat + ' - '  + data.EmployeeDTRInfo.EndDateFormat);
                        }
                       
                        //Basic Info 
                        $("#EmployeeRateID").val(FormatDecimal(data.EmployeeDTRInfo.EmployeeRate,2));
                        $("#EmployeeRate").val(FormatDecimal(data.EmployeeDTRInfo.EmployeeRate,2));
                        $("#RegularHours").val(FormatDecimal(data.EmployeeDTRInfo.RegularHours,2));
                        $("#LateHours").val(FormatDecimal(data.EmployeeDTRInfo.LateHours,2));
                        $("#UndertimeHours").val(FormatDecimal(data.EmployeeDTRInfo.UndertimeHours,2));
                        $("#NDHours").val(FormatDecimal(data.EmployeeDTRInfo.NDHours,2));
                        $("#Absent").val(FormatDecimal(data.EmployeeDTRInfo.Absent,2));
                         //Leaves Info 
                        $("#Leave01").val(FormatDecimal(data.EmployeeDTRInfo.Leave01,2));
                        $("#Leave02").val(FormatDecimal(data.EmployeeDTRInfo.Leave02,2));
                        $("#Leave03").val(FormatDecimal(data.EmployeeDTRInfo.Leave03,2));
                        $("#Leave04").val(FormatDecimal(data.EmployeeDTRInfo.Leave04,2));
                        $("#Leave05").val(FormatDecimal(data.EmployeeDTRInfo.Leave05,2));
                        $("#Leave06").val(FormatDecimal(data.EmployeeDTRInfo.Leave06,2));
                        $("#Leave07").val(FormatDecimal(data.EmployeeDTRInfo.Leave07,2));
                        $("#Leave08").val(FormatDecimal(data.EmployeeDTRInfo.Leave08,2));
                        $("#Leave09").val(FormatDecimal(data.EmployeeDTRInfo.Leave09,2));
                        $("#Leave10").val(FormatDecimal(data.EmployeeDTRInfo.Leave10,2));
                        $("#Leave11").val(FormatDecimal(data.EmployeeDTRInfo.Leave11,2));
                        $("#Leave12").val(FormatDecimal(data.EmployeeDTRInfo.Leave12,2));
                        $("#Leave13").val(FormatDecimal(data.EmployeeDTRInfo.Leave13,2));
                        $("#Leave14").val(FormatDecimal(data.EmployeeDTRInfo.Leave14,2));
                        $("#Leave15").val(FormatDecimal(data.EmployeeDTRInfo.Leave15,2));  
                        $("#Leave16").val(FormatDecimal(data.EmployeeDTRInfo.Leave16,2));
                        $("#Leave17").val(FormatDecimal(data.EmployeeDTRInfo.Leave17,2));
                        $("#Leave18").val(FormatDecimal(data.EmployeeDTRInfo.Leave18,2));
                        $("#Leave19").val(FormatDecimal(data.EmployeeDTRInfo.Leave19,2));
                        $("#Leave20").val(FormatDecimal(data.EmployeeDTRInfo.Leave20,2));  
                        //OT Hours Info 
                        $("#OTHours01").val(FormatDecimal(data.EmployeeDTRInfo.OTHours01,2));
                        $("#OTHours02").val(FormatDecimal(data.EmployeeDTRInfo.OTHours02,2));
                        $("#OTHours03").val(FormatDecimal(data.EmployeeDTRInfo.OTHours03,2));
                        $("#OTHours04").val(FormatDecimal(data.EmployeeDTRInfo.OTHours04,2));
                        $("#OTHours05").val(FormatDecimal(data.EmployeeDTRInfo.OTHours05,2));
                        $("#OTHours06").val(FormatDecimal(data.EmployeeDTRInfo.OTHours06,2));
                        $("#OTHours07").val(FormatDecimal(data.EmployeeDTRInfo.OTHours07,2));
                        $("#OTHours08").val(FormatDecimal(data.EmployeeDTRInfo.OTHours08,2));
                        $("#OTHours09").val(FormatDecimal(data.EmployeeDTRInfo.OTHours09,2));
                        $("#OTHours10").val(FormatDecimal(data.EmployeeDTRInfo.OTHours10,2));
                        $("#OTHours11").val(FormatDecimal(data.EmployeeDTRInfo.OTHours11,2));
                        $("#OTHours12").val(FormatDecimal(data.EmployeeDTRInfo.OTHours12,2));
                        $("#OTHours13").val(FormatDecimal(data.EmployeeDTRInfo.OTHours13,2));
                        $("#OTHours14").val(FormatDecimal(data.EmployeeDTRInfo.OTHours14,2));
                        $("#OTHours15").val(FormatDecimal(data.EmployeeDTRInfo.OTHours15,2));  
                        $("#OTHours16").val(FormatDecimal(data.EmployeeDTRInfo.OTHours16,2));
                        $("#OTHours17").val(FormatDecimal(data.EmployeeDTRInfo.OTHours17,2));
                        $("#OTHours18").val(FormatDecimal(data.EmployeeDTRInfo.OTHours18,2));
                        $("#OTHours19").val(FormatDecimal(data.EmployeeDTRInfo.OTHours19,2));
                        $("#OTHours20").val(FormatDecimal(data.EmployeeDTRInfo.OTHours20,2)); 
                        $("#OTHours21").val(FormatDecimal(data.EmployeeDTRInfo.OTHours21,2)); 
                        $("#OTHours22").val(FormatDecimal(data.EmployeeDTRInfo.OTHours22,2)); 
                        $("#OTHours23").val(FormatDecimal(data.EmployeeDTRInfo.OTHours23,2)); 
                        $("#OTHours24").val(FormatDecimal(data.EmployeeDTRInfo.OTHours24,2)); 
                        $("#OTHours25").val(FormatDecimal(data.EmployeeDTRInfo.OTHours25,2)); 

                         $("#Remarks").val(data.EmployeeDTRInfo.Remarks);
                         $(".remaining_chars").text(250-data.EmployeeDTRInfo.Remarks.length);                        

                        if(data.EmployeeDTRInfo.Status=='Pending'){
                            $("#Status").val('Pending');                            
                            $("#Status").attr("style", "color: red !important; font-weight: bold;border: 1px solid rgb(204, 204, 204) !important");
                        }

                        if(data.EmployeeDTRInfo.Status=='Approved'){
                             $("#Status").val('Approved');                             
                             $("#Status").attr("style", "color: green !important; font-weight: bold;border: 1px solid rgb(204, 204, 204) !important");
                        }

                        if(data.EmployeeDTRInfo.Status=='Cancelled'){
                             $("#Status").val('Cancelled');
                             $("#Status").css("color", "#f68c1f");
                        }

                        $("#nav-basic-tab").click();

                        buttonOneClick("btnSaveRecord", "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);   

                        if(vAllowEdit){
                           $("#btnSaveRecord").show();
                           $("#btnCancelRecord").text('Cancel');
                        }else{
                          $("#btnSaveRecord").hide();
                          $("#btnCancelRecord").text('Close');
                        }

                        EnabledDisbledText(true);

                        $("#divLoader").hide();
                        $("#record-modal").modal();

                    }else{
                        $("#divLoader").hide();
                        showHasErrorMessage('', data.ResponseMessage);
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

        vDTRTable=$("#DTRTable").val();

        if(vDTRTable==0){ //Temp Table
           var postURL="{{ URL::route('do-save-dtr-temp-transaction')}}"; 
            vIsUploaded=1;          
        }else{ //Final Table
          var postURL="{{ URL::route('do-save-employee-dtr-transaction')}}";
           vIsUploaded=0;
        }

        vDTR_ID=$("#EmployeeDTRID").val();
        vEmployeeID=$("#EmployeeID").val();
        vEmployeeNo=$("#EmployeeNo").val();
        vEmployeeName=$("#EmployeeName").val();        
        vTransDate=$("#TransDate").val();

        vYear=$("#PayrollPeriodYear").val();
        vPayrollPeriodCode=$("#PayrollPeriodCode").val();
        //Basic Info 
        vEmployeeRate=$("#EmployeeRate").val();
        vRegularHours=$("#RegularHours").val();
        vLateHours=$("#LateHours").val();
        vUnderTimeHours=$("#UndertimeHours").val();
        vNDHours=$("#NDHours").val();
        vAbsent=$("#Absent").val();       
         //Leaves Info 
        vLeave01=$("#Leave01").val();
        vLeave02=$("#Leave02").val();
        vLeave03=$("#Leave03").val();
        vLeave04=$("#Leave04").val();
        vLeave05=$("#Leave05").val();
        vLeave06=$("#Leave06").val();
        vLeave07=$("#Leave07").val();
        vLeave08=$("#Leave08").val();
        vLeave09=$("#Leave09").val();
        vLeave10=$("#Leave10").val();
        vLeave11=$("#Leave11").val();
        vLeave12=$("#Leave12").val();
        vLeave13=$("#Leave13").val();
        vLeave14=$("#Leave14").val();
        vLeave15=$("#Leave15").val();  
        vLeave16=$("#Leave16").val();
        vLeave17=$("#Leave17").val();
        vLeave18=$("#Leave18").val();
        vLeave19=$("#Leave19").val();
        vLeave20=$("#Leave20").val();  
        //OT Hours Info 
        vOTHours01=$("#OTHours01").val();
        vOTHours02=$("#OTHours02").val();
        vOTHours03=$("#OTHours03").val();
        vOTHours04=$("#OTHours04").val();
        vOTHours05=$("#OTHours05").val();
        vOTHours06=$("#OTHours06").val();
        vOTHours07=$("#OTHours07").val();
        vOTHours08=$("#OTHours08").val();
        vOTHours09=$("#OTHours09").val();
        vOTHours10=$("#OTHours10").val();
        vOTHours11=$("#OTHours11").val();
        vOTHours12=$("#OTHours12").val();
        vOTHours13=$("#OTHours13").val();
        vOTHours14=$("#OTHours14").val();
        vOTHours15=$("#OTHours15").val();  
        vOTHours16=$("#OTHours16").val();
        vOTHours17=$("#OTHours17").val();
        vOTHours18=$("#OTHours18").val();
        vOTHours19=$("#OTHours19").val();
        vOTHours20=$("#OTHours20").val(); 
        vOTHours21=$("#OTHours21").val(); 
        vOTHours22=$("#OTHours22").val(); 
        vOTHours23=$("#OTHours23").val(); 
        vOTHours24=$("#OTHours24").val(); 
        vOTHours25=$("#OTHours25").val();

        vStatus= $("#Status").val();
       
        var checkInput =true;
        checkInput=doCheckDTRInput();

        if(checkInput==false){
            return;
        }

       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                DTR_ID: vDTR_ID,
                Year: vYear,
                PayrollCode: vPayrollPeriodCode,
                TransDate: vTransDate,
                EmpNo: vEmployeeNo,
                EmpRate: vEmployeeRate,
                RegHours: vRegularHours,
                LateHours: vLateHours,
                UnderTimeHours: vUnderTimeHours,
                NDHours: vNDHours,
                Absent: vAbsent,
                Leave01: vLeave01,
                Leave02: vLeave02,
                Leave03: vLeave03,
                Leave04: vLeave04,
                Leave05: vLeave05,
                Leave06: vLeave06,
                Leave07: vLeave07,
                Leave08: vLeave08,
                Leave09: vLeave09,
                Leave10: vLeave10,
                Leave11: vLeave11,
                Leave12: vLeave12,
                Leave13: vLeave13,
                Leave14: vLeave14,
                Leave15: vLeave15,
                Leave16: vLeave16,
                Leave17: vLeave17,
                Leave18: vLeave18,
                Leave19: vLeave19,
                Leave20: vLeave20,
                OTHours01: vOTHours01,
                OTHours02: vOTHours02,
                OTHours03: vOTHours03,
                OTHours04: vOTHours04,
                OTHours05: vOTHours05,
                OTHours06: vOTHours06,
                OTHours07: vOTHours07,
                OTHours08: vOTHours08,
                OTHours09: vOTHours09,
                OTHours10: vOTHours10,
                OTHours11: vOTHours11,
                OTHours12: vOTHours12,
                OTHours13: vOTHours13,
                OTHours14: vOTHours14,
                OTHours15: vOTHours15,
                OTHours16: vOTHours16,
                OTHours17: vOTHours17,
                OTHours18: vOTHours18,
                OTHours19: vOTHours19,
                OTHours20: vOTHours20,
                OTHours21: vOTHours21,
                OTHours22: vOTHours22,
                OTHours23: vOTHours23,
                OTHours24: vOTHours24,
                OTHours25: vOTHours25,
                IsUploaded: vIsUploaded,
                Status: vStatus
            },
            url: postURL,
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

                if(data.Response =='Success'){
                     showHasSuccessMessage(data.ResponseMessage);
                    if(vDTRTable==0){ // Temp DTR Table          
                      getDTRTempRecordList(1);
                    }else{ // Final DTR Table
                       LoadRecordRow(data.EmployeeDTRInfo);
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
     
    function doCheckDTRInput(){

        var IsNotComplete=true;

        var vEmployeeDTRID = $("#EmployeeDTRID").val();
        var vTransDate = $("#TransDate").val();

        var vEmployeeRateID = $("#EmployeeRateID").val();
        var vEmployeeID = $("#EmployeeID").val();
        var vEmployeeNo = $("#EmployeeNo").val();
        var vEmployeeName = $("#EmployeeName").val();
        
        var vPayrollPeriodID = $("#PayrollPeriodID").val();
        var vPayrollPeriod = $("#PayrollPeriod").val();

        var vEmployeeRate = $("#EmployeeRate").val();
        var vRegularHours = $("#RegularHours").val();
        var vLateHours = $("#LateHours").val();
        var vUndertimeHours = $("#UndertimeHours").val();
        var vNDHours = $("#NDHours").val();
        var vAbsent = $("#Absent").val();

        var vLeave01 = $("#Leave01").val();
        var vLeave02 = $("#Leave02").val();
        var vLeave03 = $("#Leave03").val();
        var vLeave04 = $("#Leave04").val();
        var vLeave05 = $("#Leave05").val();
        var vLeave06 = $("#Leave06").val();
        var vLeave07 = $("#Leave07").val();
        var vLeave08 = $("#Leave08").val();
        var vLeave09 = $("#Leave09").val();
        var vLeave10 = $("#Leave10").val();
        var vLeave11=  $("#Leave11").val();
        var vLeave12 = $("#Leave12").val();
        var vLeave13 = $("#Leave13").val();
        var vLeave14 = $("#Leave14").val();
        var vLeave15 = $("#Leave15").val();
        var vLeave16 = $("#Leave16").val();
        var vLeave17=  $("#Leave17").val();
        var vLeave18 = $("#Leave18").val();
        var vLeave19 = $("#Leave19").val();
        var vLeave20 = $("#Leave20").val();

        var vOTHours01=$("#OTHours01").val();
        var vOTHours02=$("#OTHours02").val();
        var vOTHours03=$("#OTHours03").val();
        var vOTHours04=$("#OTHours04").val();
        var vOTHours05=$("#OTHours05").val();
        var vOTHours06=$("#OTHours06").val();
        var vOTHours07=$("#OTHours07").val();
        var vOTHours08=$("#OTHours08").val();
        var vOTHours09=$("#OTHours09").val();
        var vOTHours10=$("#OTHours10").val();
        var vOTHours11=$("#OTHours11").val();
        var vOTHours12=$("#OTHours12").val();
        var vOTHours13=$("#OTHours13").val();
        var vOTHours14=$("#OTHours14").val();
        var vOTHours15=$("#OTHours15").val();  
        var vOTHours16=$("#OTHours16").val();
        var vOTHours17=$("#OTHours17").val();
        var vOTHours18=$("#OTHours18").val();
        var vOTHours19=$("#OTHours19").val();
        var vOTHours20=$("#OTHours20").val(); 
        var vOTHours21=$("#OTHours21").val(); 
        var vOTHours22=$("#OTHours22").val(); 
        var vOTHours23=$("#OTHours23").val(); 
        var vOTHours24=$("#OTHours24").val(); 
        var vOTHours25=$("#OTHours25").val();

        var vStatus = $("#Status").val();

        resetTextBorderToNormal();
        
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

      if(vEmployeeRateID=="" || vEmployeeRateID==0 ) {
         showHasErrorMessage('EmployeeName','Selected employee does not have a salary settings yet.');
          IsNotComplete=false;
          return IsNotComplete;
       }

       // BASIC INFO
        if(vEmployeeRate=="" || vEmployeeRate==0) {
         $("#nav-basic-tab").click();
         showHasErrorMessage('EmployeeRate','Selected employee does not have a salary settings yet.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vRegularHours=="") {
         $("#nav-basic-tab").click();
         showHasErrorMessage('RegularHours','Enter employee regular hours.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vLateHours=="") {
         $("#nav-basic-tab").click();
         showHasErrorMessage('LateHours','Enter employee late hours.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vUndertimeHours=="") {
         $("#nav-basic-tab").click();          
         showHasErrorMessage('UndertimeHours','Enter employee undertime hours.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vNDHours=="") {
         $("#nav-basic-tab").click();
         showHasErrorMessage('NDHours','Enter employee ND hours.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vAbsent=="") {
         $("#nav-basic-tab").click();
         showHasErrorMessage('Absent','Enter Absent absent total hours.');
          IsNotComplete=false;
           return IsNotComplete;
       }


       // LEAVES INFO
       if(vLeave01=="") {
        $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave01','Enter leave 01/sick leave.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLeave02=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave02','Enter leave 02/vacation leave.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLeave03=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave03','Enter leave 03/emergency leave.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vLeave04=="") {
          $("#nav-leaves-tab").click();
          showHasErrorMessage('Leave04','Enter leave 04/maternity leave.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vLeave05=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave05','Enter leave 05/paternity leave.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLeave06=="") {
        $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave06','Enter leave 06/service incentive leave.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLeave07=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave07','Enter leave 07/ado leave.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLeave08=="") {
        $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave08','Enter leave 08/spl leave.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vLeave09=="") {
        $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave09','Enter 09 leave');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLeave10=="") {
        $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave10','Enter leave 10');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLeave11=="") {
        $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave11','Enter leave 11');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLeave12=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave12','Enter leave 12');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vLeave13=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave13','Enter leave 13');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLeave14=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave14','Enter leave 14');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vLeave15=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave15','Enter leave 15');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLeave16=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave16','Enter leave 16');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLeave17=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave17','Enter leave 17');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vLeave18=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave18','Enter leave 18');
          IsNotComplete=false;
       }

      if(vLeave19=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave19','Enter leave 19');
          IsNotComplete=false;
           return IsNotComplete;
       }

      if(vLeave20=="") {
         $("#nav-leaves-tab").click();
         showHasErrorMessage('Leave20','Enter leave 20');
          IsNotComplete=false;
           return IsNotComplete;
       }


        // OT HOURS INFO
       if(vOTHours01=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours01','Enter OT Hours 01/ROT.');
          IsNotComplete=false;
           return IsNotComplete;
       }
       if(vOTHours02=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours02','Enter OT Hours 02/NPROT.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours03=="") {
          $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours03','Enter OT Hours 03/DO.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours04=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours04','Enter OT Hours 04/SH.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours05=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours05','Enter OT Hours 05/LH.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours06=="") {
          $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours06','Enter OT Hours 06/SHDO.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vOTHours07=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours07','Enter OT Hours 07/LHDO.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours08=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours08','Enter OT Hours 08/OTDO.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours09=="") {
          $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours09','Enter OT Hours 09/OTSH.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vOTHours10=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours10','Enter OT Hours 10/OTLH.');
          IsNotComplete=false;
           return IsNotComplete;
       }
       if(vOTHours11=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours11','Enter OT Hours 11/OTSHDO.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours12=="") {
          $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours12','Enter OT Hours 12/OTLHDO.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vOTHours13=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours13','Enter OT Hours 13/NNDO.');
          IsNotComplete=false;
           return IsNotComplete;
       }
       if(vOTHours14=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours14','Enter OT Hours 14/NDSH.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours15=="") {
          $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours15','Enter OT Hours 15/NDLH.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vOTHours16=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours16','Enter OT Hours 16/NDLH.');
          IsNotComplete=false;
           return IsNotComplete;
       }
       if(vOTHours17=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours17','Enter OT Hours 17/NDLHDO.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours18=="") {
          $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours18','Enter OT Hours 18/NPDO.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vOTHours19=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours19','Enter OT Hours 19/NPSH.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours20=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours20','Enter OT Hours 20/NPLH.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours21=="") {
          $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours21','Enter OT Hours 21/NPSHDO.');
          IsNotComplete=false;
           return IsNotComplete;
       }

        if(vOTHours22=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours22','Enter OT Hours 22/NPLHDO.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours23=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours23','Enter OT Hours 23.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours24=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours24','Enter OT Hours 24.');
          IsNotComplete=false;
           return IsNotComplete;
       }

       if(vOTHours25=="") {
         $("#nav-othours-tab").click();
         showHasErrorMessage('OTHours24','Enter OT Hours 25.');
          IsNotComplete=false;
           return IsNotComplete;
       }

    }

  $(document).on('focus','.autocomplete_txt',function(){

       isEmployee=false;
       isPayrollPeriod=false;
       var valAttrib  = $(this).attr('data-complete-type');

       if(valAttrib=='payroll-period'){
            isPayrollPeriod=true;
            searchlen=0;
            var postURL="{{ URL::route('get-payroll-period-search-list')}}";
        }

       if(valAttrib=='employee'){
            isEmployee=true;
            searchlen=2;
            var postURL="{{ URL::route('get-employee-search-list')}}";
        }

     $(this).autocomplete({
            source: function( request, response ) {
               if(request.term.length >= searchlen ){
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
                      getEmployeeRateID(seldata[0]);  
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
 
 function getEmployeeRateID(vRecordID){

    if(vRecordID>0){
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                EmployeeID: vRecordID
            },
            url: "{{ route('get-employee-rate-id') }}",
            dataType: "json",
            success: function(data){

              if(data.Response =='Success'){
                 $("#EmployeeRateID").val(data.EmployeeRateID);
                 $("#EmployeeRate").val(FormatDecimal(data.HourlyRate,2));
                }else{
                  $("#EmployeeRateID").val(0);
                  $("#EmployeeRate").val(0);
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

  function getDTRTempRecordList(vPageNo){

      $("#tblList-Excel").DataTable().clear().draw();
      $(".paginate_button").remove(); 

      // $(".ul-paging-dtr >.paginate_button").remove();

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
            url: "{{ route('get-dtr-temp-list') }}",
            dataType: "json",
            success: function(data){

                 total_rec=data.TotalRecord;
                 LoadTempRecordList(data.DTRTempList);
                 if(total_rec>0){
                     CreateEmployeeDTRTempPaging(total_rec,vLimit);  
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

   function CreateEmployeeDTRTempPaging(vTotalRecord,vLimit){

       var i;
       paging_button="";
    
        limit=vLimit; //get limit
        totalcount=vTotalRecord; //get total count
        pages=Math.ceil(totalcount/limit); //get pages
      
       if (pages!=1) {     

          paging_button="<li class='paginate_button page-item previous' id='example2_previous'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getDTRTempRecordList(1)'>First</a></li>"
          $(".ul-paging").append(paging_button);
       }
       
        if (pages>1) {        
          for (i = 1; i <= pages; i++) {            
            paging_button="<li class='paginate_button page-item'><a href='javascript:void(0)' id='paging_button_id"+i+"' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getDTRTempRecordList("+i+")'>"+i+"</a></li>"
             $(".ul-paging").append(paging_button);
          }
        }
          
       if (pages!=1) {            
        paging_button="<li class='paginate_button page-item next' id='example2_next'><a href='javascript:void(0)' aria-controls='example2' data-dt-idx="+i+" tabindex='0' class='page-link' onClick='getDTRTempRecordList("+pages+",)'>Last</a></li>"
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

                           tdAction += "</div>"+
                    "</div>";
        
        tdYear = "<span>" + vData.Year + "</span>";
        tdPayrollPeriodCode = "<span>" + vData.PayrollPeriodCode + "</span>";
        tdEmployeeCode = "<span>" + vData.EmployeeNumber + "</span>";
        tdEmployeeName = "<span>" + vData.FullName + "</span>";

        tdEmployeeRate = "<span>" + FormatDecimal(vData.EmployeeRate,2) + "</span>";
        tdRegularHours = "<span>" + FormatDecimal(vData.RegularHours,2) + "</span>";
        tdLateHours = "<span>" + FormatDecimal(vData.LateHours,2) + "</span>";
        tdUndertimeHours = "<span>" + FormatDecimal(vData.UndertimeHours,2) + "</span>";
        tdNDHours = "<span>" + FormatDecimal(vData.NDHours,4) + "</span>";
        tdAbsent = "<span>" + FormatDecimal(vData.Absent,2) + "</span>";

        tdLeave01 = "<span>" + FormatDecimal(vData.Leave01,2) + "</span>";
        tdLeave02 = "<span>" + FormatDecimal(vData.Leave02,2) + "</span>";
        tdLeave03 = "<span>" + FormatDecimal(vData.Leave03,2) + "</span>";
        tdLeave04 = "<span>" + FormatDecimal(vData.Leave04,2) + "</span>";
        tdLeave05 = "<span>" + FormatDecimal(vData.Leave05,2) + "</span>";
        tdLeave06 = "<span>" + FormatDecimal(vData.Leave06,2) + "</span>"; 
        tdLeave07 = "<span>" + FormatDecimal(vData.Leave07,2) + "</span>";
        tdLeave08 = "<span>" + FormatDecimal(vData.Leave08,2) + "</span>";
        tdLeave09 = "<span>" + FormatDecimal(vData.Leave09,2) + "</span>";
        tdLeave10 = "<span>" + FormatDecimal(vData.Leave10,2) + "</span>";
        tdLeave11 = "<span>" + FormatDecimal(vData.Leave11,2) + "</span>";
        tdLeave12 = "<span>" + FormatDecimal(vData.Leave12,2) + "</span>"; 
        tdLeave13 = "<span>" + FormatDecimal(vData.Leave13,2) + "</span>";
        tdLeave14 = "<span>" + FormatDecimal(vData.Leave14,2) + "</span>";
        tdLeave15 = "<span>" + FormatDecimal(vData.Leave15,2) + "</span>";
        tdLeave16 = "<span>" + FormatDecimal(vData.Leave16,2) + "</span>";
        tdLeave17 = "<span>" + FormatDecimal(vData.Leave17,2) + "</span>";
        tdLeave18 = "<span>" + FormatDecimal(vData.Leave18,2) + "</span>"; 
        tdLeave19 = "<span>" + FormatDecimal(vData.Leave19,2) + "</span>";
        tdLeave20 = "<span>" + FormatDecimal(vData.Leave20,2) + "</span>";

        tdOTHours01 = "<span>" + FormatDecimal(vData.OTHours01,2) + "</span>";
        tdOTHours02 = "<span>" + FormatDecimal(vData.OTHours02,2) + "</span>";
        tdOTHours03 = "<span>" + FormatDecimal(vData.OTHours03,2) + "</span>";
        tdOTHours04 = "<span>" + FormatDecimal(vData.OTHours04,2) + "</span>"; 
        tdOTHours05 = "<span>" + FormatDecimal(vData.OTHours05,2) + "</span>"; 
        tdOTHours06 = "<span>" + FormatDecimal(vData.OTHours06,2) + "</span>";
        tdOTHours07 = "<span>" + FormatDecimal(vData.OTHours07,2) + "</span>";
        tdOTHours08 = "<span>" + FormatDecimal(vData.OTHours08,2) + "</span>";
        tdOTHours09 = "<span>" + FormatDecimal(vData.OTHours09,2) + "</span>"; 
        tdOTHours10 = "<span>" + FormatDecimal(vData.OTHours10,2) + "</span>"; 
        tdOTHours11 = "<span>" + FormatDecimal(vData.OTHours11,2) + "</span>";
        tdOTHours12= "<span>" + FormatDecimal(vData.OTHours12,2) + "</span>";
        tdOTHours13 = "<span>" + FormatDecimal(vData.OTHours13,2) + "</span>";
        tdOTHours14 = "<span>" + FormatDecimal(vData.OTHours14,2) + "</span>"; 
        tdOTHours15 = "<span>" + FormatDecimal(vData.OTHours15,2) + "</span>"; 
        tdOTHours16 = "<span>" + FormatDecimal(vData.OTHours16,2) + "</span>";
        tdOTHours17 = "<span>" + FormatDecimal(vData.OTHours17,2) + "</span>";
        tdOTHours18 = "<span>" + FormatDecimal(vData.OTHours18,2) + "</span>";
        tdOTHours19 = "<span>" + FormatDecimal(vData.OTHours19,2) + "</span>"; 
        tdOTHours20 = "<span>" + FormatDecimal(vData.OTHours20,2) + "</span>"; 
        tdOTHours21 = "<span>" + FormatDecimal(vData.OTHours21,2) + "</span>";
        tdOTHours22 = "<span>" + FormatDecimal(vData.OTHours22,2) + "</span>";
        tdOTHours23 = "<span>" + FormatDecimal(vData.OTHours23,2) + "</span>";
        tdOTHours24 = "<span>" + FormatDecimal(vData.OTHours24,2) + "</span>"; 
        tdOTHours25 = "<span>" + FormatDecimal(vData.OTHours25,2) + "</span>"; 
        
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
                curData[2] = tdPayrollPeriodCode;
                curData[3] = tdYear;
                curData[4] = tdEmployeeCode;
                curData[5] = tdEmployeeName;                
                curData[6] = tdEmployeeRate;
                curData[7] = tdRegularHours;
                curData[8] = tdLateHours;
                curData[9] = tdUndertimeHours;
                curData[10] = tdNDHours;
                curData[11] = tdAbsent;
                curData[12] = tdLeave01;
                curData[13] = tdLeave02;
                curData[14] = tdLeave03;
                curData[15] = tdLeave04;
                curData[16] = tdLeave05;
                curData[17] = tdLeave06;
                curData[18] = tdLeave07;
                curData[19] = tdLeave08;
                curData[20] = tdLeave09;
                curData[21] = tdLeave10;
                curData[22] = tdLeave11;
                curData[23] = tdLeave12;
                curData[24] = tdLeave13;
                curData[25] = tdLeave14;
                curData[26] = tdLeave15;
                curData[27] = tdLeave16;
                curData[28] = tdLeave17;
                curData[29] = tdLeave18;
                curData[30] = tdLeave19;
                curData[31] = tdLeave20;
                curData[32] = tdOTHours01;
                curData[33] = tdOTHours02;
                curData[34] = tdOTHours03;
                curData[35] = tdOTHours04;
                curData[36] = tdOTHours05;
                curData[37] = tdOTHours06;
                curData[38] = tdOTHours07;
                curData[39] = tdOTHours08;
                curData[40] = tdOTHours09;
                curData[41] = tdOTHours10;
                curData[42] = tdOTHours11;
                curData[43] = tdOTHours12;
                curData[44] = tdOTHours13;
                curData[45] = tdOTHours14;
                curData[46] = tdOTHours15;
                curData[47] = tdOTHours16;
                curData[48] = tdOTHours17;
                curData[49] = tdOTHours18;
                curData[50] = tdOTHours19;
                curData[51] = tdOTHours20;
                curData[52] = tdOTHours21;
                curData[53] = tdOTHours22;
                curData[54] = tdOTHours23;
                curData[55] = tdOTHours24;
                curData[56] = tdOTHours25;
                curData[57] = tdStatus;
                curData[58] = tdIsUploadError;
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
                tdEmployeeCode,
                tdEmployeeName,
                tdEmployeeRate,
                tdRegularHours,
                tdLateHours,
                tdUndertimeHours,
                tdNDHours,
                tdAbsent,
                tdLeave01,
                tdLeave02,
                 tdLeave03,
                 tdLeave04,
                 tdLeave05,
                 tdLeave06,
                 tdLeave07,
                 tdLeave08,
                 tdLeave09,
                 tdLeave10,
                 tdLeave11,
                 tdLeave12,
                 tdLeave13,
                 tdLeave14,
                 tdLeave15,
                 tdLeave16,
                 tdLeave17,
                 tdLeave18,
                 tdLeave19,
                 tdLeave20,
                 tdOTHours01,
                 tdOTHours02,
                 tdOTHours03,
                 tdOTHours04,
                 tdOTHours05,
                 tdOTHours06,
                 tdOTHours07,
                 tdOTHours08,
                 tdOTHours09,
                 tdOTHours10,
                 tdOTHours11,
                 tdOTHours12,
                 tdOTHours13,
                 tdOTHours14,
                 tdOTHours15,
                 tdOTHours16,
                 tdOTHours17,
                 tdOTHours18,
                 tdOTHours19,
                 tdOTHours20,
                 tdOTHours21,
                 tdOTHours22,
                 tdOTHours23,
                 tdOTHours24,
                 tdOTHours25,
                 tdStatus,
                 tdIsUploadError

                ]).draw();          
        }


    }

function clearMessageNotification(){

      let toastMain1 = document.getElementsByClassName('toast-success')[0];
      toastMain1.classList.remove("toast-show");

       let toastMain2 = document.getElementsByClassName('toast-error')[0];
        toastMain2.classList.remove("toast-show");

      }

 function clearDTRTempTransaction(){

        $("#tblList-Excel").DataTable().clear().draw();

        $.ajax({
            type: "post",
            data: {
             _token: '{{ csrf_token() }}',
            Platform: "{{ config('app.PLATFORM_ADMIN') }}"
            },
            url: "{{ route('do-clear-dtr-temp-transaction')}}",
            dataType: "json",
            success: function(data){
                  $("#spnUploadedRecord").text('0');
                  $("#spnExcelRecord").text('0');                  
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
        url: "{{ route('do-remove-duplicate-dtr-transaction') }}",
        dataType: "json",
        success: function(data){
              if(data.Response =='Success'){
                showHasSuccessMessage(data.ResponseMessage);
                // DeleteTableRow(vRecID);
                getDTRTempRecordList(1);
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
 
 function getDTRTempUploadedCount(vExcelRecord){

      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}'
        },
        url: "{{ route('get-dtr-temp-transaction-upload-count') }}",
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

 $("#EmployeeName").blur(function() {
     vEmployee=$(this).val();
       if(vEmployee.length<=5 || vEmployee==''){
         $("#EmployeeID").val(0);
         $("#EmployeeNo").val('');

         $("#EmployeeRateID").val(0);
         $("#EmployeeRate").val('');
      }
  });
  
  $("#EmployeeName").keyup(function() { 
    vEmployee=$(this).val();
       if(vEmployee.length<=5 || vEmployee==''){
         $("#EmployeeID").val(0);
         $("#EmployeeNo").val('');

         $("#EmployeeRateID").val(0);
         $("#EmployeeRate").val('');
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
   
  function SaveFinalRecord(){

     vTotalError=$("#ErrorRecords").val(); 
      
      $.ajax({
        type: "post",
        data: {
         _token: '{{ csrf_token() }}'

        },
        url: "{{ route('do-upload-save-dtr-transaction') }}",
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

        $("#divLoader").hide();
        $("#divLoader1").hide();
        $("#spnLoader1Text").hide(); 

        console.log(data.responseText);
        },

        beforeSend:function(vData){

        showHasErrorMessage('', 'There are ' + vTotalError + ' dtr records will not be save due to data issues.');
        $("#divLoader").show(); 

        // $("#divLoader").show(); 
        // $("#divLoader1").show(); 

        // $("#spnLoader1Text").show(); 
        // $("#spnLoader1Text").text('Do not interrupt while uploading final data.'); 

        // $("#spnLoadingLabel").text('Uploading...');
              
        }

    });
 }

 function ClearEmployee(){

   $("#EmployeeID").val('0');
   $("#EmployeeNo").val('');
   $("#EmployeeName").val('');
   
   $("#EmployeeRateID").val('0'); 
   $("#EmployeeRate").val(''); 

    resetTextBorderToNormal();

 }

 function ClearPayrollPeriod(){

    $("#PayrollPeriodID").val('0'); 
    $("#PayrollPeriodYear").val('');
    $("#PayrollPeriodCode").val('');
    $("#PayrollPeriodName").val('');

     resetTextBorderToNormal();
 }

 $("#nav-basic-tab").click(function(){
    $("#nav-basic-tab").css({"background":"#f68c1f"}); 
    $("#nav-leaves-tab").css({"background":"#475F7B"}); 
    $("#nav-othours-tab").css({"background":"#475F7B"});
});

  $("#nav-leaves-tab").click(function(){
    $("#nav-leaves-tab").css({"background":"#f68c1f"}); 
    $("#nav-basic-tab").css({"background":"#475F7B"}); 
    $("#nav-othours-tab").css({"background":"#475F7B"});
});

 $("#nav-othours-tab").click(function(){
    $("#nav-othours-tab").css({"background":"#f68c1f"}); 
    $("#nav-basic-tab").css({"background":"#475F7B"}); 
    $("#nav-leaves-tab").css({"background":"#475F7B"});
});

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

      $("#nav-basic-tab").click();
  });

    $("#tblList").click(function(){
       $(this).addClass('selected').siblings().removeClass('selected');        
    });
          
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
            showHasErrorMessage('','Browse and upload Employee DTR summary csv file.');
            return;
        }
        
        const file = fileInput.files[0];
        
        if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
             showHasErrorMessage('','Please upload a valid Employee DTR csv file.');
            return;
        }
        
        // Read the file
        const reader = new FileReader();
        
        reader.onload = function(e) {

           clearMessageNotification();
           clearDTRTempTransaction();

            const contents = e.target.result;
            const employee_dtr_temp_record = [];
             
            var NoNEmptyRowCount=1; 
            const vDataLen=contents.length-1;
            const lines = contents.split('\n');
            
            //SKIP HEADER COLUMN
            for (let i = 1; i < lines.length; i++) {
                const line = lines[i].trim();

                if (line) {

                    NoNEmptyRowCount++;
                    const vData = line.split(',');

                    
                        vDTR_ID = 0;
                        vEmployeeRate = 0; 
                        vTransDate=new Date();

                        vPayrollPeriodCode=(vData[0]!=undefined ? vData[0] : '');

                        if(vPayrollPeriodCode=='END' || vPayrollPeriodCode==''){
                        break;
                        }

                        vYear=(vData[1]!=undefined ? vData[1] :  vCurrentDate.getFullYear());  
                        vEmployeeNo=(vData[2]!=undefined ? vData[2] : '');

                        vRegularHours=(vData[3]!=undefined ? parseFloat(vData[3],2) : 0);
                        vLateHours=(vData[4]!=undefined ? parseFloat(vData[4],2) : 0 );
                        vUnderTimeHours=(vData[5]!=undefined ? parseFloat(vData[5],2) : 0);
                        vNDHours=(vData[6]!=undefined ? parseFloat(vData[6],2) : 0);
                        vAbsent=(vData[7]!=undefined ? parseFloat(vData[7],2) : 0);

                        // Leaves
                        vLeave01=(vData[8]!=undefined ? parseFloat(vData[8],2) : 0);
                        vLeave02=(vData[9]!=undefined ? parseFloat(vData[9],2) : 0);
                        vLeave03=(vData[10]!=undefined ? parseFloat(vData[10],2) : 0);
                        vLeave04=(vData[11]!=undefined ? parseFloat(vData[11],2) : 0);
                        vLeave05=(vData[12]!=undefined ? parseFloat(vData[12],2) : 0);
                        vLeave06=(vData[13]!=undefined ? parseFloat(vData[13],2) : 0);
                        vLeave07=(vData[14]!=undefined ? parseFloat(vData[14],2) : 0);
                        vLeave08=(vData[15]!=undefined ? parseFloat(vData[15],2) : 0);
                        vLeave09=(vData[16]!=undefined ? parseFloat(vData[16],2) : 0);
                        vLeave10=(vData[17]!=undefined ? parseFloat(vData[17],2) : 0);
                        vLeave11=(vData[18]!=undefined ? parseFloat(vData[18],2) : 0);
                        vLeave12=(vData[19]!=undefined ? parseFloat(vData[19],2) : 0);
                        vLeave13=(vData[20]!=undefined ? parseFloat(vData[20],2) : 0);
                        vLeave14=(vData[21]!=undefined ? parseFloat(vData[21],2) : 0);
                        vLeave15=(vData[22]!=undefined ? parseFloat(vData[22],2) : 0);
                        vLeave16=(vData[23]!=undefined ? parseFloat(vData[23],2) : 0);
                        vLeave17=(vData[24]!=undefined ? parseFloat(vData[24],2) : 0);
                        vLeave18=(vData[25]!=undefined ? parseFloat(vData[25],2) : 0);
                        vLeave19=(vData[26]!=undefined ? parseFloat(vData[26],2) : 0);
                        vLeave20=(vData[27]!=undefined ? parseFloat(vData[27],2) : 0);

                        // OT Hours
                        vOTHours01=(vData[28]!=undefined ? parseFloat(vData[28],2) : 0);
                        vOTHours02=(vData[29]!=undefined ? parseFloat(vData[29],2) : 0);
                        vOTHours03=(vData[30]!=undefined ? parseFloat(vData[30],2) : 0);
                        vOTHours04=(vData[31]!=undefined ? parseFloat(vData[31],2) : 0);
                        vOTHours05=(vData[32]!=undefined ? parseFloat(vData[32],2) : 0);
                        vOTHours06=(vData[33]!=undefined ? parseFloat(vData[33],2) : 0);
                        vOTHours07=(vData[34]!=undefined ? parseFloat(vData[34],2) : 0);
                        vOTHours08=(vData[35]!=undefined ? parseFloat(vData[35],2) : 0);
                        vOTHours09=(vData[36]!=undefined ? parseFloat(vData[36],2) : 0);
                        vOTHours10=(vData[37]!=undefined ? parseFloat(vData[37],2) : 0);
                        vOTHours11=(vData[38]!=undefined ? parseFloat(vData[38],2) : 0);
                        vOTHours12=(vData[39]!=undefined ? parseFloat(vData[39],2) : 0);
                        vOTHours13=(vData[40]!=undefined ? parseFloat(vData[40],2) : 0);
                        vOTHours14=(vData[41]!=undefined ? parseFloat(vData[41],2) : 0);
                        vOTHours15=(vData[42]!=undefined ? parseFloat(vData[42],2) : 0);
                        vOTHours16=(vData[43]!=undefined ? parseFloat(vData[43],2) : 0);
                        vOTHours17=(vData[44]!=undefined ? parseFloat(vData[44],2) : 0);
                        vOTHours18=(vData[45]!=undefined ? parseFloat(vData[45],2) : 0);
                        vOTHours19=(vData[46]!=undefined ? parseFloat(vData[46],2) : 0);
                        vOTHours20=(vData[47]!=undefined ? parseFloat(vData[47],2) : 0);
                        vOTHours21=(vData[48]!=undefined ? parseFloat(vData[48],2) : 0);
                        vOTHours22=(vData[49]!=undefined ? parseFloat(vData[49],2) : 0);
                        vOTHours23=(vData[50]!=undefined ? parseFloat(vData[50],2) : 0);
                        vOTHours24=(vData[51]!=undefined ? parseFloat(vData[51],2) : 0);
                        vOTHours25=(vData[52]!=undefined ? parseFloat(vData[52],2) : 0);

                        vIsUploaded=1;
                        vStatus='Pending';

                    
                    // Collect employee temp loan data
                    employee_dtr_temp_record.push({
                        DTR_ID: vDTR_ID,
                        Year: vYear,
                        PayrollCode: vPayrollPeriodCode,
                        TransDate: vTransDate,
                        EmpNo: vEmployeeNo,
                        EmpRate: vEmployeeRate,
                        RegHours: vRegularHours,
                        LateHours: vLateHours,
                        UnderTimeHours: vUnderTimeHours,
                        NDHours: vNDHours,
                        Absent: vAbsent,
                        Leave01: vLeave01,
                        Leave02: vLeave02,
                        Leave03: vLeave03,
                        Leave04: vLeave04,
                        Leave05: vLeave05,
                        Leave06: vLeave06,
                        Leave07: vLeave07,
                        Leave08: vLeave08,
                        Leave09: vLeave09,
                        Leave10: vLeave10,
                        Leave11: vLeave11,
                        Leave12: vLeave12,
                        Leave13: vLeave13,
                        Leave14: vLeave14,
                        Leave15: vLeave15,
                        Leave16: vLeave16,
                        Leave17: vLeave17,
                        Leave18: vLeave18,
                        Leave19: vLeave19,
                        Leave20: vLeave20,
                        OTHours01: vOTHours01,
                        OTHours02: vOTHours02,
                        OTHours03: vOTHours03,
                        OTHours04: vOTHours04,
                        OTHours05: vOTHours05,
                        OTHours06: vOTHours06,
                        OTHours07: vOTHours07,
                        OTHours08: vOTHours08,
                        OTHours09: vOTHours09,
                        OTHours10: vOTHours10,
                        OTHours11: vOTHours11,
                        OTHours12: vOTHours12,
                        OTHours13: vOTHours13,
                        OTHours14: vOTHours14,
                        OTHours15: vOTHours15,
                        OTHours16: vOTHours16,
                        OTHours17: vOTHours17,
                        OTHours18: vOTHours18,
                        OTHours19: vOTHours19,
                        OTHours20: vOTHours20,
                        OTHours21: vOTHours21,
                        OTHours22: vOTHours22,
                        OTHours23: vOTHours23,
                        OTHours24: vOTHours24,
                        OTHours25: vOTHours25,
                        IsUploaded:vIsUploaded,
                        Status: vStatus     
                    });

                }
            }
            
   
           // Process in batches of 10
            const temp_batches = [];
            for (let x = 0; x< employee_dtr_temp_record.length; x += recPerBatch) {
                temp_batches.push(employee_dtr_temp_record.slice(x, x + recPerBatch));
            }
          
          
            saveEmployeeTempDTRByBatchRecord(0);
            
            // Function to save each batch
            function saveEmployeeTempDTRByBatchRecord(batchIndex) {

                  $("#spnTotalData").text(batchIndex * recPerBatch +'/'+ parseInt(NoNEmptyRowCount-2));

                if (batchIndex >= temp_batches.length) {
                
                      getDTRTempUploadedCount(NoNEmptyRowCount-2);
                      getDTRTempRecordList(1); 

                      $("#upload-modal").modal('hide');        
                      $("#excel-modal").modal('show');
                    return;
                }
                
                const currentTempDTRBatch = temp_batches[batchIndex];

                 //SAVE Batch of data
                $.ajax({
                      type: "post",
                        url: "{{ route('do-save-dtr-temp-transaction-batch') }}",
                        data: {
                        _token: '{{ csrf_token() }}',
                        Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                        DTRTempDataItems: currentTempDTRBatch
                    },
                    dataType: "json",
                    success: function(data){

                        buttonOneClick("btnUploadTSSCSV", "Upload CSV", false);

                        if(data.Response =='Success'){
                          
                          $("#spnTotalData").hide();
                          $("#divLoader").hide();
                          saveEmployeeTempDTRByBatchRecord(batchIndex + 1);  // Proc


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

