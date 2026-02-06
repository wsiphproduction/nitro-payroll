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

use App\Models\IncomeDeductionType;

class GenerateExcel extends Model
{

 // ALL EMPLOYEE EXCEL
  public function generateEmployeeListExcel(){

   $query = DB::table('users as emp')
    ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
    ->join('payroll_branch as brnch', 'brnch.ID', '=', 'emp.company_branch_id')
    ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
    ->leftjoin('payroll_section as sec', 'sec.id', '=', 'emp.section_id')  
    ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')

    ->selectraw("        
        COALESCE(emp.employee_number,'') as employee_number,        

        CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as FullName,

        COALESCE(emp.contact_number,'') as MobileNo,
        COALESCE(emp.email,'') as EmailAddress,

        COALESCE(emp.tin_number,'') as tin_number,
        COALESCE(emp.sss_number,'') as sss_number,
        COALESCE(emp.pagibig_number,'') as pagibig_number,
        COALESCE(emp.philhealth_number,'') as philhealth_number,

        
        COALESCE(job.JobTitle,'') as Position,        
        COALESCE(div.Division,'') as Division,        
        COALESCE(dept.Department,'') as Department,
        COALESCE(sec.Section,'') as Section,      
        COALESCE(brnch.BranchName,'') as BranchName,


        ISNULL((SELECT TOP 1 MonthlyRate 
                        FROM payroll_employee_rates 
                        WHERE EmployeeID = emp.id
                        ORDER BY EffectivityDate DESC),0) as MonthlyRate,

                              
        ISNULL((SELECT TOP 1 DailyRate 
                        FROM payroll_employee_rates 
                        WHERE EmployeeID = emp.id
                        ORDER BY EffectivityDate DESC),0) as DailyRate, 

        ISNULL((SELECT TOP 1 HourlyRate 
                        FROM payroll_employee_rates 
                        WHERE EmployeeID = emp.id
                        ORDER BY EffectivityDate DESC),0) as HourlyRate, 
        
         CASE emp.status WHEN 1 THEN 'Active' ELSE 'Inactive' END as emp_status
         
        
    ");

    $query->orderBy("emp.last_name","ASC");
    $query->orderBy("emp.first_name","ASC");
    $list = $query->get();

    return $list;
      
  }  

 // ALL LOAN TYPE EXCEL
  public function generateLoanTypeListExcel(){
    
    $query = DB::table('payroll_loan_type as plt')
    ->selectraw("
        COALESCE(plt.Code,'') as Code,
        COALESCE(plt.Type,'') as Type,
        COALESCE(plt.Name,'') as Name,
        COALESCE(plt.Description,'') as Description,        
        COALESCE(plt.Status,'') as Status
    ");

    $query->orderBy("plt.Name", "ASC");
    $query->orderBy("plt.Status", "DESC");
    $list = $query->get();

    return $list;

}


 // ALL OT RATES EXCEL
  public function generateOTRatesListExcel(){
    
    $query = DB::table('payroll_ot_rates as ot_rate')
    ->selectraw("
        COALESCE(ot_rate.Code,'') as Code,        
        COALESCE(ot_rate.Name,'') as Name,
        COALESCE(ot_rate.Description,'') as Description,        
        COALESCE(ot_rate.Rate,'') as Rate, 
        COALESCE(ot_rate.DailyRate,'') as DailyRate, 

       CASE ot_rate.Status 
           WHEN 1 THEN 'Active'                
        ELSE 'Inactive' END as Status        
    ");

    $query->orderBy("ot_rate.Name", "ASC");
    $query->orderBy("ot_rate.Status", "DESC");
    $list = $query->get();

    return $list;

}


 // ALL INCOME & DEDUCTION TYPE EXCEL
  public function generateIncomeDeductionTypeListExcel(){

   $query = DB::table('payroll_income_deduction_type as pidt')
    ->selectraw("
        COALESCE(pidt.Code,'') as Code,      
        COALESCE(pidt.Type,'') as Type,
        COALESCE(pidt.Category,'') as Category,
        COALESCE(pidt.Name,'') as Name,
        COALESCE(pidt.Description,'') as Description,
        COALESCE(pidt.Status,'') as Status        
    ");
    
    $query->orderBy("pidt.Name", "ASC");
    $query->orderBy("pidt.Status", "DESC");
    $list = $query->get();

    return $list;

}

// PAYROLLPERIOD EXCEL
public function generatePayrollPeriodListExcel(){

    $query = DB::table('payroll_period_schedule as pps')
        ->selectraw("            
            COALESCE(pps.Code,'') as Code,

            FORMAT(pps.StartDate,'MM/dd/yyyy') as StartDateFormat,
            FORMAT(pps.EndDate,'MM/dd/yyyy') as EndDateFormat,

            CASE pps.CutOffID 
               WHEN 1 THEN '1ST HALF'                
            ELSE '2ND HALF' END as CutOff,

            COALESCE(pps.Year,'') as Year,
            COALESCE(pps.Remarks,'') as Remarks,                    
            COALESCE(pps.Status,'') as Status
        ");

    
    $query->orderBy("pps.Code", "ASC");
    $query->orderBy("pps.Status", "ASC");
    $list = $query->get();

    return $list;

}

// SSS BRACKET EXCEL
public function generateSSSTableBracketListExcel(){

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
 
    $query->orderBy("ssst.ID","ASC");
    $query->orderByraw("COALESCE(ssst.Year,'') ASC");
    $list = $query->get();

    return $list;

}

 // HDMF BRACKET EXCEL
  public function generateHDMFTableBracketListExcel(){

  $query = DB::table('payroll_hdmf_table as hdmft')
    ->selectraw("    
        COALESCE(hdmft.RangeFrom,0) as RangeFrom,
        COALESCE(hdmft.RangeTo,0) as RangeTo,
        COALESCE(hdmft.EmployeeSharePercent,0) as EmployeeSharePercent,
        COALESCE(hdmft.EmployerSharePercent,0) as EmployerSharePercent,       
        COALESCE(hdmft.Year,'') as Year,
        COALESCE(hdmft.Status,'') as Status
    ");

     
    $query->orderBy("hdmft.ID","ASC");
    $query->orderByraw("COALESCE(hdmft.Year,'') ASC");
    $list = $query->get();

    return $list;

 }

// PHIC BRACKET EXCEL
  public function generatePHICTableBracketListExcel(){

   $query = DB::table('payroll_phic_table as phict')
    ->selectraw("        
        COALESCE(phict.RangeFrom,0) as RangeFrom,
        COALESCE(phict.RangeTo,0) as RangeTo,
        COALESCE(phict.TotalSharePercent,0) as TotalSharePercent,
        COALESCE(phict.Year,'') as Year,
        COALESCE(phict.Status,'') as Status
    ");
     
     $query->orderBy("phict.ID","ASC");
     $query->orderByraw("COALESCE(phict.Year,'') ASC");
     $list = $query->get();

    return $list;

}

// ANNUAL INCOME TAX EXCEL
  public function generateAnnualIncomeTaxListExcel(){

 $query = DB::table('paryoll_annual_tax_table as patt')
    ->selectraw("        
        COALESCE(patt.RangeFrom,'') as RangeFrom,
        COALESCE(patt.RangeTo,'') as RangeTo,
        COALESCE(patt.FixTax,'') as FixTax,
         COALESCE(patt.RateOnExcessPercentage,'') as RateOnExcessPercentage,       
        COALESCE(patt.Year,'') as Year,
        COALESCE(patt.Status,'') as Status
    ");

     
     $query->orderBy("patt.ID","ASC");
     $query->orderByraw("COALESCE(patt.Year,'') ASC");
     $list = $query->get();

    return $list;

}

// WITHHOLDING TAX
  public function generateWithHoldingTaxListExcel(){

   $query = DB::table('payroll_withholding_tax_table as pwtt')
    ->selectraw("        
        
        COALESCE(pwtt.RangeFrom,0) as RangeFrom,
        COALESCE(pwtt.RangeTo,0) as RangeTo,
        COALESCE(pwtt.PayrollFrequency,'') as PayrollFrequency,
        COALESCE(pwtt.FixTax,0) as FixTax,    
        COALESCE(pwtt.RateOnExcessPercentage,'') as RateOnExcessPercentage,       
        COALESCE(pwtt.Year,'') as Year,
        COALESCE(pwtt.Status,'') as Status
    ");

     $query->orderBy("pwtt.PayrollFrequencyID","ASC");
     $query->orderByraw("COALESCE(pwtt.Year,'') ASC");
     $list = $query->get();

    return $list;

 }

// PAYROLL JOURNAL EXCEL WITH POSTED/APPROVED STATUS EXCEL REPORT
public function generatePayrollApprovedJournalListExcel($param){

    $Status = $param['Status'];
    
    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];
 
    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')          
          ->join('payroll_period_schedule as payperiod ', 'payperiod.ID', '=', 'paytrn.PayrollPeriodID') 
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')          
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  

            ->selectraw("
                          
              COALESCE(usr.shortid,'') as EmployeeNo,

                CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as EmployeeName,

              COALESCE(payperiod.Code,'') as PayrollPeriodCode,              
                          
              COALESCE(paytrnemp.BasicSalary,0) as BasicPay,

              COALESCE(paytrnincded.ECOLA,0) as ECOLA,
              COALESCE(paytrnemp.Late,0) as LateAmount,
              COALESCE(paytrnemp.Undertime,0) as UnderTimeAmount,
              COALESCE(paytrnemp.Absent,0) as AbsentAmount,

              COALESCE(paytrnemp.Leave2,0) as VL,
              COALESCE(paytrnemp.Leave1,0) as SL,
              COALESCE(paytrnemp.Leave,0) - COALESCE(paytrnemp.Leave1,0) - COALESCE(paytrnemp.Leave2,0) as OL,

              COALESCE(paytrnemp.NightDifferential,0) as NightDifferential,

              COALESCE(paytrnemp.OvertimeReg,0) as OTPay,
              COALESCE(paytrnemp.OvertimeND,0) as OTND,
              
              COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as OtherTaxableEarnings,
              COALESCE(paytrnemp.TotalNonTaxableIncome,0) as OtherNonTaxableEarnings,

              (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0)) as GrossPay,

              COALESCE(paytrnemp.SSSEEContribution,0) as SSS,
              COALESCE(paytrnemp.SSSWISPEE,0) as SSSWISP,
              COALESCE(paytrnemp.PHICEEContribution,0) as PHIC,
              COALESCE(paytrnemp.HDMFEEContribution,0) as HDMF,
              COALESCE(paytrnemp.HDMFMP2,0) as TotalHDMFMP2,
      
              COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) +  COALESCE(paytrnemp.Overtime,0) +  COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TaxableIncome,

                COALESCE(paytrnemp.WithholdingTax,0) as WTax,
                COALESCE(paytrnemp.TotalLoanDeductions,0) as LoanDeduction,
                COALESCE(paytrnemp.TotalDeductions,0) as OtherDeduction,
                (COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0)) as TotalDeduction,

                COALESCE(paytrnemp.NetPay,0) as NetPay,

                'Posted' as Status
         
        ");

     $query->where("paytrn.status",'Approved');              
     
       
    $query->orderBy("EmployeeName","ASC");
    

    $list = $query->get();

    return $list;

}

// PAYROLL JOURNAL EXCEL WITH UN-POSTED/PENDING STATUS EXCEL REPORT
public function generatePayrollPendingJournalListExcel($param){

    $Status = $param['Status'];
    
    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];
 
    $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
          ->join('payroll_transaction_income_deduction_temp as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')          
          ->join('payroll_period_schedule as payperiod ', 'payperiod.ID', '=', 'paytrn.PayrollPeriodID') 
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')          
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  

            ->selectraw("
                          
              COALESCE(usr.shortid,'') as EmployeeNo,

                CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' ,COALESCE(usr.middle_name,'')) as EmployeeName,

              COALESCE(payperiod.Code,'') as PayrollPeriodCode,              
                          
              COALESCE(paytrnemp.BasicSalary,0) as BasicPay,

              COALESCE(paytrnincded.ECOLA,0) as ECOLA,
              COALESCE(paytrnemp.Late,0) as LateAmount,
              COALESCE(paytrnemp.Undertime,0) as UnderTimeAmount,
              COALESCE(paytrnemp.Absent,0) as AbsentAmount,

              COALESCE(paytrnemp.Leave2,0) as VL,
              COALESCE(paytrnemp.Leave1,0) as SL,
              COALESCE(paytrnemp.Leave,0) - COALESCE(paytrnemp.Leave1,0) - COALESCE(paytrnemp.Leave2,0) as OL,

              COALESCE(paytrnemp.NightDifferential,0) as NightDifferential,

              COALESCE(paytrnemp.OvertimeReg,0) as OTPay,
              COALESCE(paytrnemp.OvertimeND,0) as OTND,
              
              COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as OtherTaxableEarnings,
              COALESCE(paytrnemp.TotalNonTaxableIncome,0) as OtherNonTaxableEarnings,

              (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0)) as GrossPay,

              COALESCE(paytrnemp.SSSEEContribution,0) as SSS,
              COALESCE(paytrnemp.SSSWISPEE,0) as SSSWISP,
              COALESCE(paytrnemp.PHICEEContribution,0) as PHIC,
              COALESCE(paytrnemp.HDMFEEContribution,0) as HDMF,
              COALESCE(paytrnemp.HDMFMP2,0) as TotalHDMFMP2,
      
              COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) +  COALESCE(paytrnemp.Overtime,0) +  COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TaxableIncome,

                COALESCE(paytrnemp.WithholdingTax,0) as WTax,
                COALESCE(paytrnemp.TotalLoanDeductions,0) as LoanDeduction,
                COALESCE(paytrnemp.TotalDeductions,0) as OtherDeduction,
                (COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0)) as TotalDeduction,

         COALESCE(paytrnemp.NetPay,0) as NetPay,

         'Un-Posted' as Status
         

        ");

     $query->where("paytrn.status",'Pending');              
     
       
    $query->orderBy("EmployeeName","ASC");
    

    $list = $query->get();

    return $list;

}


// PAYROLL REGISTER RAW DATA WITH POSTED/APPROVED STATUS GET DATA REPORTS
public function getPayrollApprovedRawDataListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $SiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $query = DB::table('payroll_transaction_employee as paytrnemp')
      ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')   
      ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')   
      ->join('users as emp', 'emp.ID', '=', 'paytrnemp.EmployeeID')   
      ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')   
      ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')   
      ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrnemp.DivisionID')   
      ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrnemp.SectionID')   
      ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')   
       
      ->selectraw("
               
        emp.shortid as EmpNo,

              COALESCE(dept.Department,'') as Department,
              COALESCE(div.Division,'') as Division,
              COALESCE(brn.BranchName,'') as Location,

                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' ,COALESCE(emp.middle_name,'')) as EmployeeName,
              COALESCE(sec.Section,'') as Section,
              COALESCE(job.JobTitle,'') as JobPosition,

        IIF(emp.salary_type = 1, 'Daily','Monthly') as PRSttructure,
        'SEMI-MONTHLY' as PaymentType,

          COALESCE(prd.Code,'') as PeriodCode,
          FORMAT(prd.EndDate, 'yyyy') as PeriodYear,

        COALESCE(paytrnemp.MonthlyRate,0) as MonthlyRate,

        ISNULL((SELECT TOP 1 DailyRate
            FROM payroll_employee_rates 
        WHERE EmployeeID = paytrnemp.EmployeeID
            ORDER BY [EffectivityDate] DESC),0) as DailyRate,

        COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,

        IIF(emp.[status] = 1, 'Active','Inactive') as EmpStatus,
        
        COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Basic Salary'),0
        ) as RegHrs,

        COALESCE(paytrnemp.BasicSalary,0) as RegPay,
        COALESCE((SELECT SUM( payroll_transaction_details.Total) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Basic Salary'),0
        ) as RegPay,

        COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Late Hours'),0
        ) as LateHours,
        COALESCE(paytrnemp.Late,0) as LatePay,

       COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Undertime Hours'),0
       ) as UndertimeHours,
        COALESCE(paytrnemp.Undertime,0) as UndertimePay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Absent'),0
      ) as AbsentHrs,
      COALESCE(paytrnemp.Absent,0) as AbsentPay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave'),0
      ) as TotalLeaveHrs,

