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

class Division extends Model
{

public function getDivisionList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_division as div')
        ->selectraw("
            div.ID,
            COALESCE(div.Division,'') as Division
        ");

     if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                      COALESCE(div.Division,''),
                      ''
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderByraw("COALESCE(div.Division,'') ASC");
    $list = $query->get();

    return $list;

    }

    public function postDivisionInfo($data){

        $Misc = new Misc();

        $TODAY = date("Y-m-d H:i:s");

        $id = $data['id'];
        $company_id = $data['company_id'];

        $name = trim($data['name']);
        $code = trim($data['code']);

        $manager_id = $data['manager_id'];
        $assistant_manager_id = $data['assistant_manager_id'];
        $secretary_id = $data['secretary_id'];

        $description = trim($data['description']);
      
        $status = $data['status'];

        $deletedAt = $data['deletedAt'];
        $createdAt = $data['createdAt'];
        $updatedAt = $data['updatedAt'];

        $alternate_secretary = $data['alternate_secretary'];

        //Check if record exist
        $info = DB::table('divisions')
            ->selectraw("
                id
            ")
            ->where('id',$id)
            ->first();        

        if(isset($info)){
            DB::table('divisions')
                ->where('id',$id)
                ->update([
                    'company_id' => $company_id,
                    'name' => $name,
                    'code' => $code,
                    'manager_id' => $manager_id,
                    'assistant_manager_id' => $assistant_manager_id,
                    'secretary_id' => $secretary_id,
                    'description' => $description,
                    'status' => $status,
                    'deletedAt' => $deletedAt,
                    'createdAt' => $createdAt,
                    'updatedAt' => $updatedAt,
                    'alternate_secretary' => $alternate_secretary
                ]);
        }else{
            $RecordID = DB::table('divisions')
                ->insertGetId([
                    'company_id' => $company_id,
                    'name' => $name,
                    'code' => $code,
                    'manager_id' => $manager_id,
                    'assistant_manager_id' => $assistant_manager_id,
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
        $pinfo = DB::table('payroll_division')
            ->selectraw("
                RefID
            ")
            ->where('RefID',$id)
            ->first();  

        if(isset($pinfo)){
            DB::table('payroll_division')
                ->where('RefID',$id)
                ->update([
                    'Division' => $name
                ]);

            //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $id;
            $logData['TransactedByID'] = 0;
            $logData['ModuleType'] = "Division";
            $logData['TransType'] = "Post Update Division Information";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);

        }else{
            $RecordID = DB::table('payroll_division')
                ->insertGetId([
                    'ID' => $id,
                    'RefID' => $id,
                    'Division' => $name
                  ]);  

            //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $id;
            $logData['TransactedByID'] = 0;
            $logData['ModuleType'] = "Division";
            $logData['TransType'] = "Post Division Information";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);
        }

        return "Success";

    }
    
}

