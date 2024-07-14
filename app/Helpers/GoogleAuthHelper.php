<?php

namespace App\Helpers;

class GoogleAuthHelper
{
    public static function getToken($code, $redirect_uri)
    {
        $settingModel = model('Models\SettingModel');
        $id = $settingModel->getInitialValue(['code' => 'google-client-id'], 'value');
        $secret = $settingModel->getInitialValue(['code' => 'google-client-secret'], 'value');
        $postdata = array(
            'client_id' => $id,
            'client_secret' => $secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirect_uri
        );

        $url = 'https://oauth2.googleapis.com/token';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);

        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $json = json_decode($json_response, true);
        ServerLogger::log($json_response);
        if (isset($json['error'])) {
            return false;
        }
        return $json['access_token'];
    }

    public static function getProfile($token)
    {
        $url = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $token;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return json_decode($json_response, true);
    }
}
