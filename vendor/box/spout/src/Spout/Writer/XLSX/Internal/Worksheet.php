<?php

namespace Box\Spout\Writer\XLSX\Internal;

use Box\Spout\Common\Exception\InvalidArgumentException;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Helper\StringHelper;
use Box\Spout\Writer\Common\Helper\CellHelper;
use Box\Spout\Writer\Common\Internal\WorksheetInterface;

/**
 * Class Worksheet
 * Represents a worksheet within a XLSX file. The difference with the Sheet object is
 * that this class provides an interface to write data
 *
 * @package Box\Spout\Writer\XLSX\Internal
 */
class Worksheet implements WorksheetInterface
{
    /**
     * Maximum number of characters a cell can contain
     * @see https://support.office.com/en-us/article/Excel-specifications-and-limits-16c69c74-3d6a-4aaf-ba35-e6eb276e8eaa [Excel 2007]
     * @see https://support.office.com/en-us/article/Excel-specifications-and-limits-1672b34d-7043-467e-8e27-269d656771c3 [Excel 2010]
     * @see https://support.office.com/en-us/article/Excel-specifications-and-limits-ca36e2dc-1f09-4620-b726-67c00b05040f [Excel 2013/2016]
     */
    const MAX_CHARACTERS_PER_CELL = 32767;

    const SHEET_XML_FILE_HEADER = <<<EOD
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
EOD;

    /** @var \Box\Spout\Writer\Common\Sheet The "external" sheet */
    protected $externalSheet;

    /** @var string Path to the XML file that will contain the sheet data */
    protected $worksheetFilePath;

    /**
     * =============================================
     * 임시로 저장된 work Sheet 파일 경로
     * =============================================
     */
    protected $tempWorksheetFilePath;

    /** @var \Box\Spout\Writer\XLSX\Helper\SharedStringsHelper Helper to write shared strings */
    protected $sharedStringsHelper;

    /** @var \Box\Spout\Writer\XLSX\Helper\StyleHelper Helper to work with styles */
    protected $styleHelper;

    /** @var bool Whether inline or shared strings should be used */
    protected $shouldUseInlineStrings;

    /** @var \Box\Spout\Common\Escaper\XLSX Strings escaper */
    protected $stringsEscaper;

    /** @var \Box\Spout\Common\Helper\StringHelper String helper */
    protected $stringHelper;

    /** @var Resource Pointer to the sheet data file (e.g. xl/worksheets/sheet1.xml) */
    protected $sheetFilePointer;

    /** @var int Index of the last written row */
    protected $lastWrittenRowIndex = 0;

    /**
     * ===============================================================================
     * @var boolean $mergeDo : Merge 진행 여부 (For Function mergeCells)
     * ===============================================================================
     */
    protected $mergeDo = false;
    /**
     * ==========================================================================
     * @var array $mergeCells : Merge 처리할 `Cell`이 담긴 배열 (For Function mergeCells)
     * ==========================================================================
     */
    protected $mergeCells = array();

    /**
     * ===============================================================================
     * @var boolean $colDo : 넓이 설정 진행 여부 (For Function colWidths)
     * ===============================================================================
     */
    protected $colDo = false;
    /**
     * ==========================================================================
     * @var array $colWidths : 넓이 설정할 `Column`이 담 배열 (For Function colWidths)
     * ==========================================================================
     */
    protected $colWidths = array();

