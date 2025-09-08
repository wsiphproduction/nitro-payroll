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
use App\Models\IncomeDeductionType;

class IncomeDeductionTypeController extends Controller {
 
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


public function showAdminEarningDeductionType(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Income/Deduction Type';
  $data = $this->SetAdminInitialData($data);

  return View::make('admin/admin-income-deduction-type')->with($data);

}

  public function getEarningDeductionTypeList(Request $request){

    $IncomeDeductionType = new IncomeDeductionType();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");

    $param["PageNo"] = request("PageNo");
    $param["Limit"] =  request("Limit");
    // $param["Limit"] = config('app.ListRowLimit');
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["IncomeDeductionTypeList"] = $IncomeDeductionType->getIncomeDeductionTypeList($param);

    //Get Total Paging
    $param["SearchText"] = request("SearchText");  
    $param["Status"] =request("Status");

    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $IncomeDeductionTypeList = $IncomeDeductionType->getIncomeDeductionTypeList($param);
    $RetVal["TotalRecord"] = count($IncomeDeductionTypeList);

    return response()->json($RetVal);

  }

  public function getIncomeDeductionTypeInfo(Request $request){

    $IncomeDeductionType = new IncomeDeductionType();

    $PlatformType= request("Platform");
    $IncomeDeductionID = request("IncomeDeductionID");

    $IncomeDeductionTypeInfo = $IncomeDeductionType->getIncomeDeductionTypeInfo($IncomeDeductionID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["IncomeDeductionTypeInfo"] = $IncomeDeductionTypeInfo;

    return response()->json($RetVal);
  }

   public function doSaveIncomeDeductionType(Request $request){
       
    $IncomeDeductionType = new IncomeDeductionType();
 
    $ResponseMessage = "";
    $data["EarningDeductionTypeID"] =  request('EarningDeductionTypeID');
    $data["EarningDeductionCode"] =   str_replace(' ','',request('EarningDeductionCode'));
    $data["EarningDeductionType"] =  request("EarningDeductionType");
    $data["EarningDeductionCategory"] =  request("EarningDeductionCategory");
    $data["EarningDeductionName"] =  request("EarningDeductionName");
    $data["EarningDeductionDescription"] =  request("EarningDeductionDescription");
    $data["Status"] =  request("Status");

    if(strlen($data["EarningDeductionCode"])>10){
       $ResponseMessage= "Income & Deduction Code type code must not exceeds in 10 characters.";
    }


    if($IncomeDeductionType->doCheckIncomeDeductionTypeCodeIfExist($data)){
      $ResponseMessage= "Income & deduction type code is already used.";
    }

    if(!empty($ResponseMessage)){

      $RetVal['Response'] = "Failed";
      $RetVal['ResponseMessage'] = $ResponseMessage;
      $RetVal["IncomeDeductionTypeInfo"] = null;

    }else{

      $LoanTypeID = $IncomeDeductionType->doSaveIncomeDeductionType($data);

      $RetVal['Response'] = "Success";
      $RetVal['ResponseMessage'] = "Income/deduction type has saved successfully.";
      $RetVal["IncomeDeductionTypeInfo"] =  $IncomeDeductionType->getIncomeDeductionTypeInfo($LoanTypeID);

    }
  
    return response()->json($RetVal);

  }

   public function getIncomeDeductionTypeSearchList(Request $request){

    $IncomeDeductionType = new IncomeDeductionType();

    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = 'Active';

    $IncomeDeductionTypeList = $IncomeDeductionType->getIncomeDeductionTypeList($param);

    $RetVal =array();
    foreach($IncomeDeductionTypeList as $row)
    { 

        $data = $row->ID.'|'.
        $row->Code.'|'. 
        $row->Name.'|'. 
        $row->Category;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }

  

}