      COALESCE((SELECT SUM( payroll_transaction_details.Total) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave'),0
      ) as TotalLeavePay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime'),0
      ) as TotalOvertimeHrs,

      COALESCE((SELECT SUM( payroll_transaction_details.Total) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime'),0
      ) as TotalOvertimePay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 1),0
      ) as Leave1Hrs,
      COALESCE(paytrnemp.Leave1,0) as Leave1Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 2),0
      ) as Leave2Hrs,
      COALESCE(paytrnemp.Leave2,0) as Leave2Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 3),0
      ) as Leave3Hrs,
      COALESCE(paytrnemp.Leave3,0) as Leave3Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 4),0
      ) as Leave4Hrs,
      COALESCE(paytrnemp.Leave4,0) as Leave4Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 5),0
      ) as Leave5Hrs,
      COALESCE(paytrnemp.Leave5,0) as Leave5Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 6),0
      ) as Leave6Hrs,
      COALESCE(paytrnemp.Leave6,0) as Leave6Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 7),0
      ) as Leave7Hrs,
      COALESCE(paytrnemp.Leave7,0) as Leave7Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 8),0
      ) as Leave8Hrs,
      COALESCE(paytrnemp.Leave8,0) as Leave8Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 9),0
      ) as Leave9Hrs,
      COALESCE(paytrnemp.Leave9,0) as Leave9Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 10),0
      ) as Leave10Hrs,
      COALESCE(paytrnemp.Leave10,0) as Leave10Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 11),0
      ) as Leave11Hrs,
      COALESCE(paytrnemp.Leave11,0) as Leave11Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 12),0
      ) as Leave12Hrs,
      COALESCE(paytrnemp.Leave12,0) as Leave12Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 13),0
      ) as Leave13Hrs,
      COALESCE(paytrnemp.Leave13,0) as Leave13Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 14),0
      ) as Leave14Hrs,
      COALESCE(paytrnemp.Leave14,0) as Leave14Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 15),0
      ) as Leave15Hrs,
      COALESCE(paytrnemp.Leave15,0) as Leave15Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 16),0
      ) as Leave16Hrs,
      COALESCE(paytrnemp.Leave16,0) as Leave16Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 17),0
      ) as Leave17Hrs,
      COALESCE(paytrnemp.Leave17,0) as Leave17Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 18),0
      ) as Leave18Hrs,
      COALESCE(paytrnemp.Leave18,0) as Leave18Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 19),0
      ) as Leave19Hrs,
      COALESCE(paytrnemp.Leave19,0) as Leave19Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Leave' AND  payroll_transaction_details.ReferenceID = 20),0
      ) as Leave20Hrs,
      COALESCE(paytrnemp.Leave20,0) as Leave20Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Night Differential'),0
      ) as NightDifferentialHrs,
      COALESCE(paytrnemp.NightDifferential,0) as NightDifferentialPay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 1),0
      ) as Overtime1Hrs,
      COALESCE(paytrnemp.Overtime1,0) as Overtime1Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 2),0
      ) as Overtime2Hrs,
      COALESCE(paytrnemp.Overtime2,0) as Overtime2Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 3),0
      ) as Overtime3Hrs,
      COALESCE(paytrnemp.Overtime3,0) as Overtime3Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 4),0
      ) as Overtime4Hrs,
      COALESCE(paytrnemp.Overtime4,0) as Overtime4Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 5),0
      ) as Overtime5Hrs,
      COALESCE(paytrnemp.Overtime5,0) as Overtime5Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 6),0
      ) as Overtime6Hrs,
      COALESCE(paytrnemp.Overtime6,0) as Overtime6Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 7),0
      ) as Overtime7Hrs,
      COALESCE(paytrnemp.Overtime7,0) as Overtime7Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 8),0
      ) as Overtime8Hrs,
      COALESCE(paytrnemp.Overtime8,0) as Overtime8Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 9),0
      ) as Overtime9Hrs,
      COALESCE(paytrnemp.Overtime9,0) as Overtime9Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 10),0
      ) as Overtime10Hrs,
      COALESCE(paytrnemp.Overtime10,0) as Overtime10Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 11),0
      ) as Overtime11Hrs,
      COALESCE(paytrnemp.Overtime11,0) as Overtime11Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 12),0
      ) as Overtime12Hrs,
      COALESCE(paytrnemp.Overtime12,0) as Overtime12Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 13),0
      ) as Overtime13Hrs,
      COALESCE(paytrnemp.Overtime13,0) as Overtime13Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 14),0
      ) as Overtime14Hrs,
      COALESCE(paytrnemp.Overtime14,0) as Overtime14Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 15),0
      ) as Overtime15Hrs,
      COALESCE(paytrnemp.Overtime15,0) as Overtime15Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 16),0
      ) as Overtime16Hrs,
      COALESCE(paytrnemp.Overtime16,0) as Overtime16Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 17),0
      ) as Overtime17Hrs,
      COALESCE(paytrnemp.Overtime17,0) as Overtime17Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 18),0
      ) as Overtime18Hrs,
      COALESCE(paytrnemp.Overtime18,0) as Overtime18Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 19),0
      ) as Overtime19Hrs,
      COALESCE(paytrnemp.Overtime19,0) as Overtime19Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 20),0
      ) as Overtime20Hrs,
      COALESCE(paytrnemp.Overtime20,0) as Overtime20Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 21),0
      ) as Overtime21Hrs,
      COALESCE(paytrnemp.Overtime21,0) as Overtime21Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 22),0
      ) as Overtime22Hrs,
      COALESCE(paytrnemp.Overtime22,0) as Overtime22Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 23),0
      ) as Overtime23Hrs,
      COALESCE(paytrnemp.Overtime23,0) as Overtime23Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 24),0
      ) as Overtime24Hrs,
      COALESCE(paytrnemp.Overtime24,0) as Overtime24Pay,

      COALESCE((SELECT SUM( payroll_transaction_details.Qty) FROM  payroll_transaction_details INNER JOIN payroll_transaction ON ( payroll_transaction_details.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details.ReferenceType = 'Overtime' AND  payroll_transaction_details.ReferenceID = 25),0
      ) as Overtime25Hrs,
      COALESCE(paytrnemp.Overtime25,0) as Overtime25Pay,

      COALESCE(paytrnemp.TotalNonTaxableIncome,0) as OtherNonTaxableEarnings,

      COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as OtherTaxableEarnings,

      COALESCE(paytrnemp.TotalDeductions,0) as OtherDeduction,

      0 as TaxableOtherDeduction,

      COALESCE(paytrnemp.TotalLoanDeductions,0) as LoanDeduction,

      COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) +  COALESCE(paytrnemp.Overtime,0) +  COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TaxableIncome,

      COALESCE(paytrnemp.WithholdingTax,0) as WTax,

      COALESCE(paytrnemp.SSSEEContribution,0) as SSSEE,
      COALESCE(paytrnemp.SSSWISPEE,0) as SSSWISPEE,
      COALESCE(paytrnemp.SSSERContribution,0) as SSSER,
      COALESCE(paytrnemp.SSSECER,0) as SSSECER,
          
      COALESCE(paytrnemp.PHICEEContribution,0) as PHICEE,
      COALESCE(paytrnemp.PHICERContribution,0) as PHICER,

      COALESCE(paytrnemp.HDMFEEContribution,0) as HDMFEE,
      COALESCE(paytrnemp.HDMFMP2,0) as HDMFMP2,
      COALESCE(paytrnemp.HDMFERContribution,0) as HDMFER,

      (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0)) as GrossPay,

      COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalDeduction,

        paytrnemp.NetPay as TotalPay,

        COALESCE(paytrnemp.NetPay,0) as NetPay,

        'Posted' as Status

            ");


          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

      if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
         $query->whereIn('paytrnemp.BranchID',$BranchID);
       }else if($FilterType!='' && $FilterType=='Site' && !empty($SiteID)){
          $query->whereIn('emp.company_branch_site_id',$SiteID);
       }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
         $query->whereIn('dept.DivisionID',$DivisionID);
       }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
         $query->whereIn('emp.department_id',$DepartmentID);
       }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
          $query->whereIn('sec.ID',$SectionID);
       }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
         $query->whereIn('emp.job_title_id',$JobTypeID);
       }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
         $query->where('paytrnemp.EmployeeID',$EmployeeID);
       }


    $query->where("paytrn.status",'Approved');  

    $query->orderBy("EmployeeName","ASC");
    

    $list = $query->get();

    return $list;

}

