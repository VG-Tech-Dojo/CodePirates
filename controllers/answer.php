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
    if (($answer_info = $answer->getAnswerByAnsId($a_id)) == null) {
        $app->error('その回答は存在しません');
    } else {
        if (($question_info = $question->getQuestionByID($answer_info['q_id'])) == null) {
            $app->error('問題が存在しません');
        }
        if (($answerer_info = $user->getUserById($answer_info['u_id'])) == null){
            $app->error('ユーザーが存在しません');
        }
    }

    $can_see_like = $like->getLikeFromAandUID($user_info['id'], $a_id);
    if(isset($can_see_like[0])){
        $like = false;
    }else{
        $like = true;
    }

    if (!$user->canSee($user_info['id'], $answer_info['q_id'])){
        $app->error("この問題の回答を閲覧するには、問題への回答が必須です");
    }
    
    if (!($answer_comment = $comment->getCommentByAnsId($a_id))) {
        $answer_comment = "";    
    }
    $footmark->register(
        $user_info['id'],
        $a_id
    );
    $app->render('answer/answer.twig', array('user' => $user_info,'slimFlash' => $_SESSION['slim.flash'], 'answer' => $answer_info ,'question' => $question_info, 'answerer' => $answerer_info, 'comment' => $answer_comment, 'sessionid' => $sessionid, 'like' => $like));
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
 * ある問題に対する回答一覧画面(GET)
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
    $answer_infos = array();

    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }

    if (($question_info = $question->getQuestionByID($q_id)) == null) { 
        $app->error('問題が存在しません');
    } else {
        if (($answer_infos = $answer->getAnswerByQuesId($question_info['id'])) == null) {
           $app->error('回答がありません'); 
        } else {
            if (!$user->canSee($user_info['id'], $q_id)){
                $app->error("回答一覧を見るには該当問題へ回答が必須です");
            }

            $answerer_id = array();
            $answerer_name = array();
            $answerer_info = array();
            $user_items = array();
            $commentsinfo = $comment->getAllComments();
            $count_for_comment = array();
            $like_info = $like->getAllLike();
            $count_for_like = array();
            for($i = 0; $i < count($commentsinfo); $i++){
                $count_for_comment[] = $commentsinfo[$i]['a_id'];    
            }
            $count_for_comment = array_count_values($count_for_comment);
            for($i = 0; $i < count($like_info); $i++){
                $count_for_like[] = $like_info[$i]['a_id'];    
            }
            $count_for_like = array_count_values($count_for_like);

            foreach($answer_infos as $answer_info){
                $user_data[] = $answer_info['u_id'];
            }
            foreach(array_unique($user_data) as $answerd_user){
                $user_items = $user->getUserById($answerd_user);
                $answerdata[$answerd_user]['name'] = $user_items['name'];
                $answerdata[$answerd_user]['answer'] = $answer->getAnswerByUserIdQuestionId($answerd_user,$q_id);
                for($i = 0; $i < count($answerdata[$answerd_user]['answer']); $i++){
                    if(array_key_exists($answerdata[$answerd_user]['answer'][$i]['id'], $count_for_comment)){
                        $answerdata[$answerd_user]['answer'][$i]['comment'] = $count_for_comment[$answerdata[$answerd_user]['answer'][$i]['id']];
                    }else{
                        $answerdata[$answerd_user]['answer'][$i]['comment'] = 0;
                    }
                    if(array_key_exists($answerdata[$answerd_user]['answer'][$i]['id'], $count_for_like)){
                        $answerdata[$answerd_user]['answer'][$i]['like'] = $count_for_like[$answerdata[$answerd_user]['answer'][$i]['id']];
                    }else{
                        $answerdata[$answerd_user]['answer'][$i]['like'] = 0;
                    }
                }
            }
        }
    }
    $flash_msg = $_SESSION['slim.flash'];
    $app->render('answer/answerlist.twig', array('user' => $user_info, 'answer_data' => $answerdata, 'question' => $question_info, 'flash_msg' => $flash_msg));
});


