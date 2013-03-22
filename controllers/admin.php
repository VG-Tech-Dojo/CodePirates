<?php

/**
 * 問題投稿画面
 */
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
    $sessionid = $session->id();
    $app->render('admin/postQuestion.twig', array('user' => $user_info, 'session' => $sessionid));
});

/**
 * 問題投稿確認画面
 */
$app->post('/admin/confirm' ,function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once LIB_DIR . '/FormValidator/AdminPostQuestionFormValidator.php';
    require_once MODELS_DIR . '/Question.php';


    $session = $app->factory->getSession();
    $form_validator = $app->factory->getFormValidator_AdminPostQuestionFormValidator();
    $params = $app->request()->post();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $session->set('posted',true);
    if($form_validator->run($params)){
        $app->render('admin/confirm.twig', array('user' => $user_info, 'question' => $params, 'session' =>$params['sessionid']));
        exit();
    } else {
        $errors = $form_validator->getErrors();
    }
    $app->render('admin/postQuestion.twig', array('user' => $user_info, 'errors' => $errors));
});


/**
 * 問題投稿完了画面
 */
$app->post('/admin/posted' ,function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Question.php';


    $session = $app->factory->getSession();
    $params = $app->request()->post();
    $user_info = array();
    $question = $app->factory->getQuestion();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }

    if($session->get('posted') && $session->id() === $params['sessionid']){
        try {
            $question->register(
                $params['title'],
                $params['content']
            );
            $session->remove('posted');
            $session->remove('sessionid');
        } catch (PDOException $e){
            $app->error('登録に失敗しました。');
        }
    }
    $app->render('admin/posted.twig', array('user' => $user_info));
});