// PAYROLL REGISTER RAW DATA REPORT WITH UN-POST/PENDING STATUS GET DATA REPORTS
public function getPayrollPendingRawDataListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $SiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
      ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')   
      ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')   
      ->join('users as emp', 'emp.ID', '=', 'paytrnemp.EmployeeID')   
      ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')   
      ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')   
      ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrnemp.DivisionID')   
      ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrnemp.SectionID')   
      ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')   
       
      ->selectraw("

        emp.shortid as EmpNo,

              COALESCE(dept.Department,'') as Department,
              COALESCE(div.Division,'') as Division,
              COALESCE(brn.BranchName,'') as Location,

                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' ,COALESCE(emp.middle_name,'')) as EmployeeName,
              COALESCE(sec.Section,'') as Section,
              COALESCE(job.JobTitle,'') as JobPosition,

        IIF(emp.salary_type = 1, 'Daily','Monthly') as PRSttructure,
        'SEMI-MONTHLY' as PaymentType,

          COALESCE(prd.Code,'') as PeriodCode,
          FORMAT(prd.EndDate, 'yyyy') as PeriodYear,

        COALESCE(paytrnemp.MonthlyRate,0) as MonthlyRate,

        ISNULL((SELECT TOP 1 DailyRate
            FROM payroll_employee_rates 
        WHERE EmployeeID = paytrnemp.EmployeeID
            ORDER BY [EffectivityDate] DESC),0) as DailyRate,

        COALESCE(paytrnemp.HourlyRate,0) as HourlyRate,

        IIF(emp.[status] = 1, 'Active','Inactive') as EmpStatus,

        COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Basic Salary'),0
        ) as RegHrs,
        COALESCE(paytrnemp.BasicSalary,0) as RegPay,

        COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Late Hours'),0
        ) as LateHours,
        COALESCE(paytrnemp.Late,0) as LatePay,

       COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Undertime Hours'),0
       ) as UndertimeHours,
       COALESCE(paytrnemp.Undertime,0) as UndertimePay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Absent'),0
      ) as AbsentHrs,
      COALESCE(paytrnemp.Absent,0) as AbsentPay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave'),0
      ) as TotalLeaveHrs,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Total) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave'),0
      ) as TotalLeavePay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime'),0
      ) as TotalOvertimeHrs,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Total) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime'),0
      ) as TotalOvertimePay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 1),0
      ) as Leave1Hrs,
      COALESCE(paytrnemp.Leave1,0) as Leave1Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 2),0
      ) as Leave2Hrs,
      COALESCE(paytrnemp.Leave2,0) as Leave2Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 3),0
      ) as Leave3Hrs,
      COALESCE(paytrnemp.Leave3,0) as Leave3Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 4),0
      ) as Leave4Hrs,
      COALESCE(paytrnemp.Leave4,0) as Leave4Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 5),0
      ) as Leave5Hrs,
      COALESCE(paytrnemp.Leave5,0) as Leave5Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 6),0
      ) as Leave6Hrs,
      COALESCE(paytrnemp.Leave6,0) as Leave6Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 7),0
      ) as Leave7Hrs,
      COALESCE(paytrnemp.Leave7,0) as Leave7Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 8),0
      ) as Leave8Hrs,
      COALESCE(paytrnemp.Leave8,0) as Leave8Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 9),0
      ) as Leave9Hrs,
      COALESCE(paytrnemp.Leave9,0) as Leave9Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 10),0
      ) as Leave10Hrs,
      COALESCE(paytrnemp.Leave10,0) as Leave10Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 11),0
      ) as Leave11Hrs,
      COALESCE(paytrnemp.Leave11,0) as Leave11Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 12),0
      ) as Leave12Hrs,
      COALESCE(paytrnemp.Leave12,0) as Leave12Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 13),0
      ) as Leave13Hrs,
      COALESCE(paytrnemp.Leave13,0) as Leave13Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 14),0
      ) as Leave14Hrs,
      COALESCE(paytrnemp.Leave14,0) as Leave14Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 15),0
      ) as Leave15Hrs,
      COALESCE(paytrnemp.Leave15,0) as Leave15Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 16),0
      ) as Leave16Hrs,
      COALESCE(paytrnemp.Leave16,0) as Leave16Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 17),0
      ) as Leave17Hrs,
      COALESCE(paytrnemp.Leave17,0) as Leave17Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 18),0
      ) as Leave18Hrs,
      COALESCE(paytrnemp.Leave18,0) as Leave18Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 19),0
      ) as Leave19Hrs,
      COALESCE(paytrnemp.Leave19,0) as Leave19Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Leave' AND  payroll_transaction_details_temp.ReferenceID = 20),0
      ) as Leave20Hrs,
      COALESCE(paytrnemp.Leave20,0) as Leave20Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Night Differential'),0
      ) as NightDifferentialHrs,
      COALESCE(paytrnemp.WithholdingTax,0) as NightDifferentialPay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 1),0
      ) as Overtime1Hrs,
      COALESCE(paytrnemp.Overtime1,0) as Overtime1Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 2),0
      ) as Overtime2Hrs,
      COALESCE(paytrnemp.Overtime2,0) as Overtime2Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 3),0
      ) as Overtime3Hrs,
      COALESCE(paytrnemp.Overtime3,0) as Overtime3Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 4),0
      ) as Overtime4Hrs,
      COALESCE(paytrnemp.Overtime4,0) as Overtime4Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 5),0
      ) as Overtime5Hrs,
      COALESCE(paytrnemp.Overtime5,0) as Overtime5Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 6),0
      ) as Overtime6Hrs,
      COALESCE(paytrnemp.Overtime6,0) as Overtime6Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 7),0
      ) as Overtime7Hrs,
      COALESCE(paytrnemp.Overtime7,0) as Overtime7Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 8),0
      ) as Overtime8Hrs,
      COALESCE(paytrnemp.Overtime8,0) as Overtime8Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 9),0
      ) as Overtime9Hrs,
      COALESCE(paytrnemp.Overtime9,0) as Overtime9Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 10),0
      ) as Overtime10Hrs,
      COALESCE(paytrnemp.Overtime10,0) as Overtime10Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 11),0
      ) as Overtime11Hrs,
      COALESCE(paytrnemp.Overtime11,0) as Overtime11Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 12),0
      ) as Overtime12Hrs,
      COALESCE(paytrnemp.Overtime12,0) as Overtime12Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 13),0
      ) as Overtime13Hrs,
      COALESCE(paytrnemp.Overtime13,0) as Overtime13Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 14),0
      ) as Overtime14Hrs,
      COALESCE(paytrnemp.Overtime14,0) as Overtime14Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 15),0
      ) as Overtime15Hrs,
      COALESCE(paytrnemp.Overtime15,0) as Overtime15Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 16),0
      ) as Overtime16Hrs,
      COALESCE(paytrnemp.Overtime16,0) as Overtime16Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 17),0
      ) as Overtime17Hrs,
      COALESCE(paytrnemp.Overtime17,0) as Overtime17Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 18),0
      ) as Overtime18Hrs,
      COALESCE(paytrnemp.Overtime18,0) as Overtime18Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 19),0
      ) as Overtime19Hrs,
      COALESCE(paytrnemp.Overtime19,0) as Overtime19Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 20),0
      ) as Overtime20Hrs,
      COALESCE(paytrnemp.Overtime20,0) as Overtime20Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 21),0
      ) as Overtime21Hrs,
      COALESCE(paytrnemp.Overtime21,0) as Overtime21Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 22),0
      ) as Overtime22Hrs,
      COALESCE(paytrnemp.Overtime22,0) as Overtime22Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 23),0
      ) as Overtime23Hrs,
      COALESCE(paytrnemp.Overtime23,0) as Overtime23Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 24),0
      ) as Overtime24Hrs,
      COALESCE(paytrnemp.Overtime24,0) as Overtime24Pay,

      COALESCE((SELECT SUM( payroll_transaction_details_temp.Qty) FROM  payroll_transaction_details_temp INNER JOIN payroll_transaction ON ( payroll_transaction_details_temp.PayrollTransactionID = payroll_transaction.ID) WHERE  payroll_transaction_details_temp.EmployeeID = paytrnemp.EmployeeID AND payroll_transaction.PayrollPeriodID = paytrn.PayrollPeriodID AND  payroll_transaction_details_temp.ReferenceType = 'Overtime' AND  payroll_transaction_details_temp.ReferenceID = 25),0
      ) as Overtime25Hrs,
      COALESCE(paytrnemp.Overtime25,0) as Overtime25Pay,

      COALESCE(paytrnemp.TotalNonTaxableIncome,0) as OtherNonTaxableEarnings,

      COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as OtherTaxableEarnings,

      COALESCE(paytrnemp.TotalDeductions,0) as OtherDeduction,

      0 as TaxableOtherDeduction,

      COALESCE(paytrnemp.TotalLoanDeductions,0) as LoanDeduction,

      COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) +  COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TaxableIncome,

      COALESCE(paytrnemp.WithholdingTax,0) as WTax,

      COALESCE(paytrnemp.SSSEEContribution,0) as SSSEE,
      COALESCE(paytrnemp.SSSWISPEE,0) as SSSWISPEE,
      COALESCE(paytrnemp.SSSERContribution,0) as SSSER,
      COALESCE(paytrnemp.SSSECER,0) as SSSECER,
          
      COALESCE(paytrnemp.PHICEEContribution,0) as PHICEE,
      COALESCE(paytrnemp.PHICERContribution,0) as PHICER,

      COALESCE(paytrnemp.HDMFEEContribution,0) as HDMFEE,
      COALESCE(paytrnemp.HDMFMP2,0) as HDMFMP2,
      COALESCE(paytrnemp.HDMFERContribution,0) as HDMFER,

      (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0)) as GrossPay,

      COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalDeduction,

      paytrnemp.NetPay as TotalPay,

      COALESCE(paytrnemp.NetPay,0) as NetPay,

      'Un-Posted' as Status

      ");


        $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

      if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
         $query->whereIn('paytrnemp.BranchID',$BranchID);
       }else if($FilterType!='' && $FilterType=='Site' && !empty($SiteID)){
          $query->whereIn('emp.company_branch_site_id',$SiteID);
       }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
         $query->whereIn('dept.DivisionID',$DivisionID);
       }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
         $query->whereIn('emp.department_id',$DepartmentID);
       }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
          $query->whereIn('sec.ID',$SectionID);
       }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
         $query->whereIn('emp.job_title_id',$JobTypeID);
       }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
         $query->where('paytrnemp.EmployeeID',$EmployeeID);
       }

       $query->where("paytrn.status",'Pending');  
           
    $query->orderBy("EmployeeName","ASC");
    

    $list = $query->get();

    return $list;

}

