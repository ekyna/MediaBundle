<?php

declare(strict_types=1);

use Ekyna\Bundle\MediaBundle\Model;

$medias = [
    'media_archive_1'    => [
        'title'  => 'Archive 1',
        'folder' => '@folder_archive',
        'file'   => '<uploadMedia("archive/file.zip")>',
        'type'   => 'archive',
    ],
    'media_file_1'       => [
        'title'  => 'File 1',
        'folder' => '@folder_file',
        'file'   => '<uploadMedia("file/01.md")>',
        'type'   => 'file',
    ],
    'media_file_2'       => [
        'title'  => 'File 2',
        'folder' => '@folder_file',
        'file'   => '<uploadMedia("file/02.txt")>',
        'type'   => 'file',
    ],
    'media_flash_1'      => [
        'title'  => 'Flash 1',
        'folder' => '@folder_flash',
        'file'   => '<uploadMedia("flash/01.swf")>',
        'type'   => 'flash',
    ],
    'media_image_{1..6}' => [
        'title'  => 'Image <current()>',
        'folder' => '@folder_image',
        'file'   => '<uploadMedia("image/0%d.jpg", <current()>)>',
        'type'   => 'image',
    ],
    'media_logo_{1..6}'  => [
        'title'  => 'Logo <current()>',
        'folder' => '@folder_logo',
        'file'   => '<uploadMedia("logo/%d.jpg", <current()>)>',
        'type'   => 'image',
    ],
    'media_video_1'      => [
        'title'  => 'Video 1',
        'folder' => '@folder_video',
        'file'   => '<uploadMedia("video/01.mpg")>',
        'type'   => 'video',
    ],
    'media_video_2'      => [
        'title'  => 'Video 2',
        'folder' => '@folder_video',
        'file'   => '<uploadMedia("video/02.flv")>',
        'type'   => 'video',
    ],
    'media_video_3'      => [
        'title'  => 'Video 3',
        'folder' => '@folder_video',
        'file'   => '<uploadMedia("video/03.webm")>',
        'type'   => 'video',
    ],
];

$medias = array_map(
    function (array $media) {
        return array_replace(
            [
                '__factory' => [
                    '@ekyna_media.factory.media::create' => [],
                ],
            ],
            $media
        );
    },
    $medias
);

return [
    Model\MediaInterface::class  => $medias,
];
