@extends('layout.adminweb')
@section('content')

<style>
    .bx {
    font-size: 42px;
}
    .badge.badge-dot {
    display: inline-block;
    margin: 0;
    padding: 0;
    width: 0.625rem;
    height: 0.625rem;
    border-radius: 50%;
    vertical-align: middle;
}
.avatar {
    position: relative;
    width: 3.375rem;
    height: 3.375rem;
    cursor: pointer;
}

.mx-auto {
    margin-right: auto !important;
    margin-left: auto !important;
}
.avatar .avatar-initial {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    background-color: #FF5B5C;
    font-weight: 600;
}
.rounded-circle {
    border-radius: 50% !important;
}
.bg-label-success {
    background-color: #dff9ec !important;
    color: #39da8a !important;
}
.mb-2 {
    margin-bottom: 1rem !important;
}

.card-header {
    padding: 10px 32px;
}

@media (min-width: 1200px){}
.mb-xl-0, .my-xl-0 {
    /*margin-bottom: auto !important;*/
  }
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
                                    <li class="breadcrumb-item active">Payroll Dashboard
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
                            <div class="card" style="box-shadow:none;">                            
                                <div class="card-content" style="background: #f2f4f3;">
                                    <div class="card-body card-dashboard">
                                        <div class="row">

<!-- All Users -->
  <div class="col-md-6 col-lg-6 col-xl-6" style="height: 215px;">
    <div class="card h-100">
      <div class="card-header">
        <h5 class="card-title mb-2" style='color: red;'>Total Employees</h5>
        <h1 class="display-6 fw-normal mb-0">{{number_format($TotalEmployee,0)}}</h1>
      </div>
      <div class="card-body" style="padding: 10px 1.7rem;">
        <span class="d-block mb-2">Current Total No. of Employees</span>
        <div class="progress progress-stacked" style="height:15px;">
          <div class="progress-bar bg-success" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
          <div class="progress-bar bg-danger" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>       
        </div>
        <!-- 
        <ul class="p-0 m-0">
          <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-success me-2"></span> ACCOUNTING
            </div>
            <div class="d-flex gap-3">
              <span>29.5k</span>
              <span class="fw-semibold">56%</span>
            </div>
          </li>
          <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> ADMINISTRATION OFFICE 
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
            <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> GENERAL SERVICES 
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
            <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> HUMAN RESOURCES
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
            <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> INFORMATION AND COMMUNICATIONS TECHNOLOGY
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
            <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> MATERIALS CONTROL
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
            <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> MEDICAL
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
            <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> SECURITY
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
            <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> WEIGHBRIDGE
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
            <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> PMC-Agusan 
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
            <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> PMC-Agusan 
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
            <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> PMC-Agusan 
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
            <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> PMC-Agusan 
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
            <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> PMC-Agusan 
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
           <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> PMC-Agusan 
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>
           <li class="d-flex justify-content-between">
            <div class="d-flex align-items-center lh-1 me-3">
              <span class="badge badge-dot bg-danger me-2"></span> PMC-Agusan 
            </div>
            <div class="d-flex gap-3">
              <span>25.7k</span>
              <span class="fw-semibold">26%</span>
            </div>
          </li>

       
        </ul> -->


      </div>
    </div>
  </div>

    <div class="col-lg-6 col-12">
        <div class="row">
          <!-- Statistics Cards -->
          <div class="col-6 col-md-4 col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-body text-center">
                <div class="avatar mx-auto mb-2">
                  <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-user-check"></i></span>
                </div>
                <span class="d-block text-nowrap">Total Active Employee</span>
                <h2 class="mb-0">{{number_format($TotalActiveEmployee,0)}}</h2>
              </div>
            </div>
          </div>
          <div class="col-6 col-md-4 col-lg-6 mb-4">
            <div class="card h-100">
              <div class="card-body text-center">
                <div class="avatar mx-auto mb-2">
                  <span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bx-user-x"></i></span>
                </div>
                <span class="d-block text-nowrap">Total Inactive</span>
                <h2 class="mb-0">{{number_format($TotalInActiveEmployee,0)}}</h2>
              </div>
            </div>
          </div>
          <!--/ Statistics Cards -->
    </div>

    <div class="row" style="display:none;">
      <!-- Statistics Cards -->
      <div class="col-6 col-md-4 col-lg-6 mb-4">
        <div class="card h-100">
          <div class="card-body text-center">
            <div class="avatar mx-auto mb-2">
              <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bxs-institution"></i></span>
            </div>
            <span class="d-block text-nowrap">Total Employee of PMC Agusan</span>
            <h2 class="mb-0">{{number_format($TotalPMCAgusan,0)}}</h2>
          </div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-6 mb-4">
        <div class="card h-100">
          <div class="card-body text-center">
            <div class="avatar mx-auto mb-2">
              <span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bxs-school"></i></span>
            </div>
            <span class="d-block text-nowrap">Total Employee of PMC Davao</span>
            <h2 class="mb-0">{{number_format($TotalPMCDavao,0)}}</h2>
          </div>
        </div>
      </div>
      <!--/ Statistics Cards -->
    </div>

     </div>

  </div>
  <!--/ All Users -->

