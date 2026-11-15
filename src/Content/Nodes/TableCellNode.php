<?php

declare(strict_types=1);

namespace Odiseo\SyliusBlogPlugin\Content\Nodes;

final class TableCellNode extends AbstractTableCellNode
{
    public function getType(): string
    {
        return 'tableCell';
    }
}