    /**
     * @param \Box\Spout\Writer\Common\Sheet $externalSheet The associated "external" sheet
     * @param string $worksheetFilesFolder Temporary folder where the files to create the XLSX will be stored
     * @param \Box\Spout\Writer\XLSX\Helper\SharedStringsHelper $sharedStringsHelper Helper for shared strings
     * @param \Box\Spout\Writer\XLSX\Helper\StyleHelper Helper to work with styles
     * @param bool $shouldUseInlineStrings Whether inline or shared strings should be used
     * @throws \Box\Spout\Common\Exception\IOException If the sheet data file cannot be opened for writing
     */
    public function __construct($externalSheet, $worksheetFilesFolder, $sharedStringsHelper, $styleHelper, $shouldUseInlineStrings)
    {
        $this->externalSheet = $externalSheet;
        $this->sharedStringsHelper = $sharedStringsHelper;
        $this->styleHelper = $styleHelper;
        $this->shouldUseInlineStrings = $shouldUseInlineStrings;

        /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
        $this->stringsEscaper = \Box\Spout\Common\Escaper\XLSX::getInstance();
        $this->stringHelper = new StringHelper();

        $this->worksheetFilePath = $worksheetFilesFolder . '/' . strtolower($this->externalSheet->getName()) . '.xml';
        /**
         * 임시로 저장될 worksheet(.xml) 파일 경로값 담기
         */
        $this->tempWorksheetFilePath = $worksheetFilesFolder . '/tmp_' . strtolower($this->externalSheet->getName()) . '.xml';
        $this->startSheet();
    }

    /**
     * Prepares the worksheet to accept data
     *
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the sheet data file cannot be opened for writing
     */
    protected function startSheet()
    {
        $this->sheetFilePointer = fopen($this->worksheetFilePath, 'w');
        $this->throwIfSheetFilePointerIsNotAvailable();

        fwrite($this->sheetFilePointer, self::SHEET_XML_FILE_HEADER);

        fwrite($this->sheetFilePointer, '<sheetData>');
    }

    /**
     * Checks if the book has been created. Throws an exception if not created yet.
     *
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the sheet data file cannot be opened for writing
     */
    protected function throwIfSheetFilePointerIsNotAvailable()
    {
        if (!$this->sheetFilePointer) {
            throw new IOException('Unable to open sheet for writing.');
        }
    }

    /**
     * @return \Box\Spout\Writer\Common\Sheet The "external" sheet
     */
    public function getExternalSheet()
    {
        return $this->externalSheet;
    }

    /**
     * @return int The index of the last written row
     */
    public function getLastWrittenRowIndex()
    {
        return $this->lastWrittenRowIndex;
    }

    /**
     * @return int The ID of the worksheet
     */
    public function getId()
    {
        // sheet index is zero-based, while ID is 1-based
        return $this->externalSheet->getIndex() + 1;
    }

    /**
     * ===================================================================================================
     * Style을 포함한 Row 추가 시, 세 번째 매개 변수에 배열 $custom 전달하여 `height` 설정 가능
     * ===================================================================================================
     *
     * Adds data to the worksheet.
     *
     * @param array $dataRow Array containing data to be written. Cannot be empty.
     *          Example $dataRow = ['data1', 1234, null, '', 'data5'];
     * @param \Box\Spout\Writer\Style\Style $style Style to be applied to the row. NULL means use default style.
     * @param array $custom : Key 값으로 'height' 명시하고 value 로 높이 지정
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the data cannot be written
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If a cell value's type is not supported
     */
    public function addRow($dataRow, $style, $custom)
    {
        if (!$this->isEmptyRow($dataRow)) {
            $this->addNonEmptyRow($dataRow, $style, $custom); // $custom 배열을 통해 높이($height) 값 전달
        }

        $this->lastWrittenRowIndex++;
    }

    /**
     * Returns whether the given row is empty
     *
     * @param array $dataRow Array containing data to be written. Cannot be empty.
     *          Example $dataRow = ['data1', 1234, null, '', 'data5'];
     * @return bool Whether the given row is empty
     */
    protected function isEmptyRow($dataRow)
    {
        $numCells = count($dataRow);
        // using "reset()" instead of "$dataRow[0]" because $dataRow can be an associative array
        return ($numCells === 1 && CellHelper::isEmpty(reset($dataRow)));
    }

