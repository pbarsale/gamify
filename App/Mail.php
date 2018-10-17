<?php
namespace App;
/**
 * This example shows sending a message using PHP's mail() function.
 */
include_once dirname(__DIR__) . '/vendor/autoload.php';

class Mail{
	public static function send($to,$subject,$text,$html){

		try{
			//Create a new PHPMailer instance
			$mail = new \PHPMailer(true);
			//$mail->SMTPDebug = 2;                                 // Enable verbose debug output
			$mail->isSMTP();                                      // Set mailer to use SMTP
		    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
		    $mail->SMTPAuth = true;                               // Enable SMTP authentication
		    $mail->Username = 'buffalo.science.museum@gmail.com';                 // SMTP username
		    $mail->Password = 'Museum1234';                           // SMTP password
		    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		    $mail->Port = 587;                                    // TCP port to connect to

		    //Recipients
		    $mail->setFrom('buffalo.science.museum@gmail.com', 'Science Museum');
		    $mail->addAddress($to, 'Pratibha');     // Add a recipient
		    //$mail->addAddress('ellen@example.com');               // Name is optional
		    //$mail->addReplyTo('info@example.com', 'Information');
		    //$mail->addCC('cc@example.com');
		    //$mail->addBCC('bcc@example.com');

		    //Attachments
		    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

		    //Content
		    $mail->isHTML(true);                                  // Set email format to HTML
		    $mail->Subject = $subject;
		    $mail->Body    = $html;
		    $mail->AltBody = $text;

		    $mail->send();
		    //echo 'Message has been sent';
		} catch (Exception $e) {
		    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
		}
	}
}
