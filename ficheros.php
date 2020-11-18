<?php

//Mostrar errores de ejecución PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Función para subir imagenes asociadas a la pregunta y su respuestas en una carpeta en servidor
function subir_fichero ($nombre_fichero, $nombre_tmp, $nombre_size, $nombre_type, $target_path){
  if (!file_exists($target_path)) {
    $oldumask = umask(0);
    mkdir('data/'.$target_path, 0777, true);
    umask($oldumask);
  }

  //Parámetros optimización, resolución máxima permitida
  $max_ancho = 1024;
  $max_alto = 200;

  $medidasimagen= getimagesize($nombre_tmp);

  //Path de la imagen ya redimensionada
  $target_path = $target_path."/".basename($nombre_fichero);

  //Si las imagenes tienen una resolución y un peso aceptable, se suben tal cual
  if($medidasimagen[1] < $max_alto && $nombre_size < 100000){
    if(move_uploaded_file($nombre_tmp, 'data/'.$target_path)) {
      chmod('data/'.$target_path, 0777);
      echo "El archivo ".  basename($nombre_fichero).
      " ha sido subido al servidor, como estaba originalmente";
      echo "<br/>";
    } else{
      echo "<br/>";
    }
  }
  //Sino se redimensiona de acuerdo al tipo de imagen
  else {
    if($nombre_type=='image/jpeg'){
      $original = imagecreatefromjpeg($nombre_tmp);
    }
    else if($nombre_type=='image/png'){
      $original = imagecreatefrompng($nombre_tmp);
    }
    else if($nombre_type=='image/gif'){
      $original = imagecreatefromgif($nombre_tmp);
    }

    $x_ratio = $max_ancho / $medidasimagen[0];
    $y_ratio = $max_alto / $medidasimagen[1];

    if( ($medidasimagen[0] <= $max_ancho) && ($medidasimagen[1] <= $max_alto) ){
      $ancho_final = $medidasimagen[0];
      $alto_final = $medidasimagen[1];
    }elseif (($x_ratio * $medidasimagen[1]) < $max_alto){
      $alto_final = ceil($x_ratio * $medidasimagen[1]);
      $ancho_final = $max_ancho;
    }else{
      $ancho_final = ceil($y_ratio * $medidasimagen[0]);
      $alto_final = $max_alto;
    }

    $lienzo=imagecreatetruecolor($ancho_final,$alto_final);

    imagecopyresampled($lienzo,$original,0,0,0,0,$ancho_final, $alto_final,$medidasimagen[0],$medidasimagen[1]);

    if($nombre_type=='image/jpeg'){
      imagejpeg($lienzo,'data/'.$target_path);
      chmod('data/'.$target_path, 0777);
      echo "El archivo ".  basename($nombre_fichero)." ha sido subido al servidor desde lienzo";
      echo "<br/>";
    }
    else if($nombre_type=='image/png'){
      imagepng($lienzo,'data/'.$target_path);
      chmod('data/'.$target_path, 0777);
      echo "El archivo ".  basename($nombre_fichero)." ha sido subido al servidor desde lienzo";
      echo "<br/>";
    }
    else if($nombre_type=='image/gif'){
      imagegif($lienzo,'data/'.$target_path);
      chmod('data/'.$target_path, 0777);
      echo "El archivo ".  basename($nombre_fichero)." ha sido subido al servidor desde lienzo";
      echo "<br/>";
    }
  }
}
?>
