<?php
if (!defined('_PS_VERSION_'))
  exit;
 
class MyModule extends Module
{
  /*construction du module*/
  public function __construct()
  {
    $this->name = 'mymodule';
    $this->tab = 'front_office_features';
    $this->version = '1.0';
    $this->author = 'Aurel';
    $this->need_instance = 0;
    $this->dependencies = array('blockcart');
 
    parent::__construct();
 
    $this->displayName = $this->l('My module');
    $this->description = $this->l('Ajoute un bloc texte sur la page d\'accueil.');
 
    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
 
    if (!Configuration::get('MYMODULE_NAME'))      
      $this->warning = $this->l('No name provided');
  }

  /*installe le module*/
  public function install()
  {
  //if (Shop::isFeatureActive())
  //  Shop::setContext(Shop::CONTEXT_ALL);
 
    return parent::install() &&
    $this->registerHook('rightColumn') &&
    Configuration::updateValue('MYMODULE_NAME', '');
  }

/*permet de désinstaller*/
  public function uninstall()
  {
  if (!parent::uninstall())
    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'mymodule`');
    parent::uninstall();
  }
/*permet d'assigner le template du module dans la colonne de gauche*/
  public function hookDisplayRightColumn($params)
{
  global $smarty;
    
    Tools::addCSS(($this->_path).'mymodule.css', 'all');

  $this->context->smarty->assign(
      array(
          'my_module_name' => Configuration::get('MYMODULE_NAME'),
          'my_module_title' => Configuration::get('title'),
          'my_module_link' => $this->context->link->getModuleLink('mymodule', 'display'),
          'my_module_message' => $this->l('This is a simple text message') // Ne pas oublier de mettre la chaîne dans la méthode de traduction l()
      )
  );
  return $this->display(__FILE__, 'mymodule.tpl');
}
   /*ajoute le style du module*/
/*public function hookDisplayHeader()
{
  $this->context->controller->addCSS($this->_path.'mymodule.css', 'all');
  var_dump($this->_path);
}   */

/*permet de configurer la page configuration du box office PS*/
public function getContent()
{
         $output = null;
 
    if (Tools::isSubmit('submit'.$this->name))
    {
        $my_module_name = strval(Tools::getValue('MYMODULE_NAME'));
        $my_module_title = strval(Tools::getValue('title'));
        if (!$my_module_name  || empty($my_module_name) || !Validate::isGenericName($my_module_name) || !$my_module_title  || empty($my_module_title) || !Validate::isGenericName($my_module_title))
            $output .= $this->displayError( $this->l('Invalid Configuration value') );
        else
        {
            Configuration::updateValue('MYMODULE_NAME', $my_module_name);
            Configuration::updateValue('title', $my_module_title);
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
    }
    return $output.$this->displayForm();
}

//Le formulaire de configuration lui-même est affiché par la méthode displayForm()
public function displayForm()
{
    // récupération de la valeur de la langue par défaut
    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
     
    // Initialise les champs du tableau
    //tableau multidimensionnel avec field_form
    $fields_form[0]['form'] = array(
        'legend' => array(
            'title' => $this->l('Settings'),
        ),
        'input' => array(
           array(
                'type'     => 'text',                               
                'label'    => $this->l('Titre'), 
                'name'     => 'title',                                
                'size'     => 50,                                    
                'required' => true,                                  
                'desc'     => $this->l('Entrez le titre')    
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Texte'),
                'name' => 'MYMODULE_NAME',
                'rows' => 5,
                'cols' => 70,
                'desc'     => $this->l('Entrez le texte'),
                //'size' => 20,
                'required' => true
            )
        ),
        'submit' => array(
            'title' => $this->l('Save'),
            'class' => 'button'
        )
    );
     
    $helper = new HelperForm();
     
    // Module, token and currentIndex
    // requiert une instance du module qui utilisera les données du formulaire.
    $helper->module = $this;
    $helper->name_controller = $this->name;
    // requiert un jeton (token) unique et propre au module. getAdminTokenLite() en génère un pour vous.
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
     
    // Langue par défaut de la boutique
    $helper->default_form_language = $default_lang;
    $helper->allow_employee_form_lang = $default_lang;
     
    // Title and toolbar
    $helper->title = $this->displayName;
    $helper->show_toolbar = true;        // false -> remove toolbar
    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
    $helper->submit_action = 'submit'.$this->name;
    $helper->toolbar_btn = array(
        'save' =>
        array(
            'desc' => $this->l('Save'),
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
            '&token='.Tools::getAdminTokenLite('AdminModules'),
        ),
        'back' => array(
            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Back to list')
        )
    );
     
    // Load current value
    $helper->fields_value['MYMODULE_NAME'] = Configuration::get('MYMODULE_NAME');
    $helper->fields_value['title'] = Configuration::get('title');     
    return $helper->generateForm($fields_form);
}
}
