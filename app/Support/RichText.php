<?php

namespace App\Support;

use DOMDocument;
use DOMNode;

class RichText
{
    private const TAGS = ['p', 'br', 'strong', 'em', 'ul', 'ol', 'li', 'a'];

    public static function clean(string $html): string
    {
        $source = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $source->loadHTML('<?xml encoding="utf-8"?><div id="content">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        $target = new DOMDocument('1.0', 'UTF-8');
        $root = $target->createElement('div');
        $target->appendChild($root);
        $content = $source->getElementById('content');
        if ($content !== null) {
            foreach ($content->childNodes as $child) {
                self::copySafe($child, $root, $target);
            }
        }

        $result = '';
        foreach ($root->childNodes as $child) {
            $result .= $target->saveHTML($child);
        }

        return $result;
    }

    private static function copySafe(DOMNode $node, DOMNode $parent, DOMDocument $target): void
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            $parent->appendChild($target->createTextNode($node->textContent ?? ''));

            return;
        }

        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return;
        }

        $tag = strtolower($node->nodeName);
        if (in_array($tag, ['script', 'style', 'iframe', 'object', 'svg', 'math'], true)) {
            return;
        }

        $destination = $parent;
        if (in_array($tag, self::TAGS, true)) {
            $element = $target->createElement($tag);
            if ($tag === 'a' && $node->attributes !== null) {
                $href = trim($node->attributes->getNamedItem('href')->nodeValue ?? '');
                if (preg_match('~^(https?://|/(?!/))~i', $href) === 1) {
                    $element->setAttribute('href', $href);
                    $element->setAttribute('rel', 'nofollow noopener noreferrer');
                }
            }
            $parent->appendChild($element);
            $destination = $element;
        }

        foreach ($node->childNodes as $child) {
            self::copySafe($child, $destination, $target);
        }
    }
}
