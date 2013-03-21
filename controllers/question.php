<?php

/**
 * 質問の一覧表示 
 */
$app->get('/question', 'authorized', function () use ($app) {
    require_once MODELS_DIR . '/Question.php';

    $question = $app->factory->getQuestion();
    $errors = array();
    
    try {
        $questionList = $question->getAllQuestion();
    } catch (PDOException $e){
        echo $e->getMessage();
        $app->error('何も質問がありません'); 
    }
    $app->render('question/questionList.twig', array('errors' => $errors, 'questionList' => $questionList));
});


/**
 * 問題と解答フォームの表示
 */
$app->get('/question/:id', 'authorized', function ($id) use ($app) {
    require_once MODELS_DIR . '/Question.php';

    $question = $app->factory->getQuestion();
    $errors = array();
    
    try {
        $question_item = $question->getQuestionByID($id);
    } catch (PDOException $e){
        echo $e->getMessage();
        $app->error('その問題は存在しません'); 
    }
    $app->render('question/questionForm.twig', array('errors' => $errors, 'question' => $question_item));
});



/**
 * 問題に回答した後の確認画面
 */
$app->post('/question/confarm', 'authorized', function () use ($app) {
    require_once LIB_DIR . '/FormValidator/CodeFormValidator.php';
    require_once MODELS_DIR . '/Answer.php';

    $params = $app->request()->post();
    $errors = array();
    $form_validator = $app->factory->getFormValidator_CodeFormValidator();

    if ($form_validator->run($params)) {
        $confarmcode = $params['code'];
    } else {
        $confarmcode = '';
        $errors = $form_validator->getErrors();
    }
    $app->render('question/confarm.twig', array('errors' => $errors, 'code' => $confarmcode, 'question_num' => $params['question_num']));
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
    $app->render('question/register.twig', array('errors' => $errors, 'question_num' => $params['question_num']));
});

