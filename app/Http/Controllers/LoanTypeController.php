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
use App\Models\LoanType;

class LoanTypeController extends Controller {
 
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


public function showAdminLoanType(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'Loan Type';
  $data = $this->SetAdminInitialData($data);

  return View::make('admin/admin-loan-type')->with($data);

}

  public function getLoanTypeList(Request $request){

    $LoanType = new LoanType();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");

    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
    // $param["Limit"] = config('app.ListRowLimit');
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["LoanTypeList"] = $LoanType->getLoanTypeList($param);

    //Get Total Paging
    $param["SearchText"] = request("SearchText");  
    $param["Status"] =request("Status");

    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $LoanTypeList = $LoanType->getLoanTypeList($param);
    $RetVal["TotalRecord"] = count($LoanTypeList);

    return response()->json($RetVal);

  }

  public function getLoanTypeInfo(Request $request){

    $LoanType = new LoanType();

    $PlatformType=request("Platform");
    $LoanTypeID = request("LoanTypeID");

    $LoanTypeInfo = $LoanType->getLoanTypeInfo($LoanTypeID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["LoanTypeInfo"] = $LoanTypeInfo;

    return response()->json($RetVal);
  }

   public function doSaveLoanType(Request $request) {
       
    $LoanType = new LoanType();

    $ResponseMessage = "";
    $data["LoanTypeID"] =  request('LoanTypeID');
    $data["LoanTypeCode"] =  str_replace(' ','',request('LoanTypeCode'));
    $data["LoanType"] =  request("LoanType");
    $data["LoanTypeName"] =  request("LoanTypeName");
    $data["LoanTypeDescription"] =  request("LoanTypeDescription");
    $data["Status"] =  request("Status");

    if(strlen($data["LoanTypeCode"])>10){
       $ResponseMessage= "Loan type code must not exceeds in 10 characters.";
    }

     if($LoanType->doCheckLoanTypeCodeIfExist($data)){
        $ResponseMessage= "Loan type code is already used.";
    }

    if(!empty($ResponseMessage)){

      $RetVal['Response'] = "Failed";
      $RetVal['ResponseMessage'] = $ResponseMessage;
      $RetVal["LoanTypeInfo"] = null;

    }else{

      $LoanTypeID = $LoanType->doSaveLaonType($data);

      $RetVal['Response'] = "Success";
      $RetVal['ResponseMessage'] = "Loan type has saved successfully.";
      $RetVal["LoanTypeInfo"] =  $LoanType->getLoanTypeInfo($LoanTypeID);

    }

    return response()->json($RetVal);

  }

  public function getLoanTypeSearchList(Request $request){

    $LoanType = new LoanType();

    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = 'Active';

    $LoanTypeList = $LoanType->getLoanTypeList($param);

    $RetVal =array();
    foreach($LoanTypeList as $row)
    { 

        $data = $row->ID.'|'.
        $row->Code.'|'. 
        $row->Name;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }

}



