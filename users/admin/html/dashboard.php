<?php
require '../../../actions/analytics.php'; // Include the data file
require_once __DIR__ . '/../../../actions/admin_contact_unread.php';
$adminDashboardInquiryUnread = (isset($conn) && $conn instanceof mysqli) ? admin_contact_unread($conn) : 0;
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

    <title>Admin Dashboard</title>

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
    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>

    <style>
      body{
        overflow-x: hidden;
      }
    </style>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <?php include 'sidebar.php'; ?>
        <!-- / Menu -->
        
        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <?php include 'topnav.php'; ?>

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              
              <div class="row">
                <!-- Events Card -->
                <div class="col-lg-4 col-md-6 col-12 mb-6">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="avatar">
                          <div class="avatar-initial bg-label-primary rounded">
                            <i class="bx bx-calendar-event bx-lg"></i>
                          </div>
                        </div>
                      </div>
                      <div class="card-info">
                        <h4 class="card-title mb-3">Total Events</h4>
                        <div class="d-flex align-items-center mb-1">
                          <h2 class="mb-0 me-2"><?php echo $total_events; ?></h2>
                          <span class="<?php echo $events_percentage >= 0 ? 'text-success' : 'text-danger'; ?>">
                            (<?php echo $events_percentage >= 0 ? '+' : ''; ?><?php echo number_format($events_percentage, 1); ?>%)
                          </span>
                        </div>
                        <p class="text-muted mb-0">Events created this month</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Announcements Card -->
                <div class="col-lg-4 col-md-6 col-12 mb-6">
                  <div class="card h-100">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="avatar">
                          <div class="avatar-initial bg-label-success rounded">
                            <i class="bx bx-megaphone bx-lg"></i>
                          </div>
                        </div>
                      </div>
                      <div class="card-info">
                        <h4 class="card-title mb-3">Announcements</h4>
                        <div class="d-flex align-items-center mb-1">
                          <h2 class="mb-0 me-2"><?php echo $total_announcements; ?></h2>
                          <span class="<?php echo $announcements_percentage >= 0 ? 'text-success' : 'text-danger'; ?>">
                            (<?php echo $announcements_percentage >= 0 ? '+' : ''; ?><?php echo number_format($announcements_percentage, 1); ?>%)
                          </span>
                        </div>
                        <p class="text-muted mb-0">Posted this month</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Website inquiries -->
                <div class="col-lg-4 col-md-6 col-12 mb-6">
                  <div class="card h-100 border border-primary border-opacity-25">
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="avatar">
                          <div class="avatar-initial bg-label-primary rounded">
                            <i class="bx bx-envelope bx-lg"></i>
                          </div>
                        </div>
                        <?php if ($adminDashboardInquiryUnread > 0): ?>
                        <span class="badge bg-danger rounded-pill"><?php echo (int) $adminDashboardInquiryUnread; ?> new</span>
                        <?php endif; ?>
                      </div>
                      <div class="card-info">
                        <h4 class="card-title mb-3">Website inquiries</h4>
                        <p class="text-muted mb-3">Messages sent from the public events page contact form.</p>
                        <a href="messages.php" class="btn btn-sm btn-primary">Open messages</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Total Users Chart -->
              <div class="row">
                <div class="col-12 mb-6">
                  <div class="card">
                    <div class="row row-bordered g-0">
                      <div class="col-lg-12">
                        <div class="card-header d-flex align-items-center justify-content-between">
                          <div class="card-title mb-0">
                            <h5 class="m-0 me-2">Total Users: <?php echo $total_users_count; ?></h5>
                          </div>
                        </div>
                        <div id="totalRevenueChart" class="px-3 py-3"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Total Users Chart -->

            </div>
            <!-- / Content -->

            <!-- Users list -->
            <div class="container-xxl">
              <div class="row justify-content-center mt-2 mb-4">
                <div class="col-12">
                  <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                      <h5 class="card-title mb-0">Recent Users</h5>
                    </div>
                    <div class="table-responsive">
                      <table class="table table-hover align-middle mb-0">
                      <thead class="table-light">
                        <tr>
                          <th scope="col">Full Name</th>
                          <th scope="col">Email</th>
                          <th scope="col">School ID</th>
                          <th scope="col">Date Joined</th>
                        </tr>
                      </thead>
                        <tbody>
                        <?php
                          if($result_users && mysqli_num_rows($result_users) > 0) {
                            while($user = mysqli_fetch_assoc($result_users)) {
                              $full_name = htmlspecialchars(trim($user['f_name'] . ' ' . $user['m_name'] . ' ' . $user['l_name']));
                              $username = htmlspecialchars($user['username']);
                              $email = htmlspecialchars($user['email']);
                              $sch_id = htmlspecialchars($user['sch_id']);
                              $date_joined = date('F j, Y', strtotime($user['date_created']));
                              
                              echo "
                              <tr>
                                <td>
                                  <div class='fw-semibold'>$full_name</div>
                                  <small class='text-muted'>@$username</small>
                                </td>
                                <td>$email</td>
                                <td>$sch_id</td>
                                <td>$date_joined</td>
                              </tr>
                              ";
                            }
                          } else {
                            echo "
                            <tr>
                              <td colspan='4' class='text-center text-muted py-4'>No users found</td>
                            </tr>
                            ";
                          }
                        ?>
                        </tbody>
                      </table>
                    </div>
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
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/dashboards-analytics.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <script>
      const id = document.getElementById('1');
      const id2 = document.getElementById('1.5');

      id.classList.toggle('active');
      id.classList.toggle('open');
      id2.classList.toggle('active');
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart configuration
        const chartData = {
            series: [{
                name: 'Total Users',
                data: <?php echo $users_chart_data; ?>
            }],
            chart: {
                height: 300,
                type: 'line',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            colors: ['#696cff'],
            stroke: {
                curve: 'smooth',
                width: 3
            },
            dataLabels: {
                enabled: false
            },
            markers: {
                size: 5,
                hover: {
                    size: 7
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 4,
                padding: {
                    top: -20,
                    bottom: -10,
                    left: 20,
                    right: 20
                }
            },
            xaxis: {
                categories: <?php echo $months_labels; ?>,
                labels: {
                    style: {
                        colors: '#697a8d',
                        fontSize: '13px'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#697a8d',
                        fontSize: '13px'
                    },
                    formatter: function(val) {
                        return Math.floor(val);
                    }
                }
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function(val) {
                        return val + ' users';
                    }
                }
            }
        };

        // Render the chart
        const chartElement = document.querySelector('#totalRevenueChart');
        if (chartElement && typeof ApexCharts !== 'undefined') {
            const chart = new ApexCharts(chartElement, chartData);
            chart.render();
        }
    });
    </script>
  </body>
</html>
