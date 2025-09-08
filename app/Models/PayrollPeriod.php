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

class PayrollPeriod extends Model
{

public function getPayrollPeriodList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_period_schedule as pps')
        ->selectraw("
            pps.ID,
            COALESCE(pps.Code,'') as Code,
            COALESCE(pps.StartDate,'') as StartDate,
            COALESCE(pps.EndDate,'') as EndDate,

            FORMAT(pps.StartDate,'MM/dd/yyyy') as StartDateFormat,
            FORMAT(pps.EndDate,'MM/dd/yyyy') as EndDateFormat,

            CONVERT(varchar(10),pps.StartDate,101) as SearchStartDateFormat,
            CONVERT(varchar(10),pps.EndDate,101) as SearchEndDateFormat,

            COALESCE(pps.Remarks,'') as Remarks,
             COALESCE(pps.CutOffID,'') as CutOff,
            COALESCE(pps.Year,'') as Year,
            COALESCE(pps.Status,'') as Status
        ");

      if($Status!=''){
        if($Status=='Close' || $Status=='Open'){
           $query->where("pps.status",$Status);   
        }

        if($Status=='1st Half'){
           $query->where("pps.CutOffID",1);   
        }

        if($Status=='2nd Half'){
           $query->where("pps.CutOffID",2);   
        }
      } 

     if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(pps.Code,''),
                        COALESCE(pps.StartDate,''),
                        COALESCE(pps.EndDate,''),
                        COALESCE(pps.Remarks,''),
                        COALESCE(pps.Year,''),
                        COALESCE(pps.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    // $query->orderByraw("COALESCE(pps.Code,'') ASC");
    $query->orderByraw("COALESCE(pps.ID,'') DESC");
    $list = $query->get();

    return $list;

}

public function getPayrollPeriodScheduleInfo($getPayrollPeriodSchedule_ID){

    $info = DB::table('payroll_period_schedule as pps')
        ->selectraw("
            pps.ID,
            COALESCE(pps.Code,'') as Code,
            COALESCE(pps.StartDate,'') as StartDate,
            COALESCE(pps.EndDate,'') as EndDate,
            FORMAT(pps.StartDate,'MM/dd/yyyy') as StartDateFormat,
            FORMAT(pps.EndDate,'MM/dd/yyyy') as EndDateFormat,
            COALESCE(pps.Year,'') as Year,
            COALESCE(pps.CutOffID,'') as CutOff,
            COALESCE(pps.Remarks,'') as Remarks,
            COALESCE(pps.Status,'') as Status
        ")
    ->where("pps.ID",$getPayrollPeriodSchedule_ID)
    ->first();

    return $info;

}

public function getPayrollPeriodScheduleInfoByCode($getPayrollPeriodSchedule_Code){

    $info = DB::table('payroll_period_schedule as pps')
        ->selectraw("
            pps.ID,
            COALESCE(pps.Code,'') as Code,
            COALESCE(pps.StartDate,'') as StartDate,
            COALESCE(pps.EndDate,'') as EndDate,
            COALESCE(pps.Year,'') as Year,
            COALESCE(pps.CutOffID,'') as CutOff,
            COALESCE(pps.Remarks,'') as Remarks,
            COALESCE(pps.Status,'') as Status
        ")
    ->where("pps.Code",$getPayrollPeriodSchedule_Code)
    ->first();

    return $info;

}

public function doSavePayrollPeriod($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $PayrollScheduleID = $data['PayrollScheduleID'];
    $PayrollScheduleCode = trim($data['PayrollScheduleCode']);
    $PayrollScheduleRemarks = trim($data['PayrollScheduleRemarks']);

    $PayrollStartDate = trim($data['PayrollStartDate']);
    $PayrollEndDate = trim($data['PayrollEndDate']);

    $Year = $data['Year'];
    $CutOff = $data['CutOff'];
    $Status = $data['Status'];
  
    $PayrollNewStartDate=date('Y-m-d',strtotime($PayrollStartDate)); 
    $PayrollNewEndDate=date('Y-m-d',strtotime($PayrollEndDate)); 

    if($PayrollScheduleID > 0){

        DB::table('payroll_period_schedule')
            ->where('ID',$PayrollScheduleID)
            ->update([
                'Code' => trim($PayrollScheduleCode),
                'Remarks' => ucwords($PayrollScheduleRemarks),
                'StartDate' => trim($PayrollNewStartDate),
                'EndDate' => trim($PayrollNewEndDate),
                'Year' => $Year,
                'CutOffID' => $CutOff,
                'Status' => trim($Status)
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $PayrollScheduleID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Payroll Period Schedule";
        $logData['TransType'] = "Update Payroll Period Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        //$PayrollScheduleCode = $Misc->GenerateRandomNo(6,'payroll_periodsched','Code');
        $PayrollScheduleID = DB::table('payroll_period_schedule')
            ->insertGetId([
                'Code' => trim($PayrollScheduleCode),
                'Remarks' => ucwords($PayrollScheduleRemarks),
                'StartDate' => trim($PayrollStartDate),
                'EndDate' => trim($PayrollNewEndDate),
                'Year' => $Year,
                'CutOffID' => $CutOff,
                'Status' => trim($Status)
              ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $PayrollScheduleID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Payroll Period Schedule";
        $logData['TransType'] = "New Payroll Period Schedule";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $PayrollScheduleID;

    }

   public function doCheckPayrollPeriodCodeIfExist($param){

    $IsExist = false;
    $PayrollPeriodID=$param['PayrollScheduleID'];
    $PayrollPeriodCode=$param['PayrollScheduleCode'];

      $PayrollPeriodInfo = DB::table('payroll_period_schedule')
          ->where('Code','=',trim($PayrollPeriodCode))
          ->first();

    if(isset($PayrollPeriodInfo)){
        if($PayrollPeriodID > 0){
          if($PayrollPeriodInfo->ID != $PayrollPeriodID){
            $IsExist = true;
          }
        }else{
          $IsExist = true;
        }
      }

      return $IsExist;
    }

    public function doCheckPayrollPeriodDatesIfExist($param){

    $IsExist = false;
    $PayrollPeriodID=$param['PayrollScheduleID'];  
    $PayrollPeriodDateFrom=$param['PayrollStartDate'];
    $PayrollPeriodDateTo=$param['PayrollEndDate'];

    $PayrollNewStartDate=date('Y-m-d',strtotime($PayrollPeriodDateFrom)); 
    $PayrollNewEndDate=date('Y-m-d',strtotime($PayrollPeriodDateTo)); 

      $PayrollPeriodInfo = DB::table('payroll_period_schedule')
          ->where('StartDate','=',$PayrollNewStartDate)
          ->where('EndDate','=',$PayrollNewEndDate)
          ->first();

    if(isset($PayrollPeriodInfo)){
        if($PayrollPeriodID > 0){
          if($PayrollPeriodInfo->ID != $PayrollPeriodID){
            $IsExist = true;
          }
        }else{
          $IsExist = true;
        }
      }

      return $IsExist;
    }
    

}

