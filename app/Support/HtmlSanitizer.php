<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/** Allow-list HTML cleaner for editor content (no scripts, styles, event handlers or unsafe links). */
class HtmlSanitizer
{
    private const KEEP = ['p', 'br', 'strong', 'em', 'u', 'ul', 'ol', 'li', 'h2', 'h3', 'h4', 'blockquote', 'a', 'hr'];
    private const RENAME = ['b' => 'strong', 'i' => 'em', 'h1' => 'h2', 'h5' => 'h4', 'h6' => 'h4', 'div' => 'p'];
    private const DROP = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'svg', 'math', 'link', 'meta', 'noscript', 'template', 'input', 'button', 'textarea', 'select', 'img', 'video', 'audio'];

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        $doc = new DOMDocument('1.0', 'UTF-8');
        $prev = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?><div id="__root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $root = $doc->getElementById('__root');
        if (! $root) {
            return e(strip_tags($html));
        }

        self::walk($root, $doc);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        // Remove empty paragraphs the editor leaves behind.
        $out = preg_replace('#<p>(\s|&nbsp;|<br\s*/?>)*</p>#i', '', $out);

        return trim($out);
    }

    /** Visible text only (used for length checks). */
    public static function text(?string $html): string
    {
        $text = html_entity_decode(strip_tags(preg_replace('#</(p|li|h[1-6]|blockquote)>|<br\s*/?>#i', ' ', (string) $html)), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $text));
    }

    private static function walk(DOMNode $node, DOMDocument $doc): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if (! $child instanceof DOMElement) {
                if ($child->nodeType !== XML_TEXT_NODE) {
                    $node->removeChild($child); // comments, CDATA, processing instructions
                }
                continue;
            }

            $tag = strtolower($child->nodeName);

            if (in_array($tag, self::DROP, true)) {
                $node->removeChild($child);
                continue;
            }

            self::walk($child, $doc);

            $tag = self::RENAME[$tag] ?? $tag;

            if (! in_array($tag, self::KEEP, true)) {
                // Unknown tag (span, font, table...): keep its content, drop the tag.
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            if ($tag !== strtolower($child->nodeName)) {
                $new = $doc->createElement($tag);
                while ($child->firstChild) {
                    $new->appendChild($child->firstChild);
                }
                $node->replaceChild($new, $child);
                $child = $new;
            }

            $href = $tag === 'a' ? trim($child->getAttribute('href')) : '';
            while ($child->attributes->length) {
                $child->removeAttributeNode($child->attributes->item(0));
            }

            if ($tag === 'a') {
                if (preg_match('#^(https?://|mailto:|tel:)#i', $href)) {
                    $child->setAttribute('href', $href);
                    $child->setAttribute('target', '_blank');
                    $child->setAttribute('rel', 'noopener noreferrer');
                } else { // unsafe or missing link: keep the text only
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                }
            }
        }
    }
}
