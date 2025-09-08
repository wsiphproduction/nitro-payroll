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
use App\Models\AllowanceType;

class AllowanceTypeController extends Controller {
 
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


public function showAdminAllowanceType(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Allowance Type';
  $data = $this->SetAdminInitialData($data);

  return View::make('admin/admin-allowance-type')->with($data);

}

  public function getAllowanceTypeList(Request $request){

    $AllowanceType = new AllowanceType();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");

    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
       
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AllowanceTypeList"] = $AllowanceType->getAllowanceTypeList($param);

    //Get Total Paging
    $param["SearchText"] = request("SearchText");  
    $param["Status"] =request("Status");

    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $AllowanceTypeList = $AllowanceType->getAllowanceTypeList($param);
    $RetVal["TotalRecord"] = count($AllowanceTypeList);

    return response()->json($RetVal);

  }

  public function getAllowanceTypeInfo(Request $request){

    $AllowanceType = new AllowanceType();

    $PlatformType=request("Platform");
    $AllowanceTypeID = request("AllowanceTypeID");

    $AllowanceTypeInfo = $AllowanceType->getAllowanceTypeInfo($AllowanceTypeID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["AllowanceTypeInfo"] = $AllowanceTypeInfo;

    return response()->json($RetVal);
  }

   public function doSaveAllowanceType(Request $request) {
       
    $AllowanceType = new AllowanceType();

    $ResponseMessage = "";
    $data["AllowanceTypeID"] =  request('AllowanceTypeID');
    $data["AllowanceTypeCode"] =  str_replace(' ','',request('AllowanceTypeCode'));
    $data["AllowanceType"] =  request("AllowanceType");
    $data["AllowanceTypeName"] =  request("AllowanceTypeName");
    $data["AllowanceTypeDescription"] =  request("AllowanceTypeDescription");
    $data["Status"] =  request("Status");

    if(strlen($data["AllowanceTypeCode"])>10){
       $ResponseMessage= "Allowance type code must not exceeds in 10 characters.";
    }

     if($AllowanceType->doCheckAllowanceTypeCodeIfExist($data)){
        $ResponseMessage= "Allowance type code is already used.";
    }

    if(!empty($ResponseMessage)){

      $RetVal['Response'] = "Failed";
      $RetVal['ResponseMessage'] = $ResponseMessage;
      $RetVal["AllowanceTypeInfo"] = null;

    }else{

      $AllowanceTypeID = $AllowanceType->doSaveAllowanceType($data);

      $RetVal['Response'] = "Success";
      $RetVal['ResponseMessage'] = "Allowance type has saved successfully.";
      $RetVal["AllowanceTypeInfo"] =  $AllowanceType->getAllowanceTypeInfo($AllowanceTypeID);

    }

    return response()->json($RetVal);

  }

  public function getAllowanceTypeSearchList(Request $request){

    $AllowanceType = new AllowanceType();

    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = 'Active';

    $AllowanceTypeList = $AllowanceType->getAllowanceTypeList($param);

    $RetVal =array();
    foreach($AllowanceTypeList as $row)
    { 

        $data = $row->ID.'|'.
        $row->Code.'|'. 
        $row->Name;      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }

}



