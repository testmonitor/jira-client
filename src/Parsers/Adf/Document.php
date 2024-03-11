<?php

namespace TestMonitor\Jira\Parsers\Adf;

use DH\Adf\Node\Node;
use DH\Adf\Node\BlockNode;
use DH\Adf\Node\Block\Document as AdfDocument;

class Document extends AdfDocument
{
    /**
     * Load array as document and filter out unsupported node types.
     *
     * @param array $data
     * @param null|\DH\Adf\Node\BlockNode $parent
     *
     * @return \DH\Adf\Node\BlockNode
     */
    public static function load(array $data, ?BlockNode $parent = null): BlockNode
    {
        return AdfDocument::load(
            static::filterUnsupportedNodeTypes($data), $parent
        );
    }

    /**
     * Removes unsupported node types from the content.
     *
     * @param array $node
     * @return array
     */
    protected static function filterUnsupportedNodeTypes(array $node): array
    {
        if (! static::nodeTypeIsSupported($node)) {
            return [];
        }

        if (array_key_exists('content', $node)) {
            foreach ($node['content'] as $key => $content) {
                $node['content'][$key] = static::filterUnsupportedNodeTypes($content);

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
    protected static function nodeTypeIsSupported(array $node): bool
    {
        return ! isset($node['type']) || in_array($node['type'], static::supportedNodeTypes());
    }

    /**
     * Returns a list of supported node types.
     *
     * @return array
     */
    protected static function supportedNodeTypes(): array
    {
        return array_keys(Node::NODE_MAPPING);
    }
}
