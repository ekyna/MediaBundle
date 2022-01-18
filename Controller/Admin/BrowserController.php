<?php

declare(strict_types=1);

namespace Ekyna\Bundle\MediaBundle\Controller\Admin;

use Ekyna\Bundle\MediaBundle\Factory\FolderFactoryInterface;
use Ekyna\Bundle\MediaBundle\Form\Type\MediaImportFlow;
use Ekyna\Bundle\MediaBundle\Form\Type\UploadType;
use Ekyna\Bundle\MediaBundle\Manager\FolderManagerInterface;
use Ekyna\Bundle\MediaBundle\Manager\MediaManagerInterface;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use Ekyna\Bundle\MediaBundle\Model\Import\MediaImport;
use Ekyna\Bundle\MediaBundle\Model\Import\MediaUpload;
use Ekyna\Bundle\MediaBundle\Repository\FolderRepositoryInterface;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepositoryInterface;
use Ekyna\Bundle\UiBundle\Model\Modal;
use Ekyna\Bundle\UiBundle\Service\Modal\ModalRenderer;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;
use Twig\Environment;

use function array_replace;

/**
 * Class BrowserController
 * @package Ekyna\Bundle\MediaBundle\Controller\Admin
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * TODO Split into multiple controllers or actions
 */
class BrowserController
{
    public const SESSION_FOLDER_ID = 'ekyna_media.folder_id';

    private FolderFactoryInterface    $folderFactory;
    private FolderRepositoryInterface $folderRepository;
    private FolderManagerInterface    $folderManager;
    private MediaRepositoryInterface  $mediaRepository;
    private MediaManagerInterface     $mediaManager;
    private ModalRenderer             $modal;
    private Environment               $twig;
    private ValidatorInterface        $validator;
    private Serializer                $serializer;
    private FormFactoryInterface      $formFactory;
    private UrlGeneratorInterface     $urlGenerator;
    private MediaImportFlow           $importFlow;

    public function __construct(
        FolderFactoryInterface    $folderFactory,
        FolderRepositoryInterface $folderRepository,
        FolderManagerInterface    $folderManager,
        MediaRepositoryInterface  $mediaRepository,
        MediaManagerInterface     $mediaManager,
        ModalRenderer             $modal,
        Environment               $twig,
        ValidatorInterface        $validator,
        Serializer                $serializer,
        FormFactoryInterface      $formFactory,
        UrlGeneratorInterface     $urlGenerator,
        MediaImportFlow           $importFlow
    ) {
        $this->folderFactory = $folderFactory;
        $this->folderRepository = $folderRepository;
        $this->folderManager = $folderManager;
        $this->mediaRepository = $mediaRepository;
        $this->mediaManager = $mediaManager;
        $this->modal = $modal;
        $this->twig = $twig;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->importFlow = $importFlow;
    }

    public function index(Request $request): Response
    {
        $config = $this->buildConfig($request);

        /** @noinspection PhpUnhandledExceptionInspection */
        $content = $this->twig->render('@EkynaMedia/Manager/index.html.twig', ['config' => $config]);

        return new Response($content);
    }

    public function modal(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $config = $this->buildConfig($request);

        /** @noinspection PhpUnhandledExceptionInspection */
        $browser = $this->twig->render('@EkynaMedia/Manager/render.html.twig', ['config' => $config]);

        $modal = new Modal();
        $modal
            ->setTitle('browser.title.' . $config['mode'])
            ->setDomain('EkynaMedia')
            ->setHtml($browser);

        if ($config['mode'] == 'multiple_selection') {
            $modal->setButtons([
                array_replace(Modal::BTN_SUBMIT, [
                    'label' => 'button.validate',
                ]),
                Modal::BTN_CLOSE,
            ]);
        }

        return $this->modal->render($modal);
    }

