<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\EventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Events';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
		$dataProvider->pagination->pageSize=10;
	?>

    <?= GridView::widget([
		'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,

		'columns' => [
			['class' => 'yii\grid\SerialColumn'],
			'id',
			'event_name',
			[
				'attribute' => 'date',
				'value' => 'date',
				'filter' => kartik\widgets\DatePicker::widget([
					'model'=>$searchModel,
					'name'=>'updated_at',
					'language' => 'ru',
					//'value' => date('Y-m-d', $searchModel->date),

					'pluginOptions' => [
						'format' => 'yyyy-mm-dd',
						'startView'=> 2,
						'autoclose'=>true,
						'todayHighlight' => true
					],
				]),
				'format' => 'html',
			],
			['class' => 'yii\grid\ActionColumn'],
		],
	]); ?>
</div>