<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/User.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Answer.php';

class MailNotifier extends Model
{
  /**
   *  メール送信
   *  @commentUserName コメントをしてくれた人
   *  @commentedUserID コメントをもらった人のID
   *  @commentedUserAddress コメントをもらった人のアドレス
   */
  public function sendMail($u_id, $a_id)
  {
    $user = $this->getFactory()->getDb_Dao_User();
    $answer = $this->getFactory()->getDb_Dao_Answer();

    $commentUserName = $user->findByUserId($u_id);
    $commentedUserID = $answer->getCommentedUserID($a_id);
    $commentedUserAddress = $user->getCommentedUserAddress($a_id);
    if($u_id != $commentedUserID['u_id']){
      $to = $commentedUserAddress['email'];
      $subject = 'CodePiratesからのお知らせ';
      $message = $commentUserName['name'] . 'さんからコメントがつきました';
      $mail_result = mb_send_mail($to, $subject, $message);
    }else{
      //TODO : 送信に失敗した時の例外処理を記述(今後try-catchに変更予定)
      echo '送信していません';
      exit;
    }
  }
}
