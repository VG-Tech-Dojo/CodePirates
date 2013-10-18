<?php

require_once dirname(__FILE__) . '/Abstract.php';

class Db_Dao_Sender extends Db_Dao_Abstract
{
  public function getCommentUserName($u_id)
  {
        $dbh = $this->getDbHandler();

        $query = 'select name from user where id=:ID ';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':ID', $u_id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public function getMailUserID($a_id)
  {
        $dbh = $this->getDbHandler();

        $query = 'select u_id from answer where id=:A_ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':A_ID', $a_id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
  }

  public function getMailUserAddress($u_id)
  {
        $dbh = $this->getDbHandler();

        $query = 'select email from user where id=:U_ID';
        $statement = $dbh->prepare($query);
        $statement->bindValue(':U_ID', $u_id, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
  }
}