    /**
     * ===================================================================================================
     * 비어있지 않은 Style을 포함한 Row 추가 시, 세 번째 매개 변수에 배열 $custom 전달하여 `height` 설정 가능
     * ===================================================================================================
     *
     * Adds non empty row to the worksheet.
     *
     * @param array $dataRow Array containing data to be written. Cannot be empty.
     *          Example $dataRow = ['data1', 1234, null, '', 'data5'];
     * @param \Box\Spout\Writer\Style\Style $style Style to be applied to the row. NULL means use default style.
     * @param array $custom : Key 값으로 'height' 명시하고 value 로 높이 지정
     * @return void
     * @throws \Box\Spout\Common\Exception\IOException If the data cannot be written
     * @throws \Box\Spout\Common\Exception\InvalidArgumentException If a cell value's type is not supported
     */
    protected function addNonEmptyRow($dataRow, $style, $custom)
    {
        $cellNumber = 0;
        $rowIndex = $this->lastWrittenRowIndex + 1;
        $numCells = count($dataRow);
         // $custom 배열을 통해 높이($height) 값 전달
        $customAttr = array();
        if (is_array($custom)) {
            foreach ($custom as $key => $row) {
                switch($key) {
                    case 'height':
                        $customAttr[] = 'ht="' . $row . '"';
                        $customAttr[] = 'customHeight="1"';
                        break;
                }
            }
        }
        $rowXML = '<row r="' . $rowIndex . '" spans="1:' . $numCells . '" ' . implode(' ', $customAttr) . '>';

        foreach($dataRow as $cellValue) {
            $rowXML .= $this->getCellXML($rowIndex, $cellNumber, $cellValue, $style->getId());
            $cellNumber++;
        }

        $rowXML .= '</row>';

        $wasWriteSuccessful = fwrite($this->sheetFilePointer, $rowXML);
        if ($wasWriteSuccessful === false) {
            throw new IOException("Unable to write data in {$this->worksheetFilePath}");
        }
    }

    /**
     * Build and return xml for a single cell.
     *
     * @param int $rowIndex
     * @param int $cellNumber
     * @param mixed $cellValue
     * @param int $styleId
     * @return string
     * @throws InvalidArgumentException If the given value cannot be processed
     */
    protected function getCellXML($rowIndex, $cellNumber, $cellValue, $styleId)
    {
        $columnIndex = CellHelper::getCellIndexFromColumnIndex($cellNumber);
        $cellXML = '<c r="' . $columnIndex . $rowIndex . '"';
        $cellXML .= ' s="' . $styleId . '"';

        if (CellHelper::isNonEmptyString($cellValue)) {
            $cellXML .= $this->getCellXMLFragmentForNonEmptyString($cellValue);
        } else if (CellHelper::isBoolean($cellValue)) {
            $cellXML .= ' t="b"><v>' . intval($cellValue) . '</v></c>';
        } else if (CellHelper::isNumeric($cellValue)) {
            $cellXML .= '><v>' . $cellValue . '</v></c>';
        } else if (empty($cellValue)) {
            if ($this->styleHelper->shouldApplyStyleOnEmptyCell($styleId)) {
                $cellXML .= '/>';
            } else {
                // don't write empty cells that do no need styling
                // NOTE: not appending to $cellXML is the right behavior!!
                $cellXML = '';
            }
        } else {
            throw new InvalidArgumentException('Trying to add a value with an unsupported type: ' . gettype($cellValue));
        }

        return $cellXML;
    }

    /**
     * Returns the XML fragment for a cell containing a non empty string
     *
     * @param string $cellValue The cell value
     * @return string The XML fragment representing the cell
     * @throws InvalidArgumentException If the string exceeds the maximum number of characters allowed per cell
     */
    protected function getCellXMLFragmentForNonEmptyString($cellValue)
    {
        if ($this->stringHelper->getStringLength($cellValue) > self::MAX_CHARACTERS_PER_CELL) {
            throw new InvalidArgumentException('Trying to add a value that exceeds the maximum number of characters allowed in a cell (32,767)');
        }

        if ($this->shouldUseInlineStrings) {
            $cellXMLFragment = ' t="inlineStr"><is><t>' . $this->stringsEscaper->escape($cellValue) . '</t></is></c>';
        } else {
            $sharedStringId = $this->sharedStringsHelper->writeString($cellValue);
            $cellXMLFragment = ' t="s"><v>' . $sharedStringId . '</v></c>';
        }

        return $cellXMLFragment;
    }

