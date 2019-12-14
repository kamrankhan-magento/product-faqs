<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Controller\Adminhtml\Faqs;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use FME\Prodfaqs\Model\Faqs as ModelFaqs;

class InlineEdit extends \Magento\Backend\App\Action
{
    /** @var PostDataProcessor */
    protected $dataProcessor;
    protected $FaqsModel;
    protected $jsonFactory;

    
    
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        ModelFaqs $FaqsModel,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->FaqsModel = $FaqsModel;
        $this->jsonFactory = $jsonFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Prodfaqs::faqs');
    }
    
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $Id) {
            $Faqs = $this->FaqsModel->load($Id);
            try {
                $Data = $this->filterPost($postItems[$Id]);
                $this->validatePost($Data, $Faqs, $error, $messages);
                $extendedPageData = $Faqs->getData();
                $this->setFaqsData($Faqs, $extendedPageData, $Data);
                
                $this->FaqsModel->save($Faqs);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithId($Faqs, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithId($Faqs, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithId(
                    $Faqs,
                    __('Something went wrong while saving the item.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    protected function filterPost($postData = [])
    {
        //$pageData = $this->dataProcessor->filter($postData);
       
        return $postData;
    }

  
    protected function validatePost(array $pageData, ModelFaqs $page, &$error, array &$messages)
    {
        if (!($this->dataProcessor->validate($pageData) && $this->dataProcessor->validateRequireEntry($pageData))) {
            $error = true;
            foreach ($this->messageManager->getMessages(true)->getItems() as $error) {
                $messages[] = $this->getErrorWithId($page, $error->getText());
            }
        }
    }

  
    protected function getErrorWithId(ModelFaqs $page, $errorText)
    {
        return '[Page ID: ' . $page->getId() . '] ' . $errorText;
    }

  
    public function setFaqsData(ModelFaqs $page, array $extendedPageData, array $pageData)
    {
        $page->setData(array_merge($page->getData(), $extendedPageData, $pageData));
        return $this;
    }
}
