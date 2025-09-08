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

class EmployeeAdvance extends Model
{

  public function getEmployeeAdvanceTransactionList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $query = DB::table('payroll_employee_advance_transaction as palt')
            ->join('users as emp', 'emp.id', '=', 'palt.EmployeeID')
            ->join('payroll_period_schedule as pp', 'pp.ID', '=', 'palt.PayrollPeriodID')   
            ->selectraw("
                palt.ID,                
                COALESCE(palt.TransactionDate,'') as TransactionDate,

                COALESCE(palt.Year,'') as Year,
                COALESCE(palt.PayrollPeriodID,0) as PayrollPeriodID,
                COALESCE(palt.PayrollPeriodCode,'') as PayrollPeriodCode,

                COALESCE(pp.StartDate,'') as StartDate,
                COALESCE(pp.EndDate,'') as EndDate,

               FORMAT(pp.StartDate,'MM/dd/yyyy') as StartDateFormat,
               FORMAT(pp.EndDate,'MM/dd/yyyy') as EndDateFormat,

                COALESCE(palt.EmployeeID,0) as EmployeeID,
                COALESCE(palt.EmployeeNumber,'') as EmployeeNumber,
                CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

                COALESCE(palt.CutOffID,0) as CutOff,
                COALESCE(palt.VoucherNo,'') as VoucherNo,
                COALESCE(palt.DateIssue,'') as DateIssue,

                COALESCE(palt.AdvanceAmount,0) as AdvanceAmount,
                COALESCE(palt.TotalAdvanceAmount,0) as TotalAdvanceAmount,
                COALESCE(palt.InterestAmount,0) as InterestAmount,
                COALESCE(palt.AmortizationAmount,0) as AmortizationAmount,
                
                COALESCE(palt.PaymentStartDate,'') as PaymentStartDate,

                COALESCE(palt.Remarks,'') as Remarks,
                COALESCE(palt.Status,'') as Status,

                COALESCE(palt.IsPosted,0) as IsPosted,
                COALESCE(palt.IsClosed,0) as IsClosed,      
                COALESCE(palt.IsUploadError,0) as IsUploadError,          

                FORMAT(palt.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(palt.PaymentStartDate,'MM/dd/yyyy') as PaymentStartDateFormat,

                COALESCE(palt.CreatedByID,0) as CreatedByID,
                COALESCE(palt.DateTimeCreated,'') as DateTimeCreated,
                COALESCE(palt.CancelledByID,0) as CancelledByID,
                COALESCE(palt.DateTimeCancelled,'') as DateTimeCancelled,
                COALESCE(palt.ApprovedByID,0) as ApprovedByID,
                COALESCE(palt.DateTimeApproved,'') as DateTimeApproved
            ");

     if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',                        
                        COALESCE(palt.TransactionDate,''),
                        COALESCE(emp.employee_number,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,''),
                        COALESCE(emp.middle_name,''),
                        COALESCE(palt.VoucherNo,''),
                        COALESCE(palt.Remarks,''),
                        COALESCE(palt.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("palt.ID","DESC");    
    $list = $query->get();

    return $list;

}

public function getEmployeeAdvanceTransactionInfo($EmployeeAdvanceTransaction_ID){

     $info = DB::table('payroll_employee_advance_transaction as palt')
      ->join('users as emp', 'emp.id', '=', 'palt.EmployeeID')   
      ->join('payroll_period_schedule as pp', 'pp.ID', '=', 'palt.PayrollPeriodID')  
         ->join('payroll_department as dept', 'dept.ID', '=', 'emp.department_id')
      ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
      ->leftjoin('payroll_section as sec', 'sec.id', '=', 'emp.section_id')  
            ->selectraw("
                palt.ID,                
                COALESCE(palt.TransactionDate,'') as TransactionDate,

                COALESCE(palt.Year,'') as Year,
                COALESCE(palt.PayrollPeriodID,0) as PayrollPeriodID,
                COALESCE(palt.PayrollPeriodCode,'') as PayrollPeriodCode,

                COALESCE(pp.StartDate,'') as StartDate,
                COALESCE(pp.EndDate,'') as EndDate,

               FORMAT(pp.StartDate,'MM/dd/yyyy') as StartDateFormat,
               FORMAT(pp.EndDate,'MM/dd/yyyy') as EndDateFormat,

                COALESCE(palt.EmployeeID,'') as EmployeeID,
                COALESCE(palt.EmployeeNumber,'') as EmployeeNumber,
                CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

                COALESCE(palt.CutOffID,0) as CutOff,
                COALESCE(palt.VoucherNo,'') as VoucherNo,
                COALESCE(palt.DateIssue,'') as DateIssue,

                COALESCE(palt.AdvanceAmount,0) as AdvanceAmount,
                COALESCE(palt.TotalAdvanceAmount,0) as TotalAdvanceAmount,
                COALESCE(palt.InterestAmount,0) as InterestAmount,
                COALESCE(palt.AmortizationAmount,0) as AmortizationAmount,

                COALESCE(palt.PaymentStartDate,'') as PaymentStartDate,

                COALESCE(dept.DivisionID,0) as DivisionID,
                COALESCE(div.Division,'') as Division,

                COALESCE(emp.department_id,0) as DepartmentID,
                COALESCE(dept.Department,'') as Department,

                COALESCE(sec.ID,0) as SectionID,
                COALESCE(sec.Section,'') as Section,   

                COALESCE(palt.Remarks,'') as Remarks,
                COALESCE(palt.Status,'') as Status,

                COALESCE(palt.IsPosted,0) as IsPosted,
                COALESCE(palt.IsClosed,0) as IsClosed,  

                FORMAT(palt.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(palt.DateIssue,'MM/dd/yyyy') as DateIssueFormat,
                FORMAT(palt.PaymentStartDate,'MM/dd/yyyy') as PaymentStartDateFormat,

                COALESCE(palt.CreatedByID,0) as CreatedByID,
                COALESCE(palt.DateTimeCreated,'') as DateTimeCreated,
                COALESCE(palt.CancelledByID,0) as CancelledByID,
                COALESCE(palt.DateTimeCancelled,'') as DateTimeCancelled,
                COALESCE(palt.ApprovedByID,0) as ApprovedByID,
                COALESCE(palt.DateTimeApproved,'') as DateTimeApproved
            ")

    ->where("palt.ID",$EmployeeAdvanceTransaction_ID)
    ->first();

    return $info;

}

public function doSaveEmployeeAdvanceTransaction($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");

    $EmployeeAdvanceTransID=$data["EmployeeAdvanceTransID"];

    $Year=$data["Year"];
    $TransDate=$data["TransDate"];

    $EmployeeID=$data["EmpID"];
    $EmployeeNo=$data["EmpNo"];

    $CutOffID=$data["CutOffID"];

    $PayrollID=$data["PayrollID"];
    $PayrollCode=$data["PayrollCode"];

    $ReferenceNo=trim($data["ReferenceNo"]);

    $DateIssued=$data["DateIssued"];
    $DateStartPayment=$data["DateStartPayment"];
    $AmortizationAmnt=$data["AmortizationAmnt"];

    $InterestAmnt=$data["InterestAmnt"];
    $AdvanceAmnt=$data["AdvanceAmnt"];
    $TotalAdvanceAmnt=$data["TotalAdvanceAmnt"];

    $TotalPayment=$data["TotalPayment"];
    $TotalBalance=$data["TotalBalance"];

    $Remarks=$data["Remarks"];
    $Status=$data["Status"];

    $TransDate=date('Y-m-d',strtotime($TransDate)); 
    $DateIssued=date('Y-m-d',strtotime($DateIssued)); 
    $DateStartPayment=date('Y-m-d',strtotime($DateStartPayment));   

    if($EmployeeAdvanceTransID > 0){

        DB::table('payroll_employee_advance_transaction')
            ->where('ID',$EmployeeAdvanceTransID)
            ->update([
                'TransactionDate' => $TransDate,
                'PayrollPeriodID' => $PayrollID,
                'PayrollPeriodCode' => $PayrollCode,
                'Year' => $Year,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,
                'CutOffID' => $CutOffID,
                'VoucherNo' => $ReferenceNo,
                'DateIssue' => $DateIssued,
                'AdvanceAmount' => $AdvanceAmnt,
                'TotalAdvanceAmount' => $TotalAdvanceAmnt,
                'InterestAmount' => $InterestAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'PaymentStartDate' => $DateStartPayment,
                'Remarks' => $Remarks,
                'Status' => $Status,
                'UpdatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' => $TODAY
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $EmployeeAdvanceTransID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee Advance Transaction";
        $logData['TransType'] = "Update Employee Advance Transaction Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{
        
        $EmployeeAdvanceTransID = DB::table('payroll_employee_advance_transaction')
            ->insertGetId([               
                'TransactionDate' => $TransDate,
                'PayrollPeriodID' => $PayrollID,
                'PayrollPeriodCode' => $PayrollCode,
                'Year' => $Year,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,
                'CutOffID' => $CutOffID,
                'VoucherNo' => $ReferenceNo,
                'DateIssue' => $DateIssued,
                'AdvanceAmount' => $AdvanceAmnt,
                'TotalAdvanceAmount' => $TotalAdvanceAmnt,
                'InterestAmount' => $InterestAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'PaymentStartDate' => $DateStartPayment,
                'Remarks' => $Remarks,
                'Status' => $Status,    
                'CreatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' => $TODAY
              ]); 
            
        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $EmployeeAdvanceTransID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee Advance Transaction";
        $logData['TransType'] = "New Employee Advance Transaction Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $EmployeeAdvanceTransID;

    }

public function doCheckExistingEmployeeAdvance($param){

    $IsExist = false;
    $EmployeeID=$param['EmpID'];

      $EmployeeAdvance = DB::table('payroll_employee_advance_transaction as palt')
          ->where('palt.EmployeeID','=',$EmployeeID)
          ->where('palt.Status','=','Pending')
          ->get();

    if(count($EmployeeAdvance)>0){
        $IsExist = true;
    }

      return $IsExist;
    }
 
//TEMP TABLE  
  public function getEmployeeAdvanceTransactionTempList($param){

    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_employee_advance_temp as peat')
            ->leftjoin('users as emp', 'emp.id', '=', 'peat.EmployeeID')
            ->leftjoin('payroll_period_schedule as pp', 'pp.ID', '=', 'peat.PayrollPeriodID')   
            ->selectraw("
                peat.ID,
                COALESCE(peat.TransactionDate,'') as TransactionDate,

                COALESCE(peat.Year,'') as Year,
                COALESCE(peat.PayrollPeriodID,0) as PayrollPeriodID,
                COALESCE(peat.PayrollPeriodCode,'') as PayrollPeriodCode,

                COALESCE(pp.StartDate,'') as StartDate,
                COALESCE(pp.EndDate,'') as EndDate,

               FORMAT(pp.StartDate,'MM/dd/yyyy') as StartDateFormat,
               FORMAT(pp.EndDate,'MM/dd/yyyy') as EndDateFormat,

                COALESCE(peat.EmployeeID,0) as EmployeeID,
                COALESCE(peat.EmployeeNumber,'') as EmployeeNumber,
                CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

                COALESCE(peat.CutOffID,0) as CutOff,
                COALESCE(peat.VoucherNo,'') as VoucherNo,
                COALESCE(peat.DateIssue,'') as DateIssue,
                COALESCE(peat.AdvanceAmount,0) as AdvanceAmount,
                COALESCE(peat.TotalAdvanceAmount,0) as TotalAdvanceAmount,
                COALESCE(peat.InterestAmount,0) as InterestAmount,
                COALESCE(peat.AmortizationAmount,0) as AmortizationAmount,
                COALESCE(peat.PaymentStartDate,'') as PaymentStartDate,

                FORMAT(peat.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(peat.DateIssue,'MM/dd/yyyy') as DateIssueFormat,
                FORMAT(peat.PaymentStartDate,'MM/dd/yyyy') as PaymentStartDateFormat,

                COALESCE(peat.Remarks,'') as Remarks,
                COALESCE(peat.Status,'') as Status,
    
                COALESCE(peat.IsUploadError,0) as IsUploadError
            ");

    if($Status!=''){
        $query->where("peat.Status",$Status);
    }    

    $query->where("peat.UploadedByID",Session::get('ADMIN_USER_ID'));   

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("peat.IsUploadError","DESC");
    $query->orderBy("peat.PayrollPeriodCode","ASC");
    $list = $query->get();

    return $list;

}

public function doSaveAdvanceTempTransactionPerBatch($data){

    $Employee = new Employee();   
    $PayrollPeriod= new PayrollPeriod();   
    $EmployeeAdvance = new EmployeeAdvance();

    $TransDate = date("Y-m-d");

    $TODAY = date("Y-m-d H:i:s");
    $AdvanceTempDataItems = $data['AdvanceTempDataItems'];

    if(!empty($AdvanceTempDataItems)){

      for($x=0; $x< count($AdvanceTempDataItems); $x++) {
            
        $AdvanceTransID = $AdvanceTempDataItems[$x]["AdvanceTransID"];
        $Year = $AdvanceTempDataItems[$x]["Year"];

        $PayrollCode = $AdvanceTempDataItems[$x]["PayrollCode"];
        $PayrollID=0;
        if($PayrollCode!=''){
            $payroll_info = $PayrollPeriod->getPayrollPeriodScheduleInfoByCode($PayrollCode);
            if(isset($payroll_info)>0){
            $PayrollID=$payroll_info->ID;
        }
        }
        
        $EmployeeNo = $AdvanceTempDataItems[$x]["EmpNo"];
        $EmployeeID=0;
        if($EmployeeNo!=''){
            $emp_info = $Employee->getEmployeeInfo('ByEmployeeNo',$EmployeeNo);
            if(isset($emp_info)>0){
               $EmployeeID=$emp_info->employee_id;
            }  

        }

        $CutOff = trim($AdvanceTempDataItems[$x]["CutOff"]);        
        $CutOffID=0;
        
        if($CutOff!=''){            
            if($CutOff=='1ST HALF'){
                $CutOffID=1;
            }elseif($CutOff=='2ND HALF'){              
               $CutOffID=2;
            }else{
               $CutOffID=3;
            }  
        }
                
        $DateIssued = $AdvanceTempDataItems[$x]["DateIssued"];
        $DateIssued=date('Y-m-d',strtotime($DateIssued)); 

        $ReferenceNo = $AdvanceTempDataItems[$x]["ReferenceNo"];

        $AdvanceAmnt = $AdvanceTempDataItems[$x]["AdvanceAmnt"];
        $TotalAdvanceAmnt = $AdvanceTempDataItems[$x]["TotalAdvanceAmnt"];
        $InterestAmnt = $AdvanceTempDataItems[$x]["InterestAmnt"];
        $AmortizationAmnt = $AdvanceTempDataItems[$x]["AmortizationAmnt"];

        $DateStartPayment = $AdvanceTempDataItems[$x]["DateStartPayment"];
        $DateStartPayment=date('Y-m-d',strtotime($DateStartPayment)); 
        
        $Remarks =$AdvanceTempDataItems[$x]["Remarks"];
        $Status =$AdvanceTempDataItems[$x]["Status"];
                
        $IsUploadError=0;
        if($PayrollID==0 || $EmployeeID==0){
             $IsUploadError=1;
        }

        $AdvanceTransID = DB::table('payroll_employee_advance_temp')
              ->insertGetId([
                'TransactionDate' => $TransDate,
                'PayrollPeriodID' => $PayrollID,
                'PayrollPeriodCode' => $PayrollCode,
                'Year' => $Year,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,                
                'CutOffID' => $CutOffID,                                            
                'VoucherNo' => $ReferenceNo,
                'DateIssue' => $DateIssued,
                'AdvanceAmount' => $AdvanceAmnt,                
                'TotalAdvanceAmount' => $TotalAdvanceAmnt,                
                'InterestAmount' => $InterestAmnt,                
                'AmortizationAmount' => $AmortizationAmnt,                
                'PaymentStartDate' => $DateStartPayment,
                'Remarks' => $Remarks,
                'Status' => $Status,
                'IsUploadError'=> $IsUploadError,
                'UploadedByID'=> Session::get('ADMIN_USER_ID'),    
                'DateTimeUploaded'=> $TODAY      
              ]); 


          $this->checkUploadedExcelRecordHasDuplicateRecord($PayrollID,$Year,$EmployeeID);            
      }
    }
    return "Success";
}

public function doSaveEmployeeAdvanceTempTransaction($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $TODAY_UPLOADED_TRANS_DATE = date("Y-m-d");

    $EmployeeAdvanceTransID=$data["EmployeeAdvanceTransID"];

    $Year=$data["Year"];
    $TransDate=$data["TransDate"];

    $EmployeeID=$data["EmpID"];
    $EmployeeNo=$data["EmpNo"];

    $CutOffID=$data["CutOffID"];
    
    $PayrollID=$data["PayrollID"];
    $PayrollCode=$data["PayrollCode"];

    $ReferenceNo=trim($data["ReferenceNo"]);

    $DateIssued=$data["DateIssued"];
    $DateStartPayment=$data["DateStartPayment"];

    $AmortizationAmnt=$data["AmortizationAmnt"];
    $InterestAmnt=$data["InterestAmnt"];
    $AdvanceAmnt=$data["AdvanceAmnt"];
    $TotalAdvanceAmnt=$data["TotalAdvanceAmnt"];

    $TotalPayment=$data["TotalPayment"];
    $TotalBalance=$data["TotalBalance"];
    
    $Remarks=$data["Remarks"];
    $Status=$data["Status"];

    $IsUploaded=$data["IsUploaded"];

    $DateIssued=date('Y-m-d',strtotime($DateIssued)); 
    $DateStartPayment=date('Y-m-d',strtotime($DateStartPayment));  

    if($IsUploaded==1){
        $TransDate=$TODAY_UPLOADED_TRANS_DATE;
    }else{
        $TransDate=date('Y-m-d',strtotime($TransDate)); 
    }


    if($EmployeeAdvanceTransID > 0){

        DB::table('payroll_employee_advance_temp')
            ->where('ID',$EmployeeAdvanceTransID)
            ->update([
                'TransactionDate' => $TransDate,
                'PayrollPeriodID' => $PayrollID,
                'PayrollPeriodCode' => $PayrollCode,
                'Year' => $Year,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,
                'CutOffID' => $CutOffID,
                'VoucherNo' => $ReferenceNo,
                'DateIssue' => $DateIssued,
                'AdvanceAmount' => $AdvanceAmnt,
                'TotalAdvanceAmount' => $TotalAdvanceAmnt,                
                'InterestAmount' => $InterestAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'PaymentStartDate' => $DateStartPayment,
                'Remarks' => $Remarks,
                'Status' => $Status          
            ]);

        $this->checkUploadedExcelRecordHasDuplicateRecord($PayrollID,$Year,$EmployeeID);
    
    }else{

        $IsUploadError=0;

        if($PayrollID<=0 || $EmployeeID<=0){
             $IsUploadError=1;
        }
   
        $EmployeeAdvanceTransID = DB::table('payroll_employee_advance_temp')
            ->insertGetId([
                'TransactionDate' => $TransDate,
                'PayrollPeriodID' => $PayrollID,
                'PayrollPeriodCode' => $PayrollCode,
                'Year' => $Year,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,
                'CutOffID' => $CutOffID,
                'VoucherNo' => $ReferenceNo,
                'DateIssue' => $DateIssued,
                'AdvanceAmount' => $AdvanceAmnt,
                'TotalAdvanceAmount' => $TotalAdvanceAmnt,                
                'InterestAmount' => $InterestAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'PaymentStartDate' => $DateStartPayment,
                'Remarks' => $Remarks,
                'Status' => $Status,
                'IsUploadError'=> $IsUploadError        
              ]); 

        $this->checkUploadedExcelRecordHasDuplicateRecord($PayrollID,$Year,$EmployeeID);

    }
    return $EmployeeAdvanceTransID;
  }

public function getEmployeeAdvanceTempTransactionInfo($EmployeeAdvanceTrans_ID){

     $info = DB::table('payroll_employee_advance_temp as peat')
      ->leftjoin('users as emp', 'emp.id', '=', 'peat.EmployeeID')   
      ->leftjoin('payroll_period_schedule as pp', 'pp.ID', '=', 'peat.PayrollPeriodID')
            ->selectraw("
              peat.ID,
                COALESCE(peat.TransactionDate,'') as TransactionDate,

                COALESCE(peat.Year,'') as Year,
                COALESCE(peat.PayrollPeriodID,0) as PayrollPeriodID,
                COALESCE(peat.PayrollPeriodCode,'') as PayrollPeriodCode,

                COALESCE(pp.StartDate,'') as StartDate,
                COALESCE(pp.EndDate,'') as EndDate,

               FORMAT(pp.StartDate,'MM/dd/yyyy') as StartDateFormat,
               FORMAT(pp.EndDate,'MM/dd/yyyy') as EndDateFormat,

                COALESCE(peat.EmployeeID,0) as EmployeeID,
                COALESCE(peat.EmployeeNumber,'') as EmployeeNumber,
                CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

                COALESCE(peat.CutOffID,0) as CutOff,
                COALESCE(peat.VoucherNo,'') as VoucherNo,
                COALESCE(peat.DateIssue,'') as DateIssue,
                COALESCE(peat.AdvanceAmount,0) as AdvanceAmount,
                COALESCE(peat.TotalAdvanceAmount,0) as TotalAdvanceAmount,
                COALESCE(peat.InterestAmount,0) as InterestAmount,
                COALESCE(peat.AmortizationAmount,0) as AmortizationAmount,
                COALESCE(peat.PaymentStartDate,'') as PaymentStartDate,
   
                FORMAT(peat.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(peat.DateIssue,'MM/dd/yyyy') as DateIssueFormat,
                FORMAT(peat.PaymentStartDate,'MM/dd/yyyy') as PaymentStartDateFormat,

                COALESCE(peat.Remarks,'') as Remarks,
                COALESCE(peat.Status,'') as Status,
  
                COALESCE(peat.IsUploadError,0) as IsUploadError     
            ")

    ->where("peat.ID",$EmployeeAdvanceTrans_ID)
    ->first();

    return $info;

}

public function doSaveUploadFinalEmployeeAdvanceTransaction($data){

    $hasDataError=false;
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

    $info_list=$this->getEmployeeAdvanceTransactionTempList($data);

    //check if has data error status
    if(count($info_list)>0){
        foreach($info_list as $list){
            if($list->IsUploadError>0){
              $hasDataError=true;  
            }
        } 
    }

    if($hasDataError){
        $RetVal['Response'] = "Failed";
        $RetVal['ResponseMessage'] = "Sorry! Employee advances data cannot be saved. Data still has issues that need to be resolved.";
    }else{

         DB::statement("insert into payroll_employee_advance_transaction(TransactionDate,Year,PayrollPeriodID,PayrollPeriodCode,EmployeeID,EmployeeNumber,CutOffID,VoucherNo,DateIssue,AdvanceAmount,TotalAdvanceAmount,InterestAmount,AmortizationAmount,PaymentStartDate,Remarks,Status,CreatedByID,DateTimeCreated) 
         Select TransactionDate,Year,PayrollPeriodID,PayrollPeriodCode,EmployeeID,EmployeeNumber,CutOffID,VoucherNo,DateIssue,AdvanceAmount,TotalAdvanceAmount,InterestAmount,AmortizationAmount,PaymentStartDate,Remarks,'Approved',UploadedByID,DateTimeUploaded from payroll_employee_advance_temp where UploadedByID=?" ,[Session::get('ADMIN_USER_ID')]);
            
        //Clear temp table where currrent login Admin
        DB::table('payroll_employee_advance_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->delete();   

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Employee advances has saved successfully.";
        
    }

    return $RetVal;
}

 public function doRemoveDuplicateAdvanceTempTransaction($tempID){

        $chkYear='';
        $chkEmployeeID=0;
        $chkPayrollID=0;

        $info=$this->getEmployeeAdvanceTempTransactionInfo($tempID);

        if(isset($info)>0){

            $chkYear=$info->Year;
            $chkEmployeeID=$info->EmployeeID;
            $chkPayrollID=$info->PayrollPeriodID;

            DB::table('payroll_employee_advance_temp')->where('ID',$tempID)->delete();
            $this->checkUploadedExcelRecordHasDuplicateRecord($chkPayrollID,$chkYear,$chkEmployeeID);

        }
    }
  
  //check if uploaded excel has duplicate payroll id and duplicate employee
    public function checkUploadedExcelRecordHasDuplicateRecord($PayrollPeriodID,$Year,$EmployeeID){
      
      if($PayrollPeriodID>0 && $EmployeeID>0){

            $info = DB::table('payroll_employee_advance_temp')
               ->where("PayrollPeriodID",$PayrollPeriodID)
               ->where("EmployeeID",$EmployeeID)
               ->where("Year",$Year)
               ->where("Status",'Pending')
               ->get();
        
                if(count($info)>=2){

                    DB::table('payroll_employee_advance_temp')
                        ->where("PayrollPeriodID",$PayrollPeriodID)
                        ->where("EmployeeID",$EmployeeID)
                        ->where("Year",$Year)
                        ->where("Status",'Pending')

                        ->update([
                            'IsUploadError' => 2
                          ]);  
              }else{

                     DB::table('payroll_employee_advance_temp')
                        ->where("PayrollPeriodID",$PayrollPeriodID)
                        ->where("EmployeeID",$EmployeeID)
                        ->where("Year",$Year)
                        ->where("Status",'Pending')

                        ->update([
                            'IsUploadError' => 0
                          ]);  
              }
        }
    }

}

