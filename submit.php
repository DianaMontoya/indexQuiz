<?php

//Mostrar errores de ejecución PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//Importar código para subir archivos al servidor
require 'ficheros.php';
//Importar dirección del servidor
require 'dirs.php';

//Sacar los blancos del título del test
$tituloSinBlancos = str_replace(" ", "", $_POST['titulo']);
//Codifica el título reemplazando los caracteres especiales
$titulovalido = urlencode($tituloSinBlancos);

//Definir path para nombre de archivos/carpetas en servidor
$path = date("ymdHi").$tituloSinBlancos;
$pathValido = date("ymdHi").$titulovalido;

if(isset($_POST['submit'])){
  //Cantidad de preguntas del test
  $cantidad = $_POST['cantidadPreg'];

  //Verificar si existe ya la carpeta con el mismo nombre en el servidor
  if (!file_exists($path)) {
      $oldumask = umask(0);
      mkdir('data/'.$path, 0777, true);
      umask($oldumask);
  }

  //Crear archivos para exportar formatos .gift
  //Con ponderación negativa 0%
  $archivo = fopen('data/'.$path."/"."formatogift_pond0.txt", "w");
  chmod('data/'.$path."/"."formatogift_pond0.txt", 0777);

  //Con ponderación negativa 25%
  $archivo25 = fopen('data/'.$path."/"."formatogift_pond25.txt", "w");
  chmod('data/'.$path."/"."formatogift_pond25.txt", 0777);

  //Con ponderación negativa 33%
  $archivo33 = fopen('data/'.$path."/"."formatogift_pond33.txt", "w");
  chmod('data/'.$path."/"."formatogift_pond33.txt", 0777);

  //Con ponderación negativa 50%
  $archivo50 = fopen('data/'.$path."/"."formatogift_pond50.txt", "w");
  chmod('data/'.$path."/"."formatogift_pond50.txt", 0777);

  //Con ponderación negativa 100%
  $archivo100 = fopen('data/'.$path."/"."formatogift_pond100.txt", "w");
  chmod('data/'.$path."/"."formatogift_pond100.txt", 0777);

  //Subir información extra / instrucciones del cuestionario
  if (!empty($_FILES['informacion']['name'])) {
    subir_fichero($_FILES['informacion']['name'], $_FILES['informacion']['tmp_name'],$path);
  }

  //Considerar la cantidad de preguntas que tiene el test
  for ($j=1; $j <= $cantidad; $j++) {

      //Campos del formulario asociados a cada pregunta
      $preguntaImag = $_POST['preguntaImag'.$j];
      $opcionAgregadaImag = $_POST['opcionAgregadaImag'.$j];
      $opcionAgregadaImagValor = $_POST['opcionAgregadaImagValor'.$j];
      $retroalimAgregada = $_POST['retroalimAgregada'.$j];
      $listaOpciones = $_POST['listaOpciones'.$j];
      $feedbackArr = $_POST['feedback'.$j];

      //Verificar que exista el enunciado en este número de pregunta
      if(!empty($preguntaImag)){
              //Por cada pregunta:
              for($i = 0; $i < count($_POST['preguntaImag'.$j]); $i++){
                  if(!empty($preguntaImag[$i])){
                    $preguntaImag = $preguntaImag[$i];
                    $feedback = $feedbackArr[$i];
                    $numeroRelleno = str_pad($j, 3, "0", STR_PAD_LEFT);

                    //Escribir en cada archivo, el enunciado de la pregunta
                    fwrite($archivo, "::".$tituloSinBlancos."PREGUNTA".$numeroRelleno."::". PHP_EOL);
                    fwrite($archivo, "".$preguntaImag);

                    fwrite($archivo25, "::".$tituloSinBlancos."PREGUNTA".$numeroRelleno."::". PHP_EOL);
                    fwrite($archivo25, "".$preguntaImag);

                    fwrite($archivo33, "::".$tituloSinBlancos."PREGUNTA".$numeroRelleno."::". PHP_EOL);
                    fwrite($archivo33, "".$preguntaImag);

                    fwrite($archivo50, "::".$tituloSinBlancos."PREGUNTA".$numeroRelleno."::". PHP_EOL);
                    fwrite($archivo50, "".$preguntaImag);

                    fwrite($archivo100, "::".$tituloSinBlancos."PREGUNTA".$numeroRelleno."::". PHP_EOL);
                    fwrite($archivo100, "".$preguntaImag);

                    //Verificar si la pregunta tiene imagen asociada
                    if (!empty($_FILES['output_image'.$j]['name'])) {
                      //Sacar los blancos del nombre de la imagen
                      $nombreImagenSinBlancos = str_replace(" ", "", $_FILES['output_image'.$j]['name']);
                      //Codifica el nombre de la imagen reemplazando los caracteres especiales
                      $nombreImagenValido = urlencode($nombreImagenSinBlancos);

                      //Sube la imagen al servidor
                      subir_fichero($nombreImagenSinBlancos, $_FILES['output_image'.$j]['tmp_name'], $_FILES['output_image'.$j]['size'],$_FILES['output_image'.$j]['type'], $path);

                      //Escribe la URL de la imagen (que acabamos de subir al servidor) en cada archivo
                      fwrite($archivo, "<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenSinBlancos."'/>". PHP_EOL);
                      fwrite($archivo25, "<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenSinBlancos."'/>". PHP_EOL);
                      fwrite($archivo33, "<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenSinBlancos."'/>". PHP_EOL);
                      fwrite($archivo50, "<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenSinBlancos."'/>". PHP_EOL);
                      fwrite($archivo100, "<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenSinBlancos."'/>". PHP_EOL);
                    }

                    //Si hay opciones en la pregunta, las empieza a considerar. Sino, es pregunta informativa
                    if(!empty($_POST['opcionAgregadaImag'.$j])){
                      fwrite($archivo, "{");
                      fwrite($archivo25, "{");
                      fwrite($archivo33, "{");
                      fwrite($archivo50, "{");
                      fwrite($archivo100, "{");
                    }

                    $num = 0;
                    $opCorrecta = $listaOpciones[$num];

                    //Por cada opción:
                    foreach ($_FILES['output_imageOpcionAgregada'.$j]['name'] as $key => $value) {
                      //Si tiene imagen asociada
                      if (!empty($_FILES['output_imageOpcionAgregada'.$j]['name'][$num])){
                          //Sacar los blancos del nombre de la imagen
                          $nombreImagenOpcionSinBlancos = str_replace(" ", "", $_FILES['output_imageOpcionAgregada'.$j]['name'][$num]);
                          //Codifica el nombre de la imagen reemplazando los caracteres especiales
                          $nombreImagenOpcionValido = urlencode($nombreImagenOpcionSinBlancos);

                          //Sube la imagen al servidor
                          subir_fichero($nombreImagenOpcionSinBlancos, $_FILES['output_imageOpcionAgregada'.$j]['tmp_name'][$num],$_FILES['output_imageOpcionAgregada'.$j]['size'][$num],$_FILES['output_imageOpcionAgregada'.$j]['type'][$num],$path);

                          //Diferenciar opciones que se tildaron correctas de las falsas, para detallarlo en el archivo exportado
                          if ($opCorrecta == $opcionAgregadaImagValor[$num] ){
                            fwrite($archivo, "=".$opcionAgregadaImag[$num]."<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenOpcionValido."'/>" );
                            fwrite($archivo25, "=".$opcionAgregadaImag[$num]."<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenOpcionValido."'/>" );
                            fwrite($archivo33, "=".$opcionAgregadaImag[$num]."<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenOpcionValido."'/>" );
                            fwrite($archivo50, "=".$opcionAgregadaImag[$num]."<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenOpcionValido."'/>" );
                            fwrite($archivo100, "=".$opcionAgregadaImag[$num]."<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenOpcionValido."'/>" );
                          }else{
                            fwrite($archivo, "~".$opcionAgregadaImag[$num]."<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenOpcionValido."'/>");
                            fwrite($archivo25, "~%-25%".$opcionAgregadaImag[$num]."<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenOpcionValido."'/>");
                            fwrite($archivo33, "~%-33.33333%".$opcionAgregadaImag[$num]."<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.'data/'.$pathValido."/".$nombreImagenOpcionValido."'/>");
                            fwrite($archivo50, "~%-50%".$opcionAgregadaImag[$num]."<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.$pathValido.'data/'."/".$nombreImagenOpcionValido."'/>");
                            fwrite($archivo100, "~%-100%".$opcionAgregadaImag[$num]."<img style\='vertical-align: middle; margin: 10px;' src\='".SERVER_PATH.$pathValido.'data/'."/".$nombreImagenOpcionValido."'/>");
                          }
                          //Si hay retroalimentación de la pregunta, la agrega al archivo exportado
                          if (!empty($retroalimAgregada[$num])){
                            fwrite($archivo, "#".$retroalimAgregada[$num]);
                            fwrite($archivo25, "#".$retroalimAgregada[$num]);
                            fwrite($archivo33, "#".$retroalimAgregada[$num]);
                            fwrite($archivo50, "#".$retroalimAgregada[$num]);
                            fwrite($archivo100, "#".$retroalimAgregada[$num]);

                          }
                          fwrite($archivo, "".PHP_EOL);
                          fwrite($archivo25, "".PHP_EOL);
                          fwrite($archivo33, "".PHP_EOL);
                          fwrite($archivo50, "".PHP_EOL);
                          fwrite($archivo100, "".PHP_EOL);
                        }
                        //Si no tiene imagenes asociadas a cada opción:
                        else{
                          //Diferenciar opciones que se tildaron correctas de las falsas
                          if ($opCorrecta == $opcionAgregadaImagValor[$num]){
                            fwrite($archivo, "=".$opcionAgregadaImag[$num]);
                            fwrite($archivo25, "=".$opcionAgregadaImag[$num]);
                            fwrite($archivo33, "=".$opcionAgregadaImag[$num]);
                            fwrite($archivo50, "=".$opcionAgregadaImag[$num]);
                            fwrite($archivo100, "=".$opcionAgregadaImag[$num]);
                          }else{
                            fwrite($archivo, "~".$opcionAgregadaImag[$num]);
                            fwrite($archivo25, "~%-25%".$opcionAgregadaImag[$num]);
                            fwrite($archivo33, "~%-33.33333%".$opcionAgregadaImag[$num]);
                            fwrite($archivo50, "~%-50%".$opcionAgregadaImag[$num]);
                            fwrite($archivo100, "~%-100%".$opcionAgregadaImag[$num]);
                          }
                          //Si hay retroalimentación de la pregunta, la agrega al archivo exportado
                          if (!empty($retroalimAgregada[$num])){
                            fwrite($archivo, "#".$retroalimAgregada[$num]);
                            fwrite($archivo25, "#".$retroalimAgregada[$num]);
                            fwrite($archivo33, "#".$retroalimAgregada[$num]);
                            fwrite($archivo50, "#".$retroalimAgregada[$num]);
                            fwrite($archivo100, "#".$retroalimAgregada[$num]);
                          }
                          fwrite($archivo, "".PHP_EOL);
                          fwrite($archivo25, "".PHP_EOL);
                          fwrite($archivo33, "".PHP_EOL);
                          fwrite($archivo50, "".PHP_EOL);
                          fwrite($archivo100, "".PHP_EOL);
                        }
                        $num = $num+1;
                    }
                  }
                  if (!empty($feedback)){
                      fwrite($archivo, "####".$feedback.PHP_EOL);
                      fwrite($archivo25, "####".$feedback.PHP_EOL);
                      fwrite($archivo33, "####".$feedback.PHP_EOL);
                      fwrite($archivo50, "####".$feedback.PHP_EOL);
                      fwrite($archivo100, "####".$feedback.PHP_EOL);

                  }
                  if(!empty($_POST['opcionAgregadaImag'.$j])){
                      fwrite($archivo, "}". PHP_EOL);
                      fwrite($archivo25, "}". PHP_EOL);
                      fwrite($archivo33, "}". PHP_EOL);
                      fwrite($archivo50, "}". PHP_EOL);
                      fwrite($archivo100, "}". PHP_EOL);
                  }
              }
      fwrite($archivo, PHP_EOL);
      fwrite($archivo25, PHP_EOL);
      fwrite($archivo33, PHP_EOL);
      fwrite($archivo50, PHP_EOL);
      fwrite($archivo100, PHP_EOL);

      }

  }

  fclose($archivo);
  fclose($archivo25);
  fclose($archivo33);
  fclose($archivo50);
  fclose($archivo100);

}
?>
