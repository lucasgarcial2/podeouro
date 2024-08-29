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
 * @author      Purpletree Infotech Private Limited
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
namespace Purpletree\Marketplace\Model;

class Upload
{
   /**
    * constructor
    *
    * @param \Magento\MediaStorage\Model\File\UploaderFactory
    * @param \Magento\Store\Model\StoreManagerInterface
    * @param \Magento\Framework\Filesystem
    */
    public $uploaderFactory;
    public $fileSystem;
    public $storeManager;

    public function __construct(
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $fileSystem
    ) {
        $this->uploaderFactory      =       $uploaderFactory;
        $this->fileSystem           =       $fileSystem;
        $this->storeManager         =       $storeManager;
    }

    /**
     * upload file
     *
     * @param $input
     * @param $destinationFolder
     * @param $data
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function uploadFileAndGetName($input, $data, $oldPath)
    {
        try {
                $uploader = $this->uploaderFactory->create(['fileId' => $input]);
                $uploader->setAllowRenameFiles(true);
                $uploader->setAllowedExtensions(['jpg','jpeg','png','gif']);
                $uploader->setFilesDispersion(true);
                $uploader->setAllowCreateFolders(true);
                $destinationFolder=$this->fileSystem
                ->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('/marketplace/');
                $result = $uploader->save($destinationFolder);
                $path='pub/media/marketplace';
            if ($oldPath!='') {
                try {
                    if (file_exists('pub/media/marketplace'.$oldPath)) {
                        unlink('pub/media/marketplace'.$oldPath);
                    }
                } catch (\Exception $e) {
                    throw new \Magento\Framework\Exception\LocalizedException($e->getMessage());
                }
            }
             return $result['file'];
        } catch (\Exception $e) {
            if ($e->getCode() != \Magento\Framework\File\Uploader::TMP_NAME_EMPTY) {
                throw new \Magento\Framework\Exception\LocalizedException($e->getMessage());
            } else {
                if (isset($data[$input]['value'])) {
                    return $data[$input]['value'];
                }
            }
        }
        return '';
    }
}
