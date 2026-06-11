<?php

namespace App\Http\Controllers;

use App\Models\GenerateExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function printPayrollRegister(Request $request)
    {
        $Reports = new GenerateExcel();

        if ($request->Status == "Approved") {
            $list = $Reports->generatePayrollRegisterApprovedListExcel(
                $request->all()
            );
        } elseif ($request->Status == "Pending") {
            $list = $Reports->generatePayrollRegisterPendingListExcel(
                $request->all()
            );
        }

        $OtherEarningsTypes = DB::table('payroll_employee_income_deduction_transaction as t')
            ->join(
                'payroll_income_deduction_type as p',
                'p.ID',
                '=',
                't.IncomeDeductionTypeID'
            )
            ->select(
                'p.ID as IncomeDeductionTypeID',
                'p.Name',
                'p.Code'
            )
            ->where('p.Code', 'like', 'OE%')
            ->distinct()
            ->orderBy('p.Code')
            ->get();

            // dd($OtherEarningsTypes, $list);

        return view(
            'admin.payroll-register-print',
            compact(
                'list',
                'OtherEarningsTypes'
            )
        );
    }
}
