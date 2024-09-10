<?php

namespace App\Helpers;

class ExchangeRateHelper
{

    public static function search($authKey, $date)
    {
        $url = 'https://www.koreaexim.go.kr/site/program/financial/exchangeJSON?authkey='
            . $authKey .
            '&searchdate='
            . $date .
            '&data=AP01';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        print curl_error($curl);
        curl_close($curl);
        $json = json_decode($json_response, true);
        return (array)$json;
    }

    public static function find($exchangeRateModel, $authKey, $date, $maxDepth = 5)
    {
        if ($maxDepth == 0) return [];
        $rates = $exchangeRateModel->get(['date' => $date]);
        if (sizeof($rates) > 0 && !isset($rates[0]['exchange_rate_id'])) {
            return ['inserted_id' => $rates[0]['id']];
        }

        if (sizeof($rates) > 0) {
            $yesterday = date("Y-m-d", strtotime('-1 day', strtotime($date)));
            return self::find($exchangeRateModel, $authKey, $yesterday, $maxDepth - 1);
        }
        $searched = self::search($authKey, $date);
        if (sizeof($searched) == 0) {
            $yesterday = date("Y-m-d", strtotime('-1 day', strtotime($date)));
            $result = self::find($exchangeRateModel, $authKey, $yesterday, $maxDepth - 1);
            if (isset($result['inserted_id'])) {
                $need_refresh = (date('H') < 11) ? 1 : 0;
                if (sizeof($rates) == 0) {
                    $exchangeRateModel->insert([
                        'date' => $date,
                        'exchange_rate_id' => $result['inserted_id'],
                        'need_refresh' => $need_refresh,
                    ]);
                }
            }
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
            return ['inserted_id' => $inserted_id];
        }
    }

    public static function getExchangeRate()
    {
        $settingModel = model('Models\SettingModel');
        $authKey = $settingModel->getInitialValue(['code' => 'koreaexim-auth-key'], 'value');
        $exchangeRateModel = model('Models\ExchangeRateModel');

        $exchangeRateModel->db->query('DELETE FROM exchange_rate WHERE need_refresh = 1');

        $date = date("Y-m-d");

        $rates = $exchangeRateModel->get(['date' => $date]);
        if (sizeof($rates) == 0) {
            self::find($exchangeRateModel, $authKey, $date);
        }
        $rates = $exchangeRateModel->get(['date' => $date]);
        $rate = $rates[0];
        if (isset($rate['exchange_rate_id'])) {
            $rates = $exchangeRateModel->get(['id' => $rate['exchange_rate_id']]);
            $rate = $rates[0];
        }
        return (array)json_decode($rate['data'], true);
    }
}
