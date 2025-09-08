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

class PayrollTransaction extends Model
{

  public function GetSettingsNextTransNo(){

    $info = DB::table('payroll_trans_no')
      ->selectraw("
        CAST(COALESCE(PayrollTransNo,'0') as INT) as CurrentNo
      ")
      ->first();  

    if(isset($info) > 0){
      $CurrentNo = $info->CurrentNo + 1;
      $CurrentNo = str_pad($CurrentNo, 5, "0", STR_PAD_LEFT);
      return $CurrentNo;
    }

    return 0;

  }

  public function SetSettingsNextTransNo($CurrentNo){
    $TODAY = date("Y-m-d H:i:s");

    DB::table('payroll_trans_no')
      ->update([
        'PayrollTransNo' => $CurrentNo
    ]);

    return true;

  }

  public function getPayrollTransactionList($param){

      $PayrollPeriodID = $param['PayrollPeriodID'];
      $BranchID = $param['BranchID'];
      $SearchText = trim($param['SearchText']);
      $Status = $param['Status'];
      $Limit = $param['Limit'];
      $PageNo = $param['PageNo'];

      $query = DB::table('payroll_transaction as paytrn')
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->leftjoin('users as usr', 'usr.id', '=', 'paytrn.EmployeeID')
        ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrn.BranchID')
        ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'paytrn.DepartmentID')
        ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrn.DivisionID')
        ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrn.SectionID')
        ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'paytrn.JobTypeID')
        ->selectraw("

            COALESCE(paytrn.ID,0) as ID,

            COALESCE(paytrn.TransNo,'') as TransNo,
            FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,

            COALESCE(paytrn.PayrollType,'') as PayrollType,

            COALESCE(paytrn.FilterType,'') as FilterType,

            COALESCE(paytrn.BranchID,0) as BranchID,
            COALESCE(brn.BranchName,'') as BranchName,

            COALESCE(paytrn.DivisionID,0) as DivisionID,
            COALESCE(div.Division,'') as Division,

            COALESCE(paytrn.DepartmentID,0) as DepartmentID,
            COALESCE(dept.Department,'') as Department,

            COALESCE(paytrn.SectionID,0) as SectionID,
            COALESCE(sec.Section,'') as Section,

            COALESCE(paytrn.JobTypeID,0) as JobTypeID,
            COALESCE(job.JobTitle,'') as JobTitle,

            COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
            COALESCE(period.Code,'') as PayrollPeriodCode,
            FORMAT(period.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
            FORMAT(period.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,
            COALESCE(period.CutOffID,'') as PayrollPeriodCutOffID,

            COALESCE(paytrn.EmployeeID,0) as EmployeeID,
            COALESCE(usr.shortid,'') as EmployeeNo,
            COALESCE(usr.first_name,'') as FirstName,
            COALESCE(usr.middle_name,'') as MiddleName,
            COALESCE(usr.last_name,'') as LastName,
            CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,

            COALESCE(paytrn.Remarks,'') as Remarks,

            COALESCE(paytrn.Status,'') as Status,

            CASE COALESCE(paytrn.Status,'')
                WHEN '".config('app.STATUS_PENDING')."'  THEN 1
                WHEN '".config('app.STATUS_APPROVED')."'  THEN 2
                WHEN '".config('app.STATUS_POSTED')."'  THEN 3
                WHEN '".config('app.STATUS_CANCELLED')."'  THEN 4
                ELSE 0
            END as SortOption
      ");

    if($BranchID > 0){
        $query->where("paytrn.BranchID",$BranchID);
    }

    if($PayrollPeriodID > 0){
      $query->where("paytrn.PayrollPeriodID",$PayrollPeriodID);
    }

    if(!empty($Status)){
      $query->where("paytrn.Status",$Status);
    }

    if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                      COALESCE(paytrn.TransNo,''),' ',
                      COALESCE(paytrn.PayrollType,''),' ',
                      COALESCE(brn.BranchName,''),' ',
                      COALESCE(div.Division,''),' ',
                      COALESCE(dept.Department,''),' ',
                      COALESCE(sec.Section,''),' ',
                      COALESCE(job.JobTitle,''),' ',
                      COALESCE(period.Code,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderByraw("CASE COALESCE(paytrn.Status,'')
                WHEN '".config('app.STATUS_PENDING')."'  THEN 1
                WHEN '".config('app.STATUS_APPROVED')."'  THEN 2
                WHEN '".config('app.STATUS_POSTED')."'  THEN 3
                WHEN '".config('app.STATUS_CANCELLED')."'  THEN 4
                ELSE 0
            END ASC");

    $list = $query->get();

    return $list;

  }

  public function getPayrollTransactionInfo($ID){

    $info = DB::table('payroll_transaction as paytrn')
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->leftjoin('users as usr', 'usr.id', '=', 'paytrn.EmployeeID')
        ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrn.BranchID')
        ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'paytrn.DepartmentID')
        ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrn.DivisionID')
        ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrn.SectionID')
        ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'paytrn.JobTypeID')
        ->selectraw("

            COALESCE(paytrn.ID,0) as ID,

            COALESCE(paytrn.TransNo,'') as TransNo,
            FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,

            COALESCE(paytrn.PayrollType,'') as PayrollType,

            COALESCE(paytrn.FilterType,'') as FilterType,

            COALESCE(paytrn.BranchID,0) as BranchID,
            COALESCE(brn.BranchName,'') as BranchName,

            COALESCE(paytrn.DivisionID,0) as DivisionID,
            COALESCE(div.Division,'') as Division,

            COALESCE(paytrn.DepartmentID,0) as DepartmentID,
            COALESCE(dept.Department,'') as Department,

            COALESCE(paytrn.SectionID,0) as SectionID,
            COALESCE(sec.Section,'') as Section,

            COALESCE(paytrn.JobTypeID,0) as JobTypeID,
            COALESCE(job.JobTitle,'') as JobTitle,

            COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
            COALESCE(period.Code,'') as PayrollPeriodCode,
            FORMAT(period.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
            FORMAT(period.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,
            COALESCE(period.CutOffID,'') as PayrollPeriodCutOffID,

            COALESCE(paytrn.EmployeeID,0) as EmployeeID,
            COALESCE(usr.shortid,'') as EmployeeNo,
            COALESCE(usr.first_name,'') as FirstName,
            COALESCE(usr.middle_name,'') as MiddleName,
            COALESCE(usr.last_name,'') as LastName,
            CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,

            COALESCE(paytrn.Remarks,'') as Remarks,

            COALESCE(paytrn.Status,'') as Status,

            CASE COALESCE(paytrn.Status,'')
                WHEN '".config('app.STATUS_PENDING')."'  THEN 1
                WHEN '".config('app.STATUS_APPROVED')."'  THEN 2
                WHEN '".config('app.STATUS_POSTED')."'  THEN 3
                WHEN '".config('app.STATUS_CANCELLED')."'  THEN 4
                ELSE 0
            END as SortOption
      ")
      ->where("paytrn.ID",$ID)
      ->first();

    return $info;

  }

  public function getPayrollTransactionInfoTransNo($TransNo){

    $info = DB::table('payroll_transaction as paytrn')
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->leftjoin('users as usr', 'usr.id', '=', 'paytrn.EmployeeID')
        ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrn.BranchID')
        ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'paytrn.DepartmentID')
        ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrn.DivisionID')
        ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrn.SectionID')
        ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'paytrn.JobTypeID')
        ->selectraw("

            COALESCE(paytrn.ID,0) as ID,

            COALESCE(paytrn.TransNo,'') as TransNo,
            FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,

            COALESCE(paytrn.PayrollType,'') as PayrollType,

            COALESCE(paytrn.FilterType,'') as FilterType,

            COALESCE(paytrn.BranchID,0) as BranchID,
            COALESCE(brn.BranchName,'') as BranchName,

            COALESCE(paytrn.DivisionID,0) as DivisionID,
            COALESCE(div.Division,'') as Division,

            COALESCE(paytrn.DepartmentID,0) as DepartmentID,
            COALESCE(dept.Department,'') as Department,

            COALESCE(paytrn.SectionID,0) as SectionID,
            COALESCE(sec.Section,'') as Section,

            COALESCE(paytrn.JobTypeID,0) as JobTypeID,
            COALESCE(job.JobTitle,'') as JobTitle,

            COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
            COALESCE(period.Code,'') as PayrollPeriodCode,
            FORMAT(period.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
            FORMAT(period.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,
            COALESCE(period.CutOffID,'') as PayrollPeriodCutOffID,

            COALESCE(paytrn.EmployeeID,0) as EmployeeID,
            COALESCE(usr.shortid,'') as EmployeeNo,
            COALESCE(usr.first_name,'') as FirstName,
            COALESCE(usr.middle_name,'') as MiddleName,
            COALESCE(usr.last_name,'') as LastName,
            CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,

            COALESCE(paytrn.Remarks,'') as Remarks,

            COALESCE(paytrn.Status,'') as Status,

            CASE COALESCE(paytrn.Status,'')
                WHEN '".config('app.STATUS_PENDING')."'  THEN 1
                WHEN '".config('app.STATUS_APPROVED')."'  THEN 2
                WHEN '".config('app.STATUS_POSTED')."'  THEN 3
                WHEN '".config('app.STATUS_CANCELLED')."'  THEN 4
                ELSE 0
            END as SortOption
      ")
      ->where("paytrn.TransNo",$TransNo)
      ->first();

    return $info;

  }

  public function getPayrollTransactionInfoByPeriod($PayrollPeriodID, $Status){

    $info = DB::table('payroll_transaction as paytrn')
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->leftjoin('users as usr', 'usr.id', '=', 'paytrn.EmployeeID')
        ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrn.BranchID')
        ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'paytrn.DepartmentID')
        ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrn.DivisionID')
        ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrn.SectionID')
        ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'paytrn.JobTypeID')
        ->selectraw("

            COALESCE(paytrn.ID,0) as ID,

            COALESCE(paytrn.TransNo,'') as TransNo,
            FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,

            COALESCE(paytrn.PayrollType,'') as PayrollType,

            COALESCE(paytrn.FilterType,'') as FilterType,

            COALESCE(paytrn.BranchID,0) as BranchID,
            COALESCE(brn.BranchName,'') as BranchName,

            COALESCE(paytrn.DivisionID,0) as DivisionID,
            COALESCE(div.Division,'') as Division,

            COALESCE(paytrn.DepartmentID,0) as DepartmentID,
            COALESCE(dept.Department,'') as Department,

            COALESCE(paytrn.SectionID,0) as SectionID,
            COALESCE(sec.Section,'') as Section,

            COALESCE(paytrn.JobTypeID,0) as JobTypeID,
            COALESCE(job.JobTitle,'') as JobTitle,

            COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
            COALESCE(period.Code,'') as PayrollPeriodCode,
            FORMAT(period.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
            FORMAT(period.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,
            COALESCE(period.CutOffID,'') as PayrollPeriodCutOffID,

            COALESCE(paytrn.EmployeeID,0) as EmployeeID,
            COALESCE(usr.shortid,'') as EmployeeNo,
            COALESCE(usr.first_name,'') as FirstName,
            COALESCE(usr.middle_name,'') as MiddleName,
            COALESCE(usr.last_name,'') as LastName,
            CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,

            COALESCE(paytrn.Remarks,'') as Remarks,

            COALESCE(paytrn.Status,'') as Status,

            CASE COALESCE(paytrn.Status,'')
                WHEN '".config('app.STATUS_PENDING')."'  THEN 1
                WHEN '".config('app.STATUS_APPROVED')."'  THEN 2
                WHEN '".config('app.STATUS_POSTED')."'  THEN 3
                WHEN '".config('app.STATUS_CANCELLED')."'  THEN 4
                ELSE 0
            END as SortOption
      ")
      ->where("paytrn.PayrollPeriodID",$PayrollPeriodID)
      ->where("paytrn.Status",$Status)
      ->first();

    return $info;

  }

  public function getPayrollTransactionEmployeeListByPeriod($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $pending = DB::table('payroll_transaction_employee_temp as paytrnemp')
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("

              COALESCE(paytrnemp.ID,0) as ID,

              COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
              COALESCE(paytrn.TransNo,'') as TransNo,
              FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
              COALESCE(paytrn.Status,'') as Status,

              COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
     
              COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
              COALESCE(usr.shortid,'') as EmployeeNo,
              COALESCE(usr.first_name,'') as FirstName,
              COALESCE(usr.middle_name,'') as MiddleName,
              COALESCE(usr.last_name,'') as LastName,
              CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,

              iif(COALESCE(usr.status,1) = 1, 'Active', 'Inactive') as EmployeeStatus,
              COALESCE(usr.contact_number,'') as ContactNumber,
              COALESCE(usr.email,'') as EmailAddress,

              COALESCE(paytrnemp.BranchID,0) as BranchID,
              COALESCE(brn.BranchName,'') as BranchName,

              COALESCE(dept.DivisionID,0) as DivisionID,
              COALESCE(div.Division,'') as Division,

              COALESCE(usr.department_id,0) as DepartmentID,
              COALESCE(dept.Department,'') as Department,

              COALESCE(sec.ID,0) as SectionID,
              COALESCE(sec.Section,'') as Section,

              COALESCE(usr.job_title_id,0) as JobTitleID,
              COALESCE(job.JobTitle,'') as JobTitle,

              COALESCE(usr.salary_type,0) as SalaryType,
              COALESCE(paytrnemp.MonthlyRate,0) as MonthlyRate,
              COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,
              COALESCE(paytrnemp.BasicSalary,0) as BasicSalary,
              COALESCE(paytrnemp.NightDifferential,0) as NightDifferential,
              COALESCE(paytrnemp.Overtime,0) as Overtime,
              COALESCE(paytrnemp.Leave,0) as Leave,
              COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as TotalOtherTaxableIncome,
              COALESCE(paytrnemp.TotalNonTaxableIncome,0) as TotalNonTaxableIncome,
              COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalEEInsurancePremiums,
              COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalOtherDeductions,
              COALESCE(paytrnemp.NetPay,0) as NetPay,
              COALESCE(paytrnemp.MinTakeHomePay,0) as MinTakeHomePay
          ")
          ->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

        if($SearchText != ''){
            $arSearchText = explode(" ",$SearchText);
            if(count($arSearchText) > 0){
                for($x=0; $x< count($arSearchText); $x++) {
                    $pending->whereraw(
                        "CONCAT_WS(' ',
                          COALESCE(usr.shortid,''),
                          COALESCE(usr.first_name,''),
                          COALESCE(usr.middle_name,''),
                          COALESCE(usr.last_name,'')
                        ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
                }
            }
        }

    $approved = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("
     
              COALESCE(paytrnemp.ID,0) as ID,

              COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
              COALESCE(paytrn.TransNo,'') as TransNo,
              FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
              COALESCE(paytrn.Status,'') as Status,
     
              COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,

              COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
              COALESCE(usr.shortid,'') as EmployeeNo,
              COALESCE(usr.first_name,'') as FirstName,
              COALESCE(usr.middle_name,'') as MiddleName,
              COALESCE(usr.last_name,'') as LastName,
              CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,
              iif(COALESCE(usr.status,1) = 1, 'Active', 'Inactive') as EmployeeStatus,
              COALESCE(usr.contact_number,'') as ContactNumber,
              COALESCE(usr.email,'') as EmailAddress,

              COALESCE(paytrnemp.BranchID,0) as BranchID,
              COALESCE(brn.BranchName,'') as BranchName,

              COALESCE(dept.DivisionID,0) as DivisionID,
              COALESCE(div.Division,'') as Division,

              COALESCE(usr.department_id,0) as DepartmentID,
              COALESCE(dept.Department,'') as Department,

              COALESCE(sec.ID,0) as SectionID,
              COALESCE(sec.Section,'') as Section,

              COALESCE(usr.job_title_id,0) as JobTitleID,
              COALESCE(job.JobTitle,'') as JobTitle,
     
              COALESCE(usr.salary_type,0) as SalaryType,
              COALESCE(paytrnemp.MonthlyRate,0) as MonthlyRate,
              COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,
              COALESCE(paytrnemp.BasicSalary,0) as BasicSalary,
              COALESCE(paytrnemp.NightDifferential,0) as NightDifferential,
              COALESCE(paytrnemp.Overtime,0) as Overtime,
              COALESCE(paytrnemp.Leave,0) as Leave,
              COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as TotalOtherTaxableIncome,
              COALESCE(paytrnemp.TotalNonTaxableIncome,0) as TotalNonTaxableIncome,
              COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalEEInsurancePremiums,
              COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalOtherDeductions,
              COALESCE(paytrnemp.NetPay,0) as NetPay,
              COALESCE(paytrnemp.MinTakeHomePay,0) as MinTakeHomePay
          ")
          ->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

    if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $approved->whereraw(
                    "CONCAT_WS(' ',
                      COALESCE(usr.shortid,''),
                      COALESCE(usr.first_name,''),
                      COALESCE(usr.middle_name,''),
                      COALESCE(usr.last_name,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    $final = $approved->union($pending);

    $query = $final->toSql();
    $query_final = DB::table(DB::raw("($query) as a"))->mergeBindings($final);

    if($Limit > 0){
      $query_final->limit($Limit);
      $query_final->offset(($PageNo-1) * $Limit);
    }
   
    $query_final->orderBy("FullName","ASC");
    
    $list = $query_final->get();

    return $list;

  }

  public function getPayrollTransactionEmployeeListByPeriodCount($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $pending = DB::table('payroll_transaction_employee_temp as paytrnemp')
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("
              COALESCE(paytrnemp.ID,0) as ID
          ")
          ->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

    if($Status != ''){
        $pending->whereraw('paytrn.Status = ?', [$Status]);
    }

    if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $pending->whereraw(
                    "CONCAT_WS(' ',
                      COALESCE(usr.shortid,''),
                      COALESCE(usr.first_name,''),
                      COALESCE(usr.middle_name,''),
                      COALESCE(usr.last_name,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    $approved = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("
              COALESCE(paytrnemp.ID,0) as ID
          ")
          ->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

    if($Status != ''){
        $approved->whereraw('paytrn.Status = ?', [$Status]);
    }

    if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $approved->whereraw(
                    "CONCAT_WS(' ',
                      COALESCE(usr.shortid,''),
                      COALESCE(usr.first_name,''),
                      COALESCE(usr.middle_name,''),
                      COALESCE(usr.last_name,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    $final = $approved->union($pending);

    $query = $final->toSql();
    $query_final = DB::table(DB::raw("($query) as a"))->mergeBindings($final);

    if($Limit > 0){
      $query_final->limit($Limit);
      $query_final->offset(($PageNo-1) * $Limit);
    }
   
    $list = $query_final->get();

    return $list;

  }


  public function getPayrollTransactionEmployeeList($param){

    $PayrollTransactionID = $param['PayrollTransactionID'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    if($Status == config('app.STATUS_PENDING')){
        $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
              ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
              ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
              ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
              ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
              ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
              ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
              ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
              ->selectraw("

                  COALESCE(paytrnemp.ID,0) as ID,

                  COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
                  COALESCE(paytrn.TransNo,'') as TransNo,
                  FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
                  COALESCE(paytrn.Status,'') as Status,

                  COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
         
                  COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
                  COALESCE(usr.shortid,'') as EmployeeNo,
                  COALESCE(usr.first_name,'') as FirstName,
                  COALESCE(usr.middle_name,'') as MiddleName,
                  COALESCE(usr.last_name,'') as LastName,
                  CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,

                  COALESCE(paytrnemp.BranchID,0) as BranchID,
                  COALESCE(brn.BranchName,'') as BranchName,

                  COALESCE(dept.DivisionID,0) as DivisionID,
                  COALESCE(div.Division,'') as Division,

                  COALESCE(usr.department_id,0) as DepartmentID,
                  COALESCE(dept.Department,'') as Department,

                  COALESCE(sec.ID,0) as SectionID,
                  COALESCE(sec.Section,'') as Section,

                  COALESCE(usr.job_title_id,0) as JobTitleID,
                  COALESCE(job.JobTitle,'') as JobTitle,

                  COALESCE(usr.salary_type,0) as SalaryType,
                  COALESCE(paytrnemp.MonthlyRate,0) as MonthlyRate,
                  COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,
                  COALESCE(paytrnemp.BasicSalary,0) as BasicSalary,
                  COALESCE(paytrnemp.NightDifferential,0) as NightDifferential,
                  COALESCE(paytrnemp.Overtime,0) as Overtime,
                  COALESCE(paytrnemp.Leave,0) as Leave,
                  COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as TotalOtherTaxableIncome,
                  COALESCE(paytrnemp.TotalNonTaxableIncome,0) as TotalNonTaxableIncome,
                  COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalEEInsurancePremiums,
                  COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalOtherDeductions,
                  COALESCE(paytrnemp.NetPay,0) as NetPay,
                  COALESCE(paytrnemp.MinTakeHomePay,0) as MinTakeHomePay
              ")
              ->where('paytrnemp.PayrollTransactionID',$PayrollTransactionID);

            if($SearchText != ''){
                $arSearchText = explode(" ",$SearchText);
                if(count($arSearchText) > 0){
                    for($x=0; $x< count($arSearchText); $x++) {
                        $query->whereraw(
                            "CONCAT_WS(' ',
                              COALESCE(usr.shortid,''),
                              COALESCE(usr.first_name,''),
                              COALESCE(usr.middle_name,''),
                              COALESCE(usr.last_name,'')
                            ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
                    }
                }
            }

            $query->orderBy("usr.first_name","ASC");
            $query->orderBy("usr.middle_name","ASC");
            $query->orderBy("usr.last_name","ASC");

    }else{
        
        $query = DB::table('payroll_transaction_employee as paytrnemp')
              ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
              ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
              ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
              ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
              ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
              ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
              ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
              ->selectraw("
         
                  COALESCE(paytrnemp.ID,0) as ID,

                  COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
                  COALESCE(paytrn.TransNo,'') as TransNo,
                  FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
                  COALESCE(paytrn.Status,'') as Status,
         
                  COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,

                  COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
                  COALESCE(usr.shortid,'') as EmployeeNo,
                  COALESCE(usr.first_name,'') as FirstName,
                  COALESCE(usr.middle_name,'') as MiddleName,
                  COALESCE(usr.last_name,'') as LastName,
                  CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,
                  iif(COALESCE(usr.status,1) = 1, 'Active', 'Inactive') as EmployeeStatus,
                  COALESCE(usr.contact_number,'') as ContactNumber,
                  COALESCE(usr.email,'') as EmailAddress,

                  COALESCE(paytrnemp.BranchID,0) as BranchID,
                  COALESCE(brn.BranchName,'') as BranchName,

                  COALESCE(dept.DivisionID,0) as DivisionID,
                  COALESCE(div.Division,'') as Division,

                  COALESCE(usr.department_id,0) as DepartmentID,
                  COALESCE(dept.Department,'') as Department,

                  COALESCE(sec.ID,0) as SectionID,
                  COALESCE(sec.Section,'') as Section,

                  COALESCE(usr.job_title_id,0) as JobTitleID,
                  COALESCE(job.JobTitle,'') as JobTitle,
         
                  COALESCE(usr.salary_type,0) as SalaryType,
                  COALESCE(paytrnemp.MonthlyRate,0) as MonthlyRate,
                  COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,
                  COALESCE(paytrnemp.BasicSalary,0) as BasicSalary,
                  COALESCE(paytrnemp.NightDifferential,0) as NightDifferential,
                  COALESCE(paytrnemp.Overtime,0) as Overtime,
                  COALESCE(paytrnemp.Leave,0) as Leave,
                  COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as TotalOtherTaxableIncome,
                  COALESCE(paytrnemp.TotalNonTaxableIncome,0) as TotalNonTaxableIncome,
                  COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalEEInsurancePremiums,
                  COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalOtherDeductions,
                  COALESCE(paytrnemp.NetPay,0) as NetPay,
                  COALESCE(paytrnemp.MinTakeHomePay,0) as MinTakeHomePay
              ")
              ->where('paytrnemp.PayrollTransactionID',$PayrollTransactionID);

            if($SearchText != ''){
                $arSearchText = explode(" ",$SearchText);
                if(count($arSearchText) > 0){
                    for($x=0; $x< count($arSearchText); $x++) {
                        $query->whereraw(
                            "CONCAT_WS(' ',
                              COALESCE(usr.shortid,''),
                              COALESCE(usr.first_name,''),
                              COALESCE(usr.middle_name,''),
                              COALESCE(usr.last_name,'')
                            ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
                    }
                }
            }

            $query->orderBy("usr.first_name","ASC");
            $query->orderBy("usr.middle_name","ASC");
            $query->orderBy("usr.last_name","ASC");

    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }


    $list = $query->get();

    return $list;

  }

  public function getPayrollTransactionEmployeeInfo($param){

    $PayrollTransactionID = $param['PayrollTransactionID'];
    $EmployeeID = $param['EmployeeID'];
    $Status = $param['Status'];

    if($Status == config('app.STATUS_PENDING')){
        $info = DB::table('payroll_transaction_employee_temp as paytrnemp')
              ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
              ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
              ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
              ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
              ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
              ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
              ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
              ->selectraw("
         
                  COALESCE(paytrnemp.ID,0) as ID,

                  COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
                  COALESCE(paytrn.TransNo,'') as TransNo,
                  FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
                  COALESCE(paytrn.Status,'') as Status,
         
                  COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,

                  COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
                  COALESCE(usr.shortid,'') as EmployeeNo,
                  COALESCE(usr.first_name,'') as FirstName,
                  COALESCE(usr.middle_name,'') as MiddleName,
                  COALESCE(usr.last_name,'') as LastName,
                  CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,
                  COALESCE(usr.contact_number,'') as ContactNumber,
                  COALESCE(usr.email,'') as EmailAddress,
                  iif(COALESCE(usr.status,1) = 1, 'Active', 'Inactive') as EmployeeStatus,

                  COALESCE(paytrnemp.BranchID,0) as BranchID,
                  COALESCE(brn.BranchName,'') as BranchName,

                  COALESCE(dept.DivisionID,0) as DivisionID,
                  COALESCE(div.Division,'') as Division,

                  COALESCE(usr.department_id,0) as DepartmentID,
                  COALESCE(dept.Department,'') as Department,

                  COALESCE(sec.ID,0) as SectionID,
                  COALESCE(sec.Section,'') as Section,

                  COALESCE(usr.job_title_id,0) as JobTitleID,
                  COALESCE(job.JobTitle,'') as JobTitle,

                  COALESCE(usr.salary_type,0) as SalaryType,
                  COALESCE(paytrnemp.MonthlyRate,0) as MonthlyRate,
                  COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,
                  COALESCE(paytrnemp.BasicSalary,0) as BasicSalary,
                  COALESCE(paytrnemp.NightDifferential,0) as NightDifferential,
                  COALESCE(paytrnemp.Overtime,0) as Overtime,
                  COALESCE(paytrnemp.Leave,0) as Leave,
                  COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as TotalOtherTaxableIncome,
                  COALESCE(paytrnemp.TotalNonTaxableIncome,0) as TotalNonTaxableIncome,
                  COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalEEInsurancePremiums,
                  COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalOtherDeductions,
                  COALESCE(paytrnemp.NetPay,0) as NetPay,
                  COALESCE(paytrnemp.MinTakeHomePay,0) as MinTakeHomePay
              ")
              ->where('paytrnemp.PayrollTransactionID',$PayrollTransactionID)
              ->where('paytrnemp.EmployeeID',$EmployeeID)
              ->first();

        return $info;

    }else{
        $info = DB::table('payroll_transaction_employee as paytrnemp')
              ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
              ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
              ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
              ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
              ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
              ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
              ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
              ->selectraw("
         
                  COALESCE(paytrnemp.ID,0) as ID,

                  COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
                  COALESCE(paytrn.TransNo,'') as TransNo,
                  FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
                  COALESCE(paytrn.Status,'') as Status,

                  COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
         
                  COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
                  COALESCE(usr.shortid,'') as EmployeeNo,
                  COALESCE(usr.first_name,'') as FirstName,
                  COALESCE(usr.middle_name,'') as MiddleName,
                  COALESCE(usr.last_name,'') as LastName,
                  CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,
                  COALESCE(usr.contact_number,'') as ContactNumber,
                  COALESCE(usr.email,'') as EmailAddress,
                  iif(COALESCE(usr.status,1) = 1, 'Active', 'Inactive') as EmployeeStatus,

                  COALESCE(paytrnemp.BranchID,0) as BranchID,
                  COALESCE(brn.BranchName,'') as BranchName,

                  COALESCE(dept.DivisionID,0) as DivisionID,
                  COALESCE(div.Division,'') as Division,

                  COALESCE(usr.department_id,0) as DepartmentID,
                  COALESCE(dept.Department,'') as Department,

                  COALESCE(sec.ID,0) as SectionID,
                  COALESCE(sec.Section,'') as Section,

                  COALESCE(usr.job_title_id,0) as JobTitleID,
                  COALESCE(job.JobTitle,'') as JobTitle,
         
                  COALESCE(usr.salary_type,0) as SalaryType,
                  COALESCE(paytrnemp.MonthlyRate,0) as MonthlyRate,
                  COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,
                  COALESCE(paytrnemp.BasicSalary,0) as BasicSalary,
                  COALESCE(paytrnemp.NightDifferential,0) as NightDifferential,
                  COALESCE(paytrnemp.Overtime,0) as Overtime,
                  COALESCE(paytrnemp.Leave,0) as Leave,
                  COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as TotalOtherTaxableIncome,
                  COALESCE(paytrnemp.TotalNonTaxableIncome,0) as TotalNonTaxableIncome,
                  COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalEEInsurancePremiums,
                  COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalOtherDeductions,
                  COALESCE(paytrnemp.NetPay,0) as NetPay,
                  COALESCE(paytrnemp.MinTakeHomePay,0) as MinTakeHomePay
              ")
              ->where('paytrnemp.PayrollTransactionID',$PayrollTransactionID)
              ->where('paytrnemp.EmployeeID',$EmployeeID)
              ->first();

        return $info;
    }

  }

  public function getPayrollTransactionEmployeeInfoByPeriod($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $EmployeeID = $param['EmployeeID'];

    $info = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("
     
              COALESCE(paytrnemp.ID,0) as ID,

              COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
              COALESCE(paytrn.TransNo,'') as TransNo,
              FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
              COALESCE(paytrn.Status,'') as Status,

              COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
     
              COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
              COALESCE(usr.shortid,'') as EmployeeNo,
              COALESCE(usr.first_name,'') as FirstName,
              COALESCE(usr.middle_name,'') as MiddleName,
              COALESCE(usr.last_name,'') as LastName,
              CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,
              COALESCE(usr.contact_number,'') as ContactNumber,
              COALESCE(usr.email,'') as EmailAddress,
              iif(COALESCE(usr.status,1) = 1, 'Active', 'Inactive') as EmployeeStatus,

              COALESCE(paytrnemp.BranchID,0) as BranchID,
              COALESCE(brn.BranchName,'') as BranchName,

              COALESCE(dept.DivisionID,0) as DivisionID,
              COALESCE(div.Division,'') as Division,

              COALESCE(usr.department_id,0) as DepartmentID,
              COALESCE(dept.Department,'') as Department,

              COALESCE(sec.ID,0) as SectionID,
              COALESCE(sec.Section,'') as Section,

              COALESCE(usr.job_title_id,0) as JobTitleID,
              COALESCE(job.JobTitle,'') as JobTitle,
     
              COALESCE(usr.salary_type,0) as SalaryType,
              COALESCE(paytrnemp.MonthlyRate,0) as MonthlyRate,
              COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,
              COALESCE(paytrnemp.BasicSalary,0) as BasicSalary,
              COALESCE(paytrnemp.NightDifferential,0) as NightDifferential,
              COALESCE(paytrnemp.Overtime,0) as Overtime,
              COALESCE(paytrnemp.Leave,0) as Leave,
              COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as TotalOtherTaxableIncome,
              COALESCE(paytrnemp.TotalNonTaxableIncome,0) as TotalNonTaxableIncome,
              COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalEEInsurancePremiums,
              COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalOtherDeductions,
              COALESCE(paytrnemp.NetPay,0) as NetPay,
              COALESCE(paytrnemp.MinTakeHomePay,0) as MinTakeHomePay
          ")
          ->where('paytrn.PayrollPeriodID',$PayrollPeriodID)
          ->where('paytrnemp.EmployeeID',$EmployeeID)
          ->first();

    return $info;

  }

  public function getPayrollTransactionEmployeeListByReferenceType($param){

    $EmployeeID = $param['EmployeeID'];
    $ReferenceType = $param['ReferenceType'];
    $DateStart = $param['DateStart'];
    $DateEnd = $param['DateEnd'];

    $query = DB::table('payroll_transaction_details as paytrndet')
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrndet.PayrollTransactionID')
          ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')
          ->selectraw("

              COALESCE(paytrndet.ID,0) as ID,

              COALESCE(paytrndet.PayrollTransactionID,0) as PayrollTransactionID,

              COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
              COALESCE(prd.Code,'') as PayrollPeriodCode,
              FORMAT(prd.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
              FORMAT(prd.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,
     
              COALESCE(paytrndet.Total,0) as Total
          ")
          ->where('paytrndet.EmployeeID',$EmployeeID)
          ->where('paytrn.Status','Approved')
          ->where('paytrndet.ReferenceType',$ReferenceType)
          ->whereBetween('prd.EndDate', [$DateStart, $DateEnd])
          ->orderBy("prd.StartDate","ASC");

    $list = $query->get();

    return $list;

  }

  public function getPayrollTransactionDetails($param){

    $PayrollTransactionID = $param['PayrollTransactionID'];
    $EmployeeID = $param['EmployeeID'];
    $Status = $param['Status'];

    if($Status == config('app.STATUS_PENDING')){
        $query = DB::table('payroll_transaction_details_temp as paytrndet')
              ->selectraw("
         
                  COALESCE(paytrndet.ID,0) as ID,

                  COALESCE(paytrndet.PayrollTransactionID,0) as PayrollTransactionID,
         
                  COALESCE(paytrndet.EmployeeID,0) as EmployeeID,

                  COALESCE(paytrndet.ReferenceType,'') as ReferenceType,
                  COALESCE(paytrndet.ReferenceID,0) as ReferenceID,

                  CASE COALESCE(paytrndet.ReferenceType,'')
                        WHEN 'Overtime' THEN ISNULL((SELECT TOP 1 Code FROM payroll_ot_rates WHERE ID = paytrndet.ReferenceID),'')

                        WHEN 'Leave' THEN ISNULL((SELECT TOP 1 LeaveType FROM payroll_leave_type WHERE ID = paytrndet.ReferenceID),'')

                        WHEN 'Income' THEN ISNULL((SELECT TOP 1 inc.[Name] 
                                                   FROM payroll_employee_income_deduction_transaction as inctrn 
                                                   INNER JOIN payroll_income_deduction_type as inc ON (inc.ID = inctrn.IncomeDeductionTypeID) 
                                                   WHERE inctrn.ID = paytrndet.ReferenceID),'')

                        WHEN 'Allowance' THEN ISNULL((SELECT TOP 1 allowancetype.[Name] 
                                                   FROM payroll_allowance_type as allowancetype 
                                                   WHERE allowancetype.ID = paytrndet.ReferenceID),'')

                        WHEN 'Advance' THEN ISNULL((SELECT TOP 1 CONCAT('Advance Ref. No. ', advtrn.VoucherNo) 
                                                   FROM payroll_employee_advance_transaction as advtrn
                                                   WHERE advtrn.ID = paytrndet.ReferenceID),'')

                        WHEN 'Loan' THEN ISNULL((SELECT TOP 1 loan.[Name] 
                                                   FROM payroll_employee_loan_transaction as loantrn
                                                   INNER JOIN payroll_loan_type as loan ON (loan.ID = loantrn.LoanTypeID) 
                                                   WHERE loantrn.ID = paytrndet.ReferenceID),'')

                        WHEN 'Deduction' THEN ISNULL((SELECT TOP 1 inc.[Name] 
                                                   FROM payroll_employee_income_deduction_transaction as inctrn 
                                                   INNER JOIN payroll_income_deduction_type as inc ON (inc.ID = inctrn.IncomeDeductionTypeID) 
                                                   WHERE inctrn.ID = paytrndet.ReferenceID),'')

                        ELSE COALESCE(paytrndet.ReferenceType,'')
                  END as Reference,

                  COALESCE(paytrndet.Qty,0) as Qty,
                  COALESCE(paytrndet.Amount,0) as Amount,
                  COALESCE(paytrndet.Total,0) as Total,
                  COALESCE(paytrndet.Percentage,0) as Percentage,
                  COALESCE(paytrndet.IsTaxable,0) as IsTaxable
              ")
              ->where('paytrndet.PayrollTransactionID',$PayrollTransactionID)
              ->where('paytrndet.EmployeeID',$EmployeeID);

            $query->orderByraw("CASE COALESCE(paytrndet.ReferenceType,'')
                        WHEN 'Basic Salary' THEN 1
                        WHEN 'Late Hours' THEN 2
                        WHEN 'Undertime Hours' THEN 3
                        WHEN 'Night Differential' THEN 4
                        WHEN 'Overtime' THEN 5
                        WHEN 'Leave' THEN 6
                        WHEN 'SSS EE Contribution' THEN 7
                        WHEN 'SSS ER Contribution' THEN 8
                        WHEN 'PHIC EE Contribution' THEN 9
                        WHEN 'PHIC ER Contribution' THEN 10
                        WHEN 'HDMF EE Contribution' THEN 11
                        WHEN 'HDMF ER Contribution' THEN 12
                        WHEN 'Withholding Tax' THEN 13
                        WHEN 'Income' THEN 14
                        WHEN 'Advance' THEN 15
                        WHEN 'Loan' THEN 16
                        WHEN 'Deduction' THEN 17
                        ELSE 18
                  END ASC");

    }else{
        
        $query = DB::table('payroll_transaction_details as paytrndet')
              ->selectraw("
         
                  COALESCE(paytrndet.ID,0) as ID,

                  COALESCE(paytrndet.PayrollTransactionID,0) as PayrollTransactionID,
         
                  COALESCE(paytrndet.EmployeeID,0) as EmployeeID,

                  COALESCE(paytrndet.ReferenceType,'') as ReferenceType,
                  COALESCE(paytrndet.ReferenceID,0) as ReferenceID,

                  CASE COALESCE(paytrndet.ReferenceType,'')
                        WHEN 'Overtime' THEN ISNULL((SELECT TOP 1 Code FROM payroll_ot_rates WHERE ID = paytrndet.ReferenceID),'')

                        WHEN 'Leave' THEN ISNULL((SELECT TOP 1 LeaveType FROM payroll_leave_type WHERE ID = paytrndet.ReferenceID),'')

                        WHEN 'Income' THEN ISNULL((SELECT TOP 1 inc.[Name] 
                                                   FROM payroll_employee_income_deduction_transaction as inctrn 
                                                   INNER JOIN payroll_income_deduction_type as inc ON (inc.ID = inctrn.IncomeDeductionTypeID) 
                                                   WHERE inctrn.ID = paytrndet.ReferenceID),'')

                        WHEN 'Allowance' THEN ISNULL((SELECT TOP 1 allowancetype.[Name] 
                                                   FROM payroll_allowance_type as allowancetype 
                                                   WHERE allowancetype.ID = paytrndet.ReferenceID),'')

                        WHEN 'Advance' THEN ISNULL((SELECT TOP 1 CONCAT('Advance Ref. No. ', advtrn.VoucherNo) 
                                                   FROM payroll_employee_advance_transaction as advtrn
                                                   WHERE advtrn.ID = paytrndet.ReferenceID),'')

                        WHEN 'Loan' THEN ISNULL((SELECT TOP 1 loan.[Name] 
                                                   FROM payroll_employee_loan_transaction as loantrn
                                                   INNER JOIN payroll_loan_type as loan ON (loan.ID = loantrn.LoanTypeID) 
                                                   WHERE loantrn.ID = paytrndet.ReferenceID),'')

                        WHEN 'Deduction' THEN ISNULL((SELECT TOP 1 inc.[Name] 
                                                   FROM payroll_employee_income_deduction_transaction as inctrn 
                                                   INNER JOIN payroll_income_deduction_type as inc ON (inc.ID = inctrn.IncomeDeductionTypeID) 
                                                   WHERE inctrn.ID = paytrndet.ReferenceID),'')

                        ELSE COALESCE(paytrndet.ReferenceType,'')
                  END as Reference,

                  COALESCE(paytrndet.Qty,0) as Qty,
                  COALESCE(paytrndet.Amount,0) as Amount,
                  COALESCE(paytrndet.Total,0) as Total,
                  COALESCE(paytrndet.Percentage,0) as Percentage,
                  COALESCE(paytrndet.IsTaxable,0) as IsTaxable
              ")
              ->where('paytrndet.PayrollTransactionID',$PayrollTransactionID)
              ->where('paytrndet.EmployeeID',$EmployeeID);

            $query->orderByraw("CASE COALESCE(paytrndet.ReferenceType,'')
                        WHEN 'Basic Salary' THEN 1
                        WHEN 'Late Hours' THEN 2
                        WHEN 'Undertime Hours' THEN 3
                        WHEN 'Night Differential' THEN 4
                        WHEN 'Overtime' THEN 5
                        WHEN 'SSS EE Contribution' THEN 6
                        WHEN 'SSS ER Contribution' THEN 7
                        WHEN 'PHIC EE Contribution' THEN 8
                        WHEN 'PHIC ER Contribution' THEN 9
                        WHEN 'HDMF EE Contribution' THEN 10
                        WHEN 'HDMF ER Contribution' THEN 11
                        WHEN 'Withholding Tax' THEN 12
                        WHEN 'Leave' THEN 13
                        WHEN 'Income' THEN 14
                        WHEN 'Advance' THEN 15
                        WHEN 'Loan' THEN 16
                        WHEN 'Deduction' THEN 17
                        ELSE 18
                  END ASC");

    }

    $list = $query->get();

    return $list;

  }

  public function doGeneratePayroll($data){
    
    $Misc = new Misc();
    
    $TODAY = date("Y-m-d H:i:s");

    $PayrollType = $data['PayrollType'];
    $PayrollPeriodID = $data['PayrollPeriodID'];
    $FilterType = $data['FilterType'];
    $BranchID = $data['BranchID'];
    $DivisionID = $data['DivisionID'];
    $DepartmentID = $data['DepartmentID'];
    $SectionID = $data['SectionID'];
    $JobTypeID = $data['JobTypeID'];
    $EmployeeID = $data['EmployeeID'];
    $Status = $data['Status'];
    $ProcessNo = $data['ProcessNo'];

    $PayrollTransactionID = 0;
    $PayrollTransactionInfo = $this->getPayrollTransactionInfoByPeriod($PayrollPeriodID, config('app.STATUS_PENDING'));
    if(isset($PayrollTransactionInfo)){
        $PayrollTransactionID = $PayrollTransactionInfo->ID;
    }

    if($ProcessNo == 1){
        if($PayrollTransactionID > 0){

            DB::table('payroll_transaction')
              ->where('ID',$PayrollTransactionID)
              ->update([
                'PayrollPeriodID' => $PayrollPeriodID,
                'PayrollType' => $PayrollType,
                'FilterType' => $FilterType,
                'BranchID' => $BranchID,
                'DivisionID' => $DivisionID,
                'DepartmentID' => $DepartmentID,
                'SectionID' => $SectionID,
                'JobTypeID' => $JobTypeID,
                'EmployeeID' => $EmployeeID,
                'Status' => $Status,
              ]);

            //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $PayrollTransactionID;
            $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
            $logData['ModuleType'] = "Payroll Transaction";
            $logData['TransType'] = "Update Payroll Transaction";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);

        }else{

            $TransNo = $this->GetSettingsNextTransNo();

            $PayrollTransactionID =  DB::table('payroll_transaction')
                ->insertGetId([

                  'TransNo' => $TransNo,
                  'TransDateTime' => $TODAY,

                  'PayrollPeriodID' => $PayrollPeriodID,
                  'PayrollType' => $PayrollType,
                    'FilterType' => $FilterType,
                    'BranchID' => $BranchID,
                    'DivisionID' => $DivisionID,
                    'DepartmentID' => $DepartmentID,
                    'SectionID' => $SectionID,
                    'JobTypeID' => $JobTypeID,
                  'EmployeeID' => $EmployeeID,

                  'Status' => $Status
              ]);

            //Update Number counter
            $this->SetSettingsNextTransNo($TransNo);

            //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $PayrollTransactionID;
            $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
            $logData['ModuleType'] = "Payroll Transaction";
            $logData['TransType'] = "New Order Information";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);
        }
    }

    DB::statement("SET NOCOUNT ON; 
                   exec spDoGeneratePayroll 
                        @PayrollTransactionID =  ?,
                        @PayrollPeriodID = ?, 
                        @PayrollType = ?, 
                        @BranchID = ?, 
                        @DivisionID = ?, 
                        @DepartmentID = ?, 
                        @SectionID = ?, 
                        @JobTypeID = ?, 
                        @EmployeeID = ?, 
                        @Status = ?,
                        @ProcessNo = ?",
                        array($PayrollTransactionID,
                              $PayrollPeriodID, 
                              $PayrollType, 
                              ($PayrollType == config('app.GENERATE_PAYROLL_BATCH') ? $BranchID : 0),  
                              ($PayrollType == config('app.GENERATE_PAYROLL_BATCH') ? $DivisionID : 0), 
                              ($PayrollType == config('app.GENERATE_PAYROLL_BATCH') ? $DepartmentID : 0), 
                              ($PayrollType == config('app.GENERATE_PAYROLL_BATCH') ? $SectionID : 0), 
                              ($PayrollType == config('app.GENERATE_PAYROLL_BATCH') ? $JobTypeID : 0), 
                              $EmployeeID, 
                              $Status,
                              $ProcessNo
                        )
                );

    return $PayrollTransactionID;

  }

  public function doRegenerateEmployeePayroll($data){
    
    $Misc = new Misc();
    
    $TODAY = date("Y-m-d H:i:s");

    $PayrollTransactionID = $data['PayrollTransactionID'];
    $PayrollType = $data['PayrollType'];
    $PayrollPeriodID = $data['PayrollPeriodID'];
    $BranchID = $data['BranchID'];
    $EmployeeID = $data['EmployeeID'];
    $Status = $data['Status'];

    if($PayrollTransactionID > 0){
        DB::statement("SET NOCOUNT ON; exec spDoGeneratePayroll @PayrollTransactionID =  ?, @PayrollPeriodID = ?, @PayrollType = ?, @BranchID = ?, @DivisionID = ?, @DepartmentID = ?, @SectionID = ?, @JobTypeID = ?, @EmployeeID = ?, @Status = ?",array($PayrollTransactionID,$PayrollPeriodID, $PayrollType,  0,  0, 0, 0, 0, $EmployeeID, $Status));
    }

    return $PayrollTransactionID;

  }

  public function doApproveGeneratedPayroll($data){
    
    $Misc = new Misc();
    
    $TODAY = date("Y-m-d H:i:s");

    $PayrollTransactionID = $data['PayrollTransactionID'];

    if($PayrollTransactionID > 0){

        DB::table('payroll_transaction')
          ->where('ID',$PayrollTransactionID)
          ->where('Status',config('app.STATUS_PENDING'))
          ->update([
            'Status' => config('app.STATUS_APPROVED'),
            'ApproveDateTime' => $TODAY
          ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $PayrollTransactionID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Payroll Transaction";
        $logData['TransType'] = "Approve Payroll Transaction";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

        DB::statement("SET NOCOUNT ON; exec spDoApproveGeneratedPayroll @PayrollTransactionID =  ?",array($PayrollTransactionID));


    }

    return $PayrollTransactionID;

  }

  public function doCancelGeneratedPayroll($data){
    
    $Misc = new Misc();
    
    $TODAY = date("Y-m-d H:i:s");

    $PayrollPeriodID = $data['PayrollPeriodID'];

    if($PayrollPeriodID > 0){

        DB::table('payroll_transaction')
          ->where('PayrollPeriodID',$PayrollPeriodID)
          ->update([
            'Status' => config('app.STATUS_CANCELLED')
          ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $PayrollPeriodID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Payroll Transaction";
        $logData['TransType'] = "Cancel Payroll Transaction";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

    return $PayrollPeriodID;

  }

//PAYROLL REGISTER SUMMARY REPORT
public function getPayrollRegisterSummary($param){

    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $PayrollPeriodID = $param['PayrollPeriodID'];

    $query = DB::table('payroll_transaction_employee as paytrnemp')
      ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
          $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
          $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
      })
      ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
      ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
            ->selectraw("
            paytrn.ID as PayrollTransactionID,
            paytrnemp.EmployeeID,
            emp.shortid as EmployeeNo,
              CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as EmployeeName,
            COALESCE(paytrnemp.BasicSalary,0) as BasicPay,

            SUM(COALESCE(paytrnincded.ECOLA,0)) as ECOLA,

            COALESCE(paytrnemp.LateUnderTime,0) as LateUTAbsent,

            COALESCE(paytrnemp.Leave1,0) as SL,

            COALESCE(paytrnemp.Leave2,0) as VL,

            COALESCE(paytrnemp.Leave,0) - COALESCE(paytrnemp.Leave1,0) - COALESCE(paytrnemp.Leave2,0) as OL,

            COALESCE(paytrnemp.NightDifferential,0) as NightDiff,

            COALESCE(paytrnemp.Overtime,0) as OTPay,

            COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as OtherTaxableEarnings,

            COALESCE(paytrnemp.TotalNonTaxableIncome,0) as OtherNonTaxableEarnings,

            (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as GrossPay,

            COALESCE(paytrnemp.SSSEEContribution,0) as SSS,
            COALESCE(paytrnemp.PHICEEContribution,0) as PHIC,
            COALESCE(paytrnemp.HDMFEEContribution,0) as HDMF,

            COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TaxableIncome,

            COALESCE(paytrnemp.WithholdingTax,0) as WTax,

            COALESCE(paytrnemp.TotalLoanDeductions,0) as LoanDeduction,

            COALESCE(paytrnemp.TotalDeductions,0) as OtherDeduction,

            COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalDeduction,

            COALESCE(paytrnemp.NetPay,0) as NetPay
            ");

       $query->where("paytrn.status",'Approved');  
       $query->whereRaw("paytrn.PayrollPeriodID=?",[$PayrollPeriodID]);     

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    // $query->orderBy("peit.ID","DESC");
    // $query->orderBy("pidt.Code","ASC");
    $list = $query->get();

    return $list;

    }

  public function getPayrollTransactionLoanDeduction($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $EmployeeID = $param['EmployeeID'];
    $BranchID = $param['BranchID'];
    $DivisionID = $param['DivisionID'];
    $DepartmentID = $param['DepartmentID'];
    $SectionID = $param['SectionID'];
    $JobTypeID = $param['JobTypeID'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("
     
              COALESCE(paytrnemp.ID,0) as ID,

              COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
              COALESCE(paytrn.TransNo,'') as TransNo,
              FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
              COALESCE(paytrn.Status,'') as Status,
     
              COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,

              COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
              COALESCE(usr.shortid,'') as EmployeeNo,
              COALESCE(usr.first_name,'') as FirstName,
              COALESCE(usr.middle_name,'') as MiddleName,
              COALESCE(usr.last_name,'') as LastName,
              CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,
              iif(COALESCE(usr.status,1) = 1, 'Active', 'Inactive') as EmployeeStatus,
              COALESCE(usr.contact_number,'') as ContactNumber,
              COALESCE(usr.email,'') as EmailAddress,

              COALESCE(paytrnemp.BranchID,0) as BranchID,
              COALESCE(brn.BranchName,'') as BranchName,

              COALESCE(dept.DivisionID,0) as DivisionID,
              COALESCE(div.Division,'') as Division,

              COALESCE(usr.department_id,0) as DepartmentID,
              COALESCE(dept.Department,'') as Department,

              COALESCE(sec.ID,0) as SectionID,
              COALESCE(sec.Section,'') as Section,

              COALESCE(usr.job_title_id,0) as JobTitleID,
              COALESCE(job.JobTitle,'') as JobTitle,
     
              COALESCE(usr.salary_type,0) as SalaryType,

              COALESCE(paytrnincded.Loan1,0) as SSSSalaryLoan,
              COALESCE(paytrnincded.Loan2,0) as SSSSalaryLoan2,
              COALESCE(paytrnincded.Loan3,0) as PAGIBIGLoan1,
              COALESCE(paytrnincded.Loan4,0) as PAGIBIGLoan2,
              COALESCE(paytrnincded.Loan5,0) as PAGIBIGLoan2_2,
              COALESCE(paytrnincded.Loan6,0) as SSSCondonation,
              COALESCE(paytrnincded.Loan7,0) as BMEAShare,
              COALESCE(paytrnincded.Loan8,0) as ElectricalLoan,
              COALESCE(paytrnincded.Loan9,0) as PAGIBIGCalamity,
              COALESCE(paytrnincded.Loan10,0) as HPV,
              COALESCE(paytrnincded.Loan11,0) as CashAdvance,
              COALESCE(paytrnincded.Loan12,0) as ReceivableEmployee,
              COALESCE(paytrnincded.Loan13,0) as SalaryLoan2,
              COALESCE(paytrnincded.Loan14,0) as PersonalInsurance3,
              COALESCE(paytrnincded.Loan15,0) as MortuaryAssistance,
              COALESCE(paytrnincded.Loan16,0) as ProvidentLoan3,
              COALESCE(paytrnincded.Loan17,0) as ProvidentLoan1,
              COALESCE(paytrnincded.Loan18,0) as ProvidentLoan2,
              COALESCE(paytrnincded.Loan19,0) as OtherDeduction,
              COALESCE(paytrnincded.Loan20,0) as SSSCalamityLoan,
              COALESCE(paytrnincded.Loan21,0) as ProvidentLoan3,
              COALESCE(paytrnincded.Loan22,0) as TaxAdjustment

          ");

        if($PayrollPeriodID > 0){
            $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);
        }

        if($EmployeeID > 0){
            $query->where('paytrnemp.EmployeeID',$EmployeeID);
        }

        if($BranchID > 0){
            $query->where('paytrnemp.BranchID',$BranchID);
        }

        if($DivisionID > 0){
            $query->where('dept.DivisionID',$DivisionID);
        }

        if($DepartmentID > 0){
            $query->where('usr.department_id',$DepartmentID);
        }

        if($SectionID > 0){
            $query->where('sec.ID',$SectionID);
        }

        if($JobTypeID > 0){
            $query->where('usr.job_title_id',$JobTypeID);
        }

        if($SearchText != ''){
            $arSearchText = explode(" ",$SearchText);
            if(count($arSearchText) > 0){
                for($x=0; $x< count($arSearchText); $x++) {
                    $query->whereraw(
                        "CONCAT_WS(' ',
                          COALESCE(usr.shortid,''),
                          COALESCE(usr.first_name,''),
                          COALESCE(usr.middle_name,''),
                          COALESCE(usr.last_name,'')
                        ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
                }
            }
        }

        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");
        $query->orderBy("usr.last_name","ASC");

        if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
        }

        $list = $query->get();

        return $list;

    }

  public function getPayrollTransactionOtherDeduction($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $EmployeeID = $param['EmployeeID'];
    $BranchID = $param['BranchID'];
    $DivisionID = $param['DivisionID'];
    $DepartmentID = $param['DepartmentID'];
    $SectionID = $param['SectionID'];
    $JobTypeID = $param['JobTypeID'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("
     
              COALESCE(paytrnemp.ID,0) as ID,

              COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
              COALESCE(paytrn.TransNo,'') as TransNo,
              FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
              COALESCE(paytrn.Status,'') as Status,
     
              COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,

              COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
              COALESCE(usr.shortid,'') as EmployeeNo,
              COALESCE(usr.first_name,'') as FirstName,
              COALESCE(usr.middle_name,'') as MiddleName,
              COALESCE(usr.last_name,'') as LastName,
              CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,
              iif(COALESCE(usr.status,1) = 1, 'Active', 'Inactive') as EmployeeStatus,
              COALESCE(usr.contact_number,'') as ContactNumber,
              COALESCE(usr.email,'') as EmailAddress,

              COALESCE(paytrnemp.BranchID,0) as BranchID,
              COALESCE(brn.BranchName,'') as BranchName,

              COALESCE(dept.DivisionID,0) as DivisionID,
              COALESCE(div.Division,'') as Division,

              COALESCE(usr.department_id,0) as DepartmentID,
              COALESCE(dept.Department,'') as Department,

              COALESCE(sec.ID,0) as SectionID,
              COALESCE(sec.Section,'') as Section,

              COALESCE(usr.job_title_id,0) as JobTitleID,
              COALESCE(job.JobTitle,'') as JobTitle,
     
              COALESCE(usr.salary_type,0) as SalaryType,

              COALESCE(paytrnincded.Deduction74,0) as UnionDues,
              COALESCE(paytrnincded.Deduction75,0) as UnionDuesAdjustments,
              COALESCE(paytrnincded.Deduction76,0) as UnionMembershipFee,
              COALESCE(paytrnincded.Deduction77,0) as MembershipID,
              COALESCE(paytrnincded.Deduction78,0) as StPeterLifePlanPremium,
              COALESCE(paytrnincded.Deduction79,0) as PGECCMembershipFee,
              COALESCE(paytrnincded.Deduction80,0) as ExcessMedicalBenefit,
              COALESCE(paytrnincded.Deduction81,0) as PGECCShares,
              COALESCE(paytrnincded.Deduction82,0) as UnliquidatedAdvances,
              COALESCE(paytrnincded.Deduction83,0) as MESS,
              COALESCE(paytrnincded.Deduction84,0) as Adjustment,
              COALESCE(paytrnincded.Deduction85,0) as ChargeableSupplies,
              COALESCE(paytrnincded.Deduction86,0) as PGECCLongTermLoan,
              COALESCE(paytrnincded.Deduction88,0) as AccomRental,
              COALESCE(paytrnincded.Deduction89,0) as AccomRentalAdjustment,
              COALESCE(paytrnincded.Deduction90,0) as MortuaryForNonUnionMember,
              COALESCE(paytrnincded.Deduction91,0) as NonBusinessAdvances17,
              COALESCE(paytrnincded.Deduction92,0) as NonBusinessAdvances18,
              COALESCE(paytrnincded.Deduction93,0) as AdditionalStatutoryDeduction,
              COALESCE(paytrnincded.Deduction94,0) as MotorcycleExpenses,
              COALESCE(paytrnincded.Deduction95,0) as PGECCConsumer,
              COALESCE(paytrnincded.Deduction96,0) as PGECCCarenderia,
              COALESCE(paytrnincded.Deduction97,0) as PGECCShortTermLoan
          ");

        if($PayrollPeriodID > 0){
            $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);
        }

        if($EmployeeID > 0){
            $query->where('paytrnemp.EmployeeID',$EmployeeID);
        }

        if($BranchID > 0){
            $query->where('paytrnemp.BranchID',$BranchID);
        }

        if($DivisionID > 0){
            $query->where('dept.DivisionID',$DivisionID);
        }

        if($DepartmentID > 0){
            $query->where('usr.department_id',$DepartmentID);
        }

        if($SectionID > 0){
            $query->where('sec.ID',$SectionID);
        }

        if($JobTypeID > 0){
            $query->where('usr.job_title_id',$JobTypeID);
        }

        if($SearchText != ''){
            $arSearchText = explode(" ",$SearchText);
            if(count($arSearchText) > 0){
                for($x=0; $x< count($arSearchText); $x++) {
                    $query->whereraw(
                        "CONCAT_WS(' ',
                          COALESCE(usr.shortid,''),
                          COALESCE(usr.first_name,''),
                          COALESCE(usr.middle_name,''),
                          COALESCE(usr.last_name,'')
                        ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
                }
            }
        }

        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");
        $query->orderBy("usr.last_name","ASC");

        if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
        }

        $list = $query->get();

        return $list;

    }



  public function getPayrollTransactionOtherEarningTaxable($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $EmployeeID = $param['EmployeeID'];
    $BranchID = $param['BranchID'];
    $DivisionID = $param['DivisionID'];
    $DepartmentID = $param['DepartmentID'];
    $SectionID = $param['SectionID'];
    $JobTypeID = $param['JobTypeID'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("
     
              COALESCE(paytrnemp.ID,0) as ID,

              COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
              COALESCE(paytrn.TransNo,'') as TransNo,
              FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
              COALESCE(paytrn.Status,'') as Status,
     
              COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,

              COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
              COALESCE(usr.shortid,'') as EmployeeNo,
              COALESCE(usr.first_name,'') as FirstName,
              COALESCE(usr.middle_name,'') as MiddleName,
              COALESCE(usr.last_name,'') as LastName,
              CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,
              iif(COALESCE(usr.status,1) = 1, 'Active', 'Inactive') as EmployeeStatus,
              COALESCE(usr.contact_number,'') as ContactNumber,
              COALESCE(usr.email,'') as EmailAddress,

              COALESCE(paytrnemp.BranchID,0) as BranchID,
              COALESCE(brn.BranchName,'') as BranchName,

              COALESCE(dept.DivisionID,0) as DivisionID,
              COALESCE(div.Division,'') as Division,

              COALESCE(usr.department_id,0) as DepartmentID,
              COALESCE(dept.Department,'') as Department,

              COALESCE(sec.ID,0) as SectionID,
              COALESCE(sec.Section,'') as Section,

              COALESCE(usr.job_title_id,0) as JobTitleID,
              COALESCE(job.JobTitle,'') as JobTitle,
     
              COALESCE(usr.salary_type,0) as SalaryType,

              COALESCE(paytrnincded.Income99,0) as UnpaidRegualrHours,
              COALESCE(paytrnincded.Income100,0) as RetroAdjustment,
              COALESCE(paytrnincded.Income101,0) as OvertimeAdjustment,
              COALESCE(paytrnincded.Income102,0) as UnusedVacationLeaveTaxable,
              COALESCE(paytrnincded.Income104,0) as VacationLeaveAdjustment,
              COALESCE(paytrnincded.Income105,0) as SickLeaveTaxable,
              COALESCE(paytrnincded.Income106,0) as SickLeaveAdjustment,
              COALESCE(paytrnincded.Income107,0) as PaternityLeave,
              COALESCE(paytrnincded.Income108,0) as EmergencyLeave,
              COALESCE(paytrnincded.Income109,0) as HILTOIL,
              COALESCE(paytrnincded.Income110,0) as SeparationPayTaxable,
              COALESCE(paytrnincded.Income113,0) as HolidayPay,
              COALESCE(paytrnincded.Income119,0) as MaternityBenefit,
              COALESCE(paytrnincded.Income120,0) as ThirteenMonthPayTaxable,
              COALESCE(paytrnincded.Income122,0) as ThirteenMonthPayAdjustment,

              0 as AdjustmentPrevPayroll

          ");

        if($PayrollPeriodID > 0){
            $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);
        }

        if($EmployeeID > 0){
            $query->where('paytrnemp.EmployeeID',$EmployeeID);
        }

        if($BranchID > 0){
            $query->where('paytrnemp.BranchID',$BranchID);
        }

        if($DivisionID > 0){
            $query->where('dept.DivisionID',$DivisionID);
        }

        if($DepartmentID > 0){
            $query->where('usr.department_id',$DepartmentID);
        }

        if($SectionID > 0){
            $query->where('sec.ID',$SectionID);
        }

        if($JobTypeID > 0){
            $query->where('usr.job_title_id',$JobTypeID);
        }

        if($SearchText != ''){
            $arSearchText = explode(" ",$SearchText);
            if(count($arSearchText) > 0){
                for($x=0; $x< count($arSearchText); $x++) {
                    $query->whereraw(
                        "CONCAT_WS(' ',
                          COALESCE(usr.shortid,''),
                          COALESCE(usr.first_name,''),
                          COALESCE(usr.middle_name,''),
                          COALESCE(usr.last_name,'')
                        ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
                }
            }
        }

        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");
        $query->orderBy("usr.last_name","ASC");

        if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
        }

        $list = $query->get();

        return $list;

    }

  public function getPayrollTransactionOtherEarningNonTaxable($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $EmployeeID = $param['EmployeeID'];
    $BranchID = $param['BranchID'];
    $DivisionID = $param['DivisionID'];
    $DepartmentID = $param['DepartmentID'];
    $SectionID = $param['SectionID'];
    $JobTypeID = $param['JobTypeID'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("
     
              COALESCE(paytrnemp.ID,0) as ID,

              COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
              COALESCE(paytrn.TransNo,'') as TransNo,
              FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
              COALESCE(paytrn.Status,'') as Status,
     
              COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,

              COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
              COALESCE(usr.shortid,'') as EmployeeNo,
              COALESCE(usr.first_name,'') as FirstName,
              COALESCE(usr.middle_name,'') as MiddleName,
              COALESCE(usr.last_name,'') as LastName,
              CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,
              iif(COALESCE(usr.status,1) = 1, 'Active', 'Inactive') as EmployeeStatus,
              COALESCE(usr.contact_number,'') as ContactNumber,
              COALESCE(usr.email,'') as EmailAddress,

              COALESCE(paytrnemp.BranchID,0) as BranchID,
              COALESCE(brn.BranchName,'') as BranchName,

              COALESCE(dept.DivisionID,0) as DivisionID,
              COALESCE(div.Division,'') as Division,

              COALESCE(usr.department_id,0) as DepartmentID,
              COALESCE(dept.Department,'') as Department,

              COALESCE(sec.ID,0) as SectionID,
              COALESCE(sec.Section,'') as Section,

              COALESCE(usr.job_title_id,0) as JobTitleID,
              COALESCE(job.JobTitle,'') as JobTitle,
     
              COALESCE(usr.salary_type,0) as SalaryType,

              COALESCE(paytrnincded.Income103,0) as UnusedVacationLeaveNonTaxable,
              COALESCE(paytrnincded.Income111,0) as RedundancyPay,
              COALESCE(paytrnincded.Income112,0) as ServiceIncentivePay,
              COALESCE(paytrnincded.Income114,0) as RiceAllowance,
              COALESCE(paytrnincded.Income115,0) as ManwayIncentives,
              COALESCE(paytrnincded.Income116,0) as TrainingAllowance,
              COALESCE(paytrnincded.Income117,0) as MealAllowance,
              COALESCE(paytrnincded.Income121,0) as ThirteenMonthPayNonoTaxable,
              COALESCE(paytrnincded.Income123,0) as ProductionIncentives,
              COALESCE(paytrnincded.Income125,0) as Lighting,
              COALESCE(paytrnincded.Income126,0) as Housing,
              COALESCE(paytrnincded.Income127,0) as UnusedSickLeaveTaxable,
              COALESCE(paytrnincded.Income129,0) as UnionAllowanceAdjustments,
              COALESCE(paytrnincded.Income130,0) as OtherEarning32,
              COALESCE(paytrnincded.Income131,0) as HousingAdjustment,
              COALESCE(paytrnincded.Income132,0) as LightingAdjustment,
              0 as UnpaidEarningPrevPayroll,
              COALESCE(paytrnincded.Income118,0) as MealAllowanceAdjustment,
              COALESCE(paytrnincded.ECOLA,0) as ECOLA

          ");

        if($PayrollPeriodID > 0){
            $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);
        }

        if($EmployeeID > 0){
            $query->where('paytrnemp.EmployeeID',$EmployeeID);
        }

        if($BranchID > 0){
            $query->where('paytrnemp.BranchID',$BranchID);
        }

        if($DivisionID > 0){
            $query->where('dept.DivisionID',$DivisionID);
        }

        if($DepartmentID > 0){
            $query->where('usr.department_id',$DepartmentID);
        }

        if($SectionID > 0){
            $query->where('sec.ID',$SectionID);
        }

        if($JobTypeID > 0){
            $query->where('usr.job_title_id',$JobTypeID);
        }

        if($SearchText != ''){
            $arSearchText = explode(" ",$SearchText);
            if(count($arSearchText) > 0){
                for($x=0; $x< count($arSearchText); $x++) {
                    $query->whereraw(
                        "CONCAT_WS(' ',
                          COALESCE(usr.shortid,''),
                          COALESCE(usr.first_name,''),
                          COALESCE(usr.middle_name,''),
                          COALESCE(usr.last_name,'')
                        ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
                }
            }
        }

        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");
        $query->orderBy("usr.last_name","ASC");

        if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
        }

        $list = $query->get();

        return $list;

    }



}

