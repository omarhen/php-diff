<?php

declare(strict_types=1);

namespace Jfcherng\Diff\Renderer\Html\LineRenderer;

use Jfcherng\Utility\MbString;

class Line extends AbstractLineRenderer
{
    /**
     * {@inheritdoc}
     */
    public function render(MbString $mbFrom, MbString $mbTo): LineRendererInterface
    {
        [$start, $end] = $this->getChangedExtentBeginEnd($mbFrom, $mbTo);

        // two strings are the same
        if ($end === 0) {
            return $this;
        }

        // two strings are different, we do rendering
        $mbFrom->str_enclose_i(
            self::HTML_CLOSURES,
            $start,
            $end + $mbFrom->strlen() - $start + 1
        );
        $mbTo->str_enclose_i(
            self::HTML_CLOSURES,
            $start,
            $end + $mbTo->strlen() - $start + 1
        );

        return $this;
    }

    /**
     * Given two strings, determine where the changes in the two strings begin,
     * and where the changes in the two strings end.
     *
     * @param MbString $mbFrom the megabytes from line
     * @param MbString $mbTo   the megabytes to line
     *
     * @return array Array containing the starting position (non-negative) and the ending position (negative)
     *               [0, 0] if two strings are the same
     */
    protected function getChangedExtentBeginEnd(MbString $mbFrom, MbString $mbTo): array
    {
        // two strings are the same
        // most lines should be this cases, an early return could save many function calls
        if ($mbFrom->getRaw() === $mbTo->getRaw()) {
            return [0, 0];
        }

        // calculate $start
        $start = 0;
        $startLimit = \min($mbFrom->strlen(), $mbTo->strlen());
        while (
            $start < $startLimit && // index out of range
            $mbFrom->getAtRaw($start) === $mbTo->getAtRaw($start)
        ) {
            ++$start;
        }

        // calculate $end
        $end = -1; // trick
        $endLimit = $startLimit - $start;
        while (
            -$end <= $endLimit && // index out of range
            $mbFrom->getAtRaw($end) === $mbTo->getAtRaw($end)
        ) {
            --$end;
        }

        return [$start, $end];
    }
}
