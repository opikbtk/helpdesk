</div> <footer class="footer">
                <p>&copy; <?php echo date('Y'); ?> Helpdesk System. Didesain dengan ❤️.</p>
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