<?php
/**
 * Copyright © 2016 AionNext Ltd. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Model;

class Products extends \Magento\Framework\Model\AbstractModel
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
    protected $_cacheTag = 'prodfaqs_products';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'prodfaqs_products';

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
        $this->_init('FME\Prodfaqs\Model\ResourceModel\Products');
    }


    public function CountProductFaq($product_id)
    {
            
         $conn = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')
                                    ->getConnection('core_read');
            
            
        $productsTable = $conn->getTableName('fme_faqs_product');
                                                
           $select = $conn->select()->from(['f' => $productsTable])
                                    ->where('f.product_id ='.$product_id);
                                    
                                    
            //    echo $select;exit;

        $result = $conn->fetchAll($select);

           
            
        return $result;
    }

    public function CountVisibleProductFaq($product_id)
    {
            
         $conn = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')
                                    ->getConnection('core_read');
            
            
        $productsTable = $conn->getTableName('fme_faqs_product');
           $faqsTable = $conn->getTableName('fme_faq');
           $select = $conn->select()->from(['f' => $productsTable])
                                    ->where('f.product_id ='.$product_id);
                                    
                                    $select->join(
                                        ['fs' => $faqsTable],
                                        'f.faq_id = fs.faq_id AND fs.status=1',
                                        []
                                    );
            //    echo $select;exit;

        $result = $conn->fetchAll($select);
       // echo $select;
      // print_r($result);exit;
            
        return $result;
    }
/*
    public function getProductsCommaseprated($faq_id)
    {
         $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $conn = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')
                                    ->getConnection('core_read');
            
            
        $productsTable = $conn->getTableName('fme_faqs_product');
           $productsentitytable = $conn->getTableName('catalog_product_entity');
           $select = $conn->select()->from(['f' => $productsTable])
                                    ->where('f.faq_id ='.$faq_id);
                                    
                                    
                                   // return $select;
                //echo $select;exit;

        $result = $conn->fetchAll($select);
       // $products_arr[] =null;
        foreach ($result as $value) {
             $product = $objectManager->get('Magento\Catalog\Model\Product')->load($value['product_id']);
             $products_arr[] = $product['name'];
        }
       // echo $select;
//echo "<pre>";
        //print_r($products_arr);
       // echo "<br>";
        return implode(',', $products_arr);
//echo "<br>";
        
      //  print_r($result);      exit;
       //
    }  */

   /* public function getProductsIdCommaseprated($faq_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
         
        $conn = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')
                              ->getConnection('core_read');
            
            
        $productsTable = $conn->getTableName('fme_faqs_product');
        $productsentitytable = $conn->getTableName('catalog_product_entity');
        $select = $conn->select()->from(['f' => $productsTable])
                              ->where('f.faq_id ='.$faq_id);
                                    
                                    
                             // return $select;
          //echo $select;exit;

        $result = $conn->fetchAll($select);
       // $products_arr[] =null;
        foreach ($result as $value) {
            // $product = $objectManager->get('Magento\Catalog\Model\Product')->load($value['product_id']);
             $products_arr[] = $value['product_id'];
        }
       // echo $select;
//echo "<pre>";
        //print_r($products_arr);
       // echo "<br>";
        return implode(',', $products_arr);
//echo "<br>";
        
      //  print_r($result);      exit;
       //
    }  */
    
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
