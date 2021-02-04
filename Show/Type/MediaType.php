<?php

namespace Ekyna\Bundle\MediaBundle\Show\Type;

use Ekyna\Bundle\AdminBundle\Show\Exception\InvalidArgumentException;
use Ekyna\Bundle\AdminBundle\Show\Type\AbstractType;
use Ekyna\Bundle\AdminBundle\Show\View;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;

/**
 * Class MediaType
 * @package Ekyna\Bundle\MediaBundle\Show\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class MediaType extends AbstractType
{
    /**
     * @var MediaRepository
     */
    private $repository;


    /**
     * Constructor.
     *
     * @param MediaRepository $repository
     */
    public function __construct(MediaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function build(View $view, $value, array $options = [])
    {
        if (is_numeric($value)) {
            $value = $this->repository->find($value);
        }

        if ($value && !$value instanceof MediaInterface) {
            throw new InvalidArgumentException("Expected instance of " . MediaInterface::class);
        }

        parent::build($view, $value, $options);
    }

    /**
     * @inheritDoc
     */
    public function getWidgetPrefix()
    {
        return 'media';
    }
}
