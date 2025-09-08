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

class EmployeeAllowance extends Model
{
  
  // SET EMPLOYEE ALLOWANCES
  public function doSaveUpdateEmployeeAllowance($data){
    
    $Misc = new Misc();
    $TODAY = date("Y-m-d H:i:s");

    $EmployeeID = $data['EmployeeID'];                    
    $ItemCount = $data['ItemCount'];
    $EmployeeAllowanceItems = $data['EmployeeAllowanceItems'];

    if(!empty($EmployeeAllowanceItems) && $ItemCount>0){

      //CLEAR & REMOVE OLD EMPLOYEE ALLOWANCE
      DB::table('payroll_employee_allowance_setup')->where('EmployeeID', '=', $EmployeeID)->delete();  
      
      //CREATE & INSERT NEW SERVICE ITEMS
      for($x=1; $x < count($EmployeeAllowanceItems); $x++) {
        
        $AllowanceID = $EmployeeAllowanceItems[$x]["AllowanceID"];
        $AllowanceAmount = $Misc->setNumeric($EmployeeAllowanceItems[$x]["AllowanceAmount"]);         
        $FrequencyID = $EmployeeAllowanceItems[$x]["FrequencyID"];

        if($AllowanceID > 0){
            $EmployeeAllowanceID=  DB::table('payroll_employee_allowance_setup')
              ->insertGetId([
                'EmployeeID' => $EmployeeID,
                'AllowanceID' => $AllowanceID,
                'FrequencyID' => $FrequencyID,
                'Amount' => $AllowanceAmount,                            
                'CreatedByID' =>  Session::get('ADMIN_USER_ID'),
                'DateTimeCreated' => $TODAY,
                'UpdatedByID' =>  Session::get('ADMIN_USER_ID'),
                'DateTimeUpdated' => $TODAY,
              ]);

            
                $Misc = new Misc();
                $logData['TransRefID'] = $EmployeeAllowanceID;
                $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
                $logData['ModuleType'] = "Employee Allowance";
                $logData['TransType'] = "Set Employee Allowance";
                $logData['Remarks'] = "";
                $Misc->doSaveTransactionLog($logData);    
          }          
      }
    }

    return "Success";
  }
  
 public function getEmployeeAllowanceList($EmployeeID){

   $query = DB::table('payroll_employee_allowance_setup as emp_alwn')
    ->join('payroll_allowance_type as alwn_type', 'alwn_type.ID', '=', 'emp_alwn.AllowanceID')
 
   ->selectraw("
        emp_alwn.ID as EmployeeAllowanceID,
        emp_alwn.AllowanceID as AllowanceID,
        COALESCE(emp_alwn.EmployeeID,'') as EmployeeNumber,
        COALESCE(emp_alwn.FrequencyID,0) as FrequencyID,
        COALESCE(emp_alwn.Amount,0) as AllowanceAmount,        

        COALESCE(alwn_type.Code,'') as AllowanceCode,
        COALESCE(alwn_type.Name,'') as AllowanceName
        
    ");
    
    $query->where("emp_alwn.EmployeeID",$EmployeeID);          
     $query->orderBy("alwn_type.Code","ASC");

    $list = $query->get();

    return $list;

 }
}

