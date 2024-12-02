<?php

namespace App\Controller\Admin;

use App\Repository\SmsMessageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/sms')]
//#[IsGranted('ROLE_ADMIN')]
class SmsAdminController extends AbstractController
{

    public function __construct(
        private readonly SmsMessageRepository $smsMessageRepository,
        private readonly PaginatorInterface $paginator,
    )
    {
    }

    #[Route('/', name: 'app_sms_index')]
    public function index(Request $request): Response {

        $sms = $this->paginator->paginate(
            $this->smsMessageRepository->paginate(),
            $request->query->getInt('page', 1),
            10,
        );
        return $this->render('pages/sms/sms-index.html.twig', [
            'sms' => $sms,
        ]);
    }
}
