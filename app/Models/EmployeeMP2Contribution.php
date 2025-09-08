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

class EmployeeMP2Contribution extends Model
{

  // EMPLOYEE MP2 CONTRIBUTION
  public function doSaveUpdateEmployeeMP2Contribution($data){
    
    $Misc = new Misc();
    $TODAY = date("Y-m-d H:i:s");
    
    $EmployeeID = $data['EmployeeID'];
    $MP2AccountNo = trim($data['MP2AccountNo']);
    $MP2FrequencyID = trim($data['MP2Frequency']);
    $MP2ContributionAmount = $Misc->setNumeric($data["MP2ContributionAmount"]);      

    $MP2_ID=DB::table('payroll_employee_mp2_setup')->where('EmployeeID',$EmployeeID)->value('ID');

    if($MP2_ID > 0){

        DB::table('payroll_employee_mp2_setup')
            ->where('ID',$MP2_ID)
            ->update([
                'MP2No' => $MP2AccountNo,
                'EmployeeID' => $EmployeeID,                
                'MP2Amount' => $MP2ContributionAmount,                  
                'FrequencyID' => $MP2FrequencyID,                
                'Status' => 'Active',                
                'UpdatedByID' =>  Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' =>$TODAY,               
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $MP2_ID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee MP2 Contribution";
        $logData['TransType'] = "Update MP2 Contribution";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

         $MP2_ID = DB::table('payroll_employee_mp2_setup')
            ->insertGetId([                
                'MP2No' => $MP2AccountNo,
                'EmployeeID' => $EmployeeID,
                'MP2Amount' => $MP2ContributionAmount,                  
                'FrequencyID' => $MP2FrequencyID,                
                'Status' => 'Active',                
                'CreatedByID' =>  Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' =>$TODAY,                
              ]); 


        $Misc = new Misc();
        $logData['TransRefID'] = $MP2_ID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee MP2 Contribution";
        $logData['TransType'] = "Set New MP2 Contribution";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData); 
    }

     return "Success";

  }
  
 //TEMP MP2 CONTRIBUTION  TABLE  
 public function getEmployeeTempMP2List($param){

    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];
   
    $query = DB::table('payroll_employee_mp2_setup_temp as temprt')
    ->leftjoin('users as emp', 'emp.id', '=', 'temprt.EmployeeID')
     
    ->selectraw("
        temprt.ID as EmployeeMP2ID,   

        COALESCE(emp.id,0) as employee_id, 
        COALESCE(emp.employee_number,'') as employee_number,
        COALESCE(emp.first_name,'') as first_name,
        COALESCE(emp.last_name,'') as last_name,
        COALESCE(emp.middle_name,'') as middle_name,
        CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

        COALESCE(temprt.MP2No,'') as MP2AccountNo,
        COALESCE(temprt.MP2Amount,0) as MP2Amount,
        COALESCE(temprt.FrequencyID,0) as FrequencyID,
        COALESCE(temprt.Status,'') as Status,
        COALESCE(temprt.IsUploadError,0) as IsUploadError
        
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

public function getEmployeeTempMP2Info($EmployeeMP2ID){

   $info = DB::table('payroll_employee_mp2_setup_temp as temprt')
     ->leftjoin('users as emp', 'emp.id', '=', 'temprt.EmployeeID')   
        ->selectraw("
        temprt.ID as EmployeeMP2ID,   

        COALESCE(emp.id,0) as employee_id, 
        COALESCE(emp.employee_number,'') as employee_number,
        COALESCE(emp.first_name,'') as first_name,
        COALESCE(emp.last_name,'') as last_name,
        COALESCE(emp.middle_name,'') as middle_name,
        CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

        COALESCE(temprt.MP2No,'') as MP2AccountNo,
        COALESCE(temprt.MP2Amount,0) as MP2Amount,
        COALESCE(temprt.FrequencyID,0) as FrequencyID,
        COALESCE(temprt.Status,'') as Status,
        COALESCE(temprt.IsUploadError,0) as IsUploadError

           ")

    ->where("temprt.ID",$EmployeeMP2ID)
    ->first();

    return $info;

}

 public function doSaveEmployeeTempMP2Batch($data){

    $Misc = new Misc();
    $Employee = new Employee();

    $TODAY = date("Y-m-d H:i:s");

    $TempMP2DataItems = $data['TempMP2DataItems'];

    if(!empty($TempMP2DataItems)){

      for($x=0; $x< count($TempMP2DataItems); $x++) {
            
        $EmployeeMP2ID = $TempMP2DataItems[$x]["EmployeeMP2ID"];

        $EmployeeNo = $TempMP2DataItems[$x]["EmpNo"];
        $EmployeeID=0;
        if($EmployeeNo!=''){
            $emp_info = $Employee->getEmployeeInfo('ByEmployeeNo',$EmployeeNo);
            if(isset($emp_info)>0){
               $EmployeeID=$emp_info->employee_id;
            }  

        }
    
        $MP2AccountNo = $TempMP2DataItems[$x]["MP2AccountNo"];
        $MP2ADeductionAmount = $TempMP2DataItems[$x]["MP2ADeductionAmount"];
        $FrequencySchedule= $TempMP2DataItems[$x]["MP2Frequency"];

        if($FrequencySchedule=='1ST HALF'){
            $FrequencyID=1;
        }else if($FrequencySchedule=='2ND HALF'){
            $FrequencyID=2;
        }else if($FrequencySchedule=='EVERY CUTOFF'){
            $FrequencyID=3;
        }else{
           $FrequencyID=6;
        }

        $Status =$TempMP2DataItems[$x]["Status"];  
        $IsUploaded =$TempMP2DataItems[$x]["IsUploaded"];  

        $IsUploadError=0;

        if($EmployeeID==0){
           $IsUploadError=1;
        }

        $EmployeeRateID = DB::table('payroll_employee_mp2_setup_temp')
            ->insertGetId([
                'EmployeeID' => $EmployeeID,
                'MP2No' => $MP2AccountNo,  
                'MP2Amount' => $MP2ADeductionAmount, 
                'FrequencyID' => $FrequencyID, 
                'Status' => $Status,                 
                'IsUploadError'=> $IsUploadError,
                'UploadedByID'=> Session::get('ADMIN_USER_ID'),    
                'DateTimeUploaded'=> $TODAY
              ]);  

         $this->checkUploadedExcelRecordHasDuplicateRecord($EmployeeID);
                
      }
   }
 return "Success";

}

public function doSaveEmployeeTempMP2($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $EmployeeTempMP2ID = $data['EmployeeTempMP2ID'];
    $EmployeeID = $data['EmployeeID'];

    $MP2AccountNo =$data['MP2AccountNo'];
    $MP2DeductionAmount = $Misc->setNumeric($data['MP2DeductionAmount']);    
    $FrequencyID =$data['MP2Frequency'];    
    
    if($EmployeeTempMP2ID > 0){

        DB::table('payroll_employee_mp2_setup_temp')
            ->where('ID',$EmployeeTempMP2ID)
            ->update([
                'EmployeeID' => $EmployeeID,
                'MP2No' => $MP2AccountNo,  
                'MP2Amount' => $MP2DeductionAmount, 
                'FrequencyID' => $FrequencyID,                        
            ]);

          $this->checkUploadedExcelRecordHasDuplicateRecord($EmployeeID);
       }
  return $EmployeeTempMP2ID;
 }

 public function doRemoveDuplicateTempMP2Upload($tempID){

        $chkEmployeeID=0;
        $info=$this->getEmployeeTempMP2Info($tempID);

        if(isset($info)>0){
            $chkEmployeeID=$info->employee_id;
            DB::table('payroll_employee_mp2_setup_temp')->where('ID',$tempID)->delete();
            $this->checkUploadedExcelRecordHasDuplicateRecord($chkEmployeeID);
        }
}

public function doSaveUploadEmployeeMP2($data){

    $hasDataError=false;
    $TODAY = date("Y-m-d H:i:s");

    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";
    
    //add param only Uploaded By Admin
    $data['UploadedByID']= Session::get('ADMIN_USER_ID');
    $info_list=$this->getEmployeeTempMP2List($data);
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
        $info_list=$this->getEmployeeTempMP2List($data);
        //check if has data error status
        if(count($info_list)>0){

          $Misc = new Misc();
          $UploadBatchNo = $Misc->GetSettingsNextUploadBatchNo();

          foreach($info_list as $info){
          
           $chkEmployeeID=0;
           $EmployeeID = $info->employee_id;
           $MP2AccountNo = $info->MP2AccountNo;
           $MP2ADeductionAmount = $info->MP2Amount;
           $FrequencyID= $info->FrequencyID;
           
           $IsUploadError=0;

           $EmployeeMP2ID=DB::table('payroll_employee_mp2_setup')->where('EmployeeID',$EmployeeID)->value('ID');

           if($EmployeeMP2ID>0){

                DB::table('payroll_employee_mp2_setup')
                ->where("EmployeeID",$EmployeeID) 
                ->update([
                    'EmployeeID' => $EmployeeID,
                    'MP2No' => $MP2AccountNo,  
                    'MP2Amount' => $MP2ADeductionAmount, 
                    'FrequencyID' => $FrequencyID,                     
                    'IsUploaded' => 1, 
                    'UploadBatchNo' => $UploadBatchNo,                                 
                    'UploadedByID'=> Session::get('ADMIN_USER_ID'),    
                    'DateTimeUploaded'=> $TODAY
                  ]); 

                    //Save Transaction Log
                    $Misc = new Misc();
                    $logData['TransRefID'] = $EmployeeMP2ID;
                    $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
                    $logData['ModuleType'] = "Employee MP2";
                    $logData['TransType'] = "Edit Upload Employee MP2 Information";
                    $logData['Remarks'] = "";
                    $Misc->doSaveTransactionLog($logData);  

            }else{

                 $EmployeeMP2ID = DB::table('payroll_employee_mp2_setup')
                    ->insertGetId([
                        'EmployeeID' => $EmployeeID,
                        'MP2No' => $MP2AccountNo,  
                        'MP2Amount' => $MP2ADeductionAmount, 
                        'FrequencyID' => $FrequencyID,                                     
                        'IsUploaded' => 1, 
                        'Status' => 'Active', 
                        'UploadBatchNo' => $UploadBatchNo,                                 
                        'UploadedByID'=> Session::get('ADMIN_USER_ID'),    
                        'DateTimeUploaded'=> $TODAY
                      ]); 

                    //Save Transaction Log
                    $Misc = new Misc();
                    $logData['TransRefID'] = $EmployeeMP2ID;
                    $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
                    $logData['ModuleType'] = "Employee MP2";
                    $logData['TransType'] = "Upload Employee MP2 Information";
                    $logData['Remarks'] = "";
                    $Misc->doSaveTransactionLog($logData);  
                }        
           }

          //Update Batch Number counter
          $Misc->SetSettingsNextUploadBatchNo($UploadBatchNo);

        }

        //Clear temp table where currrent login Admin
        DB::table('payroll_employee_mp2_setup_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->delete();

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Employee MP2 Deduction has saved successfully.";
        
    //}

    return $RetVal;
}

 //check if uploaded excel has duplicate payroll id and duplicate employee
    public function checkUploadedExcelRecordHasDuplicateRecord($EmployeeID){
      
      if($EmployeeID>0){

            $info = DB::table('payroll_employee_mp2_setup_temp')               
               ->where("EmployeeID",$EmployeeID)                           
               ->get();
        
                if(count($info)>=2){

                    DB::table('payroll_employee_mp2_setup_temp')                        
                        ->where("EmployeeID",$EmployeeID)                                               

                        ->update([
                            'IsUploadError' => 2
                          ]);  
              }else{

                     DB::table('payroll_employee_mp2_setup_temp')                        
                        ->where("EmployeeID",$EmployeeID)                                     

                        ->update([
                            'IsUploadError' => 0
                          ]);  
              }
        }
    }

}

