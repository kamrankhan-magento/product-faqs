<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Controller\Adminhtml\Faqs;

use Magento\Backend\App\Action;

class Products extends \Magento\Backend\App\Action
{
    
    
    
    protected $resultLayoutFactory;

    
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;
        
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Prodfaqs::faqs');
    }

   
   
    public function execute()
    {
       
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('prodfaqs.edit.tab.products')
                ->setInProducts($this->getRequest()->getPost('faqs_products', null));
        
        return $resultLayout;
    }
}
