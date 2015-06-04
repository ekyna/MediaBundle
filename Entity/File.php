<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use Ekyna\Bundle\CoreBundle\Model as Core;
use Ekyna\Bundle\MediaBundle\Model\FileInterface;

/**
 * Class File
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class File extends AbstractUploadable implements FileInterface, Core\TaggedEntityInterface
{
    use Core\TaggedEntityTrait;

    /**
     * @var integer
     */
    protected $id;


    /**
     * Get id
     *
     * @return number
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public static function getEntityTagPrefix()
    {
        return 'ekyna_media.file';
    }
}
