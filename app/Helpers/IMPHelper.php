<?php

namespace App\Helpers;

class IMPHelper
{
    private static $host = 'https://api.iamport.kr';

    public static function getToken()
    {
        $settingModel = model('Models\SettingModel');
        $key = $settingModel->getInitialValue(['code' => 'imp-api-key'], 'value');
        $secret = $settingModel->getInitialValue(['code' => 'imp-api-secret'], 'value');
        $postdata = array(
            'imp_key' => $key,
            'imp_secret' => $secret
        );

        $url = IMPHelper::$host . "/users/getToken";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);

        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $json = json_decode($json_response, true);
        if ($json['code'] == 0) {
            return $json['response']['access_token'];
        } else {
            return false;
        }
    }

    public static function getPaymentData($imp_uid)
    {
        $token = IMPHelper::getToken();
        $header = array(
            'Authorization: Bearer ' . $token,
        );

        $url = IMPHelper::$host . "/payments/" . $imp_uid;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $json = json_decode($json_response, true);
        if ($json['code'] == 0) {
            return [
                'success' => true,
                'data' => $json['response']
            ];
        } else {
            return [
                'success' => false,
                'message' => $json['message']
            ];
        }
    }
}
