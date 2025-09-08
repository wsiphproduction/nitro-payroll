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

use App\Models\Misc;

class WithHoldingTaxTable extends Model
{

public function getWithHoldingTaxTableBracketList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_withholding_tax_table as pwtt')
    ->selectraw("
        pwtt.ID,
        COALESCE(pwtt.PayrollFrequencyID,0) as PayrollFrequencyID,
        COALESCE(pwtt.PayrollFrequency,'') as PayrollFrequency,
        COALESCE(pwtt.RangeFrom,0) as RangeFrom,
        COALESCE(pwtt.RangeTo,0) as RangeTo,
        COALESCE(pwtt.FixTax,0) as FixTax,
        COALESCE(pwtt.RateOnExcessPercentage,'') as RateOnExcessPercentage,       
        COALESCE(pwtt.Year,'') as Year,
        COALESCE(pwtt.Status,'') as Status
    ");

    if($Status!=''){
        if($Status=='Active' || $Status=='Inactive'){
           $query->where("pwtt.status",$Status);   
        }

        if($Status=='Monthly' || $Status=='Daily' || $Status=='Semi-monthly' || $Status=='Weekly'){
           $query->where("pwtt.PayrollFrequency",$Status);   
        }

      } 

   if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(pwtt.PayrollFrequency,''),
                        COALESCE(pwtt.RangeFrom,''),
                        COALESCE(pwtt.RangeTo,''),
                        COALESCE(pwtt.FixTax,''),
                        COALESCE(pwtt.RateOnExcessPercentage,''),
                        COALESCE(pwtt.Year,''),
                        COALESCE(pwtt.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }
    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }
     
     $query->orderBy("pwtt.ID","ASC");
     $query->orderByraw("COALESCE(pwtt.Year,'') ASC");
    $list = $query->get();

    return $list;

}

public function getWithholdingTaxBracketInfo($WithHoldingID){

    $info = DB::table('payroll_withholding_tax_table as pwtt')
        ->selectraw("
           pwtt.ID,
            COALESCE(pwtt.PayrollFrequencyID,0) as PayrollFrequencyID,
            COALESCE(pwtt.PayrollFrequency,'') as PayrollFrequency,
            COALESCE(pwtt.RangeFrom,0) as RangeFrom,
            COALESCE(pwtt.RangeTo,0) as RangeTo,
            COALESCE(pwtt.FixTax,0) as FixTax,
            COALESCE(pwtt.RateOnExcessPercentage,'') as RateOnExcessPercentage,       
            COALESCE(pwtt.Year,'') as Year,
            COALESCE(pwtt.Status,'') as Status
        ")
    ->where("pwtt.ID",$WithHoldingID)
    ->first();

    return $info;

}

public function doSaveWithHoldingTaxTableBracket($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $WithholdingTaxID = $data['WithholdingTaxID'];

    $SalaryFrom = $Misc->setNumeric($data['SalaryFrom']);
    $SalaryTo = $Misc->setNumeric($data['SalaryTo']);
    $FixTax = $Misc->setNumeric($data['FixTax']);

    $PayrollFrequencyID = $data['PayrollFrequency'];
    $RateonExcessPercent = $data['RateonExcessPercent'];
    $Status = $data['Status'];

    if($PayrollFrequencyID==1){
       $PayrollFrequency='Daily';
    }elseif($PayrollFrequencyID==2){
       $PayrollFrequency='Monthly';
    }elseif($PayrollFrequencyID==3){
       $PayrollFrequency='Weekly';
    }else{
       $PayrollFrequency='Semi-monthly';
    }

    if($WithholdingTaxID > 0){

        DB::table('payroll_withholding_tax_table')
            ->where('ID',$WithholdingTaxID)
            ->update([
                'RangeFrom' => $SalaryFrom,
                'RangeTo' => $SalaryTo,
                'PayrollFrequencyID' => $PayrollFrequencyID,
                'PayrollFrequency' => $PayrollFrequency,
                'FixTax' => $FixTax,
                'RateOnExcessPercentage' => $RateonExcessPercent,
                'Status' => trim($Status)
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $WithholdingTaxID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Withholding Tax";
        $logData['TransType'] = "Update Withholding Tax Table Bracket";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        $WithholdingTaxID = DB::table('payroll_withholding_tax_table')
            ->insertGetId([
                'RangeFrom' => $SalaryFrom,
                'RangeTo' => $SalaryTo,
                'PayrollFrequencyID' => $PayrollFrequencyID,
                'PayrollFrequency' => $PayrollFrequency,
                'FixTax' => $FixTax,
                'RateOnExcessPercentage' => $RateonExcessPercent,
                'Status' => trim($Status)
              ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $WithholdingTaxID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Withholding Tax";
        $logData['TransType'] = "New Withholding Tax Table Bracket";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $WithholdingTaxID;

    }
}

