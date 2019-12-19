<?php

namespace App\Controller;

use App\Email\SenderToAdmin;
use App\Form\Model\Feedback;
use App\Form\Type\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedbackController extends AbstractController
{
    /**
     * @Route("/", name="app_feedback_send")
     * @param Request $request
     * @param SenderToAdmin $emailSender
     * @return Response
     */
    public function send(Request $request, SenderToAdmin $emailSender)
    {
        $feedbackForm = $this->createForm(FeedbackType::class);
        $feedbackForm->handleRequest($request);
        if ($feedbackForm->isSubmitted() && $feedbackForm->isValid()) {
            /** @var Feedback $feedback */
            $feedback = $feedbackForm->getData();
            $emailSender->sendFeedback($feedback);
            $this->addFlash('success', 'Your message as been sent successfuly.');

            return $this->redirectToRoute('app_feedback_send');
        }

        return $this->render('feedback/send.html.twig', [
            'feedbackForm' => $feedbackForm->createView(),
        ]);
    }
}
