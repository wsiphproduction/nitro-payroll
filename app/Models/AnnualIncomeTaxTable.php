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

class AnnualIncomeTaxTable extends Model
{

public function getAnnualIncomeTaxBracketList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('paryoll_annual_tax_table as patt')
    ->selectraw("
        patt.ID,
        COALESCE(patt.RangeFrom,'') as RangeFrom,
        COALESCE(patt.RangeTo,'') as RangeTo,
        COALESCE(patt.FixTax,'') as FixTax,
         COALESCE(patt.RateOnExcessPercentage,'') as RateOnExcessPercentage,       
        COALESCE(patt.Year,'') as Year,
        COALESCE(patt.Status,'') as Status
    ");

    if($Status!=''){
        if($Status=='Active' || $Status=='Inactive'){
           $query->where("patt.status",$Status);   
        }
      } 

   if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(patt.EmployeeSharePercent,''),
                        COALESCE(patt.EmployerSharePercent,''),
                        COALESCE(patt.Year,''),
                        COALESCE(patt.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }
    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }
     
     $query->orderBy("patt.ID","ASC");
     $query->orderByraw("COALESCE(patt.Year,'') ASC");
    $list = $query->get();

    return $list;

}

public function getAnnualIncomeTaxTableInfo($AnnualIncomeTaxID){

    $info = DB::table('paryoll_annual_tax_table as patt')
        ->selectraw("
            patt.ID,
            COALESCE(patt.RangeFrom,'') as RangeFrom,
            COALESCE(patt.RangeTo,'') as RangeTo,
            COALESCE(patt.FixTax,'') as FixTax,
            COALESCE(patt.RateOnExcessPercentage,'') as RateOnExcessPercentage,       
            COALESCE(patt.Year,'') as Year,
            COALESCE(patt.Status,'') as Status
        ")
    ->where("patt.ID",$AnnualIncomeTaxID)
    ->first();

    return $info;

}

public function doSaveAnnaulIncomeTaxTableBracket($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $AnnualIncomeTaxID = $data['AnnualIncomeTaxID'];

    $SalaryFrom = $Misc->setNumeric($data['SalaryFrom']);
    $SalaryTo = $Misc->setNumeric($data['SalaryTo']);
    $FixTax = $Misc->setNumeric($data['FixTax']);

    $RateonExcessPercent = $data['RateonExcessPercent'];
    $Status = $data['Status'];

  
    if($AnnualIncomeTaxID > 0){

        DB::table('paryoll_annual_tax_table')
            ->where('ID',$AnnualIncomeTaxID)
            ->update([
                'RangeFrom' => $SalaryFrom,
                'RangeTo' => $SalaryTo,
                'FixTax' => $FixTax,
                'RateOnExcessPercentage' => $RateonExcessPercent,
                'Status' => trim($Status)
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $AnnualIncomeTaxID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Annual Income Tax";
        $logData['TransType'] = "Update PAnnual Income Tax Table Bracket";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{


        $AnnualIncomeTaxID = DB::table('paryoll_annual_tax_table')
            ->insertGetId([
                'RangeFrom' => $SalaryFrom,
                'RangeTo' => $SalaryTo,
                'FixTax' => $FixTax,
                'RateOnExcessPercentage' => $RateonExcessPercent,
                'Status' => trim($Status)
              ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $AnnualIncomeTaxID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Annual Income Tax";
        $logData['TransType'] = "New PAnnual Income Tax Table Bracket";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $AnnualIncomeTaxID;

    }

}

