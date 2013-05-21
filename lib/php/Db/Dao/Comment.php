<?php

//require_once PROJECT_DIR . '/lib/php/Db/Abstract.php';
require_once dirname(__FILE__) . '/Abstract.php';

/**
 * Db_Dao_Commentクラス
 *
 * @package Db
 * @subpackage Dao
 * @version $Id$
 *
 */
class Db_Dao_Comment extends Db_Dao_Abstract
{
    /**
     * すべてのコメントを返す
     *
     * @return array ついたコメント
     * @throws PDOException
     */
   
    public function getAllCommentsDao()
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from comment';
        $statement = $dbh->prepare($query);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * AnswerIDを指定してコメントを返す
     *
     * @param string $a_id 回答ID
     * @return array ついたコメント
     * @throws PDOException
     */
   
    public function getCommentByAnswerIdDao($a_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select comment.id, comment.a_id, comment.u_id, comment.content, comment.created_at, user.name as name  from comment, user where user.id = comment.u_id and a_id = :ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $a_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
       

    /**
     * AnswerIDを指定してコメントを削除する
     *
     * @param string $a_id 回答ID
     * @throws PDOException
     */
   
    public function deleteCommentFromAIdDao($a_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'delete from comment where a_id = :ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $a_id, PDO::PARAM_INT);
        $statement->execute();

    }
    
    
    /**
     * AnswerIDを指定してコメント数を返す
     *
     * @param string $a_id AnswerID
     * @return int 人数
     * @throws PDOException
     */
    /*
    public function getcommentnumbyanswerid($a_id)
    {
        $dbh = $this->getDbHandler();

        $query = ' a_id = :ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $q_id, PDO::PARAM_INT);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result['cnt'];
    }
    */
    /**
     * コメントを追加する
     *
     * @param int $u_id ユーザーID
     * @param int $a_id 回答ID
     * @param string $content 内容
     * @return boolean 追加が成功して場合true, 失敗した場合false
     */
    
    public function insert($u_id, $a_id, $content)
    {
        $dbh = $this->getDbHandler();
        $query = 'insert into comment (u_id, a_id, content, created_at) values (:U_ID, :A_ID, :CONTENT, now())';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':U_ID', $u_id, PDO::PARAM_INT);
        $statement->bindValue(':A_ID', $a_id, PDO::PARAM_INT);
        $statement->bindValue(':CONTENT', $content, PDO::PARAM_STR);
        $statement->execute();

        return ($statement->rowCount() === 1);
    }



}
