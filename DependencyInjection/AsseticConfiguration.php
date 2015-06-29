<?php

namespace Ekyna\Bundle\MediaBundle\DependencyInjection;

/**
 * Class AsseticConfiguration
 * @package Ekyna\Bundle\MediaBundle\DependencyInjection
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class AsseticConfiguration
{
    /**
     * Builds the assetic configuration.
     *
     * @param string $outputDir
     * @return array
     */
    public function build($outputDir)
    {
        // Fix output dir trailing slash
        if (strlen($outputDir) > 0 && '/' !== substr($outputDir, -1)) {
            $outputDir .= '/';
        }

        $output['fancytree_css'] = $this->buildFancyTreeCss($outputDir);
        $output['fancytree_js'] = $this->buildFancyTreeJs($outputDir);
        $output['media_thumb_js'] = $this->buildMediaThumbJs($outputDir);

        return $output;
    }

    /**
     * Builds the fancy tree css asset.
     *
     * @param string $outputDir
     * @return array
     */
    protected function buildFancyTreeCss($outputDir)
    {
        $inputs = array(
            '@EkynaMediaBundle/Resources/asset/css/fancytree/ui.fancytree.css',
        );

        return array(
            'inputs'  => $inputs,
            'filters' => array('yui_css'),
            'output'  => $outputDir . 'css/fancytree.css',
            'debug'   => false,
        );
    }

    /**
     * Builds the fancy tree js asset.
     *
     * @param string $outputDir
     * @return array
     */
    protected function buildFancyTreeJs($outputDir)
    {
        $inputs = array(
            '@EkynaMediaBundle/Resources/asset/js/jquery.ui-contextmenu.min.js',
            '@EkynaMediaBundle/Resources/asset/js/fancytree/jquery.fancytree.js',
            '@EkynaMediaBundle/Resources/asset/js/fancytree/jquery.fancytree.dnd.js',
            '@EkynaMediaBundle/Resources/asset/js/fancytree/jquery.fancytree.edit.js',
        );

        return array(
            'inputs'  => $inputs,
            'filters' => array('yui_js'),
            'output'  => $outputDir . 'js/fancytree.js',
            'debug'   => false,
        );
    }

    /**
     * Builds the media_browser_js asset.
     *
     * @param string $outputDir
     * @return array
     */
    protected function buildMediaThumbJs($outputDir)
    {
        $inputs = array(
            '@EkynaMediaBundle/Resources/views/thumb.html.twig',
        );
        return array(
            'inputs'  => $inputs,
            'filters' => array('twig_js'),
            'output'  => $outputDir . 'js/media-thumb.js',
            'debug'   => false,
        );
    }
}
