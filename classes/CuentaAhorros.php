<?php
require_once __DIR__ . '/CuentaBancaria.php';

/**
 * HERENCIA:
 * La clase 'CuentaAhorros' hereda todas las propiedades y métodos 
 * de la clase padre 'CuentaBancaria' utilizando la palabra clave 'extends'.
 */
class CuentaAhorros extends CuentaBancaria {
    
/**
* POLIMORFISMO:
* Aqui sobrescribimos ('override') el metodo abstracto retirar() 
* adaptandolo especificamente para las reglas de una cuenta de ahorros 
* por ejemplo, tiene un limite estricto de retiro de hasta $1000.
*/
    public function retirar(float $monto): bool {
        if ($monto <= 1000.00 && $this->getSaldo() >= $monto) {
            $this->ajustarSaldo(-$monto);
            return true;
        }
        return false;
    }
}