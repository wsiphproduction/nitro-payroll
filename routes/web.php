<?php

use App\Http\Controllers\SyncEmployeeController;
use Illuminate\Support\Facades\Route;

//ADMIN LOGIN====================================================================
Route::get('/',[
    'uses'=>'AdminUserController@showAdminLogin',
    'as'=> '/'
]);

Route::get('home',[
    'uses'=>'AdminUserController@showAdminLogin',
    'as'=> 'home'
]);

Route::get('admin-login',[
    'uses'=>'AdminUserController@showAdminLogin',
    'as'=> 'admin-login'
]);

Route::get('admin-logout`',[
    'uses'=>'AdminUserController@AdminLogout',
    'as'=> 'admin-logout'
]);

Route::post('do-admin-check-login',[
    'uses'=>'AdminUserController@doAdminCheckLogin',
    'as'=> 'do-admin-check-login'
]);

Route::get('forgot-password',[
    'uses'=>'AdminUserController@showAdminForgotPassword',
    'as'=> 'forgot-password'
]);

Route::post('do-admin-forgot-password',[
    'uses'=>'AdminUserController@doAdminForgotPassword',
    'as'=> 'do-admin-forgot-password'
]);

Route::get('admin-change-password',[
    'uses'=>'AdminUserController@showAdminChangePassword',
    'as'=> 'admin-change-password'
]);

Route::post('do-admin-change-password',[
    'uses'=>'AdminUserController@doAdminChangePassword',
    'as'=> 'do-admin-change-password'
]);

Route::post('request-change-password',[
    'uses'=>'AdminUserController@doAdminRequestForChangePassword',
    'as'=> 'request-change-password'
]);

//USER ADMIN===================================================================
Route::get('admin-dashboard',[
    'uses'=>'AdminUserController@showAdminDashboard',
    'as'=> 'admin-dashboard'
]);

Route::get('admin-user-account-list',[
    'uses'=>'AdminUserController@showAdminUserAccountList',
    'as'=> 'admin-user-account-list'
]);

Route::post('get-admin-user-list',[
    'uses'=>'AdminUserController@getEmployeeAdminList',
    'as'=> 'get-admin-user-list'
]);

Route::post('get-admin-user-info',[
    'uses'=>'AdminUserController@getEmployeeAdminInfo',
    'as'=> 'get-admin-user-info'
]);

Route::post('do-save-user-account',[
    'uses'=>'AdminUserController@doSaveUserAccount',
    'as'=> 'do-save-user-account'
]);

Route::post('get-admin-user-info-access-menu',[
    'uses'=>'AdminUserController@getEmployeeAdminInfo',
    'as'=> 'get-admin-user-info-access-menu'
]);

//EMPLOYEE ===================================================================
Route::get('admin-employee-list',[
    'uses'=>'EmployeeController@showAdminEmployee',
    'as'=> 'admin-employee-list'
]);

Route::post('get-employee-list',[
    'uses'=>'EmployeeController@getEmployeeList',
    'as'=> 'get-employee-list'
]);

Route::post('get-employee-info',[
    'uses'=>'EmployeeController@getEmployeeInfo',
    'as'=> 'get-employee-info'
]);

Route::post('get-employee-search-list',[
    'uses'=>'EmployeeController@getEmployeeSearchList',
    'as'=> 'get-employee-search-list'
]);

Route::post('post-employee-info',[
    'uses'=>'EmployeeController@postEmployeeInfo',
    'as'=> 'post-employee-info'
]);

//SET NEW EMPLOYEE HDMF CONTRIBUTION=============================================
Route::post('do-update-employee-new-hmdf-contribution',[
    'uses'=>'EmployeeController@doUpdateEmployeeNewHMDFContribution',
    'as'=> 'do-update-employee-new-hmdf-contribution'
]);

//SET EMPLOYEE MP2 SAVING CONTRIBUTION===========================================
Route::post('do-save-employee-mp2-contribution-set-up',[
    'uses'=>'EmployeeController@doSaveUpdateEmployeeMP2Contribution',
    'as'=> 'do-save-employee-mp2-contribution-set-up'
]);

Route::post('do-clear-mp2-temp-upload',[
    'uses'=>'EmployeeController@doClearMP2TempUpload',
    'as'=> 'do-clear-mp2-temp-upload'
]);

Route::post('get-mp2-temp-upload-count',[
    'uses'=>'EmployeeController@getTempMP2UploadCount',
    'as'=> 'get-mp2-temp-upload-count'
]);

Route::post('do-save-employee-temp-mp2-batch',[
    'uses'=>'EmployeeController@doSaveEmployeeTempMP2Batch',
    'as'=> 'do-save-employee-temp-mp2-batch'
]);

Route::post('get-employee-temp-mp2-list',[
    'uses'=>'EmployeeController@getEmployeeTempMP2List',
    'as'=> 'get-employee-temp-mp2-list'
]);

Route::post('do-remove-duplicate-temp-employee-mp2',[
    'uses'=>'EmployeeController@doRemoveDuplicateTempMP2Upload',
    'as'=> 'do-remove-duplicate-temp-employee-mp2'
]);

Route::post('get-employee-temp-mp2-info',[
    'uses'=>'EmployeeController@getEmployeeTempMP2Info',
    'as'=> 'get-employee-temp-mp2-info'
]);

Route::post('do-upload-save-employee-mp2',[
    'uses'=>'EmployeeController@doSaveUploadEmployeeMP2',
    'as'=> 'do-upload-save-employee-mp2'
]);

Route::post('do-save-employee-temp-mp2',[
    'uses'=>'EmployeeController@doSaveEmployeeTempMP2',
    'as'=> 'do-save-employee-temp-mp2'
]);

//SET EMPLOYEE ALLOWANCE SAVING CONTRIBUTION======================================
Route::post('do-save-employee-allowance-set-up',[
    'uses'=>'EmployeeController@doSaveUpdateEmployeeAllowance',
    'as'=> 'do-save-employee-allowance-set-up'
]);

Route::post('get-employee-allowance-set-up-list',[
    'uses'=>'EmployeeController@getEmployeeAllowanceSetUpList',
    'as'=> 'get-employee-allowance-set-up-list'
]);

//EMPLOYEE RATE===================================================================
Route::post('get-employee-rate-list',[
    'uses'=>'EmployeeController@getEmployeeRateList',
    'as'=> 'get-employee-rate-list'
]);

Route::post('get-employee-rate-info',[
    'uses'=>'EmployeeController@getEmployeeRateInfo',
    'as'=> 'get-employee-rate-info'
]);

