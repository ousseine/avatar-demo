<?php

namespace App\Controller;

use App\Form\AvatarFormType;
use App\Form\EditProfileFormType;
use App\Repository\UserRepository;
use App\Services\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    private UserRepository $userRepository;
    private FileUploader $fileUploader;

    public function __construct(UserRepository $userRepository, FileUploader $fileUploader)
    {
        $this->userRepository = $userRepository;
        $this->fileUploader = $fileUploader;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'Mon profile',
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/modifier-le-profile', name: 'app_profile_edit', methods: ['POST', 'GET'])]
    public function edit(Request $request): Response
    {
        $form = $this->createForm(EditProfileFormType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->add($this->getUser(), true);

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'Modifier le profile'
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/modifier-la-photo-de-profile', name: 'app_profile_edit_avatar')]
    public function editAvatar(Request $request): Response
    {
        $form = $this->createForm(AvatarFormType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileExist = $this->getParameter('avatar_directory') . '/' . $this->getUser()->getAvatar();
            $filename = $form->get('avatar')->getData();

            if ($filename) {
                // je supprime l'avatar qui existe
                if (file_exists($fileExist)) unlink($fileExist);

                $avatar = $this->fileUploader->upload($filename);
                $this->getUser()->setAvatar($avatar);
            }

            $this->userRepository->add($this->getUser(), true);

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/edit-avatar.html.twig', [
            'form' => $form,
            'controller_name' => 'Modifier la photo de profile'
        ]);
    }
}
