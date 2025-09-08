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

class JobType extends Model
{

public function getJobTypeList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];
    $Status = $param['Status'];

    $query = DB::table('payroll_job_type as pjob')
        ->selectraw("
            pjob.ID,
            COALESCE(pjob.JobTitle,'') as JobTitle
        ");

     if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                      COALESCE(pjob.JobTitle,''),
                      ''
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderByraw("COALESCE(pjob.JobTitle,'') ASC");
    $list = $query->get();

    return $list;

    }
 
    public function postJobTypeInfo($data){

        $Misc = new Misc();

        $TODAY = date("Y-m-d H:i:s");

        $id = $data['id'];
        $name = $data['name'];

        $createdAt = $data['createdAt'];
        $updatedAt = $data['updatedAt'];

        //Check if record exist
        $info = DB::table('positions')
            ->selectraw("
                id
            ")
            ->where('id',$id)
            ->first();        

        if(isset($info)){
            DB::table('positions')
                ->where('id',$id)
                ->update([
                    'name' => $name,
                    'createdAt' => $createdAt,
                    'updatedAt' => $updatedAt
                ]);
        }else{
            $RecordID = DB::table('positions')
                ->insertGetId([
                    'name' => $name,
                    'createdAt' => $createdAt,
                    'updatedAt' => $updatedAt
                  ]);  
        }


        //Check PAYROLL record exist
        $pinfo = DB::table('payroll_job_type')
            ->selectraw("
                RefID
            ")
            ->where('RefID',$id)
            ->first();  

        if(isset($pinfo)){
            DB::table('payroll_job_type')
                ->where('RefID',$id)
                ->update([
                    'JobTitle' => $name
                ]);

            //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $id;
            $logData['TransactedByID'] = 0;
            $logData['ModuleType'] = "Job Type";
            $logData['TransType'] = "Post Update Job Type Information";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);

        }else{

            $pinfoID = DB::table('payroll_job_type')
                ->selectraw("
                    MAX(ID) as ID
                ")
                ->first();  

            $MaxID = 0;
            if(isset($pinfoID)){
                $MaxID = $pinfoID->ID;
            }

            $RecordID = DB::table('payroll_job_type')
                ->insertGetId([
                    'ID' => $MaxID + 1,
                    'RefID' => $id,
                    'JobTitle' => $name
                  ]);  

            //Save Transaction Log
            $Misc = new Misc();
            $logData['TransRefID'] = $id;
            $logData['TransactedByID'] = 0;
            $logData['ModuleType'] = "Job Type";
            $logData['TransType'] = "Post Job Type Information";
            $logData['Remarks'] = "";
            $Misc->doSaveTransactionLog($logData);
        }

        return "Success";

    }

    
}

