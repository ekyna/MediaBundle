<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model;

/**
 * Interface MediaSubjectInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface MediaSubjectInterface
{
    public function setMedia(?MediaInterface $media): MediaSubjectInterface;

    public function getMedia(): ?MediaInterface;
}
