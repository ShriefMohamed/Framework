<?php

namespace Framework\controllers;

use Framework\lib\AbstractController;
use Framework\lib\Cipher;
use Framework\lib\FilterInput;
use Framework\lib\Helper;
use Framework\lib\Redirect;
use Framework\lib\Request;
use Framework\lib\Session;
use Framework\models\CountriesModel;
use Framework\models\User_addressModel;
use Framework\models\User_roleModel;
use Framework\models\UsersModel;

class LoginController extends AbstractController
{
    /**
     * Method  DefaultAction
     *
     *
     * if the user typed "www.x.com/login/" then he will be in the default action page,
     * so redirect him to login page.
     *
     * @author Shrief Mohamed
     */
    public function DefaultAction()
    {
        Redirect::To('login/login');
    }

    /**
     * Method  LoginAction
     *
     *
     * take the email/username/phone and password and log him in or display error.
     *
     * @author Shrief Mohamed
     */
    public function LoginAction()
    {
        // make sure that there's a post request. if not then display the page normally.
        if (Request::Check('post', 'login')) {
            // html form will send username, we should check first to decide if this value is
            // email or phone or username and filter it with the appropriate filtering method.
            if ((FilterInput::FilterEmail(Request::Post('username')))) {
                $username = FilterInput::FilterEmail(
                    Request::Post('username', false, true)
                );
            } elseif (FilterInput::FilterInt(Request::Post('username'))) {
                $username = FilterInput::FilterInt(
                    Request::Post('username', false, true)
                );
            } else {
                $username = FilterInput::FilterString(
                    Request::Post('username', false, true)
                );
            }
            // get the password from the user.
            $password = Request::Post('password', false, true);
            // hash the password to compare hashes.
            $password = Helper::HashPassword($password);

            // send the username and password to usersmodel to check if there's a user with these credentials.
            $authentication = UsersModel::Authenticate($username, $password);

            if ($authentication !== false) {
                // if authentication was successful then:
                // check the status of the user account to check if it's verified, if not then display an error
                // message depending on the status of the user's account.
                if ($authentication->status == 'verified') {
                    // if the account is verified:
                    // set a session with the user details.
                    Session::Set('logged_in', $authentication);
                    // log this login into the log file.

                    $this->logger->info('New login : user_id-> ' . $authentication->user_id);

                    // depending on the logged in user's role redirect him to the appropriate page.
                    if ($authentication->role == 'admin') {
                        /*@TODO
                        /* create privileges for every admin and get these privileges in login and store it in a session
                        /* $privileges = get_priv($auth->id)
                        /* Session::Set('admin_privileges', $privileges);
                        */

                        $this->logger->info('Admin Login : user_id-> ' . $authentication->user_id);
                        Redirect::To('admin');
                    } elseif ($authentication->role == 'user') {
                        $this->logger->info('User Login : user_id-> ' . $authentication->user_id);
                        Redirect::ReturnURL();
                    } else {
                        $this->logger->info('Unknown User Role: user_id-> ' . $authentication->user_id . ' Role-> ' . $authentication->role);
                    }
                    // if login failed then display error message and log the attempt to the log file.
                } elseif ($authentication->status == 'unverified') {
                    parent::SetFeedback('FEEDBACK_ACCOUNT_UNVERIFIED');
                    // log the failure to the log file.
                    $this->logger->info('login failure with username: ' . $username . ' reason: account unverified.');
                } elseif ($authentication->status == 'suspended') {
                    parent::SetFeedback('FEEDBACK_ACCOUNT_SUSPENDED');
                    // log the failure to the log file.
                    $this->logger->info('login failure with username: ' . $username . ' reason: account suspended.');
                } elseif ($authentication->status == 'deleted') {
                    parent::SetFeedback('FEEDBACK_ACCOUNT_DELETED');
                    // log the failure to the log file.
                    $this->logger->info('login failure with username: ' . $username . ' reason: account deleted.');
                } else {
                    parent::SetFeedback('FEEDBACK_ACCOUNT_UNKNOWN_STATUS');
                    // log the failure to the log file.
                    $this->logger->info('login failure with username: ' . $username . ' reason: account\'s status is unknown.');
                }
            } else {
                // display login failed message.
                parent::SetFeedback('FEEDBACK_LOGIN_FAILED');
                // log the failure to the log file.
                // should I include the password in the log or only the username??
                $this->logger->info('login failure with username: ' . $username);
            }
        }

        $this->_template->SetViews(['head', 'view', 'footer'])
            ->Render();
    }

