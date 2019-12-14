<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Model\Faqs;

/**
 * Tax Rate CSV Import Handler
 */
class CsvImportHandler
{
    /**
     * Collection of publicly available stores
     *
     * @var \Magento\Store\Model\ResourceModel\Store\Collection
     */
    protected $_publicStores;

    protected $_faqsFactory;
    protected $_answersFactory;
    protected $_topicFactory;

    protected $_taxRateFactory;
    
    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @param \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection
     * @param \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory
     * @param \Magento\Framework\File\Csv $csvProcessor
     */
    public function __construct(
        \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection,
        \FME\Prodfaqs\Model\FaqsFactory $faqsFactory,
        \FME\Prodfaqs\Model\TopicFactory $topicFactory,
        \FME\Prodfaqs\Model\AnswersFactory $answersFactory,
        \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory,
        \Magento\Framework\File\Csv $csvProcessor
    ) {
        // prevent admin store from loading
        $this->_publicStores = $storeCollection->setLoadDefault(false);
        $this->_faqsFactory = $faqsFactory;
        $this->_topicFactory = $topicFactory;
         $this->_answersFactory = $answersFactory;
        $this->_taxRateFactory = $taxRateFactory;
        $this->csvProcessor = $csvProcessor;
    }

    /**
     * Retrieve a list of fields required for CSV file (order is important!)
     *
     * @return array
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => __('Question'),
            1 => __('Answer'),
            2 => __('Identifier'),
            3 => __('Topic Title'),
            4 => __('Topic identifier')
        ];
    }

    /**
     * Import Tax Rates from CSV file
     *
     * @param array $file file info retrieved from $_FILES array
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importFromCsvFile($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $faqsRawData = $this->csvProcessor->getData($file['tmp_name']);
        // first row of file represents headers
        $fileFields = $faqsRawData[0];
        
        $validFields = $this->_filterFileFields($fileFields);
        
        if (!$validFields) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file fields found.'));
        }
                
        
        foreach ($faqsRawData as $rowIndex => $dataRow) {
            // skip headers
            if ($rowIndex == 0) {
                continue;
            }
            $faqsCache = $this->_importFaq($dataRow);
        }
    }

    /**
     * Filter file fields (i.e. unset invalid fields)
     *
     * @param array $fileFields
     * @return string[] filtered fields
     */
    protected function _filterFileFields(array $fileFields)
    {
        $requiredFields = $this->getRequiredCsvFields();
        $requiredFieldsNum = count($this->getRequiredCsvFields());
        $fileFieldsNum = count($fileFields);
        
        if ($fileFieldsNum < $requiredFieldsNum) {
            return false;
        }
        
        // check the required fields availability in file
        
        for ($index = 0; $index < $requiredFieldsNum; $index++) {
            if (array_search(strtolower($requiredFields[$index]), array_map('strtolower', $fileFields)) === false) {
                return false;
            }
        }

        return true;
    }



    /**
     * Import single rate
     *
     * @param array $faqData
     * @param array $regionsCache cache of regions of already used countries (is used to optimize performance)
     * @param array $storesCache cache of stores related to tax rate titles
     * @return array regions cache populated with regions related to country of imported tax rate
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _importFaq(array $faqData)
    {
        //if topic identifier exist
        if (!empty($faqData[8])) {
            //first save the topic, if not exist.
            $topic_model = $this->_topicFactory->create();
            $topic = $topic_model->load($faqData[8], 'identifier');
            $topic_id = $topic->getId();
            
            if (!$topic_id) {
                $topicModelData = [
                    'title' => $faqData[7],
                    'identifier' => $faqData[8],
                    'image' => '',
                    'status' => $faqData[9],
                    'show_on_main' => $faqData[10],
                    'topic_order' => $faqData[11],
                    'create_date' => date("Y-m-d H:i:s"),
                    'update_date' => date("Y-m-d H:i:s"),
                    'store_id' => ($faqData[12] == '') ? [0] : explode(',', $faqData[12]),
                ];
                
                $topic->setData($topicModelData);
                $topic->save();
                $topic_id = $topic->getId();
            }
            
            
            //save the faq against topic_id, if not exist
            $faqs_model = $this->_faqsFactory->create();
            $faq = $faqs_model->load($faqData[2], 'identifier');
            $faq_id = $faq->getId();
            
            if (!$faq_id) {
                $faqModelData = [
                    'faqs_topic_id' => $topic_id,
                    'title' => $faqData[0],
                    'identifier' => $faqData[2],
                    'show_on_main' => $faqData[5],
                    'faq_order' => $faqData[6],
                    'rating_num' => 0,
                    'rating_count' => 0,
                    'rating_stars' => 0,
                    'accordion_opened' => 0,
                    'tags' => $faqData[3],
                    'status' => $faqData[4],
                    'create_date' => date("Y-m-d H:i:s"),
                    'update_date' => date("Y-m-d H:i:s"),
                ];
                
                $faq->setData($faqModelData);
                $faq->save();
                 $faq_id = $faq->getId();


                 $ans_model = $this->_answersFactory->create();
               //  $ans = $ans_model->load();
                 $ansModelData = [
                    'faq_id' => $faq_id,
                    'answer' => $faqData[1],
                    'answer_by' => 'admin',
                    'user_id' => 0,
                    'create_date' => date("Y-m-d H:i:s"),
                   
                 ];
                
                 $ans_model->setData($ansModelData);
                 $ans_model->save();
            }
        }

        return $faq;
    }
}
