<?php

namespace App\Controllers;

use App\Models\ProfileSection;
use App\Repositories\ProfileRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;

class ProfileController
{
    private ResponseFactory $responseFactory;

    private ProfileRepositoryInterface $profileRepository;

    public function __construct(
        ResponseFactory $responseFactory,
        ProfileRepositoryInterface $profileRepository
    ) {
        $this->responseFactory = $responseFactory;
        $this->profileRepository = $profileRepository;
    }

    public function index(): Response
    {
        $sections = $this->getProfileSections();

        return $this->responseFactory->view('profile/index.html.twig', $sections);
    }

    public function edit(): Response
    {
        $sections = $this->getProfileSections();

        return $this->responseFactory->view('profile/edit.html.twig', array_merge($sections, [
            'errors' => [],
        ]));
    }

    public function update(Request $request): Response
    {
        $sections = $this->getProfileSections();

        $errors = $this->validate($request);

        if (!empty($errors)) {
            return $this->responseFactory->view('profile/edit.html.twig', array_merge($sections, [
                'errors' => $errors,
            ]));
        }

        $aboutMe = $sections['aboutMe'];
        $aboutMe->title = trim($request->get('about_me_title') ?? '');
        $aboutMe->content = trim($request->get('about_me_content') ?? '');
        $aboutMe->updatedAt = date('Y-m-d H:i:s');

        $programmingSkills = $sections['programmingSkills'];
        $programmingSkills->title = trim($request->get('programming_skills_title') ?? '');
        $programmingSkills->content = trim($request->get('programming_skills_content') ?? '');
        $programmingSkills->updatedAt = date('Y-m-d H:i:s');

        $languages = $sections['languages'];
        $languages->title = trim($request->get('languages_title') ?? '');
        $languages->content = trim($request->get('languages_content') ?? '');
        $languages->updatedAt = date('Y-m-d H:i:s');

        $this->profileRepository->update($aboutMe);
        $this->profileRepository->update($programmingSkills);
        $this->profileRepository->update($languages);

        return $this->responseFactory->redirect('/profile');
    }

    /**
     * @return array{
     *     aboutMe: ProfileSection,
     *     programmingSkills: ProfileSection,
     *     languages: ProfileSection
     * }
     */
    private function getProfileSections(): array
    {
        $aboutMe = $this->profileRepository->findBySectionKey('about_me');
        $programmingSkills = $this->profileRepository->findBySectionKey('programming_skills');
        $languages = $this->profileRepository->findBySectionKey('languages');

        if ($aboutMe === null || $programmingSkills === null || $languages === null) {
            throw new \RuntimeException('Profile sections are missing from the database.');
        }

        return [
            'aboutMe' => $aboutMe,
            'programmingSkills' => $programmingSkills,
            'languages' => $languages,
        ];
    }

    /**
     * @return string[]
     */
    private function validate(Request $request): array
    {
        $errors = [];

        if (trim($request->get('about_me_title') ?? '') === '') {
            $errors[] = 'About me title is required.';
        }

        if (trim($request->get('about_me_content') ?? '') === '') {
            $errors[] = 'About me content is required.';
        }

        if (trim($request->get('programming_skills_title') ?? '') === '') {
            $errors[] = 'Programming skills title is required.';
        }

        if (trim($request->get('programming_skills_content') ?? '') === '') {
            $errors[] = 'Programming skills content is required.';
        }

        if (trim($request->get('languages_title') ?? '') === '') {
            $errors[] = 'Languages title is required.';
        }

        if (trim($request->get('languages_content') ?? '') === '') {
            $errors[] = 'Languages content is required.';
        }

        return $errors;
    }
}
