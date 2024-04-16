<?php

namespace TestMonitor\Jira\Tests;

use DH\Adf\Node\BlockNode;
use PHPUnit\Framework\TestCase;
use DH\Adf\Node\Block\Document;

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
        $document = Document::load($content);

        // Then
        $this->assertInstanceOf(BlockNode::class, $document);
        $this->assertIsArray($document->getContent());
        $this->assertEquals((new Document)->paragraph()->text('test')->end(), $document);
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
        $document = Document::load($content);

        // Then
        $this->assertInstanceOf(BlockNode::class, $document);
        $this->assertIsArray($document->getContent());
        $this->assertEquals((new Document)->paragraph()->text('lorem')->text('ipsum')->end(), $document);
    }
}