Route::post('do-save-employee-rate',[
    'uses'=>'EmployeeController@doSaveEmployeeRate',
    'as'=> 'do-save-employee-rate'
]);

Route::post('get-employee-and-rate-search-list',[
    'uses'=>'EmployeeController@getEmployeeAndRateSearchList',
    'as'=> 'get-employee-and-rate-search-list'
]);

Route::post('get-employee-rate-id',[
    'uses'=>'EmployeeController@getEmployeeRateID',
    'as'=> 'get-employee-rate-id'
]);

Route::post('do-upload-save-employee-rate',[
    'uses'=>'EmployeeController@doSaveUploadEmployeeRates',
    'as'=> 'do-upload-save-employee-rate'
]);

//UPLOAD TEMP RATE=================================================================
Route::post('get-employee-temp-rate-list',[
    'uses'=>'EmployeeController@getEmployeeTempRateList',
    'as'=> 'get-employee-temp-rate-list'
]);

Route::post('do-clear-rate-temp-upload',[
    'uses'=>'EmployeeController@doClearRateTempUpload',
    'as'=> 'do-clear-rate-temp-upload'
]);

Route::post('do-save-employee-temp-rate-batch',[
    'uses'=>'EmployeeController@doSaveEmployeeTempRateBatch',
    'as'=> 'do-save-employee-temp-rate-batch'
]);

Route::post('get-rate-temp-upload-count',[
    'uses'=>'EmployeeController@getTempRateUploadCount',
    'as'=> 'get-rate-temp-upload-count'
]);

Route::post('do-remove-duplicate-temp-employee-rate',[
    'uses'=>'EmployeeController@doRemoveDuplicateTempRateUpload',
    'as'=> 'do-remove-duplicate-temp-employee-rate'
]);

Route::post('get-employee-temp-rate-info',[
    'uses'=>'EmployeeController@getEmployeeTempRateInfo',
    'as'=> 'get-employee-temp-rate-info'
]);

Route::post('do-save-employee-temp-rate',[
    'uses'=>'EmployeeController@doSaveEmployeeTempRate',
    'as'=> 'do-save-employee-temp-rate'
]);

//LOAN TYPE=================================================================
Route::get('admin-loan-type',[
    'uses'=>'LoanTypeController@showAdminLoanType',
    'as'=> 'admin-loan-type'
]);

Route::post('get-loan-type-list',[
    'uses'=>'LoanTypeController@getLoanTypeList',
    'as'=> 'get-loan-type-list'
]);

Route::post('get-loan-type-info',[
    'uses'=>'LoanTypeController@getLoanTypeInfo',
    'as'=> 'get-loan-type-info'
]);

Route::post('do-save-loan-type',[
    'uses'=>'LoanTypeController@doSaveLoanType',
    'as'=> 'do-save-loan-type'
]);

Route::post('get-loan-type-search-list',[
    'uses'=>'LoanTypeController@getLoanTypeSearchList',
    'as'=> 'get-loan-type-search-list'
]);

//ALLOWANCE TYPE=================================================================
Route::get('admin-allowance-type',[
    'uses'=>'AllowanceTypeController@showAdminAllowanceType',
    'as'=> 'admin-allowance-type'
]);

Route::post('get-allowance-type-list',[
    'uses'=>'AllowanceTypeController@getAllowanceTypeList',
    'as'=> 'get-allowance-type-list'
]);

Route::post('get-allowance-type-info',[
    'uses'=>'AllowanceTypeController@getAllowanceTypeInfo',
    'as'=> 'get-allowance-type-info'
]);

Route::post('do-save-allowance-type',[
    'uses'=>'AllowanceTypeController@doSaveAllowanceType',
    'as'=> 'do-save-allowance-type'
]);

Route::post('get-allowance-type-search-list',[
    'uses'=>'AllowanceTypeController@getAllowanceTypeSearchList',
    'as'=> 'get-allowance-type-search-list'
]);


//OT RATES =================================================================
Route::get('admin-ot-rates',[
    'uses'=>'OTRateController@showAdminOTRates',
    'as'=> 'admin-ot-rates'
]);

Route::post('get-ot-rate-list',[
    'uses'=>'OTRateController@getOTRateList',
    'as'=> 'get-ot-rate-list'
]);

Route::post('get-ot-rate-info',[
    'uses'=>'OTRateController@getOTRateInfo',
    'as'=> 'get-ot-rate-info'
]);

Route::post('do-save-ot-rate',[
    'uses'=>'OTRateController@doSaveOTRate',
    'as'=> 'do-save-ot-rate'
]);

Route::post('get-loan-type-search-list',[
    'uses'=>'LoanTypeController@getLoanTypeSearchList',
    'as'=> 'get-loan-type-search-list'
]);
//INCOME/DEDUCTION TYPE==============================================================
Route::get('admin-earning-deduction-type',[
    'uses'=>'IncomeDeductionTypeController@showAdminEarningDeductionType',
    'as'=> 'admin-earning-deduction-type'
]);

Route::post('get-earning-deduction-type-list',[
    'uses'=>'IncomeDeductionTypeController@getEarningDeductionTypeList',
    'as'=> 'get-earning-deduction-type-list'
]);

Route::post('get-earning-deduction-type-info',[
    'uses'=>'IncomeDeductionTypeController@getIncomeDeductionTypeInfo',
    'as'=> 'get-earning-deduction-type-info'
]);

Route::post('do-save-earning-deduction-type',[
    'uses'=>'IncomeDeductionTypeController@doSaveIncomeDeductionType',
    'as'=> 'do-save-earning-deduction-type'
]);

Route::post('get-earning-deduction-type-search-list',[
    'uses'=>'IncomeDeductionTypeController@getIncomeDeductionTypeSearchList',
    'as'=> 'get-earning-deduction-type-search-list'
]);

//PAYROLL SCHEDULE=================================================================
Route::get('admin-payroll-period-schedule',[
    'uses'=>'PayrollPeriodController@showAdminPayrollPeriodSchedule',
    'as'=> 'admin-payroll-period-schedule'
]);

Route::post('get-payroll-period-schedule-list',[
    'uses'=>'PayrollPeriodController@getPayrollPeriodSheduleList',
    'as'=> 'get-payroll-period-schedule-list'
]);

Route::post('get-payroll-period-schedule-info',[
    'uses'=>'PayrollPeriodController@getPayrollScheduleInfo',
    'as'=> 'get-payroll-period-schedule-info'
]);

Route::post('do-save-payroll-period-schedule',[
    'uses'=>'PayrollPeriodController@doSavePayrollPeriodSchedule',
    'as'=> 'do-save-payroll-period-schedule'
]);

