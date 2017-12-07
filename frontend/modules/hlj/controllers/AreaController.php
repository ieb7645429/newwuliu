<?php

namespace frontend\modules\hlj\controllers;

use Yii;
use frontend\modules\hlj\models\Area;
use yii\web\Response;

class AreaController extends \yii\web\Controller
{
    public function actionIndex()
    {
//         return $this->render('index');
    }
    
    public function actions()
    {
        $actions=parent::actions();
        $actions['get-region']=[
            'class'=>\chenkby\region\RegionAction::className(),
            'model'=>Area::className()
        ];
        return $actions;
    }
    
    public function actionSearch() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $area = new Area();
        $areaList = $area->getAreaList(Yii::$app->request->get('key'));
        
        if($areaList){
            return ['code' => 200, 'msg' => '成功', 'datas' => $areaList];
        } else {
            return ['code' => 201, 'msg' => '没有物流线路！'];
        }
    }

}
