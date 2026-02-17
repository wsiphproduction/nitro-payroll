<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SyncEmployeesJob;

class SyncEmployeeController extends Controller
{
    public function start()
    {
        SyncEmployeesJob::dispatch();

        return response()->json([
            'message' => 'Employee sync has started. This will run in the background.'
        ]);
    }
}
