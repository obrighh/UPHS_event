<?php
   require '../../../actions/view_profile.php';
?>
<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <meta name="description" content="" />
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />
  <link rel="stylesheet" href="../assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>
</head>
<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <!-- Menu -->
    
      <?php
        include 'sidebar.php';
      ?>

      <!-- / Menu -->

      <div class="layout-page">
        <!-- Navbar -->
        <?php
          include 'topnav.php';
        ?>

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Account Settings Card -->
            <div class="card mb-4">
              <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4 pb-4 border-bottom">
                  <img src="../assets/img/avatars/5.png" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
                  <div class="button-wrapper">
                    <label for="upload" class="btn btn-primary me-3 mb-2" tabindex="0">
                      <span class="d-none d-sm-block">Upload new photo</span>
                      <i class="icon-base bx bx-upload d-block d-sm-none"></i>
                      <input type="file" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg" />
                    </label>
                    <button type="button" class="btn btn-outline-secondary account-image-reset mb-2">
                      <i class="icon-base bx bx-reset d-block d-sm-none"></i>
                      <span class="d-none d-sm-block">Reset</span>
                    </button>
                    <div class="text-muted">Allowed JPG or PNG.</div>
                  </div>
                </div>

                <form id="formAccountSettings" method="POST" onsubmit="return false" class="pt-4">
                  <div class="row g-2">
                    <div class="col-md-6">
                      <!-- First Name -->
                      <div class="col-md-10">
                        <label for="firstName" class="form-label">First Name</label>
                        <input class="form-control" type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($f_name, ENT_QUOTES, 'UTF-8'); ?>" autofocus />
                      </div>

                      <!-- Middle Name -->
                      <div class="col-md-10">
                        <label for="middleName" class="form-label">Middle Name</label>
                        <input class="form-control" type="text" name="middleName" id="middleName" value="<?php echo htmlspecialchars($m_name, ENT_QUOTES, 'UTF-8'); ?>" />
                      </div>

                      <!-- Last Name -->
                      <div class="col-md-10">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input class="form-control" type="text" name="lastName" id="lastName" value="<?php echo htmlspecialchars($l_name, ENT_QUOTES, 'UTF-8'); ?>" />
                      </div>
                    </div>

                    <div class="col-md-6">
                      <!-- Username -->
                      <div class="col-md-10">
                        <label for="username" class="form-label">Username</label>
                        <input class="form-control" type="text" id="username" name="username" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>" />
                      </div>

                      <!-- Password -->
                      <div class="col-md-10">
                        <label for="password" class="form-label">Password</label>
                        <input class="form-control" type="password" id="password" name="password" placeholder="Enter password" />
                      </div>

                      <!-- Confirm Password -->
                      <div class="col-md-10">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input class="form-control" type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm password" />
                      </div>
                    </div>
                  </div>

                  <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Save changes</button>
                    <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Delete Account Card -->
            <div class="card">
              <h5 class="card-header">Delete Account</h5>
              <div class="card-body">
                <div class="alert alert-warning mb-4">
                  <h5 class="alert-heading mb-1">Are you sure you want to delete your account?</h5>
                  <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
                </div>
                <form id="formAccountDeactivation" onsubmit="return false">
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="accountActivation" id="accountActivation" />
                    <label class="form-check-label" for="accountActivation">I confirm my account deactivation</label>
                  </div>
                  <button type="submit" class="btn btn-danger deactivate-account">Deactivate Account</button>
                </form>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Core JS -->
  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>

  <!-- Vendors JS -->
  <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

  <!-- Main JS -->
  <script src="../assets/js/main.js"></script>
  <script src="../assets/js/dashboards-analytics.js"></script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>

  <script>
      const id = document.getElementById('4');
      const id2 = document.getElementById('4.5')

      id.classList.toggle('active');
      id.classList.toggle('open');

      id2.classList.toggle('active');
    </script>
</body>
</html>
