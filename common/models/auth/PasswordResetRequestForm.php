<?php
namespace common\models\auth;

use Yii;
use yii\base\Model;

use common\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with such email.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     * @param email $emailFrom
     * @param string $siteName
     * @return boolean whether the email was send
     */
    public function sendEmail($emailFrom = null, $siteName = null)
    {
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
        if ($user) {
            $user->withoutTripium = true;
            
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            if ($user->save()) {
                return Yii::$app->mailer->compose(['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'], ['user' => $user])
                    ->setFrom([$emailFrom => $siteName." Ticket Service"])
                    ->setTo($this->email)
                    ->setBcc(Yii::$app->params['passwordResetRequestEmailCopy'])
                    ->setSubject('Password reset for ' . $siteName)
                    ->send();
            }
        }

        return false;
    }
}
