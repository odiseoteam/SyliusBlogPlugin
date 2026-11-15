<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/** A YouTube (without cookies) or Vimeo video, saved as the URL the author pasted. */
final class VideoNode extends AbstractContentNode
{
    public function getType(): string
    {
        return 'video';
    }

    public function getAttributes(): array
    {
        return ['src' => null];
    }

    public function getConstraints(): array
    {
        return ['src' => [
            new Assert\Type('string'),
            new Assert\Callback(static function (mixed $value, ExecutionContextInterface $context): void {
                if (is_string($value) && null === self::embedUrl($value)) {
                    $context->buildViolation('odiseo_blog.content.video.unsupported')->addViolation();
                }
            }),
        ]];
    }

    public function getVariables(Node $node): array
    {
        $src = $this->attribute($node, 'src');

        return ['embed_url' => is_string($src) ? self::embedUrl($src) : null];
    }

    public function toMarkdown(Node $node, array $children): string
    {
        $src = $this->attribute($node, 'src');

        return is_string($src) && null !== self::embedUrl($src) ? '<' . $src . '>' : '';
    }

    /** The URL to embed, or null if the video is not from a supported site. */
    public static function embedUrl(string $url): ?string
    {
        if (1 === preg_match('~^https://(?:www\.)?youtube(?:-nocookie)?\.com/(?:watch\?(?:[^#]*&)?v=|embed/)([\w-]{11})(?:[&?#]|$)~', $url, $match) ||
            1 === preg_match('~^https://youtu\.be/([\w-]{11})(?:[?#]|$)~', $url, $match)) {
            return 'https://www.youtube-nocookie.com/embed/' . $match[1];
        }

        if (1 === preg_match('~^https://(?:www\.)?vimeo\.com/(\d+)(?:[/?#]|$)~', $url, $match) ||
            1 === preg_match('~^https://player\.vimeo\.com/video/(\d+)(?:[/?#]|$)~', $url, $match)) {
            return 'https://player.vimeo.com/video/' . $match[1];
        }

        return null;
    }
}
