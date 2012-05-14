<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Content.php';

class Content extends Model
{
    public $id;
    public $title;
    public $author;
    public $price;
    public $image_path;
    public $category;
    public $description;

    public function paginate($page = 1, $limit = 1)
    {
        $content_dao = $this->getFactory()->getDb_Dao_Content();
        $num = $content_dao->countAll();
        $total = intval(ceil($num / $limit));
        if ($total < $page) {
            $page = $total;
        }
        $offset = $limit * ($page - 1);
        $list = $content_dao->findLatestList($offset, $limit);

        $prev = $page - 1;
        if ($prev < 1) {
            $prev = null;
        }
        $next = $page + 1;
        if ($total < $next) {
            $next = null;
        }

        return array(
            'prev' => $prev,
            'page' => $page,
            'next' => $next,
            'total' => $total,
            'list' => $list
        );
    }
}
