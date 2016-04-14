<?php
/**
 * @var Img[] $imgs
 */
use common\models\Img;
use common\models\User;
use frontend\models\Lang;
use yii\helpers\Html;
use yii\helpers\Url;

$thisUser = User::thisUser();
?>
<div class="modal fade modal-add-img bs-example-modal-md" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" data-user-id="<?= $thisUser->id ?>">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Lang::t('main/img', 'modalAddImgTitle') ?></h4>
            </div>
            <div class="modal-body">
                <div class="block-img-user-list">
                    <?= \frontend\widgets\AddImgWidget::widget([]) ?>
                    <hr/>
                    <div class="block-imgs">
                        <?php
                        foreach ($imgs as $img) {
                            ?>
                            <div class="img-input-group block-img-add" data-id="<?= $img->id ?>" data-url="<?= $img->short_url ?>">
                                <?= Html::tag(
                                    'div',
                                    Html::tag('div', '', ['style' => "background-image:url('{$img->short_url}')", 'class' => 'background-img']),
                                    ['class' => 'img-input-group']
                                )
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="block-add-img hide">
                </div>
            </div>
            <div class="modal-footer">
                <?= Html::button(Lang::t('main/img', 'modalAddImg_addFromList'), ['class' => 'btn btn-link btn-add-from-list pull-left no-focus hide']) ?>
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?= Lang::t('page/listView', 'cancel') ?></button>
            </div>
        </div>
    </div>
</div>
