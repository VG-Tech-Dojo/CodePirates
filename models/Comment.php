<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Comment.php';

class Comment extends Model
{

    /**
     * すべてのコメントを取得する
     */
    public function getAllComments()
    {
        $comment = $this->getFactory()->getDb_Dao_Comment();
        return $comment->getallcomments();
    }


    /**
     * answer_idからCommentを返す
     *
     * @param string $a_id Answer_id
     */
    public function getCommentByAnsId($a_id)
    {
        $comment = $this->getFactory()->getDb_Dao_Comment();
        return $comment->getcommentbyanswerid($a_id);
    }


    /**
     * answer_idから回答人数を返す
     *
     * @param string $a_id Answer_id
     */
    /*
    public function getanswerpeoplenumbyquestionid($a_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->getanswerpeoplenumbyquestionid($a_id);
    }
    */

    /**
     * 回答をテーブルに格納する
     *
     * @param int $u_id ユーザーID
     * @param int $a_id 回答ID
     * @param string $content 内容
     */
    
    public function register($u_id, $a_id, $content)
    {
        $answer = $this->getFactory()->getDb_Dao_Comment();
        return $answer->insert($u_id, $a_id, $content);
    }
   
}
