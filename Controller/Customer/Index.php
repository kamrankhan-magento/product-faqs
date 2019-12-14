<?php
namespace FME\Prodfaqs\Controller\Customer;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    
    protected $resultPageFactory;


    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
        
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
            
        $faqsHelper = $this->_objectManager->get('FME\Prodfaqs\Helper\Data');
            
        $resultPage->getConfig()->getTitle()->set('Product Questions');
        $resultPage->getConfig()->setKeywords($faqsHelper->getFaqsPageMetaKeywords());
        $resultPage->getConfig()->setDescription($faqsHelper->getFaqsPageMetaDesc());
            
        return $resultPage;
    }
}
