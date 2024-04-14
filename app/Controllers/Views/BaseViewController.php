<?php

namespace Views;

use App\Controllers\BaseController;
use App\Helpers\Utils;
use Config\Services;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class BaseViewController extends BaseController
{

    protected int $per_page = 30;

    /**
     * header 에 데이터 load 할 때 resource file 들의 참조를 규격화하는 기능
     * @param string $type
     * @param array|string $urls
     * @return string
     */
    private function generateAssetStatement(string $type, array|string $urls): string
    {
        $result = "";
        $getter = null;
        switch ($type) {
            case 'css' :
                $getter = function ($url) {
                    if (Utils::startsWith($url, '//') || Utils::startsWith($url, 'http')) {
                        return "<link href=\"" . $url . "\" type=\"text/css\" rel=\"stylesheet\"/>";
                    }
                    $url .= Utils::endsWith($url, '.css') ? '' : '.css';
                    $path = "/asset/css" . (Utils::startsWith($url, '/') ? '' : '/') . $url;
                    return "<link href=\"" . $path . "\" type=\"text/css\" rel=\"stylesheet\"/>";
                };
                break;
            case 'js':
            case 'javascript':
                $getter = function ($url) {
                    if (Utils::startsWith($url, '//') || Utils::startsWith($url, 'http')) {
                        return "<script src=\"" . $url . "\" type=\"text/javascript\"></script>";
                    }
                    $url .= Utils::endsWith($url, '.js') ? '' : '.js';
                    $path = "/asset/js" . (Utils::startsWith($url, '/') ? '' : '/') . $url;
                    return "<script src=\"" . $path . "\" type=\"text/javascript\"></script>";
                };
                break;
        }
        if (!$getter) "";
        if (is_array($urls)) {
            foreach ($urls as $url) {
                $result .= $getter($url) . "\n";
            }
        } else {
            $result .= $getter($urls) . "\n";
        }
        return $result;
    }

    /**
     * header 출력 시에 공통적으로 load 될 데이터 호출 기능
     * @param array $input
     * @param array $initData
     * @return array
     */
    protected function loadDataForHeader(array $input, array $initData = []): array
    {
        $data = array_merge([
            'is_login' => $this->session->is_login,
            'user_id' => $this->session->user_id,
            'user_type' => $this->session->user_type,
            'is_admin' => $this->session->is_admin,
        ], $initData);
        if (isset($input['css'])) {
            $data['css'] = $this->generateAssetStatement("css", $input['css']);
        }
        if (isset($input['js'])) {
            $data['js'] = $this->generateAssetStatement("js", $input['js']);
        }
        return $data;
    }

    /**
     * body 출력 시에 공통적으로 load 될 데이터 호출 기능
     * @return array
     */
    protected function getViewData(): array
    {
        // js 에서도 사용자의 기본 상태를 알기 위해 cookie 에도 매번 refresh
        $this->setCookie('is_login', $this->session->is_login);
        $this->setCookie('user_id', $this->session->user_id);
        $this->setCookie('user_type', $this->session->user_type);
        $this->setCookie('is_admin', $this->session->is_admin);
        $this->setCookie('lang', $this->session->lang);
        return [
            'is_login' => $this->session->is_login,
            'user_id' => $this->session->user_id,
            'user_type' => $this->session->user_type,
            'is_admin' => $this->session->is_admin,
            'lang' => $this->session->lang,
        ];
    }

    protected function setCookie($key, $value): void
    {
        if ($value) {
            set_cookie($key, $value, httpOnly: false);
        } else {
            set_cookie($key, '', httpOnly: false);
        }
    }

    public function updateSession($key, $value): void
    {
        $this->session->set([
            $key => $value,
        ]);
    }

    #[NoReturn] protected function handleException(Exception $e): void
    {
        switch ($e->getMessage()) {
            case '400' :
            case 'bad request' :
            {
                $response = Services::response();
                $response->setBody(view('/error', [
                    'code' => '400',
                    'title' => 'Bad Request',
                    'message' => lang('Errors.400'),
                ]));
                $response->sendBody();
                exit;
            }
            case '401' :
            case 'unauthorized' :
            {
                $response = Services::response();
                $response->setBody(view('/error', [
                    'code' => '401',
                    'title' => 'Unauthorized',
                    'message' => lang('Errors.401'),
                ]));
                $response->sendBody();
                exit;
            }
            case '403' :
            case 'forbidden' :
            {
                $response = Services::response();
                $response->setBody(view('/error', [
                    'code' => '403',
                    'title' => 'Forbidden',
                    'message' => lang('Errors.403'),
                ]));
                $response->sendBody();
                exit;
            }
            case '404' :
            case 'not found' :
            {
                $response = Services::response();
                $response->setBody(view('/error', [
                    'code' => '404',
                    'title' => 'Not Found',
                    'message' => lang('Errors.404'),
                ]));
                $response->sendBody();
                exit;
            }
            case '410' :
            case 'deleted' :
            {
                $response = Services::response();
                $response->setBody(view('/error', [
                    'code' => '410',
                    'title' => 'Gone',
                    'message' => lang('Errors.410'),
                ]));
                $response->sendBody();
                exit;
            }
            default :
            {
                $response = Services::response();
                $response->setBody(view('/error', [
                    'code' => '500',
                    'title' => 'Internal Server Error',
                    'message' => lang('Errors.500', ['error_message' => $e->getMessage()]),
                    'trace' => $e->getTraceAsString(),
                ]));
                $response->sendBody();
                exit;
            }
        }
    }
}
