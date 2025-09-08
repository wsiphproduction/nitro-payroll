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

class LoanType extends Model
{
    
public function getLoanTypeList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_loan_type as plt')
    ->selectraw("
        plt.ID,
        COALESCE(plt.Type,'') as Type,
        COALESCE(plt.Code,'') as Code,
        COALESCE(plt.Name,'') as Name,
        COALESCE(plt.Description,'') as Description,
        COALESCE(plt.CreatedByID,'') as CreatedByID,
        COALESCE(plt.DateTimeCreated,'') as DateTimeCreated,
        COALESCE(plt.UpdatedByID,'') as UpdatedByID,
        COALESCE(plt.DateTimeUpdated,'') as DateTimeUpdated,
        COALESCE(plt.Status,'') as Status
    ");

    if($Status!=''){
        if($Status=='Active' || $Status=='Inactive'){
           $query->where("plt.status",$Status);   
        }

        if($Status=='Non-Taxable Income' || $Status=='Taxable Income'){
           $query->where("plt.Type",$Status);   
        }
        
    }

    if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS('',
                        COALESCE(plt.Type,''),
                        COALESCE(plt.Code,''),
                        COALESCE(plt.Name,''),
                        COALESCE(plt.Description,''),
                        COALESCE(plt.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("plt.Name", "ASC");
    $query->orderBy("plt.Status", "DESC");
    $list = $query->get();

    return $list;

}

public function getLoanTypeInfo($LoanTypeID){

    $info = DB::table('payroll_loan_type as plt')
        ->selectraw("
            plt.ID,
            COALESCE(plt.Type,'') as Type,
            COALESCE(plt.Code,'') as Code,
            COALESCE(plt.Name,'') as Name,
            COALESCE(plt.Description,'') as Description,
            COALESCE(plt.CreatedByID,'') as CreatedByID,
            COALESCE(plt.DateTimeCreated,'') as DateTimeCreated,
            COALESCE(plt.UpdatedByID,'') as UpdatedByID,
            COALESCE(plt.DateTimeUpdated,'') as DateTimeUpdated,
            COALESCE(plt.Status,'') as Status
        ")
    ->where("plt.ID",$LoanTypeID)
    ->first();

    return $info;

}

public function getLoanTypeInfoByCode($LoanTypeCode){

    $info = DB::table('payroll_loan_type as plt')
        ->selectraw("
            plt.ID,
            COALESCE(plt.Type,'') as Type,
            COALESCE(plt.Code,'') as Code,
            COALESCE(plt.Name,'') as Name,
            COALESCE(plt.Description,'') as Description,
            COALESCE(plt.CreatedByID,'') as CreatedByID,
            COALESCE(plt.DateTimeCreated,'') as DateTimeCreated,
            COALESCE(plt.UpdatedByID,'') as UpdatedByID,
            COALESCE(plt.DateTimeUpdated,'') as DateTimeUpdated,
            COALESCE(plt.Status,'') as Status
        ")
    ->where("plt.Code",$LoanTypeCode)
    ->first();

    return $info;

}

public function doSaveLaonType($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $LoanTypeID = $data['LoanTypeID'];
    $LoanType = trim($data['LoanType']);
    $LoanTypeCode = trim($data['LoanTypeCode']);
    $LoanTypeName = trim($data['LoanTypeName']);
    $LoanTypeDescription = trim($data['LoanTypeDescription']);
    $Status = $data['Status'];
  
    if($LoanTypeID > 0){

        DB::table('payroll_loan_type')
            ->where('ID',$LoanTypeID)
            ->update([
                'Type' => $LoanType,
                'Code' => trim($LoanTypeCode),
                'Name' => ucwords($LoanTypeName),
                'Description' => ucwords($LoanTypeDescription),
                'UpdatedByID' =>  Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' =>$TODAY,
                'Status' => trim($Status)
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $LoanTypeID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Loan Type";
        $logData['TransType'] = "Update Laon Type Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        //$LoanTypeCode = $Misc->GenerateRandomNo(6,'payroll_loan_type','Code');
        $LoanTypeID = DB::table('payroll_loan_type')
            ->insertGetId([
                'Type' => $LoanType,
                'Code' => trim($LoanTypeCode),
                'Name' => ucwords($LoanTypeName),
                'Description' => ucwords($LoanTypeDescription),
                'CreatedByID' =>  Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' =>$TODAY,
                'Status' => trim($Status)
              ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $LoanTypeID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Laon Type";
        $logData['TransType'] = "New Laon Type";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $LoanTypeID;

    }

  public function doCheckLoanTypeCodeIfExist($param){

    $IsExist = false;
    $LoanTypeID=$param['LoanTypeID'];
    $LoanTypeCode=$param['LoanTypeCode'];

      $LoanTypeInfo = DB::table('payroll_loan_type')
          ->where('Code','=',trim($LoanTypeCode))
          ->first();

    if(isset($LoanTypeInfo)){
        if($LoanTypeID > 0){
          if($LoanTypeInfo->ID != $LoanTypeID){
            $IsExist = true;
          }
        }else{
          $IsExist = true;
        }
      }

      return $IsExist;
    }


}

