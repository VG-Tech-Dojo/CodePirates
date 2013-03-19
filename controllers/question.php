<?php

/**
 * 質問の一覧表示 
 */
$app->get('/question/list', 'noauthorized', function () use ($app) {
    require_once MODELS_DIR . '/Question.php';

    $question = $app->factory->getQuestion();
    $errors = array();
    
    try {
        $questionList = $question->getAllQuestion();
    } catch (PDOException $e){
        $app->error('何も質問がありません'); 
    }
    $app->render('question/questionList.twig',array('errors' => $errors, 'questionList' => $questionList));
});
