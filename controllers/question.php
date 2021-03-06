<?php

/**
 * コードの行数カウント 
 */
$app->get('/countLine', function () use ($app) {
    require_once MODELS_DIR . '/Answer.php';
    $answer = $app->factory->getAnswer();
    $answer = $app->factory->getAnswer();
    print("コードの行数をカウントします");
    echo"<br>";
    echo"<br>";
    $answer_info = $answer->getAllAnswer();
    foreach($answer_info as $answer_item){
        print($answer_item['content']);
        echo"<br>";
        print(substr_count($answer_item['content'], "\n"));
        echo"<br>";
        $answer->update(
            $answer_item['id'],
            $answer_item['u_id'],
            $answer_item['q_id'],
            $answer_item['content'],
            $answer_item['lang'],
            substr_count($answer_item['content'], "\n")
        );
    }
});


/**
 * 質問の一覧表示 
 */
$app->get('/question', 'authorized', function () use ($app) {
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/Answer.php';
    require_once MODELS_DIR . '/Difficulty.php';
    require_once LIB_DIR . '/Session.php';

    $question = $app->factory->getQuestion();
    $answer = $app->factory->getAnswer();
    $difficulty = $app->factory->getDifficulty();
    $session = $app->factory->getSession();
    $errors = array();

    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }

    try {
        if (($questionList = $question->getAllQuestionOrderByDiff()) == null) {
            $errors = '質問がありません';
        } else {
        }
    } catch (PDOException $e){
        echo $e->getMessage();
        $app->error('おかしいのでリロードしてください。'); 
    }
    $difficulties = $difficulty->getDifficulty();
    $difficultyLevel = array();
    for($i = 0; $i < count($difficulties); $i++ ){
        $difficultyLevel[$difficulties[$i]['id']] = $difficulties[$i]['content'];
    }
    for($i = 0; $i < count($questionList); $i++ ){
        $questionList[$i]['difficultyNum'] = $questionList[$i]['difficulty'];
        $questionList[$i]['difficulty'] = $difficultyLevel[$questionList[$i]['difficulty']];
        $questionList[$i]['answernum'] = $answer->getAnsweredPeopleByQuestionId($questionList[$i]['id']);
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
    require_once MODELS_DIR . '/Answer.php';

    $question = $app->factory->getQuestion();
    $answer = $app->factory->getAnswer();
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
    $app->render('question/questionForm.twig', array('user' => $user_info, 'errors' => $errors, 'question' => $question_item, 'old_code' => $old_code, 'answer_user_num' => $answer_user_num, 'session' => $sessionid));
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
 * 問題に回答した後の確認画面（登録処理＆リダイレクト）
 */
$app->post('/question/save', 'authorized', function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Answer.php';
    require_once MODELS_DIR . '/Question.php';

    $session = $app->factory->getSession();
    $answer = $app->factory->getAnswer();
    $question = $app->factory->getQuestion();
    
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
            if($question->getQuestionByID($params['question_num']) == null){
                $app->error("そのような問題は存在しません");
            }
            $answer->register(
                $user_info['id'],
                $params['question_num'],
                $params['code'],
                $params['lang'],
                substr_count($params['code'], "\n")
            );
            $session->set('question_id', $params['question_num']);
            $session->remove('sessionidQ');

            $app->flash('confarm_msg', '登録完了しました。みんなの回答を見てみましょう！');
            
            $app->redirect('/answerlist/question/'.$params['question_num']);
        } catch (PDOException $e) {
            $app->error('登録に失敗しました。');
        }
    } else {
        $app->redirect('/question/'.$params['question_num']);
    }
});
