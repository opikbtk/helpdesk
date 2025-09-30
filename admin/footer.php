</div> <footer class="footer">
                <div style="margin-bottom: 15px;">
                    <p style="font-weight: 600; font-size: 15px; color: var(--dark-color); margin-bottom: 10px;">
                        <i class="fa-solid fa-users"></i> Kelompok 1
                    </p>
                    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 8px 20px; max-width: 900px; margin: 0 auto;">
                        <p style="margin: 0;">• Mohamad Taufik Wibowo</p>
                        <p style="margin: 0;">• Fabian Jason Song</p>
                        <p style="margin: 0;">• Ridwan Abdillah</p>
                        <p style="margin: 0;">• Reiksa Azra Octavian</p>
                    </div>
                </div>
                <p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color);">
                    &copy; <?php echo date('Y'); ?> Helpdesk System. Didesain dengan <i class="fa-solid fa-heart" style="color: #ef4444;"></i>
                </p>
            </footer>
        </main>
    </div>

    <script>
        // Toggle sidebar (persist state)
        function toggleSidebar() {
            const el = document.getElementById('sidebar');
            el.classList.toggle('active');
            localStorage.setItem('adminSidebarActive', el.classList.contains('active') ? '1' : '0');
        }
        // Theme toggle (persist across app)
        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('appTheme', next);
            localStorage.setItem('adminTheme', next);
        }
        // Init on load
        (function(){
            const savedTheme = localStorage.getItem('appTheme') || localStorage.getItem('adminTheme');
            if (savedTheme) document.documentElement.setAttribute('data-theme', savedTheme);
            const sidebarActive = localStorage.getItem('adminSidebarActive');
            if (sidebarActive === '1') {
                const el = document.getElementById('sidebar');
                if (el) el.classList.add('active');
            }
        })();
    </script>
</body>
</html>