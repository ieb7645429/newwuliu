<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use frontend\assets\BalanceEditIndexAsset;
BalanceEditIndexAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '查看';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<style>
.form-group{
    width:50%;
}
</style>
<div class="logistics-order-index">


    <?php $template = '{update} {delete}';?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//             ['class' => 'yii\grid\SerialColumn'],

//             'order_id',
            'logistics_sn',
            [
                'attribute' => 'order_sn',
                'value' => function($model){
                    if(!empty($model->order_sn)&&!is_numeric($model->order_sn)){
                        return unserialize($model->order_sn);
                    }
                    if(empty($model->order_sn))
                        return '';
                    return $model->order_sn;
                }
            ],
            [
                'label' => '会员号',
                'attribute' => 'userName',
                'value' => 'userName.username',
            ],
            'freight',
            'goods_price',
            'make_from_price',
            [
                'attribute'=>'collection',
                'value'=>function($model){
                    if($model->collection==1){
                        return '代收';
                    }elseif($model->collection==2){
                        return '不代收';
                    }
                },
                'filter'=> ['1'=>'代收','2'=>'不代收'],
            ],
            [
                'label' => '是否正常',
                'attribute'=>'abnormal',
                'value'=>function($model){
                    if($model->abnormal==1){
                        return '异常';
                    }elseif($model->abnormal==2){
                        return '正常';
                    }
                },
                'filter'=> ['1'=>'异常','2'=>'正常'],
            ],
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
				 'update' => function ($url, $model, $key) {
                    $options = [
                        'title' => Yii::t('yii', 'View'),
                        'aria-label' => Yii::t('yii', 'View'),
                        'data-pjax' => '0',
                    ];
                        $url = '?r=employee/update&id='.$model->order_id;
                        return Html::a('修改', $url, $options);
                 },
                 'delete' => function ($url, $model, $key) {
                     $options = [
                             'data-order-id'=>$model->order_id,
                             'class' => 'operation',
                             'data-toggle' => 'modal',
                             'data-target' => '#create-modal',
                     ];
                 return Html::a('删除', 'javascript:;', $options);
                 },
                ]
            ],
        ],
    ]); ?>
</div>
<?php Modal::begin([
        'id' => 'create-modal',
        'header' => '<h4 class="modal-title">备注信息</h4>',
]);
?>
<div class="payDiv">
    <div class="payInput">
    	<?=Html::input('text','delContent','',['id'=>'delContent','class' => 'form-control pay-input']);?>
    	<?=Html::input('hidden','order_id','',['id'=>'order_id','class' => 'form-control pay-input']);?>
    </div>
    <div class="row payButtonDiv">
    	<div class="col-md-6 payButton"><?php echo Html::button('确定', ['id'=>'confirm','class'=>'btn btn-primary']);?></div>
    	<div class="col-md-6 payButton"><?php echo Html::button('取消', ['class'=>'btn btn-primary','data-dismiss'=>'modal']);?></div>
    </div>
</div>
<?php Modal::end();?>
