<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FME\Prodfaqs\Controller\Adminhtml\Faqs;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Magento\Ui\Api\Data\BookmarkInterface;
use Magento\Ui\Api\Data\BookmarkInterfaceFactory;
use Magento\Ui\Controller\Adminhtml\AbstractAction;

class Filters extends \Magento\Backend\App\Action
{
    const CURRENT_IDENTIFIER = 'current';

    const ACTIVE_IDENTIFIER = 'activeIndex';

    const VIEWS_IDENTIFIER = 'views';

    /**
     * @var BookmarkRepositoryInterface
     */
    protected $bookmarkRepository;

    /**
     * @var BookmarkManagementInterface
     */
    protected $bookmarkManagement;

    /**
     * @var BookmarkInterfaceFactory
     */
    protected $bookmarkFactory;

    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @var DecoderInterface
     */
    protected $jsonDecoder;
    protected $jsonEncoder;
    /**
     * {@inheritdoc}
     */
    public function __construct(
        Context $context,
        BookmarkRepositoryInterface $bookmarkRepository,
        BookmarkManagementInterface $bookmarkManagement,
        BookmarkInterfaceFactory $bookmarkFactory,
        UserContextInterface $userContext,
        DecoderInterface $jsonDecoder,
        EncoderInterface $jsonEncoder
    ) {
        parent::__construct($context);
        $this->bookmarkRepository = $bookmarkRepository;
        $this->bookmarkManagement = $bookmarkManagement;
        $this->bookmarkFactory = $bookmarkFactory;
        $this->userContext = $userContext;
        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('FME_Prodfaqs::faqs');
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
     
        $bookmark = $this->bookmarkFactory->create();
         $params = $this->getRequest()->getParams();

        if (isset($params['p'])) {
            $jsonData = "{\"current\":{\"filters\":{\"applied\":{\"placeholder\":true}},\"search\":{\"value\":\"\"},\"columns\":{\"product_id\":{\"visible\":true,\"sorting\":false},\"product_name\":{\"visible\":true,\"sorting\":false},\"sku\":{\"visible\":true,\"sorting\":false},\"questions\":{\"visible\":true,\"sorting\":false},\"visiblequestions\":{\"visible\":true,\"sorting\":false},\"ids\":{\"visible\":true,\"sorting\":false}},\"displayMode\":\"grid\",\"positions\":{\"ids\":0,\"product_name\":1,\"product_id\":2,\"sku\":3,\"visiblequestions\":4,\"questions\":5},\"paging\":{\"options\":{\"20\":{\"value\":20,\"label\":20},\"30\":{\"value\":30,\"label\":30},\"50\":{\"value\":50,\"label\":50},\"100\":{\"value\":100,\"label\":100},\"200\":{\"value\":200,\"label\":200}},\"value\":20}}}";
          
            $json2 = $jsonData;
         
            $data = $this->jsonDecoder->decode($json2);
        } elseif (isset($params['f'])) {
            $jsonData = "{\"current\":{\"search\":{\"value\":\"\"},\"columns\":{\"faq_id\":{\"visible\":true,\"sorting\":\"asc\"},\"title\":{\"visible\":true,\"sorting\":false},\"faq_answer\":{\"visible\":true,\"sorting\":false},\"ids\":{\"visible\":true,\"sorting\":false},\"status\":{\"visible\":true,\"sorting\":false},\"actions\":{\"visible\":true,\"sorting\":false},\"create_date\":{\"visible\":true,\"sorting\":false},\"newanswers\":{\"visible\":true,\"sorting\":false},\"answers\":{\"visible\":true,\"sorting\":false},\"show_on_main\":{\"visible\":true,\"sorting\":false},\"rating\":{\"visible\":true,\"sorting\":false},\"question_by\":{\"visible\":true,\"sorting\":false},\"product_name\":{\"visible\":true,\"sorting\":false}},\"filters\":{\"applied\":{\"placeholder\":true}},\"displayMode\":\"grid\",\"paging\":{\"options\":{\"20\":{\"value\":20,\"label\":20},\"30\":{\"value\":30,\"label\":30},\"50\":{\"value\":50,\"label\":50},\"100\":{\"value\":100,\"label\":100},\"200\":{\"value\":200,\"label\":200}},\"value\":20},\"positions\":{\"faq_id\":0,\"ids\":1,\"title\":2,\"status\":3,\"show_on_main\":4,\"answers\":5,\"newanswers\":6,\"rating\":7,\"create_date\":8,\"actions\":9,\"question_by\":10,\"product_name\":11}}}";

            $json2 = $jsonData;
         
            $data = $this->jsonDecoder->decode($json2);
       //    echo "<pre>";
      // print_r($data);exit;
        } elseif (isset($params['id'])) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            $product = $objectManager->get('Magento\Catalog\Model\Product')->load($params['id']);
        
        // print_r($params);exit;
            $jsonData = "{\"current\":{\"search\":{\"value\":\"\"},\"columns\":{\"faq_id\":{\"visible\":true,\"sorting\":\"asc\"},\"title\":{\"visible\":true,\"sorting\":false},\"faq_answer\":{\"visible\":true,\"sorting\":false},\"ids\":{\"visible\":true,\"sorting\":false},\"status\":{\"visible\":true,\"sorting\":false},\"actions\":{\"visible\":true,\"sorting\":false},\"create_date\":{\"visible\":true,\"sorting\":false},\"newanswers\":{\"visible\":true,\"sorting\":false},\"answers\":{\"visible\":true,\"sorting\":false},\"show_on_main\":{\"visible\":true,\"sorting\":false},\"rating\":{\"visible\":true,\"sorting\":false},\"question_by\":{\"visible\":true,\"sorting\":false},\"product_name\":{\"visible\":true,\"sorting\":false}},\"filters\":{\"applied\":{\"placeholder\":true,\"product_name\":\"product 1\"}},\"displayMode\":\"grid\",\"paging\":{\"options\":{\"20\":{\"value\":20,\"label\":20},\"30\":{\"value\":30,\"label\":30},\"50\":{\"value\":50,\"label\":50},\"100\":{\"value\":100,\"label\":100},\"200\":{\"value\":200,\"label\":200}},\"value\":20},\"positions\":{\"faq_id\":0,\"ids\":1,\"title\":2,\"status\":3,\"show_on_main\":4,\"answers\":5,\"newanswers\":6,\"rating\":7,\"create_date\":8,\"actions\":9,\"question_by\":10,\"product_name\":11}}}";
        // echo $jsonData;
            $data = $this->jsonDecoder->decode($jsonData);
            $data['current']['filters']['applied']['product_name'] = $product['name'];
       // echo "<pre>";
      // print_r($data);exit;

            $json2 = $this->jsonEncoder->encode($data);
        }

