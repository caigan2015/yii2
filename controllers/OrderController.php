<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 14:58
 */

namespace app\controllers;


use yii\web\Controller;

class OrderController extends Controller
{
    public $layout = false;

    public function actionIndex()
    {
        $this->layout = 'layout_2';
        return $this->render('index');
    }
    public function actionCheck()
    {
        $this->layout = 'layout_1';
        return $this->render('check');
    }
}