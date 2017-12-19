<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\grid\GridView;
use frontend\assets\MemberAsset;
MemberAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '物流单列表';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<style>
.form-group{
    width:50%;
}
</style>
<div class="logistics-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
	<?php $form = ActiveForm::begin(['method'=>'get'])?>
    <?= $form->field($searchModel, 'add_time')->label('开单时间')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$searchModel,
            'options' => [
                'class' => 'form-control',
                'value' => !empty(Yii::$app->request->get('LogisticsOrderSearch')['add_time']) ? Yii::$app->request->get('LogisticsOrderSearch')['add_time'] : date('Y-m-d') .' - ' . date('Y-m-d') ,
            ],
            'pluginOptions'=>[
                'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
            ]
   ])?>
   
    <p>
    <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('发货', ['create-order'], ['class' => 'btn btn-success']) ?>
      <!--   <?php echo Html::button('打印', ['class'=>'btn btn-primary js-loading-print']) ?>-->
		<span style="display: block;width:260px;color:red;float:right;">以下情况不赔付:玻璃和外包装无破损货物</span>
    </p>
	 <?php ActiveForm::end(); ?>
	 
	 <?php //$form = ActiveForm::begin(['action'=>['member/print'],'method'=>'post','id'=>'print'])?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//             ['class' => 'yii\grid\SerialColumn'],
            [
			'class'=>\yii\grid\CheckboxColumn::className(),
                'checkboxOptions' => function ($model, $key, $index, $column) {
                  return ['value'=>$model->order_id,'class'=>'checkbox'];
                }
			],
//             'order_id',
            [
                'attribute' => 'logistics_sn',
                'value' => function($model){
                    if(!empty($model->return_logistics_sn)){
                        return $model->logistics_sn."(已原返)";
                    }
                    return $model->logistics_sn;
                }
            ],
            [
                'attribute' => 'order_sn',
                'value' => function($model){
                if(!empty($model->order_sn)&&!is_numeric($model->order_sn)){
                    return unserialize($model->order_sn);
                }
                return $model->order_sn;
            }
            ],
//             'order_sn',
            'freight',
            'goods_price',
            // 'make_from_price',
            'goods_num',
            [
                'attribute' => 'order_state',
                'value' => function($model, $key, $index, $column) {
                    return $model -> getOrderStateName($model->order_state,$model);
                },
                'headerOptions' =>[
                        'width'=>'120px',
                ],
                'filter'=>['5'=>'用户下单','10'=>'已开单','50'=>'已封车','71'=>'待送货','72'=>'已送货']
            ],
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
            // 'state',
            // 'abnormal',
//             'collection',
            // 'collection_poundage_one',
            // 'collection_poundage_two',
            // 'order_type',
            // 'add_time',
            'member_name',
            // 'member_id',
//             [
//                 'attribute' => 'memberCityName.area_name',
//                 'label' => '发货人城市'
//             ],
            'member_phone',
            'receiving_name',
            'receiving_phone',
            'receiving_name_area',
            [
                'label' => '收货人市',
                'attribute' => 'receivingCityName.area_name',
            		'contentOptions' => [
            				'width'=>'80'
            		],
            ],
            [
                'label' => '收货人区',
                'attribute' => 'receivingAreaName.area_name',
            ],
            // 'terminus_id',
            // 'logistics_route_id',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update}',
                'visibleButtons' => [
                    'update' => function ($model, $key, $index) {
                        return $model->	order_state== '5';
                    },
                ],
				'buttons' => [
						 'view' => function ($url, $model, $key) {
                    return Html::a('查看', $url);
                 },
				],
            ],
        ],
    ]); ?>
</div>
	 <?php //ActiveForm::end(); ?>