<?php

namespace Ekyna\Bundle\MediaBundle\Entity;

use DateTime;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;

/**
 * Class ConversionRequest
 * @package Ekyna\Bundle\MediaBundle\Entity
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ConversionRequest
{
    public const STATE_PENDING = 'pending';
    public const STATE_RUNNING = 'running';
    public const STATE_ERROR   = 'error';

    /**
     * @var int
     */
    private $id;

    /**
     * @var MediaInterface
     */
    private $media;

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $state;

    /**
     * @var DateTime
     */
    private $createdAt;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->state     = self::STATE_PENDING;
        $this->createdAt = new DateTime();
    }

    /**
     * Returns the id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the id.
     *
     * @param int $id
     *
     * @return ConversionRequest
     */
    public function setId(int $id): ConversionRequest
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Returns the media.
     *
     * @return MediaInterface
     */
    public function getMedia(): MediaInterface
    {
        return $this->media;
    }

    /**
     * Sets the media.
     *
     * @param MediaInterface $media
     *
     * @return ConversionRequest
     */
    public function setMedia(MediaInterface $media): ConversionRequest
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Returns the format.
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Sets the format.
     *
     * @param string $format
     *
     * @return ConversionRequest
     */
    public function setFormat(string $format): ConversionRequest
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Returns the state.
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Sets the state.
     *
     * @param string $state
     *
     * @return ConversionRequest
     */
    public function setState(string $state): ConversionRequest
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Returns the "created at" date.
     *
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Sets the "created at" date.
     *
     * @param DateTime $date
     *
     * @return ConversionRequest
     */
    public function setCreatedAt(DateTime $date): ConversionRequest
    {
        $this->createdAt = $date;

        return $this;
    }
}
