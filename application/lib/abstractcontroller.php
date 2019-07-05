<?php

namespace Framework\lib;

use Framework\models\FeedbackModel;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;


//Load Composer's autoloader
require APPLICATION_DIR . 'vendor/autoload.php';

/**
 * Class AbstractController
 *
 * @package Framework\lib
 *
 * All the controllers classes extend this abstract controller which will contain the main methods needed by
 * the other controllers to operate.
 *
 * @author Shrief Mohamed
 */
class AbstractController
{
    /**
     * @var _controller
     * the name of the controller that got instantiated.
     */
    protected $_controller;
    /**
     * @var _action
     * the name of the action the user requested in the url.
     */
    protected $_action;
    /**
     * @var _params
     * the parameters passed in the url to be used later in the controller.
     */
    protected $_params;
    /**
     * @var _template
     *
     * Holds the new template object so we can access that object later at any controller and render some views
     */
    protected $_template;
    /**
     * @var visitor
     *
     * holds the visitor geo info to be able to access it anywhere in the controllers
     */
    protected $_visitor;

    protected $_language;

    public $logger;

    public function __construct()
    {
        // Set Log
        $dateFormat = "Y-m-d H:i:s a";
        $output = "[%datetime%] %channel%.%level_name%: %message% %context%*\n";
        $formatter = new LineFormatter($output, $dateFormat);

        $stream = new StreamHandler(LOG_FILE, Logger::DEBUG);
        $stream->setFormatter($formatter);

        $this->logger = new Logger('logs.Framework');
        $this->logger->pushHandler($stream);
        $this->logger->pushHandler(new FirePHPHandler());

        $this->SetLanguage();
        $this->LogVisit();
    }

    /**
     * Method  NotFoundAction
     *
     * When we encounter an unknown class name (controller) or unknown action name we take the user to this method
     * to display a 404 not found.
     *
     * @author Shrief Mohamed
     */
    public function NotFoundAction()
    {
        $this->_template->SetViews(['head', 'header', 'view', 'footer'])
            ->Render();
    }

    /**
     * Method  SetContActParam
     *
     * @param $controllerName
     * @param $action
     * @param $params
     *
     * inside of the front controller class where we first got the controller name and action name and params,
     * we use this method to:
     *  set these variables (the name of the controller and action and parameters)
     *  in case of we needed to use them here or inside any controller class,
     *  and of course inside the controller class we don't have access
     *  to the front controller class so this is the perfect way to parse these values from there to here, and
     *  to every controller we create.
     * after setting the controller and action and params, we call the method "initializeTemplate" in order to
     * create a new object from the class template, and place that object in the class property "_template" so it
     * would be accessible at any controller.
     *
     * @author Shrief Mohamed
     */
    public function SetContActParam($controllerName, $action, $params)
    {
        $this->_controller = $controllerName;
        $this->_action = $action;
        $this->_params = $params;

        $this->InitializeTemplate();
    }

    /**
     * Method  InitializeTemplate
     *
     *
     * Creates a new object from the class template, and place that object in the class property "_template" so it
     * would be accessible at any controller.
     *
     * @author Shrief Mohamed
     */
    public function InitializeTemplate()
    {
        $this->_template = new Template($this->_controller, $this->_action);
        $this->_template->SetLanguage($this->_language);
    }

    public function SetFeedback($feedback, $get = true)
    {
        $language = $this->_language;
        $language = trim($language, '"');
        if ($get !== true) {
            $feedback = $feedback;
        } else {
            $feedbackOb = FeedbackModel::GetTitles($language, $feedback);
            if ($feedbackOb) {
                $feedback = $feedbackOb->$language;
            }
        }

        $key = 'feedback';
        if ($feedback !== null) {
            if (Session::CookieExists($key) && Session::GetCookie($key) !== false) {
                $feedbacks = Session::GetCookie($key);
            } else {
                $feedbacks = array();
            }

            $feedbacks[] = $feedback;
            Session::SetCookie($key, $feedbacks, 1);
        }
    }

    public function Validate($first_name, $sur_name, $username, $usernameCheck, $email,
                             $confirmEmail, $emailCheck, $password, $confirmPassword, $phone, $age)
    {
        $error = 0;

        if ($first_name == null) {
            $this->SetFeedback('FEEDBACK_FIRSTNAME_FIELD_EMPTY');
            $error = 1;
        }
        if ($sur_name == null) {
            $this->SetFeedback('FEEDBACK_SURNAME_FIELD_EMPTY');
            $error = 1;
        }
        if ($username == null) {
            $this->SetFeedback('FEEDBACK_USERNAME_FIELD_EMPTY');
            $error = 1;
        }
        if (strlen($username) > 64 || strlen($username) < 6) {
            $this->SetFeedback('FEEDBACK_USERNAME_TOO_SHORT_OR_TOO_LONG');
            $error = 1;
        }
        if ($usernameCheck >= 1) {
            $this->SetFeedback('FEEDBACK_USERNAME_ALREADY_TAKEN');
            $error = 1;
        }
        if ($email == null) {
            $this->SetFeedback('FEEDBACK_EMAIL_FIELD_EMPTY');
            $error = 1;
        }
        if ($email !== $confirmEmail) {
            $this->SetFeedback('FEEDBACK_EMAIL_REPEAT_WRONG');
            $error = 1;
        }
        if ($emailCheck >= 1) {
            $this->SetFeedback('FEEDBACK_EMAIL_ALREADY_TAKEN');
            $error = 1;
        }
        if ($password == null) {
            $this->SetFeedback('FEEDBACK_PASSWORD_FIELD_EMPTY');
            $error = 1;
        }
        if ($password !== $confirmPassword) {
            $this->SetFeedback('FEEDBACK_PASSWORD_REPEAT_WRONG');
            $error = 1;
        }
        if (strlen($password) < 10) {
            $this->SetFeedback('FEEDBACK_PASSWORD_TOO_SHORT');
            $error = 1;
        }
        if (!preg_match("#[0-9]+#", $password)) {
            $this->SetFeedback('FEEDBACK_PASSWORD_INCLUDE_NUMBER');
            $error = 1;
        }
        if (!preg_match("#[a-zA-Z]+#", $password)) {
            $this->SetFeedback('FEEDBACK_PASSWORD_INCLUDE_LETTER');
            $error = 1;
        }
        if ($phone == null) {
            $this->SetFeedback('FEEDBACK_PHONE_NUMBER_ERROR');
            $error = 1;
        }
        if ($age < 16) {
            $this->SetFeedback('FEEDBACK_AGE_UNDER_16');
            $error = 1;
        }

        return $error !== 0 ? false : true;
    }

    private function SetLanguage()
    {
        if (Session::CookieExists('language')) {
            $this->_language = Session::GetCookie('language', true);
        } else {
            $this->_language = 'en';
            Session::SetCookie('language', 'en', 24);
        }
    }

    private function LogVisit()
    {
        $visit = new Geolocation;
        $visit->Initialize();
        $this->_visitor = $visit;

        if (!Session::CookieExists('visit')) {
            $logMessage = "visitor location details:: ";
            if ($this->_visitor->status !== 'success') {
                $logMessage .= "failed to get visitor location. ip: " . $this->_visitor->query . ' & message: ' . $this->_visitor->message;
            } else {
                foreach ($this->_visitor as $key => $value) {
                    $logMessage .= $key . ': ' . $value . ', ';
                }
            }
            $this->logger->info($logMessage);
            Session::SetCookie('visit', time(), 0.05);
        }


    }
}