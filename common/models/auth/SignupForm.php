<?php
namespace common\models\auth;

use common\models\Users;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_repeat;
    public $user;
    public $emailFrom;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            /*['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
*/
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            // the regular expression to check email address
            ['email', 'match', 'pattern' => '/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/'],
            ['email', 'unique', 'targetClass' => '\common\models\Users', 'message' => 'This email address has already been taken.', 'filter' => ['status' => [Users::STATUS_ACTIVE, Users::STATUS_INACTIVE]]],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            
            ['password_repeat', 'required'],
        	['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match"],
        	
        ];
    }

    /**
     * Signs user up.
     *
     * @return Users|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
        	$user = Users::findOne(['email' => $this->email, 'status' => Users::STATUS_REGISTER]);
        	if (!$user) {
                $user = new Users();
                $user->username = $this->email;
                $user->email = $this->email;
                $user->status = Users::STATUS_REGISTER;
                $user->generateAuthKey();
        	}
        	
        	$user->setPassword($this->password);
        	
        	$user->withoutTripium = true;
        	
            if ($user->save()) {
            	$this->user = $user;
            	$this->send();
                return $user;
            } else {
            	if ($errors = $user->getErrors()) {
            		foreach ($errors as $key => $mess) {
            			if ($key == "email") {
            				$this->addError($key, $mess[0]);
            			}
            		}
            	}
            }
        }
        
        return null;
    }
	
	/**
	 * Sends an email to the specified email address using the information collected by this model.
	 * @return boolean whether the email was sent
	 */
	public function send()
    {
        $scheme = parse_url(Yii::$app->request->hostInfo, PHP_URL_SCHEME) . "://";
        
        Yii::$app->mailer
            ->compose('confirm/confirm_email', ['params' => [
                "confirm_link" => $scheme . Yii::$app->params['domain'] . "/confirm/?id=" . $this->user->id . "&ak=" . $this->user->auth_key,
                "scheme" => $scheme,
        ]])
        ->setTo($this->user->email)
        ->setFrom($this->emailFrom)
        ->setSubject('Confirm your email address')
        ->send();
		
		return true;
	}
}
