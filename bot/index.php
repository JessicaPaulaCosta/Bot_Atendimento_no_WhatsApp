<?php
   require_once './classe/Conexao.php'; //busca o arquivo
  
   try{
       $u = new conexaoMySQL("bot","localhost", "root",""); //instanciando a classe conexaoMySQL 
       include ("./classe/menu.php"); //CHAMA O MENU 
   }catch(PDOException $e){
       echo "Erro ao conectar ao banco";
   }
   
   // DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
    date_default_timezone_set('America/Sao_Paulo');   
    $dia =  date('d/m/Y', time());
    $horas = date('H:i:s', time());
   
   $msg = $_GET['msg'];
   $telefone = $_GET['telefone'];
   

   /* **********************************************************
         VERIFICA SE O TELEFONE EXISTE NO BANCO
      ********************************************************** */
   if(!empty($u->consultaTelefone($telefone))){ //se o usuario já falou comigo alguma vez    

         if(!empty($u->consultaUltimaConversa($telefone, $dia))){ //se esta falando comigo agora
            $interacoes = $u->buscaStatus($telefone); 
            $escolhas = $u->buscaOpcao1($telefone); // consulta a 1o opção
            $consultaNome = $u->buscaNome($telefone);


            if($interacoes == "1"){ //cadastra o nome do user               
               $nome = ucfirst($msg); //Converte para maiúscula o primeiro caractere de uma string
               $u->atualizaNome($telefone, $nome, $interacoes);                        
               $u->cadastraConversa($telefone, $msg, $menu1, $dia, $horas);
               echo "É uma prazer falar com você $nome .\n $menu1";

            /* ----------------------------------------
                        PRIMEIRO MENU
             ------------------------------------------ */
            }elseif ($interacoes == "2") {
               $opcao = $msg;

               if($opcao <= '5' AND $opcao > '0'){
                  $u->atualizaOpcao1($telefone, $opcao);  // atualiza opção 1
                  
                  switch ($opcao) {
                     case '1':
                        $u->cadastraConversa($telefone, $msg, $missa, $dia, $horas);
                        echo $missa;
                        $u->atualizaStatus($telefone, "3"); // atualiza o status de interação com o bot           
                        break;
                     case '2':
                        $u->cadastraConversa($telefone, $msg, $cemimonial, $dia, $horas);
                        $u->atualizaStatus($telefone, "2"); // atualiza o status de interação com o bot  
                        echo $cemimonial;                                
                        break;
                     case '3':
                        $u->cadastraConversa($telefone, $msg, $inscricao, $dia, $horas);
                        $u->atualizaStatus($telefone, "2"); // atualiza o status de interação com o bot  
                        echo $inscricao;
                        break;
                     case '4':
                        echo $doacao;
                        $u->atualizaStatus($telefone, "3"); // atualiza o status de interação com o bot        
                        break;
                     case '5':
                        echo $secretaria;
                        $u->atualizaStatus($telefone, "3"); // atualiza o status de interação com o bot                    
                        break;            
                     default:                   
                        $bot = "$consultaNome você não digitou um número válido! Escolha uma das opção acima que estou anotando ... ";
                        $u->cadastraConversa($telefone, $msg, $bot, $dia, $horas);                                            
                        echo $bot;                 
                        break;                     
                  } //end switch                                                 

               }else{ 
                  $bot = "Não entendi $consultaNome, pode repedir qual das opções acima?";
                  $u->cadastraConversa($telefone, $msg, $bot, $dia, $horas);
                  echo $bot;
               } //end if interacoes


            /* --------------------------------------------
                     SEGUNDO MENU :: CERIMONIAL
             ----------------------------------------------- */ 
            }elseif ($interacoes == "3" AND $escolhas == "2"){
               $opcao2 = $msg;            

               if($opcao2 <= '4' AND $opcao2 > '0'){
                  $u->atualizaOpcao2($telefone, $opcao2); // atualiza opção 2

                  switch ($opcao2) {
                     case '1':
                        $u->cadastraConversa($telefone, $msg, $casamento, $dia, $horas);
                        echo $casamento;
                        $u->atualizaStatus($telefone, "3"); // atualiza o status de interação com o bot                 
                        break;
                     case '2':
                        $u->cadastraConversa($telefone, $msg, $batizado, $dia, $horas);
                        echo $batizado;
                        $u->atualizaStatus($telefone, "3"); // atualiza o status de interação com o bot                  
                        break;
                     case '3':
                        $u->cadastraConversa($telefone, $msg, $falecimento, $dia, $horas);
                        echo $falecimento;
                        $u->atualizaStatus($telefone, "3"); // atualiza o status de interação com o bot              
                        break;
                     case '4':
                        $u->cadastraConversa($telefone, $msg, $oracao, $dia, $horas);
                        echo $oracao;
                        $u->atualizaStatus($telefone, "3"); // atualiza o status de interação com o bot              
                        break;
                     default:
                        $bot = "Acho que não estou te ajudando muito! Pode repedir qual das opções acima?, por favor!";             
                        $u->cadastraConversa($telefone, $msg, $bot, $dia, $horas);
                        echo $bot;
                        break;
                  } //end switch 

               }else{
                  $bot = "Acho que não estou te ajudando muito! Escolha uma das opção acima que estou anotando ";                   
                  $u->cadastraConversa($telefone, $msg, $bot, $dia, $horas);
                  echo $bot;
               }



            /* ----------------------------------------------------
                     SEGUNDO MENU :: INSCRIÇÕES
             ------------------------------------------------------ */
            }elseif ($interacoes == "3" AND $escolhas == "3"){
               $opcao = $msg;                         

               if($opcao <= '2' AND $opcao > '0'){    
                  $u->atualizaOpcao2($telefone, $opcao); // atualiza opção 2           
                  switch ($opcao) {
                     case '1':
                        $u->cadastraConversa($telefone, $msg, $crisma, $dia, $horas);
                        echo $crisma;
                        $u->atualizaStatus($telefone, "3"); // atualiza o status de interação com o bot           
                        break;
                     case '2':
                        $u->cadastraConversa($telefone, $msg, $eucaristia, $dia, $horas);
                        echo $eucaristia;
                        $u->atualizaStatus($telefone, "3"); // atualiza o status de interação com o bot                        
                        break;
                     default:
                        $bot = "Já anotei o seu contato $consultaNome e estou chamando um humano para te ajudar, gentileza aguardar!";
                        $u->cadastraConversa($telefone, $msg, $bot, $dia, $horas);
                        echo $bot;                       
                        break;
                  } //end switch                

               }else{
                  $bot = "Já anotei o seu contato e estou chamando um humano para te ajudar, gentileza aguardar!";
                  $u->cadastraConversa($telefone, $msg, $bot, $dia, $horas);
                  echo $bot;
               }



            /* ------------------------------------------------
                              ÚLTIMO MENU
             --------------------------------------------------- */
            }elseif ($interacoes == "4"){                
                  $u->atualizaStatus($telefone, "4");
                  echo $fim;

            }elseif ($interacoes == "5"){
               $novoContato = strtolower($msg); //Converte a string para minusculo

               if($novoContato == "1" or $novoContato == "sim"){
                  $u->atualizaStatus($telefone, "1");
                  $u->atualizaOpcao1($telefone, "");
                  $u->atualizaOpcao2($telefone, "");
                  echo $menu1;                  
               }elseif($novoContato == "2" or $novoContato == "nao" or $novoContato == "não" ){                  
                  $u->atualizaStatus($telefone, "4");
                  echo "Tchau Tchau!";
               }else{
                   echo $fim;
               }

            }else{
               echo "Acho que não estou te ajudando muito. Estou chamando um humano para dar continuidade ao atendimento. Aguarde o nosso contato, por gentileza!";
            }

         }else{ //NÃO FALOU COMIGO TEM MAIS DE HORAS OU DIAS            
            $bot =  "Que bom te ver novamente $consultaNome ,
            $menu1 ";        
            $u->atualizaStatus($telefone, "1");
            $u->atualizaOpcao1($telefone, "");
            $u->atualizaOpcao2($telefone, "");
            $u->cadastraConversa($telefone, $msg, $bot, $dia, $horas);
            echo $bot;
         }

         

      /* *****************************************
            USUARÍO NUNCA FALOU COMIGO
      ******************************************** */
   }else {            
      $u->cadastroUser("", $telefone, "1", $dia, $horas, "","");
      $u->cadastraConversa($telefone, $msg, $saudacao, $dia, $horas);         
      echo $saudacao; 
   }



?>
