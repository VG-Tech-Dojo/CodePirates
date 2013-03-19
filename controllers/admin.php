<?php

$app->get('/admin/postQuestion' ,function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Answer.php';
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/User.php';


    $session = $app->factory->getSession();
    $answer = $app->factory->getAnswer();
    $question = $app->factory->getQuestion();
    $user = $app->factory->getUser();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $app->render('admin/postQuestion.twig', array('user' => $user_info));
});



$app->post('/admin/confirm' ,function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Question.php';


    $session = $app->factory->getSession();
    $question = $app->factory->getQuestion();
    $params = $app->request()->post();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    /*$question->register(
        $params['title'],
        $params['content']
    );*/
    $app->render('admin/confirm.twig', array('user' => $user_info, 'question' => $params));
});


$app->post('/admin/posted' ,function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Question.php';


    $session = $app->factory->getSession();
    $question = $app->factory->getQuestion();
    $params = $app->request()->post();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $question->register(
        $params['title'],
        $params['content']
    );
    $app->render('admin/posted.twig', array('user' => $user_info));
});


