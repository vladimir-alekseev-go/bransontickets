<?php
namespace common\controllers;

use common\analytics\Analytics;
use common\helpers\General;
use common\hubspot\HubSpot;
use common\models\CartForm;
use common\models\Custumer;
use common\models\CustumerForm;
use common\models\form\CartCouponForm;
use common\models\LocationServices;
use common\models\Mailchimp;
use common\models\OrderModifyForm;
use common\models\Package;
use common\models\PaymentForm;
use common\models\PaymentFormAddCard;
use common\models\PaymentModifyForm;
use common\models\PaymentModifyFormAddCard;
use common\models\TrAttractions;
use common\models\TrBasket;
use common\models\TrLunchs;
use common\models\TrOrders;
use common\models\TrShows;
use common\models\User;
use common\tripium\Tripium;
use wlfrontend\widgets\scheduleSlider\ScheduleSliderWidget;
use DateTime;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

trait OrderController
{
    public function actionCart()
    {
        $get = Yii::$app->request->get();
    	$backurl = isset($get["backurl"]) ? $get["backurl"] : ['order/cart', 'changed'=>true];
    	
    	$Basket = TrBasket::build();
    	
    	$CartForm = new CartForm;
    	
    	$CartCouponForm = new CartCouponForm(['coupon'=>$Basket->getCoupon() ? $Basket->getCoupon()->code : null]);

    	if (Yii::$app->request->isPost) {
    	    
    	    if ($CartCouponForm->load(Yii::$app->request->post()) && $CartCouponForm->send()) {
    	        return $this->redirect($backurl);
    	    }
    	    
    	    if ($CartForm->load(Yii::$app->request->post()) && $CartForm->validate()) {
    		    
    		    $Basket->accept_terms = $CartForm->agree;
    		    $resultReservation = $Basket->reserve();

                if (!$resultReservation && isset($Basket->tripium)
                    && (int)$Basket->tripium->errorCode === Tripium::ITINERARY_WAS_NOT_FOUND) {
                    TrBasket::removeSessionID($Basket->getAttribute('session_id'));
                    return $this->redirect(['order/cart']);
                }

    		    if ($resultReservation) {
    		        return $this->redirect(['order/payment', 'isAuth' => true]);
	    		}
	    		
	    		if(!empty($Basket->getErrors())) {
                    Yii::$app->session->setFlash('errors', array_values($Basket->getFirstErrors()));
                }
	    		if(!empty($Basket->warnings)) {
                    Yii::$app->session->setFlash('warnings', $Basket->warnings);
                }
	    		if(!empty($Basket->messages)) {
                    Yii::$app->session->setFlash('messages', $Basket->messages);
                }
    		}
    	}
    	
    	if (!empty($get["remove_id"])) {
    		$Basket->removePackage($get["remove_id"]);
    		
    		Yii::$app->session->setFlash('remove', true);
    		
    		if(!empty($Basket->getErrors())) {
                Yii::$app->session->setFlash('errors', array_values($Basket->getFirstErrors()));
            }
    		if(!empty($Basket->warnings)) {
                Yii::$app->session->setFlash('warnings', $Basket->warnings);
            }
    		if(!empty($Basket->messages)) {
                Yii::$app->session->setFlash('messages', $Basket->messages);
            }
    		
    		if (empty($Basket->getErrors())) {
    			return $this->redirect($backurl);
    		}
    	}
    	
    	if (!empty($get["remove_all"])) {
    	    $Basket->removeAll();
    		
    		Yii::$app->session->setFlash('remove', true);
    		
    		if(!empty($Basket->getErrors())) {
                Yii::$app->session->setFlash('errors', array_values($Basket->getFirstErrors()));
            }
    		if(!empty($Basket->warnings)) {
                Yii::$app->session->setFlash('warnings', $Basket->warnings);
            }
    		if(!empty($Basket->messages)) {
                Yii::$app->session->setFlash('messages', $Basket->messages);
            }
    			
    		return $this->redirect($backurl);
    	}
    	
    	if (!empty($get["remove_package_id"])) {
    	    $Basket->removePackage($get["remove_package_id"]);
    		
    		Yii::$app->session->setFlash('remove', true);
    		
    		if(!empty($Basket->getErrors())) {
                Yii::$app->session->setFlash('errors', array_values($Basket->getFirstErrors()));
            }
    		if(!empty($Basket->warnings)) {
                Yii::$app->session->setFlash('warnings', $Basket->warnings);
            }
    		if(!empty($Basket->messages)) {
                Yii::$app->session->setFlash('messages', $Basket->messages);
            }
    			
    		return $this->redirect($backurl);
    	}
    	
    	if (!empty($Basket->getPackages())) {
            $AnalyticsData = [];

            foreach ($Basket->getPackages() as $package) {
                $AnalyticsData[] = ['package' => $package];
            }
    		
    		Analytics::addEvent(Analytics::EVENT_CHECKOUT, $AnalyticsData, ['step' => 1, 'option' => 'checkout']);
    	}
    	
    	return $this->render('cart', compact('Basket', 'CartForm', 'CartCouponForm'));
    }
    
