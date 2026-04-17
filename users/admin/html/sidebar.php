<!-- Menu -->
<?php
require_once __DIR__ . '/../../../actions/admin_contact_unread.php';
$adminInquiryUnread = 0;
if (isset($conn) && $conn instanceof mysqli) {
    $adminInquiryUnread = admin_contact_unread($conn);
}
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="home.php" class="app-brand-link">
      <span class="app-brand-logo demo">
      <img src="../../../img/logo.jpg" alt="Logo" style="width: 60px; height: auto; border-radius: 50px;">
      </span>
      <span class="app-brand-text demo menu-text fw-bold ms-2">Admin</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
    </a>
  </div>

  <div class="menu-divider mt-0"></div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">

  <li id="1.6" class="menu-item">
    <a href="home.php" class="menu-link">
      <i class="menu-icon tf-icons bx bx-home-alt"></i>
      <div class="text-truncate" data-i18n="Reports">Home</div>
    </a>
  </li>

  <li id="2" class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="menu-icon tf-icons bx bx-calendar-event"></i>
      <div class="text-truncate">Events</div>
    </a>
    <ul class="menu-sub">
      <li id="2.5" class="menu-item">
        <a href="events.php?view=pending" class="menu-link">
          <div class="text-truncate">Event requests</div>
        </a>
      </li>
      <li id="2.6" class="menu-item">
        <a href="events.php?view=approved" class="menu-link">
          <div class="text-truncate">Approved Events</div>
        </a>
      </li>
      <li id="2.7" class="menu-item">
        <a href="events.php?view=declined" class="menu-link">
          <div class="text-truncate">Declined Events</div>
        </a>
      </li>
    </ul>
  </li>

  <li id="3.5" class="menu-item">
    <a href="messages.php" class="menu-link d-flex align-items-center justify-content-between gap-2">
      <span class="d-flex align-items-center overflow-hidden">
        <i class="menu-icon tf-icons bx bx-message-square"></i>
        <div class="text-truncate" data-i18n="Reports">Messages</div>
      </span>
      <?php if ($adminInquiryUnread > 0): ?>
      <span class="badge rounded-pill bg-danger flex-shrink-0"><?php echo (int) $adminInquiryUnread; ?></span>
      <?php endif; ?>
    </a>
  </li>

  <li id="4" class="menu-item">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
      <i class="menu-icon tf-icons bx bx-dock-top"></i>
      <div class="text-truncate" data-i18n="Account Settings">Account Settings</div>
    </a>
    <ul class="menu-sub">
      <li id="4.4" class="menu-item">
        <a href="manageAccounts.php" class="menu-link" id="sidebar-account-link">Manage Accounts</a>
      </li>
      <li id="4.5" class="menu-item">
        <a href="accounts.php" class="menu-link" id="sidebar-profile-link">View Profile</a>
      </li>
    </ul>
  </li>
</aside>
