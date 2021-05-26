<?php

namespace Esites\KunstmaanExtrasBundle\Service;

class EmojiService
{
    public function removeEmojis(string $text): string
    {
        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';

        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';

        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';

        // Match flags (iOS)
        $regexFlags = '/[\x{1F1E0}-\x{1F1FF}]/u';

        $result = preg_replace([$regexEmoticons, $regexSymbols, $regexTransport, $regexFlags], '', $text);

        if (!is_string($result)) {
            return '';
        }

        return $result;
    }
}
