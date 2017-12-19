<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use frontend\assets\ReturnAsset;
ReturnAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsReturnOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '查看';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-return-order-index">

    <hr style="border-top:1px solid #ccc"></hr>
<?php if(empty($identity)||$identity==1){//$identity==1 西部退货组?>
    <p><?= Html::a('添加退货单', ['create2'], ['class' => 'btn btn-success']) ?></p>
    <?php 
        if($identity==1){
            $template = '{view} {update} {sender} {delete}';
        }else{
            $template = '{view} {update} {delete}';
        }
    ?>
<?php }else{
        $template = '{view} {sender} {delete}';
}?>

<?php $form = ActiveForm::begin(['action'=>['return/index'],'method'=>'get'])?>
<?= $form->field($searchModel, 'add_time')->label('开单时间')->widget(DateRangePicker::classname(), [
        'convertFormat'=>true,
        'presetDropdown'=>true,
        'model'=>$searchModel,
        'options' => [
            'class' => 'form-control',
            'value' => !empty(Yii::$app->request->get('LogisticsReturnOrderSearch')['add_time']) ? Yii::$app->request->get('LogisticsReturnOrderSearch')['add_time'] : date('Y-m-d') .' - ' . date('Y-m-d') ,
        ],
        'pluginOptions'=>[
            'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
        ]
])?>
<div class="form-group">
	<?= Html::hiddenInput('download_type', '0', ['id' => 'download_type']) ?>
    <?= Html::submitButton('查询', ['class' => 'btn btn-primary','id'=>'searchButton']) ?>
    <?= Html::Button('导出Excel', ['class' => 'btn btn-warning', 'id' => 'downloadExcel']); ?>
    <?php // echo Html::resetButton('重置', ['class' => 'btn btn-default']); ?>
</div>
<?php //echo '商品数量:'.$goods_num.'件  今日订单:'.($orderNum['1']['orderNum']+$orderNum['2']['orderNum']).'  今日同城订单:'.$orderNum['1']['orderNum'].'  代收款订单数 :'.$allPrice['count'].'件   代收款金额:'.$allPrice['price'];?>
<?php ActiveForm::end(); ?>



    <table class="table">
    <tr>
    	<th>票数</th>
    	<th>代收款</th>
    	<th>运费</th>
    </tr>
    <tr>
    	<td><?= empty($count['order_num'])?0:$count['order_num']?></td>
    	<td><?= empty($count['order_price'])?0:$count['order_price']?></td>
    	<td><?= empty($count['freight'])?0:$count['freight']?></td>
    </tr>
    </table>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'logistics_sn',
            'ship_logistics_sn',
//             'goods_sn',
//             'order_sn',
            'freight',
            'goods_price',
//             [
//                 'label' => '垫付',
//                 'attribute' => 'advance',
//                 'value' => function($model){
//                     return $model->getAdvanceShow($model->ship_logistics_sn);
//                 },
//                 'filter'=>['1'=>'已追回','2'=>'已垫付']
            