    /**
     * Delete VP in cart
     *
     * @param $uniqueHash
     *
     * @return mixed
     */
    function actionDeleteVatationPackage($uniqueHash)
    {
        $backurl = isset($get["backurl"]) ? $get["backurl"] : ['order/cart', 'changed'=>true];
        
        $Basket = TrBasket::build();
        $Basket->removeVacationPackage($uniqueHash);
        
        Yii::$app->session->setFlash('remove', true);
        
        if (!empty($Basket->getErrors())) {
            Yii::$app->session->setFlash('errors', array_values($Basket->getFirstErrors()));
        }
        
        return $this->redirect($backurl);
    }
    
    function actionCheckout()
    {
        $basket = TrBasket::build();

		if ($basket === null) {
			return $this->redirect(['order/cart']);
		}

        if (!Yii::$app->user->isGuest) {
    	    return $this->redirect(['order/payment']);
		}

    	$custumerForm = new CustumerForm;
    	$Custumer = Custumer::get();
    	$custumerForm->setAttributes($Custumer->getAttributes());

    	if ($custumerForm->load(Yii::$app->request->post()) && $custumerForm->register()) {
            return $this->redirect(['order/payment']);
        }

        $errors = $custumerForm->getErrors();

        if (!empty($errors)) {
    		Yii::$app->session->setFlash('errors', array_values(array_shift($errors)));
    	} else {
            $AnalyticsData = [];
            foreach ($basket->getPackages() as $package) {
                $AnalyticsData[] = ['package' => $package];
            }
    	    Analytics::addEvent(Analytics::EVENT_CHECKOUT, $AnalyticsData, ['step' => 2, 'option' => 'auth']);
    	}

    	return $this->render('auth', compact('basket', 'custumerForm'));
    }
    
