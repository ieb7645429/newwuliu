<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use frontend\modules\dl\assets\MemberPayableAsset;
MemberPayableAsset::register($this);

$this->title = '可提现余额';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
echo Html::a('申请提现', '#', [
        'id' => 'create',
        'data-toggle' => 'modal',
        'data-target' => '#create-modal',
        'class' => 'btn btn-success create',
]);
Modal::begin([
        'id' => 'create-modal',
        'header' => '<h4 class="modal-title">申请提现</h4>',
]);?>
<?php if($withdrawal_amount==0):?>
<div class="payDiv">
<div class="payInput">
<?=Html::input('text','money','',['id'=>'money','class' => 'form-control pay-input','placeholder'=>'输入提现金额']);?>
    </div>
    <div class="payPromptDiv"><div class="payPrompt">输入金额必须为整数</div></div>
    <div class="row payButtonDiv">
	<div class="col-md-6 payButton"><?php echo Html::button('确定', ['id'=>'confirm','class'=>'btn btn-primary','disabled'=>'true']);?></div>
	<div class="col-md-6 payButton"><?php echo Html::button('取消', ['class'=>'btn btn-primary','data-dismiss'=>'modal']);?></div>
</div>
</div>
<?php else:?>
<div class="payDiv">
<div class="payInput">
<?=Html::input('text','money',$withdrawal_amount,['id'=>'money','class' => 'form-control pay-input','readonly'=>'readonly']);?>
    </div>
    <div class="payPromptDiv"><div class="payPrompt">输入金额必须为整数</div></div>
    <div class="row payButtonDiv">
	<div class="col-md-6 payButton"><?php echo Html::button('确定', ['id'=>'confirm','class'=>'btn btn-primary']);?></div>
	<div class="col-md-6 payButton"><?php echo Html::button('取消', ['class'=>'btn btn-primary','data-dismiss'=>'modal']);?></div>
</div>
</div>
<?php endif;?>
<?php Modal::end();
$requestUrl = Url::toRoute('payable-div');
?>
<input type="hidden" id="actionUrl" value="<?=$requestUrl?>">
<input type="hidden" id="withdrawal_amount" value="<?=intval($withdrawal_amount)?>">
<div class="withdrawal-log-index">
<?php echo $this->render('_search', ['model' => $withdrawalLog,'add_time'=>$add_time]); ?>
<div style="height:30px;line-height: 30px">可提现余额:<?=$withdrawal_amount?></div>
<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
             [
                   'attribute' => 'order_sn',
                   'value' => function($model){
                        return empty($model->order_sn)?'':$model->order_sn;
                    },
                   'headerOptions' => ['style'=>'width:25%;'],
               ],
               [
                   'label' => '订单编号',
                   'attribute' => 'orderSn',
                   'value' => function($model){
                        if(!empty($model->orderSn->order_sn)){
                            if(!is_numeric($model->orderSn->order_sn)){
                                 return unserialize($model->orderSn->order_sn);
                            }
                            return $model->orderSn->order_sn;
                        }
                        return '';
                    },
                    'headerOptions' => ['style'=>'width:25%;'],
               ],
//             'id',
//             'uid',
              [
                    'attribute' => 'amount', 
                    'value' => function($model){
                        return $model->getViewAmount($model->type).$model->amount;
                    }
                ],
                [
                'attribute' => 'add_time',
                'value' => function ($model) {
                return date('Y-m-d H:i:s',$model->add_time);
                }
                ],
//             [
//                 'label' => '操作前金额',
//                 'attribute' => 'before_amount'
//             ],
//             [
//                 'label' => '操作后金额',
//                 'attribute' => 'after_amount'
//             ],
//             // 'content:ntext',
//             [
//                 'label' => '操作',
//                 'attribute' => 'type',
//                 'value' => function($model){
//                     return $model->getType($model->type);
//                 }
//             ],
        ],
    ]); ?>
</div>