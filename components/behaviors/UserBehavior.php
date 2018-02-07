<?php
/**
 * Created by Valerii Tikhomirov
 * E-mail: <v.tikhomirov.dev@gmail.com>
 * Date: 08.02.2018, 0:55
 */

namespace app\components\behaviors;

use app\models\UserStatusChanges;

/**
 * Class UserBehavior
 */
class UserBehavior extends \yii\base\Behavior
{
    const EVENT_STATUS_CHANGE = 'user-status-change';

    /**
     * @return array
     */
    public function events()
    {
        return [
            self::EVENT_STATUS_CHANGE => 'userStatusChange',
        ];
    }

    /**
     * Add log user status change
     * @throws \yii\db\Exception
     */
    public function userStatusChange()
    {
        /* @var $owner \app\models\User */
        $owner = $this->owner;
        $us = new UserStatusChanges();
        $us->setAttributes([
            'user_id' => $owner->id,
            'status' => $owner->status,
        ]);
        if (!$us->save()) {
            throw new \yii\db\Exception(\Yii::t('exception', 'Can not save user status change record #{id}', [
                'id' => $owner->id,
            ]));
        }
    }
}
