<?php

namespace Baarlord\DonorShip;

use Bitrix\Main\Config\Option;
use Baarlord\DonorShip\Manager;

class Handler
{
    public static $images = [];

    function replacePathImgFromDonor(&$content)
    {
        $pattern = '/(?:(?:<img.*?src=[\'\"])|(?:url\([\'\"]?))(.*?)(?:(?:[\'\"].*?>)|(?:[\'\"]?\)))/is';
        $donor = Option::get(Manager::$moduleId, 'DONOR', '');

        preg_match_all(
            $pattern,
            $content,
            $matches
        );

        foreach ($matches[1] as $item) {
            if (!in_array($item, self::$images)) {

                self::preparePath($item);

                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $item)) {

                    if (self::isAbsolutePath($item)) {
                        continue;
                    }

                    if (strpos($item, $donor) === false) {
                        $content = str_replace($item, $donor . $item, $content);
                    }
                    self::$images[] = $item;
                }
            }
        }
    }

    public static function preparePath(&$path)
    {
        $domain = $_SERVER['HTTP_HOST'];

        if (strpos($path, $domain) !== false) {
            $path = str_replace(array('//', 'http://', 'https://', $domain), '', $path);
        }
    }

    public static function isAbsolutePath($path)
    {
        if (substr($path, 0, 2) === '//') {
            return true;
        }

        if (substr($path, 0, 4) === 'http') {
            return true;
        }

        return false;
    }

}
