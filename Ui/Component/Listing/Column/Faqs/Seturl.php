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
class Seturl extends Column
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
        \FME\Prodfaqs\Model\Products $productsModel,
        array $components = [],
        array $data = []
    ) {
       
        $this->urlBuilder = $urlBuilder;
        $this->_answersModel = $answersModel;
        $this->_productsModel = $productsModel;
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
                $url = $this->urlBuilder->getUrl('prodfaqs/faqs/edit', ['faq_id' => $item['faq_id']]);
                $item['product_id'] = $this->_productsModel->getProductsIdCommaseprated($item['faq_id']);
                 $faq =  $item[$fieldName];
                 $item[$fieldName] =  ("<a  onclick=\"window.location='$url'\" href='$url' >".$faq."</a>");
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
