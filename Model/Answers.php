<?php
/**
 * Copyright © 2016 AionNext Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Model;

class Answers extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Aion Test cache tag
     */
    const CACHE_TAG = 'prodfaqs_answer';

    /**
     * @var string
     */
    protected $_cacheTag = 'prodfaqs_answer';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'prodfaqs_answer';

    /**
     * @return void
     */

    protected $_resource;
        
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
        $this->_resource = $resource;
            
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('FME\Prodfaqs\Model\ResourceModel\Answers');
    }


    public function loadAnswersCount($faq_id)
    {
            
        $faqsCollection = $this->getCollection()->addFieldToFilter('faq_id', $faq_id);
                                                
                                             // ->addFieldToFilter('status', 1);
     

              
            
        return $faqsCollection->getData();
    }

    public function loadNewAnswers($faq_id)
    {
            
        $faqsCollection = $this->getCollection()->addFieldToFilter('faq_id', $faq_id)
                                                
                                              ->addFieldToFilter('is_email', 0)
                                              ->addFieldToFilter('answer_by', ['neq' => 'admin']);
                                                

           
            
        return $faqsCollection->getData();
    }

    public function loadAnswers($customer_id)
    {
           
        $ansCollection = $this->getCollection()->addFieldToFilter('user_id', $customer_id);
                                                
                                                
       // echo (string)$faqsCollection->getSelect();exit;
           // print_r($ansCollection->getData());exit;
        return $ansCollection->getData();
    }
        
    public function loadFaqanswers($faq_id)
    {
           
        $ansCollection = $this->getCollection()->addFieldToFilter('faq_id', $faq_id)
                                                ->addFieldToFilter('status', 1);
                                                
       // echo (string)$faqsCollection->getSelect();exit;
           // print_r($ansCollection->getData());exit;
        return $ansCollection->getData();
    }
    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Prepare item's statuses
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
}
