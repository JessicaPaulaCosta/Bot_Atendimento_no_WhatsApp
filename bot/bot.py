from selenium import webdriver
import time
from selenium.webdriver.common.keys import Keys
import requests

# **********************************
# ABRINDO O SITE > CAPTURA QR CODE
# **********************************
driver = webdriver.Chrome()
driver.get('https://web.whatsapp.com/') #navegue na página fornecida
time.sleep(10) #PAUSA DE 10 SEG PARA ACESSAR O QR CODE DO WHATSAPP


# *****************************************************************************
#  FUNÇÃO DO BOT PARA BUSCAR AS NOTIFICAÇÕES OU AGUARDAR AS PROXIMAS MENSAGENS
# *****************************************************************************
def bot():
	try:		
		# --------------------------------------------------------------
		# CAPTURA A BOLINHA VERDE DE NOTIFICAÇÃO DO WHATSAPP WEB
		# --------------------------------------------------------------
		bolinha_verde = driver.find_element_by_class_name('_23LrM') #localiza um elemento, posição da bolinhas da msg
		bolinha_verde = driver.find_elements_by_class_name('_23LrM') #localiza vários elementos, posição de várias bolinhas de msg
		mouse = bolinha_verde[-1] #posição da ultima mensagem recebida (notificação é por pilha)

		# ---------------------------------------------------------------
		# FUNÇÕES DO WEBDRIVE PARA CLICAR NA NOTIFICAÇÃO, AÇÕES DO MOUSE
		# ---------------------------------------------------------------
		acao_mouse = webdriver.common.action_chains.ActionChains(driver) #ação dentro do navegador
		acao_mouse.move_to_element_with_offset(mouse,0,-20) #mova da ultima mensagem para a posição: X, Y
		
		#EVENTOS: CLICK NA MENSAGEM 2x PARA LER
		acao_mouse.click().perform()		
		acao_mouse.click().perform()		


		# -----------------------------------------------------
		# CAPTURA DO TELEFONE DO CLIENTE QUE ENVIOU A MENSAGEM
		# -----------------------------------------------------
		pega_tel_cliente = driver.find_element_by_xpath('//*[@id="main"]/header/div[2]/div[1]/div/span')
		telefone_cliente = pega_tel_cliente.text #transforma o codigo em texto
		print(telefone_cliente)

		# ---------------------------------------------
		# LENDO A ULTIMA MENSAGEM ENVIADA PELO CLIENTE
		# ---------------------------------------------
		todas_as_msg = driver.find_elements_by_class_name('_1Gy50') #pega as msg's
		lista_msg_texto = [e.text for e in todas_as_msg] #colocando em um array todas as msg's enviadas pelo cliente no formato texto
		ultima_msg = lista_msg_texto[-1] #posição da ultima msg enviada pelo cliente.
		print(ultima_msg)

		# -----------------------------
		# RESPONDENDO A MSG DO RECEBIDA DO CLIENTE
		# -----------------------------
		resposta_bot = driver.find_element_by_xpath('//*[@id="main"]/footer/div[1]/div[2]/div/div[1]/div/div[2]')
		resposta_bot.click()

		# -------------------------------------------------------------
		# CONSULTA AS INFORMAÇÕES DA WEB | INTEGRAÇÃO: PHP COM PYTHON
		# -------------------------------------------------------------
																				#passando como paramentros chave e valor
		bonco_resposta = requests.get("http://localhost/bot/index.php", params={'msg': {ultima_msg},'telefone':{telefone_cliente}})
		bot_resposta = bonco_resposta.text #transforma o codigo em texto

		time.sleep(3) #espera 3 seg
		resposta_bot.send_keys(bot_resposta, Keys.ENTER) #enviando ações usadas no teclado: responde e pressione CTRL + ENTER
		
		# ---------------------------------------------------
		# VOLTANDO PARA O CONTATO DO PADÃO - CONVERSA FIXADA
		# ---------------------------------------------------
		contato_fixo = driver.find_element_by_class_name('_1pJ9J')
		acao_contato = webdriver.common.action_chains.ActionChains(driver)
		acao_contato.move_to_element_with_offset(contato_fixo,0,-20)
		
		#EVENTOS: CLICK NA MENSAGEM 2x PARA LER
		acao_contato.click().perform()
		acao_contato.click().perform()


	#AGUARDANDO 3 SEGUNDO E VERIFICA SE TEM NOVAS MENSAGENS
	except:
		print('Buscando novas mensagens não lidas!')
		time.sleep(3)
# ------------ END: BOT ---------------


# ****************************
# LOOP DE EXECUÇÃO DA FUNÇÃO
# ****************************
while True:
	bot()