<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\CoreBundle\Model as Core;
use Ekyna\Bundle\MediaBundle\Model\ImageInterface;

/**
 * Class Image
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Image extends AbstractUploadable implements ImageInterface, Core\TaggedEntityInterface
{
    use Core\TaggedEntityTrait;

    /**
     * Id
     *
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $alt;


    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * {@inheritdoc}
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_media.image';
    }
}
