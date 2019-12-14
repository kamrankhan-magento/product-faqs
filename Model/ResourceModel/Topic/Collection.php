<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Model\ResourceModel\Topic;

use \FME\Prodfaqs\Model\ResourceModel\AbstractCollection;

/**
 *  collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'faqs_topic_id';

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
        $this->_init('FME\Prodfaqs\Model\Topic', 'FME\Prodfaqs\Model\ResourceModel\Topic');
        $this->_map['fields']['faqs_topic_id'] = 'main_table.faqs_topic_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    

    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
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
    
    
    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('fme_faqs_topic_store', 'faqs_topic_id');
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('fme_faqs_topic_store', 'faqs_topic_id');
    }
}
