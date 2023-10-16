<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Parametro extends BaseController
{
    protected $parametro = array(
        array('id' => 'AFECTO', 'text' => 'AFECTO'),
        array('id' => 'ANTICIPO', 'text' => 'ANTICIPO'),
        array('id' => 'DESCUENTO', 'text' => 'DESCUENTO'),
        array('id' => 'IGV', 'text' => 'IGV'),
        array('id' => 'PERCEPCION', 'text' => 'PERCEPCION'),
        array('id' => 'ISC', 'text' => 'ISC'),
        array('id' => 'INAFECTO', 'text' => 'INAFECTO'),
        array('id' => 'EXONERADO', 'text' => 'EXONERADO'),
        array('id' => 'TOTAL', 'text' => 'TOTAL'),
        array('id' => 'OTRO TRIBUTO', 'text' => 'OTRO TRIBUTO'),
        array('id' => 'ICBP', 'text' => 'ICBP')
    );

    public function getParametro($search)
    {
        if (!empty($search)) {
            $parametro_auxiliar = array();

            foreach ($this->parametro as $indice => $valor) {
                if (strpos(strtolower($valor['id']), strtolower($search)) !== false) {
                    $array = array('id' => $valor['id'], 'text' => $valor['text']);

                    $parametro_auxiliar[] = $array;
                }
            }

            $this->parametro = $parametro_auxiliar;
        }

        return $this->parametro;
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            $parametro = $this->parametro;

            if (isset($post['App']) && !empty($post['App']) && $post['App'] == 'Ventas') {
                if (isset($post['search'])) {
                    $search = $post['search'];

                    $parametro_auxiliar = array();

                    foreach ($parametro as $indice => $valor) {
                        if (strpos(strtolower($valor['text']), strtolower($search)) !== false) {
                            $array = array('id' => $valor['id'], 'text' => $valor['text']);

                            $parametro_auxiliar[] = $array;
                        }
                    }

                    $parametro = $parametro_auxiliar;
                }
            } else {
                if (isset($post['search'])) {
                    $search = $post['search'];

                    $parametro_auxiliar = array();

                    foreach ($parametro as $indice => $valor) {
                        if (strpos(strtolower($valor['text']), strtolower($search)) !== false) {
                            $array = array('id' => $valor['id'], 'text' => $valor['text']);

                            $parametro_auxiliar[] = $array;
                        }
                    }

                    $parametro = $parametro_auxiliar;
                }
            }

            echo json_encode($parametro);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
