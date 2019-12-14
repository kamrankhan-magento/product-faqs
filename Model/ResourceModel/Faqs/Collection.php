<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Model\ResourceModel\Faqs;

use \FME\Prodfaqs\Model\ResourceModel\AbstractCollection;

/**
 *  collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'faq_id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('FME\Prodfaqs\Model\Faqs', 'FME\Prodfaqs\Model\ResourceModel\Faqs');
        $this->_map['fields']['faq_id'] = 'main_table.faq_id';
    }
    

    protected function _afterLoad()
    {
        
       

        $items = $this->getColumnValues('faq_id');


        if (count($items)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['cps' => $this->getTable('fme_faq_answers')])
                    ->where('cps.faq_id IN (?)', $items);
            $result = $connection->fetchall($select);
               //echo $select;
          //  echo "<pre>";
           //    print_r($result);exit;
            if ($result) {
            //  $cms_idd = implode(',', $result);
                foreach ($this as $item) {
                    $item->setData('answers_dynamic', $result);
                }
            }
        }

  /*     $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
      if(count($items)){
             $connection = $this->getConnection();
            
             // echo "<pre>";
                 foreach ($this as $item) {

                   $select = $connection->select()->from(['cps' => $this->getTable('fme_faqs_product')],['cps.product_id'])
                    ->where('cps.faq_id ='.$item->getData()['faq_id']);
            $result = $connection->fetchall($select);
            

             foreach ($result as $value) {
                $product = $objectManager->get('Magento\Catalog\Model\Product')->load($value['product_id']);
                 $pro_arr[] = $product['name'];
             }
             if(isset($pro_arr)){
                 $products_names = implode(',', $pro_arr);
            $item->setData('product_name',$products_names);
                unset($pro_arr);
             }
           
             //  echo $select;
              
            //echo "<pre>";
             //  print_r($pro_arr);
                    
               
                    //print_r($item->getData()['faq_id']);
                //$item->setData('products_id', $result);
                   // break;
                }
            }//exit;   */
            
           



        $this->_previewFlag = false;
        return parent::_afterLoad();
    }
    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }
    
    
    public function joinTopicTable()
    {
        $this->_select->joinLeft(
            ['topic_table' => $this->getTable('fme_faqs_topic')],
            'main_table.faqs_topic_id = topic_table.faqs_topic_id',
            ['t_title' => 'title',
                't_identifier' => 'identifier',
                't_status' => 'status',
                't_show_on_main' => 'show_on_main',
                't_topic_order' => 'topic_order']
        );
        return $this;
    }
    
    public function joinStoreTable()
    {
        $this->_select->joinLeft(
            ['store_table' => $this->getTable('fme_faqs_topic_store')],
            'main_table.faqs_topic_id = store_table.faqs_topic_id',
            ['store_id' => 'store_id']
        );
        return $this;
    }
    public function joinAnswerTable()
    {
        $this->_select->joinLeft(
            ['answer_table' => $this->getTable('fme_faq_answers')],
            'main_table.faq_id = answer_table.faq_id',
            ['a_answer' => 'answer']
        );
        return $this;
    }
    public function joinProductAttachmentTable()
    {
        $this->_select->joinLeft(
            ['fp_table' => $this->getTable('fme_faqs_product')],
            'main_table.faq_id = fp_table.faq_id',
            ['product_id' => 'product_id']
        );
        return $this;
    }
}