    function actionPayment()	
    {
        $Basket = TrBasket::build();

		if ($Basket === null) {
			return $this->redirect(['order/cart']);
		}

        $Basket->scenario = TrBasket::SCENARIO_PAYMENT;
        if (!$Basket->validate()) {
            Yii::$app->session->setFlash('errors', array_values($Basket->getFirstErrors()));
            return $this->redirect(['order/cart']);
        }
        
        if (empty($Basket->packages) && empty($Basket->getVacationPackages())) {
    	    return $this->redirect(['order/cart']);
    	}

    	$customerTripium = $user = User::getCustomerTripium();

    	if ($customerTripium instanceOf Custumer && !$customerTripium->validate()) {
    	    Yii::$app->getUser()->setReturnUrl(["/order/payment"]);
    	    Yii::$app->session->setFlash('warnings', "You need to fill in the required fields");
    	    return $this->redirect(["/order/checkout"]);
    	}

        if ($customerTripium instanceOf User) {
            $customerTripium->scenario = User::SCENARIO_PROFILE;
            if (!$customerTripium->validate()) {
                Yii::$app->getUser()->setReturnUrl(["/order/payment"]);
                Yii::$app->session->setFlash('warnings', "You need to fill in the required fields");
                return $this->redirect(["/profile/edit"]);
            }
        }

        $tripium_id = $customerTripium && $customerTripium->tripium_id ? $customerTripium->tripium_id : null;

    	if (!$tripium_id && Yii::$app->user->isGuest) {
    		Yii::$app->getUser()->setReturnUrl("/order/payment/");
    		return $this->redirect("/order/checkout/");
    	}

        if (!$tripium_id && !Yii::$app->user->isGuest) {
            Yii::$app->getUser()->setReturnUrl("/order/payment/");
            Yii::$app->session->setFlash('message', "You need to fill in the required fields");
            return $this->redirect("/profile/edit/");
        }

        if ($customerTripium && Yii::$app->request->post('subscribe')) {
            if (!empty(Yii::$app->params['hubSpot']['hApiKey'])) {
                try {
                    $hubSpot = new HubSpot(Yii::$app->params['hubSpot']['hApiKey']);
                    $hubSpot->updateContactByEmail(
                        $customerTripium->email,
                        [
                            'firstname' => $customerTripium->first_name,
                            'lastname' => $customerTripium->last_name,
                            'phone' => $customerTripium->phone,
                        ]
                    );
                } catch (Exception $e) {
                }
            }
            if (!empty(Yii::$app->siteSettings->data->mailchimp_key) && !empty(Yii::$app->siteSettings->data->mailchimp_list_id)) {
                $mailchimp = new Mailchimp([
                    'apikey' => Yii::$app->siteSettings->data->mailchimp_key,
                    'listID' => Yii::$app->siteSettings->data->mailchimp_list_id
                ]);
                $mailchimp->email = $customerTripium->email;
                $mailchimp->subscribe([
                    "FNAME" => $customerTripium->first_name,
                    "LNAME" => $customerTripium->last_name,
                ]);
            }
        }

    	$tripium = new Tripium();
    	$cards = $tripium->getCustomerCars($tripium_id);
    	if (!empty($cards)) {
    		$cards = ArrayHelper::map($cards, 'id', 'cardNumber');
    	}
    	$model = new PaymentForm();
    	$modelAddCard = new PaymentFormAddCard();
    	$post = Yii::$app->request->post();
    	
    	if (isset(Yii::$app->params["tripium_info"]) && Yii::$app->params["tripium_info"] === "mobile") {
    		if (Yii::$app->user->isGuest) {
	    		$successMsg = "Your order was successfully completed. An email has been sent to you with links to your voucher(s) or you can view voucher(s) below and download image.";
    		} else {
	    		$successMsg = "Your order was successfully completed. Your order is available in your profile. An email has been sent to you with links to your voucher(s) or you can view voucher(s) below and download image.";
    		}
    	} elseif (Yii::$app->user->isGuest) {
            $successMsg = "Your order was successfully completed. An email has been sent to you with links to your voucher(s).";
        } else {
            $successMsg = "Your order was successfully completed. Your order is available in your profile. An email has been sent to you with links to your voucher(s).";
        }
    	
    	if (!empty($post["PaymentForm"]) && $model->load($post) && $order = $model->pay()) {
    		Yii::$app->session->setFlash('success', $successMsg);
    		return $this->redirect(['order/detail', 'orderNumber' => $order["orderNumber"], 'id' => $tripium_id]);
        }

        if (!empty($post["PaymentFormAddCard"]) && $modelAddCard->load($post) && $order = $modelAddCard->pay()) {
        	Yii::$app->session->setFlash('success', $successMsg);
        	return $this->redirect(['order/detail', 'orderNumber' => $order["orderNumber"], 'id' => $tripium_id]);
        }

        if ($model->getErrors()) {
            Yii::$app->session->setFlash('errors', array_values($model->getFirstErrors()));
        }
        if ($modelAddCard->getErrors()) {
            Yii::$app->session->setFlash('errors', array_values($modelAddCard->getFirstErrors()));
        }

        $AnalyticsData = [];
        foreach ($Basket->getPackages() as $package) {
            $AnalyticsData[] = ['package' => $package];
        }

        if (Yii::$app->getRequest()->get('isAuth') == 'true') {
            Analytics::addEvent(Analytics::EVENT_CHECKOUT, $AnalyticsData, ['step' => 2, 'option' => 'auth']);
        }

    	Analytics::addEvent(Analytics::EVENT_CHECKOUT, $AnalyticsData, ['step' => 3, 'option' => 'payment']);
        
        return $this->render('payment', compact('model', 'modelAddCard', 'cards', 'user', 'Basket'));
    }

    /**
     * @param $orderNumber
     *
     * @return TrOrders|null
     */
    protected function getOrder($orderNumber) 
    {
        if (Yii::$app->user->identity && Yii::$app->user->identity->tripium_id) {
            $tripium_id = Yii::$app->user->identity->tripium_id;
        } else {
            $id = Yii::$app->getRequest()->get('id');
            $tripium_id = $id ? $id : User::getCustomerTripiumID();
        }
        if (empty($tripium_id)) {
            return null;
        }
        /**
         * @var $order TrOrders
         */
        $order = TrOrders::find()->where(["tripium_user_id"=>$tripium_id, "order_number"=>$orderNumber])->one();
        
        if ($order === null) {
            return null;
        }
        
        $order->updateByTripium();
        
        return $order;
    }

