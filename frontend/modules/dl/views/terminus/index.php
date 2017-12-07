<?php
/* @var $this yii\web\View */
use frontend\modules\dl\assets\TerminusAsset;
TerminusAsset::register($this);

$this->title = '落地点';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>

<?php if(!empty($carList)){?>
    <table class="table tableTop">
       
       <tbody>
       <thead>
              <tr class="tableBg">
                 <th>车辆id</th>
                 <th>车辆类型</th>
                 <th>线路</th>
                 <th>司机</th>
                 <th>封车时间</th>
                 <th class="thCenter">操作</th>
              </tr>
           </thead>
       <?php 
            foreach($carList as $key=>$value){
        ?>
            
          <tr class="info">
             <td><?php echo $value['logistics_car_id']; ?></td>
             <td>干线</td>
             <td><?php echo $value['route']; ?></td>
             <td><?php echo $value['driver_name']; ?></td>
             <td><?php echo $value['ruck_time']; ?></td>
             <td><a href="?r=terminus/list&driver=<?=$value['driverInfo']['member_id'] ?>"><span class="finish" data-driver-id="<?php echo $value['driverInfo']['member_id']?>">详情</span></a></td>
          </tr>
        <?php }?>
       </tbody>
    </table>
<?php }?>

