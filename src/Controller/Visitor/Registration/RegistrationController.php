<?php

namespace App\Controller\Visitor\Registration;

use App\Entity\User;
use App\Service\SendEmailService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'visitor.registration.register')]
    public function register(
        Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, TokenGeneratorInterface $tokenGenerator, SendEmailService $sendEmailService): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('visitor.welcome.index');
        }
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //generate deadline for email verification
            $deadline = (new \DatetimeImmutable('now'))->add(new \DateInterval('P1D'));
            $user->setDeadlineForEmailVerification($deadline);

            // generate token for email registration
            $tokenGenerate = $tokenGenerator->generateToken();
            $user->setTokenForEmailVerification($tokenGenerate);

            // encode the plain password
            $userPasswordHashed = $userPasswordHasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($userPasswordHashed);

            //Insert user table in the database
            $entityManager->persist($user);
            $entityManager->flush();

            // sending email
            $sendEmailService->send([
                "sender_email" => "medecine-du-monde@gmail.com",
                "sender_name" => "Jean Dupont",
                "recipient_email" => $user->getEmail(),
                "subject"=> "Verification de vote comptre sur le site medecine-du-monde.fr",
                "html_template" => "email/email_verification.html.twig",
                "context" => [
                    "user_id" => $user->getId(),
                    "token_for_email_verification" => $user -> getTokenForEmailVerification(),
                    "deadline_for_email_verification" => $user->getDeadlineForEmailVerification()->format('d/m/Y à H:i:s'),
                ]
            ]);


            return $this->redirectToRoute('visitor.registration.waiting_for_email_verification');
        }

        return $this->render('page/visitor/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/inscription/en-attente-de-la-verification-du-compte', name: 'visitor.registration.waiting_for_email_verification')]
    public function waitingForEmailVerification()
    {
        return $this->render('page/visitor/registration/waiting_for_email_verification.html.twig');
    }

    #[Route('/inscription/verification-du-compte/{id}/{token}', name: 'visitor.registration.email_verification')]
    public function emailVerification(User $user, $token, UserRepository $userRepository)
    {
        if ( ! $user) 
        {
            throw new AccessDeniedException();
        }

        if ( $user->getIsVerified() ) 
        {
            $this->addFlash('warning', "Votre compte est déjà vérifié!");
            return $this->redirectToRoute('visitor.welcome.index');
        }

        if ( empty($token) || ($user->getTokenForEmailVerification() == null) || ($token !== $user->getTokenForEmailVerification()) )
        {
            throw new AccessDeniedException();
        }

        if (new \DateTimeImmutable('now') > $user->getDeadlineForEmailVerification()) 
        {
            $deadline = $user->getDeadlineForEmailVerification();
            $userRepository->remove($user, true);

            throw new CustomUserMessageAccountStatusException("Votre délai de validation du compte a expiré le : $deadline");
        }

        $user->setIsVerified(true);
        $user->setTokenForEmailVerification('');
        $user->setVerifiedAt(new \DateTimeImmutable('now'));

        $userRepository->add($user, true);

        $this->addFlash("success", "Votre compte est activé, veuillez vous connecter!");
        return $this->redirectToRoute("visitor.welcome.index");
    }
}
