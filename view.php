<?php

// Строгая типизация
declare(strict_types=1);

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/blocklib.php');

// Указываем контекст
$context = context_system::instance();

// Устанавливаем заголовок страницы
$PAGE->set_context($context);
$PAGE->set_url('/blocks/calculator/view.php');
$PAGE->set_heading('Calculator History');
$PAGE->set_title('Calculator History');
$PAGE->navbar->add('Calculator History');

// Проверка прав доступа
require_login();

// Подключение шапки страницы
echo $OUTPUT->header();

// Получение записей из базы данных
$calculator_history = $DB->get_records('calculator_history', null, 'timestamp DESC');

// Вывод истории
if ($calculator_history) {
    echo '<table border="1">';
    echo '<tr><th>a</th><th>b</th><th>c</th><th>x1</th><th>x2</th><th>Timestamp</th></tr>';
    foreach ($calculator_history as $record) {
        echo '<tr>';
        echo '<td>' . $record->a . '</td>';
        echo '<td>' . $record->b . '</td>';
        echo '<td>' . $record->c . '</td>';
        echo '<td>' . $record->x1 . '</td>';
        echo '<td>' . $record->x2 . '</td>';
        echo '<td>' . $record->timestamp . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo 'No calculator history found.';
}

// Подключение подвала страницы
echo $OUTPUT->footer();
