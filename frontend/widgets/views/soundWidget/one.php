<?php
/**
 * @var Music $music
 */

use common\models\Music;

$sec = floor($music->playtime);
$min = floor($sec / 60);
$sec = $sec % 60;
if ($sec < 10) {
    $sec = "0" . $sec;
}
?>
<div class="sound-item"
     data-music-id="<?= $music->id ?>"
     href="<?= $music->short_url ?>"
     data-duration="<?= $music->playtime * 1000 ?>"
     data-title="<?= $music->title ?>"
     data-artist="<?= $music->artist ?>"
>
    <table width="100%">
        <tr class="sound-title">
            <td width="20px">
                <div class="background">
                    <div class="play"></div>
                    <div class="pause firsthalf hide"></div>
                    <div class="pause secondhalf hide"></div>
                </div>
            </td>
            <td>
                <div class="sound-artist-title">
                    <b><?= $music->getArtist() ?></b> – <?= $music->getTitle() ?>
                </div>
            </td>
            <td class="time-info">
                <div class="time-play"><?= $min . ":" . $sec ?></div>
            </td>
        </tr>
    </table>
    <table width="100%" class="table-progress hide">
        <tr>
            <td>
                <div class="audio-pr">
                    <div class="audio-white-line"></div>
                    <div class="audio-back-line"></div>
                    <div class="audio-load-line" style="width: 0%;"></div>
                    <div class="audio-progress-line" style="width: 0%;"></div>
                </div>
            </td>
            <td width="23px">
                <i class="glyphicon glyphicon-volume-down"></i>
            </td>
            <td width="70px">
                <div class="audio-pr">
                    <div class="audio-volume-white-line"></div>
                    <div class="audio-volume-back-line"></div>
                    <div class="audio-volume-load-line" style="width: 100%;"></div>
                    <div class="audio-volume-progress-line" style="width: 0%;"></div>
                </div>
            </td>
        </tr>
    </table>

</div>
