<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\Twig;

use Esites\KunstmaanExtrasBundle\Service\MediaService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MediaExtension extends AbstractExtension
{
    private MediaService $mediaService;

    public function __construct(
        MediaService $mediaService
    ) {
        $this->mediaService = $mediaService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'get_media_type',
                [
                    $this->mediaService,
                    'getMediaType',
                ]
            ),
        ];
    }
}
