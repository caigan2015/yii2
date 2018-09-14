<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 15:28
 */

namespace app\controllers;


use yii\web\Controller;

class MemberController extends Controller
{
    public $layout = false;

    public function actionAuth()
    {
        $this->layout = 'layout_2';
        return $this->render('auth');
    }
}