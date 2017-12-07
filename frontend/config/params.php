<?php
return [
    'adminEmail' => 'admin@example.com',
    
    // 商品状态
    'goodsStateCancel' => 0,
    'goodsStateEmployee' => 10,
    'goodsStateDivide' => 20,
    'goodsStateFerry' => 30,
    'goodsStateDivide2' => 40,
    'goodsStateDriver' => 50,
    'goodsStateTerminus' => 60,
    'goodsStateDelivery' => 70,
    'goodsStateComplete' => 80,
    'goodsStateAbnormal' => 200,//商品挂起状态
    
    //退货商品状态
    'returnGoodsStateCancel' => 0,
    'returnGoodsStateEmployee' => 10,
    'returnGoodsStateDivide' => 20,
    'returnGoodsStateFerry' => 30,
    'returnGoodsStateDivide2' => 40,
    'returnGoodsStateDriver' => 50,
    'returnGoodsStateTerminus' => 60,
    'returnGoodsStateDelivery' => 70,
    'returnGoodsStateComplete' => 80,
    'returnGoodsStateAbnormal' => 200,//商品挂起状态

    // 角色
    'roleMember' => '用户',
    'roleEmployee' => '开单员',
    'roleDriver' => '司机',
    'roleTerminus' => '落地点',
    'roleMoney' => '财务',
    'roleDelivery' => '送货员',
    'PutInStorage' => '入库',
    'roleReturn' => '退货员',
    'roleAllReturn' => '同城员',
    'roleDriverManager' => '司机领队',
    'roleUserManager' => '用户管理',
    'roleDriverManagerCityWide' => '司机同城领队',
    'roleEmployeeDelete' => '开单员-删除',
		
		
    //区号
    'city_num'=>'024',
    'provinceId'=>'6',
    
    //名称
    'logistics_sn'=>'票号',
    'goods_sn'=>'货号',
    
    //分页
    'page_size'=>20,
];
