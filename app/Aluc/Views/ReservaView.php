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
        $reservas = Reserva::getReservaEstado($_SESSION['id'], 'reservado');
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
        return $this->listAll([
            'reservas' => [$reserva]
        ]);
    }

    public function listReservasUsuario($user_id, $type = 'all') {
        if ($type === 'all') {
            return $this->listAll([
                'reservas' => Reserva::getReservaUsuario($user_id)
            ]);
        } else if ($type === 'new') {
            $type = 'reservado';
            return $this->listAll([
                'reservas' => Reserva::getReservaEstado($user_id, $type)
            ]);
        } else if ($type === 'old') {
            return $this->listAll([
                'reservas' => array_merge(
                    Reserva::getReservaEstado($user_id, 'cancelado'),
                    Reserva::getReservaEstado($user_id, 'cancelado_ausencia'),
                    Reserva::getReservaEstado($user_id, 'procesado')
                )
            ]);
        }
    }

    public function codigo_qr($reserva) {
        $this->setTemplate(
            ['reserva' => $reserva],
            'reservas/codigo-qr.php'
        );
        return $this;
    }
}
