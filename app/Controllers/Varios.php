<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Anexo;
use App\Models\PlanContable;

class Varios extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Varios';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $datos = (new Anexo())->getAnexo(
                    $this->CodEmpresa,
                    0,
                    [13, 14, 32, 43],
                    '',
                    'anexos.*, 
                    CASE
                        WHEN TipoAnexo = 13 THEN "Cuentas Diversos"
                        WHEN TipoAnexo = 14 THEN "Cuentas Anticipo"
                        WHEN TipoAnexo = 32 THEN "Tipo Cliente"
                        WHEN TipoAnexo = 43 THEN "Situación Activo Fijo"
                    END AS TipoDescripcion, 
                    IF(Estado = 0, "Inactivo", "Activo") AS Estado, 
                    CASE
                        WHEN TipoAnexo = 13 THEN 1
                        WHEN TipoAnexo = 14 THEN 2
                        WHEN TipoAnexo = 32 THEN 3
                        WHEN TipoAnexo = 43 THEN 4
                    END AS Tipo',
                    [],
                    '',
                    'TipoAnexo ASC'
                );

                return viewApp($this->page, 'app/mantenience/some/index', [
                    'datos' => $datos,
                    'typeOrder' => 'string'
                ]);
            } else {
                return (new Empresa())->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function create()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $tipos = array('Cuentas Diversos', 'Cuentas Anticipo', 'Tipo Cliente', 'Situación Activo Fijo');

                $options_tipos = '';

                foreach ($tipos as $indice => $valor) {
                    $selected = '';

                    if ($indice == 0) $selected = 'selected';

                    $options_tipos .= '<option value="' . ($indice + 1) . '" ' . $selected . '>' . $valor . '</option>';
                }

                $estado = (new Anexo())->getAnexo($this->CodEmpresa, 0, 1, '', '', [], 'CodInterno = 1', '')[0];

                $option_estado = '<option value="' . $estado['CodInterno'] . '">' . $estado['DescAnexo'] . '</option>';

                $script = (new Empresa())->generar_script(['app/mantenience/some/create.js']);

                return viewApp($this->page, 'app/mantenience/some/create', [
                    'options_tipos' => $options_tipos,
                    'option_estado' => $option_estado,
                    'typeOrder' => 'string',
                    'script' => $script
                ]);
            } else {
                return (new Empresa())->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit($IdAnexo, $Tipo)
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $varios = (new Anexo())->getAnexo($this->CodEmpresa, $IdAnexo, 0, '', '', [], '', '')[0];

                $tipos = array('Cuentas Diversos', 'Cuentas Anticipo', 'Tipo Cliente', 'Situación Activo Fijo');

                $options_tipos = '';

                foreach ($tipos as $indice => $valor) {
                    $selected = '';

                    if (($indice + 1) == $Tipo) $selected = 'selected';

                    $options_tipos .= '<option value="' . ($indice + 1) . '" ' . $selected . '>' . $valor . '</option>';
                }

                $option_plan_contable = '';

                if (isset($varios['CodInterno'])) {
                    $plan_contable = (new PlanContable())->getPlanContable(
                        $this->CodEmpresa,
                        date('Y'),
                        $varios['CodInterno'],
                        'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled',
                        [],
                        '',
                        'CodCuenta ASC'
                    )[0];

                    $option_plan_contable = '<option value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';
                }

                $estado = (new Anexo())->getAnexo($this->CodEmpresa, 0, 1, '', '', [], 'CodInterno = ' . $varios['Estado'], '')[0];

                $option_estado = '<option value="' . $estado['CodInterno'] . '">' . $estado['DescAnexo'] . '</option>';

                $script = (new Empresa())->generar_script(['app/mantenience/some/edit.js']);

                return viewApp($this->page, 'app/mantenience/some/edit', [
                    'varios' => $varios,
                    'Tipo' => $Tipo,
                    'options_tipos' => $options_tipos,
                    'option_plan_contable' => $option_plan_contable,
                    'option_estado' => $option_estado,
                    'typeOrder' => 'string',
                    'script' => $script
                ]);
            } else {
                return (new Empresa())->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function save()
    {
        try {
            $post = $this->request->getPost();

            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $Tipo = $post['Tipo'];

            $post['DescAnexo'] = strtoupper(trim($post['DescAnexo' . $Tipo]));
            $post['CodInterno'] = isset($post['CodInterno' . $Tipo]) ? trim($post['CodInterno' . $Tipo]) : NULL;
            $post['Estado'] = $post['Estado' . $Tipo];

            switch ($Tipo) {
                case 1:
                    $post['TipoAnexo'] = 13;

                    break;
                case 2:
                    $post['TipoAnexo'] = 14;

                    break;
                case 3:
                    $post['TipoAnexo'] = 32;

                    break;
                case 4:
                    $post['TipoAnexo'] = 43;

                    break;
            }

            (new Anexo())->agregar($post);

            if ($this->db->transStatus() === FALSE) {
                $this->db->transRollback();

                $result = false;
            } else {
                $this->db->transCommit();

                $result = true;
            }

            if ($result) {
                $_SESSION['code'] = 'success';
            } else {
                $_SESSION['code'] = 'error';
            }

            return redirect()->to(base_url('app/mantenience/some/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update()
    {
        try {
            $post = $this->request->getPost();

            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $Tipo = $post['Tipo'];

            $post['IdAnexo'] = $post['IdAnexo' . $Tipo];
            $post['DescAnexo'] = strtoupper(trim($post['DescAnexo' . $Tipo]));
            $post['CodInterno'] = isset($post['CodInterno' . $Tipo]) ? trim($post['CodInterno' . $Tipo]) : NULL;
            $post['Estado'] = $post['Estado' . $Tipo];

            switch ($Tipo) {
                case 1:
                    $post['TipoAnexo'] = 13;

                    break;
                case 2:
                    $post['TipoAnexo'] = 14;

                    break;
                case 3:
                    $post['TipoAnexo'] = 32;

                    break;
                case 4:
                    $post['TipoAnexo'] = 43;

                    break;
            }

            (new Anexo())->actualizar($this->CodEmpresa, $post['IdAnexo'], $post);

            if ($this->db->transStatus() === FALSE) {
                $this->db->transRollback();

                $result = false;
            } else {
                $this->db->transCommit();

                $result = true;
            }

            if ($result) {
                $_SESSION['code'] = 'success';
            } else {
                $_SESSION['code'] = 'error';
            }

            return redirect()->to(base_url('app/mantenience/some/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($IdAnexo)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            (new Anexo())->eliminar($this->CodEmpresa, $IdAnexo);

            if ($this->db->transStatus() === FALSE) {
                $this->db->transRollback();

                $result = false;
            } else {
                $this->db->transCommit();

                $result = true;
            }

            if ($result) {
                $_SESSION['code'] = 'success';
            } else {
                $_SESSION['code'] = 'error';
            }

            return redirect()->to(base_url('app/mantenience/some/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel()
    {
        try {
            $excel = new Excel();

            $excel->creacion('Varios - Reporte');

            $columnas = array('Tipo', 'Código', 'Descripción', 'Estado');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $result = (new Anexo())->getAnexo(
                $this->CodEmpresa,
                0,
                [13, 14, 32, 43],
                '',
                'anexos.*, 
                CASE
                    WHEN TipoAnexo = 13 THEN "Cuentas Diversos"
                    WHEN TipoAnexo = 14 THEN "Cuentas Anticipo"
                    WHEN TipoAnexo = 32 THEN "Tipo Cliente"
                    WHEN TipoAnexo = 43 THEN "Situación Activo Fijo"
                END AS TipoDescripcion, 
                IF(Estado = 0, "Inactivo", "Activo") AS Estado, 
                CASE
                    WHEN TipoAnexo = 13 THEN 1
                    WHEN TipoAnexo = 14 THEN 2
                    WHEN TipoAnexo = 32 THEN 3
                    WHEN TipoAnexo = 43 THEN 4
                END AS Tipo',
                [],
                '',
                'TipoAnexo ASC'
            );

            foreach ($result as  $indice => $valor) {
                $values = array(
                    $valor['TipoDescripcion'],
                    $valor['CodInterno'],
                    $valor['DescAnexo'],
                    $valor['Estado']
                );

                $excel->setValues($values);

                $excel->body($indice + 2, 'valor');
            }

            $excel->footer('varios_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf()
    {
        try {
            $result = (new Anexo())->getAnexo(
                $this->CodEmpresa,
                0,
                [13, 14, 32, 43],
                '',
                'anexos.*, 
                CASE
                    WHEN TipoAnexo = 13 THEN "Cuentas Diversos"
                    WHEN TipoAnexo = 14 THEN "Cuentas Anticipo"
                    WHEN TipoAnexo = 32 THEN "Tipo Cliente"
                    WHEN TipoAnexo = 43 THEN "Situación Activo Fijo"
                END AS TipoDescripcion, 
                IF(Estado = 0, "Inactivo", "Activo") AS Estado, 
                CASE
                    WHEN TipoAnexo = 13 THEN 1
                    WHEN TipoAnexo = 14 THEN 2
                    WHEN TipoAnexo = 32 THEN 3
                    WHEN TipoAnexo = 43 THEN 4
                END AS Tipo',
                [],
                '',
                'TipoAnexo ASC'
            );

            $columnas = array('Tipo', 'Código', 'Descripción', 'Estado');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['TipoDescripcion'] . '</td>
                    <td align="left">' . $valor['CodInterno'] . '</td>
                    <td align="left">' . $valor['DescAnexo'] . '</td>
                    <td align="left">' . $valor['Estado'] . '</td>
                <tr>
            ';
            }

            $pdf = new PDF();

            $pdf->setFilename('varios_reporte');
            $pdf->creacion('Varios - Reporte', $tr, '', 'A3', true);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
