<?php

namespace TestMonitor\Jira\Parsers\Adf;

use DH\Adf\Node\Node;
use DH\Adf\Node\BlockNode;
use DH\Adf\Node\Block\Document as AdfDocument;

class Document
{
    /**
     * Document content.
     *
     * @var array
     */
    protected array $content;

    /**
     * Document constructor.
     *
     * @param array $content
     */
    public function __construct(array $content = [])
    {
        $this->content = $content;
    }

    /**
     * Returns the document content as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->content;
    }

    /**
     * Returns the document content as a blocknode.
     *
     * @throws \InvalidArgumentException
     *
     * @return null|\DH\Adf\Node\BlockNode
     */
    public function toBlockNode(): ?BlockNode
    {
        if (empty($this->content)) {
            return null;
        }

        return AdfDocument::load(
            $this->filterUnsupportedNodeTypes($this->content)
        );
    }

    /**
     * Removes unsupported node types from the content.
     *
     * @param array $node
     * @return array
     */
    protected function filterUnsupportedNodeTypes(array $node): array
    {
        if (! $this->nodeTypeIsSupported($node)) {
            return [];
        }

        if (array_key_exists('content', $node)) {
            foreach ($node['content'] as $key => $content) {
                $node['content'][$key] = $this->filterUnsupportedNodeTypes($content);

                // Discard empty content
                if (empty($node['content'][$key])) {
                    unset($node['content'][$key]);
                }
            }
        }

        return $node;
    }

    /**
     * Determines if the node type is supported.
     *
     * @param array $node
     * @return bool
     */
    protected function nodeTypeIsSupported(array $node): bool
    {
        return ! isset($node['type']) || in_array($node['type'], $this->supportedNodeTypes());
    }

    /**
     * Returns a list of supported node types.
     *
     * @return array
     */
    protected function supportedNodeTypes(): array
    {
        return array_keys(Node::NODE_MAPPING);
    }
}
