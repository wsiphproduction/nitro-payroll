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
use App\Models\PayrollSetting;

class PayrollSettingController extends Controller {
 
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
        Session::flash('session_expires','Your session has been expired. Please log-in again.');
        return false;
    }

    return true;
  }


public function showAdminPayrollSetting(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Payroll Setting';
  $data = $this->SetAdminInitialData($data);

  $PayrollSetting = new PayrollSetting();
  $data['PayrollSetting']=$PayrollSetting->getPayrollSettingInfo(config('app.DEFAULT_SYSTEM_SETTING'));

  return View::make('admin/admin-payroll-settings')->with($data);

}

  public function doSavePayrollSetting(Request $request){
       
    $PayrollSetting = new PayrollSetting();

    $ResponseMessage = "";
    $data["SettingID"] =  request('SettingID');
    $data["CompanyCode"] =  request("CompanyCode");
    $data["CompanyName"] =  request("CompanyName");
    $data["CompanyWebsite"] =  request("CompanyWebsite");
    $data["CompanyPhoneNo"] =  request("CompanyPhoneNo");
    $data["CompanyMobileNo"] =  request("CompanyMobileNo");
    $data["CompanyFaxNo"] =  request("CompanyFaxNo");
    $data["CompanyEmailAddress"] =  request("CompanyEmailAddress");

    $data["CompanyAddress"] =  request("CompanyAddress");
    $data["CompanyCity"] =  request("CompanyCity");
    $data["CompanyPostalCode"] =  request("CompanyPostalCode");
    $data["CompanyCountry"] =  request("CompanyCountry");

      if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;

      }else{

        $PayrollSettingID = $PayrollSetting->doSavePayrollSetting($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Payroll setting for company information has saved successfully.";

      }
  
    return response()->json($RetVal);

  }

   public function doSavePayrollSettingClosingDate(Request $request){

    $PayrollSetting = new PayrollSetting();

    $ResponseMessage = "";
    $data["SettingID"] =  request('SettingID');
    $data["ClosingDate"] =  request("ClosingDate");

      if(!empty($ResponseMessage)){
        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
      }else{
        $PayrollSettingID = $PayrollSetting->doSavePayrollSettingClosingDate($data);
        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Payroll setting for closing date has saved successfully.";

      }
  
    return response()->json($RetVal);

   }

  public function doSavePayrollEmployeeSetting(Request $request){

    $PayrollSetting = new PayrollSetting();

    $ResponseMessage = "";
    $data["SettingID"] =  request('SettingID');
    $data["NDPercentage"] =  request("NDPercentage");
    $data["MinTakeHomePercentage"] =  request("MinTakeHomePercentage");

      if(!empty($ResponseMessage)){
        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
      }else{
        $PayrollSettingID = $PayrollSetting->doSavePayrollEmployeeSetting($data);
        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Payroll setting for employee has saved successfully.";

      }
  
    return response()->json($RetVal);

   }

   public function doSavePayrollGovermentPremiumSetting(Request $request){

    $PayrollSetting = new PayrollSetting();

    $ResponseMessage = "";
    $data["SettingID"] =  request('SettingID');
    $data["SSSSched"] =  request("SSSSched");
    $data["PHICSched"] =  request("PHICSched");
    $data["HDMFSched"] =  request("HDMFSched");

      if(!empty($ResponseMessage)){
        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
      }else{
        $PayrollSettingID = $PayrollSetting->doSavePayrollGovermentPremiumSetting($data);
        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Payroll setting for goverment premiums deduction schedule has saved successfully.";

      }
  
    return response()->json($RetVal);

   }


}