//             ],
                [
                    'label' => '订单类型',
                    'attribute' => 'order_type',
                    'value' => function($model){
                    switch ($model->order_type)
                    {
                        case '1':
                            return '西部';
                            break;
                        case '3':
                            return '瑞胜';
                            break;
                        case '4':
                            return '塔湾';
                            break;
                        default:
                            return '无';
                            break;
                    }
                    },
                    'filter'=>['1'=>'西部','3'=>'瑞胜','4'=>'塔湾']
    
                ],
                [
                    'label' => '退回类型',
                    'attribute' => 'return_type',
                    'value' => function($model){
                        return $model->getReturnTypeName($model->return_type);
                    },
                    'filter'=>['1'=>'返货','2'=>'退货','3'=>'追回']
                ],
                [
                'attribute' => 'order_state',
                'label' => '订单状态',
                'value' => function($model, $key, $index, $column) {
                    switch ($model->order_state)
                    {
                        case '10':
                            return '已开单';
                            break;
                        case '20':
                            return '待分拨';
                            break;
                        case '30':
                            return '待入库';
                            break;
                        case '50':
                            return '待送货';
                            break;
                        case '70':
                            return '已收款';
                            break;
                        default:
                            return 0;
                    }
                },
                'headerOptions' =>[
                        'width'=>'100px',
                ],
                'filter'=>['10'=>'已开单','20'=>'待分拨','30'=>'待入库','50'=>'待送货','70'=>'已收款']
            ],
            // 'make_from_price',
            // 'goods_num',
            // 'order_state',
            // 'state',
            // 'abnormal',
            // 'collection',
            // 'collection_poundage_one',
            // 'collection_poundage_two',
            // 'order_type',
            // 'return_type',
            // 'add_time',
            'member_name',
            // 'member_id',
            // 'member_cityid',
            'member_phone',
            'receiving_name',
            'receiving_phone',
            [
                'label'=>'开单时间',
                'attribute'=>'add_time',
                'value'=>function($model){
                    return date('Y-m-d H:i:s',$model->add_time);
                 }
            ],
            [
                'label' => '开单员',
                'attribute' => 'trueName',
                'value' => 'trueName.user_truename',
            ],
            [
                'label' => '送货员',
                'attribute' => 'senderName',
                'value' => 'senderName.sender',
            ],
            // 'receiving_name_area',
            // 'receiving_provinceid',
            // 'receiving_cityid',
            // 'receiving_areaid',
            // 'terminus_id',
            // 'logistics_route_id',
            // 'shipping_type',

            ['class' => 'yii\grid\ActionColumn',
             'template' => $template,
             'buttons' => [
                'update' => function ($url, $model, $key) {
                    if($model->order_state<50&&$model->return_type!=3){
                        if($model->return_type==1){
                            $url = '?r=return/update&id='.$model->order_id;
                        }else{
                            $url = '?r=return/update2&id='.$model->order_id;
                        }
                         return Html::a('修改', $url);
                    }
                 },
				 'view' => function ($url, $model, $key) {
				 if($model->return_type==1||$model->return_type==3){
				     $url = '?r=return/view&id='.$model->order_id;
				 }else if($model->return_type==2){
				     $url = '?r=return/view2&id='.$model->order_id;
				 }
                    return Html::a('查看', $url);
                 },
                 'sender' => function ($url, $model, $key) {
                     if($model->order_state == 50 && $model->isSender($model->order_id) && $model->order_type == $model->getIdentity()){
                         return Html::a('送货', 'javascript:;', 
                                 [
                                     'class' => 'operation',
                                     'data-toggle' => 'modal',
                                     'data-target' => '#create-modal',
                                     'data-order-id' => $model->order_id,
                         ]);
                     }
                 },

                 /*0.0
                  * 添加删除按钮
                  */
                 'delete' => function ($url, $model, $key) {
//                     var_dump($url);exit();
                     $options = [
                         'title' => Yii::t('yii', 'View'),
                         'aria-label' => Yii::t('yii', 'View'),
                         'data-confirm' => '是否删除订单'.$model->logistics_sn.'?',
                         'data-method' => 'post',
                         'data-pjax' => '0',
                     ];

//    /*在此处判断，只有当满足条件的用户才可以看到删除按钮*/
                      if(($model->order_state==10 && $model->return_type==2)&&$model->employee_id==Yii::$app->user->id){
                         return Html::a('删除', $url, $options);
                     }
                 },
                ]
            ],
            
        ],
    ]); ?>
</div>
<?php Modal::begin([
        'id' => 'create-modal',
        'header' => '<h4 class="modal-title">填写送货人</h4>',
]);
?>
<div class="payDiv">
    <div class="payInput">
    	<?=Html::input('text','sender','',['id'=>'sender','class' => 'form-control pay-input']);?>
    	<?=Html::input('hidden','order_id','',['id'=>'order_id','class' => 'form-control pay-input']);?>
    </div>
    <div class="row payButtonDiv">
    	<div class="col-md-6 payButton"><?php echo Html::button('确定', ['id'=>'confirm','class'=>'btn btn-primary']);?></div>
    	<div class="col-md-6 payButton"><?php echo Html::button('取消', ['class'=>'btn btn-primary','data-dismiss'=>'modal']);?></div>
    </div>
</div>
<?php Modal::end();?>