    /**
     * Lists the children folders.
     */
    public function list(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $root = $this->folderRepository->findRoot();
        if ($id = $this->getRequestFolderId($request)) {
            if (!$this->activateFolderById($id, $root)) {
                $root->setActive(true);
            }
        } else {
            $root->setActive(true);
        }

        $response = new Response($this->serializeData([$root]));
        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * Creates the folder.
     */
    public function create(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $refFolder = $this->findFolderById($request->attributes->getInt('id'));

        $newFolder = $this->folderFactory->create();
        $newFolder->setName('New folder');

        $mode = strtolower($request->request->get('mode'));
        if (!in_array($mode, ['child', 'after'])) {
            $response = new Response(json_encode([
                'error'   => true,
                'message' => 'Unexpected creation mode.',
            ], JSON_FORCE_OBJECT));
        } else {
            if ($mode === 'after') {
                $this->folderRepository->persistAsNextSiblingOf($newFolder, $refFolder);
            } else {
                $this->folderRepository->persistAsFirstChildOf($newFolder, $refFolder);
            }

            if (null !== $message = $this->validateFolder($newFolder)) {
                $response = new Response(json_encode([
                    'error'   => true,
                    'message' => $message,
                ], JSON_FORCE_OBJECT));
            } else {
                $this->folderManager->flush();

                $response = new Response(sprintf('{"node":%s}', $this->serializeData($newFolder)));
            }
        }

        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * Renames the folder.
     */
    public function rename(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->getInt('id'));

        $folder->setName($request->request->get('name'));

        if (null !== $message = $this->validateFolder($folder)) {
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
     */
    public function delete(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->getInt('id'));

        $result = [];
        try {
            $this->removeFolder($folder);
        } catch (Throwable $throwable) {
            $result = [
                'error'   => true,
                'message' => 'Impossible de supprimer ce dossier.',
            ];
        }

        $response = new Response(json_encode($result, JSON_FORCE_OBJECT));
        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * Moves the folder.
     */
    public function move(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->getInt('id'));

        $result = [];
        $mode = $request->request->get('mode');

        if (!in_array($mode, ['before', 'after', 'over'])) {
            $result = [
                'error'   => true,
                'message' => 'Unexpected creation mode.',
            ];
        } else {
            $reference = $this->findFolderById($request->request->getInt('reference'));

            if ($mode === 'before') {
                $this->folderRepository->persistAsPrevSiblingOf($folder, $reference);
            } elseif ($mode === 'after') {
                $this->folderRepository->persistAsNextSiblingOf($folder, $reference);
            } elseif ($mode === 'over') {
                $this->folderRepository->persistAsLastChildOf($folder, $reference);
            }
            $this->folderManager->flush();
        }

        $response = new Response(json_encode($result, JSON_FORCE_OBJECT));
        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * Lists the medias by folder.
     */
    public function listMedia(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->getInt('id'));
        $request->getSession()->set(self::SESSION_FOLDER_ID, $folder->getId());

        $medias = $this
            ->mediaRepository
            ->findByFolderAndTypes($folder, (array)$request->query->get('types'));

        $response = new Response($this->serializeData(['medias' => $medias]));

        $response->headers->add(['Content-Type' => 'application/json']);

        return $response;
    }

    /**
     * Moves the media to the folder.
     */
    public function moveMedia(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folder = $this->findFolderById($request->attributes->getInt('id'));

        $media = $this->mediaRepository->find($request->attributes->getInt('mediaId'));

        if (null === $media) {
            throw new NotFoundHttpException('Media not found.');
        }

        if ($folder === $media->getFolder()) {
            $result = ['success' => true];
        } else {
            $media->setFolder($folder);
            $event = $this->mediaManager->update($media);
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
     */
    public function createMedia(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folderId = $request->attributes->getInt('id');
        $folder = $this->findFolderById($folderId);

        $upload = new MediaUpload();

        $form = $this->formFactory->create(UploadType::class, $upload, [
            'action' => $this->urlGenerator->generate(
                'admin_ekyna_media_browser_create_media',
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
            foreach ($upload->getMedias() as $media) {
                $event = $this->mediaManager->create($media);
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
        $modal
            ->setTitle('upload.title')
            ->setDomain('EkynaMedia')
            ->setForm($form->createView())
            ->setVars([
                'form_template' => '@EkynaMedia/Manager/upload.html.twig',
            ]);

        return $this->modal->render($modal);
    }

    /**
     * Imports the media into the folder.
     */
    public function importMedia(Request $request): Response
    {
        throw new NotFoundHttpException('Broken code');

        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        $folderId = $request->attributes->getInt('id');
        $folder = $this->findFolderById($folderId);

        $import = new MediaImport($folder);

        $this->importFlow->bind($import);

        $form = $this->importFlow->createForm();
        if ($this->importFlow->isValid($form)) {
            $this->importFlow->saveCurrentStepData($form);

            if ($this->importFlow->nextStep()) {
                $form = $this->importFlow->createForm();
            } else {
                foreach ($import->getMedias() as $media) {
                    $event = $this->mediaManager->create($media);
                    // TODO better error handling
                    if ($event->isPropagationStopped()) {
                        /** @var Session $session */
                        $session = $request->getSession();
                        $session
                            ->getFlashBag()
                            ->add('danger', sprintf('Failed to create "%s" media.', $media->getPath()));
                    }
                }

                return new JsonResponse(['success' => true]);
            }
        }

        $modal = $this->createModal();
        $modal
            ->setTitle('import.title')
            ->setDomain('EkynaMedia')
            ->setForm($form->createView())
            ->setVars([
                'flow'          => $this->importFlow,
                'form_template' => '@EkynaMedia/Manager/import_flow.html.twig',
            ]);

        return $this->modal->render($modal);
    }

    protected function createModal(): Modal
    {
        $modal = new Modal('media.header.new');
        $modal->setDomain('EkynaMedia');
        $modal->setButtons([
            array_replace(Modal::BTN_SUBMIT, [
                'label' => 'button.save',
            ]),
            Modal::BTN_CLOSE,
        ]);
        $modal->setVars([
            'resource_name' => 'ekyna_media.media',
            'form_template' => '@EkynaAdmin/Entity/Crud/_form_default.html.twig',
        ]);

        return $modal;
    }

    /**
     * Builds the browser config.
     */
    private function buildConfig(Request $request): array
    {
        $config = [
            'folderId' => $this->getRequestFolderId($request),
            'mode'     => $request->query->get('mode', 'browse'),
        ];
        if (null !== $types = $request->query->get('types')) {
            // TODO validate types
            $config['types'] = $types;
        }

        return $config;
    }

    /**
     * Returns the request's folder id.
     */
    private function getRequestFolderId(Request $request): ?int
    {
        if (0 < $id = $request->query->getInt('folderId')) {
            $request->getSession()->set(self::SESSION_FOLDER_ID, $id);
        } else {
            $id = $request->getSession()->get(self::SESSION_FOLDER_ID);
        }

        return $id;
    }

    /**
     * Activate the folder by id.
     */
    private function activateFolderById(int $id, FolderInterface $folder): bool
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
     * Serializes the given data.
     *
     * @param object|array $data
     */
    private function serializeData($data): string
    {
        return $this->serializer->serialize($data, 'json', ['groups' => ['Manager']]);
    }

    /**
     * Persists the folder.
     */
    private function persistFolder(FolderInterface $folder): void
    {
        $this->folderManager->persist($folder);
        $this->folderManager->flush();
    }

    /**
     * Removes the folder.
     */
    private function removeFolder(FolderInterface $folder): void
    {
        $this->folderManager->remove($folder);
        $this->folderManager->flush();
    }

    /**
     * Validates the folder.
     */
    private function validateFolder(FolderInterface $folder): ?string
    {
        $errorList = $this->validator->validate($folder);
        if ($errorList->count()) {
            return $errorList->get(0)->getMessage();
        }

        return null;
    }

    /**
     * Returns the folder by id.
     */
    private function findFolderById(int $id): FolderInterface
    {
        $folder = $this->folderRepository->find($id);
        if (null === $folder) {
            throw new NotFoundHttpException('Folder not found.');
        }

        return $folder;
    }
}
