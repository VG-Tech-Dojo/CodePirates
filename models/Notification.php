<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/User.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Answer.php';

class Notification extends Model
{
  public function MailNotifier($u_id, $a_id)
  {
    $judgeSendingMail = $this->judgeSendingMail($u_id);
    if($judgeSendingMail == "true"){
      $mailContents = $this->getMailContents($u_id, $a_id);
      $sendMail = $this->sendMail($mailContents["to"], $mailContents["subject"], $mailContents["message"]);
    }
  }
  
  /**
   *  メールを送るかどうかを判定
   *  @param int commentedUserID コメントをされたひとのユーザーID
   *  @return boolian メールを送信する場合はtrueそうでなければfalseを返す 
   */
  private function judgeSendingMail($u_id)
  {
    $answer = $this->getFactory()->getDb_Dao_Answer();

    $commentedUserID = $answer->getCommentedUserID($u_id);

    if($u_id != $commentedUserID['u_id']){
      return true;
    }else{
      return false;
    }
  }

  /**
   *  メール本文取得
   *  @param array $commentedUserName コメントしたひとの情報
   *  @param array commentedUserAddress コメントされたひとのアドレス
   */
  private function getMailContents($u_id, $a_id)
  {
    $user = $this->getFactory()->getDb_Dao_User();
    $answer = $this->getFactory()->getDb_Dao_Answer();

    $commentUser = $user->findByUserId($u_id);
    $commentedUserAddress = $user->getCommentedUserAddress($a_id);

    $mailContents["to"] = $commentedUserAddress['email'];
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
