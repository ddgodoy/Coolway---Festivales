<?php

namespace CoolwayFestivales\BackendBundle\Util;

/**
 * @author pinika
 * @version 1.2 [2012]
 * 
 *	$options = array('metodo'  => 'full',
 *					 'zoom'    => 10,
 *					 'relleno' => '#000000',
 *					 'destino' => 'upload/',
 *                   'calidad_png' => 9,
 *			         'calidad_jpg' => 90);
 *
 *	$oR->setSimple('001.jpg', 'nueva.jpg', 'upload/', 250, 250, 0, 0, $options);
 *
 *	$thumbs = array('img1.jpg' => array('w'=>300, 'h'=>300),
 *					'img2.jpg' => array('w'=>200, 'h'=>200),
 *					'img3.jpg' => array('w'=>100, 'h'=>100));
 *
 *	$oR->setMultiple('001.jpg', $thumbs, 'upload/');
 */
class ResizeImage
{
	var $nombre_img_origen;// obligatorio
	var $nombre_img_nueva; // obligatorio
	var $ruta_origen;      // obligatorio
	var $extension_img;    // obligatorio
	var $ancho_nuevo;
	var $alto_nuevo;
	var $metodo; 		   // proportional | full | inflate
	var $relleno;
	var $zoom;			   // en porcentaje + | -
	var $ruta_destino;
	var $errores;
	//
	var $objeto_origen;
	var $objeto_destino;
	var $ruta_final;
	var $calidad_jpg;
	var $calidad_png;
	var $ancho_origen;
	var $alto_origen;
	//
	var $x_destination_point;
	var $y_destination_point;
	var $x_source_point;
	var $y_source_point;
	var $finalW;
	var $finalH;

	function clsRedimensionar()
	{
		$this->resetearValores();
	}

