<?php
    include('class/Appointment.php');
    $object = new Appointment;

    if(isset($_POST["action"])){

        if($_POST["action"]=='check_login'){
            if(isset($_SESSION['patient_id'])){
                echo 'dashboard.php';
            }else{
                echo 'login.php';
            }
        }
    
        if($_POST['action']=='patient_register'){
            $error = '';
            $success = '';
            
            $data = array(
                ':pateint_email_address' =>$_POST["patient_email_adderess"]
            );

            $object->query = "
            SELECT * FROM patient_table
            WHERE pateint_email_address = :patient_email_adderess
            ";

            $object->execute($data);

            if($object->row_count() >0){
                $error = '<div class="alert alert-danger">Email Address Already Exists</div>';
            }
            else{
                $patient_verification_code = md5(uniqid());
                $data = array(
                    ':pateint_email_address'            => $object->clean_input($_POST["patient_email_address"]),
                    ':pateint_password'                 =>$_POST["patient_password"],
                    ':patient_first_name'               =>$object->clean_input($_POST["patient_first_name"]),
                    ':patient_last_name'                =>$object->clean_input($_POST["patient_last_name"]),
                    ':patient_date_of_birth'            =>$object->clean_input($_POST["patient_date_of_birth"]),,
                    ':patien_gender'                    =>$object->clean_input($_POST["patient_gender"]),,
                    ':patient_address'                  =>$object->clean_input($_POST["patient_address"]),,
                    ':patient_phone_no'                 =>$object->clean_input($_POST["patient_phone_no"]),,
                    ':patient_maritial_status'          =>$object->clean_input($_POST["patient_maritial_status"]),,
                    ':patient_added_on'                 =>$object->now,
                    ':patient_verification_code'        =>$patient_verification_code,
                    ':email_verify'                     =>'No'

                );
                $object->query = "
			    INSERT INTO patient_table 
			    (patient_email_address, patient_password, patient_first_name, patient_last_name, patient_date_of_birth, patient_gender, patient_address, patient_phone_no, patient_maritial_status, patient_added_on, patient_verification_code, email_verify) 
			    VALUES (:patient_email_address, :patient_password, :patient_first_name, :patient_last_name, :patient_date_of_birth, :patient_gender, :patient_address, :patient_phone_no, :patient_maritial_status, :patient_added_on, :patient_verification_code, :email_verify)
			    ";

                $object->execute($data);

                require 'class/class.phpmailer.php';
                $mail  = new PHPMailer;
                $mail ->IsSMTP();
                $mail ->Port = '80';
                $mail ->SMTPAuth = true;
                $mail ->Username = 'Dimuth C Bandara';
                $mail ->Password = 'phpabc0147';
                $mail ->SMTPSecure ='';
                $mail ->From = 'dimuthchathu101@gmail.com';
                $mail ->FromName ='Dimuth C Bandara';
                $mail ->AddAddress($_POST["patient_email_address"]);

                $message_body = '
			    <p>For verify your email address, Please click on this <a href="'.$object->base_url.'verify.php?code='.$patient_verification_code.'"><b>link</b></a>.</p>
			    <p>Sincerely,</p>
			    <p>DimuthC.info</p>
			    ';

                $mail->Body = $message_body;

                if($mail->Send())
                {
                    $success = '<div class="alert alert-success">Please Check Your Email for email Verification</div>';
                }
                else
                {
                    $error = '<div class="alert alert-danger">' . $mail->ErrorInfo . '</div>';
                }
        }

        $output = array(
            'error' =>$error,
            'success' =>$success
        );
        echo json_encode($output);
    }
    
    if($_POST['action']=='patient_login'){
        $error = '';
        $data  = array(
            ':patient_email_address'
        );
        $object->query = "
        SELECT * FROM patient_table
        WHERE patient_email_address = :patient_email_address
        ";
        $object->execute($data);
        if($object->row_Count()>0){
            $result = $object->statement_result();

            foreach($result as $row){
                if($row["email_verify"] =='Yes')
                {
                    if($row["patient_password"] == $_POST["patient_password"]){
                        $_SESSION['patient_id'] = $row["patient_id"];
                        $_SESSION['patient_name'] = $row['patient_first_name'].' '.$row['patient_last_name'] ;
                    }
                    else{
                        $error ='<div class="alert alert-danger">Wrong Password</div>';
                        
                    }
                   $error ='<div class="alert alert-danger">Please Verify Your Email Address</div>';
                }
            }
        }
        else{
            $error ='<div class="alert alert-danger"Wrong Email Address</div>';
        }
        $output = array(
            'error' =>$erorr
        );

        echo json_encode($output);
    }

    if($_POST['action'] == 'fetch_schedule')
    {
        $output = array();
        $order_column = array('doctor_table.doctor_name',
        'doctor_table.doctor_degree','doctor_table.doctor_expert_in',
        'doctor_schedule_table.doctor_schedule_date',
        'doctor_schedule_table.doctor_schedule_day',
        'doctor_schedule_table.doctor_schedule_start_time'
        );
        
        $main_query = "
        SLELECT * FROM doctor_schedule_table
        INNER JOIN doctor_table
        ON doctor_table.doctor_id = doctor_schedule_table.doctor_id
        ";

        $search_query = '
		WHERE doctor_schedule_table.doctor_schedule_date >= "'.date('Y-m-d').'" 
		AND doctor_schedule_table.doctor_schedule_status = "Active" 
		AND doctor_table.doctor_status = "Active" 
		';

        if(isset($_POST["search"]["value"]))
        {
            $search_query = 'AND(doctor_table.doctor_name LIKE '
        }
    }

    }

?>