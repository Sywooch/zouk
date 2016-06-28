<?php
namespace frontend\widgets;

use common\models\Img;
use common\models\Music;
use common\models\Video;
use yii\data\Pagination;

class ModalDialogsWidget extends \yii\bootstrap\Widget
{

    /** @var string $action */
    public $action;

    /** @var Music[] $musics */
    public $musics;

    /** @var Img[] $imgs */
    public $imgs;

    /** @var int $id */
    public $id;

    const ACTION_MODAL_ALARM         = 'ModalAlarm';
    const ACTION_MODAL_ADD_MUSIC     = 'ModalAddMusic';
    const ACTION_MODAL_EDIT_MUSIC    = 'ModalEditMusic';
    const ACTION_MODAL_ADD_IMG       = 'ModalAddImg';
    const ACTION_MODAL_SHOW_IMG      = 'ModalShowImg';
    const ACTION_MODAL_ADD_AVATAR    = 'ModalAddAvatar';
    const ACTION_MODAL_ADD_LOCATION  = 'ModalAddLocation';
    const ACTION_MODAL_SHOW_LOCATION = 'ModalShowLocation';

    private function getActionList()
    {
        return [
            self::ACTION_MODAL_ALARM,
            self::ACTION_MODAL_ADD_MUSIC,
            self::ACTION_MODAL_EDIT_MUSIC,
            self::ACTION_MODAL_ADD_IMG,
            self::ACTION_MODAL_SHOW_IMG,
            self::ACTION_MODAL_ADD_AVATAR,
            self::ACTION_MODAL_ADD_LOCATION,
            self::ACTION_MODAL_SHOW_LOCATION,
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
            'modalDialogsWidget1/modalAddMusic',
            ['musics' => $this->musics]
        );
    }

    public function dialogModalEditMusic()
    {
        return $this->render(
            'modalDialogsWidget1/modalEditMusic',
            []
        );
    }

    public function dialogModalAlarm()
    {
        return $this->render(
            'modalDialogsWidget1/modalAlarm',
            ['itemId' => $this->id]
        );
    }

    public function dialogModalAddImg()
    {
        return $this->render(
            'modalDialogsWidget1/modalAddImg',
            ['imgs' => $this->imgs]
        );
    }

    public function dialogModalShowImg()
    {
        return $this->render(
            'modalDialogsWidget1/modalShowImg',
            []
        );
    }

    public function dialogModalAddAvatar()
    {
        return $this->render(
            'modalDialogsWidget1/modalAddAvatar',
            []
        );
    }

    public function dialogModalAddLocation()
    {
        return $this->render(
            'modalDialogsWidget1/modalAddLocation',
            []
        );
    }

    public function dialogModalShowLocation()
    {
        return $this->render(
            'modalDialogsWidget1/modalShowLocation',
            []
        );
    }
}