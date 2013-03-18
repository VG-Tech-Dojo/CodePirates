<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Question.php';

class Question extends Model
{

    private $question_dao = $this->getFactory()->getDb_Dao_Question();

    /**
     * 質問IDを指定してDBからデータを取得して返す
     *
     * @param string $question_id 問題ID
     * @return array $question_info そのIDの質問タイトルと内容と生成日時  
     */
    public function getQuestionByID($question_id)
    {
        return $question_dao->findByQuestionID($question_id);
    }

    /**
     * 問題リストをDBからデータを取得して返す
     *
     * @return array $question_list 全質問のリスト
     */
    public function getAllQuestion()
    {
        return $question_dao->questionList();
    }

    /**
     * 問題を登録する
     *
     * @param string $title 問題のタイトル 
     * @param string $contnt 問題の中身 
     * @return boolean 処理が成功した場合true, 失敗した場合false
     */
    public function register($title, $content)
    {
        return $question_dao->insert($title, $content);
    }
}
