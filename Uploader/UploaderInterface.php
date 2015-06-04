<?php

namespace Ekyna\Bundle\MediaBundle\Uploader;

use Ekyna\Bundle\MediaBundle\Model\UploadableInterface;

/**
 * Interface UploaderInterface
 * @package Ekyna\Bundle\MediaBundle\Uploader
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
interface UploaderInterface
{
    /**
     * Prepare the entity for upload.
     *
     * @param UploadableInterface $image
     */
    public function prepare(UploadableInterface $image);

    /**
     * Move the uploadable file.
     * 
     * @param UploadableInterface $image
     */
    public function upload(UploadableInterface $image);

    /**
     * Unlink the file.
     *  
     * @param UploadableInterface $image
     */
    public function remove(UploadableInterface $image);
}
