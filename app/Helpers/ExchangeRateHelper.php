<?php

namespace App\Helpers;

class ExchangeRateHelper
{

    public static function search($date)
    {
        $settingModel = model('Models\SettingModel');
        $authKey = $settingModel->getInitialValue(['code' => 'koreaexim-auth-key'], 'value');
        $url = 'https://www.koreaexim.go.kr/site/program/financial/exchangeJSON?authkey='
            . $authKey .
            '&searchdate='
            . $date .
            '&data=AP01';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $json = json_decode($json_response, true);
        return (array)$json;
    }

    public static function find($exchangeRateModel, $date)
    {
        $searched = self::search($date);
        if (sizeof($searched) == 0) {
            $yesterday = date("Y-m-d", strtotime('-1 day', strtotime($date)));
            $result = self::find($exchangeRateModel, $yesterday);
            $exchangeRateModel->insert([
                'date' => $date,
                'exchange_rate_id' => $result['inserted_id'],
            ]);
            return $result;
        } else {
            $result = [];
            foreach ($searched as $item) {
                $result[$item['cur_unit']] = $item;
            }
            $inserted_id = $exchangeRateModel->insert([
                'date' => $date,
                'data' => json_encode($result)
            ]);
            return ['result' => $searched, 'inserted_id' => $inserted_id];
        }
    }

    public static function getExchangeRate()
    {
        $settingModel = model('Models\SettingModel');
        $authKey = $settingModel->getInitialValue(['code' => 'koreaexim-auth-key'], 'value');
        $exchangeRateModel = model('Models\ExchangeRateModel');

        $rates = $exchangeRateModel->get(['date' => date("Y-m-d")]);
        if (sizeof($rates) == 0) {
            self::find($exchangeRateModel, date("Y-m-d"));
        } else {
            $rate = $rates[0];
            if (isset($rate['exchange_rate_id'])) {
                $rates = $exchangeRateModel->get(['id' => $rate['exchange_rate_id']]);
                $rate = $rates[0];
            }
            return (array)json_decode($rate['data'], true);
        }
        $rates = $exchangeRateModel->get(['date' => date("Y-m-d")]);
        $rate = $rates[0];
        return (array)json_decode($rate['data'], true);
    }
}
