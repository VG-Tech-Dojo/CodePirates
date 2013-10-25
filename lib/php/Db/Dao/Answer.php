<?php

require_once dirname(__FILE__) . '/Abstract.php';

/**
 * Db_Dao_Answerクラス
 *
 * @package Db
 * @subpackage Dao
 * @version $Id$
 *
 */
class Db_Dao_Answer extends Db_Dao_Abstract
{

    /**
     * すべての回答情報を返す
     *
     * @throws PDOException
     */
    public function getallanswer()
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from answer';
        $statement = $dbh->prepare($query);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


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
     * AnswerIDを指定してユーザー名を付与して回答情報を返す
     *
     * @param string $a_id 回答ID
     * @return array ユーザー情報
     * @throws PDOException
     */
    public function getanswerbyansidwithuname($a_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select answer.id as id, answer.u_id as u_id, answer.q_id as q_id, answer.content as content, answer.lang as lang, answer.line_count as line_count, answer.created_at as created_at, user.name as u_name from answer, user where answer.id = :ID and answer.u_id = user.id';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $a_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * AnswerIDを指定してカラムを削除する
     *
     * @param string $a_id 回答ID
     * @throws PDOException
     */
    public function deleteanswerbyid($a_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'delete from answer where id = :ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $a_id, PDO::PARAM_INT);
        $statement->execute();
        print("dbh");

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
     * QuestionIDを指定して回答人数を返す
     *
     * @param string $q_id QuestionID
     * @return int 人数
     * @throws PDOException
     */
    public function getanswerpeoplenumbyquestionid($q_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select count(distinct u_id) cnt from answer where q_id = :ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $q_id, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result['cnt'];
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
     * UserIDを指定して問題順に回答情報を返す
     *
     * @param string $u_id 回答ID
     * @return array ユーザー情報
     * @throws PDOException
     */
    public function getanswerbyuseridofqnum($u_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from answer where u_id = :ID order by q_id';
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
     * UserID,QuestionIdを指定して回答情報を返す
     *
     * @param string $u_id 回答ID
     * @param string $q_id QuestionID
     * @return array ユーザー情報
     * @throws PDOException
     */
    public function getanswerbyuseridquestionid($u_id,$q_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from answer where u_id = :ID and q_id=:Q_ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $u_id, PDO::PARAM_INT);
        $statement->bindValue(':Q_ID', $q_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * 回答情報を追加する
     *
     * @param int $u_id ユーザーID
     * @param int $q_id 問題ID
     * @param string $content 内容
     * @param string $lang 言語
     * @param int $linecount コード行数
     * @return boolean 追加が成功して場合true, 失敗した場合false
     */
    public function insert($u_id, $q_id, $content, $lang, $linecount)
    {
        $dbh = $this->getDbHandler();
        $query = 'insert into answer (u_id, q_id, content, lang, line_count, created_at) values (:U_ID, :Q_ID, :CONTENT, :LANG, :LINECOUNT, now())';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':U_ID', $u_id, PDO::PARAM_INT);
        $statement->bindValue(':Q_ID', $q_id, PDO::PARAM_INT);
        $statement->bindValue(':CONTENT', $content, PDO::PARAM_STR);
        $statement->bindValue(':LANG', $lang, PDO::PARAM_STR);
        $statement->bindValue(':LINECOUNT', $linecount, PDO::PARAM_INT);
        $statement->execute();

        return ($statement->rowCount() === 1);
    }


    /**
     * 回答情報を更新する
     *
     * @param int $a_id 回答ID
     * @param int $u_id ユーザーID
     * @param int $q_id 問題ID
     * @param string $content 内容
     * @param string $lang 言語
     * @param int $linecount コード行数
     * @return boolean 追加が成功して場合true, 失敗した場合false
     */
    public function updateans($a_id, $u_id, $q_id, $content, $lang, $linecount)
    {
        $dbh = $this->getDbHandler();
        $query = 'update answer set u_id = :U_ID, q_id = :Q_ID, content = :CONTENT, lang = :LANG, line_count = :LINECOUNT where id = :A_ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':A_ID', $a_id, PDO::PARAM_INT);
        $statement->bindValue(':U_ID', $u_id, PDO::PARAM_INT);
        $statement->bindValue(':Q_ID', $q_id, PDO::PARAM_INT);
        $statement->bindValue(':CONTENT', $content, PDO::PARAM_STR);
        $statement->bindValue(':LANG', $lang, PDO::PARAM_STR);
        $statement->bindValue(':LINECOUNT', $linecount, PDO::PARAM_INT);
        $statement->execute();

        return ($statement->rowCount() === 1);
    }

    /**
     * AnswerIDを指定してコメントをもらったユーザーのIDを返す
     *
     * @param int $a_id 回答ID
     * @return 
     */
    public function getCommentedUserID($a_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select u_id from answer where id=:A_ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':A_ID', $a_id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    } 

}
