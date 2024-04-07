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
$routes->get('/board/([a-zA-Z][a-zA-Z0-9\-\_]*)/([0-9]+)', [\Views\BoardController::class, 'getBoard']);
$routes->addRedirect('/board/([a-zA-Z][a-zA-Z0-9\-\_]*)', '/board/$1/1');
$routes->get('/board/([a-zA-Z][a-zA-Z0-9\-\_]*)/topic/create', [\Views\BoardController::class, 'createTopic']);
$routes->get('/topic/([0-9]+)', [\Views\BoardController::class, 'getTopic']);
$routes->get('/topic/([0-9]+)/edit', [\Views\BoardController::class, 'editTopic']);

//admin pages
$routes->addRedirect('/admin', '/admin/category');
$routes->get('/admin/login', [\Views\Admin\LoginController::class, 'index']);
$routes->get('/admin/registration', [\Views\Admin\RegistrationController::class, 'index']);
$routes->get('/admin/reset-password', [\Views\Admin\RegistrationController::class, 'resetPassword']);
$routes->get('/admin/profile', [\Views\Admin\ProfileController::class, 'index']);
$routes->get('/admin/board/([0-9]+)', [\Views\Admin\BoardController::class, 'index']);
$routes->addRedirect('/admin/board', '/admin/board/1');
$routes->get('/admin/board/([a-zA-Z][a-zA-Z0-9\-\_]*)/([0-9]+)', [\Views\Admin\BoardController::class, 'getBoard']);
$routes->addRedirect('/admin/board/([a-zA-Z][a-zA-Z0-9\-\_]*)', '/admin/board/$1/1');
$routes->get('/admin/board/([a-zA-Z][a-zA-Z0-9\-\_]*)/topic/create', [\Views\Admin\BoardController::class, 'createTopic']);
$routes->get('/admin/topic/([0-9]+)', [\Views\Admin\BoardController::class, 'getTopic']);
$routes->get('/admin/topic/([0-9]+)/edit', [\Views\Admin\BoardController::class, 'editTopic']);
$routes->get('/admin/topic/reply/([0-9]+)', [\Views\Admin\ReplyController::class, 'index']);
$routes->addRedirect('/admin/topic/reply', '/admin/topic/reply/1');

$routes->get('/admin/user/([0-9]+)', [\Views\Admin\UserController::class, 'index']);
$routes->addRedirect('/admin/user', '/admin/user/1');

$routes->get('/admin/artist/([0-9]+)', [\Views\Admin\ArtistController::class, 'index']);
$routes->addRedirect('/admin/artist', '/admin/artist/1');
$routes->get('/admin/artist/create', [\Views\Admin\ArtistController::class, 'createArtist']);
$routes->get('/admin/artist/([0-9]+)/view', [\Views\Admin\ArtistController::class, 'getArtist']);
$routes->get('/admin/artist/([0-9]+)/edit', [\Views\Admin\ArtistController::class, 'editArtist']);

$routes->get('/admin/project/([0-9]+)', [\Views\Admin\ProjectController::class, 'index']);
$routes->addRedirect('/admin/project', '/admin/project/1');
$routes->get('/admin/project/create', [\Views\Admin\ProjectController::class, 'create']);
$routes->get('/admin/project/([0-9]+)/view', [\Views\Admin\ProjectController::class, 'get']);
$routes->get('/admin/project/([0-9]+)/edit', [\Views\Admin\ProjectController::class, 'edit']);

$routes->get('/admin/graphic-setting', [\Views\Admin\GraphicSettingController::class, 'index']);

$routes->get('/admin/setting', [\Views\Admin\SettingController::class, 'index']);

$routes->get('/admin/category', [\Views\Admin\CategoryController::class, 'index']);
$routes->get('/admin/category/([a-zA-Z][a-zA-Z0-9\-\_]*)', [\Views\Admin\CategoryController::class, 'getCategory']);

