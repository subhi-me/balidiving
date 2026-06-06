<div class="col-12 col-md-3 mbr-fonts-style display-7">
    <p class="mbr-text">
        <strong>
<?php
$file = fopen("database/link-footer.txt", "r");

if ($file) {
    while (($line = fgets($file)) !== false) {
        list($title, $url) = explode(";", $line);
        echo '<a href="' . trim($url) . '" class="text-success">' . trim($title) . '</a><br>';
    }

    fclose($file);
} else {
    echo "Error opening the file.";
}
?>
        </strong><br><br>
    </p>
</div>
