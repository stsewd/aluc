<?php
namespace Aluc\Service;

use Aluc\Common\AlucException;
use Aluc\Models\LectorQr;
use Aluc\Models\Moderador;
use Aluc\Views\AdministradorView;
use Aluc\Views\LectorQrView;
use Aluc\Views\ModeradorView;
use Aluc\Views\GeneralView;
use Aluc\Common\Tools;


/**
 * Clase que maneja todas las solicitudes a /admin
 */
class AdministradorSrv {
    private static $view_moderador;
    private static $view_general;
    private static $view_lector_qr;
    private static $view_administrador;

    public static function init() {
        static::$view_moderador = ModeradorView::getInstance();
        static::$view_general = GeneralView::getInstance();
        static::$view_lector_qr = LectorQrView::getInstance();
        static::$view_administrador = AdministradorView::getInstance();
    }

    private static function admin_do($func, $error) {
        try {
            if (Tools::check_session('admin')) {
                $func();
            } else {
                ErrorSrv::redirect404();
            }
        } catch (\Exception $e) {
            $error($e);
        }
    }

    /**
     * /admin
     *
     * Muestra la página principal del administrador
     * desde la cual tiene acceso a varias herramientas
     * de administración.
     * @param $data
     */
    public static function home($data) {
        self::admin_do(
            function () use ($data){
                self::$view_administrador
                    ->home()
                    ->render();
            },
            function ($e) {
                self::$view_general
                    ->error404()
                    ->render();
            }
        );
    }

    /**
     * /admin/moderadores
     *
     * Muestra una lista de todos los moderadores.
     * Sólo el administrador tiene acceso.
     * @param $data
     */
    public static function moderadores($data) {
        self::admin_do(
            function () use ($data) {
                self::$view_moderador
                    ->listAll()
                    ->render();
            },
            function ($e) {
                self::$view_general
                    ->error404()
                    ->render();
            }
        );
    }


    /**
     * /admin/moderadores/nuevo
     *
     * Crea un nuevo moderador.
     * La petición se debe hacer vía post.
     * Sólo un administrador puede realizar esta acción.
     * @param $data
     */
    public static function moderadores_nuevo($data) {
        self::admin_do (
            function () use ($data) {
                if (!empty($data) and Tools::check_method('post')) {
                    $id = $data['id'];
                    $id_laboratorio = $data['id_laboratorio'];
                    $moderador = Moderador::getNewInstace($id, $id_laboratorio);
                    $moderador->save();
                    self::$view_moderador
                        ->getList(['id' => $id])
                        ->render();
                } else {
                    self::$view_general
                        ->error404()
                        ->render();
                }
            },
            function ($e) {
                self::$view_general
                    ->error_json_default($e)
                    ->render();
            }
        );
    }

    /**
     * /admin/moderadores/actualizar
     *
     * Editar la información de un moderador.
     * La petición se debe hacer vía post.
     * Sólo un administrador puede realizar esta acción.
     * @param $data
     */
    public static function moderadores_actualizar($data) {
        self::admin_do(
            function () use ($data) {
                if (!empty($data) and Tools::check_method('post')) {
                    $id = $data['id'];
                    $moderador = Moderador::getInstance($id);
                    $id_laboratorio = $data['id_laboratorio'];
                    $moderador->id_laboratorio = $id_laboratorio;
                    $moderador->save();
                    self::$view_moderador
                        ->getList(['id' => $id])
                        ->render();
                } else {
                    self::$view_general
                        ->error404()
                        ->render();
                }
            },
            function ($e) {
                self::$view_general
                    ->error_json_default($e)
                    ->render();
            }
        );
    }


    /**
     * /admin/moderadores/eliminar
     *
     * Elimina un moderador del sistema.
     * La petición se debe hacer vía post.
     * Sólo un administrador puede realizar esta acción.
     * @param $data
     */
    public static function moderadores_eliminar($data) {
        self::admin_do(
            function () use ($data) {
                if (!empty($data) and Tools::check_method('post')) {
                    $id = $data['id'];
                    $moderador = Moderador::getInstance($id);
                    $moderador->delete();
                    self::$view_general
                        ->success_json()
                        ->render();
                } else {
                    self::$view_general
                        ->error404()
                        ->render();
                }
            },
            function ($e) {
                self::$view_general
                    ->error_json_default($e)
                    ->render();
            }
        );
    }

    /**
     * /admin/moderadores/filtrar
     *
     * Regresa todos los moderadores que coinciden
     * con los criterios de búsqueda.
     * La petición se hace vía get.
     * Sólo el administrador puede realizar esta acción.
     * @param $data
     */
    public static function moderadores_filtrar($data) {
        self::admin_do(
            function () use ($data) {
                if (!empty($data) and Tools::check_method('post')) {
                    $id = $data['id'];
                    self::$view_moderador
                        ->getAll(['id' => $id])
                        ->render();
                } else {
                    self::$view_general
                        ->error404()
                        ->render();
                }
            },
            function ($e) {
                self::$view_general
                    ->error404()
                    ->render();
            }
        );
    }

    /**
     * /admin/lectores-qr
     *
     * Muestra una lista de todos los lectores QR.
     * Sólo el administrador tiene acceso.
     * @param $data
     */
    public static function lectores($data) {
        self::admin_do(
            function () use ($data) {
                self::$view_lector_qr
                    ->listAll()
                    ->render();
            },
            function () {
                self::$view_general
                    ->error404()
                    ->render();
            }
        );
    }


