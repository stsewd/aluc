<?php
namespace Aluc\Views;

use Aluc\Models\LectorQr;

/**
 * Clase encargada de representar todos los objetos
 * relacionados con la clase LectorQr.
 */
class LectorQrView extends View {
    private static $instance = null;

    public static function getInstance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function listAll($data = []) {
        $data['lectores'] = LectorQr::getAll();
        $this->setTemplate(
            $data,
            'lectores.php'
        );
        return $this;
    }
}
