<script>
    function exportCSV(object){
        toCSV(object);
        hiddenElement = document.createElement('a');
        hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);
        hiddenElement.target = '_blank';
        hiddenElement.download = 'lang_file.csv';
        hiddenElement.click();
    }
    function toCSV(obj) {
        let tempArray = [];
        for (key in obj) {
            if(typeof obj[key] == 'object' ) {
                toCSV(obj[key]);
            }
            else {
                if(!tempArray.includes(obj[key])){
                    let str = '"' + obj[key] + '"' + "\n";
                    tempArray.push(obj[key]);
                    csv += str;
                }
            }
        }
    }

    function replaceJSON(original_json, translated_json) {
        for (key in original_json) {
            if(typeof original_json[key] == 'object' ) {
                replaceJSON(original_json[key], translated_json);
            }
            else {
                original_json[key] = translated_json[original_json[key]];
            }
        }
    }

    function importCSV(translated_object, original_json){
        let translated_json = {};
        translated_object.forEach(val => {
            translated_json[val[0]] = val[1];
        });
        replaceJSON(original_json,translated_json);
        return JSON.stringify(original_json);
    }
</script>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_REQUEST["action"])){
    if($_REQUEST["action"] == "export" ){
    $fileContent = file_get_contents("locale/lang_sample.json");
?>
    <script>
        let csv = "English,Your Language" + "\n";
        exportCSV(<?php echo $fileContent; ?>);
    </script>
<?php
    }else if($_REQUEST["action"] == "import" ){
        $file_to_write = "locale/lang.ch_CH.js";
        $file_to_read = "locale/lang_file.csv";
        $lang = 'ch';
        if(isset($_REQUEST['lang'])){
            $file_to_write = "locale/lang.".$_REQUEST['lang'].".js";
            $file_to_read = "locale/lang_".$_REQUEST['lang'].".csv";
            $lang = explode('_',$_REQUEST['lang'])[0];
        }
        $fileContent = file_get_contents($file_to_read);
        $fileContent = utf8_encode($fileContent);
        $json = array_map("str_getcsv", explode("\n", $fileContent));
        $sampleContent = file_get_contents("locale/lang_sample.json");
?>
    <script>
        let csv = "English,Your Language" + "\n";
        let lang_json = importCSV(<?php echo json_encode($json); ?>,<?php echo $sampleContent; ?>);
        let lang = '<?php echo $lang; ?>';
        lang_json = lang_json[0] + `"lang":"${lang}","dir":"ltr",` + lang_json.substring(1);
        var data = {content: lang_json, file_name: '<?php echo $file_to_write; ?>' }
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                alert("File saved successfully.");
            }
        };
        xhttp.open("POST", "createLangJs.php", true);
        xhttp.send(JSON.stringify(data));
    </script>
<?php
    }
}else{
    echo "please add action.";
}
?>