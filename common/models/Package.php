<?php

namespace common\models;

use common\helpers\General;
use common\helpers\MarketingItemHelper;
use common\models\priceLine\PriceLine;
use common\tripium\Tripium;
use DateTime;
use Exception;
use yii\base\Model;
use yii\helpers\Url;

/**
 * Class Package
 *
 * @property $package_id
 */
class Package extends Model
{
    public const ANY_TIME = 'Any time';

    private $startDataTime;
    private $endDataTime;
    private $ticketsQty = 0;
    private $item;
    private $comments;
    private $tickets;
    private $riceLineContract;
    private $nonRefundable = true;
    private $session;
    private $cancellationTexts;

    public $id;
    public $type_id;
    public $category;
    public $name;
    public $start_date;
    public $start_time;
    public $end_date;
    public $end_time;
    public $package_id;
    public $order;
    public $tax;
    public $total;
    public $full_total;
    public $display_price;
    public $retail_amount;
    public $type_name;
    public $isAnyTime;
    public $cancelled;
    public $modified;
    public $confirmation;
    public $rate_code;
    public $first_name;
    public $last_name;
    public $child_ages;
    public $coupon;
    public $cancellation_policy;
    public $cancellation_tax;
    public $check_in_instructions;
    public $special_check_in_instructions;
    public $itinerary_id;
    public $voucher_link;
    public $ignore_special_rates = false;
    public $sdc_voucher;
    public $serviceFee;
    public $ppnBundle;
    public $policy = [];
    public $taxDescription = [];
    public $cancelPolicies = [];
    /**
     * @var DateTime $dateUpdated
     */
    public $dateUpdated;
    /**
     * @var int $nights
     */
    public $nights;
    /**
     * @var int $rooms
     */
    public $rooms;
    /**
     * @var PriceLine $priceLine
     */
    public $priceLine;
    /**
     * @var string $hotelPhone
     */
    public $hotelPhone;
    /**
     * @var string $tripId
     */
    public $tripId;
    /**
     * @var string $status
     */
    public $status;

