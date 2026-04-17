<?php
  require '../../../actions/view_organizations.php';
?>

<!DOCTYPE html>
<html
  lang="en"
  class="layout-menu-fixed layout-compact"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Admin</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- endbuild -->

    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="../assets/css/organization.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="../assets/js/config.js"></script>
  </head>

<style>
</style>

<body>
    <!-- Layout wrapper -->
   <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <?php
          include 'sidebar.php';
        ?>

        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <?php
            include 'topnav.php';
          ?>


            <!-- Content wrapper -->
            <div class="content-wrapper">

              <div class="container-xxl flex-grow-1 container-p-y">

                <h4 class="fw-bold py-3 mb-4"><i class="bx bx-group"></i>Organizations</h4>
                
                <div class="d-flex flex-wrap gap-3">

                  <!-- Add Organization Card -->
                  <div class="card" style="width: 250px; height: 250px; position: relative; overflow: hidden; cursor: pointer; border: 2px dashed #ccc;" data-bs-toggle="modal" data-bs-target="#addOrgModal">
                    <div class="d-flex align-items-center justify-content-center w-100 h-100">
                      <i class="bx bx-plus" style="font-size: 80px; color: #999;"></i>
                    </div>
                  </div>

                  <?php
                    while($row = mysqli_fetch_assoc($result)){
                      echo 
                      '
                      <a id="" href="org_activities.php?id='.$row['org_id'].'" class="text-decoration-none d-block">
                        <div class="card" style="width: 250px; height: 250px; position: relative; overflow: hidden; cursor: pointer;">
                          <img src="../img/school_logo.png" class="image" alt="'.$row['org_name'].'">
                          <div class="position-absolute bottom-0 w-100 text-white p-4 bg-dark org-card">
                            <p class="mb-0"> '. $row['org_name'] .'</p>
                          </div>
                        </div>
                      </a>
                      ';
                    }
                  ?>
                </div>
              </div>
              <!-- / Footer -->
              <div class="content-backdrop fade">
              </div>
            </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>
      
      <!-- Add Organization Modal -->
      <div class="modal fade" id="addOrgModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <form action="../../../actions/dean_addOrg.php" method="POST" enctype="multipart/form-data" class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-semibold">
                <i class="bx bx-plus-circle me-2"></i>Add New Organization
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Organization Name <span class="text-danger">*</span></label>
                <input type="text" name="org_name" class="form-control" placeholder="Enter organization name" required>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Organization Logo</label>
                <input type="file" name="org_logo" class="form-control" accept="image/*">
                <small class="text-muted">Accepted formats: JPG, PNG, GIF (Max 2MB)</small>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" name="add_organization" class="btn btn-primary">
                <i class="bx bx-check me-1"></i>Add Organization
              </button>
            </div>
          </form>
        </div>
      </div>
      
      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->



    <!-- Core JS -->

    <script src="../assets/vendor/libs/jquery/jquery.js"></script>

    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>

    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->

    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/js/dashboards-analytics.js"></script>

    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
      
      const id = document.getElementById('1');
      const id2 = document.getElementById('1.6');

      id.classList.toggle('open');
      id2.classList.toggle('active');
    </script>

</body>
</html>