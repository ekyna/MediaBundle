<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Show\Type;

use Ekyna\Bundle\AdminBundle\Show\Exception\UnexpectedTypeException;
use Ekyna\Bundle\AdminBundle\Show\Type\AbstractType;
use Ekyna\Bundle\AdminBundle\Show\View;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepositoryInterface;

use function is_numeric;

/**
 * Class MediaType
 * @package Ekyna\Bundle\MediaBundle\Show\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaType extends AbstractType
{
    private MediaRepositoryInterface $repository;


    /**
     * Constructor.
     *
     * @param MediaRepositoryInterface $repository
     */
    public function __construct(MediaRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function build(View $view, $value, array $options = []): void
    {
        if (is_numeric($value)) {
            $value = $this->repository->find((int)$value);
        }

        if ($value && !$value instanceof MediaInterface) {
            throw new UnexpectedTypeException($value, MediaInterface::class);
        }

        parent::build($view, $value, $options);
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'media_media';
    }
}
