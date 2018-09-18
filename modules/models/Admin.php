<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/18
 * Time: 14:48
 */

namespace app\modules\models;


use phpDocumentor\Reflection\Types\Boolean;
use yii\db\ActiveRecord;
use yii;

class Admin extends ActiveRecord
{
    public $rememberMe = true;
    public static function tableName()
    {
        return "{{%admin}}";
    }

    public function rules()
    {
        return [
            ['adminuser','required','message'=>'管理员账号不能为空','on'=>['login','seekpass']],
            ['adminpass','required','message'=>'管理员密码不能为空','on'=>['login']],
            ['adminemail','required','message'=>'管理员邮箱不能为空','on'=>['seekpass']],
            ['adminemail','email','message'=>'管理员邮箱格式不正确','on'=>['seekpass']],
            ['adminemail','validateEmail','on'=>['seekpass']],
            ['rememberMe','boolean','on'=>['login']],
            ['adminpass','validatePass','on'=>['login']]
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
            return true;
        }
        return false;
    }

}