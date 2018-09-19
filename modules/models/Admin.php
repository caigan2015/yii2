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

class Admin extends ActiveRecord
{
    public $rememberMe = true;
    public $repass ;
    public static function tableName()
    {
        return "{{%admin}}";
    }

    public function attributeLabels()
    {
        return [
            'adminuser'=>'管理员账号',
            'adminemail'=>'管理员邮箱',
            'adminpass'=>'管理员密码',
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
    public function rules()
    {
        return [
            ['adminuser','required','message'=>'管理员账号不能为空','on'=>['login','seekpass','changepass','adminadd','changemail']],
            ['adminuser','unique','message'=>'管理员账号已存在','on'=>['adminadd']],
            ['adminpass','required','message'=>'管理员密码不能为空','on'=>['login','changepass','adminadd','changemail']],
            ['adminemail','required','message'=>'管理员邮箱不能为空','on'=>['seekpass','adminadd','changemail']],
            ['adminemail','email','message'=>'管理员邮箱格式不正确','on'=>['seekpass','adminadd','changemail']],
            ['adminemail','unique','message'=>'管理员邮箱已存在','on'=>['adminadd','changemail']],
            ['adminemail','validateEmail','on'=>['seekpass']],
            ['rememberMe','boolean','on'=>['login']],
            ['adminpass','validatePass','on'=>['login','changemail']],
            ['repass','required','message'=>'确认密码不能为空','on'=>['changepass','adminadd']],
            ['repass','compare','compareAttribute'=>'adminpass','message'=>'两次输入密码不一致','on'=>['changepass','adminadd']]
        ];
    }

    public function validatePass()
    {
        if(!$this->hasErrors()){
            $data = self::find()->where('adminuser = :user and adminpass = :pass',[':user'=>$this->adminuser,':pass'=>md5($this->adminpass)])->one();
            if(is_null($data)){
                $this->addError('adminpass','用户账号或密码错误！');
            }
        }
    }

    public function validateEmail()
    {
        if(!$this->hasErrors()){
            $data = self::find()->where('adminuser = :user and adminemail = :email',[':user'=>$this->adminuser,':email'=>$this->adminemail])->one();
            if(is_null($data)){
                $this->addError('adminemail','账号或邮箱不正确！');
            }
        }
    }

    public function login($post)
    {
        $this->scenario = 'login';
        if($this->load($post) && $this->validate()){
            $lifetime = $this->rememberMe ? 24* 3600:0;
            $session = Yii::$app->session;
            session_set_cookie_params($lifetime);
            $session['admin']=[
                'adminuser' => $this->adminuser,
                'isLogin' => 1,
            ];
            $this->updateAll(['logintime'=>time(),'loginip'=>ip2long(Yii::$app->request->userIP)],'adminuser = :user',[':user'=>$this->adminuser]);
            return (bool)$session['admin']['isLogin'];
        }
        return false;
    }

    public function seekPass($data)
    {
        $this->scenario = 'seekpass';
        if($this->load($data) && $this->validate()){
            $time = time();
            $token = $this->createToken($data['Admin']['adminuser'],$time);
            $mailer = Yii::$app->mailer->compose('seekpass',['adminuser'=>$data['Admin']['adminuser'],'token'=>$token,'time'=>$time]);
            $mailer->setFrom('caigan2008@163.com')->setTo($data['Admin']['adminemail'])->setSubject('黑猫商城 - 找回密码');
            if($mailer->send()){
                return true;
            }
        }
        return false;
    }

    public function createToken($user,$time)
    {
        return md5(md5($user).base64_encode(Yii::$app->request->userIp).md5($time));
    }

    public function changePass($data)
    {
        $this->scenario = 'changepass';
        if($this->load($data) && $this->validate()){
            $result = $this->updateAll(['adminpass'=>md5($this->adminpass)],'adminuser = :user',[':user'=>$this->adminuser]);
            if($result!==false){
                return true;
            }
        }
        return false;
    }

    public function getList()
    {
        $model = self::find();
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['manage'];
        $pager = new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $manages = $model->orderBy(['createtime'=> SORT_DESC])->offset($pager->offset)->limit($pager->pageSize)->all();
        return ['manages'=>$manages,'pager'=>$pager];
    }

    public function reg($data)
    {
        $this->scenario = 'adminadd';
        if($this->load($data) && $this->validate()){
            $this->adminpass = md5($this->adminpass);
            return (bool)$this->save(false);
        }
        return false;
    }

    public function changemail($data)
    {
        $this->scenario = 'changemail';
        if($this->load($data) && $this->validate()){
            return (bool)self::updateAll(['adminemail'=>$this->adminemail],'adminuser = :user',[':user'=>$this->adminuser]);
        }
        return false;
    }
}