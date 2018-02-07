<?php

namespace app\models;

use app\components\behaviors\UserBehavior;
use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $login
 * @property int $status
 * @property string $updated
 */
class User extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            UserBehavior::className(),
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login'], 'required'],
            [['status'], 'integer'],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
            [['updated'], 'safe'],
            [['login'], 'string', 'min' => 4, 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'      => 'ID',
            'login'   => \Yii::t('user', 'Login'),
            'status'  => \Yii::t('user', 'Status'),
            'updated' => \Yii::t('user', 'Updated'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserStatusChanges()
    {
        return $this->hasMany(UserStatusChanges::className(), ['user_id' => 'id']);
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if (array_key_exists('status', $changedAttributes)) {
            $this->trigger(UserBehavior::EVENT_STATUS_CHANGE);
        }
        parent::afterSave($insert, $changedAttributes);
    }
}
