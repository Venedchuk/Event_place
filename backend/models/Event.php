<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "event".
 *
 * @property integer $id
 * @property string $event_name
 * @property string $date
 *
 * @property PlaceEvent[] $placeEvents
 * @property Place[] $places
 */
class Event extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['event_name'], 'string', 'max' => 255],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_name' => 'Event Name',
            'date' => 'Date',
			//'place' => 'Place',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceEvents()
    {
        return $this->hasMany(PlaceEvent::className(), ['event_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaces()
    {
        return $this->hasMany(Place::className(), ['id' => 'place_id'])->viaTable('place_event', ['event_id' => 'id']);
    }
}
