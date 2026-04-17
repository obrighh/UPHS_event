<?php
    if(isset($_POST['signout'])){
        session_destroy();
        echo 
        '
            <script> window.location = "../../../login.php"; </script>
        ';
    }
    if (!function_exists('admin_contact_unread')) {
        require_once __DIR__ . '/../../../actions/admin_contact_unread.php';
    }
    $topnavInquiryUnread = 0;
    if (isset($conn) && $conn instanceof mysqli) {
        $topnavInquiryUnread = admin_contact_unread($conn);
    }
?>

<nav
            class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
            id="layout-navbar">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                <i class="icon-base bx bx-menu icon-md"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center justify-content-end flex-grow-1" id="navbar-collapse">
              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <li class="nav-item me-2 me-xl-3">
                  <a class="nav-link p-2 position-relative" href="messages.php" title="Website inquiries">
                    <i class="icon-base bx bx-envelope icon-md"></i>
                    <?php if ($topnavInquiryUnread > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.75rem; padding: 0.35rem 0.55rem; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                      <?php echo (int) $topnavInquiryUnread; ?>
                      <span class="visually-hidden">new inquiries</span>
                    </span>
                    <?php endif; ?>
                  </a>
                </li>

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a
                    class="nav-link dropdown-toggle hide-arrow p-0"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img src="../assets/img/avatars/5.png" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <img src="../assets/img/avatars/5.png" alt class="w-px-40 h-auto rounded-circle" />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-0">John Doe</h6>
                            <small class="text-body-secondary">Admin</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="accounts.php">
                        <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
                      </a>
                    </li>

                    <li>
                      <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                      <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                      <button name="signout" type="submit" class="dropdown-item">
                        <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Log Out</span>
                      </button>
                      </form>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav>