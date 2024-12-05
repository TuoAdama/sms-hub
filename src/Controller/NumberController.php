<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\NumberVerification;
use App\Entity\User;
use App\Form\NumberFormType;
use App\Service\Token\JWTServiceToken;
use App\Service\Token\TokenGenerator;
use App\Service\Token\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
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
        private readonly TokenService $tokenService,
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
            $this->handleNumberVerification($user);
            $this->entityManager->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render('pages/number/number_register.html.twig', [
            'form' => $form,
        ]);
    }


    /**
     * @throws RandomException
     */
    private function handleNumberVerification(User $user): void {
        $numberVerification = $user->getNumberVerification();
        if ($numberVerification !== null) {
            $this->entityManager->remove($numberVerification);
        }
        $verification = new NumberVerification();

        $verification->setUser($user)
            ->setVerified(false)
            ->setToken($this->tokenService->generateNumberVerificationToken($user))
            ->setTemporalCode(random_int(1000, 9999));

        $this->entityManager->persist($verification);
    }
}
