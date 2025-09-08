@extends('layout.adminweb')
@section('content')

  
<style>
@import url(&quot;https://fonts.googleapis.com/css2?family=Inter:wght@400;500&amp;display=swap&quot;);

button.accordion.is-open {
    background-color: #f68c1f !important;
    color: white !important;
}

button.accordion:hover{
    background-color: #475F7B !important;
    color: white !important;
}

.card {
  padding: 16px;
  min-width: 450px;
  max-width: 70%;
  display: grid;
  gap: 24px;
  background: #ffffff;
  box-shadow: rgba(0, 0, 0, 0.04) 0px 1px 3px, rgba(0, 0, 0, 0.06) 0px 1px 2px;
  border-radius: 4px;
}
.card article .title {
  color: #374151;
  font-weight: 500;
  font-size: 13px;
  line-height: 150%;
  color: #f68c1f !important;
}
.card article .title p {
  font-weight: 400;
  font-size: 14px;
  line-height: 20px;
  color: #6b7280;
}

fieldset {
  display: flex;
  flex-direction: column;
}

label {
  color: #6b7280;
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  font-size: 14px;
  line-height: 20px;
}

.input-group {
  display: flex;
  align-items: center;
  height: 36px;
}
.input-group span {
  display: flex;
  color: #9ca3af;
  height: 100%;
  background: #f3f4f6;
  padding: 8px 16px;
  font-size: 14px;
  line-height: 20px;
  box-shadow: 0px 1px 2px rgba(31, 41, 55, 0.08);
}
.input-group span:last-child {
  border-right: 1px solid #e5e7eb;
  border-bottom: 1px solid #e5e7eb;
  border-top: 1px solid #e5e7eb;
  border-radius: 0px 4px 4px 0px;
}
.input-group span:first-child {
  border-radius: 4px 0px 0px 4px;
  border-left: 1px solid #e5e7eb;
  border-bottom: 1px solid #e5e7eb;
  border-top: 1px solid #e5e7eb;
}
.input-group input[type=text] {
  font-family: &quot;Inter&quot;;
  font-style: normal;
  font-weight: 400;
  font-size: 14px;
  line-height: 20px;
  border-radius: 0;
  box-shadow: 0px 1px 2px rgba(31, 41, 55, 0.08);
  outline: none;
  color: #374151;
  height: 100%;
  background: #ffffff;
  padding: 8px;
  border: 1px solid #e5e7eb;
}

input[type=text] {
  font-family: &quot;Inter&quot;;
  font-style: normal;
  box-shadow: 0px 1px 2px rgba(31, 41, 55, 0.08);
  border-radius: 4px;
  font-weight: 400;
  font-size: 14px;
  line-height: 20px;
  outline: none;
  color: #374151;
  height: 100%;
  background: #ffffff;
  padding: 8px;
  border: 1px solid #e5e7eb;
  transition: 0.16s ease-in;
}
input[type=text]::placeholder {
  color: #d1d5db;
}
input[type=text]:focus {
  box-shadow: 0 0 0 3px #e0e8ff;
  z-index: 9;
  border-color: #5048e5;
}

.input-icon {
  display: flex;
}
.input-icon .icon {
  position: absolute;
  height: 35px;
  width: 35px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.input-icon .icon svg {
  stroke: #9ca3af;
  width: 20px;
  height: 20px;
}
.input-icon input {
  padding-left: 35px;
}

.checkbox {
  display: flex;
  align-items: center;
}
.checkbox input[type=checkbox] {
  height: 18px;
}
.checkbox input[type=checkbox].switch {
  overflow: hidden;
  border: 0;
  border-radius: 999px;
  width: 30px;
  appearance: none;
  display: flex;
  align-items: center;
  background: #fff;
  margin: 0;
  position: relative;
  border: 1px solid #d1d5db;
  transition: 0.2s ease-in;
  cursor: pointer;
}
.checkbox input[type=checkbox].switch:before {
  content: &quot;&quot;;
  width: 12px;
  height: 12px;
  transform: translatex(0);
  position: absolute;
  border-radius: 50%;
  margin: 2px;
  background: #d1d5db;
  transition: 0.2s ease-in;
}
.checkbox input[type=checkbox].switch:checked {
  background: #5048e5;
  border-color: #5048e5;
}
.checkbox input[type=checkbox].switch:checked:before {
  transform: translatex(100%);
  background: #fff;
}
.checkbox input[type=checkbox].switch ~ label {
  cursor: pointer;
  color: #374151;
  font-weight: 400;
  margin: 0 0 0 10px;
}

.flex {
  display: flex;
}

p {
  color: #6b7280;
}

.grid {
  display: grid;
}
.grid.col-2 {
  grid-template-columns: 0.5fr 0.5fr;
}

.gap-big {
  gap: 24px;
}
.gap-medium {
  gap: 12px;
}
</style>

<style type="text/css">
    
button.accordion {
  width: 100%;
  background-color: whitesmoke;
  border: none;
  outline: none;
  text-align: left;
  padding: 15px 20px;
  font-size: 15px;
  color: #333;
  cursor: pointer;
  transition: background-color 0.2s linear;
  margin-bottom: 20px;
}

/*button.accordion:after {
  font-size: 15px;
  float: right;
}

button.accordion.is-open:after {
  content: "\f151";
}*/

button.accordion:hover,
button.accordion.is-open {
  background-color: #ddd;
}

.accordion-content {
  background-color: white;
  border-left: 1px solid whitesmoke;
  border-right: 1px solid whitesmoke;
  padding: 0 20px;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.2s ease-in-out;
}

</style>

@php($PayrollSettingID=0) 
@php($CompanyLogoFileName='') 
@php($CompanyDomainWebsite='')
@php($CompanyDomainName='')
@php($CompanyCode='')   
@php($CompanyName='')         
@php($EmailAddress='')
@php($MobileNo='')
@php($PhoneNo='')     
@php($FaxNo='')        

@php($Address='')         
@php($City='')         
@php($PostalCode='')         
@php($Country='')

@php($ClosingDate='')

@php($NDPercentage=0)
@php($MinTakeHomePercentage=0)

@php($SSSSched=0)
@php($HDMFSched=0)
@php($PHICSched=0)

@if(isset($PayrollSetting)>0)
    
    @php($PayrollSettingID=$PayrollSetting->ID)
    @php($CompanyLogoFileName=$PayrollSetting->CompanyLogo) 
    @php($CompanyCode=$PayrollSetting->CompanyCode)
    @php($CompanyDomainWebsite=$PayrollSetting->DomainWebsite)
    @php($CompanyName=$PayrollSetting->CompanyName)         
    @php($EmailAddress=$PayrollSetting->EmailAddress)
    @php($MobileNo=$PayrollSetting->MobileNo)
    @php($PhoneNo=$PayrollSetting->PhoneNo)     
    @php($FaxNo=$PayrollSetting->FaxNo)        

    @php($Address=$PayrollSetting->Address)         
    @php($City=$PayrollSetting->City)         
    @php($PostalCode=$PayrollSetting->PostalCode) 
    @php($Country=$PayrollSetting->Country) 

    @php($ClosingDate=$PayrollSetting->ClosingDate) 

    @php($NDPercentage=$PayrollSetting->NDPercentage)
    @php($MinTakeHomePercentage=$PayrollSetting->MinTakeHomePercentage)  

    @php($SSSSched=$PayrollSetting->SSSSched)
    @php($HDMFSched=$PayrollSetting->HDMFSched)  
    @php($PHICSched=$PayrollSetting->PHICSched)  

@endif

@if($ClosingDate!='' || $ClosingDate!=null)
   @php($ClosingDate=date('m/d/Y',strtotime($PayrollSetting->ClosingDate)))
@else
   @php($ClosingDate='')
@endif


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
                                    <li class="breadcrumb-item active">  Payroll Settings
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
                        <div class="col-md-12">
                            <div class="card" style="gap:0px;">
                                <div class="card-header">
                                    <h4 class="card-title"> <i class='bx bx-cog mr-1'></i> Payroll Settings</h4>
                                    <br>                                    
                                      <p>
                                          Below are some Payroll Settings that serve as the default configurations that ensure the accurate management of processing schedule of payroll. These settings include such payroll closing date, which specifies the deadline for completing payroll processing for each pay period.                                                                                                                    
                                            Payroll Settings also capture the company-related information, including the company's official name, contact details, and physical address. This information  will reflect in payroll-related documentation reflects the correct company information such as payslip and other printing reports.                                            
                                            <br><br>
                                            The settings also cover employee-related configurations, such as deduction schedules, pay frequencies (e.g., weekly, bi-weekly, monthly), and overtime rules,ensuring that employees are compensated according to their employment terms. With in the Payroll Settings just be mindful on configuring Payroll Settings, to ensure accurate, timely, and compliant payroll processing, fostering trust and satisfaction among employees while minimizing the risk of errors or regulatory non-compliance.

                                      </p>
                                </div>
                                <div class="card-content">
                                  <input id="SettingID" type="hidden" value="{{$PayrollSettingID}}">
                                  <div class="container">
                                  <button class="accordion" style="border-top-left-radius: 20px;border-top-right-radius: 20px;"> <i class="bx bx-calendar"></i> Payroll Closing Date </button>
                                  <div class="accordion-content">
                                    <article class="card">
                                            <article class="grid gap-big">
                                            <fieldset>
                                            <label>Payroll Closing Date: <span class="required_field">*</span> <span style="font-size:13px;text-transform: lowercase;color:red;">mm/dd/yyyy</span></label>
                                                <div class="div-percent">
                                                  <input id="CloseDate" type="text" class="form-control" placeholder="mm/dd/yyyy" value="{{$ClosingDate}}" autocomplete="off"><span class='percent-sign'> <i class="bx bx-calendar" style="line-height: 18px;"></i> </span>
                                                </div>
                                            </fieldset>

                                            <center>
                                                @if(Session::get('IS_SUPER_ADMIN') || $Allow_Edit_Update==1)     
                                                  <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="SavePayrollClosingDateRecord()">
                                                      <i class="bx bx-save"></i> Save Closing Date  Setting
                                                  </button> 
                                                @endif  
                                            </center>

                                        </article>
                                   </article>
                                  </div>

                               <button class="accordion" style="border-top-left-radius: 20px;border-top-right-radius: 20px;"><i class="bx bx-buildings" ></i> Company Information </button>
                                  <div class="accordion-content">
                                    <article class="card">
                                        <article class="grid gap-big">

                                            <input id="SettingID" type="hidden" value="{{$PayrollSettingID}}">
                                            <fieldset>
                                                <label>Website</label>
                                                <div class="input-group">
                                                    <span>
                                                        https://www.
                                                    </span>
                                                    <input id="CompWebsite" type="text" value="{{$CompanyDomainWebsite}}" autocomplete="off"> 
                                                    <span>
                                                        .com
                                                    </span>
                                                </div>
                                            </fieldset>

                                        </article>

                                         <article class="grid gap-big">
                                            <div class="title">.: Company Information :.</div>
                                            <fieldset>
                                                <label>Company Code: <span class="required_field">*</span></label>
                                                <input id="CompCode" type="text" placeholder="Company Code" value="{{$CompanyCode}}"autocomplete="off">
                                            </fieldset>
                                            <fieldset>
                                                <label>Company Name: <span class="required_field">*</span></label>
                                                <input id="CompName" type="text" placeholder="Company Name" value="{{$CompanyName}}"autocomplete="off">
                                            </fieldset>
                                        </article>

                                        <hr>

                                        <article class="grid gap-big">
                                            <div class="title">.: Contact Information :.</div>
                                            <div class="grid col-12 gap-medium">
                                                <fieldset>
                                                    <label>Email Address: <span class="required_field">*</span></label>
                                                    <input id="CompEmailAddress" type="text" placeholder="Email" value="{{$EmailAddress}}" autocomplete="off">
                                                </fieldset>
                                                <fieldset>
                                                    <label>Phone Number: <span class="required_field">*</span></label>
                                                    <input id="CompPhoneNo" type="text" placeholder="Telephone Number" value="{{$PhoneNo}}" autocomplete="off">
                                                </fieldset>
                                                <fieldset>
                                                    <label>Mobile Number: <span class="required_field">*</span></label>
                                                    <input id="CompMobileNo" type="text" placeholder="Mobile Number" value="{{$MobileNo}}" autocomplete="off">
                                                </fieldset>
                                                <fieldset>
                                                    <label>Fax Number: <span class="required_field">*</span></label>
                                                    <input id="CompFaxNo" type="text" placeholder="FaxNumber" value="{{$FaxNo}}" autocomplete="off">
                                                </fieldset>
                                            </div>
                                        </article>

                                         <hr> 

                                        <article class="grid gap-big">
                                            <div class="title">.: Address Information :.</div>
                                            <div class="grid col-12 gap-medium">
                                                <fieldset>
                                                    <label>Address: <span class="required_field">*</span></label>
                                                    <input id="CompAddress" type="text" placeholder="Adress" value="{{$Address}}" autocomplete="off">
                                                </fieldset>
                                                <fieldset>
                                                    <label>City: <span class="required_field">*</span></label>
                                                    <input id="CompCity" type="text" placeholder="City" value="{{$City}}" autocomplete="off">
                                                </fieldset>
                                                <fieldset>
                                                    <label>Postal Code: <span class="required_field">*</span></label>
                                                    <input id="CompPostalCode" type="text" placeholder="Postal Code" value="{{$PostalCode}}" autocomplete="off">
                                                </fieldset>
                                                  <fieldset>
                                                    <label>Country: <span class="required_field">*</span></label>
                                                    <input id="CompCountry" type="text" placeholder="Postal Code" value="{{$Country}}" autocomplete="off">
                                                </fieldset>
                                            </div>
                                        </article>

                                         <center>
                                            @if(Session::get('IS_SUPER_ADMIN') || $Allow_Edit_Update==1)     
                                           <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="SaveCompanyRecord()">
                                                <i class="bx bx-save"></i> Save Company Information Setting
                                            </button> 
                                            @endif
                                         </center>

                                    </article> 


                                  </div>

                                <button class="accordion" style="border-top-left-radius: 20px;border-top-right-radius: 20px;"> <i class="bx bx-user"></i> Employee Settings </button>
                                  <div class="accordion-content">
                                    <article class="card">
                                            <article class="grid gap-big">
                                            <fieldset>
                                            <label>Night Differential Percentage: <span class="required_field">*</span> </label>
                                                <div class="div-percent">
                                                  <input id="NDPercentage" type="text" class="form-control" placeholder=">Night Differential Percentage" value="{{$NDPercentage}}" autocomplete="off">
                                                </div>
                                            </fieldset>

                                             <fieldset>
                                            <label>Minimum Take Home Percentage: <span class="required_field">*</span> </label>
                                                <div class="div-percent">
                                                  <input id="MinTakeHomePercentage" type="text" class="form-control" placeholder="Minimum Take Home Percentage" value="{{$MinTakeHomePercentage}}" autocomplete="off">
                                                </div>
                                            </fieldset>

                                    <center>
                                        @if(Session::get('IS_SUPER_ADMIN') || $Allow_Edit_Update==1)
                                         <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="SaveEmployeeSettings()">
                                              <i class="bx bx-save"></i> Save Employee Settings 
                                          </button> 
                                       @endif   
                                      </center>

                                        </article>
                                   </article>
                                  </div>

                                <button class="accordion" style="border-top-left-radius: 20px;border-top-right-radius: 20px;"> <i class="bx bx-calculator"></i> Goverment Premiums Deduction </button>
                                  <div class="accordion-content">
                                    <article class="card">
                                            <article class="grid gap-big">
                                            <fieldset class="form-group">
                                            <label for="Status">SSS Schedule Deduction: <span class="required_field">*</span></label>
                                            <div class="form-group">
                                                <select id="SSSSched" class="form-control">
                                                   <option value="">Please Select</option>
                                                    <option value="{{ config('app.PERIOD_1ST_HALF_ID') }}" {{$SSSSched==1 ? 'selected' : '' }}>1ST HALF</option>
                                                    <option value="{{ config('app.PERIOD_2ND_HALF_ID') }}" {{$SSSSched==2 ? 'selected' : '' }}>2ND HALF</option>
                                                    <option value="{{ config('app.PERIOD_EVERY_CUTOFF_ID') }}" {{$SSSSched==3 ? 'selected' : '' }}>EVERY CUTOFF</option>
                                                </select>
                                            </div>
                                            </fieldset>

                                            <fieldset class="form-group">
                                            <label for="Status">PHIC Schedule Deduction: <span class="required_field">*</span></label>
                                            <div class="form-group">
                                                <select id="PHICSched" class="form-control">
                                                   <option value="">Please Select</option>
                                                    <option value="{{ config('app.PERIOD_1ST_HALF_ID') }}" {{$PHICSched==1 ? 'selected' : '' }}>1ST HALF</option>
                                                    <option value="{{ config('app.PERIOD_2ND_HALF_ID') }}" {{$PHICSched==2 ? 'selected' : '' }}>2ND HALF</option>
                                                    <option value="{{ config('app.PERIOD_EVERY_CUTOFF_ID') }}" {{$PHICSched==3 ? 'selected' : '' }}>EVERY CUTOFF</option>
                                                </select>
                                            </div>
                                            </fieldset>

                                            <fieldset class="form-group">
                                            <label for="Status">HDMF Schedule Deduction: <span class="required_field">*</span></label>
                                            <div class="form-group">
                                                <select id="HDMFSched" class="form-control">
                                                   <option value="">Please Select</option>
                                                    <option value="{{ config('app.PERIOD_1ST_HALF_ID') }}" {{$HDMFSched==1 ? 'selected' : '' }}>1ST HALF</option>
                                                    <option value="{{ config('app.PERIOD_2ND_HALF_ID') }}" {{$HDMFSched==2 ? 'selected' : '' }}>2ND HALF</option>
                                                    <option value="{{ config('app.PERIOD_EVERY_CUTOFF_ID') }}" {{$HDMFSched==3 ? 'selected' : '' }}>EVERY CUTOFF</option>
                                                </select>
                                            </div>
                                            </fieldset>

                                    <center>
                                       @if(Session::get('IS_SUPER_ADMIN') || $Allow_Edit_Update==1)
                                          <button type="button" class="btn btn-icon btn-outline-primary mr-1 mb-1" onclick="SaveGovermentPremiumSettings()">
                                            <i class="bx bx-save"></i> Save Goverment Premiums Settings 
                                           </button> 
                                        @endif
                                     </center>

                                    </article>
                                   </article>
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
</section>

<script type="text/javascript">


const accordionBtns = document.querySelectorAll(".accordion");

accordionBtns.forEach((accordion) => {
  accordion.onclick = function () {
    this.classList.toggle("is-open");

    let content = this.nextElementSibling;
    // console.log(content);

    if (content.style.maxHeight) {
      //this is if the accordion is open
      content.style.maxHeight = null;
    } else {
      //if the accordion is currently closed
      content.style.maxHeight = content.scrollHeight + "px";
      // console.log(content.style.maxHeight);
    }
  };
});


 $( function() {
    $( "#CloseDate").datepicker();

  } );
 
    function SaveCompanyRecord(){

        var vSettingID =$("#SettingID").val();
        var vCompCode= $("#CompCode").val();
        var vCompanyName= $("#CompName").val();
        var vCompanyFaxNo= $("#CompFaxNo").val();
        var vCompanyTelNo= $("#CompPhoneNo").val();
        var vCompanyMobileNo= $("#CompMobileNo").val();
        var vCompanyWebsite= $("#CompWebsite").val();
        var vCompanyEmailAddress= $("#CompEmailAddress").val();

        var vCompanyAddress= $("#CompAddress").val();
        var vCompanyCity= $("#CompCity").val();
        var vCompanyPostalCode= $("#CompPostalCode").val();
       
        $("#CompCode").css({"border":"#ccc 1px solid"});
        $("#CompName").css({"border":"#ccc 1px solid"});
        $("#CompFaxNo").css({"border":"#ccc 1px solid"}); 
        $("#CompPhoneNo").css({"border":"#ccc 1px solid"}); 
        $("#CompMobileNo").css({"border":"#ccc 1px solid"});  
        $("#CompWebsite").css({"border":"#ccc 1px solid"}); 
        $("#CompEmailAddress").css({"border":"#ccc 1px solid"}); 

        $("#CompAddress").css({"border":"#ccc 1px solid"});
        $("#CompCity").css({"border":"#ccc 1px solid"});  
        $("#CompPostalCode").css({"border":"#ccc 1px solid"});  

        if(vCompCode.trim()=="") {
          $("#CompCode").css({"border":"#a94442 1px solid"});   
          showHasErrorMessage('CompCode', "Enter company code.");
         return;
       }else{
          $("#CompCode").css({"border":"#ccc 1px solid"});      
       }

       if(vCompanyName.trim()=="") {
          $("#CompName").css({"border":"#a94442 1px solid"});   
          showHasErrorMessage('CompName', "Enter company name.");
         return;
       }else{
          $("#CompName").css({"border":"#ccc 1px solid"});      
       }
       
        if(vCompanyEmailAddress=="") {
         $("#CompEmailAddress").css({"border":"#a94442 1px solid"});   
          showHasErrorMessage('CompEmailAddress', "Enter company email address.");
         return;
       }else{
          $("#CompEmailAddress").css({"border":"#ccc 1px solid"});      
       }

       if(vCompanyTelNo=="") {
         $("#CompPhoneNo").css({"border":"#a94442 1px solid"});   
          showHasErrorMessage('CompPhoneNo', "Enter company phone no.");
          return;
       }else{
          $("#CompPhoneNo").css({"border":"#ccc 1px solid"});      
       }

       if(vCompanyMobileNo.trim()=="") {
         $("#CompMobileNo").css({"border":"#a94442 1px solid"});   
          showHasErrorMessage('CompMobileNo', "Enter remakrs.");
          return;
       }else{
          $("#CompMobileNo").css({"border":"#ccc 1px solid"});      
       }

        if(vCompanyFaxNo.trim()=="") {
         $("#CompFaxNo").css({"border":"#a94442 1px solid"});   
          showHasErrorMessage('CompFaxNo', "Enter company fax no.");
          return;
       }else{
          $("#CompFaxNo").css({"border":"#ccc 1px solid"});      
       }
   
       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SettingID: $("#SettingID").val(),
                CompanyCode: $("#CompCode").val(),
                CompanyName: $("#CompName").val(),
                CompanyWebsite: $("#CompWebsite").val(),
                CompanyEmailAddress: $("#CompEmailAddress").val(),
                CompanyPhoneNo: $("#CompPhoneNo").val(),
                CompanyMobileNo: $("#CompMobileNo").val(),
                CompanyFaxNo: $("#CompFaxNo").val(),
                CompanyAddress: $("#CompAddress").val(),
                CompanyCity: $("#CompCity").val(),
                CompanyPostalCode: $("#CompPostalCode").val(),
                CompanyCountry: $("#CompCountry").val()
            },
            url: "{{ route('do-save-payroll-setting') }}",
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
                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

               
                console.log(data.responseText);
               
            },
            beforeSend:function(vData){
                $("#divLoader").show();
                buttonOneClick("btnSaveRecord", "", true);
            }
        });

    }

    function SavePayrollClosingDateRecord(){

        var vSettingID =$("#SettingID").val();
        var vCloseDate= $("#CloseDate").val();
       
        $("#CloseDate").css({"border":"#ccc 1px solid"});

        if(vCloseDate.trim()=="") {
          $("#CloseDate").css({"border":"#a94442 1px solid"});   
          showHasErrorMessage('CloseDate', "Enter payroll closing date setting.");
         return;
       }else{
          $("#CloseDate").css({"border":"#ccc 1px solid"});      
       }

   
       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SettingID: $("#SettingID").val(),
                ClosingDate: $("#CloseDate").val()
     
            },
            url: "{{ route('do-save-payroll-setting-closing-date') }}",
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
                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

               
                console.log(data.responseText);
               
            },
            beforeSend:function(vData){
                $("#divLoader").show();
                buttonOneClick("btnSaveRecord", "", true);
            }
        });

    }

    function SaveEmployeeSettings(){

        var vSettingID =$("#SettingID").val();
        var vNDPercentage= $("#NDPercentage").val();
        var vMinTakeHomePercentage= $("#MinTakeHomePercentage").val();
       
        $("#NDPercentage").css({"border":"#ccc 1px solid"});
        $("#MinTakeHomePercentage").css({"border":"#ccc 1px solid"});

        if(vNDPercentage.trim()=="") {
          $("#NDPercentage").css({"border":"#a94442 1px solid"});   
          showHasErrorMessage('NDPercentage', "Enter night differential percentage setting.");
         return;
       }else{
          $("#NDPercentage").css({"border":"#ccc 1px solid"});      
       }

        if(vMinTakeHomePercentage.trim()=="") {
          $("#MinTakeHomePercentage").css({"border":"#a94442 1px solid"});   
          showHasErrorMessage('MinTakeHomePercentage', "Enter minimum take home percentage setting.");
         return;
       }else{
          $("#MinTakeHomePercentage").css({"border":"#ccc 1px solid"});      
       }

   
       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SettingID: $("#SettingID").val(),
                NDPercentage: $("#NDPercentage").val(),
                MinTakeHomePercentage: $("#MinTakeHomePercentage").val()
     
            },
            url: "{{ route('do-save-payroll-employee-setting') }}",
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
                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

               
                console.log(data.responseText);
               
            },
            beforeSend:function(vData){
                $("#divLoader").show();
                buttonOneClick("btnSaveRecord", "", true);
            }
        });

    }

    function SaveGovermentPremiumSettings(){

        var vSettingID =$("#SettingID").val();
        var SSSSched= $("#SSSSched").val();
        var vPHICSched= $("#PHICSched").val();
        var vHDMFSched= $("#HDMFSched").val();
       
        $("#SSSSChed").css({"border":"#ccc 1px solid"});
        $("#PHICSched").css({"border":"#ccc 1px solid"});
        $("#HDMFSched").css({"border":"#ccc 1px solid"});

        if(SSSSched.trim()=="") {
          $("#SSSSched").css({"border":"#a94442 1px solid"});   
          showHasErrorMessage('SSSSched', "Enter SSS goverment premium deduction schedule for setting.");
         return;
       }else{
          $("#NDPercentage").css({"border":"#ccc 1px solid"});      
       }

       if(vPHICSched.trim()=="") {
          $("#PHICSched").css({"border":"#a94442 1px solid"});   
         showHasErrorMessage('PHICSched', "Enter PHICS goverment premium deduction schedule for setting.");
         return;
       }else{
          $("#PHICSched").css({"border":"#ccc 1px solid"});      
       }

        if(vHDMFSched.trim()=="") {
          $("#HDMFSched").css({"border":"#a94442 1px solid"});   
         showHasErrorMessage('HDMFSched', "Enter HDMF goverment premium deduction schedule for setting.");
         return;
       }else{
          $("#HDMFSched").css({"border":"#ccc 1px solid"});      
       }

       // SAVE DATA
        $.ajax({
            type: "post",
            data: {
                _token: '{{ csrf_token() }}',
                Platform: "{{ config('app.PLATFORM_ADMIN') }}",
                SettingID: $("#SettingID").val(),
                SSSSched: $("#SSSSched").val(),
                PHICSched: $("#PHICSched").val(),
                HDMFSched: $("#HDMFSched").val()
     
            },
            url: "{{ route('do-save-payroll-goverment-premiums-setting') }}",
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
                $("#divLoader").hide();
                buttonOneClick("btnSaveRecord", "Save", false);

               
                console.log(data.responseText);
               
            },
            beforeSend:function(vData){
                $("#divLoader").show();
                buttonOneClick("btnSaveRecord", "", true);
            }
        });

    }
  
</script>

@endsection



