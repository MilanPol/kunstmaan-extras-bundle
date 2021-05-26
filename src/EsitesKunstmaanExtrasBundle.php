<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle;

use Esites\KunstmaanExtrasBundle\DependencyInjection\EsitesKunstmaanExtrasExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EsitesKunstmaanExtrasBundle extends Bundle
{
	public function build(ContainerBuilder $container): void
	{
		parent::build($container);

		$container->registerExtension(new EsitesKunstmaanExtrasExtension());
	}
}