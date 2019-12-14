<?php
namespace FME\Prodfaqs\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Search extends \Magento\Framework\App\Action\Action
{
    
    protected $resultPageFactory;
    protected $_topicModel;
    protected $_faqsModel;
    protected $_coreRegistry = null;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \FME\Prodfaqs\Model\Topic $topicModel,
        \FME\Prodfaqs\Model\Faqs $faqsModel,
        \Magento\Framework\Registry $registry
    ) {
            
        $this->_faqsModel = $faqsModel;
        $this->_topicModel = $topicModel;
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
        
    public function execute()
    {
        $post = $this->getRequest()->getParam('faqssearch');
                                    
        if ($searchTerm = $post) {
            $result = $this->_faqsModel->setFaqsQueryFilter($searchTerm);
                                
            $this->_coreRegistry->register('faq_search_results', $result);
                
            $resultPage = $this->resultPageFactory->create();

            $faqsHelper = $this->_objectManager->get('FME\Prodfaqs\Helper\Data');

            $resultPage->getConfig()->getTitle()->set($searchTerm ? _('Search results for: '). "'" .$searchTerm."'" : $faqsHelper->getFaqsPageTitle());
            $resultPage->getConfig()->setKeywords($faqsHelper->getFaqsPageMetaKeywords());
            $resultPage->getConfig()->setDescription($faqsHelper->getFaqsPageMetaDesc());
                
            return $resultPage;
        }
    }
}
