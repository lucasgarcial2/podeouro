<?php
/**
 * Purpletree_Marketplace GetState
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Purpletree\Marketplace\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
use \Magento\Customer\Model\Session as CustomerSession;

class GetState extends \Magento\Framework\App\Action\Action
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context
     * @param \Magento\Framework\Json\Helper\Data
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Framework\View\Result\PageFactory
     *
     */
    public $countryFactory;
    public $countrySouce;
    public $jsonHelper;
    public $_customer;
    public $resultFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        CustomerSession $customer,
        \Magento\Directory\Model\Config\Source\Country $countrySouce,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\View\Result\PageFactory $resultFactory
    ) {
        $this->countryFactory = $countryFactory;
        $this->countrySouce = $countrySouce;
        $this->jsonHelper = $jsonHelper;
        $this->_customer = $customer;
        $this->resultFactory = $resultFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'text/plain');
        if (!$this->_customer->isLoggedIn()) {
            $response->setContents(
                $this->jsonHelper->jsonEncode(
                    [
                    'status' => 'notlogged',
                    'message' => 'Not Logged In'
                    ]
                )
            );
            return $response;
        }
        $countryHelper = $this->countrySouce;
        $countryFactory = $this->countryFactory;
        $data = $this->getRequest()->getPostValue();
        $countries = $countryHelper->toOptionArray();
        $states = [];
        $states[0] = ['status' => 'false'];
        foreach ($countries as $countryKey => $country) {
            if ($country['value'] == $data['country_id']) {
                $stateArray = $countryFactory->create()->setId(
                    $country['value']
                )->getLoadedRegionCollection()->toOptionArray();

                if ($stateArray && count($stateArray) > 0) {
                    $states[0] = ['status' => 'true'];
                    $states[] = $stateArray;
                }
            }
        }
        $response->setContents(
            $this->jsonHelper->jsonEncode($states)
        );
        return $response;
    }
}
