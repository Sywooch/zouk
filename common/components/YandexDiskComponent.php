<?php

namespace common\components;

class YandexDiskComponent
{

    public $key;

    public $keys;

    private $user;
    private $psw;

    const DECODE_TYPE_JSON    = 'json';
    const DECODE_TYPE_XML     = 'xml';
    const DECODE_TYPE_DEFAULT = self::DECODE_TYPE_XML;

    const THIS_ENTITY = 'yandex';

    public function __construct()
    {
    }

    public function setClientInfo($key = null)
    {
        if (empty($key)) {
            $key = $this->key;
        }
        $this->user = $this->keys[$key]['user'];
        $this->psw = $this->keys[$key]['psw'];
    }

    public function getDiskSpaceInfo($key = null)
    {
        $this->setClientInfo($key);

        $body = '<D:propfind xmlns:D="DAV:"><D:prop><D:quota-available-bytes/><D:quota-used-bytes/></D:prop></D:propfind>';

        $response = $this->send('PROPFIND', '', $body, ['depth: 0']);
        $decodedResponseBody = $this->getDecodedBody($response);
        if (is_null($decodedResponseBody)) {
            return null;
        }

        $info = (array)$decodedResponseBody->children('DAV:')->response->propstat->prop;

        return $info;
    }


    public function getDirectoryContents($path = '', $offset = null, $amount = null, $key = null)
    {
        $this->setClientInfo($key);
        $path .= '?offset=' . $offset . '&amount' . $amount;

        $response = $this->send('PROPFIND', $path, '', ['depth: 1']);

        $decodedResponseBody = $this->getDecodedBody($response);
        if (is_null($decodedResponseBody)) {
            return null;
        }

        $contents = [];
        foreach ($decodedResponseBody->children('DAV:') as $element) {
            array_push(
                $contents,
                [
                    'href'          => $element->href->__toString(),
                    'status'        => $element->propstat->status->__toString(),
                    'creationDate'  => $element->propstat->prop->creationdate->__toString(),
                    'lastModified'  => $element->propstat->prop->getlastmodified->__toString(),
                    'displayName'   => $element->propstat->prop->displayname->__toString(),
                    'contentLength' => $element->propstat->prop->getcontentlength->__toString(),
                    'resourceType'  => $element->propstat->prop->resourcetype->collection ? 'dir' : 'file',
                    'contentType'   => $element->propstat->prop->getcontenttype->__toString(),
                ]
            );
        }
        return $contents;
    }

    public function getProperty($path = '', $property = '', $namespace = 'default:namespace', $key = null)
    {
        $this->setClientInfo($key);
        if (!empty($property)) {
            $body = '<?xml version="1.0" encoding="utf-8" ?><propfind xmlns="DAV:"><prop><' . $property
                    . ' xmlns="' . $namespace . '"/></prop></propfind>';

            $response = $this->send('PROPFIND', $path, $body, ['depth: 0']);

            $decodedResponseBody = $this->getDecodedBody($response);
            if (is_null($decodedResponseBody)) {
                return false;
            }

            $resultStatus = $decodedResponseBody->children('DAV:')->response->propstat->status;
            if (strpos($resultStatus, '200 OK')) {
                return (string)$response->propstat->prop->children();
            }
        } else {
            $response = $this->send('PROPFIND', $path, '', ['depth: 0']);

            $decodedResponseBody = $this->getDecodedBody($response);
            if (is_null($decodedResponseBody)) {
                return false;
            }

            $result = $decodedResponseBody->children('DAV:')->response->propstat;
            if (strpos($result->status, '200 OK')) {
                return $result->prop;
            }
        }

        return false;
    }

    public function createDirectory($path = '', $key = null)
    {
        $this->setClientInfo($key);

        return $this->send('MKCOL', $path, '', ['depth: 0']);
    }

    public function uploadFile($path = '/', $file = null, $fileName = '', $extraHeaders = null, $key = null)
    {
        $this->setClientInfo($key);

        if (file_exists($file['tempName'])) {
            $headers = [
                'Content-Length' => (string)$file['size'],
            ];
            $finfo = finfo_open(FILEINFO_MIME);
            $mime = finfo_file($finfo, $file['tempName']);
            $parts = explode(";", $mime);
            $headers['Content-Type'] = $parts[0];
            $headers['Etag'] = md5_file($file['tempName']);
            $headers['Sha256'] = hash_file('sha256', $file['tempName']);
            $headers = isset($extraHeaders) ? array_merge($headers, $extraHeaders) : $headers;

            if (empty($fileName)) {
                $fileName = 'no_file_name';
            }
            $response = $this->send(
                'PUT',
                $path . $fileName,
                file_get_contents($file['tempName']),
                $headers
            );

            return $response;
        }
    }

    public function startPublishing($path = '', $key = null)
    {
        $this->setClientInfo($key);
        $body = '<propertyupdate xmlns="DAV:"><set><prop>
            <public_url xmlns="urn:yandex:disk:meta">true</public_url>
            </prop></set></propertyupdate>';


        $response = $this->send('PROPPATCH', $path, $body, ['depth: 0', 'Content-Length' => strlen($body)]);

        $decodedResponseBody = $this->getDecodedBody($response);

        $publicUrl = $decodedResponseBody->children('DAV:')->response->propstat->prop->children()->public_url;
        return (string)$publicUrl;
    }

    public function send($method, $path, $body, $addHeaders)
    {
        $user = $this->user;
        $psw = $this->psw;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, 'https://webdav.yandex.ru' . $path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $headers = [
            "Authorization: Basic " . base64_encode("$user:$psw"),
        ];
        $headers = array_merge($headers, $addHeaders);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $answer = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return $answer;
    }

    public function getDecodedBody($body, $type = null)
    {
        if (!isset($type)) {
            $type = static::DECODE_TYPE_DEFAULT;
        }
        if ($body == "Not found.\n") {
            return null;
        }
        try {
            if (strpos($body, "not authorized") > 0) {
                return [];
            }
            switch ($type) {
                case self::DECODE_TYPE_XML:
                    return simplexml_load_string((string)$body);
                case self::DECODE_TYPE_JSON:
                default:
                    return json_decode((string)$body, true);
            }
        } catch (Exception $e) {
            return null;
        }
    }
}
