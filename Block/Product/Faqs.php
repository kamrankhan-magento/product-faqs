<?php
namespace FME\Prodfaqs\Block\Product;

class Faqs extends \FME\Prodfaqs\Block\Faqs
{
    const CONFIG_CAPTCHA_ENABLE = 'prodfaqs/google_options/captchastatus';
    const CONFIG_CAPTCHA_PRIVATE_KEY = 'prodfaqs/google_options/googleprivatekey';
    const CONFIG_CAPTCHA_PUBLIC_KEY = 'prodfaqs/google_options/googlepublickey';
    const CONFIG_CAPTCHA_THEME = 'prodfaqs/google_options/theme';
    
    const PFAQS_HEADING = 'prodfaqs/product_questions/title';
    const PFAQS_ASK_ENABLE = 'prodfaqs/product_questions/enable_ask';
    const PFAQS_CUSTOMR_ASK_ALLOWED = 'prodfaqs/product_questions/allow_customers';
    const PFAQS_ASK_POPUPSLIDE   = 'prodfaqs/product_questions/open_form';
    
    const PFAQS_FAQS_SORTBY   = 'prodfaqs/product_questions/sortby';
    

    const ANS_ASK_ENABLE = 'prodfaqs/answers/enable_ask';
    const ANS_CUSTOMR_ASK_ALLOWED = 'prodfaqs/answers/allow_customers';

