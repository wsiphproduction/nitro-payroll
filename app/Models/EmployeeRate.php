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

class EmployeeRate extends Model
{

public function getEmployeeRateList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_employee_rates as emprt')
      ->join('users as emp', 'emp.id', '=', 'emprt.EmployeeID')
      ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
      ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
      ->leftjoin('payroll_section as sec', 'sec.id', '=', 'emp.section_id')    

       ->selectraw("
            COALESCE(emprt.ID,0) as EmployeeRateID,   

            COALESCE(emp.id,0) as employee_id,       
            COALESCE(emp.employee_number,'') as employee_number,
            COALESCE(emp.first_name,'') as first_name,
            COALESCE(emp.last_name,'') as last_name,
            COALESCE(emp.middle_name,'') as middle_name,
            CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

            COALESCE(emprt.MonthlyRate,0) as MonthlyRate,
            COALESCE(emprt.DailyRate,0) as DailyRate,
            COALESCE(emprt.HourlyRate,0) as HourlyRate,
            COALESCE(emprt.EffectivityDate,'') as EffectivityDate,
            FORMAT(emprt.EffectivityDate,'MM/dd/yyyy') as EffectivityDateFormat,            
            COALESCE(emprt.Remarks,'') as Remarks,
            
            COALESCE(dept.DivisionID,0) as DivisionID,
            COALESCE(div.Division,'') as Division,

            COALESCE(emp.department_id,0) as DepartmentID,
            COALESCE(dept.Department,'') as Department,

            COALESCE(sec.ID,0) as SectionID,
            COALESCE(sec.Section,'') as Section,   

            COALESCE(emp.sss_number,'') as sss_number,
            COALESCE(emp.pagibig_number,'') as pagibig_number,
            COALESCE(emp.tin_number,'') as tin_number,
            COALESCE(emp.philhealth_number,'') as philhealth_number,

            COALESCE(emp.salary_type,'') as salary_type,
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
        
        if($Status=='InActive'){
           $query->where("emp.status",2);    
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
                        COALESCE(emp.email,''),
                        COALESCE(div.Division,''),                        
                        COALESCE(dept.Department,''),
                        COALESCE(sec.Section,''),
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

    $query->orderBy("emp.first_name","ASC");
    $list = $query->get();

    return $list;

}

public function getEmployeeRateListByEmployeeID($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];
    $EmployeeID = $param['EmployeeID'];

    $query = DB::table('payroll_employee_rates as emprt')
     ->join('users as emp', 'emp.id', '=', 'emprt.EmployeeID')
     ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
     ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
     ->leftjoin('payroll_section as sec', 'sec.id', '=', 'emp.section_id')      
      ->selectraw("
        COALESCE(emprt.ID,0) as EmployeeRateID, 

        COALESCE(emp.id,0) as employee_id, 
        COALESCE(emp.employee_number,'') as employee_number,
        COALESCE(emp.first_name,'') as first_name,
        COALESCE(emp.last_name,'') as last_name,
        COALESCE(emp.middle_name,'') as middle_name,
        CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

        COALESCE(emprt.MonthlyRate,0) as MonthlyRate,
        COALESCE(emprt.DailyRate,0) as DailyRate,
        COALESCE(emprt.HourlyRate,0) as HourlyRate,

        COALESCE(emprt.EffectivityDate,'') as EffectivityDate,
        FORMAT(emprt.EffectivityDate,'MM/dd/yyyy') as EffectivityDateFormat,
        
        COALESCE(emprt.Remarks,'') as Remarks,
        
        COALESCE(dept.DivisionID,0) as DivisionID,
        COALESCE(div.Division,'') as Division,

        COALESCE(emp.department_id,0) as DepartmentID,
        COALESCE(dept.Department,'') as Department,

        COALESCE(sec.ID,0) as SectionID,
        COALESCE(sec.Section,'') as Section,   

        COALESCE(emp.sss_number,'') as sss_number,
        COALESCE(emp.pagibig_number,'') as pagibig_number,
        COALESCE(emp.tin_number,'') as tin_number,
        COALESCE(emp.philhealth_number,'') as philhealth_number,

        COALESCE(emp.salary_type,'') as salary_type,
        COALESCE(emp.contact_number,'') as MobileNo,
        COALESCE(emp.email,'') as EmailAddress,
        COALESCE(emp.status,'') as Status 
    ");

    $query->where("emp.id",$EmployeeID); 

        if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(emprt.MonthlyRate,0),
                        COALESCE(emprt.DailyRate,0),
                        COALESCE(emprt.HourlyRate,0),                        
                        COALESCE(emprt.Remarks,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }
   
    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("emp.first_name","ASC");
    $list = $query->get();

    return $list;

}

public function getEmployeeRateInfo($OptionSearch,$OptionValue){

    $info = DB::table('payroll_employee_rates as emprt')
     ->leftjoin('users as emp', 'emp.id', '=', 'emprt.EmployeeID')  
      ->selectraw("
        emprt.ID as EmployeeRateID,  

        COALESCE(emp.id,0) as employee_id, 
        COALESCE(emp.employee_number,'') as employee_number,
        COALESCE(emp.first_name,'') as first_name,
        COALESCE(emp.last_name,'') as last_name,
        COALESCE(emp.middle_name,'') as middle_name,
        CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

        COALESCE(emprt.MonthlyRate,0) as MonthlyRate,
        COALESCE(emprt.DailyRate,0) as DailyRate,
        COALESCE(emprt.HourlyRate,0) as HourlyRate,
        COALESCE(emprt.EffectivityDate,'') as EffectivityDate,
        FORMAT(emprt.EffectivityDate,'MM/dd/yyyy') as EffectivityDateFormat,        
        COALESCE(emprt.Remarks,'') as Remarks,

        COALESCE(emp.sss_number,'') as sss_number,
        COALESCE(emp.pagibig_number,'') as pagibig_number,
        COALESCE(emp.tin_number,'') as tin_number,
        COALESCE(emp.philhealth_number,'') as philhealth_number,

        COALESCE(emp.salary_type,'') as salary_type,
        COALESCE(emp.contact_number,'') as MobileNo,
        COALESCE(emp.email,'') as EmailAddress,
        COALESCE(emp.status,'') as Status 
    ");

     if($OptionSearch=='ByID'){
         $info->where("emprt.ID",$OptionValue);

     }

      if($OptionSearch=='ByEmployeeID'){
         $info->where("emp.id",$OptionValue);
         
     }

     if($OptionSearch=='ByEmployeeNo'){
         $info->where("emp.employee_number",$OptionValue);
         
     }
      
    $info->orderBy("emprt.EffectivityDate","DESC");  
    $record=$info->first();

    return $record;

}

public function doSaveEmployeeRate($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $EmployeeRateID = $data['EmployeeRateID'];
    $EmployeeID = $data['EmployeeID'];
    $EffectivityDate =$data['EffectivityDate'];
    
    $MonthlyRate = $Misc->setNumeric($data['MonthlyRate']);
    $DailyRate = $Misc->setNumeric($data['DailyRate']);
    $HourlyRate = $Misc->setNumeric($data['HourlyRate']);

    $RateRemarks =$data['RateRemarks'];

    $EffectivityDate=date('Y-m-d',strtotime($EffectivityDate)); 

    if($EmployeeRateID > 0){

        DB::table('payroll_employee_rates')
            ->where('ID',$EmployeeRateID)
            ->update([
                'EmployeeID' => $EmployeeID,
                'EffectivityDate' => $EffectivityDate,
                'MonthlyRate' => $MonthlyRate,
                'HourlyRate' => $HourlyRate,
                'DailyRate' => $DailyRate, 
                'Remarks' => $RateRemarks,                
                'UpdatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' => $TODAY
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $EmployeeRateID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee Rate";
        $logData['TransType'] = "Update Employee Rate Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

     
        $EmployeeRateID = DB::table('payroll_employee_rates')
            ->insertGetId([
                'EmployeeID' => $EmployeeID,
                'EffectivityDate' => $EffectivityDate,
                'MonthlyRate' => $MonthlyRate,
                'HourlyRate' => $HourlyRate,
                'DailyRate' => $DailyRate, 
                'Remarks' => $RateRemarks,                              
                'CreatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' => $TODAY
              ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $EmployeeRateID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee Rate";
        $logData['TransType'] = "New Employee Rate Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

  return $EmployeeRateID;
 }

 public function doSaveUploadEmployeeRates($data){

    $hasDataError=false;
    $TODAY = date("Y-m-d H:i:s");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    
    //add param only Uploaded By Admin
    $data['UploadedByID']= Session::get('ADMIN_USER_ID');
    $info_list=$this->getEmployeeTempRateList($data);
    //check if has data error status
    if(count($info_list)>0){
        foreach($info_list as $list){
            if($list->IsUploadError>0){
                $hasDataError=true;  
            }
        } 
    }
    
    //REMOVE CHECKING OF FINAL SAVE
    //if($hasDataError){
    //    $RetVal['Response'] = "Failed";
    //    $RetVal['ResponseMessage'] = "Sorry! Employee Rate data cannot be saved. Please check data base on color highlight.";
    //}else{

        $EmployeeID = 0;
        $EffectivityDate = 0;        
        $NewMonthlyRate = 0;
        $NewDailyRate = 0;
        $NewHourlyRate = 0;

        $data['UploadedByID']= Session::get('ADMIN_USER_ID');    
        $info_list=$this->getEmployeeTempRateList($data);
        //check if has data error status
        if(count($info_list)>0){

          $Misc = new Misc();
          $UploadBatchNo = $Misc->GetSettingsNextUploadBatchNo();

          foreach($info_list as $info){
          
           $EmployeeID = $info->employee_id;
           $EffectivityDate = $info->EffectivityDate;          
           $NewMonthlyRate = $info->MonthlyRate;
           $NewDailyRate = $info->DailyRate;
           $NewHourlyRate = $info->HourlyRate;
           $Remarks = $info->Remarks;
        
    
          $EmployeeRateID = DB::table('payroll_employee_rates')
            ->insertGetId([
                'EmployeeID' => $EmployeeID,
                'EffectivityDate' => $EffectivityDate,  
                'MonthlyRate' => $NewMonthlyRate, 
                'DailyRate' => $NewDailyRate, 
                'HourlyRate' => $NewHourlyRate, 
                'IsUploaded' => 1, 
                'UploadBatchNo' => $UploadBatchNo, 
                'Remarks' => $Remarks,                        
                'UploadedByID'=> Session::get('ADMIN_USER_ID'),    
                'DateTimeUploaded'=> $TODAY
              ]);  
                
            } 

           //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $EmployeeRateID;
            $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
            $logData['ModuleType'] = "Employee Rate";
            $logData['TransType'] = "Upload Employee Rate Information";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);

         //Update Batch Number counter
        $Misc->SetSettingsNextUploadBatchNo($UploadBatchNo);

        }

        //Clear temp table where currrent login Admin
        DB::table('payroll_employee_rates_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->delete();

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Employee New Rates has saved successfully.";
        
    //}

    return $RetVal;
}

// TEMP TABLE RATE EMPLOYEE LIST================================================
public function getEmployeeTempRateList($param){

    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];
   
    $query = DB::table('payroll_employee_rates_temp as temprt')
    ->leftjoin('users as emp', 'emp.id', '=', 'temprt.EmployeeID')
     
    ->selectraw("
        temprt.ID as EmployeeRateID,   

        COALESCE(emp.id,0) as employee_id, 
        COALESCE(emp.employee_number,'') as employee_number,        
        CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

        COALESCE(temprt.MonthlyRate,0) as MonthlyRate,
        COALESCE(temprt.DailyRate,0) as DailyRate,
        COALESCE(temprt.HourlyRate,0) as HourlyRate,
        COALESCE(temprt.EffectivityDate,'') as EffectivityDate,
        FORMAT(temprt.EffectivityDate,'MM/dd/yyyy') as EffectivityDateFormat,        
        COALESCE(temprt.Remarks,'') as Remarks,
        COALESCE(temprt.IsUploadError,0) as IsUploadError,
                
        COALESCE(emp.status,'') as Status
                  
    ");

    if($Status!=''){
        if($Status=='Active'){
           $query->where("emp.status",1);    
        }
        
        if($Status=='InActive'){
           $query->where("emp.status",2);    
        }
    }
    
    $query->where("temprt.UploadedByID",Session::get('ADMIN_USER_ID'));

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("temprt.IsUploadError","DESC");
    
    $list = $query->get();

    return $list;

}

public function getEmployeeTempRateInfo($EmployeeRateID){

   $info = DB::table('payroll_employee_rates_temp as temprt')
     ->leftjoin('users as emp', 'emp.id', '=', 'temprt.EmployeeID')   
        ->selectraw("
            temprt.ID as EmployeeRateID,  

            COALESCE(emp.id,0) as employee_id, 
            COALESCE(emp.employee_number,'') as employee_number,
            COALESCE(emp.first_name,'') as first_name,
            COALESCE(emp.last_name,'') as last_name,
            COALESCE(emp.middle_name,'') as middle_name,
            CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

            COALESCE(temprt.MonthlyRate,0) as MonthlyRate,
            COALESCE(temprt.DailyRate,0) as DailyRate,
            COALESCE(temprt.HourlyRate,0) as HourlyRate,
            COALESCE(temprt.EffectivityDate,'') as EffectivityDate,
            FORMAT(temprt.EffectivityDate,'MM/dd/yyyy') as EffectivityDateFormat,            
            COALESCE(temprt.Remarks,'') as Remarks,
            
            COALESCE(emp.sss_number,'') as sss_number,
            COALESCE(emp.pagibig_number,'') as pagibig_number,
            COALESCE(emp.tin_number,'') as tin_number,
            COALESCE(emp.philhealth_number,'') as philhealth_number,

            COALESCE(emp.salary_type,'') as salary_type,
            COALESCE(emp.contact_number,'') as MobileNo,
            COALESCE(emp.email,'') as EmailAddress,
            COALESCE(emp.status,'') as Status
 
           ")

    ->where("temprt.ID",$EmployeeRateID)
    ->first();

    return $info;

}

 public function doSaveEmployeeTempRateBatch($data){

    $Misc = new Misc();
    $Employee = new Employee();

    $TODAY = date("Y-m-d H:i:s");
    $TempRateDataItems = $data['TempRateDataItems'];

    if(!empty($TempRateDataItems)){

      for($x=0; $x< count($TempRateDataItems); $x++) {
            
        $EmployeeRateID = $TempRateDataItems[$x]["EmployeeRateID"];

        $EmployeeNo = $TempRateDataItems[$x]["EmpNo"];
        $EmployeeID=0;
        if($EmployeeNo!=''){
            $emp_info = $Employee->getEmployeeInfo('ByEmployeeNo',$EmployeeNo);
            if(isset($emp_info)>0){
               $EmployeeID=$emp_info->employee_id;
            }  

        }

        $EffectivityDate = $TempRateDataItems[$x]["EffectivityDate"];
        $EffectivityDate=date('Y-m-d',strtotime($EffectivityDate));
     
        $NewMonthlyRate = $TempRateDataItems[$x]["NewMonthlyRate"];
        $NewDailyRate = $TempRateDataItems[$x]["NewDailyRate"];
        $NewHourlyRate = $TempRateDataItems[$x]["NewHourlyRate"];

        $IsUploaded =$TempRateDataItems[$x]["IsUploaded"];
        $Remarks = $TempRateDataItems[$x]["Remarks"];        
        
        $IsUploadError=0;

        if($EmployeeID==0){
             $IsUploadError=1;
        }

        $EmployeeRateID = DB::table('payroll_employee_rates_temp')
            ->insertGetId([
                'EmployeeID' => $EmployeeID,
                'EffectivityDate' => $EffectivityDate,  
                'MonthlyRate' => $NewMonthlyRate, 
                'DailyRate' => $NewDailyRate, 
                'HourlyRate' => $NewHourlyRate, 
                'Remarks' => $Remarks,                        
                'IsUploadError'=> $IsUploadError,
                'UploadedByID'=> Session::get('ADMIN_USER_ID'),    
                'DateTimeUploaded'=> $TODAY
              ]);  

         $this->checkUploadedExcelRecordHasDuplicateRecord($EmployeeID);
                
      }
    }

 return "Success";

}

public function doSaveEmployeeTempRate($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $EmployeeTempRateID = $data['EmployeeTempRateID'];
    $EmployeeID = $data['EmployeeID'];
    $EffectivityDate =$data['EffectivityDate'];
    
    $MonthlyRate = $Misc->setNumeric($data['MonthlyRate']);
    $DailyRate = $Misc->setNumeric($data['DailyRate']);
    $HourlyRate = $Misc->setNumeric($data['HourlyRate']);

    $RateRemarks =$data['RateRemarks'];

    $EffectivityDate=date('Y-m-d',strtotime($EffectivityDate)); 

    if($EmployeeTempRateID > 0){

        DB::table('payroll_employee_rates_temp')
            ->where('ID',$EmployeeTempRateID)
            ->update([
                'EmployeeID' => $EmployeeID,
                'EffectivityDate' => $EffectivityDate,
                'MonthlyRate' => $MonthlyRate,
                'HourlyRate' => $HourlyRate,
                'DailyRate' => $DailyRate, 
                'IsUploadError' => 0,
                'Remarks' => $RateRemarks
            ]);

          $this->checkUploadedExcelRecordHasDuplicateRecord($EmployeeID);
       }
  return $EmployeeTempRateID;
 }

 public function doRemoveDuplicateTempRateUpload($tempID){

        $chkEmployeeID=0;
        $info=$this->getEmployeeTempRateInfo($tempID);

        if(isset($info)>0){
            $chkEmployeeID=$info->employee_id;
            DB::table('payroll_employee_rates_temp')->where('ID',$tempID)->delete();
            $this->checkUploadedExcelRecordHasDuplicateRecord($chkEmployeeID);
        }
    }

 //check if uploaded excel has duplicate payroll id and duplicate employee
    public function checkUploadedExcelRecordHasDuplicateRecord($EmployeeID){
      
      if($EmployeeID>0){

            $info = DB::table('payroll_employee_rates_temp')               
               ->where("EmployeeID",$EmployeeID)                           
               ->get();
        
                if(count($info)>=2){

                    DB::table('payroll_employee_rates_temp')                        
                        ->where("EmployeeID",$EmployeeID)                                               

                        ->update([
                            'IsUploadError' => 2
                          ]);  
              }else{

                     DB::table('payroll_employee_rates_temp')                        
                        ->where("EmployeeID",$EmployeeID)                                     

                        ->update([
                            'IsUploadError' => 0
                          ]);  
              }
        }
    }
}

