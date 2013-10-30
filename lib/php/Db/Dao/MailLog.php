<?php
require_once dirname(__FILE__) . '/Abstract.php';
/**
 * Db_Dao_MailLogクラス
 *
 * @package Db
 * @subpackage Dao
 *
 */
class Db_Dao_MailLog extends Db_Dao_Abstract
{
  /**
   * メール送信時に、内容をDBに記録する
   *
   * @param int $u_id メールを送信したユーザーのID
   * @param string $email_address 送信したユーザーのメールアドレス
   * @param string $subject メールのタイトル
   * @param string $message メールの本文
   * @return boolean 記録が成功した場合true,失敗した場合false
   */
  public function insert($u_id, $email_address, $subject, $message)
  {
    $dbh = $this->getDBHandler();

    $query = 'insert into mail_log (u_id, email_address, subject, message, created_at) values (:U_ID, :EMAIL_ADDRESS, :SUBJECT, :MESSAGE, now())';
    $statement = $dbh->prepare($query);
    $statement->bindValue(':U_ID', $u_id, PDO::PARAM_INT);
    $statement->bindValue(':EMAIL_ADDRESS', $email_address, PDO::PARAM_STR);
    $statement->bindValue(':SUBJECT', $subject, PDO::PARAM_STR);
    $statement->bindValue(':MESSAGE', $message, PDO::PARAM_STR);
    $statement->execute();

    return ($statement->rowCount() === 1);
  }
}
