<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 14:20
 */

namespace app\models;


use yii\db\ActiveRecord;

class Test extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user}}';
    }
}