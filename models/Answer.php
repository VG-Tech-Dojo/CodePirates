<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Answer.php';

class Answer extends Model
{


    /**
     * すべてのAnswerを返す
     *
     */
    public function getAllAnswer()
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getallanswer();
    }


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
     * answer_idからAnswerを削除する
     *
     * @param string $a_id Answer_id
     */
    public function deleteAnswerById($a_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->deleteanswerbyid($a_id);
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
     * question_idから回答人数を返す
     *
     * @param string $q_id Question_id
     */
    public function getanswerpeoplenumbyquestionid($q_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getanswerpeoplenumbyquestionid($q_id);
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
     * User_idから問題番号順にAnswerを返す
     *
     * @param string $u_id User_id
     */
    public function getAnswerByUserIdOfQuestionNum($u_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getanswerbyuseridofqnum($u_id);
    }

    /**
     * Question_idから回答人数を返す
     *
     * @param string $q_id Question_id
     */
    public function getAnsweredPeopleByQuestionId($q_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getanswerpeoplenumbyquestionid($q_id);
    }


    /**
     * User_id,Question_idからAnswerを返す
     *
     * @param string $u_id User_id
     * @param string $q_id Question_id
     */
    public function getAnswerByUserIdQuestionId($u_id,$q_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getanswerbyuseridquestionid($u_id,$q_id);
    }

    /**
     * 回答をテーブルに格納する
     *
     * @param int $u_id ユーザーID
     * @param int $q_id 問題ID
     * @param string $content 内容
     * @param string $lang 言語
     */
    public function register($u_id, $q_id, $content, $lang, $linecount)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->insert($u_id, $q_id, $content, $lang, $linecount);
    }

    /**
     * 回答を更新する
     *
     * @param int $a_id 回答ID
     * @param int $u_id ユーザーID
     * @param int $q_id 問題ID
     * @param string $content 内容
     * @param string $lang 言語
     * @param string $linecount 行数
     */
    public function update($a_id, $u_id, $q_id, $content, $lang, $linecount)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->updateans($a_id, $u_id, $q_id, $content, $lang, $linecount);
    }
}
