<?php

namespace OsdAurox;

use OsdAurox\AppConfig;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    public ?PHPMailer $mail = null;

    public function __construct() {
        $mail = new PHPMailer(DEBUG);
        $mail->isSMTP();
        $mail->Host = AppConfig::get('mail_host');
        $mail->SMTPAuth = true;
        $mail->Username = AppConfig::get('mail_user');
        $mail->Password = AppConfig::get('mail_pass');
        $mail->SMTPSecure = AppConfig::get('mail_ssl') ? 'ssl' : 'tls';
        $mail->Port = AppConfig::get('mail_port');
        $mail->setFrom(AppConfig::get('mail_from'), AppConfig::get('appName'));
        $mail->isHTML();
        $mail->CharSet = 'UTF-8';

        $this->mail = $mail;
    }
    public static function send($to, $subject, $content, $cc = false) {
        $mail = new Mailer();
        $mail->mail->addAddress($to);
        if ($cc) {
            $mail->mail->addCC($cc);
        }
        $mail->mail->Subject = $subject;
        $mail->mail->Body = $content;

        return $mail->mail->send();
    }


}