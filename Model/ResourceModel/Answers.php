<?php

namespace FME\Prodfaqs\Model\ResourceModel;

class Answers extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    protected function _construct()
    {
        $this->_init('fme_faq_answers', 'answer_id');
    }
}