    public function loadData($data): void
    {
        if (!empty($data['id'])) {
            $this->id = $data['id'];
        }
        if (!empty($data['session'])) {
            $this->session = $data['session'];
        }
        if (!empty($data['typeId'])) {
            $this->type_id = $data['typeId'];
        }
        if (!empty($data['category'])) {
            if (in_array($data['category'], [TrPosHotels::TYPE, 'hotels'], true)) {
                $this->category = TrPosHotels::TYPE;
            } else {
                $this->category = $data['category'];
            }
        }
        if (!empty($data['name'])) {
            $this->name = $data['name'];
        }
        if (!empty($data['date'])) {
            $this->start_date = $data['date'];
        }
        if (!empty($data['endDate'])) {
            $this->end_date = $data['endDate'];
        }
        if (!empty($data['time'])) {
            $this->start_time = $data['time'];
            $this->end_time = $data['time'];
            $this->isAnyTime = $data['time'] === self::ANY_TIME;
        }
        if (!empty($data['order'])) {
            $this->order = $data['order'];
        } else if (!empty($data['orderNumber'])) {
            $this->order = $data['orderNumber'];
        }
        if (!empty($data['packageId'])) {
            $this->package_id = $data['packageId'];
        }
        if (!empty($data['ppnBundle'])) {
            $this->ppnBundle = $data['ppnBundle'];
        }
        if (!empty($data['ppnBundle'])) {
            $this->ppnBundle = $data['ppnBundle'];
        }
        if (!empty($data['rooms'])) {
            $this->rooms = $data['rooms'];
        }
        if (!empty($data['nights'])) {
            $this->nights = $data['nights'];
        } elseif (isset($data['category']) && $data['category'] === TrPosHotels::TYPE) {
            $this->nights = $this->getStartDataTime()->diff($this->getEndDataTime())->days;
        }
        if (!empty($data['hotelPhone'])) {
            $this->hotelPhone = $data['hotelPhone'];
        }
        if (!empty($data['tripId'])) {
            $this->tripId = $data['tripId'];
        }
        if (!empty($data['status'])) {
            $this->status = $data['status'];
        }
        if (!empty($data['priceDetails'])) {
            $this->priceLine = new PriceLine();
            $this->priceLine->loadData($data['priceDetails']);
            $this->tax = $data['priceDetails']['display_taxes'];
            $this->total = $data['priceDetails']['display_sub_total'];
            $this->full_total = $data['priceDetails']['display_total'];
            $this->display_price = $data['priceDetails']['display_price'];
        } else {
            if (!empty($data['tax'])) {
                $this->tax = $data['tax'];
            }
            if (!empty($data['total'])) {
                $this->total = $data['total'];
            }
            if (!empty($data['fullTotal'])) {
                $this->full_total = $data['fullTotal'];
            }
        }
        if (!empty($data['retailAmount'])) {
            $this->retail_amount = $data['retailAmount'];
        }
        if (!empty($data['typeName'])) {
            $this->type_name = $data['typeName'];
        }
        if (!empty($data['comments'])) {
            $this->comments = $data['comments'];
        }
        if (isset($data['cancelled'])) {
            $this->cancelled = $data['cancelled'];
        }
        if (isset($data['confirmation'])) {
            $this->confirmation = $data['confirmation'];
        }
        if (isset($data['modified'])) {
            $this->modified = $data['modified'];
        }
        if (!empty($data['rateCode'])) {
            $this->rate_code = $data['rateCode'];
        }
        if (!empty($data['firstName'])) {
            $this->first_name = $data['firstName'];
        }
        if (!empty($data['lastName'])) {
            $this->last_name = $data['lastName'];
        }
        if (!empty($data['childAges'])) {
            $this->child_ages = $data['childAges'];
        }
        if (!empty($data['coupon'])) {
            $this->coupon = $data['coupon'];
        }
        if (!empty($data['cancellationPolicy'])) {
            $this->cancellation_policy = $data['cancellationPolicy'];
        }
        if (!empty($data['cancellationTax'])) {
            $this->cancellation_tax = $data['cancellationTax'];
        }
        if (!empty($data['checkInInstructions'])) {
            $this->check_in_instructions = $data['checkInInstructions'];
        }
        if (!empty($data['specialCheckInInstructions'])) {
            $this->special_check_in_instructions = $data['specialCheckInInstructions'];
        }
        if (!empty($data['itineraryId'])) {
            $this->itinerary_id = $data['itineraryId'];
        }
        if (!empty($data['voucherLink'])) {
            $this->voucher_link = $data['voucherLink'];
        }
        if (!empty($data['ignoreSpecialRates'])) {
            $this->ignore_special_rates = $data['ignoreSpecialRates'];
        }
        if (!empty($data['ignore_special_rates'])) {
            $this->ignore_special_rates = $data['ignore_special_rates'];
        }
        if (!empty($data['serviceFee'])) {
            $this->serviceFee = $data['serviceFee'];
        }
        if (!empty($data['policy'])) {
            $this->policy = $data['policy'];
        }
        if (!empty($data['taxDescription'])) {
            $this->taxDescription = $data['taxDescription'];
        }
        if (isset($data['nonRefundable'])) {
            $this->nonRefundable = (bool)$data['nonRefundable'];
        }
        if (!empty($data['cancelPolicies'])) {
            $this->cancelPolicies = $data['cancelPolicies'];
        }
        if (!empty($data['dateUpdated'])) {
            $this->dateUpdated = (new DateTime())->setTimestamp($data['dateUpdated']/1000);
        }
        if (!empty($data['sdc_voucher'])) {
            $this->sdc_voucher = $data['sdc_voucher'];
            $tickets = [];
            foreach ($this->sdc_voucher['tickets'] as $ticketData) {
                $TicketSdc = new TicketSdc();
                $TicketSdc->loadData($ticketData);
                $tickets[] = $TicketSdc;
            }
            $this->sdc_voucher['tickets'] = $tickets;
        }
        if (!empty($data['tickets'])) {
            foreach ($data['tickets'] as $ticket) {
                $this->ticketsQty += $ticket['qty'];
                $Ticket = new Ticket;
                $Ticket->loadData($ticket);
                $this->tickets[] = $Ticket;
            }
        }
        $this->item = null;
    }

    public function getStartDataTime(): DateTime
    {
        if ($this->isAnyTime) {
            try {
                $this->startDataTime = new DateTime($this->start_date);
            } catch (Exception $e) {
            }
        } else {
            try {
                $this->startDataTime = new DateTime($this->start_date . ' ' . $this->start_time);
            } catch (Exception $e) {
            }
        }
        return $this->startDataTime;
    }

    public function getEndDataTime(): DateTime
    {
        if ($this->isAnyTime) {
            try {
                $this->endDataTime = new DateTime($this->end_date);
            } catch (Exception $e) {
            }
        } else {
            try {
                $this->endDataTime = new DateTime($this->end_date . ' ' . $this->end_time);
            } catch (Exception $e) {
            }
        }
        return $this->endDataTime;
    }

    public function getTicketsQty(): int
    {
        return $this->ticketsQty;
    }

    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return TrShows|TrAttractions|TrPosHotels|null
     */
    public function getItem()
    {
        if (empty($this->item) && !empty($this->category) && $this->id) {
            $model = MarketingItemHelper::getItemClassNames()[$this->category];
            $this->item = $model::find()->where(['id_external' => $this->id])->one();
        }
        return $this->item;
    }

    /**
     * @return string|null
     */
    public function getItemUrl(): ?string
    {
        if ($this->getItem()) {
            return $this->getItem()->getUrl();
        }

        return null;
    }

    /**
     * @return Ticket[]
     */
    public function getTickets(): array
    {
        if ($this->category === TrPosHotels::TYPE) {
            usort($this->tickets, static function ($a, $b) {
                if ($a->supplementary === $b->supplementary) {
                    return 0;
                }
                return ($a < $b) ? -1 : 1;
            });
        }

        return $this->tickets;
    }

    /**
     * Total saved in the package
     *
     * @return double
     */
    public function getSaved(): float
    {
        $total = 0;
        foreach ($this->getTickets() as $ticket) {
            $total += $ticket->qty * $ticket->getSaved();
        }
        return $total;
    }

