<?php
require_once dirname(__FILE__) . '/../Validator.php';

class FormValidator_CommentFormValidator extends Validator
{

    public $labels = array(
        'comment' => 'コメント',
    );

    public function __construct()
    {
        parent::__construct();
     
    
        $this->addRule('comment', 'required', $this->labels['comment']);
 
    }


}

