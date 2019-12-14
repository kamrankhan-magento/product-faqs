<?php
namespace FME\Prodfaqs\Block;

use Magento\Framework\View\Element\Template;

class Customer extends Template
{
   

    protected $_objectManager;
    protected $_faqsModel;
    protected $_answersModel;
    protected $_coreRegistry = null;
    protected $customerSession;
    protected $urlBuilder;
    protected $_productRepository;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \FME\Prodfaqs\Model\Faqs $faqsModel,
        \FME\Prodfaqs\Model\Answers $answersModel,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Registry $registry
    ) {
                        
        $this->customerSession = $customerSession;
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->_productRepository = $productRepository;
        $this->_faqsModel = $faqsModel;
        $this->_answersModel = $answersModel;
        $this->urlBuilder = $context->getUrlBuilder();
                
        parent::__construct($context);
    }
        
        
    public function getCustomerQuestions($customer_id)
    {
                
        $faqsCollection = $this->_faqsModel->loadFaqsOfCustomer($customer_id);
                
        return $faqsCollection;
    }

    public function getCustomerAnswers($customer_id)
    {
                
        $answers = $this->_answersModel->loadAnswers($customer_id);
            
        return $answers;
    }
   
    public function getLoginId()
    {
        return($this->customerSession->getCustomer()->getId());
    }

    public function getAnswersCount($faq_id)
    {
        $answersCollection = $this->_answersModel->loadAnswersCount($faq_id);
             
        return $answersCollection;
    }
    
    public function getFaq($faq_id)
    {

        $faq = $this->_faqsModel->loadFaq($faq_id);
             
        return $faq;
    }

    public function getproname($productid)
    {
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         $product = $objectManager->get('Magento\Catalog\Model\Product')->load($productid);
         return $product['name'];
    }
    public function getprourl($productid)
    {
        $product = $this->_productRepository->getById($productid);
        return $product->getUrlModel()->getUrl($product);
    }
    public function redirectIfNotLoggedIn()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->customerSession->setAfterAuthUrl($this->urlBuilder->getCurrentUrl());
            $this->customerSession->authenticate();
        }
    }
}
