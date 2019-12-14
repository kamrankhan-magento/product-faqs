<?php

namespace FME\Prodfaqs\Model\ResourceModel;

class Topic extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
        
    protected $_storeManager;
        
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
    }
           
    protected function _construct()
    {
        $this->_init('fme_faqs_topic', 'faqs_topic_id');
    }
        
        /**
         * Process topic data before deleting
         *
         * @param \Magento\Framework\Model\AbstractModel $object
         * @return $this
         */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['faqs_topic_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('fme_faqs_topic_store'), $condition);

        return parent::_beforeDelete($object);
    }
        
        
        
        /**
         * Process page data before saving
         *
         * @param \Magento\Framework\Model\AbstractModel $object
         * @return $this
         * @throws \Magento\Framework\Exception\LocalizedException
         */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
            
            
        if (!$this->isValidIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The topic URL key contains capital letters or disallowed symbols.')
            );
        }
            
        if ($this->isNumericIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The topic URL key cannot be made of only numbers.')
            );
        }


               
        if (!$this->getIsUniqueTopicToStores($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('URL key for specified store already exists.')
            );
        }
                        
            
        return parent::_beforeSave($object);
    }

       

       /**
        * Perform operations after object load
        *
        * @param \Magento\Framework\Model\AbstractModel $object
        * @return $this
        */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());

            $object->setData('store_id', $stores);
        }
            
        return parent::_afterLoad($object);
    }
        
        
        /**
         * Assign topic to store views
         *
         * @param \Magento\Framework\Model\AbstractModel $object
         * @return $this
         */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
            
            
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
            
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table = $this->getTable('fme_faqs_topic_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['faqs_topic_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = ['faqs_topic_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }
            
        return parent::_afterSave($object);
    }
        
        
        
        
         /**
          *  Check whether topic identifier is numeric
          *
          * @param \Magento\Framework\Model\AbstractModel $object
          * @return bool
          */
    protected function isNumericIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

        /**
         *  Check whether topic identifier is valid
         *
         * @param \Magento\Framework\Model\AbstractModel $object
         * @return bool
         */
    protected function isValidIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }
        
        /**
         * Get store ids to which specified item is assigned
         *
         * @param int $pageId
         * @return array
         */
    public function lookupStoreIds($faqTopicId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('fme_faqs_topic_store'),
            'store_id'
        )->where(
            'faqs_topic_id = ?',
            (int)$faqTopicId
        );

        return $connection->fetchCol($select);
    }
        
        
               
        /**
         * Check for unique of identifier of block to selected store(s).
         *
         * @param \Magento\Framework\Model\AbstractModel $object
         * @return bool
         * @SuppressWarnings(PHPMD.BooleanGetMethodName)
         */
    public function getIsUniqueTopicToStores(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($this->_storeManager->hasSingleStore()) {
            $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->getConnection()->select()->from(
            ['ft' => $this->getMainTable()]
        )->join(
            ['fts' => $this->getTable('fme_faqs_topic_store')],
            'ft.faqs_topic_id = fts.faqs_topic_id',
            []
        )->where(
            'ft.identifier = ?',
            $object->getData('identifier')
        )->where(
            'fts.store_id IN (?)',
            $stores
        );

        if ($object->getId()) { //in edit mode, compare other then current
            $select->where('ft.faqs_topic_id <> ?', $object->getId());
        }

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }

        return true;
    }
        
        
    public function checkIdentifier($identifier, $storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)->columns('ft.faqs_topic_id')->order('fts.store_id DESC')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }
        
        
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['ft' => $this->getMainTable()]
        )->join(
            ['fts' => $this->getTable('fme_faqs_topic_store')],
            'ft.faqs_topic_id = fts.faqs_topic_id',
            []
        )->where(
            'ft.identifier = ?',
            $identifier
        )->where(
            'fts.store_id IN (?)',
            $store
        );

        if (!is_null($isActive)) {
            $select->where('ft.status = ?', $isActive);
        }

        return $select;
    }
}
