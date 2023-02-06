<?php
/**
 * StupidEncrypt
 * Usage: StupidEncrypt.php?input=stringtoencode&pass=password&live=1
 * Usage: StupidEncrypt.php?output=stringtodecode&pass=password&live=1
 * 
 * cmd:
 * php -f {path_to}\StupidEncrypt.php input={string to encrypt} pass={pass} live=1
 * php -f {path_to}\StupidEncrypt.php output={string to decrypt} pass={pass} live=1
 *
 */
$res = '';		
parse_str(implode('&', array_slice($argv, 1)), $_GET);

if (!isset($_GET['pass']) ) {
	$res = 'passwort missing';
	
} else {
	if (trim($_GET['pass']) == '') {
		$res = 'passwort empty';
	
	} else {
		$respass = bin2hex(base64_encode($_GET['pass']));
		$respasschunks = intval(strlen($respass)/4);
		$arrrespasschunks = array();
		for ($irp = 0; $irp <= $respasschunks+1; $irp++) {
			if ($irp == $respasschunks+1) {
				$arrrespasschunks[$irp] = substr($respass, $irp*4, 40);		
			} else  {
				$arrrespasschunks[$irp] = substr($respass, $irp*4, 4);
			}
			
		}
		
		$strrespasstest = implode('', $arrrespasschunks);
		if (isset($_GET['input']) ) {
			$result = bin2hex(base64_encode($_GET['input']));
			$res = 'passwort not complex enough. Please improve complexity and try again.';		
			$lenresult = strlen($result);
			$resultchunks = intval(strlen($result)/4);			
			$arrresultchunks = array();
			for ($irp = 0; $irp < $resultchunks+1; $irp++) {
				if ($irp ==$resultchunks) {
					if (trim(substr($result, $irp*4, 40)) != '') {
						$arrresultchunks[$irp] = substr($result, $irp*4, 40);
					}
			
				} else  {
					$arrresultchunks[$irp] = substr($result, $irp*4, 4);
				}
				
			}
			
			$strrestest = implode('.', $arrresultchunks);
			$arrmergechunks = array();
			$loopcnt = 0;
			$respasschunkswrk=$respasschunks;
			for ($irp = 0; $irp < $resultchunks; $irp++) {
				if ($irp > $respasschunkswrk) {
					$loopcnt++;
					$respasschunkswrk=$respasschunkswrk+$respasschunks;
				} 
				
				$arrmergechunks[$irp] = dechex(hexdec('00' . $arrresultchunks[$irp])+hexdec('00' . $arrrespasschunks[$irp-$respasschunks*$loopcnt]));					
			}
			
			$strrespasstestw = implode('.', $arrmergechunks); 
			$strrespasstest = implode('', $arrmergechunks);
		
			$res = $strrespasstest;
		
		} elseif (isset($_GET['output']) ) {	
			$result = $_GET['output'];
			$res =trim($_GET['output']);	
			$lenresult = strlen($result);
			$resultchunks = intval(strlen($result)/4);		
			$arrresultchunks = array();
			for ($irp = 0; $irp < $resultchunks+1; $irp++) {
				if ($irp ==$resultchunks) {
					if (trim(substr($result, $irp*4, 40)) != '') {
						$arrresultchunks[$irp] = substr($result, $irp*4, 40);
					}
						
				} else  {
					$arrresultchunks[$irp] = substr($result, $irp*4, 4);
				}
			}
			
			$arrmergechunks = array();
			$loopcnt=0;
			$respasschunkswrk = $respasschunks;
			for ($irp = 0; $irp < $resultchunks; $irp++) {
				if ($irp > $respasschunkswrk) {
					$loopcnt++;
					$respasschunkswrk=$respasschunkswrk+$respasschunks;
				}
				 
				$arrmergechunks[$irp] = dechex(hexdec('00' . $arrresultchunks[$irp])-hexdec('00' . $arrrespasschunks[$irp-$respasschunks*$loopcnt]));
			}
		
			$strrespasstest = implode('', $arrmergechunks);
			$res = @base64_decode(@hex2bin($strrespasstest));
			if (trim($res) == '') {
				$res = 'wrong password';
			}
			
		}
	}
}

echo 'result: ';
echo $res;
?>
