<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Difficulty.php';

class Difficulty extends Model
{

    /**
     * 難易度をarrayで返す
     *
     */
    public function getDifficulty()
    {
        
        $difficulty_dao = $this->getFactory()->getDb_Dao_Difficulty();
        return $difficulty_dao->getDifficultyDao();
    }
}
