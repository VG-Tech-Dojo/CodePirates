<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/User.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Answer.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Comment.php';

class Notification extends Model
{
  public function MailNotifier($answerPostedUserID, $postedAnswerID)
  {
    $judgementedFlag = $this->judgeTransmissionMail($answerPostedUserID, $postedAnswerID);
  }
  
  /**
   *  メールを送るかどうかを判定
   */
  private function judgeTransmissionMail($answerPostedUserID, $postedAnswerID)
  {
    $answer = $this->getFactory()->getDb_Dao_Answer();
    $comment = $this->getFactory()->getDb_Dao_Comment();

    $commentedUsersID = $comment->findCommentedUserIDByAnswerID($postedAnswerID);
    foreach($commentedUsersID as $uniqueID => $ID){
      if($answerPostedUserID != $ID){
        $mailContents = $this->getMailContents($answerPostedUserID,$commentedUsersID, $ID);
        $sendMail = $this->sendMail($mailContents["to"], $mailContents["subject"], $mailContents["message"]);
      }
    }
  }

  /**
   *  メール送信に必要な情報を取得
   */
  private function getMailContents($answerPostedUserID,$commentedUserID, $postedAnswerUserID)
  {
    $user = $this->getFactory()->getDb_Dao_User();
    $answer = $this->getFactory()->getDb_Dao_Answer();

    $commentUser = $user->findByUserId($answerPostedUserID);
    $commentedUser = $user->getuserbyid($postedAnswerUserID);

    $mailContents["to"] = $commentedUser['email'];
    $mailContents["subject"] = 'CodePiratesからのお知らせ';
    $mailContents["message"] = $commentUser['name'] . 'さんからコメントがつきました';

    return $mailContents;
  }

  /**
   *  メールを送信
   */
   private function sendMail($to, $subject, $message){
      $mail_result = mb_send_mail($to, $subject, $message);
   }
}
