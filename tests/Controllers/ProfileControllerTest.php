<?php

namespace Controllers;

use App\Controllers\ProfileController;
use App\Models\ProfileSection;
use App\Repositories\ProfileRepositoryInterface;
use Framework\Request;
use Framework\Response;
use Framework\ResponseFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProfileControllerTest extends TestCase
{
    public function testIndexRendersProfileSections(): void
    {
        $repository = $this->createProfileRepositoryMock();

        $responseFactory = $this->createMock(ResponseFactory::class);

        $responseFactory
            ->expects($this->once())
            ->method('view')
            ->with(
                'profile/index.html.twig',
                $this->callback(function (array $context): bool {
                    return $context['aboutMe']->sectionKey === 'about_me'
                        && $context['programmingSkills']->sectionKey === 'programming_skills'
                        && $context['languages']->sectionKey === 'languages';
                })
            )
            ->willReturn(new Response('profile page'));

        $controller = new ProfileController($responseFactory, $repository);

        $response = $controller->index();

        $this->assertSame('profile page', $response->body);
    }

    public function testEditRendersProfileEditPage(): void
    {
        $repository = $this->createProfileRepositoryMock();

        $responseFactory = $this->createMock(ResponseFactory::class);

        $responseFactory
            ->expects($this->once())
            ->method('view')
            ->with(
                'profile/edit.html.twig',
                $this->callback(function (array $context): bool {
                    return $context['errors'] === []
                        && $context['aboutMe']->title === 'About me';
                })
            )
            ->willReturn(new Response('edit profile'));

        $controller = new ProfileController($responseFactory, $repository);

        $response = $controller->edit();

        $this->assertSame('edit profile', $response->body);
    }

    public function testUpdateReturnsErrorsWhenFieldsAreEmpty(): void
    {
        $repository = $this->createProfileRepositoryMock();

        $repository
            ->expects($this->never())
            ->method('update');

        $responseFactory = $this->createMock(ResponseFactory::class);

        $responseFactory
            ->expects($this->once())
            ->method('view')
            ->with(
                'profile/edit.html.twig',
                $this->callback(function (array $context): bool {
                    return count($context['errors']) === 6
                        && in_array('About me title is required.', $context['errors'], true)
                        && in_array('Languages content is required.', $context['errors'], true);
                })
            )
            ->willReturn(new Response('profile errors'));

        $controller = new ProfileController($responseFactory, $repository);

        $request = new Request('POST', '/profile/edit', [], []);

        $response = $controller->update($request);

        $this->assertSame('profile errors', $response->body);
    }

    public function testUpdateSavesSectionsAndRedirects(): void
    {
        $aboutMe = $this->createSection(1, 'about_me', 'About me', 'Old about');
        $programmingSkills = $this->createSection(2, 'programming_skills', 'Programming', 'Old skills');
        $languages = $this->createSection(3, 'languages', 'Languages', 'Old languages');

        $repository = $this->createMock(ProfileRepositoryInterface::class);

        $repository
            ->method('findBySectionKey')
            ->willReturnMap([
                ['about_me', $aboutMe],
                ['programming_skills', $programmingSkills],
                ['languages', $languages],
            ]);

        $repository
            ->expects($this->exactly(3))
            ->method('update')
            ->willReturnArgument(0);

        $responseFactory = $this->createMock(ResponseFactory::class);

        $responseFactory
            ->expects($this->once())
            ->method('redirect')
            ->with('/profile')
            ->willReturn(new Response('', 302, 'Location: /profile'));

        $controller = new ProfileController($responseFactory, $repository);

        $request = new Request('POST', '/profile/edit', [], [
            'about_me_title' => 'Updated About',
            'about_me_content' => 'Updated about content',
            'programming_skills_title' => 'Updated Skills',
            'programming_skills_content' => 'PHP, JS',
            'languages_title' => 'Updated Languages',
            'languages_content' => 'English, Ukrainian',
        ]);

        $response = $controller->update($request);

        $this->assertSame(302, $response->responseCode);

        $this->assertSame('Updated About', $aboutMe->title);
        $this->assertSame('Updated about content', $aboutMe->content);
        $this->assertSame('Updated Skills', $programmingSkills->title);
        $this->assertSame('PHP, JS', $programmingSkills->content);
        $this->assertSame('Updated Languages', $languages->title);
        $this->assertSame('English, Ukrainian', $languages->content);
    }

    public function testIndexThrowsExceptionWhenProfileSectionIsMissing(): void
    {
        $repository = $this->createMock(ProfileRepositoryInterface::class);

        $repository
            ->method('findBySectionKey')
            ->willReturn(null);

        $responseFactory = $this->createMock(ResponseFactory::class);

        $controller = new ProfileController($responseFactory, $repository);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Profile sections are missing from the database.');

        $controller->index();
    }

    /**
     * @return ProfileRepositoryInterface&MockObject
     */
    private function createProfileRepositoryMock(): ProfileRepositoryInterface
    {
        /** @var ProfileRepositoryInterface&MockObject $repository */
        $repository = $this->createMock(ProfileRepositoryInterface::class);

        $repository
            ->method('findBySectionKey')
            ->willReturnMap([
                ['about_me', $this->createSection(1, 'about_me', 'About me', 'About content')],
                ['programming_skills', $this->createSection(2, 'programming_skills', 'Programming', 'PHP')],
                ['languages', $this->createSection(3, 'languages', 'Languages', 'English')],
            ]);

        return $repository;
    }

    private function createSection(
        int $id,
        string $sectionKey,
        string $title,
        string $content
    ): ProfileSection {
        $section = new ProfileSection();

        $section->id = $id;
        $section->sectionKey = $sectionKey;
        $section->title = $title;
        $section->content = $content;
        $section->updatedAt = '2026-06-01 10:00:00';

        return $section;
    }
}
