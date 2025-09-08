@extends('layout.adminweb')
@section('content')

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-12 mb-2 mt-1">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb p-0 mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="javascript:void(0);">
                                            <i class="bx bx-home-alt"></i>
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active">Change Password
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <section id="basic-input">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Change Password</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <fieldset class="form-group">
                                                        <label for="CurrentPassword">Current Password: <span class="required_field">*</span></label>
                                                        <input type="password" class="form-control" id="CurrentPassword" name="CurrentPassword" placeholder="Current Password">
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="NewPassword">New Password: <span class="required_field">*</span></label>
                                                        <input type="password" class="form-control" id="NewPassword" name="NewPassword"  placeholder="New Password">
                                                    </fieldset>
                                                    <fieldset class="form-group">
                                                        <label for="ConfirmNewPassword">Confirm New Password: <span class="required_field">*</span> </label>
                                                        <input type="password" class="form-control" id="ConfirmNewPassword" name="ConfirmNewPassword" placeholder="Confirm New Password">
                                                    </fieldset>
                                                      <div class="checkbox checkbox-sm">
                                                          <input id="chkShowPassword" type="checkbox" onclick="showPassword()" class="form-check-input">
                                                          <label class="checkboxsmall" for="chkShowPassword">
                                                            <small>Show Password</small>
                                                          </label>
                                                          <p style="color:#a94442;font-size:11px;line-height: 15px;">
                                                            Note: Password must be atleast 6 characters or more<br>
                                                            Consist either alpha or numeric password characters.
                                                          </p>
                                                      </div>
                                                  

                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button class="btn btn-primary" type="button" onclick="doChangePassword();">Change Password</button>
                                                </div>
                                            </div>
                                     
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <script type="text/javascript">

        function showPassword() {
          
          var x = document.getElementById("CurrentPassword");
          var y = document.getElementById("NewPassword");
          var z = document.getElementById("ConfirmNewPassword");

          if (x.type === "password") {
            x.type = "text";
          }else{
            x.type = "password";
          }

          if (y.type === "password") {
            y.type = "text";
          }else{
            y.type = "password";
          }

          if (z.type === "password") {
            z.type = "text";
          }else{
            z.type = "password";
          }

        }
         
    function doChangePassword(){

        var vCurrentPassword= $("#CurrentPassword").val();
        var vNewPassword= $("#NewPassword").val();
        var vConfirmNewPassword= $("#ConfirmNewPassword").val()
       
        $("#CurrentPassword").css({"border":"#ccc 1px solid"});
        $("#NewPassword").css({"border":"#ccc 1px solid"});
        $("#ConfirmNewPassword").css({"border":"#ccc 1px solid"});

        if(vCurrentPassword.trim()=="") {
          $("#CurrentPassword").css({"border":"#a94442 1px solid"});   
          toast('toast-error', "Enter current password");
         return;
       }else{
          $("#CurrentPassword").css({"border":"#ccc 1px solid"});      
       }

        if(vNewPassword.trim()=="") {
          $("#NewPassword").css({"border":"#a94442 1px solid"});   
          toast('toast-error', "Enter new password.");
         return;
       }else{
          $("#vNewPassword").css({"border":"#ccc 1px solid"});      
       }

       if(vConfirmNewPassword.trim()=="") {
          $("#ConfirmNewPassword").css({"border":"#a94442 1px solid"});   
          toast('toast-error', "Confirm new password");
         return;
       }else{
          $("#ConfirmNewPassword").css({"border":"#ccc 1px solid"});      
       }

       if(vNewPassword!=vConfirmNewPassword) {
          $("#ConfirmNewPassword").css({"border":"#a94442 1px solid"});   
          $("#NewPassword").css({"border":"#a94442 1px solid"});   
          toast('toast-error', "New password & confirm password does not matched.");
         return;
       }else{
          $("#ConfirmNewPassword").css({"border":"#ccc 1px solid"});      
          $("#NewPassword").css({"border":"#ccc 1px solid"});  
       }

       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                CurrentPassword: $("#CurrentPassword").val(),
                NewPassword: $("#NewPassword").val(),
                ConfirmNewPassword: $("#ConfirmNewPassword").val()
     
            },
            url: "{{ route('do-admin-change-password') }}",
            dataType: "json",
            success: function(data){

                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

                if(data.Response =='Success'){
                        toast('toast-success', data.ResponseMessage);
                          setTimeout(function () {
                                  location.reload();
                                 let toastMain = document.getElementsByClassName('toast-success')[0];
                                 toastMain.classList.remove("toast-show");
                          }, 2000);
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

@endsection



