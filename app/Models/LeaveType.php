<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Session;
use Hash;
use View;
use Input;
use Image;
use DB;

class LeaveType extends Model
{
    
public function getLeaveTypeList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $query = DB::table('payroll_leave_type as ltype')
    ->selectraw("
        ltype.ID,
        COALESCE(ltype.Code,'') as Code,        
        COALESCE(ltype.LeaveType,'') as LeaveType
    ");

    if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS('',                        
                        COALESCE(ltype.Code,''),
                        COALESCE(ltype.LeaveType,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("ltype.ID", "ASC");
    $list = $query->get();

    return $list;

}

public function getLeaveTypeInfo($ID){

    $info = DB::table('payroll_leave_type as ltype')
    ->selectraw("
        ltype.ID,
        COALESCE(ltype.Code,'') as Code,        
        COALESCE(ltype.LeaveType,'') as LeaveType
        ")
    ->where("ltype.ID",$ID)
    ->first();

    return $info;

}

public function getLeaveTypeByID($ID, $vList){

    $LeaveType = "";

    foreach ($vList as $key) {
        if($key->ID == $ID){
            $LeaveType = $key->Code;
        }
    }

    return $LeaveType;

}





}

