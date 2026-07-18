<?php

use App\Models\BuilderModel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
foreach (BuilderModel::all() as $m) {
    echo 'ID: '.$m->id.' Name: '.$m->name."\n";
    echo 'Model URL: '.$m->model_url."\n";
    echo 'Layers Metadata: '.json_encode($m->layers_metadata)."\n\n";
}
