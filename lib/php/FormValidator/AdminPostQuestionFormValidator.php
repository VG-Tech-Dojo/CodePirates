<?php
require_once dirname(__FILE__) . '/UserFormValidator.php';

class FormValidator_AdminPostQuestionFormValidator extends FormValidator_UserFormValidator
{
    public $labels = array(
        'title' => 'タイトル',
        'content' => '本文'
    );

    public function __construct()
    {
        parent::__construct();

        $this->addRule('title', 'required', $this->labels['title'])
            ->addRule('content', 'required', $this->labels['content']);
    }
}

