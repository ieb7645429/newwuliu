<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use common\models\Area;
use yii\helpers\Url;
use common\models\ShippingTpye;
use common\models\LogisticsOrder;
use backend\assets\Nationwide;

Nationwide::register($this);
/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */




$this->title = '查询全国发货单';
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
    <hr style="border-top:1px solid #ccc"></hr>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!--0.0  发货按钮  -->
    <!--0.0  发货按钮  -->
    <p>
            <!--   <?//= Html::a('发货', ['create'], ['class' => 'btn btn-success']) ?>     -->
    </p>


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


    <!--0.0添加全国城市搜索选择 下拉菜单-->
    <!--0.0添加全国城市搜索选择 下拉菜单-->
    <?php
//            $allStatus=\common\models\Area::find()->select(["area_name","area_id"])->indexBy("area_id")->column();
    ?>
  <!-- 0.0 文本框查询 <?/*= $form->field($searchModel, 'memberCityName')->label('输入查询城市') */?>  -->

  <?= $form->field($searchModel,"memberCityName")->label('选择查询城市')->dropDownList(
          ["沈阳市"=>"沈阳市","哈尔滨市"=>"哈尔滨市","大连市"=>"大连市"]/*$allStatus*/,
      [/*"prompt"=>"请选择查询城市",*/ 'options'=>[$a=>['Selected'=>true]]]);
  ?>
            <?php
//            var_dump($a);
            ?>
<!--    <?php /*echo "<span style=\" font-weight:600\">选择查询城市</span>";*/?>
    <?php
/*    echo "<br>";
    */?>
    <select name="dgLink2" id="dgLink2" style="width: 765px;height: 40px;margin-bottom: 10px;display: block;padding: 6px 12px;
    font-size: 14px;line-height: 1.42857143;color: #555;background-color: #fff;background-image: none;border: 1px solid #ccc;
    border-radius: 4px;transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;" onchange="_jumpMenu('parent',this,0)">
        <option value="#" selected="selected">请选择查询城市</option>
        <option value="http://localhost/wl/backend/web/index.php?r=teller/heilongjiang">哈尔滨市</option>
        <option value="http://localhost/wl/backend/web/index.php?r=teller/nationwide">沈阳市</option>
    </select>-->


    <!--0.0-->
    <div class="form-group">
        <!-- 0.0 JS 自定义跳转地址Button查询按钮     -->
   <?= Html::Button('查询', ['class' => 'btn btn-primary js-print1']) ?>

        <!--0.0 submitButton查询按钮可查询日期-->
   <!-- <?//= Html::submitButton('查询', ['class' => 'btn btn-primary js-print1']) ?>  -->
        <?php // echo Html::resetButton('重置', ['class' => 'btn btn-default']); ?>

    </div>

    <?php ActiveForm::end(); ?>


<!--  0.0 原生   -->
   <!--0.0  自定义下拉框  选择下拉框内容自动提交-->
    <!--<?php /*echo "<span style=\" font-weight:600\">选择查询城市</span>";*/?>
    <?php
/*        echo "<br>";
        */?>
    <select name="dgLink2" id="dgLink2" style="width: 765px;height: 40px;margin-bottom: 10px;display: block;padding: 6px 12px;
    font-size: 14px;line-height: 1.42857143;color: #555;background-color: #fff;background-image: none;border: 1px solid #ccc;
    border-radius: 4px;transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;" onchange="_jumpMenu('parent',this,0)">
        <option value="#" selected="selected">请选择查询城市</option>
        <option value="http://localhost/wl/backend/web/index.php?r=teller/heilongjiang">哈尔滨市</option>
        <option value="http://localhost/wl/backend/web/index.php?r=teller/nationwide">沈阳市</option>
    </select>-->
    <!--0.0  form  表单  外部  查询按钮-->
    <!--<div style="margin-bottom: 15px">
        <?/*= Html::submitButton('查询', ['class' => 'btn btn-primary js-print1']) */?>
    </div>-->
