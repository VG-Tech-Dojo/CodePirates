<?php

/**
 *問題修正のための問題リスト画面
 */
$app->get('/adminview', 'admin_auth' ,function () use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Question.php';
    require_once MODELS_DIR . '/User.php';


    $session = $app->factory->getSession();
    $question = $app->factory->getQuestion();
    $user = $app->factory->getUser();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }
    $questionList = $question->getAllQuestion();
    $app->render('adminview/questionList.twig', array('user' => $user_info, 'questionList' => $questionList));
});
/**
 * 回答のコード表示画面
 */
$app->get('/adminview/:a_id', 'admin_auth'  ,function ($a_id) use ($app) {
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
        $app->error("この問題の回答を閲覧するには、問題への回答が必須です");
    }
    
    if (!($answer_comment = $comment->getCommentByAnsId($a_id))) {
        $answer_comment = "";    
    }
    $footmark->register(
        $user_info['id'],
        $a_id
    );
    $app->render('adminview/answer.twig', array('user' => $user_info,'slimFlash' => $_SESSION['slim.flash'], 'answer' => $answerInfo ,'question' => $questionInfo, 'answerer' => $answererInfo, 'comment' => $answer_comment, 'sessionid' => $sessionid, 'like' => $like));
});



/**
 * ある問題に対する回答一覧画面(GET)
 */
$app->get('/adminview/question/:id','admin_auth'  ,  function ($q_id) use ($app) {
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
                $app->error("回答一覧を見るには該当問題へ回答が必須です");
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
    $app->render('adminview/answerlist.twig', array('user' => $user_info, 'answer_data' => $answerdata, 'question' => $questionInfo, 'flash_msg' => $flash_msg));
});


/**
 * ある問題に対する回答一覧画面(POST)
 */
$app->post('/adminview/question/:id', 'admin_auth'  ,  function ($q_id) use ($app) {
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
                $app->error("回答一覧を見るには該当問題へ回答が必須です");
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
            if($params['sort'] && $params['sort'] !== 'userABCsort'){
                $arraytemp = array();
                foreach($answerdata as $answerdata_items){
                    foreach($answerdata_items['answer'] as $answerdata_item){
                        $answerdata_item['name'] = $answerdata_items['name'];
                        $arraytemp[] = $answerdata_item;
                    }
                }
                if($params['sort'] === "like"){//同じ処理をしているので後にまとめる
                    $likeRank = array();
                    for($i = 0; $i < count($arraytemp); $i++){
                        $likeRank[$i] = $arraytemp[$i]['like'];
                    }
                    arsort($likeRank);
                    $answerdata = array();
                    foreach($likeRank as $key => $value){
                        $answerdata[] = $arraytemp[$key];
                    }   
                }else if($params['sort'] === "comment"){
                    $commentRank = array();
                    for($i = 0; $i < count($arraytemp); $i++){
                        $commentRank[$i] = $arraytemp[$i]['comment'];
                    }
                    arsort($commentRank);
                    $answerdata = array();
                    foreach($commentRank as $key => $value){
                        $answerdata[] = $arraytemp[$key];
                    }   
                }else if($params['sort'] === "line"){
                    $lineRank = array();
                    foreach($arraytemp as $key => $arraytemp_item){
                        $lineRank[$key] = $arraytemp_item['line_count'];
                    }
                    asort($lineRank);
                    $answerdata = array();
                    foreach($lineRank as $key => $value){
                        $answerdata[] = $arraytemp[$key];
                    }
                }else if($params['sort'] === "PV"){
                    $footmarksInfo = $footmark->getFootmarkByQID($q_id);
                    $footmarkRank = array();
                    foreach($footmarksInfo as $footmark_item){
                        if(!isset($footmarkRank[$footmark_item['a_id']])){
                            $footmarkRank[$footmark_item['a_id']] = 0;
                        }
                        $footmarkRank[$footmark_item['a_id']]++;
                    }
                    arsort($footmarkRank);
                    $answerdata = array();
                    $answerdata_key = array();
                    foreach($arraytemp as $key => $arraytemp_item){
                        $answerdata_key[$arraytemp_item['id']] = $key; 
                    }
                    foreach($footmarkRank as $key => $footmarkRank_item){
                        $arraytemp[$answerdata_key[$key]]['PV'] = $footmarkRank_item;
                        $answerdata[] = $arraytemp[$answerdata_key[$key]];
                        unset($arraytemp[$answerdata_key[$key]]);
                    }
                    $answerdata = array_merge($answerdata,$arraytemp);
                }
            }else if($params['sort'] === 'userABCsort'){
                $userABCrank = array();
                foreach($answerdata as $key => $answerdata_item){
                    $userABCrank[$key] = $answerdata_item['name'];
                }
                asort($userABCrank);
                $arraytemp = $answerdata;
                $answerdata = array();
                foreach($userABCrank as $key => $userABCrank_item){
                    $answerdata[] = $arraytemp[$key];
                }
            }
        }
    }
    $flash_msg = $_SESSION['slim.flash'];
    $app->render('adminview/answerlist.twig', array('user' => $user_info, 'answer_data' => $answerdata, 'question' => $questionInfo, 'flash_msg' => $flash_msg, 'lang_narrow' => $params['lang_narrow'], 'sort' => $params['sort']));
});


