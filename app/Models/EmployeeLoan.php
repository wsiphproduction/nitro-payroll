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

class EmployeeLoan extends Model
{
 
  public function getEmployeeLoanTransactionList($param){

    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $SearchText = trim($param['SearchText']);

    $query = DB::table('payroll_employee_loan_transaction as pelt')      
      ->join('users as emp', 'emp.id', '=', 'pelt.EmployeeID')   
      ->leftjoin('payroll_loan_type as plt', 'plt.ID', '=', 'pelt.LoanTypeID') 
            ->selectraw("
                pelt.ID,                
                COALESCE(pelt.TransactionDate,'') as TransactionDate,

                COALESCE(pelt.EmployeeID,'') as EmployeeID,
                COALESCE(emp.employee_number,'') as EmployeeNumber,
                CONCAT(COALESCE(emp.last_name,''), ', ', COALESCE(emp.first_name,''), ' ' , COALESCE(emp.middle_name,'')) as FullName,

                COALESCE(pelt.CutOffID,0) as CutOff,
                COALESCE(pelt.LoanTypeID,0) as LoanTypeID,
                COALESCE(plt.Code,'') as LoanTypeCode,
                COALESCE(plt.Name,'') as LoanTypeName,

                COALESCE(pelt.VoucherNo,'') as VoucherNo,
                COALESCE(pelt.DateIssue,'') as DateIssue,

                COALESCE(pelt.DelayedPaymentOption,'') as DelayedPaymentOption,
                
                COALESCE(pelt.LoanAmount,0) as LoanAmount,
                COALESCE(pelt.TotalLoanAmount,0) as TotalLoanAmount,
                COALESCE(pelt.InterestAmount,0) as InterestAmount,
                COALESCE(pelt.AmortizationAmount,0) as AmortizationAmount,
                COALESCE(pelt.MonthsToPay,0) as MonthsToPay,
                COALESCE(pelt.TotalPayment,0) as TotalPayment,
                
                COALESCE(pelt.PaymentStartDate,'') as PaymentStartDate,
                
                COALESCE(pelt.Remarks,'') as Remarks,
                COALESCE(pelt.Status,'') as Status,

                COALESCE(pelt.IsPosted,0) as IsPosted,
                COALESCE(pelt.IsClosed,0) as IsClosed,

                FORMAT(pelt.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(pelt.DateIssue,'MM/dd/yyyy') as DateIssueFormat,
                FORMAT(pelt.PaymentStartDate,'MM/dd/yyyy') as PaymentStartDateFormat,

                COALESCE(pelt.CreatedByID,0) as CreatedByID,
                COALESCE(pelt.DateTimeCreated,'') as DateTimeCreated,
                COALESCE(pelt.CancelledByID,0) as CancelledByID,
                COALESCE(pelt.DateTimeCancelled,'') as DateTimeCancelled,
                COALESCE(pelt.ApprovedByID,0) as ApprovedByID,
                COALESCE(pelt.DateTimeApproved,'') as DateTimeApproved,

                COALESCE(pelt.TotalLoanAmount - pelt.TotalPayment,0) as BalanceAmount

            ");


    if($Status!=''){

        if($Status=='Pending'){
           $query->where("pelt.status",'Pending');    
        }else if($Status=='Approved'){
          $query->where("pelt.status",'Approved');    
        }else if($Status=='Cancelled'){
          $query->where("pelt.status",'Cancelled');    
        }else if($Status=='Paid'){
             $query->where("pelt.Status","<>","Cancelled");  
             $query->whereRaw("COALESCE(pelt.TotalLoanAmount - pelt.TotalPayment,0)  < 0");  
        }else if($Status=='Balance'){
             $query->where("pelt.Status","<>","Cancelled");   
             $query->whereRaw("COALESCE(pelt.TotalLoanAmount - pelt.TotalPayment,0) > 0");  
        }else{
            $arStatus = explode("|",$Status);
            if($arStatus[0] == "Location"){
             $query->where("emp.company_branch_id",$arStatus[1]);  
            }else if($arStatus[0] == "Site"){
             $query->where("emp.company_branch_site_id",$arStatus[1]);  
            }
        }
    }

    if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',                        
                        COALESCE(pelt.TransactionDate,''),
                        COALESCE(emp.employee_number,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,''),
                        COALESCE(emp.middle_name,''),
                        COALESCE(plt.Code,''),
                        COALESCE(plt.Name,''),
                        COALESCE(pelt.VoucherNo,''),
                        COALESCE(pelt.Remarks,''),
                        COALESCE(pelt.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("FullName","ASC");
    $query->orderBy("pelt.ID","DESC");
    $query->orderBy("pelt.Status","DESC");

    $list = $query->get();

    return $list;

}

  public function getEmployeeLoanTransactionListByEmployeeID($param){

    $SearchText = trim($param['SearchText']);
    $EmployeeID = $param['EmployeeID'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $query = DB::table('payroll_employee_loan_transaction as pelt')      
      ->join('payroll_loan_type as plt', 'plt.ID', '=', 'pelt.LoanTypeID') 
      ->join('users as emp', 'emp.id', '=', 'pelt.EmployeeID')   
            ->selectraw("
                pelt.ID,                
                COALESCE(pelt.TransactionDate,'') as TransactionDate,

                COALESCE(pelt.EmployeeID,'') as EmployeeID,
                COALESCE(emp.employee_number,'') as EmployeeNumber,
                CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

                COALESCE(pelt.CutOffID,0) as CutOff,
                COALESCE(pelt.LoanTypeID,0) as LoanTypeID,
                COALESCE(plt.Code,'') as LoanTypeCode,
                COALESCE(plt.Name,'') as LoanTypeName,

                COALESCE(pelt.VoucherNo,'') as VoucherNo,                
                COALESCE(pelt.DelayedPaymentOption,'') as DelayedPaymentOption,

                COALESCE(pelt.DateIssue,'') as DateIssue,
                COALESCE(pelt.PaymentStartDate,'') as PaymentStartDate,

                COALESCE(pelt.LoanAmount,0) as LoanAmount,
                COALESCE(pelt.TotalLoanAmount,0) as TotalLoanAmount,
                COALESCE(pelt.InterestAmount,0) as InterestAmount,
                COALESCE(pelt.AmortizationAmount,0) as AmortizationAmount,
                COALESCE(pelt.MonthsToPay,0) as MonthsToPay,
                COALESCE(pelt.TotalPayment,0) as TotalPayment,
                                
                COALESCE(pelt.Remarks,'') as Remarks,
                COALESCE(pelt.Status,'') as Status,

                COALESCE(pelt.IsPosted,0) as IsPosted,
                COALESCE(pelt.IsClosed,0) as IsClosed,

                FORMAT(pelt.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(pelt.DateIssue,'MM/dd/yyyy') as DateIssueFormat,
                FORMAT(pelt.PaymentStartDate,'MM/dd/yyyy') as PaymentStartDateFormat,

                COALESCE(pelt.CreatedByID,0) as CreatedByID,
                COALESCE(pelt.DateTimeCreated,'') as DateTimeCreated,
                COALESCE(pelt.CancelledByID,0) as CancelledByID,
                COALESCE(pelt.DateTimeCancelled,'') as DateTimeCancelled,
                COALESCE(pelt.ApprovedByID,0) as ApprovedByID,
                COALESCE(pelt.DateTimeApproved,'') as DateTimeApproved
            ");

      $query->where("pelt.EmployeeID",$EmployeeID);       

     if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',                        
                        COALESCE(pelt.TransactionDate,''),
                        COALESCE(emp.employee_number,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,''),
                        COALESCE(emp.middle_name,''),
                        COALESCE(plt.Code,''),
                        COALESCE(plt.Name,''),
                        COALESCE(pelt.VoucherNo,''),
                        COALESCE(pelt.Remarks,''),
                        COALESCE(pelt.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("pelt.ID","DESC");
    $query->orderBy("plt.Code","ASC");
    $list = $query->get();

    return $list;

}

public function getEmployeeLoanPaymentManualTransactionList($param){

    $SearchText = trim($param['SearchText']);
    $LoanTransID = $param['LoanTransID'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $query = DB::table('payroll_employee_loan_set_manual_payment as pelsmp')
      ->join('users as emp', 'emp.id', '=', 'pelsmp.EmployeeID')   
      ->join('payroll_employee_loan_transaction as pelt', 'pelt.ID', '=', 'pelsmp.LaonTransactionID')   

            ->selectraw("
                pelsmp.ID,                
                COALESCE(pelsmp.PaymentDate,'') as PaymentDate,
                COALESCE(pelsmp.Amount,0) as Amount,
                
                COALESCE(pelsmp.EmployeeID,'') as EmployeeID,
                COALESCE(pelt.EmployeeNumber,'') as EmployeeNumber,
                CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

                COALESCE(pelt.LoanTypeID,0) as LoanTypeID,    
                COALESCE(pelt.LoanTypeCode,'') as LoanTypeCode,
                COALESCE(pelt.CutOffID,0) as CutOff,
                                                
                COALESCE(pelt.VoucherNo,'') as VoucherNo,
                COALESCE(pelt.DateIssue,'') as DateIssue,

                COALESCE(pelt.LoanAmount,0) as LoanAmount,
                COALESCE(pelt.TotalLoanAmount,0) as TotalLoanAmount,
                COALESCE(pelt.InterestAmount,0) as InterestAmount,
                COALESCE(pelt.AmortizationAmount,0) as AmortizationAmount,

                COALESCE(pelt.MonthsToPay,0) as MonthsToPay,
                COALESCE(pelt.TotalPayment,0) as TotalPayment,
                                                      
                FORMAT(pelt.DateIssue,'MM/dd/yyyy') as DateIssueFormat,
                FORMAT(pelsmp.PaymentDate,'MM/dd/yyyy') as PaymentDateFormat

            ");
  
    if($LoanTransID>0){
        $query->where("pelsmp.LaonTransactionID",$LoanTransID);           
    }

     if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',                        
                        COALESCE(pelt.TransactionDate,''),
                        COALESCE(emp.employee_number,''),
                        COALESCE(emp.first_name,''),
                        COALESCE(emp.last_name,''),
                        COALESCE(emp.middle_name,''),
                        COALESCE(pelt.LoanTypeCode,''),                        
                        COALESCE(pelt.VoucherNo,''),
                        COALESCE(pelt.Remarks,''),
                        COALESCE(pelt.Status,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("pelsmp.ID","DESC");
    $list = $query->get();

    return $list;

}

public function getEmployeeLoanPaymentLedgerList($param){

    $SearchText = trim($param['SearchText']);
    $LoanTransID = $param['LoanTransID'];
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $query = DB::table('payroll_loan_payment_ledger as paylgr')
      ->join('payroll_employee_loan_transaction as pelt', 'pelt.ID', '=', 'paylgr.LoanTransID')
      ->join('payroll_loan_type as plt', 'plt.id', '=', 'pelt.LoanTypeID')   
      ->join('users as emp', 'emp.id', '=', 'pelt.EmployeeID')   
  
    
            ->selectraw("
                 paylgr.ID,                  
                COALESCE(paylgr.LoanPaymentModuleType,'') as PaymentModuleType,
                COALESCE(paylgr.ReferrenceTransID,0) as ReferrenceTransID,

                COALESCE(paylgr.LoanTransID,0) as LoanTransID,
                
                COALESCE(pelt.EmployeeID,'') as EmployeeID,
                COALESCE(pelt.EmployeeNumber,'') as EmployeeNumber,
                CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

                 COALESCE(pelt.TotalPayment,0) as TotalPayment,
                 COALESCE(pelt.TotalLoanAmount,0) as TotalLoanAmount,

                COALESCE(plt.Code,'') as LoanTypeCode,
                COALESCE(plt.Name,'') as LoanTypeName,

                COALESCE(paylgr.AmountPayment,0) as AmountPayment,

                FORMAT(paylgr.PaymentDate,'MM/dd/yyyy') as PaymentDateFormat
            ");
  
    if($LoanTransID>0){
        $query->where("paylgr.LoanTransID",$LoanTransID);           
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

public function getEmployeeLoanTransactionInfo($SearchOption,$SearchValue){

    $info = DB::table('payroll_employee_loan_transaction as pelt')      
      ->join('payroll_loan_type as plt', 'plt.ID', '=', 'pelt.LoanTypeID')       
      ->leftjoin('users as emp', 'emp.id', '=', 'pelt.EmployeeID')   
            ->selectraw("
                 pelt.ID,
                
                COALESCE(pelt.TransactionDate,'') as TransactionDate,

                COALESCE(pelt.EmployeeID,'') as EmployeeID,
                COALESCE(emp.employee_number,'') as EmployeeNumber,

                CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

                COALESCE(pelt.CutOffID,0) as CutOff,
                COALESCE(pelt.LoanTypeID,0) as LoanTypeID,
                COALESCE(plt.Code,'') as LoanTypeCode,
                COALESCE(plt.Name,'') as LoanTypeName,

                COALESCE(pelt.VoucherNo,'') as VoucherNo,
                COALESCE(pelt.DelayedPaymentOption,'') as DelayedPaymentOption,                
                
                COALESCE(pelt.DateIssue,'') as DateIssue,
                COALESCE(pelt.PaymentStartDate,'') as PaymentStartDate,

                COALESCE(pelt.LoanAmount,0) as LoanAmount,
                COALESCE(pelt.TotalLoanAmount,0) as TotalLoanAmount,
                COALESCE(pelt.InterestAmount,0) as InterestAmount,
                COALESCE(pelt.AmortizationAmount,0) as AmortizationAmount,
                
                COALESCE(pelt.MonthsToPay,0) as MonthsToPay,
                COALESCE(pelt.TotalPayment,0) as TotalPayment,
                                    
                COALESCE(pelt.Remarks,'') as Remarks,
                COALESCE(pelt.Status,'') as Status,

                COALESCE(pelt.IsPosted,0) as IsPosted,
                COALESCE(pelt.IsClosed,0) as IsClosed,

                FORMAT(pelt.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(pelt.DateIssue,'MM/dd/yyyy') as DateIssueFormat,
                FORMAT(pelt.PaymentStartDate,'MM/dd/yyyy') as PaymentStartDateFormat,

                COALESCE(pelt.CreatedByID,0) as CreatedByID,
                COALESCE(pelt.DateTimeCreated,'') as DateTimeCreated,
                COALESCE(pelt.CancelledByID,0) as CancelledByID,
                COALESCE(pelt.DateTimeCancelled,'') as DateTimeCancelled,
                COALESCE(pelt.ApprovedByID,0) as ApprovedByID,
                COALESCE(pelt.DateTimeApproved,'') as DateTimeApproved
            ");

            if($SearchOption=='ByID'){
                $info->where("pelt.ID",$SearchValue);                
            }

            if($SearchOption=='ByEmployeeID'){
                $info->where("pelt.EmployeeID",$SearchValue);                
            }

           if($SearchOption=='ByEmployeeNo'){
                $info->where("pelt.EmployeeNumber",$SearchValue);           
            }
    
            $record=$info->first();

    return $record;

}


public function doSaveUploadFinalEmployeeLoanTransaction($data){
   
   $TODAY = date("Y-m-d H:i:s");

    $hasDataError=false;
    $RetVal['Response'] = "Success";
    $RetVal['ResponseMessage'] = "";

    $info_list=$this->getEmployeeLoanTransactionTempList($data);

    //CHECK IF HAS DATA ERROR STATUS
    if(count($info_list)>0){
        foreach($info_list as $list){
            if($list->IsUploadError==1){
              $hasDataError=true;  
            }
        } 
    }

    // if($hasDataError){
    //     $RetVal['Response'] = "Failed";
    //     $RetVal['ResponseMessage'] = "Sorry! Employee loan data cannot be saved. Data still has issues that need to be resolved.";
    // }else{
         
        //BATCH INSERT SQL
        DB::statement("insert into payroll_employee_loan_transaction(TransactionDate,EmployeeID,EmployeeNumber,LoanTypeID,LoanTypeCode,CutOffID,VoucherNo,DateIssue,LoanAmount,MonthsToPay,TotalLoanAmount,InterestAmount,AmortizationAmount,PaymentStartDate,Remarks,Status,CreatedByID,DateTimeCreated) 
        Select TransactionDate,EmployeeID,EmployeeNumber,LoanTypeID,LoanTypeCode,CutOffID,VoucherNo,DateIssue,LoanAmount,MonthsToPay,TotalLoanAmount,InterestAmount,AmortizationAmount,PaymentStartDate,Remarks,'Approved',UploadedByID,DateTimeUploaded from payroll_employee_loan_temp where IsUploadError!=1 AND UploadedByID=?" ,[Session::get('ADMIN_USER_ID')] );
     
        //CLEAR TEMP LOAN AFTER SAVE
        DB::table('payroll_employee_loan_temp')->where('UploadedByID',Session::get('ADMIN_USER_ID'))->delete();

        $RetVal['Response'] = "Success";
        $RetVal['ResponseMessage'] = "Uploaded Employee Loan has saved successfully.";
    //}

    return $RetVal;
}

public function doSaveEmployeeLoanTransaction($data){

    $Misc = new Misc();
    $TODAY = date("Y-m-d H:i:s");

    $EmployeeLoanTransID=$data["EmployeeLoanTransID"];
    $TransDate=$data["TransDate"];

    $EmployeeID=$data["EmpID"];
    $EmployeeNo=$data["EmpNo"];

    $CutOffID=$data["CutOffID"];

    $LoanTypeID=$data["LoanTypeID"];
    $LoanTypeCode=$data["LoanTypeCode"];

    $VoucherNo=trim($data["VoucherNo"]);
    $DelayedOptions=$data["DelayedOptions"];
    
    $DateIssued=$data["DateIssued"];
    $DateStartPayment=$data["DateStartPayment"];

    $AmortizationAmnt=$Misc->setNumeric($data["AmortizationAmnt"]);
    $InterestAmnt=$Misc->setNumeric($data["InterestAmnt"]);
    $LoanAmnt=$Misc->setNumeric($data["LoanAmnt"]);
    $TotalLoanAmnt=$Misc->setNumeric($data["TotalLoanAmnt"]);
    $MonthsToPay=$data["MonthsToPay"];
    
    $Remarks=$data["Remarks"];
    $IsUploaded=$data["IsUploaded"];

    $TransDate=date('Y-m-d',strtotime($TransDate)); 
    $DateIssued=date('Y-m-d',strtotime($DateIssued)); 
    $DateStartPayment=date('Y-m-d',strtotime($DateStartPayment));

    if($EmployeeLoanTransID > 0){

        DB::table('payroll_employee_loan_transaction')
            ->where('ID',$EmployeeLoanTransID)
            ->update([
                'TransactionDate' => $TransDate,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,
                'CutOffID' => $CutOffID,
                'LoanTypeID' => $LoanTypeID,
                'LoanTypeCode' => $LoanTypeCode,
                'VoucherNo' => $VoucherNo,
                'DelayedPaymentOption' => $DelayedOptions,
                'DateIssue' => $DateIssued,
                'LoanAmount' => $LoanAmnt,
                'MonthsToPay' => $MonthsToPay,
                'InterestAmount' => $InterestAmnt,
                'TotalLoanAmount' => $TotalLoanAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'PaymentStartDate' => $DateStartPayment,
                'Remarks' => $Remarks, 
                'UpdatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' => $TODAY
            ]);


        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $EmployeeLoanTransID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee Loan Transaction";
        $logData['TransType'] = "Update Employee Loan Transaction Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        $EmployeeLoanTransID = DB::table('payroll_employee_loan_transaction')
            ->insertGetId([            
                'TransactionDate' => $TransDate,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,
                'CutOffID' => $CutOffID,
                'LoanTypeID' => $LoanTypeID,
                'LoanTypeCode' => $LoanTypeCode,
                'VoucherNo' => $VoucherNo,
                'DateIssue' => $DateIssued,
                'LoanAmount' => $LoanAmnt,
                'MonthsToPay' => $MonthsToPay,
                'InterestAmount' => $InterestAmnt,
                'TotalLoanAmount' => $TotalLoanAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'PaymentStartDate' => $DateStartPayment,
                'Remarks' => $Remarks,
                'Status' => 'Pending',
                'CreatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' => $TODAY
              ]); 
        
        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $EmployeeLoanTransID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Employee Loan Transaction";
        $logData['TransType'] = "New Employee Loan Transaction Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $EmployeeLoanTransID;

    }

public function doSetLoanTransactionStatus($data){

$LoanID = $data['LoanID'];
$NewStatus = $data['NewStatus'];

 DB::table('payroll_employee_loan_transaction')
        ->where('ID',$LoanID)
        ->update([
            'Status' => $NewStatus
        ]);

    //Save Transaction Log
    $Misc = new Misc();
    $logData['TransRefID'] = $LoanID;
    $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
    $logData['ModuleType'] = "Employee Loan Transaction";

    if($NewStatus=='Approved'){
        $logData['TransType'] = "Set Approved Employee Loan Information";
    }
    if($NewStatus=='Cancelled'){
        $logData['TransType'] = "Set Cancelled Employee Loan Information";
    }

    $logData['Remarks'] = "";
    $Misc->doSaveTransactionLog($logData);

     return "Success";
}


public function doSaveEmployeeLoanPayment($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $LoanPaymentTransID = $data['LoanPaymentTransID'];
    $EmployeeLoanTransID = $data['EmployeeLoanTransID'];
    $EmployeeID = $data['EmployeeID'];
    
    $PaymentDate =$data['PaymentDate'];
    $PaymentLoanAmount = $Misc->setNumeric($data['PaymentLoanAmount']);
    $PaymentRemarks =$data['PaymentRemarks'];

    $PaymentDate=date('Y-m-d',strtotime($PaymentDate)); 

    if($LoanPaymentTransID > 0){
                            
        DB::table('payroll_employee_loan_set_manual_payment')
            ->where('ID',$LoanPaymentTransID)
            ->update([                
                'LaonTransactionID' => $EmployeeLoanTransID,
                'EmployeeID' => $EmployeeID,
                'PaymentDate' => $PaymentDate,
                'Amount' => $PaymentLoanAmount,                
                'Remarks' => $PaymentRemarks,                
                'UpdatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' => $TODAY
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $LoanPaymentTransID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Manual Loan Payment";
        $logData['TransType'] = "Update Loan Payment Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

       $LoanPaymentTransID = DB::table('payroll_employee_loan_set_manual_payment')
            ->insertGetId([
                'LaonTransactionID' => $EmployeeLoanTransID,
                'EmployeeID' => $EmployeeID,
                'PaymentDate' => $PaymentDate,
                'Amount' => $PaymentLoanAmount,                
                'Status' => 'Active',               
                'Remarks' => $PaymentRemarks,               
                'CreatedByID' => Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' => $TODAY
              ]); 

          //SAVE TO LEDGER
          DB::table('payroll_loan_payment_ledger')
            ->insert([
                'LoanPaymentModuleType' => 'Manual',
                'ReferrenceTransID' => $LoanPaymentTransID,
                'LoanTransID' => $EmployeeLoanTransID,
                'AmountPayment' => $PaymentLoanAmount,                
                'PaymentDate' => $PaymentDate           
              ]);
         
         //GET TOTAL LOAN
         $TotalPaymentMade=0; 
         if($EmployeeLoanTransID>0){
             $TotalPaymentMade=DB::table('payroll_employee_loan_transaction')
                              ->where('ID',$EmployeeLoanTransID)
                              ->value('TotalPayment');
         
        $TotalPaymentMade=$TotalPaymentMade + $PaymentLoanAmount; 

         //UPDATE TOTAL PAYMENT
          DB::table('payroll_employee_loan_transaction')
            ->where('ID',$EmployeeLoanTransID)
            ->update([  
                'TotalPayment' => $TotalPaymentMade,
            ]);                             
         }
          
        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $LoanPaymentTransID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Manual Loan Payment";
        $logData['TransType'] = "New Loan Payment Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

  return $LoanPaymentTransID;
 }
 public function doCheckExistingEmployeeLoan($param){

    $IsExist = false;
    $EmployeeLoanTransID=$param['EmployeeLoanTransID'];
    $EmployeeID=$param['EmpID'];
    $LoanTypeID=$param['LoanTypeID'];

    $EmployeeLoan = DB::table('payroll_employee_loan_transaction')
          ->where('EmployeeID','=',$EmployeeID)
          ->where('LoanTypeID','=',$LoanTypeID)
          ->where('Status','=','Pending')
          ->first();

    if(isset($EmployeeLoan)){
        if($EmployeeLoanTransID > 0){
          if($EmployeeLoan->ID != $EmployeeLoanTransID){
            $IsExist = true;
          }
        }else{
          $IsExist = true;
        }
      }

      return $IsExist;
    }

 //TEMP TABLE  
  public function getEmployeeLoanTransactionTempList($param){

    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_employee_loan_temp as pelt')
      ->leftjoin('users as emp', 'emp.id', '=', 'pelt.EmployeeID')   
      ->leftjoin('payroll_loan_type as plt', 'plt.ID', '=', 'pelt.LoanTypeID')   
            ->selectraw("
                pelt.ID,
                COALESCE(pelt.TransactionDate,'') as TransactionDate,

                COALESCE(pelt.EmployeeID,0) as EmployeeID,
                COALESCE(emp.employee_number,'') as EmployeeNumber,
                CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

                COALESCE(pelt.CutOffID,0) as CutOff,
                COALESCE(pelt.LoanTypeID,0) as LoanTypeID,
                COALESCE(plt.Code,'') as LoanTypeCode,
                COALESCE(plt.Name,'') as LoanTypeName,

                COALESCE(pelt.VoucherNo,'') as VoucherNo,
                COALESCE(pelt.DateIssue,'') as DateIssue,

                COALESCE(pelt.LoanAmount,0) as LoanAmount,
                COALESCE(pelt.TotalLoanAmount,0) as TotalLoanAmount,
                COALESCE(pelt.InterestAmount,0) as InterestAmount,
                COALESCE(pelt.AmortizationAmount,0) as AmortizationAmount,

                COALESCE(pelt.PaymentStartDate,'') as PaymentStartDate,
                
                FORMAT(pelt.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(pelt.DateIssue,'MM/dd/yyyy') as DateIssueFormat,
                FORMAT(pelt.PaymentStartDate,'MM/dd/yyyy') as PaymentStartDateFormat,

                COALESCE(pelt.Remarks,'') as Remarks,
                COALESCE(pelt.Status,'') as Status,
                COALESCE(pelt.IsUploadError,0) as IsUploadError
            ");

    if($Status!=''){
        $query->where("pelt.Status",$Status);
    }      

    $query->where("pelt.UploadedByID",Session::get('ADMIN_USER_ID')); 

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("pelt.IsUploadError","DESC");
    $list = $query->get();

    return $list;

}

public function doSaveLoanTempTransactionPerBatch($data){

    $Employee = new Employee();  
    $LoanType = new LoanType();   
    $EmployeeLoan = new EmployeeLoan();


    $TransDate = date("Y-m-d");

    $TODAY = date("Y-m-d H:i:s");
    $LoanTempDataItems = $data['LoanTempDataItems'];

    if(!empty($LoanTempDataItems)){

      for($x=0; $x< count($LoanTempDataItems); $x++) {
            
        $LoanTransID = $LoanTempDataItems[$x]["LoanTransID"];
        $EmployeeNo = $LoanTempDataItems[$x]["EmpNo"];
        $EmployeeID=0;
        if($EmployeeNo!=''){
            $emp_info = $Employee->getEmployeeInfo('ByEmployeeNo',$EmployeeNo);
            if(isset($emp_info)>0){
               $EmployeeID=$emp_info->employee_id;
            }  

        }

        $LoanTypeCode = $LoanTempDataItems[$x]["LoanTypeCode"];
        $LoanTypeID=0;
        if($LoanTypeCode!=''){
            $loan_type_info = $LoanType->getLoanTypeInfoByCode($LoanTypeCode);
            if(isset($loan_type_info)>0){
               $LoanTypeID=$loan_type_info->ID;
            }  
        }
                
        $DateIssued = $LoanTempDataItems[$x]["DateIssued"];
        $DateIssued=date('Y-m-d',strtotime($DateIssued)); 
        $VoucherNo = $LoanTempDataItems[$x]["VoucherNo"];

        $CutOff = trim($LoanTempDataItems[$x]["CutOff"]);        
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
        
        $InterestAmnt = $LoanTempDataItems[$x]["InterestAmnt"];
        $AmortizationAmnt = $LoanTempDataItems[$x]["AmortizationAmnt"];

        $LoanAmnt = $LoanTempDataItems[$x]["LoanAmnt"];
        $TotalLoanAmnt = $LoanTempDataItems[$x]["TotalLoanAmnt"];
        $MonthsToPay = $LoanTempDataItems[$x]["MonthsToPay"];

        $DateStartPayment = $LoanTempDataItems[$x]["DateStartPayment"];
        $DateStartPayment=date('Y-m-d',strtotime($DateStartPayment)); 

        $Remarks =$LoanTempDataItems[$x]["Remarks"];
                
        $IsUploadError=0;
        if($EmployeeID==0 || $LoanTypeID==0){
             $IsUploadError=1;
        }

        $EmployeeIncomeDeductionTransID = DB::table('payroll_employee_loan_temp')
              ->insertGetId([
                'TransactionDate' => $TransDate,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,
                'CutOffID' => $CutOffID,
                'LoanTypeID' => $LoanTypeID,
                'LoanTypeCode' => $LoanTypeCode,
                'VoucherNo' => $VoucherNo,
                'DateIssue' => $DateIssued,
                'LoanAmount' => $LoanAmnt,
                'TotalLoanAmount' => $TotalLoanAmnt,
                'InterestAmount' => $InterestAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'PaymentStartDate' => $DateStartPayment,
                'MonthsToPay' => $MonthsToPay,
                'Status' => 'Pending',                
                'Remarks' => $Remarks,                
                'IsUploadError'=> $IsUploadError,
                'UploadedByID'=> Session::get('ADMIN_USER_ID'),    
                'DateTimeUploaded'=> $TODAY      
              ]); 


         $this->checkUploadedExcelRecordHasDuplicateRecord($EmployeeID,$LoanTypeID,$VoucherNo);
                
      } 
    }
    return "Success";
}

public function doSaveEmployeeLoanTempTransaction($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");
    $TODAY_UPLOADED_TRANS_DATE = date("Y-m-d");

    $EmployeeLoanTransID=$data["EmployeeLoanTransID"];
    $TransDate=$data["TransDate"];

    $EmployeeID=$data["EmpID"];
    $EmployeeNo=$data["EmpNo"];

    $CutOffID=$data["CutOffID"];

    $LoanTypeID=$data["LoanTypeID"];
    $LoanTypeCode=$data["LoanTypeCode"];

    $VoucherNo=trim($data["VoucherNo"]);

    $DateIssued=$data["DateIssued"];
    $DateStartPayment=$data["DateStartPayment"];

    $AmortizationAmnt=$Misc->setNumeric($data["AmortizationAmnt"]);
    $InterestAmnt=$Misc->setNumeric($data["InterestAmnt"]);
    $LoanAmnt=$Misc->setNumeric($data["LoanAmnt"]);
    $TotalLoanAmnt=$Misc->setNumeric($data["TotalLoanAmnt"]);

    $MonthsToPay=$data["MonthsToPay"];
    
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

    if($EmployeeLoanTransID > 0){

        DB::table('payroll_employee_loan_temp')
            ->where('ID',$EmployeeLoanTransID)
            ->update([
                'TransactionDate' => $TransDate,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,
                'CutOffID' => $CutOffID,
                'LoanTypeID' => $LoanTypeID,
                'LoanTypeCode' => $LoanTypeCode,
                'VoucherNo' => $VoucherNo,
                'DateIssue' => $DateIssued,
                'LoanAmount' => $LoanAmnt,
                'MonthsToPay' => $MonthsToPay,
                'TotalLoanAmount' => $TotalLoanAmnt,
                'InterestAmount' => $InterestAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'PaymentStartDate' => $DateStartPayment,
                'Remarks' => $Remarks,
                'Status' => $Status
          
            ]);

             $this->checkUploadedExcelRecordHasDuplicateRecord($EmployeeID,$LoanTypeID,$VoucherNo);
    
    }else{

        $IsUploadError=0;
        if($EmployeeID==0 || $LoanTypeID==0){
             $IsUploadError=1;
        }
   
        $EmployeeLoanTransID = DB::table('payroll_employee_loan_temp')
            ->insertGetId([
                'TransactionDate' => $TransDate,
                'EmployeeID' => $EmployeeID,
                'EmployeeNumber' => $EmployeeNo,
                'CutOffID' => $CutOffID,
                'LoanTypeID' => $LoanTypeID,
                'LoanTypeCode' => $LoanTypeCode,
                'VoucherNo' => $VoucherNo,
                'DateIssue' => $DateIssued,
                'LoanAmount' => $LoanAmnt,
                'TotalLoanAmount' => $TotalLoanAmnt,
                'InterestAmount' => $InterestAmnt,
                'AmortizationAmount' => $AmortizationAmnt,
                'PaymentStartDate' => $DateStartPayment,
                'Remarks' => $Remarks,
                'Status' => $Status,
                'IsUploadError'=> $IsUploadError      
              ]); 
  
        $this->checkUploadedExcelRecordHasDuplicateRecord($EmployeeID,$LoanTypeID,$VoucherNo);
     }
     return $EmployeeLoanTransID;
 }

public function getEmployeeLoanTempTransactionInfo($EmployeeLaonTransaction_ID){

    $info = DB::table('payroll_employee_loan_temp as pelt')
      ->leftjoin('users as emp', 'emp.id', '=', 'pelt.EmployeeID')   
      ->leftjoin('payroll_loan_type as plt', 'plt.ID', '=', 'pelt.LoanTypeID')   
            ->selectraw("
                pelt.ID,
                COALESCE(pelt.TransactionDate,'') as TransactionDate,

                COALESCE(pelt.EmployeeID,0) as EmployeeID,
                COALESCE(pelt.EmployeeNumber,'') as EmployeeNumber,
                CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

                COALESCE(pelt.CutOffID,0) as CutOff,
                COALESCE(pelt.LoanTypeID,0) as LoanTypeID,
                COALESCE(plt.Code,'') as LoanTypeCode,
                COALESCE(plt.Name,'') as LoanTypeName,

                COALESCE(pelt.VoucherNo,'') as VoucherNo,
                COALESCE(pelt.DateIssue,'') as DateIssue,

                COALESCE(pelt.LoanAmount,0) as LoanAmount,
                COALESCE(pelt.TotalLoanAmount,0) as TotalLoanAmount,
                COALESCE(pelt.InterestAmount,0) as InterestAmount,
                COALESCE(pelt.AmortizationAmount,0) as AmortizationAmount,
               
                COALESCE(pelt.MonthsToPay,0) as MonthsToPay,
                COALESCE(pelt.PaymentStartDate,'') as PaymentStartDate,
                
                FORMAT(pelt.TransactionDate,'MM/dd/yyyy') as TransactionDateFormat,
                FORMAT(pelt.DateIssue,'MM/dd/yyyy') as DateIssueFormat,
                FORMAT(pelt.PaymentStartDate,'MM/dd/yyyy') as PaymentStartDateFormat,

                COALESCE(pelt.Remarks,'') as Remarks,
                COALESCE(pelt.Status,'') as Status,
                COALESCE(pelt.IsUploadError,0) as IsUploadError
            ")

    ->where("pelt.ID",$EmployeeLaonTransaction_ID)
    ->first();

    return $info;

}

 public function doRemoveDuplicateLoanTempTransaction($tempID){

        $chkEmployeeID=0;
        $chkLoanTypeID=0;
        $chkVoucherCode='';

        $info=$this->getEmployeeLoanTempTransactionInfo($tempID);

        if(isset($info)>0){
            $chkEmployeeID=$info->EmployeeID;
            $chkLoanTypeID=$info->LoanTypeID;
            $chkVoucherCode=$info->VoucherNo;

            DB::table('payroll_employee_loan_temp')->where('ID',$tempID)->delete();
            $this->checkUploadedExcelRecordHasDuplicateRecord($chkEmployeeID,$chkLoanTypeID,$chkVoucherCode);

        }
    }
  
  //check if uploaded excel has duplicate payroll id and duplicate loan record employee
    public function checkUploadedExcelRecordHasDuplicateRecord($EmployeeID,$LoanTypeID,$VoucherCode){
      
      if($EmployeeID>0 && $LoanTypeID>0){

            $info = DB::table('payroll_employee_loan_temp')
               ->where("EmployeeID",$EmployeeID)
               ->where("LoanTypeID",$LoanTypeID)
               ->where("VoucherNo",$VoucherCode)               
               ->where("Status",'Pending')
               ->get();
        
                if(count($info)>=2){

                    DB::table('payroll_employee_loan_temp')
                        ->where("EmployeeID",$EmployeeID)
                        ->where("LoanTypeID",$LoanTypeID)
                        ->where("Status",'Pending')

                        ->update([
                            'IsUploadError' => 2
                          ]);  
              }else{

                     DB::table('payroll_employee_loan_temp')
                        ->where("EmployeeID",$EmployeeID)
                        ->where("LoanTypeID",$LoanTypeID)
                        ->where("Status",'Pending')

                        ->update([
                            'IsUploadError' => 0
                          ]);  
              }
         }
    }


}

