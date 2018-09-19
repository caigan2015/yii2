<?php
/**
 * Created by PhpStorm.
 * User: caigan
 * Date: 2018-09-19
 * Time: 22:34
 */

namespace app\modules\models;


use yii\db\ActiveRecord;

class Profile extends ActiveRecord
{
    public static function tableName()
    {
        return "{{%profile}}";
    }
}