Route::post('get-payroll-period-search-list',[
    'uses'=>'PayrollPeriodController@getPayrollPeriodSearchList',
    'as'=> 'get-payroll-period-search-list'
]);

//SSS TAX BRACKET SCHEDULE================================================================
Route::get('admin-sss-table-bracket',[
    'uses'=>'SSSTableController@showAdminSSSTableBracket',
    'as'=> 'admin-sss-table-bracket'
]);

Route::post('get-sss-table-bracket-list',[
    'uses'=>'SSSTableController@getSSSTableBracketList',
    'as'=> 'get-sss-table-bracket-list'
]);

Route::post('get-sss-table-bracket-info',[
    'uses'=>'SSSTableController@getSSSTableBracketnfo',
    'as'=> 'get-sss-table-bracket-info'
]);

Route::post('do-save-sss-table-bracket',[
    'uses'=>'SSSTableController@doSaveSSSTableBracket',
    'as'=> 'do-save-sss-table-bracket'
]);

//PHIC TAX BRACKET SCHEDULE=================================================================
Route::get('admin-phic-table-bracket',[
    'uses'=>'PHICTableController@showAdminPHICTableBracket',
    'as'=> 'admin-phic-table-bracket'
]);

Route::post('get-phic-table-bracket-list',[
    'uses'=>'PHICTableController@getPHICTableBracketList',
    'as'=> 'get-phic-table-bracket-list'
]);

Route::post('get-phic-table-bracket-info',[
    'uses'=>'PHICTableController@getPHICTableBracketnfo',
    'as'=> 'get-phic-table-bracket-info'
]);

Route::post('do-phic-table-bracket',[
    'uses'=>'PHICTableController@doSavePHICTableBracket',
    'as'=> 'do-phic-table-bracket'
]);

//HDMF TAX BRACKET SCHEDULE=================================================================
Route::get('admin-hdmf-table-bracket',[
    'uses'=>'HDMFTableController@showAdminHDMFTableBracket',
    'as'=> 'admin-hdmf-table-bracket'
]);

Route::post('get-hdmf-table-bracket-list',[
    'uses'=>'HDMFTableController@getHDMFTableBracketList',
    'as'=> 'get-hdmf-table-bracket-list'
]);

Route::post('get-hdmf-table-bracket-info',[
    'uses'=>'HDMFTableController@getDMFTableBracketInfo',
    'as'=> 'get-hdmf-table-bracket-info'
]);

Route::post('do-hdmf-table-bracket',[
    'uses'=>'HDMFTableController@doSaveHDMFTableBracket',
    'as'=> 'do-hdmf-table-bracket'
]);

//ANNUAL TAX INCOME BRACKET SCHEDULE===========================================================
Route::get('admin-annual-income-tax-table-bracket',[
    'uses'=>'AnnualIncomeTaxController@showAdminAnnualIncomeTaxTableBracket',
    'as'=> 'admin-annual-income-tax-table-bracket'
]);

Route::post('get-annual-income-tax-table-bracket-list',[
    'uses'=>'AnnualIncomeTaxController@getAnnualIncomeTaxTableBracketList',
    'as'=> 'get-annual-income-tax-table-bracket-list'
]);

Route::post('get-annual-income-tax-table-info',[
    'uses'=>'AnnualIncomeTaxController@getAnnualIncomeTaxTableInfo',
    'as'=> 'get-annual-income-tax-table-info'
]);

Route::post('do-save-annual-income-tax-table',[
    'uses'=>'AnnualIncomeTaxController@doSaveAnnaulIncomeTaxTableBracket',
    'as'=> 'do-save-annual-income-tax-table'
]);

//WITH HOLDING TAX BRACKET SCHEDULE================================================================
Route::get('admin-withholding-tax-table-bracket',[
    'uses'=>'WithHoldingTaxController@showAdminWithholdingTaxTableBracket',
    'as'=> 'admin-withholding-tax-table-bracket'
]);

Route::post('get-withholding-tax-table-bracket-list',[
    'uses'=>'WithHoldingTaxController@getWithholdingTaxTableBracketList',
    'as'=> 'get-withholding-tax-table-bracket-list'
]);

Route::post('get-withholding-tax-table-bracket-info',[
    'uses'=>'WithHoldingTaxController@getWithholdingTaxBracketInfo',
    'as'=> 'get-withholding-tax-table-bracket-info'
]);

Route::post('do-save-withholding-tax-table',[
    'uses'=>'WithHoldingTaxController@doSaveWithHoldingTaxTableBracket',
    'as'=> 'do-save-withholding-tax-table'
]);

//EMPLOYEE DTR =================================================================

Route::get('admin-employee-dtr',[
    'uses'=>'EmployeeDTRController@showAdminDTRUploader',
    'as'=> 'admin-employee-dtr'
]);

Route::post('get-employee-dtr-list',[
    'uses'=>'EmployeeDTRController@getEmployeeDTRList',
    'as'=> 'get-employee-dtr-list'
]);

Route::post('get-employee-dtr-info',[
    'uses'=>'EmployeeDTRController@getEmployeeDTRInfo',
    'as'=> 'get-employee-dtr-info'
]);

Route::post('do-save-employee-dtr-transaction',[
    'uses'=>'EmployeeDTRController@doSaveEmployeeDTR',
    'as'=> 'do-save-employee-dtr-transaction'
]);

Route::post('do-upload-save-dtr-transaction',[
    'uses'=>'EmployeeDTRController@doSaveUploadFinalDTRDTransaction',
    'as'=> 'do-upload-save-dtr-transaction'
]);

Route::post('do-set-dtr-transaction-status',[
    'uses'=>'EmployeeDTRController@doSetDTRTransactionStatus',
    'as'=> 'do-set-dtr-transaction-status'
]);

//DTR TEMP*****************************************************************
Route::post('get-dtr-temp-list',[
    'uses'=>'EmployeeDTRController@getDTRTempList',
    'as'=> 'get-dtr-temp-list'
]);

Route::post('get-dtr-temp-info',[
    'uses'=>'EmployeeDTRController@getDTRTempInfo',
    'as'=> 'get-dtr-temp-info'
]);

Route::post('do-clear-dtr-temp-transaction',[
    'uses'=>'EmployeeDTRController@doClearDTRTempTransaction',
    'as'=> 'do-clear-dtr-temp-transaction'
]);

Route::post('do-save-dtr-temp-transaction',[
    'uses'=>'EmployeeDTRController@doSaveDTRTempTransaction',
    'as'=> 'do-save-dtr-temp-transaction'
]);

