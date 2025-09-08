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

class Section extends Model
{

public function getSectionList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_section as sec')
        ->join('payroll_department as dept', 'dept.ID', '=', 'sec.DepartmentID')
        ->join('payroll_division as div', 'div.ID', '=', 'dept.DivisionID')
        ->selectraw("
            sec.ID,
            COALESCE(sec.Section,'') as Section,

            COALESCE(sec.DepartmentID,0) as DepartmentID,
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
                      COALESCE(sec.Section,''),
                      ''
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderByraw("COALESCE(sec.Section,'') ASC");
    $list = $query->get();

    return $list;

    }

    public function postSectionInfo($data){

        $Misc = new Misc();

        $TODAY = date("Y-m-d H:i:s");

        $id = $data['id'];
        $department_id = $data['department_id'];

        $section_name = trim($data['section_name']);
        $section_code = trim($data['section_code']);

        $supervisor_id = $data['supervisor_id'];
        $assistant_supervisor_id = $data['assistant_supervisor_id'];
        $secretary_id = $data['secretary_id'];

        $description = trim($data['description']);
      
        $status = $data['status'];

        $deletedAt = $data['deletedAt'];
        $createdAt = $data['createdAt'];
        $updatedAt = $data['updatedAt'];

        $alternate_secretary = $data['alternate_secretary'];

        //Check if record exist
        $info = DB::table('sections')
            ->selectraw("
                id
            ")
            ->where('id',$id)
            ->first();        

        if(isset($info)){
            DB::table('sections')
                ->where('id',$id)
                ->update([
                    'department_id' => $department_id,
                    'section_name' => $section_name,
                    'section_code' => $section_code,
                    'supervisor_id' => $supervisor_id,
                    'assistant_supervisor_id' => $assistant_supervisor_id,
                    'secretary_id' => $secretary_id,
                    'description' => $description,
                    'status' => $status,
                    'deletedAt' => $deletedAt,
                    'createdAt' => $createdAt,
                    'updatedAt' => $updatedAt,
                    'alternate_secretary' => $alternate_secretary
                ]);
        }else{
            $RecordID = DB::table('sections')
                ->insertGetId([
                    'department_id' => $department_id,
                    'section_name' => $section_name,
                    'section_code' => $section_code,
                    'supervisor_id' => $supervisor_id,
                    'assistant_supervisor_id' => $assistant_supervisor_id,
                    'secretary_id' => $secretary_id,
                    'description' => $description,
                    'status' => $status,
                    'deletedAt' => $deletedAt,
                    'createdAt' => $createdAt,
                    'updatedAt' => $updatedAt,
                    'alternate_secretary' => $alternate_secretary
                  ]);  
        }


        //Check PAYROLL record exist
        $pinfo = DB::table('payroll_section')
            ->selectraw("
                RefID
            ")
            ->where('RefID',$id)
            ->first();  

        if(isset($pinfo)){
            DB::table('payroll_section')
                ->where('RefID',$id)
                ->update([
                    'DepartmentID' => $department_id,
                    'Section' => $section_name
                ]);

            //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $id;
            $logData['TransactedByID'] = 0;
            $logData['ModuleType'] = "Section";
            $logData['TransType'] = "Post Update Section Information";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);

        }else{
            $RecordID = DB::table('payroll_section')
                ->insertGetId([
                    'ID' => $id,
                    'RefID' => $id,
                    'DepartmentID' => $department_id,
                    'Section' => $section_name
                  ]);  

            //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $id;
            $logData['TransactedByID'] = 0;
            $logData['ModuleType'] = "Section";
            $logData['TransType'] = "Post Section Information";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);
        }

        return "Success";

    }

    
}

