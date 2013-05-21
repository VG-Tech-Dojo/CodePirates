<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Answer.php';

class Answer extends Model
{


    /**
     * すべての回答を返す
     *
     */
    public function getAllAnswer()
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getAllAnswerDao();
    }


    /**
     * answer_idを指定して回答を返す
     *
     * @param string $a_id Answer_id
     */
    public function getAnswerByAnsId($a_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getAnswerByAnsIdDao($a_id);
    }

    /**
     * answer_idからユーザーのユーザー名を結合して回答を返す
     *
     * @param string $a_id Answer_id
     */
    public function getAnswerByAnsIdWithUName($a_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getAnswerByAnsIdWithUNameDao($a_id);
    }

    /**
     * answer_iを指定してカラムを削除する
     *
     * @param string $a_id Answer_id
     */
    public function deleteAnswerByIdDao($a_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->deleteAnswerById($a_id);
    }

    /**
     * question_idを指定してAnswerを返す
     *
     * @param string $q_id Question_id
     */
    public function getAnswerByQuesId($q_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getAnswerByQuesIdDao($q_id);
    }

    /**
     * question_idを指定して回答人数を返す
     *
     * @param string $q_id Question_id
     */
    public function getanswerpeoplenumbyquestionid($q_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getAnswerPeopleNumByQuestionIdDao($q_id);
    }


    /**
     * User_idを指定して回答を返す
     *
     * @param string $u_id User_id
     */
    public function getAnswerByUserId($u_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getAnswerByUserIdDao($u_id);
    }

    /**
     * User_idを指定して問題番号順に回答を返す
     *
     * @param string $u_id User_id
     */
    public function getAnswerByUserIdOfQuestionNum($u_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getAnswerByUserIdOfQNumDao($u_id);
    }

    /**
     * Question_id指定して回答人数を返す
     *
     * @param string $q_id Question_id
     */
    public function getAnsweredPeopleByQuestionId($q_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getAnswerPeopleNumByQuestionIdDao($q_id);
    }


    /**
     * User_id,Question_idを指定して回答を返す
     *
     * @param string $u_id User_id
     * @param string $q_id Question_id
     */
    public function getAnswerByUserIdQuestionId($u_id,$q_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getAnswerByUserIdQuestionIdDao($u_id,$q_id);
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
        return $answer->update($a_id, $u_id, $q_id, $content, $lang, $linecount);
    }
}
