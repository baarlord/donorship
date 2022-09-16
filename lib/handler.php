<?php

namespace Baarlord\DonorShip;

use Bitrix\Main\Config\Option;

class Handler
{
    public static $images = [];


    public static function replacePathImgFromDonor(&$content): void
    {
        $handler = new Handler();
        if (!$handler->canUseDonor()) {
            return;
        }

        $pattern = $handler->getImgPattern();
        $donor = $handler->getDonor();

        preg_match_all(
            $pattern,
            $content,
            $matches
        );

        foreach ($matches[1] as $item) {
            $originalItem = $item;
            if (in_array($originalItem, self::$images)) {
                continue;
            }

            $handler->preparePath($item);

            if (file_exists(dirname(__DIR__, 4) . $item)) {
                continue;
            }

            if ($handler->isAbsolutePath($item)) {
                continue;
            }

            if (strpos($item, $donor) !== false) {
                continue;
            }
            $content = str_replace($item, $donor . $item, $content);
            self::$images[] = $originalItem;
        }
    }

    private function canUseDonor(): bool
    {
        $canUse = Option::get('baarlord.donorship', 'USE_DONOR', 'N');
        return $canUse === 'Y';
    }

    private function getImgPattern(): string
    {
        return '/(?:(?:<img.*?src=[\'\"])|(?:url\([\'\"]?))(.*?)(?:(?:[\'\"].*?>)|(?:[\'\"]?\)))/is';
    }

    private function getDonor(): string
    {
        return Option::get('baarlord.donorship', 'DONOR', '');
    }

    private function preparePath(&$path): void
    {
        $domain = Option::get('main', 'server_name', '');
        if (strpos($path, $domain) === false) {
            return;
        }
        $path = str_replace(['//', 'http://', 'https://', $domain], '', $path);
    }

    private function isAbsolutePath($path): bool
    {
        return ((substr($path, 0, 2) === '//') || (substr($path, 0, 4) === 'http'));
    }

}
