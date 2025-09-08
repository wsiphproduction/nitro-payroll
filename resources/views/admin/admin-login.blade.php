
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title> .:: NITRO PAYROLL ::. </title>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="NITRO Payroll">
    <meta name="author" content="NITRO Payroll">

    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{URL::asset('public/web/css/admin-login.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/vendors/css/vendors.min.css') }}">    
    <script src="{{ URL::to('public/admin/login/js/jquery.min.js') }}"></script>
</head>
<style>
  .close-toast:hover{
    background-color: transparent !important;
  }
   .bx bx-window-close:hover{
    background-color: transparent !important;
  }
  
 .toast-error-notification {
  z-index: 99999999;
  position: fixed;
  top: 100px;
  right: -310px;
  width: 300px;
  background-color: #bd2130;
  color: #fff;
  box-shadow: 0px 0px 4px #404040ba;
  border-radius: 4px 0 0 4px;
  text-align: center;
  padding:10px;

  a:link,
  a:visited,
  a:hover {
    color: #ffcb00;
    text-decoration: underline;
  }

  a:hover {
    color: #ffe062;
    text-decoration: underline;
  }

  .toast-content {

      width: 100%;
      text-align: center;
      padding: 5px;
      font-size: 16px;
      color:#fff;

    .close-toast {
      font-weight: 700;
/*      line-height: 1;*/
      opacity: 0.8;
      text-shadow: 0 1px 0 #fff;
/*      background-color: red;*/
/*      box-shadow: 1px 1px 2px black;*/
      padding: 4px;
      font-size: 20px;
/*      border-radius: 20px;*/
      line-height: 12px;
      color: #fff;
      width: 25px;
      left: -7px;
      position: absolute;
      top: -8px;
      cursor: pointer;
    }
    .close-toast:hover{
      background-color: black;
    }
    .toast-message {
/*      padding: 20px;*/
      color:#fff;
    }
  }
}

