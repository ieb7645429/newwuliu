<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\db\Query;
use mdm\admin\components\MenuHelper;

class EntityController extends Controller
{
    public function actionIndex()
    {
        $onlineTime = time() - 600;
        $params =[
            'filter' => 'inactive_time:'.$onlineTime,
            'page_index' =>1,
            'page_size'=>1000,
            'ak' => '8Pg5gm5k70KMyfSbZZsS4cDKgF742ZuG',
            'service_id' => '151478'
        ];
        $result = $this->httpGet( 'http://yingyan.baidu.com/api/v3/entity/search?'. http_build_query($params));

        $data = json_decode($result, true);

        // 定义数组索引
        $entities = array_column($data['entities'],null,'entity_name');

        // 查询状态为1的离线实体
        $result = (new Query())->select('*')->from('app_login a')
            ->join('LEFT JOIN', 'user u','a.user_id = u.id' )
            ->where(['status'=>1])->andWhere(['u.id'=>array_keys($entities)])->indexBy('id')->all();
        // 格式化数据
        foreach ($result as $key => & $rows) {
            $rows['address'] = $this->getAddress($entities[$key]['latest_location']);
            $rows['loc_time'] = date('Y-m-d H:i:s', $entities[$key]['latest_location']['loc_time']);
        }

        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $result
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'menus' => $this->_getMenus()
        ]);
    }

    /**
     * 根据经纬度获取地址
     * @param array $params
     * @return string
     */
    protected function getAddress($location)
    {
        $params =[
            'location'=> $location['latitude'].','.$location['longitude'],
            'ak' => '8Pg5gm5k70KMyfSbZZsS4cDKgF742ZuG',
            'output'=>'json'
        ];

        $dataGeo = $this->httpGet('http://api.map.baidu.com/geocoder/v2/?'. http_build_query($params));
        $dataGeo = json_decode($dataGeo);

        if ($dataGeo->result->formatted_address !== '') {
            $address = $dataGeo->result->formatted_address;
        } else {
            $address = $dataGeo->result->addressComponent->city . ', ' . $dataGeo->result->addressComponent->country;
        }
        return $address;
    }

    protected function httpGet($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $content = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);
        if(intval($status["http_code"])==200){
            return $content;
        }else{
            return false;
        }
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
            'index' => ['menu' => '/entity/index', 'item' => false],
        );
        return $arr[Yii::$app->controller->action->id];
    }

}
