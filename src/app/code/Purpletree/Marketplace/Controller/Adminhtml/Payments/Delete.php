<?php
/**
 * Purpletree_Marketplace Delete
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Infotech Private Limited
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
 
namespace Purpletree\Marketplace\Controller\Adminhtml\Payments;

class Delete extends \Purpletree\Marketplace\Controller\Adminhtml\Payments
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Marketplace::payments');
    }
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
         $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
         $this->messageManager->addError(__('Cannot delete Payment'));
        $id = $this->getRequest()->getParam('entity_id');
        return  $resultRedirect->setPath(
            'purpletree_marketplace/payments/edit',
            [
                    'entity_id' => $id,
                    '_current' => true
                ]
        );
    }
}
