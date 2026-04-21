<?php

namespace common\models\auth;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use common\models\User;

class ResetPasswordForm extends Model
{
    public ?string $password = null;
    public ?string $password_repeat = null;

    private ?User $_user = null;

    /**
     * @param string $token
     * @throws InvalidArgumentException
     */
    public function __construct($token, array $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException('Password reset token cannot be blank.');
        }

        $this->_user = User::findByPasswordResetToken($token);

        if (!$this->_user) {
            throw new InvalidArgumentException('Wrong password reset token.');
        }

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['password', 'password_repeat'], 'required'],
            ['password', 'string', 'min' => 6, 'max' => 255],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => "Passwords don't match."],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'password' => 'New Password',
            'password_repeat' => 'Repeat New Password',
        ];
    }

    public function resetPassword(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        $user->withoutTripium = true;

        return $user->save();
    }
}