<div class="row">        
  <!-- Pendng Loans-->

  <div class="col-md-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0" style='color: red;'>Pending Loan </h5> 
        <a href="{{URL::route('admin-employee-loan-transaction')}}" class="btn btn-sm btn-primary" type="button">View All Transaction</a>     
      </div>  
      <div class="table-responsive">
        <div id="style-2" class="table-responsive table_default_height">
        <table id="tblLoan-List" class="table zero-configuration complex-headers border">
            <thead>
                <tr>
                    <th></th>                                    
                    <th style="width:15% !important;">Emp. No.</th>
                    <th style="width:15% !important;">Full Name</th>
                    <th style="width:8% !important;">Loan Code</th>
                    <th style="width:20% !important;">Loan Description</th>
                    <th style="width:6% !important;">Loan Amt</th>                    
                    <th style="width:10% !important;">Status</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!--Pending Income Deduction-->
   <div class="col-md-6">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0" style='color: red;'>Pending Income & Deduction </h5>  
        <a href="{{URL::route('admin-employee-income-deduction-transaction')}}" class="btn btn-sm btn-primary" type="button">View All Transaction</a>     
      </div>  
      <div class="table-responsive">
        <div id="style-2" class="table-responsive table_default_height">
        <table id="tblIncomeDeduction-List" class="table zero-configuration complex-headers border">
            <thead>
                <tr>
                    <th></th>                    
                    <th style="width:15% !important;">Emp. No.</th>
                    <th style="width:15% !important;">Full Name</th>
                    <th style="width:8% !important;">Type</th>
                    <th style="width:8% !important;">Loan Code</th>
                    <th style="width:20% !important;">Loan Description</th>
                    <th style="width:6% !important;">Loan Amt</th>                    
                    <th style="width:10% !important;">Status</th>
               </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>  
  <!--/ Marketing Campaigns -->
</div>  

    </div>
  </div>
 </div>
 </div>
 </div>
 </section>   
</div>

