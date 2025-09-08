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

class IncomeDeductionType extends Model
{

public function getIncomeDeductionTypeList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_income_deduction_type as pidt')
    ->selectraw("
        pidt.ID,
        COALESCE(pidt.Code,'') as Code,
        COALESCE(pidt.Type,'') as Type,
        COALESCE(pidt.Category,'') as Category,
        COALESCE(pidt.Name,'') as Name,
        COALESCE(pidt.Description,'') as Description,
        COALESCE(pidt.Status,'') as Status,
        COALESCE(pidt.CreatedByID,'') as CreatedByID,
        COALESCE(pidt.DateTimeCreated,'') as DateTimeCreated,
        COALESCE(pidt.UpdatedByID,'') as UpdatedByID,
        COALESCE(pidt.DateTimeUpdated,'') as DateTimeUpdated
    ");

   if($Status!=''){
        if($Status=='Active' || $Status=='Inactive'){
           $query->where("pidt.status",$Status);   
        }

        if($Status=='Non-Taxable Income' || $Status=='Taxable Income'){
           $query->where("pidt.Type",$Status);   
        }

         if($Status=='Earning' || $Status=='Deduction'){
           $query->where("pidt.Category",$Status);   
        }
    }

     if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(pidt.Code,''),
                        COALESCE(pidt.Type,''),
                        COALESCE(pidt.Category,''),
                        COALESCE(pidt.Name,''),
                        COALESCE(pidt.Description,''),
                        COALESCE(pidt.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("pidt.Name", "ASC");
    $query->orderBy("pidt.Status", "DESC");
    $list = $query->get();

    return $list;

}

public function getIncomeDeductionTypeInfo($EarningDeductionTypeID){

    $info = DB::table('payroll_income_deduction_type as pidt')
        ->selectraw("
                pidt.ID,
                COALESCE(pidt.Code,'') as Code,
                COALESCE(pidt.Type,'') as Type,
                COALESCE(pidt.Category,'') as Category,
                COALESCE(pidt.Name,'') as Name,
                COALESCE(pidt.Description,'') as Description,
                COALESCE(pidt.Status,'') as Status,
                COALESCE(pidt.CreatedByID,'') as CreatedByID,
                COALESCE(pidt.DateTimeCreated,'') as DateTimeCreated,
                COALESCE(pidt.UpdatedByID,'') as UpdatedByID,
                COALESCE(pidt.DateTimeUpdated,'') as DateTimeUpdated
        ")
    ->where("pidt.ID",$EarningDeductionTypeID)
    ->first();

    return $info;

}


public function getIncomeDeductionTypeInfoByCode($EarningDeductionTypeCode){

    $info = DB::table('payroll_income_deduction_type as pidt')
        ->selectraw("
                pidt.ID,
                COALESCE(pidt.Code,'') as Code,
                COALESCE(pidt.Type,'') as Type,
                COALESCE(pidt.Category,'') as Category,
                COALESCE(pidt.Name,'') as Name,
                COALESCE(pidt.Description,'') as Description,
                COALESCE(pidt.Status,'') as Status,
                COALESCE(pidt.CreatedByID,'') as CreatedByID,
                COALESCE(pidt.DateTimeCreated,'') as DateTimeCreated,
                COALESCE(pidt.UpdatedByID,'') as UpdatedByID,
                COALESCE(pidt.DateTimeUpdated,'') as DateTimeUpdated
        ")
    ->where("pidt.Code",$EarningDeductionTypeCode)
    ->first();

    return $info;

}

public function doSaveIncomeDeductionType($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $EarningDeductionTypeID = $data['EarningDeductionTypeID'];
    $EarningDeductionCode = trim($data['EarningDeductionCode']);
    $EarningDeductionType = $data['EarningDeductionType'];
    $EarningDeductionCategory = $data['EarningDeductionCategory'];
    $EarningDeductionName = trim($data['EarningDeductionName']);
    $EarningDeductionDescription = trim($data['EarningDeductionDescription']);
    $Status = $data['Status'];
  
    if($EarningDeductionTypeID > 0){

        DB::table('payroll_income_deduction_type')
            ->where('ID',$EarningDeductionTypeID)
            ->update([
                'Code' => trim($EarningDeductionCode),
                'Type' => $EarningDeductionType,
                'Category' => $EarningDeductionCategory,
                'Name' => ucwords($EarningDeductionName),
                'Description' => ucwords($EarningDeductionDescription),
                'Status' => $Status,
                'UpdatedByID' =>Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' => $TODAY
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $EarningDeductionTypeID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Earning Deduction Type";
        $logData['TransType'] = "Update Earning Deduction Type Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        //$EarningDeductionCode = $Misc->GenerateRandomNo(6,'payroll_incomedeductiontype','Code');
        $EarningDeductionTypeID = DB::table('payroll_income_deduction_type')
            ->insertGetId([
                'Code' => trim($EarningDeductionCode),
                'Type' => $EarningDeductionType,
                 'Category' => $EarningDeductionCategory,
                'Name' => ucwords($EarningDeductionName),
                'Description' => ucwords($EarningDeductionDescription),
                'Status' => $Status,
                'CreatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' => $TODAY
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $EarningDeductionTypeID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Earning Deduction Type";
        $logData['TransType'] = "New Earning Deduction Type";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $EarningDeductionTypeID;

    }

public function doCheckIncomeDeductionTypeCodeIfExist($param){

    $IsExist = false;
    $EarningDeductionTypeID=$param['EarningDeductionTypeID'];
    $EarningDeductionCode=$param['EarningDeductionCode'];

      $IncomeDeductionInfo = DB::table('payroll_income_deduction_type')
          ->where('Code','=',trim($EarningDeductionCode))
          ->first();

    if(isset($IncomeDeductionInfo)){
        if($EarningDeductionTypeID > 0){
          if($IncomeDeductionInfo->ID != $EarningDeductionTypeID){
            $IsExist = true;
          }
        }else{
          $IsExist = true;
        }
      }

      return $IsExist;
    }
 
 
}

