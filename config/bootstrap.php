<?php
require 'const.php';
require_once LIB_DIR . '/Factory.php';

$app->factory = new Factory();


/**
 * ログインしている場合はトップにリダイレクトする
 */
function noauthorized()
{
    require_once LIB_DIR . '/Session.php';

    $app = Slim::getInstance();
    $session = $app->factory->getSession();
    if ($session->get('user_id')) {
        $app->redirect('/');
    }
}

/**
 * ログインしていない場合はトップにリダイレクトする
 */
function authorized()
{
    require_once LIB_DIR . '/Session.php';

    $app = Slim::getInstance();
    $session = $app->factory->getSession();

    if (!$session->get('user_id')) {
        $app->redirect('/');
    }
}

/**
 * adminじゃない場合はトップにリダイレクトするfor Treasure2013
 */
function admin_auth()
{
    require_once LIB_DIR . '/Session.php';

    $app = Slim::getInstance();
    $session = $app->factory->getSession();

    if ($session->get('user_name') !== "treasureAdmin") {
        $app->redirect('/');
    }
}
