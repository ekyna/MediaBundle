<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Message;

/**
 * Class ConvertVideo
 * @package Ekyna\Bundle\MediaBundle\Message
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ConvertVideo
{
    public function __construct(
        private readonly int $id,
        private readonly string $path,
        private readonly string $format,
        private readonly bool $override,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function isOverride(): bool
    {
        return $this->override;
    }
}
