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
class NewAnswers extends Column
{
   
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

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
        \FME\Prodfaqs\Model\Answers $answersModel,
        array $components = [],
        array $data = []
    ) {
       
        $this->urlBuilder = $urlBuilder;
        $this->_answersModel = $answersModel;
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
                 $item[$fieldName] =  count($this->_answersModel->loadNewAnswers($item['faq_id']));
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
