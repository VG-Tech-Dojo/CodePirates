<?php

//require_once PROJECT_DIR . '/lib/php/Db/Abstract.php';
require_once dirname(__FILE__) . '/Abstract.php';

/**
 * Db_Dao_Goodクラス
 *
 * @package Db
 * @subpackage Dao
 * @version $Id$
 *
 */
class Db_Dao_Good extends Db_Dao_Abstract
{
    /**
     * UserIDを指定しLike情報を返す
     *
     * @param int $q_id 問題ID
     * @throws PDOException
     */
    public function getlikefromuid($u_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from good where u_id = :ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $u_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * AnswerIDを指定してLike情報を返す
     *
     * @param  int $a_id 回答ID
     * @throws PDOException
     */
    public function getlikefromaid($a_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from good where a_id = :ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $a_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * AnswerID, UserIDを指定してLike情報を返す
     *
     * @param  int $a_id 回答ID
     * @param  int $u_id UserID
     * @throws PDOException
     */
    public function getlikefromaanduid($u_id, $a_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from good where u_id = :U_ID AND a_id = :A_ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':U_ID', $u_id, PDO::PARAM_INT);
        $statement->bindValue(':A_ID', $a_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * いいねを投稿する
     *
     * @param int $u_id ユーザーID
     * @param int $a_id 回答ID
     * @return boolean 追加が成功して場合true, 失敗した場合false
     */
    public function insert($u_id, $a_id)
    { 
        $dbh = $this->getDbHandler();
        $query = 'insert into good (u_id, a_id, created_at) values (:U_ID, :A_ID, now())';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':U_ID', $u_id, PDO::PARAM_INT);
        $statement->bindValue(':A_ID', $a_id, PDO::PARAM_INT);
        $statement->execute();

        return ($statement->rowCount() === 1);
    }
}