$routes->get('/file/(:any)/thumbnail', [\Views\CustomFileController::class, 'getFileThumbnail']);
$routes->get('/file/(:any)', [\Views\CustomFileController::class, 'getFile']);

/**
 * API Routes
 */
$targets = 'topic|main|relation|project|user_profile|artist_profile|artist_preview';
$routes->post('/api/file/(' . $targets . ')/upload/([a-zA-Z0-9\-\_]*)', [\API\CustomFileController::class, 'uploadFile']);
$routes->post('/api/file/(' . $targets . '|all)/refresh/([a-zA-Z0-9\-\_]*)', [\API\CustomFileController::class, 'refreshFile']);
$routes->post('/api/file/(' . $targets . ')/confirm/([a-zA-Z0-9\-\_]*)', [\API\CustomFileController::class, 'confirmFile']);
$routes->post('/api/file/(' . $targets . ')/confirm', [\API\CustomFileController::class, 'confirmFile']);
$routes->delete('/api/file/delete/([0-9]+)', [\API\CustomFileController::class, 'deleteFile']);

$routes->get('/api/user/get/profile', [\API\UserController::class, 'getProfile']);
$routes->get('/api/user/get/([0-9]+)', [\API\UserController::class, 'getUser']);
$routes->post('/api/user/update/profile', [\API\UserController::class, 'updateProfile']);
$routes->post('/api/user/update/([0-9]+)', [\API\UserController::class, 'updateUser']);
$routes->post('/api/user/registration/verify', [\API\UserController::class, 'verifyRegistration']);
$routes->post('/api/user/registration/register', [\API\UserController::class, 'register']);
$routes->post('/api/user/reset-password/verify', [\API\UserController::class, 'verifyResetPassword']);
$routes->post('/api/user/reset-password/confirm', [\API\UserController::class, 'confirmResetPassword']);
$routes->post('/api/user/login', [\API\UserController::class, 'login']);
$routes->post('/api/user/logout', [\API\UserController::class, 'logout']);
$routes->post('/api/user/password-change', [\API\UserController::class, 'changePassword']);

$routes->get('/api/board/get/([0-9]+)', [\API\BoardController::class, 'getBoard']);
$routes->post('/api/board/create', [\API\BoardController::class, 'createBoard']);
$routes->post('/api/board/update/([0-9]+)', [\API\BoardController::class, 'updateBoard']);
$routes->delete('/api/board/delete/([0-9]+)', [\API\BoardController::class, 'deleteBoard']);
$routes->get('/api/board/topic/get/([a-zA-Z][a-zA-Z0-9\-\_]*)', [\API\BoardController::class, 'getStaticBoardTopics']);

$routes->get('/api/topic/get/([0-9]+)', [\API\TopicController::class, 'getTopic']);
$routes->post('/api/topic/create', [\API\TopicController::class, 'createTopic']);
$routes->post('/api/topic/update/([0-9]+)', [\API\TopicController::class, 'updateTopic']);
$routes->delete('/api/topic/delete/([0-9]+)', [\API\TopicController::class, 'deleteTopic']);
$routes->get('/api/topic/([0-9]+)/get/reply', [\API\TopicController::class, 'getTopicReply']);
$routes->post('/api/topic/([0-9]+)/create/reply', [\API\TopicController::class, 'createReply']);
$routes->get('/api/topic/reply/([0-9]+)/get/nested-reply', [\API\TopicController::class, 'getNestedReply']);
$routes->post('/api/topic/reply/([0-9]+)/create/nested-reply', [\API\TopicController::class, 'createNestedReply']);
$routes->get('/api/topic/reply/get/([0-9]+)', [\API\TopicController::class, 'getReply']);
$routes->delete('/api/topic/reply/delete/([0-9]+)', [\API\TopicController::class, 'deleteReply']);

$routes->get('/api/setting/get/([0-9]+)', [\API\SettingController::class, 'getSetting']);
$routes->post('/api/setting/create', [\API\SettingController::class, 'createSetting']);
$routes->post('/api/setting/update/([0-9]+)', [\API\SettingController::class, 'updateSetting']);
$routes->delete('/api/setting/delete/([0-9]+)', [\API\SettingController::class, 'deleteSetting']);

