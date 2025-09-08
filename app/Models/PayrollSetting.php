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

class PayrollSetting extends Model
{


public function getPayrollSettingList($param){

    $SearchText = trim($param['SearchText']);
    $Limit = $param['Limit'];
    $PageNo = $param['PageNo'];

    $query = DB::table('payroll_setting as ps')
    ->selectraw("
        ps.ID,
        COALESCE(ps.CompanyLogo,'') as CompanyLogo,
        COALESCE(ps.CompanyCode,'') as CompanyCode,
        COALESCE(ps.CompanyName,'') as CompanyName,
        COALESCE(ps.DomainWebsite,'') as DomainWebsite,
        COALESCE(ps.EmailAddress,'') as EmailAddress,
        COALESCE(ps.PhoneNo,'') as PhoneNo,
        COALESCE(ps.MobileNo,'') as MobileNo,
        COALESCE(ps.FaxNo,'') as FaxNo,
        COALESCE(ps.Address,'') as Address,
        COALESCE(ps.City,'') as City,
        COALESCE(ps.PostalCode,'') as PostalCode,
        COALESCE(ps.Country,'') as Country,
        COALESCE(ps.ClosingDate,'') as ClosingDate,
        COALESCE(ps.NDPercentage,0) as NDPercentage,
        COALESCE(ps.MinTakeHomePercentage,0) as MinTakeHomePercentage,
        COALESCE(ps.SSSSched,0) as SSSSched,
        COALESCE(ps.HDMFSched,0) as HDMFSched,
        COALESCE(ps.PHICSched,0) as PHICSched
    ");


    if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                    "CONCAT_WS(' ',
                        COALESCE(ps.CompanyLogo,''),
                        COALESCE(ps.CompanyCode,''),
                        COALESCE(ps.CompanyName,''),
                        COALESCE(ps.DomainWebsite,''),
                        COALESCE(ps.EmailAddress,''),
                        COALESCE(ps.LoanTypeDescription,''),
                        COALESCE(ps.PhoneNo,''),
                        COALESCE(ps.MobileNo,''),
                        COALESCE(ps.FaxNo,''),
                        COALESCE(ps.Address,''),
                        COALESCE(ps.City,''),
                        COALESCE(ps.PostalCode,''),
                        COALESCE(ps.Country,'')
                    ) like '%".str_replace("'", "''", $arSearchText[$x])."%'");
            }
        }
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderByraw("COALESCE(ps.CompanyCode,'') ASC");
    $list = $query->get();

    return $list;

}

