<?php
require_once dirname(__FILE__) . '/Abstract.php';

class Db_Dao_Content extends Db_Dao_Abstract
{
    /**
     * 全レコード数を取得する
     *
     * @return int レコード数
     */
    public function countAll()
    {
        $dbh = $this->getDbHandler();

        $query  = 'select count(id) cnt from content';
        $statement = $dbh->prepare($query);

        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return intval($result['cnt']);
    } 

    /**
     * 作成日が新しい順でコンテンツ情報の一覧を取得する
     *
     * @param int $offset 取得位置
     * @param int $limit 取得件数
     */
    public function findLatestList($offset, $limit)
    {
        $dbh = $this->getDbHandler();

        $query  = 'select id, title, author, price, image_path, description, category from content order by created_at desc limit :OFFSET, :LIMIT';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':OFFSET', $offset, PDO::PARAM_INT);
        $statement->bindValue(':LIMIT', $limit, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }
}
