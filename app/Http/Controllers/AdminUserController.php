<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use Mail;
use Session;
use Hash;
use View;
use Image;
use DB;
use Excel;
use PDF;

use App\Models\Misc;
use App\Models\AdminUsers;
use App\Models\PayrollPeriod;
use App\Models\AdminDashboard;

class AdminUserController extends Controller {
 
 //ADMIN//
 function SetAdminInitialData($data){
    
    $Page=$data['Page'];
    $AdminUsers = new AdminUsers();  

    $data['Allow_View_Print_Export']=0;
    $data['Allow_Add_Create_Import_Upload']=0;
    $data['Allow_Edit_Update']=0;
    $data['Allow_Delete_Cancel']=0;
    $data['Allow_Post_UnPost_Approve_UnApprove']=0;

    if($Page!='' && ($Page!='Admin Dashboard' || $Page!='Forgot Password')){

      $hasMenuAccess=0;

      if($AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),$data['Page'])){
        $hasMenuAccess=1;

        $RetVal=$AdminUsers->getAdminUserAccessMenuDetails(Session::get('ADMIN_USER_ID'),$data['Page']);

        $data['Allow_View_Print_Export'] = $RetVal['Allow_View_Print_Export'];
        $data['Allow_Add_Create_Import_Upload'] =  $RetVal['Allow_Add_Create_Import_Upload'];
        $data['Allow_Edit_Update'] =  $RetVal['Allow_Edit_Update'];
        $data['Allow_Delete_Cancel'] =  $RetVal['Allow_Delete_Cancel'];
        $data['Allow_Post_UnPost_Approve_UnApprove'] =  $RetVal['Allow_Post_UnPost_Approve_UnApprove'];

       }else{
          // if no access then go to admin dashboard
          //return Redirect::route('admin-dashboard');
       }
     }

     $data["AdminUsers"]=$AdminUsers;

     return $data;
  }

  function IsAdminLoggedIn(){

    if (!Session('ADMIN_LOGGED_IN')) {
        Session::flash('session_expires','You have been idle for several minutes & your session has been expired. Please log-in again');
        return false;
    }

    return true;
  }

  public function AdminLogout(){
    
    $ExpiredSession=Session::get('session_expires');

    Session::flush();
    Session::put('session_expires', $ExpiredSession);
    
    if(Session::get('session_expires')!=''){
        Session::flash('session_expires','You have been idle for several minutes & your session has been expired. Please log-in again');
    }

    return Redirect::route('admin-login');

  }

 public function showAdminLogin(Request $request){

    $data['Page']='Admin Login Page';
    $data = $this->SetAdminInitialData($data);

    $param['SearchText']='';
    $param["Limit"] = 0;
    $param["PageNo"] = 0;
    $param["Status"]='Open';

    $PayrollPeriod = new PayrollPeriod();
    $data['PayrollPeriodList']=$PayrollPeriod->getPayrollPeriodList($param);

    return View::make('admin/admin-login')->with($data);
 }

 public function getEmployeeAdminList(Request $request){

    $AdminUsers = new AdminUsers();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = config('app.ListRowLimit');
    $param["Status"] = '';
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AdminUserList"] = $AdminUsers->getAdminUserList($param);

    return response()->json($RetVal);

  }
  
  public function doAdminCheckLogin(Request $request){

      $AdminUsers = new AdminUsers();

      $ResponseMessage = "";
      $data['Username'] = $request['Username'];
      $data['UserPassword'] = $request['UserPassword'];
      $data['PayrollPeriodCode'] = $request['PayrollPeriod'];

      if (empty($data['Username'])) {
        $ResponseMessage = 'Please enter your username.';
      }elseif(empty($data['UserPassword'])){
        $ResponseMessage ='Please enter your password.';
      }

      if (!empty($ResponseMessage)){
          return Redirect::back()->with('Error_Message',$ResponseMessage);
      }else{
          if(!$AdminUsers->doAdminCheckLoginAccount($data)){
              $ResponseMessage='Sorry. The system is unable to verify your account.';
              $RetVal['Response'] = "Failed";
              $RetVal['ResponseMessage'] = $ResponseMessage;
          }else{
              $RetVal['Response'] = "Success";
          }
      }

       return response()->json($RetVal);

  }

  public function showAdminForgotPassword(Request $request){

    $data['Page'] = 'Forgot Password';
    $data = $this->SetAdminInitialData($data);

    return View::make('admin/admin-forgot-password')->with($data);
 }

  public function showAdminDashboard(Request $request){

    if(!$this->IsAdminLoggedIn()){
      return Redirect::route('admin-logout');
    }

    $data['Page'] = 'Admin Dashboard';
    $data = $this->SetAdminInitialData($data);
    

    $AdminDashboard= new AdminDashboard();
    $retVal=$AdminDashboard->getDashboardTotals();
    
    $data['TotalEmployee']=$retVal['TotalEmployee'];

    $data['TotalActiveEmployee']=$retVal['TotalActiveEmployee'];
    $data['TotalInActiveEmployee']=$retVal['TotalInActiveEmployee'];

    $data['TotalPMCDavao']=$retVal['TotalPMCDavao'];
    $data['TotalPMCAgusan']=$retVal['TotalPMCAgusan'];

    return View::make('admin/admin-dashboard')->with($data);
  
 }

  public function showAdminChangePassword(Request $request){

    if(!$this->IsAdminLoggedIn()){
      return Redirect::route('admin-logout');
    }

    $data['Page']='Admin Change Password';
    $data = $this->SetAdminInitialData($data);

     return View::make('admin/admin-change-password')->with($data);
 }

   public function showAdminUserAccountList(Request $request){

    if(!$this->IsAdminLoggedIn()){
      return Redirect::route('admin-logout');
    }

    $data['Page'] = 'User Account List';
    $data = $this->SetAdminInitialData($data);

    return View::make('admin/admin-user-account-list')->with($data);
  
 }

  public function doAdminChangePassword(Request $request){

    $AdminUsers = new AdminUsers();

    $ResponseMessage = "";
    $data["AdminUserID"] = Session('ADMIN_USER_ID');

    $data["CurrentPassword"] = $request['CurrentPassword'];
    $data["NewPassword"] = $request['NewPassword'];
    $data["ConfirmNewPassword"] = $request['ConfirmNewPassword'];

    if(empty($data["CurrentPassword"])) {
        $ResponseMessage='Please enter your current password.';
    }else if(empty($data["NewPassword"])) {
        $ResponseMessage='Please enter your new password.';
    }else if(empty($data["ConfirmNewPassword"])) {
        $ResponseMessage='Please confirm your new password.';
    }

    if(strlen($data["NewPassword"])<6 && strlen($data["ConfirmNewPassword"])<6) {
         $ResponseMessage='Password must be atleast 6 characters or more. Consist either alpha or numeric password characters';
    }

    if($data["NewPassword"] != $data["ConfirmNewPassword"]) {
        $ResponseMessage='New password and confirm new password does not matched.';
    }

    if(!empty($ResponseMessage)) {
            $RetVal['Response'] = "Failed";
            $RetVal['ResponseMessage'] = $ResponseMessage;
    }else{

        $ResponseMessage = $AdminUsers->doAdminChangePassword($data);

        if($ResponseMessage != 'Success'){
            $RetVal['Response'] = "Failed";
            $RetVal['ResponseMessage'] = $ResponseMessage;
        }else{
            $RetVal['Response'] = "Success";
            $ResponseMessage="You have successfully changed your password.";
            $RetVal['ResponseMessage'] = $ResponseMessage;
        }
    }
    return response()->json($RetVal);
  }


  public function doSaveUserAccount(Request $request){
  
  $AdminUsers = new AdminUsers();
    
  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $err_message=null;

  $data['UserAccountID'] = $request['UserAccountID'];
  $data['PersonnelID'] = $request['PersonnelID'];
  $data['EmployeeNo'] = $request['EmployeeNo'];

  $data['BranchID'] = $request['BranchID'];

  $data['Username'] = $request['Username'];
  $data['UserPassword'] = $request['UserPassword'];

  $data['IsSuperAdmin'] = empty($request['IsSuperAdmin']) ? 0 : 1;
 
 //TRANSACTION
 //Employee DTR Transaction
$data['EmployeeDTR_V_P_E'] = empty($request['EmployeeDTR_V_P_E']) ? 0 : 1;
$data['EmployeeDTR_A_C_I_U'] = empty($request['EmployeeDTR_A_C_I_U']) ? 0 : 1;
$data['EmployeeDTR_E_U'] = empty($request['EmployeeDTR_E_U']) ? 0 : 1;
$data['EmployeeDTR_D_C'] = empty($request['EmployeeDTR_D_C']) ? 0 : 1;
$data['EmployeeDTR_P_U_A_U'] = empty($request['EmployeeDTR_P_U_A_U']) ? 0 : 1;

//Employee Loan
$data['EmployeeLoan_V_P_E'] = empty($request['EmployeeLoan_V_P_E']) ? 0 : 1;
$data['EmployeeLoan_A_C_I_U'] = empty($request['EmployeeLoan_A_C_I_U']) ? 0 : 1;
$data['EmployeeLoan_E_U'] = empty($request['EmployeeLoan_E_U']) ? 0 : 1;
$data['EmployeeLoan_D_C'] = empty($request['EmployeeLoan_D_C']) ? 0 : 1;
$data['EmployeeLoan_P_U_A_U'] = empty($request['EmployeeLoan_P_U_A_U']) ? 0 : 1;

//Employee Income/Deduction
$data['EmployeeIncomeDeduction_V_P_E'] = empty($request['EmployeeIncomeDeduction_V_P_E']) ? 0 : 1;
$data['EmployeeIncomeDeduction_A_C_I_U'] = empty($request['EmployeeIncomeDeduction_A_C_I_U']) ? 0 : 1;
$data['EmployeeIncomeDeduction_E_U'] = empty($request['EmployeeIncomeDeduction_E_U']) ? 0 : 1;
$data['EmployeeIncomeDeduction_D_C'] = empty($request['EmployeeIncomeDeduction_D_C']) ? 0 : 1;
$data['EmployeeIncomeDeduction_P_U_A_U'] = empty($request['EmployeeIncomeDeduction_P_U_A_U']) ? 0 : 1;

//Payroll Transaction
$data['PayrollTransaction_V_P_E'] = empty($request['PayrollTransaction_V_P_E']) ? 0 : 1;
$data['PayrollTransaction_A_C_I_U'] = empty($request['PayrollTransaction_A_C_I_U']) ? 0 : 1;
$data['PayrollTransaction_E_U'] = empty($request['PayrollTransaction_E_U']) ? 0 : 1;
$data['PayrollTransaction_D_C'] = empty($request['PayrollTransaction_D_C']) ? 0 : 1;
$data['PayrollTransaction_P_U_A_U'] = empty($request['PayrollTransaction_P_U_A_U']) ? 0 : 1;

//13 Month Transaction
$data['ThirteenMonthTransaction_V_P_E'] = empty($request['ThirteenMonthTransaction_V_P_E']) ? 0 : 1;
$data['ThirteenMonthTransaction_A_C_I_U'] = empty($request['ThirteenMonthTransaction_A_C_I_U']) ? 0 : 1;
$data['ThirteenMonthTransaction_E_U'] = empty($request['ThirteenMonthTransaction_E_U']) ? 0 : 1;
$data['ThirteenMonthTransaction_D_C'] = empty($request['ThirteenMonthTransaction_D_C']) ? 0 : 1;
$data['ThirteenMonthTransaction_P_U_A_U'] = empty($request['ThirteenMonthTransaction_P_U_A_U']) ? 0 : 1;

//Final Pay Transaction
$data['FinalPayTransaction_V_P_E'] = empty($request['FinalPayTransaction_V_P_E']) ? 0 : 1;
$data['FinalPayTransaction_A_C_I_U'] = empty($request['FinalPayTransaction_A_C_I_U']) ? 0 : 1;
$data['FinalPayTransaction_E_U'] = empty($request['FinalPayTransaction_E_U']) ? 0 : 1;
$data['FinalPayTransaction_D_C'] = empty($request['FinalPayTransaction_D_C']) ? 0 : 1;
$data['FinalPayTransaction_P_U_A_U'] = empty($request['FinalPayTransaction_P_U_A_U']) ? 0 : 1;

//Annual Tax Transaction
$data['AnnualTaxTransaction_V_P_E'] = empty($request['AnnualTaxTransaction_V_P_E']) ? 0 : 1;
$data['AnnualTaxTransaction_A_C_I_U'] = empty($request['AnnualTaxTransaction_A_C_I_U']) ? 0 : 1;
$data['AnnualTaxTransaction_E_U'] = empty($request['AnnualTaxTransaction_E_U']) ? 0 : 1;
$data['AnnualTaxTransaction_D_C'] = empty($request['AnnualTaxTransaction_D_C']) ? 0 : 1;
$data['AnnualTaxTransaction_P_U_A_U'] = empty($request['AnnualTaxTransaction_P_U_A_U']) ? 0 : 1;

//Employee PaySlip Reports
$data['EmployeePaySlipReport_V_P_E'] = empty($request['EmployeePaySlipReport_V_P_E']) ? 0 : 1;
$data['EmployeePaySlipReport_A_C_I_U'] = empty($request['EmployeePaySlipReport_A_C_I_U']) ? 0 : 1;
$data['EmployeePaySlipReport_E_U'] = empty($request['EmployeePaySlipReport_E_U']) ? 0 : 1;
$data['EmployeePaySlipReport_D_C'] = empty($request['EmployeePaySlipReport_D_C']) ? 0 : 1;
$data['EmployeePaySlipReport_P_U_A_U'] = empty($request['EmployeePaySlipReport_P_U_A_U']) ? 0 : 1;

//Payroll Register Reports
$data['PayrollRegisterReport_V_P_E'] = empty($request['PayrollRegisterReport_V_P_E']) ? 0 : 1;
$data['PayrollRegisterReport_A_C_I_U'] = empty($request['PayrollRegisterReport_A_C_I_U']) ? 0 : 1;
$data['PayrollRegisterReport_E_U'] = empty($request['PayrollRegisterReport_E_U']) ? 0 : 1;
$data['PayrollRegisterReport_D_C'] = empty($request['PayrollRegisterReport_D_C']) ? 0 : 1;
$data['PayrollRegisterReport_P_U_A_U'] = empty($request['PayrollRegisterReport_P_U_A_U']) ? 0 : 1;

//SSS Contribution Reports
$data['SSSContributionReport_V_P_E'] = empty($request['SSSContributionReport_V_P_E']) ? 0 : 1;
$data['SSSContributionReport_A_C_I_U'] = empty($request['SSSContributionReport_A_C_I_U']) ? 0 : 1;
$data['SSSContributionReport_E_U'] = empty($request['SSSContributionReport_E_U']) ? 0 : 1;
$data['SSSContributionReport_D_C'] = empty($request['SSSContributionReport_D_C']) ? 0 : 1;
$data['SSSContributionReport_P_U_A_U'] = empty($request['SSSContributionReport_P_U_A_U']) ? 0 : 1;

//HDMF Contribution Reports
$data['HDMFContributionReport_V_P_E'] = empty($request['HDMFContributionReport_V_P_E']) ? 0 : 1;
$data['HDMFContributionReport_A_C_I_U'] = empty($request['HDMFContributionReport_A_C_I_U']) ? 0 : 1;
$data['HDMFContributionReport_E_U'] = empty($request['HDMFContributionReport_E_U']) ? 0 : 1;
$data['HDMFContributionReport_D_C'] = empty($request['HDMFContributionReport_D_C']) ? 0 : 1;
$data['HDMFContributionReport_P_U_A_U'] = empty($request['HDMFContributionReport_P_U_A_U']) ? 0 : 1;

//PHIC Contribution Reports
$data['PHICContributionReport_V_P_E'] = empty($request['PHICContributionReport_V_P_E']) ? 0 : 1;
$data['PHICContributionReport_A_C_I_U'] = empty($request['PHICContributionReport_A_C_I_U']) ? 0 : 1;
$data['PHICContributionReport_E_U'] = empty($request['PHICContributionReport_E_U']) ? 0 : 1;
$data['PHICContributionReport_D_C'] = empty($request['PHICContributionReport_D_C']) ? 0 : 1;
$data['PHICContributionReport_P_U_A_U'] = empty($request['PHICContributionReport_P_U_A_U']) ? 0 : 1;

//Employee DTR Reports
$data['EmployeeDTRReport_V_P_E'] = empty($request['EmployeeDTRReport_V_P_E']) ? 0 : 1;
$data['EmployeeDTRReport_A_C_I_U'] = empty($request['EmployeeDTRReport_A_C_I_U']) ? 0 : 1;
$data['EmployeeDTRReport_E_U'] = empty($request['EmployeeDTRReport_E_U']) ? 0 : 1;
$data['EmployeeDTRReport_D_C'] = empty($request['EmployeeDTRReport_D_C']) ? 0 : 1;
$data['EmployeeDTRReport_P_U_A_U'] = empty($request['EmployeeDTRReport_P_U_A_U']) ? 0 : 1;

//Employee Loan Reports
$data['EmployeeLoanReport_V_P_E'] = empty($request['EmployeeLoanReport_V_P_E']) ? 0 : 1;
$data['EmployeeLoanReport_A_C_I_U'] = empty($request['EmployeeLoanReport_A_C_I_U']) ? 0 : 1;
$data['EmployeeLoanReport_E_U'] = empty($request['EmployeeLoanReport_E_U']) ? 0 : 1;
$data['EmployeeLoanReport_D_C'] = empty($request['EmployeeLoanReport_D_C']) ? 0 : 1;
$data['EmployeeLoanReport_P_U_A_U'] = empty($request['EmployeeLoanReport_P_U_A_U']) ? 0 : 1;

//Income Deduction Reports
$data['IncomeDeductionReport_V_P_E'] = empty($request['IncomeDeductionReport_V_P_E']) ? 0 : 1;
$data['IncomeDeductionReport_A_C_I_U'] = empty($request['IncomeDeductionReport_A_C_I_U']) ? 0 : 1;
$data['IncomeDeductionReport_E_U'] = empty($request['IncomeDeductionReport_E_U']) ? 0 : 1;
$data['IncomeDeductionReport_D_C'] = empty($request['IncomeDeductionReport_D_C']) ? 0 : 1;
$data['IncomeDeductionReport_P_U_A_U'] = empty($request['IncomeDeductionReport_P_U_A_U']) ? 0 : 1;

//Employee List
$data['EmployeeList_V_P_E'] = empty($request['EmployeeList_V_P_E']) ? 0 : 1;
$data['EmployeeList_A_C_I_U'] = empty($request['EmployeeList_A_C_I_U']) ? 0 : 1;
$data['EmployeeList_E_U'] = empty($request['EmployeeList_E_U']) ? 0 : 1;
$data['EmployeeList_D_C'] = empty($request['EmployeeList_D_C']) ? 0 : 1;
$data['EmployeeList_P_U_A_U'] = empty($request['EmployeeList_P_U_A_U']) ? 0 : 1;

//Loan Type List
$data['LoanTypeList_V_P_E'] = empty($request['LoanTypeList_V_P_E']) ? 0 : 1;
$data['LoanTypeList_A_C_I_U'] = empty($request['LoanTypeList_A_C_I_U']) ? 0 : 1;
$data['LoanTypeList_E_U'] = empty($request['LoanTypeList_E_U']) ? 0 : 1;
$data['LoanTypeList_D_C'] = empty($request['LoanTypeList_D_C']) ? 0 : 1;
$data['LoanTypeList_P_U_A_U'] = empty($request['LoanTypeList_P_U_A_U']) ? 0 : 1;

//Income Deduction Type List
$data['IncomeDeductionTypeList_V_P_E'] = empty($request['IncomeDeductionTypeList_V_P_E']) ? 0 : 1;
$data['IncomeDeductionTypeList_A_C_I_U'] = empty($request['IncomeDeductionTypeList_A_C_I_U']) ? 0 : 1;
$data['IncomeDeductionTypeList_E_U'] = empty($request['IncomeDeductionTypeList_E_U']) ? 0 : 1;
$data['IncomeDeductionTypeList_D_C'] = empty($request['IncomeDeductionTypeList_D_C']) ? 0 : 1;
$data['IncomeDeductionTypeList_P_U_A_U'] = empty($request['IncomeDeductionTypeList_P_U_A_U']) ? 0 : 1;

//Payroll Period List
$data['PayrollPeriodList_V_P_E'] = empty($request['PayrollPeriodList_V_P_E']) ? 0 : 1;
$data['PayrollPeriodList_A_C_I_U'] = empty($request['PayrollPeriodList_A_C_I_U']) ? 0 : 1;
$data['PayrollPeriodList_E_U'] = empty($request['PayrollPeriodList_E_U']) ? 0 : 1;
$data['PayrollPeriodList_D_C'] = empty($request['PayrollPeriodList_D_C']) ? 0 : 1;
$data['PayrollPeriodList_P_U_A_U'] = empty($request['PayrollPeriodList_P_U_A_U']) ? 0 : 1;

//SSS Table List
$data['SSSTableList_V_P_E'] = empty($request['SSSTableList_V_P_E']) ? 0 : 1;
$data['SSSTableList_A_C_I_U'] = empty($request['SSSTableList_A_C_I_U']) ? 0 : 1;
$data['SSSTableList_E_U'] = empty($request['SSSTableList_E_U']) ? 0 : 1;
$data['SSSTableList_D_C'] = empty($request['SSSTableList_D_C']) ? 0 : 1;
$data['SSSTableList_P_U_A_U'] = empty($request['SSSTableList_P_U_A_U']) ? 0 : 1;

//HDMF Table List
$data['HDMFTableList_V_P_E'] = empty($request['HDMFTableList_V_P_E']) ? 0 : 1;
$data['HDMFTableList_A_C_I_U'] = empty($request['HDMFTableList_A_C_I_U']) ? 0 : 1;
$data['HDMFTableList_E_U'] = empty($request['HDMFTableList_E_U']) ? 0 : 1;
$data['HDMFTableList_D_C'] = empty($request['HDMFTableList_D_C']) ? 0 : 1;
$data['HDMFTableList_P_U_A_U'] = empty($request['HDMFTableList_P_U_A_U']) ? 0 : 1;

//PHIC Table List
$data['PHICTableList_V_P_E'] = empty($request['PHICTableList_V_P_E']) ? 0 : 1;
$data['PHICTableList_A_C_I_U'] = empty($request['PHICTableList_A_C_I_U']) ? 0 : 1;
$data['PHICTableList_E_U'] = empty($request['PHICTableList_E_U']) ? 0 : 1;
$data['PHICTableList_D_C'] = empty($request['PHICTableList_D_C']) ? 0 : 1;
$data['PHICTableList_P_U_A_U'] = empty($request['PHICTableList_P_U_A_U']) ? 0 : 1;

//Annual Income Tax Table List
$data['AnnualIncomeTaxTableList_V_P_E'] = empty($request['AnnualIncomeTaxTableList_V_P_E']) ? 0 : 1;
$data['AnnualIncomeTaxTableList_A_C_I_U'] = empty($request['AnnualIncomeTaxTableList_A_C_I_U']) ? 0 : 1;
$data['AnnualIncomeTaxTableList_E_U'] = empty($request['AnnualIncomeTaxTableList_E_U']) ? 0 : 1;
$data['AnnualIncomeTaxTableList_D_C'] = empty($request['AnnualIncomeTaxTableList_D_C']) ? 0 : 1;
$data['AnnualIncomeTaxTableList_P_U_A_U'] = empty($request['AnnualIncomeTaxTableList_P_U_A_U']) ? 0 : 1;

//With Holding Tax Table List
$data['WithHoldingTaxTableList_V_P_E'] = empty($request['WithHoldingTaxTableList_V_P_E']) ? 0 : 1;
$data['WithHoldingTaxTableList_A_C_I_U'] = empty($request['WithHoldingTaxTableList_A_C_I_U']) ? 0 : 1;
$data['WithHoldingTaxTableList_E_U'] = empty($request['WithHoldingTaxTableList_E_U']) ? 0 : 1;
$data['WithHoldingTaxTableList_D_C'] = empty($request['WithHoldingTaxTableList_D_C']) ? 0 : 1;
$data['WithHoldingTaxTableList_P_U_A_U'] = empty($request['WithHoldingTaxTableList_P_U_A_U']) ? 0 : 1;

//User Account List
$data['UserAccountList_V_P_E'] = empty($request['UserAccountList_V_P_E']) ? 0 : 1;
$data['UserAccountList_A_C_I_U'] = empty($request['UserAccountList_A_C_I_U']) ? 0 : 1;
$data['UserAccountList_E_U'] = empty($request['UserAccountList_E_U']) ? 0 : 1;
$data['UserAccountList_D_C'] = empty($request['UserAccountList_D_C']) ? 0 : 1;
$data['UserAccountList_P_U_A_U'] = empty($request['UserAccountList_P_U_A_U']) ? 0 : 1;

//Payroll Setting
$data['PayrollSetting_V_P_E'] = empty($request['PayrollSetting_V_P_E']) ? 0 : 1;
$data['PayrollSetting_A_C_I_U'] = empty($request['PayrollSetting_A_C_I_U']) ? 0 : 1;
$data['PayrollSetting_E_U'] = empty($request['PayrollSetting_E_U']) ? 0 : 1;
$data['PayrollSetting_D_C'] = empty($request['PayrollSetting_D_C']) ? 0 : 1;
$data['PayrollSetting_P_U_A_U'] = empty($request['PayrollSetting_P_U_A_U']) ? 0 : 1;

$data['Status'] = $request['Status'];
  
  $data['CreatedByID'] = Session('ADMIN_USER_ID');
  $data['UpdatedByID'] = Session('ADMIN_USER_ID');

  $data['UserAccess'] = $request['chkUserAccess'];

  if((empty($data['UserAccess']) || ($data['UserAccess']<=0)) && $request['IsSuperAdmin']==0){
      $err_message='Please check applied for user menu navigation and access';
  }

  if($data['UserAccountID']==0){
      if($AdminUsers->checkUserAccountIfExist($data)){
        $err_message='Employee for admin account already exist.';
    }
  }

  if (!empty($err_message)) {
      return Redirect::back()->with('Error_Message',$err_message);
  }else{    

    $Response = $AdminUsers->doSaveUserAccount($data);

    if($Response != "Success"){
        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = "Something went wrong while saving admin account.";
        return Redirect::back()->with('Error_Message',$RetVal['ResponseMessage']);
    }else{
        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Admin account has saved successfully.";
        return Redirect::back()->with('Success_Message',$RetVal['ResponseMessage']);
    }
  }
}

