<?php

namespace FME\Prodfaqs\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
        
        
        
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
             $installer = $setup;
            $installer->startSetup();
                
        if (version_compare($context->getVersion(), '1.0.0', '<')) {
                    $topic_table = $installer->getConnection()->newTable($installer->getTable('fme_faqs_topic'))
                            ->addColumn(
                                'faqs_topic_id',
                                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                null,
                                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                                'Topic ID'
                            )
                            ->addColumn(
                                'title',
                                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                255,
                                ['nullable' => true, 'default' => null],
                                'Title'
                            )
                            ->addColumn(
                                'identifier',
                                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                255,
                                [],
                                'Url Identifier'
                            )
                            ->addColumn(
                                'image',
                                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                255,
                                ['nullable' => true, 'default' => null],
                                'Topic Image'
                            )
                            ->addColumn(
                                'status',
                                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                                null,
                                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                                'Status'
                            )
                            ->addColumn(
                                'show_on_main',
                                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                                null,
                                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                                'Show On Main Page'
                            )
                            ->addColumn(
                                'topic_order',
                                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                                null,
                                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                                'Sorting Order'
                            )
                            ->addColumn(
                                'create_date',
                                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                                null,
                                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                                'Creation Date'
                            )
                            ->addColumn(
                                'update_date',
                                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                                null,
                                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                                'Update Date'
                            );
                
                    $installer->getConnection()->createTable($topic_table);
                                
                                
                                
                                
                                
                                
                    /**
                                * Create table 'fme_faqs_topic_store'
                                */
                    $store_table = $installer->getConnection()->newTable($installer->getTable('fme_faqs_topic_store'))->addColumn(
                        'faqs_topic_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true,'primary' => true],
                        'Faqs Topic ID'
                    )->addColumn(
                        'store_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'primary' => true],
                        'Store ID'
                    )->addIndex(
                        $installer->getIdxName('fme_faqs_topic_store', ['store_id']),
                        ['store_id']
                    )->addForeignKey(
                        $installer->getFkName('fme_ft_store', 'ft_id', 'fme_ft', 'ft_id'),
                        'faqs_topic_id',
                        $installer->getTable('fme_faqs_topic'),
                        'faqs_topic_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->addForeignKey(
                        $installer->getFkName('fme_ft_store', 'store_id', 'store', 'store_id'),
                        'store_id',
                        $installer->getTable('store'),
                        'store_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->setComment(
                        'FaqsTopic To Store Linkage Table'
                    );
                    $installer->getConnection()->createTable($store_table);
                                
                                
                               
                                
                    $fme_faq_table = $installer->getTable('fme_faq');
                                
                    $installer->getConnection()->addColumn(
                        $fme_faq_table,
                        'identifier',
                        [
                                                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                                            'length' => 255,
                                                            'nullable' => true,
                                                            'default' => null,
                                                            'comment' => 'Url Identifier'
                                                    ]
                    );
                                
                    $installer->getConnection()->addColumn(
                        $fme_faq_table,
                        'tags',
                        [
                                                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                                                            'length' => 255,
                                                            'nullable' => true,
                                                            'default' => null,
                                                            'comment' => 'Tags'
                                                    ]
                    );
                                
                    $installer->getConnection()->addColumn(
                        $fme_faq_table,
                        'faqs_topic_id',
                        [
                                                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                                                            'length' => 255,
                                                            'nullable' => false,
                                                            'unsigned' => true,
                                                            'comment' => 'Topic ID'
                                                    ]
                    );
                                
                    $installer->getConnection()->addForeignKey(
                        $installer->getFkName('fme_f', 'f_id', 'fme_ft', 'ft_id'),
                        $fme_faq_table,
                        'faqs_topic_id',
                        $installer->getTable('fme_faqs_topic'),
                        'faqs_topic_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    );
                                
                                
                                
                    /**
                                * Create table 'fme_faqs_product'
                                */
                    $product_attachment_table = $installer->getConnection()->newTable($installer->getTable('fme_faqs_product'))->addColumn(
                        'faq_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'unsigned' => true,'primary' => true],
                        'Faq ID'
                    )->addColumn(
                        'product_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['unsigned' => true, 'nullable' => false, 'primary' => true],
                        'Product ID'
                    )->addForeignKey(
                        $installer->getFkName('FK_fme_faqs_product', 'fp_id', 'fme_fp', 'fp_id'),
                        'faq_id',
                        $installer->getTable('fme_faq'),
                        'faq_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->addForeignKey(
                        $installer->getFkName('FK_fme_faqs_product_entity', 'product_id', 'product', 'product_id'),
                        'product_id',
                        $installer->getTable('catalog_product_entity'),
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )->setComment(
                        'Faqs Product Attachment Table'
                    );
                    $installer->getConnection()->createTable($product_attachment_table);
        }

        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            $table = $installer->getConnection()
            ->newTable($installer->getTable('fme_faq_answers'))
            ->addColumn(
                'answer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'answer',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Answer'
            )
            ->addColumn(
                'faq_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'FAQ Id'
            )->addColumn(
                'likes',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['signed' => true, 'nullable' => false],
                'Likes'
            )->addColumn(
                'dislikes',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['signed' => true, 'nullable' => false],
                'DisLikes'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Status'
            )->addColumn(
                'is_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Is email sent'
            )->addColumn(
                'answer_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Answer By'
            )->addColumn(
                'user_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'User Email'
            )->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => true],
                'User ID'
            )->addColumn(
                'create_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['nullable' => false],
                'Creation Date'
            )
            ->addForeignKey(
                $installer->getFkName('fme_faq_ibfk_1', 'faq_id', 'fme_faq', 'faq_id'),
                'faq_id',
                $installer->getTable('fme_faq'),
                'faq_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Product Faqs Answer Table');
            $installer->getConnection()->createTable($table);
        }
        if (version_compare($context->getVersion(), '1.1.3', '<')) {
             $fme_faq_table = $installer->getTable('fme_faq');
                                
                    $installer->getConnection()->addColumn(
                        $fme_faq_table,
                        'question_by',
                        [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                         'nullable' => false,
                             'default' => '',
                            'comment' => 'Question by'
                                                    ]
                    );

                    $installer->getConnection()->addColumn(
                        $fme_faq_table,
                        'user_email',
                        [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                         'nullable' => false,
                             'default' => '',
                            'comment' => 'User email'
                                                    ]
                    );

                  $installer->getConnection()->addColumn(
                      $fme_faq_table,
                      'user_id',
                      [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                         'nullable' => true,
                            'comment' => 'User id'
                                                    ]
                  );
                  $installer->getConnection()->addColumn(
                      $fme_faq_table,
                      'pro_id',
                      [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                         'nullable' => true,
                            'comment' => 'Product id'
                                                    ]
                  );
                  $installer->getConnection()->addColumn(
                      $fme_faq_table,
                      'product_name',
                      [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                         'nullable' => true,
                            'comment' => 'Product name'
                                                    ]
                  );

                  $installer->getConnection()->addColumn(
                      $fme_faq_table,
                      'product_url',
                      [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                         'nullable' => true,
                            'comment' => 'Product URL'
                                                    ]
                  );

                  $installer->getConnection()->modifyColumn(
                      $fme_faq_table,
                      'title',
                      [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                         'nullable' => true,
                            'comment' => 'FAQ'
                                                    ]
                  );
        }

    
         $installer->endSetup();
    }
}
