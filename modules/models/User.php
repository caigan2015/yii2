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

class User extends ActiveRecord
{
    public $repass ;
    public static function tableName()
    {
        return "{{%user}}";
    }

    public function attributeLabels()
    {
        return [
            'username'=>'用户账号',
            'useremail'=>'用户邮箱',
            'userpass'=>'用户密码',
            'repass'=>'确认密码',
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
    
    public function getProfile(){
        return $this->hasOne(Profile::className(),['userid'=>'userid']);
    }
    public function rules()
    {
        return [
            ['username','required','message'=>'用户账号不能为空','on'=>['login','seekpass','changepass','useradd','changemail']],
            ['username','unique','message'=>'用户账号已存在','on'=>['useradd']],
            ['userpass','required','message'=>'用户密码不能为空','on'=>['login','changepass','useradd','changemail']],
            ['useremail','required','message'=>'用户邮箱不能为空','on'=>['seekpass','useradd','changemail']],
            ['useremail','email','message'=>'用户邮箱格式不正确','on'=>['seekpass','useradd','changemail']],
            ['useremail','unique','message'=>'用户邮箱已存在','on'=>['useradd','changemail']],
            ['useremail','validateEmail','on'=>['seekpass']],
            ['rememberMe','boolean','on'=>['login']],
            ['userpass','validatePass','on'=>['login','changemail']],
            ['repass','required','message'=>'确认密码不能为空','on'=>['changepass','useradd']],
            ['repass','compare','compareAttribute'=>'userpass','message'=>'两次输入密码不一致','on'=>['changepass','useradd']]
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

    public function reg($data)
    {
        $this->scenario = 'useradd';
        if($this->load($data) && $this->validate()){
            $this->userpass = md5($this->userpass);
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