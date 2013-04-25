<?php

/**
 * 回答のコード表示画面
 */
$app->get('/answer/:a_id', 'authorized' ,function ($a_id) use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Answer.php';
    require_once MODELS_DIR . '/Good.php';
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/User.php';
    require_once MODELS_DIR . '/Comment.php';
    require_once MODELS_DIR . '/Footmark.php';


    $session = $app->factory->getSession();
    $answer = $app->factory->getAnswer();
    $like = $app->factory->getGood();
    $question = $app->factory->getQuestion();
    $user = $app->factory->getUser();
    $comment = $app->factory->getComment();
    $footmark = $app->factory->getFootmark();

    $user_info = array();
    
    $sessionid = $session->id();
    $session->set('sessionidA', $sessionid);
    
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

    $Canseelike = $like->getLikeFromAandUID($user_info['id'], $a_id);
    if(isset($Canseelike[0])){
        $like = false;
    }else{
        $like = true;
    }

    if (!$user->canSee($user_info['id'], $answerInfo['q_id'])){
        $app->error("先にこの問題に回答してください");
    }
    
    if (!($answer_comment = $comment->getCommentByAnsId($a_id))) {
        $answer_comment = "";    
    }
    $footmark->register(
        $user_info['id'],
        $a_id
    );
    $app->render('answer/answer.twig', array('user' => $user_info,'slimFlash' => $_SESSION['slim.flash'], 'answer' => $answerInfo ,'question' => $questionInfo, 'answerer' => $answererInfo, 'comment' => $answer_comment, 'sessionid' => $sessionid, 'like' => $like));
});


/**
 * いいね投稿
 */
$app->get('/answerlike/:a_id', 'authorized' ,function ($a_id) use ($app) {
    require_once MODELS_DIR . '/Good.php';
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Answer.php';

    $session = $app->factory->getSession();
    $answer = $app->factory->getAnswer();
    $like = $app->factory->getGood();

    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $answerinfo = $answer->getAnswerByAnsId($a_id);
    if($answerinfo['u_id'] == $user_info['id']){
        $app->error('不正な処理です');
    }
    try{
        $like->registLike(
            $user_info['id'],
            $a_id
            );
        $app->flash('like','いいねは投稿されました');
    }catch (PDOException $e){
        $app->error('不正な処理です');
    }
     
    $app->redirect("/answer/$a_id");
});


/**
 * 回答削除
 */
$app->post('/answerdelete/:a_id', 'authorized' ,function ($a_id) use ($app) {
    require_once MODELS_DIR . '/Good.php';
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Answer.php';
    require_once MODELS_DIR . '/Comment.php';

    $session = $app->factory->getSession();
    $answer = $app->factory->getAnswer();
    $like = $app->factory->getGood();
    $comment = $app->factory->getComment();
    $params  = $app->request()->post();

    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $answerinfo = $answer->getAnswerByAnsId($a_id);
    if($answerinfo['u_id'] !== $user_info['id']){
        $app->error('不正な処理です');
    }
    if($params['session'] === $session->get('sessionidA')){
        try{
            $answer->deleteAnswerByID($a_id);
            $like->deleteLikeFromAID($a_id);
            $comment->deleteCommentFromAID($a_id);
            $session->remove('sessionidA');
            $app->flash('del_ans','回答は削除されました');
        }catch (PDOException $e){
            $e->getMessage();
            $app->error('不正な処理です');
        }
    }
     
    $app->redirect("/answerlist/question/$answerinfo[q_id]");
});


/**
 * ある問題に対する回答一覧画面
 */
$app->get('/answerlist/question/:id', 'authorized' ,  function ($q_id) use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Answer.php';
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/User.php';
    require_once MODELS_DIR . '/Comment.php';
    require_once MODELS_DIR . '/Good.php';


    $session = $app->factory->getSession();
    $answer = $app->factory->getAnswer();
    $question = $app->factory->getQuestion();
    $comment = $app->factory->getComment();
    $like = $app->factory->getGood();
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
            $userInfo = array();
            $commentsinfo = $comment->getAllComments();
            $countForComment = array();
            $likeInfo = $like->getAllLike();
            $countForLike = array();
            for($i = 0; $i < count($commentsinfo); $i++){
                $countForComment[] = $commentsinfo[$i]['a_id'];    
            }
            $countForComment = array_count_values($countForComment);
            for($i = 0; $i < count($likeInfo); $i++){
                $countForLike[] = $likeInfo[$i]['a_id'];    
            }
            $countForLike = array_count_values($countForLike);

            foreach($answerInfos as $answerInfo){
                $user_data[] = $answerInfo['u_id'];
            }
            foreach(array_unique($user_data) as $answerd_user){
                $userInfo = $user->getUserById($answerd_user);
                $answerdata[$answerd_user]['name'] = $userInfo['name'];
                $answerdata[$answerd_user]['answer'] = $answer->getAnswerByUserIdQuestionId($answerd_user,$q_id);
                for($i = 0; $i < count($answerdata[$answerd_user]['answer']); $i++){
                    if(array_key_exists($answerdata[$answerd_user]['answer'][$i]['id'], $countForComment)){
                        $answerdata[$answerd_user]['answer'][$i]['comment'] = $countForComment[$answerdata[$answerd_user]['answer'][$i]['id']];
                    }else{
                        $answerdata[$answerd_user]['answer'][$i]['comment'] = 0;
                    }
                    if(array_key_exists($answerdata[$answerd_user]['answer'][$i]['id'], $countForLike)){
                        $answerdata[$answerd_user]['answer'][$i]['like'] = $countForLike[$answerdata[$answerd_user]['answer'][$i]['id']];
                    }else{
                        $answerdata[$answerd_user]['answer'][$i]['like'] = 0;
                    }
                }
            }
        }
    }
    $flash_msg = $_SESSION['slim.flash'];
    $app->render('answer/answerlist.twig', array('user' => $user_info, 'answer_data' => $answerdata, 'question' => $questionInfo, 'flash_msg' => $flash_msg));
});



