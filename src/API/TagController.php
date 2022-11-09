<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\API;

use App\Entity\Tag;
use App\Form\API\TagApiEditForm;
use App\Repository\TagRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Nelmio\ApiDocBundle\Annotation\Security as ApiSecurity;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/tags')]
#[Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")]
#[OA\Tag(name: 'Tag')]
final class TagController extends BaseApiController
{
    public const GROUPS_COLLECTION = ['Default', 'Collection', 'Tag'];
    public const GROUPS_ENTITY = ['Default', 'Entity', 'Tag'];
    public const GROUPS_FORM = ['Default', 'Entity', 'Tag'];

    public function __construct(private ViewHandlerInterface $viewHandler, private TagRepository $repository)
    {
    }

    /**
     * Fetch all existing tags
     */
    #[OA\Response(response: 200, description: 'Returns the collection of all existing tags as string array', content: new OA\JsonContent(type: 'array', items: new OA\Items(type: 'string')))]
    #[Rest\Get(name: 'get_tags')]
    #[ApiSecurity(name: 'apiUser')]
    #[ApiSecurity(name: 'apiToken')]
    #[Rest\QueryParam(name: 'name', strict: true, nullable: true, description: 'Search term to filter tag list')]
    public function cgetAction(ParamFetcherInterface $paramFetcher): Response
    {
        $filter = $paramFetcher->get('name');

        $data = $this->repository->findAllTagNames($filter);

        $view = new View($data, 200);
        $view->getContext()->setGroups(self::GROUPS_COLLECTION);

        return $this->viewHandler->handle($view);
    }

    /**
     * Creates a new tag
     */
    #[OA\Post(description: 'Creates a new tag and returns it afterwards', responses: [new OA\Response(response: 200, description: 'Returns the new created tag', content: new OA\JsonContent(ref: '#/components/schemas/TagEntity'))])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/TagEditForm'))]
    #[Rest\Post(name: 'post_tag')]
    #[ApiSecurity(name: 'apiUser')]
    #[ApiSecurity(name: 'apiToken')]
    public function postAction(Request $request): Response
    {
        if (!$this->isGranted('manage_tag') && !$this->isGranted('create_tag')) {
            throw new AccessDeniedHttpException('User cannot create tags');
        }

        $tag = new Tag();

        $form = $this->createForm(TagApiEditForm::class, $tag);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $this->repository->saveTag($tag);

            $view = new View($tag, 200);
            $view->getContext()->setGroups(self::GROUPS_ENTITY);

            return $this->viewHandler->handle($view);
        }

        $view = new View($form);
        $view->getContext()->setGroups(self::GROUPS_FORM);

        return $this->viewHandler->handle($view);
    }

    /**
     * Delete a tag
     */
    #[Security("is_granted('delete_tag')")]
    #[OA\Delete(responses: [new OA\Response(response: 204, description: 'HTTP code 204 for a successful delete')])]
    #[OA\Parameter(name: 'id', in: 'path', description: 'Tag ID to delete', required: true)]
    #[ApiSecurity(name: 'apiUser')]
    #[ApiSecurity(name: 'apiToken')]
    #[Rest\Delete(path: '/{id}', name: 'delete_tag')]
    public function deleteAction(Tag $tag): Response
    {
        $this->repository->deleteTag($tag);

        $view = new View(null, Response::HTTP_NO_CONTENT);

        return $this->viewHandler->handle($view);
    }
}
