<?php

namespace Config;

// Create a new instance of our RouteCollection class.

$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('MainController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */
$CODE_RULE = '([a-zA-Z][a-zA-Z0-9\-\_]*)';
$HASH_RULE = '([a-zA-Z0-9\-\_]*)';
$ID_RULE = '([0-9]+)';
$PAGE_RULE = '([0-9]+)';

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
/**
 * View Routes
 */
$routes->get('/.well-known/acme-challenge/(:any)', [\Views\MainController::class, 'getSslChallenge']);
$routes->get('/', [\Views\MainController::class, 'index']);
$routes->get('/frame-view', [\Views\MainController::class, 'getFrameView']);
$routes->get('/get-session', [\Views\MainController::class, 'getSession']);
$routes->get('/set-session', [\Views\MainController::class, 'setSession']);
$routes->get('/login', [\Views\LoginController::class, 'index']);
$routes->get('/profile', [\Views\ProfileController::class, 'index']);
$routes->get('/registration', [\Views\RegistrationController::class, 'index']);
$routes->get('/reset-password', [\Views\RegistrationController::class, 'resetPassword']);
$routes->get('/board/' . $CODE_RULE . '/' . $PAGE_RULE, [\Views\BoardController::class, 'getBoard']);
$routes->addRedirect('/board/' . $CODE_RULE, '/board/$1/1');
$routes->get('/board/' . $CODE_RULE . '/topic/create', [\Views\BoardController::class, 'createTopic']);
$routes->get('/topic/' . $ID_RULE, [\Views\BoardController::class, 'getTopic']);
$routes->get('/topic/' . $ID_RULE . '/edit', [\Views\BoardController::class, 'editTopic']);
$routes->get('/project/purchase/complete', [\Views\ProjectController::class, 'getComplete']);
$routes->get('/project/purchase/' . $ID_RULE . '/complete', [\Views\ProjectController::class, 'getComplete']);
$routes->get('/project/category/' . $CODE_RULE, [\Views\ProjectController::class, 'getCategory']);
$routes->get('/project/' . $ID_RULE . '/view', [\Views\ProjectController::class, 'get']);
$routes->get('/project/' . $ID_RULE . '/purchase', [\Views\ProjectController::class, 'getPurchase']);
$routes->get('/purchase/' . $PAGE_RULE, [\Views\PurchaseController::class, 'index']);
$routes->addRedirect('/purchase', '/purchase/1');
$routes->get('/purchase/' . $ID_RULE . '/view', [\Views\PurchaseController::class, 'getView']);

$routes->get('/question-board/' . $CODE_RULE . '/' . $ID_RULE, [\Views\QuestionController::class, 'getBoard']);
$routes->addRedirect('/question-board/' . $CODE_RULE, '/question-board/$1/1');
$routes->get('/question/([0-9]+)', [\Views\QuestionController::class, 'get']);

//admin pages
$routes->addRedirect('/admin', '/admin/dashboard');
$routes->get('/admin/dashboard', [\Views\Admin\DashboardController::class, 'index']);
$routes->get('/admin/login', [\Views\Admin\LoginController::class, 'index']);
$routes->get('/admin/registration', [\Views\Admin\RegistrationController::class, 'index']);
$routes->get('/admin/reset-password', [\Views\Admin\RegistrationController::class, 'resetPassword']);
$routes->get('/admin/profile', [\Views\Admin\ProfileController::class, 'index']);
$routes->get('/admin/board/' . $ID_RULE, [\Views\Admin\BoardController::class, 'index']);
$routes->addRedirect('/admin/board', '/admin/board/1');
$routes->get('/admin/board/' . $CODE_RULE . '/' . $PAGE_RULE, [\Views\Admin\BoardController::class, 'getBoard']);
$routes->addRedirect('/admin/board/' . $CODE_RULE, '/admin/board/$1/1');
$routes->get('/admin/board/' . $CODE_RULE . '/topic/create', [\Views\Admin\BoardController::class, 'createTopic']);
$routes->get('/admin/topic/' . $ID_RULE, [\Views\Admin\BoardController::class, 'getTopic']);
$routes->get('/admin/topic/' . $ID_RULE . '/edit', [\Views\Admin\BoardController::class, 'editTopic']);
$routes->get('/admin/topic/reply/' . $PAGE_RULE, [\Views\Admin\ReplyController::class, 'index']);
$routes->addRedirect('/admin/topic/reply', '/admin/topic/reply/1');

$routes->get('/admin/user/' . $ID_RULE, [\Views\Admin\UserController::class, 'index']);
$routes->addRedirect('/admin/user', '/admin/user/1');

$routes->get('/admin/artist/' . $PAGE_RULE, [\Views\Admin\ArtistController::class, 'index']);
$routes->addRedirect('/admin/artist', '/admin/artist/1');
$routes->get('/admin/artist/create', [\Views\Admin\ArtistController::class, 'createArtist']);
$routes->get('/admin/artist/' . $ID_RULE . '/view', [\Views\Admin\ArtistController::class, 'getArtist']);
$routes->get('/admin/artist/' . $ID_RULE . '/edit', [\Views\Admin\ArtistController::class, 'editArtist']);

$routes->get('/admin/project/' . $PAGE_RULE, [\Views\Admin\ProjectController::class, 'index']);
$routes->addRedirect('/admin/project', '/admin/project/1');
$routes->get('/admin/project/' . $ID_RULE . '/view', [\Views\Admin\ProjectController::class, 'get']);
$routes->get('/admin/project/create', [\Views\Admin\ProjectController::class, 'create']);
$routes->get('/admin/project/' . $ID_RULE . '/edit', [\Views\Admin\ProjectController::class, 'edit']);

$routes->get('/admin/project/' . $CODE_RULE . '/reward/' . $PAGE_RULE, [\Views\Admin\ProjectRewardController::class, 'index']);
$routes->addRedirect('/admin/project/' . $CODE_RULE . '/reward', '/admin/project/$1/reward/1');
$routes->get('/admin/project/' . $CODE_RULE . '/reward/get/' . $ID_RULE . '/' . $PAGE_RULE, [\Views\Admin\ProjectRewardController::class, 'getPurchaseItemReward']);
$routes->addRedirect('/admin/project/' . $CODE_RULE . '/reward/get/' . $ID_RULE, '/admin/project/$1/reward/get/$2/1');

$routes->get('/admin/guide', [\Views\Admin\GuideController::class, 'index']);

$routes->get('/admin/graphic-setting', [\Views\Admin\GraphicSettingController::class, 'index']);

$routes->get('/admin/setting', [\Views\Admin\SettingController::class, 'index']);
$routes->get('/admin/purchase/' . $PAGE_RULE, [\Views\Admin\PurchaseController::class, 'index']);
$routes->addRedirect('/admin/purchase', '/admin/purchase/1');

$routes->get('/admin/question-board/' . $CODE_RULE . '/' . $ID_RULE, [\Views\Admin\QuestionController::class, 'getBoard']);
$routes->addRedirect('/admin/question-board/' . $CODE_RULE, '/admin/question-board/$1/1');


$routes->get('/file/(:any)/thumbnail', [\Views\FileController::class, 'getFileThumbnail']);
$routes->get('/file/(:any)', [\Views\FileController::class, 'getFile']);
$routes->get('/reward-file/(:any)/download', [\Views\FileController::class, 'getRewardFileDownload']);
$routes->get('/reward-file/(:any)', [\Views\FileController::class, 'getRewardFile']);
$routes->get('/purchase-item-file', [\Views\FileController::class, 'downloadPurchaseItem']);

/**
 * API Routes
 */
$routes->get('/api/session/(lang)/' . $CODE_RULE, [\Views\BaseViewController::class, 'updateSession']);

$routes->get('/api/file/get/' . $ID_RULE, [\API\CustomFileController::class, 'get']);
$routes->post('/api/file/update/' . $ID_RULE, [\API\CustomFileController::class, 'update']);

$TARGET_RULE = 'topic|main|main_mobile|relation|image|background|background_mobile|artist_profile|artist_preview';
$routes->post('/api/file/(' . $TARGET_RULE . ')/upload/' . $HASH_RULE, [\API\CustomFileController::class, 'uploadFile']);
$routes->post('/api/file/(' . $TARGET_RULE . '|all)/refresh/' . $HASH_RULE, [\API\CustomFileController::class, 'refreshFile']);
$routes->post('/api/file/(' . $TARGET_RULE . ')/confirm/' . $HASH_RULE, [\API\CustomFileController::class, 'confirmFile']);
$routes->post('/api/file/(' . $TARGET_RULE . ')/confirm', [\API\CustomFileController::class, 'confirmFile']);
$routes->delete('/api/file/delete/' . $ID_RULE, [\API\CustomFileController::class, 'deleteFile']);

$routes->get('/api/user/get/profile', [\API\UserController::class, 'getProfile']);
$routes->get('/api/user/get/' . $ID_RULE, [\API\UserController::class, 'getUser']);
$routes->post('/api/user/update/profile', [\API\UserController::class, 'updateProfile']);
$routes->post('/api/user/update/' . $ID_RULE, [\API\UserController::class, 'update']);
$routes->delete('/api/user/delete/' . $ID_RULE, [\API\UserController::class, 'delete']);
$routes->post('/api/user/registration/verify', [\API\UserController::class, 'verifyRegistration']);
$routes->post('/api/user/registration/register', [\API\UserController::class, 'register']);
$routes->post('/api/user/reset-password/verify', [\API\UserController::class, 'verifyResetPassword']);
$routes->post('/api/user/reset-password/confirm', [\API\UserController::class, 'confirmResetPassword']);
$routes->post('/api/user/login', [\API\UserController::class, 'login']);
$routes->post('/api/user/auto-login', [\API\UserController::class, 'autoLogin']);
$routes->post('/api/user/logout', [\API\UserController::class, 'logout']);
$routes->post('/api/user/password-change', [\API\UserController::class, 'changePassword']);
$routes->post('/api/google/profile', [\API\UserController::class, 'getGoogleProfile']);

$routes->get('/api/board/get/' . $ID_RULE, [\API\BoardController::class, 'getBoard']);
$routes->post('/api/board/create', [\API\BoardController::class, 'createBoard']);
$routes->post('/api/board/update/' . $ID_RULE, [\API\BoardController::class, 'updateBoard']);
$routes->delete('/api/board/delete/' . $ID_RULE, [\API\BoardController::class, 'deleteBoard']);
$routes->get('/api/board/topic/get/' . $CODE_RULE, [\API\BoardController::class, 'getStaticBoardTopics']);

$routes->get('/api/topic/get/' . $ID_RULE, [\API\TopicController::class, 'getTopic']);
$routes->post('/api/topic/create', [\API\TopicController::class, 'createTopic']);
$routes->post('/api/topic/update/' . $ID_RULE, [\API\TopicController::class, 'updateTopic']);
$routes->delete('/api/topic/delete/' . $ID_RULE, [\API\TopicController::class, 'deleteTopic']);
$routes->get('/api/topic/' . $ID_RULE . '/get/reply', [\API\TopicController::class, 'getTopicReply']);
$routes->post('/api/topic/' . $ID_RULE . '/create/reply', [\API\TopicController::class, 'createReply']);
$routes->get('/api/topic/reply/' . $ID_RULE . '/get/nested-reply', [\API\TopicController::class, 'getNestedReply']);
$routes->post('/api/topic/reply/' . $ID_RULE . '/create/nested-reply', [\API\TopicController::class, 'createNestedReply']);
$routes->get('/api/topic/reply/get/' . $ID_RULE, [\API\TopicController::class, 'getReply']);
$routes->delete('/api/topic/reply/delete/' . $ID_RULE, [\API\TopicController::class, 'deleteReply']);

$routes->get('/api/setting/get/' . $ID_RULE, [\API\SettingController::class, 'getSetting']);
$routes->post('/api/setting/create', [\API\SettingController::class, 'createSetting']);
$routes->post('/api/setting/update/' . $ID_RULE, [\API\SettingController::class, 'updateSetting']);
$routes->post('/api/setting/update', [\API\SettingController::class, 'updateWithCode']);
$routes->delete('/api/setting/delete/' . $ID_RULE, [\API\SettingController::class, 'deleteSetting']);
$routes->get('/api/setting/graphic-setting', [\API\SettingController::class, 'getGraphicSettings']);
$routes->get('/api/setting/graphic-setting/' . $CODE_RULE, [\API\SettingController::class, 'getGraphicSettingByTarget']);
$routes->get('/api/setting/guide', [\API\SettingController::class, 'getGuide']);
$routes->post('/api/setting/guide', [\API\SettingController::class, 'setGuide']);

$routes->get('/api/code/project/get/' . $ID_RULE, [\API\CodeController::class, 'getCodeProject']);
$routes->post('/api/code/project/create', [\API\CodeController::class, 'createCodeProject']);
$routes->post('/api/code/project/update/' . $ID_RULE, [\API\CodeController::class, 'updateCodeProject']);
$routes->delete('/api/code/project/delete/' . $ID_RULE, [\API\CodeController::class, 'deleteCodeProject']);
$routes->get('/api/code/project/exchange-priority/' . $ID_RULE . '/' . $ID_RULE, [\API\CodeController::class, 'exchangeCodeProjectPriority']);

$routes->get('/api/code/reward-request/get/' . $ID_RULE, [\API\CodeController::class, 'getCodeRewardRequest']);
$routes->post('/api/code/reward-request/create', [\API\CodeController::class, 'createCodeRewardRequest']);
$routes->post('/api/code/reward-request/update/' . $ID_RULE, [\API\CodeController::class, 'updateCodeRewardRequest']);
$routes->delete('/api/code/reward-request/delete/' . $ID_RULE, [\API\CodeController::class, 'deleteCodeRewardRequest']);

$routes->get('/api/artist', [\API\ArtistController::class, 'index']);
$routes->get('/api/artist/get/' . $ID_RULE, [\API\ArtistController::class, 'get']);
$routes->post('/api/artist/create', [\API\ArtistController::class, 'create']);
$routes->post('/api/artist/update/' . $ID_RULE, [\API\ArtistController::class, 'update']);
$routes->delete('/api/artist/delete/' . $ID_RULE, [\API\ArtistController::class, 'delete']);

$routes->get('/api/project/get/' . $ID_RULE, [\API\ProjectController::class, 'get']);
$routes->get('/api/project/reward/' . $ID_RULE, [\API\ProjectController::class, 'getReward']);
$routes->get('/api/project/artist/' . $ID_RULE, [\API\ProjectController::class, 'getArtist']);
$routes->post('/api/project/create', [\API\ProjectController::class, 'create']);
$routes->post('/api/project/update/' . $ID_RULE, [\API\ProjectController::class, 'update']);
$routes->post('/api/project/regenerate-hash/' . $ID_RULE, [\API\ProjectController::class, 'regenerateHash']);
$routes->delete('/api/project/delete/' . $ID_RULE, [\API\ProjectController::class, 'delete']);
$routes->post('/api/project/post/' . $CODE_RULE, [\API\ProjectController::class, 'postByCode']);
$routes->post('/api/project/post', [\API\ProjectController::class, 'post']);
$routes->get('/api/project/' . $CODE_RULE, [\API\ProjectController::class, 'index']);

$routes->post('/api/purchase', [\API\PurchaseController::class, 'create']);
$routes->post('/api/purchase/' . $ID_RULE . '/complete', [\API\PurchaseController::class, 'complete']);
$routes->post('/api/purchase/webhook', [\API\PurchaseController::class, 'webhook']);

$routes->get('/api/exchange-rate', [\API\ExchangeRateController::class, 'index']);

$routes->get('/api/purchase-item/get/' . $ID_RULE, [\API\PurchaseItemController::class, 'get']);
$routes->post('/api/purchase-item/update/' . $ID_RULE, [\API\PurchaseItemController::class, 'update']);
$routes->delete('/api/purchase-item/refund/' . $ID_RULE, [\API\PurchaseItemController::class, 'refund']);
$routes->post('/api/purchase-item-reward/confirm/' . $ID_RULE, [\API\PurchaseItemRewardController::class, 'confirm']);
$routes->get('/api/purchase-item-reward/get/' . $ID_RULE, [\API\PurchaseItemRewardController::class, 'get']);
$routes->post('/api/purchase-item-reward/update/' . $ID_RULE, [\API\PurchaseItemRewardController::class, 'update']);
$routes->post('/api/reward-file/update/' . $ID_RULE, [\API\RewardFileController::class, 'update']);
$routes->post('/api/reward-file/upload/' . $ID_RULE, [\API\RewardFileController::class, 'uploadFile']);
$routes->delete('/api/reward-file/delete/' . $ID_RULE, [\API\RewardFileController::class, 'deleteFile']);

//$routes->post('/api/email/send', [\API\EmailController::class, 'send']);
$routes->post('/api/email/send/verification-code', [\API\EmailController::class, 'sendVerificationCodeMail']);
$routes->post('/api/email/send/verification-code/(:any)', [\API\EmailController::class, 'sendVerificationCodeMail']);

$routes->get('/api/question-board/get/' . $ID_RULE, [\API\QuestionController::class, 'getBoard']);
$routes->post('/api/question-board/create', [\API\QuestionController::class, 'createBoard']);
$routes->post('/api/question-board/update/' . $ID_RULE, [\API\QuestionController::class, 'updateBoard']);
$routes->delete('/api/question-board/delete/' . $ID_RULE, [\API\QuestionController::class, 'deleteBoard']);

$routes->get('/api/question/get/' . $ID_RULE, [\API\QuestionController::class, 'getReservation']);
$routes->post('/api/question/request', [\API\QuestionController::class, 'requestReservation']);
$routes->post('/api/question/accept/' . $ID_RULE, [\API\QuestionController::class, 'acceptReservation']);


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
