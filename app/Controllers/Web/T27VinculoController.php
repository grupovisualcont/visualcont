<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Web\T27VinculoModel;

class T27VinculoController extends BaseController
{
    public function autoCompletado()
    {
        $search = $this->request->getGet('search');
        $items = (new T27VinculoModel())->autoCompletado($search);
        return $this->response->setJSON($items);
    }
}