// PAYROLL REGISTER WITH POSTED/APPROVED EXCEL REPORT
public function generatePayrollRegisterApprovedListExcel($param){
    
    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];
    
    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $SiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $query = DB::table('payroll_transaction_employee as paytrnemp')
      ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
          $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
          $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
      })
      ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
      ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
      ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
      ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'emp.company_branch_site_id')
      ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
      ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
      ->leftjoin('payroll_section as sec', 'sec.id', '=', 'emp.section_id')  
      ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')

            ->selectraw("              
              emp.shortid as EmployeeNo,

                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' ,COALESCE(emp.middle_name,'')) as EmployeeName,

             paytrnemp.BasicSalary as BasicPay,

              COALESCE(paytrnincded.ECOLA,0) as ECOLA,

              COALESCE(paytrnemp.Late,0) as LateAmount,
              COALESCE(paytrnemp.Undertime,0) as UndertimeAmount,
              COALESCE(paytrnemp.Absent,0) as AbsentAmount,

              COALESCE(sec.Section,'NO TEAM LEADER') as TeamLeader,

              COALESCE(paytrnemp.Leave1,0) as SL,
              COALESCE(paytrnemp.Leave2,0) as VL,
              COALESCE(paytrnemp.Leave,0) - COALESCE(paytrnemp.Leave1,0) - COALESCE(paytrnemp.Leave2,0) as OL,

              COALESCE(paytrnemp.NightDifferential,0) as NightDiff,

              COALESCE(paytrnemp.OvertimeReg,0) - COALESCE(paytrnemp.Overtime3,0) as OTPay,
              COALESCE(paytrnemp.Overtime3,0) as RDDPay,
              COALESCE(paytrnemp.OvertimeND,0) as OTND,

            COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as OtherTaxableEarnings,
            COALESCE(paytrnemp.TotalNonTaxableIncome,0) as OtherNonTaxableEarnings,

            (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0)) as GrossPay,

            COALESCE(paytrnemp.SSSEEContribution,0) + COALESCE(paytrnemp.SSSWISPEE,0) as SSS,
            COALESCE(paytrnemp.PHICEEContribution,0) as PHIC,
            COALESCE(paytrnemp.HDMFEEContribution,0) as HDMF,
            COALESCE(paytrnemp.HDMFMP2,0) as HDMFMP2,

            COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) +  COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TaxableIncome,

            COALESCE(paytrnemp.WithholdingTax,0) as WTax,

            COALESCE(paytrnemp.TotalLoanDeductions,0) as LoanDeduction,

            COALESCE(paytrnemp.TotalDeductions,0) as OtherDeduction,

            COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0) + COALESCE(paytrnemp.TotalLoanDeductions,0) + COALESCE(paytrnemp.TotalDeductions,0) as TotalDeduction,

            COALESCE(paytrnemp.NetPay,0) as NetPay,

            'Posted' as Status
            ");


       $query->where("paytrn.status",'Approved'); 
       $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID); 

      if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
         $query->whereIn('paytrnemp.BranchID',$BranchID);
       }else if($FilterType!='' && $FilterType=='Site' && !empty($SiteID)){
         $query->whereIn('emp.company_branch_site_id',$SiteID);
       }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
         $query->whereIn('dept.DivisionID',$DivisionID);
       }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
         $query->whereIn('emp.department_id',$DepartmentID);
       }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
          $query->whereIn('sec.ID',$SectionID);
       }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
         $query->whereIn('emp.job_title_id',$JobTypeID);
       }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
         $query->where('paytrnemp.EmployeeID',$EmployeeID);
       }
        
    $query->orderBy("TeamLeader", "ASC")
          ->orderBy("EmployeeName", "ASC");
    

    $list = $query->get();

    return $list;

}

// PAYROLL REGISTER WITH UN-POSTED/PENDING EXCEL REPORT
public function generatePayrollRegisterPendingListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];


    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $SiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

     $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
      ->join('payroll_transaction_income_deduction_temp as paytrnincded', function($join){
          $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
          $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
      })
      ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
      ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
      ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
      ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'emp.company_branch_site_id')
      ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
      ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
      ->leftjoin('payroll_section as sec', 'sec.id', '=', 'emp.section_id')  
      ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')           

            ->selectraw("              
              emp.shortid as EmployeeNo,

                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as EmployeeName,

             paytrnemp.BasicSalary as BasicPay,

             COALESCE(paytrnincded.ECOLA,0) as ECOLA,

              COALESCE(paytrnemp.Late,0) as LateAmount,
              COALESCE(paytrnemp.Undertime,0) as UndertimeAmount,
              COALESCE(paytrnemp.Absent,0) as AbsentAmount,

              COALESCE(sec.Section,'NO TEAM LEADER') as TeamLeader,

              COALESCE(paytrnemp.Leave1,0) as SL,
              COALESCE(paytrnemp.Leave2,0) as VL,
              COALESCE(paytrnemp.Leave,0) - COALESCE(paytrnemp.Leave1,0) - COALESCE(paytrnemp.Leave2,0) as OL,

              COALESCE(paytrnemp.NightDifferential,0) as NightDiff,
              COALESCE(paytrnemp.OvertimeReg,0) - COALESCE(paytrnemp.Overtime3,0) as OTPay,
              COALESCE(paytrnemp.Overtime3,0) as RDDPay,
              COALESCE(paytrnemp.OvertimeND,0) as OTND,

            COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as OtherTaxableEarnings,
            COALESCE(paytrnemp.TotalNonTaxableIncome,0) as OtherNonTaxableEarnings,

            (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0)) as GrossPay,


            COALESCE(paytrnemp.SSSEEContribution,0) + COALESCE(paytrnemp.SSSWISPEE,0) as SSS,
            COALESCE(paytrnemp.PHICEEContribution,0) as PHIC,
            COALESCE(paytrnemp.HDMFEEContribution,0) as HDMF,
            COALESCE(paytrnemp.HDMFMP2,0) as HDMFMP2,

            COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) +  COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TaxableIncome,
            
            COALESCE(paytrnemp.WithholdingTax,0) as WTax,

            COALESCE(paytrnemp.TotalLoanDeductions,0) as LoanDeduction,

            COALESCE(paytrnemp.TotalDeductions,0) as OtherDeduction,

            COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0) + COALESCE(paytrnemp.TotalLoanDeductions,0) + COALESCE(paytrnemp.TotalDeductions,0) as TotalDeduction,

            COALESCE(paytrnemp.NetPay,0) as NetPay,

            'Un-Posted' as Status
            ");

       $query->where("paytrn.status",'Pending'); 
       $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);  

      if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
         $query->whereIn('paytrnemp.BranchID',$BranchID);
       }else if($FilterType!='' && $FilterType=='Site' && !empty($SiteID)){
         $query->whereIn('emp.company_branch_site_id',$SiteID);
       }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
         $query->whereIn('dept.DivisionID',$DivisionID);
       }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
         $query->whereIn('emp.department_id',$DepartmentID);
       }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
          $query->whereIn('sec.ID',$SectionID);
       }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
         $query->whereIn('emp.job_title_id',$JobTypeID);
       }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
         $query->where('paytrnemp.EmployeeID',$EmployeeID);
       }
      
    $query->orderBy("TeamLeader", "ASC")
          ->orderBy("EmployeeName", "ASC");
    

    $list = $query->get();

    return $list;

}

