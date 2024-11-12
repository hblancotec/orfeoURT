<?php

/**
 * Description of EmailMessage
 *
 * @author Hladino
 * http://www.electrictoolbox.com/php-imap-message-parts/
 */
class EmailMessage {
	protected $cxc;
	protected $cxf;
	protected $unc;
	protected $mid;
	
	/**
	 * Almacena el contenido del archivo.
	 * file_get_contents
	 */
	protected $cfc;
	public $bodyHTML = '';
	public $bodyPlain = '';
	public $attachments;
	public $mailRemitente;
	public $mailDestinos;
	public $mailFechaEnv;
	public $mailAsunto;
	public function __construct($conc, $ruta) {
		$this->cxc = $conc;
		$this->mid = basename ( $ruta, ".eml" );
		$this->unc = $ruta;
	}
	public function fetch() {
		if (! file_exists ( $this->unc ) || filesize ( $this->unc ) == 0) {
			if (! imap_savebody ( $this->cxc, $this->unc, $this->mid, '', FT_UID )) {
				throw new Exception ( 'No se pudo guardar correo ' . $this->mid );
			}
		}
		$this->cfc = file_get_contents( $this->unc, false, null, 0, filesize( $this->unc ) );
		if ( strlen( $this->cfc ) == 0)
			return false;
		
		$this->cxf = mailparse_msg_parse_file ( $this->unc );
		$st = mailparse_msg_get_structure( $this->cxf );
		if (! $st) {
			return false;
		} else {
			$this->recurse( $st );
		}
		mailparse_msg_free( $this->cxf);
		return true;
	}
	public function recurse($messageParts, $prefix = '', $index = 1, $fullPrefix = true) { 
	    // 0=text/TYPETEXT 1=multipart/TYPEMULTIPART 2=message/TYPEMESSAGE 3=application/TYPEAPPLICATION       <=type
        //4=audio/TYPEAUDIO 5=image/TYPEIMAGE 6=video/TYPEVIDEO 7=model/TYPEMODEL 8=other/TYPEOTHER            <=type
		foreach ( $messageParts as $parte ) {
			
			$part = mailparse_msg_get_part ( $this->cxf, $parte );
			$part_data = mailparse_msg_get_part_data ( $part );
			if ($parte == 1) {
				$this->mailRemitente = $part_data ['headers'] ['from'];
				$this->mailDestinos = $part_data ['headers'] ['to'];
				$this->mailFechaEnv = $part_data ['headers'] ['date'];
				$this->mailAsunto = $part_data ['headers'] ['subject'];
			}
			$type = $part_data ['content-type'];
			$start = $part_data ['starting-pos-body'];
			$end = $part_data ['ending-pos-body'];
			if (substr ( $type, 0, 4 ) === 'text') { // type 0
				if (floor ( $parte ) == 1) {
					if ($type == 'text/html') {
					    $this->bodyHTML = $this->decodificar( substr( $this->cfc, $start, $end - $start ), $part_data ['transfer-encoding'] );
					}
					if ($type == 'text/plain') {
					    $this->bodyPlain = $this->decodificar( substr( $this->cfc, $start, $end - $start ), $part_data ['transfer-encoding'] );
					}
				} else {
					$this->attachments [] = array (
                        'type' => $type,
                        'subtype' => $part_data ['content-type'],
                        'filename' => $part_data ['disposition-filename'],
                        'data' => $this->encodarAiso88591 ( substr ( $this->cfc, $start, $end - $start ), $part_data ['charset'] ),
                        'id' => $parte,
                        'id_content' => $part_data ['content-id'],
                        'inline' => $part_data ['content-disposition'] 
					);
				}
			} elseif (substr ( $type, 0, 7 ) == 'message') { // type 2
                $this->attachments [] = array (
                    'type' => $type,
			        'subtype' => $part_data ['content-type'],
			        'filename' => $part_data ['disposition-filename'],
			        'data' => $this->encodarAiso88591 ( substr ( $this->cfc, $start, $end - $start ), $part_data ['charset'] ),
			        'id' => $parte,
			        'id_content' => $part_data ['content-id'],
			        'inline' => $part_data ['content-disposition']
                );
			} elseif (substr ( $type, 0, 9 ) == 'multipart') { // type 1
				    continue;
				if ($type == 'multipart/mixed') {
				}
				if ($type == 'multipart/related') {
				}
			} else {
				$this->attachments [] = array (
						'type' => $type,
						'subtype' => $part_data ['content-type'],
						'filename' => $part_data ['disposition-filename'],
						'data' => $this->encodarAiso88591 ( substr ( $this->cfc, $start, $end - $start ), $part_data ['charset'] ),
						'id' => $parte,
						'id_content' => $part_data ['content-id'],
						'inline' => $part_data ['content-disposition'] 
				);
			}
		}
	}
	
	/**
	 * Encoda $cadena a charset ISO-8859-1 desde el $varset enviado.
	 * 
	 * @param string $cadena        	
	 * @param string $charset        	
	 * @return string
	 */
	function encodarAiso88591($cadena, $charset) {
		switch ($charset) {
			case 'iso-8859-1' :
				{
					$ctndF = $ctndF;
				}
				break;
			default :
				{
					$ctndF = iconv ( $charset, 'iso-8859-1', $cadena );
				}
		}
		return $ctndF;
	}
	
	/**
	 *
	 * @param string $data        	
	 * @param string $encoding        	
	 * @return string|string
	 */
	function decodificar($data, $encoding) {
		// 0=7bit/ENC7BIT 1=8bit/ENC8BIT 2=Binary/ENCBINARY 3=Base64/ENCBASE64
		// 4=Quoted-Printable/ENCQUOTEDPRINTABLE 5=other/ENCOTHER
		switch ($encoding) {
			case 'base64' :
				{
					$ctndF = imap_base64 ( $data );
				}
				break;
			case 'quoted-printable' :
				{
					$ctndF = quoted_printable_decode ( $data );
				}
				break;
			default :
				$ctndF = $data;
		}
		return $ctndF;
	}
}
?>