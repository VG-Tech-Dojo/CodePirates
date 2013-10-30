<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/User.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Answer.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Comment.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/MailLog.php';

class Notification extends Model
{
  /**
   *  メールを送るかどうかを判定
   * @param int $commentPostUserID 今、コメントを投稿したユーザーのID
   * @param int $answerID 回答者が回答した問題のID
   * @param array $commentedAllUserID 今までに回答者が回答した問題に対してコメントを投稿したユーザー全員のID
   * @param int $commentedUserID 今までに回答者が回答した問題に対してコメントを投稿したユーザー個人のID
   */
  public function judgeTransmissionMail($commentPostUserID, $answerID)
  {
    $answer = $this->getFactory()->getDb_Dao_Answer();
    $comment = $this->getFactory()->getDb_Dao_Comment();
    $user = $this->getFactory()->getDb_Dao_User();
    $mailLog = $this->getFactory()->getDb_Dao_MailLog();

    $commentedAllUserID = $comment->findCommentedUserIDByAnswerID($answerID);
    $answerInformation = $answer->getanswerbyansid($answerID);
    $answeredUserInformation = $user->findByUserId($answerInformation['u_id']);

    //回答者へメールを送信
    if($commentPostUserID != $answeredUserInformation['id']){
      $mailContents = $this->getAnsweredUserMailContents($commentPostUserID, $answerID);
      $mailResult = mb_send_mail($mailContents["to"], $mailContents["subject"], $mailContents["message"]);
      //送信内容をDBに書き込む
      $insertedMailLog = $mailLog->insert($answerID,$mailContents["to"], $mailContents["subject"], $mailContents["message"]);
    }

    //コメントをくれたユーザーへメールを送信
    foreach($commentedAllUserID as $uniqueID => $commentedUserID){
      if($commentPostUserID != $commentedUserID && $answeredUserInformation['id'] != $commentedUserID){
        $mailContents = $this->getCommentedUserMailContents($commentPostUserID, $commentedUserID, $answerID);
        $mailResult = mb_send_mail($mailContents["to"], $mailContents["subject"], $mailContents["message"]);
        //送信内容をDBに書き込む
        $insertedMailLog = $mailLog->insert($answerID,$mailContents["to"], $mailContents["subject"], $mailContents["message"]);
      }
    }
  }

  /**
   *  回答投稿者メール送信に必要な情報を取得
   * @param int $commentPostUserID 今、コメントを投稿したユーザーのID
   * @param int $commentedUserID 今までに回答者が回答した問題に対してコメントを投稿したユーザー個人のID
   * @param int $answerID 回答者が回答した問題のID
   * @param array $commentedUserInformation 今、コメントを投稿したユーザーの情報
   * @param array $answeredUserInformation 回答を投稿したユーザーの情報
   * @return array $mailContents メール送信に必要な情報を返す(アドレス、タイトル、内容)
   */
  private function getAnsweredUserMailContents($commentPostUserID, $answerID) 
  {
    $user = $this->getFactory()->getDb_Dao_User();
    $answer = $this->getFactory()->getDb_Dao_Answer();

    //メールアドレスに必要な情報を取得
    $commentedUserInformation = $user->findByUserId($commentPostUserID);
    //本文に必要な情報を取得
    $answerInformation = $answer->getanswerbyansid($answerID);
    $answeredUserInformation = $user->findByUserId($answerInformation['u_id']);

    $mailContents["to"] = $answeredUserInformation['email'];
    $mailContents["subject"] = 'CodePiratesからのお知らせ';
    $mailContents["message"] = 'あなたが投稿した問題に' . $commentedUserInformation['name'] . 'さんからコメントがつきました';

    return $mailContents;
  }

  /**
   *  コメント→コメントメール送信に必要な情報を取得
   * @param int $commentPostUserID 今、コメントを投稿したユーザーのID
   * @param int $commentedUserID 今までに回答者が回答した問題に対してコメントを投稿したユーザー個人のID
   * @param int $answerID 回答者が回答した問題のID
   * @param array $commentedUserInformation 今、コメントを投稿したユーザーの情報
   * @param array $answeredUserInformation 回答を投稿したユーザーの情報
   * @param array $commentedUserInformation 今までにコメントを投稿したユーザーの情報
   * @return array $mailContents メール送信に必要な情報を返す(アドレス、タイトル、内容)
   */
  private function getCommentedUserMailContents($commentPostUserID, $commentedUserID, $answerID)
  {
    $user = $this->getFactory()->getDb_Dao_User();
    $answer = $this->getFactory()->getDb_Dao_Answer();

    //メールアドレスに必要な情報を取得
    $commentedUserInformation = $user->findByUserId($commentPostUserID);
    //本文に必要な情報を取得
    $answerInformation = $answer->getanswerbyansid($answerID);
    $answeredUserInformation = $user->findByUserId($answerInformation['u_id']);
    $commentedUserInformation = $user->getuserbyid($commentedUserID);

    $mailContents["to"] = $commentedUserInformation['email'];
    $mailContents["subject"] = 'CodePiratesからのお知らせ';
    $mailContents["message"] = $answeredUserInformation['name'] . 'さんが投稿した問題に' . $commentedUserInformation['name'] . 'さんからコメントがつきました';

    return $mailContents;
  }
}
