
<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
       
      <link rel="icon" href="favicon.ico" type="image/x-icon">
      <title>.:: NITRO PAYROLL ::. | {{ $Page }}</title>

  <link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/css/bootstrap.css') }}">
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="{{ URL::to('public/admin/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">

<script src="{{URL::to('public/admin/app-assets/vendors/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
 <script src="{{ URL::to('public/admin/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>


    <style type="text/css">
  
  .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    padding: 5px;
    }

    .table th, .table td{
      border-top: 0px;
    }
    .row{
      margin-right:auto;
      margin-left:auto;
    }


        @media screen {
            .page-break { height:10px; background:url(page-break.gif) 0 center repeat-x; border-top:1px dotted #999; margin-bottom:13px; }
        }
        @media print {
            .page-break { height:0; page-break-before:always; margin:0; border-top:none; }
        }
    </style>
</head>
<body onload="window.print();">

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


@if(isset($CompanyInfo)>0)
    
    @php($PayrollSettingID=$CompanyInfo->ID)
    @php($CompanyLogoFileName=$CompanyInfo->CompanyLogo) 
    @php($CompanyCode=$CompanyInfo->CompanyCode)
    @php($CompanyDomainWebsite=$CompanyInfo->DomainWebsite)
    @php($CompanyName=$CompanyInfo->CompanyName)         
    @php($EmailAddress=$CompanyInfo->EmailAddress)
    @php($MobileNo=$CompanyInfo->MobileNo)
    @php($PhoneNo=$CompanyInfo->PhoneNo)     
    @php($FaxNo=$CompanyInfo->FaxNo)        

    @php($Address=$CompanyInfo->Address)         
    @php($City=$CompanyInfo->City)         
    @php($PostalCode=$CompanyInfo->PostalCode) 
    @php($Country=$CompanyInfo->Country) 

@endif

@php($EmployeeNo='')   @php($Department='') @php($Division='')
@php($EmployeeName='') @php($Section='')  @php($Position='')

@php($IncomeDeductionTypeCode='') @php($IncomeDeductionTypeName='')   @php($Category='')  


@php($VoucherNo='') @php($DateIssue='') @php($DateStartPayment='')  @php($IncomeDeductionAmount='')
@php($InterestAmount='') @php($LoanAmount='') @php($TotalIncomeDeductionAmount='')  @php($AmortizationAmount='')  @php($RemainingBalance='')

@if(isset($EmployeeInformation)>0)
    
    @php($EmployeeName=$EmployeeInformation->FullName)    
    @php($EmployeeNo=$EmployeeInformation->employee_number)
    @php($Position=$EmployeeInformation->Position)

    @php($Department=$EmployeeInformation->Department)         
    @php($Division=$EmployeeInformation->EmailAddress)
    @php($Section=$EmployeeInformation->Division)
    
@endif

@if(isset($IncomeDeductionInformation)>0)
        
    @php($VoucherNo=$IncomeDeductionInformation->VoucherNo)    

    @php($Category=$IncomeDeductionInformation->Category)  

    @php($DateStartPayment=$IncomeDeductionInformation->DateStartPaymentFormat)
    @php($DateIssue=$IncomeDeductionInformation->DateIssueFormat)

    @php($IncomeDeductionTypeCode=$IncomeDeductionInformation->IncomeDeductionTypeCode)    
    @php($IncomeDeductionTypeName=$IncomeDeductionInformation->IncomeDeductionTypeName)
    
    @php($AmortizationAmount=$IncomeDeductionInformation->AmortizationAmount)    
    @php($IncomeDeductionAmount=$IncomeDeductionInformation->IncomeDeductionAmount)
    @php($TotalIncomeDeductionAmount=$IncomeDeductionInformation->TotalIncomeDeductionAmount)
    @php($RemainingBalance=($IncomeDeductionInformation->TotalIncomeDeductionAmount) - ($IncomeDeductionInformation->TotalPayment))

    @if($RemainingBalance<=0)
        @php($RemainingBalance=0)  
    @endif

@endif

  <section class="invoice" style="background:#fff;">        
       <div class="pull-left">                            
         <div class="col-md-12 ">
           <div class="table-responsive ">
             
             <table class="table">
               <tbody>
                
               <tr style="font-weight:normal;font-size:10px;">
                  <td style="width: 10%;">
                 <img class="logo" src="{{URL::asset('public/img/PMC Logo.png')}}" style="height: 70px;width: 100px;position: absolute;top:17px;left: 30px;">
                  </td>
                  <td style="width: 30%;">
                    <span style="font-size: 15px;">
                      <b> {{$CompanyName}} </b>  <br>
                     <span style="font-size: 13px;">
                        {{$Address}}
                      </span>
                    </span>
                  </td>
                  <td style="width: 35%;">
                    <span style="font-size: 12px;">Printed Date/Time : {{ date("Y-m-d H:i:s") }}</span>
                  </td>
               </tr> 


                  <tr style="font-weight:normal;font-size:10px;">
                  <td style="width: 10%;">
                
                  </td>
                  <td style="width: 30%;">
                    <span style="font-size: 13px;">
                        <b> Employee No: </b> {{$EmployeeNo}}   <br>
                        <b> Employee Name: </b>  {{$EmployeeName}}  <br>
                        <b> Position : </b>  {{$Position}}  <br>                                                              
                    </span>
                  </td>
                  <td style="width: 35%;">
                    <span style="font-size: 13px;">
                        <b> Department : </b>{{$Division}}  <br>
                        <b> Division : </b>  {{$Department}}  <br>
                        <b> Section : </b>  {{$Section}}  <br>                         
                      </span>
                  </td>
               </tr> 
     
             </tbody>
           </table>


           <table class="table">
               <tbody>
                
               <tr style="font-weight:normal;font-size:10px;">
                  <td style="width: 10%;">
                 
                  </td>
                  <td style="width: 30%;">
                    <span style="font-size: 15px;">
                      <b> Income/Deduction Name:  {{$IncomeDeductionTypeCode}} - {{$IncomeDeductionTypeName}} <br>                     
                    </span>
                  </td>                 
               </tr> 

                  <tr style="font-weight:normal;font-size:10px;">
                  <td style="width: 10%;">
                
                  </td>
                  <td style="width: 30%;">
                    <span style="font-size: 13px;">
                        <b> Reference No: </b>  {{$VoucherNo}}  <br>
                        <b> Amortization Amount: </b> Php {{number_format($AmortizationAmount,2)}}  <br>
                        <b> Income/Deduction Amount : </b>  Php {{number_format($IncomeDeductionAmount,2)}}  <br>                                         
                    </span>
                  </td>
                  <td style="width: 35%;">
                    <span style="font-size: 13px;">                     
                        <b> Date Issued: </b>  {{$DateIssue}}  <br>
                         @if($Category=='DEDUCTION')
                          <b> Date Start Payment: </b>  {{$DateStartPayment}}  <br>                        
                        @else
                         <b> Date Start Release: </b>  {{$DateStartPayment}}  <br>                        
                        @endif

                        <b> Total Income/Deduction Amount : </b> Php {{number_format($TotalIncomeDeductionAmount,2)}}  <br>          
                      </span>
                  </td>
               </tr> 
     
             </tbody>
           </table>


           </div>
         </div>
      </div>

     <br>
      <div class="row invoice-info">
        <div class="row">
          <div class="col-sm-12" style="text-align: center;">
            <span style="font-size: 22px;"> Payment Income & Deduction History </span>
          </div>
        </div>
      </div>
      <br>


      <div class="row" style="margin-top:10px;">
        <div class="col-xs-12 table-responsive">
          <div class="col-xs-12 table-responsive">
              <table id="tblProducts" class="table table-striped" style='font-size:11px; border: 1px solid black;'>
                <thead>
                  <tr>                     
                        @if($Category=='DEDUCTION')
                         <th style="border:1px solid black; width: 10%;text-align: center;">Date Of Payment</th>                   
                        @else
                         <th style="border:1px solid black; width: 10%;text-align: center;">Date Of Release</th>                   
                        @endif
                    
                    <th style="border:1px solid black; width: 10%;text-align: center;">Income/Deduction Code</th>  
                    <th style="border:1px solid black; width: 10%;text-align: center;">Income/Deduction Type</th>  
                    <th style="border:1px solid black; width: 30%;text-align: center;">Income/Deduction Description</th> 

                      @if($Category=='DEDUCTION')
                         <th style="border:1px solid black; width: 10%;text-align: center;">Payment Type</th>   
                        @else
                         <th style="border:1px solid black; width: 10%;text-align: center;">Release Type</th>   
                        @endif

                                     
                    <th style="border:1px solid black; width: 10%;text-align: center;">Amount </th>                    
                  </tr>
                </thead>
                <tbody>
                @php($TotalPaymentAmount=0)
                @if(count($IncomeDeductionPaymentHistory))
                    @foreach ($IncomeDeductionPaymentHistory as $key => $item)
                        <tr>  
                          <td style="border:1px solid black; width: 12%;text-align: center;">{{$item->PaymentDateFormat}}</td>
                          <td style="border:1px solid black; width: 12%;text-align: center;">{{$item->DeductionCode}}</td>                          
                          <td style="border:1px solid black; width: 12%;text-align: center;">{{$Category}}</td>                          
                          <td style="border:1px solid black; width: 30%;text-align: center;">{{$item->DeductionName}}</td>
                          @if($item->PaymentModuleType!='Manual')
                            <td style="border:1px solid black; width: 12%;text-align: center;">Payroll Deduction</td>
                         @else
                            <td style="border:1px solid black; width: 12%;text-align: center;">Manual Payment</td>
                         @endif
                          <td style="border:1px solid black; width: 10%; text-align: center;"> Php {{$item->AmountPayment}}</td>                         
              
                        </tr> 
                        @php($TotalPaymentAmount=$TotalPaymentAmount + $item->AmountPayment) 
                   @endforeach
                @else
                  <tr>  
                    <td style="border:1px solid black; width: 12%;text-align: center;">&nbsp; </td>
                    <td style="border:1px solid black; width: 12%;text-align: center;">&nbsp; </td>
                    <td style="border:1px solid black; width: 12%;text-align: center;">&nbsp; </td>
                    <td style="border:1px solid black; width: 30%;text-align: center;"><p>No Payment Made Yet. </p> </td>
                    <td style="border:1px solid black; width: 12%;text-align: center;">&nbsp; </td>
                    <td style="border:1px solid black; width: 10%;text-align: center;">&nbsp; </td>

                    </tr> 
                @endif   
               </tbody>
            </table>
        </div>
      </div>
  </div>
      
    <div class="row">                               
       <div class="col-md-12">
         <div class="table-responsive">
           <table class="table">
             <tbody>    

             <tr style="font-weight:normal;font-size:10px;">

               <th>Prepared By:</th>
               <td></td>

               <th  style="text-align:right;"></th>
               <td></td>

                <th  style="text-align:right;"></th>
                <td></td>

               <th  style="text-align:right;">Remaining Balance:</th>
               <td>Php {{number_format($RemainingBalance,2)}}</td>

               <th  style="text-align:right;">Total Payment:</th>
               <td>Php {{number_format($TotalPaymentAmount,2)}}</td>
             </tr>

           </tbody>
         </table>
         </div>
       </div> 
    </div>

   
              
</section>

<script type="text/javascript">
  
          $('#tblProducts').DataTable( {
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : false,
              "order": [[ 0, "asc" ]]
          });

</script>
</body>
</html>
