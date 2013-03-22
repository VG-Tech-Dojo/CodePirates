<?php

//require_once PROJECT_DIR . '/lib/php/Db/Abstract.php';
require_once dirname(__FILE__) . '/Abstract.php';

/**
 * Db_Dao_Userクラス
 *
 * @package Db
 * @subpackage Dao
 * @version $Id$
 *
 */
class Db_Dao_Answer extends Db_Dao_Abstract
{
    /**
     * AnswerIDを指定して回答情報を返す
     *
     * @param string $a_id 回答ID
     * @return array ユーザー情報
     * @throws PDOException
     */
    public function getanswerbyansid($a_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from answer where id = :ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $a_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * QuestionIDを指定して回答情報を返す
     *
     * @param string $q_id QuestionID
     * @return array ユーザー情報
     * @throws PDOException
     */
    public function getanswerbyquesid($q_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from answer where q_id = :ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $q_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * UserIDを指定して回答情報を返す
     *
     * @param string $u_id 回答ID
     * @return array ユーザー情報
     * @throws PDOException
     */
    public function getanswerbyuserid($u_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from answer where u_id = :ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $u_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * UserIDとQuestionIDを指定して回答したかを返す
     *
     * @param string $user_id ユーザーid
     * @param string $question_id 問題id
     * @return boolean 回答したかどうか 
     * @throws PDOException
     */
    public function isAnswered($user_id, $question_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select count(id) cnt from answer where u_id = :USERID and q_id = :QUESTIONID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':USERID', $user_id, PDO::PARAM_INT);
        $statement->bindValue(':QUESTIONID', $question_id, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return (intval($result['cnt']) > 0);
    }




    /**
     * 回答情報を追加する
     *
     * @param int $u_id ユーザーID
     * @param int $q_id 問題ID
     * @param string $content 内容
     * @param string $lang 言語
     * @return boolean 追加が成功して場合true, 失敗した場合false
     */
    public function insert($u_id, $q_id, $content, $lang)
    {
        $dbh = $this->getDbHandler();
        $query = 'insert into answer (u_id, q_id, content, lang, created_at) values (:U_ID, :Q_ID, :CONTENT, :LANG, now())';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':U_ID', $u_id, PDO::PARAM_INT);
        $statement->bindValue(':Q_ID', $q_id, PDO::PARAM_INT);
        $statement->bindValue(':CONTENT', $content, PDO::PARAM_STR);
        $statement->bindValue(':LANG', $lang, PDO::PARAM_STR);
        $statement->execute();

        return ($statement->rowCount() === 1);
    }
}
