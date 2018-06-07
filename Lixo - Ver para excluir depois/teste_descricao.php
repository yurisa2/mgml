<?php

require 'include/all_include.php';

  $meli = new Meli($app_Id, $secret_Key);

  $params = array('access_token' => token());

//     $bodyDescript = array(
//       'text' =>"A Caixa Térmica Easycooler 100L da EasyPath/Lazer possui corpo preenchido com a
//   mais alta qualidade e tecnologia de isolamento térmico o poliuretano e as paredes em Polietileno de
//   Alta Densidade, tampa altamente resistente.",
//       'plain_text' => "A Caixa Térmica Easycooler 100L da EasyPath/Lazer possui corpo preenchido com a mais alta qualidade e tecnologia de isolamento térmico o poliuretano e as paredes em Polietileno de Alta Densidade, tampa altamente resistente.<br>
// <br>Perfeitas para quem não dispensa a própria companhia a caixa térmica Easycooler 100L foi feita para quem é amante de pescaria esportiva,passeios ao ar livre,acampamento,passeios ao parque.Conserva suas bebidas como sucos,refrigerantes,cervejas dentre outros por muito mais tempo para você curtir seus momentos<br>
// <br>Experiência que impressiona<br><br>
// Possui alça estendida para transporte de maneira fácil e confortável, além das alças bilaterias curtas,roda de borracha maciça, fecho frontal em aluminio, mola limitadora de abertura em aço inox naval, dreno rosqueável.<br>
// <br>
// Fácil e rápida higienização a caixa térmica Easycooler 100L possui dobradiças embutidas no corpo e na tampa da caixa.<br>
// <br>
// Tecnologia e Inovação Térmica<br>
// <br>
// Com a exclusiva proteção UV contra os raios solares EasyUV Protection a conservação térmica aumenta ainda 2X mais.<br>
// <br>
// Características do Caixa Térmica Easycooler 100L<br>
// <br>
// -Litragem: 100 Litros<br>
// <br>
// -Capacidade: 163 latas ou 23 garrafas de 2 litros<br>
// <br>
// -Tempo de Conservação: 6 Dias com gelo<br>
// <br>
// -Proteção Térmica EasyUV Protection: Sim<br>
// <br>
// -Conservação de Produtos Quentes: Não<br>
// <br>
// -Cor: Azul no corpo e Branco na Tampa<br>
// <br>
// Composição<br>
// <br>
// Parede Externa: Polietileno de ALta Densidade ( PEAD)<br>
// <br>
// -Isolamento Térmico: Poliuretano (PU)<br>
// <br>
// -Parede Interna: Polietileno de Alta Densidade (PEAD)<br>
// <br>
// -Tampa: Poliuretano (PU)<br>
// <br>
// Especificações Técnicas<br>
// <br>
// -Tamanho Externo (Largura X Altura X Comprimento) : 430 X 440 X 895   <br>
// <br>
// -Tamanho Interno  (Largura X Altura X Comprimento): 405 X 340 X 655  <br>"
//
//     );

$responseDescript = $meli->get('/items/MLB964663771/description');

echo "<pre>";

var_dump($responseDescript);
