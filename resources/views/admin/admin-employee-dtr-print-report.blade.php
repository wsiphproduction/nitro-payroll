
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
     .page
     {
     -webkit-transform: rotate(-90deg); 
     -moz-transform:rotate(-90deg);
     filter:progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
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
            <span style="font-size: 25px;"> Employee DTR Report</span>
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
                      <th style="width: 3%;">Period</th>
                      <th style="width: 4%;">Year</th>
                      <th style="width: 5%;">Emp. No.</th>
                      <th style="width: 35%;">Emp. Name</th>
                      <th style="width: 4%;">Hour Rate</th>
                      <th style="width: 4%;">Reg. Hrs</th>
                      <th style="width: 4%;">Late Hrs</th>
                      <th style="width: 4%;">Undertime Hrs</th>
                      <th style="width: 4%;">ND Hrs</th>
                      <th style="width: 4%;">Total Absent</th>
                      <th style="width: 3%;">L01</th>
                      <th style="width: 3%;">L02</th>
                      <th style="width: 3%;">L03</th>
                      <th style="width: 3%;">L04</th>
                      <th style="width: 3%;">L05</th>
                      <th style="width: 3%;">L06</th>
                      <th style="width: 3%;">L07</th>
                      <th style="width: 3%;">L08</th>
                      <th style="width: 3%;">L09</th>
                      <th style="width: 3%;">L10</th>
                      <th style="width: 3%;">L11</th>
                      <th style="width: 3%;">L12</th>
                      <th style="width: 3%;">L13</th>
                      <th style="width: 3%;">L14</th>
                      <th style="width: 3%;">L15</th>
                      <th style="width: 3%;">L16</th>
                      <th style="width: 3%;">L17</th>
                      <th style="width: 3%;">L18</th>
                      <th style="width: 3%;">L19</th>
                      <th style="width: 3%;">L20</th>

                      <th style="width: 3%;">OT01</th>
                      <th style="width: 3%;">OT02</th>
                      <th style="width: 3%;">OT03</th>
                      <th style="width: 3%;">OT04</th>
                      <th style="width: 3%;">OT05</th>
                      <th style="width: 3%;">OT06</th>
                      <th style="width: 3%;">OT07</th>
                      <th style="width: 3%;">OT08</th>
                      <th style="width: 3%;">OT09</th>
                      <th style="width: 3%;">OT10</th>
                      <th style="width: 3%;">OT11</th>
                      <th style="width: 3%;">OT12</th>
                      <th style="width: 3%;">OT13</th>
                      <th style="width: 3%;">OT14</th>
                      <th style="width: 3%;">OT15</th>
                      <th style="width: 3%;">OT16</th>
                      <th style="width: 3%;">OT17</th>
                      <th style="width: 3%;">OT18</th>
                      <th style="width: 3%;">OT19</th>
                      <th style="width: 3%;">OT20</th>
                      <th style="width: 3%;">OT21</th>
                      <th style="width: 3%;">OT22</th>
                      <th style="width: 3%;">OT23</th>
                      <th style="width: 3%;">OT24</th>
                      <th style="width: 3%;">OT25</th>

                  </tr>
                </thead>
                <tbody>
   
                  @foreach ($DTRPrintReportList as $key => $item)
                      <tr>  

                        <td  style="border:1px solid black; width: 4%; text-align: center;">{{$item->PayrollPeriodCode}}</td>
                        <td  style="border:1px solid black; width: 4%; text-align: center;">{{$item->Year}}</td> 
                        <td  style="border:1px solid black; width: 8%; text-align: center;">{{$item->EmployeeNumber}}</td> 
                         <td  style="border:1px solid black; width: 8%; text-align: center;">{{$item->FullName}}</td> 

                        <td  style="border:1px solid black; width: 4%; text-align: center;">{{$item->EmployeeRate}}</td> 
                        <td  style="border:1px solid black; width: 4%; text-align: center;">{{$item->RegularHours}}</td> 
                        <td  style="border:1px solid black; width: 4%; text-align: center;">{{$item->LateHours}}</td> 
                        <td  style="border:1px solid black; width: 4%; text-align: center;">{{$item->UndertimeHours}}</td>
                        <td  style="border:1px solid black; width: 4%; text-align: center;">{{$item->NDHours}}</td>
                        <td  style="border:1px solid black; width: 4%; text-align: center;">{{$item->Absent}}</td>

                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave01}}</td> 
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave02}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave03}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave04}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave05}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave06}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave07}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave08}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave09}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave10}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave11}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave12}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave13}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave14}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave15}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave16}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave17}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave18}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave19}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->Leave20}}</td>

                         <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours01}}</td> 
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours02}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours03}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours04}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours05}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours06}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours07}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours08}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours09}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours10}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours11}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours12}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours13}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours14}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours15}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours16}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours17}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours18}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours19}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours20}}</td>

                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours21}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours22}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours23}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours24}}</td>
                        <td  style="border:1px solid black; width: 2%; text-align: center;">{{$item->OTHours25}}</td>


                      </tr> 

                 @endforeach
               </tbody>
            </table>
        </div>
      </div>
  </div>            
</section>


</body>
</html>
