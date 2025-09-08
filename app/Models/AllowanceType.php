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

class AllowanceType extends Model
{
    
public function getAllowanceTypeList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_allowance_type as alw')
    ->selectraw("
        alw.ID,
        COALESCE(alw.IsTaxable,'') as IsTaxable,
        COALESCE(alw.Code,'') as Code,
        COALESCE(alw.Name,'') as Name,
        COALESCE(alw.Description,'') as Description,
        COALESCE(alw.CreatedByID,'') as CreatedByID,
        COALESCE(alw.DateTimeCreated,'') as DateTimeCreated,
        COALESCE(alw.UpdatedByID,'') as UpdatedByID,
        COALESCE(alw.DateTimeUpdated,'') as DateTimeUpdated,
        COALESCE(alw.Status,'') as Status
    ");

    if($Status!=''){
        if($Status=='Active' || $Status=='Inactive'){
           $query->where("alw.status",$Status);   
        }

        if($Status=='0' || $Status=='1'){
           $query->where("alw.IsTaxable",$Status);   
        }
        
    }

    if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS('',                        
                        COALESCE(alw.Code,''),
                        COALESCE(alw.Name,''),
                        COALESCE(alw.Description,''),
                        COALESCE(alw.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("alw.Name", "ASC");
    $query->orderBy("alw.Status", "DESC");
    $list = $query->get();

    return $list;

}

public function getAllowanceTypeInfo($AllowanceTypeID){

    $info = DB::table('payroll_allowance_type as alw')
        ->selectraw("
            alw.ID,
            COALESCE(alw.IsTaxable,'') as IsTaxable,
            COALESCE(alw.Code,'') as Code,
            COALESCE(alw.Name,'') as Name,
            COALESCE(alw.Description,'') as Description,
            COALESCE(alw.CreatedByID,'') as CreatedByID,
            COALESCE(alw.DateTimeCreated,'') as DateTimeCreated,
            COALESCE(alw.UpdatedByID,'') as UpdatedByID,
            COALESCE(alw.DateTimeUpdated,'') as DateTimeUpdated,
            COALESCE(alw.Status,'') as Status
        ")
    ->where("alw.ID",$AllowanceTypeID)
    ->first();

    return $info;

}

public function getAllowanceTypeInfoByCode($AllowanceTypeCode){

    $info = DB::table('payroll_allowance_type as alw')
        ->selectraw("
            alw.ID,
            COALESCE(alw.IsTaxable,'') as IsTaxable,
            COALESCE(alw.Code,'') as Code,
            COALESCE(alw.Name,'') as Name,
            COALESCE(alw.Description,'') as Description,
            COALESCE(alw.CreatedByID,'') as CreatedByID,
            COALESCE(alw.DateTimeCreated,'') as DateTimeCreated,
            COALESCE(alw.UpdatedByID,'') as UpdatedByID,
            COALESCE(alw.DateTimeUpdated,'') as DateTimeUpdated,
            COALESCE(alw.Status,'') as Status
        ")
    ->where("alw.Code",$AllowanceTypeCode)
    ->first();

    return $info;

}

public function doSaveAllowanceType($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $AllowanceTypeID = $data['AllowanceTypeID'];
    $AllowanceType = trim($data['AllowanceType']);
    $AllowanceTypeCode = trim($data['AllowanceTypeCode']);
    $AllowanceTypeName = trim($data['AllowanceTypeName']);
    $AllowanceTypeDescription = trim($data['AllowanceTypeDescription']);
    $Status = $data['Status'];
  
    if($AllowanceTypeID > 0){

        DB::table('payroll_allowance_type')
            ->where('ID',$AllowanceTypeID)
            ->update([
                'IsTaxable' => $AllowanceType,
                'Code' => trim($AllowanceTypeCode),
                'Name' => ucwords($AllowanceTypeName),
                'Description' => ucwords($AllowanceTypeDescription),
                'UpdatedByID' =>  Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' =>$TODAY,
                'Status' => trim($Status)
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $AllowanceTypeID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Allowance Type";
        $logData['TransType'] = "Update Allowance Type Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        //$AllowanceTypeCode = $Misc->GenerateRandomNo(6,'payroll_allowance_type','Code');
        $AllowanceTypeID = DB::table('payroll_allowance_type')
            ->insertGetId([
                'IsTaxable' => $AllowanceType,
                'Code' => trim($AllowanceTypeCode),
                'Name' => ucwords($AllowanceTypeName),
                'Description' => ucwords($AllowanceTypeDescription),
                'CreatedByID' =>  Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' =>$TODAY,
                'Status' => trim($Status)
              ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $AllowanceTypeID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Allowance Type";
        $logData['TransType'] = "New Allowance Type";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $AllowanceTypeID;

    }

  public function doCheckAllowanceTypeCodeIfExist($param){

    $IsExist = false;
    $AllowanceTypeID=$param['AllowanceTypeID'];
    $AllowanceTypeCode=$param['AllowanceTypeCode'];

      $AllowanceTypeInfo = DB::table('payroll_allowance_type')
          ->where('Code','=',trim($AllowanceTypeCode))
          ->first();

    if(isset($AllowanceTypeInfo)){
        if($AllowanceTypeID > 0){
          if($AllowanceTypeInfo->ID != $AllowanceTypeID){
            $IsExist = true;
          }
        }else{
          $IsExist = true;
        }
      }

      return $IsExist;
    }


}

