<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Services\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Mailer\MailerInterface;
// use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{

    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerService $mailer)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            try {
                $to = $contactFormData['to'];
                $subject = 'Demande de contact sur votre site de ' . $contactFormData['email'];
                $content = $contactFormData['name'] . ' vous a envoyé le message suivant: ' . $contactFormData['message'];
                $mailer->sendEmail(subject: $subject, to: $to, content: $content);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'envoi du message');
                return $this->redirectToRoute('contact');
            }

            $this->addFlash('success', 'Votre message a été envoyé');
            return $this->redirectToRoute('home');
        }
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
