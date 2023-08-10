<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "content_files".
 *
 * @property int $id
 * @property string $path
 * @property string $file_name
 * @property string $file_source_name
 * @property string $dir
 * @property string|null $source_url
 * @property int $source_file_time
 * @property int|null $old
 * @property string $path_old
 *
 * @property AttractionsPhotoJoin[] $attractionsPhotoJoins
 * @property AttractionsPhotoJoin[] $attractionsPhotoJoins0
 * @property ShowsPhotoJoin[] $showsPhotoJoins
 * @property ShowsPhotoJoin[] $showsPhotoJoins0
 * @property TrAttractions[] $trAttractions
 * @property TrAttractions[] $trAttractions0
 * @property TrShows[] $trShows
 * @property TrShows[] $trShows0
 */
class _source_ContentFiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'content_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['path', 'file_name', 'file_source_name', 'dir', 'path_old'], 'required'],
            [['source_file_time', 'old'], 'integer'],
            [['path', 'source_url', 'path_old'], 'string', 'max' => 256],
            [['file_name', 'file_source_name'], 'string', 'max' => 128],
            [['dir'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'path' => 'Path',
            'file_name' => 'File Name',
            'file_source_name' => 'File Source Name',
            'dir' => 'Dir',
            'source_url' => 'Source Url',
            'source_file_time' => 'Source File Time',
            'old' => 'Old',
            'path_old' => 'Path Old',
        ];
    }

    /**
     * Gets query for [[AttractionsPhotoJoins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttractionsPhotoJoins()
    {
        return $this->hasMany(AttractionsPhotoJoin::class, ['photo_id' => 'id']);
    }

    /**
     * Gets query for [[AttractionsPhotoJoins0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttractionsPhotoJoins0()
    {
        return $this->hasMany(AttractionsPhotoJoin::class, ['preview_id' => 'id']);
    }

    /**
     * Gets query for [[ShowsPhotoJoins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShowsPhotoJoins()
    {
        return $this->hasMany(ShowsPhotoJoin::class, ['photo_id' => 'id']);
    }

    /**
     * Gets query for [[ShowsPhotoJoins0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShowsPhotoJoins0()
    {
        return $this->hasMany(ShowsPhotoJoin::class, ['preview_id' => 'id']);
    }

    /**
     * Gets query for [[TrAttractions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrAttractions()
    {
        return $this->hasMany(TrAttractions::class, ['image_id' => 'id']);
    }

    /**
     * Gets query for [[TrAttractions0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrAttractions0()
    {
        return $this->hasMany(TrAttractions::class, ['preview_id' => 'id']);
    }

    /**
     * Gets query for [[TrShows]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrShows()
    {
        return $this->hasMany(TrShows::class, ['image_id' => 'id']);
    }

    /**
     * Gets query for [[TrShows0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrShows0()
    {
        return $this->hasMany(TrShows::class, ['preview_id' => 'id']);
    }
}
