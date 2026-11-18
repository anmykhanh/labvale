<?php
/**
 * LabVale - Footer Component
 */
?>
    </main>

    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand & Mission -->
                <div>
                    <a href="<?= url('/') ?>" class="brand-link" aria-label="LabVale Trang chủ">
                        <img src="<?= asset('images/logo-white.svg') ?>" alt="Logo LabVale" class="brand-logo-img" width="160" height="38">
                    </a>
                    <p class="footer-brand-desc">
                        <?= e(get_setting('footer_note', 'LabVale là dự án nghiên cứu và phát triển công nghệ độc lập tại Việt Nam, tập trung vào robot, thiết bị bay không người lái, trí tuệ nhân tạo và những giải pháp kỹ thuật có giá trị thực tiễn.')) ?>
                    </p>
                </div>

                <!-- Navigation Links -->
                <div>
                    <h3 class="footer-heading">Khám phá</h3>
                    <ul class="footer-links">
                        <li><a href="<?= url('/') ?>" class="footer-link">Trang chủ</a></li>
                        <li><a href="<?= url('/about') ?>" class="footer-link">Giới thiệu LabVale</a></li>
                        <li><a href="<?= url('/research-areas') ?>" class="footer-link">Lĩnh vực nghiên cứu</a></li>
                        <li><a href="<?= url('/projects') ?>" class="footer-link">Dự án & Thử nghiệm</a></li>
                        <li><a href="<?= url('/research') ?>" class="footer-link">Góc nghiên cứu kỹ thuật</a></li>
                        <li><a href="<?= url('/news') ?>" class="footer-link">Tin tức & Nhật ký</a></li>
                    </ul>
                </div>

                <!-- Research Areas -->
                <div>
                    <h3 class="footer-heading">Lĩnh vực trọng tâm</h3>
                    <ul class="footer-links">
                        <li><a href="<?= url('/research-areas/robotics') ?>" class="footer-link">Robotics</a></li>
                        <li><a href="<?= url('/research-areas/drone-uav') ?>" class="footer-link">Drone & UAV</a></li>
                        <li><a href="<?= url('/research-areas/artificial-intelligence') ?>" class="footer-link">Trí tuệ nhân tạo (AI)</a></li>
                        <li><a href="<?= url('/research-areas/embedded-iot') ?>" class="footer-link">Hệ thống nhúng & IoT</a></li>
                        <li><a href="<?= url('/research-areas/software-web') ?>" class="footer-link">Phần mềm & Hạ tầng Web</a></li>
                        <li><a href="<?= url('/research-areas/research-development') ?>" class="footer-link">Nghiên cứu & Đo kiểm R&D</a></li>
                    </ul>
                </div>

                <!-- Engineering & Open Science -->
                <div>
                    <h3 class="footer-heading">Kết nối khoa học</h3>
                    <ul class="footer-links">
                        <li>
                            <a href="<?= e(get_setting('github_url', 'https://github.com/labvale-rd')) ?>" target="_blank" rel="noopener noreferrer" class="footer-link">
                                GitHub Repository
                            </a>
                        </li>
                        <li>
                            <a href="mailto:<?= e(get_setting('contact_email', 'contact@labvale.org')) ?>" class="footer-link">
                                <?= e(get_setting('contact_email', 'contact@labvale.org')) ?>
                            </a>
                        </li>
                        <li><a href="<?= url('/contact') ?>" class="footer-link">Gửi thông điệp trao đổi</a></li>
                        <li><a href="<?= url('/privacy') ?>" class="footer-link">Chính sách quyền riêng tư</a></li>
                        <li><a href="<?= url('/admin') ?>" class="footer-link">Cổng quản trị nội bộ</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Row -->
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> LabVale. Toàn bộ nội dung và tài liệu thực nghiệm được bảo lưu bản quyền khoa học.</p>
                <div class="footer-legal-links">
                    <span>Research. Innovate. Create Value.</span>
                    <a href="<?= url('/privacy') ?>" class="footer-link">Bảo mật thông tin</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Client-side Scripts -->
    <script src="<?= asset('js/main.js') ?>" defer></script>
</body>
</html>
