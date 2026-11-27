<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBlogPlugin\Integration\Controller;

use Odiseo\SyliusBlogPlugin\Controller\Admin\MediaUploadAction;
use Odiseo\SyliusBlogPlugin\Entity\ContentMedia;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Tests\Odiseo\SyliusBlogPlugin\Integration\DatabaseTestCase;

final class MediaUploadActionTest extends DatabaseTestCase
{
    private const FIXTURES = __DIR__ . '/../../../vendor/sylius/sylius/src/Sylius/Behat/Resources/fixtures/';

    /** @var list<string> */
    private array $stored = [];

    protected function setUp(): void
    {
        parent::setUp();

        // The token lives in the session of the request.
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        $requestStack = self::getContainer()->get('request_stack');
        self::assertInstanceOf(RequestStack::class, $requestStack);
        $requestStack->push($request);
    }

    protected function tearDown(): void
    {
        $uploader = self::getContainer()->get('sylius.uploader.image');
        self::assertInstanceOf(ImageUploaderInterface::class, $uploader);
        foreach ($this->stored as $path) {
            $uploader->remove($path);
        }

        parent::tearDown();
    }

    public function testItStoresTheImageAndAnswersWithItsPathAndSize(): void
    {
        $response = $this->upload($this->file('mugs.jpg'));

        self::assertSame(201, $response['status']);
        self::assertIsString($response['body']['path']);
        $this->stored[] = $response['body']['path'];
        self::assertMatchesRegularExpression('~^[\w-]+(?:/[\w-]+)*\.jpe?g$~', $response['body']['path']);
        self::assertGreaterThan(0, $response['body']['width']);
        self::assertGreaterThan(0, $response['body']['height']);

        $media = $this->entityManager->getRepository(ContentMedia::class)->findOneBy(['path' => $response['body']['path']]);
        self::assertInstanceOf(ContentMedia::class, $media);
        self::assertEqualsWithDelta(time(), $media->getCreatedAt()->getTimestamp(), 60);
    }

    public function testItNeedsTheTokenOfTheEditor(): void
    {
        $response = $this->upload($this->file('mugs.jpg'), 'forged');

        self::assertSame(403, $response['status']);
        self::assertIsString($response['body']['error']);
        self::assertSame(0, $this->entityManager->getRepository(ContentMedia::class)->count([]));
    }

    public function testItOnlyTakesImages(): void
    {
        $text = tempnam(sys_get_temp_dir(), 'odiseo_blog');
        file_put_contents((string) $text, 'Not an image');

        $response = $this->upload(new UploadedFile((string) $text, 'notes.jpg', null, null, true));

        self::assertSame(422, $response['status']);
        self::assertIsString($response['body']['error']);
        self::assertSame(0, $this->entityManager->getRepository(ContentMedia::class)->count([]));
    }

    public function testItNeedsAFile(): void
    {
        $response = $this->upload(null);

        self::assertSame(422, $response['status']);
        self::assertIsString($response['body']['error']);
    }

    private function file(string $name): UploadedFile
    {
        $copy = sys_get_temp_dir() . '/odiseo_blog_' . $name;
        copy(self::FIXTURES . $name, $copy);

        return new UploadedFile($copy, $name, null, null, true);
    }

    /** @return array{status: int, body: array<string, mixed>} */
    private function upload(?UploadedFile $file, ?string $token = null): array
    {
        $tokens = self::getContainer()->get('security.csrf.token_manager');
        self::assertInstanceOf(CsrfTokenManagerInterface::class, $tokens);
        $action = self::getContainer()->get('odiseo_blog.controller.admin.media_upload');
        self::assertInstanceOf(MediaUploadAction::class, $action);

        $request = new Request(request: ['_token' => $token ?? $tokens->getToken(MediaUploadAction::CSRF_TOKEN_ID)->getValue()], files: null === $file ? [] : ['file' => $file]);
        $response = $action($request);
        /** @var array<string, mixed> $body */
        $body = json_decode((string) $response->getContent(), true, flags: \JSON_THROW_ON_ERROR);

        return ['status' => $response->getStatusCode(), 'body' => $body];
    }
}
