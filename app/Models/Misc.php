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

class Misc extends Model
{

  public function GetSettingsNextUploadBatchNo(){

    $info = DB::table('payroll_trans_no')
      ->selectraw("
        CAST(COALESCE(UploadBatchNo,'0') as INT) as CurrentNo
      ")
      ->first();  

    if(isset($info) > 0){
      $CurrentNo = $info->CurrentNo + 1;
      $CurrentNo = str_pad($CurrentNo, 5, "0", STR_PAD_LEFT);
      return $CurrentNo;
    }

    return 0;

  }

  public function SetSettingsNextUploadBatchNo($CurrentNo){
    $TODAY = date("Y-m-d H:i:s");

    DB::table('payroll_trans_no')
      ->update([
        'UploadBatchNo' => $CurrentNo
    ]);

    return true;

  }
  
  //TRANSACTION LOG
  public function doSaveTransactionLog($data){

    $TODAY = date("Y-m-d H:i:s");
    $TransRefID = $data['TransRefID'];
    $TransactedByID = $data['TransactedByID'];
    $ModuleType = $data['ModuleType'];
    $TransType = $data['TransType'];
    $Remarks = $data['Remarks'];

    DB::table('payroll_transaction_log')
        ->insert([
          'TransRefID' => $TransRefID,
          'TransactedByID' => $TransactedByID,
          'TransactionDate' => $TODAY,
          'ModuleType' => $ModuleType,
          'TransType' => $TransType,
          'Remarks' => $Remarks,
          'DateTimeCreated' =>$TODAY
        ]);

  }

  public function getTransactionLog($data){

    $ModuleType = $data['ModuleType'];
    $TransRefID = $data['TransRefID'];
    $Limit = $data['Limit'];
    $PageNo = $data['PageNo'];
    
    $query = DB::table('payroll_transaction_log as tlog')
        ->join('admin_users as transby', 'transby.AdminUserID', '=', 'tlog.TransactedByID')
        ->selectraw("
            COALESCE(tlog.TransactionID,0) as TransactionID,
            COALESCE(tlog.TransRefID,0) as TransRefID,

            COALESCE(tlog.TransactedByID,0) as TransactedByID,
            CONCAT(COALESCE(transby.FirstName,''),' ',COALESCE(transby.LastName,''),' ',if(COALESCE(transby.MiddleName,'') != '', CONCAT(LEFT(COALESCE(transby.MiddleName,''),1),'.'),'')) as TransactedBy,
            COALESCE(tlog.TransactionDate,'') as TransactionDate,

            COALESCE(tlog.ModuleType,'') as ModuleType,
            COALESCE(tlog.TransType,'') as TransType,
            COALESCE(tlog.Remarks,'') as Remarks,

            COALESCE(tlog.DateTimeCreated,'') as DateTimeCreated,
            COALESCE(tlog.DateTimeUpdated,'') as DateTimeUpdated
    ");

    if($TransRefID > 0){
      $query->where("tlog.TransRefID",$TransRefID);
    }
    
    if(!empty($ModuleType)){
      $query->where("tlog.ModuleType",$ModuleType);
    }

    if($Limit > 0){
      $query->limit($Limit);
      $query->offset(($PageNo-1) * $Limit);
    }

    $query->orderBy("tlog.TransactionDate","DESC");

    $list = $query->get();

    return $list;
}

  public function setNumeric($Value){

    $retVal = (empty($Value) ? "0" : $Value);
    $retVal = str_replace(",", "", $retVal);

    if(empty($retVal)){
        $retVal = "0";
    }

    return $retVal;
  }

  public function GenerateRandomNo($Length, $TableName, $FieldName){

    $MinNo = "1";
    $MaxNo = "9";
    for ($i=1; $i < $Length; $i++) {
        $MinNo = $MinNo . '0';
        $MaxNo = $MaxNo . '9';
    }

    $MinNo = $MinNo + 0;
    $MaxNo = $MaxNo + 0;

    $GeneratedNo  = mt_rand($MinNo, $MaxNo);

    if($TableName != '' && !empty($GeneratedNo)){
        $check = DB::table($TableName)
          ->select($FieldName)
          ->where($FieldName,$GeneratedNo)
          ->first();
        if(isset($check) <= 0){
            return $GeneratedNo;
        }else{
            $this->GenerateRandomNo($Length, $TableName, $FieldName);
        }
    }else{
        return $GeneratedNo;
    }

  }

  // VALIDATE EMAIL 
  public function IsValidEmail($Email){

    if(filter_var($Email, FILTER_VALIDATE_EMAIL)){
      return true;
    }

    return false;
  }
  
  
  // RESIZE UPLOADED PHOTO
  public function ResizePhoto($data)
  {
    $image_uploaded = $data["ImageUpload"];
    $path = $data["Path"];
    $autoscale = $data["AutoScale"];
    $posx = $data["PosX"];
    $posy = $data["PosY"];
    $width = $data["Width"];
    $height = $data["Height"];
    $max_width = $data["MaxWidth"];
    $max_height = $data["MaxHeight"];
    $filename = $data["FileName"];

    $IsResizeImage = false;
    if(isset($data["IsResizeImage"])){
        $IsResizeImage = $data["IsResizeImage"];
    }
    switch($_FILES[$image_uploaded]['type'])
    {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($_FILES[$image_uploaded]['tmp_name']);
            break;
        case 'image/png':
            $image = imagecreatefrompng($_FILES[$image_uploaded]['tmp_name']);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($_FILES[$image_uploaded]['tmp_name']);
            break;
        default:
            exit('Unsupported type: '.$_FILES[$image_uploaded]['type']);
    }

    // Get current dimensions
    $old_width  = imagesx($image);
    $old_height = imagesy($image);
    if($IsResizeImage && $old_width > $max_width && ($posx > 0 || $posy > 0 )){

        // Calculate the scaling we need to do to fit the image inside our frame
        $scale = $max_width/$old_width;

        // Get the new dimensions
        $new_width  = ceil($scale*$old_width);
        $new_height = ceil($scale*$old_height);

//            $posx = ceil($posx * $scale);
//            $posy = ceil($posy * $scale);
//            $width = ceil($width * $scale);
//            $height = ceil($height * $scale);

        // Create new empty image
        $new = imagecreatetruecolor($new_width, $new_height);

        //allow transparency for pngs
        imagealphablending($new, false);
        imagesavealpha($new, true);

        $transparent = imagecolorallocatealpha($new, 255, 255, 255, 127);
        imagefilledrectangle($new, 0, 0, $new_width, $new_height, $transparent);

        // Resize old image into new
        imagecopyresampled($new, $image,
            0, 0, 0, 0,
            $new_width, $new_height, $old_width, $old_height);

        $image = $new;

    }

    if($posx == 0 && $posy == 0){
       $autoscale = true;
    }

    //Actual resizing
    if($autoscale){

        // Get current dimensions
        $old_width  = imagesx($image);
        $old_height = imagesy($image);

        // Calculate the scaling we need to do to fit the image inside our frame
        if($max_width == 0){
            $scale = $max_height/$old_height;
        }elseif($max_height == 0){
            $scale = $max_width/$old_width;
        }else{
            $scale = min($max_width/$old_width, $max_height/$old_height);
        }

        // Get the new dimensions
        if($IsResizeImage && $posx == 0 && $posy == 0){
            $new_width  = ceil($width * $scale);
            $new_height = ceil($height * $scale);
        }else{
            $new_width  = ceil($scale*$old_width);
            $new_height = ceil($scale*$old_height);
        }

    }else{

        $old_width  = ceil($width);
        $old_height = ceil($height);
        $new_width  = ceil($width);
        $new_height = ceil($height);
    }

    // Create new empty image
    $new = imagecreatetruecolor($new_width, $new_height);

    //allow transparency for pngs
    imagealphablending($new, false);
    imagesavealpha($new, true);

    $transparent = imagecolorallocatealpha($new, 255, 255, 255, 127);
    imagefilledrectangle($new, 0, 0, $new_width, $new_height, $transparent);

    // Resize old image into new
    imagecopyresampled($new, $image,
        0, 0, $posx, $posy,
        $new_width, $new_height, $old_width, $old_height);


    $newfilename = $path.$filename;
    #create folder if not exist
    if(!file_exists($path)){mkdir($path ,0777, TRUE);}

    //Delete File if exist
    if(is_file($newfilename)){unlink($newfilename);}

    $file_parts = pathinfo($newfilename);
    switch($file_parts['extension'])
    {
        case "jpg":
            imagejpeg($new, $newfilename);
            break;
        case "png":
            imagepng($new, $newfilename);
            break;
    }

    // Destroy resources
    imagedestroy($image);
    imagedestroy($new);

    return true;
  }  
  
    

}

