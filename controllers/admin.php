<?php

/**
 * 問題投稿画面
 */
$app->get('/admin/postQuestion' ,function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/User.php';
    require_once MODELS_DIR . '/Difficulty.php';


    $session = $app->factory->getSession();
    $user = $app->factory->getUser();
    $difficulty = $app->factory->getDifficulty();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $sessionid = $session->id();
    $difficulty_list = $difficulty->getDifficulty();
    $app->render('admin/postQuestion.twig', array('user' => $user_info, 'session' => $sessionid, 'difficulty' => $difficulty_list));
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
    $question_list = $question->getAllQuestion();
    $app->render('admin/questionList.twig', array('user' => $user_info, 'questionList' => $question_list));
});


/**
 * 問題修正画面
 */
$app->get('/admin/modifyQuestion/:id' ,function ($id) use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/User.php';
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/Difficulty.php';


    $session = $app->factory->getSession();
    $user = $app->factory->getUser();
    $question = $app->factory->getQuestion();
    $difficulty = $app->factory->getDifficulty();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $sessionid = $session->id();
    $session->set('sessionidQ', $sessionid);
    $difficulty_list = $difficulty->getDifficulty();
    $question_item = $question->getQuestionwithID($id);
    $app->render('admin/modifyQuestion.twig', array('user' => $user_info, 'session' => $sessionid, 'question' => $question_item, 'difficulty' => $difficulty_list));
});


/**
 * 問題修正確認画面
 */
$app->post('/admin/modifyQuestion/confirm' ,function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once LIB_DIR . '/FormValidator/AdminPostQuestionFormValidator.php';
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/Difficulty.php';


    $session = $app->factory->getSession();
    $form_validator = $app->factory->getFormValidator_AdminPostQuestionFormValidator();
    $params = $app->request()->post();
    $difficulty = $app->factory->getDifficulty();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $session->set('posted',true);
    $difficulty_list = $difficulty->getDifficulty();
    if($form_validator->run($params)){
        if($_FILES['inputfile']['tmp_name'] !== ""){
            $filename = $_FILES['inputfile']['name'];
            $tempname = "temp";
            $updir="./../public_html/inputs/";
            move_uploaded_file($_FILES['inputfile']['tmp_name'],$updir.$tempname);
            $app->render('admin/modifyconfirm.twig', array('user' => $user_info, 'inputfile' =>$filename, 'question' => $params, 'session' =>$params['sessionid'], 'difficulty' => $difficulty_list[$params['difficulty'] - 1]));
            exit();
        }else{
            $app->render('admin/modifyconfirm.twig', array('user' => $user_info, 'question' => $params, 'session' =>$params['sessionid'], 'difficulty' => $difficulty_list[$params['difficulty'] - 1]));
            exit();
        }
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
    require_once MODELS_DIR . '/Difficulty.php';


    $session = $app->factory->getSession();
    $form_validator = $app->factory->getFormValidator_AdminPostQuestionFormValidator();
    $params = $app->request()->post();
    $difficulty = $app->factory->getDifficulty();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $session->set('posted',true);
    $difficulty_list = $difficulty->getDifficulty();
    if($form_validator->run($params)){
        if($_FILES['inputfile']['tmp_name'] !== ""){
            $filename = $_FILES['inputfile']['name'];
            $tempname = "temp";
            $updir="./../public_html/inputs/";
            move_uploaded_file($_FILES['inputfile']['tmp_name'],$updir.$tempname);
            $app->render('admin/confirm.twig', array('user' => $user_info, 'inputfile' =>$filename, 'question' => $params, 'session' =>$params['sessionid'], 'difficulty' => $difficulty_list[$params['difficulty'] - 1]));
            exit();
        }else{
            $app->render('admin/confirm.twig', array('user' => $user_info, 'question' => $params, 'session' =>$params['sessionid'], 'difficulty' => $difficulty_list[$params['difficulty'] - 1]));
            exit();
        }
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
            $updir="./../public_html/inputs/";
            if(file_exists($updir."temp") && $params['inputfile'] !== ""){
                rename($updir."temp", $updir.$params['inputfile']);
                $question->register(
                    $params['title'],
                    $params['content'],
                    $params['difficulty'],
                    $params['inputfile']
                );
            }else{
                $question->register(
                    $params['title'],
                    $params['content'],
                    $params['difficulty']
                );
            }
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
            $updir="./../public_html/inputs/";
            if($params['inputoldfile'] !== "" && isset($params['inputfile'])){
                $oldfile = $updir.$params['inputoldfile'];
                if(file_exists($oldfile)){
                    unlink($oldfile);
                }
            }
            if(file_exists($updir."temp") && isset($params['inputfile'])){
                rename($updir."temp", $updir.$params['inputfile']);
                $question->updateQuestion(
                    $params['questionid'],
                    $params['title'],
                    $params['content'],
                    $params['difficulty'],
                    $params['inputfile']
                );
            }else{
                $question->updateQuestion(
                    $params['questionid'],
                    $params['title'],
                    $params['content'],
                    $params['difficulty'],
                    $params['inputoldfile']
                );
            }

            $session->remove('posted');
            $session->remove('sessionid');
        } catch (PDOException $e){
            $app->error('登録に失敗しました。');
        }
    }
    $app->render('admin/modifyPosted.twig', array('user' => $user_info));
});


