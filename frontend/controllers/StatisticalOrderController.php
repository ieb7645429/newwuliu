<?php

namespace frontend\controllers;
use Yii;
use common\models\StatisticalOrder;

class StatisticalOrderController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    public function actionGetInfo()
    {
    	$times = Yii::$app->request->get('time');
    	if(isset($times['bg']) && $times['en'])
    	{
    		$a = ['between','add_time', $times['bg'],$times['en']];
    	}
    }

}
