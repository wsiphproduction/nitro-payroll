<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncEmployeesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 0;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $payrollHost = config('database.connections.payroll.host');
            $payrollDatabase = config('database.connections.payroll.database');
            $payrollUsername = config('database.connections.payroll.username');
            $payrollPassword = config('database.connections.payroll.password');

            $serverName = $payrollHost;
            $connectionInfo = array("Database" => $payrollDatabase, "UID" => $payrollUsername, "PWD" => $payrollPassword);
            $conn_payroll = sqlsrv_connect($serverName, $connectionInfo);

            $hrisHost = config('database.connections.hris.host');
            $hrisDatabase = config('database.connections.hris.database');
            $hrisUsername = config('database.connections.hris.username');
            $hrisPassword = config('database.connections.hris.password');

            $connectionInfosp = array("Database" => $hrisDatabase, "UID" => $hrisUsername, "PWD" => $hrisPassword);
            $conn_hris = sqlsrv_connect($hrisHost, $connectionInfosp);


            //upload new employees from hris to payroll
            $dd = sqlsrv_query($conn_hris, "select * from users");
            while ($d = sqlsrv_fetch_array($dd)) {
                $qry = "";
                $check = sqlsrv_fetch_array(
                    sqlsrv_query($conn_payroll, "select id, hris_ref_id from users where hris_ref_id='" . $d['id'] . "'")
                );

                $dept = sqlsrv_fetch_array(sqlsrv_query($conn_hris, "
                    SELECT TOP 1 department_id
                    FROM employments
                    WHERE user_id = '".$d['id']."'
                    ORDER BY id DESC
                "));

                $department_id = $dept ? $dept['department_id'] : 0;

                if (!$check) {
                    $if = "(";
                    $iv = "(";
                    $cc = sqlsrv_query($conn_payroll, "sp_columns users");
                    while ($c = sqlsrv_fetch_array($cc)) {

                        $col =  $c['COLUMN_NAME'];
                        $not_included = ['id', 'hdmf_ee', 'hdmf_er', 'company_branch_site_id', 'section_id', 'hris_ref_id'];

                        if (!in_array($col, $not_included)) {
                            $if .= $c['COLUMN_NAME'] . ",";

                            if ($c['TYPE_NAME'] == 'date') {
                                if (isset($d[$col])) {
                                    $iv .= "'" . $d[$col]->format('Y-m-d') . "',";
                                } else {
                                    $iv .= "'',";
                                }
                            } elseif ($c['TYPE_NAME'] == 'datetime') {
                                if (isset($d[$col])) {
                                    $iv .= "'" . $d[$col]->format('Y-m-d H:i:s') . "',";
                                } else {
                                    $iv .= "'',";
                                }
                            } else {
                                $iv .= "'" . $d[$col] . "',";
                            }
                        }
                    }
                    $iv .= "'" . $d['id'] . "',";
                    $if .= "hris_ref_id" . ",";

                    $iv = rtrim($iv, ",") . ")";
                    $if = rtrim($if, ",") . ")";
                    $qry = "insert into users " . $if . " values " . $iv;
                    $exec = sqlsrv_query($conn_payroll, $qry);

                    if ($exec === false) {
                        logger()->error('Insert failed: ' . print_r(sqlsrv_errors(), true));
                    }
                } else {
                    $qryUpdate = "
                        UPDATE users SET
                            status = '".$d['status']."',
                            bank = '".$d['bank']."',
                            department_id = '".$department_id."'
                        WHERE hris_ref_id = '".$d['id']."'
                    ";

                    $execUpdate = sqlsrv_query($conn_payroll, $qryUpdate);

                    if($execUpdate === false){
                        logger()->error('Update failed: '.print_r(sqlsrv_errors(), true));
                    }
                }
            }

            //update employment details of all employees
            $q = sqlsrv_query($conn_hris, "select u.id as uid, u.employee_number, u.status, u.bank,
            u.first_name, u.last_name, u.middle_name, u.gender, u.birthdate, u.marital_status, u.email,
            di.id as division_id,di.name as division_name,
            de.id as department_idd, de.department_name as department_name,
            loc.id as location_id,loc.location as location_name,
            sec.id as section_idd,sec.section_name as section_name,
            pos.id as position_idd,pos.name as position_name,
            pos_cla.id as position_classification_id,pos_cla.name as position_name,
            c.id as company_id,c.name as company_name,u.salary_type as saltype,
            u.* from users u
            outer apply (select top 1 * from employments where employments.user_id=u.id order by employments.id desc) as e
            left join departments de on de.id=e.department_id
            left join divisions di on di.id=de.division_id
            left join locations as loc on loc.id=e.location_id
            left join sections as sec on sec.id=e.section_id
            left join positions as pos on pos.id=e.position_id
            left join positions_classification as pos_cla on pos_cla.id=e.position_classification_id
            left join companies as c on c.id=e.position_id  
            order by u.id desc
            ");

            if ($q === false) {
                throw new \RuntimeException('Employee details fetch failed: ' . print_r(sqlsrv_errors(), true));
            }

            logger()->info('Employee details update started (birthdate excluded).');

            $processedEmployees = 0;
            $unmatchedEmployees = 0;

            while ($r = sqlsrv_fetch_array($q)) {
                $updateQuery = "
                    UPDATE users SET
                        first_name = ?,
                        last_name = ?,
                        middle_name = ?,
                        gender = ?,
                        marital_status = ?,
                        email = ?,
                        division_id = ?,
                        department_id = ?,
                        status = ?,
                        bank = ?,
                        job_title_id = ?,
                        company_branch_id = 3,
                        section_id = COALESCE(?, section_id),
                        company_branch_site_id = 2,
                        hdmf_ee = 200,
                        hdmf_er = 200,
                        employee_number = ?,
                        salary_type = ?,
                        shortid = COALESCE(?, shortid),
                    WHERE hris_ref_id = ?
                ";

                $updateParams = [
                    $r['first_name'],
                    $r['last_name'],
                    $r['middle_name'],
                    $r['gender'],
                    $r['marital_status'],
                    $r['email'],
                    $r['division_id'],
                    $r['department_idd'],
                    $r['status'],
                    $r['bank'],
                    $r['position_idd'],
                    $r['section_idd'],
                    $r['employee_number'],
                    $r['saltype'],
                    $r['employee_number'],
                    $r['uid'],
                ];

                $upd = sqlsrv_query($conn_payroll, $updateQuery, $updateParams);

                if ($upd === false) {
                    throw new \RuntimeException(
                        'Employee details update failed for HRIS user ' . $r['uid'] . ': ' . print_r(sqlsrv_errors(), true)
                    );
                }

                $processedEmployees++;

                if (sqlsrv_rows_affected($upd) === 0) {
                    $unmatchedEmployees++;

                    logger()->warning('Employee details update matched no payroll user.', [
                        'hris_ref_id' => $r['uid'],
                    ]);
                }

                sqlsrv_free_stmt($upd);
            }

            if ($r === false) {
                throw new \RuntimeException('Employee details row fetch failed: ' . print_r(sqlsrv_errors(), true));
            }

            logger()->info('Employee details update completed.', [
                'processed_employees' => $processedEmployees,
                'unmatched_employees' => $unmatchedEmployees,
            ]);

            // Sync Sections from HRIS to Payroll

            // Clear payroll sections first
            sqlsrv_query($conn_payroll, "TRUNCATE TABLE payroll_section");

            sqlsrv_query($conn_payroll, "SET IDENTITY_INSERT payroll_section ON");

            $sections = sqlsrv_query($conn_hris, "
                SELECT
                    id,
                    department_id,
                    section_name
                FROM sections
            ");

            while ($section = sqlsrv_fetch_array($sections, SQLSRV_FETCH_ASSOC)) {

                $insertQry = "
                    INSERT INTO payroll_section
                    (
                        ID,
                        DepartmentID,
                        Section,
                        RefID
                    )
                    VALUES
                    (
                        '".$section['id']."',
                        '".$section['department_id']."',
                        '".str_replace("'", "''", $section['section_name'])."',
                        '".$section['id']."'
                    )
                ";

                $exec = sqlsrv_query($conn_payroll, $insertQry);

                if ($exec === false) {
                    logger()->error('Section Insert Failed: '.print_r(sqlsrv_errors(), true));
                }
            }

            sqlsrv_query($conn_payroll, "SET IDENTITY_INSERT payroll_section OFF");


            // Clear payroll sections first
            sqlsrv_query($conn_payroll, "TRUNCATE TABLE payroll_department");

            sqlsrv_query($conn_payroll, "SET IDENTITY_INSERT payroll_department ON");

            $departments = sqlsrv_query($conn_hris, "
                SELECT
                    id,
                    department_name
                FROM departments
            ");

            while ($department = sqlsrv_fetch_array($departments, SQLSRV_FETCH_ASSOC)) {

                $insertQry = "
                    INSERT INTO payroll_department
                    (
                        ID,
                        DivisionID,
                        Department,
                        RefID
                    )
                    VALUES
                    (
                        '".$department['id']."',
                        1,
                        '".str_replace("'", "''", $department['department_name'])."',
                        '".$department['id']."'
                    )
                ";

                $exec = sqlsrv_query($conn_payroll, $insertQry);

                if ($exec === false) {
                    logger()->error('Department Insert Failed: '.print_r(sqlsrv_errors(), true));
                }
            }

            sqlsrv_query($conn_payroll, "SET IDENTITY_INSERT payroll_department OFF");


            // Clear payroll positions first
            sqlsrv_query($conn_payroll, "TRUNCATE TABLE positions");

            sqlsrv_query($conn_payroll, "SET IDENTITY_INSERT positions ON");

            $positions = sqlsrv_query($conn_hris, "
                SELECT
                    id,
                    name,
                    createdAt,
                    updatedAt
                FROM positions
            ");

            while ($pos = sqlsrv_fetch_array($positions, SQLSRV_FETCH_ASSOC)) {

                $insertQry = "
                    INSERT INTO positions
                    (
                        id,
                        name,
                        createdAt
                    )
                    VALUES
                    (
                        '".$pos['id']."',
                        '".$pos['name']."',
                        '".$pos['createdAt']->format('Y-m-d H:i:s')."'
                    )
                ";

                $exec = sqlsrv_query($conn_payroll, $insertQry);

                if ($exec === false) {
                    logger()->error('positions Insert Failed: '.print_r(sqlsrv_errors(), true));
                }
            }

            sqlsrv_query($conn_payroll, "SET IDENTITY_INSERT positions OFF");


        } catch (\Throwable $e) {
            \Log::error('SyncEmployeesJob failed: ' . $e->getMessage());
            throw $e; // let queue mark as failed
        }
    }
}
