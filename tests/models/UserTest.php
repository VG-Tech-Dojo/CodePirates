<?php
require_once dirname(__FILE__) . '/../../models/User.php';
require_once dirname(__FILE__) . '/../../lib/php/Factory.php';

class UserTest extends PHPUnit_Framework_TestCase
{
    protected $obj;

    public function setUp()
    {
        $this->obj = new User();
    }

    public function tearDown()
    {
    }

    public function test_テーブルに指定した名前のデータが存在するときtrueになること()
    {
        $user = $this->getMock('Db_Dao_User', array('countByName'));
        $user->expects($this->once())
            ->method('countByName')
            ->with('a')
            ->will($this->returnValue(1));

        $factory = $this->getMock('Factory');
        $factory->expects($this->once())
            ->method('__call')
            ->will($this->returnValue($user));

        $this->obj->setFactory($factory);

        $this->assertTrue($this->obj->isMember('a'));
    }

    public function test_テーブルに指定した名前のデータが存在しないときfalseになること()
    {
        $user = $this->getMock('Db_Dao_User', array('countByName'));
        $user->expects($this->once())
            ->method('countByName')
            ->with('a')
            ->will($this->returnValue(0));

        $factory = $this->getMock('Factory');
        $factory->expects($this->once())
            ->method('__call')
            ->will($this->returnValue($user));

        $this->obj->setFactory($factory);

        $this->assertFalse($this->obj->isMember('a'));
    }

    public function test_insert処理が実行される()
    {
        $name = 'NAME';
        $pass = 'PASS';
        $salt = 'SALT';
        $email = 'EMAIL';
        $birthday = 'BIRTHDAY';

        $user = $this->getMock('Db_Dao_User');
        $user->expects($this->once())
            ->method('insert')
            ->with($name, $pass, $salt, $email, $birthday)
            ->will($this->returnValue(true));

        $factory = $this->getMock('Factory');
        $factory->expects($this->once())
            ->method('__call')
            ->will($this->returnValue($user));

        $this->obj->setFactory($factory);

        $this->assertTrue($this->obj->register($name, $pass, $salt, $email, $birthday));
    }

    public function test_ユーザー名を指定してDBからデータを取得してプロパティに設定されること()
    {
        $user_data = array(
            'id' => '1',
            'name' => 'test',
            'password' => 'pass',
            'salt' => 'salt',
            'email' => 'email',
            'birthday' => 'birthday'
        );
        $user = $this->getMock('Db_Dao_User');
        $user->expects($this->once())
            ->method('findByName')
            ->with('test')
            ->will($this->returnValue($user_data));

        $factory = $this->getMock('Factory');
        $factory->expects($this->once())
            ->method('__call')
            ->will($this->returnValue($user));

        $this->obj->setFactory($factory);

        $this->assertTrue($this->obj->loadByName('test'));

        foreach ($user_data as $k => $v) {
            $this->assertEquals($v, $this->obj->$k);
        }
    }

    public function test_指定したユーザー名のデータが存在しない場合falseが返ること()
    {
        $user = $this->getMock('Db_Dao_User');
        $user->expects($this->once())
            ->method('findByName')
            ->with('test')
            ->will($this->returnValue(false));

        $factory = $this->getMock('Factory');
        $factory->expects($this->once())
            ->method('__call')
            ->will($this->returnValue($user));

        $this->obj->setFactory($factory);

        $this->assertFalse($this->obj->loadByName('test'));
    }
}
