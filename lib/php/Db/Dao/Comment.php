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
     * AnswerIDを指定してコメントを返す
     *
     * @param string $a_id 回答ID
     * @return array ついたコメント
     * @throws PDOException
     */
   
    public function getcommentbyanswerid($a_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from comment where a_id = :ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $a_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
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
    /*
    public function insert($u_id, $a_id, $lang)
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
     */


}
