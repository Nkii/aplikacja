<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController.
 *
 * @Route("/categories")
 */

class CategoryController extends AbstractController
{

    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request            HTTP request
     * @param \App\Repository\CategoryRepository        $categoryRepository Category repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator          Paginator
     *
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="category_index",
     * )
     */
    public function index(Request $request, CategoryRepository $categoryRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $categoryRepository->queryAll(),
            $request->query->getInt('page', 1),
            CategoryRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'category/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param \App\Entity\Category $category Category entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route (
     *     "/{id}",
     *     methods={"GET"},
     *     name="category_show",
     *     requirements={"id":"[1-9]\d*"}
     * )
     */
    public function show(Category $category): Response
    {
        return $this->render(
            'category/show.html.twig',
            ['category' => $category]

        );

    }

    /**
     * Create action.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route(
     *     "/create",
     *     methods={"GET", "POST"},
     *     name="category_create",
     * )
     */
    public function create(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $categoryRepository->save($category);

            $this->addFlash('success','message_created_successfully');
            return $this->redirectToRoute('category_index');

        }

        return $this->render(
            'category/create.html.twig',
            ['form'=> $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request
     * @param Category $category
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route(
     *     "/{id}/edit",
     *     name="category_edit",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function edit(Request $request, Category $category,CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category, ['method'=> 'PUT']);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $form->getData();
            $categoryRepository->save($category);

            $this->addFlash('success','message_updated_successfully');
            return $this->redirectToRoute('category_index');

        }
        return $this->render(
            'category/edit.html.twig',
            ['form'=> $form->createView(),
            'category'=>$category,
            ]);
    }

    /**
     *
     * @param Request $request
     * @param Category $category
     * @return Response
     *
     *  @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     name="category_delete",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function delete(Request $request, Category $category,CategoryRepository $categoryRepository): Response
    {
        if ($category->getQuestions()->count()){
            $this->addFlash('warning','message_category_contains_questions');

            return $this->redirectToRoute('category_index');
        }

        $form = $this->createForm(FormType::class, $category, ['method'=>'DELETE']);
        $form->handleRequest($request);

        if  ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if($form->isSubmitted() && $form->isValid()){
            $categoryRepository->delete($category);

            $this->addFlash('success','message_deleted_successfully');

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/delete.html.twig',
            [
                'form'=>$form->createView(),
                'category'=>$category,
            ]
        );
    }
}