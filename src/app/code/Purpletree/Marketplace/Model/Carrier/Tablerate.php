<?php
/**
* Purpletree_Marketplace Tablerate
* NOTICE OF LICENSE
*
* This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
* It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
*
* @category    Purpletree
* @package     Purpletree_Marketplace
* @author      Purpletree Software
* @copyright   Copyright (c) 2020
* @license     https://www.purpletreesoftware.com/license.html
*/
namespace Purpletree\Marketplace\Model\Carrier;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Table rate shipping model
 *
 * @api
 * @since 100.0.2
 */
class Tablerate extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'purpletreetablerate';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var string
     */
    protected $_defaultConditionName = 'package_weight';

    /**
     * @var array
     */
    protected $_conditionNames = [];

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_resultMethodFactory;

    /**
     * @var \Purpletree\Marketplace\Model\ResourceModel\Carrier\TablerateFactory
     */
    protected $_tablerateFactory;
    /**
     * @var \Magento\Checkout\Model\Session $checkoutSession,
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $resultMethodFactory
     * @param \Purpletree\Marketplace\Model\ResourceModel\Carrier\TablerateFactory $tablerateFactory
     * @param array $data
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
		\Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $resultMethodFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Carrier\TablerateFactory $tablerateFactory,
        array $data = []
    ) {
		$this->_checkoutSession = $checkoutSession;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_resultMethodFactory = $resultMethodFactory;
        $this->_tablerateFactory = $tablerateFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        foreach ($this->getCode('condition_name') as $k => $v) {
            $this->_conditionNames[] = $k;
        }
    }

    /**
     * Collect rates.
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function collectRates(RateRequest $request)
    {
		//echo $request->getPackageValue(); die;
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        // exclude Virtual products price from Package value if pre-configured
        if (!$this->getConfigFlag('include_virtual_price') && $request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getProduct()->isVirtual()) {
                            $request->setPackageValue($request->getPackageValue() - $child->getBaseRowTotal());
                        }
                    }
                } elseif ($item->getProduct()->isVirtual()) {
                    $request->setPackageValue($request->getPackageValue() - $item->getBaseRowTotal());
                }
            }
        }

        // Free shipping by qty
        $freeQty = 0;
        $freeQty1 = 0;
        $freePackageValue = 0;
        $freePackageValue1 = 0;
		$sellerproducts = array();
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				 $product_object = $objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface');
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }
       $item_s = $product_object->getById($item->getProductId());
	   $sellerid = 0;
	   if($item_s->getSellerId()) {
		   $sellerid = $item_s->getSellerId();
	   }
				
                if ($item->getHasChildren() && $item->isShipSeparately()) {
					$freeQty1 = 0;
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeShipping = is_numeric($child->getFreeShipping()) ? $child->getFreeShipping() : 0;
                            $freeQty += $item->getQty() * ($child->getQty() - $freeShipping);
                            $freeQty1 += $item->getQty() * ($child->getQty() - $freeShipping);
							
                        }
                    }
                } elseif ($item->getFreeShipping() || $item->getAddress()->getFreeShipping()) {
                    $freeShipping = $item->getFreeShipping() ?
                        $item->getFreeShipping() : $item->getAddress()->getFreeShipping();
                    $freeShipping = is_numeric($freeShipping) ? $freeShipping : 0;
                    $freeQty += $item->getQty() - $freeShipping;
                    $freeQty1 = $item->getQty() - $freeShipping;
                    $freePackageValue += $item->getBaseRowTotal();
                    $freePackageValue1 = $item->getBaseRowTotal();
                }
				$priceee = $item->getBaseRowTotal() - $freePackageValue1;
				$qtyyy = $item->getQty() - $freeQty1;
				$sellerproducts[$sellerid][] = $item->getProductId().'@'.$qtyyy.'@'.$item->getWeight().'@'.$priceee;
            }
		
            $oldValue = $request->getPackageValue();
            $newPackageValue = $oldValue - $freePackageValue;
            $request->setPackageValue($newPackageValue);
            $request->setPackageValueWithDiscount($newPackageValue);
        }
        //if (!$request->getConditionName()) {
            $conditionName = $this->getConfigData('condition_name');
            $request->setConditionName($conditionName ? $conditionName : $this->_defaultConditionName);
       // }

        // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
		$rate1 = array();
		$rate1seller = array();
		$pricein = 0;
		$costin = 0;

		if(!empty($sellerproducts)) {
			foreach($sellerproducts as $sellerid => $sellerproduct1) {
				$weightt = 0;
				$qtyy = 0;
				$prc = 0;
				foreach($sellerproduct1 as $sellerproduct) {
				 $prod = explode('@',$sellerproduct);
				 $weightt += $prod[2];
				 $qtyy += $prod[1];
				 $prc += $prod[3];
				}
				
				$request->setPackageWeight($weightt);
				$request->setPackageQty($qtyy);
				$request->setPackageValue($prc);
				$request->setPackageValueWithDiscount($prc);
				$rate12 = $this->getRate($request,$sellerid);
				$rate1seller[] = $rate12;
				if (!empty($rate12) && isset($rate12['price'])) {
				$rate1[$sellerid] = $rate12['price'];
					 $pricein +=  $rate12['price'];
					 $costin +=  $rate12['cost'];
				}
			}
		}
		$this->_checkoutSession->setSellerShipping($rate1);
		//$sellershipping = $this->_checkoutSession->getSellerShipping(); // output: Hello World
        //$rate = $this->getRate($request,$sellerproducts);
		$countt1 = count($rate1);
		$countt2 = count($sellerproducts);
        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        if (!empty($rate1) && ($countt2 == $countt1)  && $pricein >= 0) {
            if ($request->getPackageQty() == $freeQty) {
                $shippingPrice = 0;
            } else {
                $shippingPrice = $this->getFinalPriceWithHandlingFee($pricein);
            }
            $method = $this->createShippingMethod($shippingPrice, $costin);
            $result->append($method);
        } elseif ($request->getPackageQty() == $freeQty) {

            /**
             * Promotion rule was applied for the whole cart.
             *  In this case all other shipping methods could be omitted
             * Table rate shipping method with 0$ price must be shown if grand total is more than minimal value.
             * Free package weight has been already taken into account.
             */
            $request->setPackageValue($freePackageValue);
            $request->setPackageQty($freeQty);
            $rate = $this->getRate($request);
            if (!empty($rate) && $rate['price'] >= 0) {
                $method = $this->createShippingMethod(0, 0);
                $result->append($method);
            }
        } else {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Error $error */
            $error = $this->_rateErrorFactory->create(
                [
                    'data' => [
                        'carrier' => $this->_code,
                        'carrier_title' => $this->getConfigData('title'),
                        'error_message' => $this->getConfigData('specificerrmsg'),
                    ],
                ]
            );
            $result->append($error);
        }

        return $result;
    }

    /**
     * Get rate.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return array|bool
     */
    public function getRate(\Magento\Quote\Model\Quote\Address\RateRequest $request,$sellerid = 0)
    {
        return $this->_tablerateFactory->create()->getRate($request,$sellerid);
    }

    /**
     * Get code.
     *
     * @param string $type
     * @param string $code
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCode($type, $code = '')
    { 
        $codes = [
            'condition_name' => [
                'package_weight' => __('Weight vs. Destination'),
                'package_value_with_discount' => __('Price vs. Destination'),
                'package_qty' => __('# of Items vs. Destination'),
            ],
            'condition_name_short' => [
                'package_weight' => __('Weight (and above)'),
                'package_value_with_discount' => __('Order Subtotal (and above)'),
                'package_qty' => __('# of Items (and above)'),
            ],
        ];

        if (!isset($codes[$type])) {
            throw new LocalizedException(
                __('The "%1" code type for Table Rate is incorrect. Verify the type and try again.', $type)
            );
        }

        if ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            throw new LocalizedException(
                __('The "%1: %2" code type for Table Rate is incorrect. Verify the type and try again.', $type, $code)
            );
        }

        return $codes[$type][$code];
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['bestway' => $this->getConfigData('name')];
    }

    /**
     * Get the method object based on the shipping price and cost
     *
     * @param float $shippingPrice
     * @param float $cost
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method 
     */
    private function createShippingMethod($shippingPrice, $cost)
    {
        /** @var  \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_resultMethodFactory->create();

        $method->setCarrier($this->getCarrierCode());
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod('bestway');
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice($shippingPrice);
        $method->setCost($cost);
        return $method;
    }
}
