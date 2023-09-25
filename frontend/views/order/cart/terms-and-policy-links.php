<?php
/**
 * @var Basket $Basket
 */

use common\models\Basket;
use yii\helpers\Url;

if ($Basket->hasHotel()) {?>
    <span class="cancellation-policy">I have read and accept
    <a class="js-popup-cancellation-policy" href="#" data-url="<?= Url::to(['order/price-line-terms-conditions'])?>">Terms and Conditions</a>,
    <a class="js-popup-cancellation-policy" href="#" data-url="<?= Url::to(['order/price-line-privacy-policy'])?>">Privacy Policy</a>,
    <a class="js-popup-cancellation-policy" href="#" data-url="<?= Url::to(['order/cancellation-policy'])?>">Policy</a></span>
<?php } else {?>
    <span class="cancellation-policy">I have read and accept terms of 
    <a class="js-popup-cancellation-policy" href="#" data-url="<?= Url::to(['order/cancellation-policy'])?>">Cancellation policy</a></span>
<?php }?>