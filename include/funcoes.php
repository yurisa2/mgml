<?php

function token()
{
  $variavel = json_decode(file_get_contents("include/files/tokens.json"));
  $access_token = $variavel->access_token;
  $refresh_token = $variavel->refresh_token;

  // echo "time() " . time() . "<br>";  //DEBUG
  // echo "variavel->time " . $variavel->time . "<br>"; //DEBUG

  if(time() > $variavel->time)
  {
    renova($access_token,$refresh_token);
    $variavel = json_decode(file_get_contents("include/files/tokens.json"));
    $access_token = $variavel->access_token;
    $refresh_token = $variavel->refresh_token;

    return $access_token;
  }
  else return $access_token;
}

function renova($access_token,$refresh_token)
{
  global $app_Id;
  global $secret_Key;

  $meli = new Meli($app_Id, $secret_Key, $access_token,$refresh_token);
  $refresh = $meli->refreshAccessToken();
  $token_info["access_token"] = $refresh["body"]->access_token;
  $token_info["refresh_token"] = $refresh["body"]->refresh_token;
  $token_info["time"] = time()+10000;
  file_put_contents("include/files/tokens.json",json_encode($token_info));
}

?>
