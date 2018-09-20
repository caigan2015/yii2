<?php
use yii\bootstrap\ActiveForm;

?>
<!-- ========================================= MAIN ========================================= -->
<main id="authentication" class="inner-bottom-md">
    <div class="container">
        <div class="row">

            <div class="col-md-6">
                <section class="section sign-in inner-right-xs">
                    <h2 class="bordered">
                        <img src="<?php echo Yii::$app->session['userinfo']['figureurl_1']; ?>" alt="">
                        完善您的信息</h2>
                    <p>请您输入您的用户名与密码</p>
                    <?php $form = ActiveForm::begin([
                        'options' => [
                            'class' => 'login-form cf-style-1',
                            'role' => 'form'
                        ],
                        'fieldConfig' => [
                            'template'=>'<div class="field-row">{label}{input}</div>{error}'
                        ]
                    ]) ?>
                    <div class="field-row">
                        <label>昵称</label>
                        <input type="text" disabled="disabled" value="<?php echo Yii::$app->session['userinfo']['nickname']; ?>" class="le-input">
                    </div>
                        <?php echo  $form -> field($model,'username')->textInput(['class'=>'le-input'])?>
                        <?php echo  $form -> field($model,'userpass')->textInput(['class'=>'le-input'])?>
                        <?php echo  $form -> field($model,'repass')->textInput(['class'=>'le-input'])?>
                        <div class="buttons-holder">
                            <?php echo \yii\helpers\Html::submitButton('提交',['class'=>'le-button huge']); ?>
                        </div><!-- /.buttons-holder -->
                    <?php ActiveForm::end(); ?>
                </section><!-- /.sign-in -->
            </div><!-- /.col -->

        </div><!-- /.row -->
    </div><!-- /.container -->
</main><!-- /.authentication -->
<!-- ========================================= MAIN : END ========================================= -->
<script>
    const qqbtn = document.getElementById('qq-login');
    qqbtn.onclick = function(){
        window.location.href = '<?php echo \yii\helpers\Url::to(['member/qqlogin']); ?>'
    }
</script>