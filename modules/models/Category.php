<?php
/**
 * Created by PhpStorm.
 * User: caigan
 * Date: 2018-09-21
 * Time: 22:06
 */

namespace app\modules\models;


use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Category extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%category}}';
    }

    public function attributeLabels()
    {
        return [
            'parentid' => '上级分类',
            'title'=>'分类标题'
        ];
    }

    public function behaviors()
    {
        return[
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
            ['parentid','required','message'=>'上级分类不能为空','on'=>['add','edit']],
            ['title','required','message'=>'分类标题不能为空','on'=>['add','edit']],
            ['title','unique','message'=>'分类标题已存在','on'=>['add']]
        ];
    }
    public function add($data)
    {
        $this->scenario = 'add';
        if($this->load($data) && $this->validate()){
          return (bool)$this->save(false);
        }
        return false;
    }

    public function getData()
    {
        $data = self::find()->all();
        $list = ArrayHelper::toArray($data);
        return $list;
    }

    public function getTree($list,$pid = 0)
    {
        if(empty($list)) return [];
        $data = [];
        foreach ($list as $item) {
            if($item['parentid'] == $pid){
                $data[] = $item;
                $data = array_merge($data,$this->getTree($list,$item['cateid']));
            }
        }

        return $data;
    }

    public function setPrefix($data,$p = "|----")
    {
        $tree = [];
        $num = 1;
        $prefix = [0=>1];
        while($val = current($data)){
            $key= key($data);
            if($key>0){
                if($data[$key-1]['parentid'] != $val['parentid']){
                 $num++;
                }
            }
            if(array_key_exists($val['parentid'],$prefix)){
                $num = $prefix[$val['parentid']];
            }
            $val['title'] = str_repeat($p,$num).$val['title'];
            $tree[] = $val;
            next($data);
        }
        return $tree;
    }

    public function getOptions()
    {
        $tree = $this->setPrefix($this->getTree($this->getData()));
        $list = ['顶级分类'];
        foreach ($tree as $item) {
            $list[$item['cateid']] = $item['title'];
        }

        return $list;
    }

    public function getTreeList()
    {
        return $this->setPrefix($this->getTree($this->getData()));

    }

    public function mod($data)
    {
        $this->scenario = 'edit';
        if($this->load($data) && $this->validate()){
            $result = $this->save();
            if(false!==$result){
                return true;
            }
        }
        return false;
    }
}