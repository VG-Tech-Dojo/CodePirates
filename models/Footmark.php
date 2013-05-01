<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Footmark.php';

class Footmark extends Model
{

    /**
     * すべてのFootmarkを返す
     *
     */
    public function getAllFootmark()
    {
        $footmark = $this->getFactory()->getDb_Dao_Footmark();
        return $footmark->getallfootmark();
    }

    /**
     * q_idで指定したFootmarkを返す
     *
     * @param int $q_id 回答id
     */
    public function getFootmarkByQID($q_id)
    {
        $footmark = $this->getFactory()->getDb_Dao_Footmark();
        return $footmark->getfootmarkbyqid($q_id);
    }


    /**
     * Footmarkを追加する
     *
     */
    public function register($u_id, $a_id)
    {
        $footmark = $this->getFactory()->getDb_Dao_Footmark();
        return $footmark->insert($u_id, $a_id);
    }



}
