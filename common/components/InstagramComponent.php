<?php

namespace common\components;


use Exception;
use InstagramAPI\Instagram;
use InstagramAPI\InstagramException;

class InstagramComponent
{
    public $login;

    public $password;

    public function sendInstagramm($filename, $caption)
    {
        $username = $this->login;
        $password = $this->password;

        $temp = tmpfile();
        fwrite($temp, file_get_contents($filename));
        fseek($temp, 0);

        /** @var Instagram $i */
        $i = new Instagram();
        $i->setUser($username, $password);

        try {
            $i->login();
        } catch (Exception $e) {
            $e->getMessage();
            fclose($temp);
            return false;
        }


        try {
            $i->uploadPhoto($filename, $caption);
        } catch (Exception $e) {
            echo $e->getMessage();
            fclose($temp);
            return false;
        }
    }
}