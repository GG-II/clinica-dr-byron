<?php
/**
 * FOOTER DEL SISTEMA
 * Sistema de Gestión Clínica - Clínica Médica de la Mujer
 * 
 * Footer con scripts y cierre de HTML
 * Incluir al final de todas las páginas
 * 
 * @author Gerbert David García Loaiza - GG-Systems
 * @version 1.0
 */

defined('ACCESS_GRANTED') or die('Acceso denegado');
?>
            </div><!-- .page-content -->
        </div><!-- .main-content -->
    </div><!-- .main-wrapper -->
    
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript adicional por página -->
    <?php if (isset($additional_js)): ?>
        <?= $additional_js ?>
    <?php endif; ?>
    
    <!-- JavaScript global -->
    <script>
        // Confirmación para eliminar registros
        function confirmarEliminar(mensaje = '¿Está seguro de eliminar este registro?') {
            return confirm(mensaje);
        }
        
        // Auto-hide de alertas después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
        
        // Helper para formatear moneda
        function formatearMoneda(numero) {
            return 'Q ' + parseFloat(numero).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
        
        // Helper para formatear fecha
        function formatearFecha(fecha) {
            const d = new Date(fecha);
            const dia = String(d.getDate()).padStart(2, '0');
            const mes = String(d.getMonth() + 1).padStart(2, '0');
            const anio = d.getFullYear();
            return `${dia}/${mes}/${anio}`;
        }
    </script>
</body>
</html>