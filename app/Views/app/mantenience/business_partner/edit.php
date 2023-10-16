<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card"><a href="<?= base_url('app/mantenience/business_partner/index') ?>" class="link-titulo">Socio de Negocio</a> / Editar <button type="button" class="btn btn-primary btn-sm float-end" onclick="submit()">Guardar</button></span>
        </div>
        <div class="card-body">
            <div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active text-link" id="tab1-tab" data-bs-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Datos Generales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-link" id="tab2-tab" data-bs-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Bancos / Otros</a>
                    </li>
                </ul>
                <form id="form" class="mt-3" method="POST" action="<?= base_url('app/mantenience/business_partner/update') ?>" onsubmit="return verificarFormulario()">
                    <input type="hidden" name="CodEmpresa" value="<?= $_COOKIE['empresa'] ?>" />
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                            <div class="container-fluid my-3">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Código</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="IdSocioN" class="form-control form-control-sm" value="<?= $socio_negocio['IdSocioN'] ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Fec. Registro</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <div class="input-group input-group-sm input-group-vc">
                                                    <input type="text" name="fecingreso" class="form-control form-control-sm mydatepicker" placeholder="dd/mm/yyyy" value="<?= date('d/m/Y', strtotime($socio_negocio['fecingreso'])) ?>" readonly>
                                                    <span class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Tipo Persona</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="CodTipPer" id="CodTipPer" class="form-control form-control-sm" onchange="verificarTipoDocumentoIdentidad()">
                                                    <?= $option_tipo_persona ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Id. Interno</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="CodInterno" class="form-control form-control-sm" value="<?= $socio_negocio['CodInterno'] ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>A. Paterno</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="ApePat" id="ApePat" class="form-control form-control-sm" value="<?= $socio_negocio['ApePat'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Nombre 1</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="Nom1" id="Nom1" class="form-control form-control-sm" value="<?= $socio_negocio['Nom1'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>A. Materno</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="ApeMat" id="ApeMat" class="form-control form-control-sm" value="<?= $socio_negocio['ApeMat'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Nombre 2</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="Nom2" id="Nom2" class="form-control form-control-sm" value="<?= $socio_negocio['Nom2'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                                <label>Razón Social</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-10 col-xl-10">
                                                <input type="text" name="razonsocial" id="razonsocial" class="form-control form-control-sm" value="<?= $socio_negocio['razonsocial'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Tipo Docum.</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="CodTipoDoc" id="CodTipoDoc" class="form-control form-control-sm" onchange="verificarTipoDocumentoIdentidad()">
                                                    <?= $option_tipo_documento_identidad ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>RUC</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <div class="d-flex">
                                                    <input type="text" name="ruc" id="ruc" class="form-control form-control-sm" value="<?= $socio_negocio['ruc'] ?>" oninput="verificarLongitudDocumento(this)" onkeypress="esNumero(event)">
                                                    <button type="button" class="btn btn-sm height-sm border" onclick="consulta_sunat('ruc')">
                                                        <img src="<?= base_url('assets/img/ruc.png') ?>" width="15" height="15" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Condición</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="IdCondicion" id="IdCondicion" class="form-control form-control-sm">
                                                    <?= $option_condicion ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Doc. Identidad</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <div class="d-flex">
                                                    <input type="text" name="docidentidad" id="docidentidad" class="form-control form-control-sm" value="<?= $socio_negocio['docidentidad'] ?>" oninput="verificarLongitudDocumento(this)" onkeypress="esNumero(event)">
                                                    <button type="button" class="btn btn-sm height-sm border" onclick="consulta_sunat('dni')">
                                                        <img src="<?= base_url('assets/img/dni.png') ?>" width="15" height="15" />
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                                <label>Direc. Principal</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-10 col-xl-10">
                                                <input type="text" name="direccion1" id="direccion1" class="form-control form-control-sm" value="<?= $socio_negocio['direccion1'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Pais</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="pais" id="pais" class="form-control form-control-sm" onchange="cambiarInputByPais()">
                                                    <?= $option_pais ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Ubigeo</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="codubigeo_pais" id="input_codubigeo" class="form-control form-control-sm <?= (substr($socio_negocio['codubigeo'], 0, 2) == '01') ? 'display-none' : '' ?>" value="<?= $socio_negocio['codubigeo'] ?>">
                                                <select name="codubigeo" id="select_codubigeo" class="form-control form-control-sm <?= (substr($socio_negocio['codubigeo'], 0, 2) != '01') ? 'display-none' : '' ?>">
                                                    <?= $option_ubigeo ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Teléfono</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="telefono" class="form-control form-control-sm" value="<?= $socio_negocio['telefono'] ?>" onkeypress="esNumero(event)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Mail</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="direlectronica" class="form-control form-control-sm" value="<?= $socio_negocio['direlectronica'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Nota</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <textarea name="comentario" class="form-control"><?= $socio_negocio['comentario'] ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Estado</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="Idestado" id="Idestado" class="form-control form-control-sm">
                                                    <?= $option_estado ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Tipo Socio Negocio</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <?= $checkbox_tipos_socio_negocio ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                            <div class="container-fluid my-3">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Vínculo economico</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="CodVinculo" id="CodVinculo" class="form-control form-control-sm">
                                                    <?= $option_vinculo ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Página Web</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="pagweb" class="form-control form-control-sm" value="<?= $socio_negocio['pagweb'] ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Sexo</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="IdSexo" id="IdSexo" class="form-control form-control-sm">
                                                    <?= $option_sexo ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Retención</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <div class="d-flex">
                                                    <input type="text" name="retencion" class="form-control form-control-sm" value="<?= $socio_negocio['retencion'] ?>" onkeypress="esNumero(event)" /><span class="ml-2 mt-1">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <label class="font-weight-bold">Datos para el telecrédito</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Tipo Docum.</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="CodTipoDoc_Tele" id="CodTipoDoc_Tele" class="form-control form-control-sm">
                                                    <?= $option_tipo_documento_identidad_banco ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Nro. Doc.</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="docidentidad_Tele" class="form-control form-control-sm" value="<?= $socio_negocio['docidentidad_Tele'] ?>" onkeypress="esNumero(event)" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <span class="font-weight-bold">Lista de Bancos
                                                    <button type="button" class="btn btn-sm btn-success shadow-sm float-right" onclick="nuevoBanco()">
                                                        Agregar <i class="fas fa-plus-circle text-white"></i>
                                                    </button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <div class="table-responsive-md">
                                                    <table class="table table-sm table-bordered" id="tablaBanco" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr>
                                                                <th width="25%">Entidad Financiera</th>
                                                                <th width="25%">Tipo Cuenta</th>
                                                                <th>N° Cta. Cte.</th>
                                                                <th>N° CCI</th>
                                                                <th>P</th>
                                                                <th>D</th>
                                                                <th>Eliminar</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            if (count($socio_negocio_banco) == 0) {
                                                            ?>
                                                                <tr id="tr_empty">
                                                                    <td align="center" colspan="7">No hay datos para mostrar</td>
                                                                </tr>
                                                                <?php
                                                            } else {
                                                                foreach ($socio_negocio_banco as $indice => $valor) {
                                                                ?>
                                                                    <tr id="tr_banco<?= $indice + 1 ?>" class="clase_banco">
                                                                        <td>
                                                                            <select name="CodBanco[]" class="CodBanco form-control form-control-sm" id="CodBanco<?= $indice + 1 ?>">
                                                                                <?= str_replace('value="' . $valor['CodBanco'] . '"', 'value="' . $valor['CodBanco'] . '" selected', $options_banco) ?>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select name="idTipoCuenta[]" class="idTipoCuenta form-control form-control-sm" id="idTipoCuenta<?= $indice + 1 ?>">
                                                                                <?= str_replace('value="' . $valor['idTipoCuenta'] . '"', 'value="' . $valor['idTipoCuenta'] . '" selected', $options_tipo_cuenta) ?>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="NroCuenta[]" class="form-control form-control-sm" value="<?= $valor['NroCuenta'] ?>" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="NroCuentaCCI[]" class="form-control form-control-sm" value="<?= $valor['NroCuentaCCI'] ?>" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="radio" name="Predeterminado" id="Predeterminado<?= $indice + 1 ?>" class="Predeterminado" value="<?= $indice + 1 ?>" onchange="cambiarPredeterminado('<?= $indice + 1 ?>')" <?= $valor['Predeterminado'] == '1' ? 'checked' : '' ?> />
                                                                        </td>
                                                                        <td></td>
                                                                        <td>
                                                                            <button type="button" class="Buttons btn btn-sm btn-danger shadow-sm" onclick="eliminar('<?= $indice + 1 ?>')">Eliminar</button>
                                                                        </td>
                                                                    </tr>
                                                            <?php
                                                                }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var id_banco = <?= (count($socio_negocio_banco) + 1) ?>;
    var datos_ruc_CodTipPer = '<?= $datos_ruc['CodTipPer'] ?>';
    var datos_ruc_CodTipoDoc = '<?= $datos_ruc['CodTipoDoc'] ?>';
    var datos_ruc_N_tip = '<?= $datos_ruc['N_tip'] ?>';
    var datos_extranjero_CodTipPer = '<?= $datos_extranjero['CodTipPer'] ?>';
    var datos_extranjero_CodTipoDoc = '<?= $datos_extranjero['CodTipoDoc'] ?>';
    var socio_negocio_ruc = '<?= $socio_negocio['ruc'] ?>';
    var socio_negocio_docidentidad = '<?= $socio_negocio['docidentidad'] ?>';
    var socio_negocio_razonsocial = '<?= str_replace("'", "\'", $socio_negocio['razonsocial']) ?>';
</script>

<?= $this->endSection() ?>