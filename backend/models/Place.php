<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "place".
 *
 * @property integer $id
 * @property string $name_place
 *
 * @property PlaceEvent[] $placeEvents
 * @property Event[] $events
 */
class Place extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'place';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name_place'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_place' => 'Name Place',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaceEvents()
    {
        return $this->hasMany(PlaceEvent::className(), ['place_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Event::className(), ['id' => 'event_id'])->viaTable('place_event', ['place_id' => 'id']);
    }
}
