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
use App\Models\JobType;

class JobTypeController extends Controller {
 

  public function getJobTypeSearchList(Request $request){

    $JobType = new JobType();

    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = 'Open';

    $JobTypeList = $JobType->getJobTypeList($param);

    $RetVal =array();
    foreach($JobTypeList as $row)
    { 

        $data = $row->ID.'|'.
        $row->JobTitle;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }

  public function postJobTypeInfo(Request $request){

    $JobType = new JobType();

    $ResponseMessage = "";
    $data["id"] =  request('id');

    $data["name"] = request('name');

    $data["createdAt"] = (empty(request('createdAt')) ?  null : request('createdAt'));
    $data["updatedAt"] = (empty(request('updatedAt')) ?  null : request('updatedAt'));
    
    if(empty($data["name"])){
       $ResponseMessage= "Job position is empty.";
    }

    if(!empty($ResponseMessage)){
        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = $ResponseMessage;
    }else{
        $RetVal['Response'] = $JobType->postJobTypeInfo($data);
        $RetVal['ResponseMessage'] = "Job position has saved successfully.";
    }
  
    return response()->json($RetVal);

  }




}



