<!-- =========================================
     FOOTER
     ========================================= -->
<footer id="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Brand -->
            <div class="footer-brand">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-logo">
                    <div class="nav-logo-icon">D</div>
                    <span class="nav-logo-text">Dave<span>IT</span></span>
                </a>
                <p>Your trusted partner for managed IT services, cybersecurity, and cloud solutions. Keeping businesses connected and secure since 2010.</p>
                <div class="footer-socials">
                    <a href="#" class="social-link" aria-label="LinkedIn">in</a>
                    <a href="#" class="social-link" aria-label="Twitter">𝕏</a>
                    <a href="#" class="social-link" aria-label="Facebook">f</a>
                    <a href="#" class="social-link" aria-label="YouTube">▶</a>
                </div>
            </div>

            <!-- Services -->
            <div class="footer-col">
                <h4>Services</h4>
                <ul>
                    <li><a href="#services">Managed IT Support</a></li>
                    <li><a href="#services">Cybersecurity</a></li>
                    <li><a href="#services">Cloud Solutions</a></li>
                    <li><a href="#services">Microsoft 365</a></li>
                    <li><a href="#services">Network Infrastructure</a></li>
                    <li><a href="#services">IT Consulting</a></li>
                </ul>
            </div>

            <!-- Company -->
            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="#">About Dave IT</a></li>
                    <li><a href="#">Our Team</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Case Studies</a></li>
                    <li><a href="#contact">Contact Us</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="footer-col">
                <h4>Contact</h4>
                <ul>
                    <li><a href="tel:+18003283483"><?php echo daveit_mod( 'daveit_phone', '+1 (800) 328-3483' ); ?></a></li>
                    <li><a href="mailto:hello@daveit.com">hello@daveit.com</a></li>
                    <li><a href="#">Support Portal</a></li>
                    <li><a href="#">Remote Assistance</a></li>
                </ul>
                <div style="margin-top:20px;">
                    <p style="font-size:0.82rem;color:var(--text-muted);">Emergency Support</p>
                    <a href="tel:+18003283483" style="font-size:1.1rem;font-weight:700;color:#fff;"><?php echo daveit_mod( 'daveit_phone', '+1 (800) 328-3483' ); ?></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date( 'Y' ); ?> Dave IT. All rights reserved.</p>
            <div class="footer-legal">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<button id="back-to-top" aria-label="Back to top">↑</button>

<?php wp_footer(); ?>
</body>
</html>
