<?php
    class conexaoMySQL{
        private $pdo; //só pode ser vista pelas classes

	    // ============= método construtor =============
	    public function __construct ($dbnome, $host, $user, $senha){
	        try {
	            //instanciado a váriavel pdo
	            $this->pdo = new PDO("mysql:dbname=".$dbnome.";host=".$host,$user,$senha);
	            //$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);               
	        } catch (PDOException $error) {
	            echo "Erro com a conexão com o banco de dados.";
	            exit();
	        }catch (Exception $error){
	            echo "Erro generico.";
	            exit();
	        }
	    }//end contrutor



	    /* ********************************************************************************************************* */
	    /*    						CADASTRO TABELA USUARIO | CONVERSAS
	    /* ********************************************************************************************************* */
    	//verifica se o telefone já existe no banco de dados	
		public function cadastroUser($nome, $telefone, $status, $dia, $horas, $opcao1, $opcao2){

            $cmd = $this->pdo->prepare("SELECT id_usuario FROM usuario WHERE telefone = :t");
            $cmd->bindValue(":t",$telefone);
            $cmd->execute();          


            if($cmd->rowCount() > 0){
            	//Telefone já cadastrado!
				return false;
            }else{            
															      //colunas do banco 		                        paramentros
				$cmd = $this->pdo->prepare("INSERT INTO usuario (nome, telefone, status, dia, horas, opcao1, opcao2) VALUES (:n, :t, :s, :d, :h, :op1, :op2)");
				$cmd->bindValue(":n",$nome);
				$cmd->bindValue(":t",$telefone);
				$cmd->bindValue(":s",$status);
				$cmd->bindValue(":d",$dia);
				$cmd->bindValue(":h",$horas);
				$cmd->bindValue(":op1",$opcao1);
				$cmd->bindValue(":op2",$opcao2);
			    $cmd->execute();			   
			    return true;
            }
		} //end cadastroUser

		//-----------------------------------------------------------------------------
		public function cadastraConversa($telefone, $msgCliente, $msgBot, $dia, $horas){		        
				$cmd = $this->pdo->prepare("INSERT INTO historico (telefone, msgCliente, msgBot, dia, horas) VALUES (:t, :msg, :bot, :d, :h)");				
				$cmd->bindValue(":t", $telefone);
				$cmd->bindValue(":msg", $msgCliente);
				$cmd->bindValue(":bot", $msgBot);
				$cmd->bindValue(":d", $dia);
				$cmd->bindValue(":h", $horas);
			    $cmd->execute();            
		} //end cadastraConversa




		/* **************************************************************************************************************** */
        /*                 BUSCA TELEFONE | HISTORICO DO CLIENTE
        /* **************************************************************************************************************** */	
		public function consultaTelefone($telefone){
			//verifica se o telefone já existe no banco de dados
            $cmd = $this->pdo->prepare("SELECT id_usuario FROM usuario WHERE telefone = :t");
            $cmd->bindValue(":t",$telefone);
            $cmd->execute();            
            $res = $cmd->fetch(PDO::FETCH_ASSOC); //TRANSFORMANDO O FORMATO ARRAY

           	if($cmd->rowCount() > 0){ 
           		return true;
           	}else{ 
           		return false;
           	} 

		} //end consultaTelefone

		//-----------------------------------------------------------------------------
		public function consultaUltimaConversa($telefone, $dia){			
            $decremendoHoras = date('H:i:s', strtotime('-60 minute')); //HORAS ATUAL + 60 MINUTOS			
			$data = array();

            $cmd = $this->pdo->prepare("SELECT horas FROM historico WHERE telefone=:t AND dia=:d");
            $cmd->bindValue(":t",$telefone);     
            $cmd->bindValue(":d",$dia);
            $cmd->execute();

            $dados = $cmd->fetchAll(PDO::FETCH_ASSOC);//TRASFORMAR A INFORMAÇÃO EM UMA MATRIZ 
            $horas = $dados[0]["horas"]; //ESTRUTURA EM FILA


            if($horas >= $decremendoHoras){ //user falou comigo agora            	
            	return true;
            }else{
            	//echo "falou comigo tem mais de 1 hora";
            	return false;
            }
		} //end consultaUltimaConversa

		//---------------------------------------------------------------------------------------------------------------------
        public function buscaStatus($telefone){        	
        	$stmt = $this->pdo->prepare("SELECT status FROM usuario WHERE telefone= :t");
            $stmt->execute(array(':t'=>$telefone));
            $res = $stmt->fetch(PDO::FETCH_ASSOC); //TRASFORMAR A INFORMAÇÃO EM UM ARRAY
            $status = $res['status'];
        	return $status;
		} //end buscaStatus

		//-----------------------------------------------------------------------------
		public function buscaOpcao1($telefone){			
        	$stmt = $this->pdo->prepare("SELECT * FROM usuario WHERE telefone= :t");
            $stmt->execute(array(':t'=>$telefone));
            $res = $stmt->fetch(PDO::FETCH_ASSOC);  //TRASFORMAR A INFORMAÇÃO EM UM ARRAY  
       		$opcao = $res['opcao1'];		            
        	return $opcao;
        }//end buscaOpcao1

        //-----------------------------------------------------------------------------
        public function buscaNome($telefone){        	       
	    	$stmt = $this->pdo->prepare("SELECT nome FROM usuario WHERE telefone= :t");
	        $stmt->execute(array(':t'=>$telefone));
	        $res = $stmt->fetch(PDO::FETCH_ASSOC);	//TRASFORMAR A INFORMAÇÃO EM UM ARRAY 
       		$nome = $res['nome'];
        	return $nome;
        }//end buscaNome





	    /* **************************************************************************************************************** */
	    /*    						ATUALIZANDO STATUS |OPÇÃO 1 | OPÇÃO 2 |NOME DO CLIENTE
	    /* **************************************************************************************************************** */
		public function atualizaNome($telefone, $nome, $status){
			$cmd = $this->pdo->prepare("UPDATE usuario SET nome=:n, status=:s WHERE telefone=:t");
			$status+=1;
			$cmd->bindValue(":t",$telefone);
            $cmd->bindValue(":n",$nome);            
            $cmd->bindValue(":s",$status);
            $cmd->execute();
		} //end atualizaNome

		//-----------------------------------------------------------------------------
		public function atualizaOpcao1($telefone, $opcao1){					
					$cmd = $this->pdo->prepare("UPDATE usuario SET opcao1=:o WHERE telefone=:t");
					$cmd->bindValue(":t",$telefone);
				    $cmd->bindValue(":o",$opcao1);
				    $cmd->execute();	
		} //end atualizaOpcao1

		//-----------------------------------------------------------------------------
		public function atualizaStatus($telefone, $status){			
			$cmd = $this->pdo->prepare("UPDATE usuario SET status=:s WHERE telefone=:t");
			$status+=1;
			$cmd->bindValue(":t",$telefone);
		    $cmd->bindValue(":s",$status);
		    $cmd->execute();	
		} //end atualizaStatus

		//-----------------------------------------------------------------------------
		public function atualizaOpcao2($telefone, $opcao1){					
			$cmd = $this->pdo->prepare("UPDATE usuario SET opcao2=:o WHERE telefone=:t");
			$cmd->bindValue(":t",$telefone);
		    $cmd->bindValue(":o",$opcao1);
		    $cmd->execute();	
		} //end atualizaOpcao2


	} //END CLASS ConexaoMySQL
?>