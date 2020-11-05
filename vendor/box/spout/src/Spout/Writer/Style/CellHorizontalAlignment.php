<?php

namespace Box\Spout\Writer\Style;

/**
 * =====================================
 * 좌우 정렬
 * Class Horizontal Alignment
 * This class provides constants to work with text alignment.
 * =====================================
 */
abstract class CellHorizontalAlignment
{
    const LEFT = 'left';
    const RIGHT = 'right';
    const CENTER = 'center';
    const JUSTIFY = 'justify';

    private static $VALID_ALIGNMENTS = [
        self::LEFT => 1,
        self::RIGHT => 1,
        self::CENTER => 1,
        self::JUSTIFY => 1,
    ];

    /**
     * @param string $cellHorizontalAlignment
     *
     * @return bool Whether the given cell horizontal alignment is valid
     */
    public static function isValid($cellHorizontalAlignment)
    {
        return isset(self::$VALID_ALIGNMENTS[$cellHorizontalAlignment]);
    }
}