<?php

/**
 * 回答のコード表示画面
 */
$app->get('/answer/:a_id', 'authorized' ,function ($a_id) use ($app) {
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
    if (($answerInfo = $answer->getAnswerByAnsId($a_id)) == null) {
        $app->error('その回答は存在しません');
    } else {
        if (($questionInfo = $question->getQuestionByID($answerInfo['q_id'])) == null) {
            $app->error('問題が存在しません');
        }
        if (($answererInfo = $user->getUserById($answerInfo['u_id'])) == null){
            $app->error('ユーザーが存在しません');
        }
    }

    if (!$user->canSee($user_info['id'], $answerInfo['q_id'])){
        $app->error("先にこの問題に回答してください");
    }
    $app->render('answer/answer.twig', array('user' => $user_info, 'answer' => $answerInfo ,'question' => $questionInfo, 'answerer' => $answererInfo));
});


/**
 * ある問題に対する回答一覧画面
 */
$app->get('/answerlist/question/:id', 'authorized' ,  function ($q_id) use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Answer.php';
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/User.php';


    $session = $app->factory->getSession();
    $answer = $app->factory->getAnswer();
    $question = $app->factory->getQuestion();
    $user = $app->factory->getUser();
    $user_info = array();
    $answerInfos = array();

    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }

    if (($questionInfo = $question->getQuestionByID($q_id)) == null) { 
        $app->error('問題が存在しません');
    } else {
        if (($answerInfos = $answer->getAnswerByQuesId($questionInfo['id'])) == null) {
           $app->error('回答がありません'); 
        } else {
            if (!$user->canSee($user_info['id'], $q_id)){
                $app->error("先にこの問題に回答してください");
            }

            $answererId = array();
            $answererName = array();
            $answererInfo = array();
            for($i = 0; $i < count($answerInfos); $i++){
                $userInfo = $user->getUserById($answerInfos[$i]['u_id']);
                $answererId[] = $answerInfos[$i]['u_id'];
                $answererName[] = $userInfo['name'];
            }
            $answererId = array_merge(array_unique($answererId));
            $answererName = array_merge(array_unique($answererName));
            for($i = 0; $i < count($answererId); $i++){
                $answererInfo[$i]['name'] = $answererName[$i];
                $answererInfo[$i]['id'] = $answererId[$i];
            }
        }
    }

    //$flash = $app->view()->getData('flash');
    //$info = $flash['error'];
    //print_r($_SESSION);
    //print_r($flash);
    $app->render('answer/answerlist.twig', array('user' => $user_info, 'answerer' => $answererInfo, 'question' => $questionInfo));
});


/**
 * ある問題に対する回答一覧画面
 */
$app->get('/answerlist/user/:u_id/question/:q_id', 'authorized' ,  function ($u_id,$q_id) use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Answer.php';
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/User.php';


    $session = $app->factory->getSession();
    $answer = $app->factory->getAnswer();
    $question = $app->factory->getQuestion();
    $user = $app->factory->getUser();
    $user_info = array();
    $answerInfos = array();

    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }

    if (($questionInfo = $question->getQuestionByID($q_id)) == null) { 
        $app->error('問題が存在しません');
    } else {
        if (($answerInfos = $answer->getAnswerByUserIdQuestionId($u_id,$q_id)) == null) {
           $app->error('回答がありません'); 
        } else {
            if (!$user->canSee($user_info['id'], $q_id)){
                $app->error("先にこの問題に回答してください");
            }

            for($i = 0; $i < 1; $i++){
                $userInfo = $user->getUserById($answerInfos[$i]['u_id']);
                $answerInfos[$i]['u_name'] = $userInfo['name'];
            }
        }
    }

    $app->render('answer/answerlistForUser.twig', array('user' => $user_info, 'answers' => $answerInfos ,'question' => $questionInfo, 'answererName' => $answerInfos[0]['u_name']));
});


