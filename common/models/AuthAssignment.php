<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "auth_assignment".
 *
 * @property string $item_name
 * @property string $user_id
 * @property int $created_at
 *
 * @property AuthItem $itemName
 */
class AuthAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'],
            [['created_at'], 'integer'],
            [['item_name', 'user_id'], 'string', 'max' => 64],
            [['item_name', 'user_id'], 'unique', 'targetAttribute' => ['item_name', 'user_id']],
            [['item_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthItem::className(), 'targetAttribute' => ['item_name' => 'name']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_name' => 'Item Name',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @Author:Fenghuan
     * @param $id
     * @return mixed
     */
    public function findModel($id)
    {
        if (($model = self::findOne($id)) !== null) {
            return $model;
        } else {
            return;
        }
    }

    /**
     * 查询多条记录
     * @Author:Fenghuan
     * @param $condition
     * @param $fields
     * @return array|\yii\db\ActiveRecord[]
     */
    public function selectItems($condition, $fields)
    {
        return self::find()->innerJoin('user_all', 'auth_assignment.user_id = user_all.id')->select($fields)->where($condition)->asArray()->all();
    }

    /**
     * @Author:Fenghuan
     * @param $condition
     * @return static
     */
    public function getOneItem($condition)
    {
        return self::findOne($condition);
    }

    /**
     * 更新一条记录
     * @Author:Fenghuan
     * @param $data
     * @param $condition
     * @return int
     */
    public function updateItem($data,$condition)
    {
        return self::updateAll($data,$condition);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemName()
    {
        return $this->hasOne(AuthItem::className(), ['name' => 'item_name']);
    }

    /**
     * @desc 保存权限为用户
     * @author 暴闯
     * @param unknown $userId
     * @return boolean
     */
    public function saveMember($userId ,$roleName = '用户') {
        $this->item_name = $roleName;
        $this->user_id = strval($userId);
        $this->created_at = time();
        return $this->save();
    }
}
