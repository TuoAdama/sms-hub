<?php

namespace App\Controller\Admin;

use App\Repository\SmsMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/sms')]
//#[IsGranted('ROLE_ADMIN')]
class SmsAdminController extends AbstractController
{

    public function __construct(
        private readonly SmsMessageRepository $smsMessageRepository,
    )
    {
    }

    #[Route('/', name: 'app_sms_index')]
    public function index(): Response {
        $sms = $this->smsMessageRepository->findAll();
        return $this->render('pages/sms/sms-index.html.twig', [
            'sms' => $sms,
        ]);
    }
}
