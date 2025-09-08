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

class EmployeeIncomeDeduction extends Model
{

  public function getEmployeeIncomeDeductionTransactionList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_employee_income_deduction_transaction as peit')      
      ->join('payroll_income_deduction_type as pidt', 'pidt.ID', '=', 'peit.IncomeDeductionTypeID')  
      ->join('users as emp', 'emp.id', '=', 'peit.EmployeeID')   
            ->selectraw("
                peit.ID,                
                COALESCE(peit.TransactionDate,'') as TransactionDate,
                  
                COALESCE(peit.ReleaseTypeID,0) as ReleaseTypeID,  

                COALESCE(peit.EmployeeID,'') as EmployeeID,
                COALESCE(emp.employee_number,'') as EmployeeNumber,
                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as FullName,
                
                COALESCE(peit.IncomeDeductionTypeID,0) as IncomeDeductionTypeID,
                COALESCE(pidt.Code,'') as IncomeDeductionTypeCode,
                COALESCE(pidt.Name,'') as IncomeDeductionTypeName,
                COALESCE(pidt.Category,'') as Category,
                
                COALESCE(peit.VoucherNo,'') as VoucherNo,
                COALESCE(peit.DateIssue,'') as DateIssue,
                COALESCE(peit.PaymentStartDate,'') as DateStartPayment,

                COALESCE(peit.InterestAmount,0) as InterestAmount,
                COALESCE(peit.AmortizationAmount,0) as AmortizationAmount,
                COALESCE(peit.IncomeDeductionAmount,0) as IncomeDeductionAmount,

                COALESCE(peit.MonthsToPay,0) as MonthsToPay,
                COALESCE(peit.TotalPayment,0) as TotalPayment,
                COALESCE(peit.TotalIncomeDeductionAmount,0) as TotalIncomeDeductionAmount,
                
                COALESCE(peit.Remarks,'') as Remarks,
                COALESCE(peit.Status,'') as Status,

                COALESCE(peit.IsClaimed,0) as IsClaimed,    
                COALESCE(peit.IsPosted,0) as IsPosted,
                COALESCE(peit.IsClosed,0) as IsClosed,                

                FORMAT(peit.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(peit.DateIssue,'MM/dd/yyyy') as DateIssueFormat,
                FORMAT(peit.PaymentStartDate,'MM/dd/yyyy') as DateStartPaymentFormat,
                
                COALESCE(peit.CreatedByID,0) as CreatedByID,
                COALESCE(peit.DateTimeCreated,'') as DateTimeCreated,
                COALESCE(peit.CancelledByID,0) as CancelledByID,
                COALESCE(peit.DateTimeCancelled,'') as DateTimeCancelled,
                COALESCE(peit.ApprovedByID,0) as ApprovedByID,
                COALESCE(peit.DateTimeApproved,'') as DateTimeApproved
            ");

     if($Status!=''){

        if($Status=='Pending'){
           $query->where("peit.status",'Pending');    
        }else if($Status=='Approved'){
          $query->where("peit.status",'Approved');    
        }else if($Status=='Cancelled'){
          $query->where("peit.status",'Cancelled');    
        }else if($Status=='OnHold'){
          $query->where("peit.status",'OnHOld');    

        }else if($Status=='Earning'){
          $query->where("pidt.Category",'EARNING');  
        }else if($Status=='Deduction'){
          $query->where("pidt.Category",'DEDUCTION'); 
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
                        COALESCE(peit.TransactionDate,''),
                        COALESCE(pidt.Name,''),
                        COALESCE(emp.employee_number,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,''),
                        COALESCE(emp.middle_name,''),
                        COALESCE(peit.VoucherNo,''),
                        COALESCE(peit.Remarks,''),
                        COALESCE(peit.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }
  
    $query->orderBy("FullName","ASC");
    $query->orderBy("peit.ID","DESC");
    $query->orderBy("peit.Status","DESC");
    
    $list = $query->get();

    return $list;

}

public function getEmployeeIncomeDeductionTransactionInfo($SearchOption,$SearchValue){

      $info = DB::table('payroll_employee_income_deduction_transaction as peit')      
      ->join('payroll_income_deduction_type as pidt', 'pidt.ID', '=', 'peit.IncomeDeductionTypeID')              
      ->join('users as emp', 'emp.id', '=', 'peit.EmployeeID')   

            ->selectraw("
                peit.ID,                
                COALESCE(peit.TransactionDate,'') as TransactionDate,
                  
                COALESCE(peit.ReleaseTypeID,0) as ReleaseTypeID,            

                COALESCE(peit.EmployeeID,'') as EmployeeID,
                COALESCE(emp.employee_number,'') as EmployeeNumber,
                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as FullName,
                
                COALESCE(peit.IncomeDeductionTypeID,0) as IncomeDeductionTypeID,
                COALESCE(pidt.Code,'') as IncomeDeductionTypeCode,
                COALESCE(pidt.Name,'') as IncomeDeductionTypeName,
                COALESCE(pidt.Category,'') as Category,
                
                COALESCE(peit.VoucherNo,'') as VoucherNo,
                COALESCE(peit.DateIssue,'') as DateIssue,
                COALESCE(peit.PaymentStartDate,'') as DateStartPayment,
          
                COALESCE(peit.InterestAmount,0) as InterestAmount,
                COALESCE(peit.AmortizationAmount,0) as AmortizationAmount,
                COALESCE(peit.IncomeDeductionAmount,0) as IncomeDeductionAmount,                
                COALESCE(peit.TotalIncomeDeductionAmount,0) as TotalIncomeDeductionAmount,

                COALESCE(peit.MonthsToPay,0) as MonthsToPay,
                COALESCE(peit.TotalPayment,0) as TotalPayment,
                
                COALESCE(peit.Remarks,'') as Remarks,
                COALESCE(peit.Status,'') as Status,

                COALESCE(peit.IsClaimed,0) as IsClaimed,    
                COALESCE(peit.IsPosted,0) as IsPosted,
                COALESCE(peit.IsClosed,0) as IsClosed,                

                FORMAT(peit.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(peit.DateIssue,'MM/dd/yyyy') as DateIssueFormat,
                FORMAT(peit.PaymentStartDate,'MM/dd/yyyy') as DateStartPaymentFormat,
                
                COALESCE(peit.CreatedByID,0) as CreatedByID,
                COALESCE(peit.DateTimeCreated,'') as DateTimeCreated,
                COALESCE(peit.CancelledByID,0) as CancelledByID,
                COALESCE(peit.DateTimeCancelled,'') as DateTimeCancelled,
                COALESCE(peit.ApprovedByID,0) as ApprovedByID,
                COALESCE(peit.DateTimeApproved,'') as DateTimeApproved
            ");

             if($SearchOption=='ByID'){
                $info->where("peit.ID",$SearchValue);                
            }

            if($SearchOption=='ByEmployeeID'){
                $info->where("peit.EmployeeID",$SearchValue);                
            }

           if($SearchOption=='ByEmployeeNo'){
                $info->where("peit.EmployeeNumber",$SearchValue);           
            }

            $record=$info->first();

    return $record;

}

public function getEmployeeIncomeDeductionPaymentLedgerList($param){

        $IncomeDeductionTransID = $param['IncomeDeductionTransID'];
        
        $SearchText = trim($param['SearchText']);
        $Limit = $param['Limit'];
        $PageNo = $param['PageNo'];

        $query = DB::table('payroll_income_deduction_payment_ledger as paylgr')
          ->join('payroll_employee_income_deduction_transaction as dedtrn', 'dedtrn.ID', '=', 'paylgr.IncomeDeductionTransID')
          ->join('payroll_income_deduction_type as inc', 'inc.ID', '=', 'dedtrn.IncomeDeductionTypeID')   
          ->join('users as emp', 'emp.id', '=', 'dedtrn.EmployeeID')   
          
                ->selectraw("
                    paylgr.ID,                
                    COALESCE(paylgr.PaymentModuleType,'') as PaymentModuleType,
                    COALESCE(paylgr.ReferrenceTransID,0) as ReferrenceTransID,

                    COALESCE(paylgr.IncomeDeductionTransID,0) as IncomeDeductionTransID,
                    
                    COALESCE(dedtrn.EmployeeID,'') as EmployeeID,
                    COALESCE(dedtrn.EmployeeNumber,'') as EmployeeNumber,
                    CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,
                     
                     COALESCE(dedtrn.TotalPayment,0) as TotalPayment,
                     COALESCE(dedtrn.TotalIncomeDeductionAmount,0) as TotalIncomeDeductionAmount,

                    COALESCE(inc.Code,'') as DeductionCode,
                    COALESCE(inc.Name,'') as DeductionName,

                    COALESCE(paylgr.AmountPayment,0) as AmountPayment,

                    FORMAT(paylgr.PaymentDate,'MM/dd/yyyy') as PaymentDateFormat
                ");
      
           if($IncomeDeductionTransID>0){
              $query->where("paylgr.IncomeDeductionTransID",$IncomeDeductionTransID);           
          }

         if($SearchText != ''){

            $arSearchText = explode(" ",$SearchText);
            if(count($arSearchText) > 0){
                for($x=0; $x< count($arSearchText); $x++) {
                    $query->whereraw(
                        "CONCAT_WS(' ',                        
                            COALESCE(paylgr.PaymentDate,''),
                            COALESCE(emp.employee_number,''),
                            COALESCE(emp.first_name,''),
                            COALESCE(emp.last_name,''),
                            COALESCE(emp.middle_name,'')                            
                        ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
                }
            }
        }

        // if($Limit > 0){
        //   $query->limit($Limit);
        //   $query->offset(($PageNo-1) * $Limit);
        // }

        $query->orderBy("paylgr.ID","DESC");
        $list = $query->get();

        return $list;
  } 

public function doSaveUploadFinalEmployeeIncomeDeductionTransaction($data){

    $hasDataError=false;
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

    $info_list=$this->getEmployeeIncomeDeductionTransactionTempList($data);

    //check if has data error status
    if(count($info_list)>0){
        foreach($info_list as $list){
            if($list->IsUploadError==1){
              $hasDataError=true;  
            }
        } 
    }

    // if($hasDataError){
    //     $RetVal['Response'] = "Failed";
    //     $RetVal['ResponseMessage'] = "Sorry! Employee income and deduction data cannot be saved. Data still has issues that need to be resolved.";
    // }else{

        DB::statement("insert into payroll_employee_income_deduction_transaction(TransactionDate,ReleaseTypeID,EmployeeID,EmployeeNumber,IncomeDeductionTypeID,IncomeDeductionTypeCode,VoucherNo,DateIssue,PaymentStartDate,InterestAmount,AmortizationAmount,IncomeDeductionAmount,MonthsToPay,TotalIncomeDeductionAmount,Remarks,Status,CreatedByID,DateTimeCreated) 
         Select TransactionDate,ReleaseTypeID,EmployeeID,EmployeeNumber,IncomeDeductionTypeID,IncomeDeductionTypeCode,VoucherNo,DateIssue,PaymentStartDate,InterestAmount,AmortizationAmount,IncomeDeductionAmount,MonthsToPay,TotalIncomeDeductionAmount,Remarks,'Approved',UploadedByID,DateTimeUploaded from payroll_employee_income_deduction_temp where IsUploadError!=1 AND UploadedByID=?",[Session::get('ADMIN_USER_ID')]);
                           
        //CLEAR TEMP TABLE AFTER SAVE
        DB::table('payroll_employee_income_deduction_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->delete();        
        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Uplaoded Employee Income & Deduction has saved successfully.";
    //}

    return $RetVal;
}

public function doSaveEmployeeIncomeDeductionTransaction($data){

    $Misc = new Misc();
    $TODAY = date("Y-m-d H:i:s");

    $EmployeeIncomeDeductionTransID=$data["EmployeeIncomeDeductionTransID"];

    $TransDate=$data["TransDate"];

    $ReleaseTypeID=$data["ReleaseTypeID"];
    $EmployeeID=$data["EmpID"];
    $EmployeeNo=$data["EmpNo"];

    $IncomeDeductionTypeID=$data["IncomeDeductionTypeID"];
    $IncomeDeductionTypeCode=$data["IncomeDeductionTypeCode"];

    $ReferenceNo=$data["ReferenceNo"];

    $DateIssued=$data["DateIssued"];
    $DateStartPayment=$data["DateStartPayment"];
        
    $InterestAmnt=$Misc->setNumeric($data["InterestAmnt"]);
    $AmortizationAmnt=$Misc->setNumeric($data["AmortizationAmnt"]);
    $IncomeDeductionAmnt=$Misc->setNumeric($data["IncomeDeductionAmnt"]);
    $TotalIncomeDeductionAmnt=$Misc->setNumeric($data["TotalIncomeDeductionAmnt"]);

   //$MonthsToPay=$data["TotalMonthsToPay"];

    $MonthsToPay=0;
    $MonthsToPay=ceil($TotalIncomeDeductionAmnt / $AmortizationAmnt);    
    
    $Remarks=$data["Remarks"];
    $Status=$data["Status"];

    $TransDate=date('Y-m-d',strtotime($TransDate)); 

    if($DateIssued!=''){
       $DateIssued=date('Y-m-d',strtotime($DateIssued)); 
    }else{
       $DateIssued='';
    }
    
    $DateStartPayment=date('Y-m-d',strtotime($DateStartPayment)); 
    
    if($EmployeeIncomeDeductionTransID > 0){

        DB::table('payroll_employee_income_deduction_transaction')
            ->where('ID',$EmployeeIncomeDeductionTransID)
            ->update([
                'TransactionDate' => $TransDate,             
                'ReleaseTypeID' => $ReleaseTypeID,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,                
                'IncomeDeductionTypeID' => $IncomeDeductionTypeID,
                'IncomeDeductionTypeCode' => $IncomeDeductionTypeCode,
                'VoucherNo' => $ReferenceNo,
                'DateIssue' => $DateIssued,
                'PaymentStartDate' => $DateStartPayment,
                'InterestAmount' => $InterestAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'IncomeDeductionAmount' => $IncomeDeductionAmnt,
                'MonthsToPay' => $MonthsToPay,
                'TotalIncomeDeductionAmount' => $TotalIncomeDeductionAmnt,                
                'Remarks' => $Remarks,
                'Status' => $Status,
                'UpdatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' => $TODAY
            ]);

           //CHCK DATE ISSUE
             if($DateIssued==''){
               DB::table('payroll_employee_income_deduction_transaction')                
                ->where('ID',$EmployeeIncomeDeductionTransID)
                ->update([                
                    'DateIssue' => NULL                
                ]);
            }


        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $EmployeeIncomeDeductionTransID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee Income & Deduction Transaction";
        $logData['TransType'] = "Update Employee Income & Deduction Transaction Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{
        
      
        $EmployeeIncomeDeductionTransID = DB::table('payroll_employee_income_deduction_transaction')
                ->insertGetId([                
                'TransactionDate' => $TransDate,     
                'ReleaseTypeID' => $ReleaseTypeID,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,                
                'IncomeDeductionTypeID' => $IncomeDeductionTypeID,
                'IncomeDeductionTypeCode' => $IncomeDeductionTypeCode,
                'VoucherNo' => $ReferenceNo,
                'DateIssue' => $DateIssued,
                'PaymentStartDate' => $DateStartPayment,
                'InterestAmount' => $InterestAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'MonthsToPay' => $MonthsToPay,
                'IncomeDeductionAmount' => $IncomeDeductionAmnt,
                'TotalIncomeDeductionAmount' => $TotalIncomeDeductionAmnt,
                'Remarks' => $Remarks,
                'Status' => $Status,      
                'CreatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' => $TODAY
              ]); 

            //CHCK DATE ISSUE
            if($DateIssued==''){
               DB::table('payroll_employee_income_deduction_transaction')                
                ->where('ID',$EmployeeIncomeDeductionTransID)
                ->update([                
                    'DateIssue' => NULL                
                ]);
            }
            

                
        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $EmployeeIncomeDeductionTransID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee Income & Deduction Transaction";
        $logData['TransType'] = "New Employee Income & Deduction Transaction Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    }
     return $EmployeeIncomeDeductionTransID;

    }

public function doSetEmployeeIncomeDeductionTransactionStatus($data){

$IncomeDeductionID = $data['IncomeDeductionID'];
$NewStatus = $data['NewStatus'];

    
     DB::table('payroll_employee_income_deduction_transaction')
        ->where('ID',$IncomeDeductionID)
        ->update([
            'Status' => $NewStatus
        ]);


    //Save Transaction Log
    $Misc = new Misc();
    $logData['TransRefID'] = $IncomeDeductionID;
    $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
    $logData['ModuleType'] = "Employee Income & Deduction Transaction";

    if($NewStatus=='Approved'){
        $logData['TransType'] = "Set Approved Employee Income & Deduction Information";
    }

    if($NewStatus=='Cancelled'){
        $logData['TransType'] = "Set Cancelled Employee Income & Deduction Information";
    }

    if($NewStatus=='OnHold'){
        $logData['TransType'] = "Set On Hold Employee Income Payment Deduction ";
    }

    if($NewStatus=='Resume'){
        $logData['TransType'] = "Set On Resume Employee Income Payment Deduction ";
    }

    $logData['Remarks'] = "";
    $Misc->doSaveTransactionLog($logData);

     return "Success";
}

 public function doCheckExistingEmployeeIncomeDeduction($param){

    $IsExist = false;
    $EmployeeIncomeDeductionTransID=$param['EmployeeIncomeDeductionTransID'];
    $EmployeeID=$param['EmpID'];
    $IncomeDeductionTypeID=$param['IncomeDeductionTypeID'];

    $EmployeeIncomeDeduction = DB::table('payroll_employee_income_deduction_transaction')
          ->where('EmployeeID','=',$EmployeeID)
          ->where('IncomeDeductionTypeID','=',$IncomeDeductionTypeID)
          ->where('Status','=','Pending')
          ->first();

    if(isset($EmployeeIncomeDeduction)){
        if($EmployeeIncomeDeductionTransID > 0){
          if($EmployeeIncomeDeduction->ID != $EmployeeIncomeDeductionTransID){
            $IsExist = true;
          }
        }else{
          $IsExist = true;
        }
      }
      return $IsExist;
    }

//TEMP TABLE  
  public function getEmployeeIncomeDeductionTransactionTempList($param){

    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_employee_income_deduction_temp as peidt')
      ->leftjoin('users as emp', 'emp.id', '=', 'peidt.EmployeeID')   
      ->leftjoin('payroll_income_deduction_type as pidt', 'pidt.ID', '=', 'peidt.IncomeDeductionTypeID')   
      
            ->selectraw("
                peidt.ID,
                COALESCE(peidt.TransactionDate,'') as TransactionDate,                        
                COALESCE(peidt.ReleaseTypeID,0) as ReleaseTypeID,   

                COALESCE(peidt.EmployeeID,0) as EmployeeID,
                COALESCE(emp.employee_number,'') as EmployeeNumber,
                COALESCE(emp.first_name,'') as first_name,
                COALESCE(emp.last_name,'') as last_name,
                COALESCE(emp.middle_name,'') as middle_name,
                CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,
                
                
                COALESCE(peidt.IncomeDeductionTypeID,0) as IncomeDeductionTypeID,
                COALESCE(pidt.Code,'') as IncomeDeductionTypeCode,
                COALESCE(pidt.Name,'') as IncomeDeductionTypeName,
                COALESCE(pidt.Category,'') as Category,

                COALESCE(peidt.VoucherNo,'') as VoucherNo,
                COALESCE(peidt.DateIssue,'') as DateIssue,
                COALESCE(peidt.PaymentStartDate,'') as DateStartPayment,

                COALESCE(peidt.InterestAmount,0) as InterestAmount,
                COALESCE(peidt.AmortizationAmount,0) as AmortizationAmount,
                COALESCE(peidt.IncomeDeductionAmount,0) as IncomeDeductionAmount,
                COALESCE(peidt.MonthsToPay,0) as MonthsToPay,
                COALESCE(peidt.TotalIncomeDeductionAmount,0) as TotalIncomeDeductionAmount,
                                                
                FORMAT(peidt.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(peidt.DateIssue,'MM/dd/yyyy') as DateIssueFormat,                
                FORMAT(peidt.PaymentStartDate,'MM/dd/yyyy') as DateStartPaymentFormat, 

                COALESCE(peidt.Remarks,'') as Remarks,
                COALESCE(peidt.Status,'') as Status,
                COALESCE(peidt.IsUploadError,0) as IsUploadError
            ");

    if($Status!=''){
        $query->where("peidt.Status",$Status);
    }       
   
   $query->where("peidt.UploadedByID",Session::get('ADMIN_USER_ID'));

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("peidt.IsUploadError","DESC");    
    $list = $query->get();

    return $list;

}

public function doSaveIncomeDeductionTempTransactionPerBatch($data){

    $Employee = new Employee();
    $PayrollPeriod= new PayrollPeriod();
    $IncomeDeductionType= new IncomeDeductionType();

    $TransDate = date("Y-m-d");    
    $TODAY = date("Y-m-d H:i:s");

    $FrequencyID=0;
    $IncomeDeductionTempDataItems = $data['IncomeDeductionTempDataItems'];
  
    if(!empty($IncomeDeductionTempDataItems)){

      for($x=0; $x< count($IncomeDeductionTempDataItems); $x++) {
            
        $IncomeDeductionTransID = $IncomeDeductionTempDataItems[$x]["IncomeDeductionTransID"];
        
        $EmployeeNo = $IncomeDeductionTempDataItems[$x]["EmpNo"];
        $EmployeeID=0;
        if($EmployeeNo!=''){
            $emp_info = $Employee->getEmployeeInfo('ByEmployeeNo',$EmployeeNo);
            if(isset($emp_info)>0){
               $EmployeeID=$emp_info->employee_id;
            }  

        }
        
        $ReleaseType=$IncomeDeductionTempDataItems[$x]["ReleaseType"];

        if($ReleaseType=='1ST HALF'){
          $FrequencyID=1;
        }else if($ReleaseType=='2ND HALF'){
          $FrequencyID=2;
        }else if($ReleaseType=='EVERY CUTOFF'){
           $FrequencyID=3;
        }else{
          $FrequencyID=6;
        }
        
        $IncomeDeductionTypeCode = $IncomeDeductionTempDataItems[$x]["IncomeDeductionTypeCode"];
        $IncomeDeductionTypeID=0;
        if($IncomeDeductionTypeCode!=''){
            $income_deduction_info = $IncomeDeductionType->getIncomeDeductionTypeInfoByCode($IncomeDeductionTypeCode);
            if(isset($income_deduction_info)>0){
               $IncomeDeductionTypeID=$income_deduction_info->ID;
            }  
        }
                
        $DateStartPayment = $IncomeDeductionTempDataItems[$x]["DateStartPayment"];
        $DateStartPayment=date('Y-m-d',strtotime($DateStartPayment)); 

        $IncomeDeductionTypeCode = $IncomeDeductionTempDataItems[$x]["IncomeDeductionTypeCode"];

        $InterestAmnt = $IncomeDeductionTempDataItems[$x]["InterestAmnt"];
        $AmortizationAmnt = $IncomeDeductionTempDataItems[$x]["AmortizationAmnt"];

        $IncomeDeductionAmnt = $IncomeDeductionTempDataItems[$x]["TotalIncomeDeductionAmnt"];
        $TotalIncomeDeductionAmnt = $IncomeDeductionTempDataItems[$x]["TotalIncomeDeductionAmnt"];
        
        $MonthsToPay=0;
        $MonthsToPay=ceil($TotalIncomeDeductionAmnt / $AmortizationAmnt);
        $Remarks =$IncomeDeductionTempDataItems[$x]["Remarks"];
        $Status =$IncomeDeductionTempDataItems[$x]["Status"];
        
        
        $IsUploadError=0;
        if($EmployeeID==0 || $IncomeDeductionTypeID==0){
             $IsUploadError=1;
        }

        $EmployeeIncomeDeductionTransID = DB::table('payroll_employee_income_deduction_temp')
              ->insertGetId([
                'TransactionDate' => $TransDate,             
                'ReleaseTypeID' => $FrequencyID,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,                
                'IncomeDeductionTypeID' => $IncomeDeductionTypeID,
                'IncomeDeductionTypeCode' => $IncomeDeductionTypeCode,
                'PaymentStartDate' => $DateStartPayment,
                'InterestAmount' => $InterestAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'MonthsToPay' => intval($MonthsToPay),
                'IncomeDeductionAmount' => $IncomeDeductionAmnt,
                'TotalIncomeDeductionAmount' => $TotalIncomeDeductionAmnt,
                'Remarks' => $Remarks,
                'Status' => $Status,
                'IsUploadError'=> $IsUploadError,
                'UploadedByID'=> Session::get('ADMIN_USER_ID'),    
                'DateTimeUploaded'=> $TODAY      
              ]); 


         $this->checkUploadedExcelRecordHasDuplicateRecord($EmployeeID,$IncomeDeductionTypeID,$IncomeDeductionAmnt);
                
      }
    }

    return "Success";
}

public function doSaveEmployeeIncomeDeductionTempTransaction($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $TODAY_UPLOADED_TRANS_DATE = date("Y-m-d");

    $EmployeeIncomeDeductionTransID=$data["EmployeeIncomeDeductionTransID"];

    
    $TransDate=$data["TransDate"];

    $ReleaseTypeID=$data["ReleaseTypeID"];

    $EmployeeID=$data["EmpID"];

    $EmployeeNo=$data["EmpNo"];

    $IncomeDeductionTypeID=$data["IncomeDeductionTypeID"];
    $IncomeDeductionTypeCode=$data["IncomeDeductionTypeCode"];

    $InterestAmnt=$Misc->setNumeric($data["InterestAmnt"]);
    $AmortizationAmnt=$Misc->setNumeric($data["AmortizationAmnt"]);
    $IncomeDeductionAmnt=$Misc->setNumeric($data["IncomeDeductionAmnt"]);
    $TotalIncomeDeductionAmnt=$Misc->setNumeric($data["TotalIncomeDeductionAmnt"]);

    $ReferenceNo=trim($data["ReferenceNo"]);

    $DateIssued=$data["DateIssued"];
    $DateStartPayment=$data["DateStartPayment"];

    $Remarks=$data["Remarks"];
    $Status=$data["Status"];

    $IsUploaded=$data["IsUploaded"];        
    $DateStartPayment=date('Y-m-d',strtotime($DateStartPayment)); 

    if($DateIssued!=''){
       $DateIssued=date('Y-m-d',strtotime($DateIssued)); 
    }else{
       $DateIssued='';
    }
     
    if($IsUploaded==1){
        $TransDate=$TODAY_UPLOADED_TRANS_DATE;
    }else{
        $TransDate=date('Y-m-d',strtotime($TransDate)); 
    }

    if($EmployeeIncomeDeductionTransID > 0){

        DB::table('payroll_employee_income_deduction_temp')
            ->where('ID',$EmployeeIncomeDeductionTransID)
            ->update([
                'TransactionDate' => $TransDate,
                'ReleaseTypeID' => $ReleaseTypeID,            
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,                
                'IncomeDeductionTypeID' => $IncomeDeductionTypeID,
                'IncomeDeductionTypeCode' => $IncomeDeductionTypeCode,
                'InterestAmount' => $InterestAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'IncomeDeductionAmount' => $IncomeDeductionAmnt,
                'TotalIncomeDeductionAmount' => $TotalIncomeDeductionAmnt,
                'VoucherNo' => $ReferenceNo,
                'DateIssue' => $DateIssued,                
                'PaymentStartDate' => $DateStartPayment,  
                'Remarks' => $Remarks,
                'Status' => $Status
          
            ]);

             $this->checkUploadedExcelRecordHasDuplicateRecord($EmployeeID,$IncomeDeductionTypeID,$IncomeDeductionAmnt);
    
    }else{

        $IsUploadError=0;
        if($EmployeeID==0 || $IncomeDeductionTypeID==0){
             $IsUploadError=1;
        }

        if($DateIssued!='' || $DateIssued!='1970-01-01'){
              $EmployeeIncomeDeductionTransID = DB::table('payroll_employee_income_deduction_temp')
                ->insertGetId([
                    'TransactionDate' => $TransDate,
                    'ReleaseTypeID' => $ReleaseTypeID,            
                    'EmployeeID' => $EmployeeID,
                    'EmployeeNumber' => $EmployeeNo,                
                    'IncomeDeductionTypeID' => $IncomeDeductionTypeID,
                    'IncomeDeductionTypeCode' => $IncomeDeductionTypeCode,
                    'VoucherNo' => $ReferenceNo,
                    'DateIssue' => $DateIssued,
                    'PaymentStartDate' => $DateStartPayment,  
                    'InterestAmount' => $InterestAmnt,
                    'AmortizationAmount' => $AmortizationAmnt,
                    'IncomeDeductionAmount' => $IncomeDeductionAmnt,
                    'TotalIncomeDeductionAmount' => $TotalIncomeDeductionAmnt,
                    'Remarks' => $Remarks,
                    'Status' => $Status,
                    'IsUploadError'=> $IsUploadError      
                  ]); 
   
        }else{

            $EmployeeIncomeDeductionTransID = DB::table('payroll_employee_income_deduction_temp')
            ->insertGetId([
                'TransactionDate' => $TransDate,
                'ReleaseTypeID' => $ReleaseTypeID,            
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,                
                'IncomeDeductionTypeID' => $IncomeDeductionTypeID,
                'IncomeDeductionTypeCode' => $IncomeDeductionTypeCode,
                'VoucherNo' => $ReferenceNo,                
                'PaymentStartDate' => $DateStartPayment,  
                'InterestAmount' => $InterestAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'IncomeDeductionAmount' => $IncomeDeductionAmnt,
                'TotalIncomeDeductionAmount' => $TotalIncomeDeductionAmnt,
                'Remarks' => $Remarks,
                'Status' => $Status,
                'IsUploadError'=> $IsUploadError      
              ]); 

        }
   

  
        $this->checkUploadedExcelRecordHasDuplicateRecord($EmployeeID,$IncomeDeductionTypeID,$IncomeDeductionAmnt);

    }

     return $EmployeeIncomeDeductionTransID;

    }

public function getEmployeeIncomeDeductionTempTransactionInfo($EmployeeIncomeDeductionTransaction_ID){

      $info = DB::table('payroll_employee_income_deduction_temp as peidt')
      ->leftjoin('users as emp', 'emp.id', '=', 'peidt.EmployeeID')   
      ->leftjoin('payroll_income_deduction_type as pidt', 'pidt.ID', '=', 'peidt.IncomeDeductionTypeID')  
       
            ->selectraw("
                peidt.ID,
                COALESCE(peidt.TransactionDate,'') as TransactionDate,                        
                COALESCE(peidt.ReleaseTypeID,0) as ReleaseTypeID,   

                COALESCE(peidt.EmployeeID,0) as EmployeeID,
                COALESCE(emp.employee_number,'') as EmployeeNumber,
                COALESCE(emp.first_name,'') as first_name,
                COALESCE(emp.last_name,'') as last_name,
                COALESCE(emp.middle_name,'') as middle_name,
                CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,
                
                
                COALESCE(peidt.IncomeDeductionTypeID,0) as IncomeDeductionTypeID,
                COALESCE(pidt.Code,'') as IncomeDeductionTypeCode,
                COALESCE(pidt.Name,'') as IncomeDeductionTypeName,
                COALESCE(pidt.Category,'') as Category,

                COALESCE(peidt.VoucherNo,'') as VoucherNo,
                COALESCE(peidt.DateIssue,'') as DateIssue,
                COALESCE(peidt.PaymentStartDate,'') as DateStartPayment,

                COALESCE(peidt.InterestAmount,0) as InterestAmount,
                COALESCE(peidt.AmortizationAmount,0) as AmortizationAmount,
                COALESCE(peidt.IncomeDeductionAmount,0) as IncomeDeductionAmount,
                COALESCE(peidt.MonthsToPay,0) as MonthsToPay,
                COALESCE(peidt.TotalIncomeDeductionAmount,0) as TotalIncomeDeductionAmount,
                                                
                FORMAT(peidt.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(peidt.DateIssue,'MM/dd/yyyy') as DateIssueFormat,                
                FORMAT(peidt.PaymentStartDate,'MM/dd/yyyy') as DateStartPaymentFormat, 

                COALESCE(peidt.Remarks,'') as Remarks,
                COALESCE(peidt.Status,'') as Status,
                COALESCE(peidt.IsUploadError,0) as IsUploadError
            ")

    ->where("peidt.ID",$EmployeeIncomeDeductionTransaction_ID)
    ->first();

    return $info;

}

 public function doRemoveDuplicateIncomeDeductionTempTransaction($tempID){
        
        $chkEmployeeID=0;        
        $chkIncomeDeductionTypeID=0;
        $chkIncomeDeductionAmount=0;

        $info=$this->getEmployeeIncomeDeductionTempTransactionInfo($tempID);

        if(isset($info)>0){
            
            $chkEmployeeID=$info->EmployeeID;            
            $chkIncomeDeductionTypeID=$info->IncomeDeductionTypeID;
            $chkIncomeDeductionAmount=$info->IncomeDeductionAmount;

            DB::table('payroll_employee_income_deduction_temp')->where('ID',$tempID)->delete();

            $this->checkUploadedExcelRecordHasDuplicateRecord($chkEmployeeID,$chkIncomeDeductionTypeID,$chkIncomeDeductionAmount);

        }
    }
  
  //check if uploaded excel has duplicate payroll id and duplicate employee with duplicate amount
    public function checkUploadedExcelRecordHasDuplicateRecord($EmployeeID,$IncomeDeductionTypeID, $IncomeDeductionAmount){
      
      if($EmployeeID>0 && $IncomeDeductionTypeID>0){

            $info = DB::table('payroll_employee_income_deduction_temp')               
               ->where("EmployeeID",$EmployeeID)
               ->where("IncomeDeductionTypeID",$IncomeDeductionTypeID)               
               ->where("IncomeDeductionAmount",$IncomeDeductionAmount)
               ->where("Status",'Pending')
               ->get();
        
                if(count($info)>=2){

                    DB::table('payroll_employee_income_deduction_temp')                        
                        ->where("EmployeeID",$EmployeeID)
                        ->where("IncomeDeductionTypeID",$IncomeDeductionTypeID)
                        ->where("IncomeDeductionAmount",$IncomeDeductionAmount)                        
                        ->where("Status",'Pending')

                        ->update([
                            'IsUploadError' => 2
                          ]);  
              }else{

                     DB::table('payroll_employee_income_deduction_temp')                        
                        ->where("EmployeeID",$EmployeeID)
                        ->where("IncomeDeductionTypeID",$IncomeDeductionTypeID)
                        ->where("IncomeDeductionAmount",$IncomeDeductionAmount)                        
                        ->where("Status",'Pending')

                        ->update([
                            'IsUploadError' => 0
                          ]);  
              }
        }
    }

}

