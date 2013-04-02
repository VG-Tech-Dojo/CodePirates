<?php

/**
 * 質問の一覧表示 
 */
$app->get('/question', 'authorized', function () use ($app) {
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/Answer.php';
    require_once LIB_DIR . '/Session.php';

    $question = $app->factory->getQuestion();
    $answer = $app->factory->getAnswer();
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
        } else {
        }
    } catch (PDOException $e){
        echo $e->getMessage();
        $app->error('おかしいのでリロードしてください。'); 
    }
    $answerInfo = $answer->getAnswerByUserId($user_info['id']);
    $answeredIdForUser = array();
    for($i = 0; $i < count($answerInfo); $i++){
        $answeredIdForUser[] = $answerInfo[$i]['q_id'];
    }
    $answeredIdForUser = array_unique($answeredIdForUser);
    for($i = 0 ; $i < count($questionList); $i++){
        if(in_array($questionList[$i]['id'],$answeredIdForUser)){
            $questionList[$i]['answered'] = true;
        }else{
            $questionList[$i]['answered'] = false;
        }
    }
    $app->render('question/questionList.twig', array('user' => $user_info, 'errors' => $errors, 'questionList' => $questionList));
});


/**
 * 問題と解答フォームの表示
 */
$app->get('/question/:id', 'authorized', function ($id) use ($app) {
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/Answer.php';
    require_once LIB_DIR . '/Session.php';

    $question = $app->factory->getQuestion();
    $answer = $app->factory->getAnswer();
    $session = $app->factory->getSession();
    $errors = array();

    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $sessionid = $session->id();
    $session->set('sessionidQ', $sessionid);
    try {
        if (($question_item = $question->getQuestionByID($id)) == null){
            $app->error('その問題は存在しません');
        } else {
            $answer_user_num =$answer->getanswerpeoplenumbyquestionid($question_item['id']);
        }    
    } catch (PDOException $e){
        echo $e->getMessage();
        $app->error('おかしいのでリロードしてください'); 
    }
    $app->render('question/questionForm.twig', array('user' => $user_info, 'errors' => $errors, 'question' => $question_item, 'session' => $sessionid, 'answer_user_num' => $answer_user_num));
});

/**
 * confirmから戻ってくるときの解答フォームの表示
 */
$app->put('/question/:id', 'authorized', function ($id) use ($app) {
    require_once MODELS_DIR . '/Question.php';
    require_once LIB_DIR . '/Session.php';

    $question = $app->factory->getQuestion();
    $session = $app->factory->getSession();
    $errors = array();
    $old_code = '';

    $params = $app->request()->post();
    $old_code = $params['code'];
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    try {
        if (($question_item = $question->getQuestionByID($id)) == null){
            $app->error('その問題は存在しません');
        }
    } catch (PDOException $e){
        echo $e->getMessage();
        $app->error('おかしいのでリロードしてください'); 
    }
    $app->render('question/questionForm.twig', array('user' => $user_info, 'errors' => $errors, 'question' => $question_item, 'old_code' => $old_code));
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
    $app->render('question/confirm.twig', array('errors' => $errors, 'code' => $confarmcode, 'question_num' => $params['question_num'], 'user' => $user_info, 'langtype' => $params['lang'], 'session' => $params['sessionid']));
});


/*
 * 問題に回答した後の確認画面（登録処理）
 */
$app->post('/question/save', 'authorized', function () use ($app) {
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
    if($user_info!=null && $session->get('sessionidQ') === $params['sessionid']){
        try {
            $answer->register(
                $user_info['id'],
                $params['question_num'],
                $params['code'],
                $params['lang']
            );
            $session->set('question_id', $params['question_num']);
            $session->remove('sessionidQ');
            
            $app->redirect('/question_recieved');
        } catch (PDOException $e) {
            $app->error('登録に失敗しました。');
        }
    }
});

/*
 * 問題に回答した後の確認画面
 */
$app->get('/question_recieved', 'authorized', function () use ($app) {
    require_once LIB_DIR . '/Session.php';

    $session = $app->factory->getSession();
    
    $errors = array();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
    } else {
        $app->error('ろぐいんしてください');
    }
    if ($session->get('question_id')) {
        $question_num = $session->get('question_id');
        $session->remove('question_id');    
    } else {
        $app->redirect('/question');
    }
    
    $app->render('question/register.twig', array('question_num' => $question_num, 'user' => $user_info));
});

