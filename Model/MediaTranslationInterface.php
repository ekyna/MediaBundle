<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Component\Resource\Model\TranslationInterface;

/**
 * Interface MediaTranslationInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface MediaTranslationInterface extends TranslationInterface
{
    public function getTitle(): ?string;

    public function setTitle(?string $title): MediaTranslationInterface;

    public function getDescription(): ?string;

    public function setDescription(?string $description): MediaTranslationInterface;
}
