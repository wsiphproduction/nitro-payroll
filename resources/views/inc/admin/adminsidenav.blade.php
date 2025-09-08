    <style type="text/css">
  
body.vertical-layout.vertical-menu-modern .main-menu .navigation .menu-content > li > a > i {
    margin-right: 1.25rem;
    width: 28px !important;
}
body.vertical-layout.vertical-menu-modern.menu-expanded .main-menu .navigation li.has-sub > a:not(.mm-next):after{
  color: #fff;
}
.main-menu.menu-light .navigation .navigation-header{
    height: 40px;
    line-height: 30px;
    text-transform: initial;
    font-size: 15px;
    text-align: left;
    margin: 5px 11px 0px 9px;
    padding: 7px 0px 10px 50px;
}
</style>
    
    <!-- BEGIN: Header-->
    <div class="header-navbar-shadow"></div>
    <nav class="header-navbar main-header-navbar navbar-expand-lg navbar navbar-with-menu fixed-top ">
        <div class="navbar-wrapper">
            <div class="navbar-container content" style="background: #fff;">
                <div class="navbar-collapse" id="navbar-mobile">
                    <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                        <ul class="nav navbar-nav">
                            <li class="nav-item mobile-menu d-xl-none mr-auto">
                                <a class="nav-link nav-menu-main menu-toggle hidden-xs" href="javascript:void(0);">
                                    <i class="ficon bx bx-menu"></i>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav navbar-nav">
                            <li class="nav-item d-none d-lg-block">
                                <a class="nav-link bookmark-star mx3-color">
                                  <h5> NITRO MANPOWER.: <span style="font-size:15px;">  </span></h5> 
                                </a>
                            </li>
                        </ul>
                    </div>
                    <ul class="nav navbar-nav float-right">
                        <li class="dropdown dropdown-user nav-item">
                          <a class="dropdown-toggle nav-link dropdown-user-link" href="javascript:void(0);" data-toggle="dropdown">
                            <div class="user-nav d-sm-flex d-none">
                               <span class="user-name mx3-color">Welcome: <span style="text-transform:capitalize;">{{Session::get('ADMIN_USERNAME')}}</span> </span>
                               <span class="user-status text-muted">Payroll Period: {{Session::get('ADMIN_PAYROLL_PERIOD_SCHED')}}</span>
                            </div>
                            <span>
                              <img class="round" src="{{ URL::to('public/img/admin-user-no-image.png') }}" alt="avatar" height="40" width="40">
                            </span>
                          </a>
                          <div class="dropdown-menu dropdown-menu-right pb-0">
                            <a class="dropdown-item" href="{{URL::route('admin-change-password')}}">
                              <i class="bx bx-lock-open-alt mr-50"></i> Change Password
                            </a>
                            <div class="dropdown-divider mb-0"></div>
                            <a class="dropdown-item" href="{{ URL::route('admin-logout') }}">
                              <i class="bx bx-power-off mr-50"></i> Logout
                            </a>
                          </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header" style="background: #f68c1f;border-right:6px solid #475F7B;">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto">
                    <a class="navbar-brand" href="{{ route('admin-dashboard') }}">
                        <div class="brand-logo">
                            <img class="logo" src="{{ URL::asset('public/img/nitromobilelogo.jpg') }}" />
                        </div>
                        <h2 class="brand-text mb-0 white-color" style="font-size: 20px;">Nitro Payroll</h2>
                    </a>
                </li>
                <li class="nav-item nav-toggle">
                    <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                        <i class="bx bx-x d-block d-xl-none font-medium-4 primary"></i>
                        <i class="toggle-icon bx bx-disc font-medium-4 d-none d-xl-block primary white-color" data-ticon="bx-disc"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation" data-icon-style="lines" style="padding-top: 10px;">
                <li class=" nav-item" style="{{$Page=='Admin Dashboard' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                  <a href="{{ route('admin-dashboard') }}">
                    <i class="menu-livicon" data-icon="line-chart"></i>
                    <span class="menu-title" data-i18n="Dashboard"style="{{$Page=='Admin Dashboard' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}">Admin Dashboard</span>
                  </a>
                </li>

            <!-- TRANSACTIONS -->   
            @php($IsEmployeeDTR = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Employee DTR'))
            @php($IsEmployeeLoan = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Employee Loan'))
            @php($IsIncomeDeductionTransaction = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Income/Deduction Transaction'))
            @php($IsPayrollTransaction = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Payroll Transaction'))
            @php($Is13thMonthTransaction = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'13th Month Transaction'))
            @php($IsFinalPayTransaction = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Final Pay Transaction'))
            @php($IsAnnualTaxTransaction = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Annual Tax Transaction'))
           
           @if($IsEmployeeDTR || $IsEmployeeLoan || $IsIncomeDeductionTransaction || $IsPayrollTransaction || $Is13thMonthTransaction || $IsFinalPayTransaction|| $IsAnnualTaxTransaction)

           @php($HasSub='has-sub') 
           @if($Page=='Employee DTR' || $Page=='Employee Loan' || $Page=='Income/Deduction Transaction' ||
                $Page=='Payroll Transaction' || $Page=='13th Month Transaction'
                )
              @php($HasSub='has-sub open')
          @endif

             <li class="nav-item {{$HasSub}}" style="background-color: #475F7B;">
                    <a href="javascript:void(0);" style="color: #fff;">
                        <i class="menu-livicon" data-icon="calculator" style="color: #fff !important;"></i>
                        <span class="menu-title" data-i18n="Product Management">Transactions List</span>
                    </a>
                    <ul class="menu-content">
                       @if($IsEmployeeDTR)  
                        <li>
                            <a href="{{URL::route('admin-employee-dtr')}}" style="{{$Page=='Employee DTR' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="timer"></i>
                                <span class="menu-item" data-i18n="Employee DTR" style="{{$Page=='Employee DTR' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Employee DTR </span>
                            </a>
                        </li>
                        @endif

                        @if($IsEmployeeLoan)  
                        <li>
                            <a href="{{URL::route('admin-employee-loan-transaction')}}" style="{{$Page=='Employee Loan' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                <i class="menu-livicon" data-icon="gift"></i>
                                <span class="menu-item" data-i18n="Employee Loan" style="{{$Page=='Employee Loan' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Employee Loan </span>
                            </a>
                        </li>
                        @endif

                        @if($IsIncomeDeductionTransaction) 
                        <li>
                            <a href="{{URL::route('admin-employee-income-deduction-transaction')}}" style="{{$Page=='Income/Deduction Transaction' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                              <i class="menu-livicon" data-icon="coins"></i>
                                <span class="menu-item" data-i18n="Income Deduction" style="{{$Page=='Income/Deduction Transaction' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Income/Deduction </span>
                            </a>
                        </li>
                        @endif

                         @if($IsPayrollTransaction) 
                        <li>
                            <a href="{{URL::route('admin-payroll-transaction')}}" style="{{$Page=='Payroll Transaction' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                              <i class="menu-livicon" data-icon="calculator"></i>
                                <span class="menu-item" data-i18n="Payroll Transaction" style="{{$Page=='Payroll Transaction' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Payroll Transaction </span>
                            </a>
                        </li>
                        @endif

                       @if($Is13thMonthTransaction) 
                        <li>
                            <a href="{{URL::route('admin-13th-month-transaction')}}" style="{{$Page=='13th Month Transaction' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                              <i class="menu-livicon" data-icon="calculator"></i>
                                <span class="menu-item" data-i18n="13th Month Trans" style="{{$Page=='13th Month Transaction' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> 13 Month Transaction </span>
                            </a>
                        </li>
                        @endif

                         @if($IsFinalPayTransaction) 
                        <li>
                            <a href="javascript:void(0);" style="{{$Page=='Final Pay Transaction' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                              <i class="menu-livicon" data-icon="calculator"></i>
                                <span class="menu-item" data-i18n="Final Pay Trans" style="{{$Page=='Final Pay Transaction' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Final Pay Transaction </span>
                            </a>
                        </li>
                        @endif

                      @if($IsAnnualTaxTransaction) 
                        <li>
                            <a href="javascript:void(0);" style="{{$Page=='Annual Tax Transaction' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                              <i class="menu-livicon" data-icon="calculator"></i>
                                <span class="menu-item" data-i18n="Annual Tax Trans" style="{{$Page=='Annual Tax Transaction' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Annual Tax Transaction </span>
                            </a>
                        </li>
                        @endif

                         <a href="javascript:void(0);" style="color: #fff;"></a>
                    </ul>
                </li>
            @endif

            <br>

            @php($IsEmployeeList = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Employee List'))
            @php($IsLoanType = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Loan Type'))
            @php($IsIncomeDeductionType = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Income/Deduction Type'))
            @php($IsPayrollPeriodSchedule = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Payroll Period Schedule'))
            @php($IsSSSTable = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'SSS Table'))
            @php($IsHDMFTable = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'HDMF Table'))
            @php($IsPHICTable = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'PHIC Table'))
            @php($IsAnnualIncomeTaxTable = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Annual Income Tax Table'))
            @php($IsWithholdingTaxTable = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'WithHolding Tax Table'))
              
            @if($IsEmployeeList || $IsLoanType || $IsIncomeDeductionType || $IsPayrollPeriodSchedule || $IsSSSTable || $IsHDMFTable || $IsPHICTable || $IsAnnualIncomeTaxTable || $IsWithholdingTaxTable)
             
            @php($HasSub='has-sub')  
            @if($Page=='Employee List' || $Page=='Loan Type' || $Page=='Allowance Type' || $Page=='OT Rates' || $Page=='Income/Deduction Type' ||
                $Page=='Payroll Period Schedule' || $Page=='SSS Table' || $Page=='HDMF Table' ||
                $Page=='PHIC Table' || $Page=='Annual Income Tax Table' || $Page=='WithHolding Tax Table'
                )
              @php($HasSub='has-sub open')
            @endif

            <li class="nav-item {{$HasSub}}" style="background-color: #475F7B;">
                    <a href="javascript:void(0);" style="color: #fff;">
                        <i class="menu-livicon" data-icon="list" style="color: #fff !important;"></i>
                        <span class="menu-title" data-i18n="Product Management"> Master Record </span>
                    </a>
                    <ul class="menu-content">
                       @if($IsEmployeeList)
                        <li>
                            <a href="{{URL::route('admin-employee-list')}}" style="{{$Page=='Employee List' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="users"></i>
                                <span class="menu-item" data-i18n="Employee List" style="{{$Page=='Employee List' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Employee </span>
                            </a>
                        </li>
                        @endif

                        @if($IsLoanType)
                        <li>
                            <a href="{{URL::route('admin-loan-type')}}" style="{{$Page=='Loan Type' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="list"></i>
                                <span class="menu-item" data-i18n="Loan Type" style="{{$Page=='Loan Type' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Loan  </span>
                            </a>
                        </li>
                        @endif

                     @if($IsLoanType)
                        <li>
                            <a href="{{URL::route('admin-allowance-type')}}" style="{{$Page=='Allowance Type' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="list"></i>
                                <span class="menu-item" data-i18n="Allowance Type" style="{{$Page=='Allowance Type' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Allowance  </span>
                            </a>
                        </li>
                        @endif

                       @if($IsLoanType)

                        <li>
                            <a href="{{URL::route('admin-ot-rates')}}" style="{{$Page=='OT Rates' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="list"></i>
                                <span class="menu-item" data-i18n="OT Rates" style="{{$Page=='OT Rates' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Over Time Rates </span>
                            </a>
                        </li>
                        @endif

                        @if($IsIncomeDeductionType)
                        <li>
                            <a href="{{URL::route('admin-earning-deduction-type')}}" style="{{$Page=='Income/Deduction Type' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="list"></i>
                                <span class="menu-item" data-i18n="Income/Deduction Type" style="{{$Page=='Income/Deduction Type' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Income/Deduction</span>
                            </a>
                        </li>
                        @endif

                        @if($IsPayrollPeriodSchedule)
                        <li>
                            <a href="{{URL::route('admin-payroll-period-schedule')}}" style="{{$Page=='Payroll Period Schedule' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="list"></i>
                                <span class="menu-item" data-i18n="Payroll Period Schedule" style="{{$Page=='Payroll Period Schedule' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Payroll Period </span>
                            </a>
                        </li>
                        @endif

                     @if($IsSSSTable)
                        <li>
                            <a href="{{URL::route('admin-sss-table-bracket')}}" style="{{$Page=='SSS Table' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="list"></i>
                                <span class="menu-item" data-i18n="SSS Table" style="{{$Page=='SSS Table' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> SSS Table </span>
                            </a>
                        </li>
                        @endif

                        @if($IsHDMFTable)
                        <li>
                            <a href="{{URL::route('admin-hdmf-table-bracket')}}" style="{{$Page=='HDMF Table' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="list"></i>
                                <span class="menu-item" data-i18n="HDMF Table" style="{{$Page=='HDMF Table' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> HDMF Table </span>
                            </a>
                        </li>
                        @endif

                        @if($IsPHICTable)
                        <li>
                            <a href="{{URL::route('admin-phic-table-bracket')}}" style="{{$Page=='PHIC Table' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="list"></i>
                                <span class="menu-item" data-i18n="PHIC Table" style="{{$Page=='PHIC Table' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> PHIC Table </span>
                            </a>
                        </li>
                        @endif

         
                        @if($IsAnnualIncomeTaxTable)
                        <li>
                            <a href="{{URL::route('admin-annual-income-tax-table-bracket')}}" style="{{$Page=='Annual Income Tax Table' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="list"></i>
                                <span class="menu-item" data-i18n="Annual Income Tax Table" style="{{$Page=='Annual Income Tax Table' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Annual Income Tax  </span>
                            </a>
                        </li>
                        @endif

                        @if($IsWithholdingTaxTable)
                        <li>
                            <a href="{{URL::route('admin-withholding-tax-table-bracket')}}" style="{{$Page=='WithHolding Tax Table' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="list"></i>
                                <span class="menu-item" data-i18n="WithHolding Tax Table" style="{{$Page=='WithHolding Tax Table' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> WithHolding Tax  </span>
                            </a>
                        </li>
                        @endif

                         <a href="javascript:void(0);" style="color: #fff;"></a>
                    </ul>
                </li>

        @endif  

           <br>  
           <!-- REPORTS -->
          @php($IsEmployeePayslipReport = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Employee Payslip Report')) 
          @php($IsPayrollRegisterReport = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Payroll Register Report')) 
          @php($IsSSSContributionReport = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'SSS Contribution Report'))
          @php($IsHDMFContributionReport = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'HDMF Contribution Report'))
          @php($IsPHICContributionReport = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'PHIC Contribution Report'))
          @php($IsEmployeeDTRReport = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Employee DTR Report'))
          @php($IsEmployeeLoanReport = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Employee Loan Report'))
          @php($IncomeDeductionReport = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Income/Deduction Report'))
 
          @if($IsEmployeePayslipReport || $IsPayrollRegisterReport || $IsSSSContributionReport || $IsHDMFContributionReport || $IsPHICContributionReport || $IsEmployeeDTRReport|| $IsEmployeeLoanReport || $IncomeDeductionReport )
            
        @php($HasSub='has-sub')    
         @if($Page=='Employee Payslip Report' || $Page=='Payroll Journal Report' || $Page=='Payroll Register Report' || $Page=='SSS Contribution Report' ||
                $Page=='HDMF Contribution Report' || $Page=='SSS Contribution Report' || $Page=='PHIC Contribution Report' || $Page=='Employee DTR Report'  ||
                $Page=='Employee Loan Report' || $Page=='Employee Other Deduction Report' || $Page=='Raw Data Report' || $Page=='Employee Income Taxable Report' || 
                $Page=='Employee Income Non Taxable Report' 
                )
              @php($HasSub='has-sub open')
          @endif
               
          <li class="nav-item {{$HasSub}}" style="background-color: #475F7B;">
                    <a href="javascript:void(0);" style="color: #fff;">
                        <i class="menu-livicon" data-icon="print-doc" style="color: #fff !important;"></i>
                        <span class="menu-title" data-i18n="Product Management"> Reports </span>
                    </a>

                    <ul class="menu-content">


                         @if($IsEmployeeDTRReport)
                        <li>
                            <a href="{{URL::route('admin-employee-dtr-summary-report')}}" style="{{$Page=='Employee DTR Report' ? 'background-color:#fff;' : 'background-color:#f2f4f3;'}}">
                                 <i class="menu-livicon" data-icon="print-doc"></i>
                                <span class="menu-item" data-i18n="Employee DTR Report" style="{{$Page=='Employee DTR Report' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;'}}"> Employee DTR </span>
                            </a>
                        </li>
                        @endif

                       @if($IsEmployeePayslipReport)
                        <li>
                            <a href="{{URL::route('admin-employee-payslip-report')}}" style="{{$Page=='Employee Payslip Report' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                                 <i class="menu-livicon" data-icon="print-doc"></i>
                                <span class="menu-item" data-i18n="Employee Payslip Report" style="{{$Page=='Employee Payslip Report' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}"> Employee Payslip  </span>
                            </a>
                        </li>
                        @endif



                        <li>
                            <a href="{{URL::route('admin-payroll-journal-report')}}" style="{{$Page=='Payroll Journal Report' ? 'background-color:#fff;' : 'background-color:#f2f4f3;'}}">
                                 <i class="menu-livicon" data-icon="print-doc"></i>
                                <span class="menu-item" data-i18n="Payroll Register Report" style="{{$Page=='Payroll Journal Report' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;'}}"> Payroll Journal </span>
                            </a>
                        </li>

                        @if($IsPayrollRegisterReport)
                        <li>
                            <a href="{{URL::route('admin-payroll-register-report')}}" style="{{$Page=='Payroll Register Report' ? 'background-color:#fff;' : 'background-color:#f2f4f3;'}}">
                                 <i class="menu-livicon" data-icon="print-doc"></i>
                                <span class="menu-item" data-i18n="Payroll Register Report" style="{{$Page=='Payroll Register Report' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;'}}"> Payroll Register </span>
                            </a>
                        </li>
                        @endif
                       

                         <li>
                            <a href="{{URL::route('admin-payroll-raw-data-report')}}" style="{{$Page=='Raw Data Report' ? 'background-color:#fff;' : 'background-color:#f2f4f3;'}}">
                                 <i class="menu-livicon" data-icon="print-doc"></i>
                                <span class="menu-item" data-i18n="Raw Data Report" style="{{$Page=='Raw Data Report' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;'}}"> Raw Data List </span>
                            </a>
                        </li>

                        @if($IsSSSContributionReport)
                        <li>
                            <a href="{{URL::route('admin-sss-contribution-report')}}" style="{{$Page=='SSS Contribution Report' ? 'background-color:#fff;' : 'background-color:#f2f4f3;'}}">
                                 <i class="menu-livicon" data-icon="print-doc"></i>
                                <span class="menu-item" data-i18n="SSS Contribution Report" style="{{$Page=='SSS Contribution Report' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;'}}"> SSS Contribution </span>
                            </a>
                        </li>
                        @endif

                        @if($IsHDMFContributionReport)
                        <li>
                            <a href="{{URL::route('admin-hdmf-contribution-report')}}" style="{{$Page=='HDMF Contribution Report' ? 'background-color:#fff;' : 'background-color:#f2f4f3;'}}">
                                 <i class="menu-livicon" data-icon="print-doc"></i>
                                <span class="menu-item" data-i18n="HDMF Contribution Report" style="{{$Page=='HDMF Contribution Report' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;'}}"> HDMF Contribution </span>
                            </a>
                        </li>
                        @endif

                        @if($IsPHICContributionReport)
                        <li>
                            <a href="{{URL::route('admin-phic-contribution-report')}}" style="{{$Page=='PHIC Contribution Report' ? 'background-color:#fff;' : 'background-color:#f2f4f3;'}}">
                                 <i class="menu-livicon" data-icon="print-doc"></i>
                                <span class="menu-item" data-i18n="PHIC Contribution Report" style="{{$Page=='PHIC Contribution Report' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;'}}"> PHIC Contribution </span>
                            </a>
                        </li>
                        @endif
                 
                        @if($IsEmployeeLoanReport)
                        <li>
                            <a href="{{URL::route('admin-employee-loan-report')}}" style="{{$Page=='Employee Loan Report' ? 'background-color:#fff;' : 'background-color:#f2f4f3;'}}">
                                 <i class="menu-livicon" data-icon="print-doc"></i>
                                <span class="menu-item" data-i18n="Employee Loan Report" style="{{$Page=='Employee Loan Report' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;'}}">  Loan Deduction</span>
                            </a>
                        </li>
                        @endif

                         <li>
                            <a href="{{URL::route('admin-employee-other-deduction-report')}}" style="{{$Page=='Employee Other Deduction Report' ? 'background-color:#fff;' : 'background-color:#f2f4f3;'}}">
                                 <i class="menu-livicon" data-icon="print-doc"></i>
                                <span class="menu-item" data-i18n="Income Deduction Report" style="{{$Page=='Employee Other Deduction Report' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;'}}"> Other Deduction </span>
                            </a>
                        </li>

                         <li>
                            <a href="{{URL::route('admin-employee-income-taxable-report')}}" style="{{$Page=='Employee Income Taxable Report' ? 'background-color:#fff;' : 'background-color:#f2f4f3;'}}">
                                 <i class="menu-livicon" data-icon="print-doc"></i>
                                <span class="menu-item" data-i18n="Employee Income Taxable Report" style="{{$Page=='Employee Income Taxable Report' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;'}}"> Other Earning Taxable </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{URL::route('admin-employee-income-non-taxable-report')}}" style="{{$Page=='Employee Income Non Taxable Report' ? 'background-color:#fff;' : 'background-color:#f2f4f3;'}}">
                                 <i class="menu-livicon" data-icon="print-doc"></i>
                                <span class="menu-item" data-i18n="Employee Income Non Taxable Report" style="{{$Page=='Employee Income Non Taxable Report' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px'}}"> Earning Non Taxable </span>
                            </a>
                        </li>

                        <a href="javascript:void(0);" style="color: #fff;"></a>
                    </ul>
                </li>

          @endif        

            <!--User Account  Management-->
            @php($IsUserAccountList = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'User Account List'))
                @if($IsUserAccountList)
                <li class="navigation-header">
                   <span  class="white-color"> Accounts </span>
                </li>
            @endif
                
            @if($IsUserAccountList)
                 <li class=" nav-item" style="{{$Page=='User Account List' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                   <a href="{{URL::route('admin-user-account-list')}}">
                    <i class="menu-livicon" data-icon="users"></i>
                    <span class="menu-title" data-i18n="Loan Type" style="{{$Page=='User Account List' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}">User Account List</span>
                  </a>
                </li>
             @endif

             @php($IsPayrollSetting = $AdminUsers->getAdminUserAccess(Session::get('ADMIN_USER_ID'),'Payroll Setting'))
                @if($IsPayrollSetting)
                <li class="navigation-header">
                   <span  class="white-color"> Settings </span>
                </li>
              @endif

              @if($IsPayrollSetting)
                 <li class=" nav-item" style="{{$Page=='Payroll Setting' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                   <a href="{{URL::route('admin-payroll-setting')}}">
                    <i class="menu-livicon" data-icon="gears"></i>
                    <span class="menu-title" data-i18n="Sales Report" style="{{$Page=='Payroll Setting' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}">Payroll Settings</span>
                  </a>
                </li>
                @endif

                <li class="navigation-header">
                         <span  class="white-color">  Others </span>
                </li>

                  <li class=" nav-item" style="{{$Page=='Admin Change Password' ? 'background-color:#fff;' : 'background-color:#f2f4f3;' }}">
                    <a href="{{URL::route('admin-change-password')}}">
                    <i class="menu-livicon" data-icon="morph-lock"></i>
                    <span class="menu-title" data-i18n="User Account" style="{{$Page=='Admin Change Password' ? 'color:#f68c1f;font-size:14px;' : 'color:#8494a7;font-size:14px;' }}">Change Password</span>
                  </a>
                </li>

               <li class=" nav-item">
                    <a href="{{URL::route('admin-logout')}}">
                    <i class="menu-livicon" data-icon="angle-wide-left-alt"></i>
                    <span class="menu-title" data-i18n="User Account">Log Out</span>
                  </a>
                </li>
            
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->
