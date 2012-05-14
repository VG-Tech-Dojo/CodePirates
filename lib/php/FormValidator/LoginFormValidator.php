<?php
require_once dirname(__FILE__) . '/UserFormValidator.php';

class FormValidator_LoginFormValidator extends FormValidator_UserFormValidator
{
    public $labels = array(
        'user_name' => 'ユーザー名',
        'password' => 'パスワード'
    );

    public function __construct()
    {
        parent::__construct();

        $this->addRule('user_name', 'required', $this->labels['user_name'])
            ->addRule('user_name', 'userName', $this->labels['user_name'])
            ->addRule('password', 'required', $this->labels['password'])
            ->addRule('password', 'password', $this->labels['password']);
    }
}

