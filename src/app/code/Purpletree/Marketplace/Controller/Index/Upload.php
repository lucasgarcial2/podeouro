<?php
/**
 * Purpletree_Marketplace Upload
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
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Customer\Model\Session as CustomerSession;

class Upload extends \Magento\Framework\App\Action\Action
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory
     *
     */
    public $adapterFactory;
    public $filesystem;
    public $mediaConfig;
    public $_customer;
    public $jsonHelper;
    public $resultFactory;
    public $storeManager;
    public $storeDetails;
    public $resultForwardFactory;
    public $dataHelper;
    public $_resultPage;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CustomerSession $customer,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\View\Result\PageFactory $resultFactory
    ) {
    
        $this->adapterFactory                =       $adapterFactory;
        $this->filesystem                =       $filesystem;
        $this->mediaConfig                =       $mediaConfig;
        $this->_customer                =       $customer;
        $this->jsonHelper               =       $jsonHelper;
        $this->resultFactory            =       $resultFactory;
        $this->storeManager             =       $storeManager;
        $this->storeDetails                 =       $storeDetails;
        $this->resultForwardFactory     =       $resultForwardFactory;
        $this->dataHelper               =       $dataHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $customerId=$this->_customer->getCustomer()->getId();
        $sellerId=$this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->_customer->isLoggedIn()) {
                $this->_customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
                $this->_customer->authenticate();
        }
        if ($sellerId=='' || !$moduleEnable) {
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
        try {
            $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'image']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
            $imageAdapter = $this->adapterFactory->create();
            $uploader->addValidateCallback('catalog_product_image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA);
            $config = $this->mediaConfig;
            $result = $uploader->save($mediaDirectory->getAbsolutePath($config->getBaseTmpMediaPath()));
            $this->_eventManager->dispatch(
                'catalog_product_gallery_upload_image_after',
                ['result' => $result, 'action' => $this]
            );
            unset($result['tmp_name']);
            unset($result['path']);
            $result['url'] = $this->mediaConfig->getTmpMediaUrl($result['file']);
            $result['file'] = $result['file'] . '.tmp';
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        $response->setContents($this->jsonHelper->jsonEncode($result));
        return $response;
    }
}
