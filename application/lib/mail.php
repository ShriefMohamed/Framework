<?php


namespace Framework\lib;

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require APP_PATH . 'vendor/autoload.php';


class Mail
{
    public $connected = true;

    public $logger;

    private $smtp_server;
    private $smtp_backup_server;
    private $smtp_username;
    private $smtp_password;
    private $smtp_encryption;
    private $smtp_port;

    public $from_email;
    public $from_name;
    public $to_email;
    public $to_name;
    public $reply_to_email;
    public $reply_to_name;
    public $is_cc = false;
    public $cc;
    public $is_bcc = false;
    public $bcc;
    public $is_attachment;
    public $attachment;
    public $subject;
    public $message;
    public $alt_message;

    public function __construct()
    {
        // Get the logger from AbstractController.
        $controller = new AbstractController();
        $this->logger = $controller->logger;

        // Set smtp configurations
        $this->smtp_server = SMTP_SERVER;
        $this->smtp_backup_server = SMTP_BACKUP_SERVER;
        $this->smtp_username = SMTP_USERNAME;
        $this->smtp_password = SMTP_PASSWORD;
        $this->smtp_encryption = SMTP_ENCRYPTION;
        $this->smtp_port = SMTP_PORT;

        // Test smtp server, if can't connect then destroy the object.

        // Create a new SMTP instance
        $smtp = new SMTP;
        // Enable connection-level debug output
        $smtp->do_debug = SMTP::DEBUG_OFF;

//        // Connect to an SMTP server
//        if (!$smtp->connect($this->smtp_server, 25)) {
//            if (!$smtp->connect($this->smtp_backup_server, 25)) {
//                $this->connected = false;
//                BackendLogger::GetInstance()->SetLogInfo(__CLASS__, 'error', 'Connecting to SMTP servers failed: ' . $smtp->getError()['error'])
//                    ->Log();
//                exit();
//            }
//        }
//
//        if (!$smtp->hello(gethostname())) {
//            $this->connected = false;
//            BackendLogger::GetInstance()->SetLogInfo(__CLASS__, 'error', 'SMTP EHLO failed: ' . $smtp->getError()['error'])
//                ->Log();
//            exit();
//        }
//
//        // Get the list of ESMTP services the server offers
//        $e = $smtp->getServerExtList();
//
//        // If server supports authentication, try to connect.
//        if (is_array($e) && array_key_exists('AUTH', $e)) {
//            if (!$smtp->authenticate($this->smtp_username, $this->smtp_password)) {
//                $this->connected = false;
//                BackendLogger::GetInstance()->SetLogInfo(__CLASS__, 'error', 'SMTP Authentication failed: ' . $smtp->getError()['error'])
//                    ->Log();
//                exit();
//            }
//        }

        //Whatever happened, close the connection.
        $smtp->quit(true);
    }

    public function Send()
    {
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            // Specify main and backup SMTP servers
            $mail->Host = "$this->smtp_server" . ";" . "$this->smtp_backup_server";
            // Enable SMTP authentication
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtp_username;
            $mail->Password = $this->smtp_password;
            // Enable TLS encryption, `ssl` also accepted
            $mail->SMTPSecure = $this->smtp_encryption;
            // TCP port to connect to
            $mail->Port = $this->smtp_port;

            // Recipients
            // Set 'From' email & name
            if ($this->from_email && $this->from_name) {
                $mail->setFrom($this->from_email, $this->from_name);
            } elseif ($this->from_email && !$this->from_name) {
                $mail->setFrom($this->from_email);
            }

            // Set recipient's name & email
            if ($this->to_email && $this->to_name) {
                $mail->addAddress($this->to_email, $this->to_name);
            } elseif ($this->to_email && !$this->to_name) {
                $mail->addAddress($this->to_email);
            } else {
                throw new \Exception("Can't send email, No recipient.");
            }

            // Set reply to email & email
            if ($this->reply_to_email && $this->reply_to_name) {
                $mail->addReplyTo($this->reply_to_email, $this->reply_to_name);
            } elseif ($this->reply_to_email && !$this->reply_to_name) {
                $mail->addReplyTo($this->reply_to_email);
            }

            // If CC, then set CC
            if ($this->is_cc) {
                $mail->addCC($this->cc);
            }

            // If BCC, then set BCC
            if ($this->is_bcc) {
                $mail->addBCC($this->bcc);
            }

            // If attachment, then set attachment's path
            if ($this->is_attachment) {
                $mail->addAttachment($this->attachment);
            }

            //Content
            // Set email format to HTML
            $mail->isHTML(true);

            // If subject, Set subject
            if ($this->subject) {
                $mail->Subject = $this->subject;
            }

            // If message, Set body
            if ($this->message) {
                $mail->Body = $this->message;
            } else {
                throw new \Exception("Can't send email, No message.");
            }

            // If alt message, Set AltBody
            if ($this->alt_message) {
                $mail->AltBody = $this->alt_message;
            }

            // If sent, log email info then return true.
            $mail->send();
            $logMessage = "Email sent successfully. ";
            $logMessage .= "From: ".$this->from_name." <".$this->from_email."> ";
            $logMessage .= "To: ".$this->to_name." <".$this->to_email."> ";
            if ($this->is_cc) {
                $logMessage .= "CC: <".$this->cc."> ";
            }
            if ($this->is_bcc) {
                $logMessage .= "BCC: <".$this->bcc."> ";
            }
            $with_attachment = ($this->is_attachment) ? "Yes" : "No";
            $logMessage .= "Includes attachment: ".$with_attachment;

            $this->logger->info($logMessage);
            return true;
        } catch (\Exception $e) {
            $message = 'Message could not be sent. Log Error: '.$e->getMessage().', Mailer Error: ' . $mail->ErrorInfo;
            $this->logger->error($message);
            return false;
        }
    }
}