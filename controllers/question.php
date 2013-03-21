<?php

/**
 * 質問の一覧表示 
 */
$app->get('/question', 'authorized', function () use ($app) {
    require_once MODELS_DIR . '/Question.php';
    require_once LIB_DIR . '/Session.php';

    $question = $app->factory->getQuestion();
    $session = $app->factory->getSession();
    $errors = array();

    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }

    try {
        if (($questionList = $question->getAllQuestion()) == null) {
            $errors = '質問がありません';
        }
    } catch (PDOException $e){
        echo $e->getMessage();
        $app->error('おかしいのでリロードしてください。'); 
    }
    $app->render('question/questionList.twig', array('user' => $user_info, 'errors' => $errors, 'questionList' => $questionList));
});


/**
 * 問題と解答フォームの表示
 */
$app->get('/question/:id', 'authorized', function ($id) use ($app) {
    require_once MODELS_DIR . '/Question.php';
    require_once LIB_DIR . '/Session.php';

    $question = $app->factory->getQuestion();
    $session = $app->factory->getSession();
    $errors = array();
    
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    try {
        $question_item = $question->getQuestionByID($id);
    } catch (PDOException $e){
        echo $e->getMessage();
        $app->error('おかしいのでリロードしてください'); 
    }
    $app->render('question/questionForm.twig', array('user' => $user_info, 'errors' => $errors, 'question' => $question_item));
});



/**
 * 問題に回答した後の確認画面
 */
$app->post('/question/confirm', 'authorized', function () use ($app) {
    require_once LIB_DIR . '/FormValidator/CodeFormValidator.php';
    require_once MODELS_DIR . '/Answer.php';
    require_once LIB_DIR . '/Session.php';

    $params = $app->request()->post();
    $session = $app->factory->getSession();
    $errors = array();
    $form_validator = $app->factory->getFormValidator_CodeFormValidator();

    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    if ($form_validator->run($params)) {
        $confarmcode = $params['code'];
    } else {
        $confarmcode = '';
        $errors = $form_validator->getErrors();
    }
    $app->render('question/confarm.twig', array('errors' => $errors, 'code' => $confarmcode, 'question_num' => $params['question_num'], 'user' => $user_info));
});


/*
 * 問題に回答した後の確認画面（登録処理）
 */
$app->post('/question/recieved', 'authorized', function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Answer.php';

    $session = $app->factory->getSession();
    $answer = $app->factory->getAnswer();
    
    $errors = array();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
    } else {
        $app->error('ろぐいんしてください');
    }
    $params = $app->request()->post();
    if($user_info!=null){
        try {
            $answer->register(
            $user_info['id'],
            $params['question_num'],
            $params['code']
            );
        } catch (PDOException $e) {
            $app->error('登録に失敗しました。');
        }
    }
    $app->render('question/register.twig', array('errors' => $errors, 'question_num' => $params['question_num'], 'user' => $user_info));
});

