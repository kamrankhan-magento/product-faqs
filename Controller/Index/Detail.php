<?php
namespace FME\Prodfaqs\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Detail extends \Magento\Framework\App\Action\Action
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
        $params = $this->getRequest()->getParams();
            
        if ($topic_id = $params['topic']) {
            $topicData = $this->_topicModel->load($topic_id);
            $this->_coreRegistry->register('current_topic', $topicData);
                
            $resultPage = $this->resultPageFactory->create();

            $faqsHelper = $this->_objectManager->get('FME\Prodfaqs\Helper\Data');

            $resultPage->getConfig()->getTitle()->set($topicData->getTitle() ? $topicData->getTitle() : $faqsHelper->getFaqsPageTitle());
            $resultPage->getConfig()->setKeywords($faqsHelper->getFaqsPageMetaKeywords());
            $resultPage->getConfig()->setDescription($faqsHelper->getFaqsPageMetaDesc());
                
            return $resultPage;
        }
    }
}
