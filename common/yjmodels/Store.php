<?php

namespace common\yjmodels;

use Yii;

/**
 * This is the model class for table "qp_store".
 *
 * @property int $store_id 店铺索引id
 * @property string $store_name 店铺名称
 * @property int $grade_id 店铺等级
 * @property int $member_id 会员id
 * @property string $member_name 会员名称
 * @property string $seller_name 店主卖家用户名
 * @property int $sc_id 店铺分类
 * @property string $store_company_name 店铺公司名称
 * @property string $province_id 店铺所在省份ID
 * @property int $city_id 	id
 * @property string $area_info 地区内容，冗余数据
 * @property string $store_address 详细地区
 * @property string $store_zip 邮政编码
 * @property int $store_state 店铺状态，0关闭，1开启，2审核中
 * @property string $store_close_info 店铺关闭原因
 * @property int $store_sort 店铺排序
 * @property string $store_time 店铺时间
 * @property string $store_end_time 店铺关闭时间
 * @property string $store_label 店铺logo
 * @property string $store_banner 店铺横幅
 * @property string $store_avatar 店铺头像
 * @property string $store_keywords 店铺seo关键字
 * @property string $store_description 店铺seo描述
 * @property string $store_qq QQ
 * @property string $store_ww 阿里旺旺
 * @property string $store_phone 商家电话
 * @property string $store_zy 主营商品
 * @property string $store_domain 店铺二级域名
 * @property int $store_domain_times 二级域名修改次数
 * @property int $store_recommend 推荐，0为否，1为是，默认为0
 * @property string $store_theme 店铺当前主题
 * @property int $store_credit 店铺信用
 * @property double $store_desccredit 描述相符度分数
 * @property double $store_servicecredit 服务态度分数
 * @property double $store_deliverycredit 发货速度分数
 * @property string $store_collect 店铺收藏数量
 * @property string $store_slide 店铺幻灯片
 * @property string $store_slide_url 店铺幻灯片链接
 * @property string $store_stamp 店铺印章
 * @property string $store_printdesc 打印订单页面下方说明文字
 * @property string $store_sales 店铺销量
 * @property string $store_presales 售前客服
 * @property string $store_aftersales 售后客服
 * @property string $store_workingtime 工作时间
 * @property string $store_free_price 超出该金额免运费，大于0才表示该值有效
 * @property string $store_decoration_switch 店铺装修开关(0-关闭 装修编号-开启)
 * @property int $store_decoration_only 开启店铺装修时，仅显示店铺装修(1-是 0-否
 * @property string $store_decoration_image_count 店铺装修相册图片数量
 * @property string $live_store_name 商铺名称
 * @property string $live_store_address 商家地址
 * @property string $live_store_tel 商铺电话
 * @property string $live_store_bus 公交线路
 * @property int $is_own_shop 是否自营店铺 1是 0否
 * @property int $bind_all_gc 自营店是否绑定全部分类 0否1是
 * @property string $store_vrcode_prefix 商家兑换码前缀
 * @property int $store_baozh 保证服务开关
 * @property int $store_baozhopen 保证金显示开关
 * @property string $store_baozhrmb 保证金金额
 * @property int $store_qtian 7天退换
 * @property int $store_zhping 正品保障
 * @property int $store_erxiaoshi 两小时发货
 * @property int $store_tuihuo 退货承诺
 * @property int $store_shiyong 试用中心
 * @property int $store_shiti 实体验证
 * @property int $store_xiaoxie 消协保证
 * @property int $store_huodaofk 货到付款
 * @property string $store_free_time 商家配送时间
 * @property string $mb_title_img 手机店铺 页头背景图
 * @property string $mb_sliders 手机店铺 轮播图链接地址
 * @property string $deliver_region 店铺默认配送区域
 * @property string $store_slide1 店铺幻灯片
 * @property string $store_slide_url1 店铺幻灯片链接
 * @property int $is_insurance 是否为保险商户
 * @property int $is_union 是否为联盟商户
 * @property int $store_commis 分佣比例
 * @property int $is_collect_freight 	是否收运费(1不收,2收)
 * @property int $buckle_price 扣钱的百分比(0 不扣,3扣百分之3,50扣百分之50)	
 * @property int $store_integrity 诚信卖家开关，1是，0否
 * @property string $small_num 小号
 */
