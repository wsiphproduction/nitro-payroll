<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
      'do-admin-check-login',
      'do-admin-forgot-password',
      'do-scheduled-job',

      'do-save-dtr-temp-transaction',

      'post-division-info',
      'post-department-info',
      'post-section-info',
      'post-jobtype-info',
      'post-employee-info',
      'get-payroll-trans-details'

   
    ];
}
