<?php
namespace Aluc\Models;

/**
 * Representación de una persona en el sistema.
 */
abstract class Persona implements DBItem {
    public $id;
    public $nombre;
    protected $is_save = true;

    protected function __construct($id, $nombre, $is_save) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->is_save = $is_save && true;
    }

}