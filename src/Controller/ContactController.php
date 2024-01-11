<?php

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\ContactMessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactMessageType::class, $contactMessage);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Сохранение данных в БД
            $entityManager->persist($contactMessage);
            $entityManager->flush();

            // Отправка email
//            $message = (new \Swift_Message('New Contact Message'))
//                ->setFrom($this->getParameter('mailer_from'))
//                ->setTo($this->getParameter('mailer_to'))
//                ->setBody(
//                    $this->renderView(
//                        'emails/contact_message.html.twig',
//                        ['message' => $contactMessage]
//                    ),
//                    'text/html'
//                );
//
//            $mailer->send($message);

            $this->addFlash('success', 'Your message has been sent successfully.');

            return $this->redirectToRoute('contact_index');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