/**
 * 回答修正時の問題と解答フォームの表示
 */
$app->get('/modify/answer/code/:id', 'authorized', function ($a_id) use ($app) {
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
    $answerInfo = $answer->getAnswerByAnsId($a_id);
    try {
        if (($question_item = $question->getQuestionByID($answerInfo['q_id'])) == null){
            $app->error('その問題は存在しません');
        } else {
            $answer_user_num =$answer->getanswerpeoplenumbyquestionid($question_item['id']);
        }    
    } catch (PDOException $e){
        echo $e->getMessage();
        $app->error('おかしいのでリロードしてください'); 
    }
    if($answerInfo['u_id'] !== $user_info['id']){
        $app->redirect('/');
    }
    $app->render('answer/modifyAnswerForm.twig', array('user' => $user_info, 'errors' => $errors, 'question' => $question_item, 'session' => $sessionid, 'answer_user_num' => $answer_user_num, 'answerInfo' => $answerInfo));
});

/**
 * confirmから戻ってくるときの解答フォームの表示
 */
$app->put('/modify/answer/code/:id', 'authorized', function ($a_id) use ($app) {
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
    $answerInfo = $answer->getAnswerByAnsId($a_id);
    try {
        if (($question_item = $question->getQuestionByID($answerInfo['q_id'])) == null){
            $app->error('その問題は存在しません');
        } else {
            $answer_user_num =$answer->getanswerpeoplenumbyquestionid($question_item['id']);
        }
    } catch (PDOException $e){
        echo $e->getMessage();
        $app->error('おかしいのでリロードしてください'); 
    }
    if($answerInfo['u_id'] !== $user_info['id']){
        $app->redirect('/');
    }
    $app->render('answer/modifyAnswerForm.twig', array('user' => $user_info, 'errors' => $errors, 'question' => $question_item, 'old_code' => $old_code, 'answer_user_num' => $answer_user_num, 'session' => $sessionid, 'answerInfo' => $answerInfo));
});

/**
 * 回答を修正した後の確認画面
 */
$app->post('/modify/answer/confirm', 'authorized', function () use ($app) {
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
    $session->set('posted',true);
    if ($form_validator->run($params)) {
        $confarmcode = $params['code'];
    } else {
        $confarmcode = '';
        $errors = $form_validator->getErrors();
    }
    if($params['answer_uId'] !== $user_info['id']){
        $app->redirect('/');
    }
    $app->render('answer/modifyConfirm.twig', array('errors' => $errors, 'code' => $confarmcode, 'question_num' => $params['question_num'], 'user' => $user_info, 'langtype' => $params['lang'], 'session' => $params['sessionid'], 'ans_num' => $params['answer_num'], 'answer_uId' => $params['answer_uId']));
});

/**
 * 回答を修正した後の登録処理
 */
$app->post('/modify/answer/result', 'authorized', function () use ($app) {
    require_once MODELS_DIR . '/Answer.php';
    require_once LIB_DIR . '/Session.php';

    $params = $app->request()->post();
    $session = $app->factory->getSession();
    $answer = $app->factory->getAnswer();
    $errors = array();

    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    if($params['answer_uId'] !== $user_info['id']){
        $app->redirect('/');
    }
    if($session->get('posted') && $params['sessionid'] === $session->get('sessionidQ')){
        try {
            $answer->update(
                $params['ans_num'],
                $user_info['id'],
                $params['question_num'],
                $params['code'],
                $params['lang'],
                substr_count($params['code'], "\n")
            );
            $session->remove('sessionidQ');
            $session->remove('posted');
            $app->flash('ans_modify', '回答の修正が完了しました。');
            $app->redirect('/answer/'.$params['ans_num']);
            
        } catch (PDOException $e) {
            print($e->getMessage());
            $app->error('登録に失敗しました。');
        }
    }
});

/**
 * コメントの登録処理
 */
$app->post('/comment/register', 'authorized', function () use ($app) {
    require_once MODELS_DIR . '/Comment.php';
    require_once LIB_DIR . '/Session.php';
    require_once LIB_DIR . '/FormValidator/CommentFormValidator.php';

    $params  = $app->request()->post();
    $session = $app->factory->getSession();
    $comment = $app->factory->getComment();
    $errors  = array();

    $form_validator = $app->factory->getFormValidator_CommentFormValidator();
    $user_info = array();
    $session->remove('error_msg');
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    if($params['comment_uId'] !== $user_info['id']){
        $app->redirect('/');
    }
    if($user_info != null && $params['sessionid'] === $session->get('sessionidA')){
        if ($form_validator->run($params)) {
            $confarmcomment = $params['comment'];
            try {
                $comment->register(
                    $user_info['id'],
                    $params['answer_num'],
                    $confarmcomment
                );
                $session->remove('sessionidA');

            } catch (PDOException $e) {
                print($e->getMessage());
                $app->error('登録に失敗しました。');
            }
      
        } else {
            $confarmcomment = '';
            $errors = $form_validator->getErrors();
            $app->flash('error', $errors['comment']);
         }
    }

    $app->redirect('/answer/'.$params['answer_num']);

});
