<?php

class Appointment{

    public $base_url = "Beezzserver Address";
    public $connect;
    public $query;
    public $statement;
    public $now;

    public function __construct()
    {
        $this->connect = new PDO("mysql:host=localhost;dbname=dimuthcbandarachannel","studentdimuthcbandarachannel","phpabc0147");
        date_default_timezone_set('Asia/Colombo');

        session_start();

        $this->now = date("Y-m-d H:i:s",  STRTOTIME(date('h:i:sa')));
    }

    function execute($data = null){
        $this->statement = $this->connect->prepare($this->query);
        if($data)
        {
            $this->statement->execute($data);
        }
        else{
            $this->statement->execute();
        }
    }
    
    function row_Count()
    {
        return $this->statement->rowCount();
    }
    function statement_result()
    {
        return $this->statement->fetchAll();
    }
    function get_result()
    {
        return $this->connect->query($this->query,PDO::FETCH_ASSOC);
    }
    function is_login(){
        if(isset($_SESSION['admin_id'])){
            return true;
        }

        return false;
    }
    function is_master_user()
    {
        if(isset($_SESSION['user_type'])){
            if($_SESSION["user_type"]=='Master'){
                return true;
            }
            return false;
        }
        return false;
    }
    function clean_input($string){
        $string = trim($string);
        $string = stripslashes($string);
        $string = htmlspecialchars($string);
        return $string;
    }

    function Generate_appontment_no()
    {
        $this->query = "
		SELECT MAX(appointment_number) as appointment_number FROM appointment_table 
		";
        $result = $this->get_result();

        $appointment_number  = 0;

        foreach($result as $row){
            $appointment_number = $row["appointment_number"];
        }

        if($appointment_number>0){
            return $appointment_number+1;
        }
        else{
            return '1000';
        }
    }

    function get_total_today_appointment()
    {
        $this->query = "
		SELECT * FROM appointment_table 
		INNER JOIN doctor_schedule_table 
		ON doctor_schedule_table.doctor_schedule_id = appointment_table.doctor_schedule_id 
		WHERE doctor_schedule_date = CURDATE() 
		";
		$this->execute();
		return $this->row_count();
    }

    function get_total_yesterday_appointmeent()
    {
        $this->query = "
		SELECT * FROM appointment_table 
		INNER JOIN doctor_schedule_table 
		ON doctor_schedule_table.doctor_schedule_id = appointment_table.doctor_schedule_id 
		WHERE doctor_schedule_date = CURDATE() - 1
		";
		$this->execute();
		return $this->row_count();
    }

    function get_total_seven_day_appointment()
    {
        $this->query = "
		SELECT * FROM appointment_table 
		INNER JOIN doctor_schedule_table 
		ON doctor_schedule_table.doctor_schedule_id = appointment_table.doctor_schedule_id 
		WHERE doctor_schedule_date >= DATE(NOW()) - INTERVAL 7 DAY
		";
		$this->execute();
		return $this->row_count();
    }
    function get_total_appointment()
    {
        $this->query = "
		SELECT * FROM appointment_table 
		";
		$this->execute();
		return $this->row_count();
        
    }
    function get_total_patient(){
        $this->query = "
		SELECT * FROM patient_table 
		";
		$this->execute();
		return $this->row_count();
    }
    
}