//PAYROLL REGISTERED DETAILED VERSION WITH UN-POSTED/PENDING EXCEL REPORT
public function generatePayrollRegisterPendingDetailedListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];


      $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
      ->join('payroll_transaction_income_deduction_temp as paytrnincded', function($join){
          $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
          $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
      })
      ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
      ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  

          ->selectraw("

            paytrn.ID as PayrollTransactionID,

            paytrnemp.EmployeeID,

            emp.shortid as EmployeeNo,

                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as EmployeeName,

            COALESCE(paytrnemp.BasicSalary,0) as BasicPay,

            COALESCE(paytrnincded.ECOLA,0) as ECOLA,

            COALESCE(paytrnemp.Absent,0) as Absences,
            COALESCE(paytrnemp.Late,0) as LateHours,
            COALESCE(paytrnemp.Undertime,0) as UndertimeHours,

            COALESCE(paytrnemp.NightDifferential,0) as NightDifferential,

            COALESCE(paytrnemp.Overtime1,0) as OvertimeROT,
            COALESCE(paytrnemp.Overtime2,0) as OvertimeNPROT,
            COALESCE(paytrnemp.Overtime3,0) as OvertimeDO,
            COALESCE(paytrnemp.Overtime4,0) as OvertimeSH,
            COALESCE(paytrnemp.Overtime5,0) as OvertimeLH,
            COALESCE(paytrnemp.Overtime6,0) as OvertimeSHDO,
            COALESCE(paytrnemp.Overtime7,0) as OvertimeLHDO,
            COALESCE(paytrnemp.Overtime8,0) as OvertimeOTDO,
            COALESCE(paytrnemp.Overtime9,0) as OvertimeOTSH,
            COALESCE(paytrnemp.Overtime10,0) as OvertimeOTLH,
            COALESCE(paytrnemp.Overtime11,0) as OvertimeOTSHDO,
            COALESCE(paytrnemp.Overtime12,0) as OvertimeOTLHDO,
            COALESCE(paytrnemp.Overtime13,0) as OvertimeNDDO,
            COALESCE(paytrnemp.Overtime14,0) as OvertimeNDSH,
            COALESCE(paytrnemp.Overtime15,0) as OvertimeNDLH,
            COALESCE(paytrnemp.Overtime16,0) as OvertimeNDSHDO,
            COALESCE(paytrnemp.Overtime17,0) as OvertimeNDLHDO,
            COALESCE(paytrnemp.Overtime18,0) as OvertimeNPDO,
            COALESCE(paytrnemp.Overtime19,0) as OvertimeNPSH,
            COALESCE(paytrnemp.Overtime20,0) as OvertimeNPLH,
            COALESCE(paytrnemp.Overtime21,0) as OvertimeNPSHDO,
            COALESCE(paytrnemp.Overtime22,0) as OvertimeNPLHDO,
            COALESCE(paytrnemp.Overtime23,0) as Overtime23,
            COALESCE(paytrnemp.Overtime24,0) as Overtime24,
            COALESCE(paytrnemp.Overtime25,0) as Overtime25,

            COALESCE(paytrnemp.Leave1,0) as LeaveSL,
            COALESCE(paytrnemp.Leave2,0) as LeaveVL,
            COALESCE(paytrnemp.Leave3,0) as LeaveEL,
            COALESCE(paytrnemp.Leave4,0) as LeaveML,
            COALESCE(paytrnemp.Leave5,0) as LeavePL,
            COALESCE(paytrnemp.Leave6,0) as LeaveSIL,
            COALESCE(paytrnemp.Leave7,0) as LeaveADO,
            COALESCE(paytrnemp.Leave8,0) as LeaveSPL,
            COALESCE(paytrnemp.Leave9,0) as LeaveSLW,
            COALESCE(paytrnemp.Leave10,0) as Leave10,
            COALESCE(paytrnemp.Leave11,0) as Leave11,
            COALESCE(paytrnemp.Leave12,0) as Leave12,
            COALESCE(paytrnemp.Leave13,0) as Leave13,
            COALESCE(paytrnemp.Leave14,0) as Leave14,
            COALESCE(paytrnemp.Leave15,0) as Leave15,
            COALESCE(paytrnemp.Leave16,0) as Leave16,
            COALESCE(paytrnemp.Leave17,0) as Leave17,
            COALESCE(paytrnemp.Leave18,0) as Leave18,
            COALESCE(paytrnemp.Leave19,0) as Leave19,
            COALESCE(paytrnemp.Leave20,0) as Leave20,

            COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as OtherTaxableEarnings,
            COALESCE(paytrnemp.TotalNonTaxableIncome,0) as OtherNonTaxableEarnings,

            (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0)) as GrossPay,

            COALESCE(paytrnemp.SSSEEContribution,0) as SSS,
            COALESCE(paytrnemp.SSSWISPEE,0) as SSSWISP,
            COALESCE(paytrnemp.PHICEEContribution,0) as PHIC,
            COALESCE(paytrnemp.HDMFEEContribution,0) as HDMF,
            COALESCE(paytrnemp.HDMFMP2,0) as HDMFMP2,
            
            COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TaxableIncome,

            COALESCE(paytrnemp.WithholdingTax,0) as WTax,

            COALESCE(paytrnemp.TotalLoanDeductions,0) as LoanDeduction,
            COALESCE(paytrnemp.TotalDeductions,0) as OtherDeduction,

            COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalDeduction,

            COALESCE(paytrnemp.NetPay,0) as NetPay

        ");


          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

          if($FilterType!='' && $FilterType=='Location' && $BranchID>0){
             $query->where('paytrnemp.BranchID',$BranchID);
           }else if($FilterType!='' && $FilterType=='Division' && $DivisionID>0){
             $query->where('dept.DivisionID',$DivisionID);
           }else if($FilterType!='' && $FilterType=='Department' && $DepartmentID>0){
             $query->where('usr.department_id',$DepartmentID);
           }else if($FilterType!='' && $FilterType=='Section' && $SectionID>0){
              $query->where('sec.ID',$SectionID);
           }else if($FilterType!='' && $FilterType=='Job Type' && $JobTypeID>0){
             $query->where('usr.job_title_id',$JobTypeID);
           }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
             $query->where('paytrnemp.EmployeeID',$EmployeeID);
           }

       $query->where("paytrn.status",'Pending');          
       $query->orderBy("EmployeeName","ASC");
    
       $list = $query->get();
       
       return $list;

}

//PAYROLL REGISTERED DETAILED VERSION WITH POSTED/APPROVED EXCEL REPORT
public function generatePayrollRegisterApprovedDetailedListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

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

                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' ,COALESCE(emp.middle_name,'')) as EmployeeName,
            
            COALESCE(paytrnemp.BasicSalary,0) as BasicPay,

            COALESCE(paytrnincded.ECOLA,0) as ECOLA,

            COALESCE(paytrnemp.Absent,0) as Absences,
            COALESCE(paytrnemp.Late,0) as LateHours,
            COALESCE(paytrnemp.Undertime,0) as UndertimeHours,

            COALESCE(paytrnemp.NightDifferential,0) as NightDifferential,

            COALESCE(paytrnemp.Overtime1,0) as OvertimeROT,
            COALESCE(paytrnemp.Overtime2,0) as OvertimeNPROT,
            COALESCE(paytrnemp.Overtime3,0) as OvertimeDO,
            COALESCE(paytrnemp.Overtime4,0) as OvertimeSH,
            COALESCE(paytrnemp.Overtime5,0) as OvertimeLH,
            COALESCE(paytrnemp.Overtime6,0) as OvertimeSHDO,
            COALESCE(paytrnemp.Overtime7,0) as OvertimeLHDO,
            COALESCE(paytrnemp.Overtime8,0) as OvertimeOTDO,
            COALESCE(paytrnemp.Overtime9,0) as OvertimeOTSH,
            COALESCE(paytrnemp.Overtime10,0) as OvertimeOTLH,
            COALESCE(paytrnemp.Overtime11,0) as OvertimeOTSHDO,
            COALESCE(paytrnemp.Overtime12,0) as OvertimeOTLHDO,
            COALESCE(paytrnemp.Overtime13,0) as OvertimeNDDO,
            COALESCE(paytrnemp.Overtime14,0) as OvertimeNDSH,
            COALESCE(paytrnemp.Overtime15,0) as OvertimeNDLH,
            COALESCE(paytrnemp.Overtime16,0) as OvertimeNDSHDO,
            COALESCE(paytrnemp.Overtime17,0) as OvertimeNDLHDO,
            COALESCE(paytrnemp.Overtime18,0) as OvertimeNPDO,
            COALESCE(paytrnemp.Overtime19,0) as OvertimeNPSH,
            COALESCE(paytrnemp.Overtime20,0) as OvertimeNPLH,
            COALESCE(paytrnemp.Overtime21,0) as OvertimeNPSHDO,
            COALESCE(paytrnemp.Overtime22,0) as OvertimeNPLHDO,
            COALESCE(paytrnemp.Overtime23,0) as Overtime23,
            COALESCE(paytrnemp.Overtime24,0) as Overtime24,
            COALESCE(paytrnemp.Overtime25,0) as Overtime25,

            COALESCE(paytrnemp.Leave1,0) as LeaveSL,
            COALESCE(paytrnemp.Leave2,0) as LeaveVL,
            COALESCE(paytrnemp.Leave3,0) as LeaveEL,
            COALESCE(paytrnemp.Leave4,0) as LeaveML,
            COALESCE(paytrnemp.Leave5,0) as LeavePL,
            COALESCE(paytrnemp.Leave6,0) as LeaveSIL,
            COALESCE(paytrnemp.Leave7,0) as LeaveADO,
            COALESCE(paytrnemp.Leave8,0) as LeaveSPL,
            COALESCE(paytrnemp.Leave9,0) as LeaveSLW,
            COALESCE(paytrnemp.Leave10,0) as Leave10,
            COALESCE(paytrnemp.Leave11,0) as Leave11,
            COALESCE(paytrnemp.Leave12,0) as Leave12,
            COALESCE(paytrnemp.Leave13,0) as Leave13,
            COALESCE(paytrnemp.Leave14,0) as Leave14,
            COALESCE(paytrnemp.Leave15,0) as Leave15,
            COALESCE(paytrnemp.Leave16,0) as Leave16,
            COALESCE(paytrnemp.Leave17,0) as Leave17,
            COALESCE(paytrnemp.Leave18,0) as Leave18,
            COALESCE(paytrnemp.Leave19,0) as Leave19,
            COALESCE(paytrnemp.Leave20,0) as Leave20,

            COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as OtherTaxableEarnings,
            COALESCE(paytrnemp.TotalNonTaxableIncome,0) as OtherNonTaxableEarnings,

            (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0)) as GrossPay,

            COALESCE(paytrnemp.SSSEEContribution,0) as SSS,
            COALESCE(paytrnemp.SSSWISPEE,0) as SSSWISP,
            COALESCE(paytrnemp.PHICEEContribution,0) as PHIC,
            COALESCE(paytrnemp.HDMFEEContribution,0) as HDMF,
            COALESCE(paytrnemp.HDMFMP2,0) as HDMFMP2,

            COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TaxableIncome,

            COALESCE(paytrnemp.WithholdingTax,0) as WTax,

            COALESCE(paytrnemp.TotalLoanDeductions,0) as LoanDeduction,
            COALESCE(paytrnemp.TotalDeductions,0) as OtherDeduction,

            COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalDeduction,

            COALESCE(paytrnemp.NetPay,0) as NetPay

        ");


          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

          if($FilterType!='' && $FilterType=='Location' && $BranchID>0){
             $query->where('paytrnemp.BranchID',$BranchID);
           }else if($FilterType!='' && $FilterType=='Division' && $DivisionID>0){
             $query->where('dept.DivisionID',$DivisionID);
           }else if($FilterType!='' && $FilterType=='Department' && $DepartmentID>0){
             $query->where('usr.department_id',$DepartmentID);
           }else if($FilterType!='' && $FilterType=='Section' && $SectionID>0){
              $query->where('sec.ID',$SectionID);
           }else if($FilterType!='' && $FilterType=='Job Type' && $JobTypeID>0){
             $query->where('usr.job_title_id',$JobTypeID);
           }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
             $query->where('paytrnemp.EmployeeID',$EmployeeID);
           }

       $query->where("paytrn.status",'Pending');  
       $query->orderBy("EmployeeName","ASC");
    
       $list = $query->get();
       return $list;
  }

// EMPLOYEE DTR EXCEL REPORT
public function generateEmployeeDTRListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $BranchSiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $query = DB::table('payroll_employee_dtr_summary as peds')
        ->join('users as emp', 'emp.id', '=', 'peds.EmployeeID') 
        ->join('payroll_period_schedule as pp', 'pp.ID', '=', 'peds.PayrollPeriodID')   
        ->join('payroll_branch as brnch', 'brnch.ID', '=', 'emp.company_branch_id')
        ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'emp.company_branch_site_id')
        ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')     
        ->leftjoin('payroll_section as sec', 'sec.id', '=', 'emp.section_id')  
        ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')   

        ->selectraw("
                                    
            COALESCE(peds.PayrollPeriodCode,'') as PayrollPeriodCode,                    
            COALESCE(peds.EmployeeNumber,'') as EmployeeNumber,

                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as FullName,
            
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
            
            COALESCE(peds.Status,'') as Status
  
        ");
    

       $query->where('peds.PayrollPeriodID',$PayrollPeriodID);
       $query->where("peds.status",$Status); 

       if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
           $query->whereIn('emp.company_branch_id',$BranchID);
       }else if($FilterType!='' && $FilterType=='Site' && !empty($BranchSiteID)){
          $query->whereIn('emp.company_branch_site_id',$BranchSiteID);
       }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
           $query->whereIn('dept.DivisionID',$DivisionID);
       }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
           $query->whereIn('emp.department_id',$DepartmentID);
       }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
            $query->whereIn('sec.ID',$SectionID);
       }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
           $query->whereIn('emp.job_title_id',$JobTypeID);
       }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
           $query->where('peds.EmployeeID',$EmployeeID);
       }

    $query->orderBy("peds.Status","ASC");
    $query->orderBy("FullName","ASC");  
    
    $list = $query->get();

    return $list;

}

