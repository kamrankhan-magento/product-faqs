<?php

namespace FME\Prodfaqs\Model;

class Topic extends \Magento\Framework\Model\AbstractModel
{
    protected $storeManager;
        
    protected $_objectManager;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
            
        $this->storeManager = $storeManager;
        $this->_objectManager = $objectManager;
            
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
        
    protected function _construct()
    {
        $this->_init('FME\Prodfaqs\Model\ResourceModel\Topic');
    }
        
        
    public function getAvailableStatuses()
    {
                
                
        $availableOptions = ['1' => 'Enable',
                          '0' => 'Disable'];
                
        return $availableOptions;
    }
        
        /*
         * topic list for admin dropdown, to attach with faqs
         */
        
    public function getTopicsList()
    {
            
        $collection = $this->getCollection()->addFieldToFilter('status', 1);
        $topicList = [];
            
        foreach ($collection as $data) {
            $topicList[$data->getId()] = $data->getTitle();
        }
            
        return $topicList;
    }

    public function gettopics()
    {
        $topics = [];
           
        $collection = $this->getCollection()->addFieldToFilter('status', 1);
        $topicList = [];
        $i=0;
        foreach ($collection as $data) {
            $topics[$i] =['value' => $data->getFaqs_topic_id(), 'label' => __($data->getTitle())];
            $i++;
        }


        return $topics;
    }
        
        
    public function loadFrontPageTopics()
    {
            
        $collection = $this->getCollection()->addFieldToFilter('status', 1)
                                        ->setOrder('topic_order', 'asc')
                                        ->addStoreFilter($this->storeManager->getStore()->getId(), true);
            
        $faqsHelper = $this->_objectManager->get('FME\Prodfaqs\Helper\Data');
        if ($faqsHelper->displaySelectedTopics()) {
            $collection->addFieldToFilter('show_on_main', 1);
        }
            
            
        return $collection;
    }
        
        
    public function getMainPageIdentifer()
    {
            
        $faqsHelper = $this->_objectManager->get('FME\Prodfaqs\Helper\Data');
        return $faqsHelper->getFaqsPageIdentifier();
    }
        
    public function getFaqsSeoIdentifierPostfix()
    {
            
        $faqsHelper = $this->_objectManager->get('FME\Prodfaqs\Helper\Data');
        return $faqsHelper->getFaqsSeoIdentifierPostfix();
    }
        
        
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }
}
