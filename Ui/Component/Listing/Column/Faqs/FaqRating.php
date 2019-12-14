<?php
namespace FME\Prodfaqs\Ui\Component\Listing\Column\Faqs;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * @method Avatar setName($name)
 */
class FaqRating extends Column
{
   
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    protected $faqsModel;
    /**
     * @var \Sample\News\Model\Uploader
     */
   

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Sample\News\Model\Uploader $imageModel
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \FME\Prodfaqs\Model\Faqs $faqsModel,
        array $components = [],
        array $data = []
       
    ) {
       
        $this->urlBuilder = $urlBuilder;
        $this->faqsModel = $faqsModel;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                 $faqData = $this->faqsModel->load($item['faq_id']);
                 $rating_num = (int) $faqData->getRatingNum();
                $rating_count = (int) $faqData->getRatingCount();
                if ($rating_count !=0) {
                    $item[$fieldName] =   round(($rating_num/$rating_count), 2);
                } else {
                     $item[$fieldName] =0;
                }
            }
        }
       
        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
}
