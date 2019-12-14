<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Controller\Adminhtml\Faqs;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportPost extends \Magento\Backend\App\Action
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
     * Export action from import/export
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        /** start csv content and set template */
        $headers = new \Magento\Framework\DataObject(
            [
                'title' => __('Question'),
                'a_answer' => __('Answer'),
                'identifier' => __('Identifier'),
                'tags' => __('Tags'),
                'status' => __('Status'),
                'show_on_main' => __('Show on main page'),
                'faq_order' => __('Faq Order'),
                't_title' => __('Topic Title'),
                't_identifier' => __('Topic identifier'),
                't_status' => __('Topic Status'),
                't_show_on_main' => __('Topic show on main page'),
                't_topic_order' => __('Topic Order'),
                'store_id' => __('Store IDs'),
            ]
        );
        
        $template = '"{{title}}","{{a_answer}}","{{identifier}}","{{tags}}","{{status}}"' .
            ',"{{show_on_main}}","{{faq_order}}","{{t_title}}","{{t_identifier}}","{{t_status}}","{{t_show_on_main}}","{{t_topic_order}}","{{store_id}}"';
        $content = $headers->toString($template);
        $content .= "\n";

        $collection = $this->_objectManager->create(
            'FME\Prodfaqs\Model\ResourceModel\Faqs\Collection'
        )->joinTopicTable()->joinStoreTable()->joinAnswerTable();
        

                
        while ($faq = $collection->fetchItem()) {
            $content .= $faq->toString($template) . "\n";
        }
        return $this->fileFactory->create('Prodfaqs.csv', $content, DirectoryList::VAR_DIR);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Prodfaqs::importexport');
    }
}
