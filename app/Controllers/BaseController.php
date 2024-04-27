<?php

namespace App\Controllers;

use App\Helpers\Utils;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

helper('cookie');

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // <MEMO> config 로 App:class 를 줘도, 비워서 default로 해도 \Config\Session 의 영향을 받음
        // $this->session = \Config\Services::session(config(\Config\App::class));
        $this->session = \Config\Services::session();

//        //initialize key
//        // Get a hex-encoded representation of the key:
//        $encoded = bin2hex(\CodeIgniter\Encryption\Encryption::createKey(32));
//
//        // Put the same value with hex2bin(),
//        // so that it is still passed as binary to the library:
//        $key = hex2bin($encoded);
//        ServerLogger::log($encoded);
    }

    protected function setLanguage(): void
    {
        $language = \Config\Services::language();
        $lang = $this->session->lang;
        $language->setLocale($lang ?? Utils::startsWith( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 'ko-KR') ? 'ko' : 'en');
    }

}
