<?php

namespace App\Http\Classes;
use PHPHtmlParser\Exceptions\CurlException;

class Curl {
    /**
     * A simple curl implementation to get the content of the url. Supports Proxying
     *
     * @param string $url
     * @param string $proxy '127.0.0.1:8888'
     * @return string
     * @throws CurlException
     */
    public function get( $url, $proxy = '')
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL,$url);
        if ( $proxy ) curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $content = curl_exec($ch);
        if ($content === false)
        {
            // there was a problem
            $error = curl_error($ch);
            throw new CurlException('Error retrieving "'.$url.'" ('.$error.')');
        }
        return $content;
    }
}