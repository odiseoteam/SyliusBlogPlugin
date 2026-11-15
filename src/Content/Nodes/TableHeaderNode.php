<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

final class TableHeaderNode extends AbstractTableCellNode
{
    public function getType(): string
    {
        return 'tableHeader';
    }
}
