<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Controller\Adminhtml\Faqs;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Byproducts extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $filterData;
    protected $filterBuilder;
    protected $contextinterface;
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        ContextInterface $contextinterface,
        FilterBuilder $filterBuilder,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
         $this->filterBuilder = $filterBuilder;
         $this->contextinterface = $contextinterface;
        $this->resultPageFactory = $resultPageFactory;
    }
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Prodfaqs::faqs');
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        // $this->addFilter(
///$this->filterBuilder->setField('faq_id')->setValue(1)->setConditionType('eq')////->create()
         //   );

        //$this->getContext()->getDataProvider()->addFilter($filter);
        //print_r($this->contextinterface->getFiltersParams());exit;
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('FME_Prodfaqs::faqs');
        $resultPage->addBreadcrumb(__('Faqs'), __('Faqs'));
        $resultPage->addBreadcrumb(__('By Products'), __('By Products'));
        $resultPage->getConfig()->getTitle()->prepend(__('By Products'));

        return $resultPage;
    }
}