<script type="text/javascript">

    var intCurrentPage = 1;
    var isPageFirstLoad = true;

    $(document).ready(function() {

         $('#tblLoan-List').DataTable( {
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
            "order": [[5, "asc" ]]
        });

        $('#tblIncomeDeduction-List').DataTable( {
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
            "order": [[5, "asc" ]]
        });

                
        //Load Initial Data
        $("#tblLoan-List").DataTable().clear().draw();
        getRecordLoanList(intCurrentPage, 'Pending');

        $("#tblIncomeDeduction-List").DataTable().clear().draw();
        getRecordIncomeDeductionList(intCurrentPage, 'Pending');

        isPageFirstLoad = false;
    });
   

    //LOAN
    function getRecordLoanList(vPageNo, vStatus){

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SearchText: vStatus,
                Status: vStatus,
                PageNo: vPageNo
            },
            url: "{{ route('get-employee-loan-transaction-list')}}",
            dataType: "json",
            success: function(data){
                LoadLoanRecordList(data.EmployeeLoanList);                
            },
            error: function(data){                
                console.log(data.responseText);
            },
            beforeSend:function(vData){
                
            }
        });

    };

    function LoadLoanRecordList(vList){

        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadLoanRecordRow(vList[x]);
            }
        }
    }

    function LoadLoanRecordRow(vData){

        var tblList = $("#tblLoan-List").DataTable();

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
            tdStatus += "<span style='color:green;display:flex;'> <i class='bx bx-check-circle' style='font-size: 20px;'></i> Approved </span>";
        }
        if(vData.Status == 'Pending'){
            tdStatus += "<span style='color:red;display:flex;'> <i class='bx bx-x-circle' style='font-size: 20px;'></i> Pending </span>";
        }
        if(vData.Status == 'Cancelled'){
            tdStatus += "<span style='color:#f68c1f;display:flex;'> <i class='bx bx-x-circle' style='font-size: 20px;'></i> Cancelled </span>";
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
                curData[5] = tdTotalLoanAmount;                
                curData[6] = tdStatus;
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
                    tdTotalLoanAmount,                    
                    tdStatus
                ]).draw();          
        }
    }

    //INCOME DEDUCTION
    function getRecordIncomeDeductionList(vPageNo, vStatus){

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SearchText: vStatus,
                Status: vStatus,
                PageNo: vPageNo
            },
            url: "{{ route('get-employee-income-deduction-transaction-list')}}",
            dataType: "json",
            success: function(data){
                LoadIncomeDeductionRecordList(data.EmployeeIncomeDeductionList);                
            },
            error: function(data){                
                console.log(data.responseText);
            },
            beforeSend:function(vData){                
            }
        });
    };

    function LoadIncomeDeductionRecordList(vList){

        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadIncomeDeductionRecordRow(vList[x]);
            }
        }
    }

    function LoadIncomeDeductionRecordRow(vData){

        var tblList = $("#tblIncomeDeduction-List").DataTable();

        tdID = vData.ID;
           
        tdCategory = "<span>" + vData.Category + "</span>";        
        tdEmpNo = "<span>" + vData.EmployeeNumber + "</span>";
        tdEmpName = "<span>" + vData.FullName + "</span>";

        tdIncomdeDeductionCode = "<span>" + vData.IncomeDeductionTypeCode + "</span>";
        tdIncomdeDeductionName = "<span>" + vData.IncomeDeductionTypeName + "</span>";
      
        tdTotalIncomeDeductionAmount = "<span>" + FormatDecimal(vData.TotalIncomeDeductionAmount,2) + "</span>";
      
         tdStatus = "";
        if(vData.Status == 'Approved'){
            tdStatus += "<span style='color:green;display:flex;'> <i class='bx bx-check-circle' style='font-size: 20px;'></i> Approved </span>";
        }
        if(vData.Status == 'Pending'){
            tdStatus += "<span style='color:red;display:flex;'> <i class='bx bx-x-circle' style='font-size: 20px;'></i> Pending </span>";
        }
        if(vData.Status == 'Cancelled'){
            tdStatus += "<span style='color:#f68c1f;display:flex;'> <i class='bx bx-x-circle' style='font-size: 20px;'></i> Cancelled </span>";
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
                curData[3] = tdCategory; 
                curData[4] = tdIncomdeDeductionCode; 
                curData[5] = tdIncomdeDeductionName; 
                curData[6] = tdTotalIncomeDeductionAmount;                              
                curData[7] = tdStatus;
                tblList.row(rowIdx).data(curData).invalidate().draw();
            }
        });

        if(!IsRecordExist){

            //New Row
            tblList.row.add([
                    tdID,
                    tdEmpNo,
                    tdEmpName,
                    tdCategory, 
                    tdIncomdeDeductionCode,
                    tdIncomdeDeductionName,                                   
                    tdTotalIncomeDeductionAmount,
                    tdStatus
                ]).draw();          
        }
    }
</script>

@endsection



