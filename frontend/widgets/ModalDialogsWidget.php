<?php
namespace frontend\widgets;

use common\models\Music;
use common\models\Video;
use yii\data\Pagination;

class ModalDialogsWidget extends \yii\bootstrap\Widget
{

    /** @var string $action */
    public $action;

    /** @var Music[] $musics */
    public $musics;

    /** @var int $id */
    public $id;

    const ACTION_MODAL_ALARM = 'ModalAlarm';
    const ACTION_MODAL_ADD_MUSIC = 'ModalAddMusic';
    const ACTION_MODAL_EDIT_MUSIC = 'ModalEditMusic';

    private function getActionList()
    {
        return [
            self::ACTION_MODAL_ALARM,
            self::ACTION_MODAL_ADD_MUSIC,
            self::ACTION_MODAL_EDIT_MUSIC,
        ];
    }

    public function init()
    {
    }

    public function run()
    {
        if (!empty($this->action) && in_array($this->action, $this->getActionList())) {
            $action = "dialog" . $this->action;
            return $this->$action();
        }
    }

    public function dialogModalAddMusic()
    {
        return $this->render(
            'modalDialogsWidget/modalAddMusic',
            ['musics' => $this->musics]
        );
    }

    public function dialogModalEditMusic()
    {
        return $this->render(
            'modalDialogsWidget/modalEditMusic',
            []
        );
    }

    public function dialogModalAlarm()
    {
        return $this->render(
            'modalDialogsWidget/modalAlarm',
            ['itemId' => $this->id]
        );
    }
}