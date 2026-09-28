<?php
/**
 * CLASE ABSTRACTA 
 * Sirve como una plantilla general. No se puede instanciar directamente, 
 * obliga a las clases hijas a implementar sus propias reglas de negocio.
 */
abstract class CuentaBancaria {
    protected int $id;
    protected string $titular;
    
/**
* ENCAPSULAMIENTO:
* La propiedad '$saldo' es 'private', lo que significa que el saldo 
* no puede ser modificado directamente desde afuera (ej. $cuenta->saldo = 1000),
* protegiendo los datos de alteraciones incorrectas.
*/
    private float $saldo; 

    public function __construct(int $id, string $titular, float $saldo) {
        $this->id = $id;
        $this->titular = $titular;
        $this->saldo = max(0, $saldo);
    }

    // Metodo getter para consultar el saldo de forma segura
    public function getSaldo(): float {
        return $this->saldo;
    }

    // Metodo protegido para modificar el saldo controlado internamente
    protected function ajustarSaldo(float $monto): void {
        $this->saldo += $monto;
    }

    /**
     * METODO ABSTRACTO:
     * Obliga a cualquier tipo de cuenta hija a escribir su propia 
     * logica de validacion para el retiro.
     */
    abstract public function retirar(float $monto): bool;
}