Route::post('do-remove-duplicate-dtr-transaction',[
    'uses'=>'EmployeeDTRController@doRemoveDuplicateDTRTempTransaction',
    'as'=> 'do-remove-duplicate-dtr-transaction'
]);

Route::post('do-save-dtr-temp-transaction-batch',[
    'uses'=>'EmployeeDTRController@doSaveDTRTempTransactionPerBatch',
    'as'=> 'do-save-dtr-temp-transaction-batch'
]);

Route::post('get-dtr-temp-transaction-upload-count',[
    'uses'=>'EmployeeDTRController@getTempDTRTransactionCount',
    'as'=> 'get-dtr-temp-transaction-upload-count'
]);

//EMPLOYEE LOAN TRANSACTION ===================================================
Route::get('admin-employee-loan-transaction',[
    'uses'=>'EmployeeLoanController@showAdminLoanTransaction',
    'as'=> 'admin-employee-loan-transaction'
]);

Route::post('get-employee-loan-transaction-list',[
    'uses'=>'EmployeeLoanController@getEmployeeLoanTransactionList',
    'as'=> 'get-employee-loan-transaction-list'
]);

Route::post('get-employee-loan-transaction-info',[
    'uses'=>'EmployeeLoanController@getEmployeeLoanTransactionInfo',
    'as'=> 'get-employee-loan-transaction-info'
]);

Route::post('do-save-employee-loan-transaction',[
    'uses'=>'EmployeeLoanController@doSaveEmployeeLoanTransaction',
    'as'=> 'do-save-employee-loan-transaction'
]);

Route::post('do-set-loan-transaction-status',[
    'uses'=>'EmployeeLoanController@doSetLoanTransactionStatus',
    'as'=> 'do-set-loan-transaction-status'
]);

Route::post('get-employee-loan-history',[
    'uses'=>'EmployeeLoanController@getEmployeeLoanHistory',
    'as'=> 'get-employee-loan-history'
]);

Route::post('do-save-employee-manual-loan-payment',[
    'uses'=>'EmployeeLoanController@doSaveEmployeeLoanPayment',
    'as'=> 'do-save-employee-manual-loan-payment'
]);

Route::post('get-employee-loan-manual-payment-list',[
    'uses'=>'EmployeeLoanController@getEmployeeLoanManualPaymeList',
    'as'=> 'get-employee-loan-manual-payment-list'
]);

Route::post('get-employee-loan-ledger-payment-list',[
    'uses'=>'EmployeeLoanController@getEmployeeLoanLedgerPaymentList',
    'as'=> 'get-employee-loan-ledger-payment-list'
]);

//EMPLOYEE LOAN TEMP**********************************************************
Route::post('do-clear-loan-temp-transaction',[
    'uses'=>'EmployeeLoanController@doClearLoanTempTransaction',
    'as'=> 'do-clear-loan-temp-transaction'
]);

Route::post('do-save-employee-loan-temp-transaction',[
    'uses'=>'EmployeeLoanController@doSaveEmployeeLoanTempTransaction',
    'as'=> 'do-save-employee-loan-temp-transaction'
]);

Route::post('get-employee-loan-transaction-temp-list',[
    'uses'=>'EmployeeLoanController@getEmployeeLoanTempTransactionList',
    'as'=> 'get-employee-loan-transaction-temp-list'
]);

Route::post('do-remove-duplicate-loan-transaction',[
    'uses'=>'EmployeeLoanController@doRemoveDuplicateLoanTempTransaction',
    'as'=> 'do-remove-duplicate-loan-transaction'
]);

Route::post('get-employee-loan-transaction-temp-info',[
    'uses'=>'EmployeeLoanController@getEmployeeLoanTempTransactionInfo',
    'as'=> 'get-employee-loan-transaction-temp-info'
]);

Route::post('do-upload-save-loan-transaction',[
    'uses'=>'EmployeeLoanController@doSaveUploadFinalEmployeeLoanTransaction',
    'as'=> 'do-upload-save-loan-transaction'
]);

Route::post('do-save-loan-temp-transaction-batch',[
    'uses'=>'EmployeeLoanController@doSaveLoanTempTransactionPerBatch',
    'as'=> 'do-save-loan-temp-transaction-batch'
]);

Route::post('get-loan-temp-transaction-upload-count',[
    'uses'=>'EmployeeLoanController@getTempLoanTransactionCount',
    'as'=> 'get-loan-temp-transaction-upload-count'
]);

Route::get('admin-employee-loan-print-report',[
    'uses'=>'EmployeeLoanController@showAdminEmployeeLoanPaymentHistoryPrintReport',
    'as'=> 'admin-employee-loan-print-report'
]);

//EMPLOYEE ADVANCE TRANSACTION ===================================================
Route::get('admin-employee-advance-transaction',[
    'uses'=>'EmployeeAdvanceController@showAdminAdvanceTransaction',
    'as'=> 'admin-employee-advance-transaction'
]);

Route::post('get-employee-advance-transaction-list',[
    'uses'=>'EmployeeAdvanceController@getEmployeeAdvanceTransactionList',
    'as'=> 'get-employee-advance-transaction-list'
]);

Route::post('get-employee-advance-transaction-info',[
    'uses'=>'EmployeeAdvanceController@getEmployeeAdvanceTransactionInfo',
    'as'=> 'get-employee-advance-transaction-info'
]);

Route::post('do-save-employee-advance-transaction',[
    'uses'=>'EmployeeAdvanceController@doSaveEmployeeAdvanceTransaction',
    'as'=> 'do-save-employee-advance-transaction'
]);

//EMPLOYEE ADVANCE TEMP*********************************************************

Route::post('get-employee-advance-transaction-temp-list',[
    'uses'=>'EmployeeAdvanceController@getEmployeeAdvanceTempTransactionList',
    'as'=> 'get-employee-advance-transaction-temp-list'
]);

Route::post('do-clear-advance-temp-transaction',[
    'uses'=>'EmployeeAdvanceController@doClearAdvanceTempTransaction',
    'as'=> 'do-clear-advance-temp-transaction'
]);

Route::post('do-save-advance-temp-transaction',[
    'uses'=>'EmployeeAdvanceController@doSaveEmployeeAdvanceTempTransaction',
    'as'=> 'do-save-advance-temp-transaction'
]);

Route::post('do-remove-duplicate-advance-transaction',[
    'uses'=>'EmployeeAdvanceController@doRemoveDuplicateAdvanceTempTransaction',
    'as'=> 'do-remove-duplicate-advance-transaction'
]);

