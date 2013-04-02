<?php

/**
 * 問題投稿画面
 */
$app->get('/admin/postQuestion' ,function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/User.php';


    $session = $app->factory->getSession();
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
 *問題修正のための問題リスト画面
 */
$app->get('/admin/modifyQuestion' ,function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/User.php';


    $session = $app->factory->getSession();
    $question = $app->factory->getQuestion();
    $user = $app->factory->getUser();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $questionList = $question->getAllQuestion();
    $app->render('admin/questionList.twig', array('user' => $user_info, 'questionList' => $questionList));
});


/**
 * 問題修正画面
 */
$app->get('/admin/modifyQuestion/:id' ,function ($id) use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/User.php';
    require_once MODELS_DIR . '/Question.php';


    $session = $app->factory->getSession();
    $user = $app->factory->getUser();
    $question = $app->factory->getQuestion();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $sessionid = $session->id();
    $session->set('sessionidQ', $sessionid);
    $question_item = $question->getQuestionByID($id);
    $app->render('admin/modifyQuestion.twig', array('user' => $user_info, 'session' => $sessionid, 'question' => $question_item));
});


/**
 * 問題修正確認画面
 */
$app->post('/admin/modifyQuestion/confirm' ,function () use ($app) {
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
        $app->render('admin/modifyconfirm.twig', array('user' => $user_info, 'question' => $params, 'session' =>$params['sessionid']));
        exit();
    } else {
        $errors = $form_validator->getErrors();
    }
    $app->render("admin/postQuestion.twig", array('user' => $user_info, 'errors' => $errors));
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
    $app->render('admin/modifyQuestion.twig', array('user' => $user_info, 'errors' => $errors));
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


/**
 * 問題修正完了画面
 */
$app->post('/admin/modifyQuestion/posted' ,function () use ($app) {
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
            $question->updateQuestion(
                $params['questionid'],
                $params['title'],
                $params['content']
            );
            $session->remove('posted');
            $session->remove('sessionid');
        } catch (PDOException $e){
            $app->error('登録に失敗しました。');
        }
    }
    $app->render('admin/modifyPosted.twig', array('user' => $user_info));
});


