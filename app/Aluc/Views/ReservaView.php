<?php
namespace Aluc\Views;

use Aluc\Models\Reserva;


class ReservaView extends View {
    private static $instance = null;

    public static function getInstance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function home($data = []) {
        $reservas = Reserva::getReservaUsuario($_SESSION['id']);
        $data['reservas'] = $reservas;
        $this->setTemplate(
            $data,
            'reservas/reservas.php'
        );
        return $this;
    }

    public function listAll($data) {
        $this->setTemplate(
            $data,
            'reservas/reservas-list.php'
        );
        return $this;
    }

    public function listReserva($id) {
        $reserva = Reserva::getInstance($id);
        var_dump($reserva);
        return $this->listAll([
            'reservas' => [$reserva]
        ]);
    }

    public function listReservasUsuario($user_id) {
        return $this->listAll([
            'reservas' => Reserva::getReservaUsuario($user_id)
        ]);
    }
}
