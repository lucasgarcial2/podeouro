<?php
/**
 * Purpletree_Marketplace Calculatecommission
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
namespace Purpletree\Marketplace\Controller\Adminhtml\Sellerorder;

use Magento\Backend\App\Action\Context;

class Calculatecommission extends \Magento\Backend\App\Action
{
    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    protected $_sellerorder;
    protected $dataHelper;

    public function __construct(
        Context $context,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder $sellerorder,
        \Purpletree\Marketplace\Helper\Data $dataHelper
    ) {
        parent::__construct($context);
           $this->_sellerorder           = $sellerorder;
           $this->dataHelper             = $dataHelper;
    }

    public function execute()
    {
        $id                 = $this->getRequest()->getParam('id');
        $seller_id          = $this->getRequest()->getParam('seller_id');
        $order_id           = $this->getRequest()->getParam('order_id');
        $entity_ids         = $this->_sellerorder->getEntityIdfromOrderId($seller_id, $order_id);
       $this->dataHelper->caclulateCommission($entity_ids,$order_id);
        $this->messageManager->addSuccess(__('Seller Order Commission Updated Successfully.'));
        return $this->_redirect('purpletree_marketplace/sellerorder/view/', ['entity_id' => $id]);
    }
}