Route::post('get-employee-advance-transaction-temp-info',[
    'uses'=>'EmployeeAdvanceController@getEmployeeAdvanceTempTransactionInfo',
    'as'=> 'get-employee-advance-transaction-temp-info'
]);

Route::post('do-upload-save-advance-transaction',[
    'uses'=>'EmployeeAdvanceController@doSaveUploadFinalEmployeeAdvanceTransaction',
    'as'=> 'do-upload-save-advance-transaction'
]);

Route::post('do-save-advance-temp-transaction-batch',[
    'uses'=>'EmployeeAdvanceController@doSaveAdvanceTempTransactionPerBatch',
    'as'=> 'do-save-advance-temp-transaction-batch'
]);

Route::post('get-advance-temp-transaction-upload-count',[
    'uses'=>'EmployeeAdvanceController@getTempAdvanceTransactionCount',
    'as'=> 'get-advance-temp-transaction-upload-count'
]);

//EMPLOYEE INCOME & DEDUCTION TRANSACTION ===================================================
Route::get('admin-employee-income-deduction-transaction',[
    'uses'=>'EmployeeIncomeDeductionController@showAdminIncomeDeductionTransaction',
    'as'=> 'admin-employee-income-deduction-transaction'
]);

Route::post('get-employee-income-deduction-transaction-list',[
    'uses'=>'EmployeeIncomeDeductionController@getEmployeeIncomeDeductionTransactionList',
    'as'=> 'get-employee-income-deduction-transaction-list'
]);

Route::post('get-employee-income-deduction-transaction-info',[
    'uses'=>'EmployeeIncomeDeductionController@getEmployeeIncomeDeductionTransactionInfo',
    'as'=> 'get-employee-income-deduction-transaction-info'
]);

Route::post('do-save-employee-income-deduction-transaction',[
    'uses'=>'EmployeeIncomeDeductionController@doSaveEmployeeIncomeDeductionTransaction',
    'as'=> 'do-save-employee-income-deduction-transaction'
]);

Route::post('do-set-income-deduction-transaction-status',[
    'uses'=>'EmployeeIncomeDeductionController@doSetEmployeeIncomeDeductionTransactionStatus',
    'as'=> 'do-set-income-deduction-transaction-status'
]);

Route::post('get-employee-income-deduction-ledger-payment-list',[
    'uses'=>'EmployeeIncomeDeductionController@getEmployeeIncomeDeductionLedgerPaymentList',
    'as'=> 'get-employee-income-deduction-ledger-payment-list'
]);

//EMPLOYEE INCOME DEDUCITION LEDGER
Route::get('admin-employee-income-deduction-print-report',[
    'uses'=>'EmployeeIncomeDeductionController@showAdminEmployeeIncomeDeductionPaymentHistoryPrintReport',
    'as'=> 'admin-employee-income-deduction-print-report'
]);

//INCOME/DEDUCTION TEMP****************************************
Route::post('get-employee-income-deduction-transaction-temp-info',[
    'uses'=>'EmployeeIncomeDeductionController@getEmployeeIncomeDeductionTempTransactionInfo',
    'as'=> 'get-employee-income-deduction-transaction-temp-info'
]);

Route::post('do-clear-income-deduction-temp-transaction',[
    'uses'=>'EmployeeIncomeDeductionController@doClearIncomeDeductionTempTransaction',
    'as'=> 'do-clear-income-deduction-temp-transaction'
]);

Route::post('do-save-employee-income-deduction-temp-transaction',[
    'uses'=>'EmployeeIncomeDeductionController@doSaveEmployeeIncomeDeductionTempTransaction',
    'as'=> 'do-save-employee-income-deduction-temp-transaction'
]);

Route::post('get-employee-income-deduction-transaction-temp-list',[
    'uses'=>'EmployeeIncomeDeductionController@getEmployeeIncomeDeductionTempTransactionList',
    'as'=> 'get-employee-income-deduction-transaction-temp-list'
]);

Route::post('do-remove-duplicate-income-deduction-transaction',[
    'uses'=>'EmployeeIncomeDeductionController@doRemoveDuplicateIncomeDeductionTempTransaction',
    'as'=> 'do-remove-duplicate-income-deduction-transaction'
]);

Route::post('do-upload-save-income-deduction-transaction',[
    'uses'=>'EmployeeIncomeDeductionController@doSaveUploadFinalEmployeeIncomeDeductionTransaction',
    'as'=> 'do-upload-save-income-deduction-transaction'
]);

Route::post('do-save-income-deduction-temp-transaction-batch',[
    'uses'=>'EmployeeIncomeDeductionController@doSaveIncomeDeductionTempTransactionPerBatch',
    'as'=> 'do-save-income-deduction-temp-transaction-batch'
]);

Route::post('get-income-deduction-temp-transaction-upload-count',[
    'uses'=>'EmployeeIncomeDeductionController@getTempIncomeDeductionTransactionCount',
    'as'=> 'get-income-deduction-temp-transaction-upload-count'
]);

//SETTINGS ===================================================================
Route::get('admin-payroll-setting',[
    'uses'=>'PayrollSettingController@showAdminPayrollSetting',
    'as'=> 'admin-payroll-setting'
]);

Route::post('get-payroll-setting-info',[
    'uses'=>'PayrollSettingController@getPayrollSettingInfo',
    'as'=> 'get-payroll-setting-info'
]);

Route::post('do-save-payroll-setting',[
    'uses'=>'PayrollSettingController@doSavePayrollSetting',
    'as'=> 'do-save-payroll-setting'
]);

Route::post('do-save-payroll-setting-closing-date',[
    'uses'=>'PayrollSettingController@doSavePayrollSettingClosingDate',
    'as'=> 'do-save-payroll-setting-closing-date'
]);

Route::post('do-save-payroll-employee-setting',[
    'uses'=>'PayrollSettingController@doSavePayrollEmployeeSetting',
    'as'=> 'do-save-payroll-employee-setting'
]);

Route::post('do-save-payroll-goverment-premiums-setting',[
    'uses'=>'PayrollSettingController@doSavePayrollGovermentPremiumSetting',
    'as'=> 'do-save-payroll-goverment-premiums-setting'
]);


//REPORTS ===================================================================


//SSS CONTRIBUTION REPORT
Route::get('admin-sss-contribution-report',[
    'uses'=>'ReportController@showAdminSSSContributionReport',
    'as'=> 'admin-sss-contribution-report'
]);

Route::get('admin-sss-contribution-print-report',[
    'uses'=>'ReportController@showAdminSSSContributionPrintReport',
    'as'=> 'admin-sss-contribution-print-report'
]);

