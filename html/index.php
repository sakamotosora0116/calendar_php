<?php
date_default_timezone_set('Asia/Tokyo');

// 前月・次月リンクが押された場合は、GETパラメーターから年月を取得
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    $ym = date('Y-m');// 一番最初はここに入る
    // $ym is 現在のカレンダーのタイトル（ほら、あの、前の月とか翌月のボタン押してその月にとぶやんか、あれのこと）year&month
}

// Create timestamp →変な値が入ったらfalseを返す
$timestamp = strtotime($ym . '-01');

// $ymを date('Y-m-01')としてはいけない。今月のひと月前の情報が欲しい時に、リアルの現在の月の情報をわたしてしまうので
// $prev_month or $next_monthの月の情報（その月の日数、など）を取得する必要があるため。

// 存在しない月や文字列が入ってないかフォーマットをチェックする→正直ここの重要性は今はまだよくわからん。
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

$today = date('Y-m-j');

// Create calendar title ex）2021/6  ちなみにこれは現在のカレンダーのタイトルのことね。現実の月とは限らんよ。現実の月が1月で,じぶんが色々見てる月は2月ってこともある
$now_month = date('Y/n', $timestamp);

// Get previous month & next month
// method1: use mktime().. mktime(hour,minute,second,month,day,year)
$prev_month = date('Y-m', mktime(0, 0, 0, date('m', $timestamp)-1, 1, date('Y', $timestamp)));
$next_month = date('Y-m', mktime(0, 0, 0, date('m', $timestamp)+1, 1, date('Y', $timestamp)));


// method2：strtotimeを使う
// $prev_month = date('Y-m', strtotime('-1 month', $timestamp));
// $next_month = date('Y-m', strtotime('+1 month', $timestamp));

// Get the number of  days in the current month
$day_count = date('t', $timestamp);

// ＜疑問＞↑これって第二引数に$timestampいる？？ date('t')はだめ？
// date('t', strtotime(date('Y-m'));でやるとすべての月が31日終わりになりおかしくなる


// ある月の１日が何曜日か 0:Sun 1:Mon ... 6:土
// method1:mktimeを使う
$youbi = date('w', mktime(0, 0, 0, date('m', $timestamp), 1, date('Y', $timestamp)));

// method2: use $timestamp
// $youbi = date('w', $timestamp);

// htmlタグの中にセルを毎度毎度いれていくのではなく、put all the cell int the array & output all together
$weeks = [];
// $weeks = array(); :another way to write
$week = '';

// Add empty cell(There is no numbers)
// 例）１日が火曜日だった場合、日・月曜日の２つ分の空セルを追加する
$week .= str_repeat('<td></td>', $youbi);

for ($day = 1; $day <= $day_count; $day++, $youbi++) {
    //＜疑問＞ 第三引数に$youbiがあるけれども、これはどういうこと？$youbiにインクリメントなんてできるのか？

    $date = $ym . '-' . $day;

    if ($today == $date) {
        $week .= '<td class="today">' . $day;
    } else {
        $week .= '<td>' . $day;
    }
    $week .= '</td>';

    /*＜Another way to write＞
    if ($today == $date) {
        $week .= '<td class="today">' . $day . '</td>';
    } else {
        $week .= '<td>' . $day . '</td>';
    }
    */

    // 週終わり、または、月終わりの場合
    if ($youbi % 7 == 6 || $day == $day_count) {

        if ($day == $day_count) {
            // monthのlast dayの場合、add empty cell
            // ex）最終日が水曜日の場合、木・金・土曜日の空セルを追加
            $week .= str_repeat('<td></td>', 6 - $youbi % 7);
            // もし仮に月曜日が週のはじめとするならばこのように書く（実際に紙に書いて実験してみるとよい）
            // $week .= str_repeat('<td></td>', 7 - $youbi % 7);
        }

        // 週の終わり、月の終わり（月の終わりの場合、中途半端でもその週は終わったことになる）にその一週間のtrと$weekをweeks配列に追加する
        $weeks[] = '<tr>' . $week . '</tr>';

        // 週の終わりにまた来週の分を新規で追加するために、リセット
        $week = '';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Calendar</title>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/calendar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h3 class="mb-5"><a href="?ym=<?php echo $prev_month; ?>">&lt;</a> <?php echo $now_month; ?> <a href="?ym=<?php echo $next_month; ?>">&gt;</a></h3>
        <table class="table table-bordered">
            <tr>
                <th>Sun</th>
                <th>Mon</th>
                <th>Tue</th>
                <th>Wed</th>
                <th>Thu</th>
                <th>Fly</th>
                <th>Sat</th>
            </tr>
            <?php
                foreach ($weeks as $week) {
                    echo $week;
                }
            ?>
        </table>
    </div>
</body>
</html>