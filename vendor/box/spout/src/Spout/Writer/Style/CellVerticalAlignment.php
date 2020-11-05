<?php

namespace Box\Spout\Writer\Style;

/**
 * =====================================
 * 상하 정렬
 * Class Vertical Alignment
 * This class provides constants to work with text alignment.
 * =====================================
 */
abstract class CellVerticalAlignment
{
    const TOP = 'top';
    const CENTER = 'center';
    const BOTTOM = 'bottom';

    private static $VALID_ALIGNMENTS = [
        self::TOP => 1,
        self::CENTER => 1,
        self::BOTTOM => 1,
    ];

    /**
     * @param string $cellVerticalAlignment
     *
     * @return bool Whether the given cell vertical alignment is valid
     */
    public static function isValid($cellVerticalAlignment)
    {
        return isset(self::$VALID_ALIGNMENTS[$cellVerticalAlignment]);
    }
}