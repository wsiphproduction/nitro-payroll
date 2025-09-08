<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use Mail;
use Session;
use Hash;
use View;
use Image;
use DB;
use Excel;
use PDF;

// use App\Models\Misc;
// use App\Models\AdminUsers;
// use App\Models\Report;

class AdminReportController extends Controller {

  function SetAdminInitialData($data){

        $AdminUsers = new AdminUsers();  
        $data["AdminUsers"]=$AdminUsers;

        return $data;
  }

  function IsAdminLoggedIn(){

    if (!Session('ADMIN_LOGGED_IN')) {
        Session::flash('session_expires','Your session has been expired. Please log-in again.');
        return false;
    }

    return true;
  }

   
}



