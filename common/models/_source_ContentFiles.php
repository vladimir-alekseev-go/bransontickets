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
 * @property string|null $path_old
 *
 * @property ShowsPhotoJoin[] $showsPhotoJoins
 * @property ShowsPhotoJoin[] $showsPhotoJoins0
 * @property TrShows[] $trShows
 * @property TrShows[] $trShows0
 * @property TrShows[] $trShows1
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
            [['path', 'file_name', 'file_source_name', 'dir'], 'required'],
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
        return $this->hasMany(TrShows::class, ['seat_map_id' => 'id']);
    }

    /**
     * Gets query for [[TrShows1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrShows1()
    {
        return $this->hasMany(TrShows::class, ['preview_id' => 'id']);
    }
}
