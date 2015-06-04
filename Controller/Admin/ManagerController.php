<?php

namespace Ekyna\Bundle\MediaBundle\Controller\Admin;

use Doctrine\DBAL\DBALException;
use Ekyna\Bundle\CoreBundle\Controller\Controller;
use Ekyna\Bundle\MediaBundle\Entity\Folder;
use Ekyna\Bundle\MediaBundle\Model\FolderInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ManagerController
 * @package Ekyna\Bundle\MediaBundle\Controller\Admin
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class ManagerController extends Controller
{
    /**
     * Lists the children folders.
     *
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        $root = $this->findRootByRequest($request);

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
        $refFolder = $this->findFolderByRequest($request);

        $newFolder = new Folder();
        $newFolder->setName('New folder');

        $mode = strtolower($request->request->get('mode'));
        if (!in_array($mode, array('child', 'after'))) {
            $response = new Response(json_encode(array(
                'error'   => true,
                'message' => 'Unexpected creation mode.',
            ), JSON_FORCE_OBJECT));
        } else {
            if ($mode === 'after') {
                $this->getFolderRepository()->persistAsNextSiblingOf($newFolder, $refFolder);
            } else {
                $this->getFolderRepository()->persistAsFirstChildOf($newFolder, $refFolder);
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
        $folder = $this->findFolderByRequest($request);

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
        $folder = $this->findFolderByRequest($request);

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
        $folder = $this->findFolderByRequest($request);

        $result = array();
        $mode = $request->request->get('mode');

        if (!in_array($mode, array('before', 'after', 'over'))) {
            $result = array(
                'error'   => true,
                'message' => 'Unexpected creation mode.',
            );
        } else {
            $root = $this->findRootByRequest($request);
            $reference = $this->findFolderById($request->request->get('reference'), $root);

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
     * Finds the request folder.
     *
     * @param Request $request
     * @param FolderInterface $root
     * @return FolderInterface
     */
    private function findFolderByRequest(Request $request, FolderInterface $root = null)
    {
        if (null === $root) {
            $root = $this->findRootByRequest($request);
        }

        $folder = $this
            ->getFolderRepository()
            ->findOneBy(array(
                'id'   => $request->attributes->get('id'),
                'root' => $root->getId(),
            ))
        ;
        if (null === $folder) {
            throw new NotFoundHttpException('Folder not found.');
        }

        return $folder;
    }

    /**
     * Returns the folder by id.
     *
     * @param integer $id
     * @param FolderInterface $root
     * @return FolderInterface
     */
    private function findFolderById($id, FolderInterface $root)
    {
        $folder = $this
            ->getFolderRepository()
            ->findOneBy(array(
                'id'   => $id,
                'root' => $root->getId(),
            ))
        ;
        if (null === $folder) {
            throw new NotFoundHttpException('Folder not found.');
        }

        return $folder;
    }

    /**
     * Returns the root folder.
     *
     * @param Request $request
     * @return FolderInterface
     */
    private function findRootByRequest(Request $request)
    {
        $root = $this
            ->getFolderRepository()
            ->findRootByName($request->attributes->get('root'))
        ;
        if (null === $root) {
            throw new NotFoundHttpException('Root folder not found.');
        }

        return $root;
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
