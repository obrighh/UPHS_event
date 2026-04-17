<?php
  session_start();
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

                <h4 class="fw-bold py-3 mb-4"><i class="bx bx-calendar-event"></i>Event Request</h4>

                <!-- Add Entry Modal -->
                <div class="modal fade" id="addLogbookModal" tabindex="-1" aria-labelledby="addLogbookModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="../../actions/insert_logbook.php" method="POST" class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="addLogbookModalLabel">Add Event Entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>

                      <div class="modal-body">
                        <div class="mb-3">
                          <label for="f_name" class="form-label">Event Name</label>
                          <input type="text" name="event_name" id="event_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                          <label for="activity" class="form-label">Activity Description</label>
                          <textarea name="" id="" class="form-control" style="height: 200px; resize: none;"></textarea>
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
                        <button type="submit" name="submit_logbook" class="btn btn-success">Add Entry</button>
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
                          <th>Event Name</th>
                          <th>Activity</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <!-- Sample Static Data -->
                        <?php
                          // $modals = ""; // Store modals separately

                          // while($row = mysqli_fetch_assoc($result)) {
                          //   $client_id = htmlspecialchars($row['client_id']);
                          //   $firstname = htmlspecialchars($row['f_name']);
                          //   $lastname = htmlspecialchars($row['l_name']);
                          //   $activity = htmlspecialchars($row['activity']);
                          //   $date = htmlspecialchars($row['date']);
                          //   $time = htmlspecialchars($row['time']);

                          //   echo "<tr>
                          //     <td>$firstname $lastname</td>
                          //     <td>$activity</td>
                          //     <td>$date</td>
                          //     <td>$time</td>
                          //     <td>
                          //       <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editModal$client_id'>Edit</button>
                          //       <a href='../../actions/delete_logbook.php?id=$client_id' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                          //     </td>
                          //   </tr>";

                          //   // Append modal HTML to $modals
                          //   $modals .= "
                          //   <div class='modal fade' id='editModal$client_id' tabindex='-1' aria-hidden='true'>
                          //     <div class='modal-dialog'>
                          //       <form action='../../actions/update_logbook.php' method='POST' class='modal-content'>
                          //         <div class='modal-header'>
                          //           <h5 class='modal-title'>Edit Entry</h5>
                          //           <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                          //         </div>
                          //         <div class='modal-body'>
                          //           <input type='hidden' name='client_id' value='$client_id'>
                          //           <div class='mb-3'>
                          //             <label class='form-label'>First Name</label>
                          //             <input type='text' name='f_name' class='form-control' value='$firstname' required>
                          //           </div>
                          //           <div class='mb-3'>
                          //             <label class='form-label'>Last Name</label>
                          //             <input type='text' name='l_name' class='form-control' value='$lastname' required>
                          //           </div>
                          //           <div class='mb-3'>
                          //             <label class='form-label'>Activity</label>
                          //             <select name='activity' class='form-select' required>
                          //               <option value='Checked In'" . ($activity == 'Checked In' ? ' selected' : '') . ">Checked In</option>
                          //               <option value='Checked Out'" . ($activity == 'Checked Out' ? ' selected' : '') . ">Checked Out</option>
                          //             </select>
                          //           </div>
                          //           <div class='mb-3'>
                          //             <label class='form-label'>Date</label>
                          //             <input type='date' name='date' class='form-control' value='$date' required>
                          //           </div>
                          //           <div class='mb-3'>
                          //             <label class='form-label'>Time</label>
                          //             <input type='time' name='time' class='form-control' value='$time' required>
                          //           </div>
                          //         </div>
                          //         <div class='modal-footer'>
                          //           <button type='submit' name='update_logbook' class='btn btn-success'>Save Changes</button>
                          //           <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                          //         </div>
                          //       </form>
                          //     </div>
                          //   </div>";
                          // }
?> 
                      </tbody>
                    </table>
                    <!-- <?php echo $modals; ?> -->
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
      const id = document.getElementById('2');
      const id2 = document.getElementById('2.6');

      id.classList.toggle('open');
      id2.classList.toggle('active');
    </script>

</body>
</html>