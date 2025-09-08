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

class PHICTable extends Model
{

public function getPHICTableBracketList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_phic_table as phict')
    ->selectraw("
        phict.ID,
        COALESCE(phict.RangeFrom,0) as RangeFrom,
        COALESCE(phict.RangeTo,0) as RangeTo,
        COALESCE(phict.TotalSharePercent,0) as TotalSharePercent,
        COALESCE(phict.Year,'') as Year,
        COALESCE(phict.Status,'') as Status
    ");

     if($Status!=''){
        if($Status=='Active' || $Status=='Inactive'){
           $query->where("phict.status",$Status);   
        }
      } 

     if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(phict.RangeFrom,''),
                        COALESCE(phict.RangeTo,''),
                        COALESCE(phict.TotalSharePercent,''),
                        COALESCE(phict.Year,''),
                        COALESCE(phict.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }
     
     $query->orderBy("phict.ID","ASC");
     $query->orderByraw("COALESCE(phict.Year,'') ASC");
    $list = $query->get();

    return $list;

}

public function getPHICTableBracketInfo($PHIC_ID){

    $info = DB::table('payroll_phic_table as phict')
        ->selectraw("
            phict.ID,
            COALESCE(phict.RangeFrom,0) as RangeFrom,
            COALESCE(phict.RangeTo,0) as RangeTo,
            COALESCE(phict.TotalSharePercent,0) as TotalSharePercent,
            COALESCE(phict.Year,'') as Year,
            COALESCE(phict.Status,'') as Status
        ")
    ->where("phict.ID",$PHIC_ID)
    ->first();

    return $info;

}

public function doSavePHICTableBracket($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $PHIC_ID = $data['PHIC_ID'];

    $SalaryFrom = $Misc->setNumeric($data['SalaryFrom']);
    $SalaryTo = $Misc->setNumeric($data['SalaryTo']);

    $TotalSharePercent = $data['TotalSharePercent'];
    $Year = $data['Year'];
    $Status = $data['Status'];

    if($PHIC_ID > 0){

        DB::table('payroll_phic_table')
            ->where('ID',$PHIC_ID)
            ->update([
                'RangeFrom' => $SalaryFrom,
                'RangeTo' => $SalaryTo,
                'TotalSharePercent' => $TotalSharePercent,
                'Year' => $Year,
                'Status' => $Status,
                'UpdatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' => $TODAY
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $PHIC_ID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "PHIC Table Bracket";
        $logData['TransType'] = "Update PHIC Table Bracket Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        $PHIC_ID = DB::table('payroll_phic_table')
            ->insertGetId([
                'RangeFrom' => $SalaryFrom,
                'RangeTo' => $SalaryTo,
                'TotalSharePercent' => $TotalSharePercent,
                'Year' => $Year,
                'Status' => $Status,
                'CreatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' => $TODAY
              ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $PHIC_ID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "PHIC Table Bracket";
        $logData['TransType'] = "New PHIC Table Bracket Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $PHIC_ID;

    }

}

