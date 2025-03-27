<?php
include('includes/db.php');
include('header.php');

$month = isset($_GET['month']) ? $_GET['month'] : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Calcular el primer día del mes y la cantidad de días en el mes
$first_day_of_month = mktime(0, 0, 0, $month, 1, $year);
$number_of_days = date('t', $first_day_of_month);
$day_of_week = date('w', $first_day_of_month);

// Crear el array con los días del mes
$calendar_days = [];
for ($i = 1; $i <= $number_of_days; $i++) {
    $calendar_days[] = $i;
}

// Calcular la cantidad de celdas vacías antes del primer día del mes
$empty_cells = ($day_of_week == 0) ? 6 : $day_of_week - 1;

?>
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .calendar-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
    }
    .calendar-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .calendar-header h2 {
        margin: 0;
    }
    .calendar-nav {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .calendar-nav a {
        text-decoration: none;
        font-weight: bold;
        color: #007bff;
    }
    .calendar-table {
        width: 100%;
        border-collapse: collapse;
    }
    .calendar-table th,
    .calendar-table td {
        padding: 10px;
        text-align: center;
        border: 1px solid #ccc;
    }
    .calendar-table th {
        background-color: #f4f4f4;
    }
    .calendar-table td {
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .calendar-table td:hover {
        background-color: #f0f0f0;
    }
    .calendar-table td.selected {
        background-color: #007bff;
        color: white;
    }
</style>
<div class="calendar-container">
    <div class="calendar-header">
        <h2>Calendario - <?php echo date('F Y', $first_day_of_month); ?></h2>
    </div>
    
    <div class="calendar-nav">
        <a href="?month=<?php echo $month - 1 == 0 ? 12 : $month - 1; ?>&year=<?php echo $month - 1 == 0 ? $year - 1 : $year; ?>">&lt; Anterior</a>
        <a href="?month=<?php echo $month + 1 > 12 ? 1 : $month + 1; ?>&year=<?php echo $month + 1 > 12 ? $year + 1 : $year; ?>">Siguiente &gt;</a>
    </div>
    
    <table class="calendar-table">
        <thead>
            <tr>
                <th>Lunes</th>
                <th>Martes</th>
                <th>Miércoles</th>
                <th>Jueves</th>
                <th>Viernes</th>
                <th>Sábado</th>
                <th>Domingo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php
                // Celdas vacías antes del primer día
                for ($i = 0; $i < $empty_cells; $i++) {
                    echo "<td></td>";
                }

                // Mostrar los días del mes
                foreach ($calendar_days as $day) {
                    echo "<td class='day' data-day='$day'>$day</td>";
                    if ((($i + 1) % 7) == 0) {
                        echo "</tr><tr>";
                    }
                    $i++;
                }
                ?>
            </tr>
        </tbody>
    </table>
</div>

<script>
    // JavaScript para manejar el clic en los días del calendario
    document.querySelectorAll('.day').forEach(function(day) {
        day.addEventListener('click', function() {
            var selectedDay = day.getAttribute('data-day');
            alert("Seleccionaste el día: " + selectedDay);
            day.classList.toggle('selected');
        });
    });
</script>
