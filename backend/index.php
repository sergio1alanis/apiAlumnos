<?php
// Require composer autoloader
require __DIR__ . '/vendor/autoload.php';

//Linea incluida desde RedBeanPhP
require 'rb.php';

//Linea cpiada desde RedBeanPHP para conexion
R::setup( 'mysql:host=localhost;dbname=aplicacion;port=3307', 'root', '12345' );

// Create Router instance
$router = new \Bramus\Router\Router();

//Habilitar permisos para realizar solicitudes
$router->options('.*', function(){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");    
    header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Authorization");
    exit();
});

//Metodo para simplificar el codigo ya que se repiten las cabeceras en cada funcion
function jsonResponse($data){
    header('Access-Control-Allow-Origin: *');  // el * significa que todos los dominios
    header("Content-Type: application/json");  //Devuelve un contenido json
    echo json_encode($data); //RedBean es el  que exporta el contenido

}

// This route handling function will only be executed when visiting http(s)://www.example.org/about
$router->get('/', function() {
    $alumnos = R::find('alumnos'); //Buscar alumnos
    
    //Colocar cabeceras llamando a la function jsonResponse
    //Esta linea hace accesible el contenido a cualquier dominio
    jsonResponse(R::exportAll($alumnos)); //RedBean es el  que exporta el contenido
    
});

//Agregar Alumnos
$router->post('/',function() { 
    $data=json_decode(file_get_contents('php://input'),true); //Recibe la informacion enviada por post en formato JSON
    
    $alumno=R::dispense('alumnos');   //Crea una instancia de la tabla "Alumnos"
    $alumno->name=$data['nombres'];      
    $alumno->lastname=$data['apellidos']; 
    $id= R::store($alumno);              
    
    jsonResponse(['mensaje'=>'Alumno agregado','id'=>$id]);     
});

//Metodo de borrado de alumno, Utiliza redBean para selecionar con el id
$router->delete( '/{@id}', function($id) {
    //trash  es el metodo que borra los datos,  ejemplo: load solo los carga
    $alumno = R::trash('alumnos', $id);
    jsonResponse(['mensaje'=>'Alumno eliminado']); 
});

// Run it!
$router->run();
