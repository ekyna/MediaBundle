<?php

namespace Ekyna\Bundle\MediaBundle\Controller\Admin;

use Doctrine\DBAL\DBALException;
use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Ekyna\Bundle\CoreBundle\Modal\Modal;
use Ekyna\Bundle\MediaBundle\Form\Type\UploadType;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\Import\MediaImport;
use Ekyna\Bundle\MediaBundle\Model\Import\MediaUpload;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class BrowserController
 * @package Ekyna\Bundle\MediaBundle\Controller\Admin
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BrowserController extends Controller
{
    /**
     * Renders the manager modal
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $config = $this->buildConfig($request);

        return $this->render('@EkynaMedia/Manager/index.html.twig', ['config' => $config]);
    }

    /**
     * Renders the manager modal
     *
     * @param Request $request
     *
     * @return Response
     */
    public function modalAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $config = $this->buildConfig($request);

        $browser = $this->renderView('@EkynaMedia/Manager/render.html.twig', ['config' => $config]);

        $modal = new Modal();
        $modal->setTitle('ekyna_media.browser.title.' . $config['mode']);
        $modal->setContent($browser);

        if ($config['mode'] == 'multiple_selection') {
            $modal->setButtons([
                [
                    'id'       => 'submit',
                    'label'    => 'ekyna_core.button.validate',
                    'icon'     => 'glyphicon glyphicon-ok',
                    'cssClass' => 'btn-success',
                    'autospin' => true,
                ],
                [
                    'id'       => 'close',
                    'label'    => 'ekyna_core.button.close',
                    'icon'     => 'glyphicon glyphicon-remove',
                    'cssClass' => 'btn-default',
                ],
            ]);
        }

        return $this->get('ekyna_core.modal')->render($modal);
    }

    /**
     * Builds the browser config.
     *
     * @param Request $request
     *
     * @return array
     */
    private function buildConfig(Request $request)
    {
        $config = [
            'mode' => $request->query->get('mode', 'browse'),
        ];
        if (null !== $types = $request->query->get('types', [])) {
            // TODO validate types
            $config['types'] = $types;
        }

        return $config;
    }

    /**
     * Lists the children folders.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $root = $this->getFolderRepository()->findRoot();
        if (null !== $id = $request->query->get('folderId')) {
            if (!$this->activateFolderById($id, $root)) {
                $root->setActive(true);
            }
        } else {
            $root->setActive(true);
        }

        $response = new Response($this->serializeObject([$root]));
        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * Activate the folder by id.
     *
     * @param int             $id
     * @param FolderInterface $folder
     *
     * @return bool
     */
    private function activateFolderById($id, FolderInterface $folder)
    {
        foreach ($folder->getChildren() as $child) {
            if ($child->getId() == $id) {
                $child->setActive(true);

                return true;
            }
            if ($this->activateFolderById($id, $child)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Creates the folder.
     *
     * @param Request $request
     *
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
        if (!in_array($mode, ['child', 'after'])) {
            $response = new Response(json_encode([
                'error'   => true,
                'message' => 'Unexpected creation mode.',
            ], JSON_FORCE_OBJECT));
        } else {
            if ($mode === 'after') {
                $repo->persistAsNextSiblingOf($newFolder, $refFolder);
            } else {
                $repo->persistAsFirstChildOf($newFolder, $refFolder);
            }

            if (true !== $message = $this->validateFolder($newFolder)) {
                $response = new Response(json_encode([
                    'error'   => true,
                    'message' => $message,
                ], JSON_FORCE_OBJECT));
            } else {
                $this->getEntityManager()->flush();

                $response = new Response(sprintf('{"node":%s}', $this->serializeObject($newFolder)));
            }
        }

        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * Renames the folder.
     *
     * @param Request $request
     *
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
            $result = [
                'error'   => true,
                'message' => $message,
            ];
        } else {
            $this->persistFolder($folder);
            $result = [
                'name' => $folder->getName(),
            ];
        }

        $response = new Response(json_encode($result, JSON_FORCE_OBJECT));
        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * Deletes the folder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->get('id'));

        $result = [];
        try {
            $this->removeFolder($folder);
        } catch (DBALException $e) {
            $result = [
                'error'   => true,
                'message' => 'Ce dossier n\'est pas vide.',
            ];
        }

        $response = new Response(json_encode($result, JSON_FORCE_OBJECT));
        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * Moves the folder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function moveAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->get('id'));

        $result = [];
        $mode = $request->request->get('mode');

        if (!in_array($mode, ['before', 'after', 'over'])) {
            $result = [
                'error'   => true,
                'message' => 'Unexpected creation mode.',
            ];
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
        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * Lists the medias by folder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function listMediaAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->get('id'));

        $medias = $this
            ->get('ekyna_media.media.repository')
            ->findByFolderAndTypes($folder, (array)$request->query->get('types'));

        $data = ['medias' => $medias];

        $response = new Response(
            $this->get('serializer')->serialize($data, 'json', ['groups' => ['Manager']])
        );

        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * Moves the media to the folder.
     *
     * @param Request $request
     *
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
            $result = ['success' => true];
        } else {
            $media->setFolder($folder);
            $event = $this->get('ekyna_media.media.operator')->update($media);
            if (!$event->hasErrors()) {
                $result = ['success' => true];
            } else {
                $result = ['success' => false];
            }
        }

        $response = new Response(json_encode($result));
        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * Creates the media into the folder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createMediaAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folderId = $request->attributes->get('id');
        $folder = $this->findFolderById($folderId);

        $upload = new MediaUpload();

        $form = $this->createForm(UploadType::class, $upload, [
            'action' => $this->generateUrl(
                'ekyna_media_browser_admin_create_media',
                ['id' => $folderId]
            ),
            'method' => 'POST',
            'attr'   => [
                'class' => 'form-horizontal form-with-tabs',
            ],
            'folder' => $folder,
        ]);

        $success = false;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $success = true;
            // TODO use ResourceManager
            foreach ($upload->getMedias() as $media) {
                $event = $this->get('ekyna_media.media.operator')->create($media);
                if ($event->hasErrors()) {
                    $success = false;
                    break;
                }
            }
        }

        if ($success) {
            return new JsonResponse(['success' => true]);
        }

        $modal = $this->createModal();
        $modal->setTitle('ekyna_media.upload.title');
        $modal->setVars([
            'form_template' => '@EkynaMedia/Manager/upload.html.twig',
        ]);
        $modal->setContent($form->createView());

        return $this->get('ekyna_core.modal')->render($modal);
    }

    /**
     * Imports the media into the folder.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function importMediaAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

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
                // TODO use ResourceManager
                $operator = $this->get('ekyna_media.media.operator');
                foreach ($import->getMedias() as $media) {
                    $event = $operator->create($media);
                    // TODO better error handling
                    if ($event->isPropagationStopped()) {
                        $this->addFlash(sprintf('Failed to create "%s" media.', $media->getPath()), 'danger');
                    }
                }

                return new JsonResponse(['success' => true]);
            }
        }

        $modal = $this->createModal();
        $modal->setTitle('ekyna_media.import.title');
        $modal->setContent($form->createView());

        return $this->get('ekyna_core.modal')->render($modal);
    }

    /**
     * Creates a modal object.
     *
     * @return Modal
     */
    protected function createModal()
    {
        $modal = new Modal('ekyna_media.media.header.new');
        $modal->setButtons([
            [
                'id'       => 'submit',
                'label'    => 'ekyna_core.button.save',
                'icon'     => 'glyphicon glyphicon-ok',
                'cssClass' => 'btn-success',
                'autospin' => true,
            ],
            [
                'id'       => 'close',
                'label'    => 'ekyna_core.button.cancel',
                'icon'     => 'glyphicon glyphicon-remove',
                'cssClass' => 'btn-default',
            ],
        ]);
        $modal->setVars([
            'resource_name' => 'ekyna_media.media',
            'form_template' => '@EkynaAdmin/Entity/Default/_form.html.twig',
        ]);

        return $modal;
    }

    private function serializeObject($object)
    {
        return $this->get('serializer')->serialize($object, 'json', ['groups' => ['Manager']]);
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
     *
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
     *
     * @return FolderInterface
     */
    private function findFolderById($id)
    {
        /** @var FolderInterface $folder */
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
