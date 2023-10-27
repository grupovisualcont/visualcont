<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Web\SocioNegocioModel;
use App\Entities\SocioNegocio;
use App\Entities\SocioNegocioXTipo;
use App\Models\Web\EmpresaModel;
use App\Models\Web\TipoPersonaModel;
use App\Models\Web\TipoDocumentoIdentidadModel;
use App\Models\Web\AnexoModel;
use App\Models\Web\UbigeoModel;
use App\Models\Web\SocioNegocioXTipoModel;
use App\Models\Web\TipoSocioNegocioModel;
use App\Models\Web\BancoModel;
use App\Models\Web\SocioNegocioBancoModel;
use App\Entities\SocioNegocioBanco;
use App\Models\Web\T27VinculoModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Exception;

use function PHPUnit\Framework\throwException;

class SocioNegocioController extends BaseController
{

    public function lista()
    {
        // @pendiente a cambiar la logica para mejorar la busqueda
        $items = (new SocioNegocioModel())->lista(self::$objEmpresa->CodEmpresa);
        return viewWeb('Socio Negocio::Lista', 'socio_negocio/vista_lista', [
            'socio_negocio' => $items,
            'typeOrder' => 'num'
        ]);
    }

    public function crear()
    {
        $predeterminados = (new EmpresaModel())->getPredeterminado(self::$objEmpresa->CodEmpresa);
        $arrTipoPersonas = (new TipoPersonaModel())->findAll();
        $arrTipoDocIdentidad = (new TipoDocumentoIdentidadModel())->findAll();
        $arrCondicion = (new AnexoModel())->getCondiciones();
        $arrPaises = (new UbigeoModel())->getPaises();
        $arrEstados = (new AnexoModel())->getEstados();
        $arrTipoSocioNegocio = (new TipoSocioNegocioModel())->findAll();

        $tipoDocIdentidadRuc = array_values(array_filter($arrTipoDocIdentidad, fn($value) => $value->CodTipoDoc == '6'))[0];
        $tipoDocIdentidadExt = array_values(array_filter($arrTipoDocIdentidad, fn($value) => $value->CodTipoDoc == '-'))[0];

        return viewWeb('Socio Negocio :: Crear', 'socio_negocio/vista_crear', compact(
            'arrTipoPersonas',
            'arrTipoDocIdentidad',
            'arrCondicion',
            'arrPaises',
            'arrEstados',
            'arrTipoSocioNegocio',
            'tipoDocIdentidadRuc',
            'tipoDocIdentidadExt'
        ));
    }

