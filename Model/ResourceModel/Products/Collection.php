<?php

namespace FME\Prodfaqs\Model\ResourceModel\Products;

use \FME\Prodfaqs\Model\ResourceModel\AbstractCollection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'product_id';

    protected $_previewFlag;

    protected function _construct()
    {
        $this->_init('FME\Prodfaqs\Model\Products', 'FME\Prodfaqs\Model\ResourceModel\Products');

        $this->_map['fields']['product_id'] ='main_table.product_id';
    }


    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->group('product_id');
        $this->getSelect()->joinLeft(
            ['secondTable' => $this->getTable('catalog_product_entity')],
            'main_table.product_id = secondTable.entity_id',
            ['main_table.product_id','main_table.faq_id','secondTable.entity_id','secondTable.sku']
        );
        //echo $this->getSelect();exit;
    }

    protected function _afterLoad()
    {
      /*
       

       $items = $this->getColumnValues('product_id');

       echo "<pre>";
       print_r(array_unique($items));
      print_r($this->getData());exit;


       
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
                 
            }



            foreach ($this as $item => $value) {

                
            	echo $item;

                print_r($value->getData());
                }



            print_r(($this->getData()));exit;

            
        }


        $this->_previewFlag = false;
        return parent::_afterLoad();
        */
    }
}
