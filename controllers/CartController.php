<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 14:55
 */

namespace app\controllers;


use yii\web\Controller;

class CartController extends Controller
{
    public $layout = false;
    public function actionIndex()
    {
        $this->layout = 'layout_1';
        return $this->render('index');
    }
}