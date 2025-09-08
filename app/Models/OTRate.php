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

class OTRate extends Model
{
    
public function getOTRateList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_ot_rates as ot_rate')
    ->selectraw("
        ot_rate.ID,
        COALESCE(ot_rate.Code,'') as Code,        
        COALESCE(ot_rate.Name,'') as Name,
        COALESCE(ot_rate.Description,'') as Description,
        COALESCE(ot_rate.Rate,0) as Rate,
        COALESCE(ot_rate.IsOTND,0) as IsOTND,
        COALESCE(ot_rate.DailyRate,0) as DailyRate,
        COALESCE(ot_rate.Status,'') as Status
    ");

    if($Status!=''){
        if($Status==1 || $Status==0){
           $query->where("ot_rate.status",$Status);   
        }
    }

    if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS('',                        
                        COALESCE(ot_rate.Code,''),
                        COALESCE(ot_rate.Name,''),
                        COALESCE(ot_rate.Description,''),
                        COALESCE(ot_rate.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("ot_rate.Name", "ASC");
    $query->orderBy("ot_rate.Status", "DESC");
    $list = $query->get();

    return $list;

}

public function getOTRateInfo($OTRateID){

    $info = DB::table('payroll_ot_rates as ot_rate')
        ->selectraw("
        ot_rate.ID,
        COALESCE(ot_rate.Code,'') as Code,        
        COALESCE(ot_rate.Name,'') as Name,
        COALESCE(ot_rate.Description,'') as Description,
        COALESCE(ot_rate.Rate,0) as Rate,
        COALESCE(ot_rate.IsOTND,0) as IsOTND,
        COALESCE(ot_rate.DailyRate,0) as DailyRate,
        COALESCE(ot_rate.Status,'') as Status
        ")
    ->where("ot_rate.ID",$OTRateID)
    ->first();

    return $info;

}

public function getOTRateInfoByCode($OTRateCode){

    $info = DB::table('payroll_ot_rates as ot_rate')
        ->selectraw("
        ot_rate.ID,
        COALESCE(ot_rate.Code,'') as Code,        
        COALESCE(ot_rate.Name,'') as Name,
        COALESCE(ot_rate.Description,'') as Description,
        COALESCE(ot_rate.Rate,0) as Rate,
        COALESCE(ot_rate.IsOTND,0) as IsOTND,
        COALESCE(ot_rate.DailyRate,0) as DailyRate,
        COALESCE(ot_rate.Status,'') as Status
        ")
    ->where("ot_rate.Code",$OTRateCode)
    ->first();

    return $info;

}

public function doSaveOTRate($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");

    $OTRateID = $data['OTRateID'];    
    $OTRateCode = trim($data['OTRateCode']);
    $OTRateName = trim($data['OTRateName']);
    $IsOTND = $data['IsOTND'];
    $OTRateDescription = trim($data['OTRateDescription']);

    $OTRate =  $Misc->setNumeric($data['OTRate']);
    $OTDailyRate =  $Misc->setNumeric($data['OTDailyRate']);

    $Status = $data['Status'];
  
    if($OTRateID > 0){

        DB::table('payroll_ot_rates')
            ->where('ID',$OTRateID)
            ->update([                
                'Code' => trim($OTRateCode),
                'Name' => ucwords($OTRateName),
                'Description' => ucwords($OTRateDescription),
                'Rate' => $OTRate,
                'IsOTND' => $IsOTND,
                'DailyRate' => $OTDailyRate,
                'Status' => trim($Status)
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $OTRateID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "OT Rates";
        $logData['TransType'] = "Update OT Rate Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        //$OTRateCode = $Misc->GenerateRandomNo(6,'payroll_loan_type','Code');
        $OTRateID = DB::table('payroll_ot_rates')
            ->insertGetId([                
                'Code' => trim($OTRateCode),
                'Name' => ucwords($OTRateName),
                'Description' => ucwords($OTRateDescription),
                'Rate' => $OTRate,
                'IsOTND' => $IsOTND,
                'DailyRate' => $OTDailyRate,
                'Status' => trim($Status)
              ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $OTRateID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "OT Rate";
        $logData['TransType'] = "New OT Rate";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $OTRateID;

    }

  public function doCheckOTRateCodeIfExist($param){

    $IsExist = false;
    $OTRateID=$param['OTRateID'];
    $OTRateCode=$param['OTRateCode'];

      $OTRateInfo = DB::table('payroll_ot_rates')
          ->where('Code','=',trim($OTRateCode))
          ->first();

    if(isset($OTRateInfo)){
        if($OTRateID > 0){
          if($OTRateInfo->ID != $OTRateID){
            $IsExist = true;
          }
        }else{
          $IsExist = true;
        }
      }

      return $IsExist;
    }

    public function getOTByID($ID, $vList){

        $OT = "";

        foreach ($vList as $key) {
            if($key->ID == $ID){
                $OT = $key->Code;
            }
        }

        return $OT;

    }


}

