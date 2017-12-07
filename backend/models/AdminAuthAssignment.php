<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "admin_auth_assignment".
 *
 * @property string $item_name
 * @property string $user_id
 * @property int $created_at
 *
 * @property AdminAuthItem $itemName
 */
class AdminAuthAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_auth_assignment';
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
            [['item_name'], 'exist', 'skipOnError' => true, 'targetClass' => AdminAuthItem::className(), 'targetAttribute' => ['item_name' => 'name']],
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
     * @return \yii\db\ActiveQuery
     */
    public function getItemName()
    {
        return $this->hasOne(AdminAuthItem::className(), ['name' => 'item_name']);
    }
    
    /**
     * 取得收款员
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getUserByRoles() {
        $query = self::find();
        return $query -> innerJoin('admin_user', 'admin_auth_assignment.user_id = admin_user.id')
                      -> select('admin_user.id, admin_user.user_truename')
                      -> where(['in', 'item_name', [Yii::$app->params['roleTellerIncomeLeader'], Yii::$app->params['roleTellerIncome']]])
                      -> asArray()
                      -> all();
    }
}
