<?php

namespace Hysryt\Bookmark\Lib\Html;

use DOMDocument;
use DOMElement;
use DOMXPath;

class HtmlDocument implements HtmlDocumentInterface {
    private DOMXPath $xpath;

    public function __construct(string $html) {
        $doc = $this->createDOMDocument($html);
        $this->xpath = new DOMXPath($doc);
    }

    private function createDOMDocument(string $html) {
        $doc = new DOMDocument();
        if ($html) {
            $orig = libxml_use_internal_errors(true);
            $doc->loadHTML($html);
            libxml_use_internal_errors($orig);
        }
        return $doc;
    }

    public function parseTitle(): ?string {
        $elements = $this->queryElements('head/title');
        if (count($elements) > 0) {
            return $elements[0]->textContent;
        }
        return null;
    }

    public function parseDescription(): ?string {
        $elements = $this->queryElements('head/meta[@name="description"]');
        if (count($elements) > 0 && $elements[0]->hasAttribute('content')) {
            return $elements[0]->getAttribute('content');
        }
        return null;
    }

    public function parseOgp(): ?OpenGraphInterface {
        return OpenGraph::fromDOMXPath($this->xpath);
    }

    public function isIndexable(): bool {
        $elements = $this->queryElements('head/meta[@name="robots"]');
        if (count($elements) > 0) {
            return $this->isAllowIndexMetaElement($elements[0]);
        }
        return true;
    }

    private function isAllowIndexMetaElement($metaElement): bool {
        if ($metaElement->hasAttribute('content')) {
            $contents = explode(' ', $metaElement->getAttribute('content'));
            return !in_array('noindex', $contents) && !in_array('none', $contents);
        }

        return true;
    }

    private function queryElements(string $query): array {
        $nodes = $this->xpath->query($query);
        if ($nodes === false) {
            return [];
        }

        $elements = [];
        foreach($nodes as $node) {
            if ($node instanceof DOMElement) {
                $elements[] = $node;
            }
        }
        return $elements;
    }
}