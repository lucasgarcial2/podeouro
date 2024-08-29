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
 
namespace Purpletree\Marketplace\Controller\Adminhtml\Categorycommission;

class Delete extends \Purpletree\Marketplace\Controller\Adminhtml\Categorycommission
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Marketplace::commission');
    }
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
         $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
          $categorycommission = $this->_initCategorycommission();
        if (!empty($categorycommission)) {
            try {
                $categorycommission->delete();
                $this->messageManager->addSuccess(__('You deleted the Commission.'));
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
            }
        } else {
            $this->messageManager->addError(__('Cannot Delete Commission.'));
        }
        return $resultRedirect->setPath('purpletree_marketplace/categorycommission/index');
    }
}
