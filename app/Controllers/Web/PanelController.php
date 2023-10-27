<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

/**
 * Class Panel
 */
class PanelController extends BaseController
{
    public function index(): string
    {
        return viewWeb('Panel', 'panel/vista_inicio');
    }
}
