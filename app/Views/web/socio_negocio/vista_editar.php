<?= $this->extend('web/layout/master') ?>

<?= $this->section('menu') ?>
<nav style="--bs-breadcrumb-divider: '/'" aria-label="breadcrumb" class="nav-breadcrumb" >
    <ol class="breadcrumb" >
        <li class="breadcrumb-item" >
            Mantenimiento 
        </li>
        <li class="breadcrumb-item" >
            <a href="<?= baseUrlWeb('mantenience/business_partner') ?>">Socio Negocio</a>
        </li>
        <li class="breadcrumb-item" >
            editar
        </li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <section class="content-buttons btn-groups" >
        <button type="button" class="btn btn-primary btn-sm px-4" id="btnGrabar" >
            <div class="d-flex align-items-center" >
                <i class="fa fa-save me-2 fs-4" ></i> Grabar
            </div>
        </button>
        <a class="btn btn-link btn-sm px-4" href="<?= baseUrlWeb('mantenience/business_partner') ?>" >
            <div class="d-flex align-items-center" >
                Cancelar
            </div>
        </a>
    </section>
    <div class="card">
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
                <form id="form" class="mt-3" method="POST" action="<?= baseUrlWeb('mantenience/business_partner/update') ?>" >
                    <input type="hidden" id="_method" name="_method" value="PUT" >
                    <input type="hidden" id="socionegocio_id" name="socionegocio_id" value="<?= $objSocioNegocio->CodSocioN ?>" >
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
                                                <input type="text" class="form-control form-control-sm" readonly>
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
                                                    <input type="text" name="fecingreso" id="fecingreso" class="form-control form-control-sm mydatepicker" 
                                                    placeholder="dd/mm/yyyy" value="<?= $objSocioNegocio->FecIngreso ?>" readonly>
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
                                                    <?php if (!empty($arrTipoPersonas)) { ?>
                                                        <?php foreach ($arrTipoPersonas as $key => $objTipoPersona) { ?>
                                                            <option value="<?= $objTipoPersona->CodTipPer ?>" <?= ($objSocioNegocio->CodTipPer == $objTipoPersona->CodTipPer) ? 'selected' : '' ?> >
                                                                <?= $objTipoPersona->DescPer ?>
                                                            </option>
                                                        <?php } ?>
                                                    <?php } ?>
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
                                                <input type="text" name="CodInterno" id="CodInterno" class="form-control form-control-sm" readonly>
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
                                                <input type="text" name="ApePat" id="ApePat" class="form-control form-control-sm" value="<?= $objSocioNegocio->ApePat ?>" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Nombre 1</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="Nom1" id="Nom1" class="form-control form-control-sm" value="<?= $objSocioNegocio->Nom1 ?>" >
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
                                                <input type="text" name="ApeMat" id="ApeMat" class="form-control form-control-sm" value="<?= $objSocioNegocio->ApeMat ?>" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Nombre 2</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="Nom2" id="Nom2" class="form-control form-control-sm" value="<?= $objSocioNegocio->Nom2 ?>" >
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
                                                <input type="text" name="razonsocial" id="razonsocial" class="form-control form-control-sm" readonly 
                                                    value="<?= $objSocioNegocio->RazonSocial ?>" >
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
                                                    <?php if (!empty($arrTipoDocIdentidad)) { ?>
                                                        <?php foreach ($arrTipoDocIdentidad as $key => $objTipoDocIdentidad) { ?>
                                                            <option value="<?= $objTipoDocIdentidad->CodTipoDoc ?>" data-tipo-dato="<?= $objTipoDocIdentidad->TipoDato ?>" <?= ($objSocioNegocio->CodTipoDoc == $objTipoDocIdentidad->CodTipoDoc) ? 'selected' : '' ?> >
                                                                <?= $objTipoDocIdentidad->DesDocumento ?>
                                                            </option>
                                                        <?php } ?>
                                                    <?php } ?>
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
                                                    <input type="text" name="ruc" id="ruc" class="form-control form-control-sm" oninput="verificarLongitudDocumento(this)" onkeypress="esNumero(event)" 
                                                        value="<?= $objSocioNegocio->Ruc ?>" >
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
                                                    <?php if (!empty($arrCondicion)) { ?>
                                                        <?php foreach ($arrCondicion as $key => $objCondicion) { ?>
                                                            <option value="<?= $objCondicion->CodAnexo ?>" <?= ($objSocioNegocio->CodCondicion == $objCondicion->CodAnexo) ? 'selected' : '' ?> >
                                                                <?= $objCondicion->DescAnexo ?>
                                                            </option>
                                                        <?php } ?>
                                                    <?php } ?>
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
                                                <div class="input-group input-group-sm">
                                                    <input type="text" name="docidentidad" id="docidentidad" class="form-control form-control-sm" oninput="verificarLongitudDocumento(this)" onkeypress="esNumero(event)" 
                                                        value="<?= $objSocioNegocio->DocIdentidad ?>" >
                                                    <div class="input-group-append" >
                                                        <button type="button" class="btn btn-sm height-sm border" onclick="consulta_sunat('dni')">
                                                            <img src="<?= base_url('assets/img/dni.png') ?>" width="15" height="15" />
                                                        </button>
                                                    </div>
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
                                                <input type="text" name="direccion1" id="direccion1" class="form-control form-control-sm" value="<?= $objSocioNegocio->Direccion1 ?>" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>País</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select id="pais" name="pais" class="form-control form-control-sm" onchange="cambiarInputByPais()">
                                                    <?php if (!empty($arrPaises)) { ?>
                                                        <?php foreach ($arrPaises as $key => $objPais) { ?>
                                                            <option value="<?= $objPais->CodUbigeo ?>"><?= $objPais->DescUbigeo ?></option>
                                                        <?php } ?>
                                                    <?php } ?>
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
                                                <input type="text" name="codubigeo" id="input_codubigeo" class="form-control form-control-sm display-none">
                                                <select name="codubigeo" id="select_codubigeo" class="form-control form-control-sm">
                                                    
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
                                                <input type="text" name="telefono" id="telefono" class="form-control form-control-sm" onkeypress="esNumero(event)" value="<?= $objSocioNegocio->Telefono ?>" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Mail</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="direlectronica" id="direlectronica" class="form-control form-control-sm" value="<?= $objSocioNegocio->DirElectronica ?>" >
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
                                                <textarea name="comentario" id="comentario" class="form-control form-control-sm"><?= $objSocioNegocio->Comentario ?></textarea>
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
                                                    <?php if (!empty($arrEstados)) { ?>
                                                        <?php foreach ($arrEstados as $key => $objEstado) { ?>
                                                            <option value="<?= $objEstado->CodAnexo ?>" <?= ($objSocioNegocio->CodEstado == $objEstado->CodAnexo) ? 'selected' : '' ?> >
                                                                <?= $objEstado->DescAnexo ?>
                                                            </option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Tipo Socio Negocio</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <?php if (!empty($arrTipoSocioNegocio)) { ?>
                                                    <?php foreach ($arrTipoSocioNegocio as $key => $objTipoSocioNegocio) { ?>
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input" 
                                                                    name="tipo_socio_negocio[<?= $key ?>]" id="tipo_socio_negocio[<?= $key ?>]" 
                                                                    <?= (!empty(array_filter($socioNegocioTipos, fn($val) => $val->CodTipoSN == $objTipoSocioNegocio->CodTipoSN))) ? 'checked' : '' ?>
                                                                    value="<?= $objTipoSocioNegocio->CodTipoSN ?>">
                                                                <?= $objTipoSocioNegocio->DescTipoSN ?>
                                                            </label>
                                                        </div>
                                                    <?php } ?>        
                                                <?php } ?>
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
                                                    <?php if (!empty($objTt27Vinculo)) { ?>
                                                        <option value="<?= $objTt27Vinculo->CodVinculo ?>"><?= $objTt27Vinculo->DescVinculo ?></option>
                                                    <?php } ?>
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
                                                <input type="text" name="pagweb" class="form-control form-control-sm" value="<?= $objSocioNegocio->PagWeb ?>" />
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
                                                    <?php if (!empty($objSexo)) { ?>
                                                        <option value="<?= $objSexo->CodAnexo ?>"><?= $objSexo->DescAnexo ?></option>
                                                    <?php } ?>
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
                                                    <input type="text" name="retencion" id="retencion" class="form-control form-control-sm" onkeypress="esNumero(event)" value="<?= $objSocioNegocio->Retencion ?>" />
                                                    <span class="ml-2 mt-1">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 h5">
                                                <label class="fw-bolder">Datos para el telecrédito</label>
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
                                                    <?php if (!empty($objDocIdentidadTele)) { ?>
                                                        <option value="<?= $objDocIdentidadTele->CodTipoDoc ?>"><?= $objDocIdentidadTele->DesDocumento ?></option>
                                                    <?php } ?>
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
                                                <input type="text" name="docidentidad_Tele" id="docidentidad_Tele" class="form-control form-control-sm" onkeypress="esNumero(event)" <?= $objSocioNegocio->DocIdentidad_Tele ?> />
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
                                                            <tr id="tr_empty">
                                                                <td align="center" colspan="7">No hay datos para mostrar</td>
                                                            </tr>
                                                            <?php if (!empty($arrBancos)) { ?>
                                                                <?php foreach($arrBancos as $key => $objBanco) { ?>
                                                                    <tr id="tr_banco${id_banco}" class="clase_banco">
                                                                        <td>
                                                                            <select name="CodBanco[]" class="CodBanco form-control form-control-sm" id="CodBanco$<?= $objBanco->Banco ?>">
                                                                                <option value="<?= $objBanco->CodBanco ?>"><?= $objBanco->Banco ?></option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select name="idTipoCuenta[]" class="idTipoCuenta form-control form-control-sm" id="idTipoCuenta$<?= $objBanco->Banco ?>">

                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="NroCuenta[]" class="form-control form-control-sm" onkeypress="esNumero(event)" value="<?= $objBanco->NroCuenta ?>" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="NroCuentaCCI[]" class="form-control form-control-sm" onkeypress="esNumero(event)" value="<?= $objBanco->NroCuentaCCI ?>" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="radio" name="Predeterminado" class="Predeterminado" <?= !empty($objBanco->Predeterminado) ? 'checked': '' ?> id="Predeterminado$<?= $objBanco->Banco ?>" value="<?= $objBanco->Predeterminado ?>" onchange="cambiarPredeterminado('$<?= $objBanco->Banco ?>')" />
                                                                        </td>
                                                                        <td></td>
                                                                        <td>
                                                                            <button type="button" class="Buttons btn btn-sm btn-danger shadow-sm" onclick="eliminar('$<?= $objBanco->Banco ?>')">Eliminar</button>
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                    <div>
                                                    </div>
                                                </div>
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

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    var datos_ruc_CodTipPer = '02';
    var datos_ruc_CodTipoDoc = '06';
    var datos_ruc_N_tip = '<?= (empty($tipoDocIdentidadRuc)) ? 'F' : $tipoDocIdentidadRuc->N_tip; ?>'
    var datos_extranjero_CodTipPer = '03';
    var datos_extranjero_CodTipoDoc = '<?= (empty($tipoDocIdentidadRuc)) ? '-' : $tipoDocIdentidadRuc->CodTipoDoc; ?>';
</script>
<script src="<?= assetVersion('js/web/socio_negocio/crear.js') ?>" ></script>
<?= $this->endSection() ?>
