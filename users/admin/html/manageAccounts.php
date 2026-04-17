<?php
  session_start();

  require '../../../actions/conn.php';

  $able = 1;
  $u_id = $_SESSION['id'];

  $sql =
  "SELECT accounts.user_id, accounts.username, accounts.email, accounts.ut_id,
          CONCAT(accounts.f_name, ' ', accounts.m_name, ' ', accounts.l_name) AS fullname,
          accounts.sch_id, accounts.date_created, user_type.categories
   FROM accounts
   JOIN user_type ON accounts.ut_id = user_type.ut_id
   WHERE accounts.user_status = 1
  ";

  $stmt = $conn->prepare($sql);
  $stmt -> execute();

  $result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
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

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="../assets/js/config.js"></script>
  </head>

<style>
    /* Fix FullCalendar from overflowing badly on smaller columns */
  #adminCalendar .fc-toolbar-title {
    font-size: 1.25rem !important;
  }

  #adminCalendar .fc-button {
    padding: 0.25rem 0.75rem;
    font-size: 0.85rem;
  }

  @media (max-width: 768px) {
    #adminCalendar {
      font-size: 0.85rem;
    }
  }

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

            <div class="content-wrapper">
        
              <div class="container-xxl flex-grow-1 container-p-y">

                <h4 class="fw-bold py-3 mb-4"><i class="bx bx-id-card"></i>Manage Accounts</h4>
                <!-- Add Entry Button -->
                <div class="mb-3 text-end">
                  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLogbookModal">
                    <i class="bx bx-plus"></i> Add User
                  </button>
                </div>

                <!-- Add Entry Modal -->
                <div class="modal fade" id="addLogbookModal" tabindex="-1" aria-labelledby="addLogbookModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="../../../actions/admin_addUser.php" method="POST" class="modal-content">

                      <div class="modal-header">
                          <h5 class="modal-title">Add User</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>

                      <div class="modal-body">
                          <div class="mb-3">
                              <label class="form-label">Full Name</label>
                              <input type="text" name="fullname" class="form-control" placeholder="e.g., Juan Dela Cruz" required>
                          </div>
                          <div class="mb-3">
                              <label class="form-label">School Id</label>
                              <input type="text" name="sch_id" class="form-control" placeholder="" required>
                          </div>

                          <div class="mb-3">
                              <label class="form-label">Username</label>
                              <input type="text" name="username" class="form-control" required>
                          </div>

                          <div class="mb-3">
                              <label class="form-label">Email</label>
                              <input type="email" name="email" class="form-control" required>
                          </div>

                          <div class="mb-3">
                              <label class="form-label">Password</label>
                              <input type="password" name="password" class="form-control" required>
                          </div>

                          <div class="mb-3">
                              <label class="form-label">Role</label>
                              <select name="role" class="form-select" required>
                                  <option value="" disabled selected>Select a role</option>
                                  <option value="1">Admin</option>
                                  <option value="3">Organization member</option>
                              </select>
                          </div>
                      </div>

                      <div class="modal-footer">
                          <button type="submit" name="add_user" class="btn btn-success">Add User</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      </div>
                    </form>
                  </div>
                </div>

                <div class="card">
                  <div class="card-datatable table-responsive">
                    <table class="table table-striped table-hover table-bordered align-middle">
                      <thead class="table-light">
                        <tr>
                          <th>Id</th>
                          <th>Fullname</th>
                          <th>School Id</th>
                          <th>Username</th>
                          <th>Email</th>
                          <th>Role</th>
                          <th>Date Created</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>

                      <?php
                        $modals = ""; // Store modals separately

                        while($row = mysqli_fetch_assoc($result)) {
                          $user_id = $row['user_id'];
                          $fullname = htmlspecialchars($row['fullname']);
                          $sch_id = htmlspecialchars($row['sch_id']);
                          $username = htmlspecialchars($row['username']);
                          $email = htmlspecialchars($row['email']);
                          $ut_id = (int) $row['ut_id'];
                          $role = htmlspecialchars($row['categories']);
                          $date_created = htmlspecialchars($row['date_created']);
                          $selAdmin = $ut_id === 1 ? 'selected' : '';
                          $selOrg = $ut_id === 3 ? 'selected' : '';
                          

                          echo "<tr>
                            <td style='max-width: 150px;'>$user_id</td>
                            <td style='max-width: 130px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;'>
                              $fullname
                            </td>
                            <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>
                              $sch_id
                            </td>
                            <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>
                              $username
                            </td>
                            <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>
                              $email
                            </td>
                            <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>
                              $role
                            </td>
                            <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>
                              $date_created
                            </td>
                            <td>
                              <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editModal$user_id'><i class='tf-icons bx bx-edit'></i></button>
                              <button class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal$user_id'><i class='tf-icons bx bx-trash bx-lg'></i></button>
                            </td>
                          </tr>";

                          // Append modal HTML to $modals
                          $modals .= "
                          <div class='modal fade' id='editModal$user_id' tabindex='-1' aria-hidden='true'>
                              <div class='modal-dialog'>
                                  <form action='../../../actions/admin_editUser.php' method='POST' class='modal-content'>
                                      <div class='modal-header'>
                                          <h5 class='modal-title'>Edit User</h5>
                                          <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                      </div>
                                      <div class='modal-body'>
                                          <input type='hidden' name='userId' value='$user_id'>
                                          
                                          <div class='mb-3'>
                                              <label class='form-label'>Full Name</label>
                                              <input type='text' name='fullname' class='form-control' value='$fullname' required>
                                          </div>
                                          <div class='mb-3'>
                                              <label class='form-label'>School ID</label>
                                              <input type='text' name='sch_id' class='form-control' value='$sch_id' required>
                                          </div>
                                          
                                          <div class='mb-3'>
                                              <label class='form-label'>Username</label>
                                              <input type='text' name='username' class='form-control' value='$username' required>
                                          </div>
                                          
                                          <div class='mb-3'>
                                              <label class='form-label'>Email</label>
                                              <input type='email' name='email' class='form-control' value='$email' required>
                                          </div>

                                          <div class='mb-3'>
                                              <label class='form-label'>New Password <small class='text-muted'>(leave blank to keep current)</small></label>
                                              <input type='password' name='password' class='form-control'>
                                          </div>

                                          <div class='mb-3'>
                                              <label class='form-label'>Role</label>
                                              <select name='role' class='form-select' required>
                                                  <option value='1' $selAdmin>Admin</option>
                                                  <option value='3' $selOrg>Organization member</option>
                                              </select>
                                          </div>
                                      </div>
                                      <div class='modal-footer'>
                                          <button type='submit' name='update_user' class='btn btn-success'>Save Changes</button>
                                          <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                      </div>
                                  </form>
                              </div>
                          </div>

                          
                          <!-- Delete Modal -->
                          <div class='modal fade' id='deleteModal$user_id' tabindex='-1' aria-hidden='true'>
                            <div class='modal-dialog modal-dialog-centered'>
                              <form action='../../../actions/admin_archiveUser.php' method='POST' class='modal-content'>
                                <div class='modal-header'>
                                  <h5 class='modal-title'>Confirm Delete</h5>
                                  <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                </div>
                                <div class='modal-body'>
                                  <input type='hidden' name='delete_userId' value='$user_id'>
                                  <p>Are you sure you want to delete this user?</p>
                                  <p class='text-muted'><strong>User:</strong> $fullname</p>
                                  <p class='text-danger'><small>This action cannot be undone.</small></p>
                                </div>
                                <div class='modal-footer'>
                                  <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                  <button type='submit' name='delete_user' class='btn btn-danger'>Delete</button>
                                </div>
                              </form>
                            </div>
                          </div>
                          ";
                        }
                        ?>
                      </tbody>
                    </table>
                    <?php echo $modals; ?>
                  </div>
                </div>
              </div>
            </div>


            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
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
      const id = document.getElementById('4.4');
      const id2 = document.getElementById('4');
      if (id) id.classList.toggle('active');
      if (id2) {
        id2.classList.toggle('active');
        id2.classList.toggle('open');
      }
    </script>

</body>
</html>