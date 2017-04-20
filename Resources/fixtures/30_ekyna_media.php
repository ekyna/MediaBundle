<?php

declare(strict_types=1);

use Ekyna\Bundle\MediaBundle\Model;

$folders = [
    'folder_archive' => 'Archive',
    'folder_file'    => 'File',
    'folder_flash'   => 'Flash',
    'folder_image'   => 'Image',
    'folder_logo'    => 'Logo',
    'folder_video'   => 'Video',
];

$folders = array_map(
    function (string $name) {
        return [
            '__factory' => [
                '@ekyna_media.factory.folder::create' => [],
            ],
            'name'      => $name,
        ];
    },
    $folders
);

return [
    Model\FolderInterface::class => $folders,
];
