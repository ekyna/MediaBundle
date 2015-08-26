<?php

namespace Ekyna\Bundle\MediaBundle\Controller\Admin;

use Doctrine\DBAL\DBALException;
use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Ekyna\Bundle\CoreBundle\Modal\Modal;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\Import\MediaImport;
use Ekyna\Bundle\MediaBundle\Model\MediaTypes;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class BrowserController
 * @package Ekyna\Bundle\MediaBundle\Controller\Admin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class BrowserController extends Controller
{
    /**
     * Renders the manager modal
     *
     * @param Request $request
     * @return Response
     */
    public function modalAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $config = array();
        if ($request->query->has('types')) {
            $config['types'] = $request->query->get('types');
        }
        $browser = $this->renderView('EkynaMediaBundle:Manager:render.html.twig', array('config' => $config));

        $modal = new Modal();
        $modal->setTitle('ekyna_media.manager.title');
        $modal->setContent($browser);

        return $this->get('ekyna_core.modal')->render($modal);
    }

    /**
     * Lists the children folders.
     *
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $root = $this->getFolderRepository()->findRoot();

        $response = new Response($this->get('jms_serializer')->serialize(
            array($root),
            'json',
            SerializationContext::create()->setGroups(array('Manager'))
        ));

        $response->headers->add(array('Content-Type' => 'application/json'));

        return $response;
    }

    /**
     * Creates the folder.
     *
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $refFolder = $this->findFolderById($request->attributes->get('id'));
        $repo = $this->getFolderRepository();

        $newFolder = $repo->createNew();
        $newFolder->setName('New folder');

        $mode = strtolower($request->request->get('mode'));
        if (!in_array($mode, array('child', 'after'))) {
            $response = new Response(json_encode(array(
                'error'   => true,
                'message' => 'Unexpected creation mode.',
            ), JSON_FORCE_OBJECT));
        } else {
            if ($mode === 'after') {
                $repo->persistAsNextSiblingOf($newFolder, $refFolder);
            } else {
                $repo->persistAsFirstChildOf($newFolder, $refFolder);
            }

            if (true !== $message = $this->validateFolder($newFolder)) {
                $response = new Response(json_encode(array(
                    'error'   => true,
                    'message' => $message,
                ), JSON_FORCE_OBJECT));
            } else {
                $this->getEntityManager()->flush();
                $data = $this->get('jms_serializer')->serialize(
                    $newFolder,
                    'json',
                    SerializationContext::create()->setGroups(array('Manager'))
                );
                $response = new Response(sprintf('{"node":%s}', $data));
            }
        }

        $response->headers->add(array('Content-Type' => 'application/json'));
        return $response;
    }

    /**
     * Renames the folder.
     *
     * @param Request $request
     * @return Response
     */
    public function renameAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->get('id'));

        $folder->setName($request->request->get('name'));

        if (true !== $message = $this->validateFolder($folder)) {
            $result = array(
                'error'   => true,
                'message' => $message,
            );
        } else {
            $this->persistFolder($folder);
            $result = array(
                'name' => $folder->getName()
            );
        }

        $response = new Response(json_encode($result, JSON_FORCE_OBJECT));
        $response->headers->add(array('Content-Type' => 'application/json'));
        return $response;
    }

    /**
     * Deletes the folder.
     *
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->get('id'));

        $result = array();
        try {
            $this->removeFolder($folder);
        } catch(DBALException $e) {
            $result = array(
                'error'   => true,
                'message' => 'Ce dossier n\'est pas vide.',
            );
        }

        $response = new Response(json_encode($result, JSON_FORCE_OBJECT));
        $response->headers->add(array('Content-Type' => 'application/json'));
        return $response;
    }

    /**
     * Moves the folder.
     *
     * @param Request $request
     * @return Response
     */
    public function moveAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->get('id'));

        $result = array();
        $mode = $request->request->get('mode');

        if (!in_array($mode, array('before', 'after', 'over'))) {
            $result = array(
                'error'   => true,
                'message' => 'Unexpected creation mode.',
            );
        } else {
            $reference = $this->findFolderById($request->request->get('reference'));

            if ($mode === 'before') {
                $this->getFolderRepository()->persistAsPrevSiblingOf($folder, $reference);
            } elseif ($mode === 'after') {
                $this->getFolderRepository()->persistAsNextSiblingOf($folder, $reference);
            } elseif ($mode === 'over') {
                $this->getFolderRepository()->persistAsLastChildOf($folder, $reference);
            }
            $this->getEntityManager()->flush();
        }

        $response = new Response(json_encode($result, JSON_FORCE_OBJECT));
        $response->headers->add(array('Content-Type' => 'application/json'));
        return $response;
    }

    /**
     * Lists the medias by folder.
     *
     * @param Request $request
     * @return Response
     */
    public function listMediaAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->get('id'));

        $medias = $this
            ->get('ekyna_media.browser')
            ->setFolder($folder)
            ->findMedias((array) $request->query->get('types'))
        ;

        $data = array('medias' => $medias);

        $response = new Response($this->get('jms_serializer')->serialize(
            $data,
            'json',
            SerializationContext::create()->setGroups(array('Manager'))
        ));

        $response->headers->add(array('Content-Type' => 'application/json'));
        return $response;
    }

    /**
     * Moves the media to the folder.
     *
     * @param Request $request
     * @return Response
     */
    public function moveMediaAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->get('id'));

        /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
        $media = $this->get('ekyna_media.media.repository')->find($request->attributes->get('mediaId'));
        if (null === $media) {
            throw new NotFoundHttpException('Media not found.');
        }

        if ($folder === $media->getFolder()) {
            $result = array('success' => true);
        } else {
            $media->setFolder($folder);
            $event = $this->get('ekyna_media.media.operator')->update($media);
            if (!$event->hasErrors()) {
                $result = array('success' => true);
            } else {
                $result = array('success' => false);
            }
        }

        $response = new Response(json_encode($result));
        $response->headers->add(array('Content-Type' => 'application/json'));
        return $response;
    }

    /**
     * Creates the media into the folder.
     *
     * @param Request $request
     * @return Response
     */
    public function createMediaAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folderId = $request->attributes->get('id');
        $folder = $this->findFolderById($folderId);

        /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
        $media = $this->get('ekyna_media.media.repository')->createNew();
        $media->setFolder($folder);

        $form = $this->createForm('ekyna_media_media', $media, array(
            'action' => $this->generateUrl(
                'ekyna_media_browser_admin_create_media',
                array('id' => $folderId)
            ),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal form-with-tabs',
            ),
        ));

        $modal = $this->createModal();

        $form->handleRequest($request);
        if ($form->isValid()) {
            // TODO use ResourceManager
            $event = $this->get('ekyna_media.media.operator')->create($media);
            if (!$event->hasErrors()) {
                $modal->setContent(array('success' => true));
            }
        } else {
            $modal->setContent($form->createView());
        }

        return $this->get('ekyna_core.modal')->render($modal);
    }

    /**
     * Imports the media into the folder.
     *
     * @param Request $request
     * @return Response
     */
    public function importMediaAction(Request $request)
    {
        /*if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }*/

        $modal = $this->createModal();
        $modal->setTitle('ekyna_media.import.title');

        $folderId = $request->attributes->get('id');
        $folder = $this->findFolderById($folderId);

        $import = new MediaImport($folder);

        $flow = $this->get('ekyna_media.import_media.form_flow');
        $flow->bind($import);

        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                $form = $flow->createForm();
            } else {
                $operator     = $this->get('ekyna_media.media.operator');
                //$mountManager = $this->get('oneup_flysystem.mount_manager');
                //$uploader     = $this->get('ekyna_media.media.uploader');
                //$validator    = $this->get('validator');

                foreach ($import->getMedias() as $media) {
                    /*if (false !== $slashPos = strpos($media->getKey(), '/')) {
                        $filename = substr($media->getKey(), $slashPos + 1);
                    } else {
                        $filename = $media->getKey();
                    }
                    $path = $uploader->generatePath($filename);
                    $source = 'local_ftp://'.$media->getKey();
                    $target = 'local_media://'.$path;

                    $media
                        ->setPath($path)
                        ->setType(MediaTypes::guessByMimeType($mountManager->getMimetype($source)))
                        ->setKey(null)
                    ;

                    $validationErrors = $validator->validate($media);
                    if (0 < $validationErrors->count()) {
                        $this->addFlash('Invalid media.', 'danger');
                        continue;
                    }

                    if (!($mountManager->has($source) && $mountManager->move($source, $target))) {
                        $this->addFlash(sprintf('Failed to move "%s".', $filename), 'danger');
                        continue;
                    }*/

                    $event = $operator->create($media);
                    if ($event->isPropagationStopped()) {
                        $this->addFlash(sprintf('Failed to create "%s" media.', $media->getPath()), 'danger');
                    }
                }

                $modal->setContent(array('success' => true));
                return $this->get('ekyna_core.modal')->render($modal);
            }
        }

        $modal->setContent($form->createView());

        return $this->get('ekyna_core.modal')->render($modal);






        /*$form = $this->createForm('ekyna_media_media', $media, array(
            'action' => $this->generateUrl(
                'ekyna_media_browser_admin_import_media',
                array('id' => $folderId)
            ),
            'method' => 'POST',
            'attr' => array(
                'class' => 'form-horizontal form-with-tabs',
            ),
        ));


        $form->handleRequest($request);
        if ($form->isValid()) {
            // TODO use ResourceManager
            $event = $this->get('ekyna_media.media.operator')->create($media);
            if (!$event->hasErrors()) {
                $modal->setContent(array('success' => true));
            }
        } else {
            $modal->setContent($form->createView());
        }

        return $this->get('ekyna_core.modal')->render($modal);*/
    }

    /**
     * Creates a modal object.
     *
     * @return Modal
     */
    protected function createModal()
    {
        $modal = new Modal('ekyna_media.media.header.new');
        $modal->setButtons(array(
            array(
                'id'       => 'submit',
                'label'    => 'ekyna_core.button.save',
                'icon'     => 'glyphicon glyphicon-ok',
                'cssClass' => 'btn-success',
                'autospin' => true,
            ),
            array(
                'id' => 'close',
                'label' => 'ekyna_core.button.cancel',
                'icon' => 'glyphicon glyphicon-remove',
                'cssClass' => 'btn-default',
            )
        ));
        $modal->setVars(array(
            'resource_name' => 'ekyna_media.media',
            'form_template' => 'EkynaAdminBundle:Entity/Default:_form.html.twig',
        ));
        return $modal;
    }

    /**
     * Persists the folder.
     *
     * @param FolderInterface $folder
     */
    private function persistFolder(FolderInterface $folder)
    {
        $em = $this->getEntityManager();
        $em->persist($folder);
        $em->flush();
    }

    /**
     * Removes the folder.
     *
     * @param FolderInterface $folder
     */
    private function removeFolder(FolderInterface $folder)
    {
        $em = $this->getEntityManager();
        $em->remove($folder);
        $em->flush();
    }

    /**
     * Returns the entity manager.
     *
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->get('doctrine.orm.default_entity_manager');
    }

    /**
     * Validates the folder.
     *
     * @param FolderInterface $folder
     * @return true|string
     */
    private function validateFolder(FolderInterface $folder)
    {
        $errorList = $this->get('validator')->validate($folder);
        if ($errorList->count()) {
            return $errorList->get(0)->getMessage();
        }
        return true;
    }

    /**
     * Returns the folder by id.
     *
     * @param integer $id
     * @return FolderInterface
     */
    private function findFolderById($id)
    {
        $folder = $this->getFolderRepository()->find($id);
        if (null === $folder) {
            throw new NotFoundHttpException('Folder not found.');
        }
        return $folder;
    }

    /**
     * Returns the folder repository.
     *
     * @return \Ekyna\Bundle\MediaBundle\Entity\FolderRepository
     */
    private function getFolderRepository()
    {
        return $this->get('ekyna_media.folder.repository');
    }
}
