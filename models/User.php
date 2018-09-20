<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use Yii;

class User extends ActiveRecord
{
    public $rememberMe = true;
    public static function tableName()
    {
        return '{{%user}}';
    }

    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'useremail'=>'电子邮箱',
            'userpass'=>'密码'
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
            ['useremail','required','message'=>'电子邮箱不能为空','on'=>['regbymail','maillogin']],
            ['useremail','email','message'=>'电子邮箱格式不正确','on'=>['regbymail','maillogin']],
            ['useremail','unique','message'=>'电子邮箱已存在','on'=>['regbymail']],

            ['username','required','message'=>'用户名不能为空','on'=>['regbymail']],
            ['username','unique','message'=>'用户名格式不正确','on'=>['regbymail']],
            ['userpass','required','message'=>'密码不能为空','on'=>['regbymail','maillogin']],
            ['userpass','validatePass','on'=>['maillogin']],
            
            ['rememberMe','boolean','on'=>['maillogin']],
        ];
    }

    public function validatePass()
    {
        if(!$this->hasErrors()){
            $user = self::find()->where('useremail = :email',[':email' => $this->useremail])->one();
            if(is_null($user)){
                $this->addError('useremail','电子邮箱不存在！');
                return false;
            }
            if($user->userpass != md5($this->userpass)){
                $this->addError('userpass','密码不正确！');
                return false;
            }
        }
    }

    public function reg()
    {
        $this->userpass = md5($this->userpass);
        return (bool) $this->save(false);
    }
    public function regByMail($data){
        $this->scenario = 'regbymail';
        $data['User']['username'] = 'black_cat_'.uniqid();
        $data['User']['userpass'] = uniqid();
        if($this->load($data) &&$this->validate()){
            $mailer = Yii::$app->mailer->compose('createuser',['username'=>$data['User']['username'],'userpass'=>$data['User']['userpass']])->setFrom('caigan2008@163.com')->setTo($this->useremail)->setSubject('黑猫商城-新建用户');
            return (bool)($mailer->send() && $this->reg());
        }
        return false;
    }

    public function mailLogin($data)
    {
        $this->scenario = 'maillogin';
        if($this->load($data) && $this->validate()){
            $time = $this->rememberMe?3600*24:0;
            session_set_cookie_params($time);
            $session = Yii::$app->session;   
            $session['loginname'] = $this->useremail;
            $session['isLogin'] = 1;
            return (bool)$session['isLogin'];
        }
        return false;
    }
}
