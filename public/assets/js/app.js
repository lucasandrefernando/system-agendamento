/**
 * Script principal do sistema de agendamento de veículos
 */

// Executa quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function () {

    // Inicializa tooltips do Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Inicializa popovers do Bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Função para fechar alertas automaticamente após 5 segundos
    setTimeout(function () {
        var alertList = document.querySelectorAll('.alert');
        alertList.forEach(function (alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Adiciona classe 'active' ao item de menu atual
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        if (currentPath.includes(linkPath) && linkPath !== '/projeto-agendamento/') {
            link.classList.add('active');
        }
    });

    // Formata campos de data para formato brasileiro
    const formatarDataBr = (data) => {
        if (!data) return '';
        const dataObj = new Date(data);
        return dataObj.toLocaleDateString('pt-BR') + ' ' +
            dataObj.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    };

    // Formata campos de data na página
    document.querySelectorAll('.data-br').forEach(el => {
        const dataOriginal = el.textContent || el.innerText;
        el.textContent = formatarDataBr(dataOriginal);
    });

    // Adiciona confirmação para ações importantes
    document.querySelectorAll('.confirmar-acao').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const href = this.getAttribute('href');
            const mensagem = this.getAttribute('data-mensagem') || 'Tem certeza que deseja realizar esta ação?';
            const titulo = this.getAttribute('data-titulo') || 'Confirmação';

            Swal.fire({
                title: titulo,
                text: mensagem,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, confirmar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed && href) {
                    window.location.href = href;
                }
            });
        });
    });

    // Melhora a experiência em dispositivos móveis
    if (window.innerWidth < 768) {
        // Fecha o menu após clicar em um item (em dispositivos móveis)
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const navbarToggler = document.querySelector('.navbar-toggler');
                if (navbarToggler && !navbarToggler.classList.contains('collapsed')) {
                    navbarToggler.click();
                }
            });
        });
    }
});