Route::post('admin-get-sss-employee-contribution-list',[
    'uses'=>'ReportController@getSSSEmployeeContributionList',
    'as'=> 'admin-get-sss-employee-contribution-list'
]);
 
// HDMF CONTRIBUTION REPORT
Route::get('admin-hdmf-contribution-report',[
    'uses'=>'ReportController@showAdminHDMFContributionReport',
    'as'=> 'admin-hdmf-contribution-report'
]);

Route::get('admin-hdmf-contribution-print-report',[
    'uses'=>'ReportController@showAdminHDMFContributionPrintReport',
    'as'=> 'admin-hdmf-contribution-print-report'
]);

Route::post('admin-get-hdmf-employee-contribution-list',[
    'uses'=>'ReportController@getHDMFEmployeeContributionList',
    'as'=> 'admin-get-hdmf-employee-contribution-list'
]);

// PHIC CONTRIBUTION REPORT
Route::get('admin-phic-contribution-report',[
    'uses'=>'ReportController@showAdminPHICDeductionReport',
    'as'=> 'admin-phic-contribution-report'
]);

Route::get('admin-phic-contribution-print-report',[
    'uses'=>'ReportController@showAdminPHICContributionPrintReport',
    'as'=> 'admin-phic-contribution-print-report'
]);

Route::post('admin-get-phic-employee-contribution-list',[
    'uses'=>'ReportController@getPHICEmployeeContributionList',
    'as'=> 'admin-get-phic-employee-contribution-list'
]);

//PAYSLIP REPORT
Route::get('admin-employee-payslip-report',[
    'uses'=>'ReportController@showAdminEmployeePayslipReport',
    'as'=> 'admin-employee-payslip-report'
]);

Route::post('get-admin-employee-payslip-list',[
    'uses'=>'ReportController@getPayrollTransactionEmployeePayslipList',
    'as'=> 'get-admin-employee-payslip-list'
]);

Route::get('admin-employee-payslip-print-report',[
    'uses'=>'ReportController@showAdminEmployeePayslipPrintReport',
    'as'=> 'admin-employee-payslip-print-report'
]);

//PAYROLL JOURNAL REPORT
Route::get('admin-payroll-journal-report',[
    'uses'=>'ReportController@showAdminPayrollJournalReport',
    'as'=> 'admin-payroll-journal-report'
]);

Route::post('get-payroll-journal-report-list',[
    'uses'=>'ReportController@getPayrollJournalReportList',
    'as'=> 'get-payroll-journal-report-list'
]);

Route::get('admin-payroll-journal-print-report',[
    'uses'=>'ReportController@showAdminPayrollJournalPrintReport',
    'as'=> 'admin-payroll-journal-print-report'
]);


//PAYROLL REGISTER REPORT
Route::get('admin-payroll-register-report',[
    'uses'=>'ReportController@showAdminPayrollRegisterReport',
    'as'=> 'admin-payroll-register-report'
]);

Route::get('admin-payroll-register-print-report',[
    'uses'=>'ReportController@showAdminPayrollRegisterPrintReport',
    'as'=> 'admin-payroll-register-print-report'
]);

Route::post('get-payroll-transaction-report-list',[
    'uses'=>'ReportController@getPayrollRegisterReportList',
    'as'=> 'get-payroll-transaction-report-list'
]);

//PAYROLL RAW DATA REPORT
Route::get('admin-payroll-raw-data-report',[
    'uses'=>'ReportController@showAdminPayrollRawDataReport',
    'as'=> 'admin-payroll-raw-data-report'
]);

Route::post('get-payroll-raw-data-report-list',[
    'uses'=>'ReportController@getPayrollRawDataReportList',
    'as'=> 'get-payroll-raw-data-report-list'
]);


Route::get('admin-employee-loan-report',[
    'uses'=>'ReportController@showAdminEmployeeLoanDeductionReport',
    'as'=> 'admin-employee-loan-report'
]);

Route::post('get-admin-employee-loan-deduction-list-by-filter',[
    'uses'=>'ReportController@getPayrollTransactionEmployeeLoanDeductionListByFilter',
    'as'=> 'get-admin-employee-loan-deduction-list-by-filter'
]);

Route::post('admin-get-employee-loan-list',[
    'uses'=>'ReportController@getEmployeeLoanList',
    'as'=> 'admin-get-employee-loan-list'
]);

//EMPLOYEE OTHER DEDUCTION REPORT
Route::get('admin-employee-other-deduction-report',[
    'uses'=>'ReportController@showAdminEmployeeOtherDeductionReport',
    'as'=> 'admin-employee-other-deduction-report'
]);

Route::post('get-admin-employee-other-deduction-list-by-filter',[
    'uses'=>'ReportController@getPayrollTransactionEmployeeOtherDeductionListByFilter',
    'as'=> 'get-admin-employee-other-deduction-list-by-filter'
]);


Route::get('admin-employee-other-deduction-print-report',[
    'uses'=>'ReportController@showAdminEmployeeOtherDeductionPrintReport',
    'as'=> 'admin-employee-other-deduction-print-report'
]);


//EMPLOYEE INCOME TAXABLE REPORT
Route::get('admin-employee-income-taxable-report',[
    'uses'=>'ReportController@showAdminEmployeeIncomeTaxableReport',
    'as'=> 'admin-employee-income-taxable-report'
]);

Route::post('get-admin-employee-income-taxable-list-by-filter',[
    'uses'=>'ReportController@getPayrollTransactionEmployeeIncomeTaxableListByFilter',
    'as'=> 'get-admin-employee-income-taxable-list-by-filter'
]);

Route::get('admin-employee-income-taxable-print-report',[
    'uses'=>'ReportController@showAdminEmployeeIncomeTaxablePrintReport',
    'as'=> 'admin-employee-income-taxable-print-report'
]);

//EMPLOYEE INCOME NON TAXABLE REPORT
Route::get('admin-employee-income-non-taxable-report',[
    'uses'=>'ReportController@showAdminEmployeeIncomeNonTaxableReport',
    'as'=> 'admin-employee-income-non-taxable-report'
]);

Route::post('get-admin-employee-income-non-taxable-list-by-filter',[
    'uses'=>'ReportController@getPayrollTransactionEmployeeIncomeNonTaxableListByFilter',
    'as'=> 'get-admin-employee-income-non-taxable-list-by-filter'
]);

Route::get('admin-employee-income-non-taxable-print-report',[
    'uses'=>'ReportController@showAdminEmployeeIncomeNonTaxablePrintReport',
    'as'=> 'admin-employee-income-non-taxable-print-report'
]);

