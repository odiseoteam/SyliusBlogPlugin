<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Odiseo\SyliusBlogPlugin\Content\Media\ImageFilters;
use Odiseo\SyliusBlogPlugin\Content\Media\ImageUrlResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** A grid of uploaded images with a caption. Each one links to a large version of itself. */
final class GalleryNode extends AbstractContentNode implements MediaNodeInterface, TextNodeInterface
{
    public const COLUMNS = [2, 3, 4];

    public const MAX_IMAGES = 40;

    public function __construct(private readonly ImageUrlResolverInterface $urlResolver)
    {
    }

    public function getType(): string
    {
        return 'gallery';
    }

    public function getAttributes(): array
    {
        return ['images' => [], 'columns' => 3, 'caption' => ''];
    }

    public function getConstraints(): array
    {
        return [
            'images' => [
                new Assert\Type('array'),
                new Assert\Count(min: 1, max: self::MAX_IMAGES),
                new Assert\All(new Assert\Collection(
                    fields: [
                        'path' => [new Assert\NotBlank(), new Assert\Type('string'), new Assert\Regex(ImageFilters::PATH)],
                        'alt' => new Assert\Optional([new Assert\Type('string'), new Assert\Length(max: 255)]),
                        'width' => new Assert\Optional([new Assert\Type('integer'), new Assert\Positive()]),
                        'height' => new Assert\Optional([new Assert\Type('integer'), new Assert\Positive()]),
                    ],
                    allowExtraFields: true,
                )),
            ],
            'columns' => new Assert\Choice(choices: self::COLUMNS),
            'caption' => [new Assert\Type('string'), new Assert\Length(max: 500)],
        ];
    }

    public function getVariables(Node $node): array
    {
        $columns = $this->attribute($node, 'columns');

        return [
            'images' => array_map(fn (array $image): array => [
                'src' => $this->urlResolver->resolve($image['path'], ImageFilters::GALLERY),
                'href' => $this->urlResolver->resolve($image['path'], ImageFilters::forWidth($image['width'] ?? \PHP_INT_MAX)),
                'alt' => $image['alt'],
                'size' => ImageFilters::GALLERY_SIZE,
            ], $this->images($node)),
            'columns' => in_array($columns, self::COLUMNS, true) ? $columns : 3,
            'caption' => $this->caption($node),
        ];
    }

    public function toMarkdown(Node $node, array $children): string
    {
        $lines = array_map(fn (array $image): string => sprintf(
            '![%s](%s)',
            MarkdownLines::escapeInline($image['alt']),
            $this->urlResolver->resolve($image['path'], ImageFilters::forWidth(1280)),
        ), $this->images($node));
        if ([] === $lines) {
            return '';
        }

        $caption = $this->caption($node);

        return implode("\n", $lines) . ('' === $caption ? '' : "\n*" . MarkdownLines::escapeInline($caption) . '*');
    }

    public function toText(Node $node): string
    {
        return $this->caption($node);
    }

    public function getMediaPaths(Node $node): array
    {
        return array_map(static fn (array $image): string => $image['path'], $this->images($node));
    }

    /**
     * The images with a valid path; the rest are left out.
     *
     * @return list<array{path: string, alt: string, width: int|null}>
     */
    private function images(Node $node): array
    {
        $images = $this->attribute($node, 'images');
        if (!is_array($images)) {
            return [];
        }

        $valid = [];
        foreach ($images as $image) {
            $path = is_array($image) ? ($image['path'] ?? null) : null;
            if (!is_array($image) || !ImageFilters::isPath($path)) {
                continue;
            }

            $width = $image['width'] ?? null;
            $valid[] = [
                'path' => $path,
                'alt' => is_string($image['alt'] ?? null) ? trim($image['alt']) : '',
                'width' => is_int($width) && $width > 0 ? $width : null,
            ];
        }

        return $valid;
    }

    private function caption(Node $node): string
    {
        $caption = $this->attribute($node, 'caption');

        return is_string($caption) ? trim($caption) : '';
    }
}