        $action = key($data);
        switch ($action) {
            case self::ACTIVE_IDENTIFIER:
                $this->updateCurrentBookmark($data[$action]);
                break;

            case self::CURRENT_IDENTIFIER:
                $this->updateBookmark(
                    $bookmark,
                    $action,
                    $bookmark->getTitle(),
                    $json2
                );

                break;

            case self::VIEWS_IDENTIFIER:
                foreach ($data[$action] as $identifier => $data) {
                    $this->updateBookmark(
                        $bookmark,
                        $identifier,
                        isset($data['label']) ? $data['label'] : '',
                        $json2
                    );
                    $this->updateCurrentBookmark($identifier);
                }

                break;

            default:
                throw new \LogicException(__('Unsupported bookmark action.'));
        }

        if (isset($params['p'])) {
            return $resultRedirect->setPath('prodfaqs/faqs/byproducts');
        } else {
            return $resultRedirect->setPath('prodfaqs/faqs/index');
        }
    }

    protected function updateBookmark(BookmarkInterface $bookmark, $identifier, $title, $config)
    {
        $updateBookmark = $this->checkBookmark($identifier);
        if ($updateBookmark !== false) {
            $bookmark = $updateBookmark;
        }
        $params = $this->getRequest()->getParams();
        
        if (isset($params['p'])) {
             $grid = 'prodfaqs_byproducts_listing';
        } else {
            $grid = 'prodfaqs_faqs_listing';
        }
          
        

        $bookmark->setUserId($this->userContext->getUserId())
            ->setNamespace($grid)
            ->setIdentifier($identifier)
            ->setTitle($title)
            ->setConfig($config);
        $this->bookmarkRepository->save($bookmark);
    }

    /**
     * Update current bookmark
     *
     * @param string $identifier
     * @return void
     */
    protected function updateCurrentBookmark($identifier)
    {
        $params = $this->getRequest()->getParams();
      
          $params = $this->getRequest()->getParams();
        
        if (isset($params['p'])) {
             $grid = 'prodfaqs_byproducts_listing';
        } else {
            $grid = 'prodfaqs_faqs_listing';
        }
        
        $bookmarks = $this->bookmarkManagement->loadByNamespace($grid);
        foreach ($bookmarks->getItems() as $bookmark) {
            if ($bookmark->getIdentifier() == $identifier) {
                $bookmark->setCurrent(true);
            } else {
                $bookmark->setCurrent(false);
            }
            $this->bookmarkRepository->save($bookmark);
        }
    }

    /**
     * Check bookmark by identifier
     *
     * @param string $identifier
     * @return bool|BookmarkInterface
     */
    protected function checkBookmark($identifier)
    {
        $result = false;
           $params = $this->getRequest()->getParams();
        
        if (isset($params['p'])) {
             $grid = 'prodfaqs_byproducts_listing';
        } else {
            $grid = 'prodfaqs_faqs_listing';
        }
       
        $updateBookmark = $this->bookmarkManagement->getByIdentifierNamespace(
            $identifier,
            $grid
        );

        if ($updateBookmark) {
            $result = $updateBookmark;
        }

        return $result;
    }
}
