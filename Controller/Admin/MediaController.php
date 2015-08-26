<?php

namespace Ekyna\Bundle\MediaBundle\Controller\Admin;

use Ekyna\Bundle\AdminBundle\Controller\Resource\TinymceTrait;
use Ekyna\Bundle\AdminBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MediaController
 * @package Ekyna\Bundle\MediaBundle\Controller\Admin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaController extends ResourceController
{
    use TinymceTrait;

    public function listAction(Request $request)
    {
        $this->isGranted('VIEW');

        $context = $this->loadContext($request);

        $response = new Response();

        $format = 'html';
        if ($request->isXmlHttpRequest()) {
            $format = 'xml';
            $response->headers->add(array(
                'Content-Type' => 'application/xml; charset=' . strtolower($this->get('kernel')->getCharset())
            ));
        }

        $response->setContent($this->renderView(
            $this->config->getTemplate('list.' . $format),
            $context->getTemplateVars()
        ));

        return $response;
    }
}