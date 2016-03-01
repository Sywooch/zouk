<?php
namespace common\components;

use Cloudinary\Api;

require_once __DIR__ . '\cloudinary\Cloudinary.php';
require_once __DIR__ . '\cloudinary\Uploader.php';
require_once __DIR__ . '\cloudinary\Api.php';

/*
 * Array
(
  [public_id] => c87hg9xfxrd4itiim3t0
  [version] => 1371995958
  [signature] => f8645b000be7d717599affc89a068157e4748276
  [width] => 864
  [height] => 576
  [format] => jpg
  [resource_type] => image
  [created_at] => 2013-06-23T13:59:18Z
  [bytes] => 120253
  [type] => upload
  [url] => http://res.cloudinary.com/demo/image/upload/v1371995958/c87hg9xfxrd4itiim3t0.jpg
  [secure_url] => https://res.cloudinary.com/demo/image/upload/v1371995958/c87hg9xfxrd4itiim3t0.jpg
)
 */

class CloudinaryComponent
{

    public function __construct()
    {
    }

    public function init()
    {
        \Cloudinary::config($this->params);
    }

    public function uploadFromUrl($url)
    {
        $this->init();
        return \Cloudinary\Uploader::upload($url);
    }

    public function uploadFromFile($tmpName, $public_id = "test", $tags = ["special"])
    {
        $this->init();
        return \Cloudinary\Uploader::upload(
            $tmpName,
            array(
                "public_id" => $public_id,
                "crop"      => "limit",
                "width"     => "300",
                "height"    => "300",
                "overwrite" => true,
                "eager"     => [
                    ["width"  => 200, "height" => 200,
                     "crop"   => "thumb", "gravity" => "face",
                     "radius" => 20, "effect" => "sepia"],
                    ["width" => 100, "height" => 150,
                     "crop"  => "fit", "format" => "png"],
                ],
                "tags"      => $tags,
            )
        );
    }

}