public function getEmployeeAdminInfo(Request $request){

    $AdminUsers = new AdminUsers();

    $PlatformType=request("Platform");
    $AdminUserID = request("AdminUserID");

    $EmployeeAdminInfo = $AdminUsers->getAdminUserInfo($AdminUserID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AdminUserInfo"] = $EmployeeAdminInfo;

    //TRANSACTION LIST

    //EMPLOYEE DTR
    $IsEmployeeDTRListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Employee DTR')){
      $IsEmployeeDTRListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Employee DTR');

      $RetVal["EmployeeDTR_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["EmployeeDTR_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["EmployeeDTR_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["EmployeeDTR_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["EmployeeDTR_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

     $RetVal["IsEmployeeDTRListMenu"] = $IsEmployeeDTRListMenu;

    //EMPLOYEE LOAN 
    $IsEmployeeLoanListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Employee Loan')){
      $IsEmployeeLoanListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Employee Loan');

      $RetVal["EmployeeLoan_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["EmployeeLoan_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["EmployeeLoan_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["EmployeeLoan_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["EmployeeLoan_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];


     }
     $RetVal["IsEmployeeLoanListMenu"] = $IsEmployeeLoanListMenu;

    //EMPLOYEE INCOME DEDUCTION 
    $IsEmployeeIncomeDeductionListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Income/Deduction Transaction')){
      $IsEmployeeIncomeDeductionListMenu=1;

       $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Income/Deduction Transaction');

      $RetVal["EmployeeIncomeDeduction_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["EmployeeIncomeDeduction_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["EmployeeIncomeDeduction_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["EmployeeIncomeDeduction_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["EmployeeIncomeDeduction_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }
     $RetVal["IsEmployeeIncomeDeductionListMenu"] = $IsEmployeeIncomeDeductionListMenu;

    //PAYROLL TRANSACTION
    $IsPayrollTransactionListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Payroll Transaction')){
      $IsPayrollTransactionListMenu=1;

       $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Payroll Transaction');

      $RetVal["PayrollTransaction_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["PayrollTransaction_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["PayrollTransaction_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["PayrollTransaction_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["PayrollTransaction_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

     $RetVal["IsPayrollTransactionListMenu"] = $IsPayrollTransactionListMenu;

    //13 MONTH TRANSACTION
    $Is13MonthTransactionListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'13th Month Transaction')){
      $Is13MonthTransactionListMenu=1;

       $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'13th Month Transaction');

      $RetVal["ThirteenMonthTransaction_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["ThirteenMonthTransaction_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["ThirteenMonthTransaction_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["ThirteenMonthTransaction_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["ThirteenMonthTransaction_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

     $RetVal["Is13MonthTransactionListMenu"] = $Is13MonthTransactionListMenu;

    //FINAL PAY TRANSACTION
    $IsFinalPayTransactionListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Final Pay Transaction')){
      $IsFinalPayTransactionListMenu=1;

       $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Final Pay Transaction');

      $RetVal["FinalPayTransaction_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["FinalPayTransaction_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["FinalPayTransaction_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["FinalPayTransaction_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["FinalPayTransaction_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

     $RetVal["IsFinalPayTransactionListMenu"] = $IsFinalPayTransactionListMenu;

    //ANNUAL TAX TRANSACTION
    $IsAnnualTaxTransactionListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Annual Tax Transaction')){
      $IsAnnualTaxTransactionListMenu=1;

       $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Annual Tax Transaction');

      $RetVal["AnnualTaxTransaction_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["AnnualTaxTransaction_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["AnnualTaxTransaction_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["AnnualTaxTransaction_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["AnnualTaxTransaction_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

     $RetVal["IsAnnualTaxTransactionListMenu"] = $IsAnnualTaxTransactionListMenu;

    //GENERATE REPORTS LIST

    //EMPLOYEE PAYSLIP REPORT
    $IsEmployeePaySlipListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Employee Payslip Report')){
      $IsEmployeePaySlipListMenu=1;

       $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Employee Payslip Report');

      $RetVal["EmployeePaySlipReport_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["EmployeePaySlipReport_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["EmployeePaySlipReport_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["EmployeePaySlipReport_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["EmployeePaySlipReport_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

     $RetVal["IsEmployeePaySlipListMenu"] = $IsEmployeePaySlipListMenu;

    //PAYROLL REGISTER REPORT
    $IsPayrollRegisterListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Payroll Register Report')){
      $IsPayrollRegisterListMenu=1;

       $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Payroll Register Report');

      $RetVal["PayrollRegisterReport_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["PayrollRegisterReport_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["PayrollRegisterReport_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["PayrollRegisterReport_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["PayrollRegisterReport_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

     $RetVal["IsPayrollRegisterListMenu"] = $IsPayrollRegisterListMenu;

    //SSS CONTRIBUTION REPORT
    $IsSSSContributionListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'SSS Contribution Report')){
      $IsSSSContributionListMenu=1;

       $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'SSS Contribution Report');

      $RetVal["SSSContributionReport_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["SSSContributionReport_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["SSSContributionReport_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["SSSContributionReport_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["SSSContributionReport_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

     $RetVal["IsSSSContributionListMenu"] = $IsSSSContributionListMenu;

    //HDMF CONTRIBUTION REPORT
    $IsHDMFContributionListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'HDMF Contribution Report')){
      $IsHDMFContributionListMenu=1;

       $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'HDMF Contribution Report');

      $RetVal["HDMFContributionReport_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["HDMFContributionReport_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["HDMFContributionReport_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["HDMFContributionReport_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["HDMFContributionReport_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

     $RetVal["IsHDMFContributionListMenu"] = $IsHDMFContributionListMenu;

    //PHIC CONTRIBUTION REPORT
    $IsPHICContributionListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'PHIC Contribution Report')){
      $IsPHICContributionListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'PHIC Contribution Report');

      $RetVal["PHICContributionReport_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["PHICContributionReport_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["PHICContributionReport_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["PHICContributionReport_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["PHICContributionReport_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

     $RetVal["IsPHICContributionListMenu"] = $IsPHICContributionListMenu;

    //EMPLOYEE DTR REPORT
    $IsEmployeeDTRReportListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Employee DTR Report')){
      $IsEmployeeDTRReportListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Employee DTR Report');

      $RetVal["EmployeeDTRReport_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["EmployeeDTRReport_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["EmployeeDTRReport_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["EmployeeDTRReport_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["EmployeeDTRReport_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsEmployeeDTRReportListMenu"] = $IsEmployeeDTRReportListMenu;

    //EMPLOYEE LOAN REPORT
    $IsEmployeeLoanReportListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Employee Loan Report')){
      $IsEmployeeLoanReportListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Employee Loan Report');

      $RetVal["EmployeeLoanReport_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["EmployeeLoanReport_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["EmployeeLoanReport_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["EmployeeLoanReport_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["EmployeeLoanReport_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsEmployeeLoanReportListMenu"] = $IsEmployeeLoanReportListMenu;

    //INCOME DEDUCTION REPORT
    $IsIncomeDeductionReportListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Income/Deduction Report')){
      $IsIncomeDeductionReportListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Income/Deduction Report');

      $RetVal["IncomeDeductionReport_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["IncomeDeductionReport_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["IncomeDeductionReport_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["IncomeDeductionReport_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["IncomeDeductionReport_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsIncomeDeductionReportListMenu"] = $IsIncomeDeductionReportListMenu;

    // MASTER RECORD LIST
    
    //EMPLOYEE LIST
    $IsEmployeeListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Employee List')){
      $IsEmployeeListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Employee List');

      $RetVal["EmployeeList_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["EmployeeList_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["EmployeeList_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["EmployeeList_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["EmployeeList_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsEmployeeListMenu"] = $IsEmployeeListMenu;

    //LOAN TYPE LIST
    $IsLoanTypeListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Loan Type')){
      $IsLoanTypeListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Loan Type');

      $RetVal["LoanTypeList_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["LoanTypeList_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["LoanTypeList_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["LoanTypeList_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["LoanTypeList_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsLoanTypeListMenu"] = $IsLoanTypeListMenu;

    // INCOME/DEDUCTION TYPE LIST
    $IsIncomeDeductionTypeListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Income/Deduction Type')){
      $IsIncomeDeductionTypeListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Income/Deduction Type');

      $RetVal["IncomeDeductionTypeList_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["IncomeDeductionTypeList_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["IncomeDeductionTypeList_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["IncomeDeductionTypeList_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["IncomeDeductionTypeList_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsIncomeDeductionTypeListMenu"] = $IsIncomeDeductionTypeListMenu;

    // PAYROLL PERIOD LIST
    $IsPayrollPeriodListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Payroll Period Schedule')){
      $IsPayrollPeriodListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Payroll Period Schedule');

      $RetVal["PayrollPeriodList_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["PayrollPeriodList_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["PayrollPeriodList_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["PayrollPeriodList_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["PayrollPeriodList_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsPayrollPeriodListMenu"] = $IsPayrollPeriodListMenu;

    // SSS TABLE LIST
    $IsSSSTableListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'SSS Table')){
      $IsSSSTableListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'SSS Table');

      $RetVal["SSSTableList_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["SSSTableList_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["SSSTableList_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["SSSTableList_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["SSSTableList_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsSSSTableListMenu"] = $IsSSSTableListMenu;

    // HDMF TABLE LIST
    $IsHDMFTableListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'HDMF Table')){
      $IsHDMFTableListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'HDMF Table');

      $RetVal["HDMFTableList_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["HDMFTableList_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["HDMFTableList_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["HDMFTableList_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["HDMFTableList_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsHDMFTableListMenu"] = $IsHDMFTableListMenu;

     // PHIC TABLE LIST
    $IsPHICTableListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'PHIC Table')){
      $IsPHICTableListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'PHIC Table');

      $RetVal["PHICTableList_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["PHICTableList_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["PHICTableList_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["PHICTableList_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["PHICTableList_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsPHICTableListMenu"] = $IsPHICTableListMenu;

    // ANNUAL INCOME TAX
    $IsAnnualIncomeTaxTableListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Annual Income Tax Table')){
      $IsAnnualIncomeTaxTableListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Annual Income Tax Table');

      $RetVal["AnnualIncomeTaxTableList_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["AnnualIncomeTaxTableList_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["AnnualIncomeTaxTableList_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["AnnualIncomeTaxTableList_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["AnnualIncomeTaxTableList_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsAnnualIncomeTaxTableListMenu"] = $IsAnnualIncomeTaxTableListMenu;

    // WITH HOLDING TAX
    $IsWithHoldingTaxTableListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'WithHolding Tax Table')){
      $IsWithHoldingTaxTableListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'WithHolding Tax Table');

      $RetVal["WithHoldingTaxTableList_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["WithHoldingTaxTableList_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["WithHoldingTaxTableList_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["WithHoldingTaxTableList_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["WithHoldingTaxTableList_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsWithHoldingTaxTableListMenu"] = $IsWithHoldingTaxTableListMenu;

    // USER ACCOUNT LIST
    $IsUserAccountListMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'User Account List')){
      $IsUserAccountListMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'User Account List');

      $RetVal["UserAccountList_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["UserAccountList_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["UserAccountList_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["UserAccountList_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["UserAccountList_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsUserAccountListMenu"] = $IsUserAccountListMenu;

    //PAYROLL SETTING
    $IsPayrollSettingMenu=0;
    if($AdminUsers->getAdminUserAccess($AdminUserID,'Payroll Setting')){
      $IsPayrollSettingMenu=1;

      $Value=$AdminUsers->getAdminUserAccessMenuDetails($AdminUserID,'Payroll Setting');

      $RetVal["PayrollSetting_V_P_E"] = $Value['Allow_View_Print_Export'];
      $RetVal["PayrollSetting_A_C_I_U"] =  $Value['Allow_Add_Create_Import_Upload'];
      $RetVal["PayrollSetting_E_U"] =  $Value['Allow_Edit_Update'];
      $RetVal["PayrollSetting_D_C"] =  $Value['Allow_Delete_Cancel'];
      $RetVal["PayrollSetting_P_U_A_U"] =  $Value['Allow_Post_UnPost_Approve_UnApprove'];

     }

    $RetVal["IsPayrollSettingMenu"] = $IsPayrollSettingMenu;

    
     return response()->json($RetVal);

  }


public function doAdminRequestForChangePassword(Request $request){

    $AdminUsers = new AdminUsers();

    $ResponseMessage = "";
    $data["AdminUserID"] = $request['AdminUserID'];

    $data["NewPassword"] = $request['NewPassword'];
    $data["ConfirmNewPassword"] = $request['ConfirmNewPassword'];
   
    if(empty($data["NewPassword"])) {
        $ResponseMessage='Please enter your new password.';
    }else if(empty($data["ConfirmNewPassword"])) {
        $ResponseMessage='Please confirm your new password.';
    }

    if(strlen($data["NewPassword"])<6 && strlen($data["ConfirmNewPassword"])<6) {
         $ResponseMessage='Password must be atleast 6 characters or more. Consist either alpha or numeric password characters';
    }

    if($data["NewPassword"] != $data["ConfirmNewPassword"]) {
        $ResponseMessage='New password and confirm new password does not matched.';
    }

    if(!empty($ResponseMessage)) {
            $RetVal['Response'] = "Failed";
            $RetVal['ResponseMessage'] = $ResponseMessage;
    }else{

        $ResponseMessage = $AdminUsers->doAdminRequestForChangePassword($data);
        if($ResponseMessage != 'Success'){
            $RetVal['Response'] = "Failed";
            $RetVal['ResponseMessage'] = $ResponseMessage;
        }else{
            $RetVal['Response'] = "Success";
            $ResponseMessage="Admin user request for reset password has successfully changed.";
            $RetVal['ResponseMessage'] = $ResponseMessage;
        }
    }
    return response()->json($RetVal);
  }


}



