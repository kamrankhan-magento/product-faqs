<?php

namespace FME\Prodfaqs\Model\ResourceModel;

class Faqs extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
        
    protected $_storeManager;
    protected $_transportBuilder;
    protected $scopeConfig;

    const XML_PATH_EMAIL_SENDER = 'prodfaqs/email/sender';
    const XML_PATH_EMAIL_TEMPLATE = 'prodfaqs/email/replytemplate';
    const XML_PATH_STORE_NAME = 'general/store_information/name';

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \FME\Prodfaqs\Helper\Data $myModuleHelper,
        \Psr\Log\LoggerInterface $logger,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_mymoduleHelper = $myModuleHelper;
        $this->_logger = $logger;
    }
           
    protected function _construct()
    {
        $this->_init('fme_faq', 'faq_id');
    }
        
        
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
            
           
            
        if (!$this->isValidIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The faq URL key contains capital letters or disallowed symbols.')
            );
        }
            
        if ($this->isNumericIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The faq URL key cannot be made of only numbers.')
            );
        }


               
        if (!$this->getIsUniqueIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('URL key for specified faq already exists.')
            );
        }
                        
            
        return parent::_beforeSave($object);
    }
        
        
        
        /**
         *  Check whether  identifier is numeric
         *
         * @param \Magento\Framework\Model\AbstractModel $object
         * @return bool
         */
    protected function isNumericIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getData('identifier') == '') {
            return false;
        }
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

        /**
         *  Check whether  identifier is valid
         *
         * @param \Magento\Framework\Model\AbstractModel $object
         * @return bool
         */
    protected function isValidIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getData('identifier') == '') {
            return true;
        }
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }
        
        /**
         * Check for unique of identifier.
         *
         * @param \Magento\Framework\Model\AbstractModel $object
         * @return bool
         * @SuppressWarnings(PHPMD.BooleanGetMethodName)
         */
    public function getIsUniqueIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getData('identifier') == '') {
            return true;
        }
            
        $select = $this->getConnection()->select()->from(
            ['f' => $this->getMainTable()]
        )->where(
            'f.identifier = ?',
            $object->getData('identifier')
        );

        if ($object->getId()) { //in edit mode, compare other then current
            $select->where('f.faq_id <> ?', $object->getId());
        }

        if ($this->getConnection()->fetchRow($select)) {
            return false;
        }

        return true;
    }
        
        
    public function checkIdentifier($identifier)
    {
        $select = $this->_getLoadByIdentifierSelect($identifier, 1);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)->columns('f.faq_id')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }
        
        
    protected function _getLoadByIdentifierSelect($identifier, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['f' => $this->getMainTable()]
        )->where(
            'f.identifier = ?',
            $identifier
        );

        if (!is_null($isActive)) {
            $select->where('f.status = ?', $isActive);
        }

        return $select;
    }
        
    public function getProducts(\FME\Prodfaqs\Model\Faqs $object)
    {
        
        $select = $this->getConnection()->select()->from(
            $this->getTable('fme_faqs_product'),
            ['product_id']
        )->where(
            'faq_id = ?',
            (int)$object->getId()
        );
            
            
        return $this->getConnection()->fetchCol($select);
    }

    public function getAnswers(\FME\Prodfaqs\Model\Faqs $object)
    {
        
        $select = $this->getConnection()->select()->from(
            $this->getTable('fme_faq_answers'),
            ['answer_id']
        )->where(
            'faq_id = ?',
            (int)$object->getId()
        );
            
            
        return $this->getConnection()->fetchCol($select);
    }
        
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $products = $this->getProducts($object);
            $object->setData('product_id', $products);
        }
            
        return parent::_afterLoad($object);
    }
        
        
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        
    
      //  echo "<pre>";
      //  print_r($object->getData());exit;
       // $this->_logger->log(100,print_r($object->getData(),true));
        $products = (array)$object->getData("product_id");
        if (isset($products)) {
            $productIds = $products;
            $condition = $this->getConnection()->quoteInto('faq_id = ?', $object->getId());
            $this->getConnection()->delete($this->getTable('fme_faqs_product'), $condition);
            foreach ($productIds as $_product) {
                $newsArray = [];
                $newsArray['faq_id'] = $object->getId();
                $newsArray['product_id'] = $_product;
                $this->getConnection()->insert($this->getTable('fme_faqs_product'), $newsArray);
            }
        }
        
        $links = $object->getData("answers_dynamic");

      //  print_r($links);exit;
        if (isset($links)) {
            $productIds = $links;
            $condition = $this->getConnection()->quoteInto('faq_id = ?', $object->getId());
            // echo $condition;exit;
            $this->getConnection()->delete($this->getTable('fme_faq_answers'), $condition);
           
            foreach ($productIds as $_product) {
                 $newsArray = [];
                if ($_product['create_date'] == '' || $_product['create_date'] == null) {
                    date_default_timezone_set($this->_mymoduleHelper->timezone());
                    $rep_time = date("Y-m-d").' '.date("h:i:s");
           
                    $newsArray['create_date']= $rep_time;
                } else {
                    $newsArray['create_date'] = $_product['create_date'];
                }

                if ($_product['answer_detail']=='admin') {
                    $newsArray['answer_by'] = $_product['answer_detail'];
                } else {
                    $newsArray['answer_by'] = $_product['answer_by'];
                }




                if ($_product['is_email'] == 0 && $object->getData('question_by') != 'admin' && $newsArray['answer_by'] == 'admin') {

                    $_product['storename']=$this->scopeConfig->getValue(self::XML_PATH_STORE_NAME, $storeScope);


                    $postObject = new \Magento\Framework\DataObject();
                    $_product['question']=$object->getData('title');
                    $postObject->setData($_product);
                
                    $transport = $this->_transportBuilder
                                    ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                                    ->setTemplateOptions(
                                        [
                                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                            'store' => $this->_storeManager->getStore()->getId(),
                                        ]
                                    )
                                    ->setTemplateVars(['data' => $postObject])
                                    ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                                    ->addTo($object->getData('user_email'))

                                    ->getTransport();
                        
                    $transport->sendMessage();
                    $_product['is_email'] = 1; 
                }
                
                $newsArray['is_email'] = $_product['is_email'];
                $newsArray['user_id'] = $_product['user_id'];
                $newsArray['answer'] = $_product['answer'];
                $newsArray['likes'] = $_product['likes'];
                $newsArray['dislikes'] = $_product['dislikes'];
                $newsArray['status'] = $_product['status'];
                 $newsArray['user_email'] = $_product['user_email'];
                $newsArray['faq_id'] = (int)$object->getId();

            //   print_r($newsArray);
                $this->getConnection()->insert($this->getTable('fme_faq_answers'), $newsArray);
            }
          // exit;
        }
            
        return parent::_afterSave($object);
    }
}
