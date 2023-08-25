<?php

namespace Keepsuit\Liquid\Drops;

use Keepsuit\Liquid\Drop;

/**
 * @property int  $col          The 1-based index of the current column.
 * @property int  $col0         The 0-based index of the current column.
 * @property bool $col_first    Returns true if the current column is the first in the row. Returns false if not.
 * @property bool $col_last     Returns true if the current column is the last in the row. Returns false if not.
 * @property bool $first        Returns true if the current iteration is the first. Returns false if not.
 * @property int  $index        The 1-based index of the current iteration.
 * @property int  $index0       The 0-based index of the current iteration.
 * @property bool $last         Returns true if the current iteration is the last. Returns false if not.
 * @property int  $length       The total number of iterations in the loop.
 * @property int  $rindex       The 1-based index of the current iteration, in reverse order.
 * @property int  $rindex0      The 0-based index of the current iteration, in reverse order.
 * @property int  $row          The 1-based index of current row.
 */
class TableRowLoopDrop extends Drop
{
    protected int $row = 1;

    protected int $col = 1;

    protected int $index = 0;

    public function __construct(
        protected int $length,
        protected int $cols
    ) {
    }

    #[DropMethodPrivate]
    public function increment(): void
    {
        $this->index += 1;

        if ($this->col === $this->cols) {
            $this->col = 1;
            $this->row += 1;
        } else {
            $this->col += 1;
        }
    }

    public function liquidMethodMissing(string $name): mixed
    {
        return match ($name) {
            'col' => $this->col,
            'col0' => $this->col - 1,
            'col_first' => $this->col === 1,
            'col_last' => $this->col === $this->cols,
            'first' => $this->index === 0,
            'index' => $this->index + 1,
            'index0' => $this->index,
            'last' => $this->index === $this->length - 1,
            'length' => $this->length,
            'rindex' => $this->length - $this->index,
            'rindex0' => $this->length - $this->index - 1,
            'row' => $this->row,
            default => parent::liquidMethodMissing($name),
        };
    }
}
