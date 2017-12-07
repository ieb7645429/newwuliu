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
        'data-target' => '#create-null',
        'class' => 'btn btn-success create',
]);
Modal::begin([
        'id' => 'create-modal',
        'header' => '<h4 class="modal-title">申请提现</h4>',
]);?>

<div class="payDiv">
<div class="payInput">
<?=Html::input('text','money','',['id'=>'money','class' => 'form-control pay-input','placeholder'=>'请选择提现订单','readonly'=>'readonly']);?>
    </div>
    <div class="payPromptDiv"><div class="payPrompt">输入金额必须为整数</div></div>
    <div class="row payButtonDiv">
	<div class="col-md-6 payButton"><?php echo Html::button('确定', ['id'=>'confirm','class'=>'btn btn-primary','disabled'=>'true']);?></div>
	<div class="col-md-6 payButton"><?php echo Html::button('取消', ['class'=>'btn btn-primary','data-dismiss'=>'modal']);?></div>
</div>
</div>
<?php Modal::end();
$requestUrl = Url::toRoute('payable-div');
?>
<input type="hidden" id="actionUrl" value="<?=$requestUrl?>">
<input type="hidden" id="withdrawal_amount" value="<?=intval($withdrawal_amount)?>">
<div class="withdrawal-log-index">
<?php echo $this->render('_search', ['model' => $withdrawalLog,'add_time'=>$add_time]); ?>
<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
             [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'order_arr',
                 'checkboxOptions' => function($searchModel, $key, $index, $column) {
                    return [
                            'value' => $searchModel->order_sn,
                            'data-price' => $searchModel->amount,
                        ];
                 },
                 'headerOptions' => ['style'=>'width:50px;'],
             ],
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

<?php $js = <<<JS
    $(document).on('click', 'input', function () {
        var order_arr =[];
        $('input[name="order_arr[]"]:checked').each(function(){
            order_arr.push($(this).val()); 
        });
        if(order_arr.length==0){
            $('#create').attr({'data-target':'#create-null'});
        }else{
            $('#create').attr({'data-target':'#create-modal'});
        }
    })

    $(document).on('click', '#create', function () {
        var order_arr =[];
        var total = 0;
        $('input[name="order_arr[]"]:checked').each(function(){
            order_arr.push($(this).val()); 
            total = accAdd(total,parseFloat($(this).data('price')));
        });
        if(order_arr.length==0){
            alert('请选择提现订单');
            $('#money').attr({'value':'0'});
            $('#confirm').prop('disabled',true);
            return false;
        }else{
            $('#money').attr({'value':total});
            $('#confirm').prop('disabled',false);
        }
    });
    function accAdd(arg1, arg2) {
        var r1, r2, m, c;
        try {r1 = arg1.toString().split(".")[1].length;}catch (e) {r1 = 0;}
        try {r2 = arg2.toString().split(".")[1].length;}catch (e) {r2 = 0;}
        c = Math.abs(r1 - r2);
        m = Math.pow(10, Math.max(r1, r2));
        if (c > 0) {
            var cm = Math.pow(10, c);
            if (r1 > r2) {
                arg1 = Number(arg1.toString().replace(".", ""));
                arg2 = Number(arg2.toString().replace(".", "")) * cm;
            } else {
                arg1 = Number(arg1.toString().replace(".", "")) * cm;
                arg2 = Number(arg2.toString().replace(".", ""));
            }
        } else {
            arg1 = Number(arg1.toString().replace(".", ""));
            arg2 = Number(arg2.toString().replace(".", ""));
        }
        return (arg1 + arg2) / m;
    }
        
JS;
$this->registerJs($js);
?>