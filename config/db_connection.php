<?php
try {
    $conn = new PDO("sqlsrv:Server=10.12.12.27;Database=NWL_Detection", "nwlproduction", "Nwl!2563789!");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed.<br>' . $e->getMessage();
}