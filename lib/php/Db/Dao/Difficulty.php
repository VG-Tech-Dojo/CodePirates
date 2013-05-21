<?php

//require_once PROJECT_DIR . '/lib/php/Db/Abstract.php';
require_once dirname(__FILE__) . '/Abstract.php';

/**
 * Db_Dao_Difficultyクラス
 *
 * @package Db
 * @subpackage Dao
 * @version $Id$
 *
 */
class Db_Dao_Difficulty extends Db_Dao_Abstract
{
    /**
     * 難易度の一覧を返す
     *
     * @return array
     * @throws PDOExeption
     *
     */
    public function getDifficultyDao()
    {
        $dbh = $this->getDbHandler();

        $query = 'select * from difficulty';
        $statement = $dbh->prepare($query);
        $statement->execute();
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
