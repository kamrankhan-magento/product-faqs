<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Controller\Adminhtml\Faqs;

use Magento\Framework\Controller\ResultFactory;

class Importexport extends \Magento\Backend\App\Action
{
    /**
     * Import and export Page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('FME\Prodfaqs\Block\Adminhtml\Faqs\ImportExport')
        );
    
        $resultPage->getConfig()->getTitle()->prepend(__('Import and Export Faqs'));
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Prodfaqs::importexport');
    }
}
