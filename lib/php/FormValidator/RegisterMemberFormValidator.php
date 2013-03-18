<?php
require_once dirname(__FILE__) . '/UserFormValidator.php';

class FormValidator_RegisterMemberFormValidator extends FormValidator_UserFormValidator
{
    public $labels = array(
        'user_name' => 'ユーザー名',
        'password' => 'パスワード',
    );

    public function __construct()
    {
        parent::__construct();

        $this->addRule('user_name', 'required', $this->labels['user_name'])
            ->addRule('user_name', 'userName', $this->labels['user_name'])
            ->addRule('password', 'required', $this->labels['password'])
            ->addRule('password', 'password', $this->labels['password']);
    }

    public function date($field, $val, $label)
    {
        $pattern = array(
            'pattern' => '/\A[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\z/',
            'pattern_name' => 'yyyy-mm-dd'
        );
        if ($this->regExp($field, $val, $label, $pattern)) {
            $date = explode('-', $val);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
            if (!checkdate($month, $day, $year)) {
                $this->_errors[$field] = '無効な日付が入力されました';
                return false;
            };
            return true;
        }
        return false;
    }
}