    public function canCancel(): bool
    {
        if ($this->isNonRefundable()) {
            return false;
        }
        $item = $this->item;

        if ($item && !empty($item->external_service) && $item->external_service === $item::EXTERNAL_SERVICE_SDC) {
            return false;
        }

        $limitTime = $this->category === TrShows::TYPE ? $this->getStartDataTime() : $this->getEndDataTime();
        return $limitTime->format('U') > time();
    }

    public function canModify(): bool
    {
        if ($this->getOrder()) {
            return false;
        }

        $item = $this->item;
        if (!empty($item->external_service) && $item->external_service === $item::EXTERNAL_SERVICE_SDC) {
            return false;
        }

        if ($this->category === TrPosHotels::TYPE) {
            return false;
        }

        return $this->getStartDataTime()->format('U') > time();
    }

    /**
     * Return hash of Data
     */
    public function getHashData()
    {
        return md5(
            implode(
                '.',
                [
                    $this->id,
                    $this->category,
                    $this->getStartDataTime()->format('Y-m-d_H:i:s'),
                    $this->getEndDataTime()->format('Y-m-d_H:i:s'),
                ]
            )
        );
    }

    /**
     * Return Tickets Sdc
     *
     * @return TicketSdc[]
     */
    public function getTicketsSdc()
    {
        return $this->sdc_voucher['tickets'];
    }

    /**
     * Get url of barcode
     *
     * @param $barCode
     *
     * @return string|null
     */
    public function getBarcodeUrl($barCode): ?string
    {
        if ($this->getItem() !== null) {
            $object = $this->item;
            if ($object->external_service === $object::EXTERNAL_SERVICE_SDC) {
                return Tripium::getBarcodeUrl($this->category, $this->package_id, $barCode);
            }
        }
        return null;
    }

    /**
     * Return status NonRefundable
     *
     * @return bool
     */
    public function isNonRefundable(): bool
    {
        return $this->nonRefundable;
    }

    /**
     * @return string|null
     */
    public function getRoomId(): ?string
    {
        foreach ($this->getTickets() as $ticket) {
            if ($ticket->getRoomId()) {
                return $ticket->getRoomId();
            }
        }
        return false;
    }

    /**
     * @return string|array
     */
    public function getCancellationPolicyText()
    {
        return $this->getItem()->getCancelPolicyText();
    }

    /**
     * @return array|null
     */
    public function getCancellationTexts(): ?array
    {
        if ($this->cancellationTexts === null) {
            $tripium = new Tripium();
            $cancellations = $tripium->getCancellationTexts($this->session);
            if (!empty($cancellations['byPackage'][$this->package_id])) {
                $this->cancellationTexts = $cancellations['byPackage'][$this->package_id];
            }
        }
        return $this->cancellationTexts;
    }
//
//    /**
//     * @return array|null
//     */
//    public function getPriceLineContract(): ?array
//    {
//        if ($this->riceLineContract) {
//            return $this->riceLineContract;
//        }
//        $tripium = new Tripium();
//        $this->riceLineContract = $tripium->getPriceLineContract($this->package_id);
//        return $this->riceLineContract;
//    }
//
//    /**
//     * @return string|null
//     */
//    public function getPpnBundle(): ?string
//    {
//        $priceLineContract = $this->getPriceLineContract();
//        if ($priceLineContract) {
//            return $priceLineContract['bundle_data']['ppn_book_bundle'];
//        }
//        return null;
//    }
    /**
     * Returns url to modify the package in a basket
     *
     * @return string|null
     */
    public function getModifyUrl(): ?string
    {
        if ($this->category === TrPosHotels::TYPE) {
            foreach ($this->getTickets() as $key => $ticket) {
                if (!$ticket->supplementary) {
                    return Url::to(
                        [
                            'lodging/reservation',
                            'code' => $this->getItem()->code,
                            'HotelReservationForm' =>[
                                'packageId' => $this->package_id
                            ],
                            'roomPriceExternalId' => $ticket->id
                        ]
                    );
                }
            }
        } else {
            $controller = $this->getItem()::TYPE;
            return Url::to(
                [
                    $controller . '/tickets',
                    'code' => $this->getItem()->code,
                    'date' => General::formatDateUrlTicket($this->getStartDataTime()->format('Y-m-d H:i:s')),
                    'allotmentId' => $this->getItem()::TYPE === TrShows::TYPE ? null : $this->type_id,
                    OrderForm::getFormName() => ['package_modify' => $this->package_id],
                    '#' => 'availability',
                ]
            );
        }
        return null;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getFullTotal()
    {
        return $this->full_total;
    }

    public function getTax()
    {
        return $this->tax;
    }

    public function getServiceFee()
    {
        return $this->serviceFee;
    }

    /**
     * @return TrOrders|null
     */
    public function getOrder(): ?TrOrders
    {
        /**
         * @var TrOrders $return
         */
        $return = TrOrders::find()->where(['order_number' => $this->order])->one();
        return $return;
    }
}
