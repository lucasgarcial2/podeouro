<?php
/**
 * Copyright © 2017 Codazon. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace Codazon\ShoppingCartPro\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    protected $mediaUrl;
    
    protected $scopeConfig;
    
    protected $themeHelper;
    
    protected $enableCustomCart;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Codazon\ThemeLayoutPro\Helper\Data $themeHelper
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();
        $this->themeHelper = $themeHelper;
    }
    
    public function getConfig($path)
    {
        return $this->themeHelper->getConfig($path, 'store');
    }
    
    public function enableCustomCart()
    {
        if ($this->enableCustomCart === null) {
            $this->enableCustomCart = $this->getConfig('shoppingcart/general/enable');
        }
        return $this->enableCustomCart;
    }
    
    public function getMiniCartStyle()
    {
        return $this->getConfig('shoppingcart/general/style');
    }
    
    public function enableAjaxWishlist()
    {
        return $this->getConfig('ajax_addto/wishlist/enable');
    }
    
    public function enableAjaxCompare()
    {
        return $this->getConfig('ajax_addto/compare/enable');
    }
    
    public function enableAjaxUpdateInCartPage()
    {
        return (bool)$this->getConfig('shoppingcart/general/enable_cart_page_ajax');
    }
    
    public function getUrlEncoder()
    {
        return $this->urlEncoder;
    }
}
