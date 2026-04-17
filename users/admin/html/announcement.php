<?php
  session_start();
  require '../../../actions/conn.php';

  $u_id = $_SESSION['id']; 
  $able = 1;
  $sql = 
  "SELECT
        a_id
      , title
      , description
      , date
      , time

   FROM announcement

   WHERE a_status = ?
  ";

  $stmt = $conn->prepare($sql);
  $stmt -> bind_param("i", $able);
  $stmt->execute();

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

                <h4 class="fw-bold py-3 mb-2"><i class="bx bx-message-square"></i>Announcement</h4>

                <!-- Add Entry Button -->
                <div class="mb-3 text-end">
                  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLogbookModal">
                    <i class="bx bx-plus"></i> Post Announcement
                  </button>
                </div>

                <!-- Add Entry Modal -->
                <div class="modal fade" id="addLogbookModal" tabindex="-1" aria-labelledby="addLogbookModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="../../../actions/admin_addAnnounce.php" method="POST" class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="addLogbookModalLabel">Post Announcement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label for="f_name" class="form-label">Title</label>
                          <input type="text" name="title" id="announce_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                          <label for="activity" class="form-label">Description</label>
                          <textarea name="description" id="" class="form-control" style="height: 200px; resize: none;"></textarea>
                        </div>
                        <div class="mb-3">
                          <label for="date" class="form-label">Date</label>
                          <input type="date" name="date" id="date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                          <label for="time" class="form-label">Time</label>
                          <input type="time" name="time" id="time" class="form-control" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="submit_announcement" class="btn btn-success">Add Announcement</button>
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
                          <th>Title</th>
                          <th>Description</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
 
                        <?php
                          $modals = ""; 

                          while($row = mysqli_fetch_assoc($result)) {
                            $a_id = $row['a_id'];
                            $title = htmlspecialchars($row['title']);
                            $description = htmlspecialchars($row['description']);
                            $date = htmlspecialchars($row['date']);
                            $time = htmlspecialchars($row['time']);

                            echo "
                            <tr>
                              <td style='max-width: 150px;'>$title</td>
                              <td style='max-width: 130px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;'>
                                $description
                              </td>
                              <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>
                                $date
                              </td>
                              <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>
                                $time
                              </td>
                              <td>
                                <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editModal$a_id'><i class='tf-icons bx bx-edit'></i></button>
                                <button class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal$a_id'><i class='tf-icons bx bx-trash bx-lg'></i></button>
                              </td>
                            </tr>";

                            // Append modal HTML to $modals
                            $modals .= "
                            <div class='modal fade' id='editModal$a_id' tabindex='-1' aria-hidden='true'>
                            <div class='modal-dialog'>
                              <form action='../../../actions/admin_editAnnounce.php' method='POST' class='modal-content'>
                                <div class='modal-header'>
                                  <h5 class='modal-title'>Edit Entry</h5>
                                  <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                </div>
                                <div class='modal-body'>
                                  <input type='hidden' name='update_aId' value='$a_id'>
                                  <div class='mb-3'>
                                    <label class='form-label'>Event Name</label>
                                    <input type='text' name='update_title' class='form-control' value='$title' required>
                                  </div>
                                  <div class='mb-3'>
                                    <label class='form-label'>description</label>
                                    <textarea name='update_description' class='form-control' style='height: 100px; resize: none;' required>" . htmlspecialchars($description) . "</textarea>
                                  </div>
                                  <div class='row'>
                                    <div class='col-md-6 mb-3'>
                                      <label class='form-label'>Date</label>
                                      <input type='date' name='update_date' class='form-control' value='$date' required>
                                    </div>

                                    <div class='col-md-6 mb-3'>
                                      <label class='form-label'>Time</label>
                                      <input type='time' name='update_time' class='form-control' value='$time' required>
                                    </div>
                                  </div>
                                </div>
                                <div class='modal-footer'>
                                  <button type='submit' name='update_announcement' class='btn btn-success'>Save Changes</button>
                                  <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                </div>
                              </form>
                            </div>
                          </div>
                          
                          <!-- Delete Modal -->
                          <div class='modal fade' id='deleteModal$a_id' tabindex='-1' aria-hidden='true'>
                            <div class='modal-dialog modal-dialog-centered'>
                              <form action='../../../actions/admin_deleteAnnounce.php' method='POST' class='modal-content'>
                                <div class='modal-header'>
                                  <h5 class='modal-title'>Confirm Delete</h5>
                                  <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                </div>
                                <div class='modal-body'>
                                  <input type='hidden' name='delete_aId' value='$a_id'>
                                  <p>Are you sure you want to delete this event?</p>
                                  <p class='text-muted'><strong>Event:</strong> $title</p>
                                  <p class='text-danger'><small>This action cannot be undone.</small></p>
                                </div>
                                <div class='modal-footer'>
                                  <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                  <button type='submit' name='delete_announcement' class='btn btn-danger'>Delete</button>
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
      const id = document.getElementById('3');

      id.classList.toggle('active');
    </script>

</body>
</html>