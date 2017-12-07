<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use frontend\modules\hlj\models\LogisticsOrder;
use frontend\modules\hlj\assets\EmployeeIndexAsset;

EmployeeIndexAsset::register($this);

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

    <hr style="border-top:1px solid #ccc"></hr>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a('发货', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php $form = ActiveForm::begin(['method'=>'get'])?>
    <?= $form->field($searchModel, 'add_time')->label('开单时间')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$searchModel,
            'options' => [
                'class' => 'form-control',
                'value' => !empty(Yii::$app->request->get('LogisticsOrderSearch')['add_time']) ? Yii::$app->request->get('LogisticsOrderSearch')['add_time'] : date('Y-m-d H:i:s',strtotime(date('Y-m-d'))) .' - ' . date('Y-m-d H:i:s') ,
            ],
            'pluginOptions'=>[
                'timePicker'=>true,
                'locale'=>['format'=>'Y-m-d H:i:s', 'separator'=>' - ',]
            ]
   ])?>
   <div class="form-group">
        <?= Html::hiddenInput('download_type', '0', ['id' => 'download_type']) ?>
        <?= Html::Button('查询', ['class' => 'btn btn-primary', 'id' => 'searchButton']) ?>
        <?php echo Html::Button('导出Excel', ['class' => 'btn btn-warning', 'id' => 'downloadExcel']); ?>
    <div class="div-print">自动打印<input id="checkbox-input" class="checkbox-input" type="checkbox" <?php if($is_print==1) echo 'checked';?>></div>
    </div>
	<?php //echo '商品数量:'.$goods_num.'件  今日订单:'.($orderNum['1']['orderNum']+$orderNum['2']['orderNum']).'  今日同城订单:'.$orderNum['1']['orderNum'].'  代收款订单数 :'.$allPrice['count'].'件   代收款金额:'.$allPrice['price'];?>
    <?php ActiveForm::end(); ?>
    <?php if(empty($indexOver))$template = '{view} {update} {delete} {return}';else$template = '{view} {return}';?>
    <table class="table">
    <tr>
    	<th>票数</th>
    	<th>件数</th>
    	<th>代收票数</th>
    	<th>代收总金额</th>
    </tr>
    <tr>
    	<td><?=empty($count['order_num'])?0:$count['order_num']?></td>
    	<td><?=empty($count['goods_num'])?0:$count['goods_num']?></td>
    	<td><?=empty($count['price_count'])?0:$count['price_count']?></td>
    	<td><?=empty($count['price'])?0:$count['price']?></td>
    </tr>
    </table>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//             ['class' => 'yii\grid\SerialColumn'],

//             'order_id',
            'member_name',
            [
                'label' => '发货人市',
                'attribute' => 'memberCityName',
                'value' => 'memberCityName.area_name',
                'contentOptions' => [
                        'width'=>'80'
                ],
            ],
            'member_phone',
            'receiving_name',
            'receiving_phone',
            //             'receiving_name_area',
            [
                    'label' => '收货人市',
                    'attribute' => 'receivingCityName',
                    'value' => 'receivingCityName.area_name',
                    'contentOptions' => [
                        				'width'=>'80'
                    ],
            ],
            [
                'attribute' => 'order_type',
                'label' => '类型',
                'filter' => [1 => '通达', 3 => '宣化'],
                'value' => function($model){
                return LogisticsOrder::getOrderType($model->order_type);
                },
                'contentOptions' => ['width' => '40px'],
            ],
            [
                'label' => '线路',
                'attribute' => 'routeName',
                'value' => 'routeName.logistics_route_name',
            ],
            [
                'label' => '开单员',
                'attribute' => 'trueName',
                'value' => 'trueName.user_truename',
            ],
            [
                'label' => '垫付',
                'attribute' => 'pay_for',
                'value' => function($model){
                if($model->pay_for == 1) return '是';
                if($model->pay_for == 0) return '否';
                },
                
                'options' => [
                        'width'=>'50'
                ],
            ],

            [
                'attribute' => 'logistics_sn',
                'value' => function($model){
                    if(!empty($model->return_logistics_sn)){
                        return $model->logistics_sn."(已原返)";
                    }
                    return $model->logistics_sn;
                }
            ],
