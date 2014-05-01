<?php
	/**
	 * Classe che definisce la sessione dell'applicazione.
	 * @author Simone
	 */
	class Session {
		var $keepAlive 	= NULL;
		var $uid 		= NULL;
		var $username 	= NULL;
		var $logged 	= FALSE;
		var $admin	 	= FALSE;	// Booleano che indica se si è amministratori
		var $user		= NULL;		// Oggetto che contiene l'utente loggato
		var $lang		= DEFAULT_LANG;

		/**
		 * Costruttore. Imposta i parametri della sessione.
		 * @param boolean $keepAlive Indica se le sessioni rimangono attive anche dopo la chiusura del browser
		 */
		public function __construct($keepAlive = false) {
			$this->keepAlive = $keepAlive;

			//Se la sessione utilizza i cookie ed esiste gia' una sessione precedente, la imposto
			if($keepAlive && isset($_COOKIE[COOKIE_NAME]) && !empty($_COOKIE[COOKIE_NAME])) {
				session_id($_COOKIE[COOKIE_NAME]);
			}

			//Se la sessione utilizza i cookie ed esiste gia' una sessione precedente, la imposto
			if($keepAlive && isset($_COOKIE[COOKIE_ADMIN]) && !empty($_COOKIE[COOKIE_ADMIN])) {
				session_id($_COOKIE[COOKIE_ADMIN]);
			}

			//Avvio la sessione
			session_start();

			//Apro la connessione al database
			$mysql_connection = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
			mysql_select_db(MYSQL_NAME, $mysql_connection);

			//Cancello tutte le sessioni scadute
			mysql_query("DELETE FROM ".MYSQL_PREFIX."_sessions WHERE expires < ".time(), $mysql_connection);

			//Recupero i dati della sessione corrente (N.B. se uso i cookie e una sessione e' scaduta l'ho rimossa al passo precedente)
			if($keepAlive) {
				if(!empty($cookie)) {
					$result = mysql_query("SELECT * FROM ".MYSQL_PREFIX."_sessions WHERE sid='{$this->cookie}'", $mysql_connection);
					$session = mysql_fetch_assoc($result);
					if($session && $session['uid'] > 0) {
					    $this->uid = $session['id'];
					    $this->logged	= TRUE;
					}
				}
			} else {
				if(isset($_SESSION[COOKIE_NAME]) && !empty($_SESSION[COOKIE_NAME])) {
					$this->uid = $_SESSION[COOKIE_NAME];
					$this->logged	= TRUE;
					$user = SysUsersTable::getInstance()->find($_SESSION[COOKIE_NAME]);
					$this->user		= $user;
				}
				if(isset($_SESSION[COOKIE_ADMIN]) && !empty($_SESSION[COOKIE_ADMIN])) {
					$this->uid = $_SESSION[COOKIE_ADMIN];
					$this->admin = TRUE;
					$user = SysAdminsTable::getInstance()->find($_SESSION[COOKIE_ADMIN]);
					$this->user		= $user;
				}
			}

			//Imposto la lingua
			$_SESSION['lang'] = isset($_SESSION['lang']) ? $_SESSION['lang'] : DEFAULT_LANG;
			if(isset($_REQUEST['lang'])) {
				$result = mysql_query("SELECT * FROM ".MYSQL_PREFIX."_languages WHERE code='{$_REQUEST['lang']}'", $mysql_connection);
				if(mysql_num_rows($result) > 0) $_SESSION['lang'] = $_REQUEST['lang']; else $_SESSION['lang'] = DEFAULT_LANG;
			} elseif(isset($_SESSION['lang'])) {
				$result = mysql_query("SELECT * FROM ".MYSQL_PREFIX."_languages WHERE code='{$_SESSION['lang']}'", $mysql_connection);
				if(mysql_num_rows($result) == 0) $_SESSION['lang'] = DEFAULT_LANG;
			} else {
				$_SESSION['lang'] = DEFAULT_LANG;
			}
			$this->lang = $_SESSION['lang'];

			//Chiudo la connessione al database
			mysql_close($mysql_connection);
		}

		/**
		 * Autentica un utente nel sistema.
		 * @param string $username Il nome utente
		 * @param string $password La passord di accesso
		 * @return true se il login e' andato a buon fine, false altrimenti.
		 */
		public function login($username, $password) {
			//Disconnetto eventuali utenti precedenti
			$this->logout();
			$this->logoutAdmin();

			//Apro la connessione al database
			$mysql_connection = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
			mysql_select_db(MYSQL_NAME, $mysql_connection);

			//Tento di recuperare l'utente, se esiste lo autentico
			$result = mysql_query("SELECT id, username FROM ".MYSQL_PREFIX."_users WHERE verified = 1 AND username='{$username}' AND password='{$password}'", $mysql_connection);
			if($result && ($row = mysql_fetch_assoc($result))) {
				$this->uid 		= $row['id'];
				$this->username = $row['username'];
				$this->logged	= TRUE;
				$user = SysUsersTable::getInstance()->find($row['id']);
				$this->user		= $user;
				if($keepAlive) {
					$expires 	= time() + COOKIE_TIMELIFE;
					$sid = session_id();
					setcookie(COOKIE_NAME, $sid, $expires);
					mysql_query("INSERT INTO ".MYSQL_PREFIX."_sessions VALUES ('{$sid}','{$this->uid}','{$expires}')");
				} else {
					$_SESSION[COOKIE_NAME] = $this->uid;
				}
				mysql_close($mysql_connection);
				return true;
			}
			mysql_close($mysql_connection);
			return false;
		}

		/**
		* Autentica un amministratore nel sistema.
		* @param string $username Il nome utente
		* @param string $password La passord di accesso
		* @return true se il login e' andato a buon fine, false altrimenti.
		*/
		public function loginAdmin($username, $password) {
			//Disconnetto eventuali utenti precedenti
			$this->logout();
			$this->logoutAdmin();

			//Apro la connessione al database
			$mysql_connection = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
			mysql_select_db(MYSQL_NAME, $mysql_connection);

			//Tento di recuperare l'utente, se esiste lo autentico
			$result = mysql_query("SELECT id, username FROM ".MYSQL_PREFIX."_admins WHERE username='{$username}' AND password='{$password}'", $mysql_connection);
			if($result && ($row = mysql_fetch_assoc($result))) {
				$this->uid 		= $row['id'];
				$this->username = $row['username'];
				$this->admin	= TRUE;
				$user = SysAdminsTable::getInstance()->find($row['id']);
				$this->user		= $user;
				if($keepAlive) {
					$expires = time() + COOKIE_TIMELIFE;
					$sid = session_id();
					setcookie(COOKIE_ADMIN, $sid, $expires);
					mysql_query("INSERT INTO ".MYSQL_PREFIX."_sessions VALUES ('{$sid}','{$this->uid}','{$this->admin}','{$expires}')");
				} else {
					$_SESSION[COOKIE_ADMIN] = $this->uid;
				}
				mysql_close($mysql_connection);
				return true;
			}
			mysql_close($mysql_connection);
			return false;
		}

		/**
		 * Disconnette l'utente dal sistema
		 */
		public function logout() {
			if($this->logged) {
				$sid = session_id();
				$mysql_connection = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
				mysql_select_db(MYSQL_NAME, $mysql_connection);
				mysql_query("DELETE FROM ".MYSQL_PREFIX."_sessions WHERE uid='{$this->uid}' AND sid='{$sid}'", $mysql_connection);
				mysql_close($mysql_connection);
				setcookie(COOKIE_NAME);
				$this->uid 		= NULL;
				$this->username = NULL;
				$this->user = NULL;
				$this->logged	= FALSE;
				session_destroy();
			}
		}

		/**
		* Disconnette l'utente dal sistema
		*/
		public function logoutAdmin() {
			if($this->admin) {
				$sid = session_id();
				$mysql_connection = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
				mysql_select_db(MYSQL_NAME, $mysql_connection);
				mysql_query("DELETE FROM ".MYSQL_PREFIX."_sessions WHERE uid='{$this->uid}' AND sid='{$sid}' AND admin=1", $mysql_connection);
				mysql_close($mysql_connection);
				setcookie(COOKIE_ADMIN);
				$this->uid 		= NULL;
				$this->username = NULL;
				$this->user = NULL;
				$this->admin	= FALSE;
				session_destroy();
			}
		}

		/**
		 * Recupera la lingua corrente
		 * @return string il codice della lingua attualmente in uso
		 */
		public function getCurrentLang() {
			return $this->lang;
		}

		/**
		* Setta la lingua corrente
		* @return string il codice della lingua attualmente in uso
		*/
		public function setCurrentLang($lang) {
			$this->lang = $lang;
			$_SESSION['lang'] = $lang;
		}

		/**
		 * Testa se un utente e' autenticato nel sistema.
		 * True se l'utente della sessione e' autenticato, false altrimenti.
		 */
		public function isLogged() {
			return $this->logged;
		}

		/**
		* Testa se un amministratore e' autenticato nel sistema.
		* True se l'amministratore della sessione e' autenticato, false altrimenti.
		*/
		public function isAdminLogged() {
			return $this->admin;
		}

		/**
		 * Restituisce l'id dell utente del sistema.
		 * @return int L'id dell'utente della sessione, se esiste, altrimenti NULL.
		 */
		public function getCurrentUid() {
			return $this->uid;
		}
	}
?>