    /**
     * =================================================
     * cell | row --> Merge 기능
     *
     * @author 홍준성 <powerstar13@kai-i.com>
     * @param string $sheetName : 시트명
     * @param string $start : 시작 cell | row
     * @param string $end : 종료 cell | row
     * =================================================
     */
    public function mergeCells($sheetName = 'sheet1', $start = '', $end = '')
    {
        if($start !== '' && $end !== '') {
            $this->mergeDo = true;
            array_push($this->mergeCells,
                array(
                    $sheetName,
                    '<mergeCell ref="' . $start . ':' . $end . '"/>'
                )
            );
        }
    }

    /**
     * =================================
     * Cell 너비 설정 기능
     *
     * @param string $sheetName : 적용할 Sheet 이름
     * @param string $min : 적용 시작할 Cell (A1 --> 1)
     * @param string $max : 적용 종료할 Cell (D1 --> 4)
     * @param string $width : 적용할 Cell의 너비값
     * @return void
     * =================================
     */
    public function colWidths($sheetName = 'sheet1', $min = '', $max = '', $width = '')
    {
        if(!empty($width)) {
            $this->colDo = true;
            array_push($this->colWidths,
                array(
                    $sheetName,
                    '<col min="'.$min.'" max="'.$max.'" width="'.$width.'" customWidth="1"/>'
                )
            );
        }
    }

    /**
     * Closes the worksheet
     *
     * @return void
     */
    public function close()
    {
        if (!is_resource($this->sheetFilePointer)) {
            return;
        }

        fwrite($this->sheetFilePointer, '</sheetData>');

        /**
         * ===================================================
         * cell | row --> Merge 기능
         * @author 홍준성 <powerstar13@kai-i.com>
         * ===================================================
         */
        if($this->mergeDo) {
            $mergeCell = '';
            foreach($this->mergeCells AS $item) {
                if ($this->externalSheet->getName() === $item[0]) {
                    $mergeCell = $mergeCell . $item[1];
                }
            }
            if($mergeCell !== '') {
                fwrite($this->sheetFilePointer, '<mergeCells>' . $mergeCell . '</mergeCells>');
            }
        }

        fwrite($this->sheetFilePointer, '</worksheet>');

        /**
         * ===================================================
         * Cell 너비 설정 기능
         * ===================================================
         */
        if ($this->colDo) {
            $cosWidthCell = '';
            foreach($this->colWidths AS $item) {
                if ($this->externalSheet->getName() === $item[0]) {
                    $cosWidthCell = $cosWidthCell . $item[1];
                }
            }
            if($cosWidthCell !== '') {
                // temp 에 복사
                if(!copy($this->worksheetFilePath, $this->tempWorksheetFilePath)) {
                  fwrite ($this->sheetFilePointer, '카피실패');
                  exit;
                }
                // 현재까지의 진행 본 읽기
                $fp = fopen($this->tempWorksheetFilePath, "r") or die("file open fail");

                // 덮어쓸 경로 재 추가
                $this->sheetFilePointer = fopen($this->worksheetFilePath, "w");

                while(!feof($fp)){
                    $line = fgets($fp);
                    $line = str_replace('<sheetData>', '<cols>'.$cosWidthCell.'</cols><sheetData>', $line);
                    fwrite ($this->sheetFilePointer, $line);
                }
                fclose($fp);
                unlink($this->tempWorksheetFilePath);
            }
        }

        fclose($this->sheetFilePointer);
    }
}
