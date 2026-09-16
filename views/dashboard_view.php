<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Operaciones - Banco POO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
    <div class="container" style="max-width: 900px;">
        
        <!-- Barra superior -->
        <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white shadow-sm rounded">
            <div>
                <h4 class="mb-0 text-primary">Bienvenido, <?= htmlspecialchars($datos['titular']) ?></h4>
                <small class="text-muted">Tipo de Cuenta: <span class="badge bg-secondary"><?= ucfirst($datos['tipo']) ?></span></small>
            </div>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Cerrar Sesión</a>
        </div>

        <?php if (!empty($datos['mensaje'])): ?>
            <div class="alert <?= $datos['tipoAlerta'] ?>" role="alert">
                <?= $datos['mensaje'] ?>
            </div>
        <?php endif; ?>

        <!-- Saldo Actual -->
        <div class="card shadow mb-4 text-center">
            <div class="card-body">
                <h5 class="text-muted">Su saldo actual disponible es:</h5>
                <h1 class="display-4 text-success fw-bold">$<?= number_format($datos['saldo'], 2) ?></h1>
            </div>
        </div>

        <!-- Formularios de Depósito y Retiro -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Realizar Depósito</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="accion" value="depositar">
                            <div class="mb-3">
                                <label class="form-label">Monto a depositar ($):</label>
                                <input type="number" step="0.01" name="monto" class="form-control" placeholder="0.00" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Depositar</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Realizar Retiro</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="accion" value="retirar">
                            <div class="mb-3">
                                <label class="form-label">Monto a retirar ($):</label>
                                <input type="number" step="0.01" name="monto" class="form-control" placeholder="0.00" required>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">Retirar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Depósitos -->
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Historial de Depósitos Realizados</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Titular</th>
                                <th>Monto</th>
                                <th>Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($datos['depositos'])): ?>
                                <?php foreach ($datos['depositos'] as $deposito): ?>
                                    <tr>
                                        <td><?= $deposito['id'] ?></td>
                                        <td><?= htmlspecialchars($deposito['titular']) ?></td>
                                        <td><span class="text-success fw-bold">+$<?= number_format($deposito['monto'], 2) ?></span></td>
                                        <td><?= $deposito['fecha'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay depósitos registrados todavía.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Historial de Retiros -->
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Historial de Retiros Realizados</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Titular</th>
                                <th>Monto</th>
                                <th>Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($datos['retiros'])): ?>
                                <?php foreach ($datos['retiros'] as $retiro): ?>
                                    <tr>
                                        <td><?= $retiro['id'] ?></td>
                                        <td><?= htmlspecialchars($retiro['titular']) ?></td>
                                        <td><span class="text-danger fw-bold">-$<?= number_format($retiro['monto'], 2) ?></span></td>
                                        <td><?= $retiro['fecha'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No hay retiros registrados todavía.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>
</html>