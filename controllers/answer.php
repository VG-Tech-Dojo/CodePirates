<?php

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
    $answerInfo = $answer->getAnswerByAnsId($a_id);
    $questionInfo = $question->getQuestionByID($answerInfo['q_id']);
    $answererInfo = $user->getUserById($answerInfo['u_id']);
    $app->render('answer/answer.twig', array('user' => $user_info, 'answer' => $answerInfo ,'question' => $questionInfo, 'answerer' => $answererInfo));
});


$app->get('/answerlist/:id', 'authorized' ,  function ($q_id) use ($app) {
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
            for($i = 0; $i < count($answerInfos); $i++){
                $userInfo = $user->getUserById($answerInfos[$i]['u_id']);
                $answerInfos[$i]['u_name'] = $userInfo['name'];
            }
        }
    }

    $app->render('answer/answerlist.twig', array('user' => $user_info, 'answers' => $answerInfos ,'question' => $questionInfo));
});
