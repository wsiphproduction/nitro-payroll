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

class SSSTable extends Model
{

public function getSSSTableBracketList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_sss_table as ssst')
    ->selectraw("
        ssst.ID,
        COALESCE(ssst.RangeFrom,0) as RangeFrom,
        COALESCE(ssst.RangeTo,0) as RangeTo,

        COALESCE(ssst.RegularSSEC,0) as RegularSSEC,
        COALESCE(ssst.RegularSSWISP,0) as RegularSSWISP,
        COALESCE(ssst.RegularSSECWISPTotal,0) as RegularSSECWISPTotal,

        COALESCE(ssst.RegularER,0) as RegularER,
        COALESCE(ssst.RegularEE,0) as RegularEE,
        COALESCE(ssst.RegularTotal,0) as RegularTotal,
        
        COALESCE(ssst.ECEE,0) as ECEE,
        COALESCE(ssst.ECER,0) as ECER,
        COALESCE(ssst.ECTotal,0) as ECTotal,
        
        COALESCE(ssst.WispER,0) as WispER,
        COALESCE(ssst.WispEE,0) as WispEE,
        COALESCE(ssst.WispTotal,0) as WispTotal,

        COALESCE(ssst.Year,'') as Year,
        COALESCE(ssst.Status,'') as Status
    ");

     if($Status!=''){
        if($Status=='Active' || $Status=='Inactive'){
           $query->where("ssst.status",$Status);   
        }
      } 

     if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(ssst.RangeFrom,''),
                        COALESCE(ssst.RangeTo,''),
                        COALESCE(ssst.RangeFrom,''),
                        COALESCE(ssst.RangeTo,''),

                        COALESCE(ssst.RegularSSEC,0),
                        COALESCE(ssst.RegularSSWISP,0),
                        COALESCE(ssst.RegularSSECWISPTotal,0),                        

                        COALESCE(ssst.RegularER,0),
                        COALESCE(ssst.RegularEE,0),
                        COALESCE(ssst.RegularTotal,0),                        

                        COALESCE(ssst.RegularSSEC,0),
                        COALESCE(ssst.RegularSSWISP,0),
                        COALESCE(ssst.RegularSSECWISPTotal,0),                        

                        COALESCE(ssst.ECEE,0),
                        COALESCE(ssst.ECER,0),
                        COALESCE(ssst.ECTotal,0),
                        
                        COALESCE(ssst.WispER,0),
                        COALESCE(ssst.WispEE,0),
                        COALESCE(ssst.WispTotal,0),

                        COALESCE(ssst.Year,''),
                        COALESCE(ssst.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }
     
     $query->orderBy("ssst.ID","ASC");
     $query->orderByraw("COALESCE(ssst.Year,'') ASC");
    $list = $query->get();

    return $list;

}

public function gettSSSTableBracketInfo($SSS_ID){

    $info = DB::table('payroll_sss_table as ssst')
        ->selectraw("
        ssst.ID,
        COALESCE(ssst.RangeFrom,0) as RangeFrom,
        COALESCE(ssst.RangeTo,0) as RangeTo,

        COALESCE(ssst.RegularSSEC,0) as RegularSSEC,
        COALESCE(ssst.RegularSSWISP,0) as RegularSSWISP,
        COALESCE(ssst.RegularSSECWISPTotal,0) as RegularSSECWISPTotal,

        COALESCE(ssst.RegularER,0) as RegularER,
        COALESCE(ssst.RegularEE,0) as RegularEE,
        COALESCE(ssst.RegularTotal,0) as RegularTotal,
        
        COALESCE(ssst.ECEE,0) as ECEE,
        COALESCE(ssst.ECER,0) as ECER,
        COALESCE(ssst.ECTotal,0) as ECTotal,
        
        COALESCE(ssst.WispER,0) as WispER,
        COALESCE(ssst.WispEE,0) as WispEE,
        COALESCE(ssst.WispTotal,0) as WispTotal,

        COALESCE(ssst.Year,'') as Year,
        COALESCE(ssst.Status,'') as Status
        ")

    ->where("ssst.ID",$SSS_ID)
    ->first();

    return $info;

}

 public function doSaveSSSTableBracket($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $SSS_ID = $data['SSS_ID'];

    $SalaryFrom = $Misc->setNumeric($data['SalaryFrom']);
    $SalaryTo = $Misc->setNumeric($data['SalaryTo']);

    $RegularSSEC = $Misc->setNumeric($data['RegularSSEC']);
    $RegularSSWISP = $Misc->setNumeric($data['RegularSSWISP']);
    $RegularSSECWISPTotal = $Misc->setNumeric($data['RegularSSECWISPTotal']);

    $RegularER = $Misc->setNumeric($data['RegularER']);
    $RegularEE = $Misc->setNumeric($data['RegularEE']);
    $RegularTotal = $Misc->setNumeric($data['RegularTotal']);

    $ECEE = $Misc->setNumeric($data['ECEE']);
    $ECER = $Misc->setNumeric($data['ECER']);
    $ECTotal = $Misc->setNumeric($data['ECTotal']);
    
    $WISPER = $Misc->setNumeric($data['WISPER']);
    $WISPEE = $Misc->setNumeric($data['WISPEE']);
    $WISPTotal = $Misc->setNumeric($data['WISPTotal']);

    $Year = $data['Year'];
    $Status = $data['Status'];

    if($SSS_ID > 0){

        DB::table('payroll_sss_table')
            ->where('ID',$SSS_ID)
            ->update([
                'RangeFrom' => $SalaryFrom,
                'RangeTo' => $SalaryTo,
                'RegularSSEC' => $RegularSSEC,
                'RegularSSWISP' => $RegularSSWISP,
                'RegularSSECWISPTotal' => $RegularSSECWISPTotal,
                'RegularER' => $RegularER,
                'RegularEE' => $RegularEE,
                'RegularTotal' => $RegularTotal,
                'ECEE' => $ECEE,
                'ECER' => $ECER,  
                'ECTotal' => $ECTotal,                  
                'WispER' => $WISPER,
                'WispEE' => $WISPEE,               
                'WispTotal' => $WISPTotal,                
                'Year' => $Year,
                'Status' => $Status,
                'UpdatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' => $TODAY
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $SSS_ID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "SSS Table Bracket";
        $logData['TransType'] = "Update SSS Table Bracket Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        $SSS_ID = DB::table('payroll_sss_table')
            ->insertGetId([
                'RangeFrom' => $SalaryFrom,
                'RangeTo' => $SalaryTo,
                'RegularSSEC' => $RegularSSEC,
                'RegularSSWISP' => $RegularSSWISP,
                'RegularSSECWISPTotal' => $RegularSSECWISPTotal,
                'RegularER' => $RegularER,
                'RegularEE' => $RegularEE,
                'RegularTotal' => $RegularTotal,
                'ECEE' => $ECEE,
                'ECER' => $ECER,  
                'ECTotal' => $ECTotal,                  
                'WispER' => $WISPER,
                'WispEE' => $WISPEE,               
                'WispTotal' => $WISPTotal,                
                'Year' => $Year,
                'Status' => $Status,
                'CreatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' => $TODAY
              ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $SSS_ID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "SSS Table Bracket";
        $logData['TransType'] = "New SSS Table Bracket Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $SSS_ID;

    }

}

