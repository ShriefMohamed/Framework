<?php

namespace Framework\models;


use Framework\lib\AbstractModel;

class UsersModel extends AbstractModel
{
    public $user_id;
    public $first_name;
    public $sur_name;
    public $username;
    public $email;
    public $password;
    public $phone;
    public $role_id;
    public $created;
    public $last_update;
    public $last_seen;
    public $image;
    public $avatar;
    public $gender;
    public $birth_date;
    public $location_geo;
    public $status;
    public $activation_string;
    public $forgotten_password_key;

    protected static $tableName = 'users';
    protected static $primaryKey = 'user_id';
    protected static $tableSchema = array(
        'first_name' => self::DATA_TYPE_STR,
        'sur_name' => self::DATA_TYPE_STR,
        'username' => self::DATA_TYPE_STR,
        'email' => self::DATA_TYPE_STR,
        'password' => self::DATA_TYPE_STR,
        'phone' => self::DATA_TYPE_INT,
        'role_id' => self::DATA_TYPE_INT,
        'created' => self::DATA_TYPE_STR,
        'last_update' => self::DATA_TYPE_STR,
        'last_seen' => self::DATA_TYPE_STR,
        'image' => self::DATA_TYPE_STR,
        'avatar' => self::DATA_TYPE_STR,
        'gender' => self::DATA_TYPE_STR,
        'birth_date' => self::DATA_TYPE_STR,
        'location_geo' => self::DATA_TYPE_STR,
        'status' => self::DATA_TYPE_STR,
        'activation_string' => self::DATA_TYPE_STR,
        'forgotten_password_key' => self::DATA_TYPE_STR
    );

    public static function Authenticate($username, $password)
    {
        $sql = "SELECT users.user_id, users.first_name, users.sur_name, users.username, users.email, 
                    users.phone, users.image, users.avatar, users.gender, users.birth_date, 
                    users.location_geo, users.status,
                    user_role.role_id, user_role.role
                FROM " . static::$tableName . "
                LEFT JOIN user_role ON users.role_id = user_role.role_id 
                WHERE (users.username = :username OR users.email = :email OR users.phone = :phone) 
                    AND users.password = :password";
        return parent::GetSQL($sql, array(
                'username' => array(parent::DATA_TYPE_STR, $username),
                'email' => array(parent::DATA_TYPE_STR, $username),
                'phone' => array(parent::DATA_TYPE_INT, $username),
                'password' => array(parent::DATA_TYPE_STR, $password)
            ),
            2);
    }

    public static function GetUserRating($user_id)
    {
        $sql = "SELECT users.user_id, users.first_name, users.sur_name, users.username, users.email, 
                    users.phone, users.image, users.avatar, users.gender, users.birth_date, 
                    users.location_geo, users.status, users.created, users.last_seen, users.last_update, 
                    user_role.role_id, user_role.role, 
                    user_address.address_id, user_address.country_id, user_address.state, user_address.street, user_address.zip,  
                    countries.country_name, countries.country_code,
                      (SELECT COUNT(advertisement_id) FROM advertisements
                        WHERE advertisements.user_id = :user_id
                      ) AS total_ads,
                      (SELECT COUNT(advertisement_id) FROM advertisements 
                        WHERE advertisements.user_id = :user_id
                        AND advertisements.verification_status = 'ended'
                        AND advertisements.success = 1
                      ) AS successful_ads,
                      (SELECT COUNT(advertisement_id) FROM advertisements 
                        WHERE advertisements.user_id = :user_id
                        AND advertisements.verification_status = 'ended'
                        AND advertisements.success = 0
                      ) AS unsuccessful_ads,
                      (SELECT COUNT(bid_id) FROM bids WHERE bids.user_id = :user_id) AS bids,
                      (SELECT COUNT(comment_id) FROM comments WHERE comments.user_id = :user_id) AS comments,
                      (SELECT COUNT(reply_id) FROM comment_reply WHERE comment_reply.user_id = :user_id) AS replies
                FROM " . static::$tableName . "
                LEFT JOIN user_role ON users.role_id = user_role.role_id
                LEFT JOIN user_address ON users.user_id = user_address.user_id
                LEFT JOIN countries ON user_address.country_id = countries.country_id
                WHERE users.user_id = :user_id ";
        return parent::GetSQL($sql, array(
            'user_id' => array(parent::DATA_TYPE_INT, $user_id)
        ), 2);
    }

    public static function GetUserAccount($user_id)
    {
        $sql = "SELECT users.user_id, users.first_name, users.sur_name, users.username, users.email, 
                    users.phone, users.image, users.avatar, users.gender, users.birth_date, 
                    users.location_geo, users.status, users.created, users.last_seen, users.last_update, 
                    user_role.role_id, user_role.role, 
                    user_address.country_id, user_address.state, user_address.street, user_address.zip,  
                    countries.country_name, countries.country_code,
                    accounts.account_id, accounts.account_number, accounts.balance, accounts.open_date, 
                    accounts.last_update, accounts.updated_by       
                FROM " . static::$tableName . "
                LEFT JOIN user_role ON users.role_id = user_role.role_id
                LEFT JOIN user_address ON users.user_id = user_address.user_id
                LEFT JOIN countries ON user_address.country_id = countries.country_id
                LEFT JOIN accounts ON users.user_id = accounts.user_id
                WHERE users.user_id = :user_id ";
        return parent::GetSQL($sql, array(
            'user_id' => array(parent::DATA_TYPE_INT, $user_id)
        ), 2);
    }
}