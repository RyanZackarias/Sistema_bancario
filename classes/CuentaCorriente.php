<?php
require_once __DIR__ . '/CuentaBancaria.php';

/**
 * HERENCIA:
 * Al igual que la clase anterior hereda de 'CuentaBancaria'.
 */
class CuentaCorriente extends CuentaBancaria {
    
    /**
     * POLIMORFISMO:
     * Sobrescribe el mismo método retirar(), pero con reglas de negocio 
     * diferentes.
     */
    public function retirar(float $monto): bool {
        if ($this->getSaldo() + 500.00 >= $monto) {
            $this->ajustarSaldo(-$monto);
            return true;
        }
        return false;
    }
}