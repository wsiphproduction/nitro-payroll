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
use App\Models\Division;

class DivisionController extends Controller {
 

  public function getDivisionSearchList(Request $request){

    $Division = new Division();

    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = 'Open';

    $DivisionList = $Division->getDivisionList($param);

    $RetVal =array();
    foreach($DivisionList as $row)
    { 

        $data = $row->ID.'|'.
        $row->Division;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }

  public function postDivisionInfo(Request $request){

    $Division = new Division();

    $ResponseMessage = "";
    $data["id"] =  request('id');
    $data["company_id"] = request('company_id');

    $data["name"] = request('name');
    $data["code"] = request('code');

    $data["manager_id"] = request('manager_id');
    $data["assistant_manager_id"] = request('assistant_manager_id');
    $data["secretary_id"] = request('secretary_id');

    $data["description"] = request('description');

    $data["status"] = request('status');

    $data["deletedAt"] = (empty(request('deletedAt')) ?  null : request('deletedAt'));
    $data["createdAt"] = (empty(request('createdAt')) ?  null : request('createdAt'));
    $data["updatedAt"] = (empty(request('updatedAt')) ?  null : request('updatedAt'));
    
    $data["alternate_secretary"] = request('alternate_secretary');

    if(empty($data["name"])){
       $ResponseMessage= "Division name is empty.";
    }

    if(!empty($ResponseMessage)){
        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
    }else{
        $RetVal['Response'] = $Division->postDivisionInfo($data);
        $RetVal['ResponseMessage'] = "Division has saved successfully.";
    }
  
    return response()->json($RetVal);

  }


}



