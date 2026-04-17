<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Manila');

include 'actions/conn.php';
require_once __DIR__ . '/actions/events_featured_compat.php';

$retentionChoices = [180, 365, 730];
$event_public_retention_days = isset($_GET['retention']) ? (int) $_GET['retention'] : 365;
if (!in_array($event_public_retention_days, $retentionChoices, true)) {
    $event_public_retention_days = 365;
}

$event_public_scope = isset($_GET['scope']) ? (string) $_GET['scope'] : 'upcoming';
if (!in_array($event_public_scope, ['upcoming', 'all'], true)) {
    $event_public_scope = 'upcoming';
}

$event_filter_month = isset($_GET['month']) ? trim((string) $_GET['month']) : '';
if ($event_filter_month !== '' && !preg_match('/^\d{4}-\d{2}$/', $event_filter_month)) {
    $event_filter_month = '';
}

if ($event_filter_month === '') {
    $event_filter_month = (new DateTime('now', new DateTimeZone('Asia/Manila')))->format('Y-m');
}

include 'actions/getEvent.php';

$events = array_slice($events, 0, 10);

$tz = new DateTimeZone('Asia/Manila');
$now = new DateTime('now', $tz);

require_once __DIR__ . '/actions/countdown_helper.php';

$cdFeat = events_has_featured_column($conn)
    ? ', COALESCE(is_featured, 0) AS is_featured'
    : '';
$cdSql = "SELECT event_id, event_name, activity AS description, date_start, date_end,
       time_start, time_end,
       CONCAT(date_start, ' - ', date_end) AS date,
       CONCAT(time_start, ' - ', time_end) AS time,
       venue, event_image
       {$cdFeat}
FROM events
WHERE event_status = 1 AND date_end >= CURDATE()";
$countdownPool = [];
$cdr = $conn->query($cdSql);
if ($cdr) {
    while ($r = $cdr->fetch_assoc()) {
        if ($cdFeat === '') {
            $r['is_featured'] = 0;
        }
        $countdownPool[] = $r;
    }
}

$nearestEvent = index_pick_countdown_event($countdownPool, $now, $tz);
$countdownIsFeatured = $nearestEvent && !empty($nearestEvent['is_featured']);

$countdownTarget = null;
if ($nearestEvent) {
    $targetDateTime = new DateTime(
        $nearestEvent['date_start'] . ' ' . $nearestEvent['time_start'],
        $tz
    );
    $countdownTarget = $targetDateTime->format('c');
}

$eventTitleHtml = $nearestEvent
    ? htmlspecialchars($nearestEvent['event_name'], ENT_QUOTES, 'UTF-8')
    : 'No Upcoming Events';

$default_event_list_subtitle = $event_public_scope === 'all'
    ? 'Upcoming and recent events (within your retention setting).'
    : 'Upcoming campus events — use filters to narrow the list.';

$contactFlash = '';
if (isset($_GET['contact'])) {
    $cf = (string) $_GET['contact'];
    if (in_array($cf, ['success', 'saved_no_mail', 'invalid', 'error', 'not_configured'], true)) {
        $contactFlash = $cf;
    }
}

