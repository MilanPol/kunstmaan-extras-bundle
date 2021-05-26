<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LinkExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'is_link_internal',
                [
                    $this,
                    'isLinkInternal',
                ]
            ),
        ];
    }

    public function isLinkInternal(?string $link): bool
    {
        if (!is_string($link)) {
            return false;
        }

        if (preg_match('/\[NT\d+\]/', $link)) {
            return true;
        }

        return false;
    }
}
