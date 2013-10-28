<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/User.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Answer.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Comment.php';

class Notification extends Model
{
  public function MailNotifier($commentPostUserID, $answerID)
  {
    $judgementedFlag = $this->judgeTransmissionMail($commentPostUserID, $answerID);
  }
  
  /**
   *  メールを送るかどうかを判定
   * @param int $commentPostUserID 今、コメントを投稿したユーザーのID
   * @param int $answerID 回答者が回答した問題のID
   * @param array $commentedAllUserID 今までに回答者が回答した問題に対してコメントを投稿したユーザー全員のID
   * @param int $commentedUserID 今までに回答者が回答した問題に対してコメントを投稿したユーザー個人のID
   */
  private function judgeTransmissionMail($commentPostUserID, $answerID)
  {
    $answer = $this->getFactory()->getDb_Dao_Answer();
    $comment = $this->getFactory()->getDb_Dao_Comment();
    $user = $this->getFactory()->getDb_Dao_User();

    $commentedAllUserID = $comment->findCommentedUserIDByAnswerID($answerID);
    $answerInformation = $answer->getanswerbyansid($answerID);
    $answerUserInformation = $user->findByUserId($answerInformation['u_id']);

    //回答者へメールを送信
    if($commentPostUserID != $answerUserInformation['id']){
      $mailContents = $this->getAnsweredUserMailContents($commentPostUserID, $answerID);
      $sendMail = $this->sendMail($mailContents["to"], $mailContents["subject"], $mailContents["message"]);
    }
    //コメントをくれたユーザーへメールを送信
    foreach($commentedAllUserID as $uniqueID => $commentedUserID){
      if($commentPostUserID != $commentedUserID){
        $mailContents = $this->getCommentedUserMailContents($commentPostUserID, $commentedUserID, $answerID);
        $sendMail = $this->sendMail($mailContents["to"], $mailContents["subject"], $mailContents["message"]);
      }
    }
  }

  /**
   *  回答投稿者メール送信に必要な情報を取得
   * @param int $commentPostUserID 今、コメントを投稿したユーザーのID
   * @param int $commentedUserID 今までに回答者が回答した問題に対してコメントを投稿したユーザー個人のID
   * @param int $answerID 回答者が回答した問題のID
   * @param array $commentPostUserInformation 今、コメントを投稿したユーザーの情報
   * @param array $answerUserInformation 回答を投稿したユーザーの情報
   * @return array $mailContents メール送信に必要な情報を返す(アドレス、タイトル、内容)
   */
  private function getAnsweredUserMailContents($commentPostUserID, $answerID) 
  {
    $user = $this->getFactory()->getDb_Dao_User();
    $answer = $this->getFactory()->getDb_Dao_Answer();

    //メールアドレスに必要な情報を取得
    $commentPostUserInformation = $user->findByUserId($commentPostUserID);
    //本文に必要な情報を取得
    $answerInformation = $answer->getanswerbyansid($answerID);
    $answerUserInformation = $user->findByUserId($answerInformation['u_id']);

    $mailContents["to"] = $answerUserInformation['email'];
    $mailContents["subject"] = 'CodePiratesからのお知らせ';
    $mailContents["message"] = 'あなたが投稿した問題に' . $commentPostUserInformation['name'] . 'さんからコメントがつきました';

    return $mailContents;
  }

  /**
   *  コメント→コメントメール送信に必要な情報を取得
   * @param int $commentPostUserID 今、コメントを投稿したユーザーのID
   * @param int $commentedUserID 今までに回答者が回答した問題に対してコメントを投稿したユーザー個人のID
   * @param int $answerID 回答者が回答した問題のID
   * @param array $commentPostUserInformation 今、コメントを投稿したユーザーの情報
   * @param array $answerUserInformation 回答を投稿したユーザーの情報
   * @param array $commentedUserInformation 今までにコメントを投稿したユーザーの情報
   * @return array $mailContents メール送信に必要な情報を返す(アドレス、タイトル、内容)
   */
  private function getCommentedUserMailContents($commentPostUserID, $commentedUserID, $answerID)
  {
    $user = $this->getFactory()->getDb_Dao_User();
    $answer = $this->getFactory()->getDb_Dao_Answer();

    //メールアドレスに必要な情報を取得
    $commentPostUserInformation = $user->findByUserId($commentPostUserID);
    //本文に必要な情報を取得
    $answerInformation = $answer->getanswerbyansid($answerID);
    $answerUserInformation = $user->findByUserId($answerInformation['u_id']);
    $commentedUserInformation = $user->getuserbyid($commentedUserID);

    $mailContents["to"] = $commentedUserInformation['email'];
    $mailContents["subject"] = 'CodePiratesからのお知らせ';
    $mailContents["message"] = $answerUserInformation['name'] . 'さんが投稿した問題に' . $commentPostUserInformation['name'] . 'さんからコメントがつきました';

    return $mailContents;
  }

  /**
   *  メールを送信
   */
   private function sendMail($to, $subject, $message)
   {
      $mail_result = mb_send_mail($to, $subject, $message);
   }
}
