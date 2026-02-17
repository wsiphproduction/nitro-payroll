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
            $connectionInfo = array( "Database"=>$payrollDatabase, "UID"=>$payrollUsername, "PWD"=>$payrollPassword);
            $conn_payroll = sqlsrv_connect( $serverName, $connectionInfo);

            $hrisHost = config('database.connections.hris.host');
            $hrisDatabase = config('database.connections.hris.database');
            $hrisUsername = config('database.connections.hris.username');
            $hrisPassword = config('database.connections.hris.password');

            $connectionInfosp = array( "Database"=>$hrisDatabase, "UID"=>$hrisUsername, "PWD"=>$hrisPassword);
            $conn_hris = sqlsrv_connect( $hrisHost, $connectionInfosp);


            //upload new employees from hris to payroll
            $dd = sqlsrv_query($conn_hris, "select * from users");
            while($d=sqlsrv_fetch_array($dd)){
            $qry="";
                $check = sqlsrv_fetch_array(sqlsrv_query($conn_payroll,"select * from users where hris_ref_id='".$d['id']."'"));
                
                if(!isset($check)){
                $if="(";
                $iv="(";
                $cc = sqlsrv_query($conn_payroll,"sp_columns users");
                while($c = sqlsrv_fetch_array($cc)){

                $col =  $c['COLUMN_NAME']; 
                $not_included = ['id','hdmf_ee','hdmf_er','company_branch_site_id','section_id','hris_ref_id'];
            
                if (!in_array($col, $not_included)) {
                    $if.=$c['COLUMN_NAME'].",";

                    if($c['TYPE_NAME'] == 'date'){
                    if(isset($d[$col])){
                    $iv.="'".$d[$col]->format('Y-m-d')."',";
                    }
                    else{
                    $iv.="'',";
                    }
                
                    }
                    elseif($c['TYPE_NAME'] == 'datetime'){
                    if(isset($d[$col])){
                    $iv.="'".$d[$col]->format('Y-m-d H:i:s')."',";
                    }
                    else{
                    $iv.="'',";
                    }
                    
                    }
                    else{
                    $iv.="'".$d[$col]."',";
                    }
                }
                
                }
                $iv .= "'".$d['id']."',";
                $if.="hris_ref_id".",";

                $iv = rtrim($iv,",").")";
                $if = rtrim($if,",").")";
                $qry = "insert into users ".$if." values ".$iv;
                
            $exec = sqlsrv_query($conn_payroll,$qry);
            }
            
            }


            //update employment details of all employees
            $q = sqlsrv_query($conn_hris,"select u.id as uid, u.employee_number,
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

            while($r = sqlsrv_fetch_array($q)){     
                $upd = sqlsrv_query($conn_payroll,"update users set department_id='".$r['department_idd']."',
                job_title_id='".$r['position_idd']."',company_branch_id=3,section_id='".$r['section_idd']."',
                company_branch_site_id=2,hdmf_ee=200,hdmf_er=200,employee_number='".$r['employee_number']."',salary_type='".$r['saltype']."',shortid='".$r['employee_number']."' where hris_ref_id='".$r['uid']."'");
            
            }
        } catch (\Throwable $e) {
            \Log::error('SyncEmployeesJob failed: '.$e->getMessage());
            throw $e; // let queue mark as failed
        }
    }
}
