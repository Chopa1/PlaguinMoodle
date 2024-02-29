<?php

class EquationSolver {
    public static function solveQuadraticEquation(float $a, float $b, float $c, $errors): array {
        // Бизнес-логика для решения квадратного уравнения
        $discriminant = $b * $b - 4 * $a * $c;
        if ($discriminant < 0) {
            throw new Exception('Уравнение не имеет действительных корней');
        }

        $x1 = (-$b + sqrt($discriminant)) / (2 * $a);
        $x2 = (-$b - sqrt($discriminant)) / (2 * $a);

        return ['x1' => $x1, 'x2' => $x2];
    }
}
?>
