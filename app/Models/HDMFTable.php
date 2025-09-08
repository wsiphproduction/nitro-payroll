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

class HDMFTable extends Model
{

public function getHDMFTableBracketList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_hdmf_table as hdmft')
    ->selectraw("
        hdmft.ID,
        COALESCE(hdmft.RangeFrom,0) as RangeFrom,
        COALESCE(hdmft.RangeTo,0) as RangeTo,
        COALESCE(hdmft.EmployeeSharePercent,0) as EmployeeSharePercent,
        COALESCE(hdmft.EmployerSharePercent,0) as EmployerSharePercent,       
        COALESCE(hdmft.Year,'') as Year,
        COALESCE(hdmft.Status,'') as Status
    ");

    if($Status!=''){
        if($Status=='Active' || $Status=='Inactive'){
           $query->where("hdmft.status",$Status);   
        }
      } 

   if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(hdmft.EmployeeSharePercent,''),
                        COALESCE(hdmft.EmployerSharePercent,''),
                        COALESCE(hdmft.Year,''),
                        COALESCE(hdmft.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }
    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }
     
     $query->orderBy("hdmft.ID","ASC");
     $query->orderByraw("COALESCE(hdmft.Year,'') ASC");
    $list = $query->get();

    return $list;

}

public function getHDMFTableBracketInfo($HDMF_ID){

    $info = DB::table('payroll_hdmf_table as hdmft')
        ->selectraw("
            hdmft.ID,
            COALESCE(hdmft.RangeFrom,0) as RangeFrom,
            COALESCE(hdmft.RangeTo,0) as RangeTo,
            COALESCE(hdmft.EmployeeSharePercent,0) as EmployeeSharePercent,
            COALESCE(hdmft.EmployerSharePercent,0) as EmployerSharePercent,       
            COALESCE(hdmft.Year,'') as Year,
            COALESCE(hdmft.Status,'') as Status
        ")
    ->where("hdmft.ID",$HDMF_ID)
    ->first();

    return $info;

}

public function doSaveHDMFTableBracket($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $HDMF_ID = $data['HDMF_ID'];

    $SalaryFrom = $Misc->setNumeric($data['SalaryFrom']);
    $SalaryTo = $Misc->setNumeric($data['SalaryTo']);

    $EmployeeShare = $data['EmployeeShare'];
    $EmployerShare = $data['EmployerShare'];
    
    $Year = $data['Year'];
    $Status = $data['Status'];

    if($HDMF_ID > 0){

        DB::table('payroll_hdmf_table')
            ->where('ID',$HDMF_ID)
            ->update([
                'RangeFrom' => $SalaryFrom,
                'RangeTo' => $SalaryTo,
                'EmployeeSharePercent' => $EmployeeShare,
                'EmployerSharePercent' => $EmployerShare,
                'Year' => $Year,
                'Status' => $Status,
                'UpdatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' => $TODAY
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $HDMF_ID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "HDMF Table Bracket";
        $logData['TransType'] = "Update HDMF Table Bracket Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        $HDMF_ID = DB::table('payroll_hdmf_table')
            ->insertGetId([
                'RangeFrom' => $SalaryFrom,
                'RangeTo' => $SalaryTo,
                'EmployeeSharePercent' => $EmployeeShare,
                'EmployerSharePercent' => $EmployerShare,
                'Year' => $Year,
                'Status' => $Status,
                'CreatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' => $TODAY
              ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $HDMF_ID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "HDMF Table Bracket";
        $logData['TransType'] = "New HDMF Table Bracket Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $HDMF_ID;

    }

}