    /**
     * Method  RegisterAction
     *
     *
     * get all info from the user via post request, filter these info and then check if everything is ok then insert it
     * to the database and create a new user.
     *
     * @author Shrief Mohamed
     */
    public function RegisterAction()
    {
        if (Request::Post('register')) {
            $user = new UsersModel;
            $user->first_name = FilterInput::FilterString(
                Request::Post('first_name', false, true)
            );
            $user->sur_name = FilterInput::FilterString(
                Request::Post('sur_name', false, true)
            );
            $user->username = FilterInput::FilterString(
                Request::Post('username', false, true)
            );
            $usernameCheck = UsersModel::Count(
                " WHERE username = '$user->username'"
            );
            $confirmEmail = FilterInput::FilterEmail(
                Request::Post('confirm_email', false, true)
            );
            $user->email = FilterInput::FilterEmail(
                Request::Post('email', false, true)
            );
            $emailCheck = UsersModel::Count(" WHERE email = '$user->email'");
            $password = Request::Post('password', false, false);
            $confirmPassword = Request::Post('confirm_password', false, false);
            $user->password = Helper::HashPassword($password);
            $user->phone = Request::Post('phone', false, true);
            $role = User_roleModel::GetAll(" WHERE role = 'user'", true);
            $user->role_id = $role->role_id;
            $user->created = SERVER_DATE_TIME;
            $user->image = 'default' . DS . 'user.png';
            $user->avatar = 'default' . DS . 'user.png';
            $user->gender = FilterInput::FilterGender(Request::Post('gender', false, false));
            $user->birth_date = FilterInput::FilterDateTime(Request::Post('birth_date', false, true), 'date');
            $age = Helper::DateDiff($user->birth_date, 'date');
            $ageYear = ($age) ? $age->y : null;
            if ($this->_visitor->lon && $this->_visitor->lat) {
                $user->location_geo = $this->_visitor->lat . ',' . $this->_visitor->lon;
            }
            $user->status = 'unverified';

            $validation = $this->Validate(
                $user->first_name, $user->sur_name, $user->username, $usernameCheck, $user->email,
                $confirmEmail, $emailCheck, $password, $confirmPassword, $user->phone,
                $ageYear);

            if ($validation) {
                $user->activation_string = $this->ActivationString($user->email);
                if ($user->Save()) {

                    // Create the user address depending on the geo location.
                    $user_address = new User_addressModel;
                    $user_address->user_id = $user->user_id;
                    if ($this->_visitor->countryCode) {
                        $country = CountriesModel::GetAll(" WHERE country_code = '$this->_visitor->countryCode' ", true);
                        if ($country) {
                            $user_address->country_id = $country->country_id;
                        } else {
                            $new_country = new CountriesModel;
                            $new_country->country_name = $this->_visitor->country;
                            $new_country->country_code = $this->_visitor->countryCode;
                            $new_country->status = 1;

                            if ($new_country->Save()) {
                                $user_address->country_id = $new_country->country_id;
                            }
                        }
                    } else {
                        $unknown = CountriesModel::GetAll(" WHERE country_code = 'unknown' ", true);
                        if ($unknown) {
                            $user_address->country_id = $unknown->country_id;
                        } else {
                            $unknown_country = new CountriesModel;
                            $unknown_country ->country_name = 'Unknown';
                            $unknown_country ->country_code = 'unknown';
                            $unknown_country ->status = 1;

                            if ($unknown_country->Save()) {
                                $user_address->country_id = $unknown_country->country_id;
                            }
                        }
                    }


                    /*@TODO
                     * after creating an email class send activation Email. with this url.
                     * */
                    echo HOST_NAME . 'login/activate/' . $user->activation_string;

//                    if ($mail) {
                    parent::SetFeedback('FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED');
//                    } else {
//                        parent::SetFeedback('FEEDBACK_VERIFICATION_MAIL_SENDING_ERROR');
//                    }
                    $this->logger->info('New registration: user_id-> ' . $user->user_id);
                } else {
                    parent::SetFeedback('FEEDBACK_ACCOUNT_CREATION_FAILED');
                    $this->logger->info('registration failed');
                }
            }
        }

        $this->_template->SetViews(['head', 'view', 'footer'])
            ->Render();
    }

    private function ActivationString($email)
    {
        $random = substr(md5(mt_rand()), 0, 32);
        $cipher = new Cipher;
        $activation = $cipher->Encrypt($random . $email);;
        return $activation;
    }

    public function ActivateAction()
    {
        if ($this->_params[0] && $this->_params[0] !== '') {
            $enc_string = $this->_params[0];
            $cipher = new Cipher;
            $activation_string = $cipher->Decrypt($enc_string);
            $email = substr($activation_string, 32);
            $user = UsersModel::GetAll(" WHERE email = '$email'", true);

            if ($user !== false) {
                if ($user->activation_string == $enc_string) {
                    if (Helper::DateDiff($user->created, 'date_time')->d == 0) {
                        $user->status = 'verified';
                        $user->activation_string = 'n/a';

                        if ($user->Save()) {
                            parent::SetFeedback('FEEDBACK_ACCOUNT_ACTIVATION_SUCCESSFUL');
                            $this->logger->info('account was activated. user: user_id-> ' . $user->user_id);
                        } else {
                            parent::SetFeedback('FEEDBACK_ACCOUNT_ACTIVATION_FAILED');
                            $this->logger->info('account activation failed. user: user_id-> ' . $user->user_id);
                        }
                    } else {
                        $user->activation_string = $this->ActivationString($email);
                        /*@TODO
                         * after creating an email class send activation Email. with this url.
                         * */
                        echo HOST_NAME . 'login/activate/' . $user->activation_string;
                        if ($user->Save()) {
                            $this->logger->info('Activation code expired and new code was set and sent to user: user_id-> ' . $user->user_id);
                            parent::SetFeedback('FEEDBACK_ACCOUNT_ACTIVATION_EXPIRED');
                        }
                    }
                } else {
                    parent::SetFeedback('FEEDBACK_ACCOUNT_ACTIVATION_FAILED');
                }
            } else {
                parent::SetFeedback('FEEDBACK_ACCOUNT_ACTIVATION_FAILED');
            }
        }
    }

    /*@TODO
     * Create the forget password method to enable users to reset passwords
     * */
    public function Forgot_passwordAction()
    {

    }
}