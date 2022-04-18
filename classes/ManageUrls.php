<?php


class ManageUrls
{
    public static function constructUrl($relUrl = '')
    {
        if($relUrl == '') {
            return sprintf(
                "%s://%s:%s",
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
                $_SERVER['SERVER_NAME'],
                $_SERVER['SERVER_PORT']
              );
        } else {
            return sprintf(
                "%s://%s:%s/%s/",
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
                $_SERVER['SERVER_NAME'],
                $_SERVER['SERVER_PORT'],
                $relUrl
              );
        } 
    }
}