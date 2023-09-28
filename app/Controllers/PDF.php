<?php

namespace App\Controllers;

require_once(COMPOSER_PATH);

class PDF extends BaseController
{
    protected $objPDF;
    protected $filename;

    public function setFilename($filename){
        $this->filename = $filename;
    }

    public function getFilename(){
        return $this->filename;
    }

    public function creacion($titulo, $datos, $html = '', $hoja = 'A3', $mostrar_titulo = false)
    {
        $this->objPDF = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => $hoja]);

		$html = view('pdf/reporte', [
            'titulo' => $titulo,
            'mostrar_titulo' => $mostrar_titulo,
            'html' => $html,
            'datos' => $datos
        ]);

        // $stylesheet = file_get_contents('http://localhost/visualcont/public/assets/css/reporte.css');

        // $this->objPDF->WriteHTML($stylesheet, 1);
		$this->objPDF->WriteHTML($html);
        
        service('response')->setHeader("Content-Type", "application/pdf");
    }

    public function imprimir(){
        $this->objPDF->Output($this->getFilename() . '.pdf', 'I');
    }
}