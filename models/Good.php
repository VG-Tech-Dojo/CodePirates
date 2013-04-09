<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Good.php';

class Good extends Model
{

    /**
     * user_idからLikeを返す
     *
     */
    public function getLikeFromUID($u_id)
    {
        $good = $this->getFactory()->getDb_Dao_Good();
        return $good->getlikefromqid($u_id);
    }

    /**
     * answer_idからLikeを返す
     *
     */
    public function getLikeFromAID($a_id)
    {
        $good = $this->getFactory()->getDb_Dao_Good();
        return $good->getlikefromaid($a_id);
    }

    /**
     * answer_id,user_idからLikeを返す
     *
     */
    public function getLikeFromAandUID($u_id, $a_id)
    {
        $good = $this->getFactory()->getDb_Dao_Good();
        return $good->getlikefromaanduid($u_id, $a_id);
    }

    /**
     * いいねを投稿する
     *
     */
    public function registLike($u_id, $a_id)
    {
        $good = $this->getFactory()->getDb_Dao_Good();
        return $good->insert($u_id, $a_id);
    }




}
