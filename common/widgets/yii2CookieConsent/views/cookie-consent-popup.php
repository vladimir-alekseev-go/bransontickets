<?php

use common\widgets\yii2CookieConsent\CookieConsentAsset;
use yii\helpers\Html;

/**
 * @var $save         string
 * @var $learnMore    string
 * @var $link         string
 * @var $message      string
 * @var $acceptAll    string
 * @var $controlsOpen string
 * @var $detailsOpen  string
 * @var $consent      array
 */

CookieConsentAsset::register($this);
?>

<div class="cookie-consent-popup py-3 py-lg-4 py-xl-5">
    <div class="fixed">
        <div class="cookie-consent-top-wrapper">
            <div class="cookie-consent-message mb-4">
                <span class="cookie-consent-text">
                    We use session cookies to facilitate essential functionalities of a website, such as maintaining
                    a user’s login status during a browsing session, adding things to a basket, and e-billing.
                    For an improved visit experience we use analysis products. These are used when you agree with
                    "Statistics".
                </span>
                <?= Html::a($learnMore, $link, ['class' => 'cookie-consent-link']) ?>
            </div>
            <button class="cookie-consent-accept-all btn btn-primary me-2"><?= $acceptAll ?></button>
            <button class="cookie-consent-controls-toggle btn btn-secondary me-2"><?= $controlsOpen ?></button>
            <?php /**?>
            <button class="cookie-consent-details-toggle btn btn-secondary"><?= $detailsOpen ?></button>
 */?>
        </div>
        <div class="cookie-consent-controls <?php if (!empty($visibleControls)): ?>open<?php endif; ?> mt-3">
            <?php foreach ($consent as $key => $item) : ?>
                <?= Html::checkbox(
                    $key,
                    $item["checked"],
                    [
                        'class'           => 'cookie-consent-checkbox',
                        'data-cc-consent' => $key,
                        'disabled'        => $item["disabled"],
                        'id'              => $key
                    ]
                ) ?>
                <label for="<?= $key ?>" class="cookie-consent-control me-3">
                    <span><?= $item["label"] ?></span>
                </label>
            <?php endforeach ?>
            <button class="cookie-consent-save btn btn-secondary" data-cc-namespace="popup"><?= $save ?></button>
        </div>
        <div class="cookie-consent-details <?php if (!empty($visibleDetails)): ?>open<?php endif; ?>">
            <?php foreach ($consent as $key => $item) : ?>
                <?php if (!empty($item['details'])): ?>
                    <label><?= $item["label"] ?></label>
                    <table>
                        <?php foreach ($item['details'] as $detail) : ?>
                            <?php if (!empty($detail['title']) && !empty($detail['description'])): ?>
                                <tr>
                                    <td><?= $detail['title'] ?></td>
                                    <td><?= $detail['description'] ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach ?>
                    </table>
                <?php endif; ?>
            <?php endforeach ?>
        </div>
    </div>
</div>
