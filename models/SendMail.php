<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/SendMail.php';

class SendMail extends Model
{
  /**
   *  メール送信
   *  @messageUserName コメントをしてくれた人
   *  @commentUserID コメントをもらった人のID
   *  @commentUserAddress コメントをもらった人のアドレス
   */
  public function sendMailToComment($u_id, $a_id)
  {
    $sendMail = $this->getFactory()->getDb_Dao_SendMail();
    $messageUserName = $sendMail->getMailUserName($u_id);
    $commentUserID = $sendMail->getCommentUser($a_id);
    $commentUserAddress = $sendMail->getMailUserAddress($commentUserID['u_id']);
    if($u_id != $commentUserID['u_id']){
      $to = $commentUserAddress['email'];
      $subject = 'CodePiratesからのお知らせ';
      $message = $messageUserName['name'] . 'さんからコメントがつきました';

      $mail_result = mb_send_mail($to, $subject, $message);
    }
  }
}
