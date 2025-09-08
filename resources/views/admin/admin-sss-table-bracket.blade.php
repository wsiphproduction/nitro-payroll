@extends('layout.adminweb')
@section('content')

<!--excel--->
<script src="{{ URL::to('public/admin/excel/xlsx.full.min.js') }}"></script>
<script src="{{ URL::to('public/admin/excel/FileSaver.js') }}"></script>

<style>
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
                                    <li class="breadcrumb-item active">SSS Table List
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
                                    <h4 class="card-title">SSS Table </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <fieldset>
                                                    <div class="input-group">

                                                          <select id="selSearchStatus" class="form-control" style="width:15%">
                                                          <option value="">All Record</option>
                                                          <option disabled="disabled">[ Status Option ]</option>
                                                          <option value="Active">Status: Active</option>
                                                          <option value="Inactive">Status: Inactive</option>
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
                                                        <th>SALARY FROM</th>
                                                        <th>SALARY TO</th>
                                                        <th>MONTHLY SALARY CREDIT EC</th>
                                                        <th>MPF</th>
                                                        <th>MONTHLY SALARY CREDIT TOTAL</th>
                                                        <th>REGULAR ER</th>
                                                        <th>REGULAR EE</th>                                                        
                                                        <th>CONTRIBUTION TOTAL</th>
                                                        <th>EC ER</th>
                                                        <th>EC EE</th>
                                                        <th>EC ER/EE TOTAL</th>                                                        
                                                        <th>WISP ER</th>
                                                        <th>WISP EE</th>
                                                        <th>WISP ER/EE TOTAL</th>
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
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title white-color">SSS Table Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="SSS_ID" value="0" readonly>
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
                                <label for="Status">Status: <span class="required_field">*</span></label>
                                <div class="form-group">
                                    <select id="Status" class="form-control">
                                        <option value="">Please Select</option>
                                        <option value="{{ config('app.STATUS_ACTIVE') }}">{{ config('app.STATUS_ACTIVE') }}</option>
                                        <option value="{{ config('app.STATUS_INACTIVE') }}">{{ config('app.STATUS_INACTIVE') }}</option>
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                    </div>
               
                    <div class="row">
                         <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="SalaryFrom">Salary From: <span class="required_field">*</span></label>
                                <input id="SalaryFrom" type="text" class="form-control DecimalOnly text-align-right" placeholder="Salary From" autocomplete="off">
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="form-group">
                                <label for="SalaryTo">Salary To: <span class="required_field">*</span></label>
                                <input id="SalaryTo" type="text" class="form-control DecimalOnly text-align-right" placeholder="Salary To" autocomplete="off">
                            </fieldset>
                        </div>
                       
                    </div>

                        <div>
                        <fieldset class="fieldset-border" style="width:100%">
                         <legend class="legend-text"> | Monthly Salary Credit |</legend>

                         <div class="row">                                                    
                                <div class="col-md-4">
                                    <fieldset class="form-group">
                                        <label for="RegularSSEC">Regular SS/EC: <span class="required_field">*</span></label>
                                        <input id="RegularSSEC" type="text" class="form-control DecimalOnly text-align-right" placeholder="Regular SS/EC" autocomplete="off">
                                    </fieldset>
                                </div>
                                <div class="col-md-4">
                                    <fieldset class="form-group">
                                        <label for="RegularSSWISP">MPF: <span class="required_field">*</span></label>
                                        <input id="RegularSSWISP" type="text" class="form-control DecimalOnly text-align-right" placeholder="MPF" autocomplete="off">
                                    </fieldset>
                                </div> 
                                <div class="col-md-4">
                                    <fieldset class="form-group">
                                        <label for="RegularSSECWISPTotal">Total Salary Credit: <span class="required_field">*</span></label>
                                        <input id="RegularSSECWISPTotal" type="text" class="form-control DecimalOnly text-align-right" placeholder="Regular SS/EC/MPF Total" autocomplete="off">
                                    </fieldset>
                                </div>                      
                            </div>

                        </fieldset>
                    </div>

                    <div>
                        <fieldset class="fieldset-border" style="width:100%">
                         <legend class="legend-text"> | Contribution EE/ER |</legend>

                         <div class="row">                                                    
                                <div class="col-md-4">
                                    <fieldset class="form-group">
                                        <label for="RegularER">Regular ER: <span class="required_field">*</span></label>
                                        <input id="RegularER" type="text" class="form-control DecimalOnly text-align-right" placeholder="Regular ER" autocomplete="off">
                                    </fieldset>
                                </div>
                                <div class="col-md-4">
                                    <fieldset class="form-group">
                                        <label for="RegularEE">Regular EE: <span class="required_field">*</span></label>
                                        <input id="RegularEE" type="text" class="form-control DecimalOnly text-align-right" placeholder="Regular EE" autocomplete="off">
                                    </fieldset>
                                </div>  
                                  <div class="col-md-4">
                                    <fieldset class="form-group">
                                        <label for="RegularTotal">Total Contribution: <span class="required_field">*</span></label>
                                        <input id="RegularTotal" type="text" class="form-control DecimalOnly text-align-right" placeholder="Regular Total" autocomplete="off">
                                    </fieldset>
                                </div>                     
                            </div>

                        </fieldset>
                    </div>

                     <div>
                        <fieldset class="fieldset-border" style="width:100%">
                         <legend class="legend-text"> | EC EE/ER |</legend>

                         <div class="row">                                                    
                           
                            <div class="col-md-4">
                                <fieldset class="form-group">
                                    <label for="ECER">EC ER: <span class="required_field">*</span></label>
                                    <input id="ECER" type="text" class="form-control DecimalOnly text-align-right" placeholder="EC ER" autocomplete="off">
                                </fieldset>
                            </div>
                            <div class="col-md-4">
                                <fieldset class="form-group">
                                    <label for="ECEE">EC EE: <span class="required_field">*</span></label>
                                    <input id="ECEE" type="text" class="form-control DecimalOnly text-align-right" placeholder="EC EE" autocomplete="off">
                                </fieldset>
                            </div>  
                            <div class="col-md-4">
                                <fieldset class="form-group">
                                    <label for="ECTotal">Total EC: <span class="required_field">*</span></label>
                                    <input id="ECTotal" type="text" class="form-control DecimalOnly text-align-right" placeholder="EC Total" autocomplete="off">
                                </fieldset>
                              </div>                     
                            </div>
                        </fieldset>
                    </div>

                    <div>
                        <fieldset class="fieldset-border" style="width:100%">
                         <legend class="legend-text"> | WISP EE/ER |</legend>

                         <div class="row">                                                    
                            <div class="col-md-4">
                                <fieldset class="form-group">
                                    <label for="WISPER">WISP ER: <span class="required_field">*</span></label>
                                    <input id="WISPER" type="text" class="form-control DecimalOnly text-align-right" placeholder="WISP ER" autocomplete="off">
                                </fieldset>
                            </div>
                            <div class="col-md-4">
                            <fieldset class="form-group">
                                <label for="WISPEE">WISP EE: <span class="required_field">*</span></label>
                                <input id="WISPEE" type="text" class="form-control DecimalOnly text-align-right" placeholder="WISP EE" autocomplete="off">
                            </fieldset>
                           </div> 
                             <div class="col-md-4">
                            <fieldset class="form-group">
                                <label for="ECTotal">Total WISP: <span class="required_field">*</span></label>
                                <input id="WISPTotal" type="text" class="form-control DecimalOnly text-align-right" placeholder="WISP Total" autocomplete="off">
                            </fieldset>
                          </div>                        
                            </div>

                        </fieldset>
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

     <!-- MODAL -->
    <div id="upload-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title white-color">SSS Excel Uploader</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                          <h5>Browse & select SSS table excel file:</h5>
                         <fieldset class="form-group">
                                <label for="myfile">Select files:</label>
                                 <input type="file" id="myfile" name="myfile" accept=".xlsx, .xls"/>
                        </fieldset>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="btnSaveRecord" type="button" class="btn btn-primary ml-1" onclick="UploadExcelRecord()">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Upload</span>
                    </button>
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
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
            url: "{{ route('get-sss-table-bracket-list') }}",
            dataType: "json",
            success: function(data){
                total_rec=data.TotalRecord;
                LoadRecordList(data.SSSTableBracketList);
                if(total_rec>0){
                     CreateSSSTableBracketPaging(total_rec,vLimit);  
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

     function CreateSSSTableBracketPaging(vTotalRecord,vLimit){

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

                         if(vData.Status=='Active'){
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

        tdSalaryFrom = "<span>" + FormatDecimal(vData.RangeFrom,2) + "</span>";
        tdSalaryTo = "<span>" + FormatDecimal(vData.RangeTo,2) + "</span>";

        tdRegularSSEC = "<span>" + FormatDecimal(vData.RegularSSEC,2) + "</span>";
        tdRegularSSWISP = "<span>" + FormatDecimal(vData.RegularSSWISP,2) + "</span>";
        tdRegularSSECWISPTotal = "<span>" + FormatDecimal(vData.RegularSSECWISPTotal,2) + "</span>";

        tdRegularER = "<span>" + FormatDecimal(vData.RegularER,2) + "</span>";
        tdRegularEE = "<span>" + FormatDecimal(vData.RegularEE,2) + "</span>";
        tdRegularTotal = "<span>" + FormatDecimal(vData.RegularTotal,2) + "</span>";
        
        tdECEE = "<span>" + FormatDecimal(vData.ECEE,2) + "</span>";
        tdECER = "<span>" + FormatDecimal(vData.ECER,2) + "</span>";
        tdECTotal = "<span>" + FormatDecimal(vData.ECTotal,2) + "</span>";
        
        tdWispER = "<span>" + FormatDecimal(vData.WispER,2) + "</span>";
        tdWispEE = "<span>" + FormatDecimal(vData.WispEE,2) + "</span>";
        tdWispTotal = "<span>" + FormatDecimal(vData.WispTotal,2) + "</span>";
        
        tdYear = "<span>" + vData.Year + "</span>";

        tdStatus = "";

        if(vData.Status == 'Active'){
            tdStatus += "<span style='color:green;display:flex;'> <i class='bx bx-check-circle'></i> Active </span>";
        }else{
            tdStatus += "<span style='color:red;display:flex;'> <i class='bx bx-x-circle'></i> Inactive </span>";
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
                curData[2] = tdSalaryFrom;
                curData[3] = tdSalaryTo;
                curData[4] = tdRegularSSEC;
                curData[5] = tdRegularSSWISP;
                curData[6] = tdRegularSSECWISPTotal;
                curData[7] = tdRegularER;
                curData[8] = tdRegularEE;
                curData[9] = tdRegularTotal;
                curData[10] = tdECER;
                curData[11] = tdECEE;
                curData[12] = tdECTotal;
                curData[13] = tdWispER;
                curData[14] = tdWispEE;
                curData[15] = tdWispTotal;
                curData[16] = tdYear;
                curData[17] = tdStatus;

                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,
                    tdAction,
                    tdSalaryFrom,
                    tdSalaryTo,
                    tdRegularSSEC,
                    tdRegularSSWISP,
                    tdRegularSSECWISPTotal,
                    tdRegularER,
                    tdRegularEE,
                    tdRegularTotal,
                    tdECER,
                    tdECEE,                    
                    tdECTotal,
                    tdWispER,
                    tdWispEE,
                    tdWispTotal,                   
                    tdYear,
                    tdStatus
                ]).draw();          
        }
    }

    function Clearfields(){

        EnabledDisbledText(false);

        $("#SSS_ID").val('0');

        $("#Year").val('').change();
        $("#Status").val('').change();

        $("#SalaryFrom").val('');
        $("#SalaryTo").val('');

        $("#RegularSSEC").val('');
        $("#RegularSSWISP").val('');
        $("#RegularSSECWISPTotal").val('');
        
        $("#RegularER").val('');
        $("#RegularEE").val('');
        $("#RegularTotal").val('');
        
        $("#ECEE").val('');
        $("#ECER").val('');
        $("#ECTotal").val('');

        $("#WISPEE").val('');
        $("#WISPER").val('');
        $("#WISPTotal").val('');

        $("#btnSaveRecord").show();
        $("#btnCancelRecord").text('Cancel');

        resetTextBorderToNormal();

    }

    function EnabledDisbledText(vEnabled){

        $("#PayrollScheduleCode").attr('disabled', vEnabled); 
        
        $("#Year").attr('disabled', vEnabled); 
        $("#Status").attr('disabled', vEnabled); 

        $("#SalaryFrom").attr('disabled', vEnabled); 
        $("#SalaryTo").attr('disabled', vEnabled); 

        $("#RegularSSEC").attr('disabled', vEnabled); 
        $("#RegularSSWISP").attr('disabled', vEnabled); 
        $("#RegularSSECWISPTotal").attr('disabled', vEnabled); 
        
        $("#RegularER").attr('disabled', vEnabled); 
        $("#RegularEE").attr('disabled', vEnabled); 
        $("#RegularTotal").attr('disabled', vEnabled); 
        
        $("#ECEE").attr('disabled', vEnabled); 
        $("#ECER").attr('disabled', vEnabled); 
        $("#ECTotal").attr('disabled', vEnabled); 

        $("#WISPEE").attr('disabled', vEnabled); 
        $("#WISPER").attr('disabled', vEnabled); 
        $("#WISPTotal").attr('disabled', vEnabled); 

        $("#Year").attr('disabled', vEnabled); 
        $("#Status").attr('disabled', vEnabled); 

   }

    function resetTextBorderToNormal(){

        $("#SalaryFrom").css({"border":"#ccc 1px solid"});
        $("#SalaryTo").css({"border":"#ccc 1px solid"}); 
        
        $("#RegularSSEC").css({"border":"#ccc 1px solid"}); 
        $("#RegularSSWISP").css({"border":"#ccc 1px solid"}); 
        $("#RegularSSECWISPTotal").css({"border":"#ccc 1px solid"});

        $("#RegularER").css({"border":"#ccc 1px solid"}); 
        $("#RegularEE").css({"border":"#ccc 1px solid"}); 
        $("#RegularTotal").css({"border":"#ccc 1px solid"}); 
        
        $("#ECEE").css({"border":"#ccc 1px solid"}); 
        $("#ECER").css({"border":"#ccc 1px solid"}); 
        $("#ECTotal").css({"border":"#ccc 1px solid"}); 

        $("#WISPEE").css({"border":"#ccc 1px solid"}); 
        $("#WISPER").css({"border":"#ccc 1px solid"}); 
        $("#WISPTotal").css({"border":"#ccc 1px solid"}); 

        $("#Year").css({"border":"#ccc 1px solid"}); 
        $("#Status").css({"border":"#ccc 1px solid"}); 

    }
    
    function NewRecord(){

        Clearfields();
        $("#record-modal").modal();

    }

    function UploadRecord(){

        Clearfields();
        $("#upload-modal").modal();

    }

       function EditRecord(vRecordID,vAllowEdit){

        if(vRecordID > 0){
            $.ajax({
                type: "post",
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    SSS_ID: vRecordID
                },
                url: "{{ route('get-sss-table-bracket-info') }}",
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.SSSTableBracketInfo != undefined){

                        Clearfields();

                        $("#SSS_ID").val(data.SSSTableBracketInfo.ID);
                        if(data.SSSTableBracketInfo.RangeFrom==0){
                            $("#SalaryFrom").val('0.00');    
                        }else{
                           $("#SalaryFrom").val(data.SSSTableBracketInfo.RangeFrom); 
                        }
                        
                        $("#SalaryTo").val(FormatDecimal(data.SSSTableBracketInfo.RangeTo,2));
                        $("#RegularSSTotal").val(FormatDecimal(data.SSSTableBracketInfo.RegularSSTotal,2));

                        $("#RegularSSEC").val(FormatDecimal(data.SSSTableBracketInfo.RegularSSEC,2));
                        $("#RegularSSWISP").val(FormatDecimal(data.SSSTableBracketInfo.RegularSSWISP,2));
                        $("#RegularSSECWISPTotal").val(FormatDecimal(data.SSSTableBracketInfo.RegularSSECWISPTotal,2));

                        $("#RegularER").val(FormatDecimal(data.SSSTableBracketInfo.RegularER,2));
                        $("#RegularEE").val(FormatDecimal(data.SSSTableBracketInfo.RegularEE,2));
                        $("#RegularTotal").val(FormatDecimal(data.SSSTableBracketInfo.RegularTotal,2));
                        
                        $("#ECEE").val(FormatDecimal(data.SSSTableBracketInfo.ECEE,2));
                        $("#ECER").val(FormatDecimal(data.SSSTableBracketInfo.ECER,2));
                        $("#ECTotal").val(FormatDecimal(data.SSSTableBracketInfo.ECTotal,2));

                        $("#WISPER").val(FormatDecimal(data.SSSTableBracketInfo.WispER,2));
                        $("#WISPEE").val(FormatDecimal(data.SSSTableBracketInfo.WispEE,2));
                        $("#WISPTotal").val(FormatDecimal(data.SSSTableBracketInfo.WispTotal,2));
                                                                    
                        $("#Year").val(data.SSSTableBracketInfo.Year).change();
                        $("#Status").val(data.SSSTableBracketInfo.Status).change();

                        $("#SalaryTo").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#SalaryFrom").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#RegularSSEC").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#RegularSSWISP").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#RegularSSECWISPTotal").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#RegularER").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#RegularEE").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#RegularTotal").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#ECEE").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#ECER").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#ECTotal").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#WISPER").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#WISPEE").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#WISPTotal").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
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

        var vSSSID = $("#SSS_ID").val();
        var vSalaryFrom = $("#SalaryFrom").val();
        var vSalaryTo = $("#SalaryTo").val();

        var vRegularSSEC = $("#RegularSSEC").val();
        var vRegularSSWISP = $("#RegularSSWISP").val();
        var vRegularSSECWISPTotal = $("#RegularSSECWISPTotal").val();
        
        var vRegularER = $("#RegularER").val();
        var vRegularEE = $("#RegularEE").val();
        var vRegularTotal = $("#RegularTotal").val();
        
        var vECEE = $("#ECEE").val();
        var vECER = $("#ECER").val();
        var vECTotal = $("#ECTotal").val();

        var vWISPEE = $("#WISPEE").val();
        var vWISPER = $("#WISPER").val();
        var vWISPTotal = $("#WISPTotal").val();

        var vYear = $("#Year").val();
        var vStatus = $("#Status").val();
        

        resetTextBorderToNormal();

        if(vYear=="") {
         showHasErrorMessage('Year','Select SSS year applied from the list.');
         return;  
       }

        if(vStatus=="") {
         showHasErrorMessage('Status','Seledct status from the list.');
         return;  
       }

        if(vSalaryFrom=="") {
         showHasErrorMessage('SalaryFrom','Enter SSS salary range from.');
         return;  
       }

       if(vSalaryTo=="") {
         showHasErrorMessage('SalaryTo','Enter SSS salary range to.');
         return;  
       }

       if(vRegularSSEC=="") {
         showHasErrorMessage('RegularSSEC','Enter SSS regular SS/EC.');
         return;  
       }

       if(vRegularSSWISP=="") {
         showHasErrorMessage('RegularSSWISP','Enter SSS MPF.');
         return;  
       }

        if(vRegularSSECWISPTotal=="") {
         showHasErrorMessage('RegularSSECWISPTotal','Enter SSS regular SS/EC/WISP Total.');
         return;  
       }

        if(vRegularER=="") {
         showHasErrorMessage('RegularER','Enter SSS regular ER.');
         return;  
       }

        if(vRegularER=="") {
         showHasErrorMessage('RegularEE','Enter SSS regular EE.');
         return;  
       }

        if(vRegularTotal=="") {
         showHasErrorMessage('RegularTotal','Enter SSS regular Total.');
         return;  
       }

       if(vECEE=="") {
         showHasErrorMessage('ECEE','Enter SSS EC EE.');
         return;  
       }

         if(vECER=="") {
         showHasErrorMessage('ECER','Enter SSS EC ER.');
         return;  
       }

       if(vECTotal=="") {
         showHasErrorMessage('ECTotal','Enter SSS EC Total.');
         return;  
       }

       if(vWISPEE=="") {
         showHasErrorMessage('WISPEE','Enter SSS WISP EE.');
         return;  
       }

        if(vWISPER=="") {
         showHasErrorMessage('WISPER','Enter SSS WISP ER.');
         return;  
       }

        if(vWISPTotal=="") {
         showHasErrorMessage('WISPTotal','Enter SSS WISP Total.');
         return;  
       }



       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SSS_ID: $("#SSS_ID").val(),
                SalaryFrom: $("#SalaryFrom").val(),
                SalaryTo: $("#SalaryTo").val(),

                RegularSSEC: $("#RegularSSEC").val(),
                RegularSSWISP: $("#RegularSSWISP").val(),
                RegularSSECWISPTotal: $("#RegularSSECWISPTotal").val(),

                RegularEE: $("#RegularEE").val(),
                RegularER: $("#RegularER").val(),
                RegularTotal: $("#RegularTotal").val(),

                ECEE: $("#ECEE").val(),
                ECER: $("#ECER").val(),
                ECTotal: $("#ECTotal").val(),

                WISPEE: $("#WISPEE").val(),
                WISPER: $("#WISPER").val(),
                WISPTotal: $("#WISPTotal").val(),
                
                Year: $("#Year").val(),
                Status: $("#Status").val()
            },
            url: "{{ route('do-save-sss-table-bracket') }}",
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

                if(data.Response =='Success'){
                    $("#record-modal").modal('hide');
                    showHasSuccessMessage(data.ResponseMessage);
                    LoadRecordRow(data.SSSTableBracketInfo);
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
          url: "{{ route('get-excel-sss-bracket-list') }}",
          dataType: "json",
          success: function(data){

              if(data.Response=="Success"){
                   resultquery=data.AllSSSBracketList;
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
                            " SALARY FROM ",
                            " SALARY TO ",
                            " MONTHLY SALARY CREDIT EC ",
                            " MPF ",
                            " MONTHLY SALARY CREDIT TOTAL ",
                            " REGULAR ER ",
                            " REGULAR EE ",
                            " REGULAR TOTAL ",

                            " EEC ",
                            " ECER ",
                            " EC TOTAL ",

                            " WISP ER ",
                            " WISP EE ",
                            " WISP TOTAL ",

                            " YEAR ",
                            " STATUS "
                            
                          ];

          
            xlsRows=resultquery;
          
            createXLSLFormatObj.push(xlsHeader);

            var intRowCnt = 1;
              $.each(xlsRows, function(index, value) {
                  var innerRowData = [];   
                  $.each(value, function(ind, val) {
                    
                    if(ind == "RangeFrom" ||
                        ind == "RangeTo" ||
 
                        ind == "RegularSSEC" ||
                        ind == "RegularSSWISP" ||
                        ind == "RegularSSECWISPTotal" ||
 
                        ind == "RegularER" ||
                        ind == "RegularEE" ||
                        ind == "RegularTotal" ||

                        ind == "ECEE" ||
                        ind == "ECER" ||
                        ind == "ECTotal" ||

                        ind == "WispER" ||
                        ind == "WispEE" ||
                        ind == "WispTotal" ||

                        ind == "Year" ||
                        ind == "Status"){

                            innerRowData.push(val);
                        }
                    });

                    createXLSLFormatObj.push(innerRowData);
                    intRowCnt = intRowCnt + 1;
        
              });

            var ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);
            for (var i = 1; i <= intRowCnt; i++) {
                ws["A" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["B" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["C" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["D" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["E" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["F" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["G" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["H" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["I" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["J" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["K" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["L" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["M" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                ws["N" + i].z = '#,##0.00_);\\(#,##0.00\\)';
                //ws["O" + i].z = '#,##0.00_);\\(#,##0.00\\)';

                ws["A" + i].t = 'n';
                ws["B" + i].t = 'n';
                ws["C" + i].t = 'n';
                ws["D" + i].t = 'n';
                ws["E" + i].t = 'n';
                ws["F" + i].t = 'n';
                ws["G" + i].t = 'n';
                ws["H" + i].t = 'n';
                ws["I" + i].t = 'n';                
                ws["J" + i].t = 'n';                
                ws["K" + i].t = 'n';                
                ws["L" + i].t = 'n';                
                ws["M" + i].t = 'n';                
                ws["N" + i].t = 'n';                
                //ws["O" + i].t = 'n';                
            }


            const countheader = Object.keys(createXLSLFormatObj); // columns name header to

             var wscols = [];
              for (var i = 0; i < countheader.length; i++) {  // columns length added
                 wscols.push({ wch: Math.max(countheader[i].toString().length + 25) })
              }
              
            ws['!cols'] = wscols;
                       
            /* File Name */
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws,"SSS Bracket List Sheet");

            /* generate file and download */
            const wbout = XLSX.write(wb, { type: "array", bookType: "xlsx" });
            saveAs(new Blob([wbout], { type: "application/octet-stream" }), "All-SSS-Bracket-Report.xlsx");

            showHasSuccessMessage('Excel file has successfully created & downloaded at Download Folder');  
                          
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



