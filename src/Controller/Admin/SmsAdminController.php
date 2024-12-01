<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/sms')]
class SmsAdminController extends AbstractController
{
    #[Route('/', name: 'app_sms_index')]
    public function index(): Response {
        return $this->render('pages/sms/sms-index.html.twig');
    }
}