    /**
     * @param $orderNumber
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDetail($orderNumber) 
    {
        $Order = $this->getOrder($orderNumber);

    	if (!$Order) {
    		throw new NotFoundHttpException;
    	}

    	if (empty($Order->getPackages()) && empty($Order->getVacationPackages())) {
    		throw new NotFoundHttpException;
    	}

		return $this->render('detail', compact('Order')); 
    }

    /**
     * @param $orderNumber
     * @param $vacationPackageId
     * @param $packageId
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionPrintVoucherVacationPackage($orderNumber, $vacationPackageId, $packageId)
    {
        $this->layout = "print";
        
        $Order = $this->getOrder($orderNumber);
        
        if (!$Order) {
            throw new NotFoundHttpException;
        }
        
        $vacationPackage = $Order->getUniqueVacationPackageById($vacationPackageId);
        $package = $vacationPackage->getPackage($packageId);
        
        if (!$package) {
            throw new NotFoundHttpException;
        }
        
        return $this->render('print-voucher-vacation-package', [
            'order' => $Order,
            'vacationPackage' => $vacationPackage,
            'package' => $package
        ]);
    }

    /**
     * @param $orderNumber
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionPrintAllVoucher($orderNumber)
    {
    	$this->layout = "print";
        
    	$Order = $this->getOrder($orderNumber);
    	
    	if (!$Order) {
    	    throw new NotFoundHttpException;
    	}
        
    	if (empty($Order->getValidPackages())) {
            throw new NotFoundHttpException;
        }
        
        return $this->render('print-voucher-all', ['order' => $Order]);
    }

    /**
     * @param $orderNumber
     * @param $packageId
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionPrintVoucher($orderNumber, $packageId)
    {
    	$this->layout = "print";
        
    	$Order = $this->getOrder($orderNumber);
    	
    	if (!$Order) {
    	    throw new NotFoundHttpException;
    	}
        
    	$package = $Order->getPackageById($packageId);
        
        if (empty($package) || (!empty($package) && $package->cancelled)) {
            throw new NotFoundHttpException;
        }
        
        return $this->render('print-voucher', ['order' => $Order, 'package' => $package]);
    }

    /**
     * @param $orderNumber
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionPrint($orderNumber)
    {
    	$this->layout = "print";
    	
    	$Order = $this->getOrder($orderNumber);
    	
    	if (!$Order) {
    	    throw new NotFoundHttpException;
    	}
    	    
		$locationServices = LocationServices::find()->where(['id_external'=>$Order->getDataByKey('location')])->one();
	    
		return $this->render('print', ['order' => $Order, 'locationServices' => $locationServices]);
    }

    /**
     * @param $orderNumber
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionPrintItinerary($orderNumber)
    {
    	$this->layout = "print";
    	
    	$Order = $this->getOrder($orderNumber);
    	
    	if (!$Order) {
    	    throw new NotFoundHttpException;
    	}
    	
		$locationServices = LocationServices::find()->where(['id_external'=>$Order->getDataByKey('location')])->one();
		
		$shows = TrShows::getAvailable()
		    ->with('preview')
		    ->andWhere(["not",["photos"=>""]])
	    	->andWhere(["marketing_level" => 1])
	    	->limit(3)->orderby("rand()")->all(); 

    	return $this->render('print-itinerary', compact('Order', 'locationServices', 'shows'));
    } 

    public function actionCancellation($orderNumber, $packageNumber = null, $vacationPackageId = null)
	{
	    if (Yii::$app->user->isGuest) {
			return '<script>document.location.reload()</script>';
		}
		
		$tripium_id = User::getCustomerTripiumID();
    	
    	$order = TrOrders::findOne(["tripium_user_id"=>$tripium_id, "order_number"=>$orderNumber]);
    	
    	if (!$order) {
	    	throw new NotFoundHttpException;
    	}
    	
    	$Tripium = new Tripium();
	    $cards = $Tripium->orderCards($orderNumber);
			    
		return $this->renderAjax('cancellation', compact('order', 'packageNumber', 'cards', 'vacationPackageId'));
	}
	
	public function actionCancel($orderNumber)
	{
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
		$tripium_id = User::getCustomerTripiumID();
    	
    	$order = TrOrders::find()->where(["tripium_user_id"=>$tripium_id, "order_number"=>$orderNumber])->one();
    	
    	if (!$order) {
	    	throw new NotFoundHttpException("The order hasn't found");
    	}
    	
	    if ($order->cancel()) {
	        $order->updateByTripium(true);
	        return ["status" => "ok"];
	    } else {
	        throw new UnprocessableEntityHttpException($order->errors[0][0]);
	    }
	}

    /**
     * @param $orderNumber
     * @param $packageNumber
     *
     * @return string[]
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
	public function actionCancelPackage($orderNumber, $packageNumber)
	{
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $tripium_id = User::getCustomerTripiumID();
    	
    	$order = TrOrders::find()->where(["tripium_user_id"=>$tripium_id, "order_number"=>$orderNumber])->one();
    	
    	if (!$order) {
	    	throw new NotFoundHttpException;
    	}
    	
    	if ($order->cancelPackage($packageNumber)) {
    	    $order->updateByTripium(true);
    	    return ["status" => "ok"];
    	}

        throw new UnprocessableEntityHttpException($order->errors[0][0]);
    }

    /**
     * @param $orderNumber
     * @param $vacationPackageId
     *
     * @return string[]
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
	public function actionCancelVacationPackage($orderNumber, $vacationPackageId)
	{
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $tripium_id = User::getCustomerTripiumID();
	    
	    $order = TrOrders::find()->where(["tripium_user_id"=>$tripium_id, "order_number"=>$orderNumber])->one();
	    
	    if (!$order) {
	        throw new NotFoundHttpException;
	    }
	        
	    if ($order->cancelUniqueVacationPackage($vacationPackageId)) {
	        $order->updateByTripium(true);
	        return ["status" => "ok"];
	    }

        throw new UnprocessableEntityHttpException($order->errors[0][0]);
    }

    /**
     * @param $orderNumber
     * @param $packageNumber
     *
     * @return string
     * @throws NotFoundHttpException
     */
	public function actionModification($orderNumber, $packageNumber)
	{
		if (Yii::$app->user->isGuest) {
			return '<script>document.location.reload()</script>';
		}
		
		$tripium_id = User::getCustomerTripiumID();
    	
    	$Order = TrOrders::find()->where(["tripium_user_id"=>$tripium_id, "order_number"=>$orderNumber])->one();
    	
    	if (!$Order) {
	    	throw new NotFoundHttpException;
    	}
		
    	$package = $Order->getPackage($packageNumber);

    	if (!$package) {
	    	throw new NotFoundHttpException;
		}

        $ScheduleSlider = new ScheduleSliderWidget(
            [
                'model' => $package->item,
                'date' => $package->startDataTime,
                'package' => $package,
                'scheduleIsShow' => false
            ]
        );
	    
	    $OrderForm = new OrderModifyForm();
	    $OrderForm->coupon_code = $Order->getCoupon() ? $Order->getCoupon()->code : null;

		return $this->renderAjax('modification', compact('ScheduleSlider', 'package', 'Order', 'OrderForm'));
	}

