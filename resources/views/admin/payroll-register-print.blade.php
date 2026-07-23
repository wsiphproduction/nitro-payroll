<!DOCTYPE html>
<html>
<head>
    <title>Payroll Register Report</title>

    <style>
        body{
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            margin:10px;
        }

        h2,h3{
            margin:0;
            text-align:center;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th,td{
            border:1px solid #000;
            padding:3px;
            white-space:nowrap;
        }

        th{
            background:#d9d9d9;
            text-align:center;
        }

        td.num{
            text-align:right;
        }

        .team-header{
            background:#f2f2f2;
            font-weight:bold;
            font-size:12px;
        }

        .subtotal{
            background:#e6e6e6;
            font-weight:bold;
        }

        .grandtotal{
            background:#cccccc;
            font-weight:bold;
        }

        @page{
            size: landscape;
            margin: 8mm;
        }

        @media print{
            .no-print{
                display:none;
            }
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom:10px">
    <button onclick="window.print()">Print</button>
</div>

<h2>NITRO PACIFIC</h2>
<h3>Payroll Register Report</h3>

<br>

@php

$currentTeamLeader = null;

$grand = [
    'Days'=>0,
    'BasicPay'=>0,
    'ECOLA'=>0,
    'LateAmount'=>0,
    'UndertimeAmount'=>0,
    'AbsentAmount'=>0,
    'SL'=>0,
    'VL'=>0,
    'OL'=>0,
    'NightDiff'=>0,
    'OTPay'=>0,
    'LH'=>0,
    'SH'=>0,
    'RDDPay'=>0,
    'OTND'=>0,
    // 'OtherTaxableEarnings'=>0,
    'OtherNonTaxableEarnings'=>0,
    'GrossPay'=>0,
    'SSS'=>0,
    'PHIC'=>0,
    'HDMF'=>0,
    'HDMFMP2'=>0,
    // 'TaxableIncome'=>0,
    'WTax'=>0,
    'SSSSalaryLoan'=>0,
    'SSSCalamityLoan'=>0,
    'HDMFLoan'=>0,
    'HDMFCalamityLoan'=>0,
    'OtherLoanDeductions'=>0,
    'OtherDeduction'=>0,
    'TotalDeduction'=>0,
    'NetPay'=>0
];

$subtotal = $grand;

foreach($OtherEarningsTypes as $oe){

    $alias = preg_replace(
        '/[^A-Za-z0-9]/',
        '_',
        strtoupper($oe->Name)
    );

    $grand[$alias] = 0;
    $subtotal[$alias] = 0;
}

$fixedColumns = [
    'Days',
    'BasicPay',
    'LateAmount',
    'UndertimeAmount',
    'AbsentAmount',
    'SL',
    'VL',
    'NightDiff',
    'OTPay',
    'GrossPay',
    // 'TaxableIncome',
    'WTax',
    'NetPay'
];

$visibleColumns = $fixedColumns;

@endphp

@php

foreach($list as $row){

    foreach($grand as $key => $value){
        $grand[$key] += (float)($row->$key ?? 0);
    }

}

foreach($grand as $field => $total){

    if($total != 0 && !in_array($field,$visibleColumns)){
        $visibleColumns[] = $field;
    }

}

@endphp

<table>

@foreach($list as $index => $row)

    @if($currentTeamLeader != $row->TeamLeader)

        @if($currentTeamLeader !== null)

        <tr class="subtotal">
            <td colspan="5">SUBTOTAL</td>

            <td class="num">{{ number_format($subtotal['Days'],2) }}</td>
            <td class="num">{{ number_format($subtotal['BasicPay'],2) }}</td>

            @if(in_array('ECOLA',$visibleColumns))
                <td class="num">{{ number_format($subtotal['ECOLA'],2) }}</td>
            @endif

            <td class="num">{{ number_format($subtotal['LateAmount'],2) }}</td>
            <td class="num">{{ number_format($subtotal['UndertimeAmount'],2) }}</td>
            <td class="num">{{ number_format($subtotal['AbsentAmount'],2) }}</td>
            <td class="num">{{ number_format($subtotal['SL'],2) }}</td>
            <td class="num">{{ number_format($subtotal['VL'],2) }}</td>

            @if(in_array('OL',$visibleColumns))
                <td class="num">{{ number_format($grand['OL'],2) }}</td>
            @endif

            <td class="num">{{ number_format($subtotal['NightDiff'],2) }}</td>
            <td class="num">{{ number_format($subtotal['OTPay'],2) }}</td>
            
            @if(in_array('LH',$visibleColumns))
                <td class="num">{{ number_format($grand['LH'],2) }}</td>
            @endif
            
            @if(in_array('SH',$visibleColumns))
                <td class="num">{{ number_format($grand['SH'],2) }}</td>
            @endif

            @if(in_array('RDDPay',$visibleColumns))
                <td class="num">{{ number_format($grand['RDDPay'],2) }}</td>
            @endif

            @if(in_array('OTND',$visibleColumns))
                <td class="num">{{ number_format($grand['OTND'],2) }}</td>
            @endif

            {{-- @if(in_array('OtherTaxableEarnings',$visibleColumns))
                <td class="num">{{ number_format($grand['OtherTaxableEarnings'],2) }}</td>
            @endif --}}

            {{-- @if(in_array('OtherNonTaxableEarnings',$visibleColumns))
                <td class="num">{{ number_format($grand['OtherNonTaxableEarnings'],2) }}</td>
            @endif --}}


            @foreach($OtherEarningsTypes as $oe)

                @php
                    $alias = preg_replace(
                        '/[^A-Za-z0-9]/',
                        '_',
                        strtoupper($oe->Name)
                    );
                @endphp

                @if(in_array($alias,$visibleColumns))
                    <td class="num">
                        {{ number_format($subtotal[$alias] ?? 0,2) }}
                    </td>
                @endif

            @endforeach

            <td class="num">{{ number_format($subtotal['GrossPay'],2) }}</td>

            @if(in_array('SSS',$visibleColumns))
                <td class="num">{{ number_format($grand['SSS'],2) }}</td>
            @endif

            @if(in_array('PHIC',$visibleColumns))
                <td class="num">{{ number_format($grand['PHIC'],2) }}</td>
            @endif

            @if(in_array('HDMF',$visibleColumns))
                <td class="num">{{ number_format($grand['HDMF'],2) }}</td>
            @endif

            @if(in_array('HDMFMP2',$visibleColumns))
                <td class="num">{{ number_format($grand['HDMFMP2'],2) }}</td>
            @endif
            
            {{-- <td class="num">{{ number_format($subtotal['TaxableIncome'],2) }}</td> --}}
            <td class="num">{{ number_format($subtotal['WTax'],2) }}</td>

            @if(in_array('SSSSalaryLoan',$visibleColumns))
                <td class="num">{{ number_format($grand['SSSSalaryLoan'],2) }}</td>
            @endif

            @if(in_array('SSSCalamityLoan',$visibleColumns))
                <td class="num">{{ number_format($grand['SSSCalamityLoan'],2) }}</td>
            @endif

            @if(in_array('HDMFLoan',$visibleColumns))
                <td class="num">{{ number_format($grand['HDMFLoan'],2) }}</td>
            @endif

            @if(in_array('HDMFCalamityLoan',$visibleColumns))
                <td class="num">{{ number_format($grand['HDMFCalamityLoan'],2) }}</td>
            @endif

            @if(in_array('OtherLoanDeductions',$visibleColumns))
                <td class="num">{{ number_format($grand['OtherLoanDeductions'],2) }}</td>
            @endif
            
            @if(in_array('OtherDeduction',$visibleColumns))
                <td class="num">{{ number_format($grand['OtherDeduction'],2) }}</td>
            @endif
            
            @if(in_array('TotalDeduction',$visibleColumns))
                <td class="num">{{ number_format($grand['TotalDeduction'],2) }}</td>
            @endif

            <td class="num">{{ number_format($subtotal['NetPay'],2) }}</td>
        </tr>

        <tr>
            <td colspan="{{ count($visibleColumns) + 3 }}" style="border:none;height:15px;"></td>
        </tr>

        @endif

        @php
            $subtotal = array_map(fn() => 0, $subtotal);
            $currentTeamLeader = $row->TeamLeader;
        @endphp

        <tr class="team-header">
            <td colspan="{{ count($visibleColumns) + 5 }}">
                TEAM LEADER : {{ $currentTeamLeader }}
            </td>
        </tr>

        <tr>
            <th>EMP NO</th>
            <th>EMPLOYEE NAME</th>
            <th>TEAM LEADER</th>
            <th>POSITION</th>
            <th>RATE</th>
            <th>DAYS</th>
            <th>BASIC PAY</th>
            @if(in_array('ECOLA',$visibleColumns))
                <th>ECOLA</th>
            @endif
            <th>LATE</th>
            <th>UT</th>
            <th>ABSENT</th>
            <th>SL</th>
            <th>VL</th>

            @if(in_array('OL',$visibleColumns))
                <th>OL</th>
            @endif

            <th>ND</th>
            <th>OT</th>

            @if(in_array('LH',$visibleColumns))
                <th>LH</th>
            @endif

            @if(in_array('SH',$visibleColumns))
                <th>SH</th>
            @endif

            @if(in_array('RDDPay',$visibleColumns))
                <th>RDD</th>
            @endif

            @if(in_array('OTND',$visibleColumns))
                <th>OTND</th>
            @endif

            {{-- @if(in_array('OtherTaxableEarnings',$visibleColumns))
                <th>OTH TAX</th>
            @endif --}}

            {{-- @if(in_array('OtherNonTaxableEarnings',$visibleColumns))
                <th>OTH NON TAX</th>
            @endif --}}

            @foreach($OtherEarningsTypes as $oe)

                @php
                    $alias = preg_replace(
                        '/[^A-Za-z0-9]/',
                        '_',
                        strtoupper($oe->Name)
                    );
                @endphp

                @if(in_array($alias,$visibleColumns))
                    <th>{{ strtoupper($oe->Name) }}</th>
                @endif

            @endforeach
            <th>GROSS</th>

            @if(in_array('SSS',$visibleColumns))
                <th>SSS</th>
            @endif

            @if(in_array('PHIC',$visibleColumns))
                <th>PHIC</th>
            @endif

            @if(in_array('HDMF',$visibleColumns))
                <th>HDMF</th>
            @endif

            @if(in_array('HDMFMP2',$visibleColumns))
                <th>MP2</th>
            @endif
            
            {{-- <th>TAXABLE</th> --}}
            <th>WTAX</th>

            @if(in_array('SSSSalaryLoan',$visibleColumns))
                <th>SSS SAL</th>
            @endif

            @if(in_array('SSSCalamityLoan',$visibleColumns))
                <th>SSS CAL</th>
            @endif

            @if(in_array('HDMFLoan',$visibleColumns))
                <th>HDMF</th>
            @endif

            @if(in_array('HDMFCalamityLoan',$visibleColumns))
                <th>HDMF CAL</th>
            @endif

            @if(in_array('OtherLoanDeductions',$visibleColumns))
                <th>OTH LOAN</th>
            @endif

            @if(in_array('OtherDeduction',$visibleColumns))
                <th>OTH DED</th>
            @endif

            @if(in_array('TotalDeduction',$visibleColumns))
                <th>TOTAL DED</th>
            @endif

            <th>NET PAY</th>
        </tr>

    @endif

    <tr>
        <td>{{ $row->EmployeeNo }}</td>
        <td>{{ $row->EmployeeName }}</td>
        <td>{{ $row->TeamLeader }}</td>
        <td>{{ $row->Position }}</td>
        <td>{{ $row->RateType == 1 ? $row->DailyRate : $row->MonthlyRate }}</td>
        <td class="num">{{ number_format($row->Days,2) }}</td>
        <td class="num">{{ number_format($row->BasicPay,2) }}</td>

        @if(in_array('ECOLA',$visibleColumns))
            <td class="num">{{ number_format($row->ECOLA,2) }}</td>
        @endif

        <td class="num">{{ number_format($row->LateAmount,2) }}</td>
        <td class="num">{{ number_format($row->UndertimeAmount,2) }}</td>
        <td class="num">{{ number_format($row->AbsentAmount,2) }}</td>
        <td class="num">{{ number_format($row->SL,2) }}</td>
        <td class="num">{{ number_format($row->VL,2) }}</td>

        @if(in_array('OL',$visibleColumns))
            <td class="num">{{ number_format($row->OL,2) }}</td>
        @endif

        <td class="num">{{ number_format($row->NightDiff,2) }}</td>
        <td class="num">{{ number_format($row->OTPay,2) }}</td>

        @if(in_array('LH',$visibleColumns))
            <td class="num">{{ number_format($row->LH,2) }}</td>
        @endif

        @if(in_array('SH',$visibleColumns))
            <td class="num">{{ number_format($row->SH,2) }}</td>
        @endif

        @if(in_array('RDDPay',$visibleColumns))
            <td class="num">{{ number_format($row->RDDPay,2) }}</td>
        @endif

        @if(in_array('OTND',$visibleColumns))
            <td class="num">{{ number_format($row->OTND,2) }}</td>
        @endif

        {{-- @if(in_array('OtherTaxableEarnings',$visibleColumns))
            <td class="num">{{ number_format($row->OtherTaxableEarnings,2) }}</td>
        @endif --}}

        {{-- @if(in_array('OtherNonTaxableEarnings',$visibleColumns))
            <td class="num">{{ number_format($row->OtherNonTaxableEarnings,2) }}</td>
        @endif --}}

        @foreach($OtherEarningsTypes as $oe)

            @php
                $alias = preg_replace(
                    '/[^A-Za-z0-9]/',
                    '_',
                    strtoupper($oe->Name)
                );
            @endphp

            @if(in_array($alias,$visibleColumns))
                <td class="num">
                    {{ number_format($row->$alias ?? 0,2) }}
                </td>
            @endif
        @endforeach

        <td class="num">{{ number_format($row->GrossPay,2) }}</td>

        @if(in_array('SSS',$visibleColumns))
            <td class="num">{{ number_format($row->SSS,2) }}</td>
        @endif

        @if(in_array('PHIC',$visibleColumns))
            <td class="num">{{ number_format($row->PHIC,2) }}</td>
        @endif

        @if(in_array('HDMF',$visibleColumns))
            <td class="num">{{ number_format($row->HDMF,2) }}</td>
        @endif

        @if(in_array('HDMFMP2',$visibleColumns))
            <td class="num">{{ number_format($row->HDMFMP2,2) }}</td>
        @endif

        {{-- <td class="num">{{ number_format($row->TaxableIncome,2) }}</td> --}}
        <td class="num">{{ number_format($row->WTax,2) }}</td>

        @if(in_array('SSSSalaryLoan',$visibleColumns))
            <td class="num">{{ number_format($row->SSSSalaryLoan,2) }}</td>
        @endif

        @if(in_array('SSSCalamityLoan',$visibleColumns))
            <td class="num">{{ number_format($row->SSSCalamityLoan,2) }}</td>
        @endif

        @if(in_array('HDMFLoan',$visibleColumns))
            <td class="num">{{ number_format($row->HDMFLoan,2) }}</td>
        @endif

        @if(in_array('HDMFCalamityLoan',$visibleColumns))
            <td class="num">{{ number_format($row->HDMFCalamityLoan,2) }}</td>
        @endif

        @if(in_array('OtherLoanDeductions',$visibleColumns))
            <td class="num">{{ number_format($row->OtherLoanDeductions,2) }}</td>
        @endif

        @if(in_array('OtherDeduction',$visibleColumns))
            <td class="num">{{ number_format($row->OtherDeduction,2) }}</td>
        @endif

        @if(in_array('TotalDeduction',$visibleColumns))
            <td class="num">{{ number_format($row->TotalDeduction,2) }}</td>
        @endif

        <td class="num">{{ number_format($row->NetPay,2) }}</td>
    </tr>

    @php

    foreach($subtotal as $key => $value){
        $subtotal[$key] += (float)($row->$key ?? 0);
    }

    @endphp

@endforeach

@if($currentTeamLeader !== null)

<tr class="subtotal">
    <td colspan="5">SUBTOTAL</td>

    <td class="num">{{ number_format($subtotal['Days'],2) }}</td>
    <td class="num">{{ number_format($subtotal['BasicPay'],2) }}</td>

    @if(in_array('ECOLA',$visibleColumns))
        <td class="num">{{ number_format($subtotal['ECOLA'],2) }}</td>
    @endif

    <td class="num">{{ number_format($subtotal['LateAmount'],2) }}</td>
    <td class="num">{{ number_format($subtotal['UndertimeAmount'],2) }}</td>
    <td class="num">{{ number_format($subtotal['AbsentAmount'],2) }}</td>
    <td class="num">{{ number_format($subtotal['SL'],2) }}</td>
    <td class="num">{{ number_format($subtotal['VL'],2) }}</td>

    @if(in_array('OL',$visibleColumns))
        <td class="num">{{ number_format($subtotal['OL'],2) }}</td>
    @endif

    <td class="num">{{ number_format($subtotal['NightDiff'],2) }}</td>
    <td class="num">{{ number_format($subtotal['OTPay'],2) }}</td>

    @if(in_array('LH',$visibleColumns))
        <td class="num">{{ number_format($subtotal['LH'],2) }}</td>
    @endif

    @if(in_array('SH',$visibleColumns))
        <td class="num">{{ number_format($subtotal['SH'],2) }}</td>
    @endif

    @if(in_array('RDDPay',$visibleColumns))
        <td class="num">{{ number_format($subtotal['RDDPay'],2) }}</td>
    @endif

    @if(in_array('OTND',$visibleColumns))
        <td class="num">{{ number_format($subtotal['OTND'],2) }}</td>
    @endif

    {{-- @if(in_array('OtherTaxableEarnings',$visibleColumns))
        <td class="num">{{ number_format($subtotal['OtherTaxableEarnings'],2) }}</td>
    @endif --}}

    {{-- @if(in_array('OtherNonTaxableEarnings',$visibleColumns))
        <td class="num">{{ number_format($subtotal['OtherNonTaxableEarnings'],2) }}</td>
    @endif --}}

    @foreach($OtherEarningsTypes as $oe)

        @php
            $alias = preg_replace(
                '/[^A-Za-z0-9]/',
                '_',
                strtoupper($oe->Name)
            );
        @endphp

        @if(in_array($alias,$visibleColumns))
            <td class="num">
                {{ number_format($subtotal[$alias] ?? 0,2) }}
            </td>
        @endif

    @endforeach

    <td class="num">{{ number_format($subtotal['GrossPay'],2) }}</td>

    @if(in_array('SSS',$visibleColumns))
        <td class="num">{{ number_format($subtotal['SSS'],2) }}</td>
    @endif

    @if(in_array('PHIC',$visibleColumns))
        <td class="num">{{ number_format($subtotal['PHIC'],2) }}</td>
    @endif

    @if(in_array('HDMF',$visibleColumns))
        <td class="num">{{ number_format($subtotal['HDMF'],2) }}</td>
    @endif

    @if(in_array('HDMFMP2',$visibleColumns))
        <td class="num">{{ number_format($subtotal['HDMFMP2'],2) }}</td>
    @endif

    {{-- <td class="num">{{ number_format($subtotal['TaxableIncome'],2) }}</td> --}}
    <td class="num">{{ number_format($subtotal['WTax'],2) }}</td>

    @if(in_array('SSSSalaryLoan',$visibleColumns))
        <td class="num">{{ number_format($subtotal['SSSSalaryLoan'],2) }}</td>
    @endif

    @if(in_array('SSSCalamityLoan',$visibleColumns))
        <td class="num">{{ number_format($subtotal['SSSCalamityLoan'],2) }}</td>
    @endif

    @if(in_array('HDMFLoan',$visibleColumns))
        <td class="num">{{ number_format($subtotal['HDMFLoan'],2) }}</td>
    @endif

    @if(in_array('HDMFCalamityLoan',$visibleColumns))
        <td class="num">{{ number_format($subtotal['HDMFCalamityLoan'],2) }}</td>
    @endif

    @if(in_array('OtherLoanDeductions',$visibleColumns))
        <td class="num">{{ number_format($subtotal['OtherLoanDeductions'],2) }}</td>
    @endif

    @if(in_array('OtherDeduction',$visibleColumns))
        <td class="num">{{ number_format($subtotal['OtherDeduction'],2) }}</td>
    @endif

    @if(in_array('TotalDeduction',$visibleColumns))
        <td class="num">{{ number_format($subtotal['TotalDeduction'],2) }}</td>
    @endif

    <td class="num">{{ number_format($subtotal['NetPay'],2) }}</td>
</tr>

@endif

<tr>
    <td colspan="{{ count($visibleColumns) + 5 }}" style="border:none;height:25px;"></td>
</tr>

<tr class="grandtotal">
    <td colspan="5">GRAND TOTAL</td>

    <td class="num">{{ number_format($grand['Days'],2) }}</td>
    <td class="num">{{ number_format($grand['BasicPay'],2) }}</td>

    @if(in_array('ECOLA',$visibleColumns))
        <td class="num">{{ number_format($grand['ECOLA'],2) }}</td>
    @endif

    <td class="num">{{ number_format($grand['LateAmount'],2) }}</td>
    <td class="num">{{ number_format($grand['UndertimeAmount'],2) }}</td>
    <td class="num">{{ number_format($grand['AbsentAmount'],2) }}</td>

    <td class="num">{{ number_format($grand['SL'],2) }}</td>
    <td class="num">{{ number_format($grand['VL'],2) }}</td>

    @if(in_array('OL',$visibleColumns))
        <td class="num">{{ number_format($grand['OL'],2) }}</td>
    @endif

    <td class="num">{{ number_format($grand['NightDiff'],2) }}</td>
    <td class="num">{{ number_format($grand['OTPay'],2) }}</td>

    @if(in_array('LH',$visibleColumns))
        <td class="num">{{ number_format($grand['LH'],2) }}</td>
    @endif

    @if(in_array('SH',$visibleColumns))
        <td class="num">{{ number_format($grand['SH'],2) }}</td>
    @endif

    @if(in_array('RDDPay',$visibleColumns))
        <td class="num">{{ number_format($grand['RDDPay'],2) }}</td>
    @endif

    @if(in_array('OTND',$visibleColumns))
        <td class="num">{{ number_format($grand['OTND'],2) }}</td>
    @endif

    {{-- @if(in_array('OtherTaxableEarnings',$visibleColumns))
        <td class="num">{{ number_format($grand['OtherTaxableEarnings'],2) }}</td>
    @endif --}}

    {{-- @if(in_array('OtherNonTaxableEarnings',$visibleColumns))
        <td class="num">{{ number_format($grand['OtherNonTaxableEarnings'],2) }}</td>
    @endif --}}

    @foreach($OtherEarningsTypes as $oe)

        @php
            $alias = preg_replace(
                '/[^A-Za-z0-9]/',
                '_',
                strtoupper($oe->Name)
            );
        @endphp

        @if(in_array($alias,$visibleColumns))
            <td class="num">
                {{ number_format($grand[$alias] ?? 0,2) }}
            </td>
        @endif

    @endforeach

    <td class="num">{{ number_format($grand['GrossPay'],2) }}</td>

    @if(in_array('SSS',$visibleColumns))
        <td class="num">{{ number_format($grand['SSS'],2) }}</td>
    @endif

    @if(in_array('PHIC',$visibleColumns))
        <td class="num">{{ number_format($grand['PHIC'],2) }}</td>
    @endif

    @if(in_array('HDMF',$visibleColumns))
        <td class="num">{{ number_format($grand['HDMF'],2) }}</td>
    @endif

    @if(in_array('HDMFMP2',$visibleColumns))
        <td class="num">{{ number_format($grand['HDMFMP2'],2) }}</td>
    @endif

    {{-- <td class="num">{{ number_format($grand['TaxableIncome'],2) }}</td> --}}
    <td class="num">{{ number_format($grand['WTax'],2) }}</td>

    @if(in_array('SSSSalaryLoan',$visibleColumns))
        <td class="num">{{ number_format($grand['SSSSalaryLoan'],2) }}</td>
    @endif

    @if(in_array('SSSCalamityLoan',$visibleColumns))
        <td class="num">{{ number_format($grand['SSSCalamityLoan'],2) }}</td>
    @endif

    @if(in_array('HDMFLoan',$visibleColumns))
        <td class="num">{{ number_format($grand['HDMFLoan'],2) }}</td>
    @endif

    @if(in_array('HDMFCalamityLoan',$visibleColumns))
        <td class="num">{{ number_format($grand['HDMFCalamityLoan'],2) }}</td>
    @endif

    @if(in_array('OtherLoanDeductions',$visibleColumns))
        <td class="num">{{ number_format($grand['OtherLoanDeductions'],2) }}</td>
    @endif

    @if(in_array('OtherDeduction',$visibleColumns))
        <td class="num">{{ number_format($grand['OtherDeduction'],2) }}</td>
    @endif

    @if(in_array('TotalDeduction',$visibleColumns))
        <td class="num">{{ number_format($grand['TotalDeduction'],2) }}</td>
    @endif

    <td class="num">{{ number_format($grand['NetPay'],2) }}</td>
</tr>

</table>

<script>
window.onload = function(){
    window.print();
}
</script>

</body>
</html>