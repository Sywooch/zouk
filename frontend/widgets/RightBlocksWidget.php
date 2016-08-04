<?php
namespace frontend\widgets;

use common\models\Event;
use common\models\Img;
use common\models\Music;
use common\models\Video;
use yii\db\Expression;

class RightBlocksWidget extends \yii\bootstrap\Widget
{
    /** @var string $action */
    public $action;


    const ACTION_RANDOM_VIDEO = 'RandomVideo';
    const ACTION_MONTH_EVENTS = 'MonthEvents';

    private function getActionList()
    {
        return [
            self::ACTION_RANDOM_VIDEO,
            self::ACTION_MONTH_EVENTS,
        ];
    }

    public function init()
    {

    }

    private function runAction($action)
    {
        if (!empty($action) && in_array($action, $this->getActionList())) {
            $action = "right" . $action;
            return $this->$action();
        }
        return "";
    }

    public function run()
    {
        if (!empty($this->action)) {
            if (is_array($this->action)) {
                $returnActions = [];
                foreach ($this->action as $action) {
                    $returnActions[] = $this->runAction($action);
                }
                return join('', $returnActions);
            } else {
                $this->runAction($this->action);
            }
        }
    }

    public function rightRandomVideo()
    {
        $video = Video::getRandomVideo();
        return $this->render(
            'rightWidget/rightRandomVideo',
            ['video' => $video]
        );
    }

    public function rightMonthEvents()
    {
        $dateStart = strtotime(date('Y-m-01'));
        $dateEnd = strtotime(date('Y-m-t'));
        $events = Event::find()->andWhere(
            'date >= :dateStart AND date <= :dateEnd',
            [':dateStart' => $dateStart, ':dateEnd' => $dateEnd]
        )->limit(5)->orderBy(new Expression('rand()'))->all();

        return $this->render(
            'rightWidget/rightMonthEvents',
            ['events' => $events]
        );
    }

}