
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
            <span style="font-size: 25px;"> Employee Loan Deduction Report</span><br>
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
                    <th style="border:1px solid black; width: 10%;text-align: center;">SSS Salary Loan </th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">SSS Salary Loan2</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">PagIbig Loan1 </th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">PagIbig Loan2</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Pag-Ibig Loan2</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">SSS Condonation</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">BMEA Share</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Electrical Loan</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Pag-Ibig Calamity</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">HPV</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Cash Advance</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Electrical Loan</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Receivable Emp.</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Salary Loan2</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Personal Insurance3</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Mortuary Asst.</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Provident Loan3</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Provident Loan1</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Other Deduction</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">SSS Calamity</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Provident Loan3</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Tax Adj.</th>
                    <th style="border:1px solid black; width: 15%;text-align: center;">Total</th>
                  </tr>
                </thead>
                <tbody>
   
                  @foreach ($EmployeeLoanDeductionList as $key => $item)
                      <tr>  
  
                        <td  style="border:1px solid black; width: 10%; text-align: center;">{{$item->EmployeeNo}}</td> 
                        <td  style="border:1px solid black; width: 10%; text-align: center;">{{$item->FullName}}</td> 
                        <td  style="border:1px solid black; width: 10%;text-align: center;">{{number_format($item->SSSSalaryLoan,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->SSSSalaryLoan2,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->PAGIBIGLoan1,2)}}</td> 
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->PAGIBIGLoan2,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->PAGIBIGLoan2_2,2)}}</td> 
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->SSSCondonation,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->BMEAShare,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->ElectricalLoan,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->PAGIBIGCalamity,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->HPV,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->CashAdvance,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->ReceivableEmployee,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->SalaryLoan2,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->PersonalInsurance3,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->MortuaryAssistance,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->ProvidentLoan3,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->ProvidentLoan1,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->ProvidentLoan2,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->OtherDeduction,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->SSSCalamityLoan,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->ProvidentLoan3_3,2)}}</td>
                        <td  style="border:1px solid black; width: 15%;text-align: center;">{{number_format($item->TaxAdjustment,2)}}</td>
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
