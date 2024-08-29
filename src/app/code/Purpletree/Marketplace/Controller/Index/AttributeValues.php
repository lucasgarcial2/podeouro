<?php
/**
 * Purpletree_Marketplace AttributeValues
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

class AttributeValues extends \Magento\Framework\App\Action\Action
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context
     * @param \Magento\Customer\Model\Session
     * @param \Purpletree\Marketplace\Model\AttributesList
     * @param \Magento\Framework\Json\Helper\Data
     * @param \Magento\Framework\View\Result\PageFactory
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     *
     */
    public $_customer;
    public $attributeRepository;
    public $jsonHelper;
    public $resultFactory;
    public $storeDetails;
    public $dataHelper;
    public $resultForwardFactory;
    public $_resultPage;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CustomerSession $customer,
        \Purpletree\Marketplace\Model\AttributesList $attributeRepository,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultFactory
    ) {
    
        $this->_customer = $customer;
        $this->attributeRepository = $attributeRepository;
        $this->jsonHelper = $jsonHelper;
        $this->resultFactory = $resultFactory;
        $this->storeDetails             =       $storeDetails;
        $this->dataHelper           =       $dataHelper;
        $this->resultForwardFactory =       $resultForwardFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $customerId=$this->_customer->getCustomer()->getId();
        $seller=$this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->_customer->isLoggedIn()) {
                $this->_customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
                $this->_customer->authenticate();
        }
        if ($seller=='' || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultFactory->create(ResultFactory::TYPE_RAW);
        $response->setHeader('Content-type', 'text/plain');
        if (!$this->_customer->isLoggedIn()) {
            $response->setContents($this->jsonHelper->jsonEncode(['status' => 'notlogged']));
            return $response;
        }
        $optionsdta = [];
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (strpos($data['attributeid'], ',') !== false) {
                $attribs = explode(',', $data['attributeid']);
                foreach ($attribs as $key => $attr) {
                    $optionsdta[] = $this->optionsdata($attr);
                }
            } else {
                $optionsdta[] = $this->optionsdata($data['attributeid']);
            }
        }
        $response->setContents($this->jsonHelper->jsonEncode($optionsdta));
        return $response;
    }
    
    /**
     * Get All Options
     *
     * @return All Options
     */
    public function optionsdata($attr)
    {
        $options = [];
        $attributeRepository = $this->attributeRepository->getAttributes();
        foreach ($attributeRepository->getItems() as $attribute) {
            if (! empty($attribute->getSource()->getAllOptions(false))) {
                if ($attr==$attribute->getId()) {
                    $options[][$attribute->getFrontendLabel()][$attribute->getAttributeCode()] = $attribute->getSource()->getAllOptions(false);
                }
            }
        }
        return $options;
    }
}
