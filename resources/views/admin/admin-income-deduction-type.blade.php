@extends('layout.adminweb')
@section('content')

<script src="{{ URL::to('public/admin/excel/xlsx.full.min.js') }}"></script>
<script src="{{ URL::to('public/admin/excel/FileSaver.js') }}"></script>


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
                                    <li class="breadcrumb-item active">Income & Deduction Type List
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
                                    <h4 class="card-title">Income & Deduction Type</h4>
                                </div>

                                <div class="card-content">
                                    
                                    <div class="card-body card-dashboard">

                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <fieldset>
                                                    <div class="input-group">

                                                           <select id="selSearchStatus" class="form-control" style="width:15%">
                                                          <option value="">All Record</option>
                                                          <option disabled="disabled">[ Type Option ]</option>
                                                          <option value="Non-Taxable Income">Type: Non-Taxable Income</option>
                                                          <option value="Taxable Income">Type: Taxable Income</option>
                                                          <option disabled="disabled">[ Category Option ]</option>
                                                          <option value="Earning">Category: Earning</option>
                                                          <option value="Deduction">Category: Deduction</option>
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
                                                        <th>CODE</th>
                                                        <th>TAXABLE TYPE</th>
                                                        <th>CATEGORY</th>
                                                        <th>INCOME & DEDUCTION NAME</th>
                                                        <th>DESCRIPTION</th>
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
                    <h5 class="modal-title white-color">Income & Deduction Type Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="EarningDeductionTypeID" value="0" readonly>
                    <div class="row">
                        <div class="col-md-7">
                            <fieldset class="form-group">
                                <label for="Status">Type: <span class="required_field">*</span></label>
                                <div class="form-group">
                                    <select id="EarningDeductionType" class="form-control">
                                        <option value="">Please Select</option>
                                        <option value="Non-Taxable Income">Non-Taxable Income</option>
                                        <option value="Taxable Income">Taxable Income</option>
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-5">
                            <fieldset class="form-group">
                                <label for="EarningDeductionCode">Code: <span class="required_field">*</span></label>
                                <input id="EarningDeductionCode" type="text" class="form-control" placeholder="Income/Deduction Code" autocomplete="off" maxlength="10">
                            </fieldset>
                        </div>
                    </div>

                     <div class="row">
                         <div class="col-md-7">
                            <fieldset class="form-group">
                                <label for="EarningDeductionName">Name: <span class="required_field">*</span></label>
                                <input id="EarningDeductionName" type="text" class="form-control" placeholder="Income/Deduction Name" autocomplete="off">
                            </fieldset>
                        </div>

                            <div class="col-md-5">
                            <fieldset class="form-group">
                                <label for="Status">Category : <span class="required_field">*</span></label>
                                <div class="form-group">
                                    <select id="EarningDeductionCategory" class="form-control">
                                        <option value="">Please Select</option>
                                        <option value="DEDUCTION">DEDUCTION</option>
                                        <option value="EARNING">EARNING</option>
                                    </select>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                      <div class="row">
                         <div class="col-md-12">
                            <fieldset class="form-group">
                                <label for="EarningDeductionDescription">Description: <span class="required_field">*</span></label>
                                <input id="EarningDeductionDescription" type="text" class="form-control" placeholder="Income/Deduction Description" autocomplete="off">
                            </fieldset>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
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

                </div>
                <div class="modal-footer">
                    <button id="btnSaveRecord" type="button" class="btn btn-primary ml-1" onclick="SaveRecord()">                        
                        <i class='bx bx-save mr-1' style="font-size: 21px;"></i> Save 
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
            "order": [[ 2, "asc" ]]
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
            url: "{{ route('get-earning-deduction-type-list') }}",
            dataType: "json",
            success: function(data){
                 total_rec=data.TotalRecord;
                LoadRecordList(data.IncomeDeductionTypeList);
                if(total_rec>0){
                     CreateIncomeDeductionTypePaging(total_rec,vLimit);  
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

    function CreateIncomeDeductionTypePaging(vTotalRecord,vLimit){

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

        tdEarningDeductionCode = "<span>" + vData.Code + "</span>";
        
        tdEarningDeductionType = "";
        if(vData.Type == 'Taxable Income'){
            tdEarningDeductionType += "<span style='color:green;display:flex;'>Taxable Income</span>";
        }else{
            tdEarningDeductionType += "<span style='color:red;display:flex;'>Non-Taxable Income</span>";
        }
       
        tdEarningDeductionCategory = "";
        if(vData.Category == 'EARNING'){
            tdEarningDeductionCategory += "<span style='color:green;display:flex;'>EARNING</span>";
        }else{
            tdEarningDeductionCategory += "<span style='color:red;display:flex;'>DEDUCTION</span>";
        }

        tdEarningDeductionName = "<span>" + vData.Name + "</span>";
        tdEarningDeductionDescription = "<span>" + vData.Description + "</span>";

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
                curData[2] = tdEarningDeductionCode;
                curData[3] = tdEarningDeductionType;
                curData[4] = tdEarningDeductionCategory;
                curData[5] = tdEarningDeductionName;
                curData[6] = tdEarningDeductionDescription;
                curData[7] = tdStatus;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,
                    tdAction,
                    tdEarningDeductionCode,
                    tdEarningDeductionType,
                    tdEarningDeductionCategory,
                    tdEarningDeductionName,
                    tdEarningDeductionDescription,
                    tdStatus
                ]).draw();          
        }
    }

    function Clearfields(){

        EnabledDisbledText(false);

        $("#EarningDeductionTypeID").val('0');

        $("#EarningDeductionCode").val('');
        $("#EarningDeductionType").val('').change();
        $("#EarningDeductionCategory").val('').change();
        
        $("#Status").val('').change();

        $("#EarningDeductionName").val('');
        $("#EarningDeductionDescription").val('');

        $("#btnSaveRecord").show();
        $("#btnCancelRecord").text('Cancel');

        resetTextBorderToNormal();

    }

   function EnabledDisbledText(vEnabled){


    $("#EarningDeductionCode").attr('disabled', vEnabled); 
    $("#EarningDeductionType").attr('disabled', vEnabled); 
    $("#EarningDeductionCategory").attr('disabled', vEnabled); 
    
    $("#Status").attr('disabled', vEnabled); 

    $("#EarningDeductionName").attr('disabled', vEnabled); 
    $("#EarningDeductionDescription").attr('disabled', vEnabled); 

   
    $("#EarningDeductionCategory").attr('disabled', vEnabled); 
    $("#EarningDeductionType").attr('disabled', vEnabled); 
    $("#Status").attr('disabled', vEnabled); 
    
   }

   function resetTextBorderToNormal(){

    $("#EarningDeductionCode").css({"border":"#ccc 1px solid"});
    $("#EarningDeductionName").css({"border":"#ccc 1px solid"});   
    $("#EarningDeductionType").css({"border":"#ccc 1px solid"}); 
    $("#EarningDeductionCategory").css({"border":"#ccc 1px solid"}); 
    $("#EarningDeductionDescription").css({"border":"#ccc 1px solid"}); 
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
                    IncomeDeductionID: vRecordID
                },
                url: "{{ route('get-earning-deduction-type-info') }}",
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.IncomeDeductionTypeInfo != undefined){

                        Clearfields();

                        $("#EarningDeductionTypeID").val(data.IncomeDeductionTypeInfo.ID);
                        $("#EarningDeductionCode").val(data.IncomeDeductionTypeInfo.Code);
                        $("#EarningDeductionName").val(data.IncomeDeductionTypeInfo.Name);
                        $("#EarningDeductionDescription").val(data.IncomeDeductionTypeInfo.Description);
                        $("#EarningDeductionType").val(data.IncomeDeductionTypeInfo.Type).change();
                        $("#EarningDeductionCategory").val(data.IncomeDeductionTypeInfo.Category).change();
                        $("#Status").val(data.IncomeDeductionTypeInfo.Status).change();

                        $("#EarningDeductionCode").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#EarningDeductionName").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#EarningDeductionDescription").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#EarningDeductionType").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#EarningDeductionCategory").attr("style", "border: 1px solid rgb(204, 204, 204) !important");
                        $("#Status").attr("style", "border: 1px solid rgb(204, 204, 204) !important");

                        buttonOneClick("btnSaveRecord", "<i class='bx bx-save mr-1' style='font-size: 21px;'></i> Save", false);

                        if(vAllowEdit){
                            $("#btnSaveRecord").show();
                            $("#btnCancelRecord").text('Close');
                            EnabledDisbledText(false);
                        }else{
                             $("#btnSaveRecord").hide();
                             $("#btnCancelRecord").text('Close');
                             EnabledDisbledText(true);
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

        var vEarningDeductionTypeID = $("#EarningDeductionTypeID").val();
        var vEarningDeductionType = $("#EarningDeductionType").val();
        var vEarningDeductionCategory = $("#EarningDeductionCategory").val();
        var vEarningDeductionCode = $("#EarningDeductionCode").val();
        var vEarningDeductionName = $("#EarningDeductionName").val();
        var vEarningDeductionDescription = $("#EarningDeductionDescription").val();
      
        var vType = $("#EarningDeductionType").val();
        var vStatus = $("#Status").val();

        resetTextBorderToNormal();

        if(vEarningDeductionType.trim()=="") {
           showHasErrorMessage('EarningDeductionType','Select earning & deduction type from the list.');
           return;
       }

        if(vEarningDeductionCode=="") {
         showHasErrorMessage('EarningDeductionCode','Enter earning/deduction type code.');
         return;  
       }

        if(vEarningDeductionCategory.trim()=="") {
           showHasErrorMessage('EarningDeductionCategory','Select earning & deduction category from the list.');
           return;
       }

        if(vEarningDeductionName=="") {
         showHasErrorMessage('EarningDeductionName','Enter earning/deduction type name.');
         return;  
       }

        if(vEarningDeductionDescription=="") {
         showHasErrorMessage('EarningDeductionDescription','Enter earning/deduction type description.');
         return;  
       }

        if(vStatus=="") {
         showHasErrorMessage('Status','Select status from the list.');
         return;  
       }


       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                EarningDeductionTypeID: $("#EarningDeductionTypeID").val(),
                EarningDeductionType: $("#EarningDeductionType").val(),
                EarningDeductionCategory: $("#EarningDeductionCategory").val(),
                EarningDeductionCode: $("#EarningDeductionCode").val(),
                EarningDeductionName: $("#EarningDeductionName").val(),
                EarningDeductionDescription: $("#EarningDeductionDescription").val(),
                Status: $("#Status").val()
            },
            url: "{{ route('do-save-earning-deduction-type') }}",
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

                if(data.Response =='Success'){
                    $("#record-modal").modal('hide');
                    showHasSuccessMessage(data.ResponseMessage);
                    LoadRecordRow(data.IncomeDeductionTypeInfo);
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
          url: "{{ route('get-excel-income-deduction-type-list') }}",
          dataType: "json",
          success: function(data){

              if(data.Response=="Success"){
                   resultquery=data.AllIncomeDeductionTypeList;
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
                            " INCOME & DEDUCTION TAXABLE TYPE ",
                            " INCOME & DEDUCTION CATEGORY ",
                            " INCOME & DEDUCTION NAME ", 
                            " INCOME & DEDUCTION DESCRIPTION ",
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
                 wscols.push({ wch: Math.max(countheader[i].toString().length + 30) })
              }
              
            ws['!cols'] = wscols;
                       
            /* File Name */
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws,"Income & Deduction List Sheet");

            /* generate file and download */
            const wbout = XLSX.write(wb, { type: "array", bookType: "xlsx" });
            saveAs(new Blob([wbout], { type: "application/octet-stream" }), "All-Income-Deduction-Report.xlsx");

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



