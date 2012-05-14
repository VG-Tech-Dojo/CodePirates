<?php
require_once dirname(__FILE__) . '/../../models/Content.php';
require_once dirname(__FILE__) . '/../../lib/php/Factory.php';

class ContentTest extends PHPUnit_Framework_TestCase
{
    protected $obj;

    public function setUp()
    {
        $this->obj = new Content();
    }

    public function test_指定したページ数からページャー情報が取得できること()
    {
        $expected = array(
            'prev' => null,
            'page' => 1,
            'next' => 2,
            'total' => 5, 
            'list' => array(1, 2)
        );

        $content = $this->getMock('Db_Dao_Content', array('countAll', 'findLatestList'));

        $content->expects($this->once())
            ->method('countAll')
            ->will($this->returnValue(10));

        $content->expects($this->once())
            ->method('findLatestList')
            ->with(0, 2)
            ->will($this->returnValue(array(1, 2)));

        $factory = $this->getMock('Factory');
        $factory->expects($this->once())
            ->method('__call')
            ->will($this->returnValue($content));

        $this->obj->setFactory($factory);

        $this->assertEquals($expected, $this->obj->paginate(1, 2));
    }

    public function test_指定したトータルページ数以上のページ数を指定した場合最終ページの情報が返ること()
    {
        $expected = array(
            'prev' => 4,
            'page' => 5,
            'next' => null,
            'total' => 5, 
            'list' => array(9, 10)
        );

        $content = $this->getMock('Db_Dao_Content', array('countAll', 'findLatestList'));

        $content->expects($this->once())
            ->method('countAll')
            ->will($this->returnValue(10));

        $content->expects($this->once())
            ->method('findLatestList')
            ->with(8, 2)
            ->will($this->returnValue(array(9, 10)));

        $factory = $this->getMock('Factory');
        $factory->expects($this->once())
            ->method('__call')
            ->will($this->returnValue($content));

        $this->obj->setFactory($factory);

        $this->assertEquals($expected, $this->obj->paginate(6, 2));
    }
}
