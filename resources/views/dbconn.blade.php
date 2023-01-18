<title>laravel & mysql db connection</title>

<div>
<?php
    if(DB::connection()->getPdo()){
        echo "Succesfully Connected to db and db name is ". DB::connection()->getDatabaseName();
    }
?>
</div>
