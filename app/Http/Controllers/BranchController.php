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
use App\Models\Branch;

class BranchController extends Controller {
 

  public function getBranchSearchList(Request $request){

    $Branch = new Branch();

    $param["SearchText"] = $request["SearchText"];
    $param["Limit"] = config('app.ListRowLimit');
    $param["PageNo"] = $request["PageNo"];
    $param["Status"] = 'Open';

    $BranchList = $Branch->getBranchList($param);

    $RetVal =array();
    foreach($BranchList as $row)
    { 

        $data = $row->ID.'|'.
        $row->BranchName;
      
      array_push($RetVal, $data);
    }

    return response()->json($RetVal);

  }




}