class Store extends \yii\db\ActiveRecord
{
    const SCENARIO_EDIT = 'edit';
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_EDIT] = [];
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qp_store';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_name', 'grade_id', 'member_id', 'member_name', 'sc_id', 'area_info', 'store_address', 'store_zip', 'store_time'], 'required'],
            [['grade_id', 'member_id', 'sc_id', 'province_id', 'city_id', 'store_state', 'store_sort', 'store_domain_times', 'store_recommend', 'store_credit', 'store_collect', 'store_sales', 'store_decoration_switch', 'store_decoration_only', 'store_decoration_image_count', 'is_own_shop', 'bind_all_gc', 'store_baozh', 'store_baozhopen', 'store_qtian', 'store_zhping', 'store_erxiaoshi', 'store_tuihuo', 'store_shiyong', 'store_shiti', 'store_xiaoxie', 'store_huodaofk', 'is_insurance', 'is_union', 'store_commis', 'is_collect_freight', 'buckle_price', 'store_integrity'], 'integer'],
            [['store_zy', 'store_slide', 'store_slide_url', 'store_presales', 'store_aftersales', 'mb_sliders', 'store_slide1', 'store_slide_url1'], 'string'],
            [['store_desccredit', 'store_servicecredit', 'store_deliverycredit', 'store_free_price'], 'number'],
            [['store_name', 'member_name', 'seller_name', 'store_company_name', 'store_qq', 'store_ww', 'store_domain', 'store_theme', 'deliver_region'], 'string', 'max' => 50],
            [['area_info', 'store_address', 'store_workingtime'], 'string', 'max' => 100],
            [['store_zip', 'store_time', 'store_end_time', 'store_baozhrmb', 'store_free_time', 'small_num'], 'string', 'max' => 10],
            [['store_close_info', 'store_label', 'store_banner', 'store_keywords', 'store_description', 'live_store_name', 'live_store_address', 'live_store_tel', 'live_store_bus'], 'string', 'max' => 255],
            [['store_avatar', 'mb_title_img'], 'string', 'max' => 150],
            [['store_phone'], 'string', 'max' => 20],
            [['store_stamp'], 'string', 'max' => 200],
            [['store_printdesc'], 'string', 'max' => 500],
            [['store_vrcode_prefix'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'store_id' => 'Store ID',
            'store_name' => 'Store Name',
            'grade_id' => 'Grade ID',
            'member_id' => 'Member ID',
            'member_name' => 'Member Name',
            'seller_name' => 'Seller Name',
            'sc_id' => 'Sc ID',
            'store_company_name' => 'Store Company Name',
            'province_id' => 'Province ID',
            'city_id' => 'City ID',
            'area_info' => 'Area Info',
            'store_address' => 'Store Address',
            'store_zip' => 'Store Zip',
            'store_state' => 'Store State',
            'store_close_info' => 'Store Close Info',
            'store_sort' => 'Store Sort',
            'store_time' => 'Store Time',
            'store_end_time' => 'Store End Time',
            'store_label' => 'Store Label',
            'store_banner' => 'Store Banner',
            'store_avatar' => 'Store Avatar',
            'store_keywords' => 'Store Keywords',
            'store_description' => 'Store Description',
            'store_qq' => 'Store Qq',
            'store_ww' => 'Store Ww',
            'store_phone' => 'Store Phone',
            'store_zy' => 'Store Zy',
            'store_domain' => 'Store Domain',
            'store_domain_times' => 'Store Domain Times',
            'store_recommend' => 'Store Recommend',
            'store_theme' => 'Store Theme',
            'store_credit' => 'Store Credit',
            'store_desccredit' => 'Store Desccredit',
            'store_servicecredit' => 'Store Servicecredit',
            'store_deliverycredit' => 'Store Deliverycredit',
            'store_collect' => 'Store Collect',
            'store_slide' => 'Store Slide',
            'store_slide_url' => 'Store Slide Url',
            'store_stamp' => 'Store Stamp',
            'store_printdesc' => 'Store Printdesc',
            'store_sales' => 'Store Sales',
            'store_presales' => 'Store Presales',
            'store_aftersales' => 'Store Aftersales',
            'store_workingtime' => 'Store Workingtime',
            'store_free_price' => 'Store Free Price',
            'store_decoration_switch' => 'Store Decoration Switch',
            'store_decoration_only' => 'Store Decoration Only',
            'store_decoration_image_count' => 'Store Decoration Image Count',
            'live_store_name' => 'Live Store Name',
            'live_store_address' => 'Live Store Address',
            'live_store_tel' => 'Live Store Tel',
            'live_store_bus' => 'Live Store Bus',
            'is_own_shop' => 'Is Own Shop',
            'bind_all_gc' => 'Bind All Gc',
            'store_vrcode_prefix' => 'Store Vrcode Prefix',
            'store_baozh' => 'Store Baozh',
            'store_baozhopen' => 'Store Baozhopen',
            'store_baozhrmb' => 'Store Baozhrmb',
            'store_qtian' => 'Store Qtian',
            'store_zhping' => 'Store Zhping',
            'store_erxiaoshi' => 'Store Erxiaoshi',
            'store_tuihuo' => 'Store Tuihuo',
            'store_shiyong' => 'Store Shiyong',
            'store_shiti' => 'Store Shiti',
            'store_xiaoxie' => 'Store Xiaoxie',
            'store_huodaofk' => 'Store Huodaofk',
            'store_free_time' => 'Store Free Time',
            'mb_title_img' => 'Mb Title Img',
            'mb_sliders' => 'Mb Sliders',
            'deliver_region' => 'Deliver Region',
            'store_slide1' => 'Store Slide1',
            'store_slide_url1' => 'Store Slide Url1',
            'is_insurance' => 'Is Insurance',
            'is_union' => 'Is Union',
            'store_commis' => 'Store Commis',
            'is_collect_freight' => 'Is Collect Freight',
            'buckle_price' => 'Buckle Price',
            'store_integrity' => 'Store Integrity',
            'small_num' => 'Small Num',
        ];
    }
    /**
     * 靳健
     * 物流会员账号修改,同时更改友件store信息
     * @param unknown $model
     */
    public function storeInfoEdit($model){
        $store_model = $this::findOne(['member_name'=>$model->getOldAttribute('username')]);
        if(!empty($store_model)){
            $store_model->scenario = 'edit';
            $store_model->member_name = $model->username;
            $store_model->seller_name = $model->username;
            $store_model->store_name = $model->user_truename;
            $store_model->store_company_name = $model->user_truename;
            return $store_model->save();
        }
        return true;
    }
}
