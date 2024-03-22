<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    class Mail{
        function SendMail($data){
            //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);

            try {
                //Server settings
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                $mail->isSMTP();                         //Send using SMTP
                $mail->isHTML(true);                   
                $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'datnt337@gmail.com';    //SMTP username
                $mail->Password   = 'wujtmqcivipiludj';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port       = 587;                                  //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                $mail->SMTPSecure = "tls";
                $mail->CharSet = "UTF-8";
                //Recipients
                $mail->setFrom('datnt337@gmail.com', 'PTIT SHOP');
                $mail->addAddress($data['email'], $data['fullname']);     //Add a recipient
                
                //$mail->addAddress('ellen@example.com');               //Name is optional
                //$mail->addReplyTo('info@example.com', 'Information');
                //$mail->addCC('n20dcat011@student.ptithcm.edu.vn');
                //$mail->addBCC('n20dcat011@student.ptithcm.edu.vn');
            
                //Attachments
                //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
            
                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = $data['subject'];
                $mail->Body    = $data['body'];
                // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            
                $mail->send();
                return "sent";
            } catch (Exception $e) {
                //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                return "failed";
            }
        }
    }
?>