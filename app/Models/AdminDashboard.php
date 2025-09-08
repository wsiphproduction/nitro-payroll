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

class AdminDashboard extends Model
{
    
    
    public function getDashboardTotals(){
     
     //TOTAL EMPLOYEE 	      
     $retVal['TotalEmployee']=DB::table('users')->count();     
     //TOTAL ACTIVE EMPLOYEE
     $retVal['TotalActiveEmployee']=DB::table('users')->where('status',1)->count();
     //TOTAL IN ACTIVE
     $retVal['TotalInActiveEmployee']=DB::table('users')->where('status',2)->count();     
     //TOTAL EMPLOYEE PMC Davao	      
     $retVal['TotalPMCDavao']=DB::table('users')->where('company_branch_id',2)->count();
     //TOTAL EMPLOYEE PMC Agusan
     $retVal['TotalPMCAgusan']=DB::table('users')->where('company_branch_id',1)->count();
     //$RetVal['TotalPMCAgusan']=DB::table('users')->where('company_branch_id',2)->value('TotalPayment');    
     return $retVal;

    }

}

