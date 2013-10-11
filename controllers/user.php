<?php

/**
 * ログイン画面
 */
$app->get('/user/login', 'noauthorized', function () use ($app) {
    $app->render('user/login.twig');
});

/**
 * ログイン画面（ログイン処理）
 */
$app->post('/user/login', 'noauthorized', function () use ($app) {
    require_once LIB_DIR . '/FormValidator/LoginFormValidator.php';
    require_once LIB_DIR . '/Session.php';
    require_once LIB_DIR . '/PasswordUtil.php';
    require_once MODELS_DIR . '/User.php';

    $errors = array();
    $params = $app->request()->post();
    $form_validator = $app->factory->getFormValidator_LoginFormValidator();

    if ($form_validator->run($params)) {
        $user = $app->factory->getUser();
        if ($user->loadByName($params['user_name'])
            && $user->password === PasswordUtil::hashPassword($params['password'], $user->salt)) {
            $session = $app->factory->getSession();
            $session->regenerate();
            $session->set('user_id', $user->id);
            $session->set('user_name', $user->name);
            $app->redirect('/question');
        } else {
            $errors['user_name'] = 'ユーザー名とパスワードの組み合わせが間違っています';
        }
    } else {
        $errors = $form_validator->getErrors();
    }
    $app->render('user/login.twig', array('errors' => $errors, 'params' => $params));
});

/**
 * ログアウト画面
 */
$app->get('/user/logout/', 'authorized', function () use ($app) {
    require_once LIB_DIR . '/Session.php';

    $session = $app->factory->getSession();
    if ($session->get('user_id')) {
        $session->destroy();
        $app->render('user/logout.twig');
    }
});

/**
 * ユーザー新規登録画面
 */
$app->get('/user/register', 'noauthorized', function () use ($app) {
    $app->render('user/register.twig');
});

/**
 * ユーザー新規登録画面（登録処理）
 */
$app->post('/user/register', 'noauthorized', function () use ($app) {
    require_once LIB_DIR . '/FormValidator/RegisterMemberFormValidator.php';
    require_once LIB_DIR . '/PasswordUtil.php';
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/User.php';

    $params = $app->request()->post();
    $errors = array();
    $form_validator = $app->factory->getFormValidator_RegisterMemberFormValidator();

    if ($form_validator->run($params)) {
        $user = $app->factory->getUser();
        try {
            if ($user->isSameName($params['user_name'])) {
                $errors['user_name'] = '既に登録されているユーザー名です';
            }
            else if ($user->isSameEmail($params['email'])) {
                $errors['email'] = '既に登録されているアドレスです';
            } else {
                $salt = PasswordUtil::generateSalt();
                $user->register(
                    $params['user_name'],
                    PasswordUtil::hashPassword($params['password'], $salt),
                    $salt,
                    $params['email']
                );

                $user->loadByName($params['user_name']);
                $session = $app->factory->getSession();
                $session->regenerate();
                $session->set('user_id', $user->id);
                $session->set('user_name', $user->name);
                $app->redirect('/question');
            }
        } catch (PDOException $e) {
            $app->error('登録に失敗しました。');
        }
    } else {
        $errors = $form_validator->getErrors();
    }
    $app->render('user/register.twig', array('errors' => $errors, 'params' => $params));
});

/**
 * マイページ
 */
$app->get('/user/:id', 'authorized', function ($id) use ($app) {
    require_once MODELS_DIR . '/Answer.php';
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/User.php';
    require_once MODELS_DIR . '/Good.php';
    require_once LIB_DIR . '/Session.php';

    $session = $app->factory->getSession();
    $user = $app->factory->getUser();
    $answer = $app->factory->getAnswer();
    $like = $app->factory->getGood();
    $question = $app->factory->getQuestion();
    $user = $app->factory->getUser();

    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    if(!$mypage_user = $user->getUserById($id)){
        $app->error("ユーザーが存在しません");
    }
    $answerList = $answer->getAnswerByUserIdOfQuestionNum($mypage_user['id']);
    $questionList_notsorted = $question->getAllQuestion();
    $likeList = $like->getLikeFromUID($mypage_user['id']);
    $user_answer = array();
    $questionList = array();
    foreach($questionList_notsorted as  $question_item){
        $questionList[$question_item['id']] = $question_item;
    }
    foreach($answerList as $key => $answer_item){
        $user_answer[$questionList[$answer_item['q_id']]['title']][] = $answer_item;
    }
    foreach($user_answer as $key => $user_answer_item){
        $user_answer_item['key'] = $key;
        $user_answer[$key] = $user_answer_item;
    }
    $likeAnswer = array();
    foreach($likeList as $like_item){
        $likeAnswer[] = $answer->getAnswerByAnsIDWithUName($like_item['a_id']);
    }
    $titledLikeAnswer = array();
    foreach($likeAnswer as $key => $likeAnswer_item){
        $titledLikeAnswer[$questionList[$likeAnswer_item['q_id']]['title']][] = $likeAnswer_item;
    }
    foreach($titledLikeAnswer as $key => $titledLikeAnswer_item){
        $titledLikeAnswer_item['key'] = $key;
        $titledLikeAnswer[$key] = $titledLikeAnswer_item;
    }
    $app->render('user/mypqge.twig', array('user' => $user_info, 'mypage_user' => $mypage_user, 'answer' => $user_answer, 'likeAnswer' => $titledLikeAnswer));
});

