<?php
  session_start();

  require '../../../actions/conn.php';

  $able = 1;
  $u_id = $_SESSION['id'];

  require '../../../actions/getEvent.php';

  if(isset($_POST['signout'])){
    session_destroy();
    echo 
    '
        <script> window.location = "../../../login.php"; </script>
    ';
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Admin - Calendar of Events</title>

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
  </head>

<style>
  /* ─── Calendar Section ─── */
  .calendar-section {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }
  .calendar-header { margin-bottom: 1.5rem; }
  .calendar-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1rem;
  }

  .mini-calendar { margin-bottom: 1.5rem; }
  .mini-calendar table { width: 100%; border-collapse: collapse; }
  .mini-calendar th,
  .mini-calendar td { text-align: center; padding: 0.5rem; font-size: 0.85rem; }
  .mini-calendar th { color: #666; font-weight: 600; border-bottom: 1px solid #eee; }
  .mini-calendar td { color: #333; cursor: pointer; transition: background 0.2s; }
  .mini-calendar td:hover { background: #f5f5f5; border-radius: 4px; }

  .mini-calendar .today {
    background: #1976D2; color: white; border-radius: 4px; font-weight: 600;
  }
  .mini-calendar .has-event {
    background: #E3F2FD; color: #1976D2; border-radius: 4px; font-weight: 600; position: relative;
  }
  .mini-calendar .has-event::after {
    content: ''; position: absolute; bottom: 2px; left: 50%; transform: translateX(-50%);
    width: 4px; height: 4px; background: #1976D2; border-radius: 50%;
  }
  .mini-calendar .has-event.selected { background: #1976D2; color: white; }
  .mini-calendar .has-event.selected::after { background: white; }
  .mini-calendar .other-month { color: #ccc; }

  .month-selector { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
  .month-selector h5 { margin: 0; font-size: 1rem; font-weight: 600; }
  .month-nav { display: flex; gap: 0.5rem; }
  .month-nav button { background: none; border: none; cursor: pointer; font-size: 1.2rem; color: #666; padding: 0.25rem 0.5rem; }

  .submission-btn {
    width: 100%; background: #1976D2; color: white; border: none; padding: 0.75rem;
    border-radius: 6px; font-weight: 500; cursor: pointer; margin-top: 1rem; transition: background 0.3s;
  }
  .submission-btn:hover { background: #1565C0; }

  .event-list-header { margin-bottom: 1rem; padding: 0 0.25rem; }
  .event-list-header h5 { font-size: 1.2rem; font-weight: 600; color: #333; margin: 0; }
  .event-list-header p  { font-size: 0.9rem; color: #666; margin: 0.25rem 0 0; }

  .event-list { display: flex; flex-direction: column; gap: 1rem; }

  .event-card {
    display: flex; gap: 1rem; background: white; border-radius: 8px; padding: 1rem;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    cursor: pointer; border: 2px solid transparent;
  }
  .event-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-color: #1976D2; }
  .event-card.hidden { display: none; }
  .event-card.search-match { border-left: 4px solid #1976D2; }

  .event-image { width: 100px; height: 100px; border-radius: 6px; object-fit: cover; flex-shrink: 0; }
  .event-details { flex: 1; }
  .event-title { font-size: 1.1rem; font-weight: 600; color: #333; margin-bottom: 0.4rem; }

  .event-meta { display: flex; align-items: center; gap: 0.5rem; color: #666; font-size: 0.9rem; margin-bottom: 0.15rem; }
  .event-meta i { font-size: 1rem; color: #1976D2; }

  .no-events-message { text-align: center; padding: 3rem 1rem; color: #999; }
  .no-events-message i { font-size: 3rem; margin-bottom: 1rem; color: #ccc; display: block; }

  .modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.45); z-index: 9999;
    justify-content: center; align-items: flex-start;
    padding: 2.5rem 1rem; overflow-y: auto;
  }
  .modal-overlay.active { display: flex; }

  .detail-modal {
    background: #fff; border-radius: 12px; width: 100%; max-width: 700px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.2); overflow: hidden;
    position: relative; animation: modalIn 0.22s ease;
  }
  @keyframes modalIn {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
  }


  .modal-close-btn {
    position: absolute; top: 14px; right: 16px; z-index: 2;
    background: rgba(255,255,255,0.88); border: none; border-radius: 50%;
    width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
    cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.15); transition: background 0.2s;
  }
  .modal-close-btn:hover { background: #fff; }
  .modal-close-btn i { font-size: 1.15rem; color: #555; }


.modal-banner { 
  width: 100%; 
  height: 220px; 
  object-fit: cover; 
  display: block; 
}
.modal-banner-placeholder {
  width: 100%; 
  height: 220px;
  background: linear-gradient(135deg, #1565C0 0%, #42A5F5 100%);
  display: flex; 
  align-items: center; 
  justify-content: center;
}
.modal-banner-placeholder i { 
  font-size: 4.5rem; 
  color: rgba(255,255,255,0.5); 
}


#modalBanner {
  width: 100%;
  overflow: hidden;
  position: relative;
}

  .modal-body { padding: 1.8rem 2rem 2rem; }

  .modal-title {
    font-size: 1.5rem; font-weight: 700; color: #1565C0;
    margin: 0 0 0.5rem; line-height: 1.3; text-align: center;
  }
  .modal-date-line {
    text-align: center; font-size: 0.9rem; color: #666; margin: 0 0 1.2rem;
  }
  .modal-date-line strong { color: #333; }

  .modal-divider { border: none; border-top: 1px solid #eee; margin: 1.2rem 0; }

  .modal-description {
    font-size: 0.95rem; color: #444; line-height: 1.75; margin: 0 0 0.6rem;
  }

  .modal-meta-row {
    display: flex; align-items: flex-start; gap: 0.6rem; margin-bottom: 0.7rem;
  }
  .modal-meta-row i { font-size: 1.15rem; color: #1976D2; flex-shrink: 0; margin-top: 2px; }
  .modal-meta-row span { font-size: 0.93rem; color: #444; line-height: 1.5; }

  .modal-register-link {
    color: #1976D2; font-weight: 600; font-size: 0.93rem;
    text-decoration: none; transition: color 0.2s;
  }
  .modal-register-link:hover { color: #1565C0; text-decoration: underline; }

  .modal-organizer { font-size: 0.88rem; color: #888; margin-top: 1rem; }
  .modal-organizer strong { color: #555; }

  @media (max-width: 768px) {
    .event-image { width: 80px; height: 80px; }
    .modal-body { padding: 1.4rem 1.25rem 1.6rem; }
    .modal-title { font-size: 1.2rem; }
  }
</style>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <?php include 'sidebar.php'; ?>

        <div class="layout-page">
          <!-- Navbar -->
          <nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                <i class="icon-base bx bx-menu icon-md"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
              <!-- Search -->
              <div class="navbar-nav align-items-center me-auto">
                <div class="nav-item d-flex align-items-center">
                  <span class="w-px-22 h-px-22"><i class="icon-base bx bx-search icon-md"></i></span>
                  <input type="text" id="topNavSearch"
                    class="form-control border-0 shadow-none ps-1 ps-sm-2 d-md-block d-none"
                    placeholder="Search events..." aria-label="Search events..." />
                </div>
              </div>

              <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                <li class="nav-item">
                  <a class="nav-link" href="messages.php">
                    <i class="bx bx-envelope" style="font-size: 24px;"></i>
                  </a>
                </li>
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
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
                    <li><div class="dropdown-divider my-1"></div></li>
                    <li>
                      <a class="dropdown-item" href="accounts.php">
                        <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
                      </a>
                    </li>
                    <li><div class="dropdown-divider my-1"></div></li>
                    <li>
                      <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                        <button name="signout" type="submit" class="dropdown-item">
                          <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Log Out</span>
                        </button>
                      </form>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row">

                <!-- Left – Calendar -->
                <div class="col-lg-4 col-md-4 mb-4">
                  <div class="calendar-section">
                    <div class="calendar-header">
                      <h4 class="calendar-title">Calendar of Events</h4>
                    </div>
                    <div class="mini-calendar">
                      <div class="month-selector">
                        <h5 id="currentMonth">January 2026</h5>
                        <div class="month-nav">
                          <button onclick="previousMonth()">&#8249;</button>
                          <button onclick="nextMonth()">&#8250;</button>
                        </div>
                      </div>
                      <table id="miniCalendarTable">
                        <thead>
                          <tr><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th><th>S</th></tr>
                        </thead>
                        <tbody id="miniCalendarBody"></tbody>
                      </table>
                    </div>
                    <button class="submission-btn" onclick="showAllEvents()">See All Events</button>
                  </div>
                </div>

                <!-- Right – Event list -->
                <div class="col-lg-8 col-md-8">
                  <div class="event-list-header">
                    <h5 id="eventListTitle">All Events</h5>
                    <p id="eventListSubtitle">Showing all upcoming events</p>
                  </div>

                  <div class="event-list" id="eventList">
                    <?php foreach($events as $row):
                      $dateString = $row['date'];
                      if (strpos($dateString, ' - ') !== false) {
                        $dateParts  = explode(' - ', $dateString);
                        $dateString = trim($dateParts[0]);
                      }
                      $date          = new DateTime($dateString);
                      $formattedDate = $date->format('F j, Y');
                      
                      // Determine image path
                      $imagePath = '../assets/img/eventPlaceholder.png'; // default
                      if (!empty($row['event_image'])) {
                        $imagePath = '../../../uploads/events/' . htmlspecialchars($row['event_image']);
                      }
                    ?>
                      <div class="event-card"
                          data-event-id="<?php echo $row['event_id']; ?>"
                          data-event-date="<?php echo $row['date']; ?>"
                          data-event-name="<?php echo htmlspecialchars($row['event_name'], ENT_QUOTES); ?>"
                          data-event-time="<?php echo htmlspecialchars($row['time'], ENT_QUOTES); ?>"
                          data-event-venue="<?php echo htmlspecialchars($row['venue'] ?? '', ENT_QUOTES); ?>"
                          data-event-description="<?php echo htmlspecialchars($row['description'] ?? '', ENT_QUOTES); ?>"
                          data-event-register="<?php echo htmlspecialchars($row['register_link'] ?? '', ENT_QUOTES); ?>"
                          data-event-organizer="<?php echo htmlspecialchars($row['organizer'] ?? '', ENT_QUOTES); ?>"
                          data-event-image="<?php echo htmlspecialchars($imagePath, ENT_QUOTES); ?>"
                          data-event-formatted-date="<?php echo htmlspecialchars($formattedDate); ?>"
                          onclick="openEventDetail(this)">
                        <div class="event-details">
                          <div class="event-title"><?php echo htmlspecialchars($row['event_name']); ?></div>
                          <div class="event-meta">
                            <i class="tf-icons bx bx-calendar-event"></i>
                            <span><?php echo htmlspecialchars($formattedDate); ?></span>
                          </div>
                          <div class="event-meta">
                            <i class="tf-icons bx bx-time"></i>
                            <span><?php echo htmlspecialchars($row['time']); ?></span>
                          </div>
                          <?php if(!empty($row['venue'])): ?>
                          <div class="event-meta">
                            <i class="tf-icons bx bx-map"></i>
                            <span><?php echo htmlspecialchars($row['venue']); ?></span>
                          </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>

                  <div class="no-events-message" id="noEventsMessage" style="display:none;">
                    <i class="tf-icons bx bx-calendar-x"></i>
                    <p>No events found for this date</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="content-backdrop fade"></div>
          </div>
        </div>
      </div>
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <!-- ─── Event Detail Modal ─── -->
    <div class="modal-overlay" id="eventModalOverlay" onclick="closeModalOutside(event)">
      <div class="detail-modal">
        <button class="modal-close-btn" onclick="closeEventDetail()">
          <i class="tf-icons bx bx-x"></i>
        </button>

        <!-- Banner / poster -->
        <div id="modalBanner">
          <div class="modal-banner-placeholder">
            <i class="tf-icons bx bx-image"></i>
          </div>
        </div>

        <!-- Content -->
        <div class="modal-body">
          <h2 class="modal-title" id="modalTitle"></h2>
          <p  class="modal-date-line" id="modalDateLine"></p>

          <hr class="modal-divider">

          <p class="modal-description" id="modalDescription"></p>

          <!-- Venue -->
          <div class="modal-meta-row" id="modalVenueRow" style="display:none;">
            <i class="tf-icons bx bx-map"></i>
            <span id="modalVenue"></span>
          </div>

          <!-- Register link -->
          <div id="modalRegisterRow" style="display:none;">
            <hr class="modal-divider">
            <div class="modal-meta-row">
              <i class="tf-icons bx bx-link"></i>
              <span>To join, register at&nbsp;<a href="#" class="modal-register-link" id="modalRegisterLink" target="_blank" rel="noopener"></a></span>
            </div>
          </div>

          <!-- Organizer -->
          <p class="modal-organizer" id="modalOrganizer" style="display:none;"></p>
        </div>
      </div>
    </div>

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/dashboards-analytics.js"></script>

    <script>
      // Menu activation
      const id = document.getElementById('1.6');
      if(id) id.classList.toggle('active');

      const eventsData = <?php echo json_encode(array_map(fn($r) => [
          'id'    => $r['event_id'],
          'title' => $r['event_name'],
          'start' => $r['date'],
          'time'  => $r['time'],
          'venue' => $r['venue'] ?? '',
          'image' => $r['event_image'] ?? ''
      ], $events)); ?>;
      let selectedDate = null;
      let isSearching  = false;

      document.addEventListener('DOMContentLoaded', function() {
        generateMiniCalendar(new Date());
        initializeTopNavSearch();
      });

      /* ─── Search ─── */
      function initializeTopNavSearch() {
        const inp = document.getElementById('topNavSearch');
        if(!inp) return;
        inp.addEventListener('input', e => {
          const t = e.target.value.trim().toLowerCase();
          t === '' ? clearSearch() : searchEvents(t);
        });
        inp.addEventListener('keydown', e => {
          if(e.key === 'Escape') { inp.value = ''; clearSearch(); }
        });
      }

      function searchEvents(term) {
        isSearching = true;
        selectedDate = null;
        generateMiniCalendar(currentDate);

        document.getElementById('eventListTitle').textContent    = 'Search Results';
        document.getElementById('eventListSubtitle').textContent = `Showing results for "${term}"`;

        let count = 0;
        document.querySelectorAll('.event-card').forEach(card => {
          const name = (card.dataset.eventName || '').toLowerCase();
          const v = (card.dataset.eventVenue || '').toLowerCase();
          const d = (card.dataset.eventFormattedDate || '').toLowerCase();
          const desc = (card.dataset.eventDescription || '').toLowerCase();
          if(name.includes(term) || v.includes(term) || d.includes(term) || desc.includes(term)) {
            card.classList.remove('hidden'); card.classList.add('search-match'); count++;
          } else {
            card.classList.add('hidden'); card.classList.remove('search-match');
          }
        });
        toggleNoEvents(count === 0, `No events found matching "${term}"`);
      }

      function clearSearch() {
        isSearching = false;
        document.querySelectorAll('.event-card').forEach(c => c.classList.remove('search-match'));
        selectedDate ? filterEventsByDate(selectedDate) : showAllEvents();
      }

      /* ─── Mini Calendar ─── */
      let currentDate = new Date();

      function generateMiniCalendar(date) {
        const year  = date.getFullYear();
        const month = date.getMonth();
        const names = ["January","February","March","April","May","June",
                       "July","August","September","October","November","December"];
        document.getElementById('currentMonth').textContent = names[month] + ' ' + year;

        const firstDay      = new Date(year, month, 1).getDay();
        const daysInMonth   = new Date(year, month + 1, 0).getDate();
        const daysInPrev    = new Date(year, month, 0).getDate();
        const startDay      = firstDay === 0 ? 6 : firstDay - 1;

        const tbody = document.getElementById('miniCalendarBody');
        tbody.innerHTML = '';
        let dayC = 1, nextC = 1;

        for(let i = 0; i < 6; i++) {
          const row = document.createElement('tr');
          for(let j = 0; j < 7; j++) {
            const cell = document.createElement('td');
            if(i === 0 && j < startDay) {
              cell.textContent = daysInPrev - startDay + j + 1;
              cell.classList.add('other-month');
            } else if(dayC > daysInMonth) {
              cell.textContent = nextC++;
              cell.classList.add('other-month');
            } else {
              const day = dayC;
              cell.textContent = day;
              const today = new Date();
              if(day === today.getDate() && month === today.getMonth() && year === today.getFullYear())
                cell.classList.add('today');

              const dStr = year + '-' + String(month+1).padStart(2,'0') + '-' + String(day).padStart(2,'0');
              if(hasEventOnDate(year, month, day)) {
                cell.classList.add('has-event');
                cell.setAttribute('data-date', dStr);
                cell.addEventListener('click', () => {
                  document.getElementById('topNavSearch').value = '';
                  isSearching = false;
                  filterEventsByDate(dStr);
                });
              }
              if(selectedDate === dStr) cell.classList.add('selected');
              dayC++;
            }
            row.appendChild(cell);
          }
          tbody.appendChild(row);
          if(dayC > daysInMonth && i > 3) break;
        }
      }

      function hasEventOnDate(y, m, d) {
        const s = y + '-' + String(m+1).padStart(2,'0') + '-' + String(d).padStart(2,'0');
        return eventsData.some(ev => ev.start.split(' - ')[0].trim().startsWith(s));
      }
      function previousMonth() { currentDate.setMonth(currentDate.getMonth()-1); generateMiniCalendar(currentDate); }
      function nextMonth()     { currentDate.setMonth(currentDate.getMonth()+1); generateMiniCalendar(currentDate); }

      /* ─── Date filter ─── */
      function filterEventsByDate(dStr) {
        selectedDate = dStr;
        generateMiniCalendar(currentDate);
        const f = new Date(dStr+'T00:00:00').toLocaleDateString('en-US',
          { weekday:'long', year:'numeric', month:'long', day:'numeric' });
        document.getElementById('eventListTitle').textContent    = 'Events on ' + f;
        document.getElementById('eventListSubtitle').textContent = 'Showing events for selected date';

        let count = 0;
        document.querySelectorAll('.event-card').forEach(card => {
          card.classList.remove('search-match');
          const ev = card.dataset.eventDate;
          const start = ev.includes(' - ') ? ev.split(' - ')[0].trim() : ev;
          if(start === dStr) { card.classList.remove('hidden'); count++; }
          else               { card.classList.add('hidden'); }
        });
        toggleNoEvents(count === 0, 'No events found for this date');
      }

      /* ─── Show all ─── */
      function showAllEvents() {
        selectedDate = null;
        document.getElementById('topNavSearch').value = '';
        isSearching = false;
        generateMiniCalendar(currentDate);
        document.getElementById('eventListTitle').textContent    = 'All Events';
        document.getElementById('eventListSubtitle').textContent = 'Showing all upcoming events';
        document.querySelectorAll('.event-card').forEach(c => c.classList.remove('hidden','search-match'));
        toggleNoEvents(false);
      }

      /* ─── Helpers ─── */
      function toggleNoEvents(show, msg) {
        const n = document.getElementById('noEventsMessage');
        const e = document.getElementById('eventList');
        if(show) { e.style.display = 'none'; n.style.display = 'block'; if(msg) n.querySelector('p').textContent = msg; }
        else     { e.style.display = 'flex'; n.style.display = 'none'; }
      }

      /* ─── Event Detail Modal ─── */
      function openEventDetail(card) {
  const ds   = card.dataset;
  const name = ds.eventName || '';
  const fDate = ds.eventFormattedDate || '';
  const time  = ds.eventTime || '';
  const venue = ds.eventVenue || '';
  const desc  = ds.eventDescription || '';
  const reg   = ds.eventRegister || '';
  const org   = ds.eventOrganizer || '';
  const img   = ds.eventImage || '';

  /* title */
  document.getElementById('modalTitle').textContent = name;

  /* date line */
  document.getElementById('modalDateLine').innerHTML =
    '<strong>Date:</strong> ' + fDate + ' | ' + time;

  /* banner image - UPDATED */
  const bannerContainer = document.getElementById('modalBanner');
  bannerContainer.remove();

  /* description – use DB value; if empty, build a generic one */
  const descEl = document.getElementById('modalDescription');
  if(desc) {
    descEl.textContent = desc;
  } else {
    descEl.textContent = '\u201C' + name + '\u201D will be held on ' + fDate + ', ' + time +
      (venue ? ', at ' + venue + '.' : '.');
  }

  /* venue */
  const venueRow = document.getElementById('modalVenueRow');
  if(venue) { 
    venueRow.style.display = 'flex'; 
    document.getElementById('modalVenue').textContent = venue; 
  } else { 
    venueRow.style.display = 'none'; 
  }

  /* register link */
  const regRow = document.getElementById('modalRegisterRow');
  if(reg) {
    regRow.style.display = 'block';
    const a = document.getElementById('modalRegisterLink');
    a.href = reg; 
    a.textContent = reg;
  } else { 
    regRow.style.display = 'none'; 
  }

  /* organizer */
  const orgEl = document.getElementById('modalOrganizer');
  if(org) { 
    orgEl.style.display = 'block'; 
    orgEl.innerHTML = 'The event is organized by <strong>' + escapeHtml(org) + '</strong>.'; 
  } else { 
    orgEl.style.display = 'none'; 
  }

  /* open */
  document.getElementById('eventModalOverlay').classList.add('active');
  document.body.style.overflow = 'hidden';
}

// Helper function to escape HTML (add this function)
function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, m => map[m]);
}

      function closeEventDetail() {
        document.getElementById('eventModalOverlay').classList.remove('active');
        document.body.style.overflow = '';
      }

      function closeModalOutside(e) {
        if(e.target === document.getElementById('eventModalOverlay')) closeEventDetail();
      }

      document.addEventListener('keydown', e => {
        if(e.key === 'Escape' && document.getElementById('eventModalOverlay').classList.contains('active'))
          closeEventDetail();
      });
    </script>
  </body>
</html>