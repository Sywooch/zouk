<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * Class VoteModel
 */
class VoteModel extends ActiveRecord
{

    const ADD_REPUTATION_CANCEL_UP   = 'cancel_up';
    const ADD_REPUTATION_UP          = 'up';
    const ADD_REPUTATION_CANCEL_DOWN = 'cancel_down';
    const ADD_REPUTATION_DOWN        = 'down';


    public function getVoteCount()
    {
        return 0;
    }

    public function addVote($changeVote)
    {

    }

    public function addReputation($addReputation)
    {

    }

    // функция превода текста с кириллицы в траскрипт
    function encodestring($str)
    {
        $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
        return str_replace($rus, $lat, $str);
    }

    function toAscii($str, $replace = array(), $delimiter = '-')
    {
        $str = trim($str);
        if (!empty($replace)) {
            $str = str_replace((array)$replace, ' ', $str);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    public function extractKeywords($str, $minWordLen = 3, $minWordOccurrences = 2, $asArray = false)
    {
        $str = preg_replace('/[^\p{L}0-9 ]/u', ' ', $str);
        $str = trim(preg_replace('/\s+/u', ' ', $str));

        $words = explode(' ', $str);
        $keywords = array();
        while(($c_word = array_shift($words)) !== null) {
            if(mb_strlen($c_word) < $minWordLen) {
                continue;
            }

            $c_word = mb_strtolower($c_word);
            if(array_key_exists($c_word, $keywords)) {
                $keywords[$c_word][1]++;
            } else {
                $keywords[$c_word] = array($c_word, 1);
            }
        }
        usort($keywords, function ($first, $sec) {
            return $sec[1] - $first[1];
        });

        $final_keywords = array();
        foreach($keywords as $keyword_det) {
            if($keyword_det[1] < $minWordOccurrences) {
                break;
            }
            array_push($final_keywords, $keyword_det[0]);
        }
        return $asArray ? $final_keywords : implode(', ', $final_keywords);
    }
}