    public function edit($id)
    {
        $objSocioNegocio = (new SocioNegocioModel())->find($id);
        if (empty($objSocioNegocio)) {
            \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $predeterminados = (new EmpresaModel())->getPredeterminado(self::$objEmpresa->CodEmpresa);
        $arrTipoPersonas = (new TipoPersonaModel())->findAll();
        $arrTipoDocIdentidad = (new TipoDocumentoIdentidadModel())->findAll();
        $arrCondicion = (new AnexoModel())->getCondiciones();
        $arrPaises = (new UbigeoModel())->getPaises();
        $arrEstados = (new AnexoModel())->getEstados();
        $arrTipoSocioNegocio = (new TipoSocioNegocioModel())->findAll();
        $objTt27Vinculo = (!empty($objSocioNegocio->CodVinculo)) ? (new T27VinculoModel())->find($objSocioNegocio->CodVinculo) : null;
        $objSexo = (!empty($objSocioNegocio->CodSexo)) ? (new AnexoModel())->find($objSocioNegocio->CodSexo) : null;
        $objDocIdentidadTele = (!empty($objSocioNegocio->CodTipoDoc_Tele)) ? (new TipoDocumentoIdentidadModel())->find($objSocioNegocio->CodTipoDoc_Tele) : null;


        $tipoDocIdentidadRuc = array_values(array_filter($arrTipoDocIdentidad, fn($value) => $value->CodTipoDoc == '6'))[0];
        $tipoDocIdentidadExt = array_values(array_filter($arrTipoDocIdentidad, fn($value) => $value->CodTipoDoc == '-'))[0];

        $socioNegocioTipos = (new SocioNegocioXTipoModel())->where('CodSocioN', $objSocioNegocio->CodSocioN)->get()->getResult();
        $arrBancos = (new SocioNegocioBancoModel())->getDetalle($objSocioNegocio->CodSocioN);

        return viewWeb('Socio Negocio :: Editar', 'socio_negocio/vista_editar', compact(
            'objSocioNegocio',
            'arrTipoPersonas',
            'arrTipoDocIdentidad',
            'arrCondicion',
            'arrPaises',
            'arrEstados',
            'arrTipoSocioNegocio',
            'tipoDocIdentidadRuc',
            'tipoDocIdentidadExt',
            'socioNegocioTipos',
            'objTt27Vinculo',
            'objSexo',
            'objDocIdentidadTele',
            'arrBancos'
        ));
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            if (!$this->request->isAJAX()) {
                \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

            /*************************************************/
            /* NEGOCIO NEGOCIO */
            /*************************************************/
            $ruc = $this->request->getVar('ruc');
            $docIdentidad = $this->request->getVar('docidentidad');
            $razonSocial = $this->request->getVar('razonsocial');
            if (!empty($ruc)) {
                $resp = (new SocioNegocioModel())->validarRuc(self::$objEmpresa->CodEmpresa, $ruc);
                if (!empty($resp)) {
                    throw new Exception('El RUC ya existe.|ruc');
                }
            }
            if (!empty($docIdentidad)) {
                $resp = (new SocioNegocioModel())->validarDocIdentidad(self::$objEmpresa->CodEmpresa, $docIdentidad);
                if (!empty($resp)) {
                    throw new Exception('El Documento de Identidad ya existe.|docidentidad');
                }
            }
            if (!empty($razonSocial)) {
                $resp = (new SocioNegocioModel())->validarRazonSocial(self::$objEmpresa->CodEmpresa, $razonSocial);
                if (!empty($resp)) {
                    throw new Exception('La Razón Social ya existe.|razonsocial');
                }
            }

            $objSocioNegocio = new SocioNegocio();
            $objSocioNegocio->CodInterno = $this->request->getVar('CodInterno');
            $objSocioNegocio->CodEmpresa = self::$objEmpresa->CodEmpresa;
            $objSocioNegocio->ApePat = $this->request->getVar('ApePat');
            $objSocioNegocio->ApeMat = $this->request->getVar('ApeMat');
            $objSocioNegocio->Nom1 = $this->request->getVar('Nom1');
            $objSocioNegocio->Nom2 = $this->request->getVar('Nom2');
            $objSocioNegocio->RazonSocial = $this->request->getVar('razonsocial');
            $objSocioNegocio->Ruc = $this->request->getVar('ruc');
            $objSocioNegocio->DocIdentidad = $this->request->getVar('docidentidad');
            $objSocioNegocio->Direccion1 = $this->request->getVar('direccion1');
            if (!empty($this->request->getVar('codubigeo'))) {
                $objSocioNegocio->CodUbigeo = $this->request->getVar('codubigeo');
            }
            $objSocioNegocio->Telefono = $this->request->getVar('telefono');
            $objSocioNegocio->DirElectronica = $this->request->getVar('direlectronica');
            $objSocioNegocio->Comentario = $this->request->getVar('comentario');
            $objSocioNegocio->FecIngreso = $this->request->getVar('fecingreso');
            $objSocioNegocio->CodTipPer = $this->request->getVar('CodTipPer');
            $objSocioNegocio->CodTipoDoc = $this->request->getVar('CodTipoDoc');
            $objSocioNegocio->CodEstado = $this->request->getVar('Idestado');
            $objSocioNegocio->CodCondicion = $this->request->getVar('IdCondicion');
            $objSocioNegocio->PagWeb = $this->request->getVar('pagweb');
            $objSocioNegocio->Retencion = $this->request->getVar('retencion');
            
            if (!empty($this->request->getVar('IdCondicion'))) {
                $objSocioNegocio->CodVinculo = $this->request->getVar('CodVinculo');
            }
            if (!empty($this->request->getVar('IdSexo'))) {
                $objSocioNegocio->CodSexo = $this->request->getVar('IdSexo');
            }
            if (!empty($this->request->getVar('CodTipoDoc_Tele'))) {
                $objSocioNegocio->CodTipoDoc_Tele = $this->request->getVar('CodTipoDoc_Tele');
                $objSocioNegocio->DocIdentidad_Tele = $this->request->getVar('docidentidad_Tele');
            }

            $objSocioNegocioModel = new SocioNegocioModel();
            $resp = $objSocioNegocioModel->save($objSocioNegocio);
            if (!$resp) {
                $this->responseResultValidation($objSocioNegocioModel->errors());
            }


            /*************************************************/
            /* TIPO SOCIO DE NEGOCIO NEGOCIO */
            /*************************************************/
            $tipoSocioNegocio = $this->request->getVar('tipo_socio_negocio');
            // Cuando no se elige ninguno se toma en cuenta por defecto cliente
            if (empty($tipoSocioNegocio)) {
                throw new Exception('Debes elegir un tipo Socio Negocio.|tipo_socio_negocio[0]');
            }
            foreach($tipoSocioNegocio as $key => $valueTipoSN) {
                if (!empty($valueTipoSN)) {
                    $objTipoSN = (new TipoSocioNegocioModel())->find($valueTipoSN);
                    if (empty($objTipoSN)) {
                        throw new Exception("Tipo Socio Negocio no pudo ser encontrado.|tipo_socio_negocio[{$key}]");
                    }
                    $objSocioNegocioTipo = new SocioNegocioXTipo();
                    $objSocioNegocioTipo->CodTipoSN = $objTipoSN->CodTipoSN;
                    $objSocioNegocioTipo->CodSocioN = $objSocioNegocioModel->getInsertID();
                    (new SocioNegocioXTipoModel())->save($objSocioNegocioTipo);
                }
            }
            
            /*************************************************/
            /* BANCOS DEL NEGOCIO NEGOCIO */
            /*************************************************/
            $arrCodBanco = $this->request->getVar('CodBanco');
            $arrCodTipoCuenta = $this->request->getVar('idTipoCuenta');
            $arrNroCuenta = $this->request->getVar('NroCuenta');
            $arrNroCuentaCII = $this->request->getVar('NroCuentaCCI');
            $arrPredeterminado = $this->request->getVar('Predeterminado');
            if (!empty($arrCodBanco)) {
                foreach($arrCodBanco as $key => $value) {
                    $codBanco = $arrCodBanco[$key] ?? null;
                    $codTipoBanco = $arrCodTipoCuenta[$key] ?? null;
                    $nroCuenta = $arrNroCuenta[$key] ?? '';
                    $nroCuentaCII = $arrNroCuentaCII[$key] ?? '';
                    $predeterminado = $arrPredeterminado[$key] ?? 0;
                    $objBancoModel = (new BancoModel())->find($codBanco);
                    if (!empty($objBancoModel)) {
                        $valPredeterminado = ($predeterminado == ($key + 1)) ? 1 : 0;
                        $objSNBanco = new SocioNegocioBanco();
                        $objSNBanco->CodSocioN = $objSocioNegocioModel->getInsertID();
                        $objSNBanco->CodBanco = $codBanco;
                        if (!empty($codTipoBanco)) {
                            $objSNBanco->idTipoCuenta = $codTipoBanco;
                        }
                        $objSNBanco->NroCuenta = $nroCuenta;
                        $objSNBanco->NroCuentaCCI = $nroCuentaCII;
                        $objSNBanco->Predeterminado = 0;
                        $objSNBanco->Detraccion = 0;
                        $objSNBanco->Predeterminado = $valPredeterminado;
                        (new SocioNegocioBancoModel())->save($objSNBanco);
                    }
                }
            }
            

            if (!$db->transStatus()) {
                throw new Exception('Error en el proceso de ejecución de Socio de Negocio.');
            }
            $db->transCommit();
            $this->isOk('Registrado correctamente.');

        } catch (Exception $ex) {
            $this->responseResultException($ex->getMessage());
            $db->transRollback();
        } catch (DatabaseException $ex) {
            $this->responseResultException($ex->getMessage());
            $db->transRollback();
        }
        return $this->responseResult();
    }

    public function update()
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            if (!$this->request->isAJAX()) {
                \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

            /*************************************************/
            /* NEGOCIO NEGOCIO */
            /*************************************************/
            $objSocioNegocio = (new SocioNegocioModel())->find($this->request->getVar('socionegocio_id'));
            if (empty($objSocioNegocio)) {
                throw new Exception('El Socio de Negocio a editar no pudo ser encontrado.');
            }
            $ruc = $this->request->getVar('ruc');
            $docIdentidad = $this->request->getVar('docidentidad');
            $razonSocial = $this->request->getVar('razonsocial');
            if (!empty($ruc)) {
                $resp = (new SocioNegocioModel())->validarRuc(self::$objEmpresa->CodEmpresa, $ruc, $objSocioNegocio->Ruc);
                if (!empty($resp)) {
                    throw new Exception('El RUC ya existe.|ruc');
                }
            }
            if (!empty($docIdentidad)) {
                $resp = (new SocioNegocioModel())->validarDocIdentidad(self::$objEmpresa->CodEmpresa, $docIdentidad, $objSocioNegocio->DocIdentidad);
                if (!empty($resp)) {
                    throw new Exception('El Documento de Identidad ya existe.|docidentidad');
                }
            }
            if (!empty($razonSocial)) {
                $resp = (new SocioNegocioModel())->validarRazonSocial(self::$objEmpresa->CodEmpresa, $razonSocial, $objSocioNegocio->RazonSocial);
                if (!empty($resp)) {
                    throw new Exception('La Razón Social ya existe.|razonsocial');
                }
            }

            $objSocioNegocio->CodInterno = $this->request->getVar('CodInterno');
            $objSocioNegocio->CodEmpresa = self::$objEmpresa->CodEmpresa;
            $objSocioNegocio->ApePat = $this->request->getVar('ApePat');
            $objSocioNegocio->ApeMat = $this->request->getVar('ApeMat');
            $objSocioNegocio->Nom1 = $this->request->getVar('Nom1');
            $objSocioNegocio->Nom2 = $this->request->getVar('Nom2');
            $objSocioNegocio->RazonSocial = $this->request->getVar('razonsocial');
            $objSocioNegocio->Ruc = $this->request->getVar('ruc');
            $objSocioNegocio->DocIdentidad = $this->request->getVar('docidentidad');
            $objSocioNegocio->Direccion1 = $this->request->getVar('direccion1');
            if (!empty($this->request->getVar('codubigeo'))) {
                $objSocioNegocio->CodUbigeo = $this->request->getVar('codubigeo');
            }
            $objSocioNegocio->Telefono = $this->request->getVar('telefono');
            $objSocioNegocio->DirElectronica = $this->request->getVar('direlectronica');
            $objSocioNegocio->Comentario = $this->request->getVar('comentario');
            $objSocioNegocio->FecIngreso = $this->request->getVar('fecingreso');
            $objSocioNegocio->CodTipPer = $this->request->getVar('CodTipPer');
            $objSocioNegocio->CodTipoDoc = $this->request->getVar('CodTipoDoc');
            $objSocioNegocio->CodEstado = $this->request->getVar('Idestado');
            $objSocioNegocio->CodCondicion = $this->request->getVar('IdCondicion');
            $objSocioNegocio->PagWeb = $this->request->getVar('pagweb');
            $objSocioNegocio->Retencion = $this->request->getVar('retencion');
            
            if (!empty($this->request->getVar('IdCondicion'))) {
                $objSocioNegocio->CodVinculo = $this->request->getVar('CodVinculo');
            }
            if (!empty($this->request->getVar('IdSexo'))) {
                $objSocioNegocio->CodSexo = $this->request->getVar('IdSexo');
            }
            if (!empty($this->request->getVar('CodTipoDoc_Tele'))) {
                $objSocioNegocio->CodTipoDoc_Tele = $this->request->getVar('CodTipoDoc_Tele');
                $objSocioNegocio->DocIdentidad_Tele = $this->request->getVar('docidentidad_Tele');
            }
            
            $objSocioNegocioModel = new SocioNegocioModel();
            $resp = $objSocioNegocioModel->save($objSocioNegocio);
            if (!$resp) {
                $this->responseResultValidation($objSocioNegocioModel->errors());
            }

            /*************************************************/
            /* TIPO SOCIO DE NEGOCIO NEGOCIO */
            /*************************************************/
            $tipoSocioNegocio = $this->request->getVar('tipo_socio_negocio');
            // Cuando no se elige ninguno se toma en cuenta por defecto cliente
            if (empty($tipoSocioNegocio)) {
                throw new Exception('Debes elegir un tipo Socio Negocio.|tipo_socio_negocio[0]');
            }
            (new SocioNegocioXTipoModel())->where('CodSocioN', $objSocioNegocio->CodSocioN)->delete();
            foreach($tipoSocioNegocio as $key => $valueTipoSN) {
                if (!empty($valueTipoSN)) {
                    $objTipoSN = (new TipoSocioNegocioModel())->find($valueTipoSN);
                    if (empty($objTipoSN)) {
                        throw new Exception("Tipo Socio Negocio no pudo ser encontrado.|tipo_socio_negocio[{$key}]");
                    }
                    $objSocioNegocioTipo = new SocioNegocioXTipo();
                    $objSocioNegocioTipo->CodTipoSN = $objTipoSN->CodTipoSN;
                    $objSocioNegocioTipo->CodSocioN = $objSocioNegocio->CodSocioN;
                    (new SocioNegocioXTipoModel())->save($objSocioNegocioTipo);
                }
            }
            
            /*************************************************/
            /* BANCOS DEL NEGOCIO NEGOCIO */
            /*************************************************/
            $arrCodBanco = $this->request->getVar('CodBanco');
            $arrCodTipoCuenta = $this->request->getVar('idTipoCuenta');
            $arrNroCuenta = $this->request->getVar('NroCuenta');
            $arrNroCuentaCII = $this->request->getVar('NroCuentaCCI');
            $arrPredeterminado = $this->request->getVar('Predeterminado');
            if (!empty($arrCodBanco)) {
                (new SocioNegocioBancoModel())->where('CodSocioN', $objSocioNegocio->CodSocioN)->delete();
                foreach($arrCodBanco as $key => $value) {
                    $codBanco = $arrCodBanco[$key] ?? null;
                    $codTipoBanco = $arrCodTipoCuenta[$key] ?? null;
                    $nroCuenta = $arrNroCuenta[$key] ?? '';
                    $nroCuentaCII = $arrNroCuentaCII[$key] ?? '';
                    $predeterminado = $arrPredeterminado[$key] ?? 0;
                    $objBancoModel = (new BancoModel())->find($codBanco);
                    if (!empty($objBancoModel)) {
                        var_dump($codBanco);
                        $valPredeterminado = ($predeterminado == ($key + 1)) ? 1 : 0;
                        $objSNBanco = new SocioNegocioBanco();
                        $objSNBanco->CodSocioN = $objSocioNegocio->CodSocioN;
                        $objSNBanco->CodBanco = $codBanco;
                        if (!empty($codTipoBanco)) {
                            $objSNBanco->idTipoCuenta = $codTipoBanco;
                        }
                        $objSNBanco->NroCuenta = $nroCuenta;
                        $objSNBanco->NroCuentaCCI = $nroCuentaCII;
                        $objSNBanco->Predeterminado = 0;
                        $objSNBanco->Detraccion = 0;
                        $objSNBanco->Predeterminado = $valPredeterminado;
                        (new SocioNegocioBancoModel())->save($objSNBanco);
                    }
                }
            }
            
            if (!$db->transStatus()) {
                throw new Exception('Error en el proceso de ejecución de Socio de Negocio.');
            }
            $db->transCommit();
            $this->isOk('Actualizado correctamente.');

        } catch (Exception $ex) {
            $this->responseResultException($ex->getMessage());
            $db->transRollback();
        } catch (DatabaseException $ex) {
            $this->responseResultException($ex->getMessage());
            $db->transRollback();
        }
        return $this->responseResult();
    }

    public function destroy($id)
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            if (!$this->request->isAJAX()) {
                \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
            $objSocioNegocio = (new SocioNegocioModel())->find($id);
            if (empty($objSocioNegocio)) {
                throw new Exception('El Socio Negocio no pudo ser encontrado.');
            }
            $resp = (new SocioNegocioModel())->delete($objSocioNegocio->CodSocioN);
            if (!$resp) {
                throw new Exception('El Socio de Negocio no pudo ser eliminado');
            }
            if (!$db->transStatus()) {
                throw new Exception('Error en el proceso de ejecución de Socio de Negocio.');
            }
            $db->transCommit();
            $this->isOk('Eliminado correctamente.');
        } catch (Exception $ex) {
            $this->responseResultException($ex->getMessage());
            $db->transRollback();
        } catch (DatabaseException $ex) {
            $this->responseResultException($ex->getMessage());
            $db->transRollback();
        }
        return $this->responseResult();
    }

}
