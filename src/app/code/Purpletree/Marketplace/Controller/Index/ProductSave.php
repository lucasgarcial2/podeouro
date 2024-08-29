<?php
/**
 * Purpletree_Marketplace ProductSave
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
 
namespace Purpletree\Marketplace\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\Uploader as FileUploader;

use Magento\Framework\App\ObjectManager;
use \Magento\Customer\Model\Session as CustomerSession;

class ProductSave extends Action
{
    /**
     * constructor
     *
     * @param \Magento\MediaStorage\Model\File\UploaderFactory
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute
     * @param \Magento\ConfigurableProduct\Helper\Product\Options\Factory
     * @param \Magento\Catalog\Model\Product\Media\Config
     * @param \Magento\Framework\Filesystem
     * @param \Magento\MediaStorage\Helper\File\Storage\Database
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Magento\Downloadable\Api\Data\LinkInterfaceFactory
     * @param \Magento\Downloadable\Api\Data\SampleInterfaceFactory
     * @param Context
     */

     public $product;
     public $optionfactory;
     public $customer;
     public $storeManager;
     public $mediaConfig;
     public $mediaDirectory;
     public $fileStorageDb;
     public $_eavAttribute;
     public $storeDetails;
     public $dataHelper;
     public $resultForwardFactory;
     public $link_repository;
     public $link_interface;
     public $sample_repository;
     public $sample_interface;
     public $LinkFactory;
     public $SampleFactory;
     public $_resultPage;

    public function __construct(
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        CustomerSession $customer,
        \Magento\Catalog\Model\Product $product,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\ConfigurableProduct\Helper\Product\Options\Factory $optionfactory,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Downloadable\Api\LinkRepositoryInterface $link_repository,
        \Magento\Downloadable\Api\Data\LinkInterface $link_interface,
        \Magento\Downloadable\Api\SampleRepositoryInterface $sample_repository,
        \Magento\Downloadable\Api\Data\SampleInterface $sample_interface,
        \Magento\Downloadable\Api\Data\LinkInterfaceFactory $LinkFactory,
        \Magento\Downloadable\Api\Data\SampleInterfaceFactory $SampleFactory,
        Context $context
    ) {
        $this->product        = $product;
        $this->optionfactory        = $optionfactory;
        $this->customer             = $customer;
        $this->storeManager         = $storeManager;
        $this->mediaConfig          = $mediaConfig;
        $this->mediaDirectory       = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->fileStorageDb        = $fileStorageDb;
        $this->_eavAttribute        = $eavAttribute;
        $this->storeDetails         = $storeDetails;
        $this->dataHelper           = $dataHelper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->link_repository      = $link_repository;
        $this->link_interface       = $link_interface;
        $this->sample_repository    = $sample_repository;
        $this->sample_interface     = $sample_interface;
        $this->LinkFactory          = $LinkFactory;
        $this->SampleFactory        = $SampleFactory;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $customerId=$this->customer->getCustomer()->getId();
        $seller=$this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
        if ($seller=='' || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $websiteOption=0;
        $data = $this->getRequest()->getPostValue();
        $simpleids = [];
        if ($data) {
            try {
                if (isset($data['variations-matrix'])) {
                    foreach ($data['variations-matrix'] as $matrixdata) {
                        $simpleids[] = $this->saveproduct($data, 'simple', $matrixdata, $blankarray = []);
                    }
                        $product_id = $this->saveproduct($data, 'configurable', $blankarray = [], $simpleids);
                } else {
                    $product_id = $this->saveproduct($data, $data['product_type']);
                }
                $this->messageManager->addSuccess(__('The Product has been saved.'));
                return $this->_redirect('marketplace/index/products');
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Product.--------->'.$e->getMessage()));
                return $this->_redirect('marketplace/index/productcreate');
            }
        }
                return $this->_redirect('marketplace/index/products');
    }
    public function saveproduct($data, $type, $matrixdata = [], $simpleid = [])
    {
        
          $productRepository  = $this->_objectManager->create('\Magento\Catalog\Model\Product');
        if (!empty($matrixdata)) {
            $visibility = 1;
        } else {
            $visibility = $data['visibilty'];
        }
        if (empty($matrixdata)) {
            $matrixdata = $data;
        }
            $productAttribute=$data['product_attribute'];
            $productType=$data['product_type'];
            // strip out all whitespace
            $sku = preg_replace('/\s*/', '', $matrixdata['sku']);
            // convert the string to all lowercase
             $sku = strtolower($sku);
            $productRepository->setSku($sku); // Set your sku here
            $productRepository->setName($matrixdata['name']); // Name of Product
        if ($this->dataHelper->getGeneralConfig('general/product_approval_required') == 0 && isset($data['status'])) {
            $productRepository->setStatus(isset($data['status'])?$data['status']:2); // Status on product enabled/ disabled 1/0
        } else {
            $productRepository->setStatus(2); // Status on product enabled/ disabled 1/0
        }
		if(!empty($data['addionalattribut'])) {
			foreach($data['addionalattribut'] as $atribkey => $addionattribvalue) {
				$productRepository->setData($atribkey,$addionattribvalue);
			}
		}
			if(!empty($data['addionalattributMulti'])) {
				foreach($data['addionalattributMulti'] as $atttt => $dsdsd) {
					$val1133 = implode(',',$dsdsd);
					$productRepository->setData($atttt,$val1133);
				}
		}
        if ($productType=='simple' || $productType=='configurable') {
            $productRepository->setWeight(isset($matrixdata['weight'])?$matrixdata['weight']:''); // weight of product
            $productRepository->setProductHasWeight($data['product_has_weight']); // weight of product
        }
          
            $productRepository->setVisibility($visibility); // visibilty of product (catalog / search / catalog, search / Not visible individually)
            $productRepository->setTaxClassId($data['tax']); // Tax class id
            $productRepository->setTypeId($type); // type of product (simple/virtual/downloadable/configurable)
            $productRepository->setPrice($matrixdata['price']); // price of product

            $productRepository->setWebsiteIds((!empty($data['productwebsite']))?$data['productwebsite']:[0]);
             $fromDate = '';
            $toDate = '';

        if (isset($data['datepickerfrom']) && $data['datepickerfrom'] != '') {
            $fromDate = date('m/d/Y h:i:s', strtotime($data['datepickerfrom']));
        }
        if (isset($data['datepickerto']) && $data['datepickerto'] != '') {
            $toDate = date('m/d/Y h:i:s', strtotime($data['datepickerto']));
        }
            $productRepository->setNewsFromDate($fromDate);
            $productRepository->setNewsToDate($toDate);
            $productRepository->setAttributeSetId($productAttribute);
            $productRepository->setCategoryIds(isset($data['category']) ? $data['category']:'');
      /*   if (($matrixdata['quantity'] == '') || $matrixdata['quantity'] == 0) {
            $is_in_stock = 0;
        } else {
            $is_in_stock = 1;
        } */
		if(isset($data['stockstatus'])) {
		$is_in_stock = $data['stockstatus'];
		} else {
		$is_in_stock = 0;
		}
            $productRepository->setStockData(
                [
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 1,
                    'is_in_stock' => $is_in_stock,
                    'qty' => (($matrixdata['quantity'] != '')) ? $matrixdata['quantity']:0
                ]
            );
                    //Advance pricing
        if ($type=='simple' || $type=='virtual' || $type=='downloadable') {
            $specialpricefrom =  '';
            $specialpriceto = '';
            
            if (isset($data['specialpricefrom']) && $data['specialpricefrom'] != '') {
                $specialpricefrom = date('m/d/Y h:i:s', strtotime($data['specialpricefrom']));
            }
            if (isset($data['specialpriceto']) && $data['specialpriceto'] != '') {
                $specialpriceto = date('m/d/Y h:i:s', strtotime($data['specialpriceto']));
            }
            $productRepository->setSpecialPrice(isset($data['special_price'])?$data['special_price']:'');
            $productRepository->setSpecialFromDate($specialpricefrom);
            $productRepository->setSpecialToDate($specialpriceto);
            $productRepository->setCost(isset($data['productcost'])?$data['productcost']:'');
            $productRepository->setMsrp(isset($data['productmsrp'])?$data['productmsrp']:'');
            $productRepository->setMsrpDisplayActualPriceType(isset($data['msrp_display_actual_price_type'])?$data['msrp_display_actual_price_type']:'');
        }
        //Advance pricing
            $productRepository->setCountryOfManufacture($data['manufacture']);
            $productRepository->setMetaTitle($data['metatitle']);
            $productRepository->setMetaKeyword($data['meta_keyword']);
            $productRepository->setMetaDescription($data['metadescription']);
            $productRepository->setDescription($data['productdescription']);
            $productRepository->setShortDescription($data['productshortdescription']);
            /*Add Images To The Product*/

        if (!empty($matrixdata['images'])) {
            foreach ($matrixdata['images'] as &$image) {
                 $newFile = $this->moveImageFromTmp($image['file']);
                 $image['new_file'] = $newFile;
                 $newImages[$image['file']] = $image;
                 $image['file'] = $newFile;
                 $productRepository->addImageToMediaGallery(
                     $this->mediaDirectory->getAbsolutePath() . 'catalog/product' . $image['file'],
                     null,
                     false,
                     false
                 );
            }
        }
        if (isset($matrixdata['image'])) {
            $productRepository->setImage($this->getFilenameFromTmp($data['image']));
        }
        if (isset($matrixdata['small_image'])) {
            $productRepository->setSmallImage($this->getFilenameFromTmp($data['small_image']));
        }
        if (isset($matrixdata['thumbnail'])) {
            $productRepository->setThumbnail($this->getFilenameFromTmp($data['thumbnail']));
        }
        if (isset($matrixdata['swatch_image'])) {
            $productRepository->setSwatchImage($this->getFilenameFromTmp($data['swatch_image']));
        }
            $productRepository->setData('seller_id', $this->sellerid());
            $productRepository->setData('is_seller_product', 1);
            $productRepository->setStoreId(0);
            $productRepository->save();
        if ($productType=='downloadable' && isset($data['downloadable'])) {
            $downloadable = $data['downloadable'];
            $productRepository->setDownloadableData($downloadable);
            $extension = $productRepository->getExtensionAttributes();
            if (isset($downloadable['link']) && is_array($downloadable['link'])) {
                $links = [];
                foreach ($downloadable['link'] as $linkData) {
                    $i=0;
                    if (!$linkData || (isset($linkData['is_delete']) && $linkData['is_delete'])) {
                        continue;
                    } else {
                        $links[] = $this->getLinkBuilder()->setData(
                            $linkData
                        )->build(
                            $this->LinkFactory()->create()
                        );
                    }
                }
                $extension->setDownloadableProductLinks($links);
            }
            if (isset($downloadable['sample']) && is_array($downloadable['sample'])) {
                $samples = [];
                foreach ($downloadable['sample'] as $sampleData) {
                    $i=0;
                    if (!$sampleData || (isset($sampleData['is_delete']) && (bool)$sampleData['is_delete'])) {
                        continue;
                    } else {
                        $samples[] = $this->getSampleBuilder()->setData(
                            $sampleData
                        )->build(
                            $this->SampleFactory()->create()
                        );
                    }
                }
                $extension->setDownloadableProductSamples($samples);
            }
            $productRepository->setExtensionAttributes($extension);
            if ($productRepository->getLinksPurchasedSeparately()) {
                $productRepository->setTypeHasRequiredOptions(true)->setRequiredOptions(true);
            } else {
                $productRepository->setTypeHasRequiredOptions(false)->setRequiredOptions(false);
            }
            $productRepository->setLinksTitle($data['linkttl']);
            $productRepository->setSamplesTitle($data['samplelinkttl']);
            if (isset($data['linkchk'])) {
                $productRepository->setLinksPurchasedSeparately(1);
            } else {
                $productRepository->setLinksPurchasedSeparately(0);
            }
                
            $productRepository->save();
        }
            
            
            $productId = $productRepository->getId();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if ($type != 'configurable') {
            //$product = $this->product->load($productId);
			$product  = $this->_objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
            if (!empty($matrixdata['configurable_attribute'])) {
                foreach ($matrixdata['configurable_attribute'] as $attributecode => $attributevalue) {
                    $product->setData($attributecode, $attributevalue, 0)->getResource()->saveAttribute($product, $attributecode);
                }
                            $product->save();
            }
        } elseif ($type == 'configurable') {
            if (!empty($simpleid)) {
                $configurableAttributesData = [];
                $position = 0;
                if (!empty($data['configurable_attribute1'])) {
                    foreach ($data['configurable_attribute1'] as $attributecode => $attribs) {
                        $attributeId = $this->_eavAttribute->getIdByCode('catalog_product', $attributecode);
                        $attributeValues = [];
                        foreach ($attribs as $optionvalue => $attributesvalues) {
                            foreach ($attributesvalues as $attributelabel1 => $optionlabel1) {
                                $attributelabel = $attributelabel1;
                                $optionlabel = $optionlabel1;
                            }
                            
                             $attributeValues[] = [
                                                    'label' => $optionlabel,
                                                    'attribute_id' => $attributeId,
                                                    'value_index' => $optionvalue,
                                                ];
                        }
                            
                        $configurableAttributesData[] = [
                                'attribute_id' => $attributeId,
                                'code' => $attributecode,
                                'label' => $attributelabel,
                                'position' => $position,
                                'values' => $attributeValues,
                            ];
                        $position++;
                    }
                }
        
                $associatedProductIds = $simpleid;
                $configurableOptions = $this->optionfactory->create($configurableAttributesData);
               // $product = $this->product->load($productId);
				$product  = $this->_objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
                $extensionConfigurableAttributes = $product->getExtensionAttributes();
                $extensionConfigurableAttributes->setConfigurableProductOptions($configurableOptions);
                $extensionConfigurableAttributes->setConfigurableProductLinks($associatedProductIds);
                $product->setExtensionAttributes($extensionConfigurableAttributes);
                $product->save();
                return true;
            }
        }
            
            return $productId;
    }
        /**
         * Move image from temporary directory to normal
         *
         * @param string $file
         * @return string
         */
    protected function moveImageFromTmp($file)
    {
        $file = $this->getFilenameFromTmp($file);
        $destinationFile = $this->getUniqueFileName($file);

        if ($this->fileStorageDb->checkDbUsage()) {
            $this->fileStorageDb->renameFile(
                $this->mediaConfig->getTmpMediaShortUrl($file),
                $this->mediaConfig->getMediaShortUrl($destinationFile)
            );

            $this->mediaDirectory->delete($this->mediaConfig->getTmpMediaPath($file));
            $this->mediaDirectory->delete($this->mediaConfig->getMediaPath($destinationFile));
        } else {
            $this->mediaDirectory->renameFile(
                $this->mediaConfig->getTmpMediaPath($file),
                $this->mediaConfig->getMediaPath($destinationFile)
            );
        }

        return str_replace('\\', '/', $destinationFile);
    }
        /**
         * @param string $file
         * @return string
         */
    protected function getFilenameFromTmp($file)
    {
        return strrpos($file, '.tmp') == strlen($file) - 4 ? substr($file, 0, strlen($file) - 4) : $file;
    }
        /**
         * Check whether file to move exists. Getting unique name
         *
         * @param string $file
         * @param bool $forTmp
         * @return string
         */
    protected function getUniqueFileName($file, $forTmp = false)
    {
        if ($this->fileStorageDb->checkDbUsage()) {
            $destFile = $this->fileStorageDb->getUniqueFilename(
                $this->mediaConfig->getBaseMediaUrlAddition(),
                $file
            );
        } else {
            $destinationFile = $forTmp
                ? $this->mediaDirectory->getAbsolutePath($this->mediaConfig->getTmpMediaPath($file))
                : $this->mediaDirectory->getAbsolutePath($this->mediaConfig->getMediaPath($file));
            $destFile = dirname($file) . '/' . FileUploader::getNewFileName($destinationFile);
        }

        return $destFile;
    }
    public function sellerid()
    {
            return $this->customer->getId();
    }
        /* Get LinkBuilder instance
     *
     * @deprecated
     * @return \Magento\Downloadable\Model\Link\Builder
     */
    private function getLinkBuilder()
    {
       // if (!$this->linkBuilder) {
            $this->linkBuilder = ObjectManager::getInstance()->get(\Magento\Downloadable\Model\Link\Builder::class);
       // }

        return $this->linkBuilder;
    }

    /**
     * Get SampleBuilder instance
     *
     * @deprecated
     * @return \Magento\Downloadable\Model\Sample\Builder
     */
    private function getSampleBuilder()
    {
        //if (!$this->sampleBuilder) {
            $this->sampleBuilder = ObjectManager::getInstance()->get(
                \Magento\Downloadable\Model\Sample\Builder::class
            );
        //}

        return $this->sampleBuilder;
    }
}