    /**
     * @param $orderNumber
     * @param $packageNumber
     * @param $date
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @throws Exception
     */
	public function actionModificationForm($orderNumber, $packageNumber, $date)
	{
	    $date = new DateTime($date);
	    
		$this->layout = "empty";
		
		$tripium_id = User::getCustomerTripiumID();
    	
		$Order = TrOrders::find()->where(['order_number'=>$orderNumber, 'tripium_user_id'=>$tripium_id])->one();
		
		if (!$Order) {
		    throw new NotFoundHttpException;
		}
		
		$package = $Order->getPackage($packageNumber);
		
		if (!$package) {
		    throw new NotFoundHttpException;
		}
		
	    $OrderForm = new OrderModifyForm();
	    //$OrderForm->load(Yii::$app->request->get());
	    $OrderForm->load(Yii::$app->request->post());
	    $OrderForm->setAttributes(['date'=>$date, 'model'=>$package->item]);
    	$OrderForm->setPackageOrder($package);
    	$OrderForm->initData();
    	$OrderForm->initPrice();
    	$OrderForm->initPackage();
    	$OrderForm->initPriceByCoupon();
    	
    	$OrderForm->updatePricesByPackages([$package]);
    	
		return $this->render('order-form', compact('OrderForm'));
	}

    /**
     * @param      $orderNumber
     * @param      $packageNumber
     * @param bool $process
     *

     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
	public function actionModificationProceed($orderNumber, $packageNumber, $process = false)
	{
	    $post = Yii::$app->request->post();
		
		$user = User::getCurrentUser();
		
		$tripium_id = User::getCustomerTripiumID();
    	
		$Order = TrOrders::find()->where(['order_number'=>$orderNumber, 'tripium_user_id'=>$tripium_id])->one();
		
		if (!$Order) {
		    throw new NotFoundHttpException;
		}
		
		$packageOld = $Order->getPackage($packageNumber);
		
		if (!$packageOld) {
		    throw new NotFoundHttpException;
		}
		
	    $date = $packageOld->startDataTime;
		
	    $OrderForm = new OrderModifyForm();
	    $OrderForm->setAttributes(['date'=>$date, 'model'=>$packageOld->item]);
	    $OrderForm->setPackageOrder($packageOld);
    	$OrderForm->load($post);
    	$OrderForm->initData();
    	$OrderForm->initPrice();
    	$OrderForm->initPackage();
    	$OrderForm->load($post);
    	$OrderForm->correctFamilyPack();
	    $result = $OrderForm->check();

	    if ((int)$process === 0) {
	        $OrderForm->initPriceByCoupon();
	        $result['orderForm'] = $this->renderPartial('order-form', compact('OrderForm'));
	    }
	    $result['prices'] = $OrderForm->prices;
	    $result['totalPrice'] = $OrderForm->totalPrice;
	    
	    $PackageNew = new Package;
	    $PackageNew->loadData($result);
	    
	    if ($PackageNew->category === TrShows::TYPE || $PackageNew->category === TrAttractions::TYPE) {
		    $result['datePackepgeFormat'] = $PackageNew->getStartDataTime()->format("l, M d, h:i A");
        } else if ($PackageNew->category === TrLunchs::TYPE){
            $result['datePackepgeFormat'] = 'Avail dates ' .
                $PackageNew->getStartDataTime()->format("l, M d, h:i A") . ' - ' . $PackageNew->getEndDataTime()
                    ->format("l, M d, h:i A");
		}
	    
	    if ($OrderForm->getErrors('check')) {
    		$result['globalErrors'] = $OrderForm->getErrors('check');
    	}
	    
	    if ((int)$process === 1) {
	    	$this->layout = "empty";
	    	$PaymentModifyForm = new PaymentModifyForm(['coupon_code'=>$OrderForm->coupon_code]);
	    	$PaymentModifyFormAddCard = new PaymentModifyFormAddCard(['coupon_code'=>$OrderForm->coupon_code]);
    		
			$PaymentModifyForm->setModifyRequest($OrderForm->createRequest());
			$PaymentModifyFormAddCard->setModifyRequest($OrderForm->createRequest());
			
			$Tripium = new Tripium;
			$result['order_modify_info'] = $this->render('order-modify-info', ['result'=>$result, 'model'=>$packageOld->item, 'package'=>$PackageNew, 'packageOld'=>$packageOld, 'OrderForm'=>$OrderForm]);
			
	    	//if ($result['fullTotal'] - $package['fullTotal'] > 0) {
	    	if ($result['modifyAmount'] > 0) {
	    		$cards = $Tripium->getCustomerCars($tripium_id);
		    	if ($cards) {
		    		$cards = ArrayHelper::map($cards, 'id', 'cardNumber');
		    	}
	    		$result['html'] = $this->render('order-form-pay', compact('result', 'PaymentModifyForm', 'PaymentModifyFormAddCard', 'cards', 'user'));
	    	} else if ($result['modifyAmount'] < 0){
	    	    $cards = $Tripium->orderCards($orderNumber);
	    		$result['html'] = $this->render('order-form-refund', compact('result', 'cards'));
	    	}
	    	
	    } else if ((int)$process === 2) {
	        
	    	if ($OrderForm->run()) {
		    	$Order->updateByTripium(true);
		    	Yii::$app->session->setFlash('success', "Item has changed");
	    	} else {
		    	$err = $OrderForm->getFirstErrors();
	        	if ($err) {
	        		$result['error'] = is_array($err) ? array_shift($err) : $err;
	        	}
	    	}
	    }
	    
	    return Json::encode($result);
	}

    /**
     * @param $orderNumber
     * @param $packageNumber
     *
     * @return string
     * @throws NotFoundHttpException
     */
	public function actionModificationPayment($orderNumber, $packageNumber)
	{
		$post = Yii::$app->request->post();
		
		$tripium_id = User::getCustomerTripiumID();
    	
		$Order = TrOrders::find()->where(['order_number'=>$orderNumber, 'tripium_user_id'=>$tripium_id])->one();
		
		if (!$Order) {
		    throw new NotFoundHttpException;
		}
		
		$package = $Order->getPackage($packageNumber);
		
		if (!$package) {
		    throw new NotFoundHttpException;
		}
		
	    $PaymentModifyForm = new PaymentModifyForm();
		$PaymentModifyFormAddCard = new PaymentModifyFormAddCard();
		
		$result = [];
		
		if (isset($post["PaymentForm"]) && $PaymentModifyForm->load($post) && $res = $PaymentModifyForm->pay()) {
		    
		    $Order->updateByTripium(true);
    		Yii::$app->session->setFlash('success', "Item has changed");
        
		} else if(isset($post["PaymentFormAddCard"]) && $PaymentModifyFormAddCard->load($post) && $res = $PaymentModifyFormAddCard->pay()) {
		    
		    $Order->updateByTripium(true);
        	Yii::$app->session->setFlash('success', "Item has changed");
        	
        } else {
            
        	$err = $PaymentModifyForm->getFirstErrors();
        	if ($err) {
        		$result['error'] = is_array($err) ? array_shift($err) : $err;
        	}
        	
        	$err = $PaymentModifyFormAddCard->getFirstErrors();
        	if ($err) {
        		$result['error'] = is_array($err) ? array_shift($err) : $err;
        	}
        }
        
		return Json::encode($result);
	}