    /**
     * /admin/lectores-qr/nuevo
     *
     * Crea un nuevo lector QR.
     * La petición se debe hacer vía post.
     * Sólo un administrador puede realizar esta acción.
     * @param $data
     */
    public static function lectores_nuevo($data) {
        self::admin_do(
            function () use ($data) {
                if (!empty($data) and Tools::check_method('post')) {
                    $mac = $data['mac'];
                    $ip = $data['ip'];
                    $id_laboratorio = $data['id_laboratorio'];
                    $lector = LectorQr::getNewInstance($mac, $ip, $id_laboratorio);
                    $lector->save();
                    self::$view_lector_qr
                        ->getList(['mac' => $mac])
                        ->render();
                } else {
                    self::$view_general
                        ->error404()
                        ->render();
                }
            },
            function ($e) {
                self::$view_general
                    ->error_json_default($e)
                    ->render();
            }
        );
    }

    /**
     * /admin/lectores-qr/actualizar
     *
     * Edita la información de un lector.
     * Si el valor de new_token es true se genera un nuevo
     * token para el lector.
     * La petición se debe hacer vía post.
     * Sólo un administrador puede realizar esta acción.
     * @param $data
     */
    public static function lectores_actualizar($data) {
        self::admin_do(
            function () use ($data) {
                if (!empty($data) and Tools::check_method('post')) {
                    $mac = $data['mac'];
                    $lector = LectorQr::getInstance($mac);
                    if (array_key_exists('ip', $data)) {
                        $lector->ip = $data['ip'];
                    }
                    if (array_key_exists('id_laboratorio', $data)) {
                        $lector->id_laboratorio = $data['id_laboratorio'];
                    }
                    $lector->save();
                    self::$view_lector_qr
                        ->getList(['mac' => $mac])
                        ->render();
                } else {
                    self::$view_general
                        ->error404()
                        ->render();
                }
            },
            function ($e) {
                self::$view_general
                    ->error_json_default($e)
                    ->render();
            }
        );
    }


    public static function lectores_actualizar_token($data) {
        self::admin_do(
            function () use ($data) {
                if (!empty($data) and Tools::check_method('post')) {
                    $mac = $data['mac'];
                    $lector = LectorQr::getInstance($mac);
                    $lector->renovarToken();
                    $lector->save();
                    self::$view_lector_qr
                        ->getList(['mac' => $mac])
                        ->render();
                } else {
                    self::$view_general
                        ->error404()
                        ->render();
                }
            },
            function ($e) {
                self::$view_general
                    ->error_json_default($e)
                    ->render();
            }
        );
    }

    /**
     * Elimina un lector del sistema.
     * La petición se debe hacer vía post.
     * Sólo un administrador puede realizar esta acción.
     * @param $data
     */
    public static function lectores_eliminar($data) {
        self::admin_do(
            function () use ($data) {
                if (!empty($data) and Tools::check_method('post')) {
                    $mac = $data['mac'];
                    $lector = LectorQr::getInstance($mac);
                    $lector->delete();
                }
                self::$view_lector_qr
                    ->listAll()
                    ->render();
            },
            function () {
                self::$view_general
                    ->error404()
                    ->render();
            }
        );
    }

    public static function verificarmod($data){
        try {
            if (!empty($data) and Tools::check_method('post')) {
                $cedula = $data['cedula'];
                $sesion = Moderador::verificarMod($cedula);
                self::$view_general
                    ->success_json($sesion)
                    ->render();
            } else {
                self::$view_general
                    ->error404()
                    ->render();
            }
        } catch (\Exception $e) {
            self::$view_general
                ->error_json_default($e)
                ->render();
        }
    }

    /**
     * URLS para el direccionamiento (url routing).
     * @return array
     */
    public static function urls() {
        $class_name =  __CLASS__;
        return [
            '/^$/i' => "{$class_name}::moderadores",
            '/^moderadores\//i' => [
                '/^$/i' => "{$class_name}::moderadores",
                '/^nuevo\/$/i' => "{$class_name}::moderadores_nuevo",
                '/^actualizar\/$/i' => "{$class_name}::moderadores_actualizar",
                '/^eliminar\/$/i' => "{$class_name}::moderadores_eliminar",
                '/^filtrar\/$/i' => "{$class_name}::moderadores_filtrar",
            ],
            '/^lectores-?qr\//i' => [
                '/^$/i' => "{$class_name}::lectores",
                '/^nuevo\/$/i' => "{$class_name}::lectores_nuevo",
                '/^actualizar\/$/i' => "{$class_name}::lectores_actualizar",
                '/^actualizar-token\/$/i' => "{$class_name}::lectores_actualizar_token",
                '/^eliminar\/$/i' => "{$class_name}::lectores_eliminar",
            ],
            '/^login-mod\/$/i' => "{$class_name}::verificarmod",
        ];
    }
}

AdministradorSrv::init();

/* ¿Dónde está Alicia?       */
/*                 Si la ves */
/*  ()()      dile que estoy */
/*  ('.')        esperándola */
/*  (()()              aquí. */
/* *(_()()                   */

