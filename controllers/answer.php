<?php

$app->get('/answer',  function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Answer.php';


    $session = $app->factory->getSession();
    $answer = $app->factory->getAnswer();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $answerInfo = $answer->getAnswerByAnsId(1);
    $app->render('answer/answer.twig', array('user' => $user_info, 'answer' => $answerInfo));
});


