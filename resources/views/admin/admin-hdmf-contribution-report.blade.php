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
                                    <li class="breadcrumb-item active">HDMF Contribution Report
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
                                    <h4 class="card-title">HDMF Contribution Report </h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">

                                          <div class="row">
                                            <div class="col-md-12">
                                                <fieldset>
                                                    <div class="input-group">

                                                        <div class="col-md-2">
                                                                        <fieldset class="form-group">
                                                                    <label for="Month">Month:</label>
                                                                    <div class="form-group">
                                                                        <select id="Month" class="form-control">
                                                                           @php($CurMonth = date("m"))
                                                                           <option value="1" {{ ($CurMonth == 1 ? "selected" : "") }}>January</option>
                                                                           <option value="2" {{ ($CurMonth == 2 ? "selected" : "") }}>February</option>
                                                                           <option value="3" {{ ($CurMonth == 3 ? "selected" : "") }}>March</option>
                                                                           <option value="4" {{ ($CurMonth == 4 ? "selected" : "") }}>April</option>
                                                                           <option value="5" {{ ($CurMonth == 5 ? "selected" : "") }}>May</option>
                                                                           <option value="6" {{ ($CurMonth == 6 ? "selected" : "") }}>June</option>
                                                                           <option value="7" {{ ($CurMonth == 7 ? "selected" : "") }}>July</option>
                                                                           <option value="8" {{ ($CurMonth == 8 ? "selected" : "") }}>August</option>
                                                                           <option value="9" {{ ($CurMonth == 9 ? "selected" : "") }}>September</option>
                                                                           <option value="10" {{ ($CurMonth == 10 ? "selected" : "") }}>October</option>
                                                                           <option value="11" {{ ($CurMonth == 11 ? "selected" : "") }}>November</option>
                                                                           <option value="12" {{ ($CurMonth == 12 ? "selected" : "") }}>December</option>
                                                                        </select>
                                                                    </div>
                                                                </fieldset>       
                                                      </div>         
                           
                                                   <div class="col-md-2">
                                                         <fieldset class="form-group">
                                                            <label for="Year">Year:</label>
                                                            <div class="form-group">
                                                                <select id="Year" class="form-control">
                                                                    @php($CurYear = date("Y"))
                                                                    @for($x = $CurYear; $x >= 2023; $x--)
                                                                        <option value="{{ $x }}" {{ ($x == $CurYear ? "selected" : "") }}>{{ $x }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-md-2">
                                                         <fieldset class="form-group">
                                                            <label for="Filter">Filter By:</label>
                                                            <div class="form-group">
                                                               <select id="Filter" class="form-control">
                                                                          <option value="">All Record</option>
                                                                          <option disabled="disabled">[ By Location ]</option>
                                                                          @foreach($BranchList as $brnrow)
                                                                          <option value="Location|{{ $brnrow->ID }}">Location : {{ $brnrow->BranchName }}</option>
                                                                          @endforeach
                                                                          <option disabled="disabled">[ By Site ]</option>
                                                                          @foreach($BranchSite as $siterow)
                                                                          <option value="Site|{{ $siterow->ID }}">Site : {{ $siterow->SiteName }}</option>
                                                                          @endforeach
                                                                      </select>
                                                            </div>
                                                        </fieldset>
                                                    </div>
                                                
                                                    <div class="col-md-2">
                                                         <fieldset class="form-group">
                                                            <label for="Status">Status:</label>
                                                            <div class="form-group">
                                                                  <select id="Status" class="form-control">
                                                                      <option value="Approved">Posted</option>
                                                                      <option value="Pending">Un-Posted</option>                                                                    
                                                                  </select>
                                                            </div>
                                                        </fieldset>
                                                    </div>

                                                        <div class="col-md-2" style="margin-left: -11px;">
                                                               
                                                            <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary" tooltip="Search Here" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;margin-top: 23px;margin-left:-2px;">
                                                                 <i class="bx bx-search"></i>
                                                            </button>

                                                             @if(Session::get('IS_SUPER_ADMIN') || $Allow_View_Print_Export==1)     
                                                              <button type="button" class="btn btn-icon btn-outline-primary" onclick="Print()" tooltip="Print Report" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;margin-top: 23px;margin-left:-2px;">
                                                                    <i class="bx bx-printer"></i> 
                                                                </button>
                                                                <button type="button" class="btn btn-icon btn-outline-primary" onclick="GenerateExcel()" tooltip="Excel Report" tooltip-position="top" style="padding: 0.4rem 0.6rem;height: 40px;margin-top: 23px;margin-left:-2px;">
                                                                   <i class="bx bx-file"></i> 
                                                                </button>
                                                              @endif     
                                                        </div>

                                                    </div>
                                                </fieldset>
                                            </div>

                        <!--                    <div class="col-md-12 ">
                                                <fieldset>
                                                    <div class="input-group">
                                     
                                                      <div class="col-md-4">
                                                            <fieldset class="form-group">
                                                                <label>Search:</label>
                                                                <input type="text" class="form-control searchtext" placeholder="Search Here..">
                                                            </fieldset>
                                                        </div>                                 
                                                    </div>
                                                </fieldset>
                                            </div> -->

                                        </div>



                                         <div id="style-2" class="table-responsive col-md-12 table_default_height">
                                            <table id="tblList" class="table zero-configuration complex-headers border alt-background">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th style="height: 25px;">EMPLOYEE ID</th>
                                                        <th>EMPLOYEE LAST NAME</th>
                                                        <th>EMPLOYEE FIRST Name</th>
                                                        <th>EMPLOYEE MIDDLE NAME</th>
                                                        <th>PAGIBIG NO</th>
                                                        <th>EMPLOYEE SHARE</th>
                                                        <th>EMPLOYEE MP2</th>
                                                        <th>EMPLOYER SHARE</th>
                                                        <th>TOTAL AMOUNT</th>
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
        //getRecordList(intCurrentPage);
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
        getRecordList(1);
    });


    $("#btnSearch").click(function(){
        $("#tblList").DataTable().clear().draw();
        getRecordList(1);
    });

    $('.searchtext').on('keypress', function (e) {
        if(e.which === 13){
            $("#tblList").DataTable().clear().draw();
            getRecordList(1);
        }
    });

    function getRecordList(vPageNo){

        intCurrentPage = vPageNo;

      $("#tblList").DataTable().clear().draw();
      $(".paginate_button").remove(); 
      vLimit=100;

        vMonth= $("#Month").val();
        vYear=$("#Year").val();
        vStatus= $("#Status").val();
        vFilter= $("#Filter").val();

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                Year: vYear,
                Month: vMonth,
                Status: vStatus,
                Filter: vFilter,
                SearchText: $('.searchtext').val(),
                Limit: vLimit,
                PageNo: vPageNo
            },
            url: "{{ route('admin-get-hdmf-employee-contribution-list') }}",
            dataType: "json",
            success: function(data){
                if(data.Response=='Success'){
                    total_rec=data.TotalRecord;  
                  
                    if(total_rec>0){
                         CreateHDMFReportPaging(total_rec,vLimit);  
                         if(total_rec>vLimit){
                            $("#divPaging").show(); 
                            $("#total-record").text(total_rec);
                            $("#paging_button_id"+vPageNo).css("background", "#0069d9");
                            $("#paging_button_id"+vPageNo).css("color", "#fff");
                         }
                           LoadRecordList(data.HDMFEmployeeContributionList);
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

     function CreateHDMFReportPaging(vTotalRecord,vLimit){

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
         var vStatus= $("#Status").val();

        tdEmployeeID = "<span>" + vData.EmployeeID + "</span>";
        tdEmployeeNo = "<span>" + vData.EmployeeNo + "</span>";
        tdLastName = "<span>" + vData.LastName + "</span>";
        tdFirstName = "<span>" + vData.FirstName + "</span>";
        tdMiddleName = "<span>" + vData.MiddleName + "</span>";

        tdPAGIBIGNo = "<span class='font-normal float_right'>" + vData.PAGIBIGNo + "</span>";
        tdEmployeeShare = "<span class='font-normal float_right'>" + FormatDecimal(vData.EmployeeShare,2) + "</span>";
        tdEmployeeMP2 = "<span class='font-normal float_right'>" + FormatDecimal(vData.EmployeeMP2,2) + "</span>";
        tdEmployerShare = "<span class='font-normal float_right'>" + FormatDecimal(vData.EmployerShare,2) + "</span>";

         tdTotal = "<span>" + FormatDecimal(vData.Total,2) + "</span>";

        if(vStatus=='Pending'){
            tdStatus = "<span style='color:red;display:flex;'> Un-Posted </span>"; 
         }else{
           tdStatus = "<span style='color:green;display:flex;'> <i class='bx bx-check-circle'></i> Posted </span>";
         }
       
        //Check if record already listed
        var IsRecordExist = false;
        tblList.rows().every(function ( rowIdx, tableLoop, rowLoop ) {
            var rowData = this.data();
            if(rowData[0] == vData.ID){

                IsRecordExist = true;
                //Edit Row
                curData = tblList.row(rowIdx).data();
                curData[0] = tdEmployeeID;
                curData[1] = tdEmployeeNo;
                curData[2] = tdLastName;
                curData[3] = tdFirstName;
                curData[4] = tdMiddleName;
                curData[5] = tdPAGIBIGNo;
                curData[6] = tdEmployeeShare;
                curData[7] = tdEmployeeMP2;
                curData[8] = tdEmployerShare;
                curData[9] = tdTotal;
                curData[10] = tdStatus;
  
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdEmployeeID,
                    tdEmployeeNo,
                    tdLastName,
                    tdFirstName,
                    tdMiddleName,
                    tdPAGIBIGNo,
                    tdEmployeeShare,
                    tdEmployeeMP2,
                    tdEmployerShare,
                    tdTotal,
                    tdStatus
                ]).draw();          
        }
    }


   function Print(){

      vYear= $("#Year").val();
      vMonth= $("#Month").val();
      vStatus= $("#Status").val();
      vFilter= $("#Filter").val();

      vSearchText='';
      // vSearchText= $('.searchtext').val();

      if(total_rec>0){
         window.open('{{config('app.url')}}admin-hdmf-contribution-print-report?Year=' +vYear +'&Month=' +vMonth +'&Status=' +vStatus+'&Filter=' +vFilter+'&SearchText=' +vSearchText+'&PageNo=' +intCurrentPage+'&Limit=' +vLimit, '_blank');
         
         
      }else{
         showHasErrorMessage('','No record(s) found base on search criteria.');
      }
    }


   function GenerateExcel(){

      if(total_rec<=0){
        showHasErrorMessage('','No record(s) found base on search criteria.');
         return;   
      }
      
      showHasSuccessMessage('Please wait while generating HDMF Contribution list excel file.');    

      vYear= $("#Year").val();
      vMonth= $("#Month").val();
      vStatus= $("#Status").val();
      vFilter= $("#Filter").val();
      vSearchText= $('.searchtext').val();
      vLimit=100;

        $.ajax({
              type: "post",
              data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",          
                Year: vYear,
                Month: vMonth,
                Status: vStatus,
                Filter: vFilter,
                SearchText: '',
                Limit: vLimit,
                PageNo: intCurrentPage
              },
              url: "{{ route('get-excel-hdmf-contribution-list') }}",
              dataType: "json",
              success: function(data){

                  if(data.Response=="Success"){
                       resultquery=data.HDMFContributionExcelList;
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
                            "HDMF Contribution Report",
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
                    "{{ strtoupper('Last Name') }}",
                    "{{ strtoupper('First Name') }}",
                    "{{ strtoupper('Middle Name') }}",
                    "{{ strtoupper('HDMF No.') }}",
                    "{{ strtoupper('Employee Share') }}",
                    "{{ strtoupper('Employee MP2') }}",
                    "{{ strtoupper('Employer Share') }}",
                    "{{ strtoupper('Total Amount') }}",
                    "{{ strtoupper('Status') }}"];

            xlsRows=resultquery;
          
            createXLSLFormatObj.push(xlsHeader);

            var intRowCnt = 5;
            var dblEmployeeShare = 0;
            var dblEmployeeMP2 = 0;
            var dblEmployerShare = 0;
            var dblTotal = 0;

              $.each(xlsRows, function(index, value) {
                  var innerRowData = [];   
                  $.each(value, function(ind, val) {
                                      
                    if(ind == "EmployeeNo" ||
                        ind == "LastName" ||
                        ind == "FirstName" ||
                        ind == "MiddleName" ||
                        ind == "PAGIBIGNo" ||
                        ind == "EmployeeShare" ||
                        ind == "EmployeeMP2" ||
                        ind == "EmployerShare" ||
                        ind == "Total" ||
                        ind == "Status"){

                        if(ind == "EmployeeShare" ||
                            ind == "EmployeeMP2" ||
                            ind == "EmployerShare" ||
                            ind == "Total"
                            ){

                            if(ind == "EmployeeShare"){
                                dblEmployeeShare = parseFloat(dblEmployeeShare) + parseFloat(val);
                            }else if(ind == "EmployeeMP2"){
                                dblEmployeeMP2 = parseFloat(dblEmployeeMP2) + parseFloat(val);
                            }else if(ind == "EmployerShare"){
                                dblEmployerShare = parseFloat(dblEmployerShare) + parseFloat(val);
                            }else if(ind == "Total"){
                                dblTotal = parseFloat(dblTotal) + parseFloat(val);
                            }

                            innerRowData.push(val);
                        }else{
                            innerRowData.push(val);
                        }
                    }

                  });

                  createXLSLFormatObj.push(innerRowData);
                  intRowCnt = intRowCnt + 1;

              });

            //Total
            var innerRowData = [];   
            innerRowData.push("Total");
            innerRowData.push("");
            innerRowData.push("");
            innerRowData.push("");
            innerRowData.push("");
            innerRowData.push(dblEmployeeShare);
            innerRowData.push(dblEmployeeMP2);
            innerRowData.push(dblEmployerShare);
            innerRowData.push(dblTotal);
            createXLSLFormatObj.push(innerRowData);
            intRowCnt = intRowCnt + 1;
            
            var ws = XLSX.utils.aoa_to_sheet(createXLSLFormatObj);
            for (var i = 6; i < intRowCnt; i++) {
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
                XLSX.utils.book_append_sheet(wb, ws,"Posted HDMF Contrib. Sheet");  
            }else{
                XLSX.utils.book_append_sheet(wb, ws,"Un-Posted HDMF Contrib. Sheet");    
            }
            
            /* generate file and download */
            const wbout = XLSX.write(wb, { type: "array", bookType: "xlsx" });
            saveAs(new Blob([wbout], { type: "application/octet-stream" }), "HDMF-Contribution-Report.xlsx");

            showHasSuccessMessage('Excel file has successfully created & downloaded at Download Folder');     
                          
     }

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



