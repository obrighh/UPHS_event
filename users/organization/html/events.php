<?php
  session_start();
  require '../../../actions/conn.php';
  require_once '../../../actions/events_featured_compat.php';
  require '../../../actions/pagination.php';
  require_once '../../../actions/events_list_search.php';

  if (!isset($_SESSION['id'])) {
    header('Location: ../../../login.php');
    exit;
  }
  $orgId = (int) $_SESSION['id'];
  $guardStmt = $conn->prepare('SELECT ut_id FROM accounts WHERE user_id = ? AND user_status = 1 LIMIT 1');
  $guardStmt->bind_param('i', $orgId);
  $guardStmt->execute();
  $guardRow = $guardStmt->get_result()->fetch_assoc();
  $guardStmt->close();
  if (!is_array($guardRow) || (int) ($guardRow['ut_id'] ?? 0) !== 3) {
    header('Location: ../../../login.php');
    exit;
  }

  $view = isset($_GET['view']) ? (string) $_GET['view'] : 'requests';
  if (!in_array($view, ['requests', 'accepted', 'declined'], true)) {
    $view = 'requests';
  }

  $orgHasFeaturedColumn = events_has_featured_column($conn);
  $orgFeatSql = $orgHasFeaturedColumn
    ? ', COALESCE(is_featured,0) AS is_featured'
    : '';

  $statusMap = ['requests' => 3, 'accepted' => 1, 'declined' => 2];
  $st = $statusMap[$view];

  $q = events_list_parse_query();
  $req_page = pagination_parse_page();

  $types = 'ii';
  $params = [$st, $orgId];
  $search_frag = events_list_append_search_fragment($q, $types, $params);

  $cnt_sql = "SELECT COUNT(*) AS cnt FROM events WHERE event_status = ? AND u_id = ?" . $search_frag;
  $cstmt = $conn->prepare($cnt_sql);
  $cstmt->bind_param($types, ...$params);
  $cstmt->execute();
  $total_rows = (int) ($cstmt->get_result()->fetch_assoc()['cnt'] ?? 0);
  [$page, $per_page, $offset, $total_pages] = pagination_limits($total_rows, $req_page);

  $types_sel = $types . 'ii';
  $params_sel = array_merge($params, [$per_page, $offset]);

  $sql =
  "SELECT event_id, event_name, activity, date_start, date_end, time_start, time_end, CONCAT(date_start, ' - ', date_end) as date, CONCAT(time_start, ' - ', time_end) as time, venue, event_status, decline_reason, events_date_posted{$orgFeatSql}
   FROM events
   WHERE event_status = ? AND u_id = ?
   " . $search_frag . "
   ORDER BY date_start DESC, event_id DESC
   LIMIT ? OFFSET ?
  ";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param($types_sel, ...$params_sel);
  $stmt->execute();

  $result = $stmt->get_result();

  $eventsSelf = static function (string $v, bool $stripQ = false) use ($q): string {
    $qs = ['view' => $v];
    if (!$stripQ && $q !== '') {
      $qs['q'] = $q;
    }
    return 'events.php?' . http_build_query($qs);
  };

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Organization</title>

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

                <?php if ($view === 'requests'): ?>
                <h4 class="fw-bold py-3 mb-4"><i class="bx bx-calendar-event"></i> Event requests (pending approval)</h4>
                <?php elseif ($view === 'accepted'): ?>
                <h4 class="fw-bold py-3 mb-4"><i class="bx bx-calendar-check"></i> Accepted Events</h4>
                <?php else: ?>
                <h4 class="fw-bold py-3 mb-4"><i class="bx bx-calendar-x"></i> Declined Events</h4>
                <?php endif; ?>

                <form id="eventsListSearchForm" method="get" class="row g-2 mb-3 align-items-end flex-wrap">
                  <input type="hidden" name="view" value="<?php echo htmlspecialchars($view, ENT_QUOTES, 'UTF-8'); ?>">
                  <div class="col-auto flex-grow-1" style="min-width: 200px; max-width: 360px;">
                    <label class="form-label small text-muted mb-0">Search</label>
                    <input type="search" name="q" class="form-control form-control-sm" placeholder="Name, activity, venue…" value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
                  </div>
                  <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">Search</button>
                  </div>
                  <?php if ($q !== ''): ?>
                  <div class="col-auto">
                    <a href="<?php echo htmlspecialchars($eventsSelf($view, true), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-secondary">Clear</a>
                  </div>
                  <?php endif; ?>
                </form>

                <?php if ($view === 'requests'): ?>
                <!-- Add Entry Button -->
                <div class="mb-3 text-end">
                  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLogbookModal">
                    <i class="bx bx-plus"></i> Add Events
                  </button>
                </div>
                <?php endif; ?>

                <?php if ($view === 'requests'): ?>
                <!-- Add Entry Modal -->
                <div class="modal fade" id="addLogbookModal" tabindex="-1" aria-labelledby="addLogbookModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <form action="../../../actions/add_events.php" method="POST" class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="addLogbookModalLabel">Add Event Entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>

                      <div class="modal-body">
                        <input type="hidden" name="_return" value="org">
                        <div class="mb-3">
                          <label for="event_name" class="form-label">Event Name</label>
                          <input type="text" name="event_name" id="event_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                          <label for="event_priority" class="form-label">Priority</label>
                          <select name="event_priority" id="event_priority" class="form-select" required>
                            <option value="minor" selected>Minor priority</option>
                            <option value="main">Main priority</option>
                          </select>
                          <div class="form-text">Main priority is used for the public homepage countdown when the event is approved. If none is main, the countdown picks a random upcoming event.</div>
                        </div>

                        <div class="mb-3">
                          <label for="activity" class="form-label">Activity Description</label>
                          <textarea name="activity" id="activity" class="form-control" style="height: 200px; resize: none;"></textarea>
                        </div>

                        <div class="mb-3">
                          <label for="venue" class="form-label">Venue</label>
                          <input type="text" name="venue" id="venue" class="form-control" placeholder="e.g., Room 301, IT Building" required>
                        </div>

                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label for="date_start" class="form-label">Date Start</label>
                            <input type="date" name="date_start" id="date_start" class="form-control" required onchange="updateEndDateMin()">
                          </div>

                          <div class="col-md-6 mb-3">
                            <label for="date_end" class="form-label">Date End</label>
                            <input type="date" name="date_end" id="date_end" class="form-control" required>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label for="time_start" class="form-label">Time Start</label>
                            <input type="time" name="time_start" id="time_start" class="form-control" required>
                          </div>

                          <div class="col-md-6 mb-3">
                            <label for="time_end" class="form-label">Time End</label>
                            <input type="time" name="time_end" id="time_end" class="form-control" required>
                          </div>
                        </div>

                        <script>
                          // Set minimum date to today
                          const today = new Date().toISOString().split('T')[0];
                          document.getElementById('date_start').setAttribute('min', today);
                          document.getElementById('date_end').setAttribute('min', today);

                          function updateEndDateMin() {
                            const startDate = document.getElementById('date_start').value;
                            if (startDate) {
                              document.getElementById('date_end').setAttribute('min', startDate);
                            }
                          }
                        </script>
                      </div>

                      <div class="modal-footer">
                        <button type="submit" name="submit_event" class="btn btn-success">Add Entry</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      </div>

                    </form>
                  </div>
                </div>
                <?php endif; ?>

                <div class="card">
                  <div class="card-datatable table-responsive">
                    <?php if ($view === 'requests'): ?>
                    <table class="table table-striped table-hover table-bordered align-middle">
                      <thead class="table-light">
                        <tr>
                          <th>Event Name</th>
                          <th>Activity</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Venue</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $modals = '';
                          while ($row = mysqli_fetch_assoc($result)) {
                            $event_id = $row['event_id'];
                            $event_name = htmlspecialchars($row['event_name']);
                            $activity = htmlspecialchars($row['activity']);
                            $activity_edit = htmlspecialchars($row['activity'], ENT_QUOTES, 'UTF-8');
                            $date = htmlspecialchars($row['date']);
                            $time = htmlspecialchars($row['time']);
                            $venue = htmlspecialchars($row['venue']);
                            $date_start = htmlspecialchars($row['date_start']);
                            $date_end = htmlspecialchars($row['date_end']);
                            $time_start = htmlspecialchars($row['time_start']);
                            $time_end = htmlspecialchars($row['time_end']);
                            $status = 'For Approval';

                            $display_name = $event_name;
                            if (!empty($row['is_featured'])) {
                              $display_name .= "<br><span class='badge bg-warning text-dark'>Main priority</span>";
                            }

                            echo "<tr>
                              <td style='max-width: 150px;'>$display_name</td>
                              <td style='max-width: 130px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;'>
                                $activity
                              </td>
                              <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>
                                $date
                              </td>
                              <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>
                                $time
                              </td>
                              <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>
                                $venue
                              </td>
                              <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>
                                $status
                              </td>
                              <td>
                                <button type='button' class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editModal$event_id'><i class='tf-icons bx bx-edit'></i></button>
                                <button type='button' class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#deleteModal$event_id'><i class='tf-icons bx bx-trash bx-lg'></i></button>
                              </td>
                            </tr>";

                            $feature_main_block = '';
                            if ($orgHasFeaturedColumn) {
                              $main_sel = !empty($row['is_featured']) ? ' selected' : '';
                              $minor_sel = empty($row['is_featured']) ? ' selected' : '';
                              $feature_main_block = "
                                  <div class='mb-3'>
                                    <label class='form-label' for='event_priority_{$event_id}'>Priority</label>
                                    <select name='event_priority' id='event_priority_{$event_id}' class='form-select' required>
                                      <option value='minor'{$minor_sel}>Minor priority</option>
                                      <option value='main'{$main_sel}>Main priority</option>
                                    </select>
                                    <div class='form-text'>Main priority drives the public countdown after approval. Only one approved event should be main at a time.</div>
                                  </div>";
                            }

                            $modals .= "
                            <div class='modal fade' id='editModal$event_id' tabindex='-1' aria-hidden='true'>
                            <div class='modal-dialog'>
                              <form action='../../../actions/edit_event.php' method='POST' class='modal-content'>
                                <div class='modal-header'>
                                  <h5 class='modal-title'>Edit Entry</h5>
                                  <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                </div>
                                <div class='modal-body'>
                                  <input type='hidden' name='update_eventId' value='$event_id'>
                                  <div class='mb-3'>
                                    <label class='form-label'>Event Name</label>
                                    <input type='text' name='update_eventName' class='form-control' value='$event_name' required>
                                  </div>
                                  <div class='mb-3'>
                                    <label class='form-label'>Activity</label>
                                    <textarea name='update_activity' class='form-control' style='height: 100px; resize: none;' required>$activity_edit</textarea>
                                  </div>
                                  <div class='mb-3'>
                                    <label class='form-label'>Venue</label>
                                    <input type='text' name='update_venue' class='form-control' value='$venue' required>
                                  </div>
                                  <div class='row'>
                                    <div class='col-md-6 mb-3'>
                                      <label class='form-label'>Date Start</label>
                                      <input type='date' name='update_dateStart' class='form-control' value='$date_start' required onchange=\"updateEditEndDateMin(this)\">
                                    </div>
                                    <div class='col-md-6 mb-3'>
                                      <label class='form-label'>Date End</label>
                                      <input type='date' name='update_dateEnd' class='form-control' value='$date_end' required>
                                    </div>
                                  </div>
                                  <div class='row'>
                                    <div class='col-md-6 mb-3'>
                                      <label class='form-label'>Time Start</label>
                                      <input type='time' name='update_timeStart' class='form-control' value='$time_start' required>
                                    </div>
                                    <div class='col-md-6 mb-3'>
                                      <label class='form-label'>Time End</label>
                                      <input type='time' name='update_timeEnd' class='form-control' value='$time_end' required>
                                    </div>
                                  </div>
                                  $feature_main_block
                                </div>
                                <div class='modal-footer'>
                                  <button type='submit' name='update_event' class='btn btn-success'>Save Changes</button>
                                  <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                </div>
                              </form>
                            </div>
                          </div>
                          <div class='modal fade' id='deleteModal$event_id' tabindex='-1' aria-hidden='true'>
                            <div class='modal-dialog modal-dialog-centered'>
                              <form action='../../../actions/delete_events.php' method='POST' class='modal-content'>
                                <div class='modal-header'>
                                  <h5 class='modal-title'>Confirm Delete</h5>
                                  <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                                </div>
                                <div class='modal-body'>
                                  <input type='hidden' name='delete_eventId' value='$event_id'>
                                  <p>Are you sure you want to delete this event?</p>
                                  <p class='text-muted'><strong>Event:</strong> $event_name</p>
                                  <p class='text-danger'><small>This action cannot be undone.</small></p>
                                </div>
                                <div class='modal-footer'>
                                  <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                  <button type='submit' name='delete_event' class='btn btn-danger'>Delete</button>
                                </div>
                              </form>
                            </div>
                          </div>
                          ";
                          }
                        ?>
                      </tbody>
                    </table>
                    <?php elseif ($view === 'accepted'): ?>
                    <table class="table table-striped table-hover table-bordered align-middle">
                      <thead class="table-light">
                        <tr>
                          <th>Event Name</th>
                          <th>Activity</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Venue</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $modals = '';
                          while ($row = mysqli_fetch_assoc($result)) {
                            $event_id = $row['event_id'];
                            $event_name = htmlspecialchars($row['event_name']);
                            $activity = htmlspecialchars($row['activity']);
                            $date = htmlspecialchars($row['date']);
                            $time = htmlspecialchars($row['time']);
                            $venue = htmlspecialchars($row['venue']);
                            $raw_posted = $row['events_date_posted'] ?? null;
                            $date_posted = ($raw_posted !== null && (string) $raw_posted !== '' && (string) $raw_posted !== '0000-00-00')
                              ? htmlspecialchars(date('Y-m-d', strtotime((string) $raw_posted)))
                              : '<span class="text-muted">—</span>';
                            $display_name = $event_name;
                            if (!empty($row['is_featured'])) {
                              $display_name .= " <span class='badge bg-warning text-dark'>Main priority</span>";
                            }
                            echo "<tr style='cursor: pointer;' data-bs-toggle='modal' data-bs-target='#orgDetailModal$event_id'>
                              <td style='max-width: 150px;'>$display_name</td>
                              <td style='max-width: 130px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;'>$activity</td>
                              <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>$date</td>
                              <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>$time</td>
                              <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>$venue</td>
                            </tr>";
                            $modals .= "
                            <div class='modal fade' id='orgDetailModal$event_id' tabindex='-1' aria-hidden='true'>
                              <div class='modal-dialog modal-dialog-centered'>
                                <div class='modal-content'>
                                  <div class='modal-header'>
                                    <h5 class='modal-title fw-semibold'>Event Details</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                  </div>
                                  <div class='modal-body'>
                                    <div class='mb-3 pb-3 border-bottom'>
                                      <label class='form-label text-muted mb-1 small'>Event Name</label>
                                      <p class='fw-semibold mb-0'>$event_name</p>
                                    </div>
                                    <div class='mb-3 pb-3 border-bottom'>
                                      <label class='form-label text-muted mb-1 small'>Activity</label>
                                      <p class='mb-0'>$activity</p>
                                    </div>
                                    <div class='row mb-3 pb-3 border-bottom'>
                                      <div class='col-6'>
                                        <label class='form-label text-muted mb-1 small'>Date</label>
                                        <p class='mb-0'>$date</p>
                                      </div>
                                      <div class='col-6'>
                                        <label class='form-label text-muted mb-1 small'>Time</label>
                                        <p class='mb-0'>$time</p>
                                      </div>
                                    </div>
                                    <div class='mb-3 pb-3 border-bottom'>
                                      <label class='form-label text-muted mb-1 small'>Venue</label>
                                      <p class='mb-0'>$venue</p>
                                    </div>
                                    <div class='mb-0'>
                                      <label class='form-label text-muted mb-1 small'>Date Posted</label>
                                      <p class='mb-0'>$date_posted</p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>";
                          }
                        ?>
                      </tbody>
                    </table>
                    <?php else: ?>
                    <table class="table table-striped table-hover table-bordered align-middle">
                      <thead class="table-light">
                        <tr>
                          <th>Event Name</th>
                          <th>Activity</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Venue</th>
                          <th>Decline reason</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $modals = '';
                          while ($row = mysqli_fetch_assoc($result)) {
                            $event_id = $row['event_id'];
                            $event_name = htmlspecialchars($row['event_name']);
                            $activity = htmlspecialchars($row['activity']);
                            $date = htmlspecialchars($row['date']);
                            $time = htmlspecialchars($row['time']);
                            $venue = htmlspecialchars($row['venue']);
                            $decline_reason = htmlspecialchars(trim((string) ($row['decline_reason'] ?? '')));
                            $decline_cell = $decline_reason !== '' ? $decline_reason : '<span class="text-muted">—</span>';
                            $raw_posted = $row['events_date_posted'] ?? null;
                            $date_posted = ($raw_posted !== null && (string) $raw_posted !== '' && (string) $raw_posted !== '0000-00-00')
                              ? htmlspecialchars(date('Y-m-d', strtotime((string) $raw_posted)))
                              : '<span class="text-muted">—</span>';
                            $decline_reason_detail = $decline_reason !== ''
                              ? nl2br($decline_reason)
                              : '<span class="text-muted">No reason recorded.</span>';
                            echo "<tr style='cursor: pointer;' data-bs-toggle='modal' data-bs-target='#orgDetailModal$event_id'>
                              <td style='max-width: 150px;'>$event_name</td>
                              <td style='max-width: 130px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;'>$activity</td>
                              <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>$date</td>
                              <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>$time</td>
                              <td style='max-width: 120px; overflow: hidden; white-space:nowrap; text-overflow: ellipsis;'>$venue</td>
                              <td style='max-width: 200px; white-space: normal;'>$decline_cell</td>
                            </tr>";
                            $modals .= "
                            <div class='modal fade' id='orgDetailModal$event_id' tabindex='-1' aria-hidden='true'>
                              <div class='modal-dialog modal-dialog-centered'>
                                <div class='modal-content'>
                                  <div class='modal-header'>
                                    <h5 class='modal-title fw-semibold'>Event Details</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                  </div>
                                  <div class='modal-body'>
                                    <div class='mb-3 pb-3 border-bottom'>
                                      <label class='form-label text-muted mb-1 small'>Event Name</label>
                                      <p class='fw-semibold mb-0'>$event_name</p>
                                    </div>
                                    <div class='mb-3 pb-3 border-bottom'>
                                      <label class='form-label text-muted mb-1 small'>Activity</label>
                                      <p class='mb-0'>$activity</p>
                                    </div>
                                    <div class='row mb-3 pb-3 border-bottom'>
                                      <div class='col-6'>
                                        <label class='form-label text-muted mb-1 small'>Date</label>
                                        <p class='mb-0'>$date</p>
                                      </div>
                                      <div class='col-6'>
                                        <label class='form-label text-muted mb-1 small'>Time</label>
                                        <p class='mb-0'>$time</p>
                                      </div>
                                    </div>
                                    <div class='mb-3 pb-3 border-bottom'>
                                      <label class='form-label text-muted mb-1 small'>Venue</label>
                                      <p class='mb-0'>$venue</p>
                                    </div>
                                    <div class='mb-3 pb-3 border-bottom'>
                                      <label class='form-label text-muted mb-1 small'>Date Posted</label>
                                      <p class='mb-0'>$date_posted</p>
                                    </div>
                                    <div class='mb-0'>
                                      <label class='form-label text-muted mb-1 small'>Reason for declining</label>
                                      <p class='mb-0'>$decline_reason_detail</p>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>";
                          }
                        ?>
                      </tbody>
                    </table>
                    <?php endif; ?>
                    <?php pagination_render_nav($page, $total_pages, $total_rows); ?>
                    <?php echo $modals; ?>
                    
                    <script>
                      const todayOrg = new Date().toISOString().split('T')[0];
                      
                      // Set minimum date for all edit date inputs
                      document.querySelectorAll('input[name="update_dateStart"]').forEach(input => {
                        input.setAttribute('min', todayOrg);
                      });
                      document.querySelectorAll('input[name="update_dateEnd"]').forEach(input => {
                        input.setAttribute('min', todayOrg);
                      });
                      
                      function updateEditEndDateMin(startDateInput) {
                        const startDate = startDateInput.value;
                        if (startDate) {
                          const endDateInput = startDateInput.closest('.row').querySelector('input[name="update_dateEnd"]');
                          if (endDateInput) {
                            endDateInput.setAttribute('min', startDate);
                          }
                        }
                      }
                    </script>
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
      (function () {
        const ev = document.getElementById('2');
        if (ev) { ev.classList.add('active', 'open'); }
        <?php if ($view === 'requests'): ?>
        document.getElementById('2.1')?.classList.add('active');
        <?php elseif ($view === 'accepted'): ?>
        document.getElementById('2.2')?.classList.add('active');
        <?php else: ?>
        document.getElementById('2.3')?.classList.add('active');
        <?php endif; ?>
      })();
    </script>

</body>
</html>