<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Question.php';

class Question extends Model
{

    /**
     * 質問IDを指定してDBからデータを取得して返す
     *
     * @param string $question_id 問題ID
     * @return array $question_info そのIDの質問タイトルと内容と生成日時  
     */
    public function getQuestionByID($question_id)
    {
        
        $question_dao = $this->getFactory()->getDb_Dao_Question();
        return $question_dao->findByQuestionID($question_id);
    }
    /**
     *
     *緊急避難メソッド
     **/
    public function getQuestionwithID($question_id)
    {
        
        $question_dao = $this->getFactory()->getDb_Dao_Question();
        return $question_dao->getByQuestionID($question_id);
    }

    /**
     * 問題リストをDBからデータを取得して返す
     *
     * @return array $question_list 全質問のリスト
     */
    public function getAllQuestion()
    {
        
        $question_dao = $this->getFactory()->getDb_Dao_Question();
        return $question_dao->questionList();
    }

    /**
     * 問題を登録する
     *
     * @param string $title 問題のタイトル 
     * @param string $contnt 問題の中身 
     * @return boolean 処理が成功した場合true, 失敗した場合false
     */
    public function register($title, $content, $difficulty, $inputfile = null)
    {
       
        $question_dao = $this->getFactory()->getDb_Dao_Question();
        return $question_dao->insert($title, $content, $inputfile, $difficulty);
    }


    /**
     * 問題を修正する
     *
     * @param int $id 問題のid 
     * @param string $title 問題のタイトル 
     * @param string $contnt 問題の中身 
     * @return boolean 処理が成功した場合true, 失敗した場合false
     */
    public function updateQuestion($id, $title, $content, $difficulty, $inputfile)
    {
       
        $question_dao = $this->getFactory()->getDb_Dao_Question();
        return $question_dao->updatequestion($id, $title, $content, $inputfile, $difficulty);
    }
}
