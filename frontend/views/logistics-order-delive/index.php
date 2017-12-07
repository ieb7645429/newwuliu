<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use frontend\controllers\LogisticsOrderDeliveController;
use frontend\assets\DeliveAsset;
DeliveAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsOrderDeliveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '配送信息管理';
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
	   <?php $form = ActiveForm::begin(['method'=>'get'])?>
	   <?= Html::input('hidden', 'status', '50', ['id' => 'status']) ?>
	   
	   <?= $form->field($searchModel, 'condition_time_by')
	   			 ->label('时间筛选规则')
  				 ->dropDownList( [
  				 	'2'=>'开单时间',
  				 	'3'=>'封车时间',
  				 	'4'=>'已完成时间'
	  			],
  				[
//  				 	'prompt' => '请选择时间查询条件',
  				 	'options'=>[$condition_time_by=>['Selected'=>true]]
	  			])
	   ?>
	   <?= $form->field($searchModel, 'condition_time')
	   			->label('')
	   			->widget(DateRangePicker::classname(), [
	       'convertFormat'=>true,
	       'presetDropdown'=>true,
	       'model'=>$searchModel,
	       'options' => [
	           'class' => 'form-control',
	           'value' => !empty($condition_time) ? $condition_time : date('Y-m-d') .' - ' . date('Y-m-d') ,
	       ],
	       'pluginOptions'=>[
	           'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
	       ]
	    ])?>
  	<div class="form-group">
  	 	<?= Html::hiddenInput('download_type', '0', ['id' => 'download_type']) ?>
        <?php  //= Html::submitButton('查询', ['class' => 'btn btn-primary', 'id' => 'searchButton']) ?>
        <?php echo Html::Button('查询', ['class' => 'btn btn-primary', 'id' => 'searchButtonq']); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <?php echo Html::Button('导出Excel', ['class' => 'btn btn-warning', 'id' => 'downloadExcel']); ?>
   </div>
   <?php ActiveForm::end(); ?>
   <?php $template = '{view1} {stamp}';?> 
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//             ['class' => 'yii\grid\SerialColumn'],
//         		'order_id',
        	[
        		'label' => '编号',
        		'attribute' => 'driverUserName',
        		'value' => 'driverUserName.username',
        	],
        	[
	        	'label' => '司机',
	        	'attribute' => 'driverTrueName',
	        	'value' => 'driverTrueName.user_truename',
        	],
        		[
        		'label' => '配送票数',
        		'value' => 'countnum',
        		],
//         	[
// 	        	'label' => '配送票数',
// 	        	'attribute' => 'memberOrderNum',
// 	        	'value' => function($model) {
// 	        	return $model->getMemberOrderNum($model->driver_member_id);
// 	        	},
//         	],        		
	        [
	        	'header' => '明细(点击查看)',
	        	'class' => 'yii\grid\ActionColumn',
	        	'template' => $template,
	        	'buttons' => [
	        		'view1' => function ($url, $model, $key) {
		        		 return Html::Button('查看', 
		        		 	[
	        		 			'class' => "btn btn-xs btn-danger js-print1",
	        		 			'data-driver'=>$model->driver_member_id
		        			 ]
		        		 );
	        		},
	        	]
	        ],
        ],
    ]); ?>
</div>
