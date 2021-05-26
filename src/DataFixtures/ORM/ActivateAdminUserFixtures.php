<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use FOS\UserBundle\Model\UserInterface;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\GeneratorBundle\DataFixtures\ORM\UserFixtures;
use Symfony\Component\Console\Output\ConsoleOutput;

class ActivateAdminUserFixtures extends AbstractFixture
{
	/**
	 * Activate the admin user
	 *
	 * @throws Exception
	 */
	public function load(ObjectManager $manager): void
	{
		$password = 'picobello';

		/** @var UserInterface|User $adminUser */
		$adminUser = $this->getReference(UserFixtures::REFERENCE_ADMIN_USER);
		if ($adminUser === null) {
			throw new \RuntimeException('Could not find user by username \'admin\'.');
		}

		$adminUser->setEnabled(true);
		$adminUser->setPlainPassword($password);
		$adminUser->setPasswordChanged(true);

		$manager->flush();

		$output = new ConsoleOutput();
		$output->writeln(["<comment>  > User 'admin' activated with password '$password'</comment>"]);
	}

	public function getOrder(): int
	{
		return 9999;
	}
}
