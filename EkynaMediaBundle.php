<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle;

use Ekyna\Bundle\MediaBundle\DependencyInjection\Compiler\AdminMenuPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class EkynaMediaBundle
 * @package Ekyna\Bundle\MediaBundle
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaMediaBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AdminMenuPass());
    }
}
