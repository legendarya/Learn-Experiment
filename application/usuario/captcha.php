<?php
class UrlController extends ProcessTemplate{
	
	function execute(){
// Configuración:
$N = 2;		// Nivel de emborronado { 2, 3, 4, ... }
$J = 80;	// Calidad JPEG { 0, 1, 2, 3, ..., 100 }
$M = 5;		// Margen.
$L = 8;		// Número de letras.
$C = FALSE;	// Case sensitive.

// Indicamos que vamos a generar una imagen ¡no una página HTML!
header("Content-type: image/jpeg");

// Inicializamos cualquier posible valor previo de captcha:
$_SESSION['captcha'] = '';
// Metemos tantos caraceteres aleatorios como sean precisos:
for( $n = 0; $n < $L; $n++ )
	$_SESSION['captcha'] .= $this->C();

// Si no es case sensitive lo ponemos todo en minúsculas:
if( ! $C )
	$_SESSION['captcha'] = strtolower( $_SESSION['captcha'] );

// Dimensiones del captcha:
$w = 2 * $M + $L * imagefontwidth ( 5 );
$h = 2 * $M +      imagefontheight( 5 );

// Creamos una  imagen:
$i = imagecreatefrompng('./img/fondo_captcha.png');

// Elegimos aleatoriamente un ángulo de emborronado:
$A = ( rand() % 180 ) / 3.14;

$angulo=rand(0,6)-3;

// Realizamos iteraciones de emborronado:
for( $n = 0; $n < $N; $n++ ) {

	// Factor de interpolación, va de 1.0 a 0.0
	$t = 1.0 - $n / ( $N - 1.0 );

	// El radio se va centrando a medida que se hace nítido:
	$r = $M * $t;

	// El color va siendo cada vez más claro:
	$c = 255-(255 * $t);
	$c = imagecolorallocate( $i, $c, $c, $c );

	// Dibujamos el texto en el sentido del ángulo y radio de desplazamiento:
	imagefttext ( $i, 20,$angulo, $M + $r * cos( $A )+15, $M + $r * sin( $A )+25,$c,'./res/captcha.ttf', $_SESSION['captcha']);

	// Pasamos otro filtro gaussiano:
	imagefilter( $i, IMG_FILTER_GAUSSIAN_BLUR );
}

// Escribimos la imagen como un JPEG en el buffer de salida:
imagejpeg( $i, NULL, $J );

// Liberamos la imagen:
imagedestroy( $i );
	}
	
// Devuelve un caracter aleatorio:
function C() {
	$W = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
	return substr( $W, rand() % strlen( $W ), 1 );
}
}
?>