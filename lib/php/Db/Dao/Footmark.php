<?php

//require_once PROJECT_DIR . '/lib/php/Db/Abstract.php';
require_once dirname(__FILE__) . '/Abstract.php';

/**
 * Db_Dao_Footmarkクラス
 *
 * @package Db
 * @subpackage Dao
 * @version $Id$
 *
 */
class Db_Dao_Footmark extends Db_Dao_Abstract
{


    /**
     * すべてのFootmark情報を返す
     *
     * @throws PDOException
     */
    public function getAllFootmarkDao()
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from footmark';
        $statement = $dbh->prepare($query);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * q_idで指定されたFootmark情報を返す
     *
     * @param int $q_id 回答id
     *
     * @throws PDOException
     */
    public function getFootmarkByQIdDao($q_id)
    {
        $dbh = $this->getDbHandler();

        $query = 'select footmark.id as id, footmark.u_id as u_id,footmark.a_id as a_id, footmark.created_at as created_at, answer.q_id as q_id from footmark,answer  where answer.q_id = :QID and answer.id = footmark.a_id';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':QID', $q_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * 足跡を保存する
     *
     * @param int $u_id ユーザーID
     * @param int $a_id 回答ID
     * @return boolean 追加が成功して場合true, 失敗した場合false
     */
    public function insert($u_id, $a_id)
    { 
        $dbh = $this->getDbHandler();
        $query = 'insert into footmark (u_id, a_id, created_at) values (:U_ID, :A_ID, now())';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':U_ID', $u_id, PDO::PARAM_INT);
        $statement->bindValue(':A_ID', $a_id, PDO::PARAM_INT);
        $statement->execute();

        return ($statement->rowCount() === 1);
    }
}
