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
use App\Models\SSSTable;

class SSSTableController extends Controller {
 

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


public function showAdminSSSTableBracket(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'SSS Table';
  $data = $this->SetAdminInitialData($data);

  return View::make('admin/admin-sss-table-bracket')->with($data);

}

  public function getSSSTableBracketList(Request $request){

    $SSSTable = new SSSTable();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");

    $param["PageNo"] = request("PageNo");
    $param["Limit"] = request("Limit");
    // $param["Limit"] = config('app.ListRowLimit');
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["SSSTableBracketList"] = $SSSTable->getSSSTableBracketList($param);

    //Get Total Paging
    $param["SearchText"] = request("SearchText");  
    $param["Status"] =request("Status");

    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $SSSTableBracketList = $SSSTable->getSSSTableBracketList($param);
    $RetVal["TotalRecord"] = count($SSSTableBracketList);

    return response()->json($RetVal);

  }

  public function getSSSTableBracketnfo(Request $request){

    $SSSTable = new SSSTable();

    $PlatformType=request("Platform");
    $SSS_ID = request("SSS_ID");

    $SSSTableBracketInfo = $SSSTable->gettSSSTableBracketInfo($SSS_ID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["SSSTableBracketInfo"] = $SSSTableBracketInfo;

    return response()->json($RetVal);
  }

   public function doSaveSSSTableBracket(Request $request){
       
    $SSSTable = new SSSTable();

    $ResponseMessage = "";
    $data["SSS_ID"] =  request("SSS_ID");
    $data["SalaryFrom"] =  request("SalaryFrom");
    $data["SalaryTo"] =  request("SalaryTo");

    $data["RegularSSEC"] =  request("RegularSSEC");
    $data["RegularSSWISP"] =  request("RegularSSWISP");
    $data["RegularSSECWISPTotal"] =  request("RegularSSECWISPTotal");

    $data["RegularEE"] =  request("RegularEE");
    $data["RegularER"] =  request("RegularER");
    $data["RegularTotal"] =  request("RegularTotal");
    
    $data["ECEE"] =  request("ECEE");
    $data["ECER"] =  request("ECER");
    $data["ECTotal"] =  request("ECTotal");
    
    $data["WISPEE"] =  request("WISPEE");
    $data["WISPER"] =  request("WISPER");
    $data["WISPTotal"] =  request("WISPTotal");

    $data["Year"] =  request("Year");
    $data["Status"] =  request("Status");

      if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["SSSTableBracketInfo"] = null;

      }else{

        $SSS_ID = $SSSTable->doSaveSSSTableBracket($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "SSS table bracket has saved successfully.";
        $RetVal["SSSTableBracketInfo"] =  $SSSTable->gettSSSTableBracketInfo($SSS_ID);

      }
  
    return response()->json($RetVal);

  }

}



