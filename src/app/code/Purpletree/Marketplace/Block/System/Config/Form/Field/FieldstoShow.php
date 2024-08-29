<?php
/**
 * Purpletree_Marketplace FieldstoShow
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
namespace Purpletree\Marketplace\Block\System\Config\Form\Field;
 
class FieldstoShow extends \Magento\Config\Block\System\Config\Form\Field
{
    const CONFIG_PATH = 'purpletree_marketplace/general/fieldstoshow';
 
    protected $_template = 'Purpletree_Marketplace::system/config/fieldstoshow.phtml';
 
    protected $_values = null;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param array $data
     */
    public $fieldstoShow;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Purpletree\Marketplace\Model\Config\Source\FieldstoShow $fieldstoShow,
        array $data = []
    ) {
        $this->fieldstoShow = $fieldstoShow;
        parent::__construct($context, $data);
    }
    /**
     * Retrieve element HTML markup.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setNamePrefix($element->getName())
            ->setHtmlId($element->getHtmlId());
 
        return $this->_toHtml();
    }
     
    public function getValues()
    {
        $values = [];
         $optons  = $this->fieldstoShow->toOptionArray();
        if (!empty($optons)) {
            foreach ($optons as $value) {
                $values[$value['value']] = $value['label'];
            }
        }
 
        return $values;
    }
    /**
     *
     * @param  $name
     * @return boolean
     */
    public function getIsChecked($name)
    {
        return in_array($name, $this->getCheckedValues());
    }
    /**
     *
     * get the checked value from config
     */
    public function getCheckedValues()
    {
        if (is_null($this->_values)) {
            $data = $this->getConfigData();
            if (isset($data[self::CONFIG_PATH])) {
                $data = $data[self::CONFIG_PATH];
            } else {
                $data = '';
            }
            $this->_values = explode(',', $data);
        }
 
        return $this->_values;
    }
}
