<?php
namespace Aluc\Models;

/**
 * Usuario que puede hacer reservas en el sistema.
 */
class Usuario extends Persona {
    protected function __construct($id, $nombre) {
        parent::__construct($id, $nombre);
    }

    public static function getInstance($id) {
    }

    public function save()
    {
        // TODO: Implement save() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }
}