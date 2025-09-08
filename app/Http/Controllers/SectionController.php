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
use App\Models\Section;

class SectionController extends Controller {
 

  public function getSectionSearchList(Request $request){

    $Section = new Section();

    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = 'Open';

    $SectionList = $Section->getSectionList($param);

    $RetVal =array();
    foreach($SectionList as $row)
    { 

        $data = $row->ID.'|'.
        $row->Section.'|'.
        $row->DepartmentID.'|'.
        $row->Department.'|'.
        $row->DivisionID.'|'.
        $row->Division;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }

  public function postSectionInfo(Request $request){

    $Section = new Section();

    $ResponseMessage = "";
    $data["id"] =  request('id');
    $data["department_id"] = request('department_id');

    $data["section_name"] = request('section_name');
    $data["section_code"] = request('section_code');

    $data["supervisor_id"] = request('supervisor_id');
    $data["assistant_supervisor_id"] = request('assistant_supervisor_id');
    $data["secretary_id"] = request('secretary_id');

    $data["description"] = request('description');

    $data["status"] = request('status');

    $data["deletedAt"] = (empty(request('deletedAt')) ?  null : request('deletedAt'));
    $data["createdAt"] = (empty(request('createdAt')) ?  null : request('createdAt'));
    $data["updatedAt"] = (empty(request('updatedAt')) ?  null : request('updatedAt'));

    $data["alternate_secretary"] = request('alternate_secretary');

    if(empty($data["section_name"])){
       $ResponseMessage= "Section name is empty.";
    }

    if(!empty($ResponseMessage)){
        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
    }else{
        $RetVal['Response'] = $Section->postSectionInfo($data);
        $RetVal['ResponseMessage'] = "Section has saved successfully.";
    }
  
    return response()->json($RetVal);

  }



}