$routes->get('/api/code/artist/get/([0-9]+)', [\API\CodeController::class, 'getCodeArtist']);
$routes->post('/api/code/artist/create', [\API\CodeController::class, 'createCodeArtist']);
$routes->post('/api/code/artist/update/([0-9]+)', [\API\CodeController::class, 'updateCodeArtist']);
$routes->delete('/api/code/artist/delete/([0-9]+)', [\API\CodeController::class, 'deleteCodeArtist']);
$routes->get('/api/code/artist/exchange-priority/([0-9]+)/([0-9]+)', [\API\CodeController::class, 'exchangeCodeArtistPriority']);

$routes->get('/api/code/reward-request/get/([0-9]+)', [\API\CodeController::class, 'getCodeRewardRequest']);
$routes->post('/api/code/reward-request/create', [\API\CodeController::class, 'createCodeRewardRequest']);
$routes->post('/api/code/reward-request/update/([0-9]+)', [\API\CodeController::class, 'updateCodeRewardRequest']);
$routes->delete('/api/code/reward-request/delete/([0-9]+)', [\API\CodeController::class, 'deleteCodeRewardRequest']);

$routes->get('/api/artist', [\API\ArtistController::class, 'index']);
$routes->get('/api/artist/get/([0-9]+)', [\API\ArtistController::class, 'get']);
$routes->post('/api/artist/create', [\API\ArtistController::class, 'create']);
$routes->post('/api/artist/update/([0-9]+)', [\API\ArtistController::class, 'update']);
$routes->delete('/api/artist/delete/([0-9]+)', [\API\ArtistController::class, 'delete']);

$routes->get('/api/project/get/([0-9]+)', [\API\ProjectController::class, 'get']);
$routes->post('/api/project/create', [\API\ProjectController::class, 'create']);
$routes->post('/api/project/update/([0-9]+)', [\API\ProjectController::class, 'update']);
$routes->delete('/api/project/delete/([0-9]+)', [\API\ProjectController::class, 'delete']);

$routes->get('/api/graphic-setting/get/all', [\API\GraphicSettingController::class, 'getGraphicSettings']);

$routes->get('/api/category/get/all', [\API\CategoryController::class, 'getCategoryAll']);
$routes->get('/api/category/get/([0-9]+)', [\API\CategoryController::class, 'getCategory']);
$routes->post('/api/category/create', [\API\CategoryController::class, 'createCategory']);
$routes->post('/api/category/update/([0-9]+)', [\API\CategoryController::class, 'updateCategory']);
$routes->delete('/api/category/delete/([0-9]+)', [\API\CategoryController::class, 'deleteCategory']);
$routes->get('/api/category/exchange-priority/([0-9]+)/([0-9]+)', [\API\CategoryController::class, 'exchangeCategoryPriority']);
$routes->get('/api/category/local/get/([0-9]+)', [\API\CategoryController::class, 'getCategoryLocal']);
$routes->post('/api/category/local/create', [\API\CategoryController::class, 'createCategoryLocal']);
$routes->post('/api/category/local/update/([0-9]+)', [\API\CategoryController::class, 'updateCategoryLocal']);
$routes->delete('/api/category/local/delete/([0-9]+)', [\API\CategoryController::class, 'deleteCategoryLocal']);
$routes->get('/api/category/local/exchange-priority/([0-9]+)/([0-9]+)', [\API\CategoryController::class, 'exchangeCategoryLocalPriority']);

//$routes->post('/api/email/send', [\API\EmailController::class, 'send']);
$routes->post('/api/email/send/verification-code', [\API\EmailController::class, 'sendVerificationCodeMail']);
$routes->post('/api/email/send/verification-code/(:any)', [\API\EmailController::class, 'sendVerificationCodeMail']);

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
