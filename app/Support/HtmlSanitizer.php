<?php

namespace App\Support;

class HtmlSanitizer
{
    /**
     * Summernote içeriklerini güvenli hale getir.
     * Script/style/iframe gibi tehlikeli etiketleri ve on* event attribute'larını temizler,
     * javascript: scheme'ini engeller. İzin verilen etiketler beyaz liste ile korunur.
     */
    public static function clean(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        // Önce DOM ile tehlikeli etiketleri içerikleriyle birlikte temizle (script/style içeriği strip_tags ile kalmasın)
        $prev = libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $wrapped = '<div>'.$html.'</div>';
        $doc->loadHTML('<?xml encoding="utf-8" ?>'.$wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);
        $xpath = new \DOMXPath($doc);
        foreach (['script', 'style', 'iframe', 'object', 'embed', 'form', 'link', 'meta'] as $tag) {
            foreach (iterator_to_array($xpath->query('//'.$tag)) as $node) {
                $node->parentNode->removeChild($node);
            }
        }
        $tmp = '';
        $wrapper = $doc->getElementsByTagName('div')->item(0);
        if ($wrapper) {
            foreach ($wrapper->childNodes as $child) {
                $tmp .= $doc->saveHTML($child);
            }
        }
        $html = $tmp;

        // İzin verilen etiketler (Summernote'un ürettiği yaygın etiketler)
        $allowed = '<p><br><strong><b><em><i><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><blockquote><code><pre><table><thead><tbody><tr><th><td><span><div><hr><sub><sup>';
        $html = strip_tags($html, $allowed);

        // DOM ile event attribute ve javascript: temizliği
        $prev = libxml_use_internal_errors(true);
        $doc = new \DOMDocument();

        // UTF-8 için wrapper
        $wrapped = '<div>'.$html.'</div>';
        $doc->loadHTML('<?xml encoding="utf-8" ?>'.$wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $xpath = new \DOMXPath($doc);

        // Tüm elemanlarda on* attribute ve javascript: href/src temizle
        foreach ($xpath->query('//*') as $el) {
            /** @var \DOMElement $el */
            $toRemove = [];
            foreach ($el->attributes ?? [] as $attr) {
                $name = strtolower($attr->nodeName);
                $value = $attr->nodeValue;

                if (str_starts_with($name, 'on')) {
                    $toRemove[] = $name;
                    continue;
                }

                if (in_array($name, ['href', 'src', 'xlink:href'], true)) {
                    $v = trim(strtolower($value));
                    if (str_starts_with($v, 'javascript:') || str_starts_with($v, 'data:text/html')) {
                        $toRemove[] = $name;
                    }
                }

                // style içinde javascript: / expression(
                if ($name === 'style' && preg_match('/javascript:|expression\s*\(/i', $value)) {
                    $toRemove[] = $name;
                }
            }
            foreach ($toRemove as $a) {
                $el->removeAttribute($a);
            }
        }

        // Wrapper div'in içini al
        $out = '';
        $wrapper = $doc->getElementsByTagName('div')->item(0);
        if ($wrapper) {
            foreach ($wrapper->childNodes as $child) {
                $out .= $doc->saveHTML($child);
            }
        }

        return $out;
    }
}
