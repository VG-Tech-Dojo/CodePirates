<?php

$app->get('/answer',  function () use ($app) {
    $app->render('answer/answer.twig');
});


$app->post('/answer',  function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Answer.php';


    $params = $app->request()->post();
    $answer = $app->factory->getAnswer();

    $app->render('answer/answer.twig', array('params' => $params));
});
