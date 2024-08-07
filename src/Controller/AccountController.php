<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountType;
use App\Repository\BusinessPartnerRepository;
use App\Repository\AccountRepository;
use App\Service\PayoutManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/', name: 'app_account_list', methods: ['GET'])]
    public function list(
        Request $request,
        AccountRepository $accountRepository,
        BusinessPartnerRepository $businessPartnerRepository
    ): Response {
        $businessPartnerId = $request->query->get('businessPartnerId');

        $businessPartner = $businessPartnerId ? $businessPartnerRepository->find($businessPartnerId) : null;

        return $this->render('account/list.html.twig', [
            'businessPartner' => $businessPartner,
            'accounts' => $businessPartner
                ? $accountRepository->findByBusinessPartner($businessPartner)
                : $accountRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_account_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($account);
            $entityManager->flush();

            return $this->redirectToRoute('app_account_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/new.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_account_show', methods: ['GET'])]
    public function show(Account $account): Response
    {
        return $this->render('account/show.html.twig', [
            'account' => $account,
        ]);
    }

    #[Route('/{id}/execute', name: 'app_account_execute', methods: ['GET'])]
    public function execute(Request $request, Account $account, PayoutManager $payoutManager): Response
    {
        try {
            $payoutManager->execute($account);
        } catch (Exception $exception) {
            $request->getSession()->getFlashBag()->add('danger', $exception->getMessage());

            return $this->redirectToRoute('app_account_list', [], Response::HTTP_SEE_OTHER);
        }

        $request->getSession()->getFlashBag()->add('success', 'Account successfully executed.');

        return $this->redirectToRoute('app_account_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'app_account_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Account $account, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_account_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('account/edit.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_account_delete', methods: ['POST'])]
    public function delete(Request $request, Account $account, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$account->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($account);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_account_list', [], Response::HTTP_SEE_OTHER);
    }
}