// SSS CONTRIBUTION WITH POSTED/APPROVED EXCEL REPORT 
public function generateSSSApprovedEmployeeContributionListExcel($param){

  $Year=$param['Year'];
  $Month=$param['Month'];

  $Limit=$param['Limit'];
  $PageNo=$param['PageNo'];

  $Filter=$param['Filter'];
  $SearchText=$param['SearchText'];

  $query = DB::table('payroll_transaction_employee as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
        ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'emp.id', '=', 'paytrnemp.EmployeeID')
        ->selectraw("
                emp.id as EmployeeID,
                emp.shortid as EmployeeNo,
                emp.last_name as LastName,
                emp.first_name as FirstName,
                emp.middle_name as MiddleName,
                emp.sss_number as SSSNo,

                SUM(COALESCE(paytrnemp.SSSEEContribution,0)) as EmployeeShare,
                SUM(COALESCE(paytrnemp.SSSWISPEE,0)) as EmployeeWISPEE,
                SUM(COALESCE(paytrnemp.SSSERContribution,0)) as EmployerShare,

                SUM(COALESCE(paytrnemp.SSSEEContribution,0)) + SUM(COALESCE(paytrnemp.SSSWISPEE,0)) + SUM(COALESCE(paytrnemp.SSSERContribution,0)) as Total,

                'Posted' as Status   
          ")
          ->whereraw("YEAR(prd.EndDate) = ?", [$Year])
          ->whereraw("MONTH(prd.EndDate) = ?", [$Month])
          ->groupBy(
              'emp.id',
              'emp.shortid',
              'emp.last_name',
              'emp.first_name',
              'emp.middle_name',
              'emp.sss_number'
          )
          ->havingraw("SUM(COALESCE(paytrnemp.SSSEEContribution,0)) + SUM(COALESCE(paytrnemp.SSSERContribution,0)) > 0");

    if(!empty($Filter)){
      $arFilter = explode("|",$Filter);
      if(trim($arFilter[0]) == "Location"){
       $query->where("emp.company_branch_id",trim($arFilter[1]));  
      }else if(trim($arFilter[0]) == "Site"){
       $query->where("emp.company_branch_site_id",trim($arFilter[1]));  
      }
    }

    if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(emp.shortid,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
             }
        }
    }

    $query->orderByraw("emp.last_name ASC");
    $query->orderByraw("emp.first_name ASC");
    $query->orderByraw("emp.middle_name ASC");

    $list = $query->get();

    return $list;

  }


// SSS CONTRIBUTION WITH UN-POSTED/PENDING EXCEL REPORT 
public function generateSSSPendingEmployeeContributionListExcel($param){

  $Year=$param['Year'];
  $Month=$param['Month'];

  $Limit=$param['Limit'];
  $PageNo=$param['PageNo'];

  $Filter=$param['Filter'];
  $SearchText=$param['SearchText'];

   $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
        ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'emp.id', '=', 'paytrnemp.EmployeeID')
        ->selectraw("
                emp.id as EmployeeID,
                emp.shortid as EmployeeNo,
                emp.last_name as LastName,
                emp.first_name as FirstName,
                emp.middle_name as MiddleName,
                emp.sss_number as SSSNo,

                SUM(COALESCE(paytrnemp.SSSEEContribution,0)) as EmployeeShare,
                SUM(COALESCE(paytrnemp.SSSWISPEE,0)) as EmployeeWISPEE,
                SUM(COALESCE(paytrnemp.SSSERContribution,0)) as EmployerShare,

                SUM(COALESCE(paytrnemp.SSSEEContribution,0)) + SUM(COALESCE(paytrnemp.SSSWISPEE,0)) + SUM(COALESCE(paytrnemp.SSSERContribution,0)) as Total,

                'Un-Posted' as Status        
          ")
          ->whereraw("YEAR(prd.EndDate) = ?", [$Year])
          ->whereraw("MONTH(prd.EndDate) = ?", [$Month])
          ->groupBy(
              'emp.id',
              'emp.shortid',
              'emp.last_name',
              'emp.first_name',
              'emp.middle_name',
              'emp.sss_number'
          )
          ->havingraw("SUM(COALESCE(paytrnemp.SSSEEContribution,0)) + SUM(COALESCE(paytrnemp.SSSERContribution,0)) > 0");

    if(!empty($Filter)){
      $arFilter = explode("|",$Filter);
      if(trim($arFilter[0]) == "Location"){
       $query->where("emp.company_branch_id",trim($arFilter[1]));  
      }else if(trim($arFilter[0]) == "Site"){
       $query->where("emp.company_branch_site_id",trim($arFilter[1]));  
      }
    }

    if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(emp.shortid,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
             }
        }
    }

    $query->orderByraw("emp.last_name ASC");
    $query->orderByraw("emp.first_name ASC");
    $query->orderByraw("emp.middle_name ASC");

    $list = $query->get();

    return $list;

  }

// HDMF CONTRIBUTION WITH POSTED/APPROVED EXCEL REPORT 
public function generateHDMFApprovedEmployeeContributionListExcel($param){

  $Year=$param['Year'];
  $Month=$param['Month'];

  $Limit=$param['Limit'];
  $PageNo=$param['PageNo'];

  $Filter=$param['Filter'];
  $SearchText=$param['SearchText'];

  $query = DB::table('payroll_transaction_employee as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
        ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'emp.id', '=', 'paytrnemp.EmployeeID')
        ->selectraw("
                emp.id as EmployeeID,
                emp.shortid as EmployeeNo,
                emp.last_name as LastName,
                emp.first_name as FirstName,
                emp.middle_name as MiddleName,
                emp.pagibig_number as PAGIBIGNo,

                SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) as EmployeeShare,
                SUM(COALESCE(paytrnemp.HDMFMP2,0)) as EmployeeMP2,
                SUM(COALESCE(paytrnemp.HDMFERContribution,0)) as EmployerShare,

                SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) + SUM(COALESCE(paytrnemp.HDMFMP2,0)) + SUM(COALESCE(paytrnemp.HDMFERContribution,0)) as Total,

                'Posted' as Status       
          ")
          ->whereraw("YEAR(prd.EndDate) = ?", [$Year])
          ->whereraw("MONTH(prd.EndDate) = ?", [$Month])
          ->groupBy(
              'emp.id',
              'emp.shortid',
              'emp.last_name',
              'emp.first_name',
              'emp.middle_name',
              'emp.pagibig_number'
          )
          ->havingraw("SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) + SUM(COALESCE(paytrnemp.HDMFERContribution,0)) > 0");

  if(!empty($Filter)){
      $arFilter = explode("|",$Filter);
      if(trim($arFilter[0]) == "Location"){
       $query->where("emp.company_branch_id",trim($arFilter[1]));  
      }else if(trim($arFilter[0]) == "Site"){
       $query->where("emp.company_branch_site_id",trim($arFilter[1]));  
      }
    }

    if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(emp.shortid,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
             }
        }
    }
         
    $query->orderByraw("emp.last_name ASC");
    $query->orderByraw("emp.first_name ASC");
    $query->orderByraw("emp.middle_name ASC");

    $list = $query->get();

    return $list;

  }

 // HDMF CONTRIBUTION WITH UN-POSTED/PENDING EXCEL REPORT 
 public function generateHDMFPendingEmployeeContributionListExcel($param){

  $Year=$param['Year'];
  $Month=$param['Month'];

  $Limit=$param['Limit'];
  $PageNo=$param['PageNo'];

  $Filter=$param['Filter'];
  $SearchText=$param['SearchText'];

  $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
        ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'emp.id', '=', 'paytrnemp.EmployeeID')
        ->selectraw("
                emp.id as EmployeeID,
                emp.shortid as EmployeeNo,
                emp.last_name as LastName,
                emp.first_name as FirstName,
                emp.middle_name as MiddleName,
                emp.pagibig_number as PAGIBIGNo,

                SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) as EmployeeShare,
                SUM(COALESCE(paytrnemp.HDMFMP2,0)) as EmployeeMP2,
                SUM(COALESCE(paytrnemp.HDMFERContribution,0)) as EmployerShare,

                SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) + SUM(COALESCE(paytrnemp.HDMFMP2,0)) + SUM(COALESCE(paytrnemp.HDMFERContribution,0)) as Total,

                'Un-Posted' as Status        
          ")
          ->whereraw("YEAR(prd.EndDate) = ?", [$Year])
          ->whereraw("MONTH(prd.EndDate) = ?", [$Month])
          ->groupBy(
              'emp.id',
              'emp.shortid',
              'emp.last_name',
              'emp.first_name',
              'emp.middle_name',
              'emp.pagibig_number'
          )
          ->havingraw("SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) + SUM(COALESCE(paytrnemp.HDMFERContribution,0)) > 0");
         
  if(!empty($Filter)){
      $arFilter = explode("|",$Filter);
      if(trim($arFilter[0]) == "Location"){
       $query->where("emp.company_branch_id",trim($arFilter[1]));  
      }else if(trim($arFilter[0]) == "Site"){
       $query->where("emp.company_branch_site_id",trim($arFilter[1]));  
      }
    }

    if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(emp.shortid,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
             }
        }
    }

    $query->orderByraw("emp.last_name ASC");
    $query->orderByraw("emp.first_name ASC");
    $query->orderByraw("emp.middle_name ASC");

    $list = $query->get();

    return $list;

  }

  //PHIC CONTRIBUTION WITH POSTED/APPROVED EXCEL REPORT 
  public function generatePHICApprovedEmployeeContributionListExcel($param){

  $Year=$param['Year'];
  $Month=$param['Month'];

  $Limit=$param['Limit'];
  $PageNo=$param['PageNo'];

  $Filter=$param['Filter'];
  $SearchText=$param['SearchText'];

  $query = DB::table('payroll_transaction_employee as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
        ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'emp.id', '=', 'paytrnemp.EmployeeID')
        ->selectraw("
                emp.id as EmployeeID,
                emp.shortid as EmployeeNo,
                emp.last_name as LastName,
                emp.first_name as FirstName,
                emp.middle_name as MiddleName,
                emp.philhealth_number as PHICNo,

                SUM(COALESCE(paytrnemp.PHICEEContribution,0)) as EmployeeShare,
                SUM(COALESCE(paytrnemp.PHICERContribution,0)) as EmployerShare,

                SUM(COALESCE(paytrnemp.PHICEEContribution,0)) + SUM(COALESCE(paytrnemp.PHICERContribution,0)) as Total,

                'Un-Posted' as Status        
          ")
          ->whereraw("YEAR(prd.EndDate) = ?", [$Year])
          ->whereraw("MONTH(prd.EndDate) = ?", [$Month])
          ->groupBy(
              'emp.id',
              'emp.shortid',
              'emp.last_name',
              'emp.first_name',
              'emp.middle_name',
              'emp.philhealth_number'
          )
          ->havingraw("SUM(COALESCE(paytrnemp.PHICEEContribution,0)) + SUM(COALESCE(paytrnemp.PHICERContribution,0)) > 0");

  if(!empty($Filter)){
      $arFilter = explode("|",$Filter);
      if(trim($arFilter[0]) == "Location"){
       $query->where("emp.company_branch_id",trim($arFilter[1]));  
      }else if(trim($arFilter[0]) == "Site"){
       $query->where("emp.company_branch_site_id",trim($arFilter[1]));  
      }
    }

    if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(emp.shortid,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
             }
        }
    }

    $query->orderByraw("emp.last_name ASC");
    $query->orderByraw("emp.first_name ASC");
    $query->orderByraw("emp.middle_name ASC");

    $list = $query->get();

    return $list;

  }

  //PHIC CONTRIBUTION  WITH UN-POSTED/PENDING EXCEL REPORT
  public function generatePHICPendingEmployeeContributionListExcel($param){

  $Year=$param['Year'];
  $Month=$param['Month'];

  $Limit=$param['Limit'];
  $PageNo=$param['PageNo'];

  $Filter=$param['Filter'];
  $SearchText=$param['SearchText'];

  $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
        ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'emp.id', '=', 'paytrnemp.EmployeeID')
        ->selectraw("
                emp.id as EmployeeID,
                emp.shortid as EmployeeNo,
                emp.last_name as LastName,
                emp.first_name as FirstName,
                emp.middle_name as MiddleName,
                emp.philhealth_number as PHICNo,

                SUM(COALESCE(paytrnemp.PHICEEContribution,0)) as EmployeeShare,
                SUM(COALESCE(paytrnemp.PHICERContribution,0)) as EmployerShare,

                SUM(COALESCE(paytrnemp.PHICEEContribution,0)) + SUM(COALESCE(paytrnemp.PHICERContribution,0)) as Total,

                'Un-Posted' as Status        
          ")
          ->whereraw("YEAR(prd.EndDate) = ?", [$Year])
          ->whereraw("MONTH(prd.EndDate) = ?", [$Month])
          ->groupBy(
              'emp.id',
              'emp.shortid',
              'emp.last_name',
              'emp.first_name',
              'emp.middle_name',
              'emp.philhealth_number'
          )
          ->havingraw("SUM(COALESCE(paytrnemp.PHICEEContribution,0)) + SUM(COALESCE(paytrnemp.PHICERContribution,0)) > 0");

  if(!empty($Filter)){
      $arFilter = explode("|",$Filter);
      if(trim($arFilter[0]) == "Location"){
       $query->where("emp.company_branch_id",trim($arFilter[1]));  
      }else if(trim($arFilter[0]) == "Site"){
       $query->where("emp.company_branch_site_id",trim($arFilter[1]));  
      }
    }

    if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(emp.shortid,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
             }
        }
    }

    $query->orderByraw("emp.last_name ASC");
    $query->orderByraw("emp.first_name ASC");
    $query->orderByraw("emp.middle_name ASC");

    $list = $query->get();

    return $list;

  }

  //LOAN DEDUCTION WITH POSTED/APPROVED STATUS EXCEL REPORTS
  public function generateEmployeeApprovedLoanDeductionListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $SiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $strFields = $this->getLoanFields();

    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("

              COALESCE(usr.shortid,'') as EmployeeNo,     

                CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,
              ".$strFields."

              'Posted' as Status

          ");

          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

         if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
           $query->whereIn('paytrnemp.BranchID',$BranchID);
         }else if($FilterType!='' && $FilterType=='Site' && !empty($SiteID)){
           $query->whereIn('usr.company_branch_site_id',$SiteID);
         }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
           $query->whereIn('dept.DivisionID',$DivisionID);
         }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
           $query->whereIn('usr.department_id',$DepartmentID);
         }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
            $query->whereIn('sec.ID',$SectionID);
         }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
           $query->whereIn('usr.job_title_id',$JobTypeID);
         }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
           $query->where('paytrnemp.EmployeeID',$EmployeeID);
         }

        $query->orderBy("usr.last_name","ASC");
        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");

        $list = $query->get();
        return $list;

    }

  //LOAN DEDUCTION WITH UN-POSTED/PENDING STATUS EXCEL REPORTS
  public function generateEmployeePendingLoanDeductionListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $BranchSiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $strFields = $this->getLoanFields();

    $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
          ->join('payroll_transaction_income_deduction_temp as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')

          ->selectraw("
     
              COALESCE(usr.shortid,'') as EmployeeNo,     
                CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,
              ".$strFields."
              
              'Un-Posted' as Status

          ");

         $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

         if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
           $query->whereIn('paytrnemp.BranchID',$BranchID);
         }else if($FilterType!='' && $FilterType=='Site' && !empty($SiteID)){
           $query->whereIn('usr.company_branch_site_id',$SiteID);
         }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
           $query->whereIn('dept.DivisionID',$DivisionID);
         }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
           $query->whereIn('usr.department_id',$DepartmentID);
         }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
            $query->whereIn('sec.ID',$SectionID);
         }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
           $query->whereIn('usr.job_title_id',$JobTypeID);
         }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
           $query->where('paytrnemp.EmployeeID',$EmployeeID);
         }

        $query->orderBy("usr.last_name","ASC");
        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");

        $list = $query->get();

        return $list;

    }