// EMPLOYEE DTR REPORT
Route::get('admin-employee-dtr-summary-report',[
    'uses'=>'ReportController@showAdminEmployeeDTRSummaryReport',
    'as'=> 'admin-employee-dtr-summary-report'
]);

Route::post('get-employee-dtr-report-list',[
    'uses'=>'ReportController@getEmployeeDTRReportList',
    'as'=> 'get-employee-dtr-report-list'
]);

Route::get('admin-employee-dtr-summary-print-report',[
    'uses'=>'ReportController@showAdminEmployeeDTRSummaryPrintReport',
    'as'=> 'admin-employee-dtr-summary-print-report'
]);

//EMPLOYEE LOAN LEDGER
Route::get('admin-employee-loan-ledger-print-report',[
    'uses'=>'ReportController@showAdminEmployeeLoanLedgerReport',
    'as'=> 'admin-employee-loan-ledger-print-report'
]);

//EMPLOYEE ADVANCE LEDGER
Route::get('admin-employee-advance-print-report',[
    'uses'=>'ReportController@showAdminEmployeeAdvanceReport',
    'as'=> 'admin-employee-advance-print-report'
]);

//BRANCH ===================================================
Route::post('get-branch-search-list',[
    'uses'=>'BranchController@getBranchSearchList',
    'as'=> 'get-branch-search-list'
]);

//DIVISION ===================================================
Route::post('get-division-search-list',[
    'uses'=>'DivisionController@getDivisionSearchList',
    'as'=> 'get-division-search-list'
]);

Route::post('post-division-info',[
    'uses'=>'DivisionController@postDivisionInfo',
    'as'=> 'post-division-info'
]);

//DEPARTMENT ===================================================
Route::post('get-department-search-list',[
    'uses'=>'DepartmentController@getDepartmentSearchList',
    'as'=> 'get-department-search-list'
]);

Route::post('post-department-info',[
    'uses'=>'DepartmentController@postDepartmentInfo',
    'as'=> 'post-department-info'
]);

//SECTION ===================================================
Route::post('get-section-search-list',[
    'uses'=>'SectionController@getSectionSearchList',
    'as'=> 'get-section-search-list'
]);

Route::post('post-section-info',[
    'uses'=>'SectionController@postSectionInfo',
    'as'=> 'post-section-info'
]);

//JOB TYPE ===================================================
Route::post('get-jobtype-search-list',[
    'uses'=>'JobTypeController@getJobTypeSearchList',
    'as'=> 'get-jobtype-search-list'
]);

Route::post('post-jobtype-info',[
    'uses'=>'JobTypeController@postJobTypeInfo',
    'as'=> 'post-jobtype-info'
]);

//PAYROLL TRANSACTION ===================================================
Route::get('admin-payroll-transaction',[
    'uses'=>'PayrollTransactionController@showAdminPayrollTransaction',
    'as'=> 'admin-payroll-transaction'
]);

Route::post('get-payroll-transaction-list',[
    'uses'=>'PayrollTransactionController@getPayrollTransactionList',
    'as'=> 'get-payroll-transaction-list'
]);

Route::post('get-payroll-transaction-search-list',[
    'uses'=>'PayrollTransactionController@getPayrollTransactionSearchList',
    'as'=> 'get-payroll-transaction-search-list'
]);

Route::post('get-payroll-transaction-info',[
    'uses'=>'PayrollTransactionController@getPayrollTransactionInfo',
    'as'=> 'get-payroll-transaction-info'
]);

Route::post('get-payroll-transaction-info-transno',[
    'uses'=>'PayrollTransactionController@getPayrollTransactionInfoTransNo',
    'as'=> 'get-payroll-transaction-info-transno'
]);

Route::post('get-payroll-transaction-info-by-period',[
    'uses'=>'PayrollTransactionController@getPayrollTransactionInfoByPeriod',
    'as'=> 'get-payroll-transaction-info-by-period'
]);

Route::post('get-payroll-transaction-employee-list',[
    'uses'=>'PayrollTransactionController@getPayrollTransactionEmployeeList',
    'as'=> 'get-payroll-transaction-employee-list'
]);

Route::post('get-payroll-transaction-employee-list-by-period',[
    'uses'=>'PayrollTransactionController@getPayrollTransactionEmployeeListByPeriod',
    'as'=> 'get-payroll-transaction-employee-list-by-period'
]);

Route::post('get-payroll-transaction-details',[
    'uses'=>'PayrollTransactionController@getPayrollTransactionDetails',
    'as'=> 'get-payroll-transaction-details'
]);

Route::post('get-payroll-trans-details',[
    'uses'=>'PayrollTransactionController@getPayrollTransDetails',
    'as'=> 'get-payroll-trans-details'
]);

Route::post('do-generate-payroll',[
    'uses'=>'PayrollTransactionController@doGeneratePayroll',
    'as'=> 'do-generate-payroll'
]);

Route::post('do-approve-generated-payroll',[
    'uses'=>'PayrollTransactionController@doApproveGeneratedPayroll',
    'as'=> 'do-approve-generated-payroll'
]);

Route::post('do-cancel-generated-payroll',[
    'uses'=>'PayrollTransactionController@doCancelGeneratedPayroll',
    'as'=> 'do-cancel-generated-payroll'
]);

Route::get('admin-payroll-payslip',[
    'uses'=>'PayrollTransactionController@showAdminPayrollPaySlip',
    'as'=> 'admin-payroll-payslip'
]);

Route::post('get-employee-payroll-earning-history',[
    'uses'=>'PayrollTransactionController@getEmployeePayrollEarningHistory',
    'as'=> 'get-employee-payroll-earning-history'
]);

//THIRTEEN MONTH TRANSACTION ===================================================
Route::get('admin-13th-month-transaction',[
    'uses'=>'ThirteenMonthTransactionController@showAdmin13thMonthTransaction',
    'as'=> 'admin-13th-month-transaction'
]);

Route::post('get-13th-month-transaction-list',[
    'uses'=>'ThirteenMonthTransactionController@get13thMonthTransactionList',
    'as'=> 'get-13th-month-transaction-list'
]);

Route::post('get-13th-month-transaction-search-list',[
    'uses'=>'ThirteenMonthTransactionController@getThirteenMonthTransactionSearchList',
    'as'=> 'get-13th-month-transaction-search-list'
]);

Route::post('get-13th-month-transaction-info',[
    'uses'=>'ThirteenMonthTransactionController@get13thMonthTransactionInfo',
    'as'=> 'get-13th-month-transaction-info'
]);

