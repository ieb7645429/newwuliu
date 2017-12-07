<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\Bankinfosearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '银行卡信息';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="bankinfo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php
	 if($dataProvider->query->count()==0):
	?>
    <p>
        <?= Html::a('填写银行卡', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
     endif;
	?>
    <?php $template = '{view}';?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'bank_info_id',
          //  'user_id',
            'bank_info_card_no',
            'bank_info_account_name',
            'bank_info_bank_name',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $template,
				 'buttons' => [
				'view' => function ($url, $model, $key) {
                    $options = [
                        'title' => Yii::t('yii', 'View'),
                        'aria-label' => Yii::t('yii', 'View'),
                        'data-pjax' => '0',
                    ];
                    return Html::a('查看', $url, $options);
                 },
                ]
               
            ],
        ],
    ]); ?>
</div>
