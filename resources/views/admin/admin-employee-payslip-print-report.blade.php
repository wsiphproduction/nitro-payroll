
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
    padding: 0px;
    }

    .table th, .table td{
      border-top: 0px;
    }
    .row{
      margin-right:auto;
      margin-left:auto;
    }
    table {  
      margin-bottom: 0px !important;  
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

<!-- Get Company Information-->
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
<!-- End Company Information -->

<!-- Get Payroll Period Info -->
@php($PayrollPeriodID=0)
@php($PayrollPeriodStartDate='')
@php($PayrollPeriodEndDate='')         
@if(isset($PayrollTransactionInfo)>0)    
    @php($PayrollPeriodID=$PayrollTransactionInfo->PayrollPeriodID) 
    @php($PayrollPeriodStartDate=$PayrollTransactionInfo->PayrollPeriodStartDate) 
    @php($PayrollPeriodEndDate=$PayrollTransactionInfo->PayrollPeriodEndDate) 
@endif   
 <!-- End Payroll Period Information  -->
 
 <!-- Get Employee List Base On Period -->
  @foreach ($PayrollTransactionEmployeeList as $key => $item)
      
      <!-- Get Employee Information -->
      @php($data['EmployeeID']=$item->EmployeeID)                                        
      @php($data['PayrollTransactionID']=$PayrollTransactionID)
      @php($data['Status']=$Status)
    
       @php($EmployeePayrollDetails=$PayrollTransaction->getPayrollTransactionEmployeeInfo($data))
      <!-- End Employee Information --> 

        <!-- Get and Set Employee Payroll Details -->
        @php($dblTotalBasicSalaryQty=0)                                        
        @php($dblTotalBasicSalary=0)  

        @php($dblTotalNightDifferentialQty=0)
        @php($dblTotalNightDifferential=0)

        @php($dblTotalOvertime=0)
        @php($dblTotalOvertimeQty=0)

        @php($dblTotalLeave=0)
        @php($dblTotalLeaveQty=0)

        @php($dblTotalOtherTaxableIncome=0)
        @php($dblTotalOtherTaxableIncomeCnt=0)

        @php($dblTotalOtherNonTaxableIncome=0)
        @php($dblTotalOtherNonTaxableIncomeCnt=0)

        @php($dblTotalEarning=0)

        @php($dblTotalLateHoursQty=0)
        @php($dblTotalLateHours=0)

        @php($dblTotalUndertimeQty=0)
        @php($dblTotalUndertime=0)

        @php($dblTotalAbsentHoursQty=0)
        @php($dblTotalAbsentHours=0)

        @php($dblTotalPHICEEContribution=0)                                     
        @php($dblTotalSSSEEContribution=0)
        @php($dblTotalHDMFEEContribution=0)
        
        @php($dblTotalWTax=0)

        @php($dblTotalAdvance=0)

        @php($dblTotalLoan=0)
        @php($dblTotalLoanCnt=0)

        @php($dblTotalOtherDeduction=0)
        @php($dblTotalOtherDeductionCnt=0)

        @php($dblTotalDeduction=0)

        @php($EmployeePayslipDetails=$PayrollTransaction->getPayrollTransactionDetails($data))
        
         @foreach ($EmployeePayslipDetails as $key => $list)

            @if($list->ReferenceType=='Basic Salary')
              @php($dblTotalBasicSalaryQty = $dblTotalBasicSalaryQty + $list->Qty)
              @php($dblTotalBasicSalary = $dblTotalBasicSalary + $list->Total)

              @php($dblTotalEarning = $dblTotalEarning + $list->Total)
            @endif

           @if($list->ReferenceType=='Night Differential')
              @php($dblTotalNightDifferentialQty = $dblTotalNightDifferentialQty + $list->Qty)
              @php($dblTotalNightDifferential = $dblTotalNightDifferential + $list->Total)

              @php($dblTotalEarning = $dblTotalEarning + $list->Total)
            @endif


           @if($list->ReferenceType=='Overtime')
              @php($dblTotalOvertime = $dblTotalOvertime + $list->Total)
              @php($dblTotalOvertimeQty= $dblTotalOvertimeQty + $list->Qty)

              @php($dblTotalEarning = $dblTotalEarning + $list->Total)
            @endif

           @if($list->ReferenceType=='Leave')
              @php($dblTotalLeave = $dblTotalLeave + $list->Total)
              @php($dblTotalLeaveQty= $dblTotalLeaveQty + $list->Qty)

              @php($dblTotalEarning = $dblTotalEarning + $list->Total)
            @endif

           @if($list->ReferenceType=='Income')
                @if($list->IsTaxable == 1)
                    @php($dblTotalOtherTaxableIncome = $dblTotalOtherTaxableIncome + $list->Total)
                    @php($dblTotalOtherTaxableIncomeCnt = $dblTotalOtherTaxableIncomeCnt + 1)
                @else
                    @php($dblTotalOtherNonTaxableIncome = $dblTotalOtherNonTaxableIncome + $list->Total)
                    @php($dblTotalOtherNonTaxableIncomeCnt = $dblTotalOtherNonTaxableIncomeCnt + 1)
                @endif

                @php($dblTotalEarning = $dblTotalEarning + $list->Total)
            @endif

              @if($list->ReferenceType=='Absent')
                @php($dblTotalAbsentHoursQty = $dblTotalAbsentHoursQty + $list->Qty)
                @php($dblTotalAbsentHours = $dblTotalAbsentHours + $list->Total) 

                @php($dblTotalDeduction = $dblTotalDeduction + $list->Total)                               
            @endif

           @if($list->ReferenceType=='Late Hours')
                @php($dblTotalLateHoursQty = $dblTotalLateHoursQty + $list->Qty)
                @php($dblTotalLateHours = $dblTotalLateHours + $list->Total)  

                @php($dblTotalDeduction = $dblTotalDeduction + $list->Total)                               
            @endif

           @if($list->ReferenceType=='Undertime Hours')
              @php($dblTotalUndertimeQty = $dblTotalUndertimeQty + $list->Qty)
              @php($dblTotalUndertime = $dblTotalUndertime + $list->Total)  

              @php($dblTotalDeduction = $dblTotalDeduction + $list->Total)
            @endif

            @if($list->ReferenceType=='PHIC EE Contribution')
              @php($dblTotalPHICEEContribution = $dblTotalPHICEEContribution + $list->Total)                                              
                @php($dblTotalDeduction = $dblTotalDeduction + $list->Total)
            @endif

            @if($list->ReferenceType=='SSS EE Contribution')
              @php($dblTotalSSSEEContribution = $dblTotalSSSEEContribution + $list->Total)
              @php($dblTotalDeduction = $dblTotalDeduction + $list->Total)
            @endif
           @if($list->ReferenceType=='HDMF EE Contribution')
              @php($dblTotalHDMFEEContribution = $dblTotalHDMFEEContribution + $list->Total)                
              @php($dblTotalDeduction = $dblTotalDeduction + $list->Total)
            @endif
            
           @if($list->ReferenceType=='Withholding Tax')  
              @php($dblTotalWTax = $dblTotalWTax + $list->Total)
              @php($dblTotalDeduction = $dblTotalDeduction + $list->Total)
            @endif

           @if($list->ReferenceType=='Advance')  
              @php($dblTotalAdvance = $dblTotalAdvance + $list->Total)
              @php($dblTotalDeduction = $dblTotalDeduction + $list->Total)
            @endif

           @if($list->ReferenceType=='Loan')
                @php($dblTotalLoanCnt = $dblTotalLoanCnt + $list->Qty)
                @php($dblTotalLoan = $dblTotalLoan + $list->Total)   

                @php($dblTotalDeduction = $dblTotalDeduction + $list->Total)                               
            @endif

           @if($list->ReferenceType=='Deduction')
                @php($dblTotalOtherDeductionCnt = $dblTotalOtherDeductionCnt + $list->Qty)
                @php($dblTotalOtherDeduction = $dblTotalOtherDeduction + $list->Total)   
                            
                @php($dblTotalDeduction = $dblTotalDeduction + $list->Total)                               
            @endif

         @endforeach
       <!-- End of Employee Payroll Details -->
           

      <!-- Get Employee DTR Information & OT Hours Detais -->
      @php($dblTotalROTQty=0)                                        
      @php($dblTotalNPROTQty=0) 
      @php($dblTotalDOQty=0) 
      @php($dblTotalSHQty=0) 
      @php($dblTotalLHQty=0) 
      @php($dblTotalSHDOQty=0)

       @php($EmployeeDTRInfo=$EmployeeDTR->getEmployeeDTRInfoByEmployeeIDAndPayrollID($data['EmployeeID'],$PayrollPeriodID))

     @if(isset($EmployeeDTRInfo)>0)    
       @php($dblTotalROTQty=$EmployeeDTRInfo->OTHours01) 
       @php($dblTotalNPROTQty=$EmployeeDTRInfo->OTHours02)

       @php($dblTotalDOQty=$EmployeeDTRInfo->OTHours03) 
       @php($dblTotalSHQty=$EmployeeDTRInfo->OTHours04)

        @php($dblTotalLHQty=$EmployeeDTRInfo->OTHours05)
        @php($dblTotalSHDOQty=$EmployeeDTRInfo->OTHours06)

     @endif   
    <!-- End of Employee DTR Information-->


    <div class="col-md-12" style="min-height: 475px;">
        <table class="table" style='width:100%;'>
            <tr style="vertical-align: top;">
                <td style="width: 70%; text-align: left;">
                    <div class="col-md-12" style="padding: 0px; margin:0px;">
                        <span style="font-size:15px; font-weight:bold;">Nitro Pacific Rockworks, Inc. </span>
                    </div>
                </td>
                <td style='width:0%;'></td>
                <td style="width: 30%; text-align: left; padding-left:10px; ">
                    <div class="col-md-12" style="padding: 0px; margin:0px;">
                        <span style="font-size:15px; font-weight:bold;">Nitro Pacific Rockworks, Inc. </span>
                    </div>
                </td>
            </tr>
            <tr style="vertical-align: top;">
                <td style="width: 70%; text-align: left;">
                    <table class="table" style='width:100%;'>
                        <tr>
                            <td style='width:50%;'>
                                <span style="font-size:12px;">EMP CODE: {{$EmployeePayrollDetails->EmployeeNo}} </span> 
                                <span style="font-size:12px;">{{$EmployeePayrollDetails->FullName}} </span>
                                <br>
                                <span style="font-size:12px;">DEPT : {{$EmployeePayrollDetails->Department}} </span>
                            </td>
                            <td style='width:50%;'>
                                <span style="font-size:12px;">  PAY DATE: {{$PayrollPeriodStartDate}} - {{$PayrollPeriodEndDate}} </span>
                            </td>
                        </tr>
                    </table>                    
                </td>
                <td style='width:0%;'></td>
                <td style="width: 30%; text-align: left; padding-left:10px; ">
                    <div>
                        <span style="font-size:12px;"> EMP CODE: {{$EmployeePayrollDetails->EmployeeNo}} </span> 
                    </div>
                    <div>
                        <span style="font-size:12px;"> DEPT : {{$EmployeePayrollDetails->Department}} </span> <br>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 70%; text-align: left;">
                    <table class="table" style='width:100%;'>
                        <tr>
                            <td style="width: 35%; font-size:12px; border: 2px solid black; padding-left: 10px; padding-right: 10px;">
                                <span style="float:left;">REGULAR DAYS</span> 
                                <span style="float:right; text-align: right;">{{ ($dblTotalBasicSalaryQty > 0 ? number_format($dblTotalBasicSalaryQty/8,2) : "-") }}</span> 
                            </td>
                            <td style="width: 35%; font-size:12px; border: 2px solid black; padding-left: 10px; padding-right: 10px;">
                                <span style="float:left;">ABSENCES/LATE/UT (HRS)</span> 
                                <span style="float:right; text-align: right;">{{ (($dblTotalAbsentHoursQty + $dblTotalLateHoursQty + $dblTotalUndertimeQty) > 0 ? number_format(($dblTotalAbsentHoursQty + $dblTotalLateHoursQty + $dblTotalUndertimeQty),2) : "-") }}</span> 
                            </td>
                            <td style="width: 30%; font-size:12px; border: 2px solid black; padding-left: 10px; padding-right: 10px;">
                                <span style="float:left;">VL/SL/OL</span> 
                                <span style="float:right; text-align: right;">{{ ($dblTotalLeaveQty > 0 ? number_format($dblTotalLeaveQty,2) : "-") }}</span> 
                            </td>
                        </tr>
                    </table>
                </td>
                <td style='width:0%;'></td>
                <td style="width: 30%;"><span style="font-size:12px;"><center>RECEIPT FOR PAY </center></span></td>
            </tr>
            <tr>
                <td style="width: 70%; text-align: left;">
                    <table class="table" style='width:100%;'>
                        <tr>
                            <td style="width: 35%; text-align: left; padding-left: 10px; padding-right: 10px; border: 1px solid black;">
                                <br>
                                <table class="table" style='width:100%;'>
                                    <tr>
                                        <td style="font-size:12px;">REGULAR</td>
                                        <td style="font-size:12px; text-align: right;">{{ ($dblTotalBasicSalary > 0 ? number_format($dblTotalBasicSalary,2) : "-") }}
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;">VL/SL/OL</td>
                                        <td style="font-size:12px; text-align: right;">{{ ($dblTotalLeave > 0 ? number_format($dblTotalLeave,2) : "-") }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;">&nbsp;</td>
                                        <td style="font-size:12px; text-align: right;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;">ABSENT/LATE/UT</td>
                                        <td style="font-size:12px; text-align: right;">{{ (($dblTotalAbsentHours + $dblTotalLateHours + $dblTotalUndertime) > 0 ? number_format(($dblTotalAbsentHours + $dblTotalLateHours + $dblTotalUndertime),2) : "-") }} </td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;">OVERTIME</td>
                                        <td style="font-size:12px; text-align: right;">{{ ($dblTotalOvertime > 0 ? number_format($dblTotalOvertime,2) : "-") }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;">NIGHT DIFF</td>
                                        <td style="font-size:12px; text-align: right;">{{ ($dblTotalNightDifferential > 0 ? number_format($dblTotalNightDifferential,2) : "-") }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;">&nbsp;</td>
                                        <td style="font-size:12px; text-align: right;">&nbsp;</td>
                                    </tr>
                                    @php($AvailableRow = 14)
                                    @foreach ($EmployeePayslipDetails as $key => $list)
                                        @if($list->ReferenceType=='Income' && $list->Total > 0)
                                            @if($list->IsTaxable == 1)
                                                <tr>
                                                    <td style="font-size:12px;">{{ $list->Reference }}</td>
                                                    <td style="font-size:12px; text-align: right;"> {{ number_format($list->Total,2) }}  </td>
                                                </tr>
                                                @php($AvailableRow = $AvailableRow - 1)
                                            @endif
                                        @endif
                                    @endforeach

                                    @foreach ($EmployeePayslipDetails as $key => $list)
                                        @if($list->ReferenceType=='Income' && $list->Total > 0)
                                            @if($list->IsTaxable == 0)
                                                <tr>
                                                    <td style="font-size:12px;">{{ $list->Reference }}</td>
                                                    <td style="font-size:12px; text-align: right;"> {{ number_format($list->Total,2) }}  </td>
                                                </tr>
                                                @php($AvailableRow = $AvailableRow - 1)
                                            @endif
                                        @endif
                                    @endforeach

                                    @for($x = $AvailableRow; $x > 0; $x--)
                                    <tr>
                                        <td style="font-size:12px;">&nbsp;</td>
                                        <td style="font-size:12px; text-align: right;">&nbsp;</td>
                                    </tr>
                                    @endfor
                                    <tr>
                                        <td style="font-size:12px; font-weight: bold;">EARNINGS</td>
                                        <td style="font-size:12px; font-weight: bold; text-align: right;">{{ ($dblTotalEarning > 0 ? number_format($dblTotalEarning,2) : "-") }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 35%; text-align: left; padding-left: 10px; padding-right: 10px; border: 1px solid black;">
                                <span style="font-size:12px; font-weight:bold;">DEDUCTIONS</span>
                                <table class="table" style='width:100%;'>
                                    <tr>
                                        <td style="font-size:12px;">WTAX</td>
                                        <td style="font-size:12px; text-align: right;">{{ ($dblTotalWTax > 0 ? number_format($dblTotalWTax,2) : "-") }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;">SSS PREMIUM</td>
                                        <td style="font-size:12px; text-align: right;">{{ ($dblTotalSSSEEContribution > 0 ? number_format($dblTotalSSSEEContribution,2) : "-") }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;">PHILHEALTH</td>
                                        <td style="font-size:12px; text-align: right;">{{ ($dblTotalPHICEEContribution > 0 ? number_format($dblTotalPHICEEContribution,2) : "-") }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;">HDMF</td>
                                        <td style="font-size:12px; text-align: right;">{{ ($dblTotalHDMFEEContribution > 0 ? number_format($dblTotalHDMFEEContribution,2) : "-") }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;">&nbsp;</td>
                                        <td style="font-size:12px; text-align: right;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;">&nbsp;</td>
                                        <td style="font-size:12px; text-align: right;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size:12px;">&nbsp;</td>
                                        <td style="font-size:12px; text-align: right;">&nbsp;</td>
                                    </tr>

                                    @php($AvailableRow = 14)
                                    @foreach ($EmployeePayslipDetails as $key => $list)
                                        @if($list->ReferenceType=='Advance' && $list->Total > 0)
                                            <tr>
                                                <td style="font-size:12px;">{{ $list->Reference }}</td>
                                                <td style="font-size:12px; text-align: right;"> {{ number_format($list->Total,2) }}  </td>
                                            </tr>
                                            @php($AvailableRow = $AvailableRow - 1)
                                        @endif
                                    @endforeach

                                    @foreach ($EmployeePayslipDetails as $key => $list)
                                        @if($list->ReferenceType=='Loan' && $list->Total > 0)
                                            <tr>
                                                <td style="font-size:12px;">{{ $list->Reference }}</td>
                                                <td style="font-size:12px; text-align: right;"> {{ number_format($list->Total,2) }}  </td>
                                            </tr>
                                            @php($AvailableRow = $AvailableRow - 1)
                                        @endif
                                    @endforeach

                                    @foreach ($EmployeePayslipDetails as $key => $list)
                                        @if($list->ReferenceType=='Deduction' && $list->Total > 0)
                                            <tr>
                                                <td style="font-size:12px;">{{ $list->Reference }}</td>
                                                <td style="font-size:12px; text-align: right;"> {{ number_format($list->Total,2) }}  </td>
                                            </tr>
                                            @php($AvailableRow = $AvailableRow - 1)
                                        @endif
                                    @endforeach

                                    @for($x = $AvailableRow; $x > 0; $x--)
                                    <tr>
                                        <td style="font-size:12px;">&nbsp;</td>
                                        <td style="font-size:12px; text-align: right;">&nbsp;</td>
                                    </tr>
                                    @endfor
                                    <tr>
                                        <td style="font-size:12px; font-weight: bold;">DEDUCTIONS</td>
                                        <td style="font-size:12px; font-weight: bold; text-align: right;">{{ ($dblTotalDeduction > 0 ? number_format($dblTotalDeduction,2) : "-") }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 30%; text-align: left; padding-left: 10px; padding-right: 10px; border: 1px solid black;">
                                @if($dblTotalOvertimeQty > 0)
                                    <span style="font-size:12px; font-weight:bold;">OT DETAILS (HOURS)</span>
                                @else
                                    <span style="font-size:12px; font-weight:bold;">&nbsp;&nbsp;&nbsp;</span>
                                @endif
                                <table class="table" style='width:100%;'>

                                    @php($AvailableRow = 21)
                                    @foreach ($EmployeePayslipDetails as $key => $list)
                                        @if($list->ReferenceType=='Overtime' && $list->Total > 0)
                                            <tr>
                                                <td style="font-size:12px;">{{ $list->Reference }}</td>
                                                <td style="font-size:12px; text-align: right;"> {{ number_format($list->Total,2) }}  </td>
                                            </tr>
                                            @php($AvailableRow = $AvailableRow - 1)
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td style="font-size:12px;">&nbsp;</td>
                                        <td style="font-size:12px; text-align: right;">&nbsp;</td>
                                    </tr>
                                    @php($AvailableRow = $AvailableRow - 1)
                                    <tr>
                                        <td style="font-size:12px; font-weight: bold;">RATE</td>
                                        <td style="font-size:12px; font-weight: bold; text-align: right;">{{ ($EmployeePayrollDetails->MonthlyRate > 0 ? number_format($EmployeePayrollDetails->MonthlyRate,2) : "-")  }}</td>
                                    </tr>
                                    @php($AvailableRow = $AvailableRow - 1)

                                    @for($x = $AvailableRow; $x > 0; $x--)
                                    <tr>
                                        <td style="font-size:12px;">&nbsp;</td>
                                        <td style="font-size:12px; text-align: right;">&nbsp;</td>
                                    </tr>
                                    @endfor
                                    <tr>
                                        <td style="font-size:12px; font-weight: bold;">NET PAY</td>
                                        <td style="font-size:12px; font-weight: bold; text-align: right;"> {{number_format($EmployeePayrollDetails->NetPay,2)}} </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style='width:0%;'></td>
                <td style="width: 30%; padding-left:10px; padding-right:10px; ">
                    <span style="font-size:12px;">PAY DATE: {{$PayrollPeriodStartDate}} - {{$PayrollPeriodEndDate}} </span>
                    <br>
                    <span style="font-size:13px; margin-left: 5px;">I acknowlegde to have received the amount of <b>  {{number_format($EmployeePayrollDetails->NetPay,2)}} </b> and have no further claims for services rendered.</span>
                    <br><br>
                    <span style="font-size:13px; margin-left: 5px;">_______________________________________</span>
                    <p style="font-weight: bold; font-size:13px; margin-left: 5px;margin-top:-3px;margin-left:45px;"> ( {{$EmployeePayrollDetails->FullName}} ) </p>
                </td>
            </tr>
        </table>

      </div>
      <hr>
    @endforeach  
   
</section>

</body>
</html>