.toast-show {
  animation-name: showtoast;
  animation-duration: 6s;
}
@keyframes  showtoast {
  0% {
    right: -310px;
    opacity: 1;
  }
  5% {
    right: 0px;
    opacity: 1;
  }
  95% {
    right: 0px;
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}

 .toast-success-notification {
      z-index: 99999999;
      position: fixed;
      top: 100px;
      right: -310px;
      width: 300px;
      background-color: #119744;
      color: #fff;
      box-shadow: 0px 0px 4px #404040ba;
      border-radius: 4px 0 0 4px;
      text-align: center;
      padding:10px;

      a:link,
      a:visited,
        a:hover {
        color: #ffcb00;
        text-decoration: underline;
        }

        a:hover {
          color: #ffe062;
          text-decoration: underline;
        }

        .toast-content {

        width: 100%;
        text-align: center;
        padding: 5px;
        font-size: 16px;
           color:#fff;

        .close-toast {
          font-weight: 700;
/*          line-height: 1;*/
          opacity: 0.8;
          text-shadow: 0 1px 0 #fff;
        /*  background-color: red;
          box-shadow: 1px 1px 2px black;*/
          padding: 4px;
          font-size: 20px;
/*          border-radius: 20px;*/
          line-height: 12px;
          color: #fff;
          width: 25px;
          left: -7px;
          position: absolute;
          top: -8px;
          cursor: pointer;
          z-index:99999;
        }
        .close-toast:hover{
          background-color: black;
        }
        .toast-message {
/*          padding: 20px;*/
          color:#fff;
        }
        }
    }
    .x-success-close{
      left: 20px;
      border: 1px solid #fff;
      width: 15px;
      height: 17px;
      background: #fff;
      color: #4bd52c;
      font-weight: bold;
      float: left;
      border-radius: 3px;
      font-size: 13px;
      line-height: 15px;
    }

 .warning-msg {
    color: #9F6000;
    background-color: #FEEFB3;
    border-left: 5px solid #f68c1f;
    margin: 10px 0;
    padding: 10px;
    border-radius: 3px 3px 3px 3px;
}
  </style>
<body>
<div id="layout" class="theme-orange">
    <!-- WRAPPER -->
    <div id="wrapper">


      <!-- Message Nofit-->
      @include('inc.admin.adminmessage')

        <div class="d-flex h100vh align-items-center auth-main w-100">
            <div class="auth-box">
              
               
                <div class="card shadow p-lg-4">

                  @if(Session::has('session_expires'))
                    <div id="divSessionExpires" class="warning-msg" style="display:none;">
                      <i class="fa fa-warning"></i>
                      {{Session::get('session_expires')}}
                    </div>
                  @endif
                    
                    <div class="logo">
                        <img style="width:200px;" src="{{URL::asset('public/img/PMC-Company-Logo.png')}}">
                    </div>
                    <div class="card-header">
                        <p class="fs-5 mb-0">Login to your account</p>
                    </div>
                    <div class="card-body">
                            <div class="form-floating mb-1">
                                <input id="Username" type="text" class="form-control" placeholder="Username" style="color:#212529;" autocomplete="off">
                                <label>Username</label>
                            </div>
                            
                            <div class="form-floating" style="padding-bottom: 5px;">
                                <input id="UserPassword" type="password" class="form-control" placeholder="Password" style="color:#212529;" autocomplete="off">
                                <label>Password</label>
                            </div>

                            <div class="form-floating" style="padding-bottom: 15px;">
                                 <select id="PayrollPeriod" class="form-control custom-select" style="padding-top: 0px;padding-bottom: 0px;height:40px;font-size:14px;color:#212529;">
                                        <option value="">Select Payroll Period</option>

                                        @foreach($PayrollPeriodList as $list)
                                           <option value="{{$list->Code}}"> Period: {{$list->Code}} - {{date('m/d/Y',strtotime($list->StartDate))}} - {{date('m/d/Y',strtotime($list->EndDate))}}</option>
                                        @endforeach

                                    </select>
                            </div>
                            <button type="button" class="btn btn-primary w-100 px-3 py-2" onclick="doCheckLogin();">LOGIN</button>
                              <div class="mt-3 pt-3 border-top">
                                <div class="checkbox checkbox-sm">
                                    <input id="chkShowPassword" type="checkbox" onclick="showPassword()" class="form-check-input">
                                    <label class="checkboxsmall" for="chkShowPassword">
                                      <small>Show Password</small>
                                    </label>
                                </div>
                           </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END WRAPPER -->
</div>

<script type="text/javascript">

    $(document).ready(function(){
        $('#divSessionExpires').hide().show('slide').delay(6000).hide('slide');   
    });

      function showPassword() {
          var x = document.getElementById("UserPassword");
          if (x.type === "password") {
            x.type = "text";
          }else{
            x.type = "password";
          }
        }

     function toast(toastClassName, toastMessage) {

        let toastMain = document.getElementsByClassName(toastClassName)[0];
        let toastContent = document.getElementById("toast-error-message");
        
        if(toastClassName!='toast-error'){
             toastContent = document.getElementById("toast-success-message");
        }
        
        toastContent.innerHTML = toastMessage;
        toastMain.classList.remove("toast-show");
        
        setTimeout(function () {
          toastMain.classList.add("toast-show");
        }, 150);
        toastMain.addEventListener("click", function () {
          toastMain.classList.remove("toast-show");
        });
      
    }

  function buttonOneClick(vID, vLabel, vIsDisabled){
          var btn = $("#"+vID);
          if(vIsDisabled){
                btn.html('<img src="{{ URL::to('public/img/button-loader.gif') }}" style="max-height:15px;">');
                btn.attr("disabled", true);
          }else{
                vHtml = "<i class='bx bx-check d-block d-sm-none'></i>";
                vHtml += "<span class='d-none d-sm-block'>" + vLabel + "</span>";
                btn.html(vHtml);
                btn.removeAttr("disabled");
          }
    } 
                 
    function doCheckLogin(){

        var vUsername= $("#Username").val();
        var vUserPassword= $("#UserPassword").val();
        var vPayrollPeriod= $("#PayrollPeriod").val()
       
        $("#Username").css({"border":"#ccc 1px solid"});
        $("#UserPassword").css({"border":"#ccc 1px solid"});

        if(vUsername.trim()=="") {
          $("#Username").css({"border":"#a94442 1px solid"});   
          toast('toast-error', "Enter admin username.");
         return;
       }else{
          $("#Username").css({"border":"#ccc 1px solid"});      
       }

        if(vUserPassword.trim()=="") {
          $("#UserPassword").css({"border":"#a94442 1px solid"});   
          toast('toast-error', "Enter admin password.");
         return;
       }else{
          $("#UserPassword").css({"border":"#ccc 1px solid"});      
       }

        if(vPayrollPeriod.trim()=="") {
          $("#PayrollPeriod").css({"border":"#a94442 1px solid"});   
          toast('toast-error', "Select payroll period from the list.");
         return;
       }else{
          $("#PayrollPeriod").css({"border":"#ccc 1px solid"});      
       }

       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                Username: $("#Username").val(),
                UserPassword: $("#UserPassword").val(),
                PayrollPeriod: $("#PayrollPeriod").val()
     
            },
            url: "{{ route('do-admin-check-login') }}",
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

                if(data.Response =='Success'){
                    window.location.replace('{{URL::route('admin-dashboard')}}');
                }else{
                     toast('toast-error', data.ResponseMessage);
                     return;
                }
            },
            error: function(data){
                buttonOneClick("btnSaveRecord", "Save", false);
                console.log(data.responseText);
               
            },
            beforeSend:function(vData){
                buttonOneClick("btnSaveRecord", "", true);
            }
        });

    }

</script>

</body>
</html>