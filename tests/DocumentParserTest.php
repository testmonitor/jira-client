<?php

namespace TestMonitor\Jira\Tests;

use DH\Adf\Node\BlockNode;
use PHPUnit\Framework\TestCase;
use TestMonitor\Jira\Parsers\Adf\Document;
use DH\Adf\Node\Block\Document as BlockDocument;

class DocumentParserTest extends TestCase
{
    /** @test */
    public function it_should_return_a_document_parser()
    {
        // Given

        // When
        $document = (new Document());

        // Then
        $this->assertInstanceOf(Document::class, $document);
    }

    /** @test */
    public function it_should_parse_content()
    {
        // Given
        $content = [
            'type' => 'doc',
            'version' => 1,
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'test',
                        ],
                    ],
                ],
            ],
        ];

        // When
        $document = (new Document($content));

        // Then
        $this->assertInstanceOf(Document::class, $document);
        $this->assertEquals($content, $document->toArray());
    }

    /** @test */
    public function it_should_parse_content_as_a_blocknode()
    {
        // Given
        $content = [
            'type' => 'doc',
            'version' => 1,
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'test',
                        ],
                    ],
                ],
            ],
        ];

        // When
        $blocknode = (new Document($content))->toBlockNode();

        // Then
        $this->assertInstanceOf(BlockNode::class, $blocknode);
        $this->assertIsArray($blocknode->getContent());
        $this->assertEquals((new BlockDocument)->paragraph()->text('test')->end(), $blocknode);
    }

    /** @test */
    public function it_should_filter_out_unknown_node_types()
    {
        // Given
        $content = [
            'type' => 'doc',
            'version' => 1,
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'lorem',
                        ],
                        [
                            'type' => 'mediaInline',
                            'attrs' => [
                                'id' => '12345',
                                'collection' => '',
                                'type' => 'file',
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => 'ipsum',
                        ],
                    ],
                ],
            ],
        ];

        // When
        $blocknode = (new Document($content))->toBlockNode();

        // Then
        $this->assertInstanceOf(BlockNode::class, $blocknode);
        $this->assertIsArray($blocknode->getContent());
        $this->assertEquals((new BlockDocument)->paragraph()->text('lorem')->text('ipsum')->end(), $blocknode);
    }
}
