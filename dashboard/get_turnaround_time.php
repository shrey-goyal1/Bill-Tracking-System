<?php
function calculateTimeDifference($date1, $time1, $date2, $time2) {
    // Including date and time into a single string
    $datetime1 = $date1 . ' ' . $time1;
    $datetime2 = $date2 . ' ' . $time2;

    $start = new DateTime($datetime1);
    $end = new DateTime($datetime2);

    $interval = $start->diff($end);

    $difference = sprintf(
        "%d days %d hours %d minutes %d seconds",
        $interval->days,
        $interval->h,
        $interval->i,
        $interval->s
    );

    return $difference;
}
?>