Route::post('get-13th-month-transaction-info-transno',[
    'uses'=>'ThirteenMonthTransactionController@get13thMonthTransactionInfoTransNo',
    'as'=> 'get-13th-month-transaction-info-transno'
]);

Route::post('get-13th-month-transaction-employee-list',[
    'uses'=>'ThirteenMonthTransactionController@get13thMonthTransactionEmployeeList',
    'as'=> 'get-13th-month-transaction-employee-list'
]);

Route::post('do-generate-13th-month',[
    'uses'=>'ThirteenMonthTransactionController@doGenerate13thMonthTransaction',
    'as'=> 'do-generate-13th-month'
]);

Route::post('do-approve-generated-13th-month',[
    'uses'=>'ThirteenMonthTransactionController@doApprove13thMonthTransaction',
    'as'=> 'do-approve-generated-13th-month'
]);

Route::post('do-cancel-generated-13th-month',[
    'uses'=>'ThirteenMonthTransactionController@doCancel13thMonthTransaction',
    'as'=> 'do-cancel-generated-13th-month'
]);

// GENERATE EXCEL RECORD=========================================================

//EMPLOYEE EXCEL
Route::post('get-excel-employee-list',[
    'uses'=>'GenerateExcelController@getExcelEmployeeList',
    'as'=> 'get-excel-employee-list'
]);

//LOAN TYPE EXCEL
Route::post('get-excel-loan-type-list',[
    'uses'=>'GenerateExcelController@getExcelLoanTypeList',
    'as'=> 'get-excel-loan-type-list'
]);

//OT RATE EXCEL
Route::post('get-excel-ot-rate-list',[
    'uses'=>'GenerateExcelController@getExcelOTRatesList',
    'as'=> 'get-excel-ot-rate-list'
]);

//INCOME & DEDUCTION TYPE EXCEL 
Route::post('get-excel-income-deduction-type-list',[
    'uses'=>'GenerateExcelController@getExcelIncomeDeductionTypeList',
    'as'=> 'get-excel-income-deduction-type-list'
]);

//PAYROLL PERIOD EXCEL
Route::post('get-excel-payroll-period-list',[
    'uses'=>'GenerateExcelController@getExcelPayrollPeriodList',
    'as'=> 'get-excel-payroll-period-list'
]);

//SSS BRACKET EXCEL
Route::post('get-excel-sss-bracket-list',[
    'uses'=>'GenerateExcelController@getExcelSSSBracketList',
    'as'=> 'get-excel-sss-bracket-list'
]);

//HDMF BRACKET EXCEL
Route::post('get-excel-hdmf-bracket-list',[
    'uses'=>'GenerateExcelController@getExcelHDMFBracketList',
    'as'=> 'get-excel-hdmf-bracket-list'
]);

//PHIC BRACKET EXCEL
Route::post('get-excel-phic-bracket-list',[
    'uses'=>'GenerateExcelController@getExcelPHICBracketList',
    'as'=> 'get-excel-phic-bracket-list'
]);

//ANNUAL INCOME BRACKET EXCEL
Route::post('get-excel-annual-income-tax-list',[
    'uses'=>'GenerateExcelController@getExcelAnnualIncomeTaxList',
    'as'=> 'get-excel-annual-income-tax-list'
]);

//WITH HOLDING TAX EXCEL
Route::post('get-excel-with-holding-tax-list',[
    'uses'=>'GenerateExcelController@getExcelWithHoldingTaxList',
    'as'=> 'get-excel-with-holding-tax-list'
]);

// PAYROLL JOURNAL EXCEL
Route::post('get-excel-payroll-journal-list',[
    'uses'=>'GenerateExcelController@getExcelPayrollJournalList',
    'as'=> 'get-excel-payroll-journal-list'
]);

// PAYROLL REGISTER EXCEL
Route::post('get-excel-payroll-register-list',[
    'uses'=>'GenerateExcelController@getExcelPayrollRegisterList',
    'as'=> 'get-excel-payroll-register-list'
]);

// PAYROLL RAW DATA EXCEL
Route::post('get-excel-payroll-raw-data-list',[
    'uses'=>'GenerateExcelController@getExcelPayrollRawDataList',
    'as'=> 'get-excel-payroll-raw-data-list'
]);

// EMPLOYEE DTR EXCEL
Route::post('get-excel-employee-dtr-list',[
    'uses'=>'GenerateExcelController@getExcelEmployeeDTRList',
    'as'=> 'get-excel-employee-dtr-list'
]);

// SSS CONTRIBUTION EXCEL
Route::post('get-excel-sss-contribution-list',[
    'uses'=>'GenerateExcelController@getExcelSSSContributionList',
    'as'=> 'get-excel-sss-contribution-list'
]);

// HDMF CONTRIBUTION EXCEL
Route::post('get-excel-hdmf-contribution-list',[
    'uses'=>'GenerateExcelController@getExcelHDMFContributionList',
    'as'=> 'get-excel-hdmf-contribution-list'
]);

// PHIC CONTRIBUTION EXCEL
Route::post('get-excel-phic-contribution-list',[
    'uses'=>'GenerateExcelController@getExcelPHICContributionList',
    'as'=> 'get-excel-phic-contribution-list'
]);

// EMPLOYEE LOAN DEDUCTION EXCEL
Route::post('get-excel-employee-loan-deduction-list',[
    'uses'=>'GenerateExcelController@getExcelEmployeeLoanDeductionList',
    'as'=> 'get-excel-employee-loan-deduction-list'
]);

// EMPLOYEE OTHER DEDUCTION EXCEL
Route::post('get-excel-employee-other-deduction-list',[
    'uses'=>'GenerateExcelController@getExcelEmployeeOtherDeductionList',
    'as'=> 'get-excel-employee-other-deduction-list'
]);

// EMPLOYEE OTHER EARNING TAXABLE EXCEL
Route::post('get-excel-employee-other-earning-taxable-list',[
    'uses'=>'GenerateExcelController@getExcelEmployeeOtherEarningTaxableList',
    'as'=> 'get-excel-employee-other-earning-taxable-list'
]);

// EMPLOYEE OTHER EARNING NON TAXABLE EXCEL
Route::post('get-excel-employee-other-earning-non-taxable-list',[
    'uses'=>'GenerateExcelController@getExcelEmployeeOtherEarningNonTaxableList',
    'as'=> 'get-excel-employee-other-earning-non-taxable-list'
]);

Route::post('sync-employees', [SyncEmployeeController::class, 'start']);