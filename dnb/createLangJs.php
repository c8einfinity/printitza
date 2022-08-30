<?php
$data = file_get_contents('php://input');
$data = json_decode($data, true);
$fh = fopen($data["file_name"], 'w') or die("Can't create file");
$content = "svgEditor.readLang(". $data["content"] . ");";
if(file_put_contents($data["file_name"], $content))
    echo 'Data successfully saved';
else 
    echo "error";
?>