    /**
     * @param      $orderNumber
     * @param null $packageId
     *
     * @throws NotFoundHttpException
     */
	public function actionVoucher($orderNumber, $packageId=null)
	{
	    $Order = $this->getOrder($orderNumber);

	    if (!$Order) {
	        throw new NotFoundHttpException;
	    }

	    if ($packageId && !$Order->getPackage($packageId) && !$Order->getPackageFromVPByPackageId($packageId)) {
            throw new NotFoundHttpException;
        }

	    $Tripium = new Tripium();
	    $link = $Tripium->getVoucherLink($orderNumber, $packageId);
	    if ($link) {
	        General::sendFile($link, $orderNumber.($packageId ? '-'.$packageId : '').'.pdf', ['inline'=>true]);
	    }
	}

    /*
     * @param null $itemType
     * @param null $itemId
     *
     * @return array
     * @deprecated
     */
	/*protected function getItemsCancellationPolicy($itemType = null, $itemId = null)
    {
        $basket = Yii::$app->controller->basket;
        
        $items = [];
        
        if (empty($itemType) && empty($itemId)) {
            foreach ($basket->getUniqueVacationPackages() as $VacationPackage) {
                $items[$VacationPackage::className()][] = $VacationPackage;
            }
            foreach ($basket->getPackages() as $package) {
                if ($package->item::TYPE === TrPosPlHotels::TYPE) {
                    $package->item->cancel_policy_text = $this->renderPartial('cart/package-policy', compact('package'));
                }
                $items[$package->category][] = $package->item;
            }
        } else {
            foreach ($basket->getPackages() as $package) {
                if (!empty($itemType) && !empty($itemId) && $package->category == $itemType && $package->id == $itemId) {
                    $items[$package->category][] = $package->item;
                }
            }
        }
        
        return $items;
	}*/

    public function actionPriceLineTermsConditions()
    {
        $Tripium = new Tripium();
        $result = $Tripium->getPLTermsConditions();

        return $this->render('cart/price-line-terms-conditions', ['result' => $result['terms_and_conditions'] ?? []]);
    }

    public function actionPriceLinePrivacyPolicy()
    {
        $Tripium = new Tripium();
        $result = $Tripium->getPLPrivacyPolicy();

        return $this->render('cart/price-line-terms-conditions', ['result' => $result['privacy_policy'] ?? []]);
    }
}
