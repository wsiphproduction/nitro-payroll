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
use App\Models\Department;

class DepartmentController extends Controller {
 

  public function getDepartmentSearchList(Request $request){

    $Department = new Department();

    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = 'Open';

    $DepartmentList = $Department->getDepartmentList($param);

    $RetVal =array();
    foreach($DepartmentList as $row)
    { 

        $data = $row->ID.'|'.
        $row->Department.'|'.
        $row->DivisionID.'|'.
        $row->Division;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }

  public function postDepartmentInfo(Request $request){

    $Department = new Department();

    $ResponseMessage = "";
    $data["id"] =  request('id');
    $data["division_id"] = request('division_id');

    $data["department_name"] = request('department_name');
    $data["department_code"] = request('department_code');

    $data["manager_id"] = request('manager_id');
    $data["assistant_manager_id"] = request('assistant_manager_id');
    $data["secretary_id"] = request('secretary_id');

    $data["description"] = request('description');

    $data["status"] = request('status');

    $data["deletedAt"] = (empty(request('deletedAt')) ?  null : request('deletedAt'));
    $data["createdAt"] = (empty(request('createdAt')) ?  null : request('createdAt'));
    $data["updatedAt"] = (empty(request('updatedAt')) ?  null : request('updatedAt'));
    
    $data["alternate_secretary"] = request('alternate_secretary');
    $data["hr_generalist_id"] = request('hr_generalist_id');

    if(empty($data["department_name"])){
       $ResponseMessage= "Department name is empty.";
    }

    if(!empty($ResponseMessage)){
        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
    }else{
        $RetVal['Response'] = $Department->postDepartmentInfo($data);
        $RetVal['ResponseMessage'] = "Department has saved successfully.";
    }
  
    return response()->json($RetVal);

  }



}



