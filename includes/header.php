<?php
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-danger shadow-sm sticky-top">

    <div class="container">

        <!-- Brand -->
        <a class="navbar-brand fw-bold fs-4" href="index.php">
            <i class="bi bi-instagram"></i>
            MehrsamInsta
        </a>

        <!-- Mobile Toggle -->
        <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#miNavbar"
                aria-controls="miNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>

        </button>

        <!-- Navigation -->
        <div class="collapse navbar-collapse" id="miNavbar">

            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        <i class="bi bi-house-door"></i>
                        خانه
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-compass"></i>
                        کاوش
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="create.php">
                        <i class="bi bi-plus-square"></i>
                        ساخت پست
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-heart"></i>
                        علاقه‌مندی‌ها
                    </a>
                </li>

            </ul>

            <!-- Authentication -->
            <div class="d-flex align-items-center gap-2 flex-wrap">

                <?php if (Auth::isLoggedIn()): ?>

                    <a href="profile.php"
                       class="btn btn-outline-light">
                        <i class="bi bi-person-circle"></i>
                        پروفایل
                    </a>

                    <a href="logout.php"
                       class="btn btn-light text-danger">
                        <i class="bi bi-box-arrow-right"></i>
                        خروج
                    </a>

                <?php else: ?>

                    <a href="login.php"
                       class="btn btn-outline-light">
                        <i class="bi bi-box-arrow-in-left"></i>
                        ورود
                    </a>

                    <a href="signup.php"
                       class="btn btn-light text-danger">
                        <i class="bi bi-person-plus"></i>
                        ثبت‌نام
                    </a>

                <?php endif; ?>

            </div>

            <!-- More Dropdown -->
            <div class="dropdown ms-lg-3 mt-2 mt-lg-0">

                <button
                        class="btn btn-outline-light dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                    <i class="bi bi-three-dots"></i>
                    بیشتر

                </button>

                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-question-circle"></i>
                            راهنما
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-code-slash"></i>
                            توسعه‌دهندگان
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="bi bi-info-circle"></i>
                            درباره ما
                        </a>
                    </li>

                </ul>

            </div>

        </div>

    </div>

</nav>
