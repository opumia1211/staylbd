<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Reject text that contains URLs/links to prevent phishing, spam and malicious links
 * in user-generated content (e.g. review title, review comment).
 */
class NoLinksInText implements Rule
{
    protected string $attributeLabel = 'field';

    public function __construct(string $attributeLabel = 'field')
    {
        $this->attributeLabel = $attributeLabel;
    }

    /**
     * Detect URLs and link-like patterns (http, https, www., .com/.net/.org etc).
     */
    public function passes($attribute, $value): bool
    {
        if (!is_string($value) || trim($value) === '') {
            return true;
        }
        $text = $value;

        // Explicit URLs
        if (preg_match('#https?://\S+#i', $text)) {
            return false;
        }
        // www. something
        if (preg_match('#\bwww\.\S+#i', $text)) {
            return false;
        }
        // Common TLDs used in links (avoid false positives: allow "I like .com" by requiring word before .tld)
        if (preg_match('#[a-z0-9\-]\.(com|net|org|io|co|bd|tk|ml|ga|cf|gq|xyz|link|click|top|site|online|store|shop|php|exe|bat)\b#i', $text)) {
            return false;
        }
        // [text](url) or [url]
        if (preg_match('#\[([^\]]*)\]\(?\s*[hH]?#', $text) || preg_match('#\]\s*\(#', $text)) {
            return false;
        }
        // href= or url= style
        if (preg_match('#(href|url|src)\s*=\s*["\']?\s*[a-z]*:?#i', $text)) {
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return __('Links and URLs are not allowed in :attribute. Do not paste website links or phishing content.', ['attribute' => $this->attributeLabel]);
    }
}
