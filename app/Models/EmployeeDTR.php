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
use App\Models\Employee;
use App\Models\EmployeeRate;
use App\Models\PayrollPeriod;

class EmployeeDTR extends Model
{

// DTR FINAL TABLE
public function getEmployeeDTRList($param){
    
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $SearchText = trim($param['SearchText']);

    $query = DB::table('payroll_employee_dtr_summary as peds')    
        ->join('users as emp', 'emp.id', '=', 'peds.EmployeeID')     
        ->join('payroll_period_schedule as pp', 'pp.ID', '=', 'peds.PayrollPeriodID')   
        ->selectraw("
            peds.ID,
                        
            COALESCE(peds.TransactionDate,'') as TransactionDate,

            COALESCE(peds.Year,'') as Year,
            COALESCE(peds.PayrollPeriodID,0) as PayrollPeriodID,
            COALESCE(peds.PayrollPeriodCode,'') as PayrollPeriodCode,

            COALESCE(pp.StartDate,'') as StartDate,
            COALESCE(pp.EndDate,'') as EndDate,

            FORMAT(pp.StartDate,'MM/dd/yyyy') as StartDateFormat,
            FORMAT(pp.EndDate,'MM/dd/yyyy') as EndDateFormat,

            CONVERT(varchar(10),pp.StartDate,101) as SearchStartDateFormat,
            CONVERT(varchar(10),pp.EndDate,101) as SearchEndDateFormat,
      
            COALESCE(peds.EmployeeID,'') as EmployeeID,
            COALESCE(peds.EmployeeNumber,'') as EmployeeNumber,
            CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as FullName,

            COALESCE(peds.EmployeeRateID,0) as EmployeeRateID,
            COALESCE(peds.EmployeeRate,0) as EmployeeRate,
            COALESCE(peds.RegularHours,0) as RegularHours,
            COALESCE(peds.LateHours,0) as LateHours,
            COALESCE(peds.UndertimeHours,0) as UndertimeHours,
            COALESCE(peds.NDHours,0) as NDHours,
            COALESCE(peds.Absent,0) as Absent,

            COALESCE(peds.Leave01,0) as Leave01,
            COALESCE(peds.Leave02,0) as Leave02,
            COALESCE(peds.Leave03,0) as Leave03,
            COALESCE(peds.Leave04,0) as Leave04,
            COALESCE(peds.Leave05,0) as Leave05,
            COALESCE(peds.Leave06,0) as Leave06,
            COALESCE(peds.Leave07,0) as Leave07,
            COALESCE(peds.Leave08,0) as Leave08,
            COALESCE(peds.Leave09,0) as Leave09,
            COALESCE(peds.Leave10,0) as Leave10,
            COALESCE(peds.Leave11,0) as Leave11,
            COALESCE(peds.Leave12,0) as Leave12,
            COALESCE(peds.Leave13,0) as Leave13,
            COALESCE(peds.Leave14,0) as Leave14,
            COALESCE(peds.Leave15,0) as Leave15,
            COALESCE(peds.Leave16,0) as Leave16,
            COALESCE(peds.Leave17,0) as Leave17,
            COALESCE(peds.Leave18,0) as Leave18,
            COALESCE(peds.Leave19,0) as Leave19,
            COALESCE(peds.Leave20,0) as Leave20,

            COALESCE(peds.OTHours01,0) as OTHours01,
            COALESCE(peds.OTHours02,0) as OTHours02,
            COALESCE(peds.OTHours03,0) as OTHours03,
            COALESCE(peds.OTHours04,0) as OTHours04,
            COALESCE(peds.OTHours05,0) as OTHours05,
            COALESCE(peds.OTHours06,0) as OTHours06,
            COALESCE(peds.OTHours07,0) as OTHours07,
            COALESCE(peds.OTHours08,0) as OTHours08,
            COALESCE(peds.OTHours09,0) as OTHours09,
            COALESCE(peds.OTHours10,0) as OTHours10,
            COALESCE(peds.OTHours11,0) as OTHours11,
            COALESCE(peds.OTHours12,0) as OTHours12,
            COALESCE(peds.OTHours13,0) as OTHours13,
            COALESCE(peds.OTHours14,0) as OTHours14,
            COALESCE(peds.OTHours15,0) as OTHours15,
            COALESCE(peds.OTHours16,0) as OTHours16,
            COALESCE(peds.OTHours17,0) as OTHours17,
            COALESCE(peds.OTHours18,0) as OTHours18,
            COALESCE(peds.OTHours19,0) as OTHours19,
            COALESCE(peds.OTHours20,0) as OTHours20,
            COALESCE(peds.OTHours21,0) as OTHours21,
            COALESCE(peds.OTHours22,0) as OTHours22,
            COALESCE(peds.OTHours23,0) as OTHours23,
            COALESCE(peds.OTHours24,0) as OTHours24,
            COALESCE(peds.OTHours25,0) as OTHours25,

            COALESCE(peds.IsForUpload,0) as IsForUpload,
            COALESCE(peds.IsUploadError,0) as IsUploadError,
            
            COALESCE(peds.IsDeletedReUpload,0) as IsDeletedReUpload,
            COALESCE(peds.IsPosted,0) as IsPosted,
            COALESCE(peds.IsClosed,0) as IsClosed,

            COALESCE(peds.Status,'') as Status,

            FORMAT(peds.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat
        ");
    

    if($Status!=''){
        if($Status=='Pending'){
           $query->where("peds.status",'Pending');    
        }else if($Status=='Approved'){
          $query->where("peds.status",'Approved');    
        }else if($Status=='Cancelled'){
          $query->where("peds.status",'Cancelled');    
        }else{
            $arStatus = explode("|",$Status);
            if(trim($arStatus[0]) == "Location"){
             $query->where("emp.company_branch_id",trim($arStatus[1]));  
            }else if(trim($arStatus[0]) == "Site"){
             $query->where("emp.company_branch_site_id",trim($arStatus[1]));  
            }
        }
    }

    if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(peds.PayrollPeriodCode,''),
                        COALESCE(peds.EmployeeNumber,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,''),
                        COALESCE(peds.Year,''),
                        COALESCE(peds.PayrollPeriodCode,''),
                        COALESCE(peds.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
             }
         }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }
   
    $query->orderBy("FullName","ASC");
    $query->orderBy("peds.Year","ASC");
    $query->orderBy("peds.PayrollPeriodCode","ASC");
    $query->orderBy("peds.Status","DESC");
    $list = $query->get();

    return $list;

}

public function getEmployeeDTRInfo($DTR_ID){

  $info = DB::table('payroll_employee_dtr_summary as peds')        
        ->join('payroll_period_schedule as pp', 'pp.ID', '=', 'peds.PayrollPeriodID')   
        ->join('users as emp', 'emp.id', '=', 'peds.EmployeeID') 
        ->selectraw("
             peds.ID,
                        
            COALESCE(peds.TransactionDate,'') as TransactionDate,
            
            COALESCE(peds.Year,'') as Year,
            COALESCE(peds.PayrollPeriodID,0) as PayrollPeriodID,
            COALESCE(peds.PayrollPeriodCode,'') as PayrollPeriodCode,
        
            COALESCE(pp.StartDate,'') as StartDate,
            COALESCE(pp.EndDate,'') as EndDate,

            FORMAT(pp.StartDate,'MM/dd/yyyy') as StartDateFormat,
            FORMAT(pp.EndDate,'MM/dd/yyyy') as EndDateFormat,

            COALESCE(peds.EmployeeID,'') as EmployeeID,
            COALESCE(emp.employee_number,'') as EmployeeNumber,
            CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

            COALESCE(peds.EmployeeRateID,0) as EmployeeRateID,
            COALESCE(peds.EmployeeRate,0) as EmployeeRate,
            COALESCE(peds.RegularHours,0) as RegularHours,
            COALESCE(peds.LateHours,0) as LateHours,
            COALESCE(peds.UndertimeHours,0) as UndertimeHours,
            COALESCE(peds.NDHours,0) as NDHours,
            COALESCE(peds.Absent,0) as Absent,

            COALESCE(peds.Leave01,0) as Leave01,
            COALESCE(peds.Leave02,0) as Leave02,
            COALESCE(peds.Leave03,0) as Leave03,
            COALESCE(peds.Leave04,0) as Leave04,
            COALESCE(peds.Leave05,0) as Leave05,
            COALESCE(peds.Leave06,0) as Leave06,
            COALESCE(peds.Leave07,0) as Leave07,
            COALESCE(peds.Leave08,0) as Leave08,
            COALESCE(peds.Leave09,0) as Leave09,
            COALESCE(peds.Leave10,0) as Leave10,
            COALESCE(peds.Leave11,0) as Leave11,
            COALESCE(peds.Leave12,0) as Leave12,
            COALESCE(peds.Leave13,0) as Leave13,
            COALESCE(peds.Leave14,0) as Leave14,
            COALESCE(peds.Leave15,0) as Leave15,
            COALESCE(peds.Leave16,0) as Leave16,
            COALESCE(peds.Leave17,0) as Leave17,
            COALESCE(peds.Leave18,0) as Leave18,
            COALESCE(peds.Leave19,0) as Leave19,
            COALESCE(peds.Leave20,0) as Leave20,

            COALESCE(peds.OTHours01,0) as OTHours01,
            COALESCE(peds.OTHours02,0) as OTHours02,
            COALESCE(peds.OTHours03,0) as OTHours03,
            COALESCE(peds.OTHours04,0) as OTHours04,
            COALESCE(peds.OTHours05,0) as OTHours05,
            COALESCE(peds.OTHours06,0) as OTHours06,
            COALESCE(peds.OTHours07,0) as OTHours07,
            COALESCE(peds.OTHours08,0) as OTHours08,
            COALESCE(peds.OTHours09,0) as OTHours09,
            COALESCE(peds.OTHours10,0) as OTHours10,
            COALESCE(peds.OTHours11,0) as OTHours11,
            COALESCE(peds.OTHours12,0) as OTHours12,
            COALESCE(peds.OTHours13,0) as OTHours13,
            COALESCE(peds.OTHours14,0) as OTHours14,
            COALESCE(peds.OTHours15,0) as OTHours15,
            COALESCE(peds.OTHours16,0) as OTHours16,
            COALESCE(peds.OTHours17,0) as OTHours17,
            COALESCE(peds.OTHours18,0) as OTHours18,
            COALESCE(peds.OTHours19,0) as OTHours19,
            COALESCE(peds.OTHours20,0) as OTHours20,
            COALESCE(peds.OTHours21,0) as OTHours21,
            COALESCE(peds.OTHours22,0) as OTHours22,
            COALESCE(peds.OTHours23,0) as OTHours23,
            COALESCE(peds.OTHours24,0) as OTHours24,
            COALESCE(peds.OTHours25,0) as OTHours25,

            COALESCE(peds.IsForUpload,0) as IsForUpload,
            COALESCE(peds.IsUploadError,0) as IsUploadError,
            
            COALESCE(peds.IsDeletedReUpload,0) as IsDeletedReUpload,
            COALESCE(peds.IsPosted,0) as IsPosted,
            COALESCE(peds.IsClosed,0) as IsClosed,

            COALESCE(peds.Remarks,'') as Remarks,
            COALESCE(peds.Status,'') as Status,

            FORMAT(peds.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat
        ")

    ->where("peds.ID",$DTR_ID)
    ->first();

    return $info;

}

public function getEmployeeDTRInfoByEmployeeIDAndPayrollID($EmployeeID,$PayrollPeriodID){

  $info = DB::table('payroll_employee_dtr_summary as peds')        
        ->join('payroll_period_schedule as pp', 'pp.ID', '=', 'peds.PayrollPeriodID')   
        ->leftjoin('users as emp', 'emp.id', '=', 'peds.EmployeeID') 
        ->selectraw("
             peds.ID,
                        
            COALESCE(peds.TransactionDate,'') as TransactionDate,
            
            COALESCE(peds.Year,'') as Year,
            COALESCE(peds.PayrollPeriodID,0) as PayrollPeriodID,
            COALESCE(peds.PayrollPeriodCode,'') as PayrollPeriodCode,
        
            COALESCE(pp.StartDate,'') as StartDate,
            COALESCE(pp.EndDate,'') as EndDate,

            FORMAT(pp.StartDate,'MM/dd/yyyy') as StartDateFormat,
            FORMAT(pp.EndDate,'MM/dd/yyyy') as EndDateFormat,

            COALESCE(peds.EmployeeID,'') as EmployeeID,
            COALESCE(emp.employee_number,'') as EmployeeNumber,
            CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

            COALESCE(peds.EmployeeRateID,0) as EmployeeRateID,
            COALESCE(peds.EmployeeRate,0) as EmployeeRate,
            COALESCE(peds.RegularHours,0) as RegularHours,
            COALESCE(peds.LateHours,0) as LateHours,
            COALESCE(peds.UndertimeHours,0) as UndertimeHours,
            COALESCE(peds.NDHours,0) as NDHours,
            COALESCE(peds.Absent,0) as Absent,

            COALESCE(peds.Leave01,0) as Leave01,
            COALESCE(peds.Leave02,0) as Leave02,
            COALESCE(peds.Leave03,0) as Leave03,
            COALESCE(peds.Leave04,0) as Leave04,
            COALESCE(peds.Leave05,0) as Leave05,
            COALESCE(peds.Leave06,0) as Leave06,
            COALESCE(peds.Leave07,0) as Leave07,
            COALESCE(peds.Leave08,0) as Leave08,
            COALESCE(peds.Leave09,0) as Leave09,
            COALESCE(peds.Leave10,0) as Leave10,
            COALESCE(peds.Leave11,0) as Leave11,
            COALESCE(peds.Leave12,0) as Leave12,
            COALESCE(peds.Leave13,0) as Leave13,
            COALESCE(peds.Leave14,0) as Leave14,
            COALESCE(peds.Leave15,0) as Leave15,
            COALESCE(peds.Leave16,0) as Leave16,
            COALESCE(peds.Leave17,0) as Leave17,
            COALESCE(peds.Leave18,0) as Leave18,
            COALESCE(peds.Leave19,0) as Leave19,
            COALESCE(peds.Leave20,0) as Leave20,

            COALESCE(peds.OTHours01,0) as OTHours01,
            COALESCE(peds.OTHours02,0) as OTHours02,
            COALESCE(peds.OTHours03,0) as OTHours03,
            COALESCE(peds.OTHours04,0) as OTHours04,
            COALESCE(peds.OTHours05,0) as OTHours05,
            COALESCE(peds.OTHours06,0) as OTHours06,
            COALESCE(peds.OTHours07,0) as OTHours07,
            COALESCE(peds.OTHours08,0) as OTHours08,
            COALESCE(peds.OTHours09,0) as OTHours09,
            COALESCE(peds.OTHours10,0) as OTHours10,
            COALESCE(peds.OTHours11,0) as OTHours11,
            COALESCE(peds.OTHours12,0) as OTHours12,
            COALESCE(peds.OTHours13,0) as OTHours13,
            COALESCE(peds.OTHours14,0) as OTHours14,
            COALESCE(peds.OTHours15,0) as OTHours15,
            COALESCE(peds.OTHours16,0) as OTHours16,
            COALESCE(peds.OTHours17,0) as OTHours17,
            COALESCE(peds.OTHours18,0) as OTHours18,
            COALESCE(peds.OTHours19,0) as OTHours19,
            COALESCE(peds.OTHours20,0) as OTHours20,
            COALESCE(peds.OTHours21,0) as OTHours21,
            COALESCE(peds.OTHours22,0) as OTHours22,
            COALESCE(peds.OTHours23,0) as OTHours23,
            COALESCE(peds.OTHours24,0) as OTHours24,
            COALESCE(peds.OTHours25,0) as OTHours25,

            COALESCE(peds.IsForUpload,0) as IsForUpload,
            COALESCE(peds.IsUploadError,0) as IsUploadError,
            
            COALESCE(peds.IsDeletedReUpload,0) as IsDeletedReUpload,
            COALESCE(peds.IsPosted,0) as IsPosted,
            COALESCE(peds.IsClosed,0) as IsClosed,

            COALESCE(peds.Remarks,'') as Remarks,
            COALESCE(peds.Status,'') as Status,

            FORMAT(peds.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat
        ")

    ->where("peds.EmployeeID",$EmployeeID)
    ->where("peds.PayrollPeriodID",$PayrollPeriodID)
    ->first();

    return $info;

}

public function doCheckEmployeeDTRIfExist($param){

    $IsExist = false;
    $DTRID=$param['DTRID'];
    $EmployeeID=$param['EmpID'];
    $PayrollPeriodID=$param['PayrollID'];

    $EmployeeDTRInfo = DB::table('payroll_employee_dtr_summary')
          ->where('EmployeeID','=',$EmployeeID)
          ->where('PayrollPeriodID','=',$PayrollPeriodID)
          ->where('Status','=','Approved')
          ->first();

    if(isset($EmployeeDTRInfo)){
        if($DTRID > 0){
          if($EmployeeDTRInfo->ID != $DTRID){
            $IsExist = true;
          }
        }else{
          $IsExist = true;
        }
      }

      return $IsExist;
    }

// DTR TEMP TABLE
public function getDTRTempList($param){

    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_dtr_temp as dtr_tmp')
        ->leftjoin('users as emp', 'emp.id', '=', 'dtr_tmp.EmployeeID') 
        ->leftjoin('payroll_period_schedule as pp', 'pp.ID', '=', 'dtr_tmp.PayrollPeriodID')   
        ->selectraw("
            dtr_tmp.ID,
            
            COALESCE(dtr_tmp.Year,'') as Year,
            COALESCE(dtr_tmp.PayrollPeriodID,0) as PayrollPeriodID,
            COALESCE(dtr_tmp.PayrollPeriodCode,'') as PayrollPeriodCode,

            COALESCE(pp.StartDate,'') as StartDate,
            COALESCE(pp.EndDate,'') as EndDate,

            FORMAT(pp.StartDate,'MM/dd/yyyy') as StartDateFormat,
            FORMAT(pp.EndDate,'MM/dd/yyyy') as EndDateFormat,

            CONVERT(varchar(10),pp.StartDate,101) as SearchStartDateFormat,
            CONVERT(varchar(10),pp.EndDate,101) as SearchEndDateFormat,

            COALESCE(emp.employee_number,'') as EmployeeNumber,
            COALESCE(emp.first_name,'') as first_name,
            COALESCE(emp.last_name,'') as last_name,
            COALESCE(emp.middle_name,'') as middle_name,
            CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,
                 
            COALESCE(dtr_tmp.EmployeeRateID,0) as EmployeeRateID,
            COALESCE(dtr_tmp.EmployeeRate,0) as EmployeeRate,
            COALESCE(dtr_tmp.RegularHours,0) as RegularHours,
            COALESCE(dtr_tmp.LateHours,0) as LateHours,
            COALESCE(dtr_tmp.UndertimeHours,0) as UndertimeHours,
            COALESCE(dtr_tmp.NDHours,0) as NDHours,
            COALESCE(dtr_tmp.Absent,0) as Absent,

            COALESCE(dtr_tmp.Leave01,0) as Leave01,
            COALESCE(dtr_tmp.Leave02,0) as Leave02,
            COALESCE(dtr_tmp.Leave03,0) as Leave03,
            COALESCE(dtr_tmp.Leave04,0) as Leave04,
            COALESCE(dtr_tmp.Leave05,0) as Leave05,
            COALESCE(dtr_tmp.Leave06,0) as Leave06,
            COALESCE(dtr_tmp.Leave07,0) as Leave07,
            COALESCE(dtr_tmp.Leave08,0) as Leave08,
            COALESCE(dtr_tmp.Leave09,0) as Leave09,
            COALESCE(dtr_tmp.Leave10,0) as Leave10,
            COALESCE(dtr_tmp.Leave11,0) as Leave11,
            COALESCE(dtr_tmp.Leave12,0) as Leave12,
            COALESCE(dtr_tmp.Leave13,0) as Leave13,
            COALESCE(dtr_tmp.Leave14,0) as Leave14,
            COALESCE(dtr_tmp.Leave15,0) as Leave15,
            COALESCE(dtr_tmp.Leave16,0) as Leave16,
            COALESCE(dtr_tmp.Leave17,0) as Leave17,
            COALESCE(dtr_tmp.Leave18,0) as Leave18,
            COALESCE(dtr_tmp.Leave19,0) as Leave19,
            COALESCE(dtr_tmp.Leave20,0) as Leave20,

            COALESCE(dtr_tmp.OTHours01,0) as OTHours01,
            COALESCE(dtr_tmp.OTHours02,0) as OTHours02,
            COALESCE(dtr_tmp.OTHours03,0) as OTHours03,
            COALESCE(dtr_tmp.OTHours04,0) as OTHours04,
            COALESCE(dtr_tmp.OTHours05,0) as OTHours05,
            COALESCE(dtr_tmp.OTHours06,0) as OTHours06,
            COALESCE(dtr_tmp.OTHours07,0) as OTHours07,
            COALESCE(dtr_tmp.OTHours08,0) as OTHours08,
            COALESCE(dtr_tmp.OTHours09,0) as OTHours09,
            COALESCE(dtr_tmp.OTHours10,0) as OTHours10,
            COALESCE(dtr_tmp.OTHours11,0) as OTHours11,
            COALESCE(dtr_tmp.OTHours12,0) as OTHours12,
            COALESCE(dtr_tmp.OTHours13,0) as OTHours13,
            COALESCE(dtr_tmp.OTHours14,0) as OTHours14,
            COALESCE(dtr_tmp.OTHours15,0) as OTHours15,
            COALESCE(dtr_tmp.OTHours16,0) as OTHours16,
            COALESCE(dtr_tmp.OTHours17,0) as OTHours17,
            COALESCE(dtr_tmp.OTHours18,0) as OTHours18,
            COALESCE(dtr_tmp.OTHours19,0) as OTHours19,
            COALESCE(dtr_tmp.OTHours20,0) as OTHours20,
            COALESCE(dtr_tmp.OTHours21,0) as OTHours21,
            COALESCE(dtr_tmp.OTHours22,0) as OTHours22,
            COALESCE(dtr_tmp.OTHours23,0) as OTHours23,
            COALESCE(dtr_tmp.OTHours24,0) as OTHours24,
            COALESCE(dtr_tmp.OTHours25,0) as OTHours25,

            COALESCE(dtr_tmp.Status,'') as Status,
            COALESCE(dtr_tmp.IsUploadError,0) as IsUploadError,
            FORMAT(dtr_tmp.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat
    
        ");

    if($Status!=''){
        $query->where("dtr_tmp.Status",$Status);
    }

    $query->where("dtr_tmp.UploadedByID",Session::get('ADMIN_USER_ID'));

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("dtr_tmp.IsUploadError","DESC");
    $query->orderBy("dtr_tmp.PayrollPeriodCode","ASC");
    $list = $query->get();

    return $list;

}

public function getDTRTempInfo($DTR_ID){

  $info = DB::table('payroll_dtr_temp as dtr_tmp')
        ->leftjoin('users as emp', 'emp.id', '=', 'dtr_tmp.EmployeeID') 
        ->leftjoin('payroll_period_schedule as pp', 'pp.ID', '=', 'dtr_tmp.PayrollPeriodID')   
        ->selectraw("
             dtr_tmp.ID,
            
            COALESCE(dtr_tmp.Year,'') as Year,
            COALESCE(dtr_tmp.PayrollPeriodID,0) as PayrollPeriodID,
            COALESCE(dtr_tmp.PayrollPeriodCode,'') as PayrollPeriodCode,
        
            COALESCE(pp.StartDate,'') as StartDate,
            COALESCE(pp.EndDate,'') as EndDate,

            FORMAT(pp.StartDate,'MM/dd/yyyy') as StartDateFormat,
            FORMAT(pp.EndDate,'MM/dd/yyyy') as EndDateFormat,

            COALESCE(dtr_tmp.EmployeeID,'') as EmployeeID,
            COALESCE(dtr_tmp.EmployeeNumber,'') as EmployeeNumber,
            CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

            COALESCE(dtr_tmp.EmployeeRateID,0) as EmployeeRateID,
            COALESCE(dtr_tmp.EmployeeRate,0) as EmployeeRate,
            COALESCE(dtr_tmp.RegularHours,0) as RegularHours,
            COALESCE(dtr_tmp.LateHours,0) as LateHours,
            COALESCE(dtr_tmp.UndertimeHours,0) as UndertimeHours,
            COALESCE(dtr_tmp.NDHours,0) as NDHours,
            COALESCE(dtr_tmp.Absent,0) as Absent,

            COALESCE(dtr_tmp.Leave01,0) as Leave01,
            COALESCE(dtr_tmp.Leave02,0) as Leave02,
            COALESCE(dtr_tmp.Leave03,0) as Leave03,
            COALESCE(dtr_tmp.Leave04,0) as Leave04,
            COALESCE(dtr_tmp.Leave05,0) as Leave05,
            COALESCE(dtr_tmp.Leave06,0) as Leave06,
            COALESCE(dtr_tmp.Leave07,0) as Leave07,
            COALESCE(dtr_tmp.Leave08,0) as Leave08,
            COALESCE(dtr_tmp.Leave09,0) as Leave09,
            COALESCE(dtr_tmp.Leave10,0) as Leave10,
            COALESCE(dtr_tmp.Leave11,0) as Leave11,
            COALESCE(dtr_tmp.Leave12,0) as Leave12,
            COALESCE(dtr_tmp.Leave13,0) as Leave13,
            COALESCE(dtr_tmp.Leave14,0) as Leave14,
            COALESCE(dtr_tmp.Leave15,0) as Leave15,
            COALESCE(dtr_tmp.Leave16,0) as Leave16,
            COALESCE(dtr_tmp.Leave17,0) as Leave17,
            COALESCE(dtr_tmp.Leave18,0) as Leave18,
            COALESCE(dtr_tmp.Leave19,0) as Leave19,
            COALESCE(dtr_tmp.Leave20,0) as Leave20,

            COALESCE(dtr_tmp.OTHours01,0) as OTHours01,
            COALESCE(dtr_tmp.OTHours02,0) as OTHours02,
            COALESCE(dtr_tmp.OTHours03,0) as OTHours03,
            COALESCE(dtr_tmp.OTHours04,0) as OTHours04,
            COALESCE(dtr_tmp.OTHours05,0) as OTHours05,
            COALESCE(dtr_tmp.OTHours06,0) as OTHours06,
            COALESCE(dtr_tmp.OTHours07,0) as OTHours07,
            COALESCE(dtr_tmp.OTHours08,0) as OTHours08,
            COALESCE(dtr_tmp.OTHours09,0) as OTHours09,
            COALESCE(dtr_tmp.OTHours10,0) as OTHours10,
            COALESCE(dtr_tmp.OTHours11,0) as OTHours11,
            COALESCE(dtr_tmp.OTHours12,0) as OTHours12,
            COALESCE(dtr_tmp.OTHours13,0) as OTHours13,
            COALESCE(dtr_tmp.OTHours14,0) as OTHours14,
            COALESCE(dtr_tmp.OTHours15,0) as OTHours15,
            COALESCE(dtr_tmp.OTHours16,0) as OTHours16,
            COALESCE(dtr_tmp.OTHours17,0) as OTHours17,
            COALESCE(dtr_tmp.OTHours18,0) as OTHours18,
            COALESCE(dtr_tmp.OTHours19,0) as OTHours19,
            COALESCE(dtr_tmp.OTHours20,0) as OTHours20,
            COALESCE(dtr_tmp.OTHours21,0) as OTHours21,
            COALESCE(dtr_tmp.OTHours22,0) as OTHours22,
            COALESCE(dtr_tmp.OTHours23,0) as OTHours23,
            COALESCE(dtr_tmp.OTHours24,0) as OTHours24,
            COALESCE(dtr_tmp.OTHours25,0) as OTHours25,

            COALESCE(dtr_tmp.Status,'') as Status,
            COALESCE(dtr_tmp.IsUploadError,0) as IsUploadError,
            FORMAT(dtr_tmp.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat

        ")
    ->where("dtr_tmp.ID",$DTR_ID)
    ->first();

    return $info;

}

//SAVE IN TEMP TABLE
public function doSaveDTRTempTransaction($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $TODAY_UPLOADED_TRANS_DATE = date("Y-m-d");

    $DTRID=$data["DTRID"];

    $Year=$data["Year"];
    $TransDate=$data["TransDate"];

    $PayrollID=$data["PayrollID"];
    $PayrollCode=$data["PayrollCode"];

    $EmployeeID=$data["EmpID"];
    $EmployeeNo=$data["EmpNo"];

    $EmployeeRate=$data["EmpRate"];
    $RegularHours=$data["RegHours"];
    $LateHours=$data["LateHours"];
    $UnderTimeHours=$data["UnderTimeHours"];
    $NDHours=$data["NDHours"];
    $Absent=$data["Absent"];

    $Leave01=$data["Leave01"];
    $Leave02=$data["Leave02"];
    $Leave03=$data["Leave03"];
    $Leave04=$data["Leave04"];
    $Leave05=$data["Leave05"];
    $Leave06=$data["Leave06"];
    $Leave07=$data["Leave07"];
    $Leave08=$data["Leave08"];
    $Leave09=$data["Leave09"];
    $Leave10=$data["Leave10"];
    $Leave11=$data["Leave11"];
    $Leave12=$data["Leave12"];
    $Leave13=$data["Leave13"];
    $Leave14=$data["Leave14"];
    $Leave15=$data["Leave15"];
    $Leave16=$data["Leave16"];
    $Leave17=$data["Leave17"];
    $Leave18=$data["Leave18"];
    $Leave19=$data["Leave19"];
    $Leave20=$data["Leave20"];

    $OTHours01=$data["OTHours01"];
    $OTHours02=$data["OTHours02"];
    $OTHours03=$data["OTHours03"];
    $OTHours04=$data["OTHours04"];
    $OTHours05=$data["OTHours05"];
    $OTHours06=$data["OTHours06"];
    $OTHours07=$data["OTHours07"];
    $OTHours08=$data["OTHours08"];
    $OTHours09=$data["OTHours09"];
    $OTHours10=$data["OTHours10"];
    $OTHours11=$data["OTHours11"];
    $OTHours12=$data["OTHours12"];
    $OTHours13=$data["OTHours13"];
    $OTHours14=$data["OTHours14"];
    $OTHours15=$data["OTHours15"];
    $OTHours16=$data["OTHours16"];
    $OTHours17=$data["OTHours17"];
    $OTHours18=$data["OTHours18"];
    $OTHours19=$data["OTHours19"];
    $OTHours20=$data["OTHours20"];
    $OTHours21=$data["OTHours21"];
    $OTHours22=$data["OTHours22"];
    $OTHours23=$data["OTHours23"];
    $OTHours24=$data["OTHours24"];
    $OTHours25=$data["OTHours25"];

    $IsUploaded=$data["IsUploaded"];
    $Status=$data["Status"];

    if($IsUploaded==1){
        $TransDate=$TODAY_UPLOADED_TRANS_DATE;
    }else{
        $TransDate=date('Y-m-d',strtotime($TransDate)); 
    }

    if($DTRID > 0){

        DB::table('payroll_dtr_temp')
            ->where('ID',$DTRID)
            ->update([
                'PayrollPeriodID' => $PayrollID,
                'TransactionDate' => $TransDate,
                'PayrollPeriodCode' => $PayrollCode,
                'Year' => $Year,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,
                'EmployeeRate' => $EmployeeRate,
                'RegularHours' => $RegularHours,
                'LateHours' => $LateHours,
                'UndertimeHours' => $UnderTimeHours,
                'NDHours' => $NDHours,
                'Absent' => $Absent,
                'Leave01' => $Leave01,
                'Leave02' => $Leave02,
                'Leave03' => $Leave03,
                'Leave04' => $Leave04,
                'Leave05' => $Leave05,
                'Leave06' => $Leave06,
                'Leave07' => $Leave07,
                'Leave08' => $Leave08,
                'Leave09' => $Leave09,
                'Leave10' => $Leave10,
                'Leave11' => $Leave11,
                'Leave12' => $Leave12,
                'Leave13' => $Leave13,
                'Leave14' => $Leave14,
                'Leave15' => $Leave15,
                'Leave16' => $Leave16,
                'Leave17' => $Leave17,
                'Leave18' => $Leave18,
                'Leave19' => $Leave19,
                'Leave20' => $Leave20,
                'OTHours01' => $OTHours01,
                'OTHours02' => $OTHours02,
                'OTHours03' => $OTHours03,
                'OTHours04' => $OTHours04,
                'OTHours05' => $OTHours05,
                'OTHours06' => $OTHours06,
                'OTHours07' => $OTHours07,
                'OTHours08' => $OTHours08,
                'OTHours09' => $OTHours09,
                'OTHours10' => $OTHours10,
                'OTHours11' => $OTHours11,
                'OTHours12' => $OTHours12,
                'OTHours13' => $OTHours13,
                'OTHours14' => $OTHours14,
                'OTHours15' => $OTHours15,
                'OTHours16' => $OTHours16,
                'OTHours17' => $OTHours17,
                'OTHours18' => $OTHours18,
                'OTHours19' => $OTHours19,
                'OTHours20' => $OTHours20,
                'OTHours21' => $OTHours21,
                'OTHours22' => $OTHours22,
                'OTHours23' => $OTHours23,
                'OTHours24' => $OTHours24,
                'OTHours25' => $OTHours25,
                'IsUploadError' => 0,
                'Status' => trim($Status)
            ]);

        $this->checkUploadedExcelRecordHasDuplicateRecord($PayrollID,$EmployeeID);

    }else{

        $IsUploadError=0;
        if($PayrollID==0 || $EmployeeID==0 ){
             $IsUploadError=1;
        }
   
        $DTRID = DB::table('payroll_dtr_temp')
            ->insertGetId([
                'PayrollPeriodID' => $PayrollID,
                'TransactionDate' => $TransDate,
                'Year' => $Year,
                'PayrollPeriodCode' => $PayrollCode,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,
                'EmployeeRate' => $EmployeeRate,
                'RegularHours' => $RegularHours,
                'LateHours' => $LateHours,
                'UndertimeHours' => $UnderTimeHours,
                'NDHours' => $NDHours,
                'Absent' => $Absent,
                'Leave01' => $Leave01,
                'Leave02' => $Leave02,
                'Leave03' => $Leave03,
                'Leave04' => $Leave04,
                'Leave05' => $Leave05,
                'Leave06' => $Leave06,
                'Leave07' => $Leave07,
                'Leave08' => $Leave08,
                'Leave09' => $Leave09,
                'Leave10' => $Leave10,
                'Leave11' => $Leave11,
                'Leave12' => $Leave12,
                'Leave13' => $Leave13,
                'Leave14' => $Leave14,
                'Leave15' => $Leave15,
                'Leave16' => $Leave16,
                'Leave17' => $Leave17,
                'Leave18' => $Leave18,
                'Leave19' => $Leave19,
                'Leave20' => $Leave20,
                'OTHours01' => $OTHours01,
                'OTHours02' => $OTHours02,
                'OTHours03' => $OTHours03,
                'OTHours04' => $OTHours04,
                'OTHours05' => $OTHours05,
                'OTHours06' => $OTHours06,
                'OTHours07' => $OTHours07,
                'OTHours08' => $OTHours08,
                'OTHours09' => $OTHours09,
                'OTHours10' => $OTHours10,
                'OTHours11' => $OTHours11,
                'OTHours12' => $OTHours12,
                'OTHours13' => $OTHours13,
                'OTHours14' => $OTHours14,
                'OTHours15' => $OTHours15,
                'OTHours16' => $OTHours16,
                'OTHours17' => $OTHours17,
                'OTHours18' => $OTHours18,
                'OTHours19' => $OTHours19,
                'OTHours20' => $OTHours20,
                'OTHours21' => $OTHours21,
                'OTHours22' => $OTHours22,
                'OTHours23' => $OTHours23,
                'OTHours24' => $OTHours24,
                'OTHours25' => $OTHours25,
                'Status' => trim($Status),
                'IsUploadError'=> $IsUploadError      
              ]);  

        $this->checkUploadedExcelRecordHasDuplicateRecord($PayrollID,$EmployeeID);
    }   

     return $DTRID;
}

public function doSaveDTRTempTransactionPerBatch($data){

    $Employee = new Employee();
    $EmployeeRate = new EmployeeRate();
    $PayrollPeriod= new PayrollPeriod();
    
    $TransDate = date("Y-m-d");
    $TODAY = date("Y-m-d H:i:s");
    $DTRTempDataItems = $data['DTRTempDataItems'];

    if(!empty($DTRTempDataItems)){

      for($x=0; $x< count($DTRTempDataItems); $x++) {
            
        $DTR_ID = $DTRTempDataItems[$x]["DTR_ID"];
        $Year = $DTRTempDataItems[$x]["Year"];

        $PayrollCode = $DTRTempDataItems[$x]["PayrollCode"];
        $PayrollID=0;
        if($PayrollCode!=''){
            $payroll_info = $PayrollPeriod->getPayrollPeriodScheduleInfoByCode($PayrollCode);
            if(isset($payroll_info)>0){
           $PayrollID=$payroll_info->ID;
        }
        }
        
        $EmployeeNo = $DTRTempDataItems[$x]["EmpNo"];
        $EmployeeID=0;
        if($EmployeeNo!=''){
            $emp_info = $Employee->getEmployeeInfo('ByEmployeeNo',$EmployeeNo);
            if(isset($emp_info)>0){
               $EmployeeID=$emp_info->employee_id;
            }  
        }

        $EmployeeRate=0;
        $EmployeeRateID=0;
        
        $retVal=$this->checkEmployeeRate($EmployeeNo);
        if(isset($retVal)>0){
            $EmployeeRateID=$retVal['EmployeeRateID'];
            $EmployeeRate=$retVal['EmployeeRate'];
        }
        
        //BASIC HOURS
        $RegularHours = $DTRTempDataItems[$x]["RegHours"];
        $LateHours = $DTRTempDataItems[$x]["LateHours"];
        $UnderTimeHours = $DTRTempDataItems[$x]["UnderTimeHours"];
        $NDHours = $DTRTempDataItems[$x]["NDHours"];
        $Absent = $DTRTempDataItems[$x]["Absent"];
        
        //LEAVE HOURS
        $Leave01 = $DTRTempDataItems[$x]["Leave01"];
        $Leave02 = $DTRTempDataItems[$x]["Leave02"];
        $Leave03 = $DTRTempDataItems[$x]["Leave03"];
        $Leave04 = $DTRTempDataItems[$x]["Leave04"];
        $Leave05 = $DTRTempDataItems[$x]["Leave05"];
        $Leave06 = $DTRTempDataItems[$x]["Leave06"];
        $Leave07 = $DTRTempDataItems[$x]["Leave07"];
        $Leave08 = $DTRTempDataItems[$x]["Leave08"];
        $Leave09 = $DTRTempDataItems[$x]["Leave09"];
        $Leave10 = $DTRTempDataItems[$x]["Leave10"];
        $Leave11 = $DTRTempDataItems[$x]["Leave11"];
        $Leave12 = $DTRTempDataItems[$x]["Leave12"];
        $Leave13 = $DTRTempDataItems[$x]["Leave13"];
        $Leave14 = $DTRTempDataItems[$x]["Leave14"];
        $Leave15 = $DTRTempDataItems[$x]["Leave15"];
        $Leave16 = $DTRTempDataItems[$x]["Leave16"];
        $Leave17 = $DTRTempDataItems[$x]["Leave17"];
        $Leave18 = $DTRTempDataItems[$x]["Leave18"];
        $Leave19 = $DTRTempDataItems[$x]["Leave19"];
        $Leave20 = $DTRTempDataItems[$x]["Leave20"];
        
        //OT HOURS
        $OTHours01 = $DTRTempDataItems[$x]["OTHours01"];
        $OTHours02 = $DTRTempDataItems[$x]["OTHours02"];
        $OTHours03 = $DTRTempDataItems[$x]["OTHours03"];
        $OTHours04 = $DTRTempDataItems[$x]["OTHours04"];
        $OTHours05 = $DTRTempDataItems[$x]["OTHours05"];
        $OTHours06 = $DTRTempDataItems[$x]["OTHours06"];
        $OTHours07 = $DTRTempDataItems[$x]["OTHours07"];
        $OTHours08 = $DTRTempDataItems[$x]["OTHours08"];
        $OTHours09 = $DTRTempDataItems[$x]["OTHours09"];
        $OTHours10 = $DTRTempDataItems[$x]["OTHours10"];
        $OTHours11 = $DTRTempDataItems[$x]["OTHours11"];
        $OTHours12 = $DTRTempDataItems[$x]["OTHours12"];
        $OTHours13 = $DTRTempDataItems[$x]["OTHours13"];
        $OTHours14 = $DTRTempDataItems[$x]["OTHours14"];
        $OTHours15 = $DTRTempDataItems[$x]["OTHours15"];
        $OTHours16 = $DTRTempDataItems[$x]["OTHours16"];
        $OTHours17 = $DTRTempDataItems[$x]["OTHours17"];
        $OTHours18 = $DTRTempDataItems[$x]["OTHours18"];
        $OTHours19 = $DTRTempDataItems[$x]["OTHours19"];
        $OTHours20 = $DTRTempDataItems[$x]["OTHours20"];
        $OTHours21 = $DTRTempDataItems[$x]["OTHours21"];
        $OTHours22 = $DTRTempDataItems[$x]["OTHours22"];
        $OTHours23 = $DTRTempDataItems[$x]["OTHours23"];
        $OTHours24 = $DTRTempDataItems[$x]["OTHours24"];
        $OTHours25 = $DTRTempDataItems[$x]["OTHours25"];
      
        $IsUploadError=0;
        if($PayrollID==0 || $EmployeeID==0){
             $IsUploadError=1;
        }
         if($EmployeeRateID==0 && $EmployeeRate==0){
             $IsUploadError=1;
        }

        $DTR_ID = DB::table('payroll_dtr_temp')
            ->insertGetId([
                'PayrollPeriodID' => $PayrollID,
                'TransactionDate' => $TransDate,
                'Year' => $Year,
                'PayrollPeriodCode' => $PayrollCode,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,
                'EmployeeRateID' => $EmployeeRateID,
                'EmployeeRate' => $EmployeeRate,
                'RegularHours' => $RegularHours,
                'LateHours' => $LateHours,
                'UndertimeHours' => $UnderTimeHours,
                'NDHours' => $NDHours,
                'Absent' => $Absent,
                'Leave01' => $Leave01,
                'Leave02' => $Leave02,
                'Leave03' => $Leave03,
                'Leave04' => $Leave04,
                'Leave05' => $Leave05,
                'Leave06' => $Leave06,
                'Leave07' => $Leave07,
                'Leave08' => $Leave08,
                'Leave09' => $Leave09,
                'Leave10' => $Leave10,
                'Leave11' => $Leave11,
                'Leave12' => $Leave12,
                'Leave13' => $Leave13,
                'Leave14' => $Leave14,
                'Leave15' => $Leave15,
                'Leave16' => $Leave16,
                'Leave17' => $Leave17,
                'Leave18' => $Leave18,
                'Leave19' => $Leave19,
                'Leave20' => $Leave20,
                'OTHours01' => $OTHours01,
                'OTHours02' => $OTHours02,
                'OTHours03' => $OTHours03,
                'OTHours04' => $OTHours04,
                'OTHours05' => $OTHours05,
                'OTHours06' => $OTHours06,
                'OTHours07' => $OTHours07,
                'OTHours08' => $OTHours08,
                'OTHours09' => $OTHours09,
                'OTHours10' => $OTHours10,
                'OTHours11' => $OTHours11,
                'OTHours12' => $OTHours12,
                'OTHours13' => $OTHours13,
                'OTHours14' => $OTHours14,
                'OTHours15' => $OTHours15,
                'OTHours16' => $OTHours16,
                'OTHours17' => $OTHours17,
                'OTHours18' => $OTHours18,
                'OTHours19' => $OTHours19,
                'OTHours20' => $OTHours20,
                'OTHours21' => $OTHours21,
                'OTHours22' => $OTHours22,
                'OTHours23' => $OTHours23,
                'OTHours24' => $OTHours24,
                'OTHours25' => $OTHours25,
                'Status' => 'Pending',                
                'IsUploadError'=> $IsUploadError,
                'UploadedByID'=> Session::get('ADMIN_USER_ID'),    
                'DateTimeUploaded'=> $TODAY
              ]);  

         $this->checkUploadedExcelRecordHasDuplicateRecord($PayrollID,$EmployeeID);      
      }
    }
   return "Success";
}

public function checkEmployeeRate($EmployeeNo){

    $EmployeeRate = new EmployeeRate();
   
   $EmployeeRateID=0;
   $MonthlyRate=0;
   $DailyRate=0;
   $HourlyRate=0;

   $EmployeeID=0;
   $retVal['EmployeeRateID']=0;
   $retVal['EmployeeRate']=0;

  $emp_info=DB::table('users')->where('shortid',$EmployeeNo)->first();
  if(isset($emp_info)>0){
    $EmployeeID=$emp_info->id;
  }

   $info = $EmployeeRate->getEmployeeRateInfo('ByEmployeeID',$EmployeeID);

    if(isset($info)>0){
        $EmployeeRateID=$EmployeeRateID=$info->EmployeeRateID;
        $MonthlyRate=$info->MonthlyRate;
        $DailyRate=$info->DailyRate;
        $HourlyRate=$info->HourlyRate;
    }
         
   if(isset($info)>0){
      $retVal['EmployeeRateID']=$info->EmployeeRateID;
      $retVal['EmployeeRate']=$info->HourlyRate;
  }
   
  return $retVal;
}

public function doSetDTRTransactionStatus($data){

$EmployeeID=0;
$EmployeeRateID=0;
$PayrollPeriodID=0;

$DTRID = $data['DTRID'];
$NewStatus = $data['NewStatus'];

   if($NewStatus=='Overwrite'){
       
       $info=$this->getEmployeeDTRInfo($DTRID);
         if(isset($info)>0){

             $EmployeeID=$info->EmployeeID;             
             $EmployeeRateID=$info->EmployeeRateID;
             $PayrollPeriodID=$info->PayrollPeriodID;

             if($EmployeeID>0 && $EmployeeRateID>0 && $PayrollPeriodID>0){
                 
                 //GET THE ID OF THE 1ST & OLD RECORD
                 $EXISTING_ID=DB::table('payroll_employee_dtr_summary')
                     ->where('EmployeeID',$EmployeeID)
                     ->where('EmployeeRateID',$EmployeeRateID)
                     ->where('PayrollPeriodID',$PayrollPeriodID)
                     ->orderBy('ID','ASC')
                     ->value('ID');
                    
                    //DELETE OLD 1ST RECORD AS DUPLICATE
                     DB::table('payroll_employee_dtr_summary')
                        ->whereRaw("ID=?",$EXISTING_ID)
                        ->delete();
                     
                   //SET NEW RECORD  
                   $NewStatus='Approved';           
                   DB::table('payroll_employee_dtr_summary')
                        ->where('ID',$DTRID)
                        ->update([
                            'Status' => 'Approved'
                        ]);
                }                
         }
   }else{      
      DB::table('payroll_employee_dtr_summary')
        ->where('ID',$DTRID)
        ->update([
            'Status' => $NewStatus
      ]);
   }

    //Save Transaction Log
    $Misc = new Misc();
    $logData['TransRefID'] = $DTRID;
    $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
    $logData['ModuleType'] = "Employee DTR";

    if($NewStatus=='Approved'){
        $logData['TransType'] = "Set Approved Employee DTR Information";
    }

    if($NewStatus=='Cancelled'){
        $logData['TransType'] = "Set Cancelled Employee DTR Information";
    }

    $logData['Remarks'] = "";
    $Misc->doSaveTransactionLog($logData);

     return "Success";
}

public function doSaveUploadFinalDTRDTransaction($data){


    $TODAY = date("Y-m-d H:i:s");

    $hasDataError=false;
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

    //add param only Uploaded By Admin
    $data['UploadedByID']= Session::get('ADMIN_USER_ID');

    $info_list=$this->getDTRTempList($data);
    //CHECK IF HAS DUPLICATE
    if(count($info_list)>0){
        foreach($info_list as $list){
            if($list->IsUploadError==2){
                $hasDataError=true;  
            }
        } 
    }


  if($hasDataError){
        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = "Sorry! Uploaded DTR Summary data cannot be saved. Please remove any duplicate records.";
    }else{
                 
       //CHECK UPLOADED DTR IN TEMP       
       $dtr_list=DB::table('payroll_dtr_temp')
             ->whereRaw('UploadedByID=?',Session::get('ADMIN_USER_ID'))
             ->orderBy('ID','DESC')
             ->get();

         if(count($dtr_list)>0){
                      
            foreach ($dtr_list as $item) {

             $chkEmployeeID=0;
             $chkEmployeeID=0;
             $chkEmployeeRateID=0;
                                     
             $chkEmployeeID=$item->EmployeeID;
             $chkEmployeeRateID=$item->EmployeeRateID;
             $chkPayrollPeriodID=$item->PayrollPeriodID;
            
            //PERFORM CLEAN AND CHECK FOR DUPLICATE 
            if($chkPayrollPeriodID>0 && $chkEmployeeID>0 && $chkEmployeeRateID>0){

                 $chkDTRInfo = DB::table('payroll_employee_dtr_summary')
                   ->whereRaw("PayrollPeriodID=?",$chkPayrollPeriodID)
                   ->whereRaw("EmployeeID=?",$chkEmployeeID)            
                   ->whereRaw("Status=?",'Approved')
                   ->orderBy('EmployeeID','ASC')
                   ->orderBy('PayrollPeriodID','ASC')
                   ->get();
            
                      //REMOVE DUPLICATE
                      // if(count($chkDTRInfo)>0){
                      //   DB::table('payroll_employee_dtr_summary')
                      //        ->whereRaw("PayrollPeriodID=?",$chkPayrollPeriodID)
                      //        ->whereRaw("EmployeeID=?",$chkEmployeeID)            
                      //        ->whereRaw("Status=?",'Approved')
                      //        ->delete();
                      //  }                                                     
                   }
              }                                                                                
        }   

       //BACTH INSERT SQL 
       DB::statement("insert into payroll_employee_dtr_summary(TransactionDate,Year,PayrollPeriodID,PayrollPeriodCode,EmployeeID,EmployeeNumber,EmployeeRateID,EmployeeRate,RegularHours,LateHours,UndertimeHours,NDHours,Absent,Leave01,Leave02,Leave03,Leave04,Leave05,Leave06,Leave07,Leave08,Leave09,Leave10,Leave11,Leave12,Leave13,Leave14,Leave15,Leave16,Leave17,Leave18,Leave19,Leave20,OTHours01,OTHours02,OTHours03,OTHours04,OTHours05,OTHours06,OTHours07,OTHours08,OTHours09,OTHours10,OTHours11,OTHours12,OTHours13,OTHours14,OTHours15,OTHours16,OTHours17,OTHours18,OTHours19,OTHours20,OTHours21,OTHours22,OTHours23,OTHours24,OTHours25,Status,CreatedByID,DateTimeCreated,UploadedByID) 
         Select TransactionDate,Year,PayrollPeriodID,PayrollPeriodCode,EmployeeID,EmployeeNumber,EmployeeRateID,EmployeeRate,RegularHours,LateHours,UndertimeHours,NDHours,Absent,Leave01,Leave02,Leave03,Leave04,Leave05,Leave06,Leave07,Leave08,Leave09,Leave10,Leave11,Leave12,Leave13,Leave14,Leave15,Leave16,Leave17,Leave18,Leave19,Leave20,OTHours01,OTHours02,OTHours03,OTHours04,OTHours05,OTHours06,OTHours07,OTHours08,OTHours09,OTHours10,OTHours11,OTHours12,OTHours13,OTHours14,OTHours15,OTHours16,OTHours17,OTHours18,OTHours19,OTHours20,OTHours21,OTHours22,OTHours23,OTHours24,OTHours25,'Approved',UploadedByID,DateTimeUploaded,UploadedByID from payroll_dtr_temp where IsUploadError=0 AND UploadedByID=?" ,[Session::get('ADMIN_USER_ID')]);

        //CLEAR TEMP DTR AFTER FINAL SAVE
        DB::table('payroll_dtr_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->delete();
       
        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Uploaded Employee DTR Summary has saved successfully.";

    } 
  
    return $RetVal;
}

public function doSaveEmployeeDTR($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $TODAY_UPLOADED_TRANS_DATE = date("Y-m-d");

    $DTRID=$data["DTRID"];

    $Year=$data["Year"];
    $TransDate=$data["TransDate"];

    $PayrollID=$data["PayrollID"];
    $PayrollCode=$data["PayrollCode"];

    $EmployeeID=$data["EmpID"];
    $EmployeeNo=$data["EmpNo"];
    
    $EmployeeRateID=$data["EmployeeRateID"];
    $EmployeeRate=$Misc->setNumeric($data["EmpRate"]);

    $RegularHours=$data["RegHours"];
    $LateHours=$data["LateHours"];
    $UnderTimeHours=$data["UnderTimeHours"];
    $NDHours=$data["NDHours"];
    $Absent=$data["Absent"];

    $Leave01=$data["Leave01"];
    $Leave02=$data["Leave02"];
    $Leave03=$data["Leave03"];
    $Leave04=$data["Leave04"];
    $Leave05=$data["Leave05"];
    $Leave06=$data["Leave06"];
    $Leave07=$data["Leave07"];
    $Leave08=$data["Leave08"];
    $Leave09=$data["Leave09"];
    $Leave10=$data["Leave10"];
    $Leave11=$data["Leave11"];
    $Leave12=$data["Leave12"];
    $Leave13=$data["Leave13"];
    $Leave14=$data["Leave14"];
    $Leave15=$data["Leave15"];
    $Leave16=$data["Leave16"];
    $Leave17=$data["Leave17"];
    $Leave18=$data["Leave18"];
    $Leave19=$data["Leave19"];
    $Leave20=$data["Leave20"];

    $OTHours01=$data["OTHours01"];
    $OTHours02=$data["OTHours02"];
    $OTHours03=$data["OTHours03"];
    $OTHours04=$data["OTHours04"];
    $OTHours05=$data["OTHours05"];
    $OTHours06=$data["OTHours06"];
    $OTHours07=$data["OTHours07"];
    $OTHours08=$data["OTHours08"];
    $OTHours09=$data["OTHours09"];
    $OTHours10=$data["OTHours10"];
    $OTHours11=$data["OTHours11"];
    $OTHours12=$data["OTHours12"];
    $OTHours13=$data["OTHours13"];
    $OTHours14=$data["OTHours14"];
    $OTHours15=$data["OTHours15"];
    $OTHours16=$data["OTHours16"];
    $OTHours17=$data["OTHours17"];
    $OTHours18=$data["OTHours18"];
    $OTHours19=$data["OTHours19"];
    $OTHours20=$data["OTHours20"];
    $OTHours21=$data["OTHours21"];
    $OTHours22=$data["OTHours22"];
    $OTHours23=$data["OTHours23"];
    $OTHours24=$data["OTHours24"];
    $OTHours25=$data["OTHours25"];

    $IsUploaded=$data["IsUploaded"];

    if($IsUploaded==1){
        $TransDate=$TODAY_UPLOADED_TRANS_DATE;
    }else{
        $TransDate=date('Y-m-d',strtotime($TransDate)); 
    }

    if($DTRID > 0){

        DB::table('payroll_employee_dtr_summary')
            ->where('ID',$DTRID)
            ->update([
                'PayrollPeriodID' => $PayrollID,
                'PayrollPeriodCode' => $PayrollCode,
                'TransactionDate' => $TransDate,
                'Year' => $Year,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,

                'EmployeeRateID' => $EmployeeRateID,
                'EmployeeRate' => $EmployeeRate,
                'RegularHours' => $RegularHours,
                'LateHours' => $LateHours,
                'UndertimeHours' => $UnderTimeHours,
                'NDHours' => $NDHours,
                'Absent' => $Absent,

                'Leave01' => $Leave01,
                'Leave02' => $Leave02,
                'Leave03' => $Leave03,
                'Leave04' => $Leave04,
                'Leave05' => $Leave05,
                'Leave06' => $Leave06,
                'Leave07' => $Leave07,
                'Leave08' => $Leave08,
                'Leave09' => $Leave09,
                'Leave10' => $Leave10,
                'Leave11' => $Leave11,
                'Leave12' => $Leave12,
                'Leave13' => $Leave13,
                'Leave14' => $Leave14,
                'Leave15' => $Leave15,
                'Leave16' => $Leave16,
                'Leave17' => $Leave17,
                'Leave18' => $Leave18,
                'Leave19' => $Leave19,
                'Leave20' => $Leave20,

                'OTHours01' => $OTHours01,
                'OTHours02' => $OTHours02,
                'OTHours03' => $OTHours03,
                'OTHours04' => $OTHours04,
                'OTHours05' => $OTHours05,
                'OTHours06' => $OTHours06,
                'OTHours07' => $OTHours07,
                'OTHours08' => $OTHours08,
                'OTHours09' => $OTHours09,
                'OTHours10' => $OTHours10,
                'OTHours11' => $OTHours11,
                'OTHours12' => $OTHours12,
                'OTHours13' => $OTHours13,
                'OTHours14' => $OTHours14,
                'OTHours15' => $OTHours15,
                'OTHours16' => $OTHours16,
                'OTHours17' => $OTHours17,
                'OTHours18' => $OTHours18,
                'OTHours19' => $OTHours19,
                'OTHours20' => $OTHours20,
                'OTHours21' => $OTHours21,
                'OTHours22' => $OTHours22,
                'OTHours23' => $OTHours23,
                'OTHours24' => $OTHours24,
                'OTHours25' => $OTHours25,
                'IsForUpload' => $IsUploaded,
                'IsUploadError' => 0

            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $DTRID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee DTR";
        $logData['TransType'] = "Update Employee DTR Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

                
        $DTRID = DB::table('payroll_employee_dtr_summary')
            ->insertGetId([
                'PayrollPeriodID' => $PayrollID,
                'TransactionDate' => $TransDate,
                'Year' => $Year,                
                'PayrollPeriodCode' => $PayrollCode,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,

                'EmployeeRateID' => $EmployeeRateID,
                'EmployeeRate' => $EmployeeRate,
                'RegularHours' => $RegularHours,
                'LateHours' => $LateHours,
                'UndertimeHours' => $UnderTimeHours,
                'NDHours' => $NDHours,
                'Absent' => $Absent,

                'Leave01' => $Leave01,
                'Leave02' => $Leave02,
                'Leave03' => $Leave03,
                'Leave04' => $Leave04,
                'Leave05' => $Leave05,
                'Leave06' => $Leave06,
                'Leave07' => $Leave07,
                'Leave08' => $Leave08,
                'Leave09' => $Leave09,
                'Leave10' => $Leave10,
                'Leave11' => $Leave11,
                'Leave12' => $Leave12,
                'Leave13' => $Leave13,
                'Leave14' => $Leave14,
                'Leave15' => $Leave15,
                'Leave16' => $Leave16,
                'Leave17' => $Leave17,
                'Leave18' => $Leave18,
                'Leave19' => $Leave19,
                'Leave20' => $Leave20,

                'OTHours01' => $OTHours01,
                'OTHours02' => $OTHours02,
                'OTHours03' => $OTHours03,
                'OTHours04' => $OTHours04,
                'OTHours05' => $OTHours05,
                'OTHours06' => $OTHours06,
                'OTHours07' => $OTHours07,
                'OTHours08' => $OTHours08,
                'OTHours09' => $OTHours09,
                'OTHours10' => $OTHours10,
                'OTHours11' => $OTHours11,
                'OTHours12' => $OTHours12,
                'OTHours13' => $OTHours13,
                'OTHours14' => $OTHours14,
                'OTHours15' => $OTHours15,
                'OTHours16' => $OTHours16,
                'OTHours17' => $OTHours17,
                'OTHours18' => $OTHours18,
                'OTHours19' => $OTHours19,
                'OTHours20' => $OTHours20,
                'OTHours21' => $OTHours21,
                'OTHours22' => $OTHours22,
                'OTHours23' => $OTHours23,
                'OTHours24' => $OTHours24,
                'OTHours25' => $OTHours25,
                'IsForUpload' => $IsUploaded,
                'Status' => 'Pending'
              ]);  

        
        //check if has error excel uploaded doesnt find employee id or payroll id 
        // if($IsUploaded==1){
        //     $this->checkUploadedExcelRecordHasDuplicateRecord($PayrollID,$EmployeeID);
        // }

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $DTRID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee DTR";

        if($IsUploaded==1){
            $logData['TransType'] = "Upload Employee DTR Information"; 
        }else{
         
           $logData['TransType'] = "New Employee DTR Information"; 
        }
      
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
      }
     return $DTRID;

    }

    public function doRemoveDuplicateDTRTempTransaction($tempID){
        $chkEmployeeID=0;
        $chkPayrollID=0;

        $info=$this->getDTRTempInfo($tempID);

        if(isset($info)>0){
            $chkEmployeeID=$info->EmployeeID;
            $chkPayrollID=$info->PayrollPeriodID;
            DB::table('payroll_dtr_temp')->where('ID',$tempID)->delete();
            $this->checkUploadedExcelRecordHasDuplicateRecord($chkPayrollID,$chkEmployeeID);
        }
    }

    //CHECK & REMOVE DUPLICATE IN TEMP DTR
    public function checkUploadedExcelRecordHasDuplicateRecord($PayrollPeriodID,$EmployeeID){
      
      if($PayrollPeriodID>0 && $EmployeeID>0){

            $info = DB::table('payroll_dtr_temp')
               ->where("PayrollPeriodID",$PayrollPeriodID)
               ->where("EmployeeID",$EmployeeID)
               ->where("Status",'Pending')
               ->get();
        
                if(count($info)>=2){

                    DB::table('payroll_dtr_temp')
                        ->where("PayrollPeriodID",$PayrollPeriodID)
                        ->where("EmployeeID",$EmployeeID)
                        ->where("Status",'Pending')

                        ->update([
                            'IsUploadError' => 2
                          ]);  
              }
              
              else{

                 DB::table('payroll_dtr_temp')
                    ->where("PayrollPeriodID",$PayrollPeriodID)
                    ->where("EmployeeID",$EmployeeID)
                    ->where("Status",'Pending')

                    ->update([
                        'IsUploadError' => 0
                      ]);  
              }
        }
    }

}

