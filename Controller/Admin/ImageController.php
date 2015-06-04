<?php

namespace Ekyna\Bundle\MediaBundle\Controller\Admin;

use Ekyna\Bundle\AdminBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ImageController
 * @package Ekyna\Bundle\MediaBundle\Controller\Admin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ImageController extends ResourceController
{
    public function listAction(Request $request)
    {
        $this->isGranted('VIEW');

        $context = $this->loadContext($request);

        /*$table = $this->getTableFactory()
            ->createBuilder($this->config->getTableType(), array(
                'name' => $this->config->getId(),
                'selector' => (bool)$request->get('selector', false), // TODO use constants (single/multiple)
                'multiple' => (bool)$request->get('multiple', false),
            ))
            ->getTable($request);*/

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
            $context->getTemplateVars(array(
                //$this->config->getResourceName(true) => $table->createView()
            ))
        ));

        return $response;
    }
}