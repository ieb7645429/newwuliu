<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,

    // 订单状态
    'orderStateCancel' => 0,
    'orderStateMember' => 5,
    'orderStateEmployee' => 10,
    'orderStateDivide' => 20,
    'orderStateFerry' => 30,
    'orderStateDivide2' => 40,
    'orderStateDriver' => 50,
    'orderStateTerminus' => 60,
    'orderStateDelivery' => 70,
    'orderStateComplete' => 80,
    
    // 退货订单状态
    'returnOrderStateCancel' => 0,
    'returnOrderStateMember' => 5,
    'returnOrderStateEmployee' => 10,
    'returnOrderStateDivide' => 20,
    'returnOrderStateFerry' => 30,
    'returnOrderStateDivide2' => 40,
    'returnOrderStateDriver' => 50,
    'returnOrderStateTerminus' => 60,
    'returnOrderStateDelivery' => 70,
    'returnOrderStateComplete' => 80,

    // 订单钱状态
    'orderBuyOut' => 1,
    'orderNotBuyOut' => 2,
    'orderReceived' => 4,
    //退货订单钱状态
    'returnOrderPayment'=>1,
    'returnOrderNotPayment'=>2,
    
    // 角色
    'roleTeller' => '财务',
    'roleTellerIncomeLeader' => '财务同城收款组长',
    'roleTellerIncome' => '财务同城收款',
];
