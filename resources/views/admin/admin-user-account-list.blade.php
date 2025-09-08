@extends('layout.adminweb')
@section('content')

<style type="text/css">
    .checkbox label:after{
            border: 1px solid #f68c1f;
    }
    .menu-livicon{
      width:  21px !important;
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
                                    <li class="breadcrumb-item active">User Account List
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
                                    <h4 class="card-title">User Account List</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body card-dashboard">
                                        <div class="row">
                                            <div class="col-md-12 mb-1">
                                                <fieldset>
                                                    <div class="input-group">

                                                      <select id="selSearchStatus" class="form-control" style="width:15%">
                                                          <option value="">All Record</option>
                                                          <option disabled="disabled">[ Location Option ]</option>
                                                          <option value="PMC Davao">Location: PMC Davao</option>
                                                          <option value="PMC Agusan">Location: PMC Agusan</option>
                                                          <option disabled="disabled">[ Admin Option ]</option>
                                                          <option value="Super">Admin: Super</option>
                                                          <option value="Approved">Admin: User</option>
                                                     
                                                        </select>
                                                  
                                                        <input type="text" class="form-control searchtext" placeholder="Search Here.." style="width: 39%;margin-left: 6px;">
                                                        <button id="btnSearch" type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1 disabled_border">
                                                            <i class="bx bx-search"></i>
                                                        </button>

                                                         @if(Session::get('IS_SUPER_ADMIN') || $Allow_Add_Create_Import_Upload==1)
                                                          <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="NewRecord()">
                                                            <i class="bx bx-plus"></i> New
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
                                                         <th></th>
                                                        <th>Employee No</th>
                                                        <th>Employee Name</th>
                                                         <th>User Name</th>
                                                        <th>Admin Type</th>
                                                        <th>Status</th>
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

    <!-- Password Reset MODAL -->
    <div id="password-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="top:-3px;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                     <h5 class="modal-title white-color">Request for Change Password</h5>
                </div>
                <div class="modal-body">

                    <input type='hidden' id="AdminEmployeeUserID" value="0" readonly>

                      <div class="row">
                         <div class="col-md-10">
                        <fieldset class="form-group">
                            <label for="PayrollPeriodName">New Password</label>                                       
                             <input id="NewPassword" type="password" class="form-control text-readonly-color password-control" placeholder="New Password" >                             
                          </fieldset>
                        </div>
                      </div>   
                      
                    <div class="row">
                        <div class="col-md-10">
                           <fieldset class="form-group">
                            <label for="PayrollPeriodName">Confirm New Password</label>                                     
                             <input id="ConfirmNewPassword" type="password" class="form-control text-readonly-color password-control" placeholder="Confirm New Password" >                             
                          </fieldset>
                        </div>
                   </div>

                   <div class="checkbox checkbox-sm">
                        <input id="chkRequestShowPassword" type="checkbox" onclick="showPassword()" class="form-check-input">
                        <label class="checkboxsmall" for="chkRequestShowPassword">
                          <small>Show Password</small>
                        </label>
                  </div>

                   <div class="row">
                        <p style="color:#a94442;font-size:11px;line-height: 15px;">
                           Note: Password must be atleast 6 characters or more<br>
                           Consist either alpha or numeric password characters.
                         </p>
                   </div>

           
                </div>
                <div class="modal-footer">

                     <button id="btnApproveDTR" type="button" class="btn btn-primary ml-1" onclick="doChangePassword()">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Change Password</span>
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

    <!-- User Account MODAL -->
    <div id="record-modal" class="modal fade text-left w-100" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content" style="width:1280px !important;">
                <div class="modal-header">
                    <h5 class="modal-title white-color">User Account Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="bx bx-x"></i>
                    </button>
                </div>   

             <div class="modal-body" >

                <form id="frmUserAccess" class="form-horizontal " action="{{ route('do-save-user-account') }}" method="post">
                     {{csrf_field()}}  
                   <input type='hidden' id="UserAccountID" name='UserAccountID' value="0" readonly>

                     <div class="col-md-3" style="float:left;">

                       <fieldset>
                          <!-- <legend>| Personnel Information |</legend> -->
                           <div class="row">
                                <div class="col-md-12">
                                  <fieldset class="form-group">
                                    <label for="EmployeeName">Employee : <span class="required_field">* </span></label><span class="search-txt">(Type & search from the list)</span>
                                     <div class="div-percent">
                                           <input id="PersonnelID" name="PersonnelID" type="hidden" value="0">
                                           <input id="SearchEmployeeName" type="text" class="form-control custom-select autocomplete_txt ui-autocomplete-input" autocomplete="off" data-complete-type="employee" placeholder="Employee Name"><span class='percent-sign' onclick="ClearEmployee()"> <i class="bx bx-trash" style="line-height: 21px;cursor:pointer;"></i> </span>
                                        </div> 
                                  </fieldset>
                            </div>
                         </div>

                            <div>                              
                                 <input id="BranchID" name="BranchID" type="hidden" value="0">
                                 <label class="col-md-12">Branch: <span class="required_field">*</span></label>
                                  <div class="col-md-12">
                                      <input type="text" id="BranchName" name="EmployeeNo" class="form-control text-readonly-color" placeholder="Branch Location" autocomplete="off" style="width:100%;" readonly>
                                  </div>
                          </div>

                        <div>
                          <label class="col-md-12">Employee No: <span class="required_field">*</span></label>
                          <div class="col-md-12">
                              <input type="text" id="EmployeeNo" name="EmployeeNo" class="form-control text-readonly-color" placeholder="Employee No" autocomplete="off" style="width:100%;" readonly>
                          </div>
                      </div> 

                        <div>
                          <label class="col-md-12">Employee Name: <span class="required_field">*</span></label>
                          <div class="col-md-12">
                              <input type="text" id="EmployeeName" name="EmployeeName" class="form-control text-readonly-color" placeholder="Employee Name" autocomplete="off" style="width:100%;" readonly>
                          </div>
                      </div>  
          
                        <div>
                          <label class="col-md-12">Contact No <span class="required_field">*</span></label>
                          <div class="col-md-12">
                              <input type="text" id="contact-no" name="ContactNo" class="form-control text-readonly-color" placeholder="Contact No"  autocomplete="off" style="width:100%;" readonly>
                          </div>
                      </div>
        
                      <div>
                          <label class="col-md-12">Email Address <span class="required_field">*</span></label>
                          <div class="col-md-12">
                              <input type="email" id="email-address" name="Email" class="form-control text-readonly-color" placeholder="E-mail Address" autocomplete="off" style="width:100%;" readonly>
                          </div>
                      </div>
                    </fieldset>
                    
                     <fieldset>
                          <div>
                              <label class="col-md-12">Username <span class="required_field">*</span></label>
                              <div class="col-md-12">
                                  <input type="text" id="user-name" name="Username" class="form-control" placeholder="Username" autocomplete="off" style="width:100%;" required="">
                              </div>
                          </div>
            
                          <div>
                              <label class="col-md-12">Password <span class="required_field">* <span style="font-size: 10px;">(Leave blank if password has no changes)</span></span> </label> 
                              <div class="col-md-12">
                                  <input type="password" id="user-password" name="UserPassword" class="form-control password-control" placeholder="Password" autocomplete="off" style="width:100%;">
                              </div>
                          </div>
                 
                            <div>
                                <label for="IsSuperAdmin">Admin Type <span class="required_field">*</span></label>
                                <select id="IsSuperAdmin" name="IsSuperAdmin" class="form-control select2">
                                    <option value="1">Super Admin</option>
                                    <option value="0">User Admin</option>
                                </select>
                          </div>

                         <div>
                                <label for="Status">Status <span class="required_field">*</span></label>
                                <select id="Status" name="Status" class="form-control select2">
                                    <option value="{{ config('app.STATUS_ACTIVE') }}">{{ config('app.STATUS_ACTIVE') }}</option>
                                    <option value="{{ config('app.STATUS_INACTIVE') }}">{{ config('app.STATUS_INACTIVE') }}</option>
                                </select>
                          </div>

                            <div class="checkbox checkbox-sm">
                                    <input id="chkShowPassword" type="checkbox" onclick="showPassword()" class="form-check-input">
                                    <label class="checkboxsmall" for="chkShowPassword">
                                      <small>Show Password</small>
                                    </label>
                              </div>
         
                      </fieldset>
                      <br>
                    </div>
                    
                    
                    <div id="style-2" class="col-md-9" style="padding: 5px;float:right;">
                        <!-- BEGIN: Main Menu-->
                        <div id="style-2"  class="main-menu menu-light menu-accordion menu-shadow" style="height:650px;width: 930px;overflow: auto;">
                           
                            <div class="main-menu-content" style="height: initial;">
                                <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines">
                                    
                                    <div class="table-responsive col-md-12 table_default_height">
                                            <div id="tblList_wrapper" class="dataTables_wrapper no-footer">
                                                <table id="tblMenuList" class="table zero-configuration complex-headers border dataTable no-footer" role="grid">
                                               <!--  <thead>
                                                    <tr role="row">
                                                        
                                                        <th style="color:#fff;text-align: center;width: 15%;"> Admin Menu List </th>
                                                        <th style="color:#fff;text-align: center;width: 5%;"> View/Print/Export </th>
                                                        <th style="color:#fff;text-align: center;width: 5%;"> Create/Add/Import/Upload </th>
                                                        <th style="color:#fff;text-align: center;width: 5%;"> Edit/Update </th>
                                                        <th style="color:#fff;text-align: center;width: 5%;"> Delete/Cancel </th>
                                                        <th style="color:#fff;text-align: center;width: 5%;"> Post/Un-Post/Approved/Un-Approved </th>
                                                    </tr>
                                                   </thead> -->
                                                  <tbody>

                                                <!-- Transaction Separator -->
                                                <tr>
                                                    <tr>
                                                    <td style="height:8px;background: #475F7B;color:#fff;font-size: 13px;">.: Transactions :.</td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> View/Print/Export </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Create/Add/Import/Upload </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Edit/Update  </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Delete/Cancel </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Post/Un-Post/Approved/Un-Approved </td>
                                                </tr>

                                                <!-- Employee DTR -->
                                                <tr role="row" class="odd">
                                                    
                                                  <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="1" id="employee-dtr-menu">
                                                        <label for="employee-dtr-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="timer"></i> 
                                                      <span style="line-height: 25px; font-size: 12px;"> Employee DTR </span> 
                                                    </span>
                                                  </td>

                                                    <td style="text-align: center; width:5%;">
                                                      <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeDTR_V_P_E" value="" class="access_checkbox" id="EmployeeDTR_V_P_E">
                                                        <label for="EmployeeDTR_V_P_E"></label>
                                                        </div>
                                                    </td>

                                                    <td style="text-align: center; width:5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeDTR_A_C_I_U" value="" class="access_checkbox" id="EmployeeDTR_A_C_I_U">
                                                        <label for="EmployeeDTR_A_C_I_U"></label>
                                                        </div>
                                                    </td>

                                                    <td style="text-align: center; width:5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeDTR_E_U" value="" class="access_checkbox" id="EmployeeDTR_E_U">
                                                        <label for="EmployeeDTR_E_U"></label>
                                                        </div>
                                                    </td>

                                                     <td style="text-align: center; width:5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeDTR_D_C" value="" class="access_checkbox" id="EmployeeDTR_D_C">
                                                        <label for="EmployeeDTR_D_C"></label>
                                                        </div>
                                                    </td>

                                                     <td style="text-align: center; width:5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeDTR_P_U_A_U" value="" class="access_checkbox"  id="EmployeeDTR_P_U_A_U">
                                                        <label for="EmployeeDTR_P_U_A_U"></label>
                                                        </div>
                                                    </td>

                                                </tr> 

                                                <!-- Employee Loan -->
                                                <tr role="row" class="odd">
                                                    
                                                  <td style="width: 15%;"> 
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                       <input type="checkbox" name="chkUserAccess[]" value="2" id="employee-loan-menu">
                                                        <label for="employee-loan-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="gift"></i> 
                                                      <span style="line-height: 25px; font-size: 12px;">Employee Loan </span> 
                                                    </span>
                                                  </td>

                                                  <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeLoan_V_P_E" value="" class="access_checkbox" id="EmployeeLoan_V_P_E">
                                                        <label for="EmployeeLoan_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeLoan_A_C_I_U" value="" class="access_checkbox" id="EmployeeLoan_A_C_I_U">
                                                        <label for="EmployeeLoan_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeLoan_E_U" value="" class="access_checkbox" id="EmployeeLoan_E_U">
                                                        <label for="EmployeeLoan_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeLoan_D_C" value="" class="access_checkbox" id="EmployeeLoan_D_C">
                                                        <label for="EmployeeLoan_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeLoan_P_U_A_U" value="" class="access_checkbox" id="EmployeeLoan_P_U_A_U">
                                                        <label for="EmployeeLoan_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Employee Income & Deduction -->
                                                <tr role="row" class="odd">
                                                    
                                                  <td style="width: 15%;"> 
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]" value="3" id="income-deduction-menu">
                                                        <label for="income-deduction-menu"></label>
                                                        </div> 
                                                       <i class="menu-livicon" data-icon="coins"></i> 
                                                       <span style="line-height: 25px; font-size: 12px;"> Income/Deduction </span> 
                                                    </span>
                                                  </td>

                                                  <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeIncomeDeduction_V_P_E" value="" class="access_checkbox" id="EmployeeIncomeDeduction_V_P_E">
                                                        <label for="EmployeeIncomeDeduction_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeIncomeDeduction_A_C_I_U" value="" class="access_checkbox" id="EmployeeIncomeDeduction_A_C_I_U">
                                                        <label for="EmployeeIncomeDeduction_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeIncomeDeduction_E_U" value="" class="access_checkbox" id="EmployeeIncomeDeduction_E_U">
                                                        <label for="EmployeeIncomeDeduction_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeIncomeDeduction_D_C" value="" class="access_checkbox" id="EmployeeIncomeDeduction_D_C">
                                                        <label for="EmployeeIncomeDeduction_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeIncomeDeduction_P_U_A_U" value="" class="access_checkbox" id="EmployeeIncomeDeduction_P_U_A_U">
                                                        <label for="EmployeeIncomeDeduction_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Payroll Transaction -->
                                                <tr role="row" class="odd">
                                                    
                                                   <td style="width: 15%;"> 
                                                    <span style="color:#8494a7; display: flex;"> 
                                                      <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]" value="4" id="payroll-transaction-menu">
                                                        <label for="payroll-transaction-menu"></label>
                                                        </div>
                                                       <i class="menu-livicon" data-icon="calculator"></i> 
                                                       <span style="line-height: 25px; font-size: 12px;"> Payroll Transaction </span> 
                                                    </span>
                                                  </td>

                                                  <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollTransaction_V_P_E" value="" class="access_checkbox" id="PayrollTransaction_V_P_E">
                                                        <label for="PayrollTransaction_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollTransaction_A_C_I_U" value="" class="access_checkbox" id="PayrollTransaction_A_C_I_U">
                                                        <label for="PayrollTransaction_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollTransaction_E_U" value="" class="access_checkbox" id="PayrollTransaction_E_U">
                                                        <label for="PayrollTransaction_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollTransaction_D_C" value="" class="access_checkbox" id="PayrollTransaction_D_C">
                                                        <label for="PayrollTransaction_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollTransaction_P_U_A_U" value="" class="access_checkbox" id="PayrollTransaction_P_U_A_U">
                                                        <label for="PayrollTransaction_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- 13 MOnth Transaction -->
                                                <tr role="row" class="odd">
                                                    
                                                  <td style="width:15%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="5" id="13month-transaction-menu">
                                                        <label for="13month-transaction-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="calculator"></i> 
                                                      <span style="line-height: 25px;font-size: 12px;"> 13 Month Transaction </span> 
                                                    </span>
                                                  </td>

                                                    <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="ThirteenMonthTransaction_V_P_E" value="" class="access_checkbox" id="ThirteenMonthTransaction_V_P_E">
                                                        <label for="ThirteenMonthTransaction_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="ThirteenMonthTransaction_A_C_I_U" value="" class="access_checkbox" id="ThirteenMonthTransaction_A_C_I_U">
                                                        <label for="ThirteenMonthTransaction_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="ThirteenMonthTransaction_E_U" value="" class="access_checkbox" id="ThirteenMonthTransaction_E_U">
                                                        <label for="ThirteenMonthTransaction_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="ThirteenMonthTransaction_D_C" value="" class="access_checkbox" id="ThirteenMonthTransaction_D_C">
                                                        <label for="ThirteenMonthTransaction_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="ThirteenMonthTransaction_P_U_A_U" value="" class="access_checkbox" id="ThirteenMonthTransaction_P_U_A_U">
                                                        <label for="ThirteenMonthTransaction_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Final Pay Transaction -->
                                                <tr role="row" class="odd">
                                                    
                                                  <td style="width:15%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="6" id="final-pay-transaction-menu">
                                                        <label for="final-pay-transaction-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="calculator"></i> 
                                                      <span style="line-height: 25px;font-size: 12px;"> Final Pay Transaction </span> 
                                                    </span>
                                                  </td>

                                                   <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="FinalPayTransaction_V_P_E" value="" class="access_checkbox" id="FinalPayTransaction_V_P_E">
                                                        <label for="FinalPayTransaction_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="FinalPayTransaction_A_C_I_U" value="" class="access_checkbox" id="FinalPayTransaction_A_C_I_U">
                                                        <label for="FinalPayTransaction_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="FinalPayTransaction_E_U" value="" class="access_checkbox" id="FinalPayTransaction_E_U">
                                                        <label for="FinalPayTransaction_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="FinalPayTransaction_D_C" value="" class="access_checkbox" id="FinalPayTransaction_D_C">
                                                        <label for="FinalPayTransaction_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="FinalPayTransaction_P_U_A_U" value="" class="access_checkbox" id="FinalPayTransaction_P_U_A_U">
                                                        <label for="FinalPayTransaction_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Annual Tax Transaction -->
                                                <tr role="row" class="odd">
                                                    
                                                   <td style="width:15%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="7" id="annual-tax-transaction-menu">
                                                        <label for="annual-tax-transaction-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="calculator"></i> 
                                                      <span style="line-height: 25px; font-size: 12px;"> Annual Tax Transaction </span> 
                                                    </span>
                                                  </td>

                                                  <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="AnnualTaxTransaction_V_P_E" value="" class="access_checkbox" id="AnnualTaxTransaction_V_P_E">
                                                        <label for="AnnualTaxTransaction_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="AnnualTaxTransaction_A_C_I_U" value="" class="access_checkbox" id="AnnualTaxTransaction_A_C_I_U">
                                                        <label for="AnnualTaxTransaction_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="AnnualTaxTransaction_E_U" value="" class="access_checkbox" id="AnnualTaxTransaction_E_U">
                                                        <label for="AnnualTaxTransaction_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="AnnualTaxTransaction_D_C" value="" class="access_checkbox" id="AnnualTaxTransaction_D_C">
                                                        <label for="AnnualTaxTransaction_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="AnnualTaxTransaction_P_U_A_U" value="" class="access_checkbox" id="AnnualTaxTransaction_P_U_A_U">
                                                        <label for="AnnualTaxTransaction_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>  

                                                <!-- Master Record Separator -->
                                                <tr>
                                                    <tr>
                                                    <td style="height:8px;background: #475F7B;color:#fff;font-size: 13px;">.: Master Record :.</td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> View/Print/Export </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Create/Add/Import/Upload </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Edit/Update  </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Delete/Cancel </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Post/Un-Post/Approved/Un-Approved </td>
                                                </tr>

                                                <!-- Reference & Master List -->
                                                <!-- Employee List -->
                                                <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="16" id="employee-list-menu">
                                                        <label for="employee-list-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="users"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> Employee List </span> 
                                                    </span>
                                                  </td>

                                                   <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeList_V_P_E" value="" class="access_checkbox" id="EmployeeList_V_P_E">
                                                        <label for="EmployeeList_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeList_A_C_I_U" value="" class="access_checkbox" id="EmployeeList_A_C_I_U">
                                                        <label for="EmployeeList_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeList_E_U" value="" class="access_checkbox" id="EmployeeList_E_U">
                                                        <label for="EmployeeList_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeList_D_C" value="" class="access_checkbox" id="EmployeeList_D_C">
                                                        <label for="EmployeeList_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeList_P_U_A_U" value="" class="access_checkbox" id="EmployeeList_P_U_A_U" disabled>
                                                        <label for="EmployeeList_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Employee List -->
                                                <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="17" id="loan-type-list-menu">
                                                        <label for="loan-type-list-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="list"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> Loan Type List </span> 
                                                    </span>
                                                  </td>

                                                   <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="LoanTypeList_V_P_E" value="" class="access_checkbox" id="LoanTypeList_V_P_E">
                                                        <label for="LoanTypeList_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="LoanTypeList_A_C_I_U" value="" class="access_checkbox" id="LoanTypeList_A_C_I_U">
                                                        <label for="LoanTypeList_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="LoanTypeList_E_U" value="" class="access_checkbox" id="LoanTypeList_E_U">
                                                        <label for="LoanTypeList_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="LoanTypeList_D_C" value="" class="access_checkbox" id="LoanTypeList_D_C">
                                                        <label for="LoanTypeList_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="LoanTypeList_P_U_A_U" value="" class="access_checkbox" id="LoanTypeList_P_U_A_U" disabled>
                                                        <label for="LoanTypeList_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Income & Deduction -->
                                                 <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="18" id="income-deduction-type-list-menu">
                                                        <label for="income-deduction-type-list-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="list"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> Income Deduction Type </span> 
                                                    </span>
                                                  </td>

                                                     <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="IncomeDeductionTypeList_V_P_E" value="" class="access_checkbox" id="IncomeDeductionTypeList_V_P_E">
                                                        <label for="IncomeDeductionTypeList_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="IncomeDeductionTypeList_A_C_I_U" value="" class="access_checkbox" id="IncomeDeductionTypeList_A_C_I_U">
                                                        <label for="IncomeDeductionTypeList_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="IncomeDeductionTypeList_E_U" value="" class="access_checkbox" id="IncomeDeductionTypeList_E_U">
                                                        <label for="IncomeDeductionTypeList_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="IncomeDeductionTypeList_D_C" value="" class="access_checkbox" id="IncomeDeductionTypeList_D_C">
                                                        <label for="IncomeDeductionTypeList_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="IncomeDeductionTypeList_P_U_A_U" value="" class="access_checkbox" id="IncomeDeductionTypeList_P_U_A_U" disabled>
                                                        <label for="IncomeDeductionTypeList_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>  

                                                <!-- Payroll Period -->
                                                 <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="19" id="payroll-period-menu">
                                                        <label for="payroll-period-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="list"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> Payroll Period Schedule </span> 
                                                    </span>
                                                  </td>

                                                     <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollPeriodList_V_P_E" value="" class="access_checkbox" id="PayrollPeriodList_V_P_E">
                                                        <label for="PayrollPeriodList_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollPeriodList_A_C_I_U" value="" class="access_checkbox" id="PayrollPeriodList_A_C_I_U">
                                                        <label for="PayrollPeriodList_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollPeriodList_E_U" value="" class="access_checkbox" id="PayrollPeriodList_E_U">
                                                        <label for="PayrollPeriodList_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollPeriodList_D_C" value="" class="access_checkbox" id="PayrollPeriodList_D_C">
                                                        <label for="PayrollPeriodList_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollPeriodList_P_U_A_U" value="" class="access_checkbox" id="PayrollPeriodList_P_U_A_U" disabled>
                                                        <label for="PayrollPeriodList_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>  

                                                <!-- SSS Table-->
                                                 <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="20" id="sss-table-menu">
                                                        <label for="sss-table-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="list"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> SSS Table
                                                    </span>
                                                  </td>

                                                     <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="SSSTableList_V_P_E" value="" class="access_checkbox" id="SSSTableList_V_P_E">
                                                        <label for="SSSTableList_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="SSSTableList_A_C_I_U" value="" class="access_checkbox" id="SSSTableList_A_C_I_U">
                                                        <label for="SSSTableList_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="SSSTableList_E_U" value="" class="access_checkbox" id="SSSTableList_E_U">
                                                        <label for="SSSTableList_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="SSSTableList_D_C" value="" class="access_checkbox" id="SSSTableList_D_C">
                                                        <label for="SSSTableList_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="SSSTableList_P_U_A_U" value="" class="access_checkbox" id="SSSTableList_P_U_A_U" disabled>
                                                        <label for="SSSTableList_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>  

                                                 <!-- HDMF Table-->
                                                 <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="21" id="hdmf-table-menu">
                                                        <label for="hdmf-table-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="list"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> HDMF Table
                                                    </span>
                                                  </td>

                                                     <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="HDMFTableList_V_P_E" value="" class="access_checkbox" id="HDMFTableList_V_P_E">
                                                        <label for="HDMFTableList_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="HDMFTableList_A_C_I_U" value="" class="access_checkbox" id="HDMFTableList_A_C_I_U">
                                                        <label for="HDMFTableList_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="HDMFTableList_E_U" value="" class="access_checkbox" id="HDMFTableList_E_U">
                                                        <label for="HDMFTableList_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="HDMFTableList_D_C" value="" class="access_checkbox" id="HDMFTableList_D_C">
                                                        <label for="HDMFTableList_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="HDMFTableList_P_U_A_U" value="" class="access_checkbox" id="HDMFTableList_P_U_A_U" disabled>
                                                        <label for="HDMFTableList_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>  

                                                <!-- PHIC Table-->
                                                 <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="22" id="phic-table-menu">
                                                        <label for="phic-table-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="list"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> PHIC Table
                                                    </span>
                                                  </td>

                                                     <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PHICTableList_V_P_E" value="" class="access_checkbox" id="PHICTableList_V_P_E">
                                                        <label for="PHICTableList_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PHICTableList_A_C_I_U" value="" class="access_checkbox" id="PHICTableList_A_C_I_U">
                                                        <label for="PHICTableList_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PHICTableList_E_U" value="" class="access_checkbox" id="PHICTableList_E_U">
                                                        <label for="PHICTableList_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PHICTableList_D_C" value="" class="access_checkbox" id="PHICTableList_D_C">
                                                        <label for="PHICTableList_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PHICTableList_P_U_A_U" value="" class="access_checkbox" id="PHICTableList_P_U_A_U" disabled>
                                                        <label for="PHICTableList_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr> 

                                                <!-- Annual Income Tax Table-->
                                                  <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="23" id="annual-income-tax-table-menu">
                                                        <label for="annual-income-tax-table-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="list"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> Annual Income Tax Table
                                                    </span>
                                                  </td>

                                                     <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="AnnualIncomeTaxTableList_V_P_E" value="" class="access_checkbox" id="AnnualIncomeTaxTableList_V_P_E">
                                                        <label for="AnnualIncomeTaxTableList_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="AnnualIncomeTaxTableList_A_C_I_U" value="" class="access_checkbox" id="AnnualIncomeTaxTableList_A_C_I_U">
                                                        <label for="AnnualIncomeTaxTableList_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="AnnualIncomeTaxTableList_E_U" value="" class="access_checkbox" id="AnnualIncomeTaxTableList_E_U">
                                                        <label for="AnnualIncomeTaxTableList_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="AnnualIncomeTaxTableList_D_C" value="" class="access_checkbox" id="AnnualIncomeTaxTableList_D_C">
                                                        <label for="AnnualIncomeTaxTableList_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="AnnualIncomeTaxTableList_P_U_A_U" value="" class="access_checkbox" id="AnnualIncomeTaxTableList_P_U_A_U" disabled>
                                                        <label for="AnnualIncomeTaxTableList_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr> 

                                                <!-- With Holding Tax Table-->
                                                 <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="24" id="with-holding-tax-table-menu">
                                                        <label for="with-holding-tax-table-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="list"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> WithHolding Tax Table
                                                    </span>
                                                  </td>

                                                     <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="WithHoldingTaxTableList_V_P_E" value="" class="access_checkbox" id="WithHoldingTaxTableList_V_P_E">
                                                        <label for="WithHoldingTaxTableList_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="WithHoldingTaxTableList_A_C_I_U" value="" class="access_checkbox" id="WithHoldingTaxTableList_A_C_I_U">
                                                        <label for="WithHoldingTaxTableList_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="WithHoldingTaxTableList_E_U" value="" class="access_checkbox" id="WithHoldingTaxTableList_E_U">
                                                        <label for="WithHoldingTaxTableList_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="WithHoldingTaxTableList_D_C" value="" class="access_checkbox" id="WithHoldingTaxTableList_D_C">
                                                        <label for="WithHoldingTaxTableList_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="WithHoldingTaxTableList_P_U_A_U" value="" class="access_checkbox" id="WithHoldingTaxTableList_P_U_A_U" disabled>
                                                        <label for="WithHoldingTaxTableList_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr> 
     

                                                <!-- Report Separator -->
                                                <tr>
                                                    <tr>
                                                    <td style="height:8px;background: #475F7B;color:#fff;font-size: 13px;">.: Reports :.</td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> View/Print/Export </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Create/Add/Import/Upload </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Edit/Update  </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Delete/Cancel </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Post/Un-Post/Approved/Un-Approved </td>
                                                </tr>

                                                <!-- Employee PaySlip Report -->
                                                <tr role="row" class="odd">
                                                    
                                                   <td style="width:15%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="8" id="employee-payslip-report-menu">
                                                        <label for="employee-payslip-report-menu"></label>
                                                        </div> 
                                                       <i class="menu-livicon" data-icon="print-doc"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> Employee Payslip  </span> 
                                                    </span>
                                                  </td>

                                                   <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeePaySlipReport_V_P_E" value="" class="access_checkbox" id="EmployeePaySlipReport_V_P_E">
                                                        <label for="EmployeePaySlipReport_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeePaySlipReport_A_C_I_U" value="" class="access_checkbox" id="EmployeePaySlipReport_A_C_I_U" disabled>
                                                        <label for="EmployeePaySlipReport_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeePaySlipReport_E_U" value="" class="access_checkbox" id="EmployeePaySlipReport_E_U" disabled>
                                                        <label for="EmployeePaySlipReport_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeePaySlipReport_D_C" value="" class="access_checkbox" id="EmployeePaySlipReport_D_C" disabled>
                                                        <label for="EmployeePaySlipReport_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeePaySlipReport_P_U_A_U" value="" class="access_checkbox" id="EmployeePaySlipReport_P_U_A_U" disabled>
                                                        <label for="EmployeePaySlipReport_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Payroll Register Report -->
                                                <tr role="row" class="odd">
                                                    
                                                  <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="9" id="payroll-register-report-menu">
                                                        <label for="payroll-register-report-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="print-doc"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> Payroll Register  </span> 
                                                    </span>
                                                  </td>

                                                  <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollRegisterReport_V_P_E" value="" class="access_checkbox" id="PayrollRegisterReport_V_P_E">
                                                        <label for="PayrollRegisterReport_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollRegisterReport_A_C_I_U" value="" class="access_checkbox" id="PayrollRegisterReport_A_C_I_U" disabled>
                                                        <label for="PayrollRegisterReport_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollRegisterReport_E_U" value="" class="access_checkbox" id="PayrollRegisterReport_E_U" disabled>
                                                        <label for="PayrollRegisterReport_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollRegisterReport_D_C" value="" class="access_checkbox" id="PayrollRegisterReport_D_C" disabled>
                                                        <label for="PayrollRegisterReport_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollRegisterReport_P_U_A_U" value="" class="access_checkbox" id="PayrollRegisterReport_P_U_A_U" disabled>
                                                        <label for="PayrollRegisterReport_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                 <!-- SSS Contribution Report -->
                                                <tr role="row" class="odd">

                                                  <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="10" id="sss-contribution-report-menu">
                                                        <label for="sss-contribution-report-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="print-doc"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> SSS Contribution </span> 
                                                    </span>
                                                  </td>

                                                  <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="SSSContributionReport_V_P_E" value="" class="access_checkbox" id="SSSContributionReport_V_P_E">
                                                        <label for="SSSContributionReport_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="SSSContributionReport_A_C_I_U" value="" class="access_checkbox" id="SSSContributionReport_A_C_I_U" disabled>
                                                        <label for="SSSContributionReport_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="SSSContributionReport_E_U" value="" class="access_checkbox" id="SSSContributionReport_E_U" disabled>
                                                        <label for="SSSContributionReport_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="SSSContributionReport_D_C" value="" class="access_checkbox" id="SSSContributionReport_D_C" disabled>
                                                        <label for="SSSContributionReport_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="SSSContributionReport_P_U_A_U" value="" class="access_checkbox" id="SSSContributionReport_P_U_A_U" disabled>
                                                        <label for="SSSContributionReport_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- HDMF Contribution Report -->
                                                <tr role="row" class="odd">
                                                 
                                                  <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="11" id="hdmf-contribution-report-menu">
                                                        <label for="hdmf-contribution-report-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="print-doc"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> HDMF Contribution </span> 
                                                    </span>
                                                  </td>

                                                   <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="HDMFContributionReport_V_P_E" value="" class="access_checkbox" id="HDMFContributionReport_V_P_E">
                                                        <label for="HDMFContributionReport_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="HDMFContributionReport_A_C_I_U" value="" class="access_checkbox" id="HDMFContributionReport_A_C_I_U" disabled>
                                                        <label for="HDMFContributionReport_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="HDMFContributionReport_E_U" value="" class="access_checkbox" id="HDMFContributionReport_E_U" disabled>
                                                        <label for="HDMFContributionReport_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="HDMFContributionReport_D_C" value="" class="access_checkbox" id="HDMFContributionReport_D_C" disabled>
                                                        <label for="HDMFContributionReport_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="HDMFContributionReport_P_U_A_U" value="" class="access_checkbox" id="HDMFContributionReport_P_U_A_U" disabled>
                                                        <label for="HDMFContributionReport_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- PHIC Contribution Report -->
                                                <tr role="row" class="odd">

                                                     <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="12" id="phic-contribution-report-menu">
                                                        <label for="phic-contribution-report-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="print-doc"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> PHIC Contribution </span> 
                                                    </span>
                                                  </td>

                                                    <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PHICContributionReport_V_P_E" value="" class="access_checkbox" id="PHICContributionReport_V_P_E">
                                                        <label for="PHICContributionReport_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PHICContributionReport_A_C_I_U" value="" class="access_checkbox" id="PHICContributionReport_A_C_I_U" disabled>
                                                        <label for="PHICContributionReport_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PHICContributionReport_E_U" value="" class="access_checkbox" id="PHICContributionReport_E_U" disabled>
                                                        <label for="PHICContributionReport_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PHICContributionReport_D_C" value="" class="access_checkbox" id="PHICContributionReport_D_C" disabled>
                                                        <label for="PHICContributionReport_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PHICContributionReport_P_U_A_U" value="" class="access_checkbox" id="PHICContributionReport_P_U_A_U" disabled>
                                                        <label for="PHICContributionReport_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Employee DTR Report -->
                                                <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="13" id="employee-dtr-report-menu">
                                                        <label for="employee-dtr-report-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="print-doc"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> Employee DTR </span> 
                                                    </span>
                                                  </td>

                                                   <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeDTRReport_V_P_E" value="" class="access_checkbox" id="EmployeeDTRReport_V_P_E">
                                                        <label for="EmployeeDTRReport_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeDTRReport_A_C_I_U" value="" class="access_checkbox" id="EmployeeDTRReport_A_C_I_U" disabled>
                                                        <label for="EmployeeDTRReport_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeDTRReport_E_U" value="" class="access_checkbox" id="EmployeeDTRReport_E_U" disabled>
                                                        <label for="EmployeeDTRReport_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeDTRReport_D_C" value="" class="access_checkbox" id="EmployeeDTRReport_D_C" disabled>
                                                        <label for="EmployeeDTRReport_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeDTRReport_P_U_A_U" value="" class="access_checkbox" id="EmployeeDTRReport_P_U_A_U" disabled>
                                                        <label for="EmployeeDTRReport_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- Employee Loan Report -->
                                                <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="14" id="employee-loan-report-menu">
                                                        <label for="employee-loan-report-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="print-doc"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> Employee Loan </span> 
                                                    </span>
                                                  </td>

                                                   <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeLoanReport_V_P_E" value="" class="access_checkbox" id="EmployeeLoanReport_V_P_E">
                                                        <label for="EmployeeLoanReport_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeLoanReport_A_C_I_U" value="" class="access_checkbox" id="EmployeeLoanReport_A_C_I_U" disabled>
                                                        <label for="EmployeeLoanReport_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeLoanReport_E_U" value="" class="access_checkbox" id="EmployeeLoanReport_E_U" disabled>
                                                        <label for="EmployeeLoanReport_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeLoanReport_D_C" value="" class="access_checkbox" id="EmployeeLoanReport_D_C" disabled>
                                                        <label for="EmployeeLoanReport_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="EmployeeLoanReport_P_U_A_U" value="" class="access_checkbox" id="EmployeeLoanReport_P_U_A_U" disabled>
                                                        <label for="EmployeeLoanReport_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>

                                                 <!-- Employee Income Deduction Report -->
                                                <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="15" id="income-deduction-report-menu">
                                                        <label for="income-deduction-report-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="print-doc"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> Income/Deduction </span> 
                                                    </span>
                                                  </td>

                                                   <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="IncomeDeductionReport_V_P_E" value="" class="access_checkbox" id="IncomeDeductionReport_V_P_E">
                                                        <label for="IncomeDeductionReport_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="IncomeDeductionReport_A_C_I_U" value="" class="access_checkbox" id="IncomeDeductionReport_A_C_I_U" disabled>
                                                        <label for="IncomeDeductionReport_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="IncomeDeductionReport_E_U" value="" class="access_checkbox" id="IncomeDeductionReport_E_U" disabled>
                                                        <label for="IncomeDeductionReport_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="IncomeDeductionReport_D_C" value="" class="access_checkbox" id="IncomeDeductionReport_D_C" disabled>
                                                        <label for="IncomeDeductionReport_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="IncomeDeductionReport_P_U_A_U" value="" class="access_checkbox" id="IncomeDeductionReport_P_U_A_U" disabled>
                                                        <label for="IncomeDeductionReport_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Account Management Separator -->
                                               <tr>
                                                    <tr>
                                                    <td style="height:8px;background: #475F7B;color:#fff;font-size: 13px;">.: Accounts :.</td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> View/Print/Export </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Create/Add/Import/Upload </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Edit/Update  </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Delete/Cancel </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Post/Un-Post/Approved/Un-Approved </td>
                                                </tr>

                                                <!-- User Account List-->
                                                <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="25" id="user-account-list-menu">
                                                        <label for="user-account-list-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="users"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> User Account List
                                                    </span>
                                                  </td>

                                                  <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="UserAccountList_V_P_E" value="" class="access_checkbox" id="UserAccountList_V_P_E">
                                                        <label for="UserAccountList_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="UserAccountList_A_C_I_U" value="" class="access_checkbox" id="UserAccountList_A_C_I_U">
                                                        <label for="UserAccountList_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="UserAccountList_E_U" value="" class="access_checkbox" id="UserAccountList_E_U">
                                                        <label for="UserAccountList_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="UserAccountList_D_C" value="" class="access_checkbox" id="UserAccountList_D_C">
                                                        <label for="UserAccountList_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="UserAccountList_P_U_A_U" value="" class="access_checkbox" id="UserAccountList_P_U_A_U" disabled>
                                                        <label for="UserAccountList_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr> 

                                                 <tr>
                                                    <tr>
                                                    <td style="height:8px;background: #475F7B;color:#fff;font-size: 13px;">.: Settings :.</td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> View/Print/Export </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Create/Add/Import/Upload </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Edit/Update  </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Delete/Cancel </td>
                                                    <td style="height:8px;background: #475F7B;width: 5%; color: #fff;font-size: 13px;"> Post/Un-Post/Approved/Un-Approved </td>
                                                </tr>

                                                <!-- Payroll Settings-->
                                                <tr role="row" class="odd">
                                                      <td style="width:12%;">     
                                                    <span style="color:#8494a7; display: flex;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="chkUserAccess[]"  value="26" id="payroll-setting-menu">
                                                        <label for="payroll-setting-menu"></label>
                                                        </div> 
                                                      <i class="menu-livicon" data-icon="gears"></i>
                                                      <span style="line-height: 25px; font-size: 12px;"> Payroll Settings
                                                    </span>
                                                  </td>

                                                  <td style="text-align: center;width: 5%;">
                                                    <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollSetting_V_P_E" value="" class="access_checkbox" id="PayrollSetting_V_P_E" disabled>
                                                        <label for="PayrollSetting_V_P_E"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollSetting_A_C_I_U" value="" class="access_checkbox" id="PayrollSetting_A_C_I_U" disabled>
                                                        <label for="PayrollSetting_A_C_I_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollSetting_E_U" value="" class="access_checkbox" id="PayrollSetting_E_U">
                                                        <label for="PayrollSetting_E_U"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollSetting_D_C" value="" class="access_checkbox" id="PayrollSetting_D_C" disabled>
                                                        <label for="PayrollSetting_D_C"></label>
                                                        </div>
                                                    </td>
                                                    <td style="text-align: center;width: 5%;">
                                                       <div class="checkbox checkbox-primary">
                                                        <input type="checkbox" name="PayrollSetting_P_U_A_U" value="" class="access_checkbox" id="PayrollSetting_P_U_A_U" disabled>
                                                        <label for="PayrollSetting_P_U_A_U"></label>
                                                        </div>
                                                    </td>
                                                </tr> 
                                                
                                                </tbody>
                                            </table>
                                          </div>
                                      </div>


                                </ul>
                            </div>
                        </div>                        
                    </div>
                </form>    
                </div>
                
                <div class="modal-footer">
                    <button id="btnSaveRecord" type="button" class="btn btn-primary ml-1" onclick="SaveRecord(0)">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Save</span>
                    </button>
                    <button type="button" class="btn btn-light-secondary" data-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>
                </div>
            </div>
        </div>
    </div>



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

        var vSuccessMessage="{{Session::get('Success_Message')}}";
        var vErrorMessage="{{Session::get('Error_Message')}}";

        if(vSuccessMessage!=''){
            showHasSuccessMessage(vSuccessMessage);
            return;
        }

         if(vErrorMessage!=''){
            showHasErrorMessage('',vErrorMessage);
            return;
        }

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

    function getRecordList(vPageNo, vSearchText){

        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SearchText: vSearchText,
                Status: $("#selSearchStatus").val(),
                PageNo: vPageNo
            },
            url: "{{ route('get-admin-user-list') }}",
            dataType: "json",
            success: function(data){
                LoadRecordList(data.AdminUserList);
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

    function LoadRecordList(vList){

        if(vList.length > 0){
            for(var x=0; x < vList.length; x++){
                LoadRecordRow(vList[x]);
            }
        }

    }

    function LoadRecordRow(vData){

        var tblList = $("#tblList").DataTable();

        tdID = vData.AdminUserID;
         
        tdAction="";

        if(IsAdmin==1 || IsAllowView==1){
         
        tdAction = "<div class='dropdown'>" + 
                        "<span class='bx bx-right-arrow-circle font-medium-3 dropdown-toggle nav-hide-arrow cursor-pointer' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' role='menu' style='color:#475F7B;'></span> " +
                        "<div class='dropdown-menu dropdown-menu-right'>"


                        if(IsAdmin==1 || IsAllowEdit==1){

                          tdAction = tdAction + 

                            "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.AdminUserID + ",true)' style='border-bottom:1px solid #DFE3E7;'>" +
                                "<i class='bx bx-edit-alt mr-1'></i> " +
                                "Edit Information" +
                            "</a>";

                          }

                        if(IsAdmin==1 || IsAllowEdit==1){

                          tdAction = tdAction + 

                             "<a class='dropdown-item' href='javascript:void(0);' onclick='PasswordReset(" + vData.AdminUserID + ")' style='border-bottom:1px solid #DFE3E7;'>"+
                                "<i class='bx bx-lock mr-1'></i> " +
                                "Password Reset" +
                            "</a>";

                          }

                            tdAction = tdAction +

                            "<a class='dropdown-item' href='javascript:void(0);' onclick='EditRecord(" + vData.AdminUserID + ",false)'>"+
                                "<i class='bx bx-search-alt mr-1'></i> " +
                                "View Information" +
                            "</a>";
                         

                         
                         tdAction = tdAction +  "</div>"+

                    "</div>";
        }

        tdEmployeeNo = "<span>" + vData.EmployeeNumber + "</span>";
        tdEmployeeName = "<span>" + vData.AdminName + "</span>";
        tdUsername = "<span>" + vData.Username + "</span>";

         tdIsAdmin = "";

        if(vData.IsSuperAdmin == 1){
            tdIsAdmin += "<span style='color:green;'> Super Admin </span>";
        }else{
            tdIsAdmin +="<span style='color:red;'> User Admin </span>";
        }

        tdStatus = "";

        if(vData.Status == 'Active'){
            tdStatus += "<span style='color:green;'> <i class='bx bx-check-circle'></i> Active </span>";
        }else{
            tdStatus += "<span style='color:red;'> <i class='bx bx-x-circle'></i> Inactive </span>";
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
                curData[4] = tdUsername;
                curData[5] = tdIsAdmin;
                curData[6] = tdStatus;

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
                    tdUsername,
                    tdIsAdmin,
                    tdStatus
                ]).draw();          
        }
    }

    function Clearfields(){

        $("#UserAccountID").val(0);
        $("#PersonnelID").val(0);

        $("#SearchEmployeeName").val('');
        $("#EmployeeName").val('');
        $("#EmployeeNo").val('');

        $("#contact-no").val('');
        $("#email-address").val('');

        $("#user-name").val('');
        $("#user-password").val('');

        $("#BranchID").val(0);
        $("#BranchName").val('');
        
        $("#IsSuperAdmin").val(1).change();
        $("#Status").val('Active').change();


        $("#btnSaveRecord").show();

        $("input[name='chkUserAccess[]']:checkbox").prop('checked',false);
        
        resetTextBorderToNormal();

    }

    function resetTextBorderToNormal(){

        $("#EmployeeName").css({"border":"#ccc 1px solid"});
        $("#EmployeeNo").css({"border":"#ccc 1px solid"}); 
        $("#BranchName").css({"border":"#ccc 1px solid"});
        $("#contact-no").css({"border":"#ccc 1px solid"}); 
        $("#email-address").css({"border":"#ccc 1px solid"}); 
        $("#user-name").css({"border":"#ccc 1px solid"}); 
        $("#password").css({"border":"#ccc 1px solid"}); 
        $("#IsSuperAdmin").css({"border":"#ccc 1px solid"}); 
        $("#Status").css({"border":"#ccc 1px solid"}); 

        $("#NewPassword").css({"border":"#ccc 1px solid"}); 
        $("#ConfirmNewPassword").css({"border":"#ccc 1px solid"}); 
   
    }

    function NewRecord(){

        Clearfields();
        resetCheckBoxes();

        $("#record-modal").modal();

    }

    function SaveRecord(){
    
     resetTextBorderToNormal();

    if($("#PersonnelID").val()==0){
      showHasErrorMessage('SearchEmployeeName', "Search and select employee from the list.");
      return;
     }

    if($("#BranchID").val()==0){
      showHasErrorMessage('BranchName', "Search and select branch from the list.");
      return;
     }
     
     
    if($("#user-name").val()==""){
      showHasErrorMessage('user-name', "Please enter admin user name.");
      return;
     }

     if($("#user-password").val()=="" && $("#UserAccountID").val()==0){
      showHasErrorMessage('user-password', "Please enter admin password.");
      return;
     }
    
    $("#frmUserAccess").submit();    
    
  }

 function trueOrFalse(bool){
    return bool;
 }

 function PasswordReset(vRecID){
  
   $("#NewPassword").val('');
   $("#ConfirmNewPassword").val('');  
    
   $("#AdminEmployeeUserID").val(vRecID);
   $("#password-modal").modal();

 }

 function EditRecord(vRecordID,vAllowEdit){

        if(vRecordID > 0){
            $.ajax({
                type: "post",
                async: false,
                data: {
                    _token: '{{ csrf_token() }}',
                    Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                    AdminUserID: vRecordID
                },
                url: "{{ route('get-admin-user-info') }}",
                dataType: "json",
                success: function(data){

                    if(data.Response =='Success' && data.AdminUserInfo != undefined){

                        Clearfields();
                        resetCheckBoxes();

                        $("#UserAccountID").val(data.AdminUserInfo.AdminUserID);
                        $("#PersonnelID").val(data.AdminUserInfo.EmployeeID);

                         $("#SearchEmployeeName").val(data.AdminUserInfo.AdminName);
                        
                        $("#EmployeeNo").val(data.AdminUserInfo.EmployeeNumber);
                        $("#EmployeeName").val(data.AdminUserInfo.AdminName);
                        $("#contact-no").val(data.AdminUserInfo.MobileNo);

                        $("#email-address").val(data.AdminUserInfo.EmailAddress);
                        $("#user-name").val(data.AdminUserInfo.Username);

                        $("#BranchID").val(data.AdminUserInfo.BranchID);
                        $("#BranchName").val(data.AdminUserInfo.BranchName);
 
                        $("#IsSuperAdmin").val(data.AdminUserInfo.IsSuperAdmin).change();
                        $("#Status").val(data.AdminUserInfo.Status).change();
             
                        // Menu Access CheckList
                        var IsAdmin=data.AdminUserInfo.IsSuperAdmin;

                        if(IsAdmin==0){

                          //Employee DTR
                          chkEmployeeDTRMenu=data.IsEmployeeDTRListMenu;
                          if(chkEmployeeDTRMenu==1){
                        
                            $("#employee-dtr-menu").prop('checked',true);

                            checkMenuAccess(data.EmployeeDTR_V_P_E,IsAdmin,'EmployeeDTR_V_P_E');
                            checkMenuAccess(data.EmployeeDTR_A_C_I_U,IsAdmin,'EmployeeDTR_A_C_I_U');
                            checkMenuAccess(data.EmployeeDTR_E_U,IsAdmin,'EmployeeDTR_E_U');
                            checkMenuAccess(data.EmployeeDTR_D_C,IsAdmin,'EmployeeDTR_D_C');
                            checkMenuAccess(data.EmployeeDTR_P_U_A_U,IsAdmin,'EmployeeDTR_P_U_A_U');

                          }

                          //Employee Loan
                          chkEmployeeLoanListMenu=data.IsEmployeeLoanListMenu;
                          if(chkEmployeeLoanListMenu==1){
                 
                            $("#employee-loan-menu").prop('checked',true);

                            checkMenuAccess(data.EmployeeLoan_V_P_E,IsAdmin,'EmployeeLoan_V_P_E');
                            checkMenuAccess(data.EmployeeLoan_A_C_I_U,IsAdmin,'EmployeeLoan_A_C_I_U');
                            checkMenuAccess(data.EmployeeLoan_E_U,IsAdmin,'EmployeeLoan_E_U');
                            checkMenuAccess(data.EmployeeLoan_D_C,IsAdmin,'EmployeeLoan_D_C');
                            checkMenuAccess(data.EmployeeLoan_P_U_A_U,IsAdmin,'EmployeeLoan_P_U_A_U');

                          }

                          //Employee Income/Deduction
                          chkEmployeeIncomeDeductionListMenu=data.IsEmployeeIncomeDeductionListMenu;
                          if(chkEmployeeIncomeDeductionListMenu==1){
                            
                            $("#income-deduction-menu").prop('checked',true);
                            
                            checkMenuAccess(data.EmployeeIncomeDeduction_V_P_E,IsAdmin,'EmployeeIncomeDeduction_V_P_E');
                            checkMenuAccess(data.EmployeeIncomeDeduction_A_C_I_U,IsAdmin,'EmployeeIncomeDeduction_A_C_I_U');
                            checkMenuAccess(data.EmployeeIncomeDeduction_E_U,IsAdmin,'EmployeeIncomeDeduction_E_U');
                            checkMenuAccess(data.EmployeeIncomeDeduction_D_C,IsAdmin,'EmployeeIncomeDeduction_D_C');
                            checkMenuAccess(data.EmployeeIncomeDeduction_P_U_A_U,IsAdmin,'EmployeeIncomeDeduction_P_U_A_U');

                          }

                          //Payroll Transaction
                          chkPayrollTransactionListMenu=data.IsPayrollTransactionListMenu;
                          if(chkPayrollTransactionListMenu==1){
                            
                            $("#payroll-transaction-menu").prop('checked',true);
                            
                            checkMenuAccess(data.PayrollTransaction_V_P_E,IsAdmin,'PayrollTransaction_V_P_E');
                            checkMenuAccess(data.PayrollTransaction_A_C_I_U,IsAdmin,'PayrollTransaction_A_C_I_U');
                            checkMenuAccess(data.PayrollTransaction_E_U,IsAdmin,'PayrollTransaction_E_U');
                            checkMenuAccess(data.PayrollTransaction_D_C,IsAdmin,'PayrollTransaction_D_C');
                            checkMenuAccess(data.PayrollTransaction_P_U_A_U,IsAdmin,'PayrollTransaction_P_U_A_U');

                          }

                          //13 Month Transaction
                          chk13MonthTransactionListMenu=data.Is13MonthTransactionListMenu;
                          if(chk13MonthTransactionListMenu==1){
                            
                            $("#13month-transaction-menu").prop('checked',true);
                            
                            checkMenuAccess(data.ThirteenMonthTransaction_V_P_E,IsAdmin,'ThirteenMonthTransaction_V_P_E');
                            checkMenuAccess(data.ThirteenMonthTransaction_A_C_I_U,IsAdmin,'ThirteenMonthTransaction_A_C_I_U');
                            checkMenuAccess(data.ThirteenMonthTransaction_E_U,IsAdmin,'ThirteenMonthTransaction_E_U');
                            checkMenuAccess(data.ThirteenMonthTransaction_D_C,IsAdmin,'ThirteenMonthTransaction_D_C');
                            checkMenuAccess(data.ThirteenMonthTransaction_P_U_A_U,IsAdmin,'ThirteenMonthTransaction_P_U_A_U');

                          }

                          //Final Pay Transaction
                          chkFinalPayTransactionListMenu=data.IsFinalPayTransactionListMenu;
                          if(chkFinalPayTransactionListMenu==1){
                            
                            $("#final-pay-transaction-menu").prop('checked',true);
                            
                            checkMenuAccess(data.FinalPayTransaction_V_P_E,IsAdmin,'FinalPayTransaction_V_P_E');
                            checkMenuAccess(data.FinalPayTransaction_A_C_I_U,IsAdmin,'FinalPayTransaction_A_C_I_U');
                            checkMenuAccess(data.FinalPayTransaction_E_U,IsAdmin,'FinalPayTransaction_E_U');
                            checkMenuAccess(data.FinalPayTransaction_D_C,IsAdmin,'FinalPayTransaction_D_C');
                            checkMenuAccess(data.FinalPayTransaction_P_U_A_U,IsAdmin,'FinalPayTransaction_P_U_A_U');

                          }

                           //Annual Tax Transaction
                          chkAnnualTaxTransactionListMenu=data.IsAnnualTaxTransactionListMenu;
                          if(chkAnnualTaxTransactionListMenu==1){
                            
                            $("#annual-tax-transaction-menu").prop('checked',true);
                            
                            checkMenuAccess(data.AnnualTaxTransaction_V_P_E,IsAdmin,'AnnualTaxTransaction_V_P_E');
                            checkMenuAccess(data.AnnualTaxTransaction_A_C_I_U,IsAdmin,'AnnualTaxTransaction_A_C_I_U');
                            checkMenuAccess(data.AnnualTaxTransaction_E_U,IsAdmin,'AnnualTaxTransaction_E_U');
                            checkMenuAccess(data.AnnualTaxTransaction_D_C,IsAdmin,'AnnualTaxTransaction_D_C');
                            checkMenuAccess(data.AnnualTaxTransaction_P_U_A_U,IsAdmin,'AnnualTaxTransaction_P_U_A_U');

                          }

                          //REPORTS

                          //Employee PaySlip Report
                          chkEmployeePaySlipListMenu=data.IsEmployeePaySlipListMenu;
                          if(chkEmployeePaySlipListMenu==1){
                            
                            $("#employee-payslip-report-menu").prop('checked',true);
                            
                            checkMenuAccess(data.EmployeePaySlipReport_V_P_E,IsAdmin,'EmployeePaySlipReport_V_P_E');
                            checkMenuAccess(data.EmployeePaySlipReport_A_C_I_U,IsAdmin,'EmployeePaySlipReport_A_C_I_U');
                            checkMenuAccess(data.EmployeePaySlipReport_E_U,IsAdmin,'EmployeePaySlipReport_E_U');
                            checkMenuAccess(data.EmployeePaySlipReport_D_C,IsAdmin,'EmployeePaySlipReport_D_C');
                            checkMenuAccess(data.EmployeePaySlipReport_P_U_A_U,IsAdmin,'EmployeePaySlipReport_P_U_A_U');

                          }

                          //Payroll Register Report
                          chkPayrollRegisterListMenu=data.IsPayrollRegisterListMenu;
                          if(chkPayrollRegisterListMenu==1){
                            
                            $("#payroll-register-report-menu").prop('checked',true);
                            
                            checkMenuAccess(data.PayrollRegisterReport_V_P_E,IsAdmin,'PayrollRegisterReport_V_P_E');
                            checkMenuAccess(data.PayrollRegisterReport_A_C_I_U,IsAdmin,'PayrollRegisterReport_A_C_I_U');
                            checkMenuAccess(data.PayrollRegisterReport_E_U,IsAdmin,'PayrollRegisterReport_E_U');
                            checkMenuAccess(data.PayrollRegisterReport_D_C,IsAdmin,'PayrollRegisterReport_D_C');
                            checkMenuAccess(data.PayrollRegisterReport_P_U_A_U,IsAdmin,'PayrollRegisterReport_P_U_A_U');

                          }

                          //SSS Contribution Report
                          chkSSSContributionListMenu=data.IsSSSContributionListMenu;
                          if(chkSSSContributionListMenu==1){
                            
                            $("#sss-contribution-report-menu").prop('checked',true);
                            
                            checkMenuAccess(data.SSSContributionReport_V_P_E,IsAdmin,'SSSContributionReport_V_P_E');
                            checkMenuAccess(data.SSSContributionReport_A_C_I_U,IsAdmin,'SSSContributionReport_A_C_I_U');
                            checkMenuAccess(data.SSSContributionReport_E_U,IsAdmin,'SSSContributionReport_E_U');
                            checkMenuAccess(data.SSSContributionReport_D_C,IsAdmin,'SSSContributionReport_D_C');
                            checkMenuAccess(data.SSSContributionReport_P_U_A_U,IsAdmin,'SSSContributionReport_P_U_A_U');

                          }

                          //HDMF Contribution Report
                          chkHDMFContributionListMenu=data.IsHDMFContributionListMenu;
                          if(chkHDMFContributionListMenu==1){
                            
                            $("#hdmf-contribution-report-menu").prop('checked',true);
                            
                            checkMenuAccess(data.HDMFContributionReport_V_P_E,IsAdmin,'HDMFContributionReport_V_P_E');
                            checkMenuAccess(data.HDMFContributionReport_A_C_I_U,IsAdmin,'HDMFContributionReport_A_C_I_U');
                            checkMenuAccess(data.HDMFContributionReport_E_U,IsAdmin,'HDMFContributionReport_E_U');
                            checkMenuAccess(data.HDMFContributionReport_D_C,IsAdmin,'HDMFContributionReport_D_C');
                            checkMenuAccess(data.HDMFContributionReport_P_U_A_U,IsAdmin,'HDMFContributionReport_P_U_A_U');

                          }

                          //PHIC Contribution Report
                          chkPHICContributionListMenu=data.IsPHICContributionListMenu;
                          if(chkPHICContributionListMenu==1){
                            
                            $("#phic-contribution-report-menu").prop('checked',true);
                            
                            checkMenuAccess(data.PHICContributionReport_V_P_E,IsAdmin,'PHICContributionReport_V_P_E');
                            checkMenuAccess(data.PHICContributionReport_A_C_I_U,IsAdmin,'PHICContributionReport_A_C_I_U');
                            checkMenuAccess(data.PHICContributionReport_E_U,IsAdmin,'PHICContributionReport_E_U');
                            checkMenuAccess(data.PHICContributionReport_D_C,IsAdmin,'PHICContributionReport_D_C');
                            checkMenuAccess(data.PHICContributionReport_P_U_A_U,IsAdmin,'PHICContributionReport_P_U_A_U');

                          }

                          //Employee DTR Report
                          chkEmployeeDTRReportListMenu=data.IsEmployeeDTRReportListMenu;
                          if(chkEmployeeDTRReportListMenu==1){
                            
                            $("#employee-dtr-report-menu").prop('checked',true);
                            
                            checkMenuAccess(data.EmployeeDTRReport_V_P_E,IsAdmin,'EmployeeDTRReport_V_P_E');
                            checkMenuAccess(data.EmployeeDTRReport_A_C_I_U,IsAdmin,'EmployeeDTRReport_A_C_I_U');
                            checkMenuAccess(data.EmployeeDTRReport_E_U,IsAdmin,'EmployeeDTRReport_E_U');
                            checkMenuAccess(data.EmployeeDTRReport_D_C,IsAdmin,'EmployeeDTRReport_D_C');
                            checkMenuAccess(data.EmployeeDTRReport_P_U_A_U,IsAdmin,'EmployeeDTRReport_P_U_A_U');

                          }

                          //Employee Loan Report
                          chkEmployeeLoanReportListMenu=data.IsEmployeeLoanReportListMenu;
                          if(chkEmployeeLoanReportListMenu==1){
                            
                            $("#employee-loan-report-menu").prop('checked',true);
                            
                            checkMenuAccess(data.EmployeeLoanReport_V_P_E,IsAdmin,'EmployeeLoanReport_V_P_E');
                            checkMenuAccess(data.EmployeeLoanReport_A_C_I_U,IsAdmin,'EmployeeLoanReport_A_C_I_U');
                            checkMenuAccess(data.EmployeeLoanReport_E_U,IsAdmin,'EmployeeLoanReport_E_U');
                            checkMenuAccess(data.EmployeeLoanReport_D_C,IsAdmin,'EmployeeLoanReport_D_C');
                            checkMenuAccess(data.EmployeeLoanReport_P_U_A_U,IsAdmin,'EmployeeLoanReport_P_U_A_U');

                          }

                          //Income Deduction Report
                          chkIncomeDeductionReportListMenu=data.IsIncomeDeductionReportListMenu;
                          if(chkIncomeDeductionReportListMenu==1){
                            
                            $("#income-deduction-report-menu").prop('checked',true);
                            
                            checkMenuAccess(data.IncomeDeductionReport_V_P_E,IsAdmin,'IncomeDeductionReport_V_P_E');
                            checkMenuAccess(data.IncomeDeductionReport_A_C_I_U,IsAdmin,'IncomeDeductionReport_A_C_I_U');
                            checkMenuAccess(data.IncomeDeductionReport_E_U,IsAdmin,'IncomeDeductionReport_E_U');
                            checkMenuAccess(data.IncomeDeductionReport_D_C,IsAdmin,'IncomeDeductionReport_D_C');
                            checkMenuAccess(data.IncomeDeductionReport_P_U_A_U,IsAdmin,'IncomeDeductionReport_P_U_A_U');

                          }

                          //MASTER RECORD LIST
                          
                          // EMPLOYEE LIST
                          chkEmployeeListMenu=data.IsEmployeeListMenu;
                          if(chkEmployeeListMenu==1){
                            
                            $("#employee-list-menu").prop('checked',true);
                            
                            checkMenuAccess(data.EmployeeList_V_P_E,IsAdmin,'EmployeeList_V_P_E');
                            checkMenuAccess(data.EmployeeList_A_C_I_U,IsAdmin,'EmployeeList_A_C_I_U');
                            checkMenuAccess(data.EmployeeList_E_U,IsAdmin,'EmployeeList_E_U');
                            checkMenuAccess(data.EmployeeList_D_C,IsAdmin,'EmployeeList_D_C');
                            checkMenuAccess(data.EmployeeList_P_U_A_U,IsAdmin,'EmployeeList_P_U_A_U');

                          }

                           // LOAN TYPE LIST
                          chkLoanTypeListMenu=data.IsLoanTypeListMenu;
                          if(chkLoanTypeListMenu==1){
                            
                            $("#loan-type-list-menu").prop('checked',true);
                            
                            checkMenuAccess(data.LoanTypeList_V_P_E,IsAdmin,'LoanTypeList_V_P_E');
                            checkMenuAccess(data.LoanTypeList_A_C_I_U,IsAdmin,'LoanTypeList_A_C_I_U');
                            checkMenuAccess(data.LoanTypeList_E_U,IsAdmin,'LoanTypeList_E_U');
                            checkMenuAccess(data.LoanTypeList_D_C,IsAdmin,'LoanTypeList_D_C');
                            checkMenuAccess(data.LoanTypeList_P_U_A_U,IsAdmin,'LoanTypeList_P_U_A_U');

                          }

                          // INCOME DEDUCTION TYPE LIST
                          chkIncomeDeductionTypeListMenu=data.IsIncomeDeductionTypeListMenu;
                          if(chkIncomeDeductionTypeListMenu==1){
                            
                            $("#income-deduction-type-list-menu").prop('checked',true);
                            
                            checkMenuAccess(data.IncomeDeductionTypeList_V_P_E,IsAdmin,'IncomeDeductionTypeList_V_P_E');
                            checkMenuAccess(data.IncomeDeductionTypeList_A_C_I_U,IsAdmin,'IncomeDeductionTypeList_A_C_I_U');
                            checkMenuAccess(data.IncomeDeductionTypeList_E_U,IsAdmin,'IncomeDeductionTypeList_E_U');
                            checkMenuAccess(data.IncomeDeductionTypeList_D_C,IsAdmin,'IncomeDeductionTypeList_D_C');
                            checkMenuAccess(data.IncomeDeductionTypeList_P_U_A_U,IsAdmin,'IncomeDeductionTypeList_P_U_A_U');

                          }

                          // PAYROLL PERIOD LIST
                          chkPayrollPeriodListMenu=data.IsPayrollPeriodListMenu;
                          if(chkPayrollPeriodListMenu==1){
                            
                            $("#payroll-period-menu").prop('checked',true);
                            
                            checkMenuAccess(data.PayrollPeriodList_V_P_E,IsAdmin,'PayrollPeriodList_V_P_E');
                            checkMenuAccess(data.PayrollPeriodList_A_C_I_U,IsAdmin,'PayrollPeriodList_A_C_I_U');
                            checkMenuAccess(data.PayrollPeriodList_E_U,IsAdmin,'PayrollPeriodList_E_U');
                            checkMenuAccess(data.PayrollPeriodList_D_C,IsAdmin,'PayrollPeriodList_D_C');
                            checkMenuAccess(data.PayrollPeriodList_P_U_A_U,IsAdmin,'PayrollPeriodList_P_U_A_U');

                          }

                          // SSS TABLE LIST
                          chkSSSTableListMenu=data.IsSSSTableListMenu;
                          if(chkSSSTableListMenu==1){
                            
                            $("#sss-table-menu").prop('checked',true);
                            
                            checkMenuAccess(data.SSSTableList_V_P_E,IsAdmin,'SSSTableList_V_P_E');
                            checkMenuAccess(data.SSSTableList_A_C_I_U,IsAdmin,'SSSTableList_A_C_I_U');
                            checkMenuAccess(data.SSSTableList_E_U,IsAdmin,'SSSTableList_E_U');
                            checkMenuAccess(data.SSSTableList_D_C,IsAdmin,'SSSTableList_D_C');
                            checkMenuAccess(data.SSSTableList_P_U_A_U,IsAdmin,'SSSTableList_P_U_A_U');

                          }

                           // HDMF TABLE LIST
                          chkHDMFTableListMenu=data.IsHDMFTableListMenu;
                          if(chkSSSTableListMenu==1){
                            
                            $("#hdmf-table-menu").prop('checked',true);
                            
                            checkMenuAccess(data.HDMFTableList_V_P_E,IsAdmin,'HDMFTableList_V_P_E');
                            checkMenuAccess(data.HDMFTableList_A_C_I_U,IsAdmin,'HDMFTableList_A_C_I_U');
                            checkMenuAccess(data.HDMFTableList_E_U,IsAdmin,'HDMFTableList_E_U');
                            checkMenuAccess(data.HDMFTableList_D_C,IsAdmin,'HDMFTableList_D_C');
                            checkMenuAccess(data.HDMFTableList_P_U_A_U,IsAdmin,'HDMFTableList_P_U_A_U');

                          }

                            // PHIC TABLE LIST
                          chkPHICTableListMenu=data.IsPHICTableListMenu;
                          if(chkPHICTableListMenu==1){
                            
                            $("#phic-table-menu").prop('checked',true);
                            
                            checkMenuAccess(data.PHICTableList_V_P_E,IsAdmin,'PHICTableList_V_P_E');
                            checkMenuAccess(data.PHICTableList_A_C_I_U,IsAdmin,'PHICTableList_A_C_I_U');
                            checkMenuAccess(data.PHICTableList_E_U,IsAdmin,'PHICTableList_E_U');
                            checkMenuAccess(data.PHICTableList_D_C,IsAdmin,'PHICTableList_D_C');
                            checkMenuAccess(data.PHICTableList_P_U_A_U,IsAdmin,'PHICTableList_P_U_A_U');

                          }

                          // ANNUAL INCOME TAX LIST
                          chkAnnualIncomeTaxTableListMenu=data.IsAnnualIncomeTaxTableListMenu;
                          if(chkAnnualIncomeTaxTableListMenu==1){
                            
                            $("#annual-income-tax-table-menu").prop('checked',true);
                            
                            checkMenuAccess(data.AnnualIncomeTaxTableList_V_P_E,IsAdmin,'AnnualIncomeTaxTableList_V_P_E');
                            checkMenuAccess(data.AnnualIncomeTaxTableList_A_C_I_U,IsAdmin,'AnnualIncomeTaxTableList_A_C_I_U');
                            checkMenuAccess(data.AnnualIncomeTaxTableList_E_U,IsAdmin,'AnnualIncomeTaxTableList_E_U');
                            checkMenuAccess(data.AnnualIncomeTaxTableList_D_C,IsAdmin,'AnnualIncomeTaxTableList_D_C');
                            checkMenuAccess(data.AnnualIncomeTaxTableList_P_U_A_U,IsAdmin,'AnnualIncomeTaxTableList_P_U_A_U');

                          }

                          // WITH HOLDING TAX LIST
                          chkWithHoldingTaxTableListMenu=data.IsWithHoldingTaxTableListMenu;
                          if(chkWithHoldingTaxTableListMenu==1){
                            
                            $("#with-holding-tax-table-menu").prop('checked',true);
                            
                            checkMenuAccess(data.WithHoldingTaxTableList_V_P_E,IsAdmin,'WithHoldingTaxTableList_V_P_E');
                            checkMenuAccess(data.WithHoldingTaxTableList_A_C_I_U,IsAdmin,'WithHoldingTaxTableList_A_C_I_U');
                            checkMenuAccess(data.WithHoldingTaxTableList_E_U,IsAdmin,'WithHoldingTaxTableList_E_U');
                            checkMenuAccess(data.WithHoldingTaxTableList_D_C,IsAdmin,'WithHoldingTaxTableList_D_C');
                            checkMenuAccess(data.WithHoldingTaxTableList_P_U_A_U,IsAdmin,'WithHoldingTaxTableList_P_U_A_U');

                          }

                           // USER ACCOUNT LIST
                          chkUserAccountListMenu=data.IsUserAccountListMenu;
                          if(chkUserAccountListMenu==1){
                            
                            $("#user-account-list-menu").prop('checked',true);
                            
                            checkMenuAccess(data.UserAccountList_V_P_E,IsAdmin,'UserAccountList_V_P_E');
                            checkMenuAccess(data.UserAccountList_A_C_I_U,IsAdmin,'UserAccountList_A_C_I_U');
                            checkMenuAccess(data.UserAccountList_E_U,IsAdmin,'UserAccountList_E_U');
                            checkMenuAccess(data.UserAccountList_D_C,IsAdmin,'UserAccountList_D_C');
                            checkMenuAccess(data.UserAccountList_P_U_A_U,IsAdmin,'UserAccountList_P_U_A_U');

                          }

                          // USER ACCOUNT LIST
                          chkPayrollSettingMenu=data.IsPayrollSettingMenu;
                          if(chkPayrollSettingMenu==1){
                            
                            $("#payroll-setting-menu").prop('checked',true);
                            
                            checkMenuAccess(data.PayrollSetting_V_P_E,IsAdmin,'PayrollSetting_V_P_E');
                            checkMenuAccess(data.PayrollSetting_A_C_I_U,IsAdmin,'PayrollSetting_A_C_I_U');
                            checkMenuAccess(data.PayrollSetting_E_U,IsAdmin,'PayrollSetting_E_U');
                            checkMenuAccess(data.PayrollSetting_D_C,IsAdmin,'PayrollSetting_D_C');
                            checkMenuAccess(data.PayrollSetting_P_U_A_U,IsAdmin,'PayrollSetting_P_U_A_U');

                          }

                          
                        }else{
                          SuperAdminAccessCheckBoxes();
                        }


                        if(vAllowEdit){
                            $("#btnSaveRecord").show();
                        }else{
                             $("#btnSaveRecord").hide();
                        }
                        
                        buttonOneClick("btnSaveRecord", "Save", false);

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

  function checkMenuAccess(vMenuAccess,vcheckIsAdmin,vElem){

    $("#"+vElem).prop("checked", false);
    $("#"+vElem).val(0); 

    if(vcheckIsAdmin==1){
       $("#"+vElem).prop("checked", true);
       $("#"+vElem).val(1);    
    }

    if(vMenuAccess == 1){
      $("#"+vElem).prop("checked", true);  
      $("#"+vElem).val(1);      
    }
  }

  $(function(){
      $('input.access_checkbox').change(function()
      {
        if($(this).is(':checked')) {
           $(this).val(1);
        }else{
           $(this).val(0);
        }
      });
    });

  function resetCheckBoxes(){

    $('input.access_checkbox').each(function () {
       $(this).prop('checked', false);
       $(this).val(0);  
    });

  }

  function SuperAdminAccessCheckBoxes(){

    $('input[type=checkbox]').each(function () {
       $(this).prop('checked', true);
       $(this).val(1);  
    });

  }

  $(document).on('focus','.autocomplete_txt',function(){
    isEmployee=false;
     isBranch=false;
     var valAttrib  = $(this).attr('data-complete-type');

       if(valAttrib=='employee'){
            isEmployee=true;
            var postURL="{{ URL::route('get-employee-search-list')}}";
        }

        $(this).autocomplete({
            source: function( request, response ) {
               if(request.term.length >= 2){
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
                      $("#PersonnelID").val(seldata[0]);
                      $("#EmployeeNo").val(seldata[1].trim());
                      
                      $("#SearchEmployeeName").val(seldata[4].trim());
                      $("#EmployeeName").val(seldata[5].trim());

                      $("#contact-no").val(seldata[6].trim());
                      $("#email-address").val(seldata[7].trim());

                      $("#BranchID").val(seldata[8].trim());
                      $("#BranchName").val(seldata[9].trim());
                      
                    }

                   
              }
        });
    });

   function doChangePassword(){

    resetTextBorderToNormal();

     if($("#NewPassword").val()==''){
      showHasErrorMessage('NewPassword', "Enter new password for password reset.");
      return;
     }

     if($("#ConfirmNewPassword").val()==''){
      showHasErrorMessage('ConfirmNewPassword', "Enter new password for password reset.");
      return;
     }

    if($("#NewPassword").val()!=$("#ConfirmNewPassword").val()){
      showHasErrorMessage('', "New password and confirm new password does not matched.");
      return;
     }

      $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                AdminUserID: $("#AdminEmployeeUserID").val(),
                NewPassword: $("#NewPassword").val(),
                ConfirmNewPassword: $("#ConfirmNewPassword").val()
              
            },
            url: "{{ route('request-change-password') }}",
            dataType: "json",
            success: function(data){

               if(data.Response =='Success'){
                   showHasSuccessMessage(data.ResponseMessage);
                    $("#NewPassword").val('');
                    $("#ConfirmNewPassword").val('');  

                    $("#password-modal").modal('hide');

                }else{
                      showHasErrorMessage('', data.ResponseMessage);
                    return; 
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

  }

   // Load & Append data on scroll down
    $(window).scroll(function() {
        if(!isPageFirstLoad){
            if($(window).scrollTop() + $(window).height() >= ($(document).height() - 10) ){
                intCurrentPage = intCurrentPage + 1;
                getRecordList(intCurrentPage, $('.searchtext').val());
            }
        }
    });

    $( document ).ready(function() {
      $('#record-modal').modal({
        backdrop: 'static',
        keyboard: true,
        show: false 
    });

});

    function showPassword() {

      var x = document.getElementById("user-password");

      if (x.type === "password") {
         x.type = "text";
      }else{
         x.type = "password";
      }

    }

</script>

@endsection



