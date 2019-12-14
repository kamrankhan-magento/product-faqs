<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Controller\Adminhtml\Topic;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use FME\Prodfaqs\Model\Topic as ModelTopic;

/**
 * Cms page grid inline edit controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InlineEdit extends \Magento\Backend\App\Action
{
    /** @var PostDataProcessor */
    protected $dataProcessor;
    protected $TopicModel;
    protected $jsonFactory;

    
    
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        ModelTopic $TopicModel,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->TopicModel = $TopicModel;
        $this->jsonFactory = $jsonFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Prodfaqs::topic');
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
            /** @var \Magento\Cms\Model\Page $page */
            $Topic = $this->TopicModel->load($Id);
            try {
                $Data = $this->filterPost($postItems[$Id]);
                $this->validatePost($Data, $Topic, $error, $messages);
                $extendedPageData = $Topic->getData();
                $this->setTopicData($Topic, $extendedPageData, $Data);
                
                $this->TopicModel->save($Topic);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithPageId($Topic, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithPageId($Topic, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPageId(
                    $Topic,
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

    /**
     * Filtering posted data.
     *
     * @param array $postData
     * @return array
     */
    protected function filterPost($postData = [])
    {
        //$pageData = $this->dataProcessor->filter($postData);
       
        return $postData;
    }

    /**
     * Validate post data
     *
     * @param array $pageData
     * @param \Magento\Cms\Model\Page $page
     * @param bool $error
     * @param array $messages
     * @return void
     */
    protected function validatePost(array $pageData, ModelTopic $page, &$error, array &$messages)
    {
        if (!($this->dataProcessor->validate($pageData) && $this->dataProcessor->validateRequireEntry($pageData))) {
            $error = true;
            foreach ($this->messageManager->getMessages(true)->getItems() as $error) {
                $messages[] = $this->getErrorWithId($page, $error->getText());
            }
        }
    }

    /**
     * Add page title to error message
     *
     * @param PageInterface $page
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithId(ModelTopic $page, $errorText)
    {
        return '[Page ID: ' . $page->getId() . '] ' . $errorText;
    }

    /**
     * Set cms page data
     *
     * @param \Magento\Cms\Model\Page $page
     * @param array $extendedPageData
     * @param array $pageData
     * @return $this
     */
    public function setTopicData(ModelTopic $page, array $extendedPageData, array $pageData)
    {
        $page->setData(array_merge($page->getData(), $extendedPageData, $pageData));
        return $this;
    }
}
