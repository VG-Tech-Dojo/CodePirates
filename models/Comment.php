<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Comment.php';

class Comment extends Model
{

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
     * @param int $q_id 問題ID
     * @param string $content 内容
     * @param string $lang 言語
     */
    /*
    public function register($u_id, $q_id, $content, $lang)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->insert($u_id, $q_id, $content, $lang);
    }
    */
}