public function getLoanFields(){

      $strFields = "";

      //Get Income List
      $param["SearchText"] = ""; 
      $param["Status"] = "";
      $param["PageNo"] = 0;
      $param["Limit"] =  0;
      $LoanType = new LoanType();
      $LoanTypeList = $LoanType->getLoanTypeList($param);

      foreach ($LoanTypeList as $row){
          if($row->ID == 1){
            $strFields = $strFields."COALESCE(paytrnincded.Loan1,0) as Loan1, ";
          }else if($row->ID == 2){
            $strFields = $strFields."COALESCE(paytrnincded.Loan2,0) as Loan2, ";
          }else if($row->ID == 3){
            $strFields = $strFields."COALESCE(paytrnincded.Loan3,0) as Loan3, ";
          }else if($row->ID == 4){
            $strFields = $strFields."COALESCE(paytrnincded.Loan4,0) as Loan4, ";
          }else if($row->ID == 5){
            $strFields = $strFields."COALESCE(paytrnincded.Loan5,0) as Loan5, ";
          }else if($row->ID == 6){
            $strFields = $strFields."COALESCE(paytrnincded.Loan6,0) as Loan6, ";
          }else if($row->ID == 7){
            $strFields = $strFields."COALESCE(paytrnincded.Loan7,0) as Loan7, ";
          }else if($row->ID == 8){
            $strFields = $strFields."COALESCE(paytrnincded.Loan8,0) as Loan8, ";
          }else if($row->ID == 9){
            $strFields = $strFields."COALESCE(paytrnincded.Loan9,0) as Loan9, ";
          }else if($row->ID == 10){
            $strFields = $strFields."COALESCE(paytrnincded.Loan10,0) as Loan10, ";
          }else if($row->ID == 11){
            $strFields = $strFields."COALESCE(paytrnincded.Loan11,0) as Loan11, ";
          }else if($row->ID == 12){
            $strFields = $strFields."COALESCE(paytrnincded.Loan12,0) as Loan12, ";
          }else if($row->ID == 13){
            $strFields = $strFields."COALESCE(paytrnincded.Loan13,0) as Loan13, ";
          }else if($row->ID == 14){
            $strFields = $strFields."COALESCE(paytrnincded.Loan14,0) as Loan14, ";
          }else if($row->ID == 15){
            $strFields = $strFields."COALESCE(paytrnincded.Loan15,0) as Loan15, ";
          }else if($row->ID == 16){
            $strFields = $strFields."COALESCE(paytrnincded.Loan16,0) as Loan16, ";
          }else if($row->ID == 17){
            $strFields = $strFields."COALESCE(paytrnincded.Loan17,0) as Loan17, ";
          }else if($row->ID == 18){
            $strFields = $strFields."COALESCE(paytrnincded.Loan18,0) as Loan18, ";
          }else if($row->ID == 19){
            $strFields = $strFields."COALESCE(paytrnincded.Loan19,0) as Loan19, ";
          }else if($row->ID == 20){
            $strFields = $strFields."COALESCE(paytrnincded.Loan20,0) as Loan20, ";
          }else if($row->ID == 21){
            $strFields = $strFields."COALESCE(paytrnincded.Loan21,0) as Loan21, ";
          }else if($row->ID == 22){
            $strFields = $strFields."COALESCE(paytrnincded.Loan22,0) as Loan22, ";
          }else if($row->ID == 23){
            $strFields = $strFields."COALESCE(paytrnincded.Loan23,0) as Loan23, ";
          }else if($row->ID == 24){
            $strFields = $strFields."COALESCE(paytrnincded.Loan24,0) as Loan24, ";
          }else if($row->ID == 25){
            $strFields = $strFields."COALESCE(paytrnincded.Loan25,0) as Loan25, ";
          }
      }

      return $strFields;

    }
    
