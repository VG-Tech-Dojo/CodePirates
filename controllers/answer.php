<?php

$app->get('/answer',  function () use ($app) {
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
    $answerInfo = $answer->getAnswerByAnsId(1);
    $questionInfo = $question->getQuestionByID($answerInfo['q_id']);
    $answererInfo = $user->getUserById($answerInfo['u_id']);
    $app->render('answer/answer.twig', array('user' => $user_info, 'answer' => $answerInfo ,'question' => $questionInfo, 'answerer' => $answererInfo));
});