//             [
//                 'attribute' => 'order_sn',
//                 'value' => function($model){
//                     if(!empty($model->order_sn)&&!is_numeric($model->order_sn)){
//                         return unserialize($model->order_sn);
//                     }
//                     if(empty($model->order_sn))
//                         return '';
//                     return $model->order_sn;
//                 }
//             ],
//             [
//                 'label' => '会员号',
//                 'attribute' => 'userName',
//                 'value' => 'userName.username',
//             ],
            'freight',
//             [
//                 'label' => '公司扣',
//                 'attribute' => 'scale',
//                 'contentOptions' => [
//                         'width'=>'60'
//                 ],
//             ],
//             [
//                 'label' => '司机收',
//                 'attribute' => 'driver_get',
//                 'value' => function($model){
//                     return $model->freight-$model->scale;
//                 },
//                 'contentOptions' => [
//                         'width'=>'60'
//                 ],
//             ],
            'goods_price',
            // 'make_from_price',
            [
                'label' => '是否代收',
                'attribute' => 'collection',
                'value' => function($model){
                    return $model->collection == 1 ? '代收' : '不代收';
                },
                'filter' =>[1=>'代收', 2 => '不代收'],
                'options' =>[
                        'width'=>'80px',
                ],
            ],
            'goods_num',
//             [
//                 'attribute' => 'order_state',
//                 'value' => function($model, $key, $index, $column) {
//                     return $model -> getOrderStateName($model->order_state,$model);
//                 },
//                 'headerOptions' =>[
//                         'width'=>'120px',
//                 ],
//                 'filter'=>['5'=>'用户下单','10'=>'已开单','50'=>'已封车','71'=>'待送货','72'=>'已送货']
//             ],
//             [
//                 'label' => '垫付',
//                 'attribute' => 'advance',
//                 'value' => function($model){
//                     return $model->getAdvanceShow($model->order_id);
//                 },
//                 'filter'=>['1'=>'已追回','2'=>'已垫付']
                
//             ],
            // 'state',
            // 'abnormal',
//             'collection',
            // 'collection_poundage_one',
            // 'collection_poundage_two',
            // 'order_type',
            // 'add_time',
            [
                'attribute' => 'add_time',
                'label' => '开单时间',
                'options' =>[
                        'width'=>'100px',
                ],
                'value' => function($model){
                    return date('Y-m-d H:i:s',$model->add_time);
                }
            ],
            
            // 'member_id',
//             [
//                 'attribute' => 'memberCityName.area_name',
//                 'label' => '发货人城市'
//             ],
//             'member_cityid',
            
            
            
            
            
            
//             [
//                 'label' => '收货人区',
//                 'attribute' => 'receivingAreaName.area_name',
//             ],
//             'receiving_cityid',
            // 'receiving_areaid',
            // 'terminus_id',
            // 'logistics_route_id',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $template,
                'buttons' => [
                'return' => function ($url, $model, $key) {
                        if(empty($model->return_logistics_sn)){
                            $url = '?r=hlj/return/create&order_id='.$model->order_id;
                            return Html::a('原返', $url,['title' => '原返']);
                        }else{
                            return '';
                        }
                    },
					'view' => function ($url, $model, $key) {
                    $options = [
                        'title' => Yii::t('yii', 'View'),
                        'aria-label' => Yii::t('yii', 'View'),
                        'data-pjax' => '0',
                    ];
                    $url = '?r=hlj/employee/view&id='.$model->order_id;
                    return Html::a('查看', $url, $options);
                 },
				 'update' => function ($url, $model, $key) {
                    $options = [
                        'title' => Yii::t('yii', 'View'),
                        'aria-label' => Yii::t('yii', 'View'),
                        'data-pjax' => '0',
                    ];
                     if(empty($model->return_logistics_sn)){
                         $url = '?r=hlj/employee/update&id='.$model->order_id;
                        return Html::a('修改', $url, $options);
                    }
                 },
                 'delete' => function ($url, $model, $key) {
                     $options = [
                             'title' => Yii::t('yii', 'View'),
                             'aria-label' => Yii::t('yii', 'View'),
                             'data-confirm' => '是否删除订单'.$model->logistics_sn.'?',
                             'data-method' => 'post',
                             'data-pjax' => '0',
                     ];
                     if(empty($model->return_logistics_sn)){
                         $url = '?r=hlj/employee/delete&id='.$model->order_id;
                         return Html::a('删除', $url, $options);
                     }
                 },
                ]
            ],
        ],
        'layout' => "\n{items}\n{pager}",
    ]); ?>
</div>
