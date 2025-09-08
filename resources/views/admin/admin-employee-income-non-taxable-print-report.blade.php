
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
      @media print{@page {size:Letter landscape}}
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

<!-- Get Payroll Period Info -->
@php($PayrollPeriodID=0)
@php($PayrollPeriodStartDate='')
@php($PayrollPeriodEndDate='')         
@if(isset($PayrollTransactionInfo)>0)    
    @php($PayrollPeriodID=$PayrollTransactionInfo->PayrollPeriodID) 
    @php($PayrollPeriodStartDate=$PayrollTransactionInfo->PayrollPeriodStartDate) 
    @php($PayrollPeriodEndDate=$PayrollTransactionInfo->PayrollPeriodEndDate) 
@endif   

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

  <section class="invoice" style="background:#fff;">        
       <div class="pull-left">                            
         <div class="col-md-12 ">
           <div class="table-responsive ">
             <table class="table">
               <tbody>
                
               <tr style="font-weight:normal;font-size:10px;">
                  <td style="width: 10%;">
                   <img class="logo" src="{{URL::asset('public/img/PMC Logo.png')}}" style="height: 70px;width: 100px;position: absolute;top: -2px;left: 30px;">
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
                    <span style="font-size: 12px;">Print Date/Time : {{ date("Y-m-d H:i:s") }}</span>
                  </td>
               </tr> 
     
             </tbody>
           </table>
           </div>
         </div>
      </div>

      <div class="row invoice-info">
        <div class="row">
          <div class="col-sm-12" style="text-align: center;">
            <span style="font-size: 25px;"> Employee Other Earning Non Taxable Report</span><br>
            <span > Payroll Period: {{$PayrollPeriodStartDate}} - {{$PayrollPeriodEndDate}} </span>
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
                    <th style="border:1px solid black; width: 10%;text-align: center;">Employee No</th>
                    <th style="border:1px solid black; width: 13%;text-align: center;">Employee Name</th>
                    <th style="border:1px solid black; width: 10%;text-align: center;">Unused VL (Non-Tax)</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Redundancy Pay</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Service Incentive Pay </th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Rice Allowance</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Manways Incentive</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Training Allowance</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Meal Allowance</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">13 Month Pay (Non-Tax)</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Production Incentive</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Lighting</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Housing</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Unused SL (Non-Tax)</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Union Allowance Adj.</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Other Earning32</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Housing Adj.</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Lighting Adj.</th>
                     <th style="border:1px solid black; width: 15%;text-align: center;">Meal Allowance Adj.</th>
                     <th style="border:1px solid black; width: 15%;text-align: center;">E-COLA</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Total</th>
                  </tr>
                </thead>
                <tbody>
   
                  @foreach ($EmployeeOtherEarningNonTaxableList as $key => $item)
                      <tr>  
  
                        <td  style="border:1px solid black; width: 10%; text-align: center;">{{$item->EmployeeNo}}</td> 
                        <td  style="border:1px solid black; width: 10%; text-align: center;">{{$item->FullName}}</td> 
                        <td  style="border:1px solid black; width: 10%;text-align: center;">{{number_format($item->UnusedVacationLeaveNonTaxable,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->RedundancyPay,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->ServiceIncentivePay,2)}}</td> 
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->RiceAllowance,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->ManwayIncentives,2)}}</td> 
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->TrainingAllowance,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->MealAllowance,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->ThirteenMonthPayNonoTaxable,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->ProductionIncentives,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->Lighting,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->Housing,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->UnusedSickLeaveTaxable,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->UnionAllowanceAdjustments,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->OtherEarning32,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->HousingAdjustment,2)}}</td>
                        
                         <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->LightingAdjustment,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->MealAllowanceAdjustment,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->ECOLA,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->UnpaidEarningPrevPayroll,2)}}</td>

                        <td  style="border:1px solid black; width: 10%;text-align: center;"></td>
                      </tr> 

                 @endforeach
               </tbody>
            </table>
        </div>
      </div>
  </div>

       <div class="row">                            
       <div class="col-md-9 pull-left">
         <div class="table-responsive ">
           <table class="table">
             <tbody>
         
                   <tr style="font-weight:normal;font-size:10px;">
               <th>Prepared By: :</th>
             <td></td>
             </tr> 
               
             <tr style="font-weight:normal;font-size:10px;">
               <th>Checked By:</th>
               <td></td>
             </tr>

              <tr style="font-weight:normal;font-size:10px;">
               <th>Verified By:</th>
               <td></td>
             </tr>

              <tr style="font-weight:normal;font-size:10px;">
               <th>Approved By:</th>
               <td></td>
             </tr>


           </tbody>
         </table>
         </div>
       </div>

<!--    
       <div class="col-md-3 pull-right">
         <div class="table-responsive">
           <table class="table">
             <tbody>    

              <tr style="font-weight:normal;font-size:10px;">
               <th  style="text-align:left;">Total Employer Share:</th>
               <td style="font-weight:normal;font-size:10px;"> </td>
             </tr>

               <tr style="font-weight:normal;font-size:10px;">
               <th  style="text-align:left;">Total Employee Share:</th>
               <td style="font-weight:normal;font-size:10px;">  </td>
             </tr>

             <tr style="font-weight:normal;font-size:10px;">
               <th  style="text-align:left;">Total Amount:</th>
               <td style="font-weight:normal;font-size:10px;"> </td>
             </tr>

           </tbody>
         </table>
         </div>
       </div> 
        -->
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
