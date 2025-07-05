<!-- header section starts  -->

<header class="header">
    <nav class="navbar nav-2">
        <section class="flex">
            <div id="menu-btn" class="fas fa-bars"></div>

            <div class="menu">
                <ul style="direction: rtl;">
                    <li><a href=" #">الحساب <i class="fas fa-angle-down" style="margin-right: 10px;"></i></a>
                        <ul>
                            <?php if ($user_id == '') { ?>
                            <li><a href="login.php">تسجيل دخول</a></li>
                            <li><a href="register.php">انشاء حساب جديد</a>
                                <?php } ?></li>
                            <?php if ($user_id != '') { ?>
                            <li><a href="update.php">تحديث الملف الشخصي</a></li>
                            <li><a href="dashboard.php">لوحة تحكم</a></li>
                            <?php if ($user_type != "client") { ?>
                            <li><a href="post_property.php">إضافة دورة تدريبية</a></li>
                            <?php } ?>
                            <li><a href="components/user_logout.php"
                                    onclick="return confirm('logout from this website?');">تسجيل خروج</a>
                                <?php } ?>
                            </li>
                        </ul>
                    </li>
                    <li><a href="#">خيارات<i class="fas fa-angle-down" style="margin-right: 10px;"></i></a>
                        <ul>
                            <li><a href="listings.php">جميع الدورات</a></li>
                            <li><a href="listing_firm.php">جميع المؤسسات</a></li>
                        </ul>
                    </li>
                    <li><a href="#">مساعدة<i class="fas fa-angle-down" style="margin-right: 10px;"></i></a>
                        <ul>
                            <li><a href="about.php">معلومات عنا</a></i></li>
                            <li><a href="contact.php">اتصل بنا</a></i></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <a href="home.php" class="logo">منصة التدريب الميداني<i class="fa-solid fa-graduation-cap"
                    style="margin-left: 10px;"></i></a>

        </section>
    </nav>

</header>

<!-- header section ends -->