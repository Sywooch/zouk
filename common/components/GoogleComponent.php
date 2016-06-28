<?php

namespace common\components;

use DateInterval;
use DateTime;

class GoogleComponent
{

    public $googleApiKey;

    public function getVideoInfo($videoId)
    {
        $key = $this->googleApiKey;
        $content = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id=" . $videoId . "&key=" . $key . "&part=snippet,statistics,contentDetails");
        $content = json_decode($content, true);
        return $content['items'];
    }

    public function getVideoTitle($videoId)
    {
        $content = $this->getVideoInfo($videoId);
        return isset($content[0]['snippet']['title']) ? $content[0]['snippet']['title'] : '';
    }

    public function getVideoProperties($videoId)
    {
        $content = $this->getVideoInfo($videoId);
        $title = isset($content[0]['snippet']['title']) ? $content[0]['snippet']['title'] : '';
        $durationString = isset($content[0]['contentDetails']['duration']) ? $content[0]['contentDetails']['duration'] : 'PT0M0S';
        $datetime = new DateTime('@0');
        $datetime->add(new DateInterval($durationString));
        $durationSec = $datetime->format('U');

        return [
            'title'    => $title,
            'duration' => $durationSec,
        ];
    }

    public function getShortUrl($longUrl)
    {
        $path = 'urlshortener/v1/url?key=' . $this->googleApiKey;
        $body = json_encode(['longUrl' => $longUrl]);
        return json_decode($this->send('POST', $path, $body, []), true);
    }

    public function getLongUrl($shortUrl)
    {
        $path = 'urlshortener/v1/url?shortUrl=' . $shortUrl . '&key=' . $this->googleApiKey;
        return json_decode($this->send('GET', $path, '', []), true);
    }

    public function getStatistic($shortUrl)
    {
        $path = 'urlshortener/v1/url?shortUrl=' . $shortUrl . '&projection=FULL&key=' . $this->googleApiKey;
        return json_decode($this->send('GET', $path, '', []), true);
    }

    public function send($method, $path, $body, $addHeaders)
    {
        $key = $this->googleApiKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/' . $path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $headers = ["Content-Type: application/json"];
        $headers = array_merge($headers, $addHeaders);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $answer = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return $answer;
    }

    public function getMapsGoogleJsFile()
    {
        return 'https://maps.googleapis.com/maps/api/js?key=' . $this->googleApiKey . '&libraries=places&signed_in=true';
    }
}