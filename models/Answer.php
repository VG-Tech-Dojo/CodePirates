<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Answer.php';

class Answer extends Model
{

    /**
     * answer_idからAnswerを返す
     *
     * @param string $a_id Answer_id
     */
    public function getAnswerByAnsId($a_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getanswerbyansid($a_id);
    }


    /**
     * question_idからAnswerを返す
     *
     * @param string $q_id Question_id
     */
    public function getAnswerByQuesId($q_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getanswerbyquesid($q_id);
    }


    /**
     * User_idからAnswerを返す
     *
     * @param string $u_id User_id
     */
    public function getAnswerByUserId($u_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getanswerbyuserid($u_id);
    }


    /**
     * 回答をテーブルに格納する
     *
     * @param string $u_id ユーザーID
     * @param string $q_id 問題ID
     * @param string $content 内容
     */
    public function register($u_id, $q_id, $content)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->insert($u_id, $q_id, $content);
    }
}
