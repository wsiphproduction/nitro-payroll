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
use App\Models\OTRate;
use App\Models\AdminUsers;

class OTRateController extends Controller {
 
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


public function showAdminOTRates(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'OT Rates';
  $data = $this->SetAdminInitialData($data);

  return View::make('admin/admin-ot-rates')->with($data);

}

  public function getOTRateList(Request $request){

    $OTRate = new OTRate();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");

    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
    // $param["Limit"] = config('app.ListRowLimit');
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["OTRateList"] = $OTRate->getOTRateList($param);

    //Get Total Paging
    $param["SearchText"] = request("SearchText");  
    $param["Status"] =request("Status");

    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $LoanTypeList = $OTRate->getOTRateList($param);
    $RetVal["TotalRecord"] = count($LoanTypeList);

    return response()->json($RetVal);

  }

  public function getOTRateInfo(Request $request){

    $OTRate = new OTRate();

    $PlatformType=request("Platform");
    $OTRateID = request("OTRateID");

    $OTRateInfo = $OTRate->getOTRateInfo($OTRateID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["OTRateInfo"] = $OTRateInfo;

    return response()->json($RetVal);
  }

   public function doSaveOTRate(Request $request) {
       
    $OTRate = new OTRate();

    $ResponseMessage = "";
    $data["OTRateID"] =  request('OTRateID');
    $data["OTRateCode"] =  str_replace(' ','',request('OTRateCode'));    
    $data["OTRateName"] =  request("OTRateName");
    $data["IsOTND"] =  request("IsOTND");
    $data["OTRateDescription"] =  request("OTRateDescription");

    $data["OTRate"] =  request("OTRate");
    $data["OTDailyRate"] =  request("OTDailyRate");
    
    $data["Status"] =  request("Status");

    if(strlen($data["OTRateCode"])>10){
       $ResponseMessage= "OT rate code must not exceeds in 10 characters.";
    }

     if($OTRate->doCheckOTRateCodeIfExist($data)){
        $ResponseMessage= "OT rate code is already used.";
    }

    if(!empty($ResponseMessage)){

      $RetVal['Response'] = "Failed";
      $RetVal['ResponseMessage'] = $ResponseMessage;
      $RetVal["OTRateInfo"] = null;

    }else{

      $OTRateID = $OTRate->doSaveOTRate($data);

      $RetVal['Response'] = "Success";
      $RetVal['ResponseMessage'] = "OT rate has saved successfully.";
      $RetVal["OTRateInfo"] =  $OTRate->getOTRateInfo($OTRateID);

    }

    return response()->json($RetVal);

  }

  public function getOTRateSearchList(Request $request){

    $OTRate = new OTRate();

    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = 'Active';

    $OTRateList = $OTRate->getOTRateList($param);

    $RetVal =array();
    foreach($OTRateList as $row)
    { 

        $data = $row->ID.'|'.
        $row->Code.'|'. 
        $row->Name;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }

}



