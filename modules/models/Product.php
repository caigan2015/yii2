<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/18
 * Time: 14:48
 */

namespace app\modules\models;

use yii;
use yii\db\ActiveRecord;
use yii\data\Pagination;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;

class Product extends ActiveRecord
{
    const AK = 'Nv-NTj93dt5treBJ3SLFl1lBnowsHL86dHA04m72';
    const SK = '8fV5AsOD-_brNkhev_PrhDvlteRDLYamlZvS_kGD';
    const DOMAIN = 'opctwxwxw.bkt.clouddn.com';
    const BUCKET = 'caigandeweilaishijie';
    public static function tableName()
    {
        return "{{%product}}";
    }

    public function attributeLabels()
    {
        return [
            'cateid'=>'所属分类',
            'title'=>'商品标题',
            'descr'=>'商品描述',
            'num'=>'商品库存',
            'price'=>'商品价格',
            'cover'=>'封面图片',
            'pics'=>'附加图片',
            'issale'=>'是否促销',
            'ishot'=>'是否热销',
            'istui'=>'是否推荐',
            'saleprice'=>'促销价',
            'ison'=>'是否上架',
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
    
    public function getCategory(){
        return $this->hasOne(Category::className(),['cateid'=>'cateid']);
    }

    public function rules()
    {
        return [
            ['cateid','required','message'=>'所属分类不能为空'],
            ['title','required','message'=>'商品标题不能为空'],
            ['descr','required','message'=>'商品描述不能为空'],
            ['num','integer','min'=>0,'message'=>'库存必须是整数'],
            ['price','required','message'=>'商品价格不能为空'],
            [['price','saleprice'],'number','message'=>'价格必须是数字'],
            ['cover','required','message'=>'封面图片不能为空'],
            [['issale','ishot','istui','ison'],'safe']
        ];
    }


    public function getList()
    {
        $model = self::find()->joinWith('profile');
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['user'];
        $pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $users = $model->orderBy(['createtime'=> SORT_DESC])->offset($pager->offset)->limit($pager->pageSize)->all();
        return ['users'=>$users,'pager'=>$pager];
    }

    public function add($data)
    {
        if($this->load($data) && $this->validate()){
            return (bool)$this->save(false);
        }
        return false;
    }

    public function del($userid)
    {
        try{
            $trans = Yii::$app->db->beginTransaction();
            $profile = Profile::find()->where('userid = :userid',[':userid'=>$userid])->one();
            if(!empty($profile)){
                if(!($profile->delete())){
                    throw new Exception();
                }
            }

            if(!(self::deleteAll('userid = :id',[':id'=>$userid]))){
                throw new Exception();
            }

            $trans->commit();
        }catch (\Exception $e){
            if(Yii::$app->db->getTransaction()){
                $trans->rollBack();
                return false;
            }
        }
        return true;
    }

}