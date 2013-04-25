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
     * a_idで指定したFootmarkを返す
     *
     * @param int $a_id 回答id
     */
    public function getFootmarkByAID($a_id)
    {
        $footmark = $this->getFactory()->getDb_Dao_Footmark();
        return $footmark->getfootmarkbyaid($a_id);
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
