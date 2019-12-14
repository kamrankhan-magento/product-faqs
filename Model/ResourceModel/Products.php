<?php

namespace FME\Prodfaqs\Model\ResourceModel;

class Products extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    protected function _construct()
    {
        $this->_init('fme_faqs_product', 'product_id');
    }
}
