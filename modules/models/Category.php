<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/21
 * Time: 16:48
 */

namespace app\modules\models;


use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Category extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%category}}';
    }

    public function attributeLabels()
    {
        return [
            'parentid'=>'上级分类',
            'title'=>'分类标题',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class'=>TimestampBehavior::className(),
                'createdAtAttribute'=>'createtime',
                'updatedAtAttribute'=>'updatetime',
                'value'=>time(),
            ],
        ];
    }

    public function rules()
    {
        return [
            ['parentid','required','message'=>'上级分类不能为空'],
            ['title','required','message'=>'分类标题不能为空']
        ];
    }
    public function add($data)
    {
        if($this->load($data) && $this->validate()){
            return (bool)$this->save(false);
        }
        return false;
    }
}