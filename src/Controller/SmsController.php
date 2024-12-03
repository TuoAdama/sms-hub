<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\SmsMessageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[Route('/sms')]
class SmsController extends AbstractController
{

    public function __construct(
        private readonly SmsMessageRepository $smsMessageRepository,
        private readonly PaginatorInterface $paginator
    )
    {
    }

    #[Route('/', name: 'sms_index')]
    public function index(#[CurrentUser] User $user, Request $request): Response
    {
        $sms = $this->paginator->paginate(
            $this->smsMessageRepository->findByUser($user),
            $request->query->getInt('page', 1),
            10,
        );
        return $this->render('pages/sms/sms-index.html.twig', [
            'sms' => $sms,
        ]);
    }
}
