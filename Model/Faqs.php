<?php

namespace FME\Prodfaqs\Model;

class Faqs extends \Magento\Framework\Model\AbstractModel
{

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
        $this->_init('FME\Prodfaqs\Model\ResourceModel\Faqs');
    }
        
        
    public function getAvailableStatuses()
    {
                
                
        $availableOptions = ['1' => 'Enable',
                          '0' => 'Disable'];
                
        return $availableOptions;
    }
        
        
    public function loadFaqsOfTopic($topic_id, $show_on_main = false)
    {
            
        $faqsCollection = $this->getCollection()->addFieldToFilter('faqs_topic_id', $topic_id)
                                                ->addFieldToFilter('status', 1)
                                                ->setOrder('faq_order', 'asc');
                
        if ($show_on_main) {
            $faqsCollection->addFieldToFilter('show_on_main', 1);
        }
                
        return $faqsCollection;
    }

    public function loadFaqsOfCustomer($customer_id)
    {
            
        $faqsCollection = $this->getCollection()->addFieldToFilter('user_id', $customer_id)
                                                
                                              ->setOrder('faq_order', 'asc');
     
            
        return $faqsCollection->getData();
    }
        
    public function loadFaq($faq_id)
    {
            
        $faqsCollection = $this->getCollection()->addFieldToFilter('faq_id', $faq_id);
                                                
                                                
     
        
        return $faqsCollection->getData();
    }
        
        
    public function setFaqsQueryFilter($query)
    {
           
        $conn = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')
                                    ->getConnection('core_read');
            
            
        $faqsTable = $conn->getTableName('fme_faq');
        $faqsTopicTable = $conn->getTableName('fme_faqs_topic');
        $faqsStoreTable = $conn->getTableName('fme_faqs_topic_store');
            
        $select = $conn->select()->from(['f' => $faqsTable])
                                    ->where('f.title LIKE "%'.$query.'%" OR f.faq_answer LIKE "%'.$query.'%" OR f.tags LIKE "%'.$query.'%"')
                                    ->where('f.status = ?', 1)
                                    ->where('fs.store_id = '. $this->storeManager->getStore()->getId() .' OR fs.store_id = 0')
                                    ->order('f.faq_order '. \Magento\Framework\DB\Select::SQL_ASC);

        $select->joinLeft(
            ['fs' => $faqsStoreTable],
            'f.faqs_topic_id = fs.faqs_topic_id',
            []
        );



        $result = $conn->fetchAll($select);

        return $result;
    }
        
    public function checkIdentifier($identifier)
    {
        return $this->_getResource()->checkIdentifier($identifier);
    }
        
        
    public function getPopularTags()
    {
            
        $faqsCollection = $this->getCollection()->addFieldToFilter('status', 1)
                                                ->setOrder('faq_order', 'asc');
            
        $tags = [];
            
        foreach ($faqsCollection as $data) {
            $tags[] = $data->getTags();
        }
            
        if(is_array($tags)){    
            $tags = implode(',', $tags);
            $tags = explode(',', $tags);
            $tags = array_unique($tags);
        }
        
            
            
        $max_num = $this->_objectManager->get('FME\Prodfaqs\Helper\Data')->getTagsMaxNum();
            
        if ($max_num != '' && $max_num != 0) {
            $tags = array_slice($tags, 0, $max_num);
        }
            
        return $tags;
    }
        
        
    public function getProducts(\FME\Prodfaqs\Model\Faqs $object)
    {

        return $this->getResource()->getProducts($object);
    }

    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return [];
        }

        //print_r($this->getData());
       // exit;

        $array = $this->getData('products_position');
        if ($array === null) {
            $temp = $this->getData('product_id');

            for ($i=0; $i<sizeof($this->getData('product_id')); $i++) {
                $array[$temp[$i]] = 0;
            }
            
            //$array = $this->getResource()->getProductsPosition($this);
            $this->setData('products_position', $array);
        }
        
       // $this->_logger->log(100,print_r($array,true));
        
        return $array;//$this->getData();
    }
}
