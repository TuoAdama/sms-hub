<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\CodeVerificationType;
use App\Form\NumberFormType;
use App\Service\NumberService;
use App\Service\Token\TokenGenerator;
use App\Validator\UniqueNumber;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NumberController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenGenerator         $tokenGenerator,
        private readonly NumberService          $numberService,
        private readonly TranslatorInterface    $translator,
        private readonly ValidatorInterface     $validator,
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
            $phoneNumber = $this->numberService->formatNumber($form->get('phoneNumber')->getData());

            $errors = $this->validator->validate($phoneNumber, new UniqueNumber());

            if (count($errors) > 0) {
                $this->addFlash('danger', $this->translator->trans("number_already_exists"));
                return $this->render('pages/number/number_register.html.twig', [
                    'form' => $form,
                ]);
            }

            $user->setNumber($phoneNumber);
            $this->numberService->handleNumberVerification($user);

            return $this->redirectToRoute('app_number_verify', [
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
        Request $request,
        string $token,
    ): Response
    {

        $form = $this->createForm(CodeVerificationType::class, [
            'action' => $this->generateUrl('app_number_verify', ['token' => $token])
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $authUser */
            $authUser = $this->getUser();

            if ($authUser->getNumberToken() !== $token) {
                throw $this->createNotFoundException();
            }

            $payload = $this->tokenGenerator->decode($authUser->getNumberToken())['payload'];
            $expiredDate = $payload['iat'];
            if ($expiredDate < time()) {
                throw $this->createNotFoundException();
            }

            $code = $form->get('code')->getData();
            if (intval($code) !== $authUser->getNumberTemporalCode()){
                $this->addFlash('errors', $this->translator->trans("verification.number.invalid.message"));
                return $this->redirectToRoute('app_number_verify', ['token' => $token]);
            }

            $numberExists = count($this->validator->validate($authUser->getNumber(), new UniqueNumber())) !== 0;
            if ($numberExists) {
                $this->addFlash('errors', $this->translator->trans("number_already_exists"));
                return $this->redirectToRoute('app_number_verify', ['token' => $token]);
            }

            $authUser->setNumberVerified(true)
                ->setNumberToken(null)
                ->setNumberTemporalCode(null);

            $this->entityManager->flush();
            $this->addFlash('success', $this->translator->trans("verification.number.success"));
            return $this->redirectToRoute('home');
        }

        return $this->render('pages/number/code_verification.html.twig', [
            'form' => $form
        ]);
    }
}
