<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace FME\Prodfaqs\Block\Adminhtml\Dropdown;

/**
 * Store switcher block
 */
class Switcher extends \Magento\Backend\Block\Template
{
    /**
     * URL for store switcher hint
     */
    

    /**
     * Name of website variable
     *
     * @var string
     */
    protected $_defaultWebsiteVarName = 'website';

    /**
     * Name of store group variable
     *
     * @var string
     */
    protected $_defaultStoreGroupVarName = 'group';

    /**
     * Name of store variable
     *
     * @var string
     */
    protected $_defaultStoreVarName = 'store';

    /**
     * @var array
     */
    protected $_storeIds;

    /**
     * Url for store switcher hint
     *
     * @var string
     */
    protected $_hintUrl;

    /**
     * @var bool
     */
    protected $_hasDefaultOption = true;

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'FME_Prodfaqs::gridswitcher/switcher.phtml';

    /**
     * Website factory
     *
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * Store Group Factory
     *
     * @var \Magento\Store\Model\GroupFactory
     */
    protected $_storeGroupFactory;
    protected $urlBuilder;
    /**
     * Store Factory
     *
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Store\Model\GroupFactory $storeGroupFactory
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Store\Model\GroupFactory $storeGroupFactory,
        \Magento\Store\Model\StoreFactory $storeFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_websiteFactory = $websiteFactory;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->_storeGroupFactory = $storeGroupFactory;
        $this->_storeFactory = $storeFactory;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }

   
    
   
    public function getselectedpage()
    {
        if ($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]) == $this->getUrl('prodfaqs/faqs/byproducts', ['_secure' => true])) {
            return "By Product";
        } elseif ($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]) == $this->getUrl('prodfaqs/faqs/index', ['_secure' => true])) {
            return "FAQ";
        } elseif ($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]) == $this->getUrl('prodfaqs/topic/index', ['_secure' => true])) {
            return "Topics";
        }
    }
    public function getbyproductsurl()
    {
        return $this->getUrl('prodfaqs/faqs/byproducts', ['_secure' => true]);
    }
    public function getfaqsurl()
    {
        return $this->getUrl('prodfaqs/faqs/index', ['_secure' => true]);
    }
    public function gettopicurl()
    {
        return $this->getUrl('prodfaqs/topic/index', ['_secure' => true]);
    }
    public function getconfigurl()
    {
        return $this->getUrl('admin/system_config/index', ['_secure' => true]);
    }
    public function getfilterurl()
    {
        $url = $this->urlBuilder->getUrl('prodfaqs/faqs/filters');
        if ($this->getselectedpage() == 'FAQ') {
            return $url.'?f=1';
        } elseif ($this->getselectedpage() == 'Topics') {
            return $url.'?t=1';
        } elseif ($this->getselectedpage() == 'By Product') {
            return $url.'?p=1';
        }
    }
}
