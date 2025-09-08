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
                                    <li class="breadcrumb-item active"> Employee DTR Report
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
                                    <h4 class="card-title"> Employee DTR Report </h4>
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
                                                                        <!-- <option value="Division">Division</option> -->
                                                                        <option value="Department">Department</option>
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
                                                                        <select id="GeneratePayrollBranch" class="form-control">
                                                                            <option value="">Please Select</option>
                                                                            @foreach($BranchList as $brow)
                                                                            <option value="{{ $brow->ID }}">{{ $brow->BranchName }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div> 
                                                                    <div id="divSite" class="div-percent" style="display:none;">
                                                                        <select id="GeneratePayrollSite" class="form-control">
                                                                            <option value="">Please Select</option>
                                                                            @foreach($BranchSite as $siterow)
                                                                            <option value="{{ $siterow->ID }}">{{ $siterow->SiteName }}</option>
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

                                                     <!--    <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="Print()">
                                                            <i class="bx bx-printer"></i> Print
                                                        </button> -->

                                                        <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="GenerateExcel()" tooltip="Excel Report" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;margin-top: 23px;margin-left: -13px;">
                                                         <i class="bx bx-file"></i> 
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
                                                        <th style="min-width: 60px;">PERIOD</th>
                                                        <th style="min-width: 60px;">YEAR</th>
                                                        <th style="min-width: 110px;">EMPLOYEE ID</th>
                                                        <th style="min-width: 120px;">EMPLOYEE NAME</th>
                                                        <th style="min-width: 110px;">HOUR RATE</th>
                                                        <th style="min-width: 110px;">REG. HRS</th>
                                                        <th style="min-width: 110px;">LATE HRS</th>
                                                        <th style="min-width: 110px;">UNDER TIME HRS</th>
                                                        <th style="min-width: 110px;">ND HRS</th>
                                                        <th style="min-width: 110px;">TOTAL ABSENT</th>

                                                        <th style="min-width: 80px;">SL</th>
                                                        <th style="min-width: 80px;">VL</th>
                                                        <th style="min-width: 80px;">EL</th>
                                                        <th style="min-width: 80px;">ML</th>
                                                        <th style="min-width: 80px;">PL</th>
                                                        <th style="min-width: 80px;">SIL</th>
                                                        <th style="min-width: 80px;">ADO</th>
                                                        <th style="min-width: 80px;">SPL</th>
                                                        <th style="min-width: 110px;">LEAVE 09</th>
                                                        <th style="min-width: 80px;">SWL</th>
                                                        <th style="min-width: 110px;">LEAVE 11</th>
                                                        <th style="min-width: 110px;">LEAVE 12</th>
                                                        <th style="min-width: 110px;">LEAVE 13</th>
                                                        <th style="min-width: 110px;">LEAVE 14</th>
                                                        <th style="min-width: 110px;">LEAVE 15</th>
                                                        <th style="min-width: 110px;">LEAVE 16</th>
                                                        <th style="min-width: 110px;">LEAVE 17</th>
                                                        <th style="min-width: 110px;">LEAVE 18</th>
                                                        <th style="min-width: 110px;">LEAVE 19</th>
                                                        <th style="min-width: 110px;">LEAVE 20</th>

                                                        <th style="min-width: 80px;">ROT</th>
                                                        <th style="min-width: 80px;">NPROT</th>
                                                        <th style="min-width: 80px;">DO</th>
                                                        <th style="min-width: 80px;">SH</th>
                                                        <th style="min-width: 80px;">LH</th>
                                                        <th style="min-width: 80px;">SHDO</th>
                                                        <th style="min-width: 80px;">LHDO</th>
                                                        <th style="min-width: 80px;">OTDO</th>
                                                        <th style="min-width: 80px;">OTSH</th>
                                                        <th style="min-width: 80px;">OTLH</th>
                                                        <th style="min-width: 80px;">OTSHDO</th>
                                                        <th style="min-width: 80px;">OTLHDO</th>
                                                        <th style="min-width: 80px;">NNDO</th>
                                                        <th style="min-width: 80px;">NDSH</th>
                                                        <th style="min-width: 80px;">NDLH</th>
                                                        <th style="min-width: 80px;">NDSHDO</th>
                                                        <th style="min-width: 80px;">NDLHDO</th>
                                                        <th style="min-width: 80px;">NPDO</th>
                                                        <th style="min-width: 80px;">NPSH</th>
                                                        <th style="min-width: 80px;">NPLH</th>
                                                        <th style="min-width: 80px;">NPSHDO</th>
                                                        <th style="min-width: 80px;">NPLHDO</th>

                                                        <th style="min-width: 110px;">OT HOURS 23</th>
                                                        <th style="min-width: 110px;">OT HOURS 24</th>
                                                        <th style="min-width: 110px;">OT HOURS 25</th>
                                                        <th>STATUS</th>
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
            "order": [[ 3, "asc" ]]
        });
         
        //Load Initial Data
        $("#tblList").DataTable().clear().draw();
        // getRecordList(intCurrentPage);
        isPageFirstLoad = false;

         //SET FULL ROW HIGHLIGHT        
        var tblList = $('#tblList').DataTable();        
        $('#tblList tbody').on('click', 'tr', function() {            
            tblList.$('tr.highlighted').removeClass('highlighted');        
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

  function getRecordList(vPageNo){

      $("#tblList").DataTable().clear().draw();
      $(".paginate_button").remove(); 
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
                DepartmentID: $("#GeneratePayrollDepartment").val(),
                DivisionID: 0,
                SectionID: 0,
                JobTypeID: $("#GeneratePayrollJobType").val(),
                EmployeeID: $("#GeneratePayrollEmployee").val(),
                Status: $("#Status").val(),
                Limit: vLimit,
                PageNo: vPageNo
            },
            url: "{{ route('get-employee-dtr-report-list') }}",
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
                        LoadRecordList(data.EmployeeDTRReport);
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

        tdID = vData.ID;
                    
        tdYear = "<span>" + vData.Year + "</span>";
        tdPayrollPeriodCode = "<span>" + vData.PayrollPeriodCode + "</span>";
        tdEmployeeCode = "<span>" + vData.EmployeeNumber + "</span>";
        tdEmployeeName = "<span>" + vData.FullName + "</span>";

        tdEmployeeRate = "<span class='font-normal float_right'>" + FormatDecimal(vData.EmployeeRate,2) + "</span>";
        tdRegularHours = "<span class='font-normal float_right'>" + FormatDecimal(vData.RegularHours,2) + "</span>";
        tdLateHours = "<span class='font-normal float_right'>" + FormatDecimal(vData.LateHours,2) + "</span>";
        tdUndertimeHours = "<span class='font-normal float_right'>" + FormatDecimal(vData.UndertimeHours,2) + "</span>";
        tdNDHours = "<span class='font-normal float_right'>" + FormatDecimal(vData.NDHours,2) + "</span>";
        tdAbsent = "<span class='font-normal float_right'>" + FormatDecimal(vData.Absent,2) + "</span>";

        tdLeave01 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave01,2) + "</span>";
        tdLeave02 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave02,2) + "</span>";
        tdLeave03 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave03,2) + "</span>";
        tdLeave04 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave04,2) + "</span>";
        tdLeave05 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave05,2) + "</span>";
        tdLeave06 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave06,2) + "</span>"; 
        tdLeave07 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave07,2) + "</span>";
        tdLeave08 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave08,2) + "</span>";
        tdLeave09 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave09,2) + "</span>";
        tdLeave10 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave10,2) + "</span>";
        tdLeave11 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave11,2) + "</span>";
        tdLeave12 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave12,2) + "</span>"; 
        tdLeave13 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave13,2) + "</span>";
        tdLeave14 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave14,2) + "</span>";
        tdLeave15 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave15,2) + "</span>";
        tdLeave16 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave16,2) + "</span>";
        tdLeave17 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave17,2) + "</span>";
        tdLeave18 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave18,2) + "</span>"; 
        tdLeave19 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave19,2) + "</span>";
        tdLeave20 = "<span class='font-normal float_right'>" + FormatDecimal(vData.Leave20,2) + "</span>";

        tdOTHours01 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours01,2) + "</span>";
        tdOTHours02 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours02,2) + "</span>";
        tdOTHours03 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours03,2) + "</span>";
        tdOTHours04 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours04,2) + "</span>"; 
        tdOTHours05 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours05,2) + "</span>"; 
        tdOTHours06 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours06,2) + "</span>";
        tdOTHours07 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours07,2) + "</span>";
        tdOTHours08 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours08,2) + "</span>";
        tdOTHours09 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours09,2) + "</span>"; 
        tdOTHours10 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours10,2) + "</span>"; 
        tdOTHours11 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours11,2) + "</span>";
        tdOTHours12 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours12,2) + "</span>";
        tdOTHours13 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours13,2) + "</span>";
        tdOTHours14 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours14,2) + "</span>"; 
        tdOTHours15 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours15,2) + "</span>"; 
        tdOTHours16 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours16,2) + "</span>";
        tdOTHours17 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours17,2) + "</span>";
        tdOTHours18 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours18,2) + "</span>";
        tdOTHours19 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours19,2) + "</span>"; 
        tdOTHours20 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours20,2) + "</span>"; 
        tdOTHours21 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours21,2) + "</span>";
        tdOTHours22 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours22,2) + "</span>";
        tdOTHours23 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours23,2) + "</span>";
        tdOTHours24 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours24,2) + "</span>"; 
        tdOTHours25 = "<span class='font-normal float_right'>" + FormatDecimal(vData.OTHours25,2) + "</span>"; 
        
        tdStatus = "";
        if(vData.Status == 'Approved'){
            tdStatus += "<span style='color:green;display:flex;'> <i class='bx bx-check-circle'></i> Posted </span>";
        }
        if(vData.Status == 'Pending'){
            tdStatus += "<span style='color:red;display:flex;'> <i class='bx bx-x-circle'></i> Un-Posted </span>";
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
                curData[1] = tdPayrollPeriodCode;
                curData[2] = tdYear;
                curData[3] = tdEmployeeCode;
                curData[4] = tdEmployeeName;                
                curData[5] = tdEmployeeRate;
                curData[6] = tdRegularHours;
                curData[7] = tdLateHours;
                curData[8] = tdUndertimeHours;
                curData[9] = tdNDHours;
                curData[10] = tdAbsent;
                curData[11] = tdLeave01;
                curData[12] = tdLeave02;
                curData[13] = tdLeave03;
                curData[14] = tdLeave04;
                curData[15] = tdLeave05;
                curData[16] = tdLeave06;
                curData[17] = tdLeave07;
                curData[18] = tdLeave08;
                curData[19] = tdLeave09;
                curData[20] = tdLeave10;
                curData[21] = tdLeave11;
                curData[22] = tdLeave12;
                curData[23] = tdLeave13;
                curData[24] = tdLeave14;
                curData[25] = tdLeave15;
                curData[26] = tdLeave16;
                curData[27] = tdLeave17;
                curData[28] = tdLeave18;
                curData[29] = tdLeave19;
                curData[30] = tdLeave20;
                curData[31] = tdOTHours01;
                curData[32] = tdOTHours02;
                curData[33] = tdOTHours03;
                curData[34] = tdOTHours04;
                curData[35] = tdOTHours05;
                curData[36] = tdOTHours06;
                curData[37] = tdOTHours07;
                curData[38] = tdOTHours08;
                curData[39] = tdOTHours09;
                curData[40] = tdOTHours10;
                curData[41] = tdOTHours11;
                curData[42] = tdOTHours12;
                curData[43] = tdOTHours13;
                curData[44] = tdOTHours14;
                curData[45] = tdOTHours15;
                curData[46] = tdOTHours16;
                curData[47] = tdOTHours17;
                curData[48] = tdOTHours18;
                curData[49] = tdOTHours19;
                curData[50] = tdOTHours20;
                curData[51] = tdOTHours21;
                curData[52] = tdOTHours22;
                curData[53] = tdOTHours23;
                curData[54] = tdOTHours24;
                curData[55] = tdOTHours25;
                curData[56] = tdStatus;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                tdID,
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
                 tdStatus
            ]).draw();          
        }
    }


  function Print(){

      vPayrollPeriodID= $("#SearchPayrollPeriodCode").val();

      if(total_rec>0){
         window.open('{{config('app.url')}}admin-employee-dtr-summary-print-report?PayrollPeriodID=' +vPayrollPeriodID, '_blank')
      }else{
         showHasErrorMessage('','No record(s) found base on search criteria.');
      }
    }
    
 function GenerateExcel(){

     if(total_rec<=0){
        showHasErrorMessage('','No record(s) found base on search criteria.');
         return;   
      }
      
        showHasSuccessMessage('Please wait while generating DTR Employee list excel file.');                     
         
        $.ajax({
                  type: "post",
                  data: {
                      _token: '{{ csrf_token() }}',
                      Platform : "{{ config('app.PLATFORM_ADMIN') }}",          
                      PayrollPeriodID: $("#SearchPayrollPeriodCode").val(),  
                      FilterType: $("#GenerateFilter").val(),
                      BranchID: $("#GeneratePayrollBranch").val(),
                      SiteID: $("#GeneratePayrollSite").val(),
                      DivisionID: 0,
                      SectionID: 0,
                      DepartmentID: $("#GeneratePayrollDepartment").val(),
                      JobTypeID: $("#GeneratePayrollJobType").val(),
                      EmployeeID: $("#GeneratePayrollEmployee").val(), 
                      Status: $("#Status").val(), 
                  },
                  url: "{{ route('get-excel-employee-dtr-list') }}",
                  dataType: "json",
                  success: function(data){

                      if(data.Response=="Success"){
                           resultquery=data.EmployeeDTRExcelList;
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

          var xlsReportHeader = [
                            "NITRO PACIFIC",
                            "",
                            ""
                          ];
          createXLSLFormatObj.push(xlsReportHeader);
          xlsReportHeader = [
                            "Employee DTR Report",
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
                            "PERIOD CODE",
                            "EMPLOYEE NO.",
                            "EMPLOYEE NAME",
                            "HOUR RATE",
                            "REGULAR HRS",
                            "LATE HRS", 
                            "UNDERTIME HRS",
                            "ND HRS",
                            "ABSENT HRS",
                            "SL",
                            "VL",
                            "EL",
                            "ML", 
                            "PL",
                            "SIL",
                            "ADO",
                            "SPL",
                            "LEAVE09",
                            "SWL",
                            "LEAVE11",
                            "LEAVE12",
                            "LEAVE13",
                            "LEAVE14",
                            "LEAVE15",
                            "LEAVE16",
                            "LEAVE17",
                            "LEAVE18",
                            "LEAVE19", 
                            "LEAVE20",
                            "ROT",
                            "NPROT",
                            "DO",
                            "SH",
                            "LH", 
                            "SHDO",
                            "LHDO",
                            "OTDO",
                            "OTSH",
                            "OTLH", 
                            "OTSHDO", 
                            "OTLHDO", 
                            "NNDO",  
                            "NDSH", 
                            "NDLH", 
                            "NDSHDO",
                            "NDLHDO",
                            "NPDO",
                            "NPSH",
                            "NPLH", 
                            "NPSHDO",
                            "NPLHDO",
                            "OT23",
                            "OT24",
                            "OT25",
                            "STATUS"     
                          ];

          
            xlsRows=resultquery;
          
            createXLSLFormatObj.push(xlsHeader);
            var intRowCnt = 5;

              $.each(xlsRows, function(index, value) {
                  var innerRowData = [];   
                  $.each(value, function(ind, val) {
               
                      
                    if(ind=='PayrollPeriodCode' || ind=='EmployeeNumber' || ind=='FullName' || ind=='Status'){

                      if(ind=='Status'){                            
                          if(val=='Approved'){
                            val='Posted'
                          }else{
                             val='Un-Posted'
                           } 
                       }

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

                for (var c = 0; c < xlsHeader.length; c++){
                    var ExcelCol = ExcelColumn(i, c);
                    ws[ExcelCol].z = '#,##0.00_);\\(#,##0.00\\)';
                    ws[ExcelCol].t = 'n';
                }
            }

            ws["A1"].s = font: {
                name: 'Arial',
                sz: 24,
                bold: true,
                color: { rgb: "000000" }
            };  

            const countheader = Object.keys(createXLSLFormatObj); // columns name header to

             var wscols = [];
              for (var i = 0; i < countheader.length; i++) {  // columns length added
                 wscols.push({ wch: Math.max(countheader[i].toString().length + 27) })
              }
              
            ws['!cols'] = wscols;

                       
            /* File Name */
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws,"Employee DTR Sheet");

            /* generate file and download */
            const wbout = XLSX.write(wb, { type: "array", bookType: "xlsx" });
            saveAs(new Blob([wbout], { type: "application/octet-stream" }), "Employee-DTR-Report.xlsx");

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

@endsection



