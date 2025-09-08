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

class Department extends Model
{

public function getDepartmentList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_department as dept')
        ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
        ->selectraw("
            dept.ID,
            COALESCE(dept.Department,'') as Department,

            COALESCE(dept.DivisionID,0) as DivisionID,
            COALESCE(div.Division,'') as Division

        ");

     if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                      COALESCE(dept.Department,''),
                      ''
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderByraw("COALESCE(dept.Department,'') ASC");
    $list = $query->get();

    return $list;

    }
    
    public function postDepartmentInfo($data){

        $Misc = new Misc();

        $TODAY = date("Y-m-d H:i:s");

        $id = $data['id'];
        $division_id = $data['division_id'];

        $department_name = trim($data['department_name']);
        $department_code = trim($data['department_code']);

        $manager_id = $data['manager_id'];
        $assistant_manager_id = $data['assistant_manager_id'];
        $secretary_id = $data['secretary_id'];

        $description = trim($data['description']);
      
        $status = $data['status'];

        $deletedAt = $data['deletedAt'];
        $createdAt = $data['createdAt'];
        $updatedAt = $data['updatedAt'];

        $alternate_secretary = $data['alternate_secretary'];
        $hr_generalist_id = $data['hr_generalist_id'];

        //Check if record exist
        $info = DB::table('departments')
            ->selectraw("
                id
            ")
            ->where('id',$id)
            ->first();        

        if(isset($info)){
            DB::table('departments')
                ->where('id',$id)
                ->update([
                    'division_id' => $division_id,
                    'department_name' => $department_name,
                    'department_code' => $department_code,
                    'manager_id' => $manager_id,
                    'assistant_manager_id' => $assistant_manager_id,
                    'secretary_id' => $secretary_id,
                    'description' => $description,
                    'status' => $status,
                    'deletedAt' => $deletedAt,
                    'createdAt' => $createdAt,
                    'updatedAt' => $updatedAt,
                    'alternate_secretary' => $alternate_secretary,
                    'hr_generalist_id' => $hr_generalist_id
                ]);
        }else{
            $RecordID = DB::table('departments')
                ->insertGetId([
                    'division_id' => $division_id,
                    'department_name' => $department_name,
                    'department_code' => $department_code,
                    'manager_id' => $manager_id,
                    'assistant_manager_id' => $assistant_manager_id,
                    'secretary_id' => $secretary_id,
                    'description' => $description,
                    'status' => $status,
                    'deletedAt' => $deletedAt,
                    'createdAt' => $createdAt,
                    'updatedAt' => $updatedAt,
                    'alternate_secretary' => $alternate_secretary,
                    'hr_generalist_id' => $hr_generalist_id
                  ]);  
        }


        //Check PAYROLL record exist
        $pinfo = DB::table('payroll_department')
            ->selectraw("
                RefID
            ")
            ->where('RefID',$id)
            ->first();  

        if(isset($pinfo)){
            DB::table('payroll_department')
                ->where('RefID',$id)
                ->update([
                    'DivisionID' => $division_id,
                    'Department' => $department_name
                ]);

            //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $id;
            $logData['TransactedByID'] = 0;
            $logData['ModuleType'] = "Department";
            $logData['TransType'] = "Post Update Department Information";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);

        }else{
            $RecordID = DB::table('payroll_department')
                ->insertGetId([
                    'ID' => $id,
                    'RefID' => $id,
                    'DivisionID' => $division_id,
                    'Department' => $department_name
                  ]);  

            //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $id;
            $logData['TransactedByID'] = 0;
            $logData['ModuleType'] = "Department";
            $logData['TransType'] = "Post Department Information";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);
        }

        return "Success";

    }
    
}

