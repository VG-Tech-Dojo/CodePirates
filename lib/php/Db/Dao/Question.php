<?php

//require_once PROJECT_DIR . '/lib/php/Db/Abstract.php';
require_once dirname(__FILE__) . '/Abstract.php';

/**
 * Db_Dao_Questionクラス
 *
 * @package Db
 * @subpackage Dao
 * @version $Id$
 *
 */
class Db_Dao_Question extends Db_Dao_Abstract
{
    /**
     * 問題IDを指定して任意の問題情報を返す
     *
     * @param int $questionId 
     * @return array 
     * @throws PDOExeption
     *
     */
    public function findByQuestionId($questionId)
    {
        $dbh = $this->getDbHandler();

        $query  = 'select * from question where id = :Question_ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':Question_ID', $questionId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 問題の一覧を返す
     *
     * @return array
     * @throws PDOExeption
     *
     */
    public function questionList()
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from question';
        $statement = $dbh->prepare($query);
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * 問題を追加する
     *
     * @param string $title 問題のタイトル  
     * @param string $content 問題の内容 
     * @return boolean 追加が成功して場合true, 失敗した場合false
     */
    public function insert($title, $content)
    { 
        $dbh = $this->getDbHandler();
        $query = 'insert into question (title, content, created_at) values (:TITLE, :CONTENT, now())';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':TITLE', $title, PDO::PARAM_STR);
        $statement->bindValue(':CONTENT', $content, PDO::PARAM_STR);
        $statement->execute();

        return ($statement->rowCount() === 1);
    }
}
