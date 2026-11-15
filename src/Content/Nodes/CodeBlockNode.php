<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

use Odiseo\SyliusBlogPlugin\Content\Document\Node;
use Symfony\Component\Validator\Constraints as Assert;

final class CodeBlockNode extends AbstractContentNode
{
    private const LANGUAGE = '/^[A-Za-z0-9_+#.-]{1,32}$/';

    public function getType(): string
    {
        return 'codeBlock';
    }

    public function getAttributes(): array
    {
        return ['language' => null];
    }

    public function getConstraints(): array
    {
        return ['language' => new Assert\Regex(pattern: self::LANGUAGE, message: 'odiseo_blog.content.code_block.invalid_language')];
    }

    public function getVariables(Node $node): array
    {
        return ['language' => $this->language($node)];
    }

    public function toMarkdown(Node $node, array $children): string
    {
        $code = implode('', array_map(static fn (Node $child): string => $child->text ?? '', $node->children));

        return sprintf("```%s\n%s\n```", $this->language($node), $code);
    }

    private function language(Node $node): ?string
    {
        $language = $this->attribute($node, 'language');

        return is_string($language) && 1 === preg_match(self::LANGUAGE, $language) ? $language : null;
    }
}
