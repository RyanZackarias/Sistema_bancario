<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Banco POO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow" style="width: 400px;">
        <div class="card-header bg-primary text-white text-center">
            <h3>Iniciar Sesión Bancaria</h3>
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Titular de la Cuenta:</label>
                    <input type="text" name="titular" class="form-control" placeholder="Ej. Ryan Cerezo" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Contraseña:</label>
                    <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg">Ingresar al Sistema</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>