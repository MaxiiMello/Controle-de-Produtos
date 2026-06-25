</main><!-- .main-content -->
</div><!-- .main-wrapper -->

<script>
// ===== SIDEBAR TOGGLE (Mobile) =====
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}

// Fecha sidebar ao redimensionar para desktop
window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('show');
    }
});
</script>

<script src="../assets/js/sortable.js"></script>
</body>
</html>
