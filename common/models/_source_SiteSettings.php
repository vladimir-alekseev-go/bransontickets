<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "site_settings".
 *
 * @property int $id
 * @property string|null $recaptcha_site_key
 * @property string|null $recaptcha_secret
 * @property string|null $mailchimp_key
 * @property string|null $mailchimp_list_id
 * @property string|null $google_api_key
 * @property string|null $googletagmanager
 * @property int|null $logo_id
 * @property int|null $logo_mobile_id
 * @property int|null $favicon_id
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $font_style
 * @property string $color_brand
 * @property string $color_brand_lighten_30
 * @property string $color_brand_darken_10
 * @property string|null $color_neutral_a
 * @property string|null $color_neutral_b
 * @property string|null $site_name
 * @property string|null $fax
 * @property string|null $service_email
 * @property int|null $logo_print_id
 * @property int $show_heared_menu
 * @property string|null $view_params
 * @property string|null $google_map_key
 * @property string|null $logo_link
 * @property string|null $default_date
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property ContentFiles $favicon
 * @property ContentFiles $logo
 * @property ContentFiles $logoMobile
 * @property ContentFiles $logoPrint
 */
class _source_SiteSettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'site_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['logo_id', 'logo_mobile_id', 'favicon_id', 'logo_print_id', 'show_heared_menu'], 'integer'],
            [['default_date', 'created_at', 'updated_at'], 'safe'],
            [['recaptcha_site_key', 'recaptcha_secret', 'mailchimp_key', 'mailchimp_list_id', 'google_api_key', 'googletagmanager', 'email', 'phone', 'font_style', 'site_name', 'fax', 'service_email', 'google_map_key'], 'string', 'max' => 64],
            [['color_brand', 'color_brand_lighten_30', 'color_brand_darken_10', 'color_neutral_a', 'color_neutral_b'], 'string', 'max' => 22],
            [['view_params'], 'string', 'max' => 2048],
            [['logo_link'], 'string', 'max' => 128],
            [['logo_id'], 'unique'],
            [['favicon_id'], 'unique'],
            [['logo_print_id'], 'unique'],
            [['favicon_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentFiles::class, 'targetAttribute' => ['favicon_id' => 'id']],
            [['logo_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentFiles::class, 'targetAttribute' => ['logo_id' => 'id']],
            [['logo_mobile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentFiles::class, 'targetAttribute' => ['logo_mobile_id' => 'id']],
            [['logo_print_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentFiles::class, 'targetAttribute' => ['logo_print_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'recaptcha_site_key' => 'Recaptcha Site Key',
            'recaptcha_secret' => 'Recaptcha Secret',
            'mailchimp_key' => 'Mailchimp Key',
            'mailchimp_list_id' => 'Mailchimp List ID',
            'google_api_key' => 'Google Api Key',
            'googletagmanager' => 'Googletagmanager',
            'logo_id' => 'Logo ID',
            'logo_mobile_id' => 'Logo Mobile ID',
            'favicon_id' => 'Favicon ID',
            'email' => 'Email',
            'phone' => 'Phone',
            'font_style' => 'Font Style',
            'color_brand' => 'Color Brand',
            'color_brand_lighten_30' => 'Color Brand Lighten 30',
            'color_brand_darken_10' => 'Color Brand Darken 10',
            'color_neutral_a' => 'Color Neutral A',
            'color_neutral_b' => 'Color Neutral B',
            'site_name' => 'Site Name',
            'fax' => 'Fax',
            'service_email' => 'Service Email',
            'logo_print_id' => 'Logo Print ID',
            'show_heared_menu' => 'Show Heared Menu',
            'view_params' => 'View Params',
            'google_map_key' => 'Google Map Key',
            'logo_link' => 'Logo Link',
            'default_date' => 'Default Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Favicon]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFavicon()
    {
        return $this->hasOne(ContentFiles::class, ['id' => 'favicon_id']);
    }

    /**
     * Gets query for [[Logo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLogo()
    {
        return $this->hasOne(ContentFiles::class, ['id' => 'logo_id']);
    }

    /**
     * Gets query for [[LogoMobile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLogoMobile()
    {
        return $this->hasOne(ContentFiles::class, ['id' => 'logo_mobile_id']);
    }

    /**
     * Gets query for [[LogoPrint]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLogoPrint()
    {
        return $this->hasOne(ContentFiles::class, ['id' => 'logo_print_id']);
    }
}
