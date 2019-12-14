<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Controller\Adminhtml\Faqs;

use Magento\Framework\Controller\ResultFactory;

class ImportPost extends \Magento\Backend\App\Action
{
    
    protected $fileFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }
    
    /**
     * import action from import/export tax
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost() && !empty($this->getRequest()->getFiles('import_faqs_file'))) {
            try {
                /** @var $importHandler \Magento\TaxImportExport\Model\Rate\CsvImportHandler */
                $importHandler = $this->_objectManager->create('FME\Prodfaqs\Model\Faqs\CsvImportHandler');
                $importHandler->importFromCsvFile($this->getRequest()->getFiles('import_faqs_file'));

                $this->messageManager->addSuccess(__('The faqs have been imported.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Invalid file upload attempt'));
            }
        } else {
            $this->messageManager->addError(__('Invalid file upload attempt.'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }

     /**
      * @return bool
      */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Prodfaqs::importexport');
    }
}
