<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\NumberVerification;
use App\Entity\User;
use App\Form\NumberFormType;
use App\Service\NumberVerificationService;
use App\Service\Token\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NumberController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenGenerator $tokenGenerator,
        private readonly NumberVerificationService $numberVerificationService,
    )
    {
    }

    /**
     * @throws RandomException
     */
    #[Route('/number/register', name: "app_number_register")]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function index(#[CurrentUser] User $user, Request $request): Response
    {
        $form = $this->createForm(NumberFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $countryCode = $form->get('countryCode')->getData();
            $number = $form->get('number')->getData();
            $user->setNumber("+".$countryCode.$number);

            $this->numberVerificationService->handleNumberVerification($user);

            return $this->render('pages/number/code_verification.html.twig', [
                'token' => $user->getNumberToken(),
            ]);
        }

        return $this->render('pages/number/number_register.html.twig', [
            'form' => $form,
        ]);
    }


    #[isGranted("IS_AUTHENTICATED_FULLY")]
    #[Route('/number/verify/{token}', name: "app_number_verify")]
    public function validateNumber(
        #[MapEntity(mapping: ['token' => 'token'])]
        User $user,
    ): Response
    {
        /** @var User $authUser */
        $authUser = $this->getUser();

        if ($user->getId() !== $authUser->getId()) {
            throw $this->createNotFoundException();
        }

        $payload = $this->tokenGenerator->decode($user->getNumberToken())['payload'];
        $expiredDate = $payload['iat'];
        if ($expiredDate > time()) {
            throw $this->createNotFoundException();
        }
        $user->setNumberVerified(true)
              ->setNumberToken(null)
              ->setNumberTemporalCode(null);

        $this->entityManager->flush();

        return $this->redirectToRoute('home');
    }
}
