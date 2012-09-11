<?php 
session_start();
echo $_SESSION["edit"];

function generatePassword(){
	
	CRYPT_SHA512 == 1;

	$var = date("N");
	$pass = "ih876unczzz92jwqpfnc18u3nd9c21";
	if($var == 1){
		$pass.= "ihdsg219";
	} else if ($var ==2){
		$pass.= "ccxqdpoda";
	}else if ($var ==3){
		$pass.= "23pisag21";
	}else if ($var ==4){
		$pass.= "09cbn7w11";
	}else if ($var ==5){
		$pass.= "szzz84fdvwq3";
	}else if ($var ==6){
		$pass.= "d88dhu31s";
	}else if ($var ==7){
		$pass.= "99ushc723";
	}
	return $pass;
}

function allowEdit(){
	$_SESSION["edit"] =  crypt(generatePassword(), date("c"));
}

function testEdit(){
	$t = $_SESSION["edit"];
	$p = generatePassword();
	if ($t === $p) return true;
	else return false;
}
?>
