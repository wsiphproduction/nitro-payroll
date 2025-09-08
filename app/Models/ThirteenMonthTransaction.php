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

class ThirteenMonthTransaction extends Model
{

  public function GetSettingsNextTransNo(){

    $info = DB::table('payroll_trans_no')
      ->selectraw("
        CAST(COALESCE(ThirteenMonthTransNo,'0') as INT) as CurrentNo
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
        'ThirteenMonthTransNo' => $CurrentNo
    ]);

    return true;

  }

  public function get13thMonthTransactionList($param){

      $PayrollPeriodID = $param['PayrollPeriodID'];
      $BranchID = $param['BranchID'];
      $SearchText = trim($param['SearchText']);
      $Status = $param['Status'];
      $Limit = $param['Limit'];
      $PageNo = $param['PageNo'];

      $query = DB::table('payroll_trans_13thmonthpay as paytrn')
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrn.BranchID')
        ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'paytrn.DepartmentID')
        ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrn.DivisionID')
        ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrn.SectionID')
        ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'paytrn.JobTypeID')
        ->selectraw("

            COALESCE(paytrn.ID,0) as ID,

            COALESCE(paytrn.TransNo,'') as TransNo,
            FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
            FORMAT(paytrn.ApproveDateTime, 'MM/dd/yyyy hh:mm:dd t') as ApproveDateTime,

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

  public function get13thMonthTransactionInfo($ID){

    $info = DB::table('payroll_trans_13thmonthpay as paytrn')
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrn.BranchID')
        ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'paytrn.DepartmentID')
        ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrn.DivisionID')
        ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrn.SectionID')
        ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'paytrn.JobTypeID')
        ->selectraw("

            COALESCE(paytrn.ID,0) as ID,

            COALESCE(paytrn.TransNo,'') as TransNo,
            FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
            FORMAT(paytrn.ApproveDateTime, 'MM/dd/yyyy hh:mm:dd t') as ApproveDateTime,

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

  public function get13thMonthTransactionInfoTransNo($TransNo){

    $info = DB::table('payroll_trans_13thmonthpay as paytrn')
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrn.BranchID')
        ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'paytrn.DepartmentID')
        ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrn.DivisionID')
        ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrn.SectionID')
        ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'paytrn.JobTypeID')
        ->selectraw("

            COALESCE(paytrn.ID,0) as ID,

            COALESCE(paytrn.TransNo,'') as TransNo,
            FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
            FORMAT(paytrn.ApproveDateTime, 'MM/dd/yyyy hh:mm:dd t') as ApproveDateTime,

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

  public function get13thMonthTransactionEmployeeList($param){

    $PayrollTransactionID = $param['PayrollTransactionID'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    if($Status == config('app.STATUS_PENDING')){
        $query = DB::table('payroll_trans_13thmonthpay_employee_temp as paytrnemp')
              ->join('payroll_trans_13thmonthpay as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
              ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
              ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
              ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
              ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          
              ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
              ->join('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
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

                  COALESCE(paytrnemp.TotalBasicSalary,0) as TotalBasicSalary,
                  COALESCE(paytrnemp.TotalLeaves,0) as TotalLeaves,
                  COALESCE(paytrnemp.TotalLate,0) as TotalLate,
                  COALESCE(paytrnemp.TotalUndertime,0) as TotalUndertime,
                  COALESCE(paytrnemp.Balance,0) as Balance,
                  COALESCE(paytrnemp.NetPay,0) as NetPay
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
        $query = DB::table('payroll_trans_13thmonthpay_employee as paytrnemp')
              ->join('payroll_trans_13thmonthpay as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
              ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
              ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
              ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
              ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
              ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
              ->join('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
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
         
                  COALESCE(paytrnemp.TotalBasicSalary,0) as TotalBasicSalary,
                  COALESCE(paytrnemp.TotalLeaves,0) as TotalLeaves,
                  COALESCE(paytrnemp.TotalLate,0) as TotalLate,
                  COALESCE(paytrnemp.TotalUndertime,0) as TotalUndertime,
                  COALESCE(paytrnemp.Balance,0) as Balance,
                  COALESCE(paytrnemp.NetPay,0) as NetPay
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

  public function get13thMonthTransactionEmployeeInfo($param){

    $PayrollTransactionID = $param['PayrollTransactionID'];
    $EmployeeID = $param['EmployeeID'];
    $Status = $param['Status'];

    if($Status == config('app.STATUS_PENDING')){
        $info = DB::table('payroll_trans_13thmonthpay_employee_temp as paytrnemp')
              ->join('payroll_trans_13thmonthpay as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
              ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
              ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
              ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
              ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
              ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
              ->join('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
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

                  COALESCE(paytrnemp.TotalBasicSalary,0) as TotalBasicSalary,
                  COALESCE(paytrnemp.TotalLeaves,0) as TotalLeaves,
                  COALESCE(paytrnemp.TotalLate,0) as TotalLate,
                  COALESCE(paytrnemp.TotalUndertime,0) as TotalUndertime,
                  COALESCE(paytrnemp.Balance,0) as Balance,
                  COALESCE(paytrnemp.NetPay,0) as NetPay
              ")
              ->where('paytrnemp.PayrollTransactionID',$PayrollTransactionID)
              ->where('paytrnemp.EmployeeID',$EmployeeID)
              ->first();

        return $info;

    }else{
        $info = DB::table('payroll_trans_13thmonthpay_employee as paytrnemp')
              ->join('payroll_trans_13thmonthpay as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
              ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
              ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
              ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
              ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
              ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
              ->join('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
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
         
                  COALESCE(paytrnemp.TotalBasicSalary,0) as TotalBasicSalary,
                  COALESCE(paytrnemp.TotalLeaves,0) as TotalLeaves,
                  COALESCE(paytrnemp.TotalLate,0) as TotalLate,
                  COALESCE(paytrnemp.TotalUndertime,0) as TotalUndertime,
                  COALESCE(paytrnemp.Balance,0) as Balance,
                  COALESCE(paytrnemp.NetPay,0) as NetPay
              ")
              ->where('paytrnemp.PayrollTransactionID',$PayrollTransactionID)
              ->where('paytrnemp.EmployeeID',$EmployeeID)
              ->first();

        return $info;
    }

  }

  public function doGenerate13thMonthTransaction($data){
    
    $Misc = new Misc();
    
    $TODAY = date("Y-m-d H:i:s");

    $PayrollTransactionID = $data['PayrollTransactionID'];
    $PayrollPeriodID = $data['PayrollPeriodID'];
    $PayrollType = $data['PayrollType'];
    $FilterType = $data['FilterType'];
    $BranchID = $data['BranchID'];
    $DivisionID = $data['DivisionID'];
    $DepartmentID = $data['DepartmentID'];
    $SectionID = $data['SectionID'];
    $JobTypeID = $data['JobTypeID'];
    $Remarks = $data['Remarks'];
    $Status = $data['Status'];

    if($PayrollTransactionID > 0){

        DB::table('payroll_trans_13thmonthpay')
          ->where('ID',$PayrollTransactionID)
          ->update([
            'PayrollPeriodID' => $PayrollPeriodID,
            'FilterType' => $FilterType,
            'BranchID' => $BranchID,
            'DivisionID' => $DivisionID,
            'DepartmentID' => $DepartmentID,
            'SectionID' => $SectionID,
            'JobTypeID' => $JobTypeID,
            'Remarks' => $Remarks,
            'Status' => $Status
          ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $PayrollTransactionID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "13th Month Transaction";
        $logData['TransType'] = "Update 13th Month Transaction";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }else{

        $TransNo = $this->GetSettingsNextTransNo();

        $PayrollTransactionID =  DB::table('payroll_trans_13thmonthpay')
            ->insertGetId([

              'TransNo' => $TransNo,
              'TransDateTime' => $TODAY,

              'PayrollPeriodID' => $PayrollPeriodID,
              'FilterType' => $FilterType,
              'BranchID' => $BranchID,
              'DivisionID' => $DivisionID,
              'DepartmentID' => $DepartmentID,
              'SectionID' => $SectionID,
              'JobTypeID' => $JobTypeID,
              'Remarks' => $Remarks,
              'Status' => $Status
          ]);

        //Update Number counter
        $this->SetSettingsNextTransNo($TransNo);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $PayrollTransactionID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "13th Month Transaction";
        $logData['TransType'] = "New 13th Month";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

    DB::statement("SET NOCOUNT ON; exec spDoGenerate13thMonthPay @PayrollTransactionID =  ?, @PayrollPeriodID = ?, @PayrollType = ?, @BranchID = ?, @DivisionID = ?, @DepartmentID = ?, @SectionID = ?, @JobTypeID = ?, @EmployeeID = ?, @Status = ?",array($PayrollTransactionID,$PayrollPeriodID, $PayrollType, $BranchID, $DivisionID, $DepartmentID, $SectionID, $JobTypeID, 0, $Status));

    return $PayrollTransactionID;

  }

  public function doRegenerate13thMonthTransaction($data){
    
    $Misc = new Misc();
    
    $TODAY = date("Y-m-d H:i:s");

    $PayrollTransactionID = $data['PayrollTransactionID'];
    $PayrollType = $data['PayrollType'];
    $PayrollPeriodID = $data['PayrollPeriodID'];
    $BranchID = $data['BranchID'];
    $EmployeeID = $data['EmployeeID'];
    $Status = $data['Status'];

    if($PayrollTransactionID > 0){

        DB::statement("SET NOCOUNT ON; exec spDoGenerate13thMonthPay @PayrollTransactionID =  ?, @PayrollPeriodID = ?, @PayrollType = ?,  @BranchID = ?, @DivisionID = ?, @DepartmentID = ?, @SectionID = ?, @JobTypeID = ?, @EmployeeID = ?, @Status = ?",array($PayrollTransactionID, $PayrollPeriodID, $PayrollType, 0, 0, 0, 0, 0, $EmployeeID, $Status));

    }

    return $PayrollTransactionID;

  }

  public function doApprove13thMonthTransaction($data){
    
    $Misc = new Misc();
    
    $TODAY = date("Y-m-d H:i:s");

    $PayrollTransactionID = $data['PayrollTransactionID'];

    if($PayrollTransactionID > 0){

        DB::table('payroll_trans_13thmonthpay')
          ->where('ID',$PayrollTransactionID)
          ->update([
            'Status' => config('app.STATUS_APPROVED'),
            'ApproveDateTime' => $TODAY          
          ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $PayrollTransactionID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "13th Month Transaction";
        $logData['TransType'] = "Approve 13th Month Transaction";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

        DB::statement("SET NOCOUNT ON; exec spDoApproveGenerated13thMonth @PayrollTransactionID =  ?",array($PayrollTransactionID));


    }

    return $PayrollTransactionID;

  }

  public function doCancel13thMonthTransaction($data){
    
    $Misc = new Misc();
    
    $TODAY = date("Y-m-d H:i:s");

    $PayrollTransactionID = $data['PayrollTransactionID'];

    if($PayrollTransactionID > 0){

        DB::table('payroll_trans_13thmonthpay')
          ->where('ID',$PayrollTransactionID)
          ->update([
            'Status' => config('app.STATUS_CANCELLED')
          ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $PayrollTransactionID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "13th Month Transaction";
        $logData['TransType'] = "Cancel 13th Month Transaction";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

    return $PayrollTransactionID;

  }








}