	function resetearValores()
	{
		$this->nombre_img_origen= '';
		$this->nombre_img_nueva = '';
		$this->ruta_origen      = '';
		$this->extension_img    = '';
		$this->ancho_nuevo = 960;
		$this->alto_nuevo  = 650;
		$this->metodo      = 'proportional';
		$this->relleno     = '';
		$this->zoom        = 0;
		$this->ruta_destino= '';
		$this->errores     = array();
		//
		$this->objeto_origen = '';
		$this->objeto_destino= '';
		$this->ruta_final    = '';
		$this->calidad_jpg   = 90;
		$this->calidad_png   = 9;
		$this->ancho_origen  = 0;
		$this->alto_origen   = 0;
		//
		$this->x_destination_point = 0;
		$this->y_destination_point = 0;
		$this->x_source_point = 0;
		$this->y_source_point = 0;
		$this->finalW = 0;
		$this->finalH = 0;
	}
//-------------------------------------------------------------------------------------
	function setNombreImgOrigen($value){ $this->nombre_img_origen = $value; }
	function setNombreImgNueva($value) { $this->nombre_img_nueva  = $value; }
	function setRutaOrigen($value)     { $this->ruta_origen  = $value; }
	function setExtensionImg($value)   { $this->extension_img= $value; }
	function setAnchoNuevo($value)     { $this->ancho_nuevo  = $value; }
	function setAltoNuevo($value)      { $this->alto_nuevo   = $value; }
	function setMetodo($value)         { $this->metodo       = $value; }
	function setRelleno($value)        { $this->relleno      = $value; }
	function setZoom($value)           { $this->zoom         = $value; }
	function setRutaDestino($value)    { $this->ruta_destino = $value; }
//-------------------------------------------------------------------------------------
	function Ejecutar()
	{
		$this->checkObligatorios();
		
		if (empty($this->errores))
		{			
			if (empty($this->ancho_origen) || empty($this->alto_origen)) {
				list($width, $height) = getimagesize($this->ruta_origen.$this->nombre_img_origen);
			} else {
				$width = $this->ancho_origen;
				$height = $this->alto_origen;
			}
			$source_width  = $width;
			$source_height = $height;

			$this->finalW = $this->ancho_nuevo;
			$this->finalH = $this->alto_nuevo;
			$wscale = $width / $this->ancho_nuevo;
			$hscale = $height / $this->alto_nuevo;
			
			if ($this->metodo == 'full') {
				if ($wscale > $hscale) {						
					$source_width = round( ( $width / $wscale * $hscale ) );
					$this->x_source_point = round( ( $width - ( $width / $wscale * $hscale ) ) / 2 );
				}
				elseif ($hscale > $wscale)
				{
					$source_height = round( ( $height / $hscale * $wscale ) );
					$this->y_source_point = round( ( $height - ( $height / $hscale * $wscale ) ) / 2 );
				}
				if ($this->zoom != 0 && $source_height > 0)
				{
					$rel_aspecto   = $source_width / $source_height;
					$source_width  = round( $source_width - ( $source_width * ($this->zoom / 100) ) );
					$source_height = round( $source_width / $rel_aspecto );
					$this->x_source_point = round( ( $width - $source_width ) / 2 );
					$this->y_source_point = round( ( $height - $source_height ) / 2 );
				}
			} elseif ($this->metodo == 'proportional') {
				$scale = 1;
				if ($hscale > 1 || $wscale > 1) {
					$scale = ($hscale > $wscale) ? $hscale : $wscale;
				}
				$this->ancho_nuevo = floor( $width / $scale );
				$this->alto_nuevo = floor( $height / $scale );
				$this->setCoordenadas();
			}
			else { // inflate
				if ($hscale > $wscale) {
					$this->ancho_nuevo = $width / $hscale;
					$this->alto_nuevo = $this->finalH;
				} else {
					$this->ancho_nuevo = $this->finalW;
					$this->alto_nuevo = $height / $wscale;
				}
				$this->setCoordenadas();
			}
			## redimension segun valores calculados
			$this->objeto_destino = imagecreatetruecolor($this->finalW, $this->finalH);

			if ($this->relleno != '') {
				$this->rellenarCanvas();
			}
			$this->crearImagenSegunTipo();

			imagecopyresampled ($this->objeto_destino,
                                $this->objeto_origen,
                                $this->x_destination_point,
                                $this->y_destination_point,
                                $this->x_source_point,
                                $this->y_source_point,
                                $this->ancho_nuevo,
                                $this->alto_nuevo,
                                $source_width,
                                $source_height);
			$this->salidaHaciaArchivo();
		}
	}
//-------------------------------------------------------------------------------------
	function crearImagenSegunTipo()
	{
		if ($this->extension_img == '.png') {
			$this->objeto_origen = imagecreatefrompng($this->ruta_origen.$this->nombre_img_origen);

			## preservar la transparencia en archivos PNG
			imagealphablending($this->objeto_destino, false);
			$colorTransparent = imagecolorallocatealpha($this->objeto_destino, 0, 0, 0, 127);

			imagefill($this->objeto_destino, 0, 0, $colorTransparent);
			imagesavealpha($this->objeto_destino, true);
			##
		} elseif ($this->extension_img == '.gif') {
			$this->objeto_origen = imagecreatefromgif($this->ruta_origen.$this->nombre_img_origen);
		} else {
			$this->objeto_origen = imagecreatefromjpeg($this->ruta_origen.$this->nombre_img_origen);
		}
	}
//
	function salidaHaciaArchivo()
	{
		## ruta final para la imagen redimensionada
		$this->ruta_final = $this->ruta_destino == '' ? $this->ruta_origen : $this->ruta_destino;
		##
		if ($this->extension_img == '.png') {
			imagepng($this->objeto_destino, $this->ruta_final.$this->nombre_img_nueva, $this->calidad_png);
		} else {
			imagejpeg($this->objeto_destino, $this->ruta_final.$this->nombre_img_nueva, $this->calidad_jpg);
		}
		imagedestroy($this->objeto_destino);

		@chmod($this->ruta_final.$this->nombre_img_nueva, 0777);
	}
//
	function rellenarCanvas()
	{
		$aBgColor = $this->htmlColorToRGB($this->relleno);
		$rscColor = imagecolorallocate($this->objeto_destino, $aBgColor['R'], $aBgColor['G'], $aBgColor['B']);

		imagefill($this->objeto_destino, 0, 0, $rscColor);	
	}
//
	function setCoordenadas()
	{
		if ($this->relleno != '') {
			$this->x_destination_point = ( $this->finalW / 2) - ($this->ancho_nuevo / 2 );
			$this->y_destination_point = ( $this->finalH / 2) - ($this->alto_nuevo / 2 );
		} else {
			$this->finalW = $this->ancho_nuevo;
			$this->finalH = $this->alto_nuevo;
		}
	}
//-------------------------------------------------------------------------------------
	function setSimple($nombre_img_origen,
                       $nombre_img_nueva,
                       $ruta_origen,
                       $ancho_nuevo = 0,
                       $alto_nuevo = 0,
                       $ancho_origen = 0,
                       $alto_origen = 0,
                       $extension_img = '',
                       $opciones = array())
	{		
		$this->resetearValores();

		$this->ancho_origen  = $ancho_origen;
		$this->alto_origen   = $alto_origen;
		$this->extension_img = $extension_img;

		$ancho_nuevo = !empty($ancho_nuevo) ? $ancho_nuevo : $this->ancho_nuevo;
		$alto_nuevo = !empty($alto_nuevo) ? $alto_nuevo : $this->alto_nuevo;

		$this->setNombreImgOrigen($nombre_img_origen);
		$this->setNombreImgNueva ($nombre_img_nueva);
		$this->setRutaOrigen($ruta_origen);
		$this->setAnchoNuevo($ancho_nuevo);
		$this->setAltoNuevo ($alto_nuevo);

		## mas opciones
		if (isset($opciones['metodo']))  { $this->setMetodo ($opciones['metodo']); }
		if (isset($opciones['zoom']))    { $this->setZoom   ($opciones['zoom']); }
		if (isset($opciones['relleno'])) { $this->setRelleno($opciones['relleno']); }
		if (isset($opciones['destino'])) { $this->setRutaDestino($opciones['destino']); }
		if (isset($opciones['calidad_jpg'])) { $this->calidad_jpg = $opciones['calidad_jpg']; }
		if (isset($opciones['calidad_png'])) { $this->calidad_png = $opciones['calidad_png']; }
		##
		$this->Ejecutar();
	}
//-------------------------------------------------------------------------------------
	function setMultiple($nombre_img_origen,
                         $array_imgs_nuevas,
                         $ruta_origen,
                         $ancho_origen = 0,
                         $alto_origen = 0,
                         $extension_img = '',
                         $opciones = array())
	{
		if (empty($ancho_origen) || empty($alto_origen)) {
			list($width, $height) = getimagesize($ruta_origen.$nombre_img_origen);
		} else {
			$width = $ancho_origen; $height = $alto_origen;
		}
		foreach ($array_imgs_nuevas as $nombre_img_nueva => $valores)
        {
			$this->setSimple(
                $nombre_img_origen,
                $nombre_img_nueva,
                $ruta_origen,
                $valores['w'],
                $valores['h'],
                $width,
                $height,
                $extension_img,
                $opciones
            );
		}
	}
//-------------------------------------------------------------------------------------
	function htmlColorToRGB($htmlColor)
	{
		$htmlColor = substr($htmlColor, 1);
		$aHexColor = array('R' => hexdec( $htmlColor[0].$htmlColor[1] ),
											 'G' => hexdec( $htmlColor[2].$htmlColor[3] ),
											 'B' => hexdec( $htmlColor[4].$htmlColor[5] ));
		return $aHexColor;
	}
//
	function rgbColorToHTML($rgbColor)
	{
		$r = dechex($rgbColor[0]);
		$g = dechex($rgbColor[1]);
		$b = dechex($rgbColor[2]);

    $color = ( strlen($r) < 2 ? '0' : '' ).$r;
    $color.= ( strlen($g) < 2 ? '0' : '' ).$g;
    $color.= ( strlen($b) < 2 ? '0' : '' ).$b;

    return '#'.strtoupper($color);
	}
//
	function clearErrores()
	{
		$this->errores = array();
	}
	function getErrores()
	{
		$error = ""; foreach($this->errores as $descripcion) {$error .= $descripcion."<br />";} return $error;
	}
//
	function checkObligatorios()
	{
		if (empty($this->extension_img)) {
			$this->setExtensionImg(strtolower(strrchr($this->nombre_img_origen, '.')));
		}
		if ($this->nombre_img_origen == '' || $this->nombre_img_nueva == '' || $this->ruta_origen == '' || $this->extension_img == '') {
			$this->errores['obligatorio'] = 'Datos insuficientes';
		}
	}
//
	function setCalidadImagenSegunTipo($valor, $tipo='jpg')
	{
		if ($tipo == 'png') {
			$this->calidad_png = $valor; // entre 0 y 9 - por defecto 9
		} elseif ($tipo == 'jpg') {
			$this->calidad_jpg = $valor; // entre 10 y 300 - por defecto 90
		}
	}

} // end class