<?php
  session_start();
  require '../../../actions/conn.php';
  require_once __DIR__ . '/../../../actions/admin_contact_unread.php';

  $u_id = $_SESSION['id'];

  $inquiriesJustOpened = admin_contact_unread($conn);
  admin_contact_mark_all_read($conn);

  $hasCreatedAt = false;
  $colChk = @$conn->query("SHOW COLUMNS FROM `contact` LIKE 'created_at'");
  if ($colChk && $colChk->num_rows > 0) {
    $hasCreatedAt = true;
  }

  $sql = $hasCreatedAt
    ? 'SELECT c_id, name, email, message, created_at FROM contact ORDER BY c_id DESC'
    : 'SELECT c_id, name, email, message FROM contact ORDER BY c_id DESC';

  $stmt = $conn->prepare($sql);
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

                <h4 class="fw-bold py-3 mb-2"><i class="bx bx-message-square"></i> Website inquiries</h4>
                <?php if ($inquiriesJustOpened > 0): ?>
                <div class="alert alert-info alert-dismissible mb-3" role="alert">
                  You had <strong><?php echo (int) $inquiriesJustOpened; ?></strong> new inquiry<?php echo $inquiriesJustOpened === 1 ? '' : 'ies'; ?>. <?php echo $inquiriesJustOpened === 1 ? 'It is' : 'They are'; ?> now marked as read.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="card">
                  <div class="card-datatable table-responsive">
                    <table class="table table-striped table-hover table-bordered align-middle">
                      <thead class="table-light">
                      <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <?php if ($hasCreatedAt): ?><th>Received</th><?php endif; ?>
                        <th>Message</th>
                        <th>Actions</th>
                      </tr>
                      </thead>
                      <tbody>
 
                        <?php
                          $modals = ""; 

                          while($row = mysqli_fetch_assoc($result)) {
                            $c_id = $row['c_id'];
                            $name = htmlspecialchars($row['name']);
                            $email = htmlspecialchars($row['email']);
                            $message = htmlspecialchars($row['message']);
                            $receivedCell = '';
                            if ($hasCreatedAt && !empty($row['created_at'])) {
                                $ts = strtotime((string) $row['created_at']);
                                $receivedLabel = $ts ? date('M j, Y g:i A', $ts) : htmlspecialchars((string) $row['created_at']);
                                $receivedCell = "<td><small class=\"text-muted\">{$receivedLabel}</small></td>";
                            } elseif ($hasCreatedAt) {
                                $receivedCell = '<td>—</td>';
                            }
                        
                            echo "
                            <tr>
                                <td>$name</td>
                                <td>$email</td>
                                {$receivedCell}
                                <td style='max-width: 200px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;'>$message</td>
                                <td>
                                    <button class='btn btn-sm btn-info' data-bs-toggle='modal' data-bs-target='#viewModal$c_id'>
                                        <i class='tf-icons bx bx-show'></i>
                                    </button>
                                    <button class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal$c_id'>
                                        <i class='tf-icons bx bx-trash'></i>
                                    </button>
                                </td>
                            </tr>";
                        
                            $modals .= "
                            <!-- View Modal -->
                            <div class='modal fade' id='viewModal$c_id' tabindex='-1' aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title'>Message from $name</h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                        </div>
                                        <div class='modal-body'>
                                            <p><strong>Name:</strong> $name</p>
                                            <p><strong>Email:</strong> $email</p>
                                            <hr>
                                            <p><strong>Message:</strong></p>
                                            <p>$message</p>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- Delete Modal -->
                            <div class='modal fade' id='deleteModal$c_id' tabindex='-1' aria-hidden='true'>
                                <div class='modal-dialog modal-dialog-centered'>
                                    <form action='../../../actions/admin_deleteMessage.php' method='POST' class='modal-content'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title'>Confirm Delete</h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                        </div>
                                        <div class='modal-body'>
                                            <input type='hidden' name='delete_cId' value='$c_id'>
                                            <p>Are you sure you want to delete this message?</p>
                                            <p class='text-muted'><strong>From:</strong> $name</p>
                                            <p class='text-danger'><small>This action cannot be undone.</small></p>
                                        </div>
                                        <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                            <button type='submit' name='delete_message' class='btn btn-danger'>Delete</button>
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
      const id = document.getElementById('3.5');

      id.classList.toggle('active');
    </script>

</body>
</html>