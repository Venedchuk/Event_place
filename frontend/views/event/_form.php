<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models;
use kartik\select2\Select2;
	use kartik\widgets\DatePicker;

	/* @var $this yii\web\View */
/* @var $model backend\models\Event */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'event_name')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'date')->textInput() ?>


	<?= $form->field($model, 'places')->widget(Select2::classname(),[
		'data'  => yii\helpers\ArrayHelper::map(\backend\models\Place::find()->all(),'id','name_place'),
		'language' => 'en',
		'options' =>['multiple' => true,'placeholder' => 'Select place'],
		'pluginOptions' => ['allowClear' => true
		],
	]);
	?>



    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
