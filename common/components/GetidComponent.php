<?php

namespace common\components;

use getID3;
use getid3_writetags;

require_once __DIR__ . '/getid/getid3.php';
require_once __DIR__ . '/getid/getid3.lib.php';
require_once __DIR__ . '/getid/write.php';

class GetidComponent
{
    public function getInfo($filename)
    {
        $getID3 = new getID3();
        $fileInfo = $getID3->analyze($filename);
        return $fileInfo;
    }

    public function addUrlSite($filename)
    {
        $tagwriter = new getid3_writetags();
        $tagwriter->filename = $filename;
        $TagData['Comment'][] = 'ProZouk project. http://BrazilianZouk.ru';
        $tagwriter->tag_data = $TagData;
        $tagwriter->WriteTags();
    }
}