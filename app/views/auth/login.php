<?php $titulo = 'Login - Sistema de Agendamento'; ?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-3">
                        <i class="fas fa-calendar-alt me-2"></i> Sistema de Agendamento
                    </h1>
                    <p class="text-muted">Faça login para acessar o sistema</p>
                </div>

                <?php if (isset($_SESSION['mensagem'])): ?>
                    <div class="alert alert-<?= $_SESSION['mensagem']['tipo'] === 'erro' ? 'danger' : 'success' ?> alert-dismissible fade show">
                        <?= $_SESSION['mensagem']['texto'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['mensagem']); ?>
                <?php endif; ?>

                <form action="/projeto-agendamento/public/login" method="post">
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuário</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="usuario" name="usuario" placeholder="nome.sobrenome" required autofocus>
                        </div>
                        <small class="form-text text-muted">Formato: nome.sobrenome</small>
                    </div>

                    <div class="mb-4">
                        <label for="senha" class="form-label">Senha</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="senha" name="senha" placeholder="4 primeiros dígitos do CPF" required>
                        </div>
                        <small class="form-text text-muted">Senha inicial: 4 primeiros dígitos do CPF</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i> Entrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>