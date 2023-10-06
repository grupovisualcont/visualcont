<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DebeHaber extends BaseController
{
    protected $debe_haber = array(array('id' => 'D', 'text' => 'Debe'), array('id' => 'H', 'text' => 'Haber'));

    public function getDebeHaber($search)
    {
        if (!empty($search)) {
            $debe_haber_auxiliar = array();

            foreach ($this->debe_haber as $indice => $valor) {
                if (strpos(strtolower($valor['id']), strtolower($search)) !== false) {
                    $array = array('id' => $valor['id'], 'text' => $valor['text']);

                    $debe_haber_auxiliar[] = $array;
                }
            }

            $this->debe_haber = $debe_haber_auxiliar;
        }

        return $this->debe_haber;
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            $debe_haber = $this->debe_haber;

            if (isset($post['search'])) {
                $search = $post['search'];

                $debe_haber_auxiliar = array();

                foreach ($debe_haber as $indice => $valor) {
                    if (strpos(strtolower($valor['text']), strtolower($search)) !== false) {
                        $array = array('id' => $valor['id'], 'text' => $valor['text']);

                        $debe_haber_auxiliar[] = $array;
                    }
                }

                $debe_haber = $debe_haber_auxiliar;
            }

            echo json_encode($debe_haber);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
