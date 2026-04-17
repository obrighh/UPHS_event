<?php
declare(strict_types=1);

function pagination_rows_per_page(): int
{
    return 10;
}

function pagination_parse_page(): int
{
    $p = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    return $p < 1 ? 1 : $p;
}

/**
 * @return array{0: int, 1: int, 2: int, 3: int} page, per_page, offset, total_pages
 */
function pagination_limits(int $total_rows, int $requested_page): array
{
    $per_page = pagination_rows_per_page();
    $total_pages = $total_rows > 0 ? (int) ceil($total_rows / $per_page) : 1;
    $page = $requested_page < 1 ? 1 : $requested_page;
    if ($page > $total_pages) {
        $page = $total_pages;
    }
    $offset = ($page - 1) * $per_page;
    return [$page, $per_page, $offset, $total_pages];
}

function pagination_render_nav(int $current_page, int $total_pages, int $total_rows): void
{
    if ($total_pages <= 1) {
        return;
    }

    $build = static function (int $p): string {
        $q = $_GET;
        $q['page'] = $p;
        return htmlspecialchars('?' . http_build_query($q), ENT_QUOTES, 'UTF-8');
    };

    $per_page = pagination_rows_per_page();
    $start = ($current_page - 1) * $per_page + 1;
    $end = min($total_rows, $current_page * $per_page);

    echo '<div class="card-body border-top d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">';
    echo '<p class="text-muted small mb-0">Showing ' . $start . '–' . $end . ' of ' . $total_rows . '</p>';
    echo '<nav aria-label="Page navigation"><ul class="pagination pagination-sm mb-0 flex-wrap">';

    $disabled = $current_page <= 1 ? ' disabled' : '';
    $href = $current_page <= 1 ? '#' : $build($current_page - 1);
    echo '<li class="page-item' . $disabled . '"><a class="page-link" href="' . $href . '">Previous</a></li>';

    $window = 5;
    $half = (int) floor($window / 2);
    $start_i = max(1, $current_page - $half);
    $end_i = min($total_pages, $start_i + $window - 1);
    $start_i = max(1, $end_i - $window + 1);

    if ($start_i > 1) {
        echo '<li class="page-item"><a class="page-link" href="' . $build(1) . '">1</a></li>';
        if ($start_i > 2) {
            echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }
    }

    for ($i = $start_i; $i <= $end_i; $i++) {
        $active = $i === $current_page ? ' active' : '';
        echo '<li class="page-item' . $active . '"><a class="page-link" href="' . $build($i) . '">' . $i . '</a></li>';
    }

    if ($end_i < $total_pages) {
        if ($end_i < $total_pages - 1) {
            echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }
        echo '<li class="page-item"><a class="page-link" href="' . $build($total_pages) . '">' . $total_pages . '</a></li>';
    }

    $disabled = $current_page >= $total_pages ? ' disabled' : '';
    $href = $current_page >= $total_pages ? '#' : $build($current_page + 1);
    echo '<li class="page-item' . $disabled . '"><a class="page-link" href="' . $href . '">Next</a></li>';

    echo '</ul></nav></div>';
}
