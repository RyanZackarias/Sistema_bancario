<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../classes/CuentaAhorros.php';
require_once __DIR__ . '/../classes/CuentaCorriente.php';

class BancoController {
    
    public function iniciarSesion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Conexion::conectar();
            $titular = trim($_POST['titular'] ?? '');
            $password = $_POST['password'] ?? '';

            $stmt = $db->prepare("SELECT * FROM cuentas WHERE titular = ?");
            if (!$stmt) {
                die("Error en la consulta SQL: " . $db->error);
            }
            $stmt->bind_param("s", $titular);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($row = $res->fetch_assoc()) {
                if ($row['password'] === $password) {
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['titular'] = $row['titular'];
                    $_SESSION['tipo'] = $row['tipo'];
                    
                    header("Location: dashboard.php");
                    exit();
                } else {
                    return "Contraseña incorrecta.";
                }
            } else {
                return "Titular no encontrado.";
            }
        }
        return "";
    }

    public function procesarOperacion() {
        if (!isset($_SESSION['id'])) {
            header("Location: index.php");
            exit();
        }

        $db = Conexion::conectar();
        $id = $_SESSION['id'];
        $mensaje = '';
        $tipoAlerta = 'alert-info';

        $stmt = $db->prepare("SELECT * FROM cuentas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        $cuenta = $row['tipo'] === 'ahorros' 
            ? new CuentaAhorros($row['id'], $row['titular'], (float)$row['saldo'])
            : new CuentaCorriente($row['id'], $row['titular'], (float)$row['saldo']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';
            $monto = (float)($_POST['monto'] ?? 0);

            if ($accion === 'retirar') {
                if ($cuenta->retirar($monto)) {
                    $nuevoSaldo = $cuenta->getSaldo();
                    $update = $db->prepare("UPDATE cuentas SET saldo = ? WHERE id = ?");
                    $update->bind_param("di", $nuevoSaldo, $id);
                    $update->execute();

                    $insertLog = $db->prepare("INSERT INTO retiros_historial (titular, monto) VALUES (?, ?)");
                    $insertLog->bind_param("sd", $row['titular'], $monto);
                    $insertLog->execute();

                    $mensaje = "¡Retiro exitoso de \$$monto! Saldo actual: \$$nuevoSaldo";
                    $tipoAlerta = 'alert-success';
                } else {
                    $mensaje = "Fondos insuficientes o límite excedido para este tipo de cuenta.";
                    $tipoAlerta = 'alert-danger';
                }
            } elseif ($accion === 'depositar') {
                if ($monto > 0) {
                    $nuevoSaldo = $cuenta->getSaldo() + $monto;
                    $update = $db->prepare("UPDATE cuentas SET saldo = ? WHERE id = ?");
                    $update->bind_param("di", $nuevoSaldo, $id);
                    $update->execute();

                    // Guardar registro en el historial de depósitos
                    $insertLog = $db->prepare("INSERT INTO depositos_historial (titular, monto) VALUES (?, ?)");
                    $insertLog->bind_param("sd", $row['titular'], $monto);
                    $insertLog->execute();

                    $mensaje = "¡Depósito exitoso de \$$monto! Saldo actual: \$$nuevoSaldo";
                    $tipoAlerta = 'alert-success';
                } else {
                    $mensaje = "Ingrese un monto válido para depositar.";
                    $tipoAlerta = 'alert-warning';
                }
            }
        }

        $stmtRefresh = $db->prepare("SELECT saldo FROM cuentas WHERE id = ?");
        $stmtRefresh->bind_param("i", $id);
        $stmtRefresh->execute();
        $saldoActual = $stmtRefresh->get_result()->fetch_assoc()['saldo'];

        // Obtener historiales independientes
        $historialRetiros = $db->query("SELECT * FROM retiros_historial ORDER BY fecha DESC");
        $retiros = $historialRetiros ? $historialRetiros->fetch_all(MYSQLI_ASSOC) : [];

        $historialDepositos = $db->query("SELECT * FROM depositos_historial ORDER BY fecha DESC");
        $depositos = $historialDepositos ? $historialDepositos->fetch_all(MYSQLI_ASSOC) : [];

        return [
            'titular' => $_SESSION['titular'],
            'tipo' => $_SESSION['tipo'],
            'saldo' => $saldoActual,
            'mensaje' => $mensaje,
            'tipoAlerta' => $tipoAlerta,
            'retiros' => $retiros,
            'depositos' => $depositos
        ];
    }
}