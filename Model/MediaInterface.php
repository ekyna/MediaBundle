<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Model;

use Ekyna\Component\Resource\Model as RM;

/**
 * Interface MediaInterface
 * @package Ekyna\Bundle\MediaBundle\Model
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @method MediaTranslationInterface translate($locale = null, $create = false)
 * @method MediaTranslationInterface[] getTranslations()
 */
interface MediaInterface extends
    RM\UploadableInterface,
    RM\TranslatableInterface,
    RM\TaggedEntityInterface
{
    public function setFolder(?FolderInterface $folder): MediaInterface;

    public function getFolder(): ?FolderInterface;

    public function setType(?string $type): MediaInterface;

    public function getType(): ?string;

    public function setTitle(?string $title): MediaInterface;

    public function getTitle(): ?string;

    public function setDescription(?string $description): MediaInterface;

    public function getDescription(): ?string;
}
