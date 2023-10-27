<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\Web\EmpresaModel;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;

    /**
     * Resultado para identificar el status, menesaje y data
     * @var array
     */
    protected static $result = [];

    /**
     * @var App\Models\Empresa
     */
    protected static $objEmpresa = null;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();

        // Se valida si existe la empresa logeado para realizar su busqueda
        if ($this->session->has('empresa')) {
            self::$objEmpresa = (new EmpresaModel())->find($this->session->get('empresa'));
        }

        /**
         * Solo se trabajara con los codigos
         * 200 : Esta todo ok
         * 500 : Indica que hay un error y debe mostrarse como una alerta
         * 422 : Indica que se encuentra errores que deben imprimirse en el formulario
         */
        self::$result = ['status' => 500, 'message' => '', 'errors' => [], 'data' => []];
    }

    /**
     * Devuelve la respuesta en base al status asignado
     */
    protected function responseResult(): mixed
    {
        if (!empty(self::$result['errors'])) {
            self::$result['status'] = 422;
        }
        // El status solo debe ser uno de los tres para continuar con la respuesta correctamente
        if (self::$result['status'] != 200 && self::$result['status'] != 422 && self::$result['status'] != 500) {
            self::$result['status'] = 500;
        }
        return response()->setStatusCode(self::$result['status'])->setJSON(self::$result);
    }

    /**
     * Realiza la busqueda del error encontrado en un exception
     */
    protected function responseResultException($mensaje) : void
    {
        if (strpos($mensaje, '|')) {
            $errors = explode('|', $mensaje);
            $this->addResultError($errors[1], $errors[0]);
        } else {
            self::$result['message'] = $mensaje;
        }
    }

    protected function responseResultValidation($errors)
    {
        if (!empty($errors)) {
            foreach($errors as $key => $error) {
                $this->responseResultException($error);
            }
        }
        throw new \Exception('');
    }

    /**
     * Agregar error para la respuesta del servidor
     */
    protected function addResultError($id, $mensaje): void
    {
        self::$result['errors'][] = [
            'form_id' => $id,
            'message' => $mensaje,
        ];
    }

    /**
     * Indica que todo esta ok
     */
    protected function isOk(string $message = ''): void
    {
        // $message = (empty($message)) ?? 'Proceso ejecutado correctamente!';
        self::$result['status'] = 200;
        self::$result['message'] = $message;
    }

}
