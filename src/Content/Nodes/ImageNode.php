<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Odiseo\SyliusBlogPlugin\Content\Media\ImageFilters;
use Odiseo\SyliusBlogPlugin\Content\Media\ImageUrlResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/** An uploaded image, with its alt text, caption and size in the column of the text. */
final class ImageNode extends AbstractContentNode implements MediaNodeInterface, TextNodeInterface
{
    /** Share of the column of the text each size takes from tablets up; phones always get the whole width. */
    public const SIZES = ['small' => 50, 'medium' => 75, 'full' => 100];

    /** Where the sizes start to apply, as the `md` breakpoint of Bootstrap. */
    private const BREAKPOINT = 768;

    /** Widest the column of the text is assumed to be, for the `sizes` of the image. */
    private const COLUMN_WIDTH = 960;

    public function __construct(private readonly ImageUrlResolverInterface $urlResolver)
    {
    }

    public function getType(): string
    {
        return 'image';
    }

    public function getAttributes(): array
    {
        return ['path' => null, 'alt' => '', 'caption' => '', 'size' => 'full', 'width' => null, 'height' => null];
    }

    public function getConstraints(): array
    {
        return [
            'path' => [new Assert\NotBlank(), new Assert\Type('string'), new Assert\Regex(ImageFilters::PATH)],
            'alt' => [new Assert\Type('string'), new Assert\Length(max: 255)],
            'caption' => [new Assert\Type('string'), new Assert\Length(max: 500)],
            'size' => new Assert\Choice(choices: array_keys(self::SIZES)),
            'width' => [new Assert\Type('integer'), new Assert\Positive()],
            'height' => [new Assert\Type('integer'), new Assert\Positive()],
        ];
    }

    public function getVariables(Node $node): array
    {
        $path = $this->attribute($node, 'path');
        if (!ImageFilters::isPath($path)) {
            return ['image' => null];
        }

        $width = $this->dimension($node, 'width');
        $height = $this->dimension($node, 'height');
        $size = $this->attribute($node, 'size');
        $percent = self::SIZES[is_string($size) ? $size : 'full'] ?? 100;
        $columnWidth = intdiv(self::COLUMN_WIDTH * $percent, 100);

        return ['image' => [
            'src' => $this->urlResolver->resolve($path, ImageFilters::forWidth($columnWidth)),
            'srcset' => $this->srcset($path, $width),
            'sizes' => sprintf('(min-width: %dpx) %dpx, (min-width: %dpx) %dvw, 100vw', self::COLUMN_WIDTH, $columnWidth, self::BREAKPOINT, $percent),
            'columns' => intdiv(12 * $percent, 100),
            'alt' => $this->text($node, 'alt'),
            'caption' => $this->text($node, 'caption'),
            'width' => null !== $height ? $width : null,
            'height' => null !== $width ? $height : null,
        ]];
    }

    public function toMarkdown(Node $node, array $children): string
    {
        $path = $this->attribute($node, 'path');
        if (!ImageFilters::isPath($path)) {
            return '';
        }

        $markdown = sprintf(
            '![%s](%s)',
            MarkdownLines::escapeInline($this->text($node, 'alt')),
            $this->urlResolver->resolve($path, ImageFilters::forWidth(1280)),
        );
        $caption = $this->text($node, 'caption');

        return '' === $caption ? $markdown : $markdown . "\n*" . MarkdownLines::escapeInline($caption) . '*';
    }

    public function toText(Node $node): string
    {
        return $this->text($node, 'caption');
    }

    public function getMediaPaths(Node $node): array
    {
        $path = $this->attribute($node, 'path');

        return ImageFilters::isPath($path) ? [$path] : [];
    }

    /** One candidate per filter: those narrower than the image, and one at its own width. */
    private function srcset(string $path, ?int $width): string
    {
        $candidates = [];
        foreach (ImageFilters::CONTENT as $filterWidth => $filter) {
            $candidates[] = sprintf('%s %dw', $this->urlResolver->resolve($path, $filter), min($filterWidth, $width ?? $filterWidth));
            if (null !== $width && $filterWidth >= $width) {
                break;
            }
        }

        return implode(', ', $candidates);
    }

    private function dimension(Node $node, string $name): ?int
    {
        $value = $this->attribute($node, $name);

        return is_int($value) && $value > 0 ? $value : null;
    }

    private function text(Node $node, string $name): string
    {
        $value = $this->attribute($node, $name);

        return is_string($value) ? trim($value) : '';
    }
}
