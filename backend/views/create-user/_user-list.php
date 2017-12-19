<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = '银行卡信息修改';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'username',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{temp-update}',
                'buttons' => [
                    'temp-update' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', 'View'),
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
							'menu'
                        ];
                        return Html::a('修改', $url, $options);
                 },
                ]
            ],
        ],
    ]); ?>
</div>