public function getPayrollSettingInfo($SystemSettingID){

    $info = DB::table('payroll_setting as ps')
        ->selectraw("
            ps.ID,
            COALESCE(ps.CompanyLogo,'') as CompanyLogo,
            COALESCE(ps.CompanyCode,'') as CompanyCode,
            COALESCE(ps.CompanyName,'') as CompanyName,
            COALESCE(ps.DomainWebsite,'') as DomainWebsite,
            COALESCE(ps.EmailAddress,'') as EmailAddress,
            COALESCE(ps.PhoneNo,'') as PhoneNo,
            COALESCE(ps.MobileNo,'') as MobileNo,
            COALESCE(ps.FaxNo,'') as FaxNo,
            COALESCE(ps.Address,'') as Address,
            COALESCE(ps.City,'') as City,
            COALESCE(ps.PostalCode,'') as PostalCode,
            COALESCE(ps.Country,'') as Country,
            COALESCE(ps.ClosingDate,'') as ClosingDate,
            COALESCE(ps.NDPercentage,0) as NDPercentage,
            COALESCE(ps.MinTakeHomePercentage,0) as MinTakeHomePercentage,
            COALESCE(ps.SSSSched,0) as SSSSched,
            COALESCE(ps.HDMFSched,0) as HDMFSched,
            COALESCE(ps.PHICSched,0) as PHICSched
        ")
    ->where("ps.ID",$SystemSettingID)
    ->first();

    return $info;

}

public function doSavePayrollSetting($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");

    $SettingID=$data["SettingID"];
    $CompanyCode=$data["CompanyCode"];
    $CompanyName=$data["CompanyName"];
    $CompanyWebsite=$data["CompanyWebsite"]; 
    $CompanyPhoneNo=$data["CompanyPhoneNo"];
    $CompanyMobileNo=$data["CompanyMobileNo"]; 
    $CompanyFaxNo=$data["CompanyFaxNo"];
    $CompanyEmailAddress=$data["CompanyEmailAddress"];

    $CompanyAddress=$data["CompanyAddress"]; 
    $CompanyCity=$data["CompanyCity"]; 
    $CompanyPostalCode=$data["CompanyPostalCode"];
    $CompanyCountry=$data["CompanyCountry"];
  
    if($SettingID > 0){

        DB::table('payroll_setting')
            ->where('ID',$SettingID)
            ->update([
                'CompanyCode' => $CompanyCode,
                'CompanyName' => ucwords($CompanyName),
                'DomainWebsite'=>strtolower($CompanyWebsite),
                'EmailAddress' => $CompanyEmailAddress,
                'PhoneNo' => $CompanyPhoneNo,
                'MobileNo' => $CompanyMobileNo,
                'FaxNo' => $CompanyFaxNo,
                'Address' => ucwords($CompanyAddress),
                'City' => ucwords($CompanyCity),
                'PostalCode' => $CompanyPostalCode,
                'Country' => ucwords($CompanyCountry)
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $SettingID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Payroll Setting";
        $logData['TransType'] = "Update Payroll Setting Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        //$LoanTypeCode = $Misc->GenerateRandomNo(6,'payroll_setting','CompanyCode');
        $SettingID = DB::table('payroll_setting')
            ->insertGetId([
                'CompanyCode' => $CompanyCode,
                'CompanyName' => ucwords($CompanyName),
                'DomainWebsite'=>strtolower($CompanyWebsite),
                'EmailAddress' => $CompanyEmailAddress,
                'PhoneNo' => $CompanyPhoneNo,
                'MobileNo' => $CompanyMobileNo,
                'FaxNo' => $CompanyFaxNo,
                'Address' => ucwords($CompanyAddress),
                'City' => ucwords($CompanyCity),
                'PostalCode' => $CompanyPostalCode,
                'Country' => ucwords($CompanyCountry)
                ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $SettingID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Payroll Setting";
        $logData['TransType'] = "New Payroll Setting";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $SettingID;

    }

public function doSavePayrollSettingClosingDate($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");

    $SettingID=$data["SettingID"];
    $ClosingDate=$data["ClosingDate"];

    $ClosingDate=date('Y-m-d', strtotime($ClosingDate));  

    if($SettingID > 0){

        DB::table('payroll_setting')
            ->where('ID',$SettingID)
            ->update([
                'ClosingDate' => $ClosingDate
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $SettingID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Payroll Setting Closing Date";
        $logData['TransType'] = "Update Payroll Setting Closing Date Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        $SettingID = DB::table('payroll_setting')
            ->insertGetId([
                'ClosingDate' => $ClosingDate
                ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $SettingID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Payroll Setting Closing Date";
        $logData['TransType'] = "New Payroll Setting Closing Date Information";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $SettingID;

    }


public function doSavePayrollEmployeeSetting($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");

    $SettingID=$data["SettingID"];
    $NDPercentage=$data["NDPercentage"];
    $MinTakeHomePercentage=$data["MinTakeHomePercentage"];

    if($SettingID > 0){

        DB::table('payroll_setting')
            ->where('ID',$SettingID)
            ->update([
                'NDPercentage' => $NDPercentage,
                'MinTakeHomePercentage' => $MinTakeHomePercentage
            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $SettingID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Payroll Employee Setting";
        $logData['TransType'] = "Update Payroll Employee Setting";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        $SettingID = DB::table('payroll_setting')
            ->insertGetId([
                'NDPercentage' => $NDPercentage,
                'MinTakeHomePercentage' => $MinTakeHomePercentage
                ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $SettingID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Payroll Employee Setting";
        $logData['TransType'] = "New Payroll Employee Setting";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $SettingID;

    }


public function doSavePayrollGovermentPremiumSetting($data){

    $Misc = new Misc();

    $TODAY = date("Y-m-d H:i:s");

    $SettingID=$data["SettingID"];
    $SSSSched=$data["SSSSched"];
    $HDMFSched=$data["HDMFSched"];
    $PHICSched=$data["PHICSched"];

    if($SettingID > 0){

        DB::table('payroll_setting')
            ->where('ID',$SettingID)
            ->update([
                'SSSSched' => $SSSSched,
                'HDMFSched' => $HDMFSched,
                'PHICSched' => $PHICSched

            ]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $SettingID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Payroll Goverment Premiums Setting";
        $logData['TransType'] = "Update Payroll Goverment Premiums Setting";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);
    
    }else{

        $SettingID = DB::table('payroll_setting')
            ->insertGetId([
                'SSSSched' => $SSSSched,
                'HDMFSched' => $HDMFSched,
                'PHICSched' => $PHICSched
                ]);  

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $SettingID;
        $logData['TransactedByID'] = Session::get('ADMIN_USER_ID');
        $logData['ModuleType'] = "Payroll Goverment Premiums Setting";
        $logData['TransType'] = "New Payroll Goverment Premiums Setting";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

    }

     return $SettingID;

    }


}

