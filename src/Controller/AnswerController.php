<?php
/**
 * Answer controller.
 */

namespace App\Controller;

use App\Entity\Answer;
use App\Form\AnswerType;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use App\Repository\AnswerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AnswerController.
 *
 * @Route("/answer")
 */
class AnswerController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Repository\AnswerRepository            $answerRepository Answer repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator      Paginator
     *
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     name="answer_index",
     * )
     */
    public function index(Request $request, AnswerRepository $answerRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $answerRepository->queryAll(),
            $request->query->getInt('page', 1),
            AnswerRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'answer/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param \App\Entity\Answer $answer Answer entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     methods={"GET"},
     *     name="answer_show",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function show(Answer $answer): Response
    {
        return $this->render(
            'answer/show.html.twig',
            ['answer' => $answer]
        );
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     * @param AnswerRepository $answerRepository
     *
     * @return Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     * @Route(
     *     "/create",
     *     methods={"GET", "POST"},
     *     name="answer_create",
     * )
     */
    public function create(Request $request, AnswerRepository $answerRepository): Response
    {
        $answer= new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer->setDate(new \DateTime());
            $answer = $form->getData();
            $answerRepository->save($answer);

            $this->addFlash('success','message_created_successfully');
            return $this->redirectToRoute('answer_index');

        }

        return $this->render(
            'answer/create.html.twig',
            ['form'=> $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request
     * @param Answer $answer

     * @return Response
     *
     *  @Route(
     *     "/{id}/edit",
     *     name="answer_edit",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function edit(Request $request, Answer $answer,AnswerRepository $answerRepository): Response
    {
        $form = $this->createForm(AnswerType::class, $answer, ['method'=> 'PUT']);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $form->getData();
            $answerRepository->save($answer);

            $this->addFlash('success','message_updated_successfully');
            return $this->redirectToRoute('answer_index');

        }
        return $this->render(
            'answer/edit.html.twig',
            ['form'=> $form->createView(),
                'answer'=>$answer,
            ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request
     * @param Answer $answer
     * @param AnswerRepository $answerRepository
     * @return Response
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     name="answer_delete",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function delete(Request $request, Answer $answer,AnswerRepository $answerRepository): Response
    {
        $form = $this->createForm(FormType::class, $answer, ['method'=>'DELETE']);
        $form->handleRequest($request);

        if  ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if($form->isSubmitted() && $form->isValid()){
            $answerRepository->delete($answer);

            $this->addFlash('success','message_deleted_successfully');

            return $this->redirectToRoute('answer_index');
        }

        return $this->render(
            'answer/delete.html.twig',
            [
                'form'=>$form->createView(),
                'answer'=>$answer,
            ]
        );
    }
}