    const ANS_LIKES_ENABLE = 'prodfaqs/answers/enable_likes';
    const ANS_LIKES_ALLOWED = 'prodfaqs/answers/likes_allow_customers';
    const PFAQS_FAQS_LOADER = 'prodfaqs/ajaxloader/placeholder';
    protected $urlBuilder;
    protected $_answersModel;
    protected $scopeConfig;
    public $_storeManager;
    protected $customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \FME\Prodfaqs\Model\Topic $topicModel,
        \FME\Prodfaqs\Model\Faqs $faqsModel,
        \FME\Prodfaqs\Model\Answers $answersModel,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry
    ) {
                        
                $this->urlBuilder = $context->getUrlBuilder();
                //$this->scopeConfig = $scopeConfig;
                $this->scopeConfig = $context->getScopeConfig();
               // $this->_answersModel = $answersModel;
                $this->customerSession = $customerSession;
                 $this->_storeManager = $context->getStoreManager();
                
                parent::__construct($context, $topicModel, $faqsModel, $answersModel, $objectManager, $registry);
                
                $this->setTabTitle();
    }
        
     
    public function getLoginId()
    {
        return($this->customerSession->getCustomer()->getId());
    }
    public function getLoginEmail()
    {
        return($this->customerSession->getCustomer()->getEmail());
    }
    public function getLoginName()
    {
        return($this->customerSession->getCustomer()->getName());
    }

    /**
     * Get current product id
     *
     * @return null|int
     */
    public function getProductId()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product ? $product->getId() : null;
    }
    public function getProductName()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product ? $product->getName() : null;
    }
    
    public function getProductUrl()
    {
        
        $product = $this->_coreRegistry->registry('product');
        return $product ? $product->getUrlModel()->getUrl($product) : null;
    }

    /**
     * Set tab title
     *
     * @return void
     */
    public function setTabTitle()
    {
        $title = __('Faqs');
        $this->setTitle($title);
    }
    
    public function getFaqPostUrl()
    {
        return $this->urlBuilder->getUrl('prodfaqs/index/post');
    }
    public function getAnswerPostUrl()
    {
        return $this->urlBuilder->getUrl('prodfaqs/index/answerpost');
    }

    public function getProductPageFaqs($product_id)
    {
        
        $collection = $this->_faqsModel->getCollection()
                                        ->joinProductAttachmentTable()
                                        ->addFieldToFilter('product_id', $product_id)
                                        ->addFieldToFilter('main_table.status', 1)
                                        ->joinTopicTable()
                                        ->addFieldToFilter('topic_table.identifier', 'product-faqs');
       
        if ($this->getSortbyOrder() == 'asc') {
            $collection->setOrder('faq_order', 'asc');
        } elseif ($this->getSortbyOrder() == 'desc') {
            $collection->setOrder('faq_order', 'desc');
        } else {
            $collection->setOrder('create_date', 'desc');
        }
        
        
        return $collection;
    }
    public function getProductPageAjaxFaqs($product_id, $type, $arrow)
    {
        
        $collection = $this->_faqsModel->getCollection()
                                        ->joinProductAttachmentTable()
                                        ->addFieldToFilter('product_id', $product_id)
                                        ->addFieldToFilter('main_table.status', 1)
                                        ->joinTopicTable()
                                        ->addFieldToFilter('topic_table.identifier', 'product-faqs');
        
        if ($type == 0 && $arrow == 'asc') {
            $collection->setOrder('rating_stars', 'asc');
        } elseif ($type == 0 && $arrow == 'desc') {
            $collection->setOrder('rating_stars', 'desc');
        } elseif ($type == 1 && $arrow == 'asc') {
            $collection->setOrder('create_date', 'asc');
        } elseif ($type == 1 && $arrow == 'desc') {
            $collection->setOrder('create_date', 'desc');
        }
        
        return $collection;
    }
    public function getfaqanswers($faq_id)
    {
    
        $answers = $this->_answersModel->loadFaqanswers($faq_id);
        return $answers;
    }
    
    public function df_module_dir($moduleName, $type = '')
    {
    /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = \Magento\Framework\App\ObjectManager::getInstance();
    /** @var \Magento\Framework\Module\Dir\Reader $reader */
        $reader = $om->get('Magento\Framework\Module\Dir\Reader');
        return $reader->getModuleDir($type, $moduleName);
    }
    
    
    public function isCaptchaEnable()
    {
        
        return $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_ENABLE);
    }
    
    public function getPrivateKey()
    {
        
        return $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_PRIVATE_KEY);
    }
    
    public function getPublicKey()
    {
        
        return $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_PUBLIC_KEY);
    }
    
    public function getCaptchaTheme()
    {
        
        return $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_THEME);
    }
    
    
    public function getProductFaqsHeading()
    {
        
        $title = $this->scopeConfig->getValue(self::PFAQS_HEADING);
        return $title ? $title : __('Faqs');
    }
    
    public function getSortbyOrder()
    {
        
        $order = $this->scopeConfig->getValue(self::PFAQS_FAQS_SORTBY);
        return $order;
    }

    public function getAjaxLoader()
    {
        $loader = $this->scopeConfig->getValue(self::PFAQS_FAQS_LOADER);
        return $loader;
    }
    
    public function isAskQuestionEnable()
    {
        
        return $this->scopeConfig->getValue(self::PFAQS_ASK_ENABLE);
    }
    
    
    public function isAskQuestionAllowed()
    {
        
        $conf = $this->scopeConfig->getValue(self::PFAQS_CUSTOMR_ASK_ALLOWED);
        $customer = $this->_objectManager->get('Magento\Customer\Model\Session');
        
        
        $allow = false;
        if ($conf == 'all') {
            $allow = true;
        } elseif ($conf == 'guests') {
            if (!$customer->isLoggedIn()) {
                $allow = true;
            } else {
                $allow = false;
            }
        } elseif ($conf == 'registered') {
            if ($customer->isLoggedIn()) {
                $allow = true;
            } else {
                $allow = false;
            }
        } else {
            $allow = false;
        }
                return $allow;
    }
    ////////////////////////  ANSWER Settings

    public function isAskAnswerEnable()
    {
        
        return $this->scopeConfig->getValue(self::ANS_ASK_ENABLE);
    }
    
    
    public function isAskAnswerAllowed()
    {
        
        $conf = $this->scopeConfig->getValue(self::ANS_CUSTOMR_ASK_ALLOWED);
        $customer = $this->_objectManager->get('Magento\Customer\Model\Session');
        
        
        $allow = false;
        if ($conf == 'all') {
            $allow = true;
        } elseif ($conf == 'guests') {
            if (!$customer->isLoggedIn()) {
                $allow = true;
            } else {
                $allow = false;
            }
        } elseif ($conf == 'registered') {
            if ($customer->isLoggedIn()) {
                $allow = true;
            } else {
                $allow = false;
            }
        } else {
            $allow = false;
        }
                return $allow;
    }


    public function isLikesEnable()
    {
        
        return $this->scopeConfig->getValue(self::ANS_LIKES_ENABLE);
    }
    
    
    public function isLikesAllowed()
    {
        
        $conf = $this->scopeConfig->getValue(self::ANS_LIKES_ALLOWED);
        $customer = $this->_objectManager->get('Magento\Customer\Model\Session');
        
        
        $allow = false;
        if ($conf == 'all') {
            $allow = true;
        } elseif ($conf == 'guests') {
            if (!$customer->isLoggedIn()) {
                $allow = true;
            } else {
                $allow = false;
            }
        } elseif ($conf == 'registered') {
            if ($customer->isLoggedIn()) {
                $allow = true;
            } else {
                $allow = false;
            }
        } else {
            $allow = false;
        }
                return $allow;
    }
    
    
    
    public function openFormPopupSlide()
    {
        
        $popupSlide = $this->scopeConfig->getValue(self::PFAQS_ASK_POPUPSLIDE);
        return $popupSlide ? $popupSlide : 'popup';
    }
}
