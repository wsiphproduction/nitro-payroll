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

class Reports extends Model
{

//EMPLOYEE PAYSLIP REPORT
 public function getPayrollTransactionEmployeeListByFilter($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $BranchSiteID=$param["BranchSiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $pending = DB::table('payroll_transaction_employee_temp as paytrnemp')
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'usr.company_branch_site_id')
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
              CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,

              iif(COALESCE(usr.status,1) = 1, 'Active', 'Inactive') as EmployeeStatus,
              COALESCE(usr.contact_number,'') as ContactNumber,
              COALESCE(usr.email,'') as EmailAddress,

              COALESCE(paytrnemp.BranchID,0) as BranchID,
              COALESCE(brn.BranchName,'') as BranchName,

              COALESCE(usr.company_branch_site_id,0) as SiteID,
              COALESCE(brnchsite.SiteName,'') as SiteName,

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
              COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as TotalOtherTaxableIncome,
              COALESCE(paytrnemp.TotalNonTaxableIncome,0) as TotalNonTaxableIncome,
              COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalEEInsurancePremiums,
              COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalOtherDeductions,
              COALESCE(paytrnemp.NetPay,0) as NetPay,
              COALESCE(paytrnemp.MinTakeHomePay,0) as MinTakeHomePay
          ");

          $pending->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

           if($FilterType!='' && $FilterType=='Location' && $BranchID>0){
             $pending->where('paytrnemp.BranchID',$BranchID);
           }else if($FilterType!='' && $FilterType=='Site' && $BranchSiteID>0){
             $pending->where('usr.company_branch_site_id',$BranchSiteID);
           }else if($FilterType!='' && $FilterType=='Division' && $DivisionID>0){
             $pending->where('dept.DivisionID',$DivisionID);
           }else if($FilterType!='' && $FilterType=='Department' && $DepartmentID>0){
             $pending->where('usr.department_id',$DepartmentID);
           }else if($FilterType!='' && $FilterType=='Section' && $SectionID>0){
              $pending->where('sec.ID',$SectionID);
           }else if($FilterType!='' && $FilterType=='Job Type' && $JobTypeID>0){
             $pending->where('usr.job_title_id',$JobTypeID);
           }else if($FilterType!='' && $FilterType=='Employee' && $EmployeeID>0){
             $pending->where('paytrnemp.EmployeeID',$EmployeeID);
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
          ->leftjoin('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'usr.company_branch_site_id')
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
              CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,
              iif(COALESCE(usr.status,1) = 1, 'Active', 'Inactive') as EmployeeStatus,
              COALESCE(usr.contact_number,'') as ContactNumber,
              COALESCE(usr.email,'') as EmailAddress,

              COALESCE(paytrnemp.BranchID,0) as BranchID,
              COALESCE(brn.BranchName,'') as BranchName,

              COALESCE(usr.company_branch_site_id,0) as SiteID,
              COALESCE(brnchsite.SiteName,'') as SiteName,

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
              COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as TotalOtherTaxableIncome,
              COALESCE(paytrnemp.TotalNonTaxableIncome,0) as TotalNonTaxableIncome,
              COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalEEInsurancePremiums,
              COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalOtherDeductions,
              COALESCE(paytrnemp.NetPay,0) as NetPay,
              COALESCE(paytrnemp.MinTakeHomePay,0) as MinTakeHomePay
          ");

         $approved->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

           if($FilterType!='' && $FilterType=='Location'){
             $approved->where('paytrnemp.BranchID',$BranchID);
           }else if($FilterType!='' && $FilterType=='Site' && $BranchSiteID>0){
             $pending->where('usr.company_branch_site_id',$BranchSiteID);
           }else if($FilterType!='' && $FilterType=='Division'){
             $approved->where('dept.DivisionID',$DivisionID);
           }else if($FilterType!='' && $FilterType=='Department'){
             $approved->where('usr.department_id',$DepartmentID);
           }else if($FilterType!='' && $FilterType=='Section'){
              $approved->where('sec.ID',$SectionID);
           }else if($FilterType!='' && $FilterType=='Job Type'){
             $approved->where('usr.job_title_id',$JobTypeID);
           }else if($FilterType!='' && $FilterType=='Employee'){
             $approved->where('paytrnemp.EmployeeID',$EmployeeID);
           }
            

    if($SearchText != ''){
        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $approved->whereraw(
                    "CONCAT_WS(' ',
                      COALESCE(usr.shortid,''),
                      COALESCE(usr.last_name,''),
                      COALESCE(usr.first_name,''),
                      COALESCE(usr.middle_name,'')
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

  // EMPLOYEE LOAN DEDUCTION WITH APPROVED/POSTED STATUS GET DATA
  public function getPayrollTransactionApprovedLoanDeduction($param){

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

    $strFields = $this->getLoanFields();

    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'usr.company_branch_site_id')
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
              CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,
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

              ".$strFields."

            'Posted' as Status  

          ");

         $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

       if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
         $query->whereIn('paytrnemp.BranchID',$BranchID);
       }else if($FilterType!='' && $FilterType=='Site' && !empty($BranchSiteID)){
         $query->whereIn('usr.company_branch_site_id',$BranchSiteID);
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

        if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
        }

        $list = $query->get();

        return $list;

    }

  // EMPLOYEE LOAN DEDUCTION WITH PENDING/UN-POSTED STATUS GET DATA
  public function getPayrollTransactionPendingLoanDeduction($param){

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

    $strFields = $this->getLoanFields();

    $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
          ->join('payroll_transaction_income_deduction_temp as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'usr.company_branch_site_id')
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
              CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,
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

              ".$strFields."

              'Un-Posted' as Status

          ");

          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

         if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
           $query->whereIn('paytrnemp.BranchID',$BranchID);
         }else if($FilterType!='' && $FilterType=='Site' && !empty($BranchSiteID)){
           $query->whereIn('usr.company_branch_site_id',$BranchSiteID);
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
 
        $query->orderBy("usr.last_name","ASC");
        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");

        if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
        }

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

// EMPLOYEE OTHER DEDUCTION WITH POSTED/APPROVED STATUS GET DATA
 public function getPayrollTransactionApprovedOtherDeduction($param){

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

    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'usr.company_branch_site_id')
          ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')          
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
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
              CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,
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
              COALESCE(paytrnincded.Deduction97,0) as PGECCShortTermLoan,
              COALESCE(paytrnincded.Deduction149,0) as BusFare,

              'Posted' as Status

          ");

         $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

         if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
           $query->whereIn('paytrnemp.BranchID',$BranchID);
         }else if($FilterType!='' && $FilterType=='Site' && !empty($BranchSiteID)){
           $query->whereIn('usr.company_branch_site_id',$BranchSiteID);
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

        $query->orderBy("usr.last_name","ASC");
        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");

        if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
        }

        $list = $query->get();

        return $list;

    }


// EMPLOYEE OTHER DEDUCTION WITH UN-POSTED/PENDING STATUS GET DATA
 public function getPayrollTransactionPendingOtherDeduction($param){

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

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
          ->leftjoin('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->leftjoin('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')          
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  
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
              CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,
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
              COALESCE(paytrnincded.Deduction97,0) as PGECCShortTermLoan,
              COALESCE(paytrnincded.Deduction149,0) as BusFare,

              'Un-Posted' as Status

          ");

          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

         if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
           $query->whereIn('paytrnemp.BranchID',$BranchID);
         }else if($FilterType!='' && $FilterType=='Site' && !empty($BranchSiteID)){
           $query->whereIn('usr.company_branch_site_id',$BranchSiteID);
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

        $query->orderBy("usr.last_name","ASC");
        $query->orderBy("usr.first_name","ASC");
        $query->orderBy("usr.middle_name","ASC");

        if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
        }

        $list = $query->get();

        return $list;

    }
 

//  OTHER DEDUCTION REPORT OLD FORMAT
public function getEmployeeDeductionDetailsInformationReport($EmployeeDeductionTransaction_RefID){

    $query = DB::table('payroll_transaction_details as paytrndet')
      ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrndet.PayrollTransactionID')
      ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')

        ->selectraw("
           paytrn.TransDateTime as TransDateTime,
          CONCAT('Payroll Period ',FORMAT(prd.StartDate, 'MM/dd/yyyy'),' to ',FORMAT(prd.EndDate, 'MM/dd/yyyy')) as TransactionReference,
          CONCAT('Transaction No. ',paytrn.TransNo) as ReferenceNo,
          paytrndet.Total as Payment
        ");

        $query->whereRaw("paytrn.Status=?",'Approved');
        $query->whereRaw("paytrndet.ReferenceType=?",'Deduction');
        $query->whereRaw("paytrndet.ReferenceID=?",$EmployeeDeductionTransaction_RefID);
        $query->orderBy("paytrn.TransDateTime", "DESC");

            
      $list = $query->get();

    return $list;
}


//EMPLOYEE ADVANCE REPORT
public function getEmployeeAdvanceDetailsInformationReport($EmployeeAdvanceTransaction_RefID){

    $query = DB::table('payroll_transaction_details as paytrndet')
      ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrndet.PayrollTransactionID')
      ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')

        ->selectraw("
           paytrn.TransDateTime as TransDateTime,
          CONCAT('Payroll Period ',FORMAT(prd.StartDate, 'MM/dd/yyyy'),' to ',FORMAT(prd.EndDate, 'MM/dd/yyyy')) as TransactionReference,
          CONCAT('Transaction No. ',paytrn.TransNo) as ReferenceNo,
          paytrndet.Total as Payment
        ");

        $query->whereRaw("paytrn.Status=?",'Approved');
        $query->whereRaw("paytrndet.ReferenceType=?",'Advance');
        $query->whereRaw("paytrndet.ReferenceID=?",$EmployeeAdvanceTransaction_RefID);
        $query->orderBy("paytrn.TransDateTime", "DESC");

            
      $list = $query->get();

    return $list;
}

// EMPLOYEE INCOME DETAILS REPORT
public function getEmployeeIncomeDetailsInformationReport($EmployeeIncomeTransaction_RefID){

    $query = DB::table('payroll_transaction_details as paytrndet')
      ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrndet.PayrollTransactionID')
      ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')

        ->selectraw("
           paytrn.TransDateTime as TransDateTime,
          CONCAT('Payroll Period ',FORMAT(prd.StartDate, 'MM/dd/yyyy'),' to ',FORMAT(prd.EndDate, 'MM/dd/yyyy')) as TransactionReference,
          CONCAT('Transaction No. ',paytrn.TransNo) as ReferenceNo,
          paytrndet.Total as Payment
        ");

        $query->whereRaw("paytrn.Status=?",'Approved');
        $query->whereRaw("paytrndet.ReferenceType=?",'Income');
        $query->whereRaw("paytrndet.ReferenceID=?",$EmployeeIncomeTransaction_RefID);
        $query->orderBy("paytrn.TransDateTime", "DESC");

            
      $list = $query->get();

    return $list;
}


// SSS CONTRIBUTION REPORT  WITH APPROVED/POSTED STATUS
public function getSSSApprovedEmployeeContribution($param){

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
         
    if($Limit > 0){
        $query->limit($Limit);
        $query->offset(($PageNo-1) * $Limit);
      }

    $query->orderByraw("emp.last_name ASC");
    $query->orderByraw("emp.first_name ASC");
    $query->orderByraw("emp.middle_name ASC");

    $list = $query->get();

    return $list;

  }

// SSS CONTRIBUTION REPORT WITH PENDING/UN-POSTED STATUS
public function getSSSPendingEmployeeContribution($param){

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
         
    if($Limit > 0){
        $query->limit($Limit);
        $query->offset(($PageNo-1) * $Limit);
      }

    $query->orderByraw("emp.last_name ASC");
    $query->orderByraw("emp.first_name ASC");
    $query->orderByraw("emp.middle_name ASC");

    $list = $query->get();

    return $list;

  }

// HDMF CONTRIBUTION REPORT WITH POSTED/APPROVED STATUS
public function getHDMFApprovedEmployeeContribution($param){

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

    if($Limit > 0){
        $query->limit($Limit);
        $query->offset(($PageNo-1) * $Limit);
      }

    $query->orderByraw("emp.last_name ASC");
    $query->orderByraw("emp.first_name ASC");
    $query->orderByraw("emp.middle_name ASC");

    $list = $query->get();

    return $list;

  }

// HDMF CONTRIBUTION REPORT WITH UN-POSTED/PENDING STATUS
public function getHDMFPendingEmployeeContribution($param){

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

    if($Limit > 0){
        $query->limit($Limit);
        $query->offset(($PageNo-1) * $Limit);
      }

    $query->orderByraw("emp.last_name ASC");
    $query->orderByraw("emp.first_name ASC");
    $query->orderByraw("emp.middle_name ASC");

    $list = $query->get();

    return $list;

  }

 // PHIC CONTRIBUTION REPORT WITH APPROVED/POSTED  STATUS
public function getPHICApprovedEmployeeContribution($param){

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

    if($Limit > 0){
        $query->limit($Limit);
        $query->offset(($PageNo-1) * $Limit);
      }

    $query->orderByraw("emp.last_name ASC");
    $query->orderByraw("emp.first_name ASC");
    $query->orderByraw("emp.middle_name ASC");

    $list = $query->get();

    return $list;

  }

 // PHIC CONTRIBUTION REPORT WITH PENDING/UN-POSTED  STATUS
public function getPHICPendingEmployeeContribution($param){

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

    if($Limit > 0){
        $query->limit($Limit);
        $query->offset(($PageNo-1) * $Limit);
      }

    $query->orderByraw("emp.last_name ASC");
    $query->orderByraw("emp.first_name ASC");
    $query->orderByraw("emp.middle_name ASC");

    $list = $query->get();

    return $list;

  }

public function getEmployeePaySlipReport($param){

    // $Year = $param['Year'];   
    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $BranchSiteID=$param["BranchSiteID"];
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
              COALESCE(paytrnemp.ID,0) as ID,

              COALESCE(paytrnemp.PayrollTransactionID,0) as PayrollTransactionID,
              COALESCE(paytrn.TransNo,'') as TransNo,
              FORMAT(paytrn.TransDateTime, 'MM/dd/yyyy hh:mm:dd t') as TransDateTime,
              COALESCE(paytrn.Status,'') as Status,
     
              COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
              COALESCE(payperiod.Code,'') as PayrollPeriodCode,
              COALESCE(payperiod.Year,'') as PayrollPeriodYear,
              COALESCE(payperiod.StartDate,'') as PayrollPeriodStartDate,
              COALESCE(payperiod.EndDate,'') as PayrollPeriodEndDate,

              COALESCE(paytrnemp.EmployeeID,0) as EmployeeID,
              COALESCE(usr.shortid,'') as EmployeeNo,
              COALESCE(usr.first_name,'') as FirstName,
              COALESCE(usr.middle_name,'') as MiddleName,
              COALESCE(usr.last_name,'') as LastName,
              CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as EmployeeName,
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
        
              COALESCE(paytrnemp.BasicSalary,0) as BasicPay,

              COALESCE(paytrnincded.ECOLA,0) as ECOLA,
              COALESCE(paytrnemp.Late,0) as LateAmount,
              COALESCE(paytrnemp.Undertime,0) as UndertimeAmount,
              COALESCE(paytrnemp.Absent,0) as AbsentAmount,

              COALESCE(paytrnemp.Leave2,0) as VL,
              COALESCE(paytrnemp.Leave1,0) as SL,

              COALESCE(paytrnemp.Leave,0) - COALESCE(paytrnemp.Leave1,0) - COALESCE(paytrnemp.Leave2,0) as OL,
              COALESCE(paytrnemp.NightDifferential,0) as NightDifferential,

              COALESCE(paytrnemp.OvertimeReg,0) as OTPay,
              COALESCE(paytrnemp.OvertimeND,0) as OTND,

              COALESCE(paytrnemp.TotalOtherTaxableIncome,0) as OtherTaxableEarnings,
              COALESCE(paytrnemp.TotalNonTaxableIncome,0) as OtherNonTaxableEarnings,

              (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0)) as TaxableIncome,

              (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0)) as GrossPay,
      
              COALESCE(paytrnemp.SSSEEContribution,0) as SSS,
              COALESCE(paytrnemp.PHICEEContribution,0) as PHIC,
              COALESCE(paytrnemp.HDMFEEContribution,0) as HDMF,
              COALESCE(paytrnemp.WithholdingTax,0) as WTax,

              COALESCE(paytrnemp.TotalLoanDeductions,0) as LoanDeduction,
              COALESCE(paytrnemp.TotalDeductions,0) as OtherDeduction,
   
              (COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0)) as TotalDeduction,
              COALESCE(paytrnemp.NetPay,0) as NetPay,

         'Posted' as Status

        ");
     
    
     $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

          if($FilterType!='' && $FilterType=='Location' && $BranchID>0){
             $query->where('paytrnemp.BranchID',$BranchID);
           }else if($FilterType!='' && $FilterType=='Site' && $BranchSiteID>0){
             $query->where('usr.company_branch_site_id',$BranchSiteID);
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

     $query->where("paytrn.status",'Approved');              
             
    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("EmployeeName","ASC");  

    $list = $query->get();

    return $list;
  
}


public function getEmployeePaySlipReportCount($param){

    // $Year = $param['Year'];   
    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $BranchSiteID=$param["BranchSiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];
 
    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')          
          ->join('payroll_period_schedule as payperiod ', 'payperiod.ID', '=', 'paytrn.PayrollPeriodID') 
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')          
          ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'usr.job_title_id')
          ->leftjoin('payroll_section as sec', 'sec.id', '=', 'usr.section_id')  

            ->selectraw("
              COALESCE(paytrnemp.ID,0) as ID
          ");
     
    
     $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

          if($FilterType!='' && $FilterType=='Location' && $BranchID>0){
             $query->where('paytrnemp.BranchID',$BranchID);
           }else if($FilterType!='' && $FilterType=='Site' && $BranchSiteID>0){
             $query->where('usr.company_branch_site_id',$BranchSiteID);
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

     $query->where("paytrn.status",'Approved');              
             
    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $list = $query->get();

    return $list;
  
}

public function getPayrollJournalReport($param){
  
    // $Year = $param['Year'];   
    $PeriodYear = $param['PeriodYear'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $FilterType = $param["FilterType"];

    $Status = $param["Status"];

    $BranchID =  $param["BranchID"];
    $SiteID =  $param["SiteID"];
    $DivisionID =  $param["DivisionID"];
    $DepartmentID = $param["DepartmentID"];
    $SectionID = $param["SectionID"];
    $JobTypeID = $param["JobTypeID"];

    $PayrollTransEmp = "";
    $PayrollTransDetails = "";
    $PayrollTransIncomdeDeduction = "";
    $PayrollTransStatus = "";
    if($Status == 'Approved'){
      $PayrollTransEmp = "payroll_transaction_employee";
      $PayrollTransDetails = "payroll_transaction_details";
      $PayrollTransIncomdeDeduction = "payroll_transaction_income_deduction";
      $PayrollTransStatus = "Approved";
    }else{
      $PayrollTransEmp = "payroll_transaction_employee_temp";
      $PayrollTransDetails = "payroll_transaction_details_temp";
      $PayrollTransIncomdeDeduction = "payroll_transaction_income_deduction_temp";
      $PayrollTransStatus = "Pending";
    }

    $query = "";

    if($FilterType == "Location" && !empty($BranchID)){

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
          ->join($PayrollTransIncomdeDeduction.' as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->join('payroll_branch as brnch', 'brnch.ID', '=', 'emp.company_branch_id')
        ->selectraw("

                COALESCE(emp.company_branch_id,0) as ID,
                COALESCE(brnch.BranchName,'') as GroupName,

                paytrnemp.EmployeeID,
                emp.shortid as EmployeeNo,
                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as EmployeeName,
                
                COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
                COALESCE(period.Code,'') as PayrollPeriodCode,
                FORMAT(period.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
                FORMAT(period.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,

                SUM(COALESCE(paytrnemp.BasicSalary,0)) as TotalBasicSalary,

                SUM(COALESCE(paytrnincded.ECOLA,0)) as TotalECOLA,
                SUM(COALESCE(paytrnemp.Late,0)) as TotalLateAmount,
                SUM(COALESCE(paytrnemp.Undertime,0)) as TotalUndertimeAmount,
                SUM(COALESCE(paytrnemp.Absent,0)) as TotalAbsentAmount,

                SUM(COALESCE(paytrnemp.Leave2,0)) as TotalVL,
                SUM(COALESCE(paytrnemp.Leave1,0)) as TotalSL,
                SUM(COALESCE(paytrnemp.Leave,0)) - SUM(COALESCE(paytrnemp.Leave1,0)) - SUM(COALESCE(paytrnemp.Leave2,0)) as TotalOL,

                SUM(COALESCE(paytrnemp.NightDifferential,0)) as TotalND,

                SUM(COALESCE(paytrnemp.OvertimeReg,0)) as TotalOT,
                SUM(COALESCE(paytrnemp.OvertimeND,0)) as TotalOTND,

                SUM(COALESCE(paytrnemp.TotalOtherTaxableIncome,0)) as TotalOtherTaxableEarnings,
                SUM(COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalOtherNonTaxableEarnings,

                (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalGrossPay,

                SUM(COALESCE(paytrnemp.SSSEEContribution,0)) as TotalSSS,
                SUM(COALESCE(paytrnemp.SSSWISPEE,0)) as TotalSSSWISP,
                SUM(COALESCE(paytrnemp.PHICEEContribution,0)) as TotalPHIC,
                SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) as TotalHDMF,
                SUM(COALESCE(paytrnemp.HDMFMP2,0)) as TotalHDMFMP2,

                COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalTaxableIncome,

                SUM(COALESCE(paytrnemp.WithholdingTax,0)) as TotalWTax,
                SUM(COALESCE(paytrnemp.TotalLoanDeductions,0)) as TotalLoanDeduction,
                SUM(COALESCE(paytrnemp.TotalDeductions,0)) as TotalOtherDeduction,
                (COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0)) as TotalDeduction,

                SUM(COALESCE(paytrnemp.NetPay,0)) as TotalNetPay,

                '".($Status == 'Approved' ? 'Posted':'Un-Posted')."' as Status 
              ")
              ->whereIn("emp.company_branch_id", $BranchID)
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('emp.company_branch_id',
                        'brnch.BranchName',
                        'paytrnemp.EmployeeID', 
                        'emp.shortid', 
                        'emp.first_name',
                        'emp.middle_name',
                        'emp.last_name',
                        'paytrn.PayrollPeriodID',
                        'period.Code',
                        'period.StartDate',
                        'period.EndDate',
                        'paytrnemp.BasicSalary',
                        'paytrnemp.NightDifferential',
                        'paytrnemp.Overtime',
                        'paytrnemp.Leave',
                        'paytrnemp.TotalOtherTaxableIncome',
                        'paytrnemp.TotalNonTaxableIncome',
                        'paytrnemp.LateUnderTime',
                        'paytrnemp.TotalEEInsurancePremiums',
                        'paytrnemp.TotalOtherDeductions',
                        'paytrnemp.NetPay',
                        'paytrnemp.PayrollTransactionID'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

            $query->orderBy("brnch.BranchName","ASC");  
            $query->orderBy("paytrnemp.EmployeeID","ASC");  
            $query->orderBy("period.EndDate","ASC"); 

    }else if($FilterType == "Site" && !empty($SiteID)){

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join($PayrollTransIncomdeDeduction.' as paytrnincded', function($join){
            $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
            $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
        })
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'emp.company_branch_site_id')
        ->selectraw("

                COALESCE(emp.company_branch_site_id,0) as ID,
                COALESCE(brnchsite.SiteName,'') as GroupName,

                paytrnemp.EmployeeID,
                emp.shortid as EmployeeNo,
                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as EmployeeName,

                COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
                COALESCE(period.Code,'') as PayrollPeriodCode,
                FORMAT(period.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
                FORMAT(period.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,

                SUM(COALESCE(paytrnemp.BasicSalary,0)) as TotalBasicSalary,

                SUM(COALESCE(paytrnincded.ECOLA,0)) as TotalECOLA,

                SUM(COALESCE(paytrnemp.Late,0)) as TotalLateAmount,
                SUM(COALESCE(paytrnemp.Undertime,0)) as TotalUndertimeAmount,
                SUM(COALESCE(paytrnemp.Absent,0)) as TotalAbsentAmount,

                SUM(COALESCE(paytrnemp.Leave2,0)) as TotalVL,
                SUM(COALESCE(paytrnemp.Leave1,0)) as TotalSL,
                SUM(COALESCE(paytrnemp.Leave,0)) - SUM(COALESCE(paytrnemp.Leave1,0)) - SUM(COALESCE(paytrnemp.Leave2,0)) as TotalOL,

                SUM(COALESCE(paytrnemp.NightDifferential,0)) as TotalND,

                SUM(COALESCE(paytrnemp.OvertimeReg,0)) as TotalOT,
                SUM(COALESCE(paytrnemp.OvertimeND,0)) as TotalOTND,

                SUM(COALESCE(paytrnemp.TotalOtherTaxableIncome,0)) as TotalOtherTaxableEarnings,
                SUM(COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalOtherNonTaxableEarnings,

                (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalGrossPay,

                SUM(COALESCE(paytrnemp.SSSEEContribution,0)) as TotalSSS,
                SUM(COALESCE(paytrnemp.SSSWISPEE,0)) as TotalSSSWISP,
                SUM(COALESCE(paytrnemp.PHICEEContribution,0)) as TotalPHIC,
                SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) as TotalHDMF,
                SUM(COALESCE(paytrnemp.HDMFMP2,0)) as TotalHDMFMP2,

                COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalTaxableIncome,

                SUM(COALESCE(paytrnemp.WithholdingTax,0)) as TotalWTax,
                SUM(COALESCE(paytrnemp.TotalLoanDeductions,0)) as TotalLoanDeduction,
                SUM(COALESCE(paytrnemp.TotalDeductions,0)) as TotalOtherDeduction,

                (COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0)) as TotalDeduction,

                SUM(COALESCE(paytrnemp.NetPay,0)) as TotalNetPay,

                '".($Status == 'Approved' ? 'Posted':'Un-Posted')."' as Status 
              ")
              ->whereIn("emp.company_branch_site_id", $SiteID)
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('emp.company_branch_site_id',
                        'brnchsite.SiteName',
                        'paytrnemp.EmployeeID', 
                        'emp.shortid', 
                        'emp.first_name',
                        'emp.middle_name',
                        'emp.last_name',
                        'paytrn.PayrollPeriodID',
                        'period.Code',
                        'period.StartDate',
                        'period.EndDate',
                        'paytrnemp.BasicSalary',
                        'paytrnemp.NightDifferential',
                        'paytrnemp.Overtime',
                        'paytrnemp.Leave',
                        'paytrnemp.TotalOtherTaxableIncome',
                        'paytrnemp.TotalNonTaxableIncome',
                        'paytrnemp.LateUnderTime',
                        'paytrnemp.TotalEEInsurancePremiums',
                        'paytrnemp.TotalOtherDeductions',
                        'paytrnemp.NetPay',
                        'paytrnemp.PayrollTransactionID'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

            $query->orderBy("brnchsite.SiteName","ASC");  
            $query->orderBy("paytrnemp.EmployeeID","ASC");  
            $query->orderBy("period.EndDate","ASC"); 

    }else if($FilterType == "Division" && !empty($DivisionID)){

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join($PayrollTransIncomdeDeduction.' as paytrnincded', function($join){
            $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
            $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
        })
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
        ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
        ->selectraw("

                COALESCE(dept.DivisionID,0) as ID,
                COALESCE(div.Division,'') as GroupName,

                paytrnemp.EmployeeID,
                emp.shortid as EmployeeNo,
                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as EmployeeName,

                COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
                COALESCE(period.Code,'') as PayrollPeriodCode,
                FORMAT(period.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
                FORMAT(period.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,

                SUM(COALESCE(paytrnemp.BasicSalary,0)) as TotalBasicSalary,

                SUM(COALESCE(paytrnincded.ECOLA,0)) as TotalECOLA,

                SUM(COALESCE(paytrnemp.Late,0)) as TotalLateAmount,
                SUM(COALESCE(paytrnemp.Undertime,0)) as TotalUndertimeAmount,
                SUM(COALESCE(paytrnemp.Absent,0)) as TotalAbsentAmount,

                SUM(COALESCE(paytrnemp.Leave2,0)) as TotalVL,
                SUM(COALESCE(paytrnemp.Leave1,0)) as TotalSL,
                SUM(COALESCE(paytrnemp.Leave,0)) - SUM(COALESCE(paytrnemp.Leave1,0)) - SUM(COALESCE(paytrnemp.Leave2,0)) as TotalOL,

                SUM(COALESCE(paytrnemp.NightDifferential,0)) as TotalND,

                SUM(COALESCE(paytrnemp.OvertimeReg,0)) as TotalOT,
                SUM(COALESCE(paytrnemp.OvertimeND,0)) as TotalOTND,

                SUM(COALESCE(paytrnemp.TotalOtherTaxableIncome,0)) as TotalOtherTaxableEarnings,

                SUM(COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalOtherNonTaxableEarnings,

                (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalGrossPay,

                SUM(COALESCE(paytrnemp.SSSEEContribution,0)) as TotalSSS,
                SUM(COALESCE(paytrnemp.SSSWISPEE,0)) as TotalSSSWISP,
                SUM(COALESCE(paytrnemp.PHICEEContribution,0)) as TotalPHIC,
                SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) as TotalHDMF,
                SUM(COALESCE(paytrnemp.HDMFMP2,0)) as TotalHDMFMP2,

                COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalTaxableIncome,

                SUM(COALESCE(paytrnemp.WithholdingTax,0)) as TotalWTax,
                SUM(COALESCE(paytrnemp.TotalLoanDeductions,0)) as TotalLoanDeduction,
                SUM(COALESCE(paytrnemp.TotalDeductions,0)) as TotalOtherDeduction,

                (COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0)) as TotalDeduction,

                SUM(COALESCE(paytrnemp.NetPay,0)) as TotalNetPay,

                '".($Status == 'Approved' ? 'Posted':'Un-Posted')."' as Status 
              ")
              ->whereIn("dept.DivisionID", $DivisionID)
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('dept.DivisionID',
                        'div.Division',
                        'paytrnemp.EmployeeID', 
                        'emp.shortid', 
                        'emp.first_name',
                        'emp.middle_name',
                        'emp.last_name',
                        'paytrn.PayrollPeriodID',
                        'period.Code',
                        'period.StartDate',
                        'period.EndDate',
                        'paytrnemp.BasicSalary',
                        'paytrnemp.NightDifferential',
                        'paytrnemp.Overtime',
                        'paytrnemp.Leave',
                        'paytrnemp.TotalOtherTaxableIncome',
                        'paytrnemp.TotalNonTaxableIncome',
                        'paytrnemp.LateUnderTime',
                        'paytrnemp.TotalEEInsurancePremiums',
                        'paytrnemp.TotalOtherDeductions',
                        'paytrnemp.NetPay',
                        'paytrnemp.PayrollTransactionID'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

            $query->orderBy("div.Division","ASC");  
            $query->orderBy("paytrnemp.EmployeeID","ASC");  
            $query->orderBy("period.EndDate","ASC"); 

    }else if($FilterType == "Department" && !empty($DepartmentID)){

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join($PayrollTransIncomdeDeduction.' as paytrnincded', function($join){
            $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
            $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
        })
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
        ->selectraw("

                COALESCE(emp.department_id,0) as ID,
                COALESCE(dept.Department,'') as GroupName,

                COALESCE(emp.company_branch_id,0) as BranchID,
                COALESCE(brnch.BranchName,'') as BranchName,

                paytrnemp.EmployeeID,
                emp.shortid as EmployeeNo,
                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')emp) as EmployeeName,
                
                COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
                COALESCE(period.Code,'') as PayrollPeriodCode,
                FORMAT(period.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
                FORMAT(period.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,

                SUM(COALESCE(paytrnemp.BasicSalary,0)) as TotalBasicSalary,

                SUM(COALESCE(paytrnincded.ECOLA,0)) as TotalECOLA,

                SUM(COALESCE(paytrnemp.Late,0)) as TotalLateAmount,
                SUM(COALESCE(paytrnemp.Undertime,0)) as TotalUndertimeAmount,
                SUM(COALESCE(paytrnemp.Absent,0)) as TotalAbsentAmount,

                SUM(COALESCE(paytrnemp.Leave2,0)) as TotalVL,
                SUM(COALESCE(paytrnemp.Leave1,0)) as TotalSL,
                SUM(COALESCE(paytrnemp.Leave,0)) - SUM(COALESCE(paytrnemp.Leave1,0)) - SUM(COALESCE(paytrnemp.Leave2,0)) as TotalOL,

                SUM(COALESCE(paytrnemp.NightDifferential,0)) as TotalND,

                SUM(COALESCE(paytrnemp.OvertimeReg,0)) as TotalOT,
                SUM(COALESCE(paytrnemp.OvertimeND,0)) as TotalOTND,

                SUM(COALESCE(paytrnemp.TotalOtherTaxableIncome,0)) as TotalOtherTaxableEarnings,

                SUM(COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalOtherNonTaxableEarnings,

                (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalGrossPay,

                SUM(COALESCE(paytrnemp.SSSEEContribution,0)) as TotalSSS,
                SUM(COALESCE(paytrnemp.SSSWISPEE,0)) as TotalSSSWISP,
                SUM(COALESCE(paytrnemp.PHICEEContribution,0)) as TotalPHIC,
                SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) as TotalHDMF,
                SUM(COALESCE(paytrnemp.HDMFMP2,0)) as TotalHDMFMP2,

                COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalTaxableIncome,

                SUM(COALESCE(paytrnemp.WithholdingTax,0)) as TotalWTax,
                SUM(COALESCE(paytrnemp.TotalLoanDeductions,0)) as TotalLoanDeduction,
                SUM(COALESCE(paytrnemp.TotalDeductions,0)) as TotalOtherDeduction,

                (COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0)) as TotalDeduction,

                SUM(COALESCE(paytrnemp.NetPay,0)) as TotalNetPay,

                '".($Status == 'Approved' ? 'Posted':'Un-Posted')."' as Status 
              ")
              ->whereIn("emp.department_id", $DepartmentID)
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('emp.department_id',
                        'dept.Department',
                        'paytrnemp.EmployeeID', 
                        'emp.shortid', 
                        'emp.first_name',
                        'emp.middle_name',
                        'emp.last_name',
                        'paytrn.PayrollPeriodID',
                        'period.Code',
                        'period.StartDate',
                        'period.EndDate',
                        'paytrnemp.BasicSalary',
                        'paytrnemp.NightDifferential',
                        'paytrnemp.Overtime',
                        'paytrnemp.Leave',
                        'paytrnemp.TotalOtherTaxableIncome',
                        'paytrnemp.TotalNonTaxableIncome',
                        'paytrnemp.LateUnderTime',
                        'paytrnemp.TotalEEInsurancePremiums',
                        'paytrnemp.TotalOtherDeductions',
                        'paytrnemp.NetPay',
                        'paytrnemp.PayrollTransactionID'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

            $query->orderBy("dept.Department","ASC");  
            $query->orderBy("paytrnemp.EmployeeID","ASC");  
            $query->orderBy("period.EndDate","ASC"); 

    }else if($FilterType == "Section" && !empty($SectionID)){

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join($PayrollTransIncomdeDeduction.' as paytrnincded', function($join){
            $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
            $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
        })
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
        ->leftjoin('payroll_section as sec', 'sec.id', '=', 'emp.section_id')  
        ->selectraw("

                COALESCE(sec.ID,0) as ID,
                COALESCE(sec.Section,'') as GroupName,

                paytrnemp.EmployeeID,
                emp.shortid as EmployeeNo,
                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as EmployeeName,
                
                COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
                COALESCE(period.Code,'') as PayrollPeriodCode,
                FORMAT(period.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
                FORMAT(period.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,

                SUM(COALESCE(paytrnemp.BasicSalary,0)) as TotalBasicSalary,

                SUM(COALESCE(paytrnincded.ECOLA,0)) as TotalECOLA,

                SUM(COALESCE(paytrnemp.Late,0)) as TotalLateAmount,
                SUM(COALESCE(paytrnemp.Undertime,0)) as TotalUndertimeAmount,
                SUM(COALESCE(paytrnemp.Absent,0)) as TotalAbsentAmount,

                SUM(COALESCE(paytrnemp.Leave2,0)) as TotalVL,
                SUM(COALESCE(paytrnemp.Leave1,0)) as TotalSL,
                SUM(COALESCE(paytrnemp.Leave,0)) - SUM(COALESCE(paytrnemp.Leave1,0)) - SUM(COALESCE(paytrnemp.Leave2,0)) as TotalOL,

                SUM(COALESCE(paytrnemp.NightDifferential,0)) as TotalND,

                SUM(COALESCE(paytrnemp.OvertimeReg,0)) as TotalOT,
                SUM(COALESCE(paytrnemp.OvertimeND,0)) as TotalOTND,

                SUM(COALESCE(paytrnemp.TotalOtherTaxableIncome,0)) as TotalOtherTaxableEarnings,

                SUM(COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalOtherNonTaxableEarnings,

                (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalGrossPay,

                SUM(COALESCE(paytrnemp.SSSEEContribution,0)) as TotalSSS,
                SUM(COALESCE(paytrnemp.SSSWISPEE,0)) as TotalSSSWISP,
                SUM(COALESCE(paytrnemp.PHICEEContribution,0)) as TotalPHIC,
                SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) as TotalHDMF,
                SUM(COALESCE(paytrnemp.HDMFMP2,0)) as TotalHDMFMP2,

                COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalTaxableIncome,

                SUM(COALESCE(paytrnemp.WithholdingTax,0)) as TotalWTax,
                SUM(COALESCE(paytrnemp.TotalLoanDeductions,0)) as TotalLoanDeduction,
                SUM(COALESCE(paytrnemp.TotalDeductions,0)) as TotalOtherDeduction,
                (COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0)) as TotalDeduction,

                SUM(COALESCE(paytrnemp.NetPay,0)) as TotalNetPay,

                '".($Status == 'Approved' ? 'Posted':'Un-Posted')."' as Status 
              ")
              ->whereIn("sec.ID", $SectionID)
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('sec.ID',
                        'sec.Section',
                        'paytrnemp.EmployeeID', 
                        'emp.shortid', 
                        'emp.first_name',
                        'emp.middle_name',
                        'emp.last_name',
                        'paytrn.PayrollPeriodID',
                        'period.Code',
                        'period.StartDate',
                        'period.EndDate',
                        'paytrnemp.BasicSalary',
                        'paytrnemp.NightDifferential',
                        'paytrnemp.Overtime',
                        'paytrnemp.Leave',
                        'paytrnemp.TotalOtherTaxableIncome',
                        'paytrnemp.TotalNonTaxableIncome',
                        'paytrnemp.LateUnderTime',
                        'paytrnemp.TotalEEInsurancePremiums',
                        'paytrnemp.TotalOtherDeductions',
                        'paytrnemp.NetPay',
                        'paytrnemp.PayrollTransactionID'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

            $query->orderBy("sec.Section","ASC");  
            $query->orderBy("paytrnemp.EmployeeID","ASC");  
            $query->orderBy("period.EndDate","ASC"); 

    }else if($FilterType == "Job Type" && !empty($JobTypeID)){

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join($PayrollTransIncomdeDeduction.' as paytrnincded', function($join){
            $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
            $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
        })
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->join('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')
        ->selectraw("

                COALESCE(emp.job_title_id,0) as ID,
                COALESCE(job.JobTitle,'') as GroupName,

                paytrnemp.EmployeeID,
                emp.shortid as EmployeeNo,
                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')emp) as EmployeeName,
                
                COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
                COALESCE(period.Code,'') as PayrollPeriodCode,
                FORMAT(period.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
                FORMAT(period.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,

                SUM(COALESCE(paytrnemp.BasicSalary,0)) as TotalBasicSalary,

                SUM(COALESCE(paytrnincded.ECOLA,0)) as TotalECOLA,

                SUM(COALESCE(paytrnemp.Late,0)) as TotalLateAmount,
                SUM(COALESCE(paytrnemp.Undertime,0)) as TotalUndertimeAmount,
                SUM(COALESCE(paytrnemp.Absent,0)) as TotalAbsentAmount,

                SUM(COALESCE(paytrnemp.Leave2,0)) as TotalVL,
                SUM(COALESCE(paytrnemp.Leave1,0)) as TotalSL,
                SUM(COALESCE(paytrnemp.Leave,0)) - SUM(COALESCE(paytrnemp.Leave1,0)) - SUM(COALESCE(paytrnemp.Leave2,0)) as TotalOL,

                SUM(COALESCE(paytrnemp.NightDifferential,0)) as TotalND,

                SUM(COALESCE(paytrnemp.OvertimeReg,0)) as TotalOT,
                SUM(COALESCE(paytrnemp.OvertimeND,0)) as TotalOTND,

                SUM(COALESCE(paytrnemp.TotalOtherTaxableIncome,0)) as TotalOtherTaxableEarnings,

                SUM(COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalOtherNonTaxableEarnings,

                (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalGrossPay,

                SUM(COALESCE(paytrnemp.SSSEEContribution,0)) as TotalSSS,
                SUM(COALESCE(paytrnemp.SSSWISPEE,0)) as TotalSSSWISP,
                SUM(COALESCE(paytrnemp.PHICEEContribution,0)) as TotalPHIC,
                SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) as TotalHDMF,
                SUM(COALESCE(paytrnemp.HDMFMP2,0)) as TotalHDMFMP2,

                COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalTaxableIncome,

                SUM(COALESCE(paytrnemp.WithholdingTax,0)) as TotalWTax,
                SUM(COALESCE(paytrnemp.TotalLoanDeductions,0)) as TotalLoanDeduction,
                SUM(COALESCE(paytrnemp.TotalDeductions,0)) as TotalOtherDeduction,
                (COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0)) as TotalDeduction,

                SUM(COALESCE(paytrnemp.NetPay,0)) as TotalNetPay,

                '".($Status == 'Approved' ? 'Posted':'Un-Posted')."' as Status 
              ")
              ->whereIn("emp.job_title_id", $JobTypeID)
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('emp.job_title_id',
                        'job.JobTitle',
                        'paytrnemp.EmployeeID', 
                        'emp.shortid', 
                        'emp.first_name',
                        'emp.middle_name',
                        'emp.last_name',
                        'paytrn.PayrollPeriodID',
                        'period.Code',
                        'period.StartDate',
                        'period.EndDate',
                        'paytrnemp.BasicSalary',
                        'paytrnemp.NightDifferential',
                        'paytrnemp.Overtime',
                        'paytrnemp.Leave',
                        'paytrnemp.TotalOtherTaxableIncome',
                        'paytrnemp.TotalNonTaxableIncome',
                        'paytrnemp.LateUnderTime',
                        'paytrnemp.TotalEEInsurancePremiums',
                        'paytrnemp.TotalOtherDeductions',
                        'paytrnemp.NetPay',
                        'paytrnemp.PayrollTransactionID'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

            $query->orderBy("job.JobTitle","ASC");  
            $query->orderBy("paytrnemp.EmployeeID","ASC");  
            $query->orderBy("period.EndDate","ASC"); 

    }else{

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join($PayrollTransIncomdeDeduction.' as paytrnincded', function($join){
            $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
            $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
        })
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->selectraw("

                COALESCE(paytrnemp.EmployeeID,0) as ID,
                '".$FilterType."' as GroupName,

                paytrnemp.EmployeeID,
                emp.shortid as EmployeeNo,
                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as EmployeeName,
                
                COALESCE(paytrn.PayrollPeriodID,0) as PayrollPeriodID,
                COALESCE(period.Code,'') as PayrollPeriodCode,
                FORMAT(period.StartDate, 'MM/dd/yyyy') as PayrollPeriodStartDate,
                FORMAT(period.EndDate, 'MM/dd/yyyy') as PayrollPeriodEndDate,

                SUM(COALESCE(paytrnemp.BasicSalary,0)) as TotalBasicSalary,

                SUM(COALESCE(paytrnincded.ECOLA,0)) as TotalECOLA,

                SUM(COALESCE(paytrnemp.Late,0)) as TotalLateAmount,
                SUM(COALESCE(paytrnemp.Undertime,0)) as TotalUndertimeAmount,
                SUM(COALESCE(paytrnemp.Absent,0)) as TotalAbsentAmount,

                SUM(COALESCE(paytrnemp.Leave2,0)) as TotalVL,
                SUM(COALESCE(paytrnemp.Leave1,0)) as TotalSL,
                SUM(COALESCE(paytrnemp.Leave,0)) - SUM(COALESCE(paytrnemp.Leave1,0)) - SUM(COALESCE(paytrnemp.Leave2,0)) as TotalOL,

                SUM(COALESCE(paytrnemp.NightDifferential,0)) as TotalND,

                SUM(COALESCE(paytrnemp.OvertimeReg,0)) as TotalOT,
                SUM(COALESCE(paytrnemp.OvertimeND,0)) as TotalOTND,

                SUM(COALESCE(paytrnemp.TotalOtherTaxableIncome,0)) as TotalOtherTaxableEarnings,

                SUM(COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalOtherNonTaxableEarnings,

                (COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) + COALESCE(paytrnemp.TotalNonTaxableIncome,0)) as TotalGrossPay,

                SUM(COALESCE(paytrnemp.SSSEEContribution,0)) as TotalSSS,
                SUM(COALESCE(paytrnemp.SSSWISPEE,0)) as TotalSSSWISP,
                SUM(COALESCE(paytrnemp.PHICEEContribution,0)) as TotalPHIC,
                SUM(COALESCE(paytrnemp.HDMFEEContribution,0)) as TotalHDMF,
                SUM(COALESCE(paytrnemp.HDMFMP2,0)) as TotalHDMFMP2,

                COALESCE(paytrnemp.BasicSalary,0) + COALESCE(paytrnemp.NightDifferential,0) + COALESCE(paytrnemp.Overtime,0) + COALESCE(paytrnemp.Leave,0) + COALESCE(paytrnemp.TotalOtherTaxableIncome,0) - COALESCE(paytrnemp.LateUnderTime,0) - COALESCE(paytrnemp.TotalEEInsurancePremiums,0) as TotalTaxableIncome,

                SUM(COALESCE(paytrnemp.WithholdingTax,0)) as TotalWTax,
                SUM(COALESCE(paytrnemp.TotalLoanDeductions,0)) as TotalLoanDeduction,
                SUM(COALESCE(paytrnemp.TotalDeductions,0)) as TotalOtherDeduction,
                (COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0)) as TotalDeduction,

                SUM(COALESCE(paytrnemp.NetPay,0)) as TotalNetPay,

                '".($Status == 'Approved' ? 'Posted':'Un-Posted')."' as Status 
              ")
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('paytrnemp.EmployeeID', 
                        'emp.shortid', 
                        'emp.first_name',
                        'emp.middle_name',
                        'emp.last_name',
                        'paytrn.PayrollPeriodID',
                        'period.Code',
                        'period.StartDate',
                        'period.EndDate',
                        'paytrnemp.BasicSalary',
                        'paytrnemp.NightDifferential',
                        'paytrnemp.Overtime',
                        'paytrnemp.Leave',
                        'paytrnemp.TotalOtherTaxableIncome',
                        'paytrnemp.TotalNonTaxableIncome',
                        'paytrnemp.LateUnderTime',
                        'paytrnemp.TotalEEInsurancePremiums',
                        'paytrnemp.TotalOtherDeductions',
                        'paytrnemp.NetPay',
                        'paytrnemp.PayrollTransactionID'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

            $query->orderBy("paytrnemp.EmployeeID","ASC");  
            $query->orderBy("period.EndDate","ASC"); 

    }


    $list = $query->get();

    return $list;

}


public function getPayrollJournalReportCount($param){
  
    // $Year = $param['Year'];   
    $PeriodYear = $param['PeriodYear'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $FilterType = $param["FilterType"];

    $Status = $param["Status"];

    $BranchID =  $param["BranchID"];
    $SiteID =  $param["SiteID"];
    $DivisionID =  $param["DivisionID"];
    $DepartmentID = $param["DepartmentID"];
    $SectionID = $param["SectionID"];
    $JobTypeID = $param["JobTypeID"];

    $PayrollTransEmp = "";
    $PayrollTransDetails = "";
    $PayrollTransIncomdeDeduction = "";
    $PayrollTransStatus = "";
    if($Status == 'Approved'){
      $PayrollTransEmp = "payroll_transaction_employee";
      $PayrollTransDetails = "payroll_transaction_details";
      $PayrollTransIncomdeDeduction = "payroll_transaction_income_deduction";
      $PayrollTransStatus = "Approved";
    }else{
      $PayrollTransEmp = "payroll_transaction_employee_temp";
      $PayrollTransDetails = "payroll_transaction_details_temp";
      $PayrollTransIncomdeDeduction = "payroll_transaction_income_deduction_temp";
      $PayrollTransStatus = "Pending";
    }

    $query = "";

    if($FilterType == "Location" && !empty($BranchID)){

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')  
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->join('payroll_branch as brnch', 'brnch.ID', '=', 'emp.company_branch_id')
        ->selectraw("
                COALESCE(emp.company_branch_id,0) as ID,
                COALESCE(brnch.BranchName,'') as GroupName,
                paytrnemp.EmployeeID,
                SUM(paytrnemp.NetPay) as TotalNetPay
              ")
              ->whereIn("emp.company_branch_id", $BranchID)
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('emp.company_branch_id',
                        'brnch.BranchName',
                        'paytrnemp.EmployeeID', 
                        'paytrnemp.NetPay'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

    }else if($FilterType == "Site" && !empty($SiteID)){

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'emp.company_branch_site_id')
        ->selectraw("
                COALESCE(emp.company_branch_site_id,0) as ID,
                COALESCE(brnchsite.SiteName,'') as GroupName,
                paytrnemp.EmployeeID,
                SUM(paytrnemp.NetPay) as TotalNetPay
              ")
              ->whereIn("emp.company_branch_site_id", $SiteID)
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('emp.company_branch_site_id',
                        'brnchsite.SiteName',
                        'paytrnemp.EmployeeID', 
                        'paytrnemp.NetPay'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

    }else if($FilterType == "Division" && !empty($DivisionID)){

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
        ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
        ->selectraw("
                COALESCE(dept.DivisionID,0) as ID,
                COALESCE(div.Division,'') as GroupName,
                paytrnemp.EmployeeID,
                SUM(paytrnemp.NetPay) as TotalNetPay
              ")
              ->whereIn("dept.DivisionID", $DivisionID)
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('dept.DivisionID',
                        'div.Division',
                        'paytrnemp.EmployeeID',
                        'paytrnemp.NetPay'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

    }else if($FilterType == "Department" && !empty($DepartmentID)){

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
        ->selectraw("
                COALESCE(emp.department_id,0) as ID,
                COALESCE(dept.Department,'') as GroupName,
                paytrnemp.EmployeeID,
                SUM(paytrnemp.NetPay) as TotalNetPay
              ")
              ->whereIn("emp.department_id", $DepartmentID)
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('emp.department_id',
                        'dept.Department',
                        'paytrnemp.EmployeeID',
                        'paytrnemp.NetPay'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

    }else if($FilterType == "Section" && !empty($SectionID)){

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
        ->leftjoin('payroll_section as sec', 'sec.id', '=', 'emp.section_id')  
        ->selectraw("

                COALESCE(sec.ID,0) as ID,
                COALESCE(sec.Section,'') as GroupName,
                paytrnemp.EmployeeID,
                SUM(paytrnemp.NetPay) as TotalNetPay
              ")
              ->whereIn("sec.ID", $SectionID)
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('sec.ID',
                        'sec.Section',
                        'paytrnemp.EmployeeID',
                        'paytrnemp.NetPay'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

    }else if($FilterType == "Job Type" && !empty($JobTypeID)){

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->join('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')
        ->selectraw("
                COALESCE(emp.job_title_id,0) as ID,
                COALESCE(job.JobTitle,'') as GroupName,
                paytrnemp.EmployeeID,
                SUM(paytrnemp.NetPay) as TotalNetPay
              ")
              ->whereIn("emp.job_title_id", [$JobTypeID])
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('emp.job_title_id',
                        'job.JobTitle',
                        'paytrnemp.EmployeeID',
                        'paytrnemp.NetPay'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }

    }else{

      $query = DB::table($PayrollTransEmp.' as paytrnemp')
        ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')  
        ->join('payroll_period_schedule as period', 'period.ID', '=', 'paytrn.PayrollPeriodID')
        ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
        ->selectraw("

                COALESCE(paytrnemp.EmployeeID,0) as ID,
                SUM(paytrnemp.NetPay) as TotalNetPay
              ")
              ->whereraw("period.Year = ?", [$PeriodYear])
              ->groupBy('paytrnemp.EmployeeID',
                        'paytrnemp.NetPay'
              );

            if($Limit > 0){
              $query->limit($Limit);
              $query->offset(($PageNo-1) * $Limit);
            }
    }


    $list = $query->get();

    return $list;

}

// PAYROLL REGISTER REPORT WITH APPROVED/POSTED STATUS
public function getPayrollRegisterApprovedReport($param){

    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

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
              paytrn.ID as PayrollTransactionID,
              paytrnemp.EmployeeID,
              emp.shortid as EmployeeNo,
              CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as EmployeeName,
              COALESCE(paytrnemp.BasicSalary,0) as BasicPay,

              COALESCE(paytrnincded.ECOLA,0) as ECOLA,

              COALESCE(paytrnemp.Late,0) as LateAmount,
              COALESCE(paytrnemp.Undertime,0) as UndertimeAmount,
              COALESCE(paytrnemp.Absent,0) as AbsentAmount,

              COALESCE(paytrnemp.Leave1,0) as SL,
              COALESCE(paytrnemp.Leave2,0) as VL,
              COALESCE(paytrnemp.Leave,0) - COALESCE(paytrnemp.Leave1,0) - COALESCE(paytrnemp.Leave2,0) as OL,

              COALESCE(paytrnemp.NightDifferential,0) as NightDiff,

              COALESCE(paytrnemp.OvertimeReg,0) as OTPay,
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

            COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalDeduction,

            COALESCE(paytrnemp.NetPay,0) as NetPay,

            paytrn.status as Status
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
           

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("EmployeeName","ASC");
    

    $list = $query->get();

    return $list;

}

public function getPayrollRegisterApprovedReportCount($param){

    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $FilterType = $param["FilterType"];
    $BranchID=$param["BranchID"];
    $SiteID=$param["SiteID"];
    $DivisionID=$param["DivisionID"];
    $DepartmentID=$param["DepartmentID"];
    $SectionID=$param["SectionID"];
    $JobTypeID=$param["JobTypeID"];
    $EmployeeID=$param["EmployeeID"];

    $query = DB::table('payroll_transaction_employee as paytrnemp')
      ->join('payroll_transaction as paytrn', 'paytrnemp.PayrollTransactionID', '=', 'paytrn.ID')   
      ->join('users as emp', 'paytrnemp.EmployeeID', '=', 'emp.id')  
      ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
      ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'emp.company_branch_site_id')
      ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
      ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
      ->leftjoin('payroll_section as sec', 'sec.id', '=', 'emp.section_id')  
      ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')
      ->selectraw("
          paytrn.PayrollPeriodID,
          paytrnemp.EmployeeID
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

       if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
       }

       $list = $query->get();

       return $list;

}

// PAYROLL REGISTER REPORT WITH PENDING STATUS
public function getPayrollRegisterPendingReport($param){

    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $PayrollPeriodID = $param['PayrollPeriodID'];
    $Status = $param['Status'];
    $SearchText = $param['SearchText'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

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
              paytrn.ID as PayrollTransactionID,
              paytrnemp.EmployeeID,
              emp.shortid as EmployeeNo,
              CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as EmployeeName,
             COALESCE(paytrnemp.BasicSalary,0) as BasicPay,

             COALESCE(paytrnincded.ECOLA,0) as ECOLA,

              COALESCE(paytrnemp.Late,0) as LateAmount,
              COALESCE(paytrnemp.Undertime,0) as UndertimeAmount,
              COALESCE(paytrnemp.Absent,0) as AbsentAmount,

              COALESCE(paytrnemp.Leave1,0) as SL,
              COALESCE(paytrnemp.Leave2,0) as VL,
              COALESCE(paytrnemp.Leave,0) - COALESCE(paytrnemp.Leave1,0) - COALESCE(paytrnemp.Leave2,0) as OL,

              COALESCE(paytrnemp.NightDifferential,0) as NightDiff,
              COALESCE(paytrnemp.OvertimeReg,0) as OTPay,
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

            COALESCE(paytrnemp.TotalEEInsurancePremiums,0) + COALESCE(paytrnemp.TotalOtherDeductions,0) as TotalDeduction,

            COALESCE(paytrnemp.NetPay,0) as NetPay,

            paytrn.status as Status
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
           

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("EmployeeName","ASC");
    

    $list = $query->get();

    return $list;

}

// PAYROLL REGISTER RAW DATA WITH POSTED/APPROVED STATUS GET DATA REPORTS
public function getPayrollApprovedRawDataReport($param){

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

    $query = DB::table('payroll_transaction_employee as paytrnemp')
      ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')   
      ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')   
      ->join('users as emp', 'emp.ID', '=', 'paytrnemp.EmployeeID')   
      ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')   
      ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'emp.company_branch_site_id')
      ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')   
      ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrnemp.DivisionID')   
      ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrnemp.SectionID')   
      ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')   
       
      ->selectraw("
               paytrnemp.EmployeeID,
               emp.shortid as EmpNo,
               CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as EmployeeName,

              COALESCE(dept.Department,'') as Department,
              COALESCE(div.Division,'') as Division,
              COALESCE(brn.BranchName,'') as Location,

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

      COALESCE(paytrnemp.NetPay,0) as NetPay,

      'Posted' as Status

    ");


         $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

          if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
             $query->whereIn('paytrnemp.BranchID',$BranchID);
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
             $query->where('paytrnemp.EmployeeID',$EmployeeID);
           }

       $query->where("paytrn.status",'Approved');  
           

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("EmployeeName","ASC");
    

    $list = $query->get();

    return $list;

}

public function getPayrollApprovedRawDataReportCount($param){

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

    $query = DB::table('payroll_transaction_employee as paytrnemp')
      ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')   
      ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')   
      ->join('users as emp', 'emp.ID', '=', 'paytrnemp.EmployeeID')   
      ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')   
      ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'emp.company_branch_site_id')
      ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')   
      ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrnemp.DivisionID')   
      ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrnemp.SectionID')   
      ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')   
      ->selectraw("
           paytrnemp.EmployeeID
      ");

     $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

      if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
         $query->whereIn('paytrnemp.BranchID',$BranchID);
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
         $query->where('paytrnemp.EmployeeID',$EmployeeID);
       }

       $query->where("paytrn.status",'Approved');  
           

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $list = $query->get();

    return $list;

}

// PAYROLL REGISTER RAW DATA REPORT WITH UN-POST/PENDING STATUS GET DATA REPORTS
public function getPayrollPendingRawDataReport($param){

    
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


    $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
      ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')   
      ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')   
      ->join('users as emp', 'emp.ID', '=', 'paytrnemp.EmployeeID')   
      ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')   
       ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'emp.company_branch_site_id')
      ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')   
      ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrnemp.DivisionID')   
      ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrnemp.SectionID')   
      ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')   
       
      ->selectraw("
               paytrnemp.EmployeeID,
               emp.shortid as EmpNo,

                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as EmployeeName,

              COALESCE(dept.Department,'') as Department,
              COALESCE(div.Division,'') as Division,
              COALESCE(brn.BranchName,'') as Location,

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

      COALESCE(paytrnemp.NetPay,0) as NetPay,

      'Un-Posted' as Status

      ");


      
         $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

      if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
         $query->whereIn('paytrnemp.BranchID',$BranchID);
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
         $query->where('paytrnemp.EmployeeID',$EmployeeID);
       }

       $query->where("paytrn.status",'Pending');  
           
    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("EmployeeName","ASC");
    

    $list = $query->get();

    return $list;

}

public function getPayrollPendingRawDataReportCount($param){

    
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


    $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
      ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')   
      ->join('payroll_period_schedule as prd', 'prd.ID', '=', 'paytrn.PayrollPeriodID')   
      ->join('users as emp', 'emp.ID', '=', 'paytrnemp.EmployeeID')   
      ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')   
       ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'emp.company_branch_site_id')
      ->leftjoin('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')   
      ->leftjoin('payroll_division as div', 'div.ID', '=', 'paytrnemp.DivisionID')   
      ->leftjoin('payroll_section as sec', 'sec.ID', '=', 'paytrnemp.SectionID')   
      ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')   
       
      ->selectraw("
               paytrnemp.EmployeeID
      ");
      
     $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

      if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
         $query->whereIn('paytrnemp.BranchID',$BranchID);
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
         $query->where('paytrnemp.EmployeeID',$EmployeeID);
       }

       $query->where("paytrn.status",'Pending');  
           

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $list = $query->get();

    return $list;

}

// EMPLOYEE DTR REPORT
public function getEmployeeDTRReport($param){

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

      if($Limit > 0){
        $query->limit($Limit);
        $query->offset(($PageNo-1) * $Limit);
      }

      $query->orderBy("peds.Status","ASC");
      $query->orderBy("FullName","ASC");    
      
      $list = $query->get();

      return $list;

}

public function getEmployeeDTRReportCount($param){

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
            peds.ID
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

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }
    
    $list = $query->get();

    return $list;

}

//OTHER EARING TAXABLE WITH POSTED/POSTED STATUS GET DATA LIST
 public function getPayrollTransactionApprovedOtherEarningTaxable($param){

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

    $strFields = $this->getOEFields("Taxable Income");

    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'usr.company_branch_site_id')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
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
              CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,
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
              ".$strFields."
              0 as AdjustmentPrevPayroll,                
              'Posted' as Status
          ");

         $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

         if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
           $query->whereIn('paytrnemp.BranchID',$BranchID);
         }else if($FilterType!='' && $FilterType=='Site' && !empty($BranchSiteID)){
           $query->whereIn('usr.company_branch_site_id',$BranchSiteID);
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

        if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
        }

        $list = $query->get();

        return $list;

    }
 
 //OTHER EARING TAXABLE WITH UN-POSTED/PENDING STATUS GET DATA LIST
 public function getPayrollTransactionPendingOtherEarningTaxable($param){
  
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

    $strFields = $this->getOEFields("Taxable Income");

    $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
          ->join('payroll_transaction_income_deduction_temp as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'usr.company_branch_site_id')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
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
              CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,
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
              ".$strFields."
              0 as AdjustmentPrevPayroll,                
              'Un-Posted' as Status

          ");

            $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

         if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
           $query->whereIn('paytrnemp.BranchID',$BranchID);
         }else if($FilterType!='' && $FilterType=='Site' && !empty($BranchSiteID)){
           $query->whereIn('usr.company_branch_site_id',$BranchSiteID);
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

        if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
        }

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
 
  //OTHER EARING NON TAXABLE WITH POSTED/APPROVED STATUS GET DATA
  public function getPayrollTransactionOtherApprovedEarningNonTaxable($param){

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

    $strFields = $this->getOEFields("Non-Taxable Income");

    $query = DB::table('payroll_transaction_employee as paytrnemp')
          ->join('payroll_transaction_income_deduction as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'usr.company_branch_site_id')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
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
              CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,
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

              ".$strFields."

              'Posted' as Status

          ");

          $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

          if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
              $query->whereIn("paytrnemp.BranchID",$BranchID);
           }else if($FilterType!='' && $FilterType=='Site' && !empty($BranchSiteID)){
              $query->whereIn('usr.company_branch_site_id',$BranchSiteID);
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

        if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
        }

        $list = $query->get();

        return $list;

    }

    //OTHER EARING NON TAXABLE WITH UN-POSTED/PENDING STATUS GET DATA
  public function getPayrollTransactionOtherPendingEarningNonTaxable($param){

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

    $strFields = $this->getOEFields("Non-Taxable Income");

    $query = DB::table('payroll_transaction_employee_temp as paytrnemp')
          ->join('payroll_transaction_income_deduction_temp as paytrnincded', function($join){
              $join->on('paytrnincded.PayrollTransactionID', '=', 'paytrnemp.PayrollTransactionID');
              $join->on('paytrnincded.EmployeeID','=', 'paytrnemp.EmployeeID');
          })
          ->join('payroll_transaction as paytrn', 'paytrn.ID', '=', 'paytrnemp.PayrollTransactionID')
          ->join('users as usr', 'usr.id', '=', 'paytrnemp.EmployeeID')
          ->join('payroll_branch as brn', 'brn.ID', '=', 'paytrnemp.BranchID')
          ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'usr.company_branch_site_id')
          ->join('payroll_department as dept', 'dept.ID', '=', 'usr.department_id')
          ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
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
              CONCAT(COALESCE(usr.last_name,''), ', ', COALESCE(usr.first_name,''), ' ' , COALESCE(usr.middle_name,'')) as FullName,
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

              ".$strFields."

              'Un-Posted' as Status

          ");

           $query->where('paytrn.PayrollPeriodID',$PayrollPeriodID);

          if($FilterType!='' && $FilterType=='Location' && !empty($BranchID)){
             $query->whereIn('paytrnemp.BranchID',$BranchID);
           }else if($FilterType!='' && $FilterType=='Site' && !empty($BranchSiteID)){
              $query->whereIn('usr.company_branch_site_id',$BranchSiteID);
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

        if($Limit > 0){
          $query->limit($Limit);
          $query->offset(($PageNo-1) * $Limit);
        }

        $list = $query->get();

        return $list;

    }

}