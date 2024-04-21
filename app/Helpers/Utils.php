<?php

namespace App\Helpers;

use Exception;

class Utils
{
    static function startsWith($haystack, $needle): bool
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    static function endsWith($haystack, $needle): bool
    {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    static function toInt($value): int
    {
        if (gettype($value) == 'string') {
            if (strlen($value) != 0) {
                $value = intval($value);
            } else {
                $value = 1;
            }
        }
        return $value;
    }

    static function parseUrl($url)
    {
        if (Utils::startsWith($url, "http")) {
            return "javascript:openWindow('" . $url . "')";
        } else if (Utils::startsWith($url, "/file")) {
            return "/frame-view?url=" . $url;
        }
        return $url;
    }

    /**
     * @throws Exception
     */
    static function generateRandomString($length = 10, $characters = '0123456789'): string
    {
//        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    static function encodeText(string $value): string
    {
        $encrypted = \Config\Services::encrypter()->encrypt($value);
//        $encrypted = utf8_encode($encrypted);
//        $encrypted = base64_encode($encrypted);
        return bin2hex($encrypted);
    }

    static function decodeText(string $value): string
    {
        $decrypted = hex2bin($value);
//        $decrypted = base64_decode($value);
//        $decrypted = utf8_decode($decrypted);
        return \Config\Services::encrypter()->decrypt($decrypted);
    }

    static function calculateAvailableReward($item)
    {
        $availableCount = $item['total_count'] - $item['purchased_count'];
        if ($availableCount < 0) {
            $availableCount = 0;
        }
        if ($availableCount > $item['limited_count']) {
            $availableCount = $item['limited_count'];
        }
        $availableCount -= $item['paid_count'];
        if ($availableCount < 0) {
            $availableCount = 0;
        }
        return $availableCount;
    }
}
