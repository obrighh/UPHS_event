        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme sidebar">
          <div class="app-brand demo">
          <a href="home.php" class="app-brand-link">
            <span class="app-brand-logo demo">
              <img src="../../../img/logo.jpg" alt="Logo" style="width: 60px; height: auto; border-radius: 50px;">
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2" style="font-size: 1.5rem;">Organization</span>
          </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
            </a>
          </div>

          <div class="menu-divider mt-0"></div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <li id="home" class="menu-item">
              <a href="home.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-alt"></i>
                <div class="text-truncate" data-i18n="Reports">Home</div>
              </a>
            </li>

            <!-- <li id="1" class="menu-item">
              <a href="org_activities.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Dashboards">Announcements</div>
              </a>
            </li> -->

            <li id="2" class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-calendar-event"></i>
                <div class="text-truncate" data-i18n="Reports">Manage Events</div>
              </a>
              <ul class="menu-sub">
                <li id="2.1" class="menu-item">
                  <a href="events.php?view=requests" class="menu-link">
                    <div class="text-truncate">Event requests</div>
                  </a>
                </li>
                <li id="2.2" class="menu-item">
                  <a href="events.php?view=accepted" class="menu-link">
                    <div class="text-truncate">Accepted Events</div>
                  </a>
                </li>
                <li id="2.3" class="menu-item">
                  <a href="events.php?view=declined" class="menu-link">
                    <div class="text-truncate">Declined Events</div>
                  </a>
                </li>
              </ul>
            </li>

            <!-- <li id="3" class="menu-item">
              <a href="announcement.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-message-square"></i>
                <div class="text-truncate" data-i18n="Reports">Manage Announcements</div>
              </a>
            </li> -->

            <li id="4.5" class="menu-item">
              <a href="accounts.php" class="menu-link" id="sidebar-account-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div class="text-truncate">Account Settings</div>
              </a>
            </li>
              </aside>