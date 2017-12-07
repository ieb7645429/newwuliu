<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use common\models\LogisticsOrder;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$type    = Yii::$app->controller->action->id;
if($type == 'rece'){
$this->title = '收货人返货统计列表';
$phone = 'receiving_phone';
}
elseif($type == 'send'){
$this->title = '发货人返货统计列表';
$phone =  array(
                'label' => '会员号',
                'attribute' => 'userName',
                'value' => 'userName.username',
            );
}
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;

?>
<style>
.form-group{
    width:50%;
}
</style>
<div class="logistics-order-index">
     <?=$this->title?>
    <hr style="border-top:1px solid #ccc"></hr>
  
   
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//             ['class' => 'yii\grid\SerialColumn'],

//             'order_id',
            $phone,
//             'receiving_name_area',
            [
                'label' => '订单总数量',
                'value' => function($model){
                   return $model->GetCount_Rece(array($model->receiving_phone,$model->member_id),Yii::$app->controller->action->id);
                },
            ],
            [
                'label' => '返货总数',							    
                'value' => function($model){
                     return $model->GetCount_Return_Rece(array($model->receiving_phone,$model->member_id),Yii::$app->controller->action->id);
                },
            ],
			  [
                'label' => '返货占总订单比率',
				// 'attribute' => 'bit',
                'value' => function($model){
                    return round($model->GetCount_Return_Rece(array($model->receiving_phone,$model->member_id),Yii::$app->controller->action->id)/$model->GetCount_Rece(array($model->receiving_phone,$model->member_id),Yii::$app->controller->action->id)*100,2).'%';
                },
            ],

          
        ],
        'layout' => "\n{items}\n{pager}",
    ]); ?>
</div>
