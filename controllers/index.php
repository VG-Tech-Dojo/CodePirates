<?php
/**
 * 繝医ャ繝礼判髱｢
 */
$app->get('/(:page)', function ($page = 1) use ($app) {
    require_once LIB_DIR . '/Session.php';
    require_once MODELS_DIR . '/Content.php';

    $session = $app->factory->getSession();
    $user_info = array();
    if ($session->get('user_id')) {
        $user_info['id'] = $session->get('user_id');
        $user_info['name'] = $session->get('user_name');
    }

    $content = $app->factory->getContent();
    $paginate = $content->paginate($page, CONTENTS_LIMIT);
    $app->render('index.twig', array('user' => $user_info, 'contents' => $paginate));

})->conditions(array('page' => '\d.*'));

/*
 * 繧ｨ繝ｩ繝ｼ逕ｻ髱｢
 */
$app->error(function ($msg='') use ($app) {
    $app->render('error.twig', array('message' => $msg), 500);
});
