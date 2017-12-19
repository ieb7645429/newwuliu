<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use frontend\assets\SmallPrintAsset;
SmallPrintAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\SmallPrint */

$this->title = $model->time;
$this->params['breadcrumbs'][] = ['label' => 'Small Prints', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="small-print-view">
<?= Html::submitButton('打印', ['class' => 'btn btn-primary small-print']) ?>
<?= Html::input('hidden', 'print_id',$print_id ,['id'=>'print_id']) ?>
    <h1><?= Html::encode($this->title) ?></h1>
	<table class="table table-striped">
	<tr>
		<th>票号</th>
		<th>修理厂</th>
		<th>件数</th>
		<th>代收款</th>
		<th>运费</th>
	</tr>
	<?php
	   $all_goods_num = 0;
	   $all_amount = 0;
	   $all_freight = 0;
	   foreach($model->data as $key => $value){
       if(!empty($value->goods_num)){
           $all_goods_num += $value->goods_num;
       }
       $all_amount += $value->goods_price;
       $all_freight += $value->freight + $value->make_from_price - $value->shipping_sale;
    ?>
	<tr>
		<td><?=$value->logistics_sn?></td>
		<td><?=$value->receiving_name?></td>
		<td><?=empty($value->goods_num)?'':$value->goods_num?></td>
		<td><?=$value->goods_price?></td>
		<td><?=$value->freight + $value->make_from_price?></td>
	</tr>
	<?php }?>
	<tr>
		<td><?=count($model->data)?></td>
		<td></td>
		<td><?=$all_goods_num==0?'':$all_goods_num?></td>
		<td><?=$all_amount?></td>
		<td><?=$all_freight?></td>
	</tr>
	</table>
    

</div>
