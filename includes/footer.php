    <footer class="footer" style="text-align:center; padding: 20px; margin-top: 30px; color: var(--muted); border-top: 1px solid var(--border);">
        <div style="margin-bottom: 10px;">
            <strong style="color: var(--text);"><i class="fa-solid fa-users"></i> Kelompok 1</strong>
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 8px 16px; margin-top: 8px;">
                <span>• Mohamad Taufik Wibowo</span>
                <span>• Fabian Jason Song</span>
                <span>• Ridwan Abdillah</span>
                <span>• Reiksa Azra Octavian</span>
            </div>
        </div>
        <p style="margin-top: 10px;">
            &copy; <?php echo date('Y'); ?> Helpdesk System. Didesain dengan <i class="fa-solid fa-heart" style="color: #ef4444;"></i>
        </p>
    </footer>

    <script>
        (function(){
            const savedTheme = localStorage.getItem('appTheme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
                if (document.body) document.body.setAttribute('data-theme', savedTheme);
            }
        })();
    </script>