<?php
require_once dirname(__FILE__) . '/../Validator.php';

class FormValidator_UserFormValidator extends Validator
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ユーザー名フィルタ
     *
     * @param string $field フィールド名
     * @param string $label メッセージ用ラベル名
     * @param string $opts オプション
     * @return boolean バリデーションでエラーがない場合true, ある場合false
     */
    public function userName($field, $val, $label)
    {
        $pattern = array(
            'pattern' => '/\A[0-9a-zA-Z]{1,15}\z/',
            'pattern_name' => '半角英数'
        );
        return $this->regExp($field, $val, $label, $pattern);
    }

    /**
     * パスワードフィルタ
     *
     * @param string $field フィールド名
     * @param string $label メッセージ用ラベル名
     * @param string $opts オプション
     * @return boolean バリデーションでエラーがない場合true, ある場合false
     */
    public function password($field, $val, $label)
    {
        $pattern = array(
            'pattern' => '/\A[0-9a-zA-Z&%$#!?_]{6,64}\z/',
            'pattern_name' => '半角英数&%$#!?_6文字から64文字'
        );
        return $this->regExp($field, $val, $label, $pattern);
    }
    /**
     * メールアドレスフィルタ
     *
     * @param string $field フィールド名
     * @param string $label メッセージ用ラベル名
     * @param string $opts オプション
     * @return boolean バリデーションでエラーがない場合true, ある場合false
     */
    public function email($field, $val, $label)
    {
      $pattern = array(
        'pattern' => '/^[-+.\\w]+@[-a-z0-9]+(\\.[-a-z0-9]+)*\\.[a-z]{2,6}$/i',
        'pattern_name' => '適切な形'
      );
      return $this->regExp($field, $val, $label, $pattern);
    }
}