// OTHER DEDUCTION WITH POSTED/APPROVE STATUS EXCEL REPORT
 public function generateEmployeeApprovedOtherDeductionListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $SiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')          
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->selectraw("
                   
              COALESCE(usr.shortid,'') as EmployeeNo,     
                CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,              

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
              COALESCE(paytrnincded.Deduction97,0) as PGECCShortTermLoan,
              COALESCE(paytrnincded.Deduction149,0) as BusFare,

              'Posted' as Status

          ");

          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

         if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
           $query->whereIn('paytrnemp.BranchID',$BranchID);
         }else if($FilterType!='' && $FilterType=='Site' && !empty($SiteID)){
           $query->whereIn('usr.company_branch_site_id',$SiteID);
         }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
           $query->whereIn('dept.DivisionID',$DivisionID);
         }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
           $query->whereIn('usr.department_id',$DepartmentID);
         }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
            $query->whereIn('sec.ID',$SectionID);
         }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
           $query->whereIn('usr.job_title_id',$JobTypeID);
         }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
           $query->where('paytrnemp.EmployeeID',$EmployeeID);
         }

        $query->orderBy("usr.last_name","ASC");
        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");

        $list = $query->get();

        return $list;

    }

    // OTHER DEDUCTION WITH UN-POSTED/PENDING STATUS EXCEL REPORT
 public function generateEmployeePendingOtherDeductionListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $SiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
          ->join('payroll_transaction_income_deduction_temp as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')          
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->selectraw("
                   
              COALESCE(usr.shortid,'') as EmployeeNo,     

                CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,              

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
              COALESCE(paytrnincded.Deduction97,0) as PGECCShortTermLoan,
              COALESCE(paytrnincded.Deduction149,0) as BusFare,

              'Un-Posted' as Status

          ");

          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

         if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
           $query->whereIn('paytrnemp.BranchID',$BranchID);
         }else if($FilterType!='' && $FilterType=='Site' && !empty($SiteID)){
           $query->whereIn('usr.company_branch_site_id',$SiteID);
         }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
           $query->whereIn('dept.DivisionID',$DivisionID);
         }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
           $query->whereIn('usr.department_id',$DepartmentID);
         }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
            $query->whereIn('sec.ID',$SectionID);
         }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
           $query->whereIn('usr.job_title_id',$JobTypeID);
         }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
           $query->where('paytrnemp.EmployeeID',$EmployeeID);
         }

        $query->orderBy("usr.last_name","ASC");
        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");


        $list = $query->get();

        return $list;

    }

   //OTHER EARING TAXABLE WITH POSTED/APPROVED STATUS EXCEL REPORT
 public function generateEmployeeApprovedOtherEarningTaxableListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $SiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $strFields = $this->getOEFields("Taxable Income");

    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("
     
              COALESCE(usr.shortid,'') as EmployeeNo,
                CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' ,COALESCE(usr.middle_name,'')) as FullName,

              ".$strFields."

              0 as AdjustmentPrevPayroll,   

            'Posted' as Status

          ");

          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

         if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
           $query->whereIn('paytrnemp.BranchID',$BranchID);
         }else if($FilterType!='' && $FilterType=='Site' && !empty($SiteID)){
           $query->whereIn('usr.company_branch_site_id',$SiteID);
         }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
           $query->whereIn('dept.DivisionID',$DivisionID);
         }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
           $query->whereIn('usr.department_id',$DepartmentID);
         }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
            $query->whereIn('sec.ID',$SectionID);
         }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
           $query->whereIn('usr.job_title_id',$JobTypeID);
         }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
           $query->where('paytrnemp.EmployeeID',$EmployeeID);
         }
 
        $query->orderBy("usr.last_name","ASC");
        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");

        $list = $query->get();

        return $list;

    }

     //OTHER EARING TAXABLE WITH POSTED/APPROVED STATUS EXCEL REPORT
 public function generateEmployeePendingOtherEarningTaxableListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $SiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $strFields = $this->getOEFields("Taxable Income");

    $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
          ->join('payroll_transaction_income_deduction_temp as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("
     
              COALESCE(usr.shortid,'') as EmployeeNo,

                CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,

              
              ".$strFields."

              0 as AdjustmentPrevPayroll,   

              'Un-Posted' as Status

          ");

          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

         if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
           $query->whereIn('paytrnemp.BranchID',$BranchID);
         }else if($FilterType!='' && $FilterType=='Site' && !empty($SiteID)){
           $query->whereIn('usr.company_branch_site_id',$SiteID);
         }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
           $query->whereIn('dept.DivisionID',$DivisionID);
         }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
           $query->whereIn('usr.department_id',$DepartmentID);
         }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
            $query->whereIn('sec.ID',$SectionID);
         }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
           $query->whereIn('usr.job_title_id',$JobTypeID);
         }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
           $query->where('paytrnemp.EmployeeID',$EmployeeID);
         }
 
        $query->orderBy("usr.last_name","ASC");
        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");

        $list = $query->get();

        return $list;

    }
 
 //OTHER EARING NON TAXABLE WITH POSTED/APPROVED STATUS EXCEL REPORT
  public function generateEmployeeOtherApprovedEarningNonTaxableListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $SiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $strFields = $this->getOEFields("Non-Taxable Income");

    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("
                 
              COALESCE(usr.shortid,'') as EmployeeNo,     
                CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' ,COALESCE(usr.middle_name,'')) as FullName,              

              
            ".$strFields."

            'Posted' as Status

          ");

          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

          if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
             $query->whereIn('paytrnemp.BranchID',$BranchID);
           }else if($FilterType!='' && $FilterType=='Site' && !empty($SiteID)){
             $query->whereIn('usr.company_branch_site_id',$SiteID);
           }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
             $query->whereIn('dept.DivisionID',$DivisionID);
           }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
             $query->whereIn('usr.department_id',$DepartmentID);
           }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
              $query->whereIn('sec.ID',$SectionID);
           }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
             $query->whereIn('usr.job_title_id',$JobTypeID);
           }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
             $query->where('paytrnemp.EmployeeID',$EmployeeID);
           }

        $query->orderBy("usr.last_name","ASC");
        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");

        $list = $query->get();

        return $list;

  }


 //OTHER EARING NON TAXABLE WITH UN-POSTED/PENDING STATUS EXCEL REPORT
  public function generateEmployeeOtherPendingEarningNonTaxableListExcel($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $SiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $strFields = $this->getOEFields("Non-Taxable Income");

    $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
          ->join('payroll_transaction_income_deduction_temp as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->selectraw("
                 
              COALESCE(usr.shortid,'') as EmployeeNo,     
                CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' ,COALESCE(usr.middle_name,'')) as FullName,              

            ".$strFields."

             'Un-Posted' as Status

          ");

          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

          if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
             $query->whereIn('paytrnemp.BranchID',$BranchID);
           }else if($FilterType!='' && $FilterType=='Site' && !empty($SiteID)){
             $query->whereIn('usr.company_branch_site_id',$SiteID);
           }else if($FilterType!='' && $FilterType=='Division' && !empty($DivisionID)){
             $query->whereIn('dept.DivisionID',$DivisionID);
           }else if($FilterType!='' && $FilterType=='Department' && !empty($DepartmentID)){
             $query->whereIn('usr.department_id',$DepartmentID);
           }else if($FilterType!='' && $FilterType=='Section' && !empty($SectionID)){
              $query->whereIn('sec.ID',$SectionID);
           }else if($FilterType!='' && $FilterType=='Job Type' && !empty($JobTypeID)){
             $query->whereIn('usr.job_title_id',$JobTypeID);
           }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
             $query->where('paytrnemp.EmployeeID',$EmployeeID);
           }

        $query->orderBy("usr.last_name","ASC");
        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");

        $list = $query->get();

        return $list;

    }
 

    public function getOEFields($vType){

      $strFields = "";

      //Get Income List
      $param["SearchText"] = ""; 
      $param["Status"] = $vType;
      $param["PageNo"] = 0;
      $param["Limit"] =  0;
      $IncomeDeductionType = new IncomeDeductionType();
      $IncomeDeductionTypeList = $IncomeDeductionType->getIncomeDeductionTypeList($param);

      foreach ($IncomeDeductionTypeList as $row){
          if($row->ID == 99){
            $strFields = $strFields."COALESCE(paytrnincded.Income99,0) as Income99, ";
          }else if($row->ID == 100){
            $strFields = $strFields."COALESCE(paytrnincded.Income100,0) as Income100, ";
          }else if($row->ID == 101){
            $strFields = $strFields."COALESCE(paytrnincded.Income101,0) as Income101, ";
          }else if($row->ID == 102){
            $strFields = $strFields."COALESCE(paytrnincded.Income102,0) as Income102, ";
          }else if($row->ID == 103){
            $strFields = $strFields."COALESCE(paytrnincded.Income103,0) as Income103, ";
          }else if($row->ID == 104){
            $strFields = $strFields."COALESCE(paytrnincded.Income104,0) as Income104, ";
          }else if($row->ID == 105){
            $strFields = $strFields."COALESCE(paytrnincded.Income105,0) as Income105, ";
          }else if($row->ID == 106){
            $strFields = $strFields."COALESCE(paytrnincded.Income106,0) as Income106, ";
          }else if($row->ID == 107){
            $strFields = $strFields."COALESCE(paytrnincded.Income107,0) as Income107, ";
          }else if($row->ID == 108){
            $strFields = $strFields."COALESCE(paytrnincded.Income108,0) as Income108, ";
          }else if($row->ID == 109){
            $strFields = $strFields."COALESCE(paytrnincded.Income109,0) as Income109, ";
          }else if($row->ID == 110){
            $strFields = $strFields."COALESCE(paytrnincded.Income110,0) as Income110, ";
          }else if($row->ID == 111){
            $strFields = $strFields."COALESCE(paytrnincded.Income111,0) as Income111, ";
          }else if($row->ID == 112){
            $strFields = $strFields."COALESCE(paytrnincded.Income112,0) as Income112, ";
          }else if($row->ID == 113){
            $strFields = $strFields."COALESCE(paytrnincded.Income113,0) as Income113, ";
          }else if($row->ID == 114){
            $strFields = $strFields."COALESCE(paytrnincded.Income114,0) as Income114, ";
          }else if($row->ID == 115){
            $strFields = $strFields."COALESCE(paytrnincded.Income115,0) as Income115, ";
          }else if($row->ID == 116){
            $strFields = $strFields."COALESCE(paytrnincded.Income116,0) as Income116, ";
          }else if($row->ID == 117){
            $strFields = $strFields."COALESCE(paytrnincded.Income117,0) as Income117, ";
          }else if($row->ID == 118){
            $strFields = $strFields."COALESCE(paytrnincded.Income118,0) as Income118, ";
          }else if($row->ID == 119){
            $strFields = $strFields."COALESCE(paytrnincded.Income119,0) as Income119, ";
          }else if($row->ID == 120){
            $strFields = $strFields."COALESCE(paytrnincded.Income120,0) as Income120, ";
          }else if($row->ID == 121){
            $strFields = $strFields."COALESCE(paytrnincded.Income121,0) as Income121, ";
          }else if($row->ID == 122){
            $strFields = $strFields."COALESCE(paytrnincded.Income122,0) as Income122, ";
          }else if($row->ID == 123){
            $strFields = $strFields."COALESCE(paytrnincded.Income123,0) as Income123, ";
          }else if($row->ID == 124){
            $strFields = $strFields."COALESCE(paytrnincded.Income124,0) as Income124, ";
          }else if($row->ID == 125){
            $strFields = $strFields."COALESCE(paytrnincded.Income125,0) as Income125, ";
          }else if($row->ID == 126){
            $strFields = $strFields."COALESCE(paytrnincded.Income126,0) as Income126, ";
          }else if($row->ID == 127){
            $strFields = $strFields."COALESCE(paytrnincded.Income127,0) as Income127, ";
          }else if($row->ID == 128){
            $strFields = $strFields."COALESCE(paytrnincded.Income128,0) as Income128, ";
          }else if($row->ID == 129){
            $strFields = $strFields."COALESCE(paytrnincded.Income129,0) as Income129, ";
          }else if($row->ID == 130){
            $strFields = $strFields."COALESCE(paytrnincded.Income130,0) as Income130, ";
          }else if($row->ID == 131){
            $strFields = $strFields."COALESCE(paytrnincded.Income131,0) as Income131, ";
          }else if($row->ID == 132){
            $strFields = $strFields."COALESCE(paytrnincded.Income132,0) as Income132, ";
          }else if($row->ID == 133){
            $strFields = $strFields."COALESCE(paytrnincded.Income133,0) as Income133, ";
          }else if($row->ID == 134){
            $strFields = $strFields."COALESCE(paytrnincded.Income134,0) as Income134, ";
          }else if($row->ID == 135){
            $strFields = $strFields."COALESCE(paytrnincded.Income135,0) as Income135, ";
          }else if($row->ID == 136){
            $strFields = $strFields."COALESCE(paytrnincded.Income136,0) as Income136, ";
          }else if($row->ID == 137){
            $strFields = $strFields."COALESCE(paytrnincded.Income137,0) as Income137, ";
          }else if($row->ID == 138){
            $strFields = $strFields."COALESCE(paytrnincded.Income138,0) as Income138, ";
          }else if($row->ID == 139){
            $strFields = $strFields."COALESCE(paytrnincded.Income139,0) as Income139, ";
          }else if($row->ID == 140){
            $strFields = $strFields."COALESCE(paytrnincded.Income140,0) as Income140, ";
          }else if($row->ID == 141){
            $strFields = $strFields."COALESCE(paytrnincded.Income141,0) as Income141, ";
          }else if($row->ID == 142){
            $strFields = $strFields."COALESCE(paytrnincded.Income142,0) as Income142, ";
          }else if($row->ID == 143){
            $strFields = $strFields."COALESCE(paytrnincded.Income143,0) as Income143, ";
          }else if($row->ID == 144){
            $strFields = $strFields."COALESCE(paytrnincded.Income144,0) as Income144, ";
          }else if($row->ID == 145){
            $strFields = $strFields."COALESCE(paytrnincded.Income145,0) as Income145, ";
          }else if($row->ID == 146){
            $strFields = $strFields."COALESCE(paytrnincded.Income146,0) as Income146, ";
          }else if($row->ID == 147){
            $strFields = $strFields."COALESCE(paytrnincded.Income147,0) as Income147, ";
          }else if($row->ID == 148){
            $strFields = $strFields."COALESCE(paytrnincded.Income148,0) as Income148, ";
          }
      }

      return $strFields;

    }

}