<?php
/**
 * @var string $addressBookId
 * @var string $name
 */

use yii\web\View;

?>
<div class="fixed">
    <div class="white-block margin-block-small">
        <div class="text-center mb-3">
            <h4><b>Join Our Newsletter</b></h4>
            <div class="description">Stay up to date with our latest news and deals</div>
        </div>

        <?php if (Yii::$app->getRequest()->get('result') === 'success') { ?>
            <div class="text-center"><b>Thank you! You are subscribed.</b></div>
        <?php } else { ?>

        <!-- Start of signup -->
        <form name="signup" id="signup" action="https://r1.for-email.com/signup.ashx" method="post" autocomplete="off" onsubmit="return validate_signup(this, true)">
            <input type="hidden" name="ci_isconsentform" value="true">
            <!-- UserID - required field, do not remove -->
            <input type="hidden" name="userid" value="358545">
            <input type="hidden" name="SIG815f247150e83b59695fb30bb8b0d04c505f2eb4377fa49e06d6f5979774f7d2" value="">
            <input type="hidden" name="addressbookid" value="<?= $addressBookId ?>" />
            <!-- ReturnURL - when the user hits submit, they'll get sent here -->
            <input type="hidden" name="ReturnURL" value="">
            <!-- ConsentText -->
            <input type="hidden" name="ci_userConsentText" value="By signing up, you agree to receive emails from <?= $name
            ?> about area promotions and news of things to do in Branson, MO.  You can unsubscribe at any time by clicking the link in the footer of our emails.">
            <!-- ConsentUrl -->
            <input type="hidden" id="ci_consenturl" name="ci_consenturl" value="">
            <div class="row">
                <div class="col-12 col-sm-4 order-1 mb-2">
                    <input type="email" name="email" id="email" aria-required="true" required placeholder="Email" class="w-100" />
                </div>
                <div class="col-12 col-sm-4 order-2 mb-2">
                    <input type="text" class="text w-100" name="cd_FIRSTNAME" id="FIRSTNAME" aria-required="true" required
                           placeholder="First Name" />
                </div>
                <div class="col-12 order-3 order-sm-4 mb-2">
                    <input type="checkbox" name="ci_userConsentCheckBox" id="ci_userConsentCheckBox" class="w-100" />
                    <label for="ci_userConsentCheckBox">
                        <small>
                        <small>
                        By signing up, you agree to receive emails from <?= $name
                            ?> about area promotions and news of things to do in Branson, MO.  You can unsubscribe at any time by clicking the link in the footer of our emails.
                        </small>
                        </small>
                    </label>
                </div>
                <div class="col-12 col-sm-4 order-4 order-sm-3 mb-2">
                    <input type="submit" id="btnsubmit" name="btnsubmit" value="Subscribe" class="btn btn-primary w-100" />
                </div>
            </div>
        </form>

        <?php } ?>
    </div>
</div>
<?php
$this->registerJS("
    var urlInput = document.getElementById('ci_consenturl');
    if (urlInput != null && urlInput != 'undefined') {
        urlInput.value = encodeURI(window.location.href);
    }
    function checkbox_Clicked(element) {
        document.getElementById(element.id + '_unchecked').value = !element.checked;
    }
    function validate_signup(frm, showAlert) {
        var emailAddress = frm.email ? frm.email.value : '';
        var smsNumber = frm.MOBILENUMBERID ? frm.MOBILENUMBERID.value : '';
        var errorString = '';
        if (frm.email && (emailAddress == '' || emailAddress.indexOf('@') == -1)) {
            errorString = 'Please enter your email address';
        }
        var checkBoxValue = frm.ci_userConsentCheckBox.checked;
        if (checkBoxValue == false && showAlert) {
            errorString = 'You must accept the terms';
        }
        if (showAlert) {
            var els = frm.getElementsByTagName('input');
            for (var i = 0; i < els.length; i++) {
                if (els[i].className == 'text' || els[i].className == 'date' || els[i].className == 'number') {
                    if (els[i].value == '') errorString = 'Please complete all required fields.';
                } else if (els[i].className == 'radio') {
                    var toCheck = document.getElementsByName(els[i].name);
                    var radioChecked = false;
                    for (var j = 0; j < toCheck.length; j++) {
                        if (toCheck[j].name == els[i].name && toCheck[j].checked) radioChecked = true;
                    }
                    if (!radioChecked) errorString = 'Please complete all required fields.';
                }
            }
        }
        var isError = false;
        if (errorString.length > 0) {
            isError = true;
            if (showAlert) alert(errorString);
        }
        return !isError;
    }
", View::POS_END);
?>
<!-- End of signup -->


