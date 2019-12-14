<?php

namespace FME\Prodfaqs\Model\ResourceModel\Answers;

use \FME\Prodfaqs\Model\ResourceModel\AbstractCollection;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'answer_id';

    protected $_previewFlag;

    protected function _construct()
    {
        $this->_init('FME\Prodfaqs\Model\Answers', 'FME\Prodfaqs\Model\ResourceModel\Answers');

        $this->_map['fields']['answer_id'] ='main_table.answer_id';
    }
}
