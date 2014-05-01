<?php
	class ContattaciController extends Controller
	{
		public function execute()
		{
			$form = new AraForm();
			$form->addField(new AraFormField('nome', AraFormField::TYPE_TEXT, 'Nome', array('required' => 1, 'max_size' => 20, 'placeholder' => 'Nome')));
			$form->addField(new AraFormField('data', AraFormField::TYPE_DATE, 'Nome', array('required' => 1, 'placeholder' => 'Data del problema',
				'format' => AraFormField::FORMAT_LANGUAGE_IT, 'delimiter' => '-'
			)));
			$form->addField(new AraFormField('telefono', AraFormField::TYPE_PHONE, 'Telefono', array('required' => 1, 'placeholder' => 'Telefono')));
			$form->addField(new AraFormField('email', AraFormField::TYPE_EMAIL, 'Email', array('required' => 1, 'placeholder' => 'Email')));
			$form->addField(new AraFormField('problema', AraFormField::TYPE_SELECT, 'Problema', array('required' => 1,
				'values' => array(
					'' => '(seleziona un problema, tanto sappiamo che hai un problema)',
					'1' => 'Problema con le mail',
					'2' => 'Problema con l\'invio di una mail',
					'3' => 'Problema con la casella email',
					'4' => 'Problema con la posta',
					'5' => 'Problema con il postino'
			))));
			$form->addField(new AraFormField('messaggio', AraFormField::TYPE_TEXT, 'Messaggio', array('required' => 1, 'multiline' => 1, 'rows' => 3, 'placeholder' => 'Messaggio')));
			$form->addField(new AraFormField('punti_cardinali', AraFormField::TYPE_CHECK, 'Punti cardinali', array('required' => 1, 'size' => 2,
				'values' => array(
					'nord' => 'Nord',
					'sud' => 'Sud',
					'ovest' => 'Ovest',
					'est' => 'Est'
			))));
			$form->addField(new AraFormField('interessi', AraFormField::TYPE_CHECK, 'Interessi', array('required' => 1, 'min_size' => 3, 'max_size' => 5,
				'values' => array(
					'sport' => 'Sport',
					'tv' => 'TV',
					'web' => 'Siti Web',
					'video' => 'Video',
					'foto' => 'Fotografia',
					'pasta' => 'Pasta',
					'grammatica' => 'Grammatica'
			))));
			$form->addField(new AraFormField('modo_comunicazione', AraFormField::TYPE_OPTION, 'Comunicazioni', array( 'required' => 1,
				'values' => array(
					'email' => 'via Email',
					'posta' => 'via Posta',
					'fagiano' => 'via Fagiano'
			))));
			$form->addField(new AraFormField('privacy', AraFormField::TYPE_CHECK, 'Privacy', array('required' => 1, 'values' => array('1' => 'Acconsento al trattamento dei dati personali'))));
			$form->changeStyle('bootstrap');

			if (Request::isPOST())
			{
				$params = $this->request->getParams();
				$check = $form->checkParams($params);

				if ($check == AraForm::CHECK_PASS)
				{
					$form->emptyFields();
					$this->smarty->assign('message', 'Modulo correttamente inviato.');
					$this->smarty->assign('message_type', 'success');
				}
				else
				{
					$this->smarty->assign('message', $form->getError());
					$this->smarty->assign('message_type', 'danger');
					$this->smarty->assign('params', $params);
				}
			}

			$this->smarty->assign('form', $form);
		}
	}
?>