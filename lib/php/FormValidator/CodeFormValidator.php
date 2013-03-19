<?php
require_once dirname(__FILE__) . '/../Validator.php';

class FormValidator_CodeFormValidator extends Validator
{

    public $labels = array(
        'code' => 'コード',
    );

    public function __construct()
    {
        parent::__construct();
     
    
        $this->addRule('code', 'required', $this->labels['code']);
 
    }


}

