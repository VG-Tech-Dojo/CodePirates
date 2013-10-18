<?php

require_once dirname(__FILE__) . '/Abstract.php';

class Db_Dao_Sender extends Db_Dao_Abstract
{
  /**
   * コメントをしてくれた人の名前を取得
   */
  public function getCommentUserName($u_id)
  {
        $dbh = $this->getDbHandler();

        $query = 'select name from user where id=:ID ';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $u_id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * コメントをもらった人のIDを取得 
   */
  public function getMailUserID($a_id)
  {
        $dbh = $this->getDbHandler();

        $query = 'select u_id from answer where id=:A_ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':A_ID', $a_id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
  }

  /**
   * コメントをもらった人のアドレスを取得 
   */
  public function getMailUserAddress($a_id)
  {
        $dbh = $this->getDbHandler();

        $query = 'select u.email as email from user u inner join answer a where a.u_id=u.id and a.id=:A_ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':A_ID', $a_id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
  }
}
