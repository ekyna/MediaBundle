<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\MessageHandler;

use Ekyna\Bundle\MediaBundle\Message\ConvertVideo;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepositoryInterface;
use Ekyna\Bundle\MediaBundle\Service\VideoManager;
use Ekyna\Component\Resource\Exception\RuntimeException;

use function set_time_limit;

/**
 * Class ConvertVideoHandler
 * @package Ekyna\Bundle\MediaBundle\MessageHandler
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ConvertVideoHandler
{
    public function __construct(
        private readonly MediaRepositoryInterface $repository,
        private readonly VideoManager $converter,
    ) {
    }

    public function __invoke(ConvertVideo $message): void
    {
        $media = $this->repository->find($message->getId());

        if (null === $media) {
            throw new RuntimeException('Media (video) not found.');
        }

        set_time_limit(5 * 60);

        $this->converter->convertVideo($media, $message->getFormat(), $message->isOverride());
    }
}
