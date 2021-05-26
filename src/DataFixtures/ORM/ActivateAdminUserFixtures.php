<?php

declare(strict_types=1);

namespace Esites\KunstmaanExtrasBundle\DataFixtures\ORM;

use Doctrine\Persistence\ObjectManager;
use Exception;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\GeneratorBundle\DataFixtures\ORM\UserFixtures;
use RuntimeException;
use Symfony\Component\Console\Output\ConsoleOutput;

class ActivateAdminUserFixtures extends AbstractFixture
{
    private const DEFAULT_PASSWORD = 'picobello';

    /**
     * Activate the admin user
     *
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $adminUser = $this->getReference(UserFixtures::REFERENCE_ADMIN_USER);

        if (!$adminUser instanceof BaseUser) {
            throw new RuntimeException('Could not find user by username \'admin\'.');
        }

        $adminUser->setEnabled(true);
        $adminUser->setPlainPassword(static::DEFAULT_PASSWORD);
        $adminUser->setPasswordChanged(true);

        $manager->flush();

        $output = new ConsoleOutput();
        $output->writeln(
            ["<comment>  > User 'admin' activated with password '" . static::DEFAULT_PASSWORD . "'</comment>"]
        );
    }

    public function getOrder(): int
    {
        return 9999;
    }
}
