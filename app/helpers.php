<?php

use Illuminate\Support\Facades\Auth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!function_exists('pre')) {
    function pre($string = '')
    {
        echo '<pre>' . print_r($string, TRUE) . '</pre>';
    }
}
if (!function_exists('checkAccess')) {
    function checkAccess($HP=NULL)
    {
        if(Auth::check())
            if($_SERVER['REQUEST_URI'] == '/')
                echo redirect('/home');
            else
                return 1;
        else
            return redirect('/');
    }
}
if (!function_exists('send_mail')) {
    function send_mail($to='lukkonop@icloud.com',$thema='miniErp',$BodyEmail)
    {
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = false;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'minierp17@gmail.com';
            $mail->Password   = 'Tomojmail1!';
            $mail->SMTPSecure = "tls";
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('from@example.com', 'miniERP - Praca Magisterska');
            $mail->addAddress($to, 'Joe User');     // Add a recipient
            $mail->addAttachment('uploads/invoices/Invoice.pdf');

            // Content
            $mail->Subject = $thema;
            $mail->Body    = $BodyEmail;
            $mail->isHTML(true);                                  // Set email format to HTML

            if($mail->send())
            {
                echo '1';
            }
        } catch (Exception $e) {
            echo 'error';
        }
    }
}
if (!function_exists('log_event')) {
    function log_event($module,$text)
    {
        $insert_data = array(
            'event'=>$text,
            'module'=>$module,
            'c_date'=>date('Y-m-d H:i:s'),
            'c_by'=>((Auth::id())?Auth::id():'0'),
            'status'=>'1',
        );
        DB::table('note')->insert($insert_data);
    }
}
