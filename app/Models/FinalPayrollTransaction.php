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

class FinalPayrollTransaction extends Model
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
        ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrn.BranchID')
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->selectraw("

            COALESCE(paytrn.ID,0) as ID,

            COALESCE(paytrn.TransNo,'') as TransNo,
            FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,

            COALESCE(paytrn.BranchID,0) as BranchID,
            COALESCE(brn.BranchName,'') as BranchName,

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
        ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrn.BranchID')
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->selectraw("

            COALESCE(paytrn.ID,0) as ID,

            COALESCE(paytrn.TransNo,'') as TransNo,
            FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,

            COALESCE(paytrn.BranchID,0) as BranchID,
            COALESCE(brn.BranchName,'') as BranchName,

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

  public function getPayrollTransactionInfoTransNo($TransNo){

    $info = DB::table('payroll_transaction as paytrn')
        ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrn.BranchID')
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->selectraw("

            COALESCE(paytrn.ID,0) as ID,

            COALESCE(paytrn.TransNo,'') as TransNo,
            FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,

            COALESCE(paytrn.BranchID,0) as BranchID,
            COALESCE(brn.BranchName,'') as BranchName,

            COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
            COALESCE(period.Code,'') as PayrollPeriodCode,
            FORMAT(period.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
            FORMAT(period.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,
            COALESCE(period.CutOffID,'') as PayrollPeriodCutOffID,

            COALESCE(paytrn.Remarks,'') as Remarks,

            COALESCE(paytrn.Status,'') as Status

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
              ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
              ->join('payroll_division as div', 'div.ID', '=', 'usr.division_id')
              ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
              ->join('payroll_section as sec', 'sec.ID', '=', 'usr.section_id')
              ->join('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
              ->selectraw("
         
                  COALESCE(paytrnemp.ID,0) as ID,

                  COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
                  COALESCE(paytrn.TransNo,'') as TransNo,
                  FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
                  COALESCE(paytrn.Status,'') as Status,
         
                  COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
                  COALESCE(usr.shortid,'') as EmployeeNo,
                  COALESCE(usr.first_name,'') as FirstName,
                  COALESCE(usr.middle_name,'') as MiddleName,
                  COALESCE(usr.last_name,'') as LastName,
                  CONCAT(COALESCE(usr.first_name,''), ' ' ,SUBSTRING(usr.middle_name,1,1) ,'. ' ,COALESCE(usr.last_name,'')) as FullName,

                  COALESCE(paytrnemp.BranchID,0) as BranchID,
                  COALESCE(brn.BranchName,'') as BranchName,

                  COALESCE(usr.division_id,0) as DivisionID,
                  COALESCE(div.Division,'') as Division,

                  COALESCE(usr.department_id,0) as DepartmentID,
                  COALESCE(dept.Department,'') as Department,

                  COALESCE(usr.section_id,0) as SectionID,
                  COALESCE(sec.Section,'') as Section,

                  COALESCE(usr.job_title_id,0) as JobTitleID,
                  COALESCE(job.JobTitle,'') as JobTitle,

                  COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,
                  COALESCE(paytrnemp.BasicSalary,0) as BasicSalary,
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
              ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
              ->join('payroll_division as div', 'div.ID', '=', 'usr.division_id')
              ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
              ->join('payroll_section as sec', 'sec.ID', '=', 'usr.section_id')
              ->join('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
              ->selectraw("
         
                  COALESCE(paytrnemp.ID,0) as ID,

                  COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
                  COALESCE(paytrn.TransNo,'') as TransNo,
                  FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
                  COALESCE(paytrn.Status,'') as Status,
         
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

                  COALESCE(usr.division_id,0) as DivisionID,
                  COALESCE(div.Division,'') as Division,

                  COALESCE(usr.department_id,0) as DepartmentID,
                  COALESCE(dept.Department,'') as Department,

                  COALESCE(usr.section_id,0) as SectionID,
                  COALESCE(sec.Section,'') as Section,

                  COALESCE(usr.job_title_id,0) as JobTitleID,
                  COALESCE(job.JobTitle,'') as JobTitle,
         
                  COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,
                  COALESCE(paytrnemp.BasicSalary,0) as BasicSalary,
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
              ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
              ->join('payroll_division as div', 'div.ID', '=', 'usr.division_id')
              ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
              ->join('payroll_section as sec', 'sec.ID', '=', 'usr.section_id')
              ->join('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
              ->selectraw("
         
                  COALESCE(paytrnemp.ID,0) as ID,

                  COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
                  COALESCE(paytrn.TransNo,'') as TransNo,
                  FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
                  COALESCE(paytrn.Status,'') as Status,
         
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

                  COALESCE(usr.division_id,0) as DivisionID,
                  COALESCE(div.Division,'') as Division,

                  COALESCE(usr.department_id,0) as DepartmentID,
                  COALESCE(dept.Department,'') as Department,

                  COALESCE(usr.section_id,0) as SectionID,
                  COALESCE(sec.Section,'') as Section,

                  COALESCE(usr.job_title_id,0) as JobTitleID,
                  COALESCE(job.JobTitle,'') as JobTitle,

                  COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,
                  COALESCE(paytrnemp.BasicSalary,0) as BasicSalary,
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
              ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
              ->join('payroll_division as div', 'div.ID', '=', 'usr.division_id')
              ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
              ->join('payroll_section as sec', 'sec.ID', '=', 'usr.section_id')
              ->join('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
              ->selectraw("
         
                  COALESCE(paytrnemp.ID,0) as ID,

                  COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
                  COALESCE(paytrn.TransNo,'') as TransNo,
                  FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
                  COALESCE(paytrn.Status,'') as Status,
         
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

                  COALESCE(usr.division_id,0) as DivisionID,
                  COALESCE(div.Division,'') as Division,

                  COALESCE(usr.department_id,0) as DepartmentID,
                  COALESCE(dept.Department,'') as Department,

                  COALESCE(usr.section_id,0) as SectionID,
                  COALESCE(sec.Section,'') as Section,

                  COALESCE(usr.job_title_id,0) as JobTitleID,
                  COALESCE(job.JobTitle,'') as JobTitle,
         
                  COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,
                  COALESCE(paytrnemp.BasicSalary,0) as BasicSalary,
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

    $PayrollTransactionID = $data['PayrollTransactionID'];
    $PayrollPeriodID = $data['PayrollPeriodID'];
    $BranchID = $data['BranchID'];
    $SalaryType = $data['SalaryType'];
    $Status = $data['Status'];

    if($PayrollTransactionID > 0){

        DB::table('payroll_transaction')
          ->where('ID',$PayrollTransactionID)
          ->update([
            'PayrollPeriodID' => $PayrollPeriodID,
            'BranchID' => $BranchID,
            'SalaryType' => $SalaryType,
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
              'BranchID' => $BranchID,
              'SalaryType' => $SalaryType,
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

    DB::statement("SET NOCOUNT ON; exec spDoGeneratePayroll @PayrollTransactionID =  ?, @PayrollPeriodID = ?, @BranchID = ?, @SalaryType = ?, @Status = ?",array($PayrollTransactionID,$PayrollPeriodID,$BranchID,$SalaryType,$Status));

    return $PayrollTransactionID;

  }

  public function doApproveGeneratedPayroll($data){
    
    $Misc = new Misc();
    
    $TODAY = date("Y-m-d H:i:s");

    $PayrollTransactionID = $data['PayrollTransactionID'];

    if($PayrollTransactionID > 0){

        DB::table('payroll_transaction')
          ->where('ID',$PayrollTransactionID)
          ->update([
            'Status' => config('app.STATUS_APPROVED')
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










}

