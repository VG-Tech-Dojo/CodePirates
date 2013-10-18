<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Sender.php';

class Sender extends Model
{
  /**
   *  メール送信
   *  @commentUserName コメントをしてくれた人
   *  @mailUserID コメントをもらった人のID
   *  @mailUserAddress コメントをもらった人のアドレス
   */
  public function sendCommentMail($u_id, $a_id)
  {
    $sender = $this->getFactory()->getDb_Dao_Sender();
    $commentUserName = $sender->getCommentUserName($u_id);
    $mailUserID = $sender->getMailUserID($a_id);
    $mailUserAddress = $sender->getMailUserAddress($mailUserID['u_id']);
    if($u_id != $mailUserID['u_id']){
      $to = $mailUserAddress['email'];
      $subject = 'CodePiratesからのお知らせ';
      $message = $commentUserName['name'] . 'さんからコメントがつきました';

      $mail_result = mb_send_mail($to, $subject, $message);
    }else{
      //TODO : 送信に失敗した時の例外処理を記述
      echo '送信していません';
      exit;
    }
  }
}
