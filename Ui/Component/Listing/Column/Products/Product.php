<?php
namespace FME\Prodfaqs\Ui\Component\Listing\Column\Products;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * @method Avatar setName($name)
 */
class Product extends Column
{
   
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    protected $_productsModel;
    /**
     * @var \Sample\News\Model\Uploader
     */
    protected $urlBuilder;

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
        \FME\Prodfaqs\Model\Products $productModel,
        array $components = [],
        array $data = []
    ) {
       
        $this->urlBuilder = $urlBuilder;
        $this->_productsModel = $productModel;
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
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if (isset($dataSource['data']['items'])) {
            $url = $this->urlBuilder->getUrl('prodfaqs/faqs/filters');

            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                // $item[$fieldName] =  count($this->_answersModel->loadAnswersCount($item['faq_id']));
                
                $suburl = $url.'?id='.$item['product_id'];
                $product = $objectManager->get('Magento\Catalog\Model\Product')->load($item['product_id']);
              //  $item[$fieldName] =  $product['name'];
                 $item[$fieldName] =  ("<a  onclick=\"window.location='$suburl'\" href='$suburl' >".$product['name']."</a>");
                $item['sku'] = $product['sku'];
                $item['questions'] = count($this->_productsModel->CountProductFaq($item['product_id']));

                $item['visiblequestions'] = count($this->_productsModel->CountVisibleProductFaq($item['product_id']));
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
