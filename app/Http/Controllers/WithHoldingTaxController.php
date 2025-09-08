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
use App\Models\WithHoldingTaxTable;

class WithHoldingTaxController extends Controller {
 
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


public function showAdminWithholdingTaxTableBracket(Request $request){

  if(!$this->IsAdminLoggedIn()){
    return Redirect::route('admin-logout');
  }

  $data['Page'] = 'WithHolding Tax Table';
  $data = $this->SetAdminInitialData($data);

  return View::make('admin/admin-wihthholding-tax-table-bracket')->with($data);

}

  public function getWithholdingTaxTableBracketList(Request $request){

    $WithHoldingTaxTable = new WithHoldingTaxTable();

    $param["SearchText"] = request("SearchText");  
    $param["Status"] = request("Status");
    $param["PageNo"] = request("PageNo");
    $param["Limit"] = config('app.ListRowLimit');
   
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["WithHoldingTaxTableBracketList"] = $WithHoldingTaxTable->getWithHoldingTaxTableBracketList($param);

    //Get Total Paging
    $param["SearchText"] = request("SearchText");  
    $param["Status"] =request("Status");

    $param["PageNo"] = 0;
    $param["Limit"] = 0;
    $WithHoldingTaxTableBracketList = $WithHoldingTaxTable->getWithHoldingTaxTableBracketList($param);
    $RetVal["TotalRecord"] = count($WithHoldingTaxTableBracketList);

    return response()->json($RetVal);

  }

  public function getWithholdingTaxBracketInfo(Request $request){

    $WithHoldingTaxTable = new WithHoldingTaxTable();

    $PlatformType=request("Platform");
    $WithHoldingID = request("WithHoldingID");

    $WithholdingTaxInfo = $WithHoldingTaxTable->getWithholdingTaxBracketInfo($WithHoldingID);

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    $RetVal["WithholdingTaxInfo"] = $WithholdingTaxInfo;

    return response()->json($RetVal);
  }

   public function doSaveWithHoldingTaxTableBracket(Request $request){
       
    $WithHoldingTaxTable = new WithHoldingTaxTable();

    $ResponseMessage = "";
    $data["WithholdingTaxID"] =  request('WithholdingTaxID');
    $data["SalaryFrom"] =  request("SalaryFrom");
    $data["SalaryTo"] =  request("SalaryTo");
    $data["PayrollFrequency"] =  request("PayrollFrequency");
    $data["FixTax"] =  request("FixTax");
    $data["RateonExcessPercent"] =  request("RateonExcessPercent");
    $data["Status"] =  request("Status");

      if(!empty($ResponseMessage)){

        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
        $RetVal["WithholdingTaxInfo"] = null;

      }else{

        $WithHoldingID = $WithHoldingTaxTable->doSaveWithHoldingTaxTableBracket($data);

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Withholding tax table bracket has saved successfully.";
        $RetVal["WithholdingTaxInfo"] =  $WithHoldingTaxTable->getWithholdingTaxBracketInfo($WithHoldingID);

      }
  
    return response()->json($RetVal);

  }



}



