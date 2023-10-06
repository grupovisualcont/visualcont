<?php

namespace App\Controllers;

require_once(COMPOSER_PATH);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel extends BaseController
{
    protected $objExcel;
    protected $objActSheet;
    protected $letras;
    protected $values;

    function __construct()
    {
        $this->objExcel = new Spreadsheet();

        $this->letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    }

    public function getLetra($index)
    {
        return $this->letras[$index];
    }

    public function creacion($titulo)
    {
        $objProps = $this->objExcel->getProperties();
        $objProps->setCreator("Jair Vasquez");
        $objProps->setLastModifiedBy("Jair Vasquez");
        $objProps->setTitle($titulo);

        $this->objExcel->setActiveSheetIndex(0);
        $this->objActSheet = $this->objExcel->getActiveSheet();
        $this->objActSheet->setTitle($titulo);
    }

    public function body($fila, $tipo)
    {
        for ($i = 0; $i < count($this->values); $i++) {
            if (is_array($this->values[$i])) {
                if (strpos($this->values[$i]['style'], 'bold') !== false) {
                    $this->setBold($this->letras[$i] . $fila);
                }

                if (strpos($this->values[$i]['style'], 'rigth') !== false) {
                    $this->setRigth($this->letras[$i] . $fila);
                }

                if (strpos($this->values[$i]['style'], 'left') !== false) {
                    $this->setLeft($this->letras[$i] . $fila);
                }

                $this->objActSheet->setCellValue($this->letras[$i] . $fila, $this->values[$i]['value']);
            } else {
                if (strpos($this->values[$i], ',') !== false) $this->objExcel->getActiveSheet()->getStyle($this->letras[$i] . $fila)->getAlignment()->setHorizontal('right');

                if ($tipo == 'columnas') {
                    $this->backgroundCell($this->letras[$i] . $fila);
                    $this->colorCell($this->letras[$i] . $fila);
                }

                $this->objActSheet->setCellValue($this->letras[$i] . $fila, $this->values[$i]);
            }

            $this->objExcel->getActiveSheet()->getColumnDimension($this->letras[$i])->setAutoSize(TRUE);
        }
    }

    public function footer($archivo)
    {
        $writer = new Xlsx($this->objExcel);

        ob_start();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . urlencode($archivo));
        header('Cache-Control: max-age=0');

        ob_end_clean();

        $writer->save('php://output');

        exit();
    }

    public function setValues($values)
    {
        $this->values = $values;
    }

    public function setCelda($celda, $valor)
    {
        $this->objExcel->getActiveSheet()->setCellValue($celda, $valor);
        $this->objExcel->getActiveSheet()->getColumnDimension(str_replace(range(0, 9), '', $celda))->setAutoSize(TRUE);
    }

    public function combinarCelda($celda)
    {
        $this->objExcel->getActiveSheet()->getStyle($celda)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $this->objExcel->getActiveSheet()->mergeCells($celda);
    }

    public function setFontSize($celda, $size)
    {
        $this->objExcel->getActiveSheet()->getStyle($celda)->getFont()->setSize($size);
    }

    public function setBold($celda)
    {
        $this->objExcel->getActiveSheet()->getStyle($celda)->getFont()->setBold(true);
    }

    public function setRigth($celda)
    {
        $this->objExcel->getActiveSheet()->getStyle($celda)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    }

    public function setLeft($celda)
    {
        $this->objExcel->getActiveSheet()->getStyle($celda)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    }

    public function backgroundCell($cells, $RGB = '4e73df')
    {
        $this->objExcel->getActiveSheet()->getStyle($cells)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($RGB);
    }

    public function colorCell($cells)
    {
        $this->objExcel->getActiveSheet()->getStyle($cells)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
    }
}
