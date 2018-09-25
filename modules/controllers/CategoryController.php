<?php
/**
 * Created by PhpStorm.
<<<<<<< HEAD
 * User: Administrator
 * Date: 2018/9/21
 * Time: 16:44
=======
 * User: caigan
 * Date: 2018-09-21
 * Time: 22:01
>>>>>>> d286863785578295dc25a735ddfcbbe38c3ca171
 */

namespace app\modules\controllers;
use app\modules\models\Category;
use yii\web\Controller;

class CategoryController extends Controller
{
    public function actionList()
    {
        $this->layout = 'main';
        $model = new Category();
        $cates = $model->getTreeList();
        return $this->render('cates',['model'=>$model,'cates'=>$cates]);
    }

    public function actionAdd()
    {
        $this->layout = 'main';
        $model = new Category();
        $list = $model->getOptions();
        if(\Yii::$app->request->isPost){
            $post = \Yii::$app->request->post();
            if($model->add($post)){
                \Yii::$app->session->setFlash('info','添加成功！');
            }
        }
        return $this->render('add',['model'=>$model,'list'=>$list]);
    }

    public function actionMod()
    {
        $this->layout = 'main';
        $model = new Category();
        if(\Yii::$app->request->isPost){
            $post = \Yii::$app->request->post();
            if($model->mod($post)){
                \Yii::$app->session->setFlash('info','修改成功');
            }
        }else{
            
            $cateid = \Yii::$app->request->get('cateid');
            $model = $model->find()->where('cateid = :id',[':id'=>$cateid])->one();
        }

        $list = $model->getOptions();
        return $this->render('add',['model'=>$model,'list'=>$list]);
    }

    public function actionDel()
    {
        try{
            $cateid = \Yii::$app->request->get('cateid');
            if(!$cateid){
                throw new \Exception('获取分类ID失败');
            }

            $children = Category::find()->where('parentid = :id',[':id'=>$cateid])->one();
            if(!empty($children)){
                throw new \Exception('不能删除有下级的分类');
            }
            if(!(Category::deleteAll('cateid = :id',[':id'=>$cateid]))){
                throw new \Exception('删除失败');
            }
        }catch (\Exception $e){
            \Yii::$app->session->setFlash('info',$e->getMessage());
        }
        return $this->redirect(['category/list']);
    }
}