?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Manup Template">
    <meta name="keywords" content="Manup, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>UNIVERSITY OF PERPETUAL</title>

    <link href="https://fonts.googleapis.com/css?family=Work+Sans:400,500,600,700,800,900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:ital,wght@0,700;1,600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="users/admin/assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <style>

  html {
    scroll-padding-top: 85px;
  }

  .header-section {
      /* border-bottom: 1px solid #dee2e6; */
      box-shadow: 0px 0px 8px 0px;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      background: linear-gradient(to right, #1e40af, #3b82f6);
  }

   /* Floating Button */
  .chatbot-btn {
    position: fixed;
    bottom: 25px;
    right: 25px;
    background: #0078ff;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 65px;
    height: 65px;
    font-size: 30px;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.3s ease;
    z-index: 1000;
  }

  .chatbot-btn:hover {
    background: #005edc;
    transform: scale(1.1);
  }

  .chat-container {
    position: fixed;
    bottom: 100px;
    right: 25px;
    width: 350px;
    max-width: 90%;
    height: 480px;
    background: rgba(255,255,255,0.95);
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 999;
    backdrop-filter: blur(10px);
  }

  .chat-header {
    background: #0078ff;
    color: white;
    text-align: center;
    padding: 15px;
    font-weight: bold;
  }

  .chat-messages {
    flex: 1;
    padding: 10px;
    overflow-y: auto;
    background: #f7f7f7;
    display: flex;
    flex-direction: column;
  }

  .message {
    margin: 8px 0;
    padding: 10px 14px;
    border-radius: 10px;
    max-width: 75%;
    line-height: 1.4;
    font-size: 14px;
  }

  .bot {
    background: #e4e6eb;
    align-self: flex-start;
  }

  .user {
    background: #0078ff;
    color: #fff;
    align-self: flex-end;
  }

  .chat-input {
    display: flex;
    border-top: 1px solid #ddd;
    background: #fff;
    padding: 8px;
  }

  .chat-input input {
    flex: 1;
    padding: 10px;
    border: none;
    outline: none;
    border-radius: 8px;
    background: #f0f0f0;
  }

  .chat-input button {
    background: #0078ff;
    color: white;
    border: none;
    margin-left: 10px;
    padding: 10px 14px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
  }

  .chat-input button:hover {
    background: #005edc;
  }

  @media (max-width: 500px) {
    .chat-container {
      right: 10px;
      bottom: 90px;
      width: 90%;
      height: 420px;
    }
    .chatbot-btn {
      width: 55px;
      height: 55px;
      font-size: 24px;
    }
  }

  /* ── Calendar Section ── */
  .calendar-section {
    background: #f0f2f5;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(25, 118, 210, 0.08);
    border: 1px solid #dde3ed;
  }
  .calendar-header { margin-bottom: 1rem; }
  .calendar-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1976D2;
    margin-bottom: 0.5rem;
  }

  /* ── Search Bar ── */
  .calendar-search input {
    background: #ffffff !important;
    border: 1px solid #c5cfe0 !important;
    color: #333 !important;
  }
  .calendar-search input::placeholder { color: #9aaac0; }
  .calendar-search input:focus { border-color: #1976D2 !important; box-shadow: 0 0 0 3px rgba(25,118,210,0.1); }

  /* ── Mini Calendar ── */
  .mini-calendar {
    margin-bottom: 1.5rem;
    background: #ffffff;
    border-radius: 8px;
    padding: 0.75rem;
    border: 1px solid #dde3ed;
  }
  .mini-calendar table { width: 100%; border-collapse: collapse; }
  .mini-calendar th,
  .mini-calendar td { text-align: center; padding: 0.5rem; font-size: 0.85rem; }
  .mini-calendar th { color: #1976D2; font-weight: 600; border-bottom: 1px solid #dde3ed; }
  .mini-calendar td { color: #444; cursor: pointer; border-radius: 4px; transition: background 0.2s; }
  .mini-calendar td:hover { background: #dde8f7; }

  .mini-calendar .today {
    background: #1976D2; color: white; border-radius: 4px; font-weight: 600;
  }
  .mini-calendar .has-event {
    background: #dbeeff; color: #1565C0; border-radius: 4px; font-weight: 600; position: relative;
  }
  .mini-calendar .has-event::after {
    content: ''; position: absolute; bottom: 2px; left: 50%; transform: translateX(-50%);
    width: 4px; height: 4px; background: #1976D2; border-radius: 50%;
  }
  .mini-calendar .has-event.selected { background: #1976D2; color: white; }
  .mini-calendar .has-event.selected::after { background: white; }
  .mini-calendar .other-month { color: #bbc8db; }

  .month-selector {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;
  }
  .month-selector h5 { margin: 0; font-size: 1rem; font-weight: 600; color: #1976D2; }
  .month-nav { display: flex; gap: 0.5rem; }
  .month-nav button {
    background: #eaf1fb; border: 1px solid #c5cfe0; cursor: pointer;
    font-size: 1.2rem; color: #1976D2; padding: 0.2rem 0.6rem;
    border-radius: 6px; transition: background 0.2s;
  }
  .month-nav button:hover { background: #1976D2; color: white; }

  .month-year-picker { position: relative; }
  .month-year-picker .picker-panel {
    position: absolute; z-index: 20; top: calc(100% + 0.5rem); left: 0; right: 0;
    background: #fff; border: 1px solid #c5cfe0; border-radius: 0.75rem;
    box-shadow: 0 18px 45px rgba(25,118,210,0.14); display: flex; gap: 0.75rem; padding: 0.75rem;
  }
  .month-year-picker .picker-column { flex: 1 1 50%; min-width: 0; }
  .month-year-picker .picker-header { font-size: 0.78rem; font-weight: 700; color: #1976D2; margin-bottom: 0.5rem; letter-spacing: 0.03em; text-transform: uppercase; }
  .month-year-picker .picker-list { max-height: 180px; overflow-y: auto; border: 1px solid #dde3ed; border-radius: 0.65rem; background: #fff; padding: 0.25rem; }
  .month-year-picker .picker-item {
    width: 100%; display: block; text-align: left; padding: 0.55rem 0.75rem;
    border: none; background: transparent; color: #2e3a4e; cursor: pointer; border-radius: 0.6rem;
    margin-bottom: 0.25rem; transition: background 0.2s, color 0.2s;
  }
  .month-year-picker .picker-item:hover { background: #e7f0ff; }
  .month-year-picker .picker-item.selected { background: #1976D2; color: #fff; }
  .month-year-picker .picker-panel.d-none { display: none; }

  .submission-btn {
    width: 100%; background: #1976D2; color: white; border: none; padding: 0.75rem;
    border-radius: 6px; font-weight: 500; cursor: pointer; margin-top: 1rem;
    transition: background 0.3s; letter-spacing: 0.3px;
  }
  .submission-btn:hover { background: #1565C0; }

  /* ── Event List ── */
  .event-list-header { margin-bottom: 1rem; padding: 0 0.25rem; }
  .event-list-header h5 { font-size: 1.2rem; font-weight: 600; color: #1976D2; margin: 0; }
  .event-list-header p  { font-size: 0.9rem; color: #7a8fa8; margin: 0.25rem 0 0; }

  .event-list { display: flex; flex-direction: column; gap: 1rem; }

  .event-card {
    display: flex; gap: 1rem;
    background: #f0f2f5;
    border-radius: 10px; padding: 1rem;
    box-shadow: 0 2px 6px rgba(25,118,210,0.07);
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    cursor: pointer; border: 2px solid #dde3ed;
  }
  .event-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(25,118,210,0.15);
    border-color: #1976D2;
    background: #eaf1fb;
  }
  .event-card.hidden { display: none; }
  .event-card.search-match { border-left: 4px solid #1976D2; background: #eaf1fb; }

  /* Pagination */
  .pagination-btn {
    transition: all 0.2s ease;
  }
  .pagination-btn:hover:not(:disabled) {
    background-color: #f0f2f5;
    border-color: #1976D2 !important;
  }
  .pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  .event-details { flex: 1; min-width: 0; }
  .event-title { font-size: 1.05rem; font-weight: 600; color: #1a2a3a; margin-bottom: 0.4rem; }

  .event-meta { display: flex; align-items: center; gap: 0.5rem; color: #5a6e85; font-size: 0.88rem; margin-bottom: 0.15rem; }
  .event-meta i { font-size: 1rem; color: #1976D2; }

  .no-events-message { text-align: center; padding: 3rem 1rem; color: #9aaac0; }
  .no-events-message i { font-size: 3rem; margin-bottom: 1rem; color: #c5cfe0; display: block; }

  /* ── Modal ── */
  .modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(10,30,60,0.45); z-index: 10001;
    justify-content: center; align-items: flex-start;
    padding: 2.5rem 1rem; overflow-y: auto;
    pointer-events: none;
  }
  .modal-overlay.active { display: flex; pointer-events: auto; }

  .detail-modal {
    background: #f4f6fa; border-radius: 12px; width: 100%; max-width: 700px;
    box-shadow: 0 12px 40px rgba(25,118,210,0.18); overflow: hidden;
    position: relative; animation: modalIn 0.22s ease;
    border: 1px solid #dde3ed;
  }
  @keyframes modalIn {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .modal-close-btn {
    position: absolute; top: 14px; right: 16px; z-index: 2;
    background: rgba(255,255,255,0.92); 
    border: none; border-radius: 50%;
    width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
    cursor: pointer; box-shadow: 0 2px 6px rgba(0,0,0,0.15); transition: background 0.2s;
  }
  .modal-close-btn:hover { background: #fff; }
  .modal-close-btn i { font-size: 1.15rem; color: #1976D2; }

  .modal-body { padding: 1.8rem 2rem 2rem; background: #f4f6fa; }

  .modal-title {
    font-size: 1.5rem; font-weight: 700; color: #1565C0;
    margin: 0 0 0.5rem; line-height: 1.3; text-align: center;
  }
  .modal-date-line {
    text-align: center; font-size: 0.9rem; color: #7a8fa8; margin: 0 0 1.2rem;
  }
  .modal-date-line strong { color: #1a2a3a; }

  .modal-divider { border: none; border-top: 1px solid #dde3ed; margin: 1.2rem 0; }

  .modal-description {
    font-size: 0.95rem; color: #4a5e72; line-height: 1.75; margin: 0 0 0.6rem;
  }

  .modal-meta-row {
    display: flex; align-items: flex-start; gap: 0.6rem; margin-bottom: 0.7rem;
  }
  .modal-meta-row i { font-size: 1.15rem; color: #1976D2; flex-shrink: 0; margin-top: 2px; }
  .modal-meta-row span { font-size: 0.93rem; color: #4a5e72; line-height: 1.5; }

  .modal-register-link {
    color: #1976D2; font-weight: 600; font-size: 0.93rem;
    text-decoration: none; transition: color 0.2s;
  }
  .modal-register-link:hover { color: #1565C0; text-decoration: underline; }

  .modal-organizer { font-size: 0.88rem; color: #9aaac0; margin-top: 1rem; }
  .modal-organizer strong { color: #5a6e85; }

  @media (max-width: 768px) {
    .modal-body { padding: 1.4rem 1.25rem 1.6rem; }
    .modal-title { font-size: 1.2rem; }
  }

  /* ── School countdown hero (matches site blue + lively accents) ── */
  .school-events-hero {
    background: linear-gradient(125deg, #0a1628 0%, #0d47a1 32%, #1565c0 55%, #1976d2 78%, #2196f3 100%);
    position: relative;
    overflow: hidden;
    border-radius: 0 0 20px 20px;
    box-shadow: 0 12px 40px rgba(13, 71, 161, 0.35);
  }
  .school-events-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 1;
    pointer-events: none;
  }
  .school-events-hero .container { position: relative; z-index: 1; }
  .hero-badge {
    display: inline-flex; align-items: center; gap: 0.35rem;
    background: linear-gradient(90deg, #ffc107, #ffca28);
    color: #1a237e; font-weight: 700; font-size: 0.75rem;
    text-transform: uppercase; letter-spacing: 0.06em;
    padding: 0.35rem 0.75rem; border-radius: 999px;
    margin-bottom: 0.75rem; box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  }
  .hero-event-title {
    font-family: 'Poppins', 'Work Sans', sans-serif;
    font-size: clamp(1.75rem, 4vw, 2.75rem);
    font-weight: 800;
    color: #fff;
    line-height: 1.15;
    margin: 0 0 0.5rem;
    text-shadow: 0 2px 16px rgba(0,0,0,0.25);
  }
  .hero-title-muted { opacity: 0.92; font-size: clamp(1.5rem, 3.5vw, 2.2rem); }
  .hero-tagline {
    font-size: 0.95rem; color: rgba(255,255,255,0.88); margin: 0 0 0.25rem;
    font-weight: 500;
  }
  .hero-countdown-label {
    font-size: 0.9rem;
    color: rgba(255,255,255,0.75);
    margin: 0;
    font-weight: 400;
  }
  .school-events-hero .cd-timer {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    align-items: stretch;
    justify-content: center;
    gap: 0.6rem;
    width: 100%;
    max-width: 100%;
    margin: 0;
    text-align: center;
  }
  @media (min-width: 992px) {
    .school-events-hero .cd-timer { justify-content: flex-end; }
  }
  .school-events-hero .cd-timer .cd-item {
    flex: 0 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.12);
    border-radius: 12px;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.25);
    margin: 0;
    width: clamp(4.25rem, 14vw, 5.5rem);
    min-height: 5.25rem;
    padding: 0.65rem 0.4rem;
    box-sizing: border-box;
  }
  .school-events-hero .cd-timer .cd-item:first-child { margin-left: 0; }
  .school-events-hero .cd-timer .cd-item:after {
    border: none;
    opacity: 0;
  }
  .school-events-hero .cd-timer .cd-item span {
    font-size: clamp(1.5rem, 4.5vw, 2rem);
    line-height: 1;
    color: #fff;
    font-weight: 700;
  }
  .school-events-hero .cd-timer .cd-item p {
    font-size: 0.65rem;
    color: rgba(255,255,255,0.85);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin: 0.35rem 0 0;
    line-height: 1.2;
  }

  .school-filters {
    background: linear-gradient(180deg, #ffffff 0%, #f0f7ff 100%);
    border: 1px solid #bbdefb !important;
    border-radius: 12px;
  }
  .school-filters label { font-size: 0.75rem; color: #1565c0; font-weight: 600; }

  .event-card-featured {
    border-color: #dde3ed !important;
    box-shadow: 0 2px 6px rgba(25,118,210,0.07) !important;
    background: #f0f2f5 !important;
  }
  .event-card-pill {
    display: inline-block;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    background: #ffc107;
    color: #1a237e;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    margin-bottom: 0.35rem;
  }

  /* ── Full-width main landing banner (campus photo) ── */
  .landing-main-banner {
    position: relative;
    min-height: clamp(320px, 62vh, 720px);
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(13, 39, 80, 0.35);
  }
  .landing-main-banner__slides {
    position: absolute;
    inset: 0;
    z-index: 0;
  }
  .landing-main-banner__slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center 40%;
    transform: scale(1.04);
    opacity: 0;
    transition: opacity 1.1s ease-in-out;
    pointer-events: none;
  }
  .landing-main-banner__slide.is-active {
    opacity: 1;
  }
  .landing-main-banner__overlay {
    position: absolute;
    inset: 0;
    z-index: 1;
    background:
      linear-gradient(105deg, rgba(10, 25, 60, 0.92) 0%, rgba(13, 71, 161, 0.55) 38%, rgba(25, 118, 210, 0.35) 65%, rgba(0, 0, 0, 0.25) 100%),
      linear-gradient(0deg, rgba(8, 20, 45, 0.85) 0%, transparent 45%);
    pointer-events: none;
  }
  .landing-main-banner__content {
    position: relative;
    z-index: 2;
    width: 100%;
    padding: 2rem 0 2.75rem;
  }
  .landing-main-banner__watermark {
    position: absolute;
    right: 4%;
    top: 18%;
    font-family: 'Poppins', sans-serif;
    font-size: clamp(2.5rem, 10vw, 6rem);
    font-weight: 800;
    color: rgba(255, 255, 255, 0.07);
    line-height: 0.9;
    text-align: right;
    pointer-events: none;
    z-index: 1;
    max-width: 55%;
  }
  .landing-main-banner__eyebrow {
    font-family: 'Poppins', sans-serif;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: #90caf9;
    margin: 0 0 0.35rem;
  }
  .landing-main-banner__title {
    font-family: 'Poppins', 'Work Sans', sans-serif;
    font-size: clamp(1.65rem, 4.2vw, 3.1rem);
    font-weight: 800;
    color: #fff;
    line-height: 1.1;
    margin: 0 0 0.75rem;
    text-shadow: 0 4px 24px rgba(0, 0, 0, 0.35);
    max-width: 18ch;
  }
  .landing-main-banner__accent {
    font-family: 'Great Vibes', cursive;
    font-weight: 400;
    font-size: clamp(2.2rem, 6vw, 4rem);
    color: #ffc107;
    display: inline-block;
    margin-left: 0.15em;
    text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
  }
  .landing-main-banner__sub {
    font-size: clamp(0.9rem, 2vw, 1.05rem);
    color: rgba(255, 255, 255, 0.9);
    max-width: 36rem;
    line-height: 1.55;
    margin: 0 0 1.25rem;
    text-shadow: 0 1px 8px rgba(0, 0, 0, 0.4);
  }
  .landing-main-banner__cta {
    display: inline-block;
    font-family: 'Poppins', sans-serif;
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #fff;
    padding: 0.65rem 1.35rem;
    border: 2px solid rgba(255, 255, 255, 0.9);
    border-radius: 4px;
    text-decoration: none;
    transition: background 0.2s, border-color 0.2s, color 0.2s;
  }
  .landing-main-banner__cta:hover {
    background: #ffc107;
    border-color: #ffc107;
    color: #0d1f4a;
  }
  @media (max-width: 576px) {
    .landing-main-banner__slide { background-position: center 30%; }
    .landing-main-banner__watermark { display: none; }
  }

  /* ── Contact section (site blue + gold) ── */
  #contact-section.contact-from-section {
    background: linear-gradient(180deg, #f5f9ff 0%, #e3f2fd 100%);
    border-top: 1px solid #bbdefb;
  }
  #contact-section .section-title h2 {
    font-family: 'Poppins', 'Work Sans', sans-serif;
    font-weight: 800;
    color: #0d47a1;
  }
  #contact-section .section-title p {
    color: #37474f;
    max-width: 36rem;
    margin-left: auto;
    margin-right: auto;
  }
  #contact-section .contact-form .form-control,
  #contact-section .contact-form input,
  #contact-section .contact-form textarea {
    border: 1px solid #90caf9;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  #contact-section .contact-form input:focus,
  #contact-section .contact-form textarea:focus {
    border-color: #1976d2;
    box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.2);
    outline: none;
  }
  #contact-section .contact-form textarea {
    min-height: 140px;
    resize: vertical;
  }
  #contact-section .contact-send-btn {
    font-family: 'Poppins', sans-serif;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    font-size: 0.8rem;
    padding: 0.75rem 2.25rem;
    border: none;
    border-radius: 999px;
    color: #fff;
    background: linear-gradient(135deg, #0d47a1 0%, #1976d2 55%, #2196f3 100%);
    box-shadow: 0 6px 20px rgba(13, 71, 161, 0.35);
    transition: transform 0.15s ease, box-shadow 0.2s ease, filter 0.2s ease;
  }
  #contact-section .contact-send-btn:hover {
    color: #fff;
    filter: brightness(1.05);
    transform: translateY(-1px);
    box-shadow: 0 8px 26px rgba(13, 71, 161, 0.45);
  }
  #contact-section .contact-send-btn:focus-visible {
    outline: 3px solid #ffc107;
    outline-offset: 3px;
  }

  .contact-flash-toast {
    position: fixed;
    bottom: 1.25rem;
    right: 1.25rem;
    z-index: 10050;
    max-width: 22rem;
    box-shadow: 0 12px 40px rgba(13, 39, 80, 0.25);
    border-radius: 12px;
    border: 1px solid rgba(25, 118, 210, 0.2);
  }
</style>
</head>

<body style="padding-top: 80px;">
    <?php 
        include 'navbar.php';
    ?>

    <section class="landing-main-banner" aria-label="Campus highlights">
      <div class="landing-main-banner__slides" role="presentation" aria-hidden="true">
        <div class="landing-main-banner__slide is-active" style="background-image:url('img/hero-slides/hero-1.png');"></div>
        <div class="landing-main-banner__slide" style="background-image:url('img/hero-slides/hero-2.png');"></div>
        <div class="landing-main-banner__slide" style="background-image:url('img/hero-slides/hero-3.png');"></div>
      </div>
      <div class="landing-main-banner__overlay" role="presentation"></div>
      <div class="landing-main-banner__watermark" aria-hidden="true">QUALITY<br>&amp;<br>Excellence</div>
      <div class="container landing-main-banner__content">
        <p class="landing-main-banner__eyebrow">Pioneering unparalleled</p>
        <h2 class="landing-main-banner__title">Quality &amp;<br><span class="landing-main-banner__accent">Excellence</span></h2>
        <p class="landing-main-banner__sub">Where every moment on campus finds its place—from events to celebrations, all in one space.</p>
        <a class="landing-main-banner__cta" href="#events-calendar">View events calendar</a>
      </div>
    </section>

    <section class="school-events-hero my-0">
      <div class="container py-4 py-lg-5">
          <div class="row align-items-center g-4">
              <div class="col-lg-5">
                  <?php if ($nearestEvent): ?>
                      <h1 class="hero-event-title"><?php echo $eventTitleHtml; ?></h1>
                      <p class="hero-tagline">University events &mdash; mark your calendar</p>
                      <p class="hero-countdown-label">Count every second until this event begins</p>
                  <?php else: ?>
                      <h1 class="hero-event-title hero-title-muted">See you at the next campus event!</h1>
                      <p class="hero-tagline">University events calendar</p>
                      <p class="hero-countdown-label">No upcoming events in the current view &mdash; try adjusting filters below or check back soon.</p>
                  <?php endif; ?>
              </div>
              <div class="col-lg-7">
                  <?php if ($countdownTarget): ?>
                  <div class="cd-timer" id="countdown" data-php-countdown="1">
                      <div class="cd-item"><span id="cd-days">00</span><p>Days</p></div>
                      <div class="cd-item"><span id="cd-hrs">00</span><p>Hrs</p></div>
                      <div class="cd-item"><span id="cd-mins">00</span><p>Mins</p></div>
                      <div class="cd-item"><span id="cd-secs">00</span><p>Secs</p></div>
                  </div>
                  <?php else: ?>
                  <div class="cd-timer text-center text-lg-end">
                      <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 1rem;">Browse scheduled activities in the calendar below.</p>
                  </div>
                  <?php endif; ?>
              </div>
          </div>
      </div>
    </section>
    <div class="layout-page py-4">
        <!-- Schedule Section Begin -->
        <div class="container">
            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">

                <div id="events-calendar" class="school-filters card border-0 shadow-sm mb-4">
                  <div class="card-body py-3">
                    <form method="get" class="row g-3 g-lg-4 align-items-end">
                      <div class="col-12 col-md-5 col-lg-4 position-relative">
                        <label class="form-label mb-1" for="monthPickerInput">Month and Year</label>
                        <div class="month-year-picker" id="monthYearPicker">
                          <input type="text" id="monthPickerInput" class="form-control form-control-sm" readonly placeholder="Select month and year" onclick="toggleMonthYearPicker()" aria-haspopup="true" aria-expanded="false">
                          <input type="hidden" name="month" id="filterMonth" value="<?php echo htmlspecialchars($event_filter_month); ?>">
                          <input type="hidden" name="year" id="filterYear" value="<?php echo htmlspecialchars($event_filter_month ? substr($event_filter_month, 0, 4) : ''); ?>">
                          <div class="picker-panel d-none" id="monthYearPickerPanel">
                            <div class="picker-column">
                              <div class="picker-header">Month</div>
                              <div class="picker-list" id="monthList"></div>
                            </div>
                            <div class="picker-column">
                              <div class="picker-header">Year</div>
                              <div class="picker-list" id="yearList"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-12 col-md-auto d-flex flex-wrap gap-2 align-items-end">
                        <button type="submit" class="btn btn-sm btn-primary">Apply filters</button>
                        <a href="index.php" class="btn btn-sm btn-outline-secondary">Reset</a>
                      </div>
                    </form>
                  </div>
                </div>

                <div class="row">

                    <!-- Left – Calendar -->
                <div class="col-lg-4 col-md-4 mb-4">
                <div class="calendar-section">
                    <div class="calendar-header">
                    <h4 class="calendar-title">Campus calendar</h4>
                    </div>

                    <!-- Search Bar -->
                    <div class="calendar-search" style="margin-bottom: 1rem;">
                    <div style="position: relative;">
                        <input 
                        type="text" 
                        id="topNavSearch" 
                        placeholder="Search events..." 
                        style="
                            width: 100%;
                            padding: 9px 36px 9px 12px;
                            border: 1px solid #ddd;
                            border-radius: 8px;
                            font-size: 0.875rem;
                            outline: none;
                            transition: border-color 0.2s;
                            box-sizing: border-box;
                        "
                        onfocus="this.style.borderColor='#1976D2'"
                        onblur="this.style.borderColor='#ddd'"
                        >
                        <i class="tf-icons bx bx-search" style="
                        position: absolute;
                        right: 10px;
                        top: 50%;
                        transform: translateY(-50%);
                        color: #999;
                        font-size: 1.1rem;
                        pointer-events: none;
                        "></i>
                    </div>
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
                        <h5 id="eventListTitle">School calendar</h5>
                        <p id="eventListSubtitle"><?php echo htmlspecialchars($default_event_list_subtitle, ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>

                    <div class="event-list" id="eventList">
                        <?php $event_index = 0; foreach($events as $row):
                        $dateString = $row['date'];
                        if (strpos($dateString, ' - ') !== false) {
                            $dateParts  = explode(' - ', $dateString);
                            $dateString = trim($dateParts[0]);
                        }
                        $date          = new DateTime($dateString);
                        $formattedDate = $date->format('F j, Y');
                        
                        // Determine image path
                        $imagePath = 'uploads/events/eventPlaceholder.png'; // default
                        if (!empty($row['event_image'])) {
                            $imagePath = 'uploads/events/' . htmlspecialchars($row['event_image']);
                        }
                        $rowFeatured = !empty($row['is_featured']);
                        $cardClass = 'event-card' . ($rowFeatured ? ' event-card-featured' : '');
                        $page_num = intdiv($event_index, 5) + 1;
                        ?>
                        <div class="<?php echo $cardClass; ?> event-card-item"
                            data-page="<?php echo $page_num; ?>"
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
                            data-event-featured="<?php echo $rowFeatured ? '1' : '0'; ?>"
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
                        <?php $event_index++; endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-controls" id="paginationControls" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem;">
                        <button onclick="goToPage(1)" id="btnFirst" class="pagination-btn" style="padding: 0.5rem 1rem; border: 1px solid #dde3ed; border-radius: 6px; color: #1976D2; background: white; cursor: pointer; font-weight: 500;">First</button>
                        <button onclick="goToPage(currentPage - 1)" id="btnPrev" class="pagination-btn" style="padding: 0.5rem 1rem; border: 1px solid #dde3ed; border-radius: 6px; color: #1976D2; background: white; cursor: pointer; font-weight: 500;">← Previous</button>
                        <span id="pageInfo" style="color: #777; font-weight: 500;"></span>
                        <button onclick="goToPage(currentPage + 1)" id="btnNext" class="pagination-btn" style="padding: 0.5rem 1rem; border: 1px solid #dde3ed; border-radius: 6px; color: #1976D2; background: white; cursor: pointer; font-weight: 500;">Next →</button>
                        <button onclick="goToPage(totalPages)" id="btnLast" class="pagination-btn" style="padding: 0.5rem 1rem; border: 1px solid #dde3ed; border-radius: 6px; color: #1976D2; background: white; cursor: pointer; font-weight: 500;">Last</button>
                    </div>

                    <script>
                      let currentPage = 1;
                      const eventsPerPage = 5;
                      const allEventCards = document.querySelectorAll('.event-card-item');
                      const totalEvents = allEventCards.length;
                      const totalPages = Math.ceil(totalEvents / eventsPerPage);

                      function showPage(page) {
                        if (page < 1 || page > totalPages) return;
                        currentPage = page;

                        // Hide all cards first
                        allEventCards.forEach(card => card.style.display = 'none');

                        // Show only cards for current page
                        const startIdx = (page - 1) * eventsPerPage;
                        const endIdx = Math.min(page * eventsPerPage, totalEvents);
                        for (let i = startIdx; i < endIdx; i++) {
                          allEventCards[i].style.display = 'flex';
                        }

                        // Update pagination buttons
                        document.getElementById('btnFirst').disabled = (page === 1);
                        document.getElementById('btnPrev').disabled = (page === 1);
                        document.getElementById('btnNext').disabled = (page === totalPages);
                        document.getElementById('btnLast').disabled = (page === totalPages);

                        document.getElementById('pageInfo').textContent = `Page ${page} of ${totalPages}`;

                        // Show/hide pagination if only 1 page
                        if (totalPages <= 1) {
                          document.getElementById('paginationControls').style.display = 'none';
                        }
                      }

                      function goToPage(page) {
                        showPage(page);
                      }

                      // Initialize
                      showPage(1);
                    </script>

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
            <div class="modal-overlay" id="eventModalOverlay" onclick="closeModalOutside(event)">
        <div class="detail-modal">
            <button class="modal-close-btn" onclick="closeEventDetail()">
            <i class="tf-icons bx bx-x"></i>
            </button>

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
    </div>
    <!-- Schedule Section End -->
    <hr>
    <!-- Contact Form Section Begin -->
    <section id="contact-section" class="contact-from-section spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title text-center">
                        <h2>We'd love to hear from you</h2>
                        <p>Send us a message about campus events, partnerships, or general questions. We will reply to the email address you provide.</p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <form action="actions/contacts.php" method="post" class="comment-form contact-form" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="sr-only" for="contactName">Name</label>
                                <input class="form-control" type="text" id="contactName" name="name" placeholder="Your name" required maxlength="200" autocomplete="name">
                            </div>
                            <div class="col-md-6">
                                <label class="sr-only" for="contactEmail">Email</label>
                                <input class="form-control" type="email" id="contactEmail" name="email" placeholder="Your email" required maxlength="200" autocomplete="email">
                            </div>
                            <div class="col-12">
                                <label class="sr-only" for="contactMessage">Message</label>
                                <textarea class="form-control" id="contactMessage" name="message" placeholder="Your message" required maxlength="8000"></textarea>
                            </div>
                            <div class="col-12 text-center pt-1">
                                <button type="submit" name="submitContact" value="1" class="contact-send-btn">Send message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section Begin -->
    <footer class="footer-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="footer-text">
                        <div class="ft-logo">
                            <a href="#" class="footer-logo"><img src="img/logo_1.png" alt="" height="110px" width="90px"></a>
                        </div>
                        <div class="ft-social">
                            <a href="https://www.facebook.com/uphgma.info.ph/"><i class="fa fa-facebook"></i></a>
                            <a href="https://www.instagram.com/uphs.gma/"><i class="fa fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->

    <!-- Chat Button -->
    <button class="chatbot-btn" id="chatbotBtn">💬</button>

    <!-- Chat Container -->
    <div class="chat-container" id="chatContainer">
    <div class="chat-header">UPHS-GMA AI Assistant 🤖</div>
    <div class="chat-messages" id="chatMessages">
        <div class="message bot">Hello! I'm your University of Perpetual Help System - GMA Campus assistant. Ask me about events, schedules, or anything about the university!</div>
    </div>
    <div class="chat-input">
        <input type="text" id="userInput" placeholder="Ask about events, schedule, etc...">
        <button id="sendBtn">Send</button>
    </div>
    </div>

    <?php if ($contactFlash !== ''): ?>
    <?php
      $toastClass = 'alert-info';
      $toastTitle = 'Notice';
      $toastBody = '';
      if ($contactFlash === 'success') {
          $toastClass = 'alert-success';
          $toastTitle = 'Message sent';
          $toastBody = 'Thank you! Your inquiry was received. We will get back to you at the email you provided.';
      } elseif ($contactFlash === 'saved_no_mail') {
          $toastClass = 'alert-warning';
          $toastTitle = 'Message saved';
          $toastBody = 'Your inquiry was saved. The server could not send email automatically—if this persists, contact IT to enable mail (SMTP). Your message is still on file for staff.';
      } elseif ($contactFlash === 'invalid') {
          $toastClass = 'alert-danger';
          $toastTitle = 'Could not send';
          $toastBody = 'Please enter a valid name, email, and message, then try again.';
      } elseif ($contactFlash === 'not_configured') {
          $toastClass = 'alert-warning';
          $toastTitle = 'Inbox not configured';
          $toastBody = 'The site administrator has not set a valid admin email yet, so your message could not be sent. Please try again later or contact the school directly.';
      } else {
          $toastClass = 'alert-danger';
          $toastTitle = 'Something went wrong';
          $toastBody = 'We could not save your message. Please try again in a few minutes.';
      }
    ?>
    <div id="contactFlashToast" class="alert <?php echo htmlspecialchars($toastClass, ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show contact-flash-toast" role="alert">
      <strong><?php echo htmlspecialchars($toastTitle, ENT_QUOTES, 'UTF-8'); ?>.</strong>
      <?php echo htmlspecialchars($toastBody, ENT_QUOTES, 'UTF-8'); ?>
      <button type="button" class="close js-contact-flash-close" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <script>
    (function () {
      var t = document.getElementById('contactFlashToast');
      if (!t) return;
      var rm = function () { if (t && t.parentNode) t.parentNode.removeChild(t); };
      var btn = t.querySelector('.js-contact-flash-close');
      if (btn) btn.addEventListener('click', rm);
      setTimeout(rm, 9000);
    })();
    </script>
    <?php endif; ?>

    <script src="users/admin/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="users/admin/assets/vendor/libs/popper/popper.js"></script>
    <script src="users/admin/assets/vendor/js/bootstrap.js"></script>
    <script src="users/admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="users/admin/assets/vendor/js/menu.js"></script>
    <script src="users/admin/assets/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="users/admin/assets/js/main.js"></script>
    <script src="users/admin/assets/js/dashboards-analytics.js"></script>

    <script>
    const chatBtn = document.getElementById("chatbotBtn");
    const chatContainer = document.getElementById("chatContainer");
    const sendBtn = document.getElementById("sendBtn");
    const chatMessages = document.getElementById("chatMessages");
    const userInput = document.getElementById("userInput");

    let upcomingEvents = null;
    let pastEvents = null;
    let lastMessageTime = 0;
    const MESSAGE_COOLDOWN = 2000;

    // Load upcoming events on page load
    async function loadEventsData(timeFilter = 'upcoming') {
        try {
        const response = await fetch('actions/get_events_for_chatbot.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ timeFilter: timeFilter })
        });
        const data = await response.json();
        
        if (data.success) {
            if (timeFilter === 'upcoming') {
            upcomingEvents = data.events;
            console.log('✅ Upcoming events loaded:', data.count);
            } else {
            pastEvents = data.events;
            console.log('✅ Past events loaded:', data.count);
            }
            return data.events;
        }
        } catch (error) {
        console.error('❌ Error loading events:', error);
        return null;
        }
    }

    // Load upcoming events on page load
    loadEventsData('upcoming');

    // Toggle chat
    chatBtn.addEventListener("click", () => {
        chatContainer.style.display = 
        chatContainer.style.display === "flex" ? "none" : "flex";
        if (chatContainer.style.display === "flex") {
        userInput.focus();
        }
    });

    // Send message
    sendBtn.addEventListener("click", sendMessage);
    userInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
        e.preventDefault();
        sendMessage();
        }
    });

    function appendMessage(text, sender) {
        const msg = document.createElement("div");
        msg.classList.add("message", sender);
        msg.innerHTML = text.replace(/\n/g, '<br>');
        chatMessages.appendChild(msg);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return msg;
    }

    async function sendMessage() {
        const userMsg = userInput.value.trim();
        if (!userMsg) return;

        // Rate limiting
        const now = Date.now();
        if (now - lastMessageTime < MESSAGE_COOLDOWN) {
        appendMessage("⏱️ Please wait a moment...", "bot");
        return;
        }
        lastMessageTime = now;

        appendMessage(userMsg, "user");
        userInput.value = "";

        const typingIndicator = appendMessage("Thinking...", "bot");
        sendBtn.disabled = true;
        userInput.disabled = true;

        try {
        const botReply = await getGeminiReply(userMsg);
        typingIndicator.remove();
        appendMessage(botReply, "bot");
        } catch (error) {
        console.error('❌ Error:', error);
        typingIndicator.remove();
        appendMessage("Please be more specific with your request.", "bot");
        } finally {
        sendBtn.disabled = false;
        userInput.disabled = false;
        }
    }

    function formatEventsForAI(events, eventType = 'upcoming') {
        if (!events || events.length === 0) {
        return eventType === 'upcoming' 
            ? "No upcoming events scheduled." 
            : "No past events found in the records.";
        }

        let eventsText = eventType === 'upcoming' 
        ? `Current upcoming events at UPHS-GMA:\n\n`
        : `Past events at UPHS-GMA:\n\n`;
        
        events.forEach((event, index) => {
        eventsText += `${index + 1}. ${event.name}\n`;
        
        if (event.description && event.description !== 'No description available') {
            eventsText += `   Description: ${event.description}\n`;
        }
        
        eventsText += `   Date: ${event.date_start}`;
        if (event.date_start !== event.date_end) {
            eventsText += ` to ${event.date_end}`;
        }
        eventsText += `\n`;
        eventsText += `   Time: ${event.time_range}\n`;
        eventsText += `   Venue: ${event.venue}\n\n`;
        });

        return eventsText;
    }

    async function getGeminiReply(userMessage) {
        // Detect if user is asking about past events
        const askingAboutPast = /\b(past|previous|before|earlier|last|History|happened|old)\b/i.test(userMessage);
        
        // Load appropriate events if not already loaded
        let eventsToUse;
        let eventType;
        
        if (askingAboutPast) {
        if (!pastEvents) {
            await loadEventsData('past');
        }
        eventsToUse = pastEvents;
        eventType = 'past';
        } else {
        if (!upcomingEvents) {
            await loadEventsData('upcoming');
        }
        eventsToUse = upcomingEvents;
        eventType = 'upcoming';
        }
        
        const eventsInfo = formatEventsForAI(eventsToUse, eventType);
        
        const systemContext = `You are the UPHS-GMA AI Assistant for University of Perpetual Help System - GMA Campus.

    Location: San Gabriel, General Mariano Alvarez, Cavite, Philippines
    Established: 1997
    Motto: "Character Building is Nation Building"

    Programs Offered:
    - Basic Education to Senior High School
    - Nursing, Engineering, IT, Business Administration, Tourism, Allied Health Sciences

    ${eventsInfo}

    Instructions:
    - Be friendly, helpful, and concise
    - When asked about upcoming events/schedule, provide information from the upcoming events list above
    - When asked about past/previous/history of events, provide information from the past events list above
    - If there are no events, clearly state "There are no ${eventType} events scheduled" or "No ${eventType} events found"
    - Format information clearly with dates, times, and venues
    - Include event descriptions when available
    - Keep responses under 150 words unless more detail is requested`;

        const requestData = {
        contents: [{
            parts: [{
            text: systemContext + "\n\nUser question: " + userMessage
            }]
        }]
        };

        try {
        const response = await fetch('actions/gemini_chat_api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(requestData)
        });

        if (!response.ok) {
            throw new Error(`API error: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.error);
        }

        if (data.candidates && 
            data.candidates.length > 0 && 
            data.candidates[0].content && 
            data.candidates[0].content.parts && 
            data.candidates[0].content.parts.length > 0) {
            return data.candidates[0].content.parts[0].text;
        }

        throw new Error('Invalid response format');

        } catch (error) {
        console.error('Gemini API Error:', error);
        throw error;
        }
    }

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
      const defaultListTitle = 'School calendar';
      const defaultListSubtitle = <?php echo json_encode($default_event_list_subtitle); ?>;
      let selectedDate = null;
      let isSearching  = false;

      document.addEventListener('DOMContentLoaded', function() {
        generateMiniCalendar(new Date());
        initializeTopNavSearch();
        initializeMonthYearPicker();
      });

      function initializeMonthYearPicker() {
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        const monthList = document.getElementById('monthList');
        const yearList = document.getElementById('yearList');
        const monthInput = document.getElementById('filterMonth');
        const yearInput = document.getElementById('filterYear');
        const displayInput = document.getElementById('monthPickerInput');
        const panel = document.getElementById('monthYearPickerPanel');
        if (!monthList || !yearList || !monthInput || !yearInput || !displayInput || !panel) return;

        const selectedValue = monthInput.value || '';
        let selectedYear = yearInput.value || selectedValue.slice(0, 4);
        let selectedMonth = selectedValue.slice(5, 7) || '';

        function renderMonths() {
          monthList.innerHTML = monthNames.map((name, index) => {
            const value = String(index + 1).padStart(2, '0');
            const selected = selectedMonth === value ? ' selected' : '';
            return `<button type="button" class="picker-item${selected}" data-month="${value}">${name}</button>`;
          }).join('');
        }

        function renderYears() {
          const currentYear = new Date().getFullYear();
          const startYear = currentYear - 3;
          const endYear = currentYear + 5;
          const years = [];
          for (let year = startYear; year <= endYear; year++) {
            const selected = String(year) === selectedYear ? ' selected' : '';
            years.push(`<button type="button" class="picker-item${selected}" data-year="${year}">${year}</button>`);
          }
          yearList.innerHTML = years.join('');
        }

        function updateHiddenInputs() {
          yearInput.value = selectedYear || '';
          if (selectedYear && selectedMonth) {
            monthInput.value = `${selectedYear}-${selectedMonth}`;
          } else {
            monthInput.value = '';
          }
        }

        function updateDisplay() {
          let labelParts = [];
          if (selectedMonth) {
            labelParts.push(monthNames[Number(selectedMonth) - 1]);
          }
          if (selectedYear) {
            labelParts.push(selectedYear);
          }
          displayInput.value = labelParts.join(' ');
          updateHiddenInputs();
        }

        function selectMonth(value) {
          selectedMonth = value;
          renderMonths();
          updateDisplay();
        }

        function selectYear(value) {
          selectedYear = value;
          renderYears();
          updateDisplay();
        }

        monthList.addEventListener('click', event => {
          const button = event.target.closest('button[data-month]');
          if (!button) return;
          selectMonth(button.dataset.month);
        });
        yearList.addEventListener('click', event => {
          const button = event.target.closest('button[data-year]');
          if (!button) return;
          selectYear(button.dataset.year);
        });

        document.addEventListener('click', event => {
          if (!document.getElementById('monthYearPicker').contains(event.target)) {
            panel.classList.add('d-none');
          }
        });

        renderMonths();
        renderYears();
        updateDisplay();
      }

      function toggleMonthYearPicker() {
        const panel = document.getElementById('monthYearPickerPanel');
        if (!panel) return;
        panel.classList.toggle('d-none');
      }

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
        document.querySelectorAll('.event-card-item').forEach(card => {
          card.classList.remove('search-match');
          const ev = card.dataset.eventDate;
          const start = ev.includes(' - ') ? ev.split(' - ')[0].trim() : ev;
          if(start === dStr) {
            card.classList.remove('hidden');
            card.style.display = 'flex';
            count++;
          } else {
            card.classList.add('hidden');
            card.style.display = 'none';
          }
        });

        document.getElementById('paginationControls').style.display = 'none';
        toggleNoEvents(count === 0, 'No events found for this date');
      }

      /* ─── Show all ─── */
      function showAllEvents() {
        selectedDate = null;
        document.getElementById('topNavSearch').value = '';
        isSearching = false;
        generateMiniCalendar(currentDate);
        document.getElementById('eventListTitle').textContent    = defaultListTitle;
        document.getElementById('eventListSubtitle').textContent = defaultListSubtitle;
        document.querySelectorAll('.event-card-item').forEach(c => {
          c.classList.remove('hidden','search-match');
          c.style.display = '';
        });
        toggleNoEvents(false);
        showPage(1);
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
      }

      function closeModalOutside(e) {
        if(e.target === document.getElementById('eventModalOverlay')) closeEventDetail();
      }

      document.addEventListener('keydown', e => {
        if(e.key === 'Escape' && document.getElementById('eventModalOverlay').classList.contains('active'))
          closeEventDetail();
      });

      <?php if($countdownTarget): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const countdownTarget = new Date("<?php echo $countdownTarget; ?>").getTime();

            function tickCountdown() {
                const now = new Date().getTime();
                const distance = countdownTarget - now;

                if (distance <= 0) {
                    document.getElementById('cd-days').textContent = '00';
                    document.getElementById('cd-hrs').textContent  = '00';
                    document.getElementById('cd-mins').textContent = '00';
                    document.getElementById('cd-secs').textContent = '00';
                    return false;
                }

                document.getElementById('cd-days').textContent = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
                document.getElementById('cd-hrs').textContent  = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
                document.getElementById('cd-mins').textContent = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
                document.getElementById('cd-secs').textContent = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
                return true;
            }

            tickCountdown();
            const countdownInterval = setInterval(function() {
                if (!tickCountdown()) {
                    clearInterval(countdownInterval);
                }
            }, 1000);
        });
      <?php endif; ?>

      document.addEventListener('DOMContentLoaded', function () {
        var slides = document.querySelectorAll('.landing-main-banner__slide');
        if (slides.length < 2) {
          return;
        }
        var idx = 0;
        setInterval(function () {
          slides[idx].classList.remove('is-active');
          idx = (idx + 1) % slides.length;
          slides[idx].classList.add('is-active');
        }, 5500);
      });
    </script>

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>