<!--    <button  class="btn btn-primary" style="margin-bottom: 15px" onclick="_designated()">查询</button>-->



    <?php if(empty($indexOver))$template = '{view} {update} {delete} {return}';else$template = '{view} {return}';?>
    <table class="table">
        <tr>
            <th>票数</th>
            <th>件数</th>
            <th>代收票数</th>
            <th>代收总金额</th>
            <th>同城票数</th>
            <th>同城件数</th>
            <th>同城代收票数</th>
            <th>同城代收总金额</th>
        </tr>
        <tr>
            <td><?=empty($count['order_num'])?0:$count['order_num']?></td>
            <td><?=empty($count['goods_num'])?0:$count['goods_num']?></td>
            <td><?=empty($count['price_count'])?0:$count['price_count']?></td>
            <td><?=empty($count['price'])?0:$count['price']?></td>
            <td><?=empty($count['same_city_order'])?0:$count['same_city_order']?></td>
            <td><?=empty($count['same_city_goods'])?0:$count['same_city_goods']?></td>
            <td><?=empty($count['same_city_price_count'])?0:$count['same_city_price_count']?></td>
            <td><?=empty($count['same_city_price'])?0:$count['same_city_price']?></td>
        </tr>
    </table>
    <?php if(empty($indexOver))$template = '{view}';else$template = '{view}';?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//             ['class' => 'yii\grid\SerialColumn'],

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
                'label' => '垫付',
                'attribute' => 'advance',
                'value' => function($model){
                    return $model->getAdvanceShow($model->order_id);
                },
                'filter'=>['1'=>'已追回','2'=>'已垫付']

            ],
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
            'member_name',
            // 'member_id',
//             [
//                 'attribute' => 'memberCityName.area_name',
//                 'label' => '发货人城市'
//             ],
//             'member_cityid',
            [
                'label' => '发货人市',
                'attribute' => 'memberCityName',
                'value' => 'memberCityName.area_name',

                /*"filter"=>\common\models\Area::find()
                    ->select(["area_name","area_id"])
                    ->indexBy("area_id")
                    ->column(),*/

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
                'label' => '线路',
                'attribute' => 'routeName',
                'value' => 'routeName.logistics_route_name',
            ],
            [
                'label' => '司机',
                'attribute' => 'driverTrueName',
                'value' => 'driverTrueName.user_truename',
            ],
            [
                'label' => '开单员',
                'attribute' => 'trueName',
                'value' => 'trueName.user_truename',
            ],
//             [
//                 'label' => '收货人区',
//                 'attribute' => 'receivingAreaName.area_name',
//             ],
//             'receiving_cityid',
            // 'receiving_areaid',
            // 'terminus_id',
            // 'logistics_route_id',



            /*0.0查询、修改、删除按钮*/

            [
//                'header' => '查看详情',
                'class' => 'yii\grid\ActionColumn',
                'template' => $template,
                'buttons' => [
                    'return' => function ($url, $model, $key) {
                        if(!($model->state&4)&&$model->collection==1&&empty($model->return_logistics_sn)&&$model->order_state==70){
                            $url = '?r=return/create&order_id='.$model->order_id;
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
//                            'member_cityid'=>$model->member_cityid,
                        ];
//                        header("Content-Type:text/html;charset=utf-8");
                        $url = $url.'&area_id='.$model->member_cityid;
                        return Html::a('查看', $url, $options);
                    },
                    /*'update' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', 'View'),
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                        ];
                        if($model->order_state==5||$model->order_state==10){
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
                        if(($model->order_state==5||$model->order_state==10)&&$model->employee_id==Yii::$app->user->id&&$model->return_logistics_sn==''){
                            return Html::a('删除', $url, $options);
                        }
                    },*/
                ]
            ],



        ],
        'layout' => "\n{items}\n{pager}",
    ]); ?>
</div>


