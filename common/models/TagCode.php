<?php
namespace common\models;

use Yii;
/**
 * TagCode model
 *

 */
class TagCode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tagcode';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           // ['code_total_num','required']

        ];
    }


    public function getcode()
    {
	    return $this->find()->asArray()->one();
    }
    public function setcode($code){
	   $data = $this->find()->one();
	   if(empty($data)){
	    $data = $this;
	   }
	   $data->code_total_num = $code;
	   return $data->save();
	}
}
