<?php
/**
 * LabVale Admin - Footer Component
 */
?>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var toggle = document.getElementById('adminSidebarToggle');
        var backdrop = document.getElementById('adminSidebarBackdrop');
        if (toggle) {
            toggle.addEventListener('click', function() {
                document.body.classList.toggle('admin-sidebar-open');
            });
        }
        if (backdrop) {
            backdrop.addEventListener('click', function() {
                document.body.classList.remove('admin-sidebar-open');
            });
        }
    });
    </script>
</body>
</html>
