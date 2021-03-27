<?php

if(!defined("_PS_VERSION_")){
    exit;
}
require_once _PS_MODULE_DIR_."/sms/SendSMS.php";

class Sms extends Module{
    protected $retour;

    public function __construct(){
        $this->name = 'sms';
        $this->tab = 'administration';
        $this->version = "1.0.0";
        $this->author = 'Andro Bien-aime';
        $this->ps_versions_compliancy = array('min'=>'1.6', 'max'=>_PS_VERSION_);
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Sms Senders');
        $this->description = $this->l("Module permettant d'envoyer des sms au client et au visiteurs.");

        $this->comfirmUninstall = $this->l('Are you sure you want to uninstall');

    }

    public function install(){
        Configuration::updateValue("LTVSMS_REGION", "us-east-1");
        Configuration::updateValue("LTVSMS_KEY", "");
        Configuration::updateValue("LTVSMS_SECRET", "");
        Configuration::updateValue("LTVSMS_SENDERID", "LesTruviens");
        Configuration::updateValue("LTVSMS_MESSAGE_ORDER", "");
        Configuration::updateValue("LTVSMS_MESSAGE_INSCRIPTION", "");

        return parent::install()
            && $this->installTab()
            && $this->registerHook("header")
            && $this->registerHook("backOfficeHeader")
            && $this->registerHook("actionValidateOrder")
            && $this->registerHook("displayOrderConfirmation");
    }

    /**
     * @return bool
     */
    public function uninstall(){
        Configuration::deleteByName("LTVSMS_REGION");
        Configuration::deleteByName("LTVSMS_VERSION");
        Configuration::deleteByName("LTVSMS_KEY");
        Configuration::deleteByName("LTVSMS_SECRET");
        Configuration::deleteByName("LTVSMS_SENDERID");
        Configuration::deleteByName("LTVSMS_MESSAGE_ORDER");
        Configuration::deleteByName("LTVSMS_MESSAGE_INSCRIPTION");

        return $this->installTab(false) && parent::uninstall();
    }

    /**
     * @return string|void
     * @throws SmartyException
     * Load Configuration Form
     */
    public function getContent(){
        $output = null;

        if(((bool)Tools::isSubmit('submitSmsModule')) == true){
            $output .= $this->displayConfirmation($this->l('Settings updated'));

            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output .= $this->context->smarty->fetch($this->local_path."views/templates/admin/configure.tpl");

        return $output.$this->renderForm();

    }

    protected function renderForm(){
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = "submitSmsModule";
        $helper->currentIndex = $this->context->link->getAdminLink("AdminModules", false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite("AdminModules");
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id

        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    public function getConfigForm(){
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 5,
                        'type' => 'text',
                        'label' => $this->l("Region"),
                        'name' => 'LTVSMS_REGION',
                        'desc' => $this->l('Save the region')
                    ),
                    array(
                        'col' => 5,
                        'type' => 'password',
                        'label' => $this->l("Key"),
                        'name' => 'LTVSMS_KEY',
                        'desc' => $this->l('The key IAM associate at your service SNS')
                    ),
                    array(
                        'col' => 5,
                        'type' => 'password',
                        'label' => $this->l('Secret'),
                        'name' => 'LTVSMS_SECRET',
                        'desc' => $this->l('The secret key IAM associate at your service SNS')
                    ),
                    array(
                        'col' => 5,
                        'type' => 'text',
                        'label' => $this->l('Sender ID'),
                        'name' => 'LTVSMS_SENDERID',
                        'desc' => $this->l('The brand of your enterprise')
                    ),
                    array(
                        'col' => 5,
                        'row' => 5,
                        'type' => 'textarea',
                        'label' => $this->l('Message Order'),
                        'name' => "LTVSMS_MESSAGE_ORDER",
                        'desc' => $this->l('The message who\'s send to a users ')
                    ),
                    array(
                        'col' => 5,
                        'row' => 5,
                        'type' => 'textarea',
                        'label' => $this->l('Message Inscription'),
                        'name' => "LTVSMS_MESSAGE_INSCRIPTION",
                        'desc' => $this->l('The message who\'s send to a users ')
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                )
            ),
        );
    }

    public function getConfigFormValues(){
        return array(
          'LTVSMS_REGION' => Configuration::get("LTVSMS_REGION", 'us-east-1'),
          'LTVSMS_KEY' => Configuration::get("LTVSMS_KEY"),
          'LTVSMS_SECRET' => Configuration::get("LTVSMS_SECRET"),
          'LTVSMS_SENDERID' => Configuration::get("LTVSMS_SENDERID"),
            'LTVSMS_MESSAGE_ORDER' => Configuration::get("LTVSMS_MESSAGE_ORDER"),
            'LTVSMS_MESSAGE_INSCRIPTION' => Configuration::get("LTVSMS_MESSAGE_INSCRIPTION"),
        );
    }
    public function postProcess(){
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key){
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }
    public function installTab($install = true){
        if($install){
            $tab = new Tab();
            $tab->module = $this->name;
            $tab->class_name = "AdminSms";
            $tab->id_parent = 0;
            $tab->active = 1;
            foreach(Language::getLanguages() as $langues){
                $tab->name[(int) $langues['id_lang']] = 'Sms Senders';
            }


            return $tab->add();
        }else{
            $id = Tab::getIdFromClassName("AdminSms");
            if($id) {
                $tab = new Tab($id);
                return $tab->delete();
            }
            return true;
        }
    }

    /**
     * @param $params
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionValidateOrder($params){
        $orders = $params['order'];

        $address = new Address((int)$orders->id_address_delivery);
        $this->phone = $address->phone;
        $country = $address->id_country;
        $indicatif = new Country((int)$country);

        $message = "Vous avez passer une commande sur lestruviens 2020";
        $phone = "+".$indicatif->call_prefix.$address->phone;

        $credentials = array(
            'key' => Configuration::get("LTVSMS_KEY"),
            'secret' => Configuration::get("LTVSMS_SECRET")
        );

        $sendSms = new SendSMS($credentials);
        $this->retour = $sendSms->envoyerSMS(Configuration::get("LTVSMS_MESSAGE_ORDER"), $phone, Configuration::get("LTVSMS_SENDERID"));

    }

    public function hookDisplayOrderConfirmation($params){
        $orders = $params['order'];

        $address = new Address((int)$orders->id_address_delivery);
        $phone = $address->phone;
        $country = $address->id_country;

        $indicatif = new Country((int)$country);


        $this->context->smarty->assign([
            'phone' => $phone,
            'call_prefix' => $indicatif->call_prefix,
        ]);

        return $this->display(__FILE__, "/views/templates/front/displayOrderConfirmation.tpl");
    }
}