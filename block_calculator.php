<?php

// Строгая типизация
declare(strict_types=1);

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Handles displaying the calculator block.
 *
 * @package    block_calculator
 * @copyright  2024 Danil
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_calculator extends block_base {

    /**
     * Initialise the block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_calculator');
    }

    /**
     * Return the content of this block.
     *
     * @return stdClass the content
     */
    public function get_content() {
        global $OUTPUT, $CFG, $DB;
    
        if ($this->content !== null) {
            return $this->content;
        }
    
        $this->content = new stdClass();
    
        // Переменные для ошибок
        $errors = '';
        $x1 = null;
        $x2 = null;

        if (!is_numeric($_POST['a']) || !is_numeric($_POST['b']) || !is_numeric($_POST['c'])) {
            $errors .= $OUTPUT->notification('Пожалуйста, введите числовые значения для a, b и c', 'error');
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['a']) && isset($_POST['b']) && isset($_POST['c'])) {
                // Получение данных из формы
                $a = (float)$_POST['a'];
                $b = (float)$_POST['b'];
                $c = (float)$_POST['c'];
                
                // Проверка ввода
                if ($a === 0) {
                    $errors .= $OUTPUT->notification('a не может быть равно 0', 'error');
                }
        
                // Расчет квадратного уравнения
                $discriminant = $b * $b - 4 * $a * $c;
                if ($discriminant < 0) {
                    $errors .= $OUTPUT->notification('Уравнение не имеет действительных корней', 'error');
                }
        
                $x1 = (-$b + sqrt($discriminant)) / (2 * $a);
                $x2 = (-$b - sqrt($discriminant)) / (2 * $a);
        
                // Сохранение в базу данных
                $data = [
                    'a' => $a,
                    'b' => $b,
                    'c' => $c,
                    'x1' => $x1,
                    'x2' => $x2
                ];

                $tableExists = $DB->get_record_sql("SHOW TABLES LIKE 'mdl_calculator_history'");

                if (!$tableExists) {
                    $sql = "
                        CREATE TABLE {calculator_history} (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            a FLOAT,
                            b FLOAT,
                            c FLOAT,
                            x1 FLOAT,
                            x2 FLOAT,
                            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        )
                    ";
                    $DB->execute($sql);
                }

                if (empty($errors)) {
                    try {
                        $DB->insert_record('calculator_history', (object)$data);
                    } catch (Exception $e) {
                        $errors .= $OUTPUT->notification('Ошибка при сохранении в базу данных: ' . $e->getMessage(), 'error');
                    }
                }
            }
        }
    
        // Вывод формы
        $form = '<form method="post">
                    <div class = "enter">
                    <label for="a">a = </label>
                    <input placeholder = "введите значение..." type="text" name="a" required>
                    </div>
                    <div class = "enter">
                    <label for="b">b = </label>
                    <input placeholder = "введите значение..." type="text" name="b" required>
                    </div>
                    <div class = "enter">
                    <label for="c">c = </label>
                    <input placeholder = "введите значение..." type="text" name="c" required>
                    </div>
                    <input class = "buttonInput" type="submit" value="Найти решение">
                </form>';

         // Вывод кнопки "История"
         $historyButton = '<div><form method="get" action="' . $CFG->wwwroot . '/blocks/calculator/view.php">
            <input class = "buttonHistory" type="submit" value="История">
        </form></div>';
        
        $this->content->text = $form . $historyButton . $errors;
    
        // Вывод значений x1 и x2
        if ((is_numeric($x1) && (!is_nan($x1))) && (is_numeric($x2)  && (!is_nan($x1)))) {
            $this->content->text .= "<p>x1 = $x1</p>";
            $this->content->text .= "<p>x2 = $x2</p>";
        }        
    
        return $this->content;
    }    
}
