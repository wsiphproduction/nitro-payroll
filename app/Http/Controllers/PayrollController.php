<?php

namespace App\Http\Controllers;

use App\Models\GenerateExcel;
use Illuminate\Http\Request;

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

        return view(
            'admin.payroll-register-print',
            compact('list')
        );
    }
}
