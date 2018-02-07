<?php

/**
 * @var $this yii\web\View
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $user \app\models\User
 * @var $form yii\bootstrap\ActiveForm
 */

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1><?php echo \Yii::t('app', 'Congratulations!'); ?></h1>

        <p class="lead"><?php echo \Yii::t('app', 'You have successfully created your Yii-powered application.'); ?></p>
    </div>

    <div class="body-content">
        <div class="row">
            <?php $form = ActiveForm::begin([
                'id' => 'add-user-form',
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                    'labelOptions' => ['class' => 'col-lg-1 control-label'],
                ],
            ]); ?>

            <?= $form->field($user, 'login')->input('text'); ?>

            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::submitButton(\Yii::t('user', 'Add user'), ['class' => 'btn btn-primary', 'name' => 'add-user-button']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <?php echo \yii\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns'      => [
                        ['class' => yii\grid\SerialColumn::class],
                        'id',
                        'login',
                        'updated:datetime',
                        [
                            'class'    => yii\grid\ActionColumn::class,
                            'header'   => \Yii::t('user', 'Status'),
                            'template' => '{status}',
                            'buttons'  => [
                                'status' => function ($url, $model, $key) {
                                    /** @var $model \app\models\User */
                                    return \lo\widgets\Toggle::widget(
                                        [
                                            'name'         => 'status',
                                            'checked'      => $model->status,
                                            'options'      => [
                                                'data-on'  => \Yii::t('user', 'Active'),
                                                'data-off' => \Yii::t('user', 'Inactive'),
                                            ],
                                            'clientEvents' => [
                                                'change' => 'function () {
                                            userStatusUpdate(' . $model->id . ', $(this).prop(\'checked\') ? 1 : 0);
                                        }',
                                            ],
                                        ]
                                    );
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var userStatusUpdate = function (id, status) {
        $.ajax({
            url: "'.\yii\helpers\Url::toRoute('/site/user-status-change').'",
            method: "post",
            dataType: "json",
            data: {id: id, status: status},
            success: function (result) {
                console.log(result); // frontend обработка
            }
        });
    }', \yii\web\View::POS_READY, 'user-status-update');
?>
