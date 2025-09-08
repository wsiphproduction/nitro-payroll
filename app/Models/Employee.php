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

class Employee extends Model
{

public function getEmployeeList($param){

  $SearchText = trim($param['SearchText']);
  $Limit = $param['Limit'];
  $PageNo = $param['PageNo'];
  $Status = $param['Status'];

   $query = DB::table('users as emp')
    ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
    ->join('payroll_branch as brnch', 'brnch.ID', '=', 'emp.company_branch_id')
    ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'emp.company_branch_site_id')
    ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
    ->leftjoin('payroll_section as sec', 'sec.id', '=', 'emp.section_id')  
    ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')

    ->selectraw("
        emp.id as employee_id,
        COALESCE(emp.employee_number,'') as employee_number,
        COALESCE(emp.first_name,'') as first_name,
        COALESCE(emp.last_name,'') as last_name,
        COALESCE(emp.middle_name,'') as middle_name,
        CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as FullName,

        COALESCE(job.ID,0) as PostitionID,
        COALESCE(job.JobTitle,'') as Position,

        COALESCE(dept.DivisionID,0) as DivisionID,
        COALESCE(div.Division,'') as Division,

        COALESCE(emp.department_id,0) as DepartmentID,
        COALESCE(dept.Department,'') as Department,

        COALESCE(brnch.ID,0) as BranchID,
        COALESCE(brnch.BranchName,'') as BranchName,

        COALESCE(emp.company_branch_site_id,0) as SiteID,
        COALESCE(brnchsite.SiteName,'') as SiteName,

         ISNULL((SELECT TOP 1 FORMAT(EffectivityDate, 'MM/dd/yyyy') 
                FROM payroll_employee_rates 
                WHERE EmployeeID = emp.id
                ORDER BY EffectivityDate DESC),'') as EffectivityDateFormat,     

        ISNULL((SELECT TOP 1 MonthlyRate 
                        FROM payroll_employee_rates 
                        WHERE EmployeeID = emp.id
                        ORDER BY EffectivityDate DESC),0) as MonthlyRate,

        ISNULL((SELECT TOP 1 ID 
                        FROM payroll_employee_rates 
                        WHERE EmployeeID = emp.id
                        ORDER BY EffectivityDate DESC),0) as EmployeeRateID,                
                        
        ISNULL((SELECT TOP 1 DailyRate 
                        FROM payroll_employee_rates 
                        WHERE EmployeeID = emp.id
                        ORDER BY EffectivityDate DESC),0) as DailyRate, 

        ISNULL((SELECT TOP 1 HourlyRate 
                        FROM payroll_employee_rates 
                        WHERE EmployeeID = emp.id
                        ORDER BY EffectivityDate DESC),0) as HourlyRate, 

        COALESCE(sec.ID,0) as SectionID,
        COALESCE(sec.Section,'') as Section,   

        COALESCE(emp.sss_number,'') as sss_number,
        COALESCE(emp.pagibig_number,'') as pagibig_number,
        COALESCE(emp.tin_number,'') as tin_number,
        COALESCE(emp.philhealth_number,'') as philhealth_number,

        COALESCE(emp.salary_type,0) as salary_type,
        COALESCE(emp.contact_number,'') as MobileNo,
        COALESCE(emp.email,'') as EmailAddress,
        COALESCE(emp.status,'') as Status,

         CASE emp.status WHEN 1 THEN 'Active' ELSE 'Inactive' END as emp_status,  
         CASE emp.salary_type WHEN 1 THEN 'Daily' ELSE 'Monthly' END as emp_salary_type  
    ");

    if($Status!=''){
        if($Status=='Active'){
           $query->where("emp.status",1);    
        }
        if($Status=='Inactive'){
           $query->where("emp.status",2);    
        }
   
         if($Status=='Daily'){
             $query->where("emp.salary_type",1);  
         }

         if($Status=='Monthly'){
             $query->where("emp.salary_type",2);  
         }
    }

   if(!empty($Status)){
      $arFilter = explode("|",$Status);
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
                        COALESCE(emp.employee_number,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,''),
                        COALESCE(emp.middle_name,''),
                        CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')),
                        COALESCE(emp.email,''),
                        COALESCE(div.Division,''),                        
                        COALESCE(dept.Department,''),
                        COALESCE(sec.Section,''),
                        COALESCE(job.JobTitle,''),
                        COALESCE(brnch.BranchName,''),
                        COALESCE(emp.sss_number,''),
                        COALESCE(emp.pagibig_number,''),
                        COALESCE(emp.tin_number,''),
                        COALESCE(emp.philhealth_number,''),
                        (CASE emp.status WHEN 1 THEN 'Active' ELSE 'Inactive' END),
                        (CASE emp.salary_type WHEN 1 THEN 'Daily' ELSE 'Monthly' END), 
                        COALESCE(emp.status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("FullName","ASC");
    $list = $query->get();

    return $list;

}

public function getEmployeeInfo($OptionSearch,$OptionValue){

  $info = DB::table('users as emp')
    ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
    ->join('payroll_branch as brnch', 'brnch.ID', '=', 'emp.company_branch_id')
    ->join('payroll_branch_site as brnchsite', 'brnchsite.ID', '=', 'emp.company_branch_site_id')
    ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
    ->leftjoin('payroll_section as sec', 'sec.id', '=', 'emp.section_id')  
    ->leftjoin('payroll_job_type as job', 'job.ID', '=', 'emp.job_title_id')
    ->leftjoin('payroll_employee_mp2_setup as mp2', 'mp2.EmployeeID', '=', 'emp.id')
   
      ->selectraw("
        emp.id as employee_id,
        COALESCE(emp.employee_number,'') as employee_number,
        COALESCE(emp.first_name,'') as first_name,
        COALESCE(emp.last_name,'') as last_name,
        COALESCE(emp.middle_name,'') as middle_name,
        CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

        COALESCE(job.ID,0) as PostitionID,
        COALESCE(job.JobTitle,'') as Position,

        COALESCE(dept.DivisionID,0) as DivisionID,
        COALESCE(div.Division,'') as Division,

        COALESCE(emp.department_id,0) as DepartmentID,
        COALESCE(dept.Department,'') as Department,

        COALESCE(sec.ID,0) as SectionID,
        COALESCE(sec.Section,'') as Section, 

        COALESCE(brnch.ID,0) as BranchID,
        COALESCE(brnch.BranchName,'') as BranchName,

        COALESCE(emp.company_branch_site_id,0) as SiteID,
        COALESCE(brnchsite.SiteName,'') as SiteName,

         ISNULL((SELECT TOP 1 FORMAT(EffectivityDate, 'MM/dd/yyyy') 
                FROM payroll_employee_rates 
                WHERE EmployeeID = emp.id
                ORDER BY EffectivityDate DESC),'') as EffectivityDateFormat,     

        ISNULL((SELECT TOP 1 MonthlyRate 
                        FROM payroll_employee_rates 
                        WHERE EmployeeID = emp.id
                        ORDER BY EffectivityDate DESC),0) as MonthlyRate,

        ISNULL((SELECT TOP 1 ID 
                        FROM payroll_employee_rates 
                        WHERE EmployeeID = emp.id
                        ORDER BY EffectivityDate DESC),0) as EmployeeRateID,                
                        
        ISNULL((SELECT TOP 1 DailyRate 
                        FROM payroll_employee_rates 
                        WHERE EmployeeID = emp.id
                        ORDER BY EffectivityDate DESC),0) as DailyRate, 

        ISNULL((SELECT TOP 1 HourlyRate 
                        FROM payroll_employee_rates 
                        WHERE EmployeeID = emp.id
                        ORDER BY EffectivityDate DESC),0) as HourlyRate, 

        COALESCE(emp.sss_number,'') as sss_number,
        COALESCE(emp.pagibig_number,'') as pagibig_number,
        COALESCE(emp.tin_number,'') as tin_number,
        COALESCE(emp.philhealth_number,'') as philhealth_number,

        COALESCE(emp.contact_number,'') as MobileNo,
        COALESCE(emp.email,'') as EmailAddress,
        COALESCE(emp.salary_type,'') as salary_type,
        COALESCE(emp.status,'') as Status,

        COALESCE(emp.hdmf_er,0) as HDMFER,
        COALESCE(emp.hdmf_ee,0) as HDMFEE,

        COALESCE(mp2.MP2No,'') as MP2No,
        COALESCE(mp2.MP2Amount,0) as MP2Amount,
        COALESCE(mp2.FrequencyID,'') as MP2FrequencyID,

         CASE emp.salary_type WHEN 1 THEN 'Daily' ELSE 'Monthly' END as emp_salary_type  
        ");

     if($OptionSearch=='ByEmployeeID'){
         $info->where("emp.id",$OptionValue);
     }

     if($OptionSearch=='ByEmployeeNo'){
         $info->where("emp.employee_number",$OptionValue);
     }
        
    $record=$info->first();

    return $record;
  }

    public function postEmployeeInfo($data){

        $Misc = new Misc();

        $TODAY = date("Y-m-d H:i:s");

        $id = $data['id'];
        $parent_id = $data['parent_id'];

        $shortid = $data['shortid'];
        $first_name = $data['first_name'];
        $middle_name = $data['middle_name'];
        $last_name = $data['last_name'];
        $suffix = $data['suffix'];
        $nick_name = $data['nick_name'];

        $gender = $data['gender'];
        $birthdate = $data['birthdate'];
        $avatar = $data['avatar'];

        $present_address = $data['present_address'];
        $permanent_address = $data['permanent_address'];

        $nationality = $data['nationality'];
        $marital_status = $data['marital_status'];

        $contact_number = $data['contact_number'];
        $email = $data['email'];

        $username = $data['username'];
        $password = $data['password'];

        $status = $data['status'];

        $user_role = $data['user_role'];
        $note = $data['note'];

        $sss_number = $data['sss_number'];
        $pagibig_number = $data['pagibig_number'];
        $tin_number = $data['tin_number'];
        $philhealth_number = $data['philhealth_number'];
        $employee_number = $data['employee_number'];

        $company_branch_id = $data['company_branch_id'];
        $department_id = $data['department_id'];
        $job_title_id = $data['job_title_id'];
        $team_id = $data['team_id'];

        $date_entry = $data['date_entry'];
        $resignation_date = $data['resignation_date'];
        $reset_token = $data['reset_token'];

        $created_by = $data['created_by'];
        $updated_by = $data['updated_by'];

        $created_at = $data['created_at'];
        $updated_at = $data['updated_at'];

        $employee_type = $data['employee_type'];
        $street = $data['street'];
        $barangay = $data['barangay'];
        $city = $data['city'];
        $province = $data['province'];
        $region = $data['region'];

        $approver_id = $data['approver_id'];
        $secretary_id = $data['secretary_id'];

        $old_employee_number = $data['old_employee_number'];

        $alternate_secretary = $data['alternate_secretary'];
        $hr_generalist_id = $data['hr_generalist_id'];
        $secondary_approver_id = $data['secondary_approver_id'];
        $salary_type = $data['salary_type'];

        //Check if record exist
        $info = DB::table('users')
            ->selectraw("
                employee_number
            ")
            ->where('employee_number',$shortid)
            ->first();        

        if(isset($info)){
            DB::table('users')
                ->where('shortid',$shortid)
                ->update([
                    'parent_id' => $parent_id,

                    'first_name' => $first_name,
                    'middle_name' => $middle_name,
                    'last_name' => $last_name,
                    'suffix' => $suffix,
                    'nick_name' => $nick_name,

                    'gender' => $gender,
                    'birthdate' => $birthdate,
                    'avatar' => $avatar,

                    'present_address' => $present_address,
                    'permanent_address' => $permanent_address,

                    'nationality' => $nationality,
                    'marital_status' => $marital_status,

                    'contact_number' => $contact_number,
                    'email' => $email,

                    'username' => $username,
                    'password' => $password,

                    'status' => $status,

                    'user_role' => $user_role,
                    'note' => $note,

                    'sss_number' => $sss_number,
                    'pagibig_number' => $pagibig_number,
                    'tin_number' => $tin_number,
                    'philhealth_number' => $philhealth_number,
                    'employee_number' => $employee_number,

                    'company_branch_id' => $company_branch_id,
                    'department_id' => $department_id,
                    'job_title_id' => $job_title_id,
                    'team_id' => $team_id,

                    'date_entry' => $date_entry,
                    'resignation_date' => $resignation_date,
                    'reset_token' => $reset_token,

                    'created_by' => $created_by,
                    'updated_by' => $updated_by,

                    'created_at' => $created_at,
                    'updated_at' => $updated_at,

                    'employee_type' => $employee_type,
                    'street' => $street,
                    'barangay' => $barangay,
                    'city' => $city,
                    'province' => $province,
                    'region' => $region,

                    'approver_id' => $approver_id,
                    'secretary_id' => $secretary_id,

                    'old_employee_number' => $old_employee_number,

                    'alternate_secretary' => $alternate_secretary,
                    'hr_generalist_id' => $hr_generalist_id,
                    'secondary_approver_id' => $secondary_approver_id,
                    
                    'salary_type' => $salary_type

                ]);

            //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $id;
            $logData['TransactedByID'] = 0;
            $logData['ModuleType'] = "Employee";
            $logData['TransType'] = "Post Update Employee Information";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);

        }else{
            $RecordID = DB::table('users')
                ->insertGetId([
                    'parent_id' => $parent_id,

                    'shortid' => $shortid,

                    'first_name' => $first_name,
                    'middle_name' => $middle_name,
                    'last_name' => $last_name,
                    'suffix' => $suffix,
                    'nick_name' => $nick_name,

                    'gender' => $gender,
                    'birthdate' => $birthdate,
                    'avatar' => $avatar,

                    'present_address' => $present_address,
                    'permanent_address' => $permanent_address,

                    'nationality' => $nationality,
                    'marital_status' => $marital_status,

                    'contact_number' => $contact_number,
                    'email' => $email,

                    'username' => $username,
                    'password' => $password,

                    'status' => $status,

                    'user_role' => $user_role,
                    'note' => $note,

                    'sss_number' => $sss_number,
                    'pagibig_number' => $pagibig_number,
                    'tin_number' => $tin_number,
                    'philhealth_number' => $philhealth_number,
                    'employee_number' => $employee_number,

                    'company_branch_id' => $company_branch_id,
                    'department_id' => $department_id,
                    'job_title_id' => $job_title_id,
                    'team_id' => $team_id,

                    'date_entry' => $date_entry,
                    'resignation_date' => $resignation_date,
                    'reset_token' => $reset_token,

                    'created_by' => $created_by,
                    'updated_by' => $updated_by,

                    'created_at' => $created_at,
                    'updated_at' => $updated_at,

                    'employee_type' => $employee_type,
                    'street' => $street,
                    'barangay' => $barangay,
                    'city' => $city,
                    'province' => $province,
                    'region' => $region,

                    'approver_id' => $approver_id,
                    'secretary_id' => $secretary_id,

                    'old_employee_number' => $old_employee_number,

                    'alternate_secretary' => $alternate_secretary,
                    'hr_generalist_id' => $hr_generalist_id,
                    'secondary_approver_id' => $secondary_approver_id,
                    
                    'salary_type' => $salary_type
                  ]);  

            //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $id;
            $logData['TransactedByID'] = 0;
            $logData['ModuleType'] = "Employee";
            $logData['TransType'] = "Post Employee Information";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);
        }

        return "Success";
    }
    
   // SET NEW HDMF CONTRIBUTION 
   public function doUpdateEmployeeNewHMDFContribution($data){
    
    $Misc = new Misc();
    $TODAY = date("Y-m-d H:i:s");
    $EmployeeID = $data['EmployeeID'];

    $HDMF_New_EE = $Misc->setNumeric($data["HDMF_New_EE"]);      
    $HDMF_New_ER = $Misc->setNumeric($data["HDMF_New_ER"]);

    if($EmployeeID > 0){

        DB::table('users')
            ->where('id',$EmployeeID)
            ->update([
                'hdmf_ee' => $HDMF_New_EE,
                'hdmf_er' => $HDMF_New_ER,                
                'updated_by' =>  Session::get('ADMIN_USER_ID'),
                'updated_at' =>$TODAY                
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $EmployeeID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee Information";
        $logData['TransType'] = "Update New HMDF Contribution";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }

     return "Success";

  }
  

}

