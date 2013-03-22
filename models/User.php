<?php
require_once dirname(__FILE__) . '/Model.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/User.php';
require_once dirname(__FILE__) . '/../lib/php/Db/Dao/Answer.php';

class User extends Model
{
    public $id;
    public $name;
    public $password;
    public $salt;

    /**
     * ユーザー名を指定してDBからデータを取得してプロパティに設定する
     *
     * @param string $user_name ユーザー名
     * @return boolean ロードが成功した場合true, 失敗した場合false
     */
    public function loadByName($user_name)
    {
        $user_dao = $this->getFactory()->getDb_Dao_User();
        $user_info = $user_dao->findByName($user_name);
        if ($user_info === false) {
            return false;
        }
        $this->id = $user_info['id'];
        $this->name = $user_info['name'];
        $this->password = $user_info['password'];
        $this->salt = $user_info['salt'];

        return true;
    }

    /**
     * 会員か非会員かを判定する
     *
     * @param string $user_name ユーザー名
     * @return boolean 会員の場合true, 非会員の場合falseを返す
     */
    public function isMember($user_name)
    {
        $user = $this->getFactory()->getDb_Dao_User();
        return $user->countByName($user_name) > 0;
    }

    /**
     * U_IDからユーザー情報を取得する
     *
     * @param string $u_id ユーザーID
     */
    public function getUserByID($u_id)
    {
        $user = $this->getFactory()->getDb_Dao_User();
        return $user->getuserbyid($u_id);
    }

    /**
     * ユーザー登録する
     *
     * @param string $name ユーザー名
     * @param string $password パスワード
     * @param string $salt サルト
     * @return boolean 処理が成功した場合true, 失敗した場合false
     */
    public function register($user_name, $password, $salt)
    {
        $user = $this->getFactory()->getDb_Dao_User();
        return $user->insert($user_name, $password, $salt);
    }

    /**
     * ユーザーが問題の解答を見れるか見れないかを判定する
     *
     * @param string $question_id 他人の解答が見れるか判断したいid
     * @return boolean 見れる場合はtrue, 見れない場合はfalse 
     */
    public function canSee($user_id, $question_id)
    {
        $answer = $this->getFactory()->getDb_Dao_Answer();
        return $answer->isAnswered($user_id, $question_id);

    }
}
