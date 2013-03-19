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
    $app->render('question/questionList.twig',array('errors' => $errors, 'questionList' => $questionList));
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
        $app->error('その問題は存在しません'); 
    }
    $app->render('question/questionForm.twig',array('errors' => $errors, 'question' => $question_item));
});




