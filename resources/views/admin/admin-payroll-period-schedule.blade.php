@extends('layout.adminweb')
@section('content')

<!--excel--->
<script src="{{ URL::to('public/admin/excel/xlsx.full.min.js') }}"></script>
<script src="{{ URL::to('public/admin/excel/FileSaver.js') }}"></script>

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
                                    <li class="breadcrumb-item active">Payroll Period Schedule List
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
                                    <h4 class="card-title">Payroll Period Schedule</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <fieldset>
                                                    <div class="input-group">

                                                        <select id="selSearchStatus" class="form-control" style="width:15%">
                                                          <option value="">All Record</option>
                                                          <option disabled="disabled">[ CutOff Option ]</option>
                                                          <option value="1st Half">CutOff: 1st Half</option>
                                                          <option value="2nd Half">CutOff: 2nd Half</option>
                                                          <option disabled="disabled">[ Status Option ]</option>
                                                          <option value="Close">Status: Closed</option>
                                                          <option value="Open">Status: Open</option>
                                                        </select>
                                                    
                                                        <input type="text" class="form-control searchtext" placeholder="Search Here.." style="width: 50%;margin-left: 6px;">

                                                        <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border" tooltip="Search Here.." tooltip-position="top">
                                                            <i class="bx bx-search"></i>
                                                        </button>

                                                        @if(Session::get('IS_SUPER_ADMIN') || $Allow_View_Print_Export==1)
                                                        <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="GenerateExcel()" tooltip="Export To Excel" tooltip-position="top" style="height: 40px;margin-left: -11px;">
                                                           <i class="bx bx-file"></i>
                                                        </button>
                                                      @endif  

                                                       @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload==1)
                                                        <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="NewRecord()" tooltip="Create New" tooltip-position="top">
                                                            <i class="bx bx-plus"></i> New
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
                                                        <th>CODE</th>
                                                        <th>START DATE: <span>(MM/DD/YYYY)</span></th>
                                                        <th>END DATE: <span>(MM/DD/YYYY)</span></th>
                                                        <th>CUT OFF</th>
                                                        <th>YEAR</th>                                                        
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
    <div id="record-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title white-color">Payroll Period Schedule Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="PayrollScheduleID" value="0" readonly>
                    <div class="row">
                         <div class="col-md-5">
                            <fieldset class="form-group">
                                <label for="PayrollScheduleCode">Code: <span class="required_field">*</span></label>
                                <input id="PayrollScheduleCode" type="text" class="form-control" placeholder="Schedule Code" autocomplete="off" maxlength="10">
                            </fieldset>
                        </div>
                   
                         <div class="col-md-7">
                            <fieldset class="form-group">
                                <label for="Status">Status: <span class="required_field">*</span></label>
                                <div class="form-group">
                                    <select id="Status" class="form-control">
                                       <option value="">Please Select</option>
                                        <option value="{{ config('app.STATUS_OPEN') }}">{{ config('app.STATUS_OPEN') }}</option>
                                        <option value="{{ config('app.STATUS_CLOSE') }}">{{ config('app.STATUS_CLOSE') }}</option>
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="PayrollStartDate">Payroll Start Date: <span class="required_field">*</span></label>
                                  <div class="div-percent">
                                   <input id="PayrollStartDate" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off"><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 20px;" onclick="LoadCalendar('PayrollStartDate')"></i> </span>
                                </div>

                            </fieldset>
                        </div>
                         <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="PayrollEndDate">Payroll End Date: <span class="required_field">*</span></label>
                                 <div class="div-percent">
                                   <input id="PayrollEndDate" type="text" class="form-control" placeholder="mm/dd/yyyy" autocomplete="off"><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 20px;" onclick="LoadCalendar('PayrollEndDate')"></i> </span>
                                </div>
                               
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                         <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="Status">Year: <span class="required_field">*</span></label>
                                <div class="form-group">
                                    <select id="Year" class="form-control">
                                       <option value="">Please Select</option>
                                         @php($CurYear = date("Y"))
                                        @for($x = $CurYear; $x >= 2023; $x--)
                                            <option value="{{ $x }}" {{ ($x == $CurYear ? "selected" : "") }}>{{ $x }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                         <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="Status">Cut Off: <span class="required_field">*</span></label>
                                <div class="form-group">
                                    <select id="CutOff" class="form-control">
                                       <option value="">Please Select</option>
                                        <option value="{{ config('app.PERIOD_1ST_HALF_ID') }}">1ST HALF</option>
                                        <option value="{{ config('app.PERIOD_2ND_HALF_ID') }}">2ND HALF</option>
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                    </div>
       
                 <div class="row">
                     <div class="form-group" style="width:100%">                             
                            <label for="Remarks">Remarks:    <span style="font-size: 11px;color: #b62020;  text-transform: lowercase;font-style: italic; font-weight: normal;"> ( characters: &nbsp; </span> <span class="remaining_chars"> 250</span></label><span style="font-size: 11px;color: #b62020;  text-transform: lowercase;font-style: italic; font-weight: normal;">&nbsp;)</span>
                           <textarea id="PayrollScheduleRemarks" class="form-control" rows="4"></textarea>                           
                     </div>
                </div> 

            </div>
                <div class="modal-footer">
                    <button id="btnSaveRecord" type="button" class="btn btn-primary ml-1" onclick="SaveRecord()">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block"> <i class='bx bx-save mr-1' style="font-size: 21px;"></i> Save</span>
                    </button>
                    <button id="btnCancelRecord" type="button" class="btn btn-light-secondary" data-dismiss="modal">
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
            "order": [[ 3, "asc" ]]
        });

        //Load Initial Data
        $("#tblList").DataTable().clear().draw();
        getRecordList(intCurrentPage, '');

        isPageFirstLoad = false;

        //===============================================================    
        var table = $('#tblList').DataTable();
        // Handle row click event
        $('#tblList tbody').on('click', 'tr', function() {            
            table.$('tr.highlighted').removeClass('highlighted');        
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
                SearchText: $('.searchtext').val(),
                Status: $("#selSearchStatus").val(),
                Limit: vLimit,
                PageNo: vPageNo
            },
            url: "{{ route('get-payroll-period-schedule-list') }}",
            dataType: "json",
            success: function(data){
                total_rec=data.TotalRecord;
                LoadRecordList(data.PayrollPeriodList);
                    if(total_rec>0){
                     CreatePayrollPeriodPaging(total_rec,vLimit);  
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
                  @if(config("app.DebugMode") == 1)
                    console.log(data.responseText);
                   @endif
            },
            beforeSend:function(vData){
                $("#divLoader").show();
            }

        });
    };

    function CreatePayrollPeriodPaging(vTotalRecord,vLimit){

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

        tdAction="";

        if(IsAdmin==1 || IsAllowView==1){

        tdAction = "<div class='dropdown'>";

                        if(vData.Status=='Open'){
                             tdAction = tdAction + "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:green;'></span> ";  
                             tdAction = tdAction +  "<div class='dropdown-menu dropdown-menu-right'>"; 
                         }else {
                            tdAction = tdAction + "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:red;'></span> ";
                            tdAction = tdAction +  "<div class='dropdown-menu dropdown-menu-right'>";                          
                         } 

                        if(IsAdmin==1 || IsAllowEdit==1){

                          tdAction = tdAction + 
                            "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",true)' style='border-bottom: 1px solid lightgray;'>" +
                            
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Information" +
                            "</a>";
                        
                        }

                        tdAction = tdAction +

                            "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.ID + ",false)'>"+
                                 "<i class='bx bx-search-alt mr-1'></i> " +
                                "View Information" +
                            "</a>";
                         
                        
                         tdAction = tdAction +  "</div>"+
                      
                    "</div>";
        }

        tdCode = "<span>" + vData.Code + "</span>";
        tdStartDate = "<span>" + vData.StartDateFormat + "</span>";
        tdEndDate = "<span>" + vData.EndDateFormat + "</span>";

        tdCutOff = "";

        if(vData.CutOff == 1){
            tdCutOff += "<span>1ST HALF</span>";
        }else if(vData.CutOff == 2){ 
            tdCutOff += "<span>2ND HALF</span>";
        }

        tdYear = "<span>" + vData.Year + "</span>";
        tdRemarks = "<span>" + vData.Remarks + "</span>";

        tdStatus = "";

        if(vData.Status == 'Open'){
            tdStatus += "<span style='color:green;display:flex;'> <i class='bx bx-check-circle'></i> Open </span>";
        }else{
            tdStatus += "<span style='color:red;display:flex;'> <i class='bx bx-x-circle'></i> Closed </span>";
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
                curData[2] = tdCode;
                curData[3] = tdStartDate;
                curData[4] = tdEndDate;
                curData[5] = tdCutOff;
                curData[6] = tdYear;                
                curData[7] = tdStatus;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,
                    tdAction,
                    tdCode,
                    tdStartDate,
                    tdEndDate,
                    tdCutOff,
                    tdYear,                    
                    tdStatus
                ]).draw();          
        }
    }

    function Clearfields(){

        EnabledDisbledText(false);

        $("#PayrollScheduleID").val('0');

        $("#PayrollScheduleCode").val('');
        $("#Year").val({{date("Y")}}).change();
        $("#CutOff").val('').change();
        $("#Status").val('').change();

        $("#PayrollStartDate").val('');
        $("#PayrollEndDate").val('');
        $("#PayrollScheduleRemarks").val('');
       
        $("#btnSaveRecord").show();
        $("#btnCancelRecord").show();

        resetTextBorderToNormal();

    }

    function EnabledDisbledText(vEnabled){


        $("#PayrollScheduleCode").attr('disabled', vEnabled); 
        $("#CutOff").attr('disabled', vEnabled); 
        $("#Status").attr('disabled', vEnabled); 

        $("#PayrollStartDate").attr('disabled', vEnabled); 
        $("#PayrollEndDate").attr('disabled', vEnabled); 
        $("#PayrollScheduleRemarks").attr('disabled', vEnabled); 

       $("#Status").attr('disabled', vEnabled); 
       $("#Year").attr('disabled', vEnabled); 
       
       $("#CutOff").attr('disabled', vEnabled);
       
       $("#PayrollStartDate").attr('disabled', vEnabled);
       $("#PayrollEndDate").attr('disabled', vEnabled);
    
   }

    function resetTextBorderToNormal(){

        $("#PayrollScheduleCode").css({"border":"#ccc 1px solid"});
        $("#PayrollScheduleRemarks").css({"border":"#ccc 1px solid"}); 
        $("#PayrollStartDate").css({"border":"#ccc 1px solid"}); 
        $("#PayrollEndDate").css({"border":"#ccc 1px solid"});
        $("#Year").css({"border":"#ccc 1px solid"});   
        $("#vCutOff").css({"border":"#ccc 1px solid"});  
        $("#Status").css({"border":"#ccc 1px solid"}); 

    }

    function NewRecord(){

        Clearfields();
        $("#record-modal").modal();

    }

    function EditRecord(vRecordID,vAllowEdit){

        if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    PayrollScheduleID: vRecordID
                },
                url: "{{ route('get-payroll-period-schedule-info') }}",
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.PayrollPeriodScheduleInfo != undefined){

                        Clearfields();

                        $("#PayrollScheduleID").val(data.PayrollPeriodScheduleInfo.ID);
                        $("#PayrollScheduleCode").val(data.PayrollPeriodScheduleInfo.Code);

                        $("#PayrollStartDate").val(data.PayrollPeriodScheduleInfo.StartDateFormat);
                        $("#PayrollEndDate").val(data.PayrollPeriodScheduleInfo.EndDateFormat);

                        $("#PayrollScheduleRemarks").val(data.PayrollPeriodScheduleInfo.Remarks);

                        $("#Year").val(data.PayrollPeriodScheduleInfo.Year).change();
                        $("#CutOff").val(data.PayrollPeriodScheduleInfo.CutOff).change();
                        $("#Status").val(data.PayrollPeriodScheduleInfo.Status).change();

                        $("#PayrollStartDate").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#PayrollEndDate").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#PayrollScheduleRemarks").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#Year").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#CutOff").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#Status").attr("style", "border: 1px solid rgb(204, 204, 204) !important");

                        buttonOneClick("btnSaveRecord", "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);                                    

                        if(vAllowEdit){
                            $("#btnSaveRecord").show();
                            EnabledDisbledText(false);
                            $("#btnCancelRecord").text('Cancel');
                        }else{
                            $("#btnSaveRecord").hide();
                            EnabledDisbledText(true);
                            $("#btnCancelRecord").text('Close');
                        }

                        $("#divLoader").hide();
                        $("#record-modal").modal();

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

    function SaveRecord(){

        var vPayrollScheduleID = $("#PayrollScheduleID").val();
        var vPayrollScheduleCode = $("#PayrollScheduleCode").val();
        var vPayrollStartDate = $("#PayrollStartDate").val();
        var vPayrollEndDate = $("#PayrollEndDate").val();
        
        var vPayrollScheduleRemarks = $("#PayrollScheduleRemarks").val();
        var vYear = $("#Year").val();
        var vCutOff = $("#CutOff").val();
        var vStatus = $("#Status").val();


        resetTextBorderToNormal();

        if(vPayrollScheduleCode=="") {
         showHasErrorMessage('PayrollScheduleCode','Enter payroll schedule code.');
         return;  
       }

        if(vStatus=="") {
         showHasErrorMessage('Status','Select status from the list.');
         return;  
       }

        if(vPayrollStartDate=="") {
         showHasErrorMessage('PayrollStartDate','Enter payroll period start date.');
         return;  
       }

        if(vPayrollEndDate=="") {
         showHasErrorMessage('PayrollEndDate','Enter payroll period end date.');
         return;  
       }

        if(vYear=="") {
         showHasErrorMessage('Year','Select payroll period year applied from the list.');
         return;  
       }

        if(vCutOff=="") {
         showHasErrorMessage('CutOff','Select cutoff from the list.');
         return;  
       }

        //Check Dates
       chkDate1=new Date($("#PayrollStartDate").val());
       chkDate2=new Date($("#PayrollEndDate").val());

       if(chkDate1>chkDate2){
         showHasErrorMessage('','Invalid Payroll Perion: End Date must be greater than Start Date.');
         return;   
       }

       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                PayrollScheduleID: $("#PayrollScheduleID").val(),
                PayrollScheduleCode: $("#PayrollScheduleCode").val(),
                PayrollScheduleRemarks: $("#PayrollScheduleRemarks").val(),
                PayrollStartDate: $("#PayrollStartDate").val(),
                PayrollEndDate: $("#PayrollEndDate").val(),
                PayrollCutOff: $("#CutOff").val(),
                PayrollYear: $("#Year").val(),
                Status: $("#Status").val()
            },
            url: "{{ route('do-save-payroll-period-schedule') }}",
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

                if(data.Response =='Success'){
                    $("#record-modal").modal('hide');
                    showHasSuccessMessage(data.ResponseMessage);
                    LoadRecordRow(data.PayrollPeriodScheduleInfo);
                }else{
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
                buttonOneClick("btnSaveRecord", "", true);
            }
        });

    }

 function GenerateExcel(){
    $.ajax({
          type: "post",
          data: {
              _token: '{{ csrf_token() }}',
              Platform : "{{ config('app.PLATFORM_ADMIN') }}"          
          },
          url: "{{ route('get-excel-payroll-period-list') }}",
          dataType: "json",
          success: function(data){

              if(data.Response=="Success"){
                   resultquery=data.AllPayrollPeriodList;
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
                            " CODE NO ",
                            " START DATE ",
                            " END DATE ", 
                            " CUT OFF ",
                            " YEAR ",
                            " REMARKS ",
                            " STATUS "                            
                          ];

          
            xlsRows=resultquery;
          
            createXLSLFormatObj.push(xlsHeader);

              $.each(xlsRows, function(index, value) {
                  var innerRowData = [];   
                  $.each(value, function(ind, val) {
                    
                    // if (ind=="CurrentCoins"){
                    //     val=parseFloat(val);
                    // }

                    innerRowData.push(val);

                  });

                  createXLSLFormatObj.push(innerRowData);
        
              });

            var ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);
            const countheader = Object.keys(createXLSLFormatObj); // columns name header to

             var wscols = [];
              for (var i = 0; i < countheader.length; i++) {  // columns length added
                 wscols.push({ wch: Math.max(countheader[i].toString().length + 25) })
              }
              
            ws['!cols'] = wscols;
                       
            /* File Name */
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws,"Payroll Period List Sheet");

            /* generate file and download */
            const wbout = XLSX.write(wb, { type: "array", bookType: "xlsx" });
            saveAs(new Blob([wbout], { type: "application/octet-stream" }), "All-Payroll-Period-Report.xlsx");

            showHasSuccessMessage('Excel file has successfully created & downloaded at Download Folder');  
                          
     }

  $( function() {
    $("#PayrollStartDate").datepicker();
    $("#PayrollEndDate").datepicker();
  });
 
 function LoadCalendar(vElem){
    $("#"+vElem).focus();
}

  var max_length = 250;
  $("#PayrollScheduleRemarks").on('change keyup keydown', function() {
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



