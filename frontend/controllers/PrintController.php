<?php

namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use mdm\admin\components\MenuHelper;
use common\models\TagCode;
class PrintController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
	/**
     * 取得menus
     * @return array[]|string[]
     */
    private function _getMenus()
    {
        $menus = MenuHelper::getAssignedMenu(Yii::$app->user->id);
        $items = array();
        
        $activeMenus = $this->_getActiveMenu();

        foreach ($menus as &$menu) {
            if ($menu['url'][0] == $activeMenus['menu']) {
                $menu['active'] = 'active';
                if($activeMenus['item'] !== false && isset($menu['items'])) {
                    foreach ($menu['items'] as &$item) {
                        if($item['url'][0] == $activeMenus['item']) {
                            $item['active'] = 'active';
                            break;
                        }
                    }
                    $items = $menu['items'];
                }
            }
        }
        return ['menus' => $menus, 'items' => $items];
    }
    
    private function _getActiveMenu() {
        $arr = array(
            'tag' => ['menu' => '/print/tag', 'item' => false],
          
        );

        return $arr[Yii::$app->controller->action->id];
    }
	/**
	*  贴纸打印
	*  独立打印,不传参
	*  2017-11-16
	*  xiaoyu
	**/
    public function actionTag() {
		$_code  = '';
		$model = new TagCode();
		//获取code值
		$code  = $model->getcode();
		//if(empty($code)){
          $_code = empty($code)?str_pad(1,9,0,STR_PAD_LEFT):$code['code_total_num'];
		//}
        return $this->render('print',[ 'menus' => $this->_getMenus(),'code' => $_code]);
	}
    /**
	* ajax调用tagcode表获取当前编码并修改
	**/
	public function actionAjaxGetCode(){
		$newcode  = array(); 
	    $model    = new TagCode();
		$num      = Yii::$app->request->post('num');
		//获得当前code值
		$code     = $model->getcode();
        $_code    = empty($code)?0:$code['code_total_num'];        
		//循环并输出code值
		for($i=1;$i<=$num;$i++){
		   $t = $_code+$i;
		   $newcode[] = str_pad($t,9,0,STR_PAD_LEFT); ; 
		}
		//保存最新code值
		$total = ($_code+$num);
		$total = str_pad($total,9,0,STR_PAD_LEFT); 
        $save  = $model->setcode($total);
		if($save){
		    echo json_encode(array('code'=>200,'datas'=>$newcode,'msg'=>'成功'));
		}
		else{
		    echo json_encode(array('code'=>100,'datas'=>$newcode,'msg'=>'失败'));
		}
		
	}
}