/**
 * ある問題に対する回答一覧画面(POST)
 */
$app->post('/answerlist/question/:id', 'authorized' ,  function ($q_id) use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Answer.php';
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/User.php';
    require_once MODELS_DIR . '/Comment.php';
    require_once MODELS_DIR . '/Good.php';
    require_once MODELS_DIR . '/Footmark.php';


    $session = $app->factory->getSession();
    $answer = $app->factory->getAnswer();
    $question = $app->factory->getQuestion();
    $comment = $app->factory->getComment();
    $like = $app->factory->getGood();
    $user = $app->factory->getUser();
    $footmark = $app->factory->getFootmark();
    $params  = $app->request()->post();
    $user_info = array();
    $answer_infos = array();

    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }

    if (($question_info = $question->getQuestionByID($q_id)) == null) { 
        $app->error('問題が存在しません');
    } else {
        if (($answer_infos = $answer->getAnswerByQuesId($question_info['id'])) == null) {
           $app->error('回答がありません'); 
        } else {
            if (!$user->canSee($user_info['id'], $q_id)){
                $app->error("回答一覧を見るには該当問題へ回答が必須です");
            }

            $answerer_id = array();
            $answerer_name = array();
            $answerer_info = array();
            $user_items = array();
            $commentsinfo = $comment->getAllComments();
            $count_for_comment = array();
            $like_info = $like->getAllLike();
            $count_for_like = array();
            for($i = 0; $i < count($commentsinfo); $i++){
                $count_for_comment[] = $commentsinfo[$i]['a_id'];    
            }
            $count_for_comment = array_count_values($count_for_comment);
            for($i = 0; $i < count($like_info); $i++){
                $count_for_like[] = $like_info[$i]['a_id'];    
            }
            $count_for_like = array_count_values($count_for_like);

            foreach($answer_infos as $answer_info){
                $user_data[] = $answer_info['u_id'];
            }
            foreach(array_unique($user_data) as $answerd_user){
                $user_items = $user->getUserById($answerd_user);
                $answerdata[$answerd_user]['name'] = $user_items['name'];
                $answerdata[$answerd_user]['answer'] = $answer->getAnswerByUserIdQuestionId($answerd_user,$q_id);
                for($i = 0; $i < count($answerdata[$answerd_user]['answer']); $i++){
                    if(array_key_exists($answerdata[$answerd_user]['answer'][$i]['id'], $count_for_comment)){
                        $answerdata[$answerd_user]['answer'][$i]['comment'] = $count_for_comment[$answerdata[$answerd_user]['answer'][$i]['id']];
                    }else{
                        $answerdata[$answerd_user]['answer'][$i]['comment'] = 0;
                    }
                    if(array_key_exists($answerdata[$answerd_user]['answer'][$i]['id'], $count_for_like)){
                        $answerdata[$answerd_user]['answer'][$i]['like'] = $count_for_like[$answerdata[$answerd_user]['answer'][$i]['id']];
                    }else{
                        $answerdata[$answerd_user]['answer'][$i]['like'] = 0;
                    }
                }
            }
            if($params['sort'] && $params['sort'] !== 'userABCsort'){
                $arraytemp = array();
                foreach($answerdata as $answerdata_items){
                    foreach($answerdata_items['answer'] as $answerdata_item){
                        $answerdata_item['name'] = $answerdata_items['name'];
                        $arraytemp[] = $answerdata_item;
                    }
                }
                if($params['sort'] === "like"){//同じ処理をしているので後にまとめる
                    $like_rank = array();
                    for($i = 0; $i < count($arraytemp); $i++){
                        $like_rank[$i] = $arraytemp[$i]['like'];
                    }
                    arsort($like_rank);
                    $answerdata = array();
                    foreach($like_rank as $key => $value){
                        $answerdata[] = $arraytemp[$key];
                    }   
                }else if($params['sort'] === "comment"){
                    $comment_rank = array();
                    for($i = 0; $i < count($arraytemp); $i++){
                        $comment_rank[$i] = $arraytemp[$i]['comment'];
                    }
                    arsort($comment_rank);
                    $answerdata = array();
                    foreach($comment_rank as $key => $value){
                        $answerdata[] = $arraytemp[$key];
                    }   
                }else if($params['sort'] === "line"){
                    $line_rank = array();
                    foreach($arraytemp as $key => $arraytemp_item){
                        $line_rank[$key] = $arraytemp_item['line_count'];
                    }
                    asort($line_rank);
                    $answerdata = array();
                    foreach($line_rank as $key => $value){
                        $answerdata[] = $arraytemp[$key];
                    }
                }else if($params['sort'] === "PV"){
                    $footmarks_info = $footmark->getFootmarkByQID($q_id);
                    $footmark_rank = array();
                    foreach($footmarks_info as $footmark_item){
                        if(!isset($footmark_rank[$footmark_item['a_id']])){
                            $footmark_rank[$footmark_item['a_id']] = 0;
                        }
                        $footmark_rank[$footmark_item['a_id']]++;
                    }
                    arsort($footmark_rank);
                    $answerdata = array();
                    $answerdata_key = array();
                    foreach($arraytemp as $key => $arraytemp_item){
                        $answerdata_key[$arraytemp_item['id']] = $key; 
                    }
                    foreach($footmark_rank as $key => $footmark_rank_item){
                        $arraytemp[$answerdata_key[$key]]['PV'] = $footmark_rank_item;
                        $answerdata[] = $arraytemp[$answerdata_key[$key]];
                        unset($arraytemp[$answerdata_key[$key]]);
                    }
                    $answerdata = array_merge($answerdata,$arraytemp);
                }
            }else if($params['sort'] === 'userABCsort'){
                $user_abc_rank = array();
                foreach($answerdata as $key => $answerdata_item){
                    $user_abc_rank[$key] = $answerdata_item['name'];
                }
                asort($user_abc_rank);
                $arraytemp = $answerdata;
                $answerdata = array();
                foreach($user_abc_rank as $key => $user_abc_rank_item){
                    $answerdata[] = $arraytemp[$key];
                }
            }
        }
    }
    $flash_msg = $_SESSION['slim.flash'];
    $app->render('answer/answerlist.twig', array('user' => $user_info, 'answer_data' => $answerdata, 'question' => $question_info, 'flash_msg' => $flash_msg, 'lang_narrow' => $params['lang_narrow'], 'sort' => $params['sort']));
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
    $answer_info = $answer->getAnswerByAnsId($a_id);
    try {
        if (($question_item = $question->getQuestionByID($answer_info['q_id'])) == null){
            $app->error('その問題は存在しません');
        } else {
            $answer_user_num =$answer->getanswerpeoplenumbyquestionid($question_item['id']);
        }    
    } catch (PDOException $e){
        echo $e->getMessage();
        $app->error('おかしいのでリロードしてください'); 
    }
    if($answer_info['u_id'] !== $user_info['id']){
        $app->redirect('/');
    }
    $app->render('answer/modifyAnswerForm.twig', array('user' => $user_info, 'errors' => $errors, 'question' => $question_item, 'session' => $sessionid, 'answer_user_num' => $answer_user_num, 'answerInfo' => $answer_info));
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
    $answer_info = $answer->getAnswerByAnsId($a_id);
    try {
        if (($question_item = $question->getQuestionByID($answer_info['q_id'])) == null){
            $app->error('その問題は存在しません');
        } else {
            $answer_user_num =$answer->getanswerpeoplenumbyquestionid($question_item['id']);
        }
    } catch (PDOException $e){
        echo $e->getMessage();
        $app->error('おかしいのでリロードしてください'); 
    }
    if($answer_info['u_id'] !== $user_info['id']){
        $app->redirect('/');
    }
    $app->render('answer/modifyAnswerForm.twig', array('user' => $user_info, 'errors' => $errors, 'question' => $question_item, 'old_code' => $old_code, 'answer_user_num' => $answer_user_num, 'session' => $sessionid, 'answerInfo